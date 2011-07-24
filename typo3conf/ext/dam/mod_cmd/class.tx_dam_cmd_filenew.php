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
 * Command module 'new file'
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
 *   66: class tx_dam_cmd_filenew extends t3lib_extobjbase
 *   74:     function accessCheck()
 *   84:     function head()
 *   94:     function getContextHelp()
 *  105:     function main()
 *  144:     function renderForm()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the create file command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
class tx_dam_cmd_filenew extends t3lib_extobjbase {


	/**
	 * Do some init things
	 *
	 * @return	void
	 */
	function cmdInit() {
		$GLOBALS['SOBE']->templateClass = 'template';
	}
	
	/**
	 * Additional access check
	 *
	 * @return	boolean Return true if access is granted
	 */
	function accessCheck() {
		return tx_dam::access_checkFileOperation('newFile');
	}


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		$GLOBALS['SOBE']->pageTitle = $GLOBALS['LANG']->getLL('tx_dam_cmd_filenew.title');
	}


	/**
	 * Returns a help icon for context help
	 *
	 * @return	string HTML
	 */
	function getContextHelp() {
// todo csh
#		return t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_rename', $GLOBALS['BACK_PATH'],'');
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
		$this->folder = tx_dam::path_compileInfo($this->pObj->folder[0]);


		if (is_array($this->pObj->data) AND $this->pObj->data['file_name']) {

				// create the file and process DB update
			$error = tx_dam::process_createFile($this->folder['dir_path_absolute'].$this->pObj->data['file_name'], $this->pObj->data['file_content']);

			if ($error) {
				$content .= $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('error'), htmlspecialchars($error), '', 2);
				$content .= $this->renderForm();

			} else {
				$this->pObj->redirect(true);
			}


		} elseif ($this->folder['dir_accessable']) {
			$content.= $this->renderForm();

		} else {
				// this should have happen in index.php already
			$content.= $this->pObj->accessDeniedMessageBox(tx_dam_guiFunc::getFolderInfoBar($this->folder));
		}

		return $content;
	}


	/**
	 * Making the form for create file
	 *
	 * @return	string		HTML content
	 */
	function renderForm()	{
		global $BE_USER, $LANG, $TYPO3_CONF_VARS;
		
		$content = '';
		$msg = array();

		$this->pObj->markers['FOLDER_INFO'] = tx_dam_guiFunc::getFolderInfoBar($this->folder);
		$msg[] = '&nbsp;';

		$msg[] = $GLOBALS['LANG']->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name',1);
		$msg[] = '<input type="input" name="data[file_name]" value="'.htmlspecialchars($this->pObj->data['file_name']).'"'.$this->pObj->doc->formWidthText(30).' />';
		$msg[] = '&nbsp;';
		$msg[] = $GLOBALS['LANG']->getLL('tx_dam_cmd_filenew.text_content',1);
		$msg[] = '<textarea rows="30" name="data[file_content]" wrap="off"'.$this->pObj->doc->formWidthText(48,'width:99%;height:65%','off').' class="fixed-font enable-tab">'.
					t3lib_div::formatForTextarea($this->pObj->data['file_content']).
					'</textarea>';

		if (tx_dam::config_checkValueEnabled('mod.txdamM1_SHARED.displayExtraButtons', 1)) {
			$buttons = '
				<input type="submit" value="'.$GLOBALS['LANG']->getLL('submitCreate',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
		}

		$this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/savedok.gif') . ' title="' . $GLOBALS['LANG']->getLL('submitCreate',1) . '" height="16" type="image" width="16">';
		$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';

		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);

		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filenew.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filenew.php']);
}

?>