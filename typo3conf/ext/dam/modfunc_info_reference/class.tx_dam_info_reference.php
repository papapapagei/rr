<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2008 Rene Fritz (r.fritz@colorcube.de)
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
 * Module extension (addition to function menu) 'References' for the 'Media>Info' module.
 * Part of the DAM (digital asset management) extension.
 * 
 * This module lists all references to a file in media>info
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @author	David Steeb <david@b13.de>
 * @package TYPO3
 * @subpackage tx_dam
 */

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(PATH_txdam.'lib/class.tx_dam_listreferences.php');
require_once (PATH_txdam.'lib/class.tx_dam_iterator_references.php');

/**
 * Module 'Media>Info>References'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @author	David Steeb <david@b13.de>
 */
class tx_dam_info_reference extends t3lib_extobjbase {

	
	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu() {
		return array(
			'tx_dam_info_reference_sortField' => '',
			'tx_dam_info_reference_sortRev' => '',
			'tx_dam_info_reference_showRootline' => 1,
		);
	}

	/**
	 * Do some init things
	 *
	 * @return	void
	 */
	function head() {
			// Init gui items
		$this->pObj->guiItems->registerFunc('getResultInfoBar', 'header');
		$this->pObj->guiItems->registerFunc('getOptions', 'footer');
			// add some options
		$this->pObj->addOption('funcCheck', 'tx_dam_info_reference_showRootline', $GLOBALS['LANG']->getLL('showRootline'));
	}

	
	/**
	 * Main function to render the reference for DAM records
	 *
	 * @return	string		HTML output
	 */
	function main() {
			// Create reference listing object
		$referenceList = t3lib_div::makeInstance('tx_dam_listreferences');
		$referenceList->displayColumns = array('page', 'content_element', 'content_age', 'media_element', 'media_element_age');
		$referenceList->showRootline = $this->pObj->MOD_SETTINGS['tx_dam_info_reference_showRootline'];
		$referenceList->init();
			// Build the reference entries array
		$references = t3lib_div::makeInstance('tx_dam_iterator_references');
		$references->read($this->pObj->selection, $referenceList->displayColumns);
		$references->sort($this->pObj->MOD_SETTINGS['tx_dam_info_reference_sortField'], $this->pObj->MOD_SETTINGS['tx_dam_info_reference_sortRev']);
			// Any references found?
		if ($this->pObj->selection->pointer->countTotal) {
			$referenceList->setParameterName('form', $this->pObj->formName);
				// Enable context menus
			$referenceList->enableContextMenus = $this->pObj->config_checkValueEnabled('contextMenuOnListItems', true);
			$referenceList->addData($references, 'references');
			$referenceList->setCurrentSorting($this->pObj->MOD_SETTINGS['tx_dam_info_reference_sortField'], $this->pObj->MOD_SETTINGS['tx_dam_info_reference_sortRev']);
			$referenceList->setParameterName('sortField', 'SET[tx_dam_info_reference_sortField]');
			$referenceList->setParameterName('sortRev', 'SET[tx_dam_info_reference_sortRev]');
			$referenceList->setPointer($this->pObj->selection->pointer);
				// Render list
			$list = $referenceList->getListTable();
		} else {
				// No search result: showing selection box
			$list = $this->pObj->doc->section('',$this->pObj->getCurrentSelectionBox(),0,1);
		}
			// Output header: info bar, result browser
		$content = $this->pObj->guiItems->getOutput('header');
		$content .= $this->pObj->doc->spacer(10);
		$content .= $list;
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_info_reference/class.tx_dam_info_reference.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_info_reference/class.tx_dam_info_reference.php']);
}

?>