<?php

require_once(t3lib_extMgm::extPath('formhandler') . 'Resources/PHP/parsecsv.lib.php');
class Tx_Formhandler_Generator_Csv extends Tx_Formhandler_AbstractGenerator {

	/**
	 * Renders the CSV.
	 *
	 * @return void
	 */
	public function process() {
		$params = $this->gp;
		$exportParams = Tx_Formhandler_StaticFuncs::getSingle($this->settings, 'exportParams');
		if (!is_array($exportParams)) {
			$exportParams = t3lib_div::trimExplode(',', $exportParams);
		}

		//build data
		foreach ($params as $key => &$value) {
			if (is_array($value)) {
				$value = implode(',', $value);
			}
			if (count($exportParams) > 0 && !in_array($key, $exportParams)) {
				unset($params[$key]);
			}
			$value = str_replace('"', '""', $value);
		}

		// create new parseCSV object.
		$csv = new parseCSV();
		$csv->output('formhandler.csv', $data, $params);
		die();
	}

	protected function getComponentLinkParams($linkGP) {
		$prefix = Tx_Formhandler_Globals::$formValuesPrefix;
		$tempParams = array(
			'action' => 'csv'
		);
		$params = array();
		if ($prefix) {
			$params[$prefix] = $tempParams;
		} else {
			$params = $tempParams;
		}
		return $params;
	}

}

?>