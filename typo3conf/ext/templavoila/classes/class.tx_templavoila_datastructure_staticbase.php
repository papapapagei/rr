<?php
/***************************************************************
* Copyright notice
*
* (c) 2010 Tolleiv Nietsch <nietsch@aoemedia.de>
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
 * Class to provide unique access to datastructure
 *
 * @author	Tolleiv Nietsch <nietsch@aoemedia.de>
 */
class tx_templavoila_datastructure_staticbase extends tx_templavoila_datastructure {

	protected $filename;
	/**
	 *
	 * @param integer $uid
	 */
	public function __construct($key) {

		$conf = $GLOBALS['TBE_MODULES_EXT']['xMOD_tx_templavoila_cm1']['staticDataStructures'][$key];

		$this->filename = $conf['path'];

		$this->setLabel($conf['title']);
		$this->setScope($conf['scope']);
			// path relative to typo3 maindir
		$this->setIcon( '../' . $conf['icon']);
	}

	/**
	 *
	 * @return string;
	 */
	public function getStoragePids() {
		$pids = array();
		$toList = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'tx_templavoila_tmplobj.uid,tx_templavoila_tmplobj.pid',
			'tx_templavoila_tmplobj',
			'tx_templavoila_tmplobj.datastructure=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->filename, 'tx_templavoila_tmplobj') . t3lib_BEfunc::deleteClause('tx_templavoila_tmplobj')
		);
		foreach ($toList as $toRow) {
			$pids[$toRow['pid']]++;
		}
		return implode(',', array_keys($pids));
	}

	/**
	 *
	 * @return string - the filename
	 */
	public function getKey() {
		return $this->filename;
	}

	/**
	 * Determine whether the current user has permission to create elements based on this
	 * datastructure or not - not really useable for static datastructure but relevant for
	 * the overall system
	 *
	 * @param mixed $parentRow
	 * @param mixed $removeItems
	 * @return boolean
	 */
	public function isPermittedForUser($parentRow = array(), $removeItems = array()) {
		return TRUE;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/classes/class.tx_templavoila_datastructure_staticbase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/classes/class.tx_templavoila_datastructure_staticbase.php']);
}
?>