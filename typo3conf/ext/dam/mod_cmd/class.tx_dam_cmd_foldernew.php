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
 * Command module 'new folder'
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
 *   66: class tx_dam_cmd_foldernew extends t3lib_extobjbase
 *   77:     function accessCheck()
 *   87:     function head()
 *   97:     function getContextHelp()
 *  109:     function main()
 *  134:     function renderForm()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the file rename command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Folder
 */
class tx_dam_cmd_foldernew extends t3lib_extobjbase {


	var $folderNumber=10;


	/**
	 * Additional access check
	 *
	 * @return	boolean Return true if access is granted
	 */
	function accessCheck() {
		return tx_dam::access_checkFileOperation('newFolder');
	}


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		$GLOBALS['SOBE']->pageTitle = $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:file_newfolder.php.pagetitle');
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
		global  $LANG, $BACK_PATH;

		$content = '';

			// Cleaning and checking target
		$this->folder = tx_dam::path_compileInfo($this->pObj->folder[0]);

		if ($this->folder['dir_accessable']) {
			$content.= $this->renderForm();

		} else {
				// this should have happen in index.php already
			$content.= $this->pObj->accessDeniedMessageBox(tx_dam_guiFunc::getFolderInfoBar($this->folder));
		}

		return $content;
	}


	/**
	 * Making the formfields for folder creation
	 *
	 * @return	string		HTML content
	 */
	function renderForm()	{
		global  $BACK_PATH, $LANG;

		$content = '';
		$msg='<input type="hidden" name="redirect" value="'.htmlspecialchars($this->pObj->redirect).'" />';

		$number = t3lib_div::intInRange(t3lib_div::_GP('number'),1,10);

		$GLOBALS['SOBE']->doc->JScode=$GLOBALS['SOBE']->doc->wrapScriptTags('
			function reload(a)	{	//
				if (!changed || (changed && confirm('.$LANG->JScharCode($LANG->sL('LLL:EXT:lang/locallang_core.xml:mess.redraw')).')))	{
					var params = "&number="+a;
					document.location.href = "'.t3lib_div::linkThisScript().'"+params;
				}
			}

			var changed = 0;
		');


		//$content .='</form><form action="'.$BACK_PATH.'tce_file.php" method="post" name="editform">';

		$this->pObj->markers['FOLDER_INFO'] = tx_dam_guiFunc::getFolderInfoBar($this->folder);

			// Making the selector box for the number of concurrent folder-creations
		$msg.='
			<div id="c-select">
				<select name="number" onchange="reload(this.options[this.selectedIndex].value);">';
		for ($a=1;$a<=$this->folderNumber;$a++)	{
			$msg.='
					<option value="'.$a.'"'.($number==$a?' selected="selected"':'').'>'.$a.' '.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_newfolder.php.folders',1).'</option>';
		}
		$msg.='
				</select>
			</div>
			';

			// Making the number of new-folder boxes needed:
		$msg.='
			<div id="c-createFolders">
			'.$LANG->getLL('foldername', 1).'
		';
		for ($a=0;$a<$number;$a++)	{
			$msg.='
					<div>'.($number>1 ? ($a+1).'. ' : '').'<input'.$GLOBALS['SOBE']->doc->formWidth(20).' type="text" name="file[newfolder]['.$a.'][data]" onchange="changed=true;" />
					<input type="hidden" name="file[newfolder]['.$a.'][target]" value="'.htmlspecialchars($this->folder['dir_path_absolute']).'" /></div>
				';
		}
		$msg.='
			</div>
		';

		$this->pObj->docHeaderButtons['SAVE'] = '<input class="c-inputButton" name="_savedok"' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/savedok.gif') . ' title="' . $LANG->sL('LLL:EXT:lang/locallang_core.xml:file_newfolder.php.submit',1) . '" height="16" type="image" width="16">';
		$this->pObj->docHeaderButtons['CLOSE'] = '<a href="#" onclick="jumpBack(); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/closedok.gif') . ' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" alt="" height="16" width="16"></a>';

		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);

		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_foldernew.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_foldernew.php']);
}


?>
