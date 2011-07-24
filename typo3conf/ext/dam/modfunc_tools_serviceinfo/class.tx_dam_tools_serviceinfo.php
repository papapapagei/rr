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
 * Module extension (addition to function menu) 'Services Info'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Dan Osipov <dosipov@phillyburbs.com>
 * @package DAM-Mod
 * @subpackage tools
 */




require_once(PATH_t3lib.'class.t3lib_extobjbase.php');


/**
 * Module 'Media>Tools>Services Info'
 *
 * @author	Dan Osipov <dosipov@phillyburbs.com> 
 * @package DAM-Mod
 * @subpackage tools
 */
class tx_dam_tools_serviceinfo extends t3lib_extobjbase {


	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()	{



		$content = '';
		$content.=  $this->pObj->getHeaderBar('', t3lib_BEfunc::getFuncMenu($this->pObj->id,'SET[tx_dam_tools_serviceinfo.func]',$this->pObj->MOD_SETTINGS['tx_dam_tools_serviceinfo.func'],$this->pObj->MOD_MENU['tx_dam_tools_serviceinfo.func']));
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
		global  $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS;

		$code='';

		require_once (PATH_txdam.'lib/class.tx_dam_svlist.php');
		$list = t3lib_div::makeInstance('tx_dam_svlist');
		$list->pObj = &$this->pObj;

		$code.= 'Indexing needs the help of some services to extract meta data or read text content from the files.<br /><br />Used service types are:<br />';
		$code.= '<strong>metaExtract</strong> - get meta data from files.<br />';
		$code.= '<strong>textExtract</strong> - get text content out of files.<br />';
		$code.= '<strong>textLang</strong> - detect the language of text content.<br />';

		$code.= $list->serviceTypeList_loaded();

		$code.= 'The "External" column shows which external programs are needed. If a service is not available, it might be the case that the program is not installed or can\'t be executed.<br />';

		$content = $this->pObj->doc->section('Available services for indexing:',$code,0,1);

		$code='';
		$code.= $list->showSearchPaths();
		if($code) {
			$content.= $this->pObj->doc->section('Configured search paths for external programs:',$code,0,1);
		}

		return $content;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_serviceinfo/class.tx_dam_tools_serviceinfo.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_serviceinfo/class.tx_dam_tools_serviceinfo.php']);
}

?>
