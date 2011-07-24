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
 * Data row iterator base class
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
 *   76: class tx_dam_iterator_base
 *  120:     function tx_dam_iterator_base($res, $conf)
 *  134:     function &__construct($res, $conf)
 *  152:     function __destruct()
 *
 *              SECTION: Iterator functions
 *  169:     function rewind()
 *  179:     function valid()
 *  198:     function next()
 *  209:     function seek($offset)
 *  225:     function key()
 *  235:     function current()
 *  245:     function count()
 *
 *              SECTION: Special iterator functions
 *  266:     function skipCurrent()
 *
 *              SECTION: Internal
 *  285:     function _fetchCurrent()
 *
 * TOTAL FUNCTIONS: 12
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */






/**
 * Provides an iterator base class for data eg. db result
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
class tx_dam_iterator_base {

	/**
	 * The database table - if needed
	 */
	var $table;

	/**
	 * result pointer
	 */
	var $res;

	/**
	 * additional setup array
	 */
	var $conf;

	/**
	 * The current entry. Data array or NULL.
	 */
	var $currentData = NULL;

	/**
	 * Used to define the current entry.
	 */
	var $currentPointer = 0;

	/**
	 * total count of rows
	 * Can be set to avoud using sql_num_rows() when amount of rows is already known.
	 */
	 var $countTotal = 0;



	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @param	mixed		$res DB result pointer
	 * @param	array		$conf Extra setup data
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_iterator_base($res, $conf) {
		$this->__construct($res, $conf);
	}


	/**
	 * Initialize the object
	 * PHP5 constructor
	 *
	 * @param	mixed		$res DB result pointer
	 * @param	array		$conf Extra setup data
	 * @return	void
	 * @see __construct()
	 */
	function &__construct($res, $conf) {
		$this->res = $res;
		$this->conf = $conf;
		$this->mode = TYPO3_MODE;
		if (is_array($conf)) {
			$this->countTotal = $conf['countTotal'];
			$this->table = $conf['table'];
			$this->mode = $conf['mode'] ? $conf['mode'] : TYPO3_MODE;
		}
		$this->_fetchCurrent();
	}


	/**
	 * Destructor
	 *
	 * @return	void
	 */
	function __destruct() {
	}



	/***************************************
	 *
	 *	 Iterator functions
	 *
	 ***************************************/


	/**
	 * Set the internal pointer to its first element.
	 *
	 * @return	void
	 */
	function rewind() {
		$this->seek(0);
	}


	/**
	 * Return true is the current element is valid.
	 *
	 * @return	boolean
	 */
	function valid() {
		static $cnt=0;
		static $pnt=0;

		$cnt++;
		if ($cnt > 100000 AND ($pnt == $this->currentPointer)) {
			die ('It seems the iterator goes wild! Forgot to call next()? ('.__FILE__.')');
		}
		$pnt = $this->currentPointer;

		return (!is_null($this->currentData));
	}


	/**
	 * Advance the internal pointer
	 *
	 * @return	void
	 */
	function next() {
		$this->currentData = NULL;
	}


	/**
	 * Set the internal pointer to the offset
	 *
	 * @param	integer		$offset
	 * @return	void
	 */
	function seek($offset) {
		if ($offset==0) {
			$this->_fetchCurrent();
			$this->currentPointer = $offset;
		} else {
			$this->currentData = NULL;
			$this->currentPointer = NULL;
		}
	}


	/**
	 * Return the pointer to the current element
	 *
	 * @return	mixed
	 */
	function key() {
		return $this->currentPointer;
	}


	/**
	 * Return the current element
	 *
	 * @return	array
	 */
	function current() {
		return $this->currentData;
	}


	/**
	 * Count elements
	 *
	 * @return	integer
	 */
	function count() {
		return $this->countTotal;
	}




	/***************************************
	 *
	 *	 Special iterator functions
	 *
	 ***************************************/


	/**
	 * Returns true if the current element should be skipped.
	 * This can happen when during fetching data it turns out that the data is not valid but the list is still valid.
	 * Please notice that the total count gives a wrong value then.
	 *
	 * @return	boolean
	 */
	function skipCurrent() {
		return false;
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
		$this->currentData = NULL;

		if ($this->conf['callbackFunc_currentData'] AND is_callable($this->conf['callbackFunc_currentData'])) {
			call_user_func ($this->conf['callbackFunc_currentData'], $this);
		}
	}

}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_base.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_base.php']);
}
?>