<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Module extension (addition to function menu) 'Media>Tools>Categories'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage tools
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   56: class tx_dam_tools_categories extends t3lib_extobjbase
 *   64:     function modMenu()
 *   80:     function main()
 *   96:     function moduleContent()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(PATH_txdam.'components/class.tx_dam_dbTriggerMediaTypes.php');

/**
 * Module 'Media>Tools>Categories'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_tools_categories extends t3lib_extobjbase {


	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

		return array(
			'tx_dam_tools_categories.func' => array(
				'txdamMedia' => $LANG->sL('LLL:EXT:dam/lib/locallang.xml:mediaTypes'),
			),
		);
	}


	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()	{

		$content = '';
		$content.=  $this->pObj->getHeaderBar('', t3lib_BEfunc::getFuncMenu($this->pObj->id,'SET[tx_dam_tools_categories.func]',$this->pObj->MOD_SETTINGS['tx_dam_tools_categories.func'],$this->pObj->MOD_MENU['tx_dam_tools_categories.func']));
		$content.= $this->pObj->doc->spacer(10);
		$content.= $this->moduleContent();

		return $content;
	}


	/**
	 * Generates the module content
	 *
	 * @return	string		HTML content
	 */
	function moduleContent()    {
		global  $BE_USER, $LANG, $BACK_PATH;

		$content = '';

		switch($func = $this->pObj->MOD_SETTINGS['tx_dam_tools_categories.func'])    {


			case 'txdamMedia':

				$content.= $this->pObj->doc->section($this->pObj->MOD_MENU['tx_dam_tools_categories.func'][$func], '',0,1);
				$content.= $this->pObj->doc->spacer(10);

				if (t3lib_div::_GP('process')) {
					
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_metypes_avail', '');
					
					$fileTypes = array();
					$mediaTypes = array();

					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('DISTINCT tx_dam.media_type, tx_dam.file_type', 'tx_dam', 'tx_dam.pid='.intval($this->pObj->defaultPid));
					
					if ($rows) {
						foreach ($rows as $row) {
							tx_dam_dbTriggerMediaTypes::insertMetaTrigger($row);
						}
					}
					
					$content.= '<p>Media types browse tree was rebuild.</p>';


				} else {

					$content.= '<p>This function cleans up the media types browse tree and remove non existing types.</p>';
					$content.= '<input type="submit" name="process" value="Perform Cleanup" />';

				}

			break;
/*
			case 'txdamMedia':

				$content.= $this->pObj->doc->section($this->pObj->MOD_MENU['tx_dam_tools_categories.func'][$func], '',0,1);
				$content.= $this->pObj->doc->spacer(10);

				if (t3lib_div::_GP('process')) {
					$removedFileTypes = array();
					$removedMediaTypes = array();
					$mediaTypes = array();

					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam_metypes_avail', 'parent_id<>0');
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{

						$mediaTypes[$row['parent_id']] = $row['parent_id'];

						$resDam = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,file_type', 'tx_dam', 'deleted=0 AND file_type='.$GLOBALS['TYPO3_DB']->fullQuoteStr($row['title'],'tx_dam'),'','','1');
						if (!$rowDam = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resDam)) {
							$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_metypes_avail', 'uid='.$row['uid']);
							$removedFileTypes[] = $row['title'];
						}
					}
					if ($removedFileTypes) {
						$content.= '<p>Removed file types:<br />'.implode(', ', $removedFileTypes).'</p>';
					}

					if ($mediaTypes) {
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam_metypes_avail', 'parent_id=0 AND type NOT IN ('.implode(',', $mediaTypes).')');
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
							$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_metypes_avail', 'uid='.$row['uid']);
							$removedMediaTypes[] = $row['title'];
						}
						if ($removedMediaTypes) {
							$content.= '<p>Removed media types:<br />'.implode(', ', $removedMediaTypes).'</p>';
						}
					}

				} else {

					$content.= '<p>This function cleans up the media types browse tree and remove non existing types.</p>';
					$content.= '<input type="submit" name="process" value="Perform Cleanup" />';

				}

			break;
*/

			default:
				$content.= '';
		}
		return $content;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_categories/class.tx_dam_tools_categories.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_categories/class.tx_dam_tools_categories.php']);
}

?>