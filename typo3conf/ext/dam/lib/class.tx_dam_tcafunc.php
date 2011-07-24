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
 * @package DAM-BeLib
 * @subpackage GUI
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   62: class tx_dam_tcaFunc
 *   95:     function addTableFieldsToItemArray (&$params, &$pObj)
 *
 *              SECTION: Tools
 *  164:     function getTypeFields ($table, $showItemDef, $includePalettes=true)
 *  196:     function getCleanFieldList($table, $list)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */








/**
 * Provide TCA functions for usage in own extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
class tx_dam_tcaFunc {


	/**
	 * Add items to an item array to be used for a select form element.
	 * This function add the fields of a table to the item list. This can be used to create a selector box in TCA for selecting a sorting field.
	 *
	 * Usage in flexform DS:
	 * <itemsProcFunc>EXT:dam/lib/class.tx_dam_tcafunc.php:tx_dam_tcaFunc-&gt;addTableFieldsToItemArray</itemsProcFunc>
	 * <itemsProcFunc_conf type="array">
	 * 	<numIndex index="fieldList"></numIndex>
	 * 	<numIndex index="fieldExcludeList">file_usage</numIndex>
	 * 	<numIndex index="fieldListFromTypes">*</numIndex>
	 * 	<numIndex index="addFieldList">my_special_field_not_configured_in_tca</numIndex>
	 * </itemsProcFunc_conf>
	 *
	 * Another configuration:
	 * <itemsProcFunc_conf type="array">
	 * 	<numIndex index="fieldList"></numIndex>
	 * 	<numIndex index="fieldExcludeList">file_usage,another_field</numIndex>
	 * 	<numIndex index="fieldListFromTypes">3,5,6</numIndex>
	 * 	<numIndex index="addFieldList">my_special_field_not_configured_in_tca</numIndex>
	 * </itemsProcFunc_conf>
	 *
	 * One more:
	 * <itemsProcFunc_conf type="array">
	 * 	<numIndex index="fieldList">title,media_type</numIndex>
	 * </itemsProcFunc_conf>
	 *
	 * @param	array	$params Comes from TCEForms
	 * @param	object	$pObj TCEForms object
	 * @return 	void
	 */
	function addTableFieldsToItemArray (&$params, &$pObj)	{
		global $TCA;


		$table = isset($params['config']['itemsProcFunc_conf']['table']) ? $params['config']['itemsProcFunc_conf']['table'] : 'tx_dam';
		t3lib_div::loadTCA($table);

		$fieldList = array();
		$fieldListFromType = array();

		if ($value = $params['config']['itemsProcFunc_conf']['fieldList']) {
			$fieldList = t3lib_div::trimExplode(',', $value, true);
		} elseif ($value = $params['config']['itemsProcFunc_conf']['fieldListFromTypes']) {
			if ($value === '*') {
				$fieldListFromType = array_keys($TCA[$table]['types']);
			} else {
				$fieldListFromType = t3lib_div::trimExplode(',', $value, true);
			}
		} else {
			$fieldListFromType[] = '0';
		}


			// get type fields including palette fields
		foreach($fieldListFromType as $type)	{
			$typeFieldList = tx_dam_tcaFunc::getTypeFields ($table, $TCA[$table]['types'][$type]['showitem'], true);
			$fieldList = array_merge($fieldList, $typeFieldList);
		}

			// get the fields to be added
		if ($value = $params['config']['itemsProcFunc_conf']['addFieldList']) {
			$addFieldList = tx_dam_tcaFunc::getCleanFieldList($table, $value);
			$fieldList = array_merge($fieldList, $addFieldList);
		}

			// excluding fields
		$fieldExcludeList = tx_dam_tcaFunc::getCleanFieldList($table, $params['config']['itemsProcFunc_conf']['fieldExcludeList']);
		$fieldList = array_diff($fieldList, $fieldExcludeList);

		if (!method_exists($pObj, 'sL') AND is_object($GLOBALS['LANG'])) {
			$pObj = & $GLOBALS['LANG'];
		}

			// add fields to item array
		foreach ($fieldList as $field) {
			$fieldConf = $TCA[$table]['columns'][$field];
			if(is_array($fieldConf) AND $fieldConf['label']) {
				$params['items'][] = array(preg_replace('#:$#', '', $pObj->sL($fieldConf['label'])), $field);
			}
		}
	}






	/*******************************************
	 *
	 * Tools
	 *
	 *******************************************/



	/**
	 * Get fields from a TCA field list
	 *
	 * @param	string	$table The table name
	 * @param	string	$list TCA field comma list
	 * @return 	array	Array with field names as key and value
	 */
	function getTypeFields ($table, $showItemDef, $includePalettes=true) {
		global $TCA;

		$fieldList = array();
		$paletteFieldList = array();

		$fieldListFromType = t3lib_div::trimExplode(',', $showItemDef, true);
		foreach($fieldListFromType as $v)	{

			list($field, $dummy, $palette) = t3lib_div::trimExplode(';', $v);

			if ($includePalettes AND $field === '--palette--' AND $palette) {
				$paletteFieldList = tx_dam_tcaFunc::getTypeFields ($table, $TCA[$table]['palettes'][$palette]['showitem'], false);
				$fieldList = array_merge($fieldList, $paletteFieldList);

			} elseif(is_array($TCA[$table]['columns'][$field])) {
				$fieldList[$field] = $field;
			}
		}

		return $fieldList;
	}


	/**
	 * Get fields from a TCA field list
	 *
	 * @param	string	$table The table name
	 * @param	string	$list TCA field comma list
	 * @return 	array	Array with field names as key and value
	 */
	function getCleanFieldList($table, $list) {
		global $TCA;

		$fieldList = array();
		$list = t3lib_div::trimExplode(',', $list, true);
		foreach($list as $v)	{
			list($field) = t3lib_div::trimExplode(';', $v);
			if(is_array($TCA[$table]['columns'][$field])) {
				$fieldList[$field] = $field;
			}
		}
		return $fieldList;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tcafunc.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tcafunc.php']);
}

?>