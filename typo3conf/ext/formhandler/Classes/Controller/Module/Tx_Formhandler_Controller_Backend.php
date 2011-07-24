<?php
/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

require_once(t3lib_extMgm::extPath('formhandler') . 'Classes/Controller/Module/class.tx_formhandler_mod1_pagination.php');

/**
 * Controller for Backend Module of Formhandler
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	Controller
 */
class Tx_Formhandler_Controller_Backend extends Tx_Formhandler_AbstractController {


	/**
	 * The GimmeFive component manager
	 *
	 * @access protected
	 * @var Tx_GimmeFive_Component_Manager
	 */
	protected $componentManager;

	/**
	 * The global Formhandler configuration
	 *
	 * @access protected
	 * @var Tx_Formhandler_Configuration
	 */
	protected $configuration;

	/**
	 * The table to select the logged records from
	 *
	 * @access protected
	 * @var string
	 */
	protected $logTable;

	/**
	 * The absolute path to the template folder
	 *
	 * @access protected
	 * @var string
	 */
	protected $templatePath;

	/**
	 * The template file name
	 *
	 * @access protected
	 * @var string
	 */
	protected $templateFile;

	/**
	 * The contents of the template file
	 *
	 * @access protected
	 * @var string
	 */
	protected $templateCode;
	
	protected $id;

	/**
	 * The constructor for a finisher setting the component manager and the configuration.
	 *
	 * @param Tx_GimmeFive_Component_Manager $componentManager
	 * @param Tx_Formhandler_Configuration $configuration
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @return void
	 */
	public function __construct(Tx_GimmeFive_Component_Manager $componentManager, Tx_Formhandler_Configuration $configuration) {
		$this->componentManager = $componentManager;
		$this->configuration = $configuration;
		$this->templatePath = t3lib_extMgm::extPath('formhandler') . 'Resources/HTML/backend/';
		$this->templateFile = $this->templatePath . 'template.html';
		$this->templateCode = t3lib_div::getURL($this->templateFile);

	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}

	/**
	 * init method to load translation data and set log table.
	 *
	 * @global $LANG
	 * @return void
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function init() {
		global $LANG;
		$LANG->includeLLFile('EXT:formhandler/Resources/Language/locallang.xml');
		$this->logTable = 'tx_formhandler_log';
		$this->pageBrowser = new tx_formhandler_mod1_pagination($this->countRecords(), $this);
	}

	/**
	 * Main method of the controller.
	 *
	 * @return string rendered view
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	public function process() {
		global $LANG;

		//init
		$this->init();

		//init gp params
		$params = t3lib_div::_GP('formhandler');
		

		//should delete records
		if($params['delete'] && isset($params['markedUids']) && is_array($params['markedUids'])) {

			//delete records
			$this->deleteRecords($params['markedUids']);
			
			

			//select all records
			$records = $this->fetchRecords();

			//show table
			$table = $this->getTable($records);

			return $table;
		}

		//should show index
		if(!$params['detailId'] && !$params['markedUids'] && !$params['csvFormat']) {

			//if log table doesn't exist, show error
			$tables = $GLOBALS['TYPO3_DB']->admin_get_tables();
			if(!in_array($this->logTable, array_keys($tables))) {
				return $this->getErrorMessage();

				//show index table
			} else {
				
				$this->pageBrowser = new tx_formhandler_mod1_pagination($this->countRecords(), $this);

				//select all records
				$records = $this->fetchRecords();

				//show table
				$table = $this->getTable($records);
				return $table;
			}

			//should export to some format
		} elseif(!$params['delete']) {

			//should show detail view of a single record
			if(!$params['renderMethod']) {

				return $this->showSingleView($params['detailId']);

				//PDF generation
			} elseif(!strcasecmp($params['renderMethod'], 'pdf')) {

				//render a single record to PDF
				if($params['detailId']) {
					return $this->generatePDF($params['detailId']);

					//render many records to PDF
				} elseif(isset($params['markedUids']) && is_array($params['markedUids'])) {
					return $this->generatePDF($params['markedUids']);
				}

				//CSV
			} elseif(!strcasecmp($params['renderMethod'], 'csv')) {

				//save single record as CSV
				if($params['detailId']) {
					return $this->generateCSV($params['detailId']);
					
					//save many records as CSV
				} elseif(isset($params['markedUids']) && is_array($params['markedUids'])) {
					return $this->generateCSV($params['markedUids']);
				} else {
					return $this->generateCSV(FALSE);
				}

			}
		}
	}

	/**
	 * Function to delete one ore more records from log table
	 *
	 * @param array $uids The record uids to delete
	 * @return void
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function deleteRecords($uids) {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery($this->logTable, ('uid IN (' . $GLOBALS['TYPO3_DB']->cleanIntList(implode(',', $uids)) . ')'));
	}

	/**
	 * Function to handle the generation of a PDF file.
	 * Before the data gets exported, the user is able to select which fields to export in a selection view.
	 * This enables the user to get rid of fields like submitted or mp-step.
	 *
	 * @param misc $detailId The record uids to export to pdf
	 * @return void/string selection view
	 * @author Reinhard Führicht
	 */
	protected function generatePDF($detailId) {

		/*
		 * if there is only one record to export, initialize an array with the one uid
		 * to ensure that foreach loops will not crash
		 */
		if(!is_array($detailId)) {
			$detailId = array($detailId);
		}

		//init gp params
		$gp = t3lib_div::_GP('formhandler');
		
		//select the records
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,pid,crdate,ip,params', $this->logTable, ('uid IN (' . implode(',', $detailId) . ')'));

		//if records were found
		if($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$records = array();
			$allParams = array();

			//loop through records
			while(FALSE !== ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {

				//unserialize params and save the array
				$row['params'] = unserialize($row['params']);
				$records[] = $row;
				if(!is_array($row['params'])) {
					$row['params'] = array();
				}

				//sum up all params for selection view
				$allParams = array_merge($allParams, $row['params']);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			$tsconfig = t3lib_BEfunc::getModTSconfig($this->id,'tx_formhandler_mod1'); 
			$configParams = array();
			
			// check if TSconfig filter is set
			if (strlen($tsconfig['properties']['config.']['csv']) > 0) {
				$configParams = t3lib_div::trimExplode(',', $tsconfig['properties']['config.']['pdf'], 1);
				$generator = $this->componentManager->getComponent('Tx_Formhandler_Generator_TCPDF');
				$generator->generateModulePDF($records, $configParams);	
			} elseif(isset($gp['exportParams'])) {
				
				//if fields were chosen in selection view, export the records using the selected fields
				
				$generator = $this->componentManager->getComponent('Tx_Formhandler_Generator_TCPDF');
				$generator->generateModulePDF($records, $gp['exportParams']);
				
				/*
				 * show selection view to find out which fields to export.
				 * This enables the user to get rid of fields like submitted or mp-step
				 */
			} else {
				
				return $this->generatePDFExportFieldsSelector($allParams);
			}
		}
	}

	/**
	 * Function to handle the generation of a CSV file.
	 * Before the data gets exported, the data is checked and the user gets informed about different formats of the data.
	 * Each format has to be exported in an own file. After the format selection, the user is able to select which fields to export in a selection view.
	 * This enables the user to get rid of fields like submitted or mp-step.
	 *
	 * @param misc $detailId The record uids to export to csv
	 * @return void/string selection view
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function generateCSV($detailId) {
		$where = '';
		
		if(!$detailId) {
			$where = '1=1';
		} elseif(!is_array($detailId)) {
			$where = 'uid=' . $detailId;
		} else {
			$where = 'uid IN (' . implode(',', $detailId) . ')';
		}

		//init gp params
		$params = t3lib_div::_GP('formhandler');

		//select the records to export
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,pid,crdate,ip,params,key_hash', $this->logTable, $where);

		//if record were found
		if($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$records = array();
			$count = 0;
			$hashes = array();
			$availableFormats = array();

			//loop through records
			while(FALSE !== ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {

				//unserialize the params array
				$row['params'] = unserialize($row['params']);

				//find the amount of different formats to inform the user.
				if(!in_array($row['key_hash'], $hashes)) {
					$hashes[] = $row['key_hash'];
					$availableFormats[] = $row['params'];
				}
				$records[] = $row;
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			$availableFormatsCount = count($availableFormats);
		
			//only one format found
			if($availableFormatsCount === 1) {
				$tsconfig = t3lib_BEfunc::getModTSconfig($this->id,'tx_formhandler_mod1'); 
				$configParams = array();
				// check if TSconfig filter is set
				if ($tsconfig['properties']['config.']['csv'] != "") {
					$configParams = t3lib_div::trimExplode(',', $tsconfig['properties']['config.']['csv'], 1);
					$generator = $this->componentManager->getComponent('Tx_Formhandler_Generator_CSV');
					$generator->generateModuleCSV($records, $configParams);	
				} elseif(isset($params['exportParams'])) {
					
					//if fields were chosen in the selection view, perform the export
					
					$generator = $this->componentManager->getComponent('Tx_Formhandler_Generator_CSV');
					$generator->generateModuleCSV($records, $params['exportParams']);

					//no fields chosen, show selection view.
				} else {
					return $this->generateCSVExportFieldsSelector($records[0]['params']);
				}

				//more than one format and user has chosen a format to export
			} elseif(isset($params['csvFormat'])) {
				$renderRecords = array();
				if($params['csvFormat'] === '*') {
					$renderRecords = $records;
				} else {

					//select the format
					$format = $hashes[$params['csvFormat']];
					$renderRecords = array();
	
					//find out which records belong to this format
					foreach($records as $record) {
						if(!strcmp($record['key_hash'], $format)) {
							$renderRecords[] = $record;
						}
					}
				}
				
				$tsconfig = t3lib_BEfunc::getModTSconfig($this->id,'tx_formhandler_mod1'); 
				$configParams = array();

				// check if TSconfig filter is set
				if ($tsconfig['properties']['config.']['csv'] != "") {
					$configParams = t3lib_div::trimExplode(',', $tsconfig['properties']['config.']['csv'], 1);
					$generator = $this->componentManager->getComponent('Tx_Formhandler_Generator_CSV');
					$generator->generateModuleCSV($renderRecords, $configParams);	
				} elseif(isset($params['exportParams'])) {
					
					//if fields were chosen in the selection view, perform the export
					
					$generator = $this->componentManager->getComponent('Tx_Formhandler_Generator_CSV');
					$generator->generateModuleCSV($renderRecords, $params['exportParams']);

					//no fields chosen, show selection view.
				} else {
					$fields = $renderRecords[0]['params'];
					if($params['csvFormat'] === '*') {
						$exportParams = array();
						foreach($renderRecords as $record) {
							foreach($record['params'] as $key=>$value) {
								if(!array_key_exists($key, $exportParams)) {
									$exportParams[$key] = $value;
								}
							}
						}
						$fields = $exportParams;
					}
					return $this->generateCSVExportFieldsSelector($fields);
				}

				//more than one format and none chosen by now, show format selection view.
			} else {
				return $this->generateFormatsSelector($availableFormats, $detailId);
			}
		}
	}

	/**
	 * This function returns a list of all available fields to export for CSV export.
	 * The user can choose several fields and start the export.
	 *
	 * @param array $params The available fields to export.
	 * @return string fields selection view
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function generateCSVExportFieldsSelector($params) {
		global $LANG;

		//if there are no params, initialize the array to ensure that foreach loops will not crash
		if(!is_array($params)) {
			$params = array();
		}

		//init gp params
		$gp = t3lib_div::_GP('formhandler');
		$selectorCode = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###EXPORT_FIELDS_SELECTOR###');
		
		$markers = array();
		$markers['###LLL:select_export_fields###'] = $LANG->getLL('select_export_fields');
		$markers['###SELECTION###'] = $this->getSelectionBox();
		$markers['###URL###'] = $_SERVER['PHP_SELF'];

		//the selected format to export
		$markers['###CSV_FORMAT###'] = $gp['csvFormat'];
		$markers['###RENDER_METHOD###'] = $gp['renderMethod'];

		/*
		 * if there is only one record to export, initialize an array with the one uid
		 * to ensure that foreach loops will not crash.
		 * UIDs could be in param "markedUids" if more records where selected or in "detailId" if only one record get exported.
		 */
		$detailId = $gp['markedUids'];
		if(!$detailId) {
			$detailId = $gp['detailId'];
		}
		if(!is_array($detailId)) {
			$detailId = array($detailId);
		}

		$markers['###SELECTED_RECORDS###'] = '';
		
		//the selected records in a previous step
		foreach($detailId as $id) {
			$markers['###SELECTED_RECORDS###'] .= '<input type="hidden" name="formhandler[markedUids][]" value="' . $id . '" />';
		}

		$markers['###EXPORTFIELDS###'] = '';
		
		//add a label and a checkbox for each available parameter
		foreach($params as $field=>$value) {
			$markers['###EXPORTFIELDS###'] .= '<tr><td><input type="checkbox" name="formhandler[exportParams][]" value="' . $field . '">' . $field . '</td></tr>';
		}
		$markers['###UID###'] = $this->id;
		$markers['###LLL:export###'] = $LANG->getLL('export');
		$returnCode = $this->getSelectionJS();
		$returnCode .= Tx_Formhandler_StaticFuncs::substituteMarkerArray($selectorCode, $markers);
		
		return $returnCode; 
	}

	/**
	 * This function returns a list of all available fields to export for PDF export.
	 * The user can choose several fields and start the export.
	 *
	 * @param array $params The available fields to export.
	 * @return string fields selection view
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function generatePDFExportFieldsSelector($params) {
		global $LANG;

		//if there are no params, initialize the array to ensure that foreach loops will not crash
		if(!is_array($params)) {
			$params = array();
		}

		//init gp params
		$gp = t3lib_div::_GP('formhandler');
		$selectorCode = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###EXPORT_FIELDS_SELECTOR###');
		
		$markers = array();
		$markers['###LLL:select_export_fields###'] = $LANG->getLL('select_export_fields');
		$markers['###SELECTION###'] = $this->getSelectionBox();
		$markers['###URL###'] = $_SERVER['PHP_SELF'];

		//the selected format to export
		$markers['###CSV_FORMAT###'] = $gp['csvFormat'];
		$markers['###RENDER_METHOD###'] = $gp['renderMethod'];

		/*
		 * if there is only one record to export, initialize an array with the one uid
		 * to ensure that foreach loops will not crash.
		 * UIDs could be in param "markedUids" if more records where selected or in "detailId" if only one record get exported.
		 */
		$detailId = $gp['markedUids'];
		if(!$detailId) {
			$detailId = $gp['detailId'];
		}
		if(!is_array($detailId)) {
			$detailId = array($detailId);
		}

		$markers['###SELECTED_RECORDS###'] = '';
		
		//the selected records in a previous step
		foreach($detailId as $id) {
			$markers['###SELECTED_RECORDS###'] .= '<input type="hidden" name="formhandler[markedUids][]" value="' . $id . '" />';
		}

		$markers['###EXPORTFIELDS###'] = '';
		$markers['###EXPORTFIELDS###'] .= '<tr><td><input type="checkbox" name="formhandler[exportParams][]" value="ip" />' . $LANG->getLL('ip_address') . '</td></tr>';
		$markers['###EXPORTFIELDS###'] .= '<tr><td><input type="checkbox" name="formhandler[exportParams][]" value="submission_date" />' . $LANG->getLL('submission_date') .  '</td></tr>';
		$markers['###EXPORTFIELDS###'] .= '<tr><td><input type="checkbox" name="formhandler[exportParams][]" value="pid" />' . $LANG->getLL('page_id') . '</td></tr>';
		
		//add a label and a checkbox for each available parameter
		foreach($params as $field => $value) {
			$markers['###EXPORTFIELDS###'] .= '<tr><td><input type="checkbox" name="formhandler[exportParams][]" value="' . $field . '">' . $field . '</td></tr>';
		}
		$markers['###UID###'] = $this->id;
		$markers['###LLL:export###'] = $LANG->getLL('export');
		$returnCode = $this->getSelectionJS();
		$returnCode .= Tx_Formhandler_StaticFuncs::substituteMarkerArray($selectorCode, $markers);
		
		return $returnCode; 
	}

	/**
	 * This function returns JavaScript code to select/deselect all checkboxes in a form
	 *
	 * @return string JavaScript code
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function getSelectionJS() {
		global $LANG;
		$content = "";		
		$code = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###JS_CODE###');	
		$markers = array();
		$markers['###HOW_MUCH_JS###'] = ($this->pageBrowser) ? intval($this->pageBrowser->getMaxResPerPage()) : 0;
		$markers['###LLL:delete_question###'] = $LANG->getLL('delete_question');
		$content = Tx_Formhandler_StaticFuncs::substituteMarkerArray($code, $markers);
		return $content;
	}

	/**
	 * This function returns a list of all available formats to export to CSV.
	 * The user has to choose one ny another and export them to different files.
	 *
	 * @param array $formats The available formats
	 * @param array $detailId The selected records to export
	 * @return string formats selection view
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function generateFormatsSelector($formats, $detailId) {
		global $LANG;
		/*
		 * if there is only one record to export, initialize an array with the one uid
		 * to ensure that foreach loops will not crash.
		 */
		if(!is_array($detailId)) {
			$detailId = array($detailId);
		}

		$selectorCode = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###FORMATS_SELECTOR###');
		
		$foundFormats = 0;
		
		//loop through formats
		foreach($formats as $key => $format) {
			
			$formatMarkers = array();
			
			//if format is valid
			if(isset($format) && is_array($format)) {
				$foundFormats++;
				$code = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###SINGLE_FORMAT###');
				$formatMarkers['###URL###'] = $_SERVER['PHP_SELF'];

				$formatMarkers['###HIDDEN_FIELDS###'] = '';
				//add hidden fields for all selected records to export
				foreach($detailId as $id) {
					$formatMarkers['###HIDDEN_FIELDS###'] .= '<input type="hidden" name="formhandler[markedUids][]" value="' . $id . '" />';
				}
				$formatMarkers['###KEY###'] = $key;
				$formatMarkers['###UID###'] = $this->id;
				$formatMarkers['###LLL:export###'] = $LANG->getLL('export');
				$formatMarkers['###FORMAT###'] = implode(',', array_keys($format));
				$markers['###FORMATS###'] .= Tx_Formhandler_StaticFuncs::substituteMarkerArray($code, $formatMarkers);
			}
			
		}
		$code = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###SINGLE_FORMAT###');
		$formatMarkers = array();
		$formatMarkers['###URL###'] = $_SERVER['PHP_SELF'];
		
		//add hidden fields for all selected records to export
		foreach($detailId as $id) {
			$formatMarkers['###HIDDEN_FIELDS###'] .= '<input type="hidden" name="formhandler[markedUids][]" value="' . $id . '" />';
		}
		$formatMarkers['###KEY###'] = '*';
		$formatMarkers['###UID###'] = $this->id;
		$formatMarkers['###LLL:export###'] = $LANG->getLL('export_all');
		$formatMarkers['###FORMAT###'] = '';
		$markers['###FORMATS###'] .= Tx_Formhandler_StaticFuncs::substituteMarkerArray($code, $formatMarkers);
		
		$markers['###UID###'] = $this->id;
		$markers['###LLL:formats_found###'] = sprintf($LANG->getLL('formats_found'), $foundFormats);
		$markers['###BACK_URL###'] = $_SERVER['PHP_SELF'];
		$markers['###LLL:back###'] = $LANG->getLL('back');
		return Tx_Formhandler_StaticFuncs::substituteMarkerArray($selectorCode, $markers);
	}

	/**
	 * This function returns a single view of a record
	 *
	 * @param int $singleUid The UID of the record to show
	 * @return string single view
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function showSingleView($singleUid) {
		global $LANG;


		//select the record
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,pid,crdate,ip,params', $this->logTable, ('uid=' . $singleUid));

		//if UID was valid
		if($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			$viewCode = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###DETAIL_VIEW###');

			//unserialize params
			$params = unserialize($row['params']);

			$markers = array();

			//start with default fields (IP address, submission date, PID)
			$markers['###PID###'] = $row['pid'];
			$markers['###CRDATE###'] = date('Y/m/d H:i', $row['crdate']);
			$markers['###IP###'] = $row['ip'];

			$markers['###LLL:page_id###'] = $LANG->getLL('page_id');
			$markers['###LLL:crdate###'] = $LANG->getLL('crdate');
			$markers['###LLL:ip_address###'] = $LANG->getLL('ip_address');

			//add the submitted params
			if(isset($params) && is_array($params)) {
				$paramsTable .= '<table>';
				foreach($params as $key=>$value) {
					if(is_array($value)) {
						$value = implode(',', $value);
					}
					$paramsTable .= '
						<tr>
							<td style="font-weight:bold">' . $key . '</td>
							<td>' . $value . '</td>
						</tr>
					';
				}
				$paramsTable .= '</table>';
			}
			$markers['###LLL:params###'] = $LANG->getLL('params');
			$markers['###PARAMS###'] = $paramsTable;

			$markers['###UID###'] = $this->id;
			$markers['###LLL:export_as###'] = $LANG->getLL('export_as');
			$markers['###EXPORT_LINKS###'] = '<a href="' . $_SERVER['PHP_SELF'] . '?formhandler[detailId]=' . $row['uid'] . '&formhandler[renderMethod]=pdf">' . $LANG->getLL('pdf') . '</a>
						/<a href="' . $_SERVER['PHP_SELF'] . '?formhandler[detailId]=' . $row['uid'] . '&formhandler[renderMethod]=csv">' . $LANG->getLL('csv') . '</a>';
			$markers['###BACK_LINK###'] = '<a href="' . $_SERVER['PHP_SELF'] . '">' . $LANG->getLL('back') . '</a>';
			$content = Tx_Formhandler_StaticFuncs::substituteMarkerArray($viewCode, $markers);
			$content = $this->addCSS($content);
			return $content;
		}
	}

	/**
	 * This function returns an error message if the log table was not found
	 *
	 * @return string HTML code with error message
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function getErrorMessage() {
		global $LANG;
		$code = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###NO_TABLE_ERROR###');
		$markers = array();
		$markers['###LLL:noLogTable###'] = $LANG->getLL('noLogTable');
		return Tx_Formhandler_StaticFuncs::substituteMarkerArray($code, $markers);
	}

	/**
	 * This function selects all logged records from the log table using the filter settings.
	 *
	 * @return array The selected records
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function fetchRecords() {
		$records = array();

		//build WHERE clause
		$where = $this->buildWhereClause();

		//select the records
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,pid,crdate,ip,params,is_spam', $this->logTable, $where, '', 'crdate DESC', $this->pageBrowser->getSqlLimitClause());

		//if records found
		if($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$count = 0;
			while(FALSE !== ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				$records[$count] = $row;
				$count++;
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $records;
	}
	
	protected function countRecords() {
		//build WHERE clause
		$where = $this->buildWhereClause();		

		//select the records
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*)', $this->logTable, $where, '', 'crdate DESC');
		$count = 0;
		if ($res) {		   
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);		
			$count = $row[0]; 
		}
		return $count;
	}

	/**
	 * This function applies the filter settings and builds an according WHERE clause for the SELECT statement
	 *
	 * @return string WHERE clause for the SELECT statement
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function buildWhereClause() {

		//init gp params
		$params = t3lib_div::_GP('formhandler');
		$where = array();
		
		// Get tsconfig from current page
		if ($this->id) {
			$tsconfig = t3lib_BEfunc::getModTSconfig($this->id, 'tx_formhandler_mod1'); 
		}
		$pidFilter = '';
		
		if(strlen(trim($params['pidFilter'])) > 0) {
			$pidFilter = $params['pidFilter'];
 		}

		if(strlen(trim($params['pidFilter'])) > 0 && trim($params['pidFilter']) != "*") {
			$pids = t3lib_div::trimExplode(',', $params['pidFilter'], 1);
			$pid_search = array();
			// check is page shall be accessed by current BE user
			foreach($pids as $pid) {
				if (t3lib_BEfunc::readPageAccess(intval($pid))) $pid_search[] = intval($pid);
			}
			// check if there's a valid pid left
			$this->pidFilter = (empty($pid_search)) ? 0 : implode(",", $pid_search);
			$where[] = 'pid IN (' . $this->pidFilter . ')';
		// show all entries (admin only)
		} else if (trim($params['pidFilter']) == "*" && $GLOBALS['BE_USER']->user['admin']) {
			$this->pidFilter = "*";
		// show clicked page (is always accessable)
		} else {		
			$where[] = 'pid = ' . $this->id;
			$this->pidFilter = $this->id;
		}	
		
		if(trim($params['ipFilter']) > 0) {
			$ips = t3lib_div::trimExplode(',', $params['ipFilter'], 1);
			$ip_search = array();
			foreach($ips as $value) {
				$ip_search[] = "'" . htmlspecialchars($value) . "'";
			}
			$where[] = 'ip IN (' . implode(",", $ip_search) . ')';
 		}

		//only records submitted after given timestamp
		if(strlen(trim($params['startdateFilter'])) > 0) {
			$tstamp = Tx_Formhandler_StaticFuncs::dateToTimestamp($params['startdateFilter']);
			$where[] = 'crdate >= ' . $tstamp;
		}

		//only records submitted before given timestamp
		if(strlen(trim($params['enddateFilter'])) > 0) {
			$tstamp = Tx_Formhandler_StaticFuncs::dateToTimestamp($params['enddateFilter'], TRUE);
			$where[] = 'crdate <= ' . $tstamp;
		}

		//if filter was applied, return the WHERE clause
		if(count($where) > 0) {
			return implode(' AND ', $where);
		}
	}

	/**
	 * This function returns the filter fields on top.
	 *
	 * @return string HTML
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function getFilterSection() {
		global $LANG;

		//init gp params
		$params = t3lib_div::_GP('formhandler');

		$filter = '';
		$filter .= $this->getSelectionJS();
		$filter .= Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###FILTER_FORM###');

		$markers = array();
		$markers['###URL###'] = 			$_SERVER['PHP_SELF'];
		$markers['###UID###'] = 			$this->pidFilter;
		$markers['###IP###'] = 				htmlspecialchars($params['ipFilter']);
		$markers['###value_startdate###'] = htmlspecialchars($params['startdateFilter']);
		$markers['###value_enddate###'] = 	htmlspecialchars($params['enddateFilter']);
		$markers['###selected_howmuch_' . $params['howmuch'] . '###'] = 'selected="selected"';	
		
		// display show all function
		if ($GLOBALS['BE_USER']->user['admin']) {
			$markers['###PID_FILTER_ALL###'] = '<input type="button" onclick="pidSelectAll()" id="pidFilter_all" value="' . $LANG->getLL('select_all') . '"/>';		
		} else {
			$markers['###PID_FILTER_ALL###'] = '';		
		}
		
		$markers['###LLL:filter###'] = 					$LANG->getLL('filter');
		$markers['###LLL:pid_label###'] = 				$LANG->getLL('pid_label');
		$markers['###LLL:ip_address###'] = 				$LANG->getLL('ip_address');
		$markers['###LLL:pagination_how_much###'] = 	$LANG->getLL('pagination_how_much');
		$markers['###LLL:pagination_entries###'] = 		$LANG->getLL('pagination_entries');
		$markers['###LLL:pagination_all_entries###'] = 	$LANG->getLL('pagination_all_entries');
		$markers['###LLL:cal###'] = 					$LANG->getLL('cal');
		$markers['###LLL:startdate###'] = 				$LANG->getLL('startdate');
		$markers['###LLL:enddate###'] = 				$LANG->getLL('enddate');

		$this->addValueMarkers($markers, $params);


		$filter .= $this->getCalendarJS();


		return Tx_Formhandler_StaticFuncs::substituteMarkerArray($filter, $markers);
	}

	/**
	 * This function fills a marker array with ###value_[xxx]### markers.
	 * [xxx] are the keys of the given array $params.
	 *
	 * @param array &$markers
	 * @param array $params
	 * @return void
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function addValueMarkers(&$markers, $params) {
		if(is_array($params)) {
			foreach($params as $key => $value) {
				$markers['###value_' . $key . '###'] = $value;
			}
		}
	}

	/**
	 * This function returns the JavaScript code to initialize the popup calendar
	 *
	 * @return string JavaScript code
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function getCalendarJS() {
		return Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###CALENDAR_JS###');
	}

	/**
	 * This function returns HTML code of the function area consisting of buttons to select/deselect all table items, to export selected items
	 * and to delete selected items.
	 *
	 * @return string HTML and JavaScript
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function getFunctionArea() {
		global $LANG;
		$code = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###FUNCTION_AREA###');
		$markers = array();
		$markers['###URL###'] = $_SERVER['PHP_SELF'];
		
		$markers['###EXPORT_FIELDS_MARKER###'] = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###EXPORT_FIELDS###');
		
		$markers['###DELETE_FIELDS_MARKER###'] = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###DELETE_FIELDS###');
		$markers['###SELECTION_BOX_MARKER###'] = $this->getSelectionBox();
		
		$markers['###WHICH_ENTRIES_MARKER###'] = $this->pageBrowser->getResultBox()->fromRec . $this->pageBrowser->getResultBox()->toRec . $this->pageBrowser->getResultBox()->totalRec;
		$markers['###WHICH_PAGES_MARKER###'] = $this->pageBrowser->getResultBox()->curPage . ' ' . $this->pageBrowser->getResultBox()->totalPage;
 		
 		$content = Tx_Formhandler_StaticFuncs::substituteMarkerArray($code, $markers);
 		$markers = array();
 		$markers['###UID###'] = $this->id;
		$markers['###LLL:delete_selected###'] = $LANG->getLL('delete_selected');
		return Tx_Formhandler_StaticFuncs::substituteMarkerArray($content, $markers);
	}

	/**
	 * This function returns HTML code of the buttons to select/deselect all table items
	 *
	 * @return string HTML
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function getSelectionBox() {
		global $LANG;
		$code = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###SELECTION_BOX###');
		$markers = array();
		$markers['###LLL:select_all###'] = $LANG->getLL('select_all');
		$markers['###LLL:deselect_all###'] = $LANG->getLL('deselect_all');
		return Tx_Formhandler_StaticFuncs::substituteMarkerArray($code, $markers);
	}

	/**
	 * This function returns the index table.
	 *
	 * @param array &$records The records to show in table
	 * @return string HTML
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function getTable(&$records) {
		global $LANG;
		
		//get filter
		$table = $this->getFilterSection();

		if(count($records) == 0) {
			return $table . '<div>' . $LANG->getLL('no_records') . '</div>';
		}


		//init gp params
		$params = t3lib_div::_GP('formhandler');


		
		$table .= $this->getFunctionArea();
		$tableCode = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###LIST_TABLE###');

		$tableMarkers = array();
		$tableMarkers['###LLL:PAGE_ID###'] = $LANG->getLL('page_id');
		$tableMarkers['###LLL:SUBMISSION_DATE###'] = $LANG->getLL('submission_date');
		$tableMarkers['###LLL:IP###'] = $LANG->getLL('ip_address');
		$tableMarkers['###LLL:DETAIL_VIEW###'] = '';
		$tableMarkers['###LLL:EXPORT###'] = $LANG->getLL('export');

		$count = 1;
		$tableMarkers['###ROWS###'] = '';
		
		//add records
		foreach($records as $record) {
			if($count % 2 == 0) {
				$style = 'class="bgColor3-20"';
			} else {
				$style = 'class="bgColor3-40"';
			}
			if($record['is_spam'] == 1) {
				$style = 'style="background-color:#dd7777"';
			}
			$rowCode = Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###LIST_TABLE_ROW###');
			$markers = array();
			$markers['###UID###'] = $this->id;
			$markers['###ROW_STYLE###'] = $style;
			$markers['###PID###'] = $record['pid'];
			$markers['###SUBMISSION_DATE###'] = date('Y/m/d H:i', $record['crdate']);
			$markers['###IP###'] = $record['ip'];
			$markers['###DETAIL_LINK###'] = '<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $this->id . '&formhandler[detailId]=' . $record['uid'] . '"><img ' . t3lib_iconWorks::skinImg('../../../../../../typo3/', 'gfx/zoom.gif') . '/></a>';
			$markers['###EXPORT_LINKS###'] = '<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $this->id . '&formhandler[detailId]=' . $record['uid'] . '&formhandler[renderMethod]=pdf">PDF</a>
						/<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $this->id . '&formhandler[detailId]=' . $record['uid'] . '&formhandler[renderMethod]=csv">CSV</a>';
			$checkbox = '<input type="checkbox" name="formhandler[markedUids][]" value="' . $record['uid'] . '" ';
			if(isset($params['markedUids']) && is_array($params['markedUids']) && in_array($record['uid'], $params['markedUids'])) {
				$checkbox .= 'checked="checked"';
			}
			$checkbox .= '/>';
			$markers['###CHECKBOX###'] = $checkbox;
			$count++;
			$tableMarkers['###ROWS###'] .= Tx_Formhandler_StaticFuncs::substituteMarkerArray($rowCode, $markers);
		}
		
		// add pagination
		$tableMarkers['###LLL:ENTRIES###'] = $LANG->getLL('pagination_show_entries');
		$tableMarkers['###WHICH_PAGEBROWSER###'] = $this->pageBrowser->displayBrowseBox();

		//add Export as option
		$table .= Tx_Formhandler_StaticFuncs::substituteMarkerArray($tableCode, $tableMarkers);
		$table .= Tx_Formhandler_StaticFuncs::getSubpart($this->templateCode, '###EXPORT_FIELDS###');
		
		$markers = array();
		$markers['###UID###'] = $this->id;
		$table = Tx_Formhandler_StaticFuncs::substituteMarkerArray($table, $markers);
		$table = $this->addCSS($table);
		return Tx_Formhandler_StaticFuncs::removeUnfilledMarkers($table);
	}

	/**
	 * Adds HTML code to include the CSS file to given HTML content.
	 *
	 * @param string The HTML content
	 * @return string The changed HML content
	 * @author Reinhard Führicht <rf@typoheads.at>
	 */
	protected function addCSS($content) {
		$cssLink = '
			<link 	rel="stylesheet" 
					type="text/css" 
					href="../../../Resources/CSS/backend/styles.css" 
			/>
		';
		return $cssLink. $content;
	}

}
?>
