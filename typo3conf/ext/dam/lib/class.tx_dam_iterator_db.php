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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
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
 * @package DAM-BeLib
 * @subpackage Iterator
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   69: class tx_dam_iterator_db extends tx_dam_iterator_base
 *   83:     function tx_dam_iterator_db($res, $conf)
 *   95:     function __destruct()
 *
 *              SECTION: Iterator functions
 *  115:     function next()
 *  127:     function seek($offset)
 *  141:     function count ()
 *
 *              SECTION: Internal
 *  161:     function _fetchCurrent()
 *  183:     function _fetchCurrentOverlayData()
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam_iterator_base.php');



/**
 * Provides an iterator for a db result
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
class tx_dam_iterator_db extends tx_dam_iterator_base {




	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @param	mixed		$res DB result pointer
	 * @param	array		$conf Extra setup data
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_iterator_db($res, $conf) {
		$this->__construct($res, $conf);
	}




	/**
	 * Destructor
	 *
	 * @return	void
	 */
	function __destruct() {
		$GLOBALS['TYPO3_DB']->sql_free_result($this->res);
	}




	/***************************************
	 *
	 *	 Iterator functions
	 *
	 ***************************************/



	/**
	 * Advance the internal pointer
	 *
	 * @return	void
	 */
	function next() {
		$this->currentPointer ++;
		$this->_fetchCurrent();
	}


	/**
	 * Set the internal pointer to the offset
	 *
	 * @param	integer		$offset
	 * @return	void
	 */
	function seek($offset) {
		$this->currentPointer = $offset;
		$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, $offset);
		$this->_fetchCurrent();
	}




	/**
	 * Count elements
	 *
	 * @return	integer
	 */
	function count () {
		return $this->countTotal ? $this->countTotal : $GLOBALS['TYPO3_DB']->sql_num_rows($this->res);
	}





	/***************************
	 *
	 * Internal
	 *
	 ***************************/


	/**
	 * Fetches the current element
	 *
	 * @return	void
	 */
	function _fetchCurrent() {
		if ($this->currentData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res)) {
			
			if (!is_null($this->currentData) AND $this->table AND $this->mode === 'FE') {
				$this->currentData = tx_dam_db::getRecordOverlay($this->table, $this->currentData, array(), $this->mode);
			}

			if ($this->conf['callbackFunc_currentData'] AND is_callable($this->conf['callbackFunc_currentData'])) {
				call_user_func ($this->conf['callbackFunc_currentData'], $this);
			}
		} else {
			$this->currentData = NULL;
		}
	}

}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_db.php']);
}
?>