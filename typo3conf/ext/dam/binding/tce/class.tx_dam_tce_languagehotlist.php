<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2006 Rene Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * using the hotlist functionality for the language select box
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   52: class tx_dam_tce_languagehotlist
 *   54:     function processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $tce)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


/**
 * using the hotlist functionality for the language select box
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class tx_dam_tce_languagehotlist {

	function processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $tce) {
		if($table === 'tx_dam' AND $fieldArray['language']) {
				// the hotlist will be updated only if the field changed, because only then it's in the $fieldArray
			tx_staticinfotables_div::updateHotlist ('static_languages', $fieldArray['language'], 'lg_iso_2', 'dam');
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/tce/class.tx_dam_tce_languagehotlist.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/tce/class.tx_dam_tce_languagehotlist.php']);
}
?>