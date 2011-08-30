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
 * Module 'Media>List'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   81: class tx_dam_mod_list extends tx_dam_SCbase
 *   91:     function main()
 *  203:     function printContent()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */








unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

$BE_USER->modAccess($MCONF,1);

require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');
require_once(PATH_txdam.'lib/class.tx_dam_guirenderlist.php');

require_once(PATH_txdam.'lib/class.tx_dam_selstorage.php');


$LANG->includeLLFile('EXT:dam/mod_list/locallang.xml');







/**
 * Script class for the DAM record list module
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
class tx_dam_mod_list extends tx_dam_SCbase {


	var $formName = 'editform';

	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS, $HTTP_GET_VARS, $HTTP_POST_VARS;

			// Init guiRenderList object:

		$this->guiItems = t3lib_div::makeInstance('tx_dam_guiRenderList');


		//
		// Initialize the template object
		//

		$this->doc = t3lib_div::makeInstance('template'); 
		$this->doc->backPath = $BACK_PATH;
		$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath('dam') . 'res/templates/mod_list.html');
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
			$this->selection->addDefaultFilter();


				// Store settings gui element
			$this->store = t3lib_div::makeInstance('t3lib_modSettings');
			$this->store->init('tx_dam_select');
			$this->store->type = 'perm';
			$this->store->setStoreList('tx_dam_select');
			$this->store->processStoreControl();


				// Store settings gui element
			$this->selExport = t3lib_div::makeInstance('tx_dam_selStorage');
			$this->selExport->init();
			$this->selExport->processStoreControl();





			//
			// Output page header
			//

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
				//$this->content.= $this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				$this->markers['SHORTCUT'] = $this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']);
			}

			$this->content.= $this->doc->spacer(10);

			$this->markers['CONTENT'] = $this->content;
			$this->markers['LANGUAGE_SELECT'] = isset($this->markers['LANGUAGE_SELECT']) ? $this->markers['LANGUAGE_SELECT'] : '';
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
	 * @return	string		HTML
	 */
	function printContent()	{
		$this->content = $this->doc->insertStylesAndJS($this->content);  
		echo $this->content;
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_list/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_list/index.php']);
}






// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dam_mod_list');
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
