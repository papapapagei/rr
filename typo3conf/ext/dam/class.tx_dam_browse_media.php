<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   89: class tx_dam_browse_media extends browse_links
 *  105:     function isValid($type, &$pObj)
 *  127:     function initDAM ()
 *  145:     function initDAMSelection ()
 *  186:     function render($type, &$pObj)
 *  261:     function main()
 *
 *              SECTION: Media Browser Module
 *  352:     function dam_select($allowedFileTypes=array(), $disallowedFileTypes=array())
 *  422:     function getSelectionSelector ()
 *  461:     function renderFileList($files, $mode='file')
 *
 *              SECTION: Upload module
 *  617:     function dam_upload($allowedFileTypes=array(), $disallowedFileTypes=array())
 *  683:     function createFolder($path)
 *
 *              SECTION: Collect Data
 *  710:     function getFileListArr ($allowedFileTypes, $disallowedFileTypes, $mode)
 *
 *              SECTION: Tools
 *  809:     function addDisplayOptions()
 *  835:     function displayThumbs()
 *  856:     function getModSettings($key='')
 *  904:     function processParams()
 *  962:     function isParamPassed ($paramName)
 *  972:     function reinitParams()
 *  998:     function quoteJSvalue($value)
 *
 * TOTAL FUNCTIONS: 18
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

require_once(PATH_txdam.'lib/class.tx_dam_browsetrees.php');
require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');
require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');




/**
 * Inserts the DAM in the element browser.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class tx_dam_browse_media extends browse_links {


	var $damSC = null;


	var $MCONF_name = 'txdam_elbrowser';

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

		if ($type === 'db' AND $pArr[3]=='tx_dam') {
			$isValid = true;
		} elseif ($type === 'file') {
			$isValid = true;
		}

		return $isValid;
	}


	/**
	 * Initializes DAM object which is the base DAM script class.
	 * All the stuff from SC is not needed but the selections stuff is used.
	 *
	 * @return void
	 */
	function initDAM () {

			// load the clickmenu
		$this->doc->getContextMenuCode();

		if (!is_object($this->damSC)) {
			$GLOBALS['LANG']->includeLLFile('EXT:dam/modfunc_file_upload/locallang.xml');
			$this->damSC = t3lib_div::makeInstance('tx_dam_SCbase');
			$this->damSC->MCONF['name'] = $this->MCONF_name;
			$this->damSC->menuConfig();
			$this->damSC->init();
			$this->damSC->doc = & $this->doc;
			$this->damSC->addDocStyles();
		}

		$path = tx_dam::path_makeAbsolute($this->damSC->path);
		if (!$path OR !@is_dir($path))	{
			$path = $this->fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		$this->damSC->path = tx_dam::path_makeRelative($path); // mabe not needed
	}


	/**
	 * Initializes DAM selection.
	 *
	 * @return void
	 */
	function initDAMSelection () {
		global $TYPO3_CONF_VARS;
		

		$this->damSC->addParams = $this->addParams;

		$txdamSel = $this->getModSettings('txdamSel');
		list($txdamSel,$key) = explode(':', $txdamSel);


		if ($txdamSel === '__txdam_current_selection') {
			$MOD_SETTINGS = $GLOBALS['BE_USER']->getModuleData('txdamM1_list', '');
			$selection = $MOD_SETTINGS['tx_dam_select'];
			$this->damSC->selection->sl->setFromSerialized($selection, false);

		} elseif ($txdamSel === '__txdam_stored_selection') {

						// Store settings gui element
			$store = t3lib_div::makeInstance('t3lib_modSettings');
			$store->init('tx_dam_select', 'tx_dam_select');
			$store->initStorage();
			if ($selection = $store->getStoredData($key)) {
				$this->damSC->selection->sl->setFromSerialized($selection['tx_dam_select'], false);
			} else {
				$txdamSel = '';
			}
		}

		if ($txdamSel === '__txdam_eb_selection' OR $txdamSel === '') {
			$this->damSC->selection->processSubmittedSelection();
		}
	}


	/**
	 * Init rendering
	 *
	 * @return	void
	 */
	function renderInit()	{
			// init class browse_links
		$this->init();

			// init the DAM object
		$this->initDAM();

			// processes MOD_SETTINGS
		$this->getModSettings();

			// Processes bparams parameter
		$this->processParams();

			// init the DAM selection after we've got the params
		$this->initDAMSelection();
	}


	/**
	 * Rendering
	 * Called in SC_browse_links::main() when isValid() returns true;
	 *
	 * @param	string		$type Type: "file", ...
	 * @param	object		$pObj Parent object.
	 * @return	string		Rendered content
	 * @see SC_browse_links::main()
	 */
	function render($type, &$pObj)	{
		global $LANG, $BE_USER;

		$this->pObj = &$pObj;
		$pObj->browser = & $this;

		$this->renderInit();

		$content = '';
		$debug   = false;

		switch((string)$type)	{
			case 'rte':
				$content = $this->main_rte();
			break;
			case 'db':
			case 'file':
				$content = $this->main();
			break;
			default:
				$content .= '<h3>ERROR</h3>';
				$content .= '<h3>Unknown or missing mode!</h3>';
				$debug = true;
			break;
		}

			// debug output
		if ($debug OR tx_dam::config_getValue('setup.devel')) {

			$bparams = explode('|', $this->bparams);

			$debugArr = array(
				'act' => $this->act,
				'mode' => $this->mode,
				'thisScript' => $this->thisScript,
				'bparams' => $bparams,
				'allowedTables' => $this->allowedTables,
				'allowedFileTypes' => $this->allowedFileTypes,
				'disallowedFileTypes' => $this->disallowedFileTypes,
				'addParams' => $this->addParams,
				'pointer' => $this->damSC->selection->pointer->page,
				'Selection' => $this->damSC->selection->sl->sel,
				'Query' => $this->damSC->selection->qg->query,
				'QueryArray' => $this->damSC->selection->qg->getQueryParts()
			);
			
			if (t3lib_div::compat_version('4.3')) {
				$debugArr['SLCMD'] = t3lib_div::_GPmerged('SLCMD');
				$debugArr['PM'] = t3lib_div::_GPmerged('PM');
			}
			else {
				$debugArr['SLCMD'] = t3lib_div::GParrayMerged('SLCMD');
				$debugArr['PM'] = t3lib_div::GParrayMerged('PM');
			}

			$this->damSC->debugContent['browse_links'] = '<h4>EB SETTINGS</h4>'.t3lib_div::view_array($debugArr);

			$dbgContent = '<div class="debugContent">'.implode('', $this->damSC->debugContent).'</div>';
			$content.= $this->damSC->buttonToggleDisplay('debug', 'Debug output', $dbgContent);
		}

		return $content;
	}


	/**
	 * Rendering of element browser parts to embed them in other EB's
	 *
	 * @param	string		$type Type: "file", ...
	 * @param	object		$pObj Parent object.
	 * @return	string		Rendered content
	 * @see SC_browse_links::main()
	 */
	function renderPart($type, &$pObj)	{

		$this->pObj = &$pObj;
		$pObj->browser = & $this;

		$this->renderInit();

		$content = '';

		switch((string)$type)	{
			case 'rte_linkfile':
				$content = $this->part_rte_linkfile();
			break;

			default:
				$content .= '<h3>ERROR</h3>';
				$content .= '<h3>Unknown or missing mode!</h3>';
				$debug = true;
			break;
		}

		if (is_object($this->pObj->doc)) {
			$this->pObj->doc->inDocStylesArray = array_merge($this->pObj->doc->inDocStylesArray, $this->doc->inDocStylesArray);
			$this->pObj->doc->JScodeArray = array_merge($this->pObj->doc->JScodeArray, $this->doc->JScodeArray);
		}
		
		return $content;
	}


 	/**
	 * TYPO3 Element Browser: Showing a browse trees and allows you to browse for records
	 *
	 * @return	string		HTML content for the module
	 */
	function main()	{
		global $LANG, $BE_USER, $TYPO3_CONF_VARS;

			// Starting content:
		$content = $this->doc->startPage('TBE file selector');

			// Initializing the action value, possibly removing blinded values etc:
		$allowedItems = array('file', 'upload');
		$allowedItems = array_diff($allowedItems, t3lib_div::trimExplode(',',$this->thisConfig['blindLinkOptions'],1));
		$path = tx_dam::path_makeAbsolute($this->damSC->path);
		if ($this->isReadOnlyFolder($path)) {
			$allowedItems = array_diff($allowedItems, array('upload'));
		}
		if (!in_array($this->act, $allowedItems))	{
			$this->act = 'file';
		}
		$this->reinitParams();


			// Making menu in top:
		$menuDef = array();
		if (in_array('file', $allowedItems)){
			$menuDef['file']['isActive'] = ($this->act === 'file');
			$menuDef['file']['label'] = $LANG->sL('LLL:EXT:dam/mod_main/locallang_mod.xml:mlang_tabs_tab',1);
			$menuDef['file']['url'] = '#';
			$menuDef['file']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=file&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('upload', $allowedItems)) {
			$menuDef['upload']['isActive'] = ($this->act === 'upload');
			$menuDef['upload']['label'] = $LANG->getLL('tx_dam_file_upload.title',1);
			$menuDef['upload']['url'] = '#';
			$menuDef['upload']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=upload&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		$content .= $this->doc->getTabMenuRaw($menuDef);


			// Depending on the current action we will create the actual module content:
		switch($this->act)	{
			case 'file':
				$this->addDisplayOptions();
				$content.= $this->dam_select($this->allowedFileTypes, $this->disallowedFileTypes);
				$content.= $this->damSC->getOptions();
			break;
			case 'upload':
				$content.= $this->dam_upload($this->allowedFileTypes, $this->disallowedFileTypes);
				$content.= $this->damSC->getOptions();
				$content.='<br /><br />';
				if ($BE_USER->isAdmin() || $BE_USER->getTSConfigVal('options.createFoldersInEB'))	{
					$content.= $this->createFolder(tx_dam::path_makeAbsolute($this->damSC->path));
				}
			break;
		}

			// Add some space
		$content.='<br />';

			// Ending page, returning content:
		$content.= $this->doc->endPage();
		$content = $this->damSC->doc->insertStylesAndJS($content);
		return $content;
	}


	/**
	 * Render link file part for RTE
	 */
	function part_rte_linkfile () {
		$this->addDisplayOptions();
		$content.= $this->dam_select($this->allowedFileTypes, $this->disallowedFileTypes);
		$content.= $this->damSC->getOptions();
		return $content;
	}



	/***************************************
	 *
	 *	 Media Browser Module
	 *
	 ***************************************/



 	/**
	 * TYPO3 Element Browser: Showing the DAM trees, allowing you to browse for media records.
	 *
	 * @param	array		$allowedFileTypes Array list of allowed file types
	 * @param	array		$disallowedFileTypes Array list of disallowed file types
	 * @return	string		HTML content for the module
	 */
	function dam_select($allowedFileTypes=array(), $disallowedFileTypes=array())	{
		global $BE_USER, $TYPO3_CONF_VARS;
		
	// workaround for rtehtmlarea
if (is_string($allowedFileTypes)) {
	$allowedFileTypes = explode(',', $allowedFileTypes);
}

		$content = '';

			// the browse trees
		$browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
		$browseTrees->init($this->thisScript, 'elbrowser');
		$trees = $browseTrees->getTrees();


		$files = $this->getFileListArr($allowedFileTypes, $disallowedFileTypes, $this->mode);

		$allowed = array();
		if ($allowedFileTypes) {
			$allowed[] = implode(' ', $allowedFileTypes);
		} else {
			$allowed[] = '*';
		}
		if ($disallowedFileTypes) {
			$allowed[] = implode(' -', $disallowedFileTypes);
		}
		$allowed = implode(' / -', $allowed);

		$fileList = '';
		$fileList .= $allowed ? $this->barheader($allowed.' ') : '<h3 class="bgColor5">&nbsp;</h3>';
		$fileList .= $this->doc->spacer(5);
		$fileList .= $this->renderFileList($files, $this->mode, $this->act);


		$content .= $this->getFormTag();
		// fix for MSIE 80 breaking the table-layout due to nested forms
		$content .= '<form action="#" method="post" id="nested-form-bug"></form>';
		$content .= $this->getSelectionSelector();
		$content .= $this->damSC->getResultInfoBar();



			// Putting the parts together, side by side:
		$content .= '

			<!--
				Wrapper table for folder tree / file list:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBfiles" width="99%">
				<tr>
					<td class="c-wCell" valign="top" width="31%">'.$this->barheader($GLOBALS['LANG']->getLL('folderTree',1).':').$trees.'</td>
					<td valign="top">'.$fileList.'</td>
				</tr>
			</table>
			';

		$content .= '</form>';

			// current selection box
		$content .= $this->getFormTag();

		$selectionBox = '<div style="width:70%;">'.$this->damSC->getCurrentSelectionBox().'</div>';
		$content .= $this->damSC->buttonToggleDisplay('selectionBox', $GLOBALS['LANG']->getLL('selection',1), $selectionBox);
		$content .= '</form>';

		$content .= $this->getFormTag();
		$content .= $this->damSC->getSearchBox('simple', false);
		$content .= '</form>';
		
		return $content;
	}


	/**
	 *
	 */
	function getSelectionSelector () {
		global $TYPO3_CONF_VARS;
		
		$txdamSel = $this->getModSettings('txdamSel');
		list($txdamSelType,$key) = explode(':', $txdamSel);


		$selectionSelector = array();

		$selectionSelector['__txdam_eb_selection'] = $GLOBALS['LANG']->getLL('eb_selection');
		$selectionSelector['__txdam_current_selection'] = $GLOBALS['LANG']->getLL('current_selection');


			// Stored selections
		$store = t3lib_div::makeInstance('t3lib_modSettings');
		$store->init('tx_dam_select', 'tx_dam_select');
		$store->initStorage();
		if (count($store->storedSettings)) {
			$selectionSelector['__txdam_stored_selection-divider'] = '--- '.$GLOBALS['LANG']->getLL('selectionClipboard',1).' ---';
			foreach($store->storedSettings as $storeIndex => $data)	{
				$title = $data['title'];
				$selectionSelector['__txdam_stored_selection:'.$storeIndex] = $title;
			}
		}


		$selectionSelector = t3lib_BEfunc::getFuncMenu($this->addParams, 'SET[txdamSel]', $this->getModSettings('txdamSel'), $selectionSelector);

		$content .= '<div class="infobar-extraline">'.$GLOBALS['LANG']->getLL('selection',1).': '.$selectionSelector.'</div>';

		return $content;
	}


	/**
	 * Render list of files.
	 *
	 * @param	array		List of files. See t3lib_div::getFilesInDir
	 * @param	string		$mode EB mode: "db", "file", ...
	 * @return	string		HTML output
	 */
	function renderFileList($files, $mode='file', $act='') {
		global $LANG, $BACK_PATH, $TCA, $TYPO3_CONF_VARS;

		$out = '';


		// sorting selector
		
		// TODO move to scbase (see tx_dam_list_thumbs too)
		
		$allFields = tx_dam_db::getFieldListForUser('tx_dam');
		if (is_array($allFields) && count($allFields)) {
			$fieldsSelItems=array();
			foreach ($allFields as $field => $title) {
				$fL = is_array($TCA['tx_dam']['columns'][$field]) ? preg_replace('#:$#', '', $GLOBALS['LANG']->sL($TCA['tx_dam']['columns'][$field]['label'])) : '['.$field.']';
				$fieldsSelItems[$field] = t3lib_div::fixed_lgd_cs($fL, 15);
			}
			$sortingSelector = $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:labelSorting',1).' ';
			$sortingSelector .= t3lib_befunc::getFuncMenu($this->addParams, 'SET[txdam_sortField]', $this->damSC->MOD_SETTINGS['txdam_sortField'], $fieldsSelItems);
			
			if($this->damSC->MOD_SETTINGS['txdam_sortRev'])	{
				$params = (array)$this->addParams + array('SET[txdam_sortRev]' => '0');
				$href = t3lib_div::linkThisScript($params);
				$sortingSelector .=  '<button name="SET[txdam_sortRev]" type="button" onclick="self.location.href=\''.htmlspecialchars($href).'\'">'.
						'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/pil2up.gif','width="12" height="7"').' alt="" />'.
						'</button>';
			} else {
				$params = (array)$this->addParams + array('SET[txdam_sortRev]' => '1');
				$href = t3lib_div::linkThisScript($params);
				$sortingSelector .=  '<button name="SET[txdam_sortRev]" type="button" onclick="self.location.href=\''.htmlspecialchars($href).'\'">'.
						'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/pil2down.gif','width="12" height="7"').' alt="" />'.
						'</button>';
			}
			$sortingSelector = $this->getFormTag().$sortingSelector.'</form>';
		}

		$out .= $sortingSelector;
		$out .= $this->doc->spacer(20);

			// Listing the files:
		if (is_array($files) AND count($files))	{

			$displayThumbs = $this->displayThumbs();
			$dragdropImage = ($mode == 'rte' && ($act == 'dragdrop' ||$act == 'media_dragdrop'));
			$addAllJS = '';

				// Traverse the file list:
			$lines=array();
			foreach($files as $fI)	{

				if (!$fI['__exists']) {
					tx_dam::meta_updateStatus ($fI);
					continue;
				}

					// Create file icon:
				$iconFile = tx_dam::icon_getFileType($fI);
				$iconTag = tx_dam_guiFunc::icon_getFileTypeImgTag($fI);
				$iconAndFilename = $iconTag.htmlspecialchars(t3lib_div::fixed_lgd_cs($fI['file_title'], max($GLOBALS['BE_USER']->uc['titleLen'], 120)));


					// Create links for adding the file:
				if (strstr($fI['file_name_absolute'], ',') || strstr($fI['file_name_absolute'], '|'))	{	// In case an invalid character is in the filepath, display error message:
					$eMsg = $LANG->JScharCode(sprintf($LANG->getLL('invalidChar'), ', |'));
					$ATag_insert = '<a href="#" onclick="alert('.$eMsg.');return false;">';

					// If filename is OK, just add it:
				} else {

						// JS: insertElement(table, uid, type, filename, fpath, filetype, imagefile ,action, close)
					$onClick_params = implode (', ', array(
						"'".$fI['_ref_table']."'",
						"'".$fI['_ref_id']."'",
						"'".$mode."'",
						t3lib_div::quoteJSvalue($fI['file_name']),
						t3lib_div::quoteJSvalue($fI['_ref_file_path']),
						"'".$fI['file_type']."'",
						"'".$iconFile."'")
						);
						
					$titleAttrib = tx_dam_guiFunc::icon_getTitleAttribute($fI);

					
					if ($mode === 'rte' AND $act === 'media') {
						$onClick = 'return link_folder(\''.t3lib_div::rawUrlEncodeFP(tx_dam::file_relativeSitePath($fI['_ref_file_path'])).'\');';
						$ATag_insert = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
						
					} elseif (!$dragdropImage) {
						$onClick = 'return insertElement('.$onClick_params.');';
						$ATag_add = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
						$addIcon = $ATag_add.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/plusbullet2.gif', 'width="18" height="16"').' title="'.$LANG->getLL('addToList',1).'" alt="" /></a>';
						
						$onClick = 'return insertElement('.$onClick_params.', \'\', 1);';
						$ATag_insert = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
						
						$addAllJS .= ($mode === 'rte')?'':'insertElement('.$onClick_params.'); ';
					}
				}
				
					// Create link to showing details about the file in a window:
				if ($fI['__exists']) {
					$infoOnClick = 'launchView(\'' . t3lib_div::rawUrlEncodeFP($fI['file_name_absolute']) . '\', \'\'); return false;';
					$ATag_info = '<a href="#" onclick="' . htmlspecialchars($infoOnClick) . '">';
					$info = $ATag_info.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/zoom2.gif', 'width="12" height="12"').' title="'.$LANG->getLL('info',1).'" alt="" /> '.$LANG->getLL('info',1).'</a>';
					$info = '<span class="button">'.$info.'</span>';
				} else {
					$info = '&nbsp;';
				}


					// Thumbnail/size generation:
				$clickThumb = '';
				if ($displayThumbs AND is_file($fI['file_name_absolute']) AND tx_dam_image::isPreviewPossible($fI))	{
					$addAttrib = array();
					$addAttrib['title'] = tx_dam_guiFunc::meta_compileHoverText($fI);
					$clickThumb = tx_dam_image::previewImgTag($fI, '', $addAttrib);
					$clickThumb = '<div class="clickThumb">'.$ATag_insert.$clickThumb.'</a>'.'</div>';
				} elseif ($displayThumbs) {
					$clickThumb = '<div style="width:68px"></div>';
				}
					// Image for drag & drop replaces the thumbnail
				if ($dragdropImage AND t3lib_div::inList($TYPO3_CONF_VARS['GFX']['imagefile_ext'], $fI['file_type']) AND is_file($fI['file_name_absolute']))	{
					if (t3lib_div::_GP('noLimit'))	{
						$maxW=10000;
						$maxH=10000;
					} else {
						$maxW=380;
						$maxH=500;
					}
					$IW = $fI['hpixels'];
					$IH = $fI['vpixels'];
					if ($IW>$maxW)	{
						$IH=ceil($IH/$IW*$maxW);
						$IW=$maxW;
					}
					if ($IH>$maxH)	{
						$IW=ceil($IW/$IH*$maxH);
						$IH=$maxH;
					}
					$clickThumb = '<img src="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').substr($fI['file_name_absolute'],strlen(PATH_site)).'" width="'.$IW.'" height="'.$IH.'"' . ($this->defaultClass?(' class="'.$this->defaultClass.'"'):''). ' alt="'.$fI['alt_text'].'" title="'.$fI[$this->imgTitleDAMColumn].'" txdam="'. $fI['uid'] .'" />';
					$clickThumb = '<div class="clickThumb2">'.$clickThumb.'</div>';
				}

					// Show element:
				$lines[] = '
					<tr>
						<td valign="middle" class="bgColor4" nowrap="nowrap" style="min-width:20em">'.($dragdropImage?'':$ATag_insert).$iconAndFilename.'</a>'.'&nbsp;</td>
						<td valign="middle" class="bgColor4" width="1%">'.($mode == 'rte'?'':$addIcon).'</td>
						<td valign="middle" nowrap="nowrap" width="1%">'.$info.'</td>
					</tr>';


				$infoText = '';
				if ($this->getModSettings('extendedInfo')) {
					$infoText = tx_dam_guiFunc::meta_compileInfoData ($fI, 'file_name, file_size:filesize, _dimensions, caption:truncate:50, instructions', 'table');
					$infoText = str_replace('<table>', '<table border="0" cellpadding="0" cellspacing="1">', $infoText);
					$infoText = str_replace('<strong>', '<strong style="font-weight:normal;">', $infoText);
					$infoText = str_replace('</td><td>', '</td><td class="bgColor-10">', $infoText);
				}


				if (($displayThumbs || $dragdropImage) AND $infoText) {
					$lines[] = '
						<tr class="bgColor">
							<td valign="top" colspan="3">
							<table border="0" cellpadding="0" cellspacing="0"><tr>
								<td valign="top">'.$clickThumb.'</td>
								<td valign="top" style="padding-left:1em">'.$infoText.'</td></tr>
							</table>
							<div style="height:0.5em;"></div>
							</td>
						</tr>';
				} elseif ($clickThumb OR $infoText) {
					$lines[] = '
						<tr class="bgColor">
							<td valign="top" colspan="3" style="padding-left:22px">
							'.$clickThumb.$infoText.'
							<div style="height:0.5em;"></div>
							</td>
						</tr>';
				}

				$lines[] = '
						<tr>
							<td colspan="3"><div style="height:0.5em;"></div></td>
						</tr>';
			}

			// Wrap all the rows in table tags:
		$out .= '



		<!--
			File listing
		-->
				<table border="0" cellpadding="1" cellspacing="0" id="typo3-fileList">
					'.implode('',$lines).'
				</table>';
		}
		
		
		if ($addAllJS) {
			$label = $LANG->getLL('eb_addAllToList', true);
			$titleAttrib = ' title="'.$label.'"';
			$onClick = $addAllJS.'return true;';
			$ATag_add = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
			$addIcon = $ATag_add.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/plusbullet2.gif', 'width="18" height="16"').' alt="" />';
	
			$addAllButton = '<div class="addAllButton"><span class="button"'.$titleAttrib.'>'.$ATag_add.$addIcon.$label.'</a></span></div>';
			$out = $out.$addAllButton;
		}

			// Return accumulated content for filelisting:
		return $out;
	}









	/***************************************
	 *
	 *	 Upload module
	 *
	 ***************************************/



 	/**
	 * Display uploads module
	 *
	 * @param	array		$allowedFileTypes Array list of allowed file types
	 * @param	array		$disallowedFileTypes Array list of disallowed file types
	 * @return	string		HTML content for the module
	 * @todo make use of $allowedFileTypes, $disallowedFileTypes ?
	 */
	function dam_upload($allowedFileTypes=array(), $disallowedFileTypes=array())	{
		global $BE_USER, $FILEMOUNTS, $TYPO3_CONF_VARS;


		$content = '';

		$path = tx_dam::path_makeAbsolute($this->damSC->path);

		if (!$path OR !@is_dir($path) OR !$this->fileProcessor->checkPathAgainstMounts($path) OR $this->isReadOnlyFolder($path)) {
			$path = $this->fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		$this->damSC->path = tx_dam::path_makeRelative($path); // maybe not needed


		if (@is_dir($path))	{

			$content .= '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

					// Upload form
			if ($objRef = $TYPO3_CONF_VARS['SC_OPTIONS']['ext/dam/class.tx_dam_browse_media.php']['upload_modfunc']) {
				$damUploadExtObj = t3lib_div::getUserObj($objRef);
			} else {
				require_once(PATH_txdam.'modfunc_file_upload/class.tx_dam_file_upload.php');
				$damUploadExtObj = t3lib_div::makeInstance('tx_dam_file_upload');
			}
			
				// it may be needed
			require_once(PATH_txdam.'lib/class.tx_dam_guirenderlist.php');
			$this->damSC->guiItems = t3lib_div::makeInstance('tx_dam_guiRenderList');
			
				// init the upload module function
			$this->damSC->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->damSC->MOD_MENU, t3lib_div::_GP('SET'), $this->damSC->MCONF['name'], 'ses');
			$damUploadExtObj->init($this->damSC, array('path' => PATH_thisScript));
			$damUploadExtObj->setEBmode($this);
			

				// call it
			if (is_callable(array($damUploadExtObj,'head')))	$damUploadExtObj->head();
			$form = $damUploadExtObj->main();


				// Create folder tree:
			$browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
			$browseTrees->init($this->thisScript, 'elbrowser', true, true);
			$trees = $browseTrees->getTrees();

			// fix for MSIE 80 breaking the table-layout due to nested forms
			$content .= '<form action="#" method="post" id="nested-form-bug"></form>';

				// Putting the parts together, side by side:
			$content .= '

				<!--
					Wrapper table for folder tree / file list:
				-->
				<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBfiles">
					<tr>
						<td class="c-wCell" valign="top" width="31%">'.$this->barheader($GLOBALS['LANG']->getLL('folderTree',1).':').$trees.'</td>
						<td valign="top" style="padding-right:5px;">'.$form.'</td>
					</tr>
				</table>
				';

			$content.= '</form>';
		}

		return $content;
	}


	/**
	 * Makes a form for creating new folders in the filemount the user is browsing.
	 * The folder creation request is sent to the tce_file.php script in the core which will handle the creation.
	 *
	 * @param	string		Absolute filepath on server in which to create the new folder.
	 * @return	string		HTML for the create folder form.
	 */
	function createFolder($path) {
		
		if ($path!='/' && @is_dir($path))	{
			return parent::createFolder($path);
		}
		return '';
	}





	/***************************************
	 *
	 *	 Collect Data
	 *
	 ***************************************/



	/**
	 * Makes a DAM db query and collects data to be used in EB display
	 *
	 * @param	array		$allowedFileTypes Array list of allowed file types
	 * @param	array		$disallowedFileTypes Array list of disallowed file types
	 * @param	string		$mode EB mode: "db", "file", ...
	 * @return	array		Array of file elements
	 */
 	function getFileListArr ($allowedFileTypes, $disallowedFileTypes, $mode) {
 		global $TCA;

		$filearray = array();

 		//
		// Use the current selection to create a query and count selected records
		//

		$this->damSC->selection->addSelectionToQuery();
		$this->damSC->selection->qg->query['FROM']['tx_dam'] = tx_dam_db::getMetaInfoFieldList(true, array('hpixels','vpixels','caption'));
		#$this->damSC->selection->qg->addSelectFields(...


		//
		// set sorting
		//

		$allFields = tx_dam_db::getFieldListForUser('tx_dam');

		
		if ($this->damSC->MOD_SETTINGS['txdam_sortField'])	{
			if (in_array($this->damSC->MOD_SETTINGS['txdam_sortField'], $allFields))	{
				$orderBy = 'tx_dam.'.$this->damSC->MOD_SETTINGS['txdam_sortField'];
			}
		} else {
			$orderBy = $TCA['tx_dam']['ctrl']['sortby'] ? $TCA['tx_dam']['ctrl']['sortby'] : $TCA['tx_dam']['ctrl']['default_sortby'];
			$orderBy = $GLOBALS['TYPO3_DB']->stripOrderBy($orderBy);
			$this->damSC->MOD_SETTINGS['txdam_sortField'] = $orderBy;
		}
		if ($this->damSC->MOD_SETTINGS['txdam_sortRev'])	$orderBy.=' DESC';
		$this->damSC->selection->qg->addOrderBy($orderBy);
		
		
		//
		// allowed media types
		//
		
		$allowedMediaTypes = array();
		$disallowedMediaTypes = array();

		foreach ($allowedFileTypes as $key => $type) {
			if ($mediaType = tx_dam::convert_mediaType($type)) {
				$allowedMediaTypes[] = $mediaType;
				unset($allowedFileTypes[$key]);
			}
		}
		foreach ($disallowedFileTypes as $key => $type) {
			if ($mediaType = tx_dam::convert_mediaType($type)) {
				$disallowedMediaTypes[] = $mediaType;
				unset($disallowedFileTypes[$key]);
			}
		}

		if ($allowedFileTypes) {
			$extList = implode (',', $GLOBALS['TYPO3_DB']->fullQuoteArray($allowedFileTypes, 'tx_dam'));
			$this->damSC->selection->qg->addWhere('AND tx_dam.file_type IN ('.$extList.')', 'WHERE', 'tx_dam.file_type');
		}
		if ($disallowedFileTypes) {
			$extList = implode (',', $GLOBALS['TYPO3_DB']->fullQuoteArray($disallowedFileTypes, 'tx_dam'));
			$this->damSC->selection->qg->addWhere('AND tx_dam.file_type NOT IN ('.$extList.')', 'WHERE', 'NOT tx_dam.file_type');
		}
		if ($allowedMediaTypes) {
			$extList = implode (',', $GLOBALS['TYPO3_DB']->fullQuoteArray($allowedMediaTypes, 'tx_dam'));
			$this->damSC->selection->qg->addWhere('AND tx_dam.media_type IN ('.$extList.')', 'WHERE', 'tx_dam.media_type');
		}
		if ($disallowedMediaTypes) {
			$extList = implode (',', $GLOBALS['TYPO3_DB']->fullQuoteArray($disallowedMediaTypes, 'tx_dam'));
			$this->damSC->selection->qg->addWhere('AND tx_dam.media_type NOT IN ('.$extList.')', 'WHERE', 'NOT tx_dam.media_type');
		}
		$this->damSC->selection->execSelectionQuery(TRUE);

			// any records found?
		if($this->damSC->selection->pointer->countTotal) {

				// limit query for browsing
			$this->damSC->selection->addLimitToQuery();
			$this->damSC->selection->execSelectionQuery();

			if($this->damSC->selection->res) {
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->damSC->selection->res)) {

					$row = $this->enhanceItemArray($row, $mode);

					$filearray[] = $row;
					if (count($filearray) >= $this->damSC->selection->pointer->itemsPerPage) {
						break;
					}
				}
			}
		}

		return $filearray;
 	}





	/***************************************
	 *
	 *	 Tools
	 *
	 ***************************************/




	function enhanceItemArray($row, $mode) {
		$row['file_title'] = $row['title'] ? $row['title'] : $row['file_name'];
		$row['file_path_absolute'] = tx_dam::path_makeAbsolute($row['file_path']);
		$row['file_name_absolute'] = $row['file_path_absolute'].$row['file_name'];
		$row['__exists'] = @is_file($row['file_name_absolute']);

		if ($mode === 'db') {
			$row['_ref_table'] = 'tx_dam';
			$row['_ref_id'] = $row['uid'];
			$row['_ref_file_path'] = '';
		} else {
			$row['_ref_table'] = '';
			$row['_ref_id'] = t3lib_div::shortMD5($row['file_name_absolute']);
			$row['_ref_file_path'] = $row['file_name_absolute'];
		}

		return $row;
	}






	/**
	 * Create HTML checkbox to enable/disable thumbnail display
	 *
	 * @return	string HTML code
	 */
	function addDisplayOptions() {

		$thumbNailCheckbox = '';

		if ($this->thumbsEnabled()) {
			$thumbNailCheckbox = t3lib_BEfunc::getFuncCheck('', 'SET[displayThumbs]',$this->displayThumbs(), $this->thisScript, t3lib_div::implodeArrayForUrl('',$this->addParams));
			$description = $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:displayThumbs',1);
			$id = 'l'.uniqid('tx_dam_scbase');
			$idAttr = ' id="'.$id.'"';
			$thumbNailCheckbox = str_replace('<input', '<input'.$idAttr, $thumbNailCheckbox);
			$thumbNailCheckbox .= ' <label for="'.$id.'">'.$description.'</label>';
			$this->damSC->addOption('html', 'thumbnailCheckbox', $thumbNailCheckbox);
		}
		$this->damSC->addOption('funcCheck', 'extendedInfo', $GLOBALS['LANG']->getLL('displayExtendedInfo',1));
	}


	/**
	 * Return true or false whether thumbs should be displayed or not
	 *
	 * @return	boolean
	 */
	function displayThumbs() {
		static $displayThumb=NULL;

		if ($displayThumb==NULL) {
				// Getting flag for showing/not showing thumbnails generally:
			$displayThumb = $this->thumbsEnabled();

			if ($displayThumb)	{
				$displayThumb = $this->getModSettings('displayThumbs');
			}
		}
		return $displayThumb;
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
	 * Return $MOD_SETTINGS array
	 *
	 * @param 	string	$key Returns $MOD_SETTINGS[$key] instead of $MOD_SETTINGS
	 * @return	array $MOD_SETTINGS
	 */
	function getModSettings($key='') {
		static $MOD_SETTINGS=NULL;

		if ($MOD_SETTINGS==NULL) {
			$MOD_MENU = array(
				'displayThumbs' => '',
				'extendedInfo' => '',
				'act' => '',
				'mode' => '',
				'bparams' => '',
				'txdamSel' => '',
				'txdam_sortField' => '',
				'txdam_sortRev' => '',
				);
			$settings = t3lib_div::_GP('SET');
				// save params in session
			if ($this->act) $settings['act'] = $this->act;
			if ($this->mode) $settings['mode'] = $this->mode;
			if ($this->bparams) $settings['bparams'] = $this->bparams;


			if (t3lib_div::_GP('SLCMD')) {
				$settings['txdamSel'] = '__txdam_eb_selection';
			}

			$MOD_SETTINGS = $GLOBALS['BE_USER']->getModuleData('txdamM1_list', '');
			$MOD_SETTINGS = is_array($MOD_SETTINGS) ? $MOD_SETTINGS : array();
			$MOD_SETTINGS = array_merge($MOD_SETTINGS, t3lib_BEfunc::getModuleData($MOD_MENU, $settings, $this->MCONF_name));
			$GLOBALS['SOBE']->MOD_SETTINGS = $this->damSC->MOD_SETTINGS = $MOD_SETTINGS;
		}
		if($key) {
			return $MOD_SETTINGS[$key];
		} else {
			return $MOD_SETTINGS;
		}
	}


	/**
	 * Processes bparams parameter
	 * Example value: "data[pages][39][bodytext]|||tt_content|" or "data[tt_content][NEW3fba56fde763d][image]|||gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai|"
	 *
	 * Values:
	 * 0: form field name reference
	 * 1: old/unused?
	 * 2: old/unused?
	 * 3: allowed types. Eg. "tt_content" or "gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai"
	 * 4: allowed file types when tx_dam table. Eg. "gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai"
	 *
	 * @return void
	 */
	function processParams() {

		$this->act = $this->isParamPassed('act') ? $this->act : $this->getModSettings('act');
		$this->mode = $this->isParamPassed('mode') ? $this->mode : $this->getModSettings('mode');
		$this->bparams = $this->isParamPassed('bparams') ? $this->bparams : $this->getModSettings('bparams');

		$this->reinitParams();

		$pArr = explode('|', $this->bparams);
		$this->formFieldName = $pArr[0];

		$this->allowedFileTypes = array();
		$this->disallowedFileTypes = array();

		switch((string)$this->mode)	{
			case 'rte':
			break;
			case 'db':
				$this->allowedTables = $pArr[3];
				if ($this->allowedTables === 'tx_dam') {
					$this->allowedFileTypes = t3lib_div::trimExplode(',', $pArr[4], true);
					$this->disallowedFileTypes = t3lib_div::trimExplode(',', $pArr[5], true);
				}
			break;
			case 'file':
			case 'filedrag':
				$this->allowedTables = '';
				$this->allowedFileTypes = t3lib_div::trimExplode(',', $pArr[3], true);
			break;
			case 'wizard':
			break;
		}

		if ($this->allowedFileTypes) {
			$allAllowed = false;
			foreach ($this->allowedFileTypes as $key => $type) {
				if ($type === '*') {
					$allAllowed = true;
				} elseif (substr($type,0,1) === '-') {
					unset($this->allowedFileTypes[$key]);
					$this->disallowedFileTypes[] = substr($type,1);
				}
			}

			if ($allAllowed) {
				$this->allowedFileTypes = array();
			}
		}

	}


	/**
	 * Check if a param was passed by GET OR POST
	 *
	 * @param string $paramName Param name
	 * @return boolean
	 */
	function isParamPassed ($paramName) {
		return isset($_POST[$paramName]) ? true : isset($_GET[$paramName]);
	}


	/**
	 * Set some variables with the current parameters
	 *
	 * @return void
	 */
	function reinitParams() {
		global $TYPO3_CONF_VARS;

			// needed for browsetrees and just to be save
		$this->addParams = array();
		$GLOBALS['SOBE']->act = $this->addParams['act'] = $this->damSC->addParams['act'] = $this->act;
		$GLOBALS['SOBE']->mode = $this->addParams['mode'] = $this->damSC->addParams['mode'] = $this->mode;
		$GLOBALS['SOBE']->bparams = $this->addParams['bparams'] = $this->damSC->addParams['bparams'] = $this->bparams;
		if (t3lib_div::_GP('SLCMD')) {
			$this->addParams['SET[txdamSel]'] = $this->damSC->addParams['SET[txdamSel]'] = '__txdam_eb_selection';
		}


	}

	function getFormTag($name='') {
		global $TYPO3_CONF_VARS;
		
		return '<form action="'.htmlspecialchars(t3lib_div::linkThisScript($this->addParams)).'" method="post" name="'.$name.'" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';
	}


	/**
	 * Check a config value if its enabled
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'my.option'
	 * @param	mixed 		$default Default value when option is not set
	 * @return boolean
	 * @see tx_dam_SCbase
	 */
	function config_checkValueEnabled($configPath, $default=false) {
		
		return $this->damSC->config_checkValueEnabled($configPath, $default);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_media.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_media.php']);
}

?>
