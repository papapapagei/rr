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
 *   46: class tx_dam_cm_record
 *   50:     function main(&$backRef, $menuItems, $table, $uid)
 *  122:     function createOnClick($url, $dontHide=false)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once (PATH_txdam.'lib/class.tx_dam_actioncall.php');

/**
 * Creates the whole (!) context menu for tx_dam records
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_cm_record {



	function main(&$backRef, $menuItems, $table, $uid)	{
		global $BE_USER, $TCA, $LANG, $TYPO3_CONF_VARS;

			// Returns directly, because the clicked item was rendered by t3lib_TCEforms::getClickMenu()
		if ($backRef->iParts[3]=='+copy,info,edit,view') {
			return $menuItems;
		}

			// Returns directly, because the clicked item was not from the DAM table
		if ($table!='tx_dam')	return $menuItems;

		$this->backRef = &$backRef;
		$item = $backRef->rec;

			// just clear the whole menu
		$menuItems = array();

		if (is_array($backRef->rec))	{
			if ($backRef->cmLevel==0)	{

				t3lib_div::loadTCA($table);

				$calcPerms = $BE_USER->calcPerms(t3lib_BEfunc::getRecord('pages',($table === 'pages'?$backRef->rec['uid']:$backRef->rec['pid'])));
				$permsEdit = ($calcPerms & 16);
				$permsDelete = ($calcPerms & 16);


				$item['__type'] = 'record';
				$item['__table'] = $table;

				$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');

				if (is_array($backRef->disabledItems)) {
					foreach ($backRef->disabledItems as $idName) {
						$actionCall->removeAction ($idName);
					}
				}

				$actionCall->setRequest('context', $item);
				$actionCall->setEnv('returnUrl', t3lib_div::_GP('returnUrl'));
				$actionCall->setEnv('backPath', $backRef->PH_backPath);
				$actionCall->setEnv('defaultCmdScript', PATH_txdam_rel.'mod_cmd/index.php');
				$actionCall->setEnv('defaultEditScript', PATH_txdam_rel.'mod_edit/index.php');
				$actionCall->setEnv('calcPerms', $calcPerms);
				$actionCall->setEnv('permsEdit', $permsEdit);
				$actionCall->setEnv('permsDelete', $permsDelete);
				$actionCall->setEnv('cmLevel', $backRef->cmLevel);
				$actionCall->initActions(true);


				$actions = $actionCall->renderActionsContextMenu(true);
				foreach ($actions as $id => $action) {
					if ($action['isDivider']) {
						$menuItems[$id] = 'spacer';

					} else {
						$onclick = $action['onclick'] ? $action['onclick'] : $this->createOnClick($action['url'], $action['dontHide']);

		                $menuItems[$id] = $backRef->linkItem(
		                    $GLOBALS['LANG']->makeEntities($action['label']),
		                    $backRef->excludeIcon($action['icon']),
		                    $onclick,
		                    $action['onlyCM'],
		                    $action['dontHide']
		                );

					}
				}
			} else {
			}
		}

		return $menuItems;
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



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/be/class.tx_dam_cm_record.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/be/class.tx_dam_cm_record.php']);
}

?>