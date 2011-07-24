<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Rene Fritz (r.fritz@colorcube.de)
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

/**
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
 
require_once (PATH_txdam.'tests/class.tx_dam_testlib.php');

class tx_dam_config_testcase extends tx_dam_testlib {




	/**
	 * tx_dam::config_setValue()
	 */
	public function test_config_setValue () {
		tx_dam::config_init();
		
		tx_dam::config_setValue('setup.indexing.auto', true);
		$value = tx_dam::config_getValue('setup.indexing.auto');
		self::assertEquals ($value, true);
		
		tx_dam::config_setValue('setup.indexing.auto', false);
		$value = tx_dam::config_getValue('setup.indexing.auto');
		self::assertEquals ($value, false);
		
	}


}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_config_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_config_testcase.php']);
//}
?>