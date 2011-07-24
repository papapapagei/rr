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
 * DAM nav frame.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   68: class tx_dam_navframe
 *   80:     function init()
 *  100:     function jumpTo(params,linkObj,highLightID)
 *  116:     function refresh_nav()
 *  121:     function _refresh_nav()
 *  183:     function main()
 *  216:     function printContent()
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */





if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}
require_once(PATH_txdam.'lib/class.tx_dam.php');
require_once(PATH_txdam.'lib/class.tx_dam_browsetrees.php');


/**
 * Main script class for the tree navigation frame
 *
 * @author	@author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
class tx_dam_navframe {

	var $doc;
	var $content;

		// Internal, static: _GP
	var $currentSubScript;

	var $mainModule ='';


		// Constructor:
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TYPO3_CONF_VARS;

		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath('dam') . 'res/templates/mod_navframe.html');
		$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('dam') . 'res/css/stylesheet.css';
		$this->doc->docType  = 'xhtml_trans';


		$this->currentSubScript = t3lib_div::_GP('currentSubScript');

			// Setting highlight mode:
		$this->doHighlight = !$BE_USER->getTSConfigVal('options.pageTree.disableTitleHighlight');



		$this->doc->JScode='';

			// Setting JavaScript for menu.
		$this->doc->JScode=$this->doc->wrapScriptTags(
			($this->currentSubScript?'top.currentSubScript=unescape("'.rawurlencode($this->currentSubScript).'");':'').'

			function jumpTo(params,linkObj,highLightID)	{
				var theUrl = top.TS.PATH_typo3+top.currentSubScript+"?"+params;

				if (top.condensedMode)	{
					top.content.document.location.href=theUrl;
				} else {
					parent.list_frame.document.location.href=theUrl;
				}
				'.($this->doHighlight?'hilight_row("row"+top.fsMod.recentIds["txdamM1"],highLightID);':'').'
				'.(!$GLOBALS['CLIENT']['FORMSTYLE'] ? '' : 'if (linkObj) {linkObj.blur();}').'
				return false;
			}


				// Call this function, refresh_nav(), from another script in the backend if you want to refresh the navigation frame (eg. after having changed a page title or moved pages etc.)
				// See t3lib_BEfunc::getSetUpdateSignal()
			function refresh_nav()	{
				window.setTimeout("_refresh_nav();",0);
			}


			function _refresh_nav()	{
				document.location.href="'.htmlspecialchars(t3lib_div::linkThisScript(array('unique' => time()))).'";
			}

				// Highlighting rows in the page tree:
			function hilight_row(frameSetModule,highLightID) {	//

					// Remove old:
				theObj = document.getElementById(top.fsMod.navFrameHighlightedID[frameSetModule]);
				if (theObj)	{
					theObj.style.backgroundColor="";
				}

					// Set new:
				top.fsMod.navFrameHighlightedID[frameSetModule] = highLightID;
				theObj = document.getElementById(highLightID);
				if (theObj)	{
					theObj.style.backgroundColor="'.t3lib_div::modifyHTMLColorAll($this->doc->bgColor,-20).'";
				}
			}
		');

		#$CMparts=$this->doc->getContextMenuCode();
		#$this->doc->bodyTagAdditions = $CMparts[1];
		#$this->doc->JScode.=$CMparts[0];
		#$this->doc->postCode.= $CMparts[2];


	}




	/**
	 * Main function, rendering the browsable page tree
	 *
	 * @return	void
	 */
	function main()	{
		global $LANG, $TYPO3_CONF_VARS;
		

		$this->content = '';
		//$this->content.= $this->doc->startPage('Navigation');


			// the trees
		$this->browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
		$this->browseTrees->init(t3lib_div::getIndpEnv('SCRIPT_NAME'));

		$this->content.= $this->browseTrees->getTrees();

		$this->markers['REFRESH'] = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('unique' => uniqid('tx_dam_navframe')))).'">'.
				'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/refresh_n.gif','width="14" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.refresh',1).'" alt="" /></a>';

			// Adding highlight - JavaScript
		if ($this->doHighlight)	$this->content .=$this->doc->wrapScriptTags('
			hilight_row("",top.fsMod.navFrameHighlightedID["web"]);
		');
	}


	/**
	 * Outputting the accumulated content to screen
	 *
	 * @return	void
	 */
	function printContent()	{
		global $LANG;
		// Null out markers:
		$docHeaderButtons = array(
			'new_page' => '',
			'csh' => '',
			'refresh' => '',
		);
		$this->markers['WORKSPACEINFO'] = '';

		$this->markers['CONTENT'] = $this->content;
		$subparts['###SECOND_ROW###'] = ''; 
		$docHeaderButtons['refresh'] = $this->markers['REFRESH'];

		$this->content = $this->doc->startPage($LANG->sL('LLL:EXT:dam/mod_main/locallang_mod.xml:mlang_labels_tablabel',1));
		$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $this->markers, $subparts);
		$this->content.= $this->doc->endPage();

		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}

}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_navframe.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_navframe.php']);
}

?>
