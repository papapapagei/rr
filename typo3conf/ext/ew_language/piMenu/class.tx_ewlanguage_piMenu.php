<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Elio Wahlen <vorname at vorname.de>
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
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(t3lib_extMgm::extPath('ew_pibase') . 'class.tx_ewpibase.php');

/**
 * Plugin 'Language Menu' for the 'ew_language' extension.
 *
 * @author	Elio Wahlen <vorname at vorname.de>
 * @package	TYPO3
 * @subpackage	tx_ewlanguage
 */
class tx_ewlanguage_piMenu extends tx_ewpibase {
	public $prefixId      = 'tx_ewlanguage_piMenu';		// Same as class name
	public $scriptRelPath = 'piMenu/class.tx_ewlanguage_piMenu.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'ew_language';	// The extension key.

	/**
	 * Instance of tx_ewlanguage
	 *
	 * @var	object	tx_ewlanguage
	 */
	protected $ewlanguageLibary = NULL;

	/**
	 * Class constructor.
	 */
	function __construct() {
		parent::__construct();

		if (!class_exists('tx_ewlanguage')) {
			require_once('../lib/class.tx_ewlanguage.php');
		}
		$this->ewlanguageLibary = t3lib_div::makeInstance('tx_ewlanguage');
	}

	/**
	 * Creates a language menu to be displayed on the website
	 *
	 * @param	string	$content: The PlugIn content
	 * @param	array	$conf: The PlugIn configuration
	 *
	 * @return	string	The content that is displayed on the website
	 */
	public function getMenu($content, $conf) {
		if (!$this->ewlanguageLibary->getShowMenu()) {
			return '';
		}

		$languageLink = array();

		foreach($this->ewlanguageLibary->getActiveSysLanguageUids() as $languageUid) {
			if ($languageUid != $GLOBALS['TSFE']->sys_language_uid) {
				$languageLink[] = $this->cObj->getTypoLink($this->ewlanguageLibary->getLanguageTitle($languageUid), $GLOBALS['TSFE']->id, array('L' => $languageUid), '_top');
			}
		}

		return '<div class="language-menu">' . join(' | ', $languageLink) . '</div>';
	}

	/**
	 * Creates an array of URLs for a language menu
	 *
	 * @param	string	$content: The PlugIn content
	 * @param	array	$conf: The PlugIn configuration
	 *
	 * @return	array	An array with the language menu URLs
	 */
	public function getLinkListe($content, $conf) {
		if (!$this->ewlanguageLibary->getShowMenu()) {
			return array();
		}

		$languageLink = array();

		foreach($this->ewlanguageLibary->getActiveSysLanguageUids() as $languageUid) {
			if ($languageUid != $GLOBALS['TSFE']->sys_language_uid) {
				$languageLink[htmlspecialchars($this->ewlanguageLibary->getLanguageTitle($languageUid))] = $this->cObj->getTypoLink_URL($GLOBALS['TSFE']->id, array('L' => $id), '_top');
			}
		}

		return $languageLink;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_language/piMenu/class.tx_ewlanguage_piMenu.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_language/piMenu/class.tx_ewlanguage_piMenu.php']);
}

?>