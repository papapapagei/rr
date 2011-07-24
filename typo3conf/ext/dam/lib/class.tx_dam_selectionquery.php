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
 *   78: class tx_dam_selectionQuery
 *  113:     function initPointer($resultPointer, $resultsPerPage, $maxPages=100)
 *  132:     function initSelection(&$SOBE, $selectionClasses, $paramPrefix, $store_MOD_SETTINGS)
 *  145:     function initQueryGen()
 *
 *              SECTION: General selection handling
 *  166:     function processSubmittedSelection()
 *  178:     function addSelectionToQuery ()
 *  192:     function addLimitToQuery ($limit='', $begin='')
 *  207:     function prepareSelectionQuery($count=false)
 *  218:     function getSelectionQueryParts($count=false)
 *  232:     function execSelectionQuery($count=false, $select='')
 *  251:     function execQuery($count=false, $select='')
 *
 *              SECTION: Special selection handling
 *  306:     function addFilemountsToQuerygen()
 *  332:     function addDefaultFilter()
 *  343:     function setSelectionLanguage($sysLanguage=0)
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_txdam.'lib/class.tx_dam_selection.php');
require_once(PATH_txdam.'lib/class.tx_dam_querygen.php');
require_once(PATH_txdam.'lib/class.tx_dam_listpointer.php');



/**
 * Selection / QueryGenerator
 *
 * Generates SQL queries from selection commands by calling registered selection classes
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_selectionQuery {


	/**
	 * Selection object
	 * @var object
	 */
	var $sl;

	/**
	 * Query generator object
	 * @var object
	 */
	var $qg;

	/**
	 * Pointer object
	 * @var integer
	 */
	var $pointer;



	/**
	 * Current SQL result
	 * @var mixed
	 */
	var $res = false;



	/**
	 * If set the user has access check is extended to other selection trees, not only for file mounts
	 * @var boolean
	 */
	var $additionalAccessLimit = false;
	
	

	/**
	 * Initializes the pointer object
	 *
	 * @param 	integer 	$resultPointer The current pointer value - may come from GET var
	 * @param 	integer 	$resultsPerPage Defines how many items should be displayed per page
	 * @param	integer		$maxPages Max allowed pages.
	 * @return void
	 */
	function initPointer($resultPointer, $resultsPerPage, $maxPages=100) {
		global $TYPO3_CONF_VARS;

		$this->pointer = t3lib_div::makeInstance('tx_dam_listPointer');
		$this->pointer->init(intval($resultPointer), $resultsPerPage, 0, $maxPages);

		return $this->pointer->getPagePointer();
	}


	/**
	 * Initializes the selection object
	 *
	 * @param	object		$SOBE That is the object that is the module and stores the session data
	 * @param 	array 		$selectionClasses Array of class resources
	 * @param	string		$paramPrefix Name of a prefix used for special commands (undo).
	 * @param	string		$store_MOD_SETTINGS Name of the MOD_SETTINGS key to store the selection.
	 * @return	void
	 */
	function initSelection(&$SOBE, $selectionClasses, $paramPrefix, $store_MOD_SETTINGS)	{
		global $TYPO3_CONF_VARS;
		
		$this->SOBE = & $SOBE;

		$this->sl = t3lib_div::makeInstance('tx_dam_selection');
		$this->sl->init($this, $SOBE, $selectionClasses, $paramPrefix, $store_MOD_SETTINGS);
	}


	/**
	 * Initializes the query generator object
	 *
	 * @return	void
	 */
	function initQueryGen()	{
		global $TYPO3_CONF_VARS;
		
		$this->qg = t3lib_div::makeInstance('tx_dam_querygen');
	}







	/********************************
	 *
	 * General selection handling
	 *
	 ********************************/

	/**
	 * Get the users last stored selection or processes an undo command
	 *
	 * @return	void
	 */
	function processSubmittedSelection() {
		$this->sl->initSelection_getStored_mergeSubmitted();
		if ($this->sl->hasChanged) {
			$this->pointer->setPagePointer(0);
		}
	}

	/**
	 * Adds the current selection to the query
	 *
	 * @return	void
	 */
	function addSelectionToQuery () {
		if($this->sl->hasSelection()) {
			$this->qg->mergeWhere($this->sl->getSelectionWhereClauseArray());
	
			if ($this->additionalAccessLimit) {
				$this->qg->mergeWhere($this->sl->getRestrictAccessWhereClauseArray());
			}
		}
	}


	/**
	 * Adds a LIMIT to the query
	 *
	 * @param	integer		$limit
	 * @param	integer		$begin
	 * @return	void
	 */
	function addLimitToQuery ($limit='', $begin='') {
		if(intval($limit) == 0) {
			$limit = $this->pointer->itemsPerPage;
			$begin = $this->pointer->firstItemNum;
		}
		$this->qg->addLimit ($limit, $begin);
	}


	/**
	 * Set the current query as count query
	 *
	 * @param	boolean		$count If set count query will be generated
	 * @return	void
	 */
	function prepareSelectionQuery($count=false) {
		$this->qg->setCount($count);
	}


	/**
	 * Generates the query from the db select array.
	 *
	 * @param	boolean		$count If set count query will be generated
	 * @return	array		Query parts array
	 */
	function getSelectionQueryParts($count=false) {
		$this->prepareSelectionQuery($count);
		$query = $this->qg->getQueryParts();
		return $query;
	}


	/**
	 * Executes the query from the db select array.
	 *
	 * @param	boolean		$count If set count query will be generated
	 * @param	string		$select Overrule SELECT query part
	 * @return	mixed		Query result pointer
	 */
	function execSelectionQuery($count=false, $select='') {

		if(!$this->sl->hasSelection() AND !$select) {
			$this->pointer->setTotalCount(0);
			$this->res = false;
			return $this->res;
		}

		return $this->execQuery($count, $select);
	}


	/**
	 * Executes the query from the db querygen array.
	 *
	 * @param	boolean		$count If set count query will be generated
	 * @param	string		$select Overrule SELECT query part
	 * @return	mixed		Query result pointer
	 */
	function execQuery($count=false, $select='') {

		$this->prepareSelectionQuery($count);
		$queryArr = $this->qg->getQueryParts();
		if ($select) {
			$queryArr['SELECT'] = $select;
		}
		$this->error = '';
		$this->res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryArr);
		$this->error = $GLOBALS['TYPO3_DB']->sql_error();

		if($count) {
			if ($this->res) {
				$numRows = $GLOBALS['TYPO3_DB']->sql_num_rows($this->res);
				if ($numRows > 1) {
					$this->pointer->setTotalCount($numRows);
				} else {
					list($countTotal) = $GLOBALS['TYPO3_DB']->sql_fetch_row($this->res);
					$this->pointer->setTotalCount($countTotal);
				}
			} else {
				$this->pointer->setTotalCount(0);
			}
		}


			// collect debug information
		if (tx_dam::config_getValue('setup.devel')) {
				if ($this->error OR !$count OR $countTotal==0) {
					if ($this->error) $this->SOBE->debugContent['queryArr'] = '<h4>ERROR</h4>'.$this->error;
					$this->SOBE->debugContent['queryArr'] = '<h4>$queryArr</h4>'.t3lib_div::view_array($queryArr);
					$query = $GLOBALS['TYPO3_DB']->SELECTquery(
								$queryArr['SELECT'],
								$queryArr['FROM'],
								$queryArr['WHERE'],
								$queryArr['GROUPBY'],
								$queryArr['ORDERBY'],
								$queryArr['LIMIT']
							);
					$this->SOBE->debugContent['query'] = '<h4>$query</h4>'.($query);
				}
			$this->SOBE->debugContent['Pointer'] = '<h4>Pointer</h4>'.t3lib_div::view_array($this->pointer->getDebugArray());
		}
		return $this->res;
	}



	/********************************
	 *
	 * Special selection handling
	 *
	 ********************************/


	/**
	 * Initializes the query with file mounts to limit access
	 *
	 * @return	void
	 */
	function addFilemountsToQuerygen() {
			// init filemounts
		if(is_object($GLOBALS['BE_USER']) AND !$GLOBALS['BE_USER']->isAdmin()) {
			if (count($GLOBALS['FILEMOUNTS'])){
				$whereArr = array();
				foreach($GLOBALS['FILEMOUNTS'] as $mount){
					$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike(tx_dam::path_makeRelative($mount['path']), 'tx_dam');
					$whereArr[] = 'tx_dam.file_path LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr.'%', 'tx_dam');
				}
				$where = implode(' OR ', $whereArr);
				$where = $where ? '('.$where.')' : '';
				$this->qg->addWhere($where, 'AND', 'tx_dam.FILEMOUNTS');

			} else {
					// no filemounts - no access at all
				$this->qg->addWhere('1=0', 'AND', 'tx_dam.FILEMOUNTS');
			}
		}
	}


	/**
	 * Set some additional 'enable fields' which are common in use but not always
	 *
	 * @return	void
	 */
	function addDefaultFilter() {
		$this->qg->addWhere('tx_dam.file_status!='.TXDAM_status_file_missing, 'AND', 'file_status');
	}


	/**
	 * Generates the query from the db select array.
	 *
	 * @param	integer		$sysLanguage Language uid
	 * @return	void
	 */
	function setSelectionLanguage($sysLanguage=0) {
		$fieldMapping = array();
		if($sysLanguage) {
			$fieldMapping = tx_dam_db::getLanguageOverlayFields('tx_dam', 'tx_dam_lgovl');
		}
		$this->sl->setFieldMapping('tx_dam', $fieldMapping);
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selectionquery.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selectionquery.php']);
}


?>
