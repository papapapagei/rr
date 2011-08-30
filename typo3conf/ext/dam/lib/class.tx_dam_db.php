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
 *  103: class tx_dam_db
 *
 *              SECTION: DAM record access
 *  128:     function getDataWhere ($select_fields, $whereClauses=array(), $groupBy='', $orderBy='', $limit='')
 *  165:     function fillWhereClauseArray($whereClauses=array())
 *
 *              SECTION: DAM record write/update
 *  205:     function insertUpdateData($meta)
 *  302:     function getLastError ()
 *  327:     function insertRecordRaw($meta)
 *  349:     function mergeAndUpdateData($rowSource, $replaceData, $appendData, $transformSourceData=true)
 *  407:     function getUpdateData($row, $replaceData, $appendData)
 *
 *              SECTION: Update
 *  500:     function updateStatus($uid, $status, $fileInfo=NULL, $hash=NULL, $deleted=NULL)
 *  528:     function updateFilePath($oldPath, $newPath)
 *  554:     function updateFilePathSetDeleted($path)
 *
 *              SECTION: References
 *  603:     function referencesQuery($local_table, $local_uid, $foreign_table, $foreign_uid, $MM_ident='', $MM_table='tx_dam_mm_ref', $fields='', $whereClauses=array(), $groupBy='', $orderBy='', $limit=1000)
 *  698:     function getReferencedFiles($foreign_table='', $foreign_uid='', $MM_ident='', $MM_table='tx_dam_mm_ref', $fields='', $whereClauses=array(), $groupBy='', $orderBy='', $limit=1000)
 *  727:     function getReferencesUidArray($foreign_table, $foreign_uid, $MM_ident)
 *  742:     function getReferencesUidList($foreign_table, $foreign_uid, $MM_ident)
 *  762:     function getMetaForUploads ($fileList, $uploadsPath='', $fields='', $whereClauses=array())
 *  819:     function getMediaUsageReferences($uidList, $foreign_table='', $MM_ident='', $fields='', $whereClauses=array(), $groupBy='', $orderBy='', $limit=1000)
 *  855:     function getMediaUsageUploads($uidList, $tableConf='', $uploadsPath='uploads/pics/', $orderBy='', $limit=1000)
 *  905:     function trackingUploadsFile($fileInfo, $hash='')
 *
 *              SECTION: DAM sysfolder
 *  950:     function getPidList ()
 *  963:     function getPid ()
 *
 *              SECTION: Meta field lists and arrays
 * 1007:     function setMetaDefaultFields($meta, $force=false)
 * 1032:     function getMetaInfoFieldList($prependTableName=TRUE, $addFields=array())
 *
 *              SECTION: General field lists and arrays (TCA)
 * 1082:     function compileFieldList($table, $fields, $checkTCA=TRUE, $prependTableName=TRUE)
 * 1116:     function cleanupRecordArray($table, $row)
 * 1134:     function cleanupFieldList($table, $fields)
 * 1156:     function getTCAFieldListArray($table, $mainFieldsOnly=FALSE, $addFields=array())
 * 1210:     function getLanguageOverlayFields ($table, $prependTableName='', $reprocess=false)
 * 1254:     function getFieldListForUser($table, $dontCheckUser = false, $useExludeFieldList = true)
 *
 *              SECTION: Helper
 * 1325:     function evalData($table, $fieldArray)
 * 1361:     function stripLabelFromGroupData($data)
 * 1384:     function enableFields($table, $mode=TYPO3_MODE)
 * 1404:     function deleteClause($table,$tableAlias='')
 *
 * TOTAL FUNCTIONS: 32
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */






/**
 * Misc DAM db functions
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_db {

	/***************************************
	 *
	 *	 DAM record access
	 *
	 ***************************************/



	/**
	 * Fetches the meta data from the index by a given where clause.
	 * All selected records will be returned which means an array of meta data arrays.
	 * If the uid field is selected it will be used as index in the returned array.
	 * Additional WHERE clauses are appended to limit access to non-deleted entries and so on.
	 *
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @param	array		$whereClauses WHERE clauses as array with associative keys (which can be used to overwrite 'enableFields' or 'pidList') or a single one as string.
	 * @param	string		$groupBy Optional GROUP BY field(s), if none, supply blank string.
	 * @param	string		$orderBy Optional ORDER BY field(s), if none, supply blank string.
	 * @param	string		$limit Optional LIMIT value ([begin,]max), if none, supply blank string.
	 * @return	array		Array of meta data arrays or false
	 */
	function getDataWhere ($select_fields, $whereClauses=array(), $groupBy='', $orderBy='', $limit='') {
		$rows = array();

		$select_fields = $select_fields ? $select_fields : tx_dam_db::getMetaInfoFieldList();

		$where = tx_dam_db::fillWhereClauseArray($whereClauses);

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
												$select_fields,
												'tx_dam',
												implode(' AND ', $where),
												$groupBy,
												$orderBy,
												$limit
											);
		#debug ($GLOBALS['TYPO3_DB']->SELECTquery($select_fields, 'tx_dam', implode(' AND ', $where), $groupBy, $orderBy, $limit), 'getDataWhere');

		if ($res) {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

				// Is there a workspace overlay?
				if (isset($GLOBALS['BE_USER']->workspace) && ($GLOBALS['BE_USER']->workspace !== 0)) {
					if (isset($GLOBALS['TSFE'])) {
						// we are in frontend
						$GLOBALS['TSFE']->sys_page->versionOL('tx_dam', $row);
					} else {
						// it's the backend
						t3lib_befunc::workspaceOL('tx_dam', $row);
					}
				}

				if(isset($row['uid'])) {
					$rows[$row['uid']] = $row;
				} else {
					$rows[] = $row;
				}
			}
		}

		return $rows;
	}


	/**
	 * Fille a where clase array with default values
	 *
	 * @param	array		$whereClauses WHERE clauses as array with associative keys (which can be used to overwrite 'enableFields' or 'pidList') or a single one as string.
	 * @return	array		WHERE clauses as array
	 */
	 function fillWhereClauseArray($whereClauses=array()) {
		if (!is_array($whereClauses)) {
			$whereClauses = array('where' => preg_replace('/^AND /', '', trim($whereClauses)));
		}

		$where = array();
		if (!isset($whereClauses['deleted']) AND !isset($whereClauses['enableFields'])) {
			$where['enableFields'] = tx_dam_db::enableFields('tx_dam');
		}
		if (!isset($whereClauses['pidList'])) {
			$where['pidList'] = 'tx_dam.pid IN ('.tx_dam_db::getPidList().')';
		}
		$where = array_merge($where, $whereClauses);

		while ($key = array_search('', $where)) {
			unset ($where[$key]);
		}

		return $where;
	 }



	/**
	 * Creates language-overlay for records in general (where translation is found in records from the same table)
	 * In future versions this may support other overlays too (versions, ...)
	 *
	 * $conf = array(
	 * 		'sys_language_uid' // sys_language uid of the wanted language
	 * 		'lovl_mode' // Overlay mode. If "hideNonTranslated" then records without translation will not be returned un-translated but false
	 * )
	 *
	 * In FE mode sys_language_uid and lovl_mode will be get from TSFE automatically
	 *
	 * @param	string		Table name
	 * @param	array		$row Record to overlay. Must containt uid, pid and $table]['ctrl']['languageField']
	 * @param	integer		$conf Configuration array that defines the wanted overlay
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default. Special mode 'NONE' do not restrict queries.
	 * @return	mixed		Returns the input record, possibly overlaid with a translation. But if $OLmode is "hideNonTranslated" then it will return false if no translation is found.
	 */
	function getRecordOverlay($table, $row, $conf=array(), $mode=TYPO3_MODE)	{
		global $TCA;


		$sys_language_content = intval($conf['sys_language_uid']);
		$OLmode = $conf['lovl_mode'];


		if ($mode ==='FE') {
			$sys_language_content = $sys_language_content ? $sys_language_content : $GLOBALS['TSFE']->sys_language_content;
			$OLmode = $OLmode ? $OLmode : $GLOBALS['TSFE']->sys_language_contentOL;

		} else {
			if (!$conf) return $row;
		}


		if ($row['uid']>0 && $row['pid']>0)	{
			if ($TCA[$table] && ($languageField=$TCA[$table]['ctrl']['languageField']) && ($transOrigPointerField=$TCA[$table]['ctrl']['transOrigPointerField']))	{
				if (!$TCA[$table]['ctrl']['transOrigPointerTable'])	{

					t3lib_div::loadTCA($table);

					$enableFields = tx_dam_db::enableFields($table, 'AND', $mode);


						// Will try to overlay a record only if the sys_language_content value is larger than zero.
					if ($sys_language_content>0)	{
							// Must be default language or [All], otherwise no overlaying:
						if ($row[$languageField]<=0)	{
								// Select overlay record:
							$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
								'*',
								$table,
								'pid='.intval($row['pid']).
									' AND '.$languageField.'='.intval($sys_language_content).
									' AND '.$transOrigPointerField.'='.intval($row['uid']).
									$enableFields,
								'',
								'',
								'1'
								);
							$olrow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

							if ($mode ==='FE') {
								$GLOBALS['TSFE']->sys_page->versionOL($table,$olrow);
							}

								// Merge record content by traversing all fields:
							if (is_array($olrow))	{
								foreach($row as $fN => $fV)	{
									if ($fN!='uid' && $fN!='pid' && isset($olrow[$fN]))	{
										if ($TCA[$table]['columns'][$fN]['l10n_mode']!=='exclude' && ($TCA[$table]['columns'][$fN]['l10n_mode']!=='mergeIfNotBlank' || strcmp(trim($olrow[$fN]),'')))	{
											$row[$fN] = $olrow[$fN];
										}
									}
								}
								$row['sys_language_uid'] = $olrow['sys_language_uid'];
								$row['_BASE_REC_UID'] = $row['uid'];
								$row['_LOCALIZED_UID'] = $olrow['uid'];


							} elseif ($OLmode==='hideNonTranslated' && $row[$languageField]==0)	{	// Unset, if non-translated records should be hidden. ONLY done if the source record really is default language and not [All] in which case it is allowed.
								$row = false;
							}

							// Otherwise, check if sys_language_content is different from the value of the record - that means a japanese site might try to display french content.
						} elseif ($sys_language_content!=$row[$languageField])	{
							$row = false;
						}
					} else {
							// When default language is displayed, we never want to return a record carrying another language!:
						if ($row[$languageField]>0)	{
							$row = false;
						}
					}
				}
			}
		}

		return $row;
	}


	/***************************************
	 *
	 *	 DAM record write/update
	 *
	 ***************************************/



	/**
	 * Insert a meta data array as record. If uid is set in the array an update will be made.
	 *
	 * The meta data have to be in TCE format. For normal fields it doesn't matter, but eg. fields with MM relations needs to be in a special format.
	 * Example:
	 * Assign the record to two categories with the uid 12 and 15
	 * $meta['category'] = '12,15';
	 * Old relations will be deleted, means you have to put them into the list if you want to preserve them.
	 *
	 * @param	array		$meta meta record values
	 * @return	integer		record id or false if an error occured
	 */
	function insertUpdateData($meta)	{
		global $TYPO3_CONF_VARS;

		$meta = tx_dam_db::cleanupRecordArray('tx_dam', $meta);

		require_once (PATH_t3lib.'class.t3lib_tcemain.php');

		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->stripslashes_values = 0;

		$data = array();
		$cmd = array();

		$id = $meta['uid'] ? $meta['uid'] : 'NEW';
		unset($meta['uid']);

			// record deletion is requested - this is a tcemain command
		if ($meta['deleted']) {
			unset($meta['deleted']);
			$cmd['tx_dam'][$id]['delete']=1;
		}

			// still data to change?
		if ($meta) {
			$meta = tx_dam_db::setMetaDefaultFields ($meta);

			if (is_object($GLOBALS['BE_USER'])) {
				$TCAdefaultOverride = $GLOBALS['BE_USER']->getTSConfigProp('TCAdefaults');
				if (is_array($TCAdefaultOverride))	{
					$tce->setDefaultsFromUserTS($TCAdefaultOverride);
				}
			}

			$data['tx_dam'][$id] = $meta;
		}

		if ($TYPO3_CONF_VARS['SC_OPTIONS']['ext/dam/lib/class.tx_dam_db.php']['writeDevLog']) 	t3lib_div::devLog('insertUpdateData(): uid='.$id, 'tx_dam_db', 0, array('cmd'=>$cmd,'data'=>$data));

		$tce->start($data, $cmd, $GLOBALS['BE_USER']);

			// data change - always before a deletion
		$tce->process_datamap();
		if (count($this->errorLog)) return false;

			// delete record when requested
		$tce->process_cmdmap();
		if (count($this->errorLog)) return false;


		if ($id === 'NEW') {

			if ($id = $tce->substNEWwithIDs[$id]) {

					// set uid again - needed for hook
				$meta['uid'] = $id;

					// hook
				if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam']['dbTriggerClasses']))	{
					foreach($TYPO3_CONF_VARS['EXTCONF']['dam']['dbTriggerClasses'] as $classKey => $classRef)	{
						if (is_object($obj = &t3lib_div::getUserObj($classRef)))	{
							if (method_exists($obj, 'insertMetaTrigger')) {
								$obj->insertMetaTrigger($meta);
							}
						}
					}
				}

			} else {
				// That shouldn't happen - really
				debug($tce->errorLog);

				return false;
			}

		} else {
				// set uid again - needed for hook
			$meta['uid'] = $id;

					// hook
			if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam']['dbTriggerClasses']))	{
				foreach($TYPO3_CONF_VARS['EXTCONF']['dam']['dbTriggerClasses'] as $classKey => $classRef)	{
					if (is_object($obj = &t3lib_div::getUserObj($classRef)))	{
						if (method_exists($obj, 'updateMetaTrigger')) {
							$obj->updateMetaTrigger($meta);
						}
					}
				}
			}
		}

		return $id;
	}


	/**
	 * Returns the last error message
	 * This is currently from sys_log only and makes sense when insertUpdateData() failed
	 *
	 * @return array Error type, Error message
	 */
	function getLastError () {
		$res_log = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					'sys_log',
					#'type=1 AND userid='.intval($this->BE_USER->user['uid']).' AND tstamp='.intval($GLOBALS['EXEC_TIME']).'	AND error!=0'
					'tstamp='.intval($GLOBALS['EXEC_TIME']).' AND error!=0',
					'','',1
				);
		$error = false;
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res_log)) {
			$log_data = unserialize($row['log_data']);
			$error = array($row['error'], sprintf($row['details'], $log_data[0],$log_data[1],$log_data[2],$log_data[3],$log_data[4]));
		}
		return $error;
	}


	/**
	 * Insert a DAM record directly without the usage of TCE.
	 * Shouldn't be used except you know what you do.
	 * Following fields will be initialized if not set in the array: pid, crdate, tstamp
	 *
	 * @param	array		$meta Meta data record as array
	 * @return	integer		Record uid
	 */
	function insertRecordRaw($meta)	{

		$meta = tx_dam_db::setMetaDefaultFields ($meta);
		$meta = tx_dam_db::cleanupRecordArray('tx_dam', $meta);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam', $meta);
		$id = $GLOBALS['TYPO3_DB']->sql_insert_id($res);

		return $id;
	}


	/**
	 * Performes a "merging" of record data and update the DB if needed.
	 * Data can be appended to existing data or replace it.
	 *
	 * @param	array		$rowSource The source record data. Needs uid and pid to be set.
	 * @param	array		$replaceData Record data that should replace the original.
	 * @param	array		$appendData Record data that should be appended to original.
	 * @param	boolean		$transformSourceData If set the source record data will be transformed into TCE format, which is needed to append MM relations.
	 * @return	array Return an array of processed data
	 */
	function mergeAndUpdateData($rowSource, $replaceData, $appendData, $transformSourceData=true) {
		global $TCA, $TYPO3_CONF_VARS;

		$updated = array();

		if ($transformSourceData) {
				// is needed to get the record for merging with submitted tceforms data
			require_once (PATH_t3lib.'class.t3lib_transferdata.php');
			$trData = t3lib_div::makeInstance('t3lib_transferData');
			$trData->lockRecords = 0;
			$trData->disableRTE = 1;
			$data = $trData->renderRecord('tx_dam', $rowSource['uid'], $rowSource['pid'], $rowSource);
			reset($trData->regTableItems_data);
			$row = current($trData->regTableItems_data);

			// Use this?
			//	$row = $processData->renderRecordRaw('tx_dam', $row['uid'], $row['pid'], $row);
			// In opposite to renderRecord() this function do not prepare things like fetching TSconfig and others.

		} else {

			$row = $rowSource;
		}


		$rowUpdate = tx_dam_db::getUpdateData($row, $replaceData, $appendData);


		if (count($rowUpdate)) {

				// update data
			$rowUpdate['uid'] = $rowSource['uid'];
			$rowUpdate['pid'] = $rowSource['pid'];
			tx_dam_db::insertUpdateData($rowUpdate);

			$updated['updated'] = $rowUpdate;
			$updated['processed'] = array_merge($row, $rowUpdate);

		} else {

			$updated['updated'] = false;
			$updated['processed'] = $row;
		}

		return $updated;
	}




	/**
	 * Performes a "merging" of record data.
	 * Data can be appended to existing data or replace it.
	 *
	 * @param	array		$rowSource The source record data. Needs uid and pid to be set.
	 * @param	array		$replaceData Record data that should replace the original.
	 * @param	array		$appendData Record data that should be appended to original.
	 * @return	array Return an array of processed data which include the changed fields only
	 */
	function getUpdateData($row, $replaceData, $appendData) {
		global $TCA, $TYPO3_CONF_VARS;


		$rowUpdate = array();

		if (is_array($replaceData)) {
			foreach($replaceData as $field => $value) {
				$rowUpdate[$field] = $value;
			}
		}

		if (is_array($appendData)) {
			t3lib_div::loadTCA('tx_dam');
			foreach($appendData as $field => $value) {

				$appended = false;
				if ($appendType = $TCA['tx_dam']['columns'][$field]['config']['appendType']) {

					$appended = true;
					switch($appendType)	{
						case 'space':
							$rowUpdate[$field] = trim($row[$field].' '.$value);
						break;
						case 'newline':
							$rowUpdate[$field] = $row[$field].($row[$field]?"\n":'').$value;
						break;
						case 'comma':
							$rowUpdate[$field] = $row[$field].($row[$field]?', ':'').$value;
						break;
						case 'charDef':
						default:
							list($type, $appendChar) = explode(':', $appendType);
							$rowUpdate[$field] = $appendChar.$value;
						break;
						default:
							$appended = false;
						break;
					}
				}

				if (!$appended) {

					switch($TCA['tx_dam']['columns'][$field]['config']['type'])	{
						case 'input':
							$rowUpdate[$field] = trim($row[$field].' '.$value);
						break;
						case 'text':
							$rowUpdate[$field] = $row[$field].($row[$field]?"\n":'').$value;
						break;
						case 'select':
						case 'group':
							$data = tx_dam_db::stripLabelFromGroupData($row[$field]);
							$rowUpdate[$field] = $data.','.$value;
						break;
						case 'none':
						case 'user':
						case 'flex':
						case 'check':
						case 'radio':
						default:
							$rowUpdate[$field] = $value; // replace anyway
						break;
					}
				}
			}
		}

		return $rowUpdate;
	}





	/***************************************
	 *
	 *	 Update
	 *
	 ***************************************/



	/**
	 * Updates the status of a meta data record.
	 *
	 * @param	integer		$uid uid - record id
	 * @param	integer		$status status value: TXDAM_status_file_XXX
	 * @param	integer		$fileInfo File info array
	 * @param	string		$hash Optional file hash
	 * @param	integer		$deleted If set the field deleted will be set
	 * @return	void
	 */
	function updateStatus($uid, $status, $fileInfo=NULL, $hash=NULL, $deleted=NULL)	{
		$meta = array();
		$meta['tstamp'] = time();
		if (isset($deleted)) {
			$meta['deleted'] = $deleted;
		}
		$meta['file_status'] = $status;
		if ($fileInfo) {
			$fileInfo = tx_dam_db::cleanupRecordArray('tx_dam', $fileInfo);
			$meta = array_merge($meta, $fileInfo);
			$meta['date_mod'] = $meta['file_mtime'];
		}
		if ($hash) {
			$meta['file_hash'] = $hash;
		}

		return $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.intval($uid), $meta);
	}


	/**
	 * Updates the file path of all meta data records that matches/begins with the old path.
	 *
	 * @param	string		$oldPath old path
	 * @param	string		$newPath new path
	 * @return	void
	 * @todo use tx_dam_db::insertUpdateData() ? That might trigger something we don't want to be triggered
	 */
	function updateFilePath($oldPath, $newPath)	{

		$oldPath = tx_dam::path_makeRelative($oldPath);
		$newPath = tx_dam::path_makeRelative($newPath);

		$where = array();
		$where['enableFields'] = '';
		$where['pidList'] = '';
		$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike($oldPath, 'tx_dam');
		$where['file_path'] = 'tx_dam.file_path LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr.'%', 'tx_dam');

		$rows = tx_dam_db::getDataWhere ('DISTINCT tx_dam.file_path', $where);

		foreach($rows as $row) {
			$updatedPath = preg_replace('#^'.preg_quote($oldPath).'#', $newPath, $row['file_path']);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($row['file_path'], 'tx_dam'), array('file_path'=>$updatedPath));
		}
	}


	/**
	 * Clone all meta data records that matches/begins with the old path and updates the file path.
	 *
	 * @param	string		$oldPath old path
	 * @param	string		$newPath new path
	 * @return	void
	 */
	function cloneFilePath($oldPath, $newPath)	{

		$oldPath = tx_dam::path_makeRelative($oldPath);
		$newPath = tx_dam::path_makeRelative($newPath);

		$where = array();
		$where['enableFields'] = 'deleted=0';
		$where['pidList'] = '';
		$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike($oldPath, 'tx_dam');
		$where['file_path'] = 'tx_dam.file_path LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr.'%', 'tx_dam');

		$rows = tx_dam_db::getDataWhere ('DISTINCT *', $where);

		$preg_oldPath = '#^'.preg_quote($oldPath).'#';
		foreach($rows as $row) {
			$row['file_path'] = preg_replace($preg_oldPath, $newPath, $row['file_path']);
			$row['uid'] = 'NEW';
			tx_dam_db::insertUpdateData($row);
		}
	}


	/**
	 * Set all records deleted that matches/begins with the given path.
	 *
	 * @param	string		$path path
	 * @return	void
	 */
	function updateFilePathSetDeleted($path)	{

		$path = tx_dam::path_makeRelative($path);

		// this way db trigger will not work
		// $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'tx_dam.file_path LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($GLOBALS['TYPO3_DB']->escapeStrForLike($path, 'tx_dam').'%', 'tx_dam'), array('deleted'=>'1'));

		$where = array();
		$where['enableFields'] = '';
		$where['pidList'] = '';
		$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike($path, 'tx_dam');
		$where['file_path'] = 'tx_dam.file_path LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr.'%', 'tx_dam');

		$rows = tx_dam_db::getDataWhere ('tx_dam.uid', $where);

		foreach($rows as $row) {
			$row['deleted'] = '1';
			tx_dam_db::insertUpdateData($row);
		}
	}



	/***************************************
	 *
	 *	 References
	 *
	 ***************************************/




	/**
	 * Returns the result of q db query by a mm-relation to the tx_dam table which is used to get eg. the references tt_content<>tx_dam
	 *
	 *
	 * @param 	string 		$local_table Eg tx_dam
	 * @param 	string 		$local_uid Uid list of tx_dam records the references shall be fetched for
	 * @param	string		$foreign_table Table name to get references for. Eg tt_content
	 * @param	integer		$foreign_uid The uid of the referenced record
	 * @param	mixed		$MM_ident Array of field/value pairs that should match in MM table. If it is a string, it will be used as value for the field 'ident'.
	 * @param	string		$MM_table The mm table to use. Default: tx_dam_mm_ref
	 * @param	string		$fields The fields to select. Needs to be prepended with table name: tx_dam.uid, tx_dam.title
	 * @param	array		$whereClauses WHERE clauses as array with associative keys (which can be used to overwrite 'enableFields') or a single one as string.
	 * @param	string		$groupBy: ...
	 * @param	string		$orderBy: ...
	 * @param	string		$limit: Default: 1000
	 * @return	mixed		db result pointer
	 */
	function referencesQuery($local_table, $local_uid, $foreign_table, $foreign_uid, $MM_ident='', $MM_table='tx_dam_mm_ref', $fields='', $whereClauses=array(), $groupBy='', $orderBy='', $limit=1000) {

		if (!is_array($whereClauses)) {
			$whereClauses = array('where' => preg_replace('/^AND /', '', trim($whereClauses)));
		}

		$MM_table = $MM_table ? $MM_table : 'tx_dam_mm_ref';

		$where = array();
		if (!isset($whereClauses['deleted']) && !isset($whereClauses['enableFields'])) {
			$where['enableFields'] = tx_dam_db::enableFields($local_table);
			if ($foreign_table) {
				$where['enableFieldsForeign'] = tx_dam_db::enableFields($foreign_table);
			}
		}
		$where = array_merge($where, $whereClauses);

		if ($foreign_table) {
			$where[] = $MM_table.'.tablenames='.$GLOBALS['TYPO3_DB']->fullQuoteStr($foreign_table, $MM_table);
		}
		if ($foreign_uid = $GLOBALS['TYPO3_DB']->cleanIntList($foreign_uid)) {
			$where[] = $MM_table.'.uid_foreign IN ('.$foreign_uid.')';
		}
		if ($local_uid = $GLOBALS['TYPO3_DB']->cleanIntList($local_uid)) {
			$where[] = $MM_table.'.uid_local IN ('.$local_uid.')';
		}
		if ($MM_ident) {
			if (!is_array($MM_ident)) {
				$MM_ident = array('ident' => $MM_ident);
			}
			foreach ($MM_ident as $field => $value) {
				$where[] = $MM_table.'.'.$field.'='.$GLOBALS['TYPO3_DB']->fullQuoteStr($value, $MM_table);
			}
		}

		if(!$orderBy) {
			if ($MM_table=='tx_dam_mm_ref' AND t3lib_div::int_from_ver(TYPO3_branch)>=t3lib_div::int_from_ver('4.1')) {
				// TODO .sorting_foreign: very much hardcoded - how to change that? MM tables don't have TCA ...
				$orderBy = $MM_table.'.sorting_foreign';
			} else {
				$orderBy = $MM_table.'.sorting';
			}
		}

		while ($key = array_search('', $where)) {
			unset ($where[$key]);
		}

		$where = implode(' AND ', $where);

		$select_local_table = strstr($fields.' '.$where, $local_table.'.') ? $local_table :  '';
		$select_foreign_table = strstr($fields.' '.$where, $foreign_table.'.') ? $foreign_table :  '';

		if ($select_local_table OR $select_foreign_table) {
			$where = $where ? ' AND '.$where : '';
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			$fields,
			$select_local_table,
			$MM_table,
			$select_foreign_table,
			$where,
			$groupBy,
			$orderBy,
			$limit
		);

		return $res;
	}






	/**
	 * Make a list of files by a mm-relation to the tx_dam table which is used to get eg. the references tt_content<>tx_dam
	 *
	 * 	Returns:
	 * 	array (
	 * 		'files' => array(
	 * 			record-uid => 'fileadmin/example.jpg',
	 * 		)
	 * 		'rows' => array(
	 * 			record-uid => array(meta data array),
	 * 		)
	 * 	);
	 *
	 * @param	string		$foreign_table Table name to get references for. Eg tt_content
	 * @param	integer		$foreign_uid The uid of the referenced record
	 * @param	mixed		$MM_ident Array of field/value pairs that should match in MM table. If it is a string, it will be used as value for the field 'ident'.
	 * @param	string		$MM_table The mm table to use. Default: tx_dam_mm_ref
	 * @param	string		$fields The fields to select. Needs to be prepended with table name: tx_dam.uid, tx_dam.title
	 * @param	array		$whereClauses WHERE clauses as array with associative keys (which can be used to overwrite 'enableFields') or a single one as string.
	 * @param	string		$groupBy: ...
	 * @param	string		$orderBy: ...
	 * @param	string		$limit: Default: 1000
	 * @return	array		...
	 */
	function getReferencedFiles($foreign_table='', $foreign_uid='', $MM_ident='', $MM_table='tx_dam_mm_ref', $fields='', $whereClauses=array(), $groupBy='', $orderBy='', $limit=1000) {

		$fields = $fields ? $fields : tx_dam_db::getMetaInfoFieldList();
		$local_table= 'tx_dam';

		$files = array();
		$rows  = array();

		$res = tx_dam_db::referencesQuery($local_table, '', $foreign_table, $foreign_uid, $MM_ident, $MM_table, $fields, $whereClauses, $groupBy, $orderBy, $limit);

		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$files[$row['uid']] = $row['file_path'].$row['file_name'];
				$rows[$row['uid']] = $row;
			}
		}

		return array('files' => $files, 'rows' => $rows);
	}


	/**
	 * Make an array of uid's by a mm-relation to the tx_dam table which is used to get eg. the references tt_content<>tx_dam
	 *
	 * @param	string		$foreign_table Table name to get references for. Eg tt_content
	 * @param	integer		$foreign_uid The uid of the referenced record
	 * @param	mixed		$MM_ident Array of field/value pairs that should match in MM table. If it is a string, it will be used as value for the field 'ident'.
	 * @return	array		uid array
	 */
	function getReferencesUidArray($foreign_table, $foreign_uid, $MM_ident) {
		$result = tx_dam_db::getReferencedFiles($foreign_table, $foreign_uid, $MM_ident, '', 'tx_dam.uid');
		$uidList = array_keys($result['rows']);
		return $uidList;
	}


	/**
	 * Make a comma list of uid's by a mm-relation to the tx_dam table which is used to get eg. the references tt_content<>tx_dam
	 *
	 * @param	string		$foreign_table Table name to get references for. Eg tt_content
	 * @param	integer		$foreign_uid The uid of the referenced record
	 * @param	mixed		$MM_ident Array of field/value pairs that should match in MM table. If it is a string, it will be used as value for the field 'ident'.
	 * @return	string		uid comma list
	 */
	function getReferencesUidList($foreign_table, $foreign_uid, $MM_ident) {
		$result = tx_dam_db::getReferencedFiles($foreign_table, $foreign_uid, $MM_ident, '', 'tx_dam.uid');
		$uidList = implode(',',array_keys($result['rows']));
		return $uidList;
	}


	/**
	 * Returns an array of meta data for a list of files from the uploads folder.
	 * This can be used to get meta data for "uploads" files.
	 *
	 * IMPORTANT
	 * The meta data does NOT include data of the uploads file itself but a matching file which is placed in fileadmin/!
	 *
	 * @param 	mixed 		$fileList Comma list or array of files
	 * @param 	string 		$uploadsPath Uploads path. If empty each file have to have a path prepended.
	 * @param	string		$fields The fields to select. Needs to be prepended with table name: tx_dam.uid, tx_dam.title
	 * @param	array		$whereClauses WHERE clauses as array with associative keys (which can be used to overwrite 'enableFields') or a single one as string.
	 * @return array
	 */
	function getMetaForUploads ($fileList, $uploadsPath='', $fields='', $whereClauses=array()) {

		$select_fields = $fields ? $fields : 'tx_dam.*';
		if (!isset($whereClauses['file_hash'])) {
			$whereClauses['file_hash'] = 'tx_dam_file_tracking.file_hash=tx_dam.file_hash';
		}

		$uploadsPath = tx_dam::path_makeClean($uploadsPath);
		$fileList = is_array($fileList) ? $fileList : explode(',', $fileList);

		$files = array();
		foreach ($fileList as $filepath) {

			$filepath = $uploadsPath.$filepath;
			$fileInfo = tx_dam::file_compileInfo($uploadsPath.$filepath);

			if ($fileInfo['__exists']) {

					// a file might be twice in fileadmin/
					// this will just use the first found entry
					// I can'T see how to detect which file (they are the same) has the right meta data for the uploads file

				$whereClauses['file_name'] = 'tx_dam_file_tracking.file_name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_name'],'tx_dam_file_tracking');
				$whereClauses['file_path'] = 'tx_dam_file_tracking.file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_path'],'tx_dam_file_tracking');

				$where = tx_dam_db::fillWhereClauseArray($whereClauses);

				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
														$select_fields,
														'tx_dam, tx_dam_file_tracking',
														implode(' AND ', $where),
														'',
														'',
														'1'
													);
				reset ($rows);
				$files[$filepath] = current($rows);
			}
		}

		return $files;
	}

	/**
	 * Returns the result of a db query on a soft ref table
	 *
	 *
	 * @param 	string 		$local_table Eg tx_dam
	 * @param 	string 		$local_uid Uid list of tx_dam records the references shall be fetched for
	 * @param	string		$foreign_table Table name to get references for. Eg tt_content
	 * @param	integer		$foreign_uid The uid of the referenced record
	 * @param	mixed		$softRef_ident Array of field/value pairs that should match in soft references table. If it is a string, it will be used as value for the field 'softref_key'.
	 * @param	string		$softRef_table The soft referneces table to use. Default: sys_refindex
	 * @param	string		$fields The fields to select. Needs to be prepended with table name: tx_dam.uid, tx_dam.title
	 * @param	array		$whereClauses WHERE clauses as array with associative keys (which can be used to overwrite 'enableFields') or a single one as string.
	 * @param	string		$groupBy: ...
	 * @param	string		$orderBy: ...
	 * @param	string		$limit: Default: 1000
	 * @return	mixed		db result pointer
	 */
	function softRefIndexQuery($local_table, $local_uid, $foreign_table, $foreign_uid, $softRef_ident='', $softRef_table='sys_refindex', $fields='', $whereClauses=array(), $groupBy='', $orderBy='', $limit=1000) {

		if (!is_array($whereClauses)) {
			$whereClauses = array('where' => preg_replace('/^AND /', '', trim($whereClauses)));
		}

		$softRef_table = $softRef_table ? $softRef_table : 'sys_refindex';

		$where = array();
		if (!isset($whereClauses['deleted']) && !isset($whereClauses['enableFields'])) {
			$where['enableFields'] = tx_dam_db::enableFields($local_table);
				// soft references table has no TCA
			$where['deleted'] = $softRef_table.'.deleted=0';
			if ($foreign_table) {
				$where['enableFields'] .= ' AND '.tx_dam_db::enableFields($foreign_table);
			}
		}
		$where['ref'] = $local_table.'.uid='.$softRef_table.'.ref_uid';
		$where['ref'] .= $foreign_table ? ' AND ' . $foreign_table . '.uid=' . $softRef_table . '.recuid' : '';
		$where['refTable'] = $softRef_table . '.ref_table=' . $GLOBALS['TYPO3_DB']->fullQuoteStr('tx_dam', $softRef_table);
		$where = array_merge($where, $whereClauses);

		if ($foreign_table) {
			$where[] = $softRef_table.'.tablename='.$GLOBALS['TYPO3_DB']->fullQuoteStr($foreign_table, $softRef_table);
		}
		if ($foreign_uid = $GLOBALS['TYPO3_DB']->cleanIntList($foreign_uid)) {
			$where[] = $softRef_table.'.recuid IN ('.$foreign_uid.')';
		}
		if ($local_uid = $GLOBALS['TYPO3_DB']->cleanIntList($local_uid)) {
			$where[] = $softRef_table.'.ref_uid IN ('.$local_uid.')';
		}
		$where[] = 'NOT ' . $softRef_table.'.softref_key=' . $GLOBALS['TYPO3_DB']->fullQuoteStr('', $softRef_table);
		if ($softRef_ident) {
			if (!is_array($softRef_ident)) {
				$softRef_ident = array('softref_key' => $softRef_ident);
			}
			foreach ($softRef_ident as $field => $value) {
				if ($value) {
					$where[] = $softRef_table.'.'.$field.'='.$GLOBALS['TYPO3_DB']->fullQuoteStr($value, $softRef_table);
				}
			}
		}

		if(!$orderBy) {
			$orderBy = $softRef_table.'.sorting';
		}

		while ($key = array_search('', $where)) {
			unset ($where[$key]);
		}
		$where = implode(' AND ', $where);

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$fields,
			$local_table . ',' .$softRef_table . ($foreign_table ? ','. $foreign_table : ''),
			$where,
			$groupBy,
			$orderBy,
			$limit
		);

		return $res;
	}

	/**
	 * Returns info about the usage of a media item as reference to a given table.
	 *
	 * @param 	string 		$uidList Uid list of tx_dam records the references shall be fetched for
	 * @param 	string 		$table Eg tt_content
	 * @param	mixed		$MM_ident Array of field/value pairs that should match in MM table. If it is a string, it will be used as value for the field 'ident'.
	 * @param	string		$fields The fields to select. Needs to be prepended with table name: tx_dam.uid, tx_dam.title
	 * @param	array		$whereClauses WHERE clauses as array with associative keys (which can be used to overwrite 'enableFields') or a single one as string.
	 * @param	string		$groupBy: ...
	 * @param	string		$orderBy: ...
	 * @param	string		$limit: Default: 1000
	 * @return array
	 */
	function getMediaUsageReferences($uidList, $foreign_table='', $MM_ident='', $fields='', $whereClauses=array(), $groupBy='', $orderBy='', $limit=1000) {
		$rows = array();
			// References in tx_dam_mm_ref table
		$fields = $fields ? $fields : 'tx_dam_mm_ref.*';
		$local_table= 'tx_dam';
		$MM_table = 'tx_dam_mm_ref';
		$res = tx_dam_db::referencesQuery($local_table, $uidList, $foreign_table, '', $MM_ident, $MM_table, $fields, $whereClauses, $groupBy, $orderBy, $limit);
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$rows[] = $row;
			}
		}
			// References in sys_refindex table
		$softRef_table = 'sys_refindex';
		$fields = $softRef_table . '.tablename AS tablenames, ' . $softRef_table . '.recuid AS uid_foreign, ' . $softRef_table . '.ref_uid AS uid_local, ' . $softRef_table . '.field, ' . $softRef_table . '.softref_key';
		$res = tx_dam_db::softRefIndexQuery($local_table, $uidList, $foreign_table, '', $softRef_ident, $softRef_table, $fields, $whereClauses, $groupBy, $orderBy, $limit);
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$rows[] = $row;
			}
		}
			// References in tx_dam_file_tracking table
		$rows = array_merge($rows, tx_dam_db::getMediaUsageUploads($uidList, '', ''));
		
		return $rows;
	}

	/**
	 * Fetches reference data from reference index based on file tracking content
	 *
	 *
	 * @param 	string 		$uidList Uid list of tx_dam records the references shall be fetched for
	 * @param 	array 		$tableConf Unused/reserved
	 * @param 	string 		$uploadsPath
	 * @param	string		$orderBy: ...
	 * @param	string		$limit: Default: 1000
	 * @return array
	 */
	function getMediaUsageUploads($uidList, $tableConf='', $uploadsPath='uploads/pics/', $orderBy='', $limit=1000) {
		$rows = array();

		$softRef_table = 'sys_refindex';
		$local_table = 'tx_dam';
		$tracking_table = 'tx_dam_file_tracking';
		$fields = array();
		$fields[] = 'tx_dam.*';
		$fields[] = $tracking_table . '.file_path AS tracking_file_path,' . $tracking_table . '.file_name AS tracking_file_name';
		$fields[] = $softRef_table . '.tablename AS tablenames,' . $softRef_table . '.recuid AS uid_foreign,' . $softRef_table . '.field';
		if (!$orderBy) {
			$orderBy = $tracking_table . '.tstamp';
		}
		$where = array();
		$where[] = 'tx_dam.uid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($uidList).')';
		$where[] = 'tx_dam_file_tracking.file_hash=tx_dam.file_hash';
		if ($uploadsPath) {
			$where[] = 'tx_dam_file_tracking.file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($uploadsPath,'tx_dam_file_tracking');
		}
			// use index to preselect records
		$where[] = $softRef_table . '.ref_string = CONCAT(' . $tracking_table . '.file_path,' . $tracking_table . '.file_name)';
		$where[] = $softRef_table . '.ref_string LIKE CONCAT(' . $tracking_table . '.file_path,' . $tracking_table . '.file_name)';
		$selectFields = implode(',', $fields);
		$whereClause = implode(' AND ', $where);
		$rowsUploads = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			$selectFields,
			$local_table . ',' . $tracking_table . ',' . $softRef_table,
			$whereClause,
			'',
			$orderBy,
			$limit
		);
		if (count($rowsUploads)) {
			foreach ($rowsUploads as $uploadRow) {
				$uploadRow['softref_key'] = 'file_copy';
				$uploadRow['uid_local'] = $uploadRow['uid'];
				$rows[] = $uploadRow;
			}
		}
		return $rows;
	}


	/**
	 * Add an uploads file to the tracking table of the DAM.
	 * This is needed to make it possible to identify files copied to uploads/
	 *
	 */
	function trackingUploadsFile($fileInfo, $hash='') {

		$fileInfo = is_array($fileInfo) ? $fileInfo : tx_dam::file_compileInfo($fileInfo);

		if ($fileInfo['__exists'] AND t3lib_div::isFirstPartOfStr($fileInfo['file_path'],'uploads/')) {

			$hash = $hash ? $hash : tx_dam::file_calcHash($fileInfo);

			$where = 'file_name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_name'], 'tx_dam_file_tracking').
					' AND file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_path'], 'tx_dam_file_tracking');
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_file_tracking', $where);

			$fields_values = array (
				'tstamp' => time(),
				'file_name' => $fileInfo['file_name'],
				'file_path' => $fileInfo['file_path'],
				'file_size' => $fileInfo['file_size'],
				'file_ctime' => max ($fileInfo['file_ctime'], $fileInfo['file_mtime']),
				'file_hash' => $hash,
			);
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_file_tracking', $fields_values);
		}
	}








	/***************************************
	 *
	 *	 DAM sysfolder
	 *
	 ***************************************/



	/**
	 * Returns a pid comma list of DAM folders.
	 * Currently only one folder is supported, but for any direct db read access this list of valid pid's should be used.
	 *
	 * @return	string		Comma list of DAM folder pid's.
	 */
	function getPidList () {
		return (string)tx_dam_db::getPid();
	}


	/**
	 * Returns a single pid of a DAM folder.
	 * This pid have to be used for storage of DAM records.
	 *
	 * For fetching data getPidList() have to be used.
	 *
	 * @return	integer		Current/default DAM folder pid for storage.
	 */
	function getPid () {
		global $TYPO3_CONF_VARS;

		static $pid = 0;

		if(!$pid AND is_object($GLOBALS['TSFE'])) {
			// get pid from TS

			//
			//  plugin.tx_dam.defaults {
			//  // The pid of the media folder. Needs to be set when multiple media folders exist
			//  pid =
			$pid = intval(tx_dam::config_getValue('plugin.defaults.pid')); # $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_dam.']['defaults.']['pid']);
		}

		if(!$pid) {
			require_once(PATH_txdam.'lib/class.tx_dam_sysfolder.php');
			$pid = tx_dam_sysfolder::init();
		}
		return $pid;
	}





	/*******************************************
	 *
	 * Meta field lists and arrays
	 *
	 *******************************************/



	/**
	 * Following fields will be initialized if not set in the array: pid, crdate, tstamp
	 *
	 * @param	array		$meta Meta data record as array
	 * @param	boolean		$force If set the field values will be set no matter what.
	 * @return	array		$meta Meta data record as array
	 */
	function setMetaDefaultFields($meta, $force=false)	{

		if($force OR !isset($meta['uid'])) {
			if($force OR !isset($meta['pid'])) {
				$meta['pid'] = tx_dam_db::getPid();
			}
			if($force OR !isset($meta['crdate'])) {
				$meta['crdate'] = time();
			}
		}
		if($force OR !isset($meta['tstamp'])) {
			$meta['tstamp'] = time();
		}

		return $meta;
	}


	/**
	 * Generates a list of tx_dam db fields which are needed to get a proper info about the record.
	 *
	 * @param	boolean		$prependTableName If set the fields are prepended with table.
	 * @param	array		$addFields Field list array which should be appended to the list
	 * @return	string		Comma list of fields with table name prepended
	 */
	function getMetaInfoFieldList($prependTableName=TRUE, $addFields=array()) {
		global $TCA;

		$infoFields = tx_dam_db::getTCAFieldListArray('tx_dam', TRUE, $addFields);

		$infoFieldsTCA = explode(',', $TCA['tx_dam']['ctrl']['txdamInterface']['info_fieldList_add']);
		foreach($infoFieldsTCA as $field) {
			if($field=trim($field)) {
				$infoFields[$field] = $field;
			}
		}
		$infoFields['file_name'] = 'file_name';
		$infoFields['file_dl_name'] = 'file_dl_name';
		$infoFields['file_path'] = 'file_path';
		$infoFields['file_size'] = 'file_size';
		$infoFields['file_type'] = 'file_type';
		$infoFields['file_ctime'] = 'file_ctime';
		$infoFields['file_mtime'] = 'file_mtime';
		$infoFields['file_hash'] = 'file_hash';
		$infoFields['file_mime_type'] = 'file_mime_type';
		$infoFields['file_mime_subtype'] = 'file_mime_subtype';
		$infoFields['media_type'] = 'media_type';
		$infoFields['file_status'] = 'file_status';
		$infoFields['index_type'] = 'index_type';
		$infoFields['parent_id'] = 'parent_id';
		$infoFields = tx_dam_db::compileFieldList('tx_dam', $infoFields, FALSE, $prependTableName);

		return $infoFields;
	}

	
	/**
	 * Get the extension record from the DB. 
	 *
	 * @param	string		$ext: Extension, for which to fetch the record. Optional
	 * @param	boolean		$mimeType: Mime Type, for which to fetch the record. Optional, but must be provided if the extension is blank.
	 * @return	array		File type record.
	 */	
	function getMediaExtension($ext='', $mimeType='') {
		// Check we have enough information to proceed
		if ($ext == '' && $mimeType == '') {
			return array();
		}
		if ($ext != '') {
			$where[] = "ext=" . $GLOBALS['TYPO3_DB']->fullQuoteStr($ext, 'tx_dam_media_types');
		}
		if ($mimeType != '') {
			$where[] = "mime=" . $GLOBALS['TYPO3_DB']->fullQuoteStr($mimeType, 'tx_dam_media_types');
		}
		
		// Query the DB 
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
												'*',
												'tx_dam_media_types',
												implode(' AND ', $where)
											);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			return $row;
		}
	}	





	/*******************************************
	 *
	 * General field lists and arrays (TCA)
	 *
	 *******************************************/



	/**
	 * Returns field list with table name prepended
	 *
	 * @param	string		$table Table name
	 * @param	mixed		$fields Field list as array or as string comma list.
	 * @param	boolean		$check If set (default) the fields are checked if defined in TCA.
	 * @param	boolean		$prependTableName If set (default) the fields are prepended with table.
	 * @return	string		Comma list of fields with table name prepended
	 */
	function compileFieldList($table, $fields, $checkTCA=TRUE, $prependTableName=TRUE) {
		global $TCA;

		$fieldList = array();

		$fields = is_array($fields) ? $fields : t3lib_div::trimExplode(',', $fields, 1);

		if ($checkTCA) {
			if (is_array($TCA[$table])) {
				$fields = tx_dam_db::cleanupFieldList($table, $fields);
			} else {
				$table = NULL;
			}
		}
		if ($table) {
			foreach ($fields as $field) {
				if ($prependTableName) {
					$fieldList[$table.'.'.$field] = $table.'.'.$field;
				} else {
					$fieldList[$field] = $field;
				}
			}
		}
		return implode(', ', $fieldList);
	}


	/**
	 * Removes fields from a record row array that are not configured in TCA.
	 *
	 * @param	string		$table Table name
	 * @param	array		$row Record row
	 * @return	array		Cleaned row
	 */
	function cleanupRecordArray($table, $row) {
		$allowedFields = tx_dam_db::getTCAFieldListArray($table);
		foreach ($row as $field => $val) {
			if (!in_array($field, $allowedFields)) {
				unset($row[$field]);
			}
		}
		return $row;
	}


	/**
	 * Removes fields from a field list that are not configured in TCA.
	 *
	 * @param	string		$table Table name
	 * @param	mixed		$fields Field list as array or as string comma list.
	 * @return	array		Cleaned field list as array.
	 */
	function cleanupFieldList($table, $fields) {
		$allowedFields = tx_dam_db::getTCAFieldListArray($table);
		$fields = is_array($fields) ? $fields : t3lib_div::trimExplode(',', $fields, 1);

		foreach ($fields as $key => $field) {
			if (!in_array($field, $allowedFields)) {
				unset($fields[$key]);
			}
		}
		return $fields;
	}


	/**
	 * Returns an array of fields for a table which are configured in TCA or ctrl fields.
	 * This includes uid, pid, and ctrl fields.
	 *
	 * @param	string		$table Table name
	 * @param	boolean		$mainFieldsOnly If true not all fields from the TCA columns-array will be used but the ones from the ctrl-array.
	 * @param	array		$addFields Field list array which should be appended to the list no matter if defined in TCA.
	 * @return	array		Field list array
	 */
	function getTCAFieldListArray($table, $mainFieldsOnly=FALSE, $addFields=array())	{
		global $TCA;

		$fieldListArr=array();

		if (!is_array($addFields)) {
			$addFields = t3lib_div::trimExplode(',', $addFields, 1);
		}
		foreach ($addFields as $field)	{
			#if ($TCA[$table]['columns'][$field]) {
				$fieldListArr[$field] = $field;
			#}
		}

		if (is_array($TCA[$table]))	{
			t3lib_div::loadTCA($table);
			if (!$mainFieldsOnly) {
				foreach($TCA[$table]['columns'] as $fieldName => $dummy)	{
					$fieldListArr[$fieldName] = $fieldName;
				}
			}
			$fieldListArr['uid'] = 'uid';
			$fieldListArr['pid'] = 'pid';

			$ctrlFields = array('label','label_alt','type','typeicon_column','tstamp','crdate','cruser_id','sortby','delete','fe_cruser_id','fe_crgroup_id','languageField','transOrigPointerField');
			foreach ($ctrlFields as $field)	{
				if ($TCA[$table]['ctrl'][$field]) {
					$subFields = t3lib_div::trimExplode(',',$TCA[$table]['ctrl'][$field],1);
					foreach ($subFields as $subField)	{
						$fieldListArr[$subField] = $subField;
					}
				}
			}

			if (is_array($TCA[$table]['ctrl']['enablecolumns'])) {
				foreach ($TCA[$table]['ctrl']['enablecolumns'] as $field)	{
					if ($field) {
						$fieldListArr[$field] = $field;
					}
				}
			}
		}
		return $fieldListArr;
	}


	/**
	 * Returns an array of fields that are configured for a table as language overlay fields.
	 *
	 * @param	string		$table Table name
	 * @param	string		$prependTableName If set the fields will be prefixed with the value as table.
	 * @param	boolean		$reprocess The field list will be cached. If $reprocess is set the cache is flushed and the fields will be detected again.
	 * @return	array		Field list array
	 */
	function getLanguageOverlayFields ($table, $prependTableName='', $reprocess=false) {
		global $TCA;

		$fields = array();

		if (is_array($TCA[$table]))	{
			if (!is_array($TCA[$table]['txdamLgOvlFields']) OR $reprocess) {
				t3lib_div::loadTCA($table);

				$languageField = $TCA[$table]['ctrl']['languageField'];
				$transOrigPointerField = $TCA[$table]['ctrl']['transOrigPointerField'];

				$TCA[$table]['txdamLgOvlFields']['uid'] = 'uid';
				$TCA[$table]['txdamLgOvlFields'][$languageField] = $languageField;
				$TCA[$table]['txdamLgOvlFields'][$transOrigPointerField] = $transOrigPointerField;

				foreach($TCA[$table]['columns'] as $fN => $fV)	{
					if ($fV['l10n_mode']!='exclude')	{
						$TCA[$table]['txdamLgOvlFields'][$fN] = $fN;
					}
				}
			}

			if($prependTableName) {
				foreach ($TCA[$table]['txdamLgOvlFields'] as $fn) {
					$fields[$fn] = $prependTableName.'.'.$fn;
				}
			} else {
				$fields = $TCA[$table]['txdamLgOvlFields'];
			}
		}

		return $fields;
	}


	/**
	 * Makes the list of fields the user can select/view for a table
	 *
	 * @param	string		Table name
	 * @param	boolean		If set, users access to the field (non-exclude-fields) is NOT checked.
	 * @param	boolean		$useExludeFieldList: ...
	 * @return	array		Array, where values are fieldnames to include in query
	 */
	function getFieldListForUser($table, $dontCheckUser = false, $useExludeFieldList = true) {
		global $TCA, $BE_USER;

			// Init fieldlist array:
		$fieldListArr = array();

			// Check table:
		if (is_array($TCA[$table])) {
			t3lib_div::loadTCA($table);

			$exludeFieldList = t3lib_div::trimExplode(',', $TCA[$table]['interface']['excludeFieldList'],1);

				// Traverse configured columns and add them to field array, if available for user.
			foreach ($TCA[$table]['columns'] as $fN => $fieldValue) {
				if (($dontCheckUser || ((!$fieldValue['exclude'] || $BE_USER->check('non_exclude_fields', $table.':'.$fN)) && $fieldValue['config']['type'] != 'passthrough')) AND (!$useExludeFieldList || !in_array($fN, $exludeFieldList))) {
					$fieldListArr[$fN] = $fN;
				}
			}

				// Add special fields:
			if ($dontCheckUser || $BE_USER->isAdmin()) {
				$fieldListArr['uid'] = 'uid';
				$fieldListArr['pid'] = 'pid';
				if ($TCA[$table]['ctrl']['tstamp'])
					$fieldListArr[$TCA[$table]['ctrl']['tstamp']] = $TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['crdate'])
					$fieldListArr[$TCA[$table]['ctrl']['tstamp']] = $TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['cruser_id'])
					$fieldListArr[$TCA[$table]['ctrl']['cruser_id']] = $TCA[$table]['ctrl']['cruser_id'];
				if ($TCA[$table]['ctrl']['sortby'])
					$fieldListArr[$TCA[$table]['ctrl']['cruser_id']] = $TCA[$table]['ctrl']['sortby'];
				if ($TCA[$table]['ctrl']['versioning'])
					$fieldListArr['t3ver_id'] = 't3ver_id';

				if ($TCA[$table]['ctrl']['versioningWS'])	{
					$fieldListArr['t3ver_id']='t3ver_id';
					$fieldListArr['t3ver_state']='t3ver_state';
					$fieldListArr['t3ver_wsid']='t3ver_wsid';
					if ($table==='pages')	{
						$fieldListArr['t3ver_swapmode']='t3ver_swapmode';
					}
				}

			}
		}
			// doesn't make sense, does it?
		unset ($fieldListArr['l18n_parent']);
		unset ($fieldListArr['l18n_diffsource']);

		return $fieldListArr;
	}





	/***************************************
	 *
	 *	 Helper
	 *
	 ***************************************/


	/**
	 * Evaluates record data using tcemain
	 * This is normally not needed because it's done automatically by tcemain, but it's helpful when using tceforms without tcemain
	 * MM fields are NOT evaluated they are passed through. That makes sense when used the data with tceforms.
	 *
	 * @param array $fieldArray
	 * @return array
	 */
	function evalData($table, $fieldArray) {
		global $TCA, $TYPO3_CONF_VARS;


			// Load TCA configuration for the given field:
		t3lib_div::loadTCA($table);

			// Create an instance of TCEmain and check the value:
		require_once(PATH_t3lib.'class.t3lib_tcemain.php');
		$TCEmain = t3lib_div::makeInstance ('t3lib_tcemain');

			// Traverse record and input-process each value:
		foreach($fieldArray as $field => $fieldValue)	{
			if (isset($TCA[$table]['columns'][$field]))	{
				$tcaFieldConf = $TCA[$table]['columns'][$field]['config'];

				if ($tcaFieldConf['MM']) {
					continue;
				}

				$res = array();
				$res = $TCEmain->checkValue_SW ($res, $fieldValue, $tcaFieldConf['config'], $table, -1, null, null, -1, null, null, null, null);
				if (isset($res['value']))	{
					$fieldArray[$field] = $res['value'];
				}
			}
		}
		return $fieldArray;
	}


	/**
	 * I'm wondering why there's no function like this somewhere else ?????
	 *
	 * @param	string		$data group element data from t3lib_transferdata
	 * @return	string		comma list
	 */
	function stripLabelFromGroupData($data) {
		$itemArray = array();
		$temp_itemArray = t3lib_div::trimExplode(',',$data,1);
		foreach($temp_itemArray as $dbRead)	{
			$recordParts = explode('|',$dbRead);
			$itemArray[] = $recordParts[0];
		}
		return implode (',', $itemArray);
	}


	/**
	 * Returns a part of a WHERE clause which will filter out records with start/end times or hidden/fe_groups fields set to values that should de-select them according to the current time, preview settings or user login. Definitely a frontend function.
	 * THIS IS A VERY IMPORTANT FUNCTION: Basically you must add the output from this function for EVERY select query you create for selecting records of tables in your own applications - thus they will always be filtered according to the "enablefields" configured in TCA
	 * Simply calls t3lib_pageSelect::enableFields() BUT will send the show_hidden flag along! This means this function will work in conjunction with the preview facilities of the frontend engine/Admin Panel.
	 *
	 * In comparison to t3lib_pageSelect::enableFields() this function don't prepend with ' AND '.
	 *
	 * @param	string		$table The table for which to get the where clause
	 * @param	string		$addOperator The table for which to get the where clause
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default. Special mode 'NONE' returns nothing, to not restrict queries.
	 * @return	string		The part of the where clause on the form " AND NOT [fieldname] AND ...". Eg. " AND hidden=0 AND starttime < 123345567"
	 * @see t3lib_pageSelect::enableFields()
	 */
	function enableFields($table, $addOperator='', $mode=TYPO3_MODE)	{
		$enableFields = '';

		if ($mode === 'NONE') {
			return '';
		} elseif ($mode === 'FE' AND is_object($GLOBALS['TSFE'])) {
			$enableFields = preg_replace('#^ AND #', '', $GLOBALS['TSFE']->sys_page->enableFields($table));
		} else {
			$enableFields =  tx_dam_db::deleteClause($table);
		}
		return ($enableFields AND $addOperator) ? ' '.$addOperator.' '.$enableFields : $enableFields;
	}


	/**
	 * Returns the WHERE clause "[tablename].[deleted-field]" if a deleted-field is configured in $TCA for the tablename, $table
	 * In comparison to t3lib_befunc:deleteClause() this function don't prepend with ' AND '.
	 *
	 * @param	string		Table name present in $TCA
	 * @param	string		Table alias if any
	 * @return	string		WHERE clause for filtering out deleted records, eg "tablename.deleted=0"
	 */
	function deleteClause($table,$tableAlias='')	{
		global $TCA;
		if ($TCA[$table]['ctrl']['delete'])	{
			return ($tableAlias ? $tableAlias : $table).'.'.$TCA[$table]['ctrl']['delete'].'=0';
		} else {
			return '';
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_db.php']);
}

?>
