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
 *   66: class tx_dam_selectionMeTypes extends tx_dam_selBrowseTree
 *   85:     function tx_dam_selectionMeTypes()
 *  110:     function getJumpToParam($row, $command='SELECT')
 *  128:     function getTitleStr ($row)
 *  146:     function getIcon($row)
 *
 *              SECTION: DAM specific functions
 *  171:     function selection_getItemTitle($id)
 *  184:     function selection_getItemIcon($id, $value)
 *  211:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *
 * TOTAL FUNCTIONS: 7
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
class tx_dam_selectionMeTypes extends tx_dam_selBrowseTree {

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
	function tx_dam_selectionMeTypes()	{
		global $LANG;

		$this->title = $LANG->sL('LLL:EXT:dam/lib/locallang.xml:mediaTypes');
		$this->treeName = 'txdamMedia';
		$this->domIdPrefix = $this->treeName;
		$this->iconName = 'mediatype.gif';
		$this->iconPath = PATH_txdam_rel.'i/';
		$this->rootIcon = PATH_txdam_rel.'i/mediafolder.gif';

		$this->table = 'tx_dam_metypes_avail';
		$this->parentField = 'parent_id';
		$this->orderByFields = 'sorting,title';
		$this->fieldArray = array('uid','parent_id','title','type','sorting');
		$this->defaultList = 'uid,pid,tstamp,sorting';
	}


	/**
	 * Returns jump-url parameter value.
	 *
	 * @param	array		The record array.
	 * @param	string		$command: SELECT, ...
	 * @return	string		The jump-url parameter.
	 */
	function getJumpToParam($row, $command='SELECT') {
		if($row['parent_id']){
			$id = $row['title'];
		} else {
			$id = $row['type'];
		}
		return '&SLCMD['.$command.']['.$this->treeName.']['.$id.']=1';
	}


	/**
	 * Returns the title for the input record. If blank, a "no title" labele (localized) will be returned.
	 * Do NOT htmlspecialchar the string from this function - has already been done.
	 *
	 * @param	array		The input row array(where the key "title" is used for the title)
	 * @param	integer		Title length (30)
	 * @return	string		The title.
	 */
	function getTitleStr ($row) {
		if ($row['parent_id']==0) {
			$title = tx_dam::convert_mediaType($row['type']);
			if(is_object($GLOBALS['LANG'])) {
				$title = $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:'.$title);
			}
		}
		return $title ? $title : $row['title'];
	}


	/**
	 * Get icon for the row.
	 * If $this->iconPath and $this->iconName is set, try to get icon based on those values.
	 *
	 * @param	array		Item row.
	 * @return	string		Image tag.
	 */
	function getIcon($row) {
		if($row['parent_id']){
			$icon = '<img' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $this->iconPath . $this->iconName, 'width="18" height="16"') . ' class="typo3-icon" alt="" />';
		} else {
			$icon = '<img' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $this->iconPath . '18/mtype_' . tx_dam::convert_mediaType($row['type']) . '.gif', 'width="18" height="16"') . ' class="typo3-icon" alt="" />';
		}

		return $this->wrapIcon($icon,$row);
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
		$itemTitle = $this->getTitleStr(array('title' => $id, 'type' => $id));
		return $itemTitle;
	}


	/**
	 * Returns the icon of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemIcon($id, $value)	{
		if(!intval($id)){
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',',$this->fieldArray), $this->table, 'title='.$GLOBALS['TYPO3_DB']->fullQuoteStr($id,$this->table));
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$id = $row['type'];
		}
		if(intval($id)){
			$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],$this->iconPath.'18/mtype_'.tx_dam::convert_mediaType($id).'.gif','width="18" height="16"').' class="typo3-icon" alt="" />';
		}

		return $icon;
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
		if (t3lib_div::testInt($id)) {
			$query= $damObj->sl->getFieldMapping('tx_dam', 'media_type').$operator.intval($id);
		} else {
			$query= $damObj->sl->getFieldMapping('tx_dam', 'file_type').$operator.$GLOBALS['TYPO3_DB']->fullQuoteStr($id,'tx_dam');
		}
		return array($queryType,$query);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionMeTypes.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionMeTypes.php']);
}
?>