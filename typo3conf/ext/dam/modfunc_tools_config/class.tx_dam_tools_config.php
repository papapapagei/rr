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
 * Module extension (addition to function menu) 'Media>Tools>Configuration'
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
 *   56: class tx_dam_tools_config extends t3lib_extobjbase
 *   64:     function modMenu()
 *   80:     function main()
 *   96:     function moduleContent()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_tsparser_ext.php');


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');


/**
 * Module 'Media>Tools>Configuration'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_tools_config extends t3lib_extobjbase {

	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()	{
		global $LANG;

		$modMenuAdd = array(
			'tx_dam_tools_config.func' => array(
				'merged' => $LANG->getLL('tx_dam_tools_config.merged'),
				'all' => $LANG->getLL('tx_dam_tools_config.all'),
			)
		);

		if (!$GLOBALS['BE_USER']->isAdmin())	unset($modMenuAdd['tsconf_parts'][99]);
		return $modMenuAdd;
	}


	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()	{
		global $LANG;

		$content = '';
		$content.=  $this->pObj->getHeaderBar('', $LANG->getLL('tx_dam_tools_config.showConfig').t3lib_BEfunc::getFuncMenu($this->pObj->id,'SET[tx_dam_tools_config.func]',$this->pObj->MOD_SETTINGS['tx_dam_tools_config.func'],$this->pObj->MOD_MENU['tx_dam_tools_config.func']));
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


		
		require_once(PATH_txdam.'lib/class.tx_dam_config.php');
		tx_dam_config::init(true);
		$config = tx_dam_config::_getConfig();
		if (!is_array($config))	$config = array();
		unset($config['pageUserTSconfig.']);
		
		if ($this->pObj->MOD_SETTINGS['tx_dam_tools_config.func']==='merged') {
			$config = $config['mergedTSconfig.'];
		}


		$tmpl = t3lib_div::makeInstance('t3lib_tsparser_ext');
		$tmpl->tt_track = 0;	// Do not log time-performance information

		$tmpl->fixedLgd = 0;
		$tmpl->linkObjects = 0;
		$tmpl->bType = '';
		$tmpl->ext_expandAllNotes = 1;
		$tmpl->ext_noPMicons = 1;


		$content = '';

		$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_config.title'), '',0,1);
		$content.= $this->pObj->doc->spacer(10);

		$content.= 	'

					<!-- DAM TSconfig Tree: -->
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td nowrap="nowrap">'.$tmpl->ext_getObjTree($config,'','').'</td>
						</tr>
					</table>';

		$content .= '
			<br /><br /><hr />
			<h4>Legend:</h4>
			<ul>
			<li><strong>mergedTSconfig</strong> Merged configuration fetched from different sources. This is used by the system.</li>
			<li><strong>definedTSconfig</strong> Configuration set by PHP with tx_dam::config_setValue().</li>
			<li><strong>userTSconfig</strong> User TSconfig</li>
			<li><strong>pageTSconfig</strong> Page TSconfig</li>
			<li><strong>*.setup</strong> <em>setup</em> maps to <em>tx_dam</em> from TSconfig</li>
			</ul>';			
			
		return $content;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_config/class.tx_dam_tools_config.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_config/class.tx_dam_tools_config.php']);
}

?>