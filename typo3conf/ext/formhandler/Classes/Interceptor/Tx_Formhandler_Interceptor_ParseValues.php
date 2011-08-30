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
 * $Id: Tx_Formhandler_Interceptor_ParseValues.php 40269 2010-11-16 15:23:54Z reinhardfuehricht $
 *                                                                       
 */

/**
 * An interceptor parsing some GET/POST parameters
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	Interceptor
 */
class Tx_Formhandler_Interceptor_ParseValues extends Tx_Formhandler_AbstractInterceptor {

	/**
	 * The main method called by the controller
	 *
	 * @return array The probably modified GET/POST parameters
	 */
	public function process() {

		//parse as float
		$fields = t3lib_div::trimExplode(',', $this->settings['parseFloatFields'], TRUE);
		$this->parseFloats($fields);
		
		return $this->gp;
	}

	/**
	 * parses the given field values from strings to floats
	 * 
	 * @return void
	 * @param array $fields
	 */
	protected function parseFloats($fields){
		if (is_array($fields)) {
			foreach ($fields as $idx => $field) {
				if (isset($this->gp[$field])) {
					$this->gp[$field] = $this->getFloat($this->gp[$field]);
				}
			}
		}
	}

	/**
	 * Parses the formated value as float. Needed for values like:
	 * x xxx,- / xx,xx / xx'xxx,xx / -xx.xxx,xx
	 * Caution: This pareses x.xxx.xxx to xxxxxxx (but xx.xx to xx.xx)
	 * 
	 * @return float
	 * @param string $value formated float
	 */
	protected function getFloat($value) {
		return floatval(preg_replace('#^([-]*[0-9\.,\' ]+?)((\.|,){1}([0-9-]{1,2}))*$#e', "str_replace(array('.', ',', \"'\", ' '), '', '\\1') . '.\\4'", $value));
	}

}
?>
