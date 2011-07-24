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
 * @package DAM-BeLib
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   62: class tx_dam_listPointer
 *  149:     function init ($page, $itemsPerPage, $countTotal=0, $maxPages=100)
 *  165:     function setPagePointer ($page)
 *  177:     function setTotalCount($countTotal)
 *
 *              SECTION: Get calculated values
 *  199:     function getPagePointer ($offset=0)
 *
 *              SECTION: Internal calculation
 *  223:     function calc ()
 *  246:     function getDebugArray()
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


/**
 * selection counter and pointers
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see tx_dam_SCbase
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_listPointer {


	/***************************************
	 *
	 *	 config values
	 *
	 ***************************************/

	/**
	 * Define how many items to list per page
	 * 1 - 1000
	 */
	var $itemsPerPage;


	/**
	 * Max allowed pages
	 */
	var $maxPages;



	/***************************************
	 *
	 *	 current values
	 *
	 ***************************************/

	/**
	 * page pointer
	 * 0 is first page
	 */
	var $page;

	/**
	 * Define the total count of items
	 */
	var $countTotal;



	/***************************************
	 *
	 *	 calculated values
	 *
	 ***************************************/

	/**
	 * the first page pointer (aleways 0)
	 */
	var $firstPage = 0;

	/**
	 * the last page pointer
	 */
	var $lastPage;

	/**
	 * $page * $itemsPerPage + 1
	 */
	var $firstItemNum;

	/**
	 * The last item number
	 */
	var $lastItemNum;

	/**
	 * $lastItemNum - $firstItemNum
	 */
	var $countItems;


	var $pagePointerParamName = 'SET[tx_dam_resultPointer]';


	/**
	 * Initialize the pointer
	 * Calcuate the other values
	 *
	 * @param	integer		$page Page pointer
	 * @param	integer		$itemsPerPage Defines the items per page
	 * @param	integer		$countTotal Can be set to the total count of items.
	 * @param	integer		$maxPages Max allowed pages.
	 * @return	void
	 */
	function init ($page, $itemsPerPage, $countTotal=0, $maxPages=100) {
		$this->page = intval($page);
		$this->itemsPerPage = t3lib_div::intInRange(intval($itemsPerPage), 1, 1000);
		$this->countTotal = intval($countTotal);
		$this->maxPages = t3lib_div::intInRange(intval($maxPages), 1, 100);

		$this->calc();
	}


	/**
	 * Set the pointer
	 *
	 * @param	integer		$page Page pointer
	 * @return	void
	 */
	function setPagePointer ($page) {
		$this->page = intval($page);
		$this->calc();
	}


	/**
	 * Set the total count of items
	 *
	 * @param	integer		$page Page pointer
	 * @return	void
	 */
	function setTotalCount($countTotal) {
		$this->countTotal = intval($countTotal);
		$this->calc();
	}




	/***************************************
	 *
	 *	 Get calculated values
	 *
	 ***************************************/



	/**
	 * Get the pointer. Might be different of the initial value because of recalculation.
	 *
	 * @param	integer		$page Page pointer offset
	 * @return	integer		$page Page pointer
	 */
	function getPagePointer ($offset=0) {
		$page = $this->page+$offset;

		$page = t3lib_div::intInRange($page, 0, $this->lastPage);

		return $page;
	}




	/***************************************
	 *
	 *	 Internal calculation
	 *
	 ***************************************/



	/**
	 * Check and caclulates the current values
	 *
	 * @return	void
	 */
	function calc () {
		if ($this->countTotal) {
			if (($this->page * $this->itemsPerPage) > $this->countTotal) {
				$this->page = floor($this->countTotal / $this->itemsPerPage);
			}
		}

		$this->firstItemNum = $this->page * $this->itemsPerPage;
		$this->lastItemNum = min($this->firstItemNum + $this->itemsPerPage, max(0, $this->countTotal-1));

		$this->lastPage = t3lib_div::intInRange(ceil($this->countTotal / $this->itemsPerPage) - 1, 0, $this->maxPages);

		$this->countItems = ($this->lastItemNum+1)-$this->firstItemNum;
		$this->countItems = min($this->countItems, $this->countTotal);

	}


	/**
	 * debug output
	 *
	 * @return	void
	 */
	function getDebugArray() {

		return array(
			'countTotal' => $this->countTotal,
			'firstItemNum' => $this->firstItemNum,
			'lastItemNum' => $this->lastItemNum,
			'page' => $this->page,
			'lastPage' => $this->lastPage,
			'countItems' => $this->countItems,
			'itemsPerPage' => $this->itemsPerPage,
			'maxPages' => $this->maxPages,
		);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listpointer.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listpointer.php']);
}
?>