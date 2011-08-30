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
 * $Id: Tx_Formhandler_ErrorCheck_Equals.php 40269 2010-11-16 15:23:54Z reinhardfuehricht $
 *                                                                        */

/**
 * Validates that a specified field equals a specified word
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	ErrorChecks
 */
class Tx_Formhandler_ErrorCheck_Equals extends Tx_Formhandler_AbstractErrorCheck {

	/**
	 * Validates that a specified field equals a specified word
	 *
	 * @param array &$check The TypoScript settings for this error check
	 * @param string $name The field name
	 * @param array &$gp The current GET/POST parameters
	 * @return string The error string
	 */
	public function check(&$check, $name, &$gp) {
		$checkFailed = '';
		$formValue = trim($gp[$name]);

		if (isset($gp[$name]) && strlen(trim($gp[$name])) > 0) {
			$checkValue = Tx_Formhandler_StaticFuncs::getSingle($check['params'], 'word');
			if (strcasecmp($formValue, $checkValue)) {

					//remove userfunc settings
				unset($check['params']['word.']);
				$checkFailed = $this->getCheckFailed($check);
			}
		}
		return $checkFailed;
	}

}
?>