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
 * Command module 'delete folder'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Folder
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   60: class tx_dam_cmd_folderdelete extends t3lib_extobjbase
 *   68:     function accessCheck()
 *   78:     function head()
 *   88:     function getContextHelp()
 *   99:     function main()
 *  138:     function renderForm()
 *  188:     function path_isEmpty ($path)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

/**
 * Class for the folder delete command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Folder
 */
class tx_dam_cmd_folderdelete extends t3lib_extobjbase {


	/**
	 * Additional access check
	 *
	 * @return	boolean Return true if access is granted
	 */
	function accessCheck() {
		return tx_dam::access_checkFileOperation('deleteFolder');
	}


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		$GLOBALS['SOBE']->pageTitle = $GLOBALS['LANG']->getLL('tx_dam_cmd_folderdelete.title');
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


		if (is_array($this->pObj->data) AND $this->pObj->data['delete_confirmed']) {

				// Delete the folder/files and process DB update
			$error = tx_dam::process_deleteFolder($this->folder['dir_path_absolute']);

			if ($error) {
				$content .= $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('error'), htmlspecialchars($error), $this->pObj->buttonBack(0), 2);

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
	 * Making the form for delete
	 *
	 * @return	string		HTML content
	 */
	function renderForm(){
		global $BACK_PATH, $LANG, $TYPO3_CONF_VARS;
		

		$content = '';

		if (!$this->path_isEmpty ($this->folder['dir_path_absolute']) AND !tx_dam::access_checkFileOperation('deleteFolderRecursively')) {

			$content .= $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('actionDenied'), $LANG->getLL('tx_dam_cmd_folderdelete.messageRecursiveDenied',1), $this->pObj->buttonBack(0), 2);

		} else {

			$msg = array();
			$this->pObj->markers['FOLDER_INFO'] = tx_dam_guiFunc::getFolderInfoBar($this->folder);
			$msg[] = '&nbsp;';
			if (!$this->path_isEmpty ($this->folder['dir_path_absolute'])) {
				$msg[] = '<strong><span class="typo3-red">'.$LANG->getLL('labelWarning',1).'</span> '.$LANG->getLL('tx_dam_cmd_folderdelete.messageRecursive',1).'</strong>';
			}
			$msg[] = sprintf($LANG->sL('LLL:EXT:lang/locallang_core.xml:mess.delete',1),$this->folder['dir_path_relative']);

			if (tx_dam::config_checkValueEnabled('mod.txdamM1_SHARED.displayExtraButtons', 1)) {
				$buttons = '
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:cm.delete',1).'" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
			}

			$this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/deletedok.gif') . ' title="' . $LANG->sL('LLL:EXT:lang/locallang_core.xml:cm.delete',1) . '" height="16" type="image" width="16">';
			$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';			

			$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);
			$content .= '<input type="hidden" name="folder" value="'.$this->folder['dir_path_absolute'].'" />
					<input type="hidden" name="data[delete_confirmed]" value="1" />';

		}

		if (!$this->path_isEmpty ($this->folder['dir_path_absolute'])) {

			$content .= $GLOBALS['SOBE']->doc->spacer(5);

			require_once (PATH_txdam.'lib/class.tx_dam_filebrowser.php');
			$filelist = t3lib_div::makeInstance('tx_dam_filebrowser');
			$filelisting = $filelist->getStaticFolderList($this->folder['dir_path_absolute'], false);
			$content.= $GLOBALS['SOBE']->doc->section($LANG->getLL('folder',1), $filelisting,0,0,0);
		}

		return $content;
	}


	/**
	 * Check if there are any files or folder in a folder.
	 * It will not be checked if the folder exists.
	 *
	 * @param 	string 		$path
	 * @return 	boolean 	TRUE if the folder is empty.
	 */
	function path_isEmpty ($path) {
		$isEmpty = true;

		if ($dh  = @opendir($path)) {
			while (false !== ($filename = @readdir($dh))) {
			    if (($filename!='.' AND $filename!='..')) {
			    	$isEmpty = false;
			    	break;
			    }
			}
		}

		return $isEmpty;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_folderdelete.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_folderdelete.php']);
}


?>