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
 * $Id: Tx_Formhandler_AbstractFinisher.php 37974 2010-09-11 19:58:17Z fabriziobranca $
 *                                                                        */

/**
 * Abstract class for Finisher Classes used by Formhandler
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	Finisher
 * @abstract
 */
abstract class Tx_Formhandler_AbstractFinisher extends Tx_Formhandler_AbstractComponent {

	/**
	 * Method to define whether the config is valid or not. If no, display a warning on the frontend.
	 * The default value is TRUE. This up to the finisher to overload this method
	 *
	 */
	public function validateConfig() {

	}

}
?>
