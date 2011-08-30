<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Tolleiv Nietsch <info@tolleiv.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
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

class tx_templavoila_cm1_ajax {

	/**
	 * Return the content of the current "displayFile"
	 *
	 * @param	array		$params
	 * @param	object		$ajaxObj
	 * @return	void
	 */
	public function getDisplayFileContent($params, &$ajaxObj) {
		$session = $GLOBALS['BE_USER']->getSessionData(t3lib_div::_GP('key'));
		echo t3lib_div::getUrl(PATH_site . $session['displayFile']);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/cm1/class.tx_templavoila_cm1_ajax.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/cm1/class.tx_templavoila_cm1_ajax.php']);
}
?>