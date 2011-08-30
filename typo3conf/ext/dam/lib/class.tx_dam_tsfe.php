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
	function fetchFileList($content, $conf) {
		$files = array();

		$filePath = $this->cObj->stdWrap($conf['additional.']['filePath'], $conf['additional.']['filePath.']);
		$fileList = trim($this->cObj->stdWrap($conf['additional.']['fileList'], $conf['additional.']['fileList.']));
		$refField = trim($this->cObj->stdWrap($conf['refField'], $conf['refField.']));
		$fileList = t3lib_div::trimExplode(',', $fileList);
		foreach ($fileList as $file) {
			if($file) {
				$files[] = $filePath . $file;
			}
		}

			// Get the uid of the current object
		$refUid = $this->cObj->data['_LOCALIZED_UID'] ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'];
			// Override uid, if defined
		if (isset($conf['refUid']) || isset($conf['refUid.'])) {
			$uid = trim($this->cObj->stdWrap($conf['refUid'], $conf['refUid.']));
			if (!empty($uid)) {
				$refUid = $uid;
			}
		}
			// Get the reference table
			// Default is tt_content
		$refTable = 'tt_content';
		if (isset($conf['refTable']) || isset($conf['refTable.'])) {
			$table = trim($this->cObj->stdWrap($conf['refTable'], $conf['refTable.']));
			if (!empty($table) && is_array($GLOBALS['TCA'][$table])) {
				$refTable = $table;
			}
		}

		if (isset($GLOBALS['BE_USER']->workspace) && $GLOBALS['BE_USER']->workspace !== 0) {
			$workspaceRecord = $GLOBALS['TSFE']->sys_page->getWorkspaceVersionOfRecord(
				$GLOBALS['BE_USER']->workspace,
				$refTable,
				$refUid,
				'uid'
			);

			if (is_array($workspaceRecord)) {
				$refUid = $workspaceRecord['uid'];
			}
		}

		$damFiles = tx_dam_db::getReferencedFiles($refTable, $refUid, $refField);

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

	/**
	 * This method hooks into the data submission process to try and assemble a jumpurl
	 * based on a reference to a DAM record passed with the locationData query variable
	 *
	 * @param tslib_fe $parentObject Back-reference to the calling tslib_fe object
	 * @return void
	 */
	public function checkDataSubmission(tslib_fe $parentObject) {
		$locationData = (string)t3lib_div::_GP('locationData');
		if (!empty($locationData)) {
			$locationDataParts = explode(':', $locationData);
				// Three parts are expected: a page id, a table name and a record id
			if (count($locationDataParts) == 3) {
					// Consider only references to the DAM
				if ($locationDataParts[1] == 'tx_dam') {
					$recordId = intval($locationDataParts[2]);
					if (!empty($recordId)) {
							/** @var $media txdam_media */
						$media = tx_dam::media_getByUid($recordId);
							// If the file is indeed available, set its path as the Jump URL
						if ($media->isAvailable) {
							$metaData = $media->getMetaArray();
							$parentObject->jumpurl = $metaData['file_path'] . $metaData['file_name'];

							// If the file is not available, issue error message
						} else {
								// If a hook is declared, call the hook for error handling
							if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['secureDownloadErrorHandler'])) {
								foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['secureDownloadErrorHandler'] as $classReference) {
									$errorHandler = t3lib_div::getUserObj($classReference);
									$errorHandler->handleDownloadError($locationDataParts, $parentObject);
								}

								// In the absence of hooks, just print a standard error message and exit the process
							} else {
									// Instantiate local language object
									/** @var $languageObject language */
								$languageObject = t3lib_div::makeInstance('language');
									// Get language code, if defined
								if (!empty($GLOBALS['TSFE']->config['config']['language'])) {
									$languageObject->lang = $GLOBALS['TSFE']->config['config']['language'];
									if (!empty($GLOBALS['TSFE']->config['config']['language_alt'])) {
										$languageObject->lang = $GLOBALS['TSFE']->config['config']['language_alt'];
									}
								}
								$parentObject->printError($languageObject->sL('LLL:EXT:dam/lib/locallang.xml:downloadFailed'), $languageObject->sL('LLL:EXT:dam/lib/locallang.xml:downloadError'));
								exit;
							}
						}
					}
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tsfe.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tsfe.php']);
}

?>