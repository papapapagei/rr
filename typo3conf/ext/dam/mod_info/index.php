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
 * Module 'Media>Info'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage info
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   70: class tx_dam_mod_info extends tx_dam_SCbase
 *   79:     function main()
 *  169:     function printContent()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');
require_once(PATH_txdam.'lib/class.tx_dam_guirenderlist.php');

$LANG->includeLLFile('EXT:dam/mod_info/locallang.xml');


$BE_USER->modAccess($MCONF,1);


/**
 * Script class for the DAM info module
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage info
 */
class tx_dam_mod_info extends tx_dam_SCbase {



	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS;

			// Init guiRenderList object:

		$this->guiItems = t3lib_div::makeInstance('tx_dam_guiRenderList');


		// Initialize the template object

		$this->doc = t3lib_div::makeInstance('template'); 
		$this->doc->backPath = $BACK_PATH;
		$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath('dam') . 'res/templates/mod_info.html');
		$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('dam') . 'res/css/stylesheet.css';
		$this->doc->docType = 'xhtml_trans';


		// Access check can not be checked in beforehand
		// The user has access when he has acces to the module and that's nothing to be checked here
		$access = true;

		// **************************
		// Main
		// **************************
		if ($access)	{

			$this->selection->processSubmittedSelection();

			//
			// Output page header
			//

			$this->doc->form = $this->getFormTag();

			$this->addDocStyles();
			$this->addDocJavaScript();


			$this->extObjHeader();

			//
			// Output tabmenu if not a single function was forced
			//

			if (!$this->forcedFunction AND count($this->MOD_MENU['function'])>1) {
				$this->markers['FUNC_MENU'] = $this->getTabMenu($this->addParams,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function']);
			}
			else {
				$this->markers['FUNC_MENU'] = '';
			}

			//
			// Call submodule function
			//

			$this->extObjContent();


			//
			// output footer: search box, options, store control, ....
			//

			$this->content.= $this->doc->spacer(10);
			$this->content.= $this->guiItems->getOutput('footer');


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->markers['SHORTCUT'] = $this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']);
			}

			$this->content.= $this->doc->spacer(10);

			$this->markers['CONTENT'] = $this->content;
			$docHeaderButtons = array(
				'VIEW' => $this->markers['VIEW'],
				'RECORD_LIST' => $this->markers['RECORD_LIST'],
				'SHORTCUT' => $this->markers['SHORTCUT'],
			);
			$this->markers['CSH'] = ''; // TODO
				// Build the <body> for the module
			$this->content = $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $this->markers);
			$this->content.= $this->doc->endPage();

		} else {
				// If no access
			$this->content.= $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->header($LANG->getLL('title'));
			$this->content.= $this->doc->spacer(5);
			$this->content.= $this->doc->section('', $LANG->sL('LLL:EXT:lang/locallang_mod_web_perm.xml:A_Denied',1));
			$this->content.= $this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	string	HTML
	 */
	function printContent()	{
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_info/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_info/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dam_mod_info');
$SOBE->init();

// Include files?
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{include_once($INC_FILE);}
$SOBE->checkExtObj();	// Checking for first level external objects

// Repeat Include files! - if any files has been added by second-level extensions
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{include_once($INC_FILE);}
$SOBE->checkSubExtObj();	// Checking second level external objects


$SOBE->main();
$SOBE->printContent();

?>
