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
 *   83: class tx_dam_iterator_references extends tx_dam_iterator_base
 *  138:     function tx_dam_iterator_references()
 *  148:     function __construct()
 *
 *              SECTION: Iterator functions
 *  166:     function rewind()
 *  176:     function valid()
 *  187:     function next()
 *  198:     function seek($offset)
 *  211:     function key()
 *  221:     function current()
 *  235:     function count ()
 *
 *              SECTION: allow/Exclude functions
 *  254:     function resetAllowExclude ()
 *  276:     function allowByRegex ($allow, $ignoreCase=true)
 *  296:     function excludeByRegex ($exclude, $ignoreCase=true)
 *  312:     function allowByFileTypes ($allow)
 *  327:     function excludeByFileTypes ($exclude)
 *
 *              SECTION: Reading/sorting references
 *  352:     function read($path, $allowTypes='file')
 *  424:     function sort($sortBy='', $sortReverse=false)
 *
 * TOTAL FUNCTIONS: 16
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once(PATH_txdam.'lib/class.tx_dam_iterator_base.php');

/**
 * Collect data for a file list and provides iterator.
 * Files and folders can be read at the same time but sorting will not work then!
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
class tx_dam_iterator_references extends tx_dam_iterator_base {

	/**
	 * Stores the file/dir entries.
	 */
	var $entries = array();

	/**
	 * Used for sorting the entries.
	 */
	var $sorting = array();

	/**
	 * Used to define the current entry.
	 */
	var $currentKey = 0;

	/**
	 * List of allow regex
	 *
	 * @access private
	 */
	var $allowRegex = array();

	/**
	 * List of allowed file types
	 *
	 * @access private
	 */
	var $allowFileTypes = array();

	/**
	 * List of exclude regex
	 *
	 * @access private
	 */
	var $excludeRegex = array();



	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_iterator_references() {
		$this->__construct();
	}


	/**
	 * Constructor
	 *
	 * @return	void
	 */
	function __construct() {
		$this->resetAllowExclude();
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
		reset($this->sorting);
	}


	/**
	 * Return true is the current element is valid.
	 *
	 * @return	boolean
	 */
	function valid() {
		$key = key($this->sorting);
		return isset($key);
	}


	/**
	 * Advance the internal pointer
	 *
	 * @return	void
	 */
	function next() {
		next($this->sorting);
	}


	/**
	 * Set the internal pointer to the offset
	 *
	 * @param	integer		$offset
	 * @return	void
	 */
	function seek($offset) {
		$this->rewind();
		for ($index = 0; $index < $offset; $index++) {
			$this->next();
		}
	}


	/**
	 * Return the pointer to the current element
	 *
	 * @return	mixed
	 */
	function key() {
		return key($this->sorting);
	}


	/**
	 * Return the current element
	 *
	 * @return	array
	 */
	function current() {
		$this->currentData = $this->entries[$this->key()];
		if (is_callable($this->conf['callbackCurrentData'])) {
			call_user_func ($this->conf['callbackCurrentData'], $this);
		}
		return $this->currentData;
	}


	/**
	 * Count elements
	 *
	 * @return	integer
	 */
	function count () {
		return count($this->entries);
	}




	/***************************************
	 *
	 *	 allow/Exclude functions
	 *
	 ***************************************/


	/**
	 * Reset the allow
	 *
	 * @return	void
	 */
	function resetAllowExclude () {
		$this->allowRegex = array();
		$this->excludeRegex = array();
		$this->allowFileTypes = array();
		$this->excludeFileTypes = array();

			// always exclude dot directories
		$this->excludeRegex[] = '/^\.$/';
		$this->excludeRegex[] = '/^\.\.$/';
	}


	/**
	 * Add allow as regex (PCRE)
	 *
	 * example:
	 * $allow='mp[23]', $ignoreCase=true: '/mp3[23]/i'
	 *
	 * @param	mixed		$allow List for matching allow files. Is array or comma list.
	 * @param	boolean		$ignoreCase If set character case will be ignored
	 * @return	void
	 */
	function allowByRegex ($allow, $ignoreCase=true) {
		$allow = is_array($allow) ? $allow : explode(',', $allow);
		$ignoreCase = $ignoreCase ? 'i' : '';

		foreach ($allow as $key => $expr) {
			$this->allowRegex[] = '/'.$expr.'/'.$ignoreCase;
		}
	}


	/**
	 * Add exclude as regex (PCRE)
	 *
	 * example:
	 * $allow='php[345]', $ignoreCase=true: '/php[345]/i'
	 *
	 * @param	mixed		$exclude List for matching exclude files. Is array or comma list.
	 * @param	boolean		$ignoreCase If set character case will be ignored
	 * @return	void
	 */
	function excludeByRegex ($exclude, $ignoreCase=true) {
		$exclude = is_array($exclude) ? $exclude : explode(',', $exclude);
		$ignoreCase = $ignoreCase ? 'i' : '';

		foreach ($exclude as $key => $expr) {
			$this->excludeRegex[] = '/'.$expr.'/'.$ignoreCase;
		}
	}


	/**
	 * Add allow as file type (txt, mp3, ...)
	 *
	 * @param	mixed		$allow List for matching allow file types. Is array or comma list.
	 * @return	void
	 */
	function allowByFileTypes ($allow) {
		$allow = is_array($allow) ? $allow : explode(',', $allow);

		foreach ($allow as $fileType) {
			$this->allowFileTypes[] = $fileType;
		}
	}


	/**
	 * Add exclude as file type (html, php, ...)
	 *
	 * @param	mixed		$exclude List for matching exclude file types. Is array or comma list.
	 * @return	void
	 */
	function excludeByFileTypes ($exclude) {
		$exclude = is_array($exclude) ? $exclude : explode(',', $exclude);

		foreach ($exclude as $fileType) {
			$this->excludeFileTypes[] = $fileType;
		}
	}




	/***************************************
	 *
	 *	 Reading/sorting references
	 *
	 ***************************************/

	/**
	 * Returns an array with reference items + an array with the sorted items
	 *
	 * @param	string		$selection: reference to a selection object
	 * @param	array		$columns: Array of column names required from the entries
	 * @return	void
	 */
	function read(&$selection, $columns) {

		$local_table = 'tx_dam';
		$softRef_table = 'sys_refindex';
		$tracking_table = 'tx_dam_file_tracking';
			// Use the current selection to create a query and count selected records
		$selection->qg->query['DISTINCT'] = false;
		$selection->addSelectionToQuery();
		$countTotal = 0;
			// Save selection where for softRef query
		$queryParts = $selection->qg->getQueryParts();
		$softRefWhere = $queryParts['WHERE'];
			// Look for references in mm table
		$selection->qg->queryAddMM($mm_table='tx_dam_mm_ref', $foreign_table='', $local_table);
		$selection->prepareSelectionQuery(TRUE);
		$queryArr = $selection->qg->getQueryParts();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryArr);
		if ($res) {
			list($countTotal) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
		}
			// If we have a selection...
		if ($selection->sl->hasSelection()) {
				// Look for references in refindex table
			$fields = ' COUNT(' . $local_table . '.uid) as count';
			$res = tx_dam_db::softRefIndexQuery($local_table, '', '', '', '', '', $fields, array($softRefWhere));
			if ($res) {
				list($softRefCountTotal) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
				$countTotal += $softRefCountTotal;
			}
				// Look for references in file tracking table
			$where = array($softRefWhere);
			$where[] = $tracking_table . '.file_hash=' . $local_table . '.file_hash';
			$where[] = $softRef_table . '.ref_string LIKE CONCAT(' . $tracking_table . '.file_path,' . $tracking_table . '.file_name)';
			$whereClause = implode(' AND ', $where);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$fields,
				$local_table . ',' . $tracking_table . ',' . $softRef_table,
				$whereClause,
				'',
				'',
				1000
			);
			if ($res) {
				list($fileTrackingCountTotal) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
				$countTotal += $fileTrackingCountTotal;
			}
		}
			// If we have references..
		if ($countTotal) {
			$rows = array();
				// Get references from mm table
			$selection->qg->query['FROM'][$local_table] = tx_dam_db::getMetaInfoFieldList();
			$selection->qg->query['FROM']['tx_dam_mm_ref'] = 'tx_dam_mm_ref.uid_foreign,tx_dam_mm_ref.tablenames,tx_dam_mm_ref.ident';
			$selection->qg->addOrderBy ('tablenames', 'tx_dam_mm_ref');
			$selection->qg->addLimit($countTotal);
			$selection->prepareSelectionQuery(FALSE);
			$queryArr = $selection->qg->getQueryParts();
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryArr);
			if ($res) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$rows[] = $row;
				}
			}
				// Get soft references
			$queryParts = $selection->qg->getQueryParts();
			$fields = tx_dam_db::getMetaInfoFieldList() . ', ' . $softRef_table . '.tablename AS tablenames, ' . $softRef_table . '.recuid AS uid_foreign, ' . $softRef_table . '.ref_uid AS uid_local, ' . $softRef_table . '.field, ' . $softRef_table . '.softref_key';
			$res = tx_dam_db::softRefIndexQuery($local_table, '', '', '', '', '', $fields, array($softRefWhere), $queryParts['GROUPBY'], $queryParts['ORDERBY'], $queryParts['LIMIT']);
			if ($res) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$rows[] = $row;
				}
			}
				// Look for references in file tracking table
			$uids = array();
			$fields = $local_table . '.uid';
			$where = array($softRefWhere);
			$where[] = $tracking_table . '.file_hash=' . $local_table . '.file_hash';
			$where[] = $softRef_table . '.ref_string LIKE CONCAT(' . $tracking_table . '.file_path,' . $tracking_table . '.file_name)';
			$whereClause = implode(' AND ', $where);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$fields,
				$local_table . ',' . $tracking_table . ',' . $softRef_table,
				$whereClause,
				'',
				'',
				1000
			);
			if ($res) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$uids[] = $row['uid'];
				}
			}
				// Get upload references
			$rows = array_merge($rows, tx_dam_db::getMediaUsageUploads(implode(',', array_unique($uids)), '', ''));
				// Post-process these rows to produce the reference entries array
			$this->processEntries($rows, $columns);
		}
			// Set the pointer
		$selection->pointer->setTotalCount(count($this->entries));
		if (count($this->entries)) {
			$this->sort('page');
			$this->rewind();
		}
	}

	/**
	 * Builds the array of reference items
	 *
	 * @param	array		$rows: Array of reference records
	 * @param	array		$columns: Array of column names required from the entries
	 * @return	void
	 */
	function processEntries($rows, $columns) {
		foreach ($rows as $damRow) {
			$refTable = $damRow['tablenames'];
			if ($refTable) {
					// Get main fields from TCA
				$selectFields = tx_dam_db::getTCAFieldListArray($refTable, TRUE);
				$orderBy = in_array('tstamp', $selectFields) ? 'tstamp DESC' : '';
				$selectFields = tx_dam_db::compileFieldList($refTable, $selectFields, FALSE);
				$selectFields = $selectFields ? $selectFields : ($refTable.'.uid,'.$refTable.'.pid');
					// Query for non-deleted tables only
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
								$selectFields,
								$refTable,
								$refTable.'.uid='.intval($damRow['uid_foreign']).
									t3lib_BEfunc::deleteClause($refTable),
								'',
								$orderBy,
								40
							);
					// Assemble data
				while ($refRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$pageRow = t3lib_BEfunc::getRecord('pages', $refRow['pid']);
					if (is_array($pageRow)) {
						$item = array();
						$item = $damRow;
						$rootline = t3lib_BEfunc::BEgetRootLine($pageRow['uid']);
						$pageOnClick = t3lib_BEfunc::viewOnClick($pageRow['uid'], '', $rootline);
						foreach ($columns as $element) {
							switch ($element) {
								case 'page':
									$item[$element] = $pageRow;
									$item['pid'] = $pageRow['uid'];
									break;
								case 'content_element':
									$item[$element] = $refRow;
									break;
								case 'content_field':
										// Create sortable item for reference field
									$item[$element] = '';
									if ($item['ident']) {
										$item[$element] = trim($GLOBALS['LANG']->sL(t3lib_befunc::getItemLabel($refTable, $item['ident'])));
									} else if ($item['field']) {
										$item[$element] = trim($GLOBALS['LANG']->sL(t3lib_befunc::getItemLabel($refTable, $item['field'])));
									}
										// Removing trailing : from field label, if any
									if (substr($item[$element], -1) == ':') {
										$item[$element] = substr($item[$element], 0, -1);
									}
									break;
								case 'content_age':
									$item[$element] = $refRow['tstamp'];
									break;
								default:
									break;
							}
						}
						$this->entries[] = $item;
					}
				}
			}
		}
	}

	/**
	 * Sort the collected file list by a fileInfo field.
	 *
	 * @param	string		$sortBy Field name of the entries array. If empty/false the sorting will be set to default.
	 * @param	boolean		$sortReverse If set the sorting will be reversed.
	 * @return	void
	 */
	function sort($sortBy='', $sortReverse=false) {

		$sortByConvert = array(
			'page' => 'pid',
			'content_element' => 'uid_foreign',
			'content_age' => 'content_age',
			'content_field' => 'content_field',
			'softref_key' => 'softref_key',
			'media_element' => 'uid',
			'media_element_age' => 'tstamp',
			);
		
		$this->sorting = array();

		foreach ($this->entries as $item)	{
			if ($sortBy)	{
				$this->sorting[] = strtoupper($item[$sortByConvert[$sortBy]]);
			} else {
				$this->sorting[] = '';
			}
		}
			// Sort if required
		if ($sortBy)	{
			if ($sortReverse)	{
				arsort($this->sorting);
			} else {
				asort($this->sorting);
			}
		}
		$this->rewind();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_references.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_references.php']);
}
?>