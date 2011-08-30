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
 * @package DAM-BeLib
 * @subpackage GUI
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   78: class tx_dam_tceFunc
 *
 *              SECTION: Rendering of TCEform fields for common usage
 *  105:     function getSingleField_selectTree($PA, &$fObj)
 *  279:     function getSingleField_selectMounts($PA, &$fObj)
 *  424:     function getSingleField_typeMedia($PA, &$fObj)
 *  573:     function renderFileList($filesArray, $displayThumbs=true, $disabled=false)
 *  695:     function getSingleField_typeFolder($PA, &$fObj)
 *
 *              SECTION: Rendering of TCEform fields for private usage for tx_dam table
 *  779:     function tx_dam_mediaType ($PA, &$fobj)
 *  905:     function tx_dam_file_mime_type ($PA, &$fobj)
 *  932:     function tx_dam_meta($PA, &$fobj)
 * 1028:     function tx_dam_fileUsage ($PA, &$fobj)
 *
 *              SECTION: Form element helper functions
 * 1116:     function array2table($array_in)
 * 1150:     function dbFileIcons($fName, $mode, $allowed, $itemArray, $selector='', $params=array(), $onFocus='', $userEBParam='')
 *
 *              SECTION: Misc helper functions
 * 1335:     function isMMForeignActive()
 *
 * TOTAL FUNCTIONS: 12
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */







require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');

/**
 * Provide TCE and TCEforms functions for usage in own extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
class tx_dam_tceFunc {







	/**********************************************************
	 *
	 * Rendering of TCEform fields for common usage
	 *
	 ************************************************************/






	/**
	 * This will render a selector box element for selecting elements of (category) trees.
	 * Mount points for the trees are checked and the display is limited accordingly.
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	function getSingleField_selectTree($PA, &$fObj)	{
		global $TYPO3_CONF_VARS, $TCA, $LANG;

		$this->tceforms = &$PA['pObj'];

		$table = $PA['table'];
		$field = $PA['field'];
		$row = $PA['row'];
		$config = $PA['fieldConf']['config'];

		$disabled = '';
		if($this->tceforms->renderReadonly || $config['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}


// TODO it seems TCE has a bug and do not work correctly with '1'
$config['maxitems'] = ($config['maxitems']==2) ? 1 : $config['maxitems'];


		$errorMsg = '';
		if (!($config['foreign_table']))	{
			$errorMsg = $PA['table'].'.'.$PA['field'].': "foreign_table" not set! (getSingleField_selectTree)';
		} elseif (!($TCA[$config['foreign_table']]['ctrl']['treeParentField']))	{
			$errorMsg = $PA['table'].'.'.$PA['field'].': "treeParentField" not set for table "'.$config['foreign_table'].'"! (getSingleField_selectTree)';
		}

		if($errorMsg) {
			return $this->tceforms->getSingleField_typeNone_render(array('rows'=>1), $errorMsg);
		}


		//
		// tree generation
		//

			// Selecting the treeViewObj
		$treeViewObj = &t3lib_div::getUserObj($config['treeViewClass'], 'user_', false);
		if($config['treeViewClass'] AND is_object($treeViewObj))      {
 			$treeViewObj->init();

 		} else {
			require_once(PATH_txdam.'lib/class.tx_dam_selprocbase.php');
			$treeViewObj = t3lib_div::makeInstance('tx_dam_browseTree');

			$treeViewObj->table = $config['foreign_table'];
			$treeViewObj->init();
			$treeViewObj->isTCEFormsSelectClass = true;
			$treeViewObj->parentField = $TCA[$config['foreign_table']]['ctrl']['treeParentField'];
			$treeViewObj->fieldArray = array('uid', $TCA[$config['foreign_table']]['ctrl']['label'], $treeViewObj->parentField);

				// default_sortby might be not set
			$defaultSortby = ($TCA[$config['foreign_table']]['ctrl']['default_sortby']) ? $GLOBALS['TYPO3_DB']->stripOrderBy($TCA[$config['foreign_table']]['ctrl']['default_sortby']) : '';
				// sortby might be not set or unset
			$sortby = ($TCA[$config['foreign_table']]['ctrl']['sortby']) ? $TCA[$config['foreign_table']]['ctrl']['sortby'] : '';
				// if we have default_sortby we use it
			$treeViewObj->orderByFields = ($defaultSortby) ? $defaultSortby : $sortby;
		}


		if ($table==$config['foreign_table']) {
			$treeViewObj->TCEforms_nonSelectableItemsArray[] = $row['uid'];
		}
		if($config['treeViewClause']) {
			$treeViewObj->clause = ' '.$config['treeViewClause'];
		}

		$treeName = $treeViewObj->getTreeName();
		$browseTree = array($treeName, $treeViewObj);


		require_once(PATH_txdam.'treelib/class.tx_dam_treelib_tceforms.php');
		$renderBrowseTrees = t3lib_div::makeInstance('tx_dam_treelib_tceforms');
		$renderBrowseTrees->init ($PA, $fObj);
		$renderBrowseTrees->setIFrameTreeBrowserScript($this->tceforms->backPath.PATH_txdam_rel.'mod_treebrowser/index.php');


		if (!$disabled) {
			if ($renderBrowseTrees->isIFrameContentRendering()) {

					// just the trees are needed - we're inside of an iframe!
				$renderBrowseTrees->renderBrowsableTrees($browseTree);
				return $renderBrowseTrees->getTreeContent();

			} elseif ($renderBrowseTrees->isIFrameRendering()) {
				// If we want to display a browseable tree, we need to run the tree in an iframe element
				// In the logic of tceforms the iframe is displayed in the "thumbnails" position
				// In consequence this means that the current function is both responsible for displaying the iframe
				// and displaying the tree. It will be called twice then. Once from alt_doc.php and from dam/mod_treebrowser/index.php

				// Within this if-condition the iframe is written
				// The source of the iframe is dam/mod_treebrowser/index.php which will be called with the current _GET variables
				// In the configuration of the TCA treeViewBrowseable is set to TRUE. The value 'iframeContent' for treeViewBrowseable will
				// be set in dam/mod_treebrowser/index.php as internal configuration logic

				// Uschi: respect $TCA[table]['columns'][field]['config']['itemListStyle']
				// This will enable us to have a higher category selection tree.
				if(isset($config['itemListStyle'])) {
					$tempIframeStyle = t3lib_div::trimExplode(';', $config['itemListStyle']);
					$iframestyle = array();
					foreach($tempIframeStyle as $style) {
						$kv = t3lib_div::trimExplode(':', $style);
						$iframestyle[$kv[0]] = $kv[1];
					}
				}

				$iframeWidth = ($iframestyle['width']) ? $iframestyle['width'] : NULL;
				$iframeHeight = ($iframestyle['height']) ? $iframestyle['height'] : NULL;

				$thumbnails = $renderBrowseTrees->renderIFrame($iframeWidth, $iframeHeight);


			} else {
					// tree frame <div>
				$renderBrowseTrees->renderBrowsableTrees($browseTree);
				$thumbnails = $renderBrowseTrees->renderDivBox();
			}
		}


			// get selected processed items
		# $itemArray = t3lib_div::trimExplode(',',$PA['itemFormElValue']);
		# $itemArray = $renderBrowseTrees->getItemArrayProcessed();
		$itemArray = $renderBrowseTrees->processItemArrayForBrowseableTrees($browseTree);

		//
		// process selected values
		//

			// Creating the label for the "No Matching Value" entry.
		$nMV_label = isset($PA['fieldTSConfig']['noMatchingValue_label']) ? $this->tceforms->sL($PA['fieldTSConfig']['noMatchingValue_label']) : '[ '.$this->tceforms->getLL('l_noMatchingValue').' ]';
		$nMV_label = @sprintf($nMV_label, $PA['itemFormElValue']);

			// Possibly remove some items:
		$removeItems = t3lib_div::trimExplode(',', $PA['fieldTSConfig']['removeItems'], true);
		foreach($itemArray as $tk => $tv) {
			$tvP = explode('|', $tv, 2);
			if (in_array($tvP[0], $removeItems) && !$PA['fieldTSConfig']['disableNoMatchingValueElement'])	{
				$tvP[1] = rawurlencode($nMV_label);
			} elseif (isset($PA['fieldTSConfig']['altLabels.'][$tvP[0]])) {
				$tvP[1] = rawurlencode($this->tceforms->sL($PA['fieldTSConfig']['altLabels.'][$tvP[0]]));
			}
			$itemArray[$tk] = implode('|', $tvP);
		}


		//
		// Rendering and output
		//

		$minitems = t3lib_div::intInRange($config['minitems'], 0);
		$maxitems = t3lib_div::intInRange($config['maxitems'], 0);
		if (!$maxitems)	$maxitems = 100000;
		$selectedListStyle = ($config['selectedListStyle']) ? ' style="' .$config['selectedListStyle'] .'"' : ' style="width:200px"';

		$this->tceforms->requiredElements[$PA['itemFormElName']] = array($minitems, $maxitems, 'imgName' => $table.'_'.$row['uid'].'_'.$field);



		$item = '';
		$item .= '<input type="hidden" name="'.$PA['itemFormElName'].'_mul" value="'.($config['multiple']?1:0).'"'.$disabled.' />';

		$params = array(
			'size' => ($disabled ? count($disabled) : $config['size']),
			'autoSizeMax' => t3lib_div::intInRange($config['autoSizeMax'], 0),
			'style' => $selectedListStyle,
			'dontShowMoveIcons' => ($maxitems<=1),
			'maxitems' => $maxitems,
			'info' => '',
			'headers' => array(
				'selector' => $this->tceforms->getLL('l_selected').':<br />',
				'items' => ($disabled ? '': $this->tceforms->getLL('l_items').':<br />')
			),
			'noBrowser' => true,
			'readOnly' => $disabled,
			'thumbnails' => $thumbnails
		);

		$item .= $this->tceforms->dbFileIcons($PA['itemFormElName'], $config['internal_type'], $config['allowed'], $itemArray, '', $params, $PA['onFocus']);

			// Wizards:
		if (!$disabled) {
			$specConf = $this->tceforms->getSpecConfFromString($PA['extra'], $PA['fieldConf']['defaultExtras']);
			$altItem = '<input type="hidden" name="'.$PA['itemFormElName'].'" value="'.htmlspecialchars($PA['itemFormElValue']).'" />';
			$item = $this->tceforms->renderWizards(array($item, $altItem), $config['wizards'], $table, $row, $field, $PA, $PA['itemFormElName'], $specConf);
		}
		return $item;
	}


	/**
	 * This will render a selector box element for selecting elements of (category) trees.
	 * Depending on the tree it display full trees or root elements only for selecting mounts points for trees.
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	function getSingleField_selectMounts($PA, &$fObj)	{
		global $TYPO3_CONF_VARS, $TCA, $LANG;

		$this->tceforms = &$PA['pObj'];

		$table = $PA['table'];
		$field = $PA['field'];
		$row = $PA['row'];
		$config = $PA['fieldConf']['config'];

		$disabled = '';
		if($this->tceforms->renderReadonly || $config['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}


// TODO it seems TCE has a bug and do not work correctly with '1'
$config['maxitems'] = ($config['maxitems']==2) ? 1 : $config['maxitems'];



		//
		// tree generation
		//

		require_once(PATH_txdam.'lib/class.tx_dam_browsetrees.php');
		$browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
		$browseTrees->init('', 'tceformsSelect');

		require_once(PATH_txdam.'treelib/class.tx_dam_treelib_tceforms.php');
		$renderBrowseTrees = t3lib_div::makeInstance('tx_dam_treelib_tceforms');
		$renderBrowseTrees->init ($PA, $fObj);
		$renderBrowseTrees->setIFrameTreeBrowserScript($this->tceforms->backPath.PATH_txdam_rel.'mod_treebrowser/index.php');

		$renderBrowseTrees->renderBrowsableMountTrees($browseTrees->treeObjArr);


		if (!$disabled) {
			if ($renderBrowseTrees->isIFrameContentRendering()) {

					// just the trees are needed - we're inside of an iframe!
				$renderBrowseTrees->renderBrowsableMountTrees($browseTrees->treeObjArr);
				return $renderBrowseTrees->getTreeContent();

			} elseif ($renderBrowseTrees->isIFrameRendering()) {
				// If we want to display a browseable tree, we need to run the tree in an iframe element
				// In the logic of tceforms the iframe is displayed in the "thumbnails" position
				// In consequence this means that the current function is both responsible for displaying the iframe
				// and displaying the tree. It will be called twice then. Once from alt_doc.php and from dam/mod_treebrowser/index.php

				// Within this if-condition the iframe is written
				// The source of the iframe is dam/mod_treebrowser/index.php which will be called with the current _GET variables
				// In the configuration of the TCA treeViewBrowseable is set to TRUE. The value 'iframeContent' for treeViewBrowseable will
				// be set in dam/mod_treebrowser/index.php as internal configuration logic

				$thumbnails = $renderBrowseTrees->renderIFrame();

			} else {
					// tree frame <div>
				$renderBrowseTrees->renderBrowsableMountTrees($browseTrees->treeObjArr);
				$thumbnails = $renderBrowseTrees->renderDivBox();
			}
		}

			// get selected processed items
		# $itemArray = t3lib_div::trimExplode(',',$PA['itemFormElValue']);
		# $itemArray = $renderBrowseTrees->getItemArrayProcessed();
		$itemArray = $renderBrowseTrees->processItemArrayForBrowseableTrees($browseTrees->treeObjArr);



		//
		// process selected values
		//

			// Creating the label for the "No Matching Value" entry.
		$nMV_label = isset($PA['fieldTSConfig']['noMatchingValue_label']) ? $this->tceforms->sL($PA['fieldTSConfig']['noMatchingValue_label']) : '[ '.$this->tceforms->getLL('l_noMatchingValue').' ]';
		$nMV_label = @sprintf($nMV_label, $PA['itemFormElValue']);

			// Possibly remove some items:
		$removeItems = t3lib_div::trimExplode(',', $PA['fieldTSConfig']['removeItems'], true);
		foreach($itemArray as $tk => $tv) {
			$tvP = explode('|', $tv, 2);
			if (in_array($tvP[0], $removeItems) && !$PA['fieldTSConfig']['disableNoMatchingValueElement'])	{
				$tvP[1] = rawurlencode($nMV_label);
			} elseif (isset($PA['fieldTSConfig']['altLabels.'][$tvP[0]])) {
				$tvP[1] = rawurlencode($this->tceforms->sL($PA['fieldTSConfig']['altLabels.'][$tvP[0]]));
			}
			$itemArray[$tk] = implode('|', $tvP);
		}


		//
		// Rendering and output
		//

		$minitems = t3lib_div::intInRange($config['minitems'], 0);
		$maxitems = t3lib_div::intInRange($config['maxitems'], 0);
		if (!$maxitems)	$maxitems = 100000;

		$this->tceforms->requiredElements[$PA['itemFormElName']] = array($minitems, $maxitems, 'imgName' => $table.'_'.$row['uid'].'_'.$field);



		$item = '';
		$item .= '<input type="hidden" name="'.$PA['itemFormElName'].'_mul" value="'.($config['multiple']?1:0).'"'.$disabled.' />';

		$params = array(
			'size' => $config['size'],
			'autoSizeMax' => t3lib_div::intInRange($config['autoSizeMax'], 0),
			'style' => ' style="width:200px;"',
			'dontShowMoveIcons' => ($maxitems<=1),
			'maxitems' => $maxitems,
			'info' => '',
			'headers' => array(
				'selector' => $this->tceforms->getLL('l_selected').':<br />',
				'items' => ($disabled ? '': $this->tceforms->getLL('l_items').':<br />')
			),
			'noBrowser' => true,
			'readOnly' => $disabled,
			'thumbnails' => $thumbnails
		);
		$item .= $this->tceforms->dbFileIcons($PA['itemFormElName'], $config['internal_type'], $config['allowed'], $itemArray, '', $params, $PA['onFocus']);


			// Wizards:
		if (!$disabled) {
			$specConf = $this->tceforms->getSpecConfFromString($PA['extra'], $PA['fieldConf']['defaultExtras']);
			$altItem = '<input type="hidden" name="'.$PA['itemFormElName'].'" value="'.htmlspecialchars($PA['itemFormElValue']).'" />';
			$item = $this->tceforms->renderWizards(array($item, $altItem), $config['wizards'], $table, $row, $field, $PA, $PA['itemFormElName'], $specConf);
		}

		return $item;
	}





	/**
	 * Generation of TCEform element of the type "group" for media elements.
	 * This is used to select media records in eg. tt_content.
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	function getSingleField_typeMedia($PA, &$fObj)	{
		global $TYPO3_CONF_VARS;

		$this->tceforms = &$PA['pObj'];


		if(!(($msg = $this->isMMForeignActive())===true)) {
			return $this->tceforms->getSingleField_typeNone_render(array('rows'=>1), $msg);
		}


		$table = $PA['table'];
		$field = $PA['field'];
		$row = $PA['row'];
		$config = $PA['fieldConf']['config'];

		$disabled = '';
		if($this->tceforms->renderReadonly || $config['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}

		$minitems = t3lib_div::intInRange($config['minitems'], 0);
		$maxitems = t3lib_div::intInRange($config['maxitems'], 0);
		if (!$maxitems)	$maxitems = 100000;

		$this->tceforms->requiredElements[$PA['itemFormElName']] = array($minitems, $maxitems, 'imgName' => $table.'_'.$row['uid'].'_'.$field);

		$item = '';
		$item .= '<input type="hidden" name="'.$PA['itemFormElName'].'_mul" value="'.($config['multiple']?1:0).'"'.$disabled.' />';

		$info = '';

			// Acting according to either "file" or "db" type:
		switch((string)$config['internal_type'])	{
			case 'db':	// If the element is of the internal type "db":

					// Creating string showing allowed types:
				$tempFT_db = t3lib_div::trimExplode(',', $config['allowed'], true);
				while(list(, $theT)=each($tempFT_db))	{
					if ($theT)	{
						$info .= '<span class="nobr">&nbsp;&nbsp;&nbsp;&nbsp;'.
								t3lib_iconWorks::getIconImage($theT, array(), $this->tceforms->backPath, 'align="top"').
								$this->tceforms->sL($GLOBALS['TCA'][$theT]['ctrl']['title'], true).
								'</span><br />';
					}
				}

					// Creating string showing allowed types:
				$tempFT = t3lib_div::trimExplode(',', $config['allowed_types'], true);
				if (!count($tempFT))	{$info .= '*';}
				foreach($tempFT as $ext)	{
					if ($ext)	{
						$info .= strtoupper($ext).' ';
					}
				}

					// Creating string, showing disallowed types:
				$tempFT_dis = t3lib_div::trimExplode(',', $config['disallowed_types'], true);
				if (count($tempFT_dis))	{$info .= '<br />';}
				foreach($tempFT_dis as $ext)	{
					if ($ext)	{
						$info .= '-'.strtoupper($ext).' ';
					}
				}



					// Collectiong file items:
				$itemArray = array();
				$filesArray = array();
				if(intval($row['uid'])) {
					$filesArray = tx_dam_db::getReferencedFiles($table, $row['uid'], $config['MM_match_fields'], $config['MM'], 'tx_dam.*');
					foreach($filesArray['rows'] as $row)	{
						$itemArray[] = array('table'=>'tx_dam', 'id' => $row['uid'], 'title' => ($row['title']?$row['title']:$row['file_name']));
					}
				}

				$thumbsnails = $this->renderFileList($filesArray, $config['show_thumbs']);
/*
					// making thumbnails
				$thumbsnails = '';
				if ($config['show_thumbs'] AND count($filesArray))	{

					foreach($filesArray['rows'] as $row)	{

							// Icon
						$absFilePath = tx_dam::file_absolutePath($row);
						$fileExists = @file_exists($absFilePath);

						$addAttrib = 'class="absmiddle"';
						$addAttrib .= tx_dam_guiFunc::icon_getTitleAttribute($row);
						$fileIcon = tx_dam::icon_getFileTypeImgTag($row, $addAttrib);


							// add clickmenu
						if ($fileExists AND !$disabled) {
#							$fileIcon = $this->tceforms->getClickMenu($fileIcon, $absFilePath);
							$fileIcon = $this->tceforms->getClickMenu($fileIcon, 'tx_dam', $row['uid']);
						}

						$title = t3lib_div::fixed_lgd_cs($this->tceforms->noTitle($row['title']), $this->tceforms->titleLen);

						$thumb = tx_dam_image::previewImgTag($row, '', 'align="middle"');

						$thumbDescr = '<div class="nobr">'.$fileIcon.$title.'<br />'.$row['file_name'].'</div>';

						$thumbsnails .= '<tr><td>'.$thumb.'</td><td>'.$thumbDescr.'</td></tr>';
					}
					$thumbsnails = '<table border="0">'.$thumbsnails.'</table>';
				}
*/

					// Creating the element:
				$params = array(
					'size' => intval($config['size']),
					'dontShowMoveIcons' => ($maxitems<=1),
					'autoSizeMax' => t3lib_div::intInRange($config['autoSizeMax'], 0),
					'maxitems' => $maxitems,
					'style' => isset($config['selectedListStyle']) ? ' style="'.htmlspecialchars($config['selectedListStyle']).'"' : ' style="'.$this->tceforms->defaultMultipleSelectorStyle.'"',
					'info' => $info,
					'thumbnails' => $thumbsnails,
					'readOnly' => $disabled
				);

					// Extra parameter for DAM element browser
				$user_eb_param = $config['allowed_types'];
				$item .= $this->dbFileIcons($PA['itemFormElName'], 'db', implode(',', $tempFT_db), $itemArray, '', $params, $PA['onFocus'], $user_eb_param);
			break;
		}

			// Wizards:
		if (!$disabled) {
			$specConf = $this->tceforms->getSpecConfFromString($PA['extra'], $PA['fieldConf']['defaultExtras']);
			$altItem = '<input type="hidden" name="'.$PA['itemFormElName'].'" value="'.htmlspecialchars($PA['itemFormElValue']).'" />';
			$item = $this->tceforms->renderWizards(array($item, $altItem), $config['wizards'], $table, $row, $field, $PA, $PA['itemFormElName'], $specConf);
		}

		return $item;
	}


	/**
	 * Render list of files.
	 *
	 * @param	array		$filesArray List of files. See tx_dam_db::getReferencedFiles
	 * @param	boolean		$displayThumbs
	 * @param	boolean		$disabled
	 * @return	string		HTML output
	 */
	function renderFileList($filesArray, $displayThumbs=true, $disabled=false) {
		global $LANG;


		$out = '';

			// Listing the files:
		if (is_array($filesArray) && count($filesArray))	{

			$lines=array();
			foreach($filesArray['rows'] as $row)	{

				$absFilePath = tx_dam::file_absolutePath($row);
				$fileExists = @file_exists($absFilePath);


				$addAttrib = 'class="absmiddle"';
				$addAttrib .= tx_dam_guiFunc::icon_getTitleAttribute($row);
				$iconTag = tx_dam::icon_getFileTypeImgTag($row, $addAttrib);


					// add clickmenu
				if ($fileExists && !$disabled) {
#							$fileIcon = $this->tceforms->getClickMenu($fileIcon, $absFilePath);
					$iconTag = $this->tceforms->getClickMenu($iconTag, 'tx_dam', $row['uid']);
				}

				$title = $row['title'] ? t3lib_div::fixed_lgd_cs($row['title'], $this->tceforms->titleLen) : t3lib_BEfunc::getNoRecordTitle();

					// Create link to showing details about the file in a window:
				if ($fileExists) {
					#$Ahref = $GLOBALS['BACK_PATH'].'show_item.php?table='.rawurlencode($absFilePath).'&returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
					$onClick = 'top.launchView(\'tx_dam\', \''.$row['uid'].'\');';
					$onClick = 'top.launchView(\''.$absFilePath.'\');';
					$ATag_info = '<a href="#" onclick="'.htmlspecialchars($onClick).'">';
					$info = $ATag_info.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/zoom2.gif', 'width="12" height="12"').' title="'.$LANG->getLL('info',1).'" alt="" /> '.$LANG->getLL('info',1).'</a>';

				} else {
					$info = '&nbsp;';
				}

					// Thumbnail/size generation:
				$clickThumb = '';
				if ($displayThumbs && $fileExists && tx_dam_image::isPreviewPossible($row))	{
					$clickThumb = tx_dam_image::previewImgTag($row);
					$clickThumb = '<div class="clickThumb">'.$clickThumb.'</div>';
				} elseif ($displayThumbs) {
					$clickThumb = '<div style="width:68px"></div>';
				}


					// Show element:
				$lines[] = '
					<tr class="bgColor4">
						<td valign="top" nowrap="nowrap" style="min-width:20em">'.$iconTag.htmlspecialchars($title).'&nbsp;</td>
						<td valign="top" nowrap="nowrap" width="1%">'.$info.'</td>
					</tr>';


				$infoText = tx_dam_guiFunc::meta_compileInfoData ($row, 'file_name, file_size:filesize, _dimensions, caption:truncate:50', 'table');
				$infoText = str_replace('<table>', '<table border="0" cellpadding="0" cellspacing="1">', $infoText);
				$infoText = str_replace('<strong>', '<strong style="font-weight:normal;">', $infoText);
				$infoText = str_replace('</td><td>', '</td><td class="bgColor-10">', $infoText);


				if ($displayThumbs) {
					$lines[] = '
						<tr class="bgColor">
							<td valign="top" colspan="2">
							<table border="0" cellpadding="0" cellspacing="0"><tr>
								<td valign="top">'.$clickThumb.'</td>
								<td valign="top" style="padding-left:1em">'.$infoText.'</td></tr>
							</table>
							<div style="height:0.5em;"></div>
							</td>
						</tr>';
				} else {
					$lines[] = '
						<tr class="bgColor">
							<td valign="top" colspan="2" style="padding-left:22px">
							'.$infoText.'
							<div style="height:0.5em;"></div>
							</td>
						</tr>';
				}

				$lines[] = '
						<tr>
							<td colspan="2"><div style="height:0.5em;"></div></td>
						</tr>';
			}

				// Wrap all the rows in table tags:
			$out .= '

		<!--
			File listing
		-->
				<table border="0" cellpadding="1" cellspacing="1">
					'.implode('',$lines).'
				</table>';
		}

			// Return accumulated content for filelisting:
		return $out;
	}





	/**
	 * Generation of TCEform elements of the type "group"
	 * This will render a selectorbox into which elements from either the file system or database can be inserted. Relations.
	 *
	 * @param	string		The table name of the record
	 * @param	string		The field name which this element is supposed to edit
	 * @param	array		The record data array where the value(s) for the field can be found
	 * @param	array		An array with additional configuration options.
	 * @return	string		The HTML code for the TCEform field
	 */
	function getSingleField_typeFolder($PA, &$fObj)	{
		global $TYPO3_CONF_VARS, $TCA, $LANG;

		$this->tceforms = &$PA['pObj'];

		$table = $PA['table'];
		$field = $PA['field'];
		$row = $PA['row'];
		$config = $PA['fieldConf']['config'];

		$disabled = '';
		if($this->tceforms->renderReadonly || $config['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}

			// Init:
		$size = intval($config['size']);
		$maxitems = t3lib_div::intInRange($config['maxitems'],0);
		if (!$maxitems)	$maxitems=100000;
		$minitems = t3lib_div::intInRange($config['minitems'],0);

		$disabled = '';
		if($this->tceforms->renderReadonly || $config['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}

		$item.= '<input type="hidden" name="'.$PA['itemFormElName'].'_mul" value="'.($config['multiple']?1:0).'"'.$disabled.' />';
		$this->tceforms->requiredElements[$PA['itemFormElName']] = array($minitems,$maxitems,'imgName'=>$table.'_'.$row['uid'].'_'.$field);
		$info='';

			// "Extra" configuration; Returns configuration for the field based on settings found in the "types" fieldlist. See http://typo3.org/documentation/document-library/doc_core_api/Wizards_Configuratio/.
		$specConf = $this->tceforms->getSpecConfFromString($PA['extra'], $PA['fieldConf']['defaultExtras']);



			// Making the array of file items:
		$itemArray = t3lib_div::trimExplode(',',$PA['itemFormElValue'],1);

			// Creating the element:
		$params = array(
			'size' => $size,
			'dontShowMoveIcons' => ($maxitems<=1),
			'autoSizeMax' => t3lib_div::intInRange($config['autoSizeMax'],0),
			'maxitems' => $maxitems,
			'style' => isset($config['selectedListStyle']) ? ' style="'.htmlspecialchars($config['selectedListStyle']).'"' : ' style="'.$this->tceforms->defaultMultipleSelectorStyle.'"',
			'info' => $info,
			'thumbnails' => '',
			'readOnly' => $disabled
		);
		$item.= $this->dbFileIcons($PA['itemFormElName'],'folder|tx_dam_folder','',$itemArray,'',$params,$PA['onFocus']);

			// Wizards:
		$altItem = '<input type="hidden" name="'.$PA['itemFormElName'].'" value="'.htmlspecialchars($PA['itemFormElValue']).'" />';
		if (!$disabled) {
			$item = $this->tceforms->renderWizards(array($item,$altItem),$config['wizards'],$table,$row,$field,$PA,$PA['itemFormElName'],$specConf);
		}

		return $item;
	}








	/**********************************************************
	 *
	 * Rendering of TCEform fields for private usage for tx_dam table
	 *
	 ************************************************************/





	/**
	 * Renders header table row with media type and previewer
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	function tx_dam_mediaType ($PA, &$fobj) {
		global $TCA;

		$this->tceforms = &$PA['pObj'];
		$config = $PA['fieldConf']['config'];
		$row = $PA['row'];
		$table = $PA['table'];

// TODO overlay all fields to be safe
		foreach (array('media_type', 'file_name', 'file_path', 'file_size', 'hpixels', 'vpixels') as $field) {
			$row[$field] = $this->tceforms->getLanguageOverlayRawValue($table, $row, $field, $TCA[$table]['columns'][$field]);
		}

		$itemMediaInfo = '';
		$itemMediaInfo .= '<div class="tableRow">'.$this->tceforms->sL('LLL:EXT:lang/locallang_general.xml:LGL.title', true).'<br />'.
					'<strong>'.htmlspecialchars($row['title']).'</strong></div>';

		$itemMediaInfo .= '<div class="tableRow">'.$this->tceforms->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name', true).'<br />'.
					'<strong>'.htmlspecialchars($row['file_name']).'</strong></div>';

		$itemMediaInfo .= '<div class="tableRow">'.$this->tceforms->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_path', true).'<br />'.
					'<strong>'.htmlspecialchars($row['file_path']).'</strong></div>';

		if ($row['media_type'] == TXDAM_mtype_image) {
			$out = '';
			$out .= $row['hpixels'] ? $row['hpixels'].'x'.$row['vpixels'].' px, ' : '';
			$out .= t3lib_div::formatSize($row['file_size']);
			$out .= $row['color_space'] ? ', '.$this->tceforms->sL(t3lib_befunc::getLabelFromItemlist($PA['table'], 'color_space', $row['color_space']), true) : '';

			$itemMediaInfo .= '<div class="tableRow"><nobr>'.htmlspecialchars($out).'</nobr></div>';
		}

		$itemMediaTypeIcon = tx_dam_guiFunc::getMediaTypeIconBox($row);

		$itemMediaInfoTable = '
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top">'.$itemMediaTypeIcon.'</td>
					<td valign="top" align="left" style="padding-left:25px;">'.
						$itemMediaInfo.'
					</td>
				</tr>
			</table>';


		$fieldTemplate = '
			<tr>
				<td colspan="2"><img src="clear.gif" width="1" height="5" alt="" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><img name="req_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="clear.gif" width="10" height="10" alt="" /><img name="cm_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="clear.gif" width="7" height="10" alt="" /></td>
				<td valign="top">###FIELD_ITEM######FIELD_PAL_LINK_ICON###</td>
			</tr>
			<tr>
				<td colspan="2"><img src="clear.gif" width="1" height="15" alt="" /></td>
			</tr>
			';

		$itemMediaInfoTable = $this->tceforms->intoTemplate( array(
					'NAME'=>'',
					'ID' => $row['uid'],
					'FIELD' => $PA['field'],
					'TABLE' => $PA['table'],
					'ITEM' => $itemMediaInfoTable,
					'HELP_ICON' => ''
				//	'HELP_ICON' => $this->tceforms->helpTextIcon($PA['table'], $PA['field'], true)
				),
				$fieldTemplate);



		//
		// previewer
		//

		$itemPreviewer = '';
		$headerCode = '';

		$previewer = NULL;
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['previewerClasses']))	{
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['previewerClasses'] as $idName => $classRessource)	{
				if (is_object($previewer = t3lib_div::getUserObj($classRessource)))      {
					if ($previewer->isValid($row, '200', 'topright')) {
						$outArr = $previewer->render($row, '200', 'topright');
						$itemPreviewer = $outArr['htmlCode'];
						$headerCode = $outArr['headerCode'];
						break;
					}
				}
			}
			unset($previewer);
			$previewer = NULL;
		}
// todo: header code should go into header - really - but how

		//
		// all together now
		//

		$out = '
			<tr>
				<td colspan="2">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
								<table border="0" cellpadding="0" cellspacing="0">'.
									$itemMediaInfoTable.'
								</table>
							</td>
							<td width="1%" valign="top" align="center" style="padding: 0px 10px 0px 10px">'.$headerCode.$itemPreviewer.'</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><img src="clear.gif" width="1" height="5" alt="" /></td>
			</tr>';


		return $out;
	}


	/**
	 * Renders merged mime type field
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	function tx_dam_file_mime_type ($PA, &$fobj) {
		global $TCA;

		$this->tceforms = &$PA['pObj'];

		if($PA['fieldConf']['config']['readOnly']) {
			$row = array();
			foreach (array('file_mime_type', 'file_mime_subtype') as $field) {
				$row[$field] = $this->tceforms->getLanguageOverlayRawValue($PA['table'], $PA['row'], $field, $TCA[$PA['table']]['columns'][$field]);
			}
			$PA['itemFormElValue'] = $row['file_mime_type'].'/'.$row['file_mime_subtype'];
			$out = $this->tceforms->getSingleField_typeNone($PA['table'], $PA['field'], $PA['row'], $PA);
		} else {
			$out = $this->tceforms->getSingleField_typeInput($PA['table'], $PA['field'], $PA['row'], $PA);
		}

		return $out;
	}


	/**
	 * This will render the field "meta" as table from it's xml content which has eg. EXIF data in it.
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	function tx_dam_meta($PA, &$fobj)	{
		global $TYPO3_CONF_VARS;

			// description of exif data
		$metaDesc['EXIF']['ExposureMode'] = array(
		   'Auto Exposure',
		   'Manual Exposure',
		   'Auto bracket');
		$metaDesc['EXIF']['MeteringMode'] = array(
		   'unknown',
		   'Average',
		   'CenterWeightedAverage',
		   'Spot',
		   'MultiSpot',
		   'Pattern',
		   'Partial');
		$metaDesc['EXIF']['SensingMethod'] = array(
		   '',
		   'Not defined',
		   'One-chip color area sensor',
		   'Two-chip color area sensor',
		   'Three-chip color area sensor',
		   'Color sequential area sensor',
		   'Trilinear sensor',
		   'Color sequential linear sensor');
		$metaDesc['EXIF']['SubjectDistanceRange'] = array(
		   'unknown',
		   'Macro',
		   'Close view',
		   'Distant view');
		$metaDesc['EXIF']['ExposureProgram'] = array(
		   'Not defined',
		   'Manual',
		   'Normal program',
		   'Aperture priority',
		   'Shutter priority',
		   'Creative program',
		   'Action program',
		   'Portrait mode',
		   'Landscape mode');


		$this->tceforms = &$PA['pObj'];

			// Init:
		$config = $PA['fieldConf']['config'];
		$config['pass_content'] = true;
		$config['fixedRows'] = true;


		$wantedCharset = $TYPO3_CONF_VARS['BE']['forceCharset'] ? $TYPO3_CONF_VARS['BE']['forceCharset'] : 'iso-8859-1';
		if(!$wantedCharset === 'utf-8') {
			$csConvObj = t3lib_div::makeInstance('t3lib_cs');
		}

		$data = t3lib_div::xml2array($PA['itemFormElValue']);

		$content = '';
		if(is_array($data)) {
			foreach($data as $key => $value) {

				if(is_array($value) AND count($value) AND !isset($value['xml'])) { // skip raw xml

						// convert pure data to human readable
					if (is_array($metaDesc[$key])) {	// exif, iptc

						foreach ($value as $dataKey => $dataVal) {
							$key = strtoupper($key);
							if (is_array($metaDesc[$key][$dataKey])) {	// description found?
								$value[$dataKey] = $metaDesc[$key][$dataKey][$dataVal];	// apply
							}
						}
					}

					$content .= '<h4>'.strtoupper($key).'</h4>';
					if(is_object($csConvObj)) {
						$csConvObj->convArray($value, 'utf-8', $wantedCharset);
					}
					$content .= $this->array2table($value);
				}
			}
		} else {
				// if decoding went wrong just show the content
			$content = nl2br(htmlspecialchars($PA['itemFormElValue']));
		}
		return $this->tceforms->getSingleField_typeNone_render($config, $content);
	}

	/**
	 * Renders file usage field
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	string		The HTML code for the TCEform field
	 */
	function tx_dam_fileUsage ($PA, $fobj) {
		return tx_dam_guiFunc::getReferencesTable($PA['row']['uid']);
	}



	/************************************************************
	 *
	 * Form element helper functions
	 *
	 ************************************************************/





	/**
	 * Returns HTML-code, which is a visual representation of a multidimensional array
	 *
	 * @param	array		Array to view
	 * @return	string		HTML output
	 */
	function array2table($array_in)	{
		if (is_array($array_in))	{
			$result = '<table border="0" cellpadding="1" cellspacing="2">';
			if (!count($array_in))	{$result .= '<tr><td class="bgColor5"></td></tr>';}
			while (list($key, $val)=each($array_in))	{
				$result .= '<tr><td class="bgColor5">'.htmlspecialchars((string)$key).'</td>';
				$result .= '<td class="bgColor4">';
				if (is_array($array_in[$key]))	{
					$result .= 'array'; #$this->array2table($array_in[$key]);
				} else
					$result .= nl2br(htmlspecialchars((string)$val));
				$result .= '</td></tr>';
			}
			$result .= '</table>';
		} else	{
			$result = false;
		}
		return $result;
	}


	/**
	 * Prints the selector box form-field for the db/file/select elements (multiple)
	 *
	 * @param	string		Form element name
	 * @param	string		Mode "db", "file" (internal_type for the "group" type) OR blank (then for the "select" type). Seperated with '|' a user defined mode can be set to be passed as param to the EB.
	 * @param	string		Commalist of "allowed"
	 * @param	array		The array of items. For "select" and "group"/"file" this is just a set of value. For "db" its an array of arrays with table/uid pairs.
	 * @param	string		Alternative selector box.
	 * @param	array		An array of additional parameters, eg: "size", "info", "headers" (array with "selector" and "items"), "noBrowser", "thumbnails"
	 * @param	string		On focus attribute string
	 * @param	string		$user_el_param Additional parameter for the EB
	 * @return	string		The form fields for the selection.
	 */
	function dbFileIcons($fName, $mode, $allowed, $itemArray, $selector='', $params=array(), $onFocus='', $userEBParam='')	{

		list($mode, $modeEB) = explode('|', $mode);
		$modeEB = $modeEB ? $modeEB : $mode;

		$disabled = '';
		if($this->tceforms->renderReadonly || $params['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}

			// Sets a flag which means some JavaScript is included on the page to support this element.
		$this->tceforms->printNeededJS['dbFileIcons']=1;

			// INIT
		$uidList=array();
		$opt=array();
		$itemArrayC=0;

			// Creating <option> elements:
		if (is_array($itemArray))	{
			$itemArrayC=count($itemArray);
			reset($itemArray);
			switch($mode)	{
				case 'db':
					while(list(,$pp)=each($itemArray))	{
						if($pp['title']) {
							$pTitle = $pp['title'];
						} else {
							if (function_exists('t3lib_BEfunc::getRecordWSOL')) {
								$pRec = t3lib_BEfunc::getRecordWSOL($pp['table'], $pp['id']);
							} else {
								$pRec = t3lib_BEfunc::getRecord($pp['table'], $pp['id']);
							}
							$pTitle = is_array($pRec) ? $pRec[$GLOBALS['TCA'][$pp['table']]['ctrl']['label']] : NULL;
						}
						if ($pTitle)	{
							$pTitle = $pTitle ? t3lib_div::fixed_lgd_cs($pTitle, $this->tceforms->titleLen) : t3lib_BEfunc::getNoRecordTitle();
							$pUid = $pp['table'] . '_' . $pp['id'];
							$uidList[] = $pUid;
							$opt[] = '<option value="'.htmlspecialchars($pUid).'">'.htmlspecialchars($pTitle).'</option>';
						}
					}
				break;
				case 'folder':
				case 'file':
					while(list(,$pp)=each($itemArray))	{
						$pParts = explode('|', $pp);
						$uidList[] = $pUid = $pTitle = $pParts[0];
						$opt[] = '<option value="'.htmlspecialchars(rawurldecode($pParts[0])).'">'.htmlspecialchars(rawurldecode($pParts[0])).'</option>';
					}
				break;
				default:
					while(list(,$pp)=each($itemArray))	{
						$pParts = explode('|', $pp, 2);
						$uidList[] = $pUid = $pParts[0];
						$pTitle = $pParts[1] ? $pParts[1] : $pParts[0];
						$opt[] = '<option value="'.htmlspecialchars(rawurldecode($pUid)).'">'.htmlspecialchars(rawurldecode($pTitle)).'</option>';
					}
				break;
			}
		}

			// Create selector box of the options
		$sSize = $params['autoSizeMax'] ? t3lib_div::intInRange($itemArrayC+1, t3lib_div::intInRange($params['size'], 1), $params['autoSizeMax']) : $params['size'];
		if (!$selector)	{
			$selector = '<select size="'.$sSize.'"'.$this->tceforms->insertDefStyle('group').' multiple="multiple" name="'.$fName.'_list" '.$onFocus.$params['style'].$disabled.'>'.implode('', $opt).'</select>';
		}


		$icons = array(
			'L' => array(),
			'R' => array(),
		);
		if (!$params['readOnly']) {
			if (!$params['noBrowser'])	{
				$aOnClick = 'setFormValueOpenBrowser(\''.$modeEB.'\',\''.($fName.'|||'.$allowed.'|'.$userEBParam.'|').'\'); return false;';
				$icons['R'][] = '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.
						'<img'.t3lib_iconWorks::skinImg($this->tceforms->backPath, 'gfx/insert3.gif', 'width="14" height="14"').' border="0" '.t3lib_BEfunc::titleAltAttrib($this->tceforms->getLL('l_browse_'.($mode === 'file'?'file':'db'))).' />'.
						'</a>';
			}
			if (!$params['dontShowMoveIcons'])	{
				if ($sSize>=5)	{
					$icons['L'][] = '<a href="#" onclick="setFormValueManipulate(\''.$fName.'\',\'Top\'); return false;">'.
							'<img'.t3lib_iconWorks::skinImg($this->tceforms->backPath, 'gfx/group_totop.gif', 'width="14" height="14"').' border="0" '.t3lib_BEfunc::titleAltAttrib($this->tceforms->getLL('l_move_to_top')).' />'.
							'</a>';
				}
				$icons['L'][] = '<a href="#" onclick="setFormValueManipulate(\''.$fName.'\',\'Up\'); return false;">'.
						'<img'.t3lib_iconWorks::skinImg($this->tceforms->backPath, 'gfx/up.gif', 'width="14" height="14"').' border="0" '.t3lib_BEfunc::titleAltAttrib($this->tceforms->getLL('l_move_up')).' />'.
						'</a>';
				$icons['L'][] = '<a href="#" onclick="setFormValueManipulate(\''.$fName.'\',\'Down\'); return false;">'.
						'<img'.t3lib_iconWorks::skinImg($this->tceforms->backPath, 'gfx/down.gif', 'width="14" height="14"').' border="0" '.t3lib_BEfunc::titleAltAttrib($this->tceforms->getLL('l_move_down')).' />'.
						'</a>';
				if ($sSize>=5)	{
					$icons['L'][] = '<a href="#" onclick="setFormValueManipulate(\''.$fName.'\',\'Bottom\'); return false;">'.
							'<img'.t3lib_iconWorks::skinImg($this->tceforms->backPath, 'gfx/group_tobottom.gif', 'width="14" height="14"').' border="0" '.t3lib_BEfunc::titleAltAttrib($this->tceforms->getLL('l_move_to_bottom')).' />'.
							'</a>';
				}
			}

// todo Clipboard
			$clipElements = $this->tceforms->getClipboardElements($allowed, $mode);
			if (count($clipElements))	{
				$aOnClick = '';
	#			$counter = 0;
				foreach($clipElements as $elValue)	{
					if ($mode === 'file' OR $mode === 'folder')	{
						$itemTitle = 'unescape(\''.rawurlencode(tx_dam::file_basename($elValue)).'\')';
					} else {	// 'db' mode assumed
						list($itemTable, $itemUid) = explode('|', $elValue);

						if (function_exists('t3lib_BEfunc::getRecordWSOL')) {
							$rec = t3lib_BEfunc::getRecordWSOL($itemTable, $itemUid);
						} else {
							$rec = t3lib_BEfunc::getRecord($itemTable, $itemUid);
						}
						$itemTitle = $GLOBALS['LANG']->JScharCode(t3lib_BEfunc::getRecordTitle($itemTable, $rec));
						$elValue = $itemTable.'_'.$itemUid;
					}
					$aOnClick .= 'setFormValueFromBrowseWin(\''.$fName.'\',\''.t3lib_div::slashJS(t3lib_div::rawUrlEncodeJS($elValue)).'\','.t3lib_div::slashJS($itemTitle).');';
					#$aOnClick .= 'setFormValueFromBrowseWin(\''.$fName.'\',unescape(\''.rawurlencode(str_replace('%20', ' ', $elValue)).'\'),'.$itemTitle.');';

	#				$counter++;
	#				if ($params['maxitems'] && $counter >= $params['maxitems'])	{	break;	}	// Makes sure that no more than the max items are inserted... for convenience.
				}
				$aOnClick .= 'return false;';
				$icons['R'][] = '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.
						'<img'.t3lib_iconWorks::skinImg($this->tceforms->backPath, 'gfx/insert5.png', 'width="14" height="14"').' border="0" '.t3lib_BEfunc::titleAltAttrib(sprintf($this->tceforms->getLL('l_clipInsert_'.($mode === 'file'?'file':'db')), count($clipElements))).' />'.
						'</a>';
			}

			$icons['L'][] = '<a href="#" onclick="setFormValueManipulate(\''.$fName.'\',\'Remove\'); return false;">'.
					'<img'.t3lib_iconWorks::skinImg($this->tceforms->backPath, 'gfx/group_clear.gif', 'width="14" height="14"').' border="0" '.t3lib_BEfunc::titleAltAttrib($this->tceforms->getLL('l_remove_selected')).' />'.
					'</a>';
		}

		$str = '<table border="0" cellpadding="0" cellspacing="0" width="1">
			'.($params['headers']?'
				<tr>
					<td>'.$this->tceforms->wrapLabels($params['headers']['selector']).'</td>
					<td></td>
					<td></td>
					<td></td>
					<td>'.($params['thumbnails'] ? $this->tceforms->wrapLabels($params['headers']['items']) : '').'</td>
				</tr>':'').
			'
			<tr>
				<td valign="top">'.
					$selector.'<br />'.
					$this->tceforms->wrapLabels($params['info']).
				'</td>
				<td valign="top">'.
					implode('<br />', $icons['L']).'</td>
				<td valign="top">'.
					implode('<br />', $icons['R']).'</td>
				<td style="height:5px;"><span></span></td>
				<td valign="top">'.
					$this->tceforms->wrapLabels($params['thumbnails']).
				'</td>
			</tr>
		</table>';

			// Creating the hidden field which contains the actual value as a comma list.
		$str .= '<input type="hidden" name="'.$fName.'" value="'.htmlspecialchars(implode(',', $uidList)).'" />';

		return $str;
	}





	/************************************************************
	 *
	 * Misc helper functions
	 *
	 ************************************************************/




	/**
	 * Checks if bidirectional MM Relations are active.
	 * see extension mmforeign
	 *
	 * @return	mixed	Return true or error message
	 */
	function isMMForeignActive()	{
		global $TYPO3_CONF_VARS;

		$error = 0;


		if(t3lib_div::int_from_ver(TYPO3_version) >= t3lib_div::int_from_ver('4.1')) {
			return true;
		}

			// is mmforeign loaded?
		if (!t3lib_extMgm::isLoaded('mmforeign')) {
			return 'Warning: DAM References are disabled! Install extension "mmforeign".';
		}

			// this forces us to think all is fine
		if($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['mmref']) {
			return true;
		}

			// XCLASS overwritten?
		if (!(preg_match('#(/ext/mmforeign/)#', $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_tcemain.php']))) {
			return 'Warning: DAM References are disabled by other extension! (overridden XCLASS):'."\n".$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_tcemain.php'];
		}
		if (!(preg_match('#(/ext/mmforeign/)#', $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_transferdata.php']))) {
			return 'Warning: DAM References are disabled by other extension! (overridden XCLASS):'."\n".$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_transferdata.php'];
		}

		return true;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tcefunc.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tcefunc.php']);
}

?>
