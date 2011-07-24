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
 *   73: class tx_dam_batchProcess
 *  105:     function processGP()
 *  135:     function runBatch($res)
 *  152:     function getProcessFieldList()
 *
 *              SECTION: GUI misc
 *  174:     function showPresetForm()
 *  208:     function showResult()
 *  228:     function getPresetForm ($rec, $fixedFields, $description)
 *  289:     function getResultTable()
 *
 *              SECTION: this and that
 *  356:     function getFormSetup()
 *  382:     function extractFormData($data, $appendFieldsArr)
 *  417:     function saveFormSetup()
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




$GLOBALS['LANG']->includeLLFile('EXT:dam/lib/locallang.xml');

require_once(PATH_txdam.'lib/class.tx_dam_db.php');



/**
 * Batch processing of DAM records
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
class tx_dam_batchProcess {

	/**
	 * Parameter name of the submitted data
	 */
	var $startParam = 'process';

	/**
	 * Data which should replace the current record data
	 */
	var $replaceData = array();


	/**
	 * Data which should be appended the current record data
	 */
	var $appendData = array();


	/**
	 * Array of the record data which has changed
	 */
	var $updated = array();




	/**
	 * Processes submitted GP data from the preset form
	 *
	 * @return	boolean		TRUE if data was submitted
	 */
	function processGP() {
		global  $BE_USER, $LANG, $BACK_PATH, $TCA;


		$this->appendData = array();
		$this->replaceData = array();

		if(t3lib_div::_GP('tx_dam_batchProcess_clearForm')) {
			$this->saveFormSetup();
			return false;
		}

		if(t3lib_div::_GP($this->startParam)) {
			$data = t3lib_div::_POST('data');
			$data_fixedFields = t3lib_div::_POST('data_fixedFields');
			$formSubmitted = $this->extractFormData($data, $data_fixedFields);
			$this->saveFormSetup();
			return $formSubmitted;
		}

		return false;
	}


	/**
	 * Run the batch
	 *
	 * @param	mixed		$res A valid db query result
	 * @return	void
	 */
	function runBatch($res) {

		if (!$res) { return FALSE; }

		$this->updated = array();

		while($rowRaw = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$this->updated[$rowRaw['uid']] = tx_dam_db::mergeAndUpdateData($rowRaw, $this->replaceData, $this->appendData);
		}
	}


	/**
	 * Return field names to be processed as array
	 *
	 * @return array Field names
	 */
	function getProcessFieldList() {
		$fields = array_keys(array_merge($this->replaceData, $this->appendData));
		$fields = tx_dam_db::getMetaInfoFieldList(TRUE, $fields);
		return $fields;
	}





	/********************************
	 *
	 * GUI misc
	 *
	 ********************************/


	/**
	 * Show the gui: preset form
	 *
	 * @return	string		HTML
	 */
	function showPresetForm() {
		global  $LANG;

		$content = '';
		$content.= $GLOBALS['SOBE']->doc->section('',$LANG->getLL('tx_dam_batchProcess.introduction',1),0,1);
		$content.= $GLOBALS['SOBE']->doc->spacer(5);


		$this->getFormSetup();
		$rec = array_merge($this->replaceData, $this->appendData);
		$fixedFields = array_keys($this->appendData);

		$code = $this->getPresetForm($rec, $fixedFields, $LANG->getLL('tx_dam_batchProcess.appendDesc',0)); // don't hsc because of <b> tags

		$cnBgColor = t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor3,-5,-5,-5);
		$content.= $GLOBALS['SOBE']->doc->section('','<table border="0" cellpadding="4" width="100%"><tr><td bgcolor="'.$cnBgColor.'">'.
						$code.
						'</td></tr></table>',0,1);

		$content.= '<br /><div class="batchProcess">
				<input type="submit" name="tx_dam_batchProcess_clearForm" value="'.$LANG->getLL('submitClearForm',1).'" />
				<input type="submit" name="'.$this->startParam.'" value="'.$LANG->getLL('submitProcess',1).'" />
				</div><br />';


		return $content;
	}


	/**
	 * Show the gui: result table
	 *
	 * @return	string		HTML
	 */
	function showResult() {
		global  $LANG;

		$content = '';

		$content.= $GLOBALS['SOBE']->doc->section('',$LANG->getLL('tx_dam_batchProcess.processed',1),0,1);
		$content.= $this->getResultTable();

		return $content;
	}


	/**
	 * Render a form with TCEForms to edit/enter the preset data
	 *
	 * @param	array		$rec Data which should be edited in the form
	 * @param	array		$fixedFields Which fields are set to fixed
	 * @param	string		$description Description given at the top as HTML
	 * @return	string		HTML
	 */
	function getPresetForm ($rec, $fixedFields, $description) {
		global  $BE_USER, $LANG, $BACK_PATH, $TCA, $TYPO3_CONF_VARS;

		$content = '';

		if(!is_array($rec)) $rec = array();
		if(!is_array($fixedFields)) $fixedFields = array();
		$rec['uid'] = 1;
		$rec['pid'] = 1;
		$rec['media_type'] = 0;
		$rec = tx_dam_db::evalData('tx_dam', $rec);


		require_once (PATH_txdam.'lib/class.tx_dam_simpleforms.php');
		$form = t3lib_div::makeInstance('tx_dam_simpleForms');
		$form->initDefaultBEmode();
		$form->setVirtualTable('tx_dam_simpleforms', 'tx_dam');
		$form->removeRequired();
		$form->tx_dam_fixedFields = $fixedFields;

			// add message for checkboxes
		$content.= '<tr bgcolor="'.$GLOBALS['SOBE']->doc->bgColor4.'">
				<td nowrap="nowrap" valign="middle">'.
				'<span class="presetForm">'.
				'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pil2down.gif', 'width="12" height="7"').' vspace="2" alt="" />'.
				'</span></td>
				<td valign="top">'.$description.'</td>
			</tr>
			<tr>
				<td colspan="2" style="height:8px"><span></span></td>
			</tr>';


		$columnsOnly = $TCA['tx_dam']['txdamInterface']['index_fieldList'];

		if ($columnsOnly)	{
			$content.= $form->getFormFromList($rec, $columnsOnly);
		} else {
			$content.= $form->getForm($rec);
		}

		$content = $form->wrapTotal($content, $rec);

		$GLOBALS['SOBE']->doc->JScode .='
		'.$form->printNeededJSFunctions_top();
		$content.= $form->printNeededJSFunctions();

		$form->removeVirtualTable('tx_dam_simpleforms');

		return $content;
	}




	/**
	 * Render the table with result records
	 *
	 * @return	string		Rendered Table
	 */
	function getResultTable()   {
		global $BACK_PATH, $BE_USER, $LANG;

		if (!count($this->updated)) { return ; }

			// init table layout
		$refTableLayout = array(
			'table' => array('<table border="0" cellpadding="1" cellspacing="1" class="typo3-recent-edited">', '</table>'),
			'0' => array(
				'tr' => array('<tr class="bgColor5">','</tr>'),
				'defCol' => Array('<th>','</th>')
			),
			'defRow' => array(
				'tr' => array('<tr class="bgColor4">','</tr>'),
				'defCol' => Array('<td valign="top">','</td>')
			)
		);

		$cTable=array();
		$tr=0;

			// Add header row to table
		$td=0;
		$cTable[$tr][$td++] = '&nbsp';
		$cTable[$tr][$td++] = $LANG->sL('LLL:EXT:lang/locallang_general.xml:LGL.title',1);
		$cTable[$tr][$td++] = $LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_type',1);
		$cTable[$tr][$td++] = $LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name',1);
		$cTable[$tr][$td++] = $LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_path',1);
		$tr++;


		foreach ($this->updated as $uid => $data) {
			$row = $data['processed'];

				// Add row to table
			$td=0;
			$cTable[$tr][$td++] = $GLOBALS['SOBE']->btn_editRec_inNewWindow('tx_dam', $row['uid']);
			$cTable[$tr][$td++] = htmlspecialchars(t3lib_div::fixed_lgd_cs($row['title'],23));
			$cTable[$tr][$td++] = htmlspecialchars(strtoupper($row['file_type']));
			$cTable[$tr][$td++] = '<span style="white-space:nowrap;">'.tx_dam::icon_getFileTypeImgTag($row,'align="top"').'&nbsp;'.htmlspecialchars(t3lib_div::fixed_lgd_cs($row['file_name'],23)).'</span>';
			$cTable[$tr][$td++] = htmlspecialchars(t3lib_div::fixed_lgd_cs($row['file_path'],-20));



			$tr++;
		}

			// Return rendered table
		return $GLOBALS['SOBE']->doc->table($cTable, $refTableLayout);
	}




	/***************************************
	 *
	 *	 this and that
	 *
	 ***************************************/

	/**
	 * Processes the submitted data for the form setup
	 *
	 * @param array $data Form data values
	 * @param array $appendFieldsArr Form data values of which fields are marked
	 * @return	void
	 */
	function getFormSetup()	{
		global  $BE_USER, $LANG, $BACK_PATH;

		$GLOBALS['SOBE']->MOD_SETTINGS = t3lib_BEfunc::getModuleData($GLOBALS['SOBE']->MOD_MENU, '', $GLOBALS['SOBE']->MCONF['name'], $GLOBALS['SOBE']->modMenu_type, $GLOBALS['SOBE']->modMenu_dontValidateList, $GLOBALS['SOBE']->modMenu_setDefaultList);

			// get stored indexing setup from last page view or last session
		$storedSetup = unserialize($GLOBALS['SOBE']->MOD_SETTINGS['tx_dambatchprocess_setup']);
		if(is_array($storedSetup['replaceData'])) {
			$this->replaceData = t3lib_div::array_merge_recursive_overrule($this->replaceData, $storedSetup['replaceData']);
		}
		if(is_array($storedSetup['appendData'])) {
			$this->appendData = t3lib_div::array_merge_recursive_overrule($this->appendData, $storedSetup['appendData']);
		}

	}


	/**
	 * Processes submitted GP data from the preset form and store the data in
	 * $this->appendData = array();
	 * $this->replaceData = array();
	 *
	 * @param array $data Form data values
	 * @param array $appendFieldsArr Form data values of which fields are marked
	 * @return boolean True if something was submitted
	 */
	function extractFormData($data, $appendFieldsArr) {
		if (is_array($data['tx_dam_simpleforms'][1])) {

				// get which fields are append
			$appendFields=array();
			if (is_array($appendFieldsArr['tx_dam_simpleforms'][1])) {
				foreach($appendFieldsArr['tx_dam_simpleforms'][1] as $field => $isAppend) {
					if($isAppend) $appendFields[] = $field;
				}
			}

				// split data to preset and append
			foreach($data['tx_dam_simpleforms'][1] as $field => $value) {
				if (trim($value)) {
					if (in_array($field, $appendFields)) {
						$this->appendData[$field] = $value;
						unset($this->replaceData[$field]);
					} else {
						$this->replaceData[$field] = $value;
						unset($this->appendData[$field]);
					}
				}
			}
			return TRUE;
		}
		return FALSE;
	}



	/**
	 * Save preset in module settings
	 *
	 * @return	void
	 */
	function saveFormSetup() {
		$setup = array(
			'replaceData' => $this->replaceData,
			'appendData' => $this->appendData,
			);

		$newSettings = array(
			'tx_dambatchprocess_setup' => serialize($setup),
		);
		$GLOBALS['SOBE']->MOD_SETTINGS = t3lib_BEfunc::getModuleData($GLOBALS['SOBE']->MOD_MENU, $newSettings, $GLOBALS['SOBE']->MCONF['name'], $GLOBALS['SOBE']->modMenu_type, $GLOBALS['SOBE']->modMenu_dontValidateList, $GLOBALS['SOBE']->modMenu_setDefaultList);
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_batchprocess.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_batchprocess.php']);
}


?>