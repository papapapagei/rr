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
 *   76: class tx_dam_simpleForms extends t3lib_TCEforms
 *   84:     function setVirtualTable($virtual, $existant)
 *  118:     function removeVirtualTable($virtual='')
 *  132:     function initDefaultBEmode()
 *  156:     function getFormFromList($row,$list)
 *  169:     function getForm($row,$depth=0)
 *  182:     function setNewBEDesign($enableCheckboxes=true)
 *  240:     function setSimpleBEDesign()
 *  287:     function setNewBEDesignOrig()
 *  303:     function addUserTemplateMarkers($marker,$table,$field,$row,&$PA)
 *  336:     function wrapTotal($c, $rec, $table='')
 *  347:     function wrapItem ($content)
 *  356:     function removeRequired()
 *  369:     function addRequired($field)
 *  379:     function removeTreeViewBrowseable()
 *  393:     function setRequired($columns)
 *  408:     function setNonEditable($columnsExclude='')
 *  433:     function setBackToEditable()
 *  448:     function setNonReadOnly()
 *  463:     function setBackToReadOnly()
 *  478:     function removeMM()
 *
 *              SECTION: local versions to make virtual table work
 *  504:     function setTSconfig($table,$row,$field='')
 *  528:     function getTSCpid($table,$uid,$pid)
 *
 * TOTAL FUNCTIONS: 22
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once (PATH_t3lib.'class.t3lib_tceforms.php');

/**
 * Modified TCEforms for usage in simple forms for data input and NOT record editing.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
class tx_dam_simpleForms extends t3lib_TCEforms {

	var $tx_dam_fixedFields = array();

	var $savedGroupData = '';

	var $virtual = '';

	function setVirtualTable($virtual, $existant) {
		global $BE_USER, $TCA;

			// fake table - to be safe
		t3lib_div::loadTCA($existant);
		$TCA[$virtual] = $TCA[$existant];
		$this->virtual = $virtual;
		$TCA[$virtual]['orig_table'] = $existant;

		$this->savedGroupData = $BE_USER->groupData;

		$checkFields = array('explicit_allowdeny', 'tables_select', 'tables_modify', 'non_exclude_fields');
		foreach ($checkFields as $key) {
			$addList = '';
			$checkList = t3lib_div::trimExplode(',', $BE_USER->groupData[$key], 1);
			foreach ($checkList as $val) {
				list($table,$field) = explode(':', $val, 2);

				if($val==$existant) {
					$addList.= ','.$virtual;
				} elseif($table==$existant AND $field) {
					$addList.= ','.$virtual.':'.$field;
				}
			}
			$BE_USER->groupData[$key] .= $addList;
		}
	}


	/**
	 * Removes the virtual table from tCA
	 *
	 * @param	string		$virtual Virtual table name
	 * @return	@return	void
	 */
	function removeVirtualTable($virtual='') {
		global $BE_USER, $TCA;

		$BE_USER->groupData = $this->savedGroupData;
		$virtual = $virtual ? $virtual : $this->virtual;
		unset($TCA[$virtual]);
	}


	/**
	 * Initialize various internal variables.
	 *
	 * @return	void
	 */
	function initDefaultBEmode()	{
		global $BACK_PATH;

		parent::initDefaultBEmode();

		$this->backPath = $BACK_PATH;
		#$this->doSaveFieldName='update';
		$this->palettesCollapsed = 0;
		$this->disableRTE = 1;
		$this->edit_showFieldHelp ='text';
		$this->globalShowHelp = 1;
		$this->hiddenFieldList = '';
		$this->edit_docModuleUpload = FALSE;
	}


	/**
	 * Will return the TCEform elements for a pre-defined list of fields.
	 * Notice that this will STILL use the configuration found in the list [types][showitem] for those fields which are found there. So ideally the list of fields given as argument to this function should also be in the current [types][showitem] list of the record.
	 *
	 * @param	array		The record array.
	 * @param	string		Commalist of fields from the table. These will be shown in the specified order in a form.
	 * @return	string		TCEform elements in a string.
	 */
	function getFormFromList($row,$list)	{
		return $this->getListedFields($this->virtual, $row, $list);
	}


	/**
	 * Based on the $table and $row of content, this displays the complete TCEform for the record.
	 * The input-$row is required to be preprocessed if necessary by eg. the t3lib_transferdata class. For instance the RTE content should be transformed through this class first.
	 *
	 * @param	array		The record from the table for which to render a field.
	 * @param	integer		Depth level
	 * @return	string		HTML output
	 */
	function getForm($row,$depth=0)	{
		return $this->getMainFields($this->virtual, $row, $depth);
	}



	/**
	 * Sets the design to the backend design.
	 * Backend
	 *
	 * @param	boolean		$enableCheckboxes If set a columns will be prepended for using eg. checkboxes
	 * @return	void
	 */
	function setNewBEDesign($enableCheckboxes=true)	{
		global $BACK_PATH;

		parent::setNewBEDesign();

		$this->totalWrap='<table border="0" cellpadding="0" cellspacing="0" width="50%">|</table>';


		if ($enableCheckboxes) {
			$_FIELD_SETFIXED = '###FIELD_SETFIXED###';
			$_BGCOLOR_HEAD = ' ###BGCOLOR_HEAD###';
		}

		$this->fieldTemplate = '
			<tr ###CLASSATTR_2###>
				<td>###FIELD_HELP_ICON###</td>
				<td width="99%"><span style="color:###FONTCOLOR_HEAD###;"###CLASSATTR_4###><b>###FIELD_NAME###</b></span>###FIELD_HELP_TEXT###</td>
			</tr>
			<tr ###CLASSATTR_1###>
				<td nowrap="nowrap" valign="middle"'.$_BGCOLOR_HEAD.'>'.
#				'<img name="req_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="'.$BACK_PATH.'clear.gif" width="10" height="10" alt="" />'.
				$_FIELD_SETFIXED.
				'<img name="cm_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="###FIELD_REQICON###" width="10" height="10" alt="" /></td>
				<td valign="top">###FIELD_ITEM######FIELD_PAL_LINK_ICON###</td>
			</tr>
			<tr>
				<td colspan="2"><img src="'.$BACK_PATH.'clear.gif" width="1" height="4" alt="" /></td>
			</tr>';


		$this->palFieldTemplate = '
			<tr ###CLASSATTR_1###>
				<td>&nbsp;</td>
				<td nowrap="nowrap" valign="top">###FIELD_PALETTE###</td>
			</tr>';

		$this->palFieldTemplateHeader = '
			<tr ###CLASSATTR_2###>
				<td>&nbsp;</td>
				<td nowrap="nowrap" valign="top"><strong>###FIELD_HEADER###</strong></td>
			</tr>';

		$this->sectionWrap = '
			<tr>
				<td colspan="2"><img src="clear.gif" width="1" height="###SPACE_BEFORE###" alt="" /></td>
			</tr>
			<tr>
				<td colspan="2"><table ###TABLE_ATTRIBS###>###CONTENT###</table></td>
			</tr>
			';
	}

	/**
	 * Sets the design to the backend design.
	 * Backend
	 *
	 * @return	void
	 */
	function setSimpleBEDesign()	{
		global $BACK_PATH;

		parent::setNewBEDesign();

		$this->totalWrap='<table border="0" cellpadding="0" cellspacing="0" width="100%">|</table>';

			// Wrapping a single field:
		$this->fieldTemplate='
			<tr ###BGCOLOR######CLASSATTR_2###>
				<td>###FIELD_HELP_ICON###</td>
				<td width="99%"><span style="color:###FONTCOLOR_HEAD###;"###CLASSATTR_4###><b>###FIELD_NAME###</b></span>###FIELD_HELP_TEXT###</td>
			</tr>
			<tr ###BGCOLOR######CLASSATTR_1###>
				<td nowrap="nowrap" style="padding-bottom:4px;"><img name="req_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="###FIELD_REQICON###" width="10" height="10" alt="" /><img name="cm_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="clear.gif" width="7" height="10" alt="" /></td>
				<td valign="top" style="padding-bottom:4px;">###FIELD_ITEM######FIELD_PAL_LINK_ICON###</td>
			</tr>';

		$this->palFieldTemplate='
			<tr ###BGCOLOR######CLASSATTR_1###>
				<td>&nbsp;</td>
				<td nowrap="nowrap" valign="top">###FIELD_PALETTE###</td>
			</tr>';
		$this->palFieldTemplateHeader='
			<tr ###BGCOLOR######CLASSATTR_2###>
				<td>&nbsp;</td>
				<td nowrap="nowrap" valign="top"><strong>###FIELD_HEADER###</strong></td>
			</tr>';

		$this->sectionWrap='
			<tr>
				<td colspan="2"><img src="clear.gif" width="1" height="###SPACE_BEFORE###" alt="" /></td>
			</tr>
			<tr>
				<td colspan="2"><table ###TABLE_ATTRIBS###>###CONTENT###</table></td>
			</tr>
			';
	}



	/**
	 * Sets the design to the original backend design.
	 * Backend
	 *
	 * @return	void
	 */
	function setNewBEDesignOrig()	{
		parent::setNewBEDesign();
		
			// Wrapping a single field:
		$this->fieldTemplate='
			<tr ###BGCOLOR_HEAD######CLASSATTR_2###>
				<td>###FIELD_HELP_ICON###</td>
				<td width="99%"><span style="color:###FONTCOLOR_HEAD###;"###CLASSATTR_4###><b>###FIELD_NAME###</b></span>###FIELD_HELP_TEXT###</td>
			</tr>
			<tr ###BGCOLOR######CLASSATTR_1###>
				<td nowrap="nowrap"><img name="req_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="###FIELD_REQICON###" width="10" height="10" alt="" /><img name="cm_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="clear.gif" width="7" height="10" alt="" /></td>
				<td valign="top">###FIELD_ITEM######FIELD_PAL_LINK_ICON###</td>
			</tr>';		
		
	}


	/**
	 * add own markers for output
	 *
	 * @param	array		Array with key/value pairs to insert in the template.
	 * @param	string		The table name of the record
	 * @param	string		The field name which this element is supposed to edit
	 * @param	array		The record data array where the value(s) for the field can be found
	 * @param	array		An array with additional configuration options.
	 * @return	array		marker array for template output
	 * @see function intoTemplate()
	 */
	function addUserTemplateMarkers($marker,$table,$field,$row,&$PA)	{
		global $BACK_PATH;

		if($PA['fieldConf']['config']['type'] === 'none' OR $PA['fieldConf']['config']['readOnly']) {
			if(in_array($field, $this->tx_dam_fixedFields)) {
				$marker['SETFIXED']='<img src="'.$BACK_PATH.'gfx/pil2right.gif" width="7" height="12" vspace="2" alt="" />';
			}
		} else {
			$itemFormElName = $this->prependFormFieldNames.'_fixedFields['.$table.']['.$row['uid'].']['.$field.']';		// Form field name

			$marker['SETFIXED'] = '<input type="hidden" name="'.$itemFormElName.'" value="0" />'.
								'<input type="checkbox" name="'.$itemFormElName.'"'.(in_array($field, $this->tx_dam_fixedFields)?' checked':'').' value="1" />';
		}

		if ($this->requiredFields[$field]) {
			$marker['REQICON'] = t3lib_iconWorks::skinImg($BACK_PATH,'gfx/required_h.gif','',1);
		} else {
			$marker['REQICON'] = $BACK_PATH.'clear.gif';
		}

		return $marker;
	}

	/**
	 * Wraps all the table rows into a single table.
	 * Used externally from scripts like alt_doc.php and db_layout.php (which uses TCEforms...)
	 *
	 * @param	string		Code to output between table-parts; table rows
	 * @param	array		The record
	 * @param	string		The table name
	 * @return	string
	 */
	function wrapTotal($c, $rec, $table='')	{
		$table = $table ? $table : $this->virtual;
		return parent::wrapTotal($c, $rec, $table);
	}

	/**
	 * Place content into $this->palFieldTemplate by replacing the marker ###FIELD_PALETTE###
	 *
	 * @param	string		$content: ...
	 * @return	string HTML content
	 */
	function wrapItem ($content) {
		return str_replace('###FIELD_PALETTE###',$content,$this->palFieldTemplate);
	}

	/**
	 * Remove "required" from the TCA array
	 *
	 * @return	void
	 */
	function removeField($field) {
		global $TCA;
		unset($TCA[$this->virtual]['columns'][$field]);
	}

	/**
	 * Remove "required" from the TCA array
	 *
	 * @return	void
	 */
	function removeRequired() {
		global $TCA;
		foreach($TCA[$this->virtual]['columns'] as $field => $config) {
			$TCA[$this->virtual]['columns'][$field]['config']['eval'] = str_replace('required','',$TCA[$this->virtual]['columns'][$field]['config']['eval']);
		}
	}

	/**
	 * Add a field to the list of required fields
	 * This will juts force to display an [!] icon - nothing more
	 *
	 * @return	void
	 */
	function addRequired($field) {
		$this->requiredFields[$field] = true;
	}


	/**
	 * Remove "treeViewBrowseable" from the TCA array
	 *
	 * @return	void
	 */
	function removeTreeViewBrowseable() {
		global $TCA;
		foreach($TCA[$this->virtual]['columns'] as $field => $config) {
			$TCA[$this->virtual]['columns'][$field]['config']['treeViewBrowseable'] = false;
		}
	}

	/**
	 * Set "required" for a field list
	 *
	 * @param	string		$columns Comma list of fields to set required
	 * @param	array		$tca TCA Array
	 * @return	void
	 */
	function setRequired($columns) {
		global $TCA;
		$columns = t3lib_div::trimExplode(',', $columns, 1);
		foreach($columns as $field) {
			$this->addRequired($field);
			$TCA[$this->virtual]['columns'][$field]['config']['eval'] .= ',required';
		}
	}

	/**
	 * Set all fields in the TCA array to "readOnly"
	 *
	 * @param	string		$columnsExclude Comma list of fields to exclude
	 * @return	void
	 */
	function setNonEditable($columnsExclude='') {
		global $TCA;
		if (isset($this->renderReadonly)) {
			$this->renderReadonly = true;
		} else {
			$columnsExclude = t3lib_div::trimExplode(',', $columnsExclude, 1);
			foreach($TCA[$this->virtual]['columns'] as $field => $config) {
				if(!in_array($field, $columnsExclude)) {
					# $TCA[$this->virtual]['columns'][$field]['config']['type'] = 'none';
					if (!$TCA[$this->virtual]['columns'][$field]['config']['readOnly']) {
						$TCA[$this->virtual]['columns'][$field]['config']['readOnly'] = true;
						$TCA[$this->virtual]['columns'][$field]['config']['__wasEditable'] = true;
						$TCA[$this->virtual]['columns'][$field]['config']['size'] = max(5,$TCA[$this->virtual]['columns'][$field]['config']['size']);
					}
				}
			}
		}
	}

	/**
	 * Revert "readOnly" set by setNonEditable()
	 *
	 * @return	void
	 * @see setNonEditable()
	 */
	function setBackToEditable() {
		global $TCA;
		foreach($TCA[$this->virtual]['columns'] as $field => $config) {
			if($TCA[$this->virtual]['columns'][$field]['config']['__wasEditable']) {
				$TCA[$this->virtual]['columns'][$field]['config']['readOnly'] = false;
				unset($TCA[$this->virtual]['columns'][$field]['config']['__wasEditable']);
			}
		}
	}

	/**
	 * Set all fields in the TCA array to editable "readOnly=false"
	 *
	 * @return	void
	 */
	function setNonReadOnly() {
		global $TCA;
		foreach($TCA[$this->virtual]['columns'] as $field => $config) {
			if($TCA[$this->virtual]['columns'][$field]['config']['readOnly']) {
				$TCA[$this->virtual]['columns'][$field]['config']['readOnly'] = false;
				$TCA[$this->virtual]['columns'][$field]['config']['__wasReadOnly'] = true;
			}
		}
	}

	/**
	 * Revert "readOnly" set by setNonReadOnly()
	 *
	 * @return	void
	 */
	function setBackToReadOnly() {
		global $TCA;
		foreach($TCA[$this->virtual]['columns'] as $field => $config) {
			if($TCA[$this->virtual]['columns'][$field]['config']['__wasReadOnly']) {
				$TCA[$this->virtual]['columns'][$field]['config']['readOnly'] = true;
				unset($TCA[$this->virtual]['columns'][$field]['config']['__wasReadOnly']);
			}
		}
	}

	/**
	 * Remove "MM" from the TCA array
	 *
	 * @return	void
	 */
	function removeMM() {
		global $TCA;
		foreach($TCA[$this->virtual]['columns'] as $field => $config) {
			unset($TCA[$this->virtual]['columns'][$field]['config']['MM']);
		}
	}



	/********************************************
	 *
	 * local versions to make virtual table work
	 *
	 ********************************************/


	/**
	 * Format field content of various types if $config['format'] is set to date, filesize, ..., user
	 * This is primarily for the field type none but can be used for user field types for example
	 *
	 * @param	array		Configuration for the display
	 * @param	string		The value to display
	 * @return	string		Formatted Field content
	 */
//	function formatValue ($config, $itemValue)	{
//		
//		// workaround for : Warning: date() expects parameter 2 to be long, string given in
//		$format = trim($config['format']);
//		switch($format)	{		
//			case 'date':
//			case 'datetime':
//			case 'time':
//			case 'timesec':
//			case 'year':		
//				$itemValue = 0 + $itemValue;
//				break;
//		}
//		
//		return parent::formatValue ($config, $itemValue);
//	}



	/**
	 * Returns TSconfig for table/row
	 * Multiple requests to this function will return cached content so there is no performance loss in calling this many times since the information is looked up only once.
	 *
	 * @param	string		The table name
	 * @param	array		The table row (Should at least contain the "uid" value, even if "NEW..." string. The "pid" field is important as well, and negative values will be intepreted as pointing to a record from the same table.)
	 * @param	string		Optionally you can specify the field name as well. In that case the TSconfig for the field is returned.
	 * @return	mixed		The TSconfig values (probably in an array)
	 * @see t3lib_BEfunc::getTCEFORM_TSconfig()
	 */
	function setTSconfig($table,$row,$field='')	{

		$mainKey = $table.':'.$row['uid'];
		if (!isset($this->cachedTSconfig[$mainKey]))	{
# this tries to read the record again, which fails when using pseudo records
#			$this->cachedTSconfig[$mainKey] = t3lib_BEfunc::getTCEFORM_TSconfig($table,$row);
		}
		if ($field)	{
			return $this->cachedTSconfig[$mainKey][$field];
		} else {
			return $this->cachedTSconfig[$mainKey];
		}
	}

	/**
	 * Return TSCpid (cached)
	 * Using t3lib_BEfunc::getTSCpid()
	 *
	 * @param	string		Tablename
	 * @param	string		UID value
	 * @param	string		PID value
	 * @return	integer		Returns the REAL pid of the record, if possible. If both $uid and $pid is strings, then pid=-1 is returned as an error indication.
	 * @see t3lib_BEfunc::getTSCpid()
	 */
	function getTSCpid($table,$uid,$pid)	{
		$key = $table.':'.$uid.':'.$pid;
		if (!isset($this->cache_getTSCpid[$key]))	{
# this tries to read the record again, which fails when using pseudo records
#			$this->cache_getTSCpid[$key] = t3lib_BEfunc::getTSCpid($table,$uid,$pid);
		}
		return $this->cache_getTSCpid[$key];
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_simpleforms.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_simpleforms.php']);
}
?>