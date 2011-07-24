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
 * Command module 'file delete'
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
 *   65: class tx_dam_cmd_filedelete extends t3lib_extobjbase
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





require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the file delete command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
class tx_dam_cmd_filedelete extends t3lib_extobjbase {

	var $passthroughMissingFiles = true;

	/**
	 * Additional access check
	 *
	 * @return	boolean Return true if access is granted
	 */
	function accessCheck() {
		return tx_dam::access_checkFileOperation('deleteFile');
	}


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		$GLOBALS['SOBE']->pageTitle = $GLOBALS['LANG']->getLL('tx_dam_cmd_filedelete.title');
	}


	/**
	 * Returns a help icon for context help
	 *
	 * @return	string HTML
	 */
	function getContextHelp() {
// todo csh
#		return t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_delete', $GLOBALS['BACK_PATH'],'');
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
		// perform delete files(s)
		//

		if (count($items) AND is_array($this->pObj->data) AND $this->pObj->data['delete']) {

			$errors = array();

			foreach ($this->pObj->data['delete'] as $id => $filepath) {
				if ($items[$id] AND $items[$id]['file_accessable']) {
					if ($error = tx_dam::process_deleteFile($items[$id])) {
						$errors[] = htmlspecialchars($error);
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

		if (count($items) == 1) {
			reset($items);
			$item = current($items);
			if ($item['file_accessable']) {

				$content.=  $this->renderFormSingle(key($items), $item);

			} else {
					// this should have happen in index.php already
				$content.= $this->pObj->accessDeniedMessageBox($item['file_name']);
			}

		} elseif (count($items) > 1) {
			// multi action

			$content.=  $this->renderFormMulti($items);
		}

		return $content;
	}


	/**
	 * Rendering the delete file form for a single item
	 *
	 * @return	string		HTML content
	 */
	function renderFormSingle($id, $meta)	{
		global  $BACK_PATH, $LANG;

		$filepath = tx_dam::file_absolutePath($meta);

		$content = '';

		$this->pObj->markers['FOLDER_INFO'] = 'File: ' . $filepath;
		if ($meta['uid']) {
			$references = tx_dam_db::getMediaUsageReferences($meta['uid']);
			if ($references) {
				$msg = $LANG->getLL('tx_dam_cmd_filedelete.messageReferences',1);
				$msg .= $GLOBALS['SOBE']->doc->spacer(5);

					// Render the references
				$references = tx_dam_guiFunc::renderReferencesTable($references);
				$references = $GLOBALS['SOBE']->doc->section($LANG->getLL('tx_dam_cmd_filedelete.references',1), $msg.$references,0,0,0);
			}
		}


		$msg = array();

		$msg[] = tx_dam_guiFunc::getRecordInfoHeaderExtra($meta);

		if ($references) {
			$msg[] = '&nbsp;';
			$msg[] = '<strong><span class="typo3-red">'.$LANG->getLL('labelWarning',1).'</span> '.$LANG->getLL('tx_dam_cmd_filedelete.messageReferencesUsed',1).'</strong>';
			$msg[] = $LANG->getLL('tx_dam_cmd_filedelete.messageReferencesDelete',1);
			$msg[] = $references;
		}

		$msg[] = '&nbsp;';
		$msg[] = $LANG->getLL('tx_dam_cmd_filedelete.message',1);

		if (tx_dam::config_checkValueEnabled('mod.txdamM1_SHARED.displayExtraButtons', 1)) {
			$buttons = '
				<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filedelete.submit',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
		}

		$this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/deletedok.gif') . ' title="' . $LANG->getLL('tx_dam_cmd_filedelete.submit',1) . '" height="16" type="image" width="16">';
		$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';			

		$content .= '<input type="hidden" name="data[delete]['.$id.'][data]" value="'.htmlspecialchars($filepath).'" />';
		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);

		//$content .= $GLOBALS['SOBE']->doc->spacer(5);

		//$content .= $references;

		return $content;
	}


	/**
	 * Rendering the delete file form for a multiple items
	 *
	 * @return	string		HTML content
	 */
	function renderFormMulti($items)	{
		global  $BACK_PATH, $LANG, $TCA;

		$content = '';

		$references = 0;

// gk added for marker replacement fix
// FOLDER_INFO is missing due to missing param in current function
		$this->pObj->markers['FOLDER_INFO'] = '';
		$this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/deletedok.gif') . ' title="' . $LANG->getLL('tx_dam_cmd_filedelete.submit',1) . '" height="16" type="image" width="16">';
		$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';			

		$titleNotExists = 'title="'.$GLOBALS['LANG']->getLL('fileNotExists', true).'"';
		$iconNotExists = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/error_h.gif', 'width="10" height="10"').' '.$titleNotExists.' valign="top" alt="" />';

		$referencedIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/is_referenced.gif', 'width="15" height="12"').' title="'.$LANG->getLL('tx_dam_cmd_filedelete.messageReferencesUsed',1).'" alt="" />';

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
		$cTable[$tr][$td++] = '&nbsp;';
		$cTable[$tr][$td++] = $LANG->sL($TCA['tx_dam']['columns']['file_path']['label'],1);

		$tr++;



		foreach ($items as $id => $meta) {
			$filepath = tx_dam::file_absolutePath($meta);
			if ($meta['file_accessable']) {
				$checkbox = '<input type="checkbox" name="data[delete]['.$id.'][data]" value="'.htmlspecialchars($filepath).'"  checked="checked" />';
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
			
			if ($meta['uid'] AND $ref=tx_dam_db::getMediaUsageReferences($meta['uid'])) {
				$references += count($ref);
			}

				// Add row to table
			$td=0;
			$cTable[$tr][$td++] = $checkbox;
			$cTable[$tr][$td++] = $icon;
			$cTable[$tr][$td++] = htmlspecialchars($title);
			$cTable[$tr][$td++] = htmlspecialchars(strtoupper($meta['file_type']));
			$cTable[$tr][$td++] = ($ref ? $referencedIcon : '');
			$cTable[$tr][$td++] = $info;
			$cTable[$tr][$td++] = htmlspecialchars(t3lib_div::fixed_lgd_cs($meta['file_path'],-15));
			$tr++;
		}

		$itemTable = $this->pObj->doc->table($cTable, $tableLayout);



		$msg = array();

		if ($references) {
			$msg[] = '&nbsp;';
			$msg[] = '<strong><span class="typo3-red">'.$LANG->getLL('labelWarning',1).'</span> '.$LANG->getLL('tx_dam_cmd_filedelete.messageReferencesUsed',1).'</strong>';
			$msg[] = $LANG->getLL('tx_dam_cmd_filedelete.messageReferencesDelete',1);
		}

		$msg[] = '&nbsp;';
		$msg[] = $LANG->getLL('tx_dam_cmd_filedelete.message',1);

		$buttons = '
			<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filedelete.submit',1).'" />
			<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';

		$msg[] = '&nbsp;';
		$msg[] = $itemTable;

		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);


		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filedelete.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filedelete.php']);
}


?>
