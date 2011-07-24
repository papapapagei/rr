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
 * Module extension (addition to function menu) 'edit selection' for the 'Media>List' module.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   64: class tx_dam_list_editsel extends t3lib_extobjbase
 *   71:     function modMenu()
 *   90:     function head()
 *  115:     function main()
 *  294:     function jumpExt(URL,anchor)
 *
 *              SECTION: selector for fields to display
 *  334:     function fieldSelectBox($table, $allFields, $selectedFields, $formFields = true)
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

require_once(PATH_txdam.'lib/class.tx_dam_listrecords.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');

/**
 * Module extension  'Media>List>Selection'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
class tx_dam_list_editsel extends t3lib_extobjbase {

	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;


		return array(
			'tx_dam_list_editsel_onlyDeselected' => '1',
			'tx_dam_list_list_sortField' => '',
			'tx_dam_list_list_sortRev' => '',
			'tx_dam_list_list_displayFields' => '',
		);
	}


	/**
	 * Initialize the class and set some HTML header code
	 *
	 * @return	void
	 */
	function head() {
		global $LANG;

		//
		// Init gui items and ...
		//

		$this->pObj->guiItems->registerFunc('getResultInfoHeader', 'header');
#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'header');

#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'footer');
		$this->pObj->guiItems->registerFunc('getSearchBox', 'footer');
		$this->pObj->guiItems->registerFunc('getOptions', 'footer');
		$this->pObj->guiItems->registerFunc('getStoreControl', 'footer');

			// add some options
		$this->pObj->addOption('funcCheck', 'tx_dam_list_editsel_onlyDeselected', $LANG->getLL('tx_dam_list_editsel.onlyDeselected'));
	}


	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()    {
		global $BE_USER, $LANG, $BACK_PATH, $TCA, $TYPO3_CONF_VARS;
		

		$content = '';

		$table = 'tx_dam';

		//
		// get records by query depending on option 'Show deselected only'
		//

		$origSel = $this->pObj->selection->sl->sel;
		if($this->pObj->MOD_SETTINGS['tx_dam_list_editsel_onlyDeselected']) {
			if(is_array($this->pObj->selection->sl->sel['NOT']['txdamRecords'])) {
				$ids = array_keys($this->pObj->selection->sl->sel['NOT']['txdamRecords']);
			} else {
				$ids = array(0); //dummy
			}

			unset($this->pObj->selection->sl->sel['NOT']['txdamRecords']);
			$this->pObj->selection->addSelectionToQuery();
			if(is_array($ids)) {
				$this->pObj->selection->qg->query['WHERE']['WHERE']['NOT_txdamRecords'] = 'AND tx_dam.uid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList(implode(',',$ids)).')';
			}

		} else {
			unset($this->pObj->selection->sl->sel['NOT']['txdamRecords']);
			$this->pObj->selection->addSelectionToQuery();
		}
		$this->pObj->selection->sl->sel = $origSel;



		//
		// Use the current selection to create a query and count selected records
		//

		$this->pObj->selection->execSelectionQuery(TRUE);



		//
		// output header: info bar, result browser, ....
		//

		$content.= $this->pObj->guiItems->getOutput('header');
		$content.= $this->pObj->doc->spacer(10);

		//
		// current selection box
		//

		$content.= $this->pObj->getCurrentSelectionBox();
		$content.= $this->pObj->doc->spacer(25);


			// any records found?
		if($this->pObj->selection->pointer->countTotal) {


			t3lib_div::loadTCA($table);


			//
			// set fields to display
			//

			$titleColumn = $TCA[$table]['ctrl']['label'];

			$allFields = tx_dam_db::getFieldListForUser($table);

			$selectedFields = t3lib_div::_GP('tx_dam_list_list_displayFields');
			$selectedFields = is_array($selectedFields) ? $selectedFields : explode(',', $this->pObj->MOD_SETTINGS['tx_dam_list_list_displayFields']);


				// remove fields that can not be selected
			if (is_array($selectedFields)) {
				$selectedFields = array_intersect($allFields, $selectedFields);
				$selectedFields = array_merge(array($titleColumn), $selectedFields);
			} else {
				$selectedFields = array();
				$selectedFields[] = $titleColumn;
			}


				// store field list
			$this->pObj->MOD_SETTINGS['tx_dam_list_list_displayFields'] = implode(',', $selectedFields);
			$GLOBALS['BE_USER']->pushModuleData($this->pObj->MCONF['name'], $this->pObj->MOD_SETTINGS);



			//
			// set query and sorting
			//


			$orderBy = ($TCA[$table]['ctrl']['sortby']) ? 'tx_dam.'.$TCA[$table]['ctrl']['sortby'] : 'tx_dam.title';

			if ($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'])	{
				if (in_array($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'], $allFields))	{
					$orderBy = 'tx_dam.'.$this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'];
					if ($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortRev'])	$orderBy.=' DESC';
				}
			}

			$queryFieldList = tx_dam_db::getMetaInfoFieldList(false, $selectedFields);
			$this->pObj->selection->qg->addSelectFields($queryFieldList);
			$this->pObj->selection->qg->addOrderBy($orderBy);




			//
			// exec query
			//

			$this->pObj->selection->addLimitToQuery();
			$res = $this->pObj->selection->execSelectionQuery();

			//
			// init iterator for query
			//

			$conf = array(	'table' => 'tx_dam',
							'countTotal' => $this->pObj->selection->pointer->countTotal	);
			$dbIterator = new tx_dam_iterator_db($res, $conf);


			//
			// make db list
			//

			$dblist = t3lib_div::makeInstance('tx_dam_listrecords');
			$dblist->init($table, $dbIterator);
			$dblist->setDataObject($dbIterator);

				// add columns to list
			$dblist->clearColumns();
			$cc = 0;
			foreach ($selectedFields as $field) {
				$fieldLabel = is_array($TCA[$table]['columns'][$field]) ? preg_replace('#:$#', '', $LANG->sL($TCA[$table]['columns'][$field]['label'])) : '['.$field.']';
				$dblist->addColumn($field, $fieldLabel);
				$cc++;
				if($cc == 1) {
						// add control at second column
					$dblist->addColumn('_CONTROL_', '');
					$cc++;
				}
			}

				// enable display of action column
			$dblist->showActions = true;
				// enable context menus
			$dblist->enableContextMenus = true;
				// Enable/disable display of thumbnails
			$dblist->showThumbs = $this->pObj->MOD_SETTINGS['tx_dam_list_list_showThumb'];
				// Enable/disable display of AlternateBgColors
			$dblist->showAlternateBgColors = $this->pObj->config_checkValueEnabled('alternateBgColors', true);


			$dblist->setPointer($this->pObj->selection->pointer);
			$dblist->setCurrentSorting($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'], $this->pObj->MOD_SETTINGS['tx_dam_list_list_sortRev']);
			$dblist->setParameterName('sortField', 'SET[tx_dam_list_list_sortField]');
			$dblist->setParameterName('sortRev', 'SET[tx_dam_list_list_sortRev]');




			#$content.= '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post" name="dblistForm">';
			$content.= $dblist->getListTable();
			#$content.= '<input type="hidden" name="cmd_table"><input type="hidden" name="cmd"></form>';


			$fieldSelectBoxContent = $this->fieldSelectBox($table, $allFields, $selectedFields);
			$content.= $this->pObj->buttonToggleDisplay('fieldselector', $LANG->getLL('field_selector'), $fieldSelectBoxContent);


		}

		return $content;
	}




	/********************************
	 *
	 * selector for fields to display
	 *
	 ********************************/


	/**
	 * Create the selector box for selecting fields to display from a table:
	 *
	 * @param	string		Table name
	 * @param	array		all fields
	 * @param	array		selected fields
	 * @param	boolean		If true, form-fields will be wrapped around
	 * @return	string		HTML table with the selector box (name: displayFields['.$table.'][])
	 */
	function fieldSelectBox($table, $allFields, $selectedFields, $formFields = true) {
		global $TCA, $LANG;

		t3lib_div::loadTCA($table);

		$formElements = array('', '');
		if ($formFields) {
			$formElements = array('<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">', '</form>');
		}


			// Create an option for each field:
		$opt = array();
		$opt[] = '<option value=""></option>';
		foreach ($allFields as $fN) {
				// Field label
			$fL = is_array($TCA[$table]['columns'][$fN]) ? preg_replace('/:$/', '', $LANG->sL($TCA[$table]['columns'][$fN]['label'])) : '['.$fN.']';
			$opt[] = '
														<option value="'.$fN.'"'. (in_array($fN, $selectedFields) ? ' selected="selected"' : '').'>'.htmlspecialchars($fL).'</option>';
		}

			// Compile the options into a multiple selector box:
		$lMenu = '
					<select size="'.t3lib_div::intInRange(count($allFields) + 1, 3, 8).'" multiple="multiple" name="tx_dam_list_list_displayFields[]">'.implode('', $opt).'
												</select>
						';

			// Table with the select box:
		$content .= '
				'.$formElements[0].'
						<!--
							Field selector for extended table view:
						-->
						<table border="0" cellpadding="0" cellspacing="0" class="bgColor4" id="typo3-dblist-fieldSelect">
							<tr>
								<td>'.$lMenu.'</td>
								<td><input type="Submit" name="search" value="&gt;&gt;"></td>
							</tr>
							</table>
					'.$formElements[1].'
				';
		return $content;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_editsel/class.tx_dam_list_editsel.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_editsel/class.tx_dam_list_editsel.php']);
}

?>