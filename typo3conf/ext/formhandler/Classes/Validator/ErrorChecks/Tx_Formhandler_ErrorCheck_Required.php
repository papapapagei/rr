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
 * $Id: Tx_Formhandler_ErrorCheck_Required.php 31246 2010-03-19 11:54:25Z reinhardfuehricht $
 *                                                                        */

/**
 * Validates that a specified field is filled out
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	ErrorChecks
 */
class Tx_Formhandler_ErrorCheck_Required extends Tx_Formhandler_AbstractErrorCheck {

	/**
	 * Validates that a specified field is filled out
	 *
	 * @param array &$check The TypoScript settings for this error check
	 * @param string $name The field name
	 * @param array &$gp The current GET/POST parameters
	 * @return string The error string
	 */
	public function check(&$check, $name, &$gp) {
		$checkFailed = '';
		if(is_array($gp[$name])) {
			if(empty($gp[$name])) {
				$checkFailed = $this->getCheckFailed($check);
			}
		} elseif(!isset($gp[$name]) || strlen(trim($gp[$name])) == 0) {
			$checkFailed = $this->getCheckFailed($check);
		}
		return $checkFailed;
	}


}
?>