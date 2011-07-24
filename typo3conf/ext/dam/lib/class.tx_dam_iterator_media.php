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
 * Media database row iterator
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   67: class tx_dam_iterator_media extends tx_dam_iterator_db
 *   85:     function &tx_ccdamdl_iterator_media($res, $conf)
 *   95:     function __destruct()
 *
 *              SECTION: Iterator functions
 *  116:     function seek($offset)
 *  127:     function count ()
 *
 *              SECTION: Special iterator functions
 *  146:     function skipCurrent()
 *
 *              SECTION: Internal
 *  166:     function _fetchCurrent()
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');



/**
 * Provides an iterator for a db result of table tx_dam
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_iterator_media extends tx_dam_iterator_db {


	/**
	 * Media object - tx_dam_media
	 */
	 var $media;


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @param	mixed		$res DB result pointer
	 * @param	array		$conf Extra setup data
	 * @return	void
	 * @see __construct()
	 */
	function &tx_ccdamdl_iterator_media($res, $conf) {
		$this->__construct($res, $conf);
	}


	/**
	 * Destructor
	 *
	 * @return	void
	 */
	function __destruct() {
		unset($this->media);
	}




	/***************************************
	 *
	 *	 Iterator functions
	 *
	 ***************************************/



	/**
	 * Set the internal pointer to the offset
	 *
	 * @param	integer		$offset
	 * @return	void
	 */
	function seek($offset) {
		$this->currentPointer = $offset;
		$this->_fetchCurrent();
	}


	/**
	 * Count elements
	 *
	 * @return	integer
	 */
	function count () {
		return $this->countTotal ? $this->countTotal : count($this->res);
	}




	/***************************************
	 *
	 *	 Special iterator functions
	 *
	 ***************************************/


	/**
	 * Returns true if the current element should be skipped.
	 *
	 * @return	boolean
	 */
	function skipCurrent() {
		return (!$this->media->isAvailable);
	}





	/***************************
	 *
	 * Internal
	 *
	 ***************************/


	/**
	 * Fetches the current element
	 *
	 * @return	boolean
	 */
	function _fetchCurrent() {
		if (isset($this->res[$this->currentPointer])) {
			$uid = intval($this->res[$this->currentPointer]);
			unset($this->media);
			$this->media = tx_dam::media_getByUid($uid);
			$this->currentData = $this->media->getMetaInfoArray();

			if ($this->media->isAvailable) {
				// TODO use tx_dam_media
				if (!is_null($this->currentData) AND $this->table AND $this->mode === 'FE') {
					$this->currentData = tx_dam_db::getRecordOverlay($this->table, $this->currentData, array(), $this->mode);
				}
			}

			if ($this->conf['callbackFunc_currentData'] AND is_callable($this->conf['callbackFunc_currentData'])) {
				call_user_func ($this->conf['callbackFunc_currentData'], $this);
			}

		} else {
			unset($this->media);
			$this->currentData = NULL;
		}
	}

}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_media.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_media.php']);
}
?>