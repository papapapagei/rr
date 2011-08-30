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
 * Module extension (addition to function menu) 'Media>Tools>Mime Types'
 *
 * @author	Dan Osipov <dosipov@phillyburbs.com>
 * @package DAM-Mod
 * @subpackage tools
 */

require_once(PATH_t3lib.'class.t3lib_tsparser_ext.php');
require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

/**
 * Module 'Media>Tools>Mime Types'
 *
 * @author	Dan Osipov <dosipov@phillyburbs.com> 
 */
class tx_dam_tools_mimetypes extends t3lib_extobjbase {

	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()	{
		global $LANG,$BACK_PATH;

		$content = '';
		//$content .= $this->pObj->getHeaderBar('', t3lib_BEfunc::getFuncMenu($this->pObj->id,'SET[tx_dam_tools_config.func]',$this->pObj->MOD_SETTINGS['tx_dam_tools_config.func'],$this->pObj->MOD_MENU['tx_dam_tools_config.func']));
		$content .= $this->pObj->doc->spacer(10);
		$content .= '<a href="' . $BACK_PATH . 'alt_doc.php?returnUrl=' . rawurlencode( t3lib_div::getIndpEnv('TYPO3_REQUEST_URL') ) . '&id=' . tx_dam_db::getPid() . '&edit[tx_dam_media_types][]=new"><img'.t3lib_iconWorks::skinImg($this->pObj->doc->backPath,'gfx/new_el.gif','width="11" height="12"').' title="Create new type" alt="" height="16" width="16"> ' . $LANG->getLL('media_types_new') . '</a>';
		$content .= $this->listMimeTypes();

		return $content;
	}

	/**
	 * List media types, along with options to edit & delete
	 *
	 * @return	string		HTML table of all the mimetypes
	 */	
	function listMimeTypes() {
		global $LANG, $BACK_PATH, $BE_USER;

			// Load template
		$content = t3lib_parsehtml::getSubpart(t3lib_div::getURL($BACK_PATH . t3lib_extMgm::extRelPath('dam') . 'modfunc_tools_mimetypes/template.html'), '###MOD_TEMPLATE###');
		$rowTemplate[1] = t3lib_parsehtml::getSubpart($content, '###ROW_1###');
		$rowTemplate[2] = t3lib_parsehtml::getSubpart($content, '###ROW_2###');

			// Add some JS
		$this->pObj->doc->JScode .= $this->pObj->doc->wrapScriptTags('
				function deleteRecord(id)	{	//
					if (confirm('.$LANG->JScharCode($LANG->getLL('deleteWarning')).'))	{
						window.location.href = "'.$BACK_PATH.'tce_db.php?cmd[tx_dam_media_types]["+id+"][delete]=1&redirect=' . rawurlencode( t3lib_div::getIndpEnv('TYPO3_REQUEST_URL') ) . '&id=' . tx_dam_db::getPid() . '&vC='.$BE_USER->veriCode().'&prErr=1&uPT=1";
					}
					return false;
				}
		');
		
			// Get content
		$alternate = 1;
		$rows = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam_media_types', '', '', 'ext ASC');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$editURL = $BACK_PATH . 'alt_doc.php?returnUrl=' . rawurlencode( t3lib_div::getIndpEnv('TYPO3_REQUEST_URL') ) . '&id=' . tx_dam_db::getPid() . '&edit[tx_dam_media_types][' . $row['uid'] . ']=edit';
			//$deleteURL = $BACK_PATH . 'alt_doc.php?returnUrl=' . rawurlencode( t3lib_div::getIndpEnv('TYPO3_REQUEST_URL') ) . '&id=' . tx_dam_db::getPid() . '&edit[tx_dam_media_types][' . $row['uid'] . '][delete]=1';
			$rowMarkers['EDIT'] = '<a href="#" onclick="window.location.href=\''. $editURL . '\'; return false;"><img'.t3lib_iconWorks::skinImg($this->pObj->doc->backPath,'gfx/edit2.gif','width="11" height="12"').' title="Edit this type" alt="" height="16" width="16"></a>';
			$rowMarkers['DELETE'] = '<a href="#" onclick="deleteRecord(' . $row['uid'] . ')"><img'.t3lib_iconWorks::skinImg($this->pObj->doc->backPath,'gfx/deletedok.gif','width="11" height="12"').' title="Delete this type" alt="" height="16" width="16"></a>';
			$rowMarkers['EXTENSION'] = $row['ext'];
			$rowMarkers['MIME'] = $row['mime'];
			$rowMarkers['ICON'] = '<img src="' . $BACK_PATH . tx_dam::icon_getFileType(array('file_type' => $row['ext'], 'media_type' => $row['type'])) . '" />';
			$rows .= t3lib_parsehtml::substituteMarkerArray($rowTemplate[$alternate], $rowMarkers, '###|###');
			
				// Cycle the alternating rows
			if ($alternate == 2) {
				$alternate = 1;
			} else {
				$alternate = 2;
			}
		}
		
		$content = t3lib_parsehtml::substituteSubpart($content, '###ROWS###', $rows);
		
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		
		return $content;
	}
		

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_mimetypes/class.tx_dam_tools_mimetypes.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_mimetypes/class.tx_dam_tools_mimetypes.php']);
}

?>
