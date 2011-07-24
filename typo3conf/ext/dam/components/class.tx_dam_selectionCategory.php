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
 *   69: class tx_dam_selectionCategory extends tx_dam_selBrowseTree
 *   87:     function tx_dam_selectionCategory()
 *
 *              SECTION: DAM specific functions
 *  143:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damSel)
 *  176:     function getControl($title,$row)
 *
 *              SECTION: categories
 *  222:     function uniqueList()
 *  254:     function getSubRecords ($uidList, $level=1, $fields='*', $table='tx_dam_cat', $where='')
 *  286:     function getSubRecordsIdList($uidList, $level=1, $table='tx_dam_cat', $where='')
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_selprocbase.php');





/**
 * category tree class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
class tx_dam_selectionCategory extends tx_dam_selBrowseTree {

	/**
	 * Defines if a browsetree for TCEForms can be rendered
	 */
	var $isTCEFormsSelectClass = true;

	/**
	 * If mounts are supported (be_users)
	 */
	var $supportMounts = true;


	/**
	 * constructor
	 *
	 * @return	void
	 */
	function tx_dam_selectionCategory()	{
		global $LANG, $BACK_PATH;

		$this->title = $LANG->sL('LLL:EXT:dam/lib/locallang.xml:categories');
		$this->treeName = 'txdamCat';
		$this->domIdPrefix = $this->treeName;

		$this->table = 'tx_dam_cat';
		$this->parentField = $GLOBALS['TCA'][$this->table]['ctrl']['treeParentField'];
		$this->typeField = $GLOBALS['TCA'][$this->table]['ctrl']['type'];

		$this->iconName = 'cat.gif';
		$this->iconPath = PATH_txdam_rel.'i/';
		$this->rootIcon = PATH_txdam_rel.'i/catfolder.gif';

		$this->fieldArray = array('uid','pid','title','sys_language_uid');
		if($this->parentField) $this->fieldArray[] = $this->parentField;
		if($this->typeField) $this->fieldArray[] = $this->typeField;
		$this->defaultList = 'uid,pid,tstamp,sorting';

		$this->clause = tx_dam_db::enableFields($this->table, 'AND');
		$this->clause .= ' AND sys_language_uid IN (0,-1)';

		// default_sortby might be not set
		$defaultSortby = ($GLOBALS['TCA'][$this->table]['ctrl']['default_sortby']) ? $GLOBALS['TYPO3_DB']->stripOrderBy($GLOBALS['TCA'][$this->table]['ctrl']['default_sortby']) : '';
		// sortby might be not set or unset
		$sortby = ($GLOBALS['TCA'][$this->table]['ctrl']['sortby']) ? $GLOBALS['TCA'][$this->table]['ctrl']['sortby'] : '';
		// if we have default_sortby it shall win
		$this->orderByFields = ($defaultSortby) ? $defaultSortby : $sortby;


			// get the right sys_language_uid for the BE users language
		if (is_object($GLOBALS['BE_USER']) AND t3lib_extMgm::isLoaded('static_info_tables')) {
			
			// Hooray - it's so simple to develop with TYPO3
			
			$lang = $GLOBALS['BE_USER']->user['lang'];
			$lang = $lang ? $lang : 'en';
		
				// TYPO3 specific: Array with the iso names used for each system language in TYPO3:
				// Missing keys means: same as Typo3
			$isoArray = array(
				'ba' => 'bs',
				'br' => 'pt_BR',
				'ch' => 'zh_CN',
				'cz' => 'cs',
				'dk' => 'da',
				'si' => 'sl',
				'se' => 'sv',
				'gl' => 'kl',
				'gr' => 'el',
				'hk' => 'zh_HK',
				'kr' => 'ko',
				'ua' => 'uk',
				'jp' => 'ja',
				'vn' => 'vi',
			);
			$iso = $isoArray[$lang] ? $isoArray[$lang] : $lang;

				// Finding the ISO code:
			if ($rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'sys_language.uid',
						'sys_language LEFT JOIN static_languages ON static_languages.uid=sys_language.static_lang_isocode',
						'static_languages.lg_iso_2='.$GLOBALS['TYPO3_DB']->fullQuoteStr(strtoupper($iso), 'static_languages').tx_dam_db::enableFields('static_languages', 'AND').tx_dam_db::enableFields('sys_language', 'AND')
					)) {
				$row = current($rows);
				$this->langOvlUid = intval($row['uid']);
			}
		}


		$this->TSconfig = tx_dam::config_getValue('setup.selections.'.$this->treeName, true);

	}


	/**
	 * Returns the title for the input record. If blank, a "no title" labele (localized) will be returned.
	 * Do NOT htmlspecialchar the string from this function - has already been done.
	 *
	 * @param	array		The input row array (where the key "title" is used for the title)
	 * @param	integer		Title length (30)
	 * @return	string		The title.
	 */
	function getTitleStr($row,$titleLen=30)	{
		$conf['sys_language_uid'] = $this->langOvlUid;
		$row = tx_dam_db::getRecordOverlay($this->table, $row, $conf);
		$title = (!strcmp(trim($row['title']),'')) ? '<em>['.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.no_title',1).']</em>' : htmlspecialchars(t3lib_div::fixed_lgd_cs($row['title'],$titleLen));
		return $title;
	}

	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/


	/**
	 * Return a control (eg. selection icons) for the element
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 */
	function getControl($title,$row)	{
		global $BACK_PATH;

		$control = '';

		if ($this->modeSelIcons
			AND !($this->mode === 'tceformsSelect')
			AND ($row['uid'] OR ($row['uid'] == '0' AND $this->linkRootCat))) {

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'OR').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/plus.gif', 'width="8" height="11"').' alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'AND').'\',this,\''.$this->treeName.'\');';
			$icon = '<img src="'.$BACK_PATH.PATH_txdam_rel.'i/equals.gif" width="8" height="11" border="0" alt="" />';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/equals.gif', 'width="8" height="11"').' alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'NOT').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/minus.gif', 'width="8" height="11"').' alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
			$control .= '<img src="'.$BACK_PATH.'clear.gif" width="12" height="11" border="0" alt="" />';
		}
		return $control;
	}





	/********************************
	 *
	 * DAM specific SQL functions
	 *
	 ********************************/


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
	 * @return	array
	 * @see tx_dam_SCbase::getWhereClausePart()
	 */
	function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damSel)      {
		$this->damSel = & $damSel;

		$depth = isset($this->TSconfig['sublevelDepth']) ? intval($this->TSconfig['sublevelDepth']) : 99;

		$catUidList = $this->uniqueList(intval($id), $this->getSubRecordsIdList(intval($id), $depth, 'tx_dam_cat'));

		if ($operator != '!=') {
			if ($queryType == 'OR') {
				$query = '(FIND_IN_SET('.implode(',GROUP_CONCAT(tx_dam_cat.uid)) OR FIND_IN_SET(',$catUidList).',GROUP_CONCAT(tx_dam_cat.uid)))';
			}
			if ($queryType == 'AND' || $queryType == 'SELECT') {
				$query = '(FIND_IN_SET(' . $id . ',GROUP_CONCAT(tx_dam_cat.uid)))';
			}
		} else {
			$query = '(NOT FIND_IN_SET('.implode(',GROUP_CONCAT(tx_dam_cat.uid)) OR NOT FIND_IN_SET(',$catUidList).',GROUP_CONCAT(tx_dam_cat.uid)))';
		}
		
		//$this->damSel->qg->query['LEFT_JOIN']['tx_dam_mm_cat'] = 'tx_dam_mm_cat.uid_local = tx_dam.uid';
		$this->damSel->qg->addMMJoin('tx_dam_mm_cat', 'tx_dam');
		$this->damSel->qg->query['LEFT_JOIN']['tx_dam_cat'] = 'tx_dam_mm_cat.uid_foreign = tx_dam_cat.uid';
		$this->damSel->qg->query['GROUPBY']['tx_dam.uid'] = 'tx_dam.uid';
		$this->damSel->qg->query['HAVING']['txdamCat.'.$id] = $query;
		return array();
	}


	/**
	 * Function, processing the query part for selecting/filtering records in DAM
	 * Called from DAM
	 *
	 * @param	string		Category - corresponds to the "treename" used for the category tree in the nav. frame
	 * @param	array		Mount itmes
	 * @param	object		Reference to the parent DAM object.
	 * @return	string
	 * @see tx_dam_selection::getSelectionWhereClauseArray()
	 */
//	function selection_getQueryPartRestrictAccess($cat, $mounts, &$damSel)      {
//
//		if ($mounts AND current($mounts)) {
//
//			$this->damSel = & $damSel;
//		
//			$depth = isset($this->TSconfig['sublevelDepth']) ? intval($this->TSconfig['sublevelDepth']) : 99;
//
//			$uidArray = array();
//			foreach ($mounts as $mount) {
//				$uidArray[] = $mount;
//				$rows = $this->getSubRecords (intval($mount), $depth, 'uid', 'tx_dam_cat');
//				$uidArray = array_merge($uidArray, $rows);
//			}
//			$catUidList = implode(',',array_keys($uidArray));
//
//			foreach ($this->aliases as $alias) {
//				$query = 'AND '.$alias.'.uid_foreign IN ('.$catUidList.')';
//				$damSel->qg->addMMJoin('tx_dam_mm_cat', 'tx_dam', $alias, $query);
//			}
//
//		}
//		$queryType = 'AND';
//		$query = 'tx_dam_mm_cat_a.uid_local IS NOT NULL';
//		return array($queryType,$query);
//	}





	/***************************************
	 *
	 *	 categories
	 *
	 ***************************************/



	/**
	 * Takes comma-separated lists and arrays and removes all duplicates.
	 *
	 * @param	string		Accept multiple parameters wich can be comma-separated lists of values and arrays.
	 * @return	array		Returns the list without any duplicates of values, space around values are trimmed
	 */
	function uniqueList()	{
		$listArray = array();

		$arg_list = func_get_args();
		foreach ($arg_list as $in_list)	{

			if (!is_array($in_list) AND empty($in_list))	{
				continue;
			}

			if (!is_array($in_list))	{
				$in_list = t3lib_div::trimExplode(',',$in_list,true);
			}
			if(count($in_list)) {
				$listArray = array_merge($listArray,$in_list);
			}
		}

		return array_unique($listArray);
	}


	/**
	 * Returns an array with rows for subrecords with parent_id IN ($uidList).
	 *
	 * @param	integer		$uidList UID list of records
	 * @param	integer		$level Level depth. How deep walk into the tree. Default is 1.
	 * @param	string		$fields List of fields to select (default is '*').
	 * @param	string		$table Table name. Default 'tx_dam_cat'
	 * @param	string		$where Additional WHERE clause, eg. " AND blablabla=0"
	 * @return	array		Returns the rows if found, otherwise empty array
	 */
	function getSubRecords ($uidList, $level=1, $fields='*', $table='tx_dam_cat', $where='')	{
		$rows = array();

		while ($level && $uidList)	{
			$level--;

			$newIdList = array();
			t3lib_div::loadTCA($table);
			$ctrl = $GLOBALS['TCA'][$table]['ctrl'];
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $ctrl['treeParentField'].' IN ('.$uidList.') '.$where.$this->damSel->qg->enableFields($table));
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$rows[$row['uid']] = $row;
				$newIdList[] = $row['uid'];
			}
			$uidList = implode(',', $newIdList);

		}


		return $rows;
	}


	/**
	 * Returns a commalist of sub record uid's with parent_id IN ($uidList).
	 *
	 * @param	integer		$uidList UID list of records
	 * @param	integer		$level Level depth. How deep walk into the tree. Default is 1.
	 * @param	string		$table Table name. Default 'tx_dam_cat'
	 * @param	string		$where Additional WHERE clause, eg. " AND blablabla=0"
	 * @return	string		Comma-list of record ids
	 */
	function getSubRecordsIdList($uidList, $level=1, $table='tx_dam_cat', $where='')	{
		$uidList = $GLOBALS['TYPO3_DB']->cleanIntList($uidList);
		$rows = $this->getSubRecords ($uidList, $level, 'uid', $table, $where);
		return implode(',',array_keys($rows));
	}

}





if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionCategory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionCategory.php']);
}
?>
