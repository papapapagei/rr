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
 *
 * $Id:$
 *                                                                        */

/**
 * This PreProcessor adds the posibility to load default values from database.
 * Values for the first step are loaded to $gp values of other steps are stored
 * to the session.
 *
 * Example configuration:
 *
 * <code>
 * preProcessors.1.class = Tx_Formhandler_PreProcessor_LoadDB
 * #DB setup (properties commented out are not required).
 * #All properties can be processed as cObjects (like TEXT and COA)
 * preProcessors.1.config.select {
 *       #selectFields = *
 *       table = my_custom_table
 *       #where = 
 *       #groupBy = 
 *       #orderBy = 
 *       #limit = 
 * }
 * preProcessors.1.config.1.contact_via.mapping = email
 * preProcessors.1.config.2.[field1].mapping = listfield
 * preProcessors.1.config.2.[field1].separator = ,
 * #The following allows for dynamic field names
 * preProcessors.1.config.2.[field2].mapping {
 *       data = page:subtitle
 *       wrap = field_|_xyz
 * }
 * preProcessors.1.config.2.[field3].mapping < plugin.tx_exampleplugin
 * <code>
 *
 *
 * @author	Mathias Bolt Lesniak, LiliO Design <mathias@lilio.com>
 * @package	Tx_Formhandler
 * @subpackage	PreProcessor
 */

class Tx_Formhandler_PreProcessor_LoadDB extends Tx_Formhandler_AbstractPreProcessor {

	/**
	 * @var Array $data as associative array. Row data from DB.
	 */
	protected $data;

	/**
	 * 
	 * @return Array GP
	 */
	public function process() {
		$this->data = $this->loadDB($this->settings['select.']);

		foreach ($this->settings as $step => $stepSettings){
			$step = preg_replace('/\.$/', '', $step);

			if ($step !== 'select') {
				if (intval($step) === 1){
					$this->loadDBToGP($stepSettings);
				} elseif (is_numeric($step)) {
					$this->loadDBToSession($stepSettings, $step);
				}
			}
		}

		return $this->gp;
	}

	/**
	 * Loads data from DB intto the GP Array
	 *
	 * @return void
	 * @param array $settings
	 */
	protected function loadDBToGP($settings) {
		$data = $this->data;

		if (is_array($settings)) {
			$arrKeys = array_keys($settings);
			foreach ($arrKeys as $idx => $fN) {
				$fN = preg_replace('/\.$/', '', $fN);

				if (!isset($this->gp[$fN])) {

					//post process the field value.
					if (is_array($settings[$fN.'.']['preProcessing.'])) {
						$settings[$fN.'.']['preProcessing.']['value'] = $this->gp[$fN];
						$this->gp[$fN] = Tx_Formhandler_StaticFuncs::getSingle($settings[$fN.'.'], 'preProcessing');
					}

					$this->gp[$fN] = $data[Tx_Formhandler_StaticFuncs::getSingle($settings[$fN.'.'], 'mapping')];
					if ($settings[$fN . '.']['separator']) {
						$separator = $settings[$fN . '.']['separator'];
						$this->gp[$fN] = t3lib_div::trimExplode($separator, $this->gp[$fN]);
					}

					//post process the field value.
					if (is_array($settings[$fN.'.']['postProcessing.'])) {
						$settings[$fN.'.']['postProcessing.']['value'] = $this->gp[$fN];
						$this->gp[$fN] = Tx_Formhandler_StaticFuncs::getSingle($settings[$fN.'.'], 'postProcessing');
					}
				}
			}
		}
	}

	/**
	 * Loads DB data into the Session. Used only for step 2+.
	 *
	 * @return void
	 * @param Array $settings
	 * @param int $step
	 */
	protected function loadDBToSession($settings, $step){
		$data = $this->data;

		session_start();
		if (is_array($settings) && $step) {
			$values = Tx_Formhandler_Globals::$session->get('values');
			$arrKeys = array_keys($settings);
			foreach ($arrKeys as $idx => $fieldname) {

				//post process the field value.
				if (is_array($settings[$fieldname . '.']['preProcessing.'])) {
					$settings[$fieldname . '.']['preProcessing.']['value'] = $values[$step][$fieldname];
					$values[$step][$fieldname] = Tx_Formhandler_StaticFuncs::getSingle($settings[$fieldname . '.'], 'preProcessing');
				}

				$fN = preg_replace('/\.$/', '', $fieldname);
				if (!isset($values[$step][$fieldname])) {
					$values[$step][$fieldname] = $data[Tx_Formhandler_StaticFuncs::getSingle($settings[$fieldname . '.'], 'mapping')];
					if ($settings[$fieldname . '.']['separator']) {
						$separator = $settings[$fieldname . '.']['separator'];
						$values[$step][$fieldname] = t3lib_div::trimExplode($separator, $values[$step][$fieldname]);
					}
				}

				//post process the field value.
				if (is_array($settings[$fieldname . '.']['postProcessing.'])) {
					$settings[$fieldname . '.']['postProcessing.']['value'] = $values[$step][$fieldname];
					$values[$step][$fieldname] = Tx_Formhandler_StaticFuncs::getSingle($settings[$fieldname . '.'], 'postProcessing');
				}
			}
			Tx_Formhandler_Globals::$session->set('values', $values);
		}
	}

	/**
	 * Loads data from DB
	 *
	 * @return Array of row data
	 * @param Array $settings
	 * @param int $step
	 */
	protected function loadDB($settings) {
		$selectFields = Tx_Formhandler_StaticFuncs::getSingle($settings, 'selectFields');
		if(strlen($selectFields) === 0) {
			$selectFields = '*';
		}

		$sql = $GLOBALS['TYPO3_DB']->SELECTquery(
			$selectFields,
			Tx_Formhandler_StaticFuncs::getSingle($settings, 'table'),
			Tx_Formhandler_StaticFuncs::getSingle($settings, 'where'),
			Tx_Formhandler_StaticFuncs::getSingle($settings, 'groupBy'),
			Tx_Formhandler_StaticFuncs::getSingle($settings, 'orderBy'),
			Tx_Formhandler_StaticFuncs::getSingle($settings, 'limit')
		);

		Tx_Formhandler_StaticFuncs::debugMessage($sql);

		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			return $row;
		}
		return array();
	}
}

?>
