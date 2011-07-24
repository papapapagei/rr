<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Rene Fritz <r.fritz@colorcube.de>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Command module 'edit text file'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Edit
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');

require_once(PATH_txdam.'lib/class.tx_dam_editorbase.php');

class tx_dam_edit_text extends tx_dam_editorBase {


	/**
	 * Returns true if this editor is able to handle the given file
	 *
	 * @param	mixed		$media Media object or itemInfo array. Currently the function have to work with a media object or an itemInfo array.
	 * @param	array		$conf Additional configuration values. Might be empty.
	 * @return	boolean		True if this is the right editor for the file
	 */
	function isValid ($media, $conf=array()) {
		$file_type = is_object($media) ? $media->getType() : $media['file_type'];
		return t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['SYS']['textfile_ext'], $file_type);
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		$iconFile = PATH_txdam_rel.'i/edit_file.gif';
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->getLL('tx_dam_edit_text.title');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->getLL('tx_dam_edit_text.descr');
	}






	/***************************************
	 *
	 *   Module
	 *
	 ***************************************/




	/**
	 * Do some init things
	 *
	 * @return	void
	 */
	function cmdInit() {
		$GLOBALS['SOBE']->templateClass = 'template';
		$GLOBALS['SOBE']->pageTitle = $this->getLabel();
	}


	/**
	 * Additional access check
	 *
	 * @return	boolean Return true if access is granted
	 */
	function accessCheck() {
		return tx_dam::access_checkFileOperation('editFile');
	}


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
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

		if (is_array($this->pObj->data) AND isset($this->pObj->data['file_content'])) {

				// process conent update
			$error = tx_dam::process_editFile($this->pObj->media->getInfoArray(), $this->pObj->data['file_content']);

			if ($error) {
				$content .= $this->pObj->errorMessageBox ($error);

			} elseif (t3lib_div::_GP('_saveandclosedok_x')) {
				$this->pObj->redirect(true);
				return;
			}
		}
		
		$content.= $this->renderForm(t3lib_div::getURL($this->pObj->media->getPathAbsolute()));

		return $content;
	}


	/**
	 * Making the form for create file
	 *
	 * @return	string		HTML content
	 */
	function renderForm($fileContent='')	{
		global $BE_USER, $LANG, $TYPO3_CONF_VARS;

		$content = '';
		$msg = array();

		$this->pObj->markers['FOLDER_INFO'] = tx_dam_guiFunc::getFolderInfoBar(tx_dam::path_compileInfo($this->pObj->media->pathAbsolute));
		$msg[] = '&nbsp;';

		$this->pObj->markers['FILE_INFO'] = $GLOBALS['LANG']->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name',1).' <strong>'.htmlspecialchars($this->pObj->media->filename).'</strong>';
		$msg[] = '&nbsp;';
		$msg[] = $GLOBALS['LANG']->getLL('tx_dam_cmd_filenew.text_content',1);
		$msg[] = '<textarea rows="30" name="data[file_content]" wrap="off"'.$this->pObj->doc->formWidthText(48,'width:99%;height:65%','off').' class="fixed-font enable-tab">'.
					t3lib_div::formatForTextarea($fileContent).
					'</textarea>';

        $this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/savedok.gif') . ' title="' . $GLOBALS['LANG']->getLL('labelCmdSave',1) . '" height="16" type="image" width="16">';
        $this->pObj->docHeaderButtons['SAVE_CLOSE'] = '<input class="c-inputButton" name="_saveandclosedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/saveandclosedok.gif') . ' title="' . $GLOBALS['LANG']->getLL('labelCmdSaveClose',1) . '" height="16" type="image" width="16">';
        $this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';

		if (tx_dam::config_checkValueEnabled('mod.txdamM1_SHARED.displayExtraButtons', 1)) {
			$buttons = '
				<input type="submit" name="save" value="'.$GLOBALS['LANG']->getLL('labelCmdSave',1).'" />
				<input type="submit" name="_saveandclosedok_x" value="'.$GLOBALS['LANG']->getLL('labelCmdSaveClose',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';
		}

		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);

		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_edit/class.tx_dam_edit_text.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_edit/class.tx_dam_edit_text.php']);
}

?>