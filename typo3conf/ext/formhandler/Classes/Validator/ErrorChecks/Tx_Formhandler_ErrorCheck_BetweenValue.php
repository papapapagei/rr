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
 * $Id: Tx_Formhandler_ErrorCheck_BetweenValue.php 40269 2010-11-16 15:23:54Z reinhardfuehricht $
 *                                                                        */

/**
 * Validates that a specified field is an integer between two specified values
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	ErrorChecks
 */
class Tx_Formhandler_ErrorCheck_BetweenValue extends Tx_Formhandler_AbstractErrorCheck {

	/**
	 * Validates that a specified field is an integer between two specified values
	 *
	 * @param array &$check The TypoScript settings for this error check
	 * @param string $name The field name
	 * @param array &$gp The current GET/POST parameters
	 * @return string The error string
	 */
	public function check(&$check, $name, &$gp) {
		$checkFailed = '';
		$min = floatval(str_replace(',', '.', Tx_Formhandler_StaticFuncs::getSingle($check['params'], 'minValue')));
		$max = floatval(str_replace(',', '.', Tx_Formhandler_StaticFuncs::getSingle($check['params'], 'maxValue')));
		$valueToCheck = str_replace(',', '.', $gp[$name]);
		if (isset($gp[$name]) &&
			(!is_numeric($valueToCheck) || 
			$valueToCheck < $min || 
			$valueToCheck > $max)) {

			$checkFailed = $this->getCheckFailed($check);
		}
		return $checkFailed;
	}

}
?>