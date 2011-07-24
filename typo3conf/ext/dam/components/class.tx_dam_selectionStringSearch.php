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
 * Contains standard selection trees/rules.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   62: class tx_dam_selectionStringSearch extends tx_dam_selProcBase
 *   70:     function tx_dam_selectionStringSearch()
 *   94:     function selection_getItemTitle($id, $value)
 *  112:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *  138:     function makeSearchQueryPart($table, $fields, $searchString)
 *  164:     function getSearchFields($table, $searchString)
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam_selprocbase.php');




/**
 * String search selection class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
class tx_dam_selectionStringSearch extends tx_dam_selProcBase {


	/**
	 * constructor
	 *
	 * @return	void
	 */
	function tx_dam_selectionStringSearch()	{
		global $LANG, $BACK_PATH;

		$this->isTreeViewClass = FALSE;
		$this->isPureSelectionClass = TRUE;

		$this->supportMounts = false;

		$this->deselectValue = '';

		$this->title = $LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.enterSearchString');
		$this->treeName = 'txdamStrSearch';

		$this->table = 'tx_dam';
	}


	/**
	 * Returns the title of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemTitle($id, $value)	{
		return $value;
	}


	/**
	 * Function, processing the query part for selecting/filtering records in DAM
	 * Called from DAM
	 *
	 * @param	string		Query type: AND, OR, ...
	 * @param	string		Operator, eg. '!=' - see DAM Documentation
	 * @param	string		Category - corresponds to the "treename" used for the category tree in the nav. frame
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @param	object		Reference to the parent DAM object.
	 * @return	string
	 * @see tx_dam_selection::getWhereClausePart()
	 */
	function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)      {

		$query = '';
		if ($value) {
			$sfields = $this->getSearchFields('tx_dam', $value);#
			$fields = array();
			foreach ($sfields as $field) {
				$fields[$field] = $damObj->sl->getFieldMapping('tx_dam', $field);
			}

			$query = $this->makeSearchQueryPart('tx_dam', $fields, $value);
			$query = $query ? 'AND '.$query : $query;
		}

		return array('WHERE', $query);
	}


	/**
	 * Creates part of query for searching after a word in fields in input table
	 *
	 * @param	string		$table Table, in which the fields are being searched.
	 * @param	array		$fields The fields to search for the string.
	 * @param	string		$searchString search string
	 * @return	string		Returns part of WHERE-clause for searching, if applicable.
	 */
	function makeSearchQueryPart($table, $fields, $searchString)	{

		$queryPart = '';
			// Make query, only if table is valid and a search string is actually defined:
		if ($searchString)	{

				// If search-fields were defined (and there always are) we create the query:
			if (count($fields))	{
				$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike($searchString, $table);
				$like=' LIKE '.$GLOBALS['TYPO3_DB']->fullQuoteStr('%'.$likeStr.'%', $table);		// Free-text searching...
				$queryPart = '('.implode($like.' OR ',$fields).$like.')';

			}
		}

		return $queryPart;
	}


	/**
	 * Creates part of query for searching after a word ($this->searchString) fields in input table
	 *
	 * @param	string		$table Table, in which the fields are being searched.
	 * @param	string		$searchString search string
	 * @return	array		The fields to search for the string.
	 */
	function getSearchFields($table, $searchString)	{
		global $TCA;

		$sfields=array();

			// Make query, only if table is valid and a search string is actually defined:
		if ($TCA[$table])	{

				// Loading full table description - we need to traverse fields:
			t3lib_div::loadTCA($table);

				// Initialize field array:
			$sfields[]='uid';	// Adding "uid" by default.

				// Traverse the configured columns and add all columns that can be searched:
			foreach($TCA[$table]['columns'] as $fieldName => $info)	{
				if ($info['config']['type'] === 'text' || ($info['config']['type'] === 'input' && !preg_match('/date|time|int/', $info['config']['eval'])))	{
					$sfields[]=$fieldName;
				}
			}
		}
		return $sfields;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionStringSearch.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionStringSearch.php']);
}
?>