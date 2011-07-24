<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   46: class tx_dam_cm_file
 *   48:     function main(&$backRef, $menuItems, $file, $uid)
 *  120:     function createOnClick($url, $dontHide=false)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once (PATH_txdam.'lib/class.tx_dam_actioncall.php');

/**
 * Creates the whole (!) context menu for files (tx_dam records)
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_cm_file {

	function main(&$backRef, $menuItems, $file, $uid) {

		// Returns directly, because the clicked item was not a file
		if ($backRef->cmLevel == 0 && $uid != '') {
			return $menuItems;
		}

		// Returns directly, because the clicked item was not the second level menu from DAM records
		if ($backRef->cmLevel == 1 && t3lib_div::_GP('subname') != 'tx_dam_cm_file') {
			return $menuItems;
		}

		$this->backRef = &$backRef;

			// this is second level menu from DAM records
		$fileDAM = t3lib_div::_GP('txdamFile');
		$file = ($fileDAM ? $fileDAM : $file);

		if (@is_file($file)) {
			$item        = tx_dam::file_compileInfo($file);
			$permsEdit   = (tx_dam::access_checkFile($item) && tx_dam::access_checkFileOperation('editFile'));
			$permsDelete = (tx_dam::access_checkFile($item) && tx_dam::access_checkFileOperation('deleteFile'));
		} elseif (@is_dir($file)) {
			$item        = tx_dam::path_compileInfo($file);
			$permsEdit   = (tx_dam::access_checkPath($item) && tx_dam::access_checkFileOperation('renameFolder'));
			$permsDelete = (tx_dam::access_checkPath($item) && tx_dam::access_checkFileOperation('deleteFolder'));
		} else {
			return $menuItems;
		}

		// clear the existing menu now and fill it with DAM specific things
		$damMenuItems = array();

		// see typo3/alt_clickmenu.php:clickmenu::enableDisableItems() for iParts[3]
		// which is called after this function
		$backRef->iParts[3] = '';


		$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');
				
		if (is_array($backRef->disabledItems)) {
			foreach ($backRef->disabledItems as $idName) {
				$actionCall->removeAction($idName);
			}
		}		
				
		$actionCall->setRequest('context', $item);
		$actionCall->setEnv('returnUrl', t3lib_div::_GP('returnUrl'));
		$actionCall->setEnv('backPath', $backRef->PH_backPath);
		$actionCall->setEnv('defaultCmdScript', PATH_txdam_rel.'mod_cmd/index.php');
		$actionCall->setEnv('defaultEditScript', PATH_txdam_rel.'mod_edit/index.php');
		$actionCall->setEnv('actionPerms',  tx_dam::access_checkFileOperation());
		$actionCall->setEnv('permsEdit', $permsEdit);
		$actionCall->setEnv('permsDelete', $permsDelete);
		$actionCall->setEnv('cmLevel', $backRef->cmLevel);
		$actionCall->setEnv('cmParent', t3lib_div::_GP('parentname'));
		$actionCall->initActions(true);


		$actions = $actionCall->renderActionsContextMenu(true);
		foreach ($actions as $id => $action) {
			if ($action['isDivider']) {
				$damMenuItems[$id] = 'spacer';
			} else {
				$onclick = $action['onclick'] ? $action['onclick'] : $this->createOnClick($action['url'], $action['dontHide']);

				$damMenuItems[$id] = $backRef->linkItem(
						$GLOBALS['LANG']->makeEntities($action['label']),
						$backRef->excludeIcon($action['icon']),
						$onclick,
						$action['onlyCM'],
						$action['dontHide']
				);
			}
		}

		// clear the file context menu, allow additional items from extensions,
		// like TemplaVoila, and the display constraints 
		// once a DAM file is found
		foreach ($menuItems as $key => $var) {
			if (!t3lib_div::inList('edit,rename,info,copy,cut,delete', $key) && !array_key_exists($key, $damMenuItems)) {
				$damMenuItems[$key] = $var;		
			}
		}

		return $damMenuItems;
	}


	/**
	 * create onclick stuff for an url
	 *
	 * @param	string		Script (eg. file_edit.php) relative to typo3/
	 * @param	boolean		If set, the clickmenu layer will not hide itself onclick - used for secondary menus to appear...
	 * @return	string		onclick stuff
	 */
	function createOnClick($url, $dontHide=false)	{

		if (!strpos($url, '?')) {
			$url .= '?';
		}

		$loc='top.content'.($this->backRef->listFrame && !$this->backRef->alwaysContentFrame ?'.list_frame':'');
		$editOnClick='if('.$loc.'){'.$loc.".location.href=top.TS.PATH_typo3+'".$url."&returnUrl='+top.rawurlencode(".$this->backRef->frameLocation($loc.'.document').');'.($dontHide?'':' hideCM();').'};';

		return $editOnClick.'return false;';
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/be/class.tx_dam_cm_file.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/be/class.tx_dam_cm_file.php']);
}

?>
