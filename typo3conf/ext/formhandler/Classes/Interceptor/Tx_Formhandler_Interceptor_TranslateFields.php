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
 * $Id: Tx_Formhandler_Interceptor_Default.php 27708 2009-12-15 09:22:07Z reinhardfuehricht $
 *                                                                        */

/**
 * Combines values entered in form field and stores it in a new entry in $this->gp.
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	Interceptor
 */
class Tx_Formhandler_Interceptor_TranslateFields extends Tx_Formhandler_AbstractInterceptor {

	/**
	 * The main method called by the controller
	 *
	 * @return array The probably modified GET/POST parameters
	 */
	public function process() {
		$this->langFiles = Tx_Formhandler_Globals::$langFiles;
		if(is_array($this->settings['translateFields.'])) {
			foreach($this->settings['translateFields.'] as $newField=>$options) {
				$newField = str_replace('.', '', $newField);
				if(isset($options['langKey'])) {
					$this->gp[$newField] = $this->translateFields($options);
					Tx_Formhandler_StaticFuncs::debugMessage('translated', $newField, $this->gp[$newField]);
				}
			}
		}
		return $this->gp;
	}
	
	protected function translateFields($options) {
		$key = $options['langKey'];
		$field = $options['field'];
		if($field) {
			$key = str_replace('|', $this->gp[$field], $key);
		} 
		return Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, $key); 
	}

}
?>
