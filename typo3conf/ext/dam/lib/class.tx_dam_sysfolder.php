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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   60: class tx_dam_sysfolder
 *   68:     function init()
 *   90:     function getAvailable()
 *  104:     function getPidList()
 *  115:     function create($pid=0)
 *  139:     function collectLostRecords($pid=NULL, $forceAll=true)
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




/**
 * DAM sysfolder functions
 * The DAM uses a sysfolder for DAM record storage. The sysfolder will be created automatically.
 * In principle it could be possible to use more than one sysfolder. But that concept is not easy to handle and therefore not used yet.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_sysfolder {


	/**
	 * Find the DAM folders or create one.
	 *
	 * @return	integer		The uid of the default sysfolder
	 */
	function init()	{

		if (!is_object($GLOBALS['TYPO3_DB'])) return false;

		$damFolders = tx_dam_sysfolder::getAvailable();
		if (!count($damFolders)) {
				// creates a DAM folder on the fly
			tx_dam_sysfolder::create();
			$damFolders = tx_dam_sysfolder::getAvailable();
		}
		$df = current($damFolders);

		return $df['uid'];
	}


	/**
	 * Find the DAM folders and return an array of record arrays.
	 *
	 * @return	array		Array of rows of found DAM folders with fields: uid, pid, title. An empty array will be returned if no folder was found.
	 */
	function getAvailable() {
		$rows=array();
		if ($damFolders = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,pid,title,doktype', 'pages', 'module='.$GLOBALS['TYPO3_DB']->fullQuoteStr('dam', 'pages').' AND deleted=0', '', '', '', 'uid')) {
			$rows = $damFolders;
		}
		return $rows;
	}


	/**
	 * Returns pidList/list of pages uid's of DAM Folders
	 *
	 * @return	string		Commalist of PID's
	 */
	function getPidList() {
		return implode(',',array_keys(tx_dam_sysfolder::getAvailable()));
	}


	/**
	 * Create a DAM folders
	 *
	 * @param	integer		$pid The PID of the sysfolder which is by default 0 to place the folder in the root.
	 * @return	void
	 */
	function create($pid=0) {
		$fields_values = array();
		$fields_values['pid'] = $pid;
		$fields_values['sorting'] = 29999;
		$fields_values['perms_user'] = 31;
		$fields_values['perms_group'] = 31;
		$fields_values['perms_everybody'] = 31;
		$fields_values['title'] = 'Media';
		$fields_values['doktype'] = 254; // sysfolder
		$fields_values['module'] = 'dam';
		$fields_values['crdate'] = time();
		$fields_values['tstamp'] = time();
		return $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $fields_values);
	}


	/**
	 * Move lost DAM records to the DAM sysfolder.
	 * This is a maintance function.
	 *
	 * @param	integer		$pid If set this PID will be used as storage sysfolder for the lost folder.
	 * @param	boolean		$forceAll If true (default) all DAM records will be moved not only the ony with pid=0.
	 * @return	void
	 */
	function collectLostRecords($pid=NULL, $forceAll=true)	{

		$pid = $pid ? $pid : tx_dam_db::getPid();

		if ($pid) {

			$mediaTables = tx_dam::register_getEntries('mediaTable');
			$values = array ('pid' => $pid);

			if($forceAll) {
				foreach ($mediaTables as $table) {
					$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $table.'.pid NOT IN ('.tx_dam_sysfolder::getPidList().')', $values);
				}
			} else {
				foreach ($mediaTables as $table) {
					$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $table.'.pid=0', $values);
				}
			}
		}
	}




}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_sysfolder.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_sysfolder.php']);
}

?>