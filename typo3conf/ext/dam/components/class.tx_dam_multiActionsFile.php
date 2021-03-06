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
 *   77: class tx_dam_multiaction_fileBase extends tx_dam_actionbase
 *  102:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *
 *
 *  123: class tx_dam_multiaction_hideRec extends tx_dam_multiaction_fileBase
 *  136:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  158:     function getLabel ()
 *  168:     function _getCommand()
 *
 *
 *  187: class tx_dam_multiaction_unHideRec extends tx_dam_multiaction_hideRec
 *  194:     function getLabel ()
 *  204:     function _getCommand()
 *
 *
 *  223: class tx_dam_multiaction_deleteRec extends tx_dam_multiaction_fileBase
 *  233:     function getLabel ()
 *  244:     function _getCommand()
 *
 *
 *  263: class tx_dam_multiActionsRecord
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once (PATH_txdam.'lib/class.tx_dam_actionbase.php');



/**
 * Edit record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_multiaction_fileBase extends tx_dam_actionbase {

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('multi');

	/**
	 * If set the action is for items with edit permissions only
	 * @access private
	 */
	var $editPermsNeeded = false;

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
			$valid = ($this->itemInfo['__type'] === 'file' OR $this->itemInfo['__type'] === 'dir');
		}
		return $valid;
	}

}





/**
 * Delete record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_multiaction_deleteFile extends tx_dam_multiaction_fileBase {

	var $cmd = 'tx_dam_cmd_filedelete';


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {
		
		$script = $this->env['defaultCmdScript'];
		$script .= '?CMD='.$this->cmd;
		$script .= '&vC='.$GLOBALS['BE_USER']->veriCode();
		$script .= '&returnUrl='.rawurlencode($this->env['returnUrl']);
		$script .= '&###PARAMLIST###';

		$commands['action'] = 'url:'.$script;

		return $commands;
	}
}



/**
 * Copy file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_multiaction_copyFile extends tx_dam_multiaction_deleteFile {

	var $cmd = 'tx_dam_cmd_filecopy';


	/**
	 * Returns the short label like: Copy
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.copy');
	}
}



/**
 * Move file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_multiaction_moveFile extends tx_dam_multiaction_deleteFile {

	var $cmd = 'tx_dam_cmd_filemove';


	/**
	 * Returns the short label like: Move
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:cm.move');
	}
}




class tx_dam_multiActionsFile {
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_multiActionsFile.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_multiActionsFile.php']);
}

?>