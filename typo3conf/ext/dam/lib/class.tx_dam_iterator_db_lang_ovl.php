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
 *   67: class tx_dam_iterator_db_lang_ovl extends tx_dam_iterator_db
 *   93:     function tx_dam_iterator_db_lang_ovl($res, $conf)
 *  105:     function initLanguageOverlay($table, $langUid)
 *
 *              SECTION: language overlay functions
 *  129:     function _fetchCurrent()
 *  148:     function _makeLanguageOverlay()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');


/**
 * Provides an iterator for a db result
 *
 * This version fetches language overlay records if possible.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
class tx_dam_iterator_db_lang_ovl extends tx_dam_iterator_db {


	/**
	 * uid of the wanted language
	 */
	var $langUid;

	/**
	 * Field of the result rows that holds the language uid.
	 */
	var $languageField = 'sys_language_uid';

	/**
	 * Field of the result rows that holds the language uid.
	 */
	var $transOrigPointerField = 'sys_language_uid';


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_iterator_db_lang_ovl($res, $conf) {
		$this->__construct($res, $conf);
	}


	/**
	 * Constructor
	 *
	 * @param	string		Table name
	 * @param	integer		uid of the wanted language
	 * @return	void
	 */
	function initLanguageOverlay($table, $langUid) {
		global $TCA;

		$this->langUid = $langUid;

		$this->table = $table;
		$this->languageField = $TCA[$table]['ctrl']['languageField'];
		$this->transOrigPointerField = $TCA[$table]['ctrl']['transOrigPointerField'];

		$this->_makeLanguageOverlay();
	}

	/***************************************
	 *
	 *	 language overlay functions
	 *
	 ***************************************/


	/**
	 * Fetches the current element
	 *
	 * @return	void
	 */
	function _fetchCurrent() {
		
		if ($this->currentData = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res)) {
			
			if ($this->langUid AND $this->table AND $this->currentData) {
				$this->_makeLanguageOverlay();
			}

			if ($this->conf['callbackFunc_currentData'] AND is_callable($this->conf['callbackFunc_currentData'])) {
				call_user_func ($this->conf['callbackFunc_currentData'], $this);
			}
		} else {
			$this->currentData = NULL;
		}
	}


	/**
	 * Fetches the current element
	 *
	 * @return	void
	 */
	function _makeLanguageOverlay() {
		
		if ($this->langUid AND $this->table AND $this->currentData AND $this->currentData[$this->languageField]!=$this->langUid) {
			
			$conf['sys_language_uid'] = $this->langUid;
			if ($row = tx_dam_db::getRecordOverlay($this->table, $this->currentData, $conf, $this->mode)) {
				$this->currentData = $row;
			}
		}
	}
	
}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_db_lang_ovl.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_db_lang_ovl.php']);
}
?>