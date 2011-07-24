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
 * Query generator
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
 *   83: class tx_dam_querygen
 *  103:     function tx_dam_querygen()
 *
 *              SECTION: Initialize
 *  123:     function init($table='')
 *  157:     function initBESelect($table='', $pidList='')
 *  176:     function initFESelect($table='', $pidList='')
 *
 *              SECTION: Modify query definition
 *  197:     function setCount($count=false)
 *  213:     function addSelectFields($fields='*', $table='')
 *  226:     function addPidList($pidList='', $table='')
 *  240:     function addEnableFields($table='')
 *  252:     function addOrderBy ($orderBy, $table='')
 *  265:     function addLimit ($limit, $begin='')
 *  280:     function addWhere($where, $type='WHERE', $key='')
 *  299:     function queryAddMM($mm_table='tx_dam_mm_cat', $foreign_table='tx_dam_cat', $local_table='tx_dam')
 *  321:     function addMMJoin($mmtable, $local_table='', $mmtableAlias='')
 *  336:     function mergeWhere($where)
 *  348:     function hasWhere()
 *
 *              SECTION: Create query from definition
 *  372:     function getQuery()
 *  393:     function getQueryParts()
 *
 *              SECTION: helper functions
 *  518:     function makeSearchQueryPart($table, $searchString)
 *  558:     function compileFieldList($table, $fields)
 *  584:     function enableFields($table='')
 *
 * TOTAL FUNCTIONS: 20
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



/**
 * Generates SQL queries
 *
 * The class generates a SQL query from a definition stored in an array.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_querygen {

	/**
	 * Current query definition
	 * From this definition a SQL SELECT can be build.
	 */
	var $query = array();

	/**
	 * Main table to select from
	 */
	var $table = 'tx_dam';



	/**
	 * Constructor
	 *
	 * @return	void
	 */
	function tx_dam_querygen() {
		$this->init();
	}




	/***************************************
	 *
	 *	 Initialize
	 *
	 ***************************************/


	/**
	 * Initialize the db select definition array.
	 *
	 * @param	string		$table Table name
	 * @return	void
	 */
	function init($table='') {

		if($table) {
			$this->table = $table;
		}

		$this->query = array(
			'DISTINCT' => true,
			'SELECT' => array(),
			'FROM' => array(),
			'LEFT_JOIN' => array(),
			'MM' => array(),
			'WHERE' => array(
				'WHERE' => array(), // self defined
				'AND' => array(), // ...
				'OR' => array(), // ...
				'NOT' => array(), // ...
			),
			'enableFields' => array(),
			'GROUPBY' => array(),
			'ORDERBY' => array(),
			'LIMIT' => '',
		);
		$this->addSelectFields();
	}


	/**
	 * Init the db select array for BE usage.
	 *
	 * @param	string		$table Table name
	 * @param	string		$pidList Comma list of pid to select from
	 * @return	void
	 */
	function initBESelect($table='', $pidList='') {
		$this->init($table);
		if ($pidList) {
			$this->query['WHERE']['WHERE'][$this->table.'.pid'] = 'AND '.$this->table.'.pid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($pidList).')';
		}
		$this->query['enableFields'][$this->table] = $this->enableFields();
		$this->addSelectFields();

		$this->mode = 'BE';
	}


	/**
	 * Init the db select array for FE usage.
	 *
	 * @param	string		$table Table name
	 * @param	string		$pidList Comma list of pid to select from
	 * @return	void
	 */
	function initFESelect($table='', $pidList='') {
		$this->mode = 'FE';
		$this->initBESelect($table='', $pidList='');
		$this->mode = 'FE';
	}



	/***************************************
	 *
	 *	 Modify query definition
	 *
	 ***************************************/


	/**
	 * Add/Remove COUNT query part
	 *
	 * @param	boolean		$count If set a COUNT query part will be added
	 * @return	void
	 */
	function setCount($count=false) {
		if($count) {
			$this->query['FROM']['COUNT'] = $this->table.'.uid';
		} else {
			unset($this->query['FROM']['COUNT']);
		}
	}


	/**
	 * Add/Defines fields to select
	 *
	 * @param	string		$fields Field list
	 * @param	string		$table Table name. Default $this->table
	 * @return	void
	 */
	function addSelectFields($fields='*', $table='') {
		$table = $table ? $table : $this->table;
		$this->query['FROM'][$table] = $this->compileFieldList($table, $fields);
	}


	/**
	 * Add/Defines pid's to select from
	 *
	 * @param	string		$pidList Comma list of pid to select from
	 * @param	string		$table Table name. Default $this->table
	 * @return	void
	 */
	function addPidList($pidList='', $table='') {
		if ($pidList) {
			$table = $table ? $table :$this->table;
			$this->query['WHERE']['WHERE'][$table.'.pidList'] = 'AND '.$table.'.pid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($pidList).')';
		}
	}


	/**
	 * Adds enable fields to the query
	 *
	 * @param	string		$table Table name. Default $this->table
	 * @return	void
	 */
	function addEnableFields($table='') {
		$table = $table ? $table :$this->table;
		$this->query['enableFields'][$table] = $this->enableFields($table);
	}


	/**
	 * Adds a LIMIT to the db select array.
	 *
	 * @param	integer		$limit: ...
	 * @return	void
	 */
	function addOrderBy ($orderBy, $table='') {
		$table = $table ? $table :$this->table;
		$this->query['ORDERBY'][$table] = $orderBy;
	}


	/**
	 * Adds a LIMIT to the db select array.
	 *
	 * @param	integer		$limit: ...
	 * @param	integer		$begin: ...
	 * @return	void
	 */
	function addLimit ($limit, $begin='') {
		if($limit) {
			$this->query['LIMIT'] = (intval($begin)?$begin.',':'').$limit;
		}
	}


	/**
	 * Add a WHERE definition to the select array.
	 *
	 * @param	mixed		$where string or array. Where clause(s).
	 * @param	string		$type type: AND, OR, NOT. Default: WHERE
	 * @param	string		$key key to be used for the select array. If empty a md5 hash will be generated from the where clause.
	 * @return	void
	 */
	function addWhere($where, $type='WHERE', $key='')	{
		if(is_array($where)) {
			$this->query['WHERE'] = t3lib_div::array_merge_recursive_overrule($this->query['WHERE'], $where);
		} else {
			$key = $key ? $key : md5($where);
			$this->query['WHERE'][$type][$key] = $where;
		}
	}


	/**
	 * Add a mm table to the select array.
	 * The where clause have to be added separately.
	 *
	 * @param	string		$mm_table mm table. Default: 'tx_dam_mm_cat'
	 * @param	string		$foreign_table foreign table. Default: 'tx_dam_cat'
	 * @param	string		$local_table local table. Default: 'tx_dam'
	 * @return	void
	 */
	function queryAddMM($mm_table='tx_dam_mm_cat', $foreign_table='tx_dam_cat', $local_table='tx_dam')	{
		$local_table = $local_table ? $local_table : $this->table;
		$key = $local_table.'.'.$mm_table.'.'.$foreign_table;

		$this->query['MM'][$local_table] = '1';
		$this->query['MM'][$mm_table] = '1';
		if($foreign_table) {
			$this->query['MM'][$foreign_table] = '1';
		}

		$this->query['WHERE']['AND'][$key] = $local_table.'.uid='.$mm_table.'.uid_local'.($foreign_table?' AND '.$foreign_table.'.uid='.$mm_table.'.uid_foreign ':'');
	}


	/**
	 * Adds a JOIN with a MM table to the query
	 *
	 * @param	string		$mmtable MM table (original name)
	 * @param	string		$local_table Local table. Default $this->table
	 * @param	string		$mmtableAlias Alias of the MM table to be used.
	 * @param	string		$additionalClause Additional ON clause
	 * @return	void
	 */
	function addMMJoin($mmtable, $local_table='', $mmtableAlias='', $additionalClause='')	{
		$local_table = $local_table ? $local_table : $this->table;
		$mmtableName = $mmtableAlias ? $mmtableAlias : $mmtable;
		$mmtableNameDef = $mmtableAlias ? $mmtable.' AS '.$mmtableAlias : $mmtable ;

		$this->query['LEFT_JOIN'][$mmtableNameDef] = $local_table.'.uid='.$mmtableName.'.uid_local '.$additionalClause;
	}


	/**
	 * Merge a WHERE definition array to the select WHERE array part.
	 *
	 * @param	array		$where Where clause(s).
	 * @return	void
	 */
	function mergeWhere($where)	{
		if(is_array($where)) {
			$this->query['WHERE'] = t3lib_div::array_merge_recursive_overrule($this->query['WHERE'], $where);
		}
	}


	/**
	 * Look for entries in the select WHERE array part and return true if there are any.
	 *
	 * @return	boolean
	 */
	function hasWhere()	{
		foreach ($this->query['WHERE'] as $type => $where) {
			if (is_array($where) AND count($where)) {
				return true;
			}
		}
		return false;
	}




	/***************************************
	 *
	 *	 Create query from definition
	 *
	 ***************************************/


	/**
	 * Generates the query from the select array.
	 *
	 * @return	string		the query
	 */
	function getQuery() {

		$queryParts = $this->getQueryParts();
		$query = $GLOBALS['TYPO3_DB']->SELECTquery(
					$queryParts['SELECT'],
					$queryParts['FROM'],
					$queryParts['WHERE'],
					$queryParts['GROUPBY'],
					$queryParts['ORDERBY'],
					$queryParts['LIMIT']
				);

		return $query;
	}


	/**
	 * Generates the query from the select array.
	 *
	 * @return	array		array of query parts
	 */
	function getQueryParts() {
		$queryParts=array(
				'SELECT' => '',
				'FROM' => '',
				'WHERE' => '',
				'GROUPBY' => '',
				'ORDERBY' => '',
				'LIMIT' => ''
				);

		$select = $this->query;


		//
		// SELECT (COUNT, DISTINCT)
		//

		$count = $select['FROM']['COUNT'];
		$distinct = $select['DISTINCT'] ? ' DISTINCT ' :'';

		if(!$count) {
			$queryParts['SELECT'].= $distinct;
		}


		if (count($select['FROM'])) {

				// count
			if($select['FROM']['COUNT']) {
				$queryParts['SELECT'].= ' COUNT('.trim($distinct.$select['FROM']['COUNT']).') as count';
				unset($select['FROM']['COUNT']);
			} else {


				//
				// FROM
				//

				$queryParts['SELECT'].= implode (', ',$select['FROM']+$select['SELECT']);
			}

				// tables
			$selectTables = array_unique(array_merge(array_keys($select['FROM']), array_keys($select['MM'])));
			$queryParts['FROM'].= ' '.implode (', ', $selectTables);


			//
			// LEFT_JOIN
			//

			$query = array();
			foreach($select['LEFT_JOIN'] as $table => $on) {
				$query[] = 'LEFT JOIN '.$table.' ON '.$on;
			}
			$queryParts['FROM'].= "\n".implode ("\n",$query);


			//
			// FROM
			//
			
			if ($selectTables_enableFields = array_diff(array_unique(array_keys($select['enableFields'])), $selectTables)) {
				$queryParts['FROM'].= ', '.implode (', ',$selectTables_enableFields);
			}


			//
			// WHERE
			//

			$query = array();

			$query[] = '1=1';

			if (is_array($select['WHERE']['WHERE']) AND count($select['WHERE']['WHERE'])) {
				$query[] = implode (' ',$select['WHERE']['WHERE']);
			}
			unset($select['WHERE']['WHERE']);

			foreach($select['WHERE'] as $operator => $items){
				if(is_array($items) AND count($items)) {
					switch($operator) {
						case 'NOT':
								// the items have already the right operator in it the make a NOT where clause
							$query[] = 'AND '.implode(' AND ',$items);
						break;
						case 'AND':
							$query[] = 'AND '.implode (' AND ',$items);
						break;
						default:
							$query[] = 'AND ('.implode (' '.$operator.' ',$items).')';
						break;
					}
				}
			}
			$query[] = implode (' ',$select['enableFields']);

			$queryParts['WHERE'] = "\n".implode ("\n",$query);


			//
			// GROUPBY, ORDERBY, LIMIT
			//

			if(count($select['GROUPBY'])) {
				$queryParts['GROUPBY'] = implode (',',$select['GROUPBY']);
			}
			if(count($select['ORDERBY']) AND !$count) {
				$queryParts['ORDERBY'] = implode (',',$select['ORDERBY']);
			}
			if(count($select['LIMIT']) AND !$count) {
				$queryParts['LIMIT'] = $select['LIMIT'];
			}	
		
			if (is_array($this->query['HAVING'])){
				foreach($this->query['HAVING'] as $id => $item){
					$havings[] = ' AND ' . $item;
				}
				$queryParts['GROUPBY'] .= ' HAVING 1'.implode ("\n",$havings);					
			}
		
		}	

		return $queryParts;
	}




	/***************************************
	 *
	 *	 helper functions
	 *
	 ***************************************/


	/**
	 * Creates part of query for searching after a word ($searchString) fields in input table
	 *
	 * @param	string		Table, in which the fields are being searched.
	 * @param	string		search string
	 * @return	string		Returns part of WHERE-clause for searching, if applicable.
	 */
	function makeSearchQueryPart($table, $searchString)	{
		global $TCA;

			// Make query, only if table is valid and a search string is actually defined:
		if ($TCA[$table] && $searchString)	{

				// Loading full table description - we need to traverse fields:
			t3lib_div::loadTCA($table);

				// Initialize field array:
			$sfields = array();
			$sfields[] = 'uid';	// Adding "uid" by default.

				// Traverse the configured columns and add all columns that can be searched:
			foreach($TCA[$table]['columns'] as $fieldName => $info)	{
				if ($info['config']['type'] === 'text' || ($info['config']['type'] === 'input' && !preg_match('/date|time|int/', $info['config']['eval'])))	{
					$sfields[] = $table.'.'.$fieldName;
				}
			}

				// If search-fields were defined (and there always are) we create the query:
			if (count($sfields))	{
				$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike($searchString, $table);
				$like=' LIKE '.$GLOBALS['TYPO3_DB']->fullQuoteStr('%'.$likeStr.'%', $table);		// Free-text searching...
				$queryPart = '('.implode($like.' OR ',$sfields).$like.')';

					// Return query:
				return $queryPart;
			}
		}
	}


	/**
	 * Returns field list with table name prepended
	 *
	 * @param	string		$table Table name
	 * @param	mixed		$fields Field list as array or comma list as string
	 * @return	string		Comma list of fields with table name prepended
	 */
	function compileFieldList($table, $fields) {
		$fieldList = array();

		if ($fields === '*') {
			$fieldList[$table] = $table.'.*';
		} else {
			$fields = is_array($fields) ? $fields : t3lib_div::trimExplode(',', $fields, 1);
			foreach ($fields as $field) {
				$fieldList[$table.'.'.$field] = $table.'.'.$field;
			}
		}

		return implode(', ',$fieldList);
	}



	/**
	 * Returns a part of a WHERE clause which will filter out records with start/end times or hidden/fe_groups fields set to values that should de-select them according to the current time, preview settings or user login. Definitely a frontend function.
	 * THIS IS A VERY IMPORTANT FUNCTION: Basically you must add the output from this function for EVERY select query you create for selecting records of tables in your own applications - thus they will always be filtered according to the "enablefields" configured in TCA
	 * Simply calls t3lib_pageSelect::enableFields() BUT will send the show_hidden flag along! This means this function will work in conjunction with the preview facilities of the frontend engine/Admin Panel.
	 *
	 * @param	string		The table for which to get the where clause
	 * @return	string		The part of the where clause on the form " AND NOT [fieldname] AND ...". Eg. " AND hidden=0 AND starttime < 123345567"
	 * @see t3lib_pageSelect::enableFields()
	 */
	function enableFields($table='')	{
		$table = $table ? $table : $this->table;

		return tx_dam_db::enableFields($table, 'AND', $this->mode);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_querygen.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_querygen.php']);
}


?>
