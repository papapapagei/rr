<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skaarhoj (kasper@typo3.com)
*  (c) 2004-2008 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
/**
 * Displays image selector for the RTE
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Stanislas Rolland <typo3(arobas)sjbr.ca>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
require_once(PATH_t3lib.'class.t3lib_stdgraphic.php');
require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once(PATH_txdam.'class.tx_dam_browse_media.php');
require_once(t3lib_extMgm::extPath('rtehtmlarea').'mod4/class.tx_rtehtmlarea_select_image.php');

class tx_dam_rtehtmlarea_select_image extends tx_dam_browse_media {
	var $content;
	var $allowedItems;
	var $imgTitleDAMColumn = 'caption';
	var $editorNo;
	var $sys_language_content;
	var $RTESelectImageObj; // instance of tx_rtehtmlarea_select_image;
	
	/**
	 * Check if this object should be rendered.
	 *
	 * @param	string		$type Type: "file", ...
	 * @param	object		$pObj Parent object.
	 * @return	boolean
	 * @see SC_browse_links::main()
	 */
	function isValid($type, &$pObj)	{
		$isValid = false;

		$pArr = explode('|', t3lib_div::_GP('bparams'));

		if ($type === 'rte' AND $pObj->button == 'image') {
			$isValid = true;
		}
		else {
			$valid = parent::isValid($type, $pObj);
		}
		
		return $isValid;
	}
	
	
	/**
	 * Initialisation
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$BACK_PATH,$TYPO3_CONF_VARS;
		
			// Main GPvars:
		$this->act = t3lib_div::_GP('act');
		$this->expandPage = t3lib_div::_GP('expandPage');
		$this->expandFolder = t3lib_div::_GP('expandFolder');
		
			// Find RTE parameters
		$this->bparams = t3lib_div::_GP('bparams');
		$this->editorNo = t3lib_div::_GP('editorNo');
		$this->sys_language_content = t3lib_div::_GP('sys_language_content');
		$this->RTEtsConfigParams = t3lib_div::_GP('RTEtsConfigParams');
		if (!$this->editorNo) {
			$pArr = explode('|', $this->bparams);
			$pRteArr = explode(':', $pArr[1]);
			$this->editorNo = $pRteArr[0];
			$this->sys_language_content = $pRteArr[1];
			$this->RTEtsConfigParams = $pArr[2];
		}
		
			// Find "mode"
		$this->mode = t3lib_div::_GP('mode');
		if (!$this->mode)	{
			$this->mode='rte';
		}
		
			// Site URL
		$this->siteURL = t3lib_div::getIndpEnv('TYPO3_SITE_URL');	// Current site url
		
			// the script to link to
		$this->thisScript = t3lib_div::getIndpEnv('SCRIPT_NAME');

			// init fileProcessor
		$this->fileProcessor = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$this->fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);

		if (!$this->act)	{
			$this->act='magic';
		}
		
		$this->RTESelectImageObj = t3lib_div::makeInstance('tx_rtehtmlarea_select_image');
		$this->RTESelectImageObj->initVariables();
		$this->RTESelectImageObj->initConfiguration();
		
		t3lib_div::loadTCA('tx_dam');
		if (is_array($this->RTESelectImageObj->buttonConfig['title.']) && is_array($TCA['tx_dam']['columns'][$this->RTESelectImageObj->buttonConfig['title.']['useDAMColumn']])) {
			$this->imgTitleDAMColumn = $this->RTESelectImageObj->buttonConfig['title.']['useDAMColumn'];
		}
		
		$this->allowedItems = $this->RTESelectImageObj->getAllowedItems('magic,plain,image,upload');
		reset($this->allowedItems);
		if (!in_array($this->act,$this->allowedItems))	{
			$this->act = current($this->allowedItems);
		}
		
			// Insert the image if we are done
		$this->insertImage();
		
			// Creating backend template object:
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->bodyTagAdditions = $this->RTESelectImageObj->getBodyTagAdditions();
		$this->doc->docType= 'xhtml_trans';
		$this->doc->backPath = $BACK_PATH;
	}
	
	/**
	 * Insert the image in the editing area
	 *
	 * @return	void
	 */
	protected function insertImage()	{
		global $TCA;
		
		if (t3lib_div::_GP('insertImage'))	{
			$filepath = t3lib_div::_GP('insertImage');
			$imgInfo = $this->RTESelectImageObj->getImageInfo($filepath);
			$imgMetaData = tx_dam::meta_getDataForFile($filepath,'uid,pid,alt_text,hpixels,vpixels,'.$this->imgTitleDAMColumn.','.$TCA['tx_dam']['ctrl']['languageField']);
			$imgMetaData = $this->getRecordOverlay('tx_dam', $imgMetaData, $this->sys_language_content);
			switch ($this->act) {
				case 'magic':
					$this->RTESelectImageObj->insertMagicImage($filepath, $imgInfo, $imgMetaData['alt_text'], $imgMetaData[$this->imgTitleDAMColumn], 'txdam='.$imgMetaData['uid']);
					exit;
					break;
				case 'plain':
					$imgInfo[0] = $imgMetaData['hpixels'];
					$imgInfo[1] = $imgMetaData['vpixels'];
					$this->RTESelectImageObj->insertPlainImage($imgInfo, $imgMetaData['alt_text'], $imgMetaData[$this->imgTitleDAMColumn], 'txdam='.$imgMetaData['uid']);
					exit;
					break;
			}
		}
	}
	
	/**
	 * Get additional DAM-specific JS functions
	 *
	 * @return	string
	 */
	function getAdditionalJSCode()	{
		global $LANG,$BACK_PATH,$TYPO3_CONF_VARS;

		$JScode='
			function insertElement(table, uid, type, filename,fp,filetype,imagefile,action, close)	{
				return jumpToUrl(\''.$this->thisScript.'?act='.$this->act.'&mode='.$this->mode.'&bparams='.$this->bparams.'&insertImage='.'\'+fp);
			}';
		return $JScode;
	}
	
	/**
	 * We need to pass along some RTE parameters
	 *
	 * @return	void
	 */
	function reinitParams() {
		if ($this->editorNo) {
			$pArr = explode('|', $this->bparams);
			$pArr[1] = implode(':', array($this->editorNo, $this->sys_language_content));
			$pArr[2] = $this->RTEtsConfigParams;
			if ($this->act == 'dragdrop' || $this->act == 'plain') {
				$pArr[3] = 'jpg,jpeg,gif,png';
			}
			$this->bparams = implode('|', $pArr);
			
		}
		parent::reinitParams();
	}

	/**
	 * Return true or false whether thumbs can be displayed or not
	 *
	 * @return	boolean
	 */
	function thumbsEnabled() {
			// Getting flag for showing/not showing thumbnails:
		$noThumbs = $GLOBALS['BE_USER']->getTSConfigVal('options.noThumbsInEB') || ($this->mode == 'rte' && $GLOBALS['BE_USER']->getTSConfigVal('options.noThumbsInRTEimageSelect'));
		return !$noThumbs;
	}
	
	/**
	 * Renders the EB for rte mode
	 *
	 * @return	string HTML
	 */
	function main_rte()	{
		global $LANG, $TYPO3_CONF_VARS, $FILEMOUNTS, $BE_USER;
		
		$path = tx_dam::path_makeAbsolute($this->damSC->path);
		if ($this->isReadOnlyFolder($path)) {
			$this->allowedItems = array_diff($this->allowedItems, array('upload'));
		}
		if (!$path OR !@is_dir($path))	{
			$path = $this->fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		$this->damSC->path = tx_dam::path_makeRelative($path); // mabe not needed
		
			// Starting content:
		$content = $this->doc->startPage($LANG->getLL('Insert Image',1));
		
		$this->reinitParams();
		
			// Making menu in top:
		$menuDef = array();
		if (in_array('image',$this->allowedItems) && ($this->act=='image' || t3lib_div::_GP('cWidth'))) {
			$menuDef['page']['isActive'] = $this->act=='image';
			$menuDef['page']['label'] = $LANG->getLL('currentImage',1);
			$menuDef['page']['url'] = '#';
			$menuDef['page']['addParams'] = 'onClick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=image&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('magic',$this->allowedItems)){
			$menuDef['file']['isActive'] = $this->act=='magic';
			$menuDef['file']['label'] = $LANG->getLL('magicImage',1);
			$menuDef['file']['url'] = '#';
			$menuDef['file']['addParams'] = 'onClick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=magic&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('plain',$this->allowedItems)) {
			$menuDef['url']['isActive'] = $this->act=='plain';
			$menuDef['url']['label'] = $LANG->getLL('plainImage',1);
			$menuDef['url']['url'] = '#';
			$menuDef['url']['addParams'] = 'onClick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=plain&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('dragdrop',$this->allowedItems)) {
			$menuDef['mail']['isActive'] = $this->act=='dragdrop';
			$menuDef['mail']['label'] = $LANG->getLL('dragDropImage',1);
			$menuDef['mail']['url'] = '#';
			$menuDef['mail']['addParams'] = 'onClick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=dragdrop&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('upload', $this->allowedItems)) {
			$menuDef['upload']['isActive'] = ($this->act=='upload');
			$menuDef['upload']['label'] = $LANG->getLL('tx_dam_file_upload.title',1);
			$menuDef['upload']['url'] = '#';
			$menuDef['upload']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=upload&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		$content .= $this->doc->getTabMenuRaw($menuDef);
		
		$pArr = explode('|', $this->bparams);
		switch($this->act)	{
			case 'image':
				$JScode = '
				document.write(printCurrentImageOptions());
				insertImagePropertiesInForm();';
				$content .= '<br />'.$this->doc->wrapScriptTags($JScode);
				break;
			case 'upload':
				$content .= $this->dam_upload($this->allowedFileTypes, $this->disallowedFileTypes);
				$content .= $this->damSC->getOptions();
				$content .='<br /><br />';
				if ($BE_USER->isAdmin() || $BE_USER->getTSConfigVal('options.createFoldersInEB'))	{
					$content .= $this->createFolder($path);
					$content .= '<br />';
				}
				break;
			case 'dragdrop':
				$this->allowedFileTypes = t3lib_div::trimExplode(',', $pArr[3], true);
				$this->addDisplayOptions();
				$content .= $this->dam_select($this->allowedFileTypes, $this->disallowedFileTypes);
				$content .= $this->damSC->getOptions();
				break;
			case 'plain':
				$this->allowedFileTypes = t3lib_div::trimExplode(',', $pArr[3], true);
				$this->addDisplayOptions();
				$content .= $this->dam_select($this->allowedFileTypes, $this->disallowedFileTypes);
				$content .= $this->damSC->getOptions();
				$content .= $this->getMsgBox($this->RTESelectImageObj->getHelpMessage($this->act));
				break;
			case 'magic':
				$this->addDisplayOptions();
				$content .= $this->dam_select($this->allowedFileTypes, $this->disallowedFileTypes);
				$content .= $this->damSC->getOptions();
				$content .= $this->getMsgBox($this->RTESelectImageObj->getHelpMessage($this->act));
				break;
			default:
				break;
		}
			// Ending page, returning content:
		$content.= $this->doc->endPage();
		$this->doc->JScodeArray['rtehtmlarea'] = $this->RTESelectImageObj->getJSCode($this->act, $this->editorNo, $this->sys_language_content);
		$this->doc->JScodeArray['rtehtmlarea-dam'] = $this->getAdditionalJSCode();
		$content = $this->damSC->doc->insertStylesAndJS($content);
		return $content;
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
	function getRecordOverlay($table,$row,$sys_language_content,$OLmode='')	{
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_select_image.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_bselect_image.php']);
}

?>
