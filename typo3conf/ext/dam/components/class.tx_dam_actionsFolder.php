<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Action
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   78: class tx_dam_action_newFolder extends tx_dam_actionbase
 *   96:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  112:     function getIcon ($addAttribute='')
 *  130:     function getLabel ()
 *  144:     function getDescription ()
 *  155:     function getWantedDivider ($type)
 *  170:     function _getCommand()
 *
 *
 *  201: class tx_dam_action_renameFolder extends tx_dam_actionbase
 *  223:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  239:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  255:     function getIcon ($addAttribute='')
 *  273:     function getLabel ()
 *  283:     function getDescription ()
 *  294:     function _getCommand()
 *
 *
 *  322: class tx_dam_action_deleteFolder extends tx_dam_action_renameFolder
 *  340:     function getIcon ($addAttribute='')
 *  358:     function getLabel ()
 *  368:     function getDescription ()
 *
 *
 *  374: class tx_dam_actionsFolder
 *
 * TOTAL FUNCTIONS: 15
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once (PATH_txdam.'lib/class.tx_dam_actionbase.php');


/**
 * New folder action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_newFolder extends tx_dam_actionbase {

	var $cmd = 'tx_dam_cmd_foldernew';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'button', 'globalcontrol', 'context');

	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		
		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid) {
			$valid = ($this->itemInfo['__type'] === 'dir');
		}
		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = PATH_txdam_rel.'i/new_webfolder_i.gif';
		} else {
			$iconFile = PATH_txdam_rel.'i/new_webfolder.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="17" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		if ($this->type === 'button') {
			return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:folder');
		} else {
			return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:newFolder');
		}
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:newFolder');
	}


	/**
	 * Tells if a spacer/margin is wanted before/after the action
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: "divider:spacer". Divider before and spacer after
	 */
	function getWantedDivider ($type) {
		$divider = '';
		if ($type === 'context') {
			$divider = 'divider:';
		}
		return $divider;
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$path = $this->itemInfo['dir_path_absolute'];

		$script = $this->env['defaultCmdScript'];
		$script .= '?CMD='.$this->cmd;
		$script .= '&vC='.$GLOBALS['BE_USER']->veriCode();
		$script .= '&folder='.rawurlencode($path);

		if ($this->type === 'context') {
			$commands['url'] = $script;
		} else {
			$script .= '&returnUrl='.rawurlencode($this->env['returnUrl']);
			$commands['href'] = $script;
		}

		return $commands;
	}

}



/**
 * Rename file action
 *
 * @author	Peter Kuehn <peter.kuehn@wmdb.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_renameFolder extends tx_dam_actionbase {

	var $cmd = 'tx_dam_cmd_folderrename';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control', 'context');


	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] === 'dir');
		}
		return $valid;
	}


	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid)	{
			$valid = ($this->itemInfo['__type'] === 'dir' && !t3lib_div::inList('fileadmin,user_upload,_temp_', $this->itemInfo['dir_name']));
		}
		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = PATH_txdam_rel.'i/rename_file_i.gif';
		} else {
			$iconFile = PATH_txdam_rel.'i/rename_file.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="13" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.rename');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.rename');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {
		$path = $this->itemInfo['dir_path_absolute'];

		$script = $this->env['defaultCmdScript'];
		$script .= '?CMD='.$this->cmd;
		$script .= '&vC='.$GLOBALS['BE_USER']->veriCode();
		$script .= '&folder='.rawurlencode($path);

		if ($this->type === 'context') {
			$commands['url'] = $script;
		} else {
			$script .= '&returnUrl='.rawurlencode($this->env['returnUrl']);
			$commands['href'] = $script;
		}

		return $commands;
	}

}

/**
 * Delete file action
 *
 * @author	Peter Kuehn, <peter.kuehn@wmdb.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_deleteFolder extends tx_dam_action_renameFolder {

	var $cmd = 'tx_dam_cmd_folderdelete';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control', 'context');


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = 'gfx/delete_record_i.gif';
		} else {
			$iconFile = 'gfx/delete_record.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete');
	}
}


class tx_dam_actionsFolder {
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_actionsFolder.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_actionsFolder.php']);
}

?>