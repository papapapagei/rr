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
 * Command module 'file copy'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   65: class tx_dam_cmd_filecopy extends t3lib_extobjbase
 *   74:     function accessCheck()
 *   84:     function head()
 *   94:     function getContextHelp()
 *  105:     function main()
 *  172:     function renderFormSingle($id, $meta)
 *  224:     function renderFormMulti($items)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_t3lib.'class.t3lib_foldertree.php');

/**
 * Base extension class which generates the folder tree.
 * Used directly by the RTE.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage core
 */
class localFolderTree extends t3lib_folderTree {
	var $ext_IconMode=1;

	var $ext_noTempRecyclerDirs=0;		// If file-drag mode is set, temp and recycler folders are filtered out.

	/**
	 * Initializes the script path
	 *
	 * @return	void
	 */
	function localFolderTree() {
		$this->thisScript = t3lib_div::linkThisScript();
		$this->t3lib_folderTree();
	}

	/**
	 * Wrapping the title in a link, if applicable.
	 *
	 * @param	string		Title, ready for output.
	 * @param	array		The "record"
	 * @return	string		Wrapping title string.
	 */
	function wrapTitle($title,$v)	{
		if ($this->ext_isLinkable($v))	{
			$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'&target='.rawurlencode($v['path']).'\');';
			return '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
		} else {
			return '<span class="typo3-dimmed">'.$title.'</span>';
		}
	}


	/**
	 * Returns true if the input "record" contains a folder which can be linked.
	 *
	 * @param	array		Array with information about the folder element. Contains keys like title, uid, path, _title
	 * @return	boolean		True is returned if the path is NOT a recycler or temp folder AND if ->ext_noTempRecyclerDirs is not set.
	 */
	function ext_isLinkable($v)	{
		if ($this->ext_noTempRecyclerDirs && (substr($v['path'],-7)=='_temp_/' || substr($v['path'],-11)=='_recycler_/'))	{
			return 0;
		} return 1;
	}


	/**
	 * Wrap the plus/minus icon in a link
	 *
	 * @param	string		HTML string to wrap, probably an image tag.
	 * @param	string		Command for 'PM' get var
	 * @param	boolean		If set, the link will have a anchor point (=$bMark) and a name attribute (=$bMark)
	 * @return	string		Link-wrapped input string
	 * @access private
	 */
	function PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($bMark)	{
			$anchor = '#'.$bMark;
			$name=' name="'.$bMark.'"';
		}
		$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'&PM='.$cmd.'\',\''.$anchor.'\');';
		return '<a href="#"'.$name.' onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
	}

	/**
	 * Create the folder navigation tree in HTML
	 *
	 * @param	mixed		Input tree array. If not array, then $this->tree is used.
	 * @return	string		HTML output of the tree.
	 */
	function printTree($treeArr='')	{
		global $BACK_PATH;
		$titleLen=intval($GLOBALS['BE_USER']->uc['titleLen']);

		if (!is_array($treeArr))	$treeArr=$this->tree;

		$out='';
		$c=0;

			// Traverse rows for the tree and print them into table rows:
		foreach($treeArr as $k => $v)	{
			$c++;
			$bgColorClass=($c+1)%2 ? 'bgColor' : 'bgColor-10';

				// Create arrow-bullet for file listing (if folder path is linkable):
			$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?command=targetFolder:'.$this->itemKey.':'.rawurlencode($v['row']['path']).'\');';
			$cEbullet = $this->ext_isLinkable($v['row']) ? '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/arrowbullet.gif','width="18" height="16"').' alt="" /></a>' : '';

				// Put table row with folder together:
			$out.='
				<tr class="'.$bgColorClass.'">
					<td nowrap="nowrap">'.$v['HTML'].$this->wrapTitle(t3lib_div::fixed_lgd_cs($v['row']['title'],$titleLen),$v['row']).'</td>
					<td>'.$cEbullet.'</td>
				</tr>';
		}

		$out='

			<!--
				Folder tree:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-tree">
				'.$out.'
			</table>';
		return $out;
	}

}






require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the file copy command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
class tx_dam_cmd_filecopy extends t3lib_extobjbase {

	var $passthroughMissingFiles = true;
	
	var $langPrefix = 'tx_dam_cmd_filecopy.';
	var $copyOrMove = 'copy';
	

	/**
	 * Additional access check
	 *
	 * @return	boolean Return true if access is granted
	 */
	function accessCheck() {
		return tx_dam::access_checkFileOperation($this->copyOrMove.'File');
	}


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		$GLOBALS['SOBE']->pageTitle = $GLOBALS['LANG']->getLL($this->langPrefix.'title');
	}


	/**
	 * Returns a help icon for context help
	 *
	 * @return	string HTML
	 */
	function getContextHelp() {
// todo csh
#		return t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_copy', $GLOBALS['BACK_PATH'],'');
	}


	/**
	 * Main function, rendering the content of the rename form
	 *
	 * @return	void
	 */
	function main()	{
		global  $LANG;

		$content = '';

		$items = $this->pObj->compileFilesAndRecordsData();

		

		//
		// perform copy files(s)
		//

		if (count($items) AND is_array($this->pObj->data) AND $this->pObj->target AND $this->pObj->data[$this->copyOrMove]) {

			$errors = array();

			foreach ($this->pObj->data[$this->copyOrMove] as $id => $filepath) {
				if ($items[$id] AND $items[$id]['file_accessable']) {
					if ($this->copyOrMove=='copy') {
						if ($error = tx_dam::process_copyFile($items[$id], $this->pObj->target)) {
							$errors[] = htmlspecialchars($error);
						}
					} else {
						if ($error = tx_dam::process_moveFile($items[$id], $this->pObj->target)) {
							$errors[] = htmlspecialchars($error);
						}
					}
				} else {
					$errors[] = $LANG->getLL('accessDenied', true).htmlspecialchars(' ('.$filepath.')');
				}
			}

			if ($errors) {
				$content .= $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('error'), implode('<br />', $errors), $this->pObj->buttonBack(0), 2);

			} else {
				$this->pObj->redirect();
			}
		}


		//
		// display forms
		//

		if (!$this->pObj->target) {
			$content.= $this->renderTargetFolderSelect();

		} elseif (count($items) == 1) {
			reset($items);
			$item = current($items);
			if ($item['file_accessable']) {

				$content.= $this->renderFormSingle(key($items), $item, $this->pObj->target);

			} else {
					// this should have happen in index.php already
				$content.= $this->pObj->accessDeniedMessageBox($item['file_name']);
			}

		} elseif (count($items) > 1) {
			// multi action

			$content.= $this->renderFormMulti($items, $this->pObj->target);
		}

		return $content;
	}

	/**
	 * Rendering a folder tree to select the target path
	 *
	 * @return	string		HTML content
	 */
	function renderTargetFolderSelect() {
		global  $BACK_PATH, $LANG;

		$content = '';

		$msg = array();

		$msg[] = '&nbsp;';
		$msg[] = $LANG->getLL($this->langPrefix.'selectTarget');
		$msg[] = '&nbsp;';
		
		$this->pObj->markers['FOLDER_INFO'] = '';
		$this->pObj->docHeaderButtons['SAVE'] = '';
		$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';			

		$foldertree = t3lib_div::makeInstance('localFolderTree');
		$foldertree->ext_noTempRecyclerDirs = true;
		$foldertree->itemKey = t3lib_div::_GP('selectTargetFor');
		$msg[] = $foldertree->getBrowsableTree();
		

		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);

		return $content;

		
	}
	
	
	/**
	 * Rendering the copy file form for a single item
	 *
	 * @return	string		HTML content
	 */
	function renderFormSingle($id, $meta, $targetFolder)	{
		global  $BACK_PATH, $LANG;

		$filepath = tx_dam::file_absolutePath($meta);
		$this->pObj->markers['FOLDER_INFO'] = 'File: ' . $filepath;
		$content = '';


		$msg = array();

		$msg[] = tx_dam_guiFunc::getRecordInfoHeaderExtra($meta);
		$msg[] = '&nbsp;';
		
		$targetFolderRel = tx_dam::path_makeRelative($targetFolder);
		$msg[] = $LANG->getLL('labelTargetFolder',1).' <strong>'.htmlspecialchars($targetFolderRel).'</strong>';
		$msg[] = '&nbsp;';
		$msg[] = htmlspecialchars(sprintf($LANG->getLL($this->langPrefix.'message'), $targetFolderRel));
		$msg[] = '&nbsp;';

		$buttons = '
			<input type="hidden" name="data['.$this->copyOrMove.']['.$id.'][data]" value="'.htmlspecialchars($filepath).'" />
			<input type="hidden" name="data['.$this->copyOrMove.']['.$id.'][target]" value="'.htmlspecialchars($targetFolder).'" />';

		if (tx_dam::config_checkValueEnabled('mod.txdamM1_SHARED.displayExtraButtons', 1)) {
			if ($this->copyOrMove=='copy') {
				$buttons .= '
					<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filecopy.submit',1).'" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
			}
			else {
				$buttons .= '
					<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filemove.submit',1).'" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
			}
		}

		$this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/clip_copy.gif') . ' title="' . $LANG->getLL($this->langPrefix.'submit',1) . '" height="16" type="image" width="16">';
		$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';			


		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);

		$content .= $GLOBALS['SOBE']->doc->spacer(5);

		return $content;
	}


	/**
	 * Rendering the copy file form for a multiple items
	 *
	 * @return	string		HTML content
	 */
	function renderFormMulti($items, $targetFolder)	{
		global  $BACK_PATH, $LANG, $TCA;

		$content = '';

		$references = 0;

// FOLDER_INFO is missing due to missing param in current function - so we set it to nothing
		$this->pObj->markers['FOLDER_INFO'] = '';

		$titleNotExists = 'title="'.$GLOBALS['LANG']->getLL('fileNotExists', true).'"';
		$iconNotExists = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/error_h.gif', 'width="10" height="10"').' '.$titleNotExists.' valign="top" alt="" />';

		$referencedIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/is_referenced.gif', 'width="15" height="12"').' title="'.$LANG->getLL($this->langPrefix.'messageReferencesUsed',1).'" alt="" />';

			// init table layout
		$tableLayout = array(
			'table' => array('<table cellpadding="2" cellspacing="1" border="0" width="100%">','</table>'),
			'0' => array(
				'defCol' => array('<th nowrap="nowrap" class="bgColor5">','</th>'),
				'0' => array('<th width="1%" class="bgColor5">','</th>'),
				'1' => array('<th width="1%" class="bgColor5">','</th>'),
				'3' => array('<th width="1%" class="bgColor5">','</th>'),
				'4' => array('<th width="1%" class="bgColor5">','</th>'),
				'5' => array('<th width="1%" class="bgColor5">','</th>'),
			),
			'defRow' => array(
				'defCol' => array('<td nowrap="nowrap" class="bgColor4">','</td>'), 
				'2' => array('<td class="bgColor4">','</td>'),
				'3' => array('<td style="text-align:center" class="bgColor4">','</td>'),
				'4' => array('<td style="padding:0 5px 0 5px" class="bgColor4">','</td>'),
				'5' => array('<td style="text-align:center" class="bgColor4">','</td>'),
			),
		);

		$cTable=array();
		$tr = 0;
		$td = 0;

		$cTable[$tr][$td++] = '&nbsp;';
		$cTable[$tr][$td++] = '&nbsp;';
		$cTable[$tr][$td++] = $LANG->sL($TCA['tx_dam']['columns']['title']['label'],1);
		$cTable[$tr][$td++] = '&nbsp;';
		$cTable[$tr][$td++] = '&nbsp;';
		$cTable[$tr][$td++] = $LANG->sL($TCA['tx_dam']['columns']['file_path']['label'],1);

		$tr++;



		foreach ($items as $id => $meta) {
			$filepath = tx_dam::file_absolutePath($meta);
			if ($meta['file_accessable']) {
				$checkbox = '<input type="checkbox" name="data['.$this->copyOrMove.']['.$id.'][data]" value="'.htmlspecialchars($filepath).'"  checked="checked" />';
			} else {
				$checkbox = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/error_h.gif', 'width="10" height="10"').' title="'.$LANG->getLL('accessDenied', true).'" alt="" />';
			}

			$title = $meta['title'] ? $meta['title'] : $meta['file_name'];
			$title = t3lib_div::fixed_lgd_cs($title,50);

			$icon = tx_dam_guiFunc::icon_getFileTypeImgTag($meta, 'class="c-recicon"', false);
			if (!@file_exists($filepath)) {
				$icon .= $iconNotExists;
				$info = '';
			} else {
				$info = $GLOBALS['SOBE']->btn_infoFile($meta);
			}

				// Add row to table
			$td=0;
			$cTable[$tr][$td++] = $checkbox;
			$cTable[$tr][$td++] = $icon;
			$cTable[$tr][$td++] = htmlspecialchars($title);
			$cTable[$tr][$td++] = htmlspecialchars(strtoupper($meta['file_type']));
			$cTable[$tr][$td++] = $info;
			$cTable[$tr][$td++] = htmlspecialchars(t3lib_div::fixed_lgd_cs($meta['file_path'],-15));
			$tr++;
		}

		$itemTable = $this->pObj->doc->table($cTable, $tableLayout);


		$targetFolderRel = tx_dam::path_makeRelative($targetFolder);
		$msg = array();
		$msg[] = '&nbsp;';
		$msg[] = $LANG->getLL('labelTargetFolder',1).' <strong>'.htmlspecialchars($targetFolderRel).'</strong>';
		$msg[] = '&nbsp;';
		$msg[] = htmlspecialchars(sprintf($LANG->getLL($this->langPrefix.'message'), $targetFolderRel));
		$msg[] = '&nbsp;';
		$msg[] = $itemTable;

		$buttons = '
			<input type="hidden" name="data['.$this->copyOrMove.']['.$id.'][data]" value="'.htmlspecialchars($filepath).'" />
			<input type="hidden" name="data['.$this->copyOrMove.']['.$id.'][target]" value="'.htmlspecialchars($targetFolder).'" />';

		if (tx_dam::config_checkValueEnabled('mod.txdamM1_SHARED.displayExtraButtons', 1)) {
			if ($this->copyOrMove=='copy') {
				$buttons .= '
					<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filecopy.submit',1).'" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
			}
			else {
				$buttons .= '
					<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filemove.submit',1).'" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
			}
		}
		
		$this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/clip_copy.gif') . ' title="' . $LANG->getLL($this->langPrefix.'submit',1) . '" height="16" type="image" width="16">';
		$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';			
			

		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);


		return $content;
	}
}


/**
 * Class for the file move command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
class tx_dam_cmd_filemove extends tx_dam_cmd_filecopy {

	var $langPrefix = 'tx_dam_cmd_filemove.';
	var $copyOrMove = 'move';

	/**
	 * Additional access check
	 *
	 * @return	boolean Return true if access is granted
	 */
	function accessCheck() {
		return tx_dam::access_checkFileOperation('moveFile');
	}
}


class tx_dam_cmd_filecopymove {
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filecopymove.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filecopymove.php']);
}


?>