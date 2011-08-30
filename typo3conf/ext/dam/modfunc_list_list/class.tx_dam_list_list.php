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
 * Module extension (addition to function menu) 'list' for the 'Media>List' module.
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
 *   71: class tx_dam_list_list extends t3lib_extobjbase
 *   78:     function modMenu()
 *   97:     function head()
 *  125:     function main()
 *
 *              SECTION: selector for fields to display
 *  406:     function fieldSelectBox($table, $allFields, $selectedFields, $formFields = true)
 *
 *              SECTION: Localization stuff
 *  475:     function languageSwitch($pid, $currentLanguage, $formFields = true)
 *  509:     function getLanguages($id)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

require_once(PATH_txdam.'lib/class.tx_dam_listrecords.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db_lang_ovl.php');

$LANG->includeLLFile('EXT:lang/locallang_mod_web_list.xml');

/**
 * Module extension  'Media>List>List'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
class tx_dam_list_list extends t3lib_extobjbase {

	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

		return array(
			'tx_dam_list_list_showThumb' => '',
			'tx_dam_list_list_showMultiActions' => '',
			'tx_dam_list_list_sortField' => '',
			'tx_dam_list_list_sortRev' => '',
			'tx_dam_list_list_displayFields' => '',
			'tx_dam_list_list_onlyDeselected' => '',
		);
	}

	/**
	 * Initialize the class and set some HTML header code
	 *
	 * @return	void
	 */
	function head()	{
		global $LANG;

		//
		// Init gui items and ...
		//

		$this->pObj->guiItems->registerFunc('getResultInfoBar', 'header');
#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'header');

#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'footer');
		$this->pObj->guiItems->registerFunc('getCurrentSelectionBox', 'footer');
		$this->pObj->guiItems->registerFunc('getSearchBox', 'footer', array('simple', true));
		$this->pObj->guiItems->registerFunc('getOptions', 'footer');
		$this->pObj->guiItems->registerFunc('getStoreControl', 'footer');

			// add some options
		$this->pObj->addOption('funcCheck', 'tx_dam_list_list_showThumb', $LANG->getLL('showThumbnails'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_list_onlyDeselected', $LANG->getLL('tx_dam_list_list.onlyDeselected'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_list_showMultiActions', $LANG->getLL('showMultiAction'));
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
		if($this->pObj->MOD_SETTINGS['tx_dam_list_list_onlyDeselected']) {
			if(is_array($this->pObj->selection->sl->sel['NOT']['txdamRecords'])) {
				$excludeUidList = array_keys($this->pObj->selection->sl->sel['NOT']['txdamRecords']);
			} else {
				$excludeUidList = array(0); //dummy
			}

			unset($this->pObj->selection->sl->sel['NOT']['txdamRecords']);
			$this->pObj->selection->addSelectionToQuery();
			if(is_array($excludeUidList)) {
				$this->pObj->selection->qg->query['WHERE']['WHERE']['NOT_txdamRecords'] = 'AND tx_dam.uid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList(implode(',',$excludeUidList)).')';
			}

		} else {
			// will display deselected unset($this->pObj->selection->sl->sel['NOT']['txdamRecords']);
			$this->pObj->selection->addSelectionToQuery();
		}
		$this->pObj->selection->sl->sel = $origSel;





		//
		// set language query
		//


		$langRows = $this->pObj->getLanguages($this->pObj->defaultPid);
		$langCurrent = intval($this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']);
		if (!isset($langRows[$langCurrent])) {
			$langCurrent = $this->pObj->MOD_SETTINGS['tx_dam_list_langSelector'] = key($langRows);
		}


		$langQuery = '';
		$languageField = $TCA[$table]['ctrl']['languageField'];
		if ($langCurrent && $this->pObj->MOD_SETTINGS['tx_dam_list_langOverlay'] === 'exclusive') {
		// if ($langCurrent) { This works but create NULL columns for non-translated records so we need to use language overlay anyway

			$lgOvlFields = tx_dam_db::getLanguageOverlayFields ($table, 'tx_dam_lgovl');

			$languageField = $TCA[$table]['ctrl']['languageField'];
			$transOrigPointerField = $TCA[$table]['ctrl']['transOrigPointerField'];

			$this->pObj->selection->setSelectionLanguage($langCurrent);
			
			$this->pObj->selection->qg->query['SELECT']['tx_dam as tx_dam_lgovl'] = implode(', ', $lgOvlFields).', tx_dam.uid as _dlg_uid, tx_dam.title as _dlg_title';
			$this->pObj->selection->qg->query['LEFT_JOIN']['tx_dam as tx_dam_lgovl'] = 'tx_dam.uid=tx_dam_lgovl.'.$transOrigPointerField;
			
			if ($this->pObj->MOD_SETTINGS['tx_dam_list_langOverlay']==='exclusive') {
				$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.'.$languageField] = 'AND tx_dam_lgovl.'.$languageField.'='.$langCurrent;
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.deleted'] = 'AND tx_dam_lgovl.deleted=0';
			} else {
				$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.'.$languageField] = 'AND (tx_dam_lgovl.'.$languageField.' IN ('.$langCurrent.', 0, -1) )';
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.deleted'] = 'AND (tx_dam_lgovl.sys_language_uid=1 OR tx_dam.sys_language_uid=0 )';
			}

		} else {
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam.'.$languageField] = 'AND tx_dam.'.$languageField.' IN (0,-1)';
		}



		//
		// Add the current selection to the query
		//

#		$this->pObj->selection->addSelectionToQuery();


		//
		// Use the current selection to create a query and count selected records
		//

		$this->pObj->selection->execSelectionQuery(TRUE);


		//
		// output header: info bar, result browser, ....
		//

		$content.= $this->pObj->guiItems->getOutput('header');
		$content.= $this->pObj->doc->spacer(10);

			// any records found?
		if($this->pObj->selection->pointer->countTotal) {



			//
			// init db list object
			//

			$dblist = t3lib_div::makeInstance('tx_dam_listrecords');
			$dblist->setParameterName('form', $this->pObj->formName);
			$dblist->init($table);
			
			$dblist->setActionsEnv(array(
					'currentLanguage' => $langCurrent,
					'allowedLanguages' => $langRows,
				));


			//
			// process multi action if needed
			//

			if ($processAction = $dblist->getMultiActionCommand()) {

				if ($processAction['onItems'] === '_all') {

					$uidList = array();
					$res = $this->pObj->selection->execSelectionQuery();

					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						$uidList[] = $row['uid'];
					}
					$itemList = implode(',', $uidList);
				} else {
					$itemList = $processAction['onItems'];
					$uidList = t3lib_div::trimExplode(',', $itemList, true);
				}

				if ($uidList) {
					switch ($processAction['actionType']) {
						case 'url':
							$url = str_replace('###ITEMLIST###', $itemList, $processAction['action']);
							header('Location: '.$url);
							exit;
						break;
						case 'tce-data':
							$params = '';
							foreach ($uidList as $uid) {
								$params .= str_replace('###UID###', $uid, $processAction['action']);
							}
							$url = $GLOBALS['SOBE']->doc->issueCommand($params, -1);


							$url = $BACK_PATH.'tce_db.php?&redirect='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI')).'&vC='.$BE_USER->veriCode().'&prErr=1&uPT=1'.$params;

							header('Location: '.$url);
							exit;
						break;
					}
				}
			}



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
			if ($langCurrent>0 AND $this->pObj->MOD_SETTINGS['tx_dam_list_langOverlay']!=='exclusive') {
				$dbIterator = new tx_dam_iterator_db_lang_ovl($res, $conf);
				$dbIterator->initLanguageOverlay($table, $this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']);
			} else {
				$dbIterator = new tx_dam_iterator_db($res, $conf);
			}


			//
			// make db list
			//

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
				// enable display of multi actions
			$dblist->showMultiActions = $this->pObj->MOD_SETTINGS['tx_dam_list_list_showMultiActions'];
				// enable context menus
			$dblist->enableContextMenus = $this->pObj->config_checkValueEnabled('contextMenuOnListItems', true);
				// Enable/disable display of thumbnails
			$dblist->showThumbs = $this->pObj->MOD_SETTINGS['tx_dam_list_list_showThumb'];
				// Enable/disable display of AlternateBgColors
			$dblist->showAlternateBgColors = $this->pObj->config_checkValueEnabled('alternateBgColors', true);



			$dblist->setPointer($this->pObj->selection->pointer);
			$dblist->setCurrentSorting($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'], $this->pObj->MOD_SETTINGS['tx_dam_list_list_sortRev']);
			$dblist->setParameterName('sortField', 'SET[tx_dam_list_list_sortField]');
			$dblist->setParameterName('sortRev', 'SET[tx_dam_list_list_sortRev]');
			$this->pObj->doc->JScodeArray['dblist-JsCode'] = $dblist->getJsCode();



// todo Clipboard			
				// It is set, if the clickmenu-layer is active AND the extended view is not enabled.
#			$dblist->dontShowClipControlPanels = $CLIENT['FORMSTYLE'] && !$BE_USER->uc['disableCMlayers'];




			#$content.= '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post" name="'.$dblist->paramName['form'].'">';
			// get all avalibale languages for the page
			if ($languageSwitch = $this->pObj->languageSwitch($langRows, intval($this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']))) {
				$this->pObj->markers['LANGUAGE_SELECT'] = '<div class="languageSwitch">'.$languageSwitch.'</div>';
			}
			else {
				$this->pObj->markers['LANGUAGE_SELECT'] = '';
			}

			// wraps the list table with the form
			$content .= tx_dam_SCbase::getFormTag();
			$content .= $dblist->getListTable();
			$content .= '</form>';

			$fieldSelectBoxContent = $this->fieldSelectBox($table, $allFields, $selectedFields);
			$content.= $this->pObj->buttonToggleDisplay('fieldselector', $LANG->getLL('field_selector'), $fieldSelectBoxContent);

		} else {
				// no search result: showing selection box

			if ($languageSwitch = $this->pObj->languageSwitch($langRows, intval($this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']))) {
				$this->pObj->markers['LANGUAGE_SELECT'] = '<div class="languageSwitch">'.$languageSwitch.'</div>';
			}
			else {
				$this->pObj->markers['LANGUAGE_SELECT'] = '';
			}			
			// is in footer now $content .= $this->pObj->getCurrentSelectionBox();
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
			$fL = is_array($TCA[$table]['columns'][$fN]) ? preg_replace('#:$#', '', $LANG->sL($TCA[$table]['columns'][$fN]['label'])) : '['.$fN.']';
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
								<td><input type="submit" name="search" value="&gt;&gt;"></td>
							</tr>
						</table>
					'.$formElements[1].'
				';
		return $content;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_list/class.tx_dam_list_list.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_list/class.tx_dam_list_list.php']);
}

?>