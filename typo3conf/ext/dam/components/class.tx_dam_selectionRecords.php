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
 *   62: class tx_dam_selectionRecords extends tx_dam_selProcBase
 *   70:     function tx_dam_selectionRecords()
 *   95:     function selection_getItemTitle($id, $value)
 *  111:     function selection_getItemIcon($id, $value)
 *  133:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *  158:     function selection_getQueryPartForItems($queryType, $cat, $itemArray, &$damObj)
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam_selprocbase.php');




/**
 * tx_dam.uid selection class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
class tx_dam_selectionRecords extends tx_dam_selProcBase {


	/**
	 * constructor
	 *
	 * @return	void
	 */
	function tx_dam_selectionRecords()	{
		global $LANG, $BACK_PATH;

		$this->isTreeViewClass = FALSE;
		$this->isPureSelectionClass = TRUE;

		$this->supportMounts = false;

		$this->deselectValue = '0';

		$this->title = $LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item');
		$this->treeName = 'txdamRecords';

		$this->table = 'tx_dam';
		$this->fieldArray = array('uid','title','file_type','media_type');
	}


	/**
	 * Returns the title of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemTitle($id, $value)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',',$this->fieldArray), $this->table, 'uid='.intval($id));
		if($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$value = $row['title'];
		}
		return $value;
	}


	/**
	 * Returns the icon of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemIcon($id, $value)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',',$this->fieldArray), $this->table, 'uid='.intval($id));
		if($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$icon = tx_dam::icon_getFileTypeImgTag($row);
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
	 * @see tx_dam_SCbase::getWhereClausePart()
	 */
	function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)      {

		if ($queryType === 'NOT')	{
			$query= $this->table.'.uid NOT IN ('.intval($id).')';
		} else {
			$query= $this->table.'.uid IN ('.intval($id).')';
		}


		return array($queryType,$query);
	}


	/**
	 * Function, processing the query part for selecting/filtering records in DAM
	 * Called from DAM
	 *
	 * @param	string		Query type: AND, OR, ...
	 * @param	string		Operator, eg. '!=' - see DAM Documentation
	 * @param	string		Category - corresponds to the "treename" used for the category tree in the nav. frame
	 * @param	string		The select value/id - value (true/false,...) array
	 * @param	object		Reference to the parent DAM object.
	 * @return	string
	 * @see tx_dam_selection::getSelectionWhereClauseArray()
	 */
	function selection_getQueryPartForItems($queryType, $cat, $itemArray, &$damObj)      {

		$ids = implode(',',array_keys($itemArray));

		if ($queryType === 'NOT')	{
			$query= $this->table.'.uid NOT IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($ids).')';
		} else {
			$query= $this->table.'.uid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($ids).')';
		}

		return array($queryType,$query);
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionRecords.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionRecords.php']);
}
?>