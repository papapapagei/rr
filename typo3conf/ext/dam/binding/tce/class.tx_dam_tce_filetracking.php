<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 *
 * Part of the DAM (digital asset management) extension.
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
 *   53: class tx_dam_tce_filetracking
 *   58:     function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, $tce)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


/**
 * Tracking of uploads files through TCE
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class tx_dam_tce_filetracking {

	/**
	 * Track uploads/* files
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, $tce) {

			// files are moved ...
		if(is_array($tce->copiedFileMap) AND count($tce->copiedFileMap)) {

				// Let's go
			foreach ($tce->copiedFileMap as $source => $dest) {

					// let's update the file tracking
				tx_dam_db::trackingUploadsFile($dest);

			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/tce/class.tx_dam_tce_filetracking.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/tce/class.tx_dam_tce_filetracking.php']);
}
?>