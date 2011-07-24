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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-FeLib
 * @subpackage
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   60: class tx_dam_tsfe
 *
 *              SECTION: TypoScript functions
 *   85:     function fetchFileList ($content, $conf)
 *
 *              SECTION: Misc functions
 *  129:     function initLangObject()
 *  155:     function getFieldLabel($field, $table='tx_dam')
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




/**
 * Provide TSFE functions for usage in own extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-FeLib
 * @subpackage
 */
class tx_dam_tsfe {




	/**********************************************************
	 *
	 * TypoScript functions
	 *
	 **********************************************************/


	/**
	 * Used to fetch a file list for TypoScript cObjects
	 *
	 *	tt_content.textpic.20.imgList >
	 *	tt_content.textpic.20.imgList.cObject = USER
	 *	tt_content.textpic.20.imgList.cObject {
	 *		userFunc = tx_dam_divFe->fetchFileList
	 *
	 * @param	mixed		$content: ...
	 * @param	array		$conf: ...
	 * @return	string		comma list of files with path
	 * @see dam_ttcontent extension
	 */
	function fetchFileList ($content, $conf) {
		$files = array();

		$filePath = $this->cObj->stdWrap($conf['additional.']['filePath'],$conf['additional.']['filePath.']);
		$fileList = trim($this->cObj->stdWrap($conf['additional.']['fileList'],$conf['additional.']['fileList.']));
		$refField = trim($this->cObj->stdWrap($conf['refField'],$conf['refField.']));
		$fileList = t3lib_div::trimExplode(',',$fileList);
		foreach ($fileList as $file) {
			if($file) {
				$files[] = $filePath.$file;
			}
		}


		$uid      = $this->cObj->data['_LOCALIZED_UID'] ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'];
		$refTable = ($conf['refTable'] && is_array($GLOBALS['TCA'][$conf['refTable']])) ? $conf['refTable'] : 'tt_content';

		if (isset($GLOBALS['BE_USER']->workspace) && $GLOBALS['BE_USER']->workspace !== 0) {
			$workspaceRecord = $GLOBALS['TSFE']->sys_page->getWorkspaceVersionOfRecord(
				$GLOBALS['BE_USER']->workspace,
				'tt_content',
				$uid,
				'uid'
			);

			if (is_array($workspaceRecord)) {
				$uid = $workspaceRecord['uid'];
			}
		}

		$damFiles = tx_dam_db::getReferencedFiles($refTable, $uid, $refField);

		$files = array_merge($files, $damFiles['files']);

		return implode(',', $files);
	}



	/**********************************************************
	 *
	 * Misc functions
	 *
	 **********************************************************/


	/**
	 * Creates an instance of 'language' (sysext/lang/lang.php) in $GLOBALS['LANG'] ...
	 * ... if TYPO3_MODE === 'FE' and the object do not exist
	 *
	 * The LANG object will be initialized with the current language used in TSFE.
	 *
	 * It is possible that a LANG object exist when a BE user preview a page.
	 * The language in that object is initialized then with the BE users language (not TSFE).
	 * This function override the language with the TSFE language. This may cause different language labels in the admin panel.
	 *
	 * @return void
	 */
	function initLangObject() {
		global $TYPO3_CONF_VARS;

		if (TYPO3_MODE === 'FE') {
			if (!is_object($GLOBALS['LANG']))	{
				require_once(PATH_site.TYPO3_mainDir.'sysext/lang/lang.php');
				$GLOBALS['LANG'] = t3lib_div::makeInstance('language');
			}

			if ($GLOBALS['TSFE']->sys_language_isocode) {
				$GLOBALS['LANG']->init($GLOBALS['TSFE']->sys_language_isocode);
				if($isoCode = $GLOBALS['LANG']->csConvObj->isoArray[$GLOBALS['TSFE']->sys_language_isocode]) {
					$GLOBALS['LANG']->init($isoCode);
				}
			} else {
				$GLOBALS['LANG']->init($GLOBALS['TSFE']->config['config']['language']);
			}
		}
	}


	/**
	 * Get a language label for a table field
	 * appended ':' will be removed
	 *
	 * @param string $field
	 * @param string $table Default: tx_dam
	 */
	function getFieldLabel($field, $table='tx_dam') {
		global $TCA;

		if (!is_object($GLOBALS['LANG'])) tx_dam_tsfe::initLangObject();

		t3lib_div::loadTCA('tx_dam');

		$label = $TCA[$table]['columns'][$field]['label'];
		$label = $GLOBALS['LANG']->sL($label);
		$label = preg_replace('#:$#', '', $label);
		return $label;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tsfe.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tsfe.php']);
}

?>