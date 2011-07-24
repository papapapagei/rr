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
 * Command module 'file replace'
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
 *   64: class tx_dam_cmd_filereplace extends t3lib_extobjbase
 *   72:     function accessCheck()
 *   82:     function head()
 *   92:     function getContextHelp()
 *  103:     function main()
 *  154:     function renderForm()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the file replace command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
class tx_dam_cmd_filereplace extends t3lib_extobjbase {


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
		$GLOBALS['SOBE']->pageTitle = $GLOBALS['LANG']->getLL('tx_dam_cmd_filereplace.title');
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

			// Cleaning and checking target
		if ($this->pObj->file[0]) {
			$this->file = tx_dam::file_compileInfo($this->pObj->file[0]);
			$this->meta = tx_dam::meta_getDataForFile($this->file);
		} elseif ($id = intval($this->pObj->record['tx_dam'][0])) {
			$this->meta = tx_dam::meta_getDataByUid($id);
			$this->file = tx_dam::file_compileInfo($this->meta);
		}
		if (!is_array($this->meta)) {
			$fileType = tx_dam::file_getType ($this->file);
			$this->meta = array_merge($this->file, $fileType);
			$this->meta['uid'] = 0;
		}

		if ($this->file['file_accessable']) {

			if (is_array($this->pObj->data) AND $this->pObj->data['upload']) {

				$error = tx_dam::process_replaceFile($this->meta, $this->pObj->data);

				if ($error) {
					$content .= $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('error'), htmlspecialchars($error), $this->pObj->buttonBack(0), 2);

				} else {
					$this->pObj->redirect();
				}


			} else {
				$content.=  $this->renderForm();
			}

		} else {
				// this should have happen in index.php already
			$content.= $this->pObj->accessDeniedMessageBox($this->file['file_name']);
		}

		return $content;
	}


	/**
	 * Rendering the upload file form fields
	 *
	 * @return	string		HTML content
	 */
	function renderForm()	{
		global $BACK_PATH, $LANG;

		$id = $this->meta['uid'];
		$path = tx_dam::path_makeAbsolute($this->meta['file_path']);

		$content = '';
		$msg = array();

		$this->pObj->markers['FOLDER_INFO'] = '[' . $this->file['file_path'] . ']:' . $this->file['file_name'];
		$msg[] = tx_dam_guiFunc::getRecordInfoHeaderExtra($this->meta);
		$msg[] = '&nbsp;';

		$msg[] = '
				<input type="file" name="upload_'.$id.'"'.$this->pObj->doc->formWidth(35).' size="45" />
				<input type="hidden" name="data[upload]['.$id.'][target]" value="'.htmlspecialchars($path).'" />
				<input type="hidden" name="data[upload]['.$id.'][data]" value="'.$id.'" />';

		if (tx_dam::config_checkValueEnabled('mod.txdamM1_SHARED.displayExtraButtons', 1)) {
			$buttons = '
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_upload.php.submit',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
		}

		$this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/savedok.gif') . ' title="' . $LANG->sL('LLL:EXT:lang/locallang_core.xml:file_upload.php.submit',1) . '" height="16" type="image" width="16">';
		$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';

		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);

		return $content;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filereplace.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filereplace.php']);
}


?>