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
 *   64: class tx_dam_selectionStatus extends tx_dam_selBrowseTree
 *   83:     function tx_dam_selectionStatus()
 *  102:     function getTreeArray()
 *  150:     function getDataInit($parentId)
 *
 *              SECTION: DAM specific functions
 *  176:     function selection_getItemTitle($id)
 *  195:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *
 * TOTAL FUNCTIONS: 5
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
class tx_dam_selectionStatus extends tx_dam_selBrowseTree {

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
	function tx_dam_selectionStatus()	{
		global $LANG;

		$this->title = $LANG->sL('LLL:EXT:dam/lib/locallang.xml:status');
		$this->treeName = 'txdamStatus';
		$this->domIdPrefix = $this->treeName;

		$this->iconName = 'statustype.gif';
		$this->iconPath = PATH_txdam_rel.'i/';
		$this->rootIcon = PATH_txdam_rel.'i/statusfolder.gif';
	}



	/**
	 * Returns an array that can be browsed by the treebrowse-class
	 *
	 * @return  array       Multidimensional.
	 */
	function getTreeArray()	{
		global $LANG;

		$tree = array(
			1 => array(
				'title' => $LANG->sL('LLL:EXT:dam/lib/locallang.xml:status_file_ok'),
				'id' => TXDAM_status_file_ok,
				'_field' => 'file_status',
			),
			2 => array(
				'title' => $LANG->sL('LLL:EXT:dam/lib/locallang.xml:status_file_changed'),
				'id' => TXDAM_status_file_changed,
				'_field' => 'file_status',
			),
			3 => array(
				'title' => $LANG->sL('LLL:EXT:dam/lib/locallang.xml:status_file_missing'),
				'id' => TXDAM_status_file_missing,
				'_field' => 'file_status',
			),
			4 => array(
				'title' => $LANG->sL('LLL:EXT:dam/lib/locallang.xml:status_idx_man'),
				'id' => 'man',
				'_field' => 'index_type',
			),
			5 => array(
				'title' => $LANG->sL('LLL:EXT:dam/lib/locallang.xml:status_idx_auto'),
				'id' => 'auto',
				'_field' => 'index_type',
			),
			6 => array(
				'title' => $LANG->sL('LLL:EXT:dam/lib/locallang.xml:status_idx_cron'),
				'id' => 'cron',
				'_field' => 'index_type',
			)
		);
		return $tree;
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
		if (!is_array($this->data)) {
			$this->_data = $this->getTreeArray();
			$this->setDataFromArray($this->_data);
		}
		return parent::getDataInit($parentId);
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
		$tree = $this->getTreeArray();
		return $tree[$id]['title'];
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
		$tree = $this->getTreeArray();
		$query= $damObj->sl->getFieldMapping('tx_dam', $tree[$id]['_field']).$operator.$GLOBALS['TYPO3_DB']->fullQuoteStr($tree[$id]['id'], 'tx_dam');
		return array($queryType,$query);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionStatus.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionStatus.php']);
}
?>