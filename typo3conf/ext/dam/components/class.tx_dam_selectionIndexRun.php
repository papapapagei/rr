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
 *   68: class tx_dam_selectionIndexRun extends tx_dam_selBrowseTree
 *   87:     function tx_dam_selectionIndexRun()
 *  116:     function getDataInit($parentId)
 *  138:     function getCount($uid)
 *  149:     function getJumpToParam($row, $command='SELECT')
 *  161:     function getTitleStr($row,$titleLen=30)
 *  177:     function getTitleAttrib($row)
 *  187:     function getId($row)
 *
 *              SECTION: DAM specific functions
 *  207:     function selection_getItemTitle($id)
 *  226:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once(PATH_txdam.'lib/class.tx_dam_selprocbase.php');





/**
 * media type tree class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
class tx_dam_selectionIndexRun extends tx_dam_selBrowseTree {

	/**
	 * is able to generate a browasable tree
	 */
	var $isTCEFormsSelectClass = true;

	/**
	 * is able to handle mount points (be_users)
	 */
	var $supportMounts = 'rootOnly';



	/**
	 * constructor
	 *
	 * @return	void
	 */
	function tx_dam_selectionIndexRun()	{
		global $LANG, $BACK_PATH, $currentTypes;

		$this->title = $LANG->sL('LLL:EXT:dam/lib/locallang.xml:indexRun');
		$this->treeName = 'txdamIndexRun';
		$this->domIdPrefix = $this->treeName;
		$this->iconName = 'indexruntype.gif';
		$this->iconPath = PATH_txdam_rel.'i/';
		$this->rootIcon = PATH_txdam_rel.'i/indexrunfolder.gif';

		$this->table='tx_dam_log_index';
		$this->parentField='';
		$this->clause='';
		$this->orderByFields='crdate DESC';
		$this->fieldArray = array('uid','item_count','type','crdate');
		$this->defaultList = 'uid,pid,tstamp,crdate,item_count,error,type';
	}



	/**
	 * Getting the tree data: Selecting/Initializing data pointer to items for a certain parent id.
	 * For tables: This will make a database query to select all children to "parent"
	 * For arrays: This will return key to the ->dataLookup array
	 *
	 * @param	integer		parent item id
	 * @return	mixed		data handle (Tables: An sql-resource, arrays: A parentId integer. -1 is returned if there were NO subLevel.)
	 * @access private
	 */
	function getDataInit($parentId) {
		$res = false;
		if (!$parentId) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'DISTINCT '.implode(',',$this->fieldArray),
					$this->table,
					'item_count>0 AND error=0',
					'',
					$this->orderByFields,
					10
				);
		}
		return $res;
	}

	/**
	 * Returns the number of records having the parent id, $uid
	 *
	 * @param	integer		id to count subitems for
	 * @return	integer
	 * @access private
	 */
	function getCount($uid)	{
		return 0;
	}

	/**
	 * Returns jump-url parameter value.
	 *
	 * @param	array		The record array.
	 * @param	string		$command: SELECT, ...
	 * @return	string		The jump-url parameter.
	 */
	function getJumpToParam($row, $command='SELECT') {
		return '&SLCMD['.$command.']['.$this->treeName.']['.rawurlencode($row['crdate']).']=1';
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
		if ($row['crdate']) {
			$title = date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'].'-'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'], $row['crdate']);
		} else {
			$title = $row['title'];
		}
		return $title;
	}

	/**
	 * Returns the value for the image "title" attribute
	 *
	 * @param	array		The input row array (where the key "title" is used for the title)
	 * @return	string		The attribute value (is htmlspecialchared() already)
	 * @see wrapIcon()
	 */
	function getTitleAttrib($row) {
		return htmlspecialchars($this->getTitleStr($row));
	}

	/**
	 * Returns the id from the record (typ. uid)
	 *
	 * @param	array		Record array
	 * @return	integer		The "uid" field value.
	 */
	function getId($row) {
		return $row['crdate'] ? $row['crdate'] : 0;
	}




	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/


	/**
	 * Returns the title of an item
	 *
	 * @param	string		$id The id of the item
	 * @return	string
	 */
	function selection_getItemTitle($id)	{
		$itemTitle = $this->getTitleStr(array('crdate' => $id));
		return $itemTitle;
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
		$query = $damObj->sl->getFieldMapping('tx_dam', 'crdate').$operator.intval($id);
		return array($queryType,$query);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionIndexRun.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionIndexRun.php']);
}
?>