<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2007 Rene Fritz (r.fritz@colorcube.de)
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   17: class tx_dam_show_item
 *   29:     function isValid($type, &$pObj)
 *   46:     function render($type, &$pObj)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


class tx_dam_show_item {

	var $meta;


	/**
	 * Check if this object should render
	 *
	 * @param	string		Type: "file"
	 * @param	object		Parent object.
	 * @return	boolean
	 */
	function isValid($type, &$pObj)	{
		$isValid = false;

		if($type === 'file' && is_array($this->meta = tx_dam::meta_getDataForFile($pObj->file, '*'))) {
			$isValid = true;
		}
		return $isValid;
	}


	/**
	 * Rendering
	 *
	 * @param	string		Type: "file"
	 * @param	object		Parent object.
	 * @return	string		Rendered content
	 */
	function render($type, &$pObj)	{
		global $LANG, $TCA, $BACK_PATH, $TYPO3_CONF_VARS;

		$contentForm = '';

		$row = $this->meta;
			// convert row data for tceforms
		$trData = t3lib_div::makeInstance('t3lib_transferData');
		$trData->lockRecords = false;
		$trData->disableRTE = true;
		$trData->renderRecord('tx_dam', $row['uid'], $row['pid'], $row);
		reset($trData->regTableItems_data);
		$row = current($trData->regTableItems_data);
		$row['uid'] = $this->meta['uid'];
		$row['pid'] = $this->meta['pid'];

			// create form
		require_once (PATH_txdam.'lib/class.tx_dam_simpleforms.php');
		$form = t3lib_div::makeInstance('tx_dam_simpleForms');
		$form->initDefaultBEmode();
		$form->enableTabMenu = TRUE;
		$form->setNewBEDesignOrig();
		$form->setVirtualTable('tx_dam_simpleforms', 'tx_dam');
		$form->removeRequired();
		$form->setNonEditable($TCA['tx_dam']['txdamInterface']['info_displayFields_isNonEditable']);
		$columnsExclude = t3lib_div::trimExplode(',', $TCA['tx_dam']['txdamInterface']['info_displayFields_exclude'], 1);
		foreach ($columnsExclude as $column) {
			unset($TCA['tx_dam_simpleforms']['columns'][$column]);
		}
		$contentForm.= $form->getForm($row);
		$contentForm = $form->wrapTotal($contentForm, $this->meta /* raw */, 'tx_dam');
		$form->removeVirtualTable('tx_dam_simpleforms');

			// Initialize document template object:
		$pObj->doc = t3lib_div::makeInstance('template');
		$pObj->doc->backPath = $GLOBALS['BACK_PATH'];
		$pObj->doc->divClass = 'typo3-mediumDoc';
		$pObj->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('dam') . 'res/css/stylesheet.css';
		$pObj->doc->JScode = $pObj->doc->getDynTabMenuJScode();

		$pObj->doc->JScodeArray['changeWindowSize'] = '
		function _resizeWindow() {
			window.resizable=true;
			width = document.getElementById(\'wrapall\').offsetWidth;
			width += 50;
			if (width < 700) {
				width = 700;
			}
			window.resizeTo(width,490);
		}';
		$pObj->doc->bodyTagAdditions .= ' onload="_resizeWindow();"';

			// Starting the page by creating page header stuff:
		$content.= $pObj->doc->startPage($LANG->sL('LLL:EXT:lang/locallang_core.xml:show_item.php.viewItem'));
		$content.= '<div id="wrapall">';
		$content.= $pObj->doc->spacer(5);


		$content.= $pObj->doc->section('',$contentForm);
		$content.= '</div>';

		return $content;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/be/class.tx_dam_show_item.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/be/class.tx_dam_show_item.php']);
}
?>