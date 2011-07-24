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
class tx_templavoila_datastructure_dbbase extends tx_templavoila_datastructure {

	protected $row;

	/**
	 *
	 * @param integer $uid
	 */
	public function __construct($uid) {
		$this->row = t3lib_beFunc::getRecordWSOL('tx_templavoila_datastructure', $uid);

		$this->setLabel($this->row['title']);
		$this->setScope($this->row['scope']);
			// path relative to typo3 maindir
		$this->setIcon( '../uploads/tx_templavoila/' . $this->row['previewicon']);
	}

	/**
	 *
	 * @return string;
	 */
	public function getStoragePids() {
		return $this->row['pid'];
	}

	/**
	 *
	 * @return string - numeric string
	 */
	public function getKey() {
		return $this->row['uid'];
	}

	/**
	 * Determine whether the current user has permission to create elements based on this
	 * datastructure or not
	 *
	 * @param mixed $parentRow
	 * @param mixed $removeItems
	 * @return boolean
	 */
	public function isPermittedForUser($parentRow = array(), $removeItems = array()) {
		if ($GLOBALS['BE_USER']->isAdmin()) {
			return TRUE;
		} else if(in_array($this->getKey(), $removeItems)) {
			return FALSE;
		}
		$permission = TRUE;
		$denyItems = tx_templavoila_div::getDenyListForUser();

		$currentSetting = $parentRow['tx_templavoila_ds'];
		if ($this->getScope() == tx_templavoila_datastructure::SCOPE_PAGE) {
			$inheritSetting = $parentRow['tx_templavoila_next_ds'];
		} else {
			$inheritSetting = -1;
		}

		$key = 'tx_templavoila_datastructure:' . $this->getKey();
		if (in_array($key, $denyItems) &&
			$key != $currentSetting &&
			$key != $inheritSetting
		) {
			$permission = FALSE;
		}
		return $permission;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/classes/class.tx_templavoila_datastructure_dbbase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/classes/class.tx_templavoila_datastructure_dbbase.php']);
}
?>
