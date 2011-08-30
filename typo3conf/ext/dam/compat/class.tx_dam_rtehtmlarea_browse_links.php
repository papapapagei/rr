<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once (PATH_t3lib.'interfaces/interface.t3lib_browselinkshook.php');
require_once(t3lib_extMgm::extPath('dam').'class.tx_dam_browse_media.php');

/**
 * Implementation of the t3lib_browselinkshook interface for DAM to hook on tx_rtehtmlarea_browse_links
 *
 * @author	Stanislas Rolland <typo3(arobas)sjbr.ca>
 * @package 	DAM
 */
class tx_dam_rtehtmlarea_browse_links implements t3lib_browseLinksHook {
	
	protected $invokingObject;
	protected $mode;
	protected $act;
	protected $bparams;
	protected $isEnabled = FALSE;
		// DAM browser object
	protected $browserRenderObj;
		// Link title derived from the DAM database
	protected $damTitle = '';
	
	/**
	 * initializes the hook object
	 *
	 * @param	browse_links	parent browse_links object
	 * @param	array		additional parameters
	 * @return	void
	 */
	public function init($parentObject, $additionalParameters) {
		$invokingObjectClass = get_class($parentObject);
		$enabled = ((string) $parentObject->mode == 'rte') && ($invokingObjectClass == 'tx_rtehtmlarea_browse_links' || $invokingObjectClass == 'ux_tx_rtehtmlarea_browse_links');
		if ($enabled) {
			$this->isEnabled = TRUE;
			$this->invokingObject =& $parentObject;
			$this->mode =& $this->invokingObject->mode;
			$this->act =& $this->invokingObject->act;
			$this->bparams =& $this->invokingObject->bparams;
			if ($this->isEnabled) {
				$this->invokingObject->anchorTypes[] = 'media';
			}
			$GLOBALS['LANG']->includeLLFile('EXT:dam/compat/locallang.xml');
		}
	}
	
	/**
	 * Adds new items to the currently allowed ones and returns them
	 * Replaces the 'file' item with the 'media' item
	 * Adds DAM upload tab
	 *
	 * @param	array	currently allowed items
	 * @return	array	currently allowed items plus added items
	 */
	public function addAllowedItems($currentlyAllowedItems) {
		$allowedItems =& $currentlyAllowedItems;
		if ($this->isEnabled) {
			foreach ($currentlyAllowedItems as $key => $item) {
				if ($item == 'file') {
					$allowedItems[$key] = 'media';
					break;
				}
			}
			$this->initMediaBrowser();
			$path = tx_dam::path_makeAbsolute($this->browserRenderObj->damSC->path);
			if (!$this->browserRenderObj->isReadOnlyFolder($path)) {
				$allowedItems[] = 'media_upload';
			}
				// Excluding items based on Page TSConfig
			$allowedItems = array_diff($allowedItems, t3lib_div::trimExplode(',',str_replace( array('file', 'upload'), array('media', 'media_upload'), $this->browserRenderObj->modPageConfig['properties']['removeTabs']),1));
		}
		return $allowedItems;
	}
	
	/**
	 * Modifies the menu definition and returns it
	 * Adds definition of the 'media' menu item
	 *
	 * @param	array	menu definition
	 * @return	array	modified menu definition
	 */
	public function modifyMenuDefinition($menuDefinition) {
		$menuDef =& $menuDefinition;
		if ($this->isEnabled && in_array('media', $this->invokingObject->allowedItems)) {
			$menuDef['media']['isActive'] = $this->invokingObject->act == 'media';
			$menuDef['media']['label'] =  $GLOBALS['LANG']->sL('LLL:EXT:dam/mod_main/locallang_mod.xml:mlang_tabs_tab',1);
			$menuDef['media']['url'] = '#';
			$menuDef['media']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars('?act=media&editorNo='.$this->invokingObject->editorNo.'&contentTypo3Language='.$this->invokingObject->contentTypo3Language.'&contentTypo3Charset='.$this->invokingObject->contentTypo3Charset).'\');return false;"';
		}
		if ($this->isEnabled && in_array('media_upload', $this->invokingObject->allowedItems)) {
			$menuDef['media_upload']['isActive'] = $this->act == 'media_upload';
			$GLOBALS['LANG']->includeLLFile('EXT:dam/modfunc_file_upload/locallang.xml');
			$menuDef['media_upload']['label'] =  $GLOBALS['LANG']->getLL('tx_dam_file_upload.title',1);
			$menuDef['media_upload']['url'] = '#';
			$menuDef['media_upload']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->invokingObject->thisScript.'?act=media_upload&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		return $menuDef;
	}
	
	/**
	 * Returns a new tab for the browse links wizard
	 * Returns the 'media' tab to the RTE link browser
	 *
	 * @param	string		current link selector action
	 * @return	string		a tab for the selected link action
	 */
	public function getTab($linkSelectorAction) {
		$content = '';
		if ($this->isEnabled) {
			switch ($linkSelectorAction) {
				case 'media':
					$this->initMediaBrowser();
					$content .= $this->addAttributesForm();
					$content .= $this->browserRenderObj->part_rte_linkfile();
					$this->addDAMStylesAndJSArrays();
					break;
				case 'media_upload':
					$this->initMediaBrowser();
					$content .= $this->browserRenderObj->dam_upload($this->allowedFileTypes);
					$content .= $this->browserRenderObj->damSC->getOptions();
					$content .='<br /><br />';
					if ($GLOBALS['BE_USER']->isAdmin() || $GLOBALS['BE_USER']->getTSConfigVal('options.createFoldersInEB'))	{
						$path = tx_dam::path_makeAbsolute($this->browserRenderObj->damSC->path);
						if (!$path OR !@is_dir($path))	{
							$path = $this->fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
						}
						$content .= $this->browserRenderObj->createFolder($path);
						$content .= '<br />';
					}
					$this->addDAMStylesAndJSArrays();
					break;
			}
		}
		return $content;
	}
	
	/**
	 * Checks the current URL and determines what to do
	 * If the link was determined to be a file link, then set the action to 'media'
	 *
	 * @param	string		$href
	 * @param	string		$siteUrl
	 * @param	array		$info
	 * @return	array
	 */
	public function parseCurrentUrl($href, $siteUrl, $info) {
		if ($this->isEnabled && $info['act'] == 'file') {
			$info['act'] = 'media';
			unset($this->invokingObject->curUrlArray['external']);
		}
		return $info;
	}

	protected function initMediaBrowser() {
		$this->browserRenderObj = t3lib_div::makeInstance('tx_dam_browse_media');
		$this->browserRenderObj->pObj =& $this->invokingObject;
		$this->invokingObject->browser =& $this->browserRenderObj;
			// init class browse_links
		$this->browserRenderObj->init();
		$this->browserRenderObj->mode =& $this->mode;
		$this->browserRenderObj->act =& $this->act;
		$this->browserRenderObj->bparams =& $this->bparams;
		$this->browserRenderObj->thisConfig =& $this->invokingObject->thisConfig;
			// init the DAM object
		$this->browserRenderObj->initDAM();
			// processes MOD_SETTINGS
		$this->browserRenderObj->getModSettings();
			// Processes bparams parameter
		$this->browserRenderObj->processParams();
			// init the DAM selection after we've got the params
		$this->browserRenderObj->initDAMSelection();
	}

	protected function addDAMStylesAndJSArrays() {
		$this->invokingObject->doc->inDocStylesArray = array_merge($this->invokingObject->doc->inDocStylesArray, $this->browserRenderObj->doc->inDocStylesArray);
		$this->invokingObject->doc->JScodeArray = array_merge($this->invokingObject->doc->JScodeArray, $this->browserRenderObj->doc->JScodeArray);
	}

	/**
	 * Redefines same function from invoking object in order to invoke local verion of addTitleSelector()
	 *
	 * @return	string		the html code to be added to the form
	 */
	protected function addAttributesForm() {
		$ltargetForm = '';
			// Add target, class selector box, title and parameters fields:
		$ltarget = $this->invokingObject->addTargetSelector();
		$lclass = $this->addClassSelector();
		$ltitle = $this->addTitleSelector();
		if ($ltarget || $lclass || $ltitle) {
			$ltargetForm = $this->invokingObject->wrapInForm($ltarget.$lclass.$ltitle);
		}
		return $ltargetForm;
	}

	/**
	 * Set the link title that may be derived from the DAM meta data
	 *
	 * @return	void
	 */
	 protected function setDAMTitle() {
		if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'], 'usedamcolumn') && $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn']) {
			$mediaId = $this->invokingObject->curUrlArray['txdam'];
			if ($mediaId) {
					// Checking if the id-parameter is int and get meta data
				if (t3lib_div::testInt($mediaId)) {
					$meta = tx_dam::meta_getDataByUid($mediaId);
				}
					// Generating configured title from meta data
				if (is_array($meta)) {
					require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');
					$displayItems = $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn.']['displayItems'] ? $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn.']['displayItems'] : '';
					$this->damTitle = tx_dam_guiFunc::meta_compileHoverText($meta, $displayItems, ', ');
				} else {
					$this->damTitle = 'No media record found: ' . $mediaId;
				}
			}
		}
	 }

	/**
	 * Redefines same function from invoking object in order to change the onChange event on the class selector
	 *
	 * @return	string		the html code to be added to the form
	 */
	protected function addClassSelector() {
		$selectClass = '';
		if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'], 'usedamcolumn') && $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn']) {
			if ($this->invokingObject->classesAnchorJSOptions[$this->act]) {
				$selectClass ='
							<tr>
								<td>'.$GLOBALS['LANG']->getLL('anchor_class',1).':</td>
								<td colspan="3">
									<select name="anchor_class" onchange="' . $this->getClassOnChangeJS() . '">
										' . $this->invokingObject->classesAnchorJSOptions[$this->act] . '
									</select>
								</td>
							</tr>';
			}
		} else {
			$selectClass = $this->invokingObject->addClassSelector();
		}
		return $selectClass;
	}

	/**
	 * Redefines same function from invoking object in order to change the class selector onChange event
	 *
	 * @return	string	class selector onChange JS code
	 */
	 protected function getClassOnChangeJS() {
		 $classOnChangeJS = $this->invokingObject->getClassOnChangeJS();
		 if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'], 'usedamcolumn') && $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn']) {
			$classOnChangeJS .= '
					if (document.getElementById(\'rtehtmlarea-dam-browse-links-useDAMColumn\').checked && '. ($this->damTitle ? 'true' : 'false') . ') {
						var damTitle = \'' . htmlspecialchars($this->damTitle) . '\';
						if (damTitle) {
							document.getElementById(\'rtehtmlarea-browse-links-title-readonly\').innerHTML = damTitle;
							document.ltargetform.anchor_title.value = damTitle;
							browse_links_setTitle(damTitle);
						}
						dam_browse_links_setTitle(true);
					}
							';
		 }
		 return $classOnChangeJS;
	 }

	/**
	 * Redefines same function from invoking object in order to add a checkbox to specify how the title attribute should be handled
	 *
	 * @return	string		the html code to be added to the form
	 */
	protected function addTitleSelector() {
		$titleSelector = $this->invokingObject->addTitleSelector();
		if ($titleSelector && t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'], 'usedamcolumn') && $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn']) {
				// Adding checkbox to the form
			$checked = ($this->invokingObject->curUrlArray['usedamcolumn'] == 'true') || $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn.']['checked'] || $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn.']['enforce'];
			$titleSelector .= '
							<tr>
								<td>
									<label for="rtehtmlarea-dam-browse-links-useDAMColumn">'.$GLOBALS['LANG']->getLL('useDAMColumn',1).':</label>
								</td>
								<td colspan="3">
									<input name="rtehtmlarea-dam-browse-links-useDAMColumn" id="rtehtmlarea-dam-browse-links-useDAMColumn" onchange="if (this.checked) { document.getElementById(\'rtehtmlarea-browse-links-title-readonly\').innerHTML =   \'' . htmlspecialchars($this->damTitle) . '\'; } browse_links_setAdditionalValue(\'usedamcolumn\', this.checked); dam_browse_links_setTitle(this.checked);"' . ($checked ? ' checked="checked" ' : ' ') . 'type="checkbox" />
								</td>
							</tr>';
				// Adding additional JS for this checkbox
			$this->browserRenderObj->doc->JScodeArray['rtehtmlarea-DAMTitle'] = $this->getTitleAttributeJSCode();
		}
		return $titleSelector;
	}
	
	protected function getTitleAttributeJSCode() {
		return '
			function dam_browse_links_setTitle(useDAMColumn, enforce) {
				if (document.getElementById("rtehtmlarea-browse-links-anchor_title")) {
					if (useDAMColumn) {
						document.getElementById("rtehtmlarea-browse-links-title-input").style.display = "none";
						document.getElementById("rtehtmlarea-browse-links-title-readonly").style.display = "inline";
						var damTitle = document.getElementById("rtehtmlarea-browse-links-title-readonly").innerHTML;
						if (damTitle) {
							document.ltargetform.anchor_title.value = damTitle;
							browse_links_setTitle(damTitle);
						}
						if (' . (!$this->invokingObject->curUrlArray['href']?'true':'false') . ') {
							document.getElementById("rtehtmlarea-browse-links-title-label").style.display = "none";
							document.getElementById("rtehtmlarea-browse-links-title-readonly").style.display = "none";
						}
						if (enforce) {
							document.getElementById("rtehtmlarea-dam-browse-links-useDAMColumn").setAttribute("disabled", true);
						}
					} else {
						document.getElementById("rtehtmlarea-browse-links-title-label").style.display = "inline";
						document.getElementById("rtehtmlarea-browse-links-title-input").style.display = "inline";
						document.getElementById("rtehtmlarea-browse-links-title-readonly").style.display = "none";
					}
				}
			}
		';
	}
	
	/**
	 * Modifies the body tag additions array and returns it
	 *
	 * @param	array	body tag additions
	 * @return	array	modified body tag additions
	 */
	public function addBodyTagAdditions($bodyTagAdditions) {
		if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'], 'usedamcolumn') && $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn']) {
			$this->setDAMTitle();
				// Adding onLoad processing if the checkbox is pre-checked
			$checked = ($this->invokingObject->curUrlArray['usedamcolumn'] == 'true') || $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn.']['checked'] || $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn.']['enforce'];
			if ($checked) {
				$enforce = $this->invokingObject->buttonConfig[$this->act.'.']['properties.']['title.']['useDAMColumn.']['enforce'] ? 'true' : 'false';
				$bodyTagAdditions['onLoad'] .= 'document.getElementById(\'rtehtmlarea-browse-links-title-readonly\').innerHTML =   \'' . htmlspecialchars($this->damTitle) . '\'; browse_links_setAdditionalValue(\'usedamcolumn\', true); dam_browse_links_setTitle(true,' . $enforce . ');';
			}
		}
		return $bodyTagAdditions;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_browse_links.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_browse_links.php']);
}
?>