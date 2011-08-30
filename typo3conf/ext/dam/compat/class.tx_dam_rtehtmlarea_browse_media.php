<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');

/**
 * Implementation of the t3lib_browselinkshook interface for DAM to hook on tx_rtehtmlarea_browse_links
 *
 * @author	Stanislas Rolland <typo3(arobas)sjbr.ca>
 * @package 	DAM
 */
class tx_dam_rtehtmlarea_browse_media implements t3lib_browseLinksHook {
	
	protected $invokingObject;
	protected $isEnabled;
	protected $mode;
	protected $act;
	protected $bparams;
	protected $sys_language_content;
	protected $buttonConfig;
	protected $allowedFileTypes;
	protected $browserRenderObj; // DAM browser object
	
	/**
	 * initializes the hook object
	 *
	 * @param	browse_links	parent browse_links object
	 * @param	array		additional parameters
	 * @return	void
	 */
	public function init($parentObject, $additionalParameters) {
		$this->invokingObject =& $parentObject;
		$this->mode =& $this->invokingObject->mode;
		$this->act =& $this->invokingObject->act;
		if ($this->act == 'magic') {
			$this->act = 'media_magic';
		}
		$this->bparams =& $this->invokingObject->bparams;
		$this->sys_language_content =& $this->invokingObject->sys_language_content;
		$this->buttonConfig =& $this->invokingObject->buttonConfig;
		$this->allowedFileTypes =& $this->invokingObject->allowedFileTypes;
		
			// Adapt bparams
		$pArr = explode('|', $this->bparams);
		if ($this->act == 'media_dragdrop' || $this->act == 'media_plain') {
			$this->allowedFileTypes = explode(',', 'jpg,jpeg,gif,png');
		}
		$pArr[3] = implode(',', $this->allowedFileTypes);
		$this->bparams = implode('|', $pArr);
	}
	
	/**
	 * Adds new items to the currently allowed ones and returns them
	 * Replaces the 'file' item with the 'media' item
	 *
	 * @param	array	currently allowed items
	 * @return	array	currently allowed items plus added items
	 */
	public function addAllowedItems($currentlyAllowedItems) {
		$allowedItems =& $currentlyAllowedItems;
		foreach ($currentlyAllowedItems as $key => $item) {
			switch ($item) {
				case 'magic':
					$allowedItems[$key] = 'media_magic';
					break;
				case 'plain':
					$allowedItems[$key] = 'media_plain';
					break;
				case 'dragdrop':
					$allowedItems[$key] = 'media_dragdrop';
					break;
				default:
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
		global $LANG;
		
		$menuDef =& $menuDefinition;
		if (in_array('media_magic', $this->invokingObject->allowedItems)) {
			$menuDef['media_magic']['isActive'] = $this->act == 'media_magic';
			$menuDef['media_magic']['label'] =  $LANG->getLL('magicImage',1);
			$menuDef['media_magic']['url'] = '#';
			$menuDef['media_magic']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->invokingObject->thisScript.'?act=media_magic&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('media_plain', $this->invokingObject->allowedItems)) {
			$menuDef['media_plain']['isActive'] = $this->act == 'media_plain';
			$menuDef['media_plain']['label'] =  $LANG->getLL('plainImage',1);
			$menuDef['media_plain']['url'] = '#';
			$menuDef['media_plain']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->invokingObject->thisScript.'?act=media_plain&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('media_dragdrop', $this->invokingObject->allowedItems)) {
			$menuDef['media_dragdrop']['isActive'] = $this->act == 'media_dragdrop';
			$menuDef['media_dragdrop']['label'] =  $LANG->getLL('dragDropImage',1);
			$menuDef['media_dragdrop']['url'] = '#';
			$menuDef['media_dragdrop']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->invokingObject->thisScript.'?act=media_dragdrop&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('media_upload', $this->invokingObject->allowedItems)) {
			$menuDef['media_upload']['isActive'] = $this->act == 'media_upload';
			$LANG->includeLLFile('EXT:dam/modfunc_file_upload/locallang.xml');
			$menuDef['media_upload']['label'] =  $LANG->getLL('tx_dam_file_upload.title',1);
			$menuDef['media_upload']['url'] = '#';
			$menuDef['media_upload']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->invokingObject->thisScript.'?act=media_upload&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		return $menuDef;
	}
	
	/**
	 * Returns a new tab to the RTE media browser
	 *
	 * @param	string		current action
	 * @return	string		a tab for the selected action
	 */
	public function getTab($act) {
		global $BE_USER, $LANG;
		
		$content = '';
		switch ($act) {
			case 'media_magic':
				$this->initMediaBrowser();
				$this->browserRenderObj->addDisplayOptions();
				$content .= $this->browserRenderObj->dam_select($this->allowedFileTypes);
				$content .= $this->browserRenderObj->damSC->getOptions();
				$content .= $this->invokingObject->getMsgBox($this->invokingObject->getHelpMessage('magic'));
				$this->addDAMStylesAndJSArrays();
				break;
			case 'media_plain':
				$this->initMediaBrowser();
				$this->browserRenderObj->addDisplayOptions();
				$content .= $this->browserRenderObj->dam_select($this->allowedFileTypes);
				$content .= $this->browserRenderObj->damSC->getOptions();
				$content .= $this->invokingObject->getMsgBox($this->invokingObject->getHelpMessage('plain'));
				$this->addDAMStylesAndJSArrays();
				break;
			case 'media_dragdrop':
				$this->initMediaBrowser();
				$this->browserRenderObj->addDisplayOptions();
				$content .= $this->browserRenderObj->dam_select($this->allowedFileTypes);
				$content .= $this->browserRenderObj->damSC->getOptions();
				$content .= $this->invokingObject->getMsgBox($LANG->getLL('findDragDrop'));
				$this->addDAMStylesAndJSArrays();
				break;
			case 'media_upload':
				$this->initMediaBrowser();
				$content .= $this->browserRenderObj->dam_upload($this->allowedFileTypes);
				$content .= $this->browserRenderObj->damSC->getOptions();
				$content .='<br /><br />';
				if ($BE_USER->isAdmin() || $BE_USER->getTSConfigVal('options.createFoldersInEB'))	{
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
		return $content;
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
		$this->invokingObject->doc->JScodeArray['rtehtmlarea-dam'] = $this->getAdditionalJSCode();
	}
	
	/**
	 * Get additional DAM-specific JS functions
	 *
	 * @return	string
	 */
	protected function getAdditionalJSCode() {
		$JScode='
			function insertElement(table, uid, type, filename,fp,filetype,imagefile,action, close)	{
				return jumpToUrl(\''.$this->invokingObject->thisScript.'?act='.$this->act.'&mode='.$this->mode.'&bparams='.$this->bparams.'&insertImage='.'\'+fp);
			}';
		return $JScode;
	}
	
	/**
	 * Inserts the element in the RTE editing area
	 *
	 * @param	string		$act: the action
	 * @return	void
	 */
	public function insertElement($act) {
		global $TCA;
		
			// Determine the DAM column from which to get the title
		$imgTitleDAMColumn = 'caption';
		t3lib_div::loadTCA('tx_dam');
		if (is_array($this->buttonConfig['title.']) && is_array($TCA['tx_dam']['columns'][$this->buttonConfig['title.']['useDAMColumn']])) {
			$imgTitleDAMColumn = $this->buttonConfig['title.']['useDAMColumn'];
		}
			// Get the image info from the DAM database
		$filepath = t3lib_div::_GP('insertImage');
		$imgInfo = $this->invokingObject->getImageInfo($filepath);
		$imgMetaData = tx_dam::meta_getDataForFile($filepath,'uid,pid,alt_text,hpixels,vpixels,'.$imgTitleDAMColumn.','.$TCA['tx_dam']['ctrl']['languageField']);
			// Localize the record in the language of the content element
		$imgMetaData = $this->getRecordOverlay('tx_dam', $imgMetaData, $this->sys_language_content);
		switch ($act) {
			case 'media_magic':
				$this->invokingObject->insertMagicImage($filepath, $imgInfo, $imgMetaData['alt_text'], $imgMetaData[$imgTitleDAMColumn], 'txdam='.$imgMetaData['uid']);
				break;
			case 'media_plain':
				$imgInfo[0] = $imgMetaData['hpixels'];
				$imgInfo[1] = $imgMetaData['vpixels'];
				$this->invokingObject->insertPlainImage($imgInfo, $imgMetaData['alt_text'], $imgMetaData[$imgTitleDAMColumn], 'txdam='.$imgMetaData['uid']);
				break;
		}
	}
	
	/**
	 * Checks the current URL and determines what to do
	 *
	 * @param	unknown_type		$href
	 * @param	unknown_type		$siteUrl
	 * @param	unknown_type		$info
	 * @return	unknown_type
	 */
	public function parseCurrentUrl($href, $siteUrl, $info) {
		return $info;
	}
	
	/**
	 * Import from t3lib_page in order to create backend version
	 * Creates language-overlay for records in general (where translation is found in records from the same table)
	 *
	 * @param	string		Table name
	 * @param	array		Record to overlay. Must containt uid, pid and $table]['ctrl']['languageField']
	 * @param	integer		Pointer to the sys_language uid for content on the site.
	 * @param	string		Overlay mode. If "hideNonTranslated" then records without translation will not be returned un-translated but unset (and return value is false)
	 * @return	mixed		Returns the input record, possibly overlaid with a translation. But if $OLmode is "hideNonTranslated" then it will return false if no translation is found.
	 */
	protected function getRecordOverlay($table,$row,$sys_language_content,$OLmode='')	{
		global $TCA, $TYPO3_DB;
		if ($row['uid']>0 && $row['pid']>0)	{
			if ($TCA[$table] && $TCA[$table]['ctrl']['languageField'] && $TCA[$table]['ctrl']['transOrigPointerField'])	{
				if (!$TCA[$table]['ctrl']['transOrigPointerTable'])	{
						// Will try to overlay a record only if the sys_language_content value is larger that zero.
					if ($sys_language_content>0)	{
							// Must be default language or [All], otherwise no overlaying:
						if ($row[$TCA[$table]['ctrl']['languageField']]<=0)	{
								// Select overlay record:
							$res = $TYPO3_DB->exec_SELECTquery(
								'*',
								$table,
								'pid='.intval($row['pid']).
									' AND '.$TCA[$table]['ctrl']['languageField'].'='.intval($sys_language_content).
									' AND '.$TCA[$table]['ctrl']['transOrigPointerField'].'='.intval($row['uid']).
									t3lib_BEfunc::BEenableFields($table).
									t3lib_BEfunc::deleteClause($table),
								'',
								'',
								'1'
								);
							$olrow = $TYPO3_DB->sql_fetch_assoc($res);
							//$this->versionOL($table,$olrow);
							
								// Merge record content by traversing all fields:
							if (is_array($olrow))	{
								foreach($row as $fN => $fV)	{
									if ($fN!='uid' && $fN!='pid' && isset($olrow[$fN]))	{
										if ($TCA[$table]['l10n_mode'][$fN]!='exclude' && ($TCA[$table]['l10n_mode'][$fN]!='mergeIfNotBlank' || strcmp(trim($olrow[$fN]),'')))	{
											$row[$fN] = $olrow[$fN];
										}
									}
								}
							} elseif ($OLmode==='hideNonTranslated' && $row[$TCA[$table]['ctrl']['languageField']]==0)	{	// Unset, if non-translated records should be hidden. ONLY done if the source record really is default language and not [All] in which case it is allowed.
								unset($row);
							}

							// Otherwise, check if sys_language_content is different from the value of the record - that means a japanese site might try to display french content.
						} elseif ($sys_language_content!=$row[$TCA[$table]['ctrl']['languageField']])	{
							unset($row);
						}
					} else {
							// When default language is displayed, we never want to return a record carrying another language!:
						if ($row[$TCA[$table]['ctrl']['languageField']]>0)	{
							unset($row);
						}
					}
				}
			}
		}

		return $row;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_browse_media.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_browse_media.php']);
}
?>
