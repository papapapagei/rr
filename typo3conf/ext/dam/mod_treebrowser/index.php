<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
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
 * @package DAM-Mod
 * @ignore
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   66: class tx_dam_treebrowser extends tx_dam_treelib_browser
 *
 * TOTAL FUNCTIONS: 0
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:lang/locallang_misc.xml');



require_once (PATH_txdam.'treelib/class.tx_dam_treelib_browser.php');


/**
 * Script Class for the treeview in TCEforms elements
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @ignore
 */
class tx_dam_treebrowser extends tx_dam_treelib_browser {
	// nothing to do here
}


// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_treebrowser/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_treebrowser/index.php']);
}





// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dam_treebrowser');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();

?>