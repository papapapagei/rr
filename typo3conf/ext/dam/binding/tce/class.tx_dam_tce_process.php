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
 *   54: class tx_dam_tce_process
 *   59:     function processCmdmap_preProcess($command, $table, $id, $value, $tce)
 *  106:     function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $tce)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


/**
 * additional TCE processing
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class tx_dam_tce_process {

	/**
	 * delete file when record is deleted
	 */
	function processCmdmap_preProcess($command, $table, $id, $value, $tce) {
		global $FILEMOUNTS, $BE_USER, $TYPO3_CONF_VARS;

		if($table === 'tx_dam') {
			if ($rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_dam', 'uid='.$id, '', '', 1, 'uid')) {
				$row = $rows[$id];

				switch ($command)	{

						// delete the file when the record is deleted
					case 'delete':

						require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');
						require_once (PATH_t3lib.'class.t3lib_extfilefunc.php');

						$filepath = tx_dam::file_absolutePath($row);
						if (@is_file($filepath)) {
							$cmd = array();
							$cmd['delete'][0]['data'] = $filepath;
	
	
								// Initializing:
							$tce->fileProcessor = t3lib_div::makeInstance('t3lib_extFileFunctions');
							$tce->fileProcessor->init($FILEMOUNTS, $TYPO3_CONF_VARS['BE']['fileExtensions']);
							$tce->fileProcessor->init_actionPerms(tx_dam::getFileoperationPermissions());
							$tce->fileProcessor->dontCheckForUnique = $tce->overwriteExistingFiles ? 1 : 0;
	
								// Checking referer / executing:
							$refInfo = parse_url(t3lib_div::getIndpEnv('HTTP_REFERER'));
							$httpHost = t3lib_div::getIndpEnv('TYPO3_HOST_ONLY');
							if ($httpHost!=$refInfo['host'] && $tce->vC!=$BE_USER->veriCode() && !$TYPO3_CONF_VARS['SYS']['doNotCheckReferer'])	{
								$tce->fileProcessor->writeLog(0,2,1,'Referer host "%s" and server host "%s" did not match!',array($refInfo['host'],$httpHost));
							} else {
								$tce->fileProcessor->start($cmd);
								$tce->fileProcessor->processData();
							}
						}

					break;
					default:
					break;
				}
			}
		}
	}

	/**
	 * status TXDAM_status_file_changed will be reset when record was edited
	 */
	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $tce) {
		if($table === 'tx_dam' AND intval($id)) {
			if ($rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_dam', 'uid='.intval($id), '', '', 1, 'uid')) {
				$row = $rows[$id];
				if ($row['file_status']==TXDAM_status_file_changed) {
					$fieldArray['file_status'] = TXDAM_status_file_ok;
				}
			}
		}
// todo: implement DB-Trigger - can't remember what this means
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/tce/class.tx_dam_tce_process.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/tce/class.tx_dam_tce_process.php']);
}
?>