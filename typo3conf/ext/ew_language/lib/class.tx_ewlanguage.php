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

/**
 * Libary 'Automatic language configuration' for the 'ew_language' extension.
 *
 * @author	Elio Wahlen <vorname at vorname.de>
 * @package	TYPO3
 * @subpackage	tx_ewlanguage
 */
class tx_ewlanguage implements t3lib_Singleton {
	public $prefixId      = 'tx_ewlanguage';		// Same as class name
	public $scriptRelPath = 'lib/class.tx_ewlanguage.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'ew_language';	// The extension key.

	/**
	 * The extension config array
	 *
	 * @var	array	The extension config array
	 */
	protected $extensionConfig = array(
			// The ISO code from the default language (to get the infos from static_info_tables and define it at config.language)
		'defaultLanguageIsoCode' => 'EN',
			// Is the default language disabled in menu
		'defaultLanguageDisabledInMenu' => 0,
			// Is the default language encoded as get var in realurl (this works only with a custom hacked tx_realurl class, patch is submitted to realurl developers!)
		'defaultLanguageRealurlEncode' => 0,
			// Redirect to the preferred browser language, if nothing is set (works only, if 'defaultLanguageRealurlEncode' or 'defaultLanguageDisabledInMenu' is set!)
		'redirectToPreferredBrowserLanguage' => 0,
			// config.sys_language_mode, see typo3 documentation
		'sys_language_mode' => 'content_fallback',
			// config.sys_language_overlay, see typo3 documentation
		'sys_language_overlay' => 'hideNonTranslated',
	);

	/**
	 * Array of installed languages from db table 'sys_languages', with 0 => default language from $this->extensionConfig
	 *
	 * @var	array	The installed languages
	 */
	protected $sysLanguages = array();

	/**
	 * Array of active language uids
	 *
	 * @var	array	The active languages uids
	 */
	protected $activeSysLanguageUids = array();

	/**
	 * The local stored language uid, if this class is used before $GLOBALS['TSFE']->sys_language_uid is initialised
	 *
	 * @var	string	The local stored language uid
	 */
	protected $localLanguageUid = 0;

	/**
	 * Should the menu be shown
	 * Set to TRUE, if more then one language is installed and not disabled in menu
	 *
	 * @var	boolean	Should the menu be shown
	 */
	protected $showMenu = FALSE;

	/**
	 * The uid from sys_languages of the preferred browser language
	 *
	 * @var	int		The uid from sys_languages of the preferred browser language
	 */
	protected $preferredBrowserLanguage = 0;

	/**
	 * The name of the language get var (default = 'L')
	 *
	 * @var	string	The language get var
	 */
	protected $languageGetVar = 'L';

	/**
	 * Indicates, if the name of the languageGetVar was read from realurl
	 *
	 * @var	boolean	Does the languageGetVar was read from realurl
	 */
	protected $languageGetVarReadFromRealurl = FALSE;

	/*
	 * Class constructor.
	 */
	function __construct() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ew_language'])) {
			$this->extensionConfig = array_merge($this->extensionConfig, $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ew_language']);
		}
		else {
			$tempExtensionConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ew_language']);
			if (is_array($tempExtensionConfig)) {
				$this->extensionConfig = array_merge($this->extensionConfig, $tempExtensionConfig);
			}
		}

		$this->getSysLanguages();
		$this->activeSysLanguageUids = array_filter($this->getSysLanguageUids(), array($this, 'isLanguageActiveInMenu'));
		$this->preferredBrowserLanguage = $this->getBrowserLanguage();

		$this->showMenu = (boolean) (count($this->getActiveSysLanguageUids()) > 1);

		$this->getLanguageGetVarFromRealurlConfig();
	}

	/**
	 * This function is a wrapper for de-/encoding the language get variable for realurl
	 *
	 * The function is called from $TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['preVars'][]['userFunc']
	 *
	 * Decoding: 
	 * @see $this->realurlLanguagePrevarDecode($params, $parentObject)
	 *
	 * Encoding:
	 * @see $this->realurlLanguagePrevarEncode($params, $parentObject)
	 *
	 * @param	string	$params: The realurl params
	 * @param	array	$parentObject: The parent realurl object
	 *
	 * @return	mixed	The matching uid(decoding)/T3-language-key(encoding) from $this->sysLanguages, or default language uid(decoding)/NULL(encoding)
	 */
	public function getRealurlLanguagePrevar($params, $parentObject) {
		$returnLanguageValue = NULL;

		if ($params['decodeAlias']) {
			$returnLanguageValue = $this->realurlLanguagePrevarDecode($params, $parentObject);
		} else {
			$returnLanguageValue = $this->realurlLanguagePrevarEncode($params, $parentObject);
		}

		return $returnLanguageValue;
	}

	/**
	 * This function decodes the language get variable for realurl
	 *
	 * It tries to evaluate the realurl preVars and returns the matching uid from $this->sysLanguages.
	 * If no matching value in $this->sysLanguages is found, it calls $this->getBestMatchingLanguageRedirect()
	 * It will bypass (like realurl 'noMatch' = 'bypass') if nothing is found
	 *
	 * @param	string	$params: The realurl params
	 * @param	array	$parentObject: The parent realurl object
	 *
	 * @return	mixed	The matching uid from $this->sysLanguages, or default language uid (if not redirected ...)
	 */
	function realurlLanguagePrevarDecode($params, $parentObject) {
		$decodedLanguageValue = NULL;

		if (!empty($params['value'])) {
			foreach ($this->getSysLanguageUids() as $languageUid) {
				if ($this->getLanguageT3($languageUid) === $params['value']) {
					$decodedLanguageValue = $languageUid;
					break;
				}
			}
		}

		if ($decodedLanguageValue === NULL) {
			$decodedLanguageValue = $this->getBestMatchingLanguageRedirect($parentObject->pObj->siteScript);

				// 'noMatch' = 'bypass' (as defined in realurl) so the path don't get corrupt
			array_unshift($params['pathParts'], $params['origValue']);
		}

		$this->localLanguageUid = $decodedLanguageValue;

		$this->writeLanguageTSConfig();

		return $decodedLanguageValue;
	}

	/**
	 * This function encodes the language get variable for realurl
	 *
	 * It returns the language key (e.g. de), only if more then one language is activated and
	 * if it is the default language, only if $this->extensionConfig['defaultLanguageRealurlEncode'] is TRUE
	 * Else it returns NULL for realurl 'noMatch' = 'bypass' functionality
	 * ATTENTION: At the moment returning NULL to bypass works only with a custom hacked tx_realurl class, patch is submitted to realurl developers!
	 *
	 * @param	string	$params: The realurl params
	 * @param	array	$parentObject: The parent realurl object
	 *
	 * @return	mixed	The matching T3-language-key from $this->sysLanguages, or NULL
	 */
	function realurlLanguagePrevarEncode($params, $parentObject) {
		$encodedLanguageValue = NULL;

		if ($params['value'] != 0 || $this->extensionConfig['defaultLanguageRealurlEncode']) {
			$linkLanguageUid = (t3lib_div::testInt($params['value']) ? $params['value'] : $this->preferredBrowserLanguage);
			$encodedLanguageValue = $this->getLanguageT3($linkLanguageUid);
		}

		return $encodedLanguageValue;
	}

	/**
	 * This function checks, if the site script is empty, so we don't go into realurl
	 * If this is TRUE, it calls $this->getBestMatchingLanguageRedirect()
	 *
	 * The function is called by the HOOK ['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc']
	 *
	 * @param	string	$params: The TSFE params
	 * @param	array	$parentObject: The TSFE object not referenced!
	 *
	 * @return	void
	 */
	public function setPreferredBrowserLanguage($params, $parentObject) {
		if (empty($params['pObj']->siteScript) || $params['pObj']->siteScript == '/') {
			$this->localLanguageUid = $this->getBestMatchingLanguageRedirect($params['pObj']->siteScript);

			$params['pObj']->mergingWithGetVars(array($this->languageGetVar => $this->localLanguageUid));

			$this->writeLanguageTSConfig();
		}
	}

	/**
	 * This function checks, if a language get var is given.
	 * If not, it will set the default (browser) language: $this->preferredBrowserLanguage
	 * and redirect if $this->extensionConfig['redirectToPreferredBrowserLanguage'] is TRUE.
	 *
	 * @param	string	$siteScript: The site script for redirect (if set)
	 *
	 * @return	int		The first/best matching language uid (if no redirect is done)
	 */
	function getBestMatchingLanguageRedirect($siteScript) {
		$this->getLanguageGetVarFromRealurlConfig();

		$tempGetVar = t3lib_div::_GET($this->languageGetVar);
		if (!t3lib_div::testInt($tempGetVar) || !in_array($tempGetVar, $this->getActiveSysLanguageUids())) {
			$tempGetVar = $this->preferredBrowserLanguage;

				// redirect and exit, if configured
			if (($this->extensionConfig['defaultLanguageRealurlEncode'] || $this->extensionConfig['defaultLanguageDisabledInMenu']) &&
					$this->extensionConfig['redirectToPreferredBrowserLanguage']) {
				t3lib_utility_Http::redirect($this->getLanguageT3($tempGetVar) . '/' . $siteScript);
				exit();
			}
		}

		return $tempGetVar;
	}

	/**
	 * This function tries to read the name of the languageGetVar from realurl and stores it in $this->languageGetVar
	 *
	 * @return	void
	 */
	function getLanguageGetVarFromRealurlConfig() {
		if ($this->languageGetVarReadFromRealurl) {
			return;
		}

		$dummy = array();
		$realurlExtensionConfig = t3lib_div::callUserFunction('EXT:realurl/class.tx_realurl.php:&tx_realurl->getConfiguration', $dummy, $this);
		if (is_array($realurlExtensionConfig) && isset($realurlExtensionConfig['pagePath']['languageGetVar'])) {
			$this->languageGetVar = $realurlExtensionConfig['pagePath']['languageGetVar'];
			$this->languageGetVarReadFromRealurl = TRUE;
		}
	}

	/**
	 * This function returns the value of a internal function, defined by $params['functionArgument']
	 * It's for use in TS := parse argument
	 *
	 * @param	string	$params: The TS parse arguments
	 * @param	array	$parentObject: FALSE, just a dummy object
	 *
	 * @return	string	The return value of the requested function
	 */
	public function getLanguageTSConfig($params, $parentObject) {
		$return = '';
		$allowedFunctions = array('getLanguageLinkVarRange');
		if (in_array($params['functionArgument'], $allowedFunctions)) {
			$return = $this->$params['functionArgument']($params);
		}
		return $return;
	}

	/**
	 * This function adds the typoscript parameters for languages
	 *
	 * @return	void
	 */
	public function writeLanguageTSConfig() {
		$this->getLanguageGetVarFromRealurlConfig();

			// define typoscript config parameters
		$typoScriptSetup = 'config.linkVars := getLanguageTSConfig(getLanguageLinkVarRange)' . "\n"; // @TODO what about other configs?

		$typoScriptConfigArray = array(
			'sys_language_mode' => $this->extensionConfig['sys_language_mode'], // @TODO fallback order of laguages
			'sys_language_overlay' => $this->extensionConfig['sys_language_overlay'],
			'sys_language_uid' => $this->localLanguageUid,
			'language' => $this->getLanguageT3($this->localLanguageUid),
			'htmlTag_langKey' => $this->getLanguageIso($this->localLanguageUid),
			'locale_all' => $this->getLocaleAll($this->localLanguageUid),
			);

		foreach ($typoScriptConfigArray as $configKey => $configValue) {
			$typoScriptSetup .= 'config.' . $configKey . '=' . $configValue . "\n";
		}

		foreach ($this->getSysLanguageUids() as $languageUid) {
			if ($languageUid > 0) {
				$typoScriptSetup .= '[globalVar = GP:' . $this->languageGetVar . ' = ' . $languageUid . ']' . "\n";
				$typoScriptSetup .= '[GLOBAL]' . "\n";
			}
		}

		t3lib_extMgm::addTypoScript($this->extKey, 'setup', $typoScriptSetup, 43);
	}

	/**
	 * returns the TYPO3 language string (e.g. 'en') for a given language uid
	 *
	 * @param	int		$languageUid: a language uid
	 *
	 * @return	string	The TYPO3 language string, empty if not found
	 */
	public function getLanguageT3($languageUid) {
		return (string) (!empty($this->sysLanguages[$languageUid]['lg_typo3']) ? $this->sysLanguages[$languageUid]['lg_typo3'] : strtolower($this->sysLanguages[$languageUid]['lg_iso_2']));
	}

	/**
	 * returns the (lowercase) language iso code for a given language uid
	 *
	 * @param	int		$languageUid: a language uid
	 *
	 * @return	string	The (lowercase) language iso code, empty if not found
	 */
	public function getLanguageIso($languageUid) {
		return (string) strtolower($this->sysLanguages[$languageUid]['lg_iso_2']);
	}

	/**
	 * returns the language locale_all string (e.g. 'en_US') for a given language uid
	 *
	 * @param	int		$languageUid: a language uid
	 *
	 * @return	string	The language locale_all string, empty if not found
	 */
	public function getLocaleAll($languageUid) {
		return (string) $this->sysLanguages[$languageUid]['lg_collate_locale'];
	}

	/**
	 * returns the local name of a language for a given language uid
	 *
	 * @param	int		$languageUid: a language uid
	 *
	 * @return	string	The language name, empty if not found
	 */
	public function getLanguageTitle($languageUid) {
		return (string) $this->sysLanguages[$languageUid]['lg_name_local'];
	}

	/**
	 * checks if a language is activated in menu
	 *
	 * @param	int		$languageUid: a language uid
	 *
	 * @return	boolean	FALSE if the language is disabled in menu
	 */
	public function isLanguageActiveInMenu($languageUid) {
		return (boolean) !$this->sysLanguages[$languageUid]['disabled_in_menu'];
	}

	/**
	 * returns the uids from all installed languages
	 *
	 * @return	array	the uids from all installed languages
	 */
	public function getSysLanguageUids() {
		return array_keys($this->sysLanguages);
	}

	/**
	 * returns the uids from all active languages
	 *
	 * @return	array	the uids from all active languages
	 */
	public function getActiveSysLanguageUids() {
		return $this->activeSysLanguageUids;
	}

	/**
	 * returns the value of $this->showMenu
	 *
	 * @return	boolean	TRUE if the menu should be shown
	 */
	public function getShowMenu() {
		return $this->showMenu;
	}

	/**
	 * returns the config.linkVars value added with allowed range for languageGetVar (e.g. L) values
	 *
	 * @param	string	$params: The TS parse arguments
	 *
	 * @return	string	The TYPO3 language string, empty if not found
	 */
	protected function getLanguageLinkVarRange(&$params) {
		$linkVarsArray = array();

			// look for other defined linkVars
		if (!empty($params['currentValue']) && $params['currentValue'] != $this->languageGetVar) {
			$linkVarsArray = t3lib_div::trimExplode(',', $params['currentValue']);
				// remove $this->languageGetVar from linkVarsArray
			$linkVarsArray = array_filter($linkVarsArray, array($this, 'isValueNotEqualLanguageGetVar'));
		}

		$linkVarsValuesArray = array();
		foreach ($this->getSysLanguageUids() as $languageUid) {
			if ($languageUid != 0 || $this->extensionConfig['defaultLanguageRealurlEncode']) {
				$linkVarsValuesArray[] = $languageUid;
			}
		}
		if (count($linkVarsValuesArray)) {
			$linkVarsArray[] = $this->languageGetVar . '(' . implode('|', $linkVarsValuesArray) . ')';
		}

		return implode(',', $linkVarsArray);
	}

	/**
	 * helper function to compare a value with the defined languageGetVar
	 *
	 * @param	string	$value: The value to compare
	 *
	 * @return	boolean	TRUE, if value is NOT equal to the $this->languageGetVar
	 */
	protected function isValueNotEqualLanguageGetVar($value) {
		return ($value !== $this->languageGetVar);
	}

	/**
	 * checks if a browser language string is given and returns the first/best matching language uid from $this->sysLanguages
	 * if nothing is found, it returns the first entry from $this->getActiveSysLanguageUids()
	 *
	 * @return	int		The first/best matching language uid
	 */
	protected function getBrowserLanguage() {
		$browserLanguage = t3lib_div::getIndpEnv('HTTP_ACCEPT_LANGUAGE');
		$tmpLanguageList = array();

		$returnLanguageUid = 0;
		if (!empty($browserLanguage)) {
			$browserLanguageOrder = array();

			foreach (explode(',', $browserLanguage) as $preferredLanguage) {
					// split the preferred language infos
				$infoArray = explode(';', $preferredLanguage);
				$q = substr($infoArray[1], strpos($infoArray[1], '=') + 1);
				$browserLanguageOrder[$infoArray[0]] = empty($q) ? '1' : $q;
			}
				// sort by preference
			arsort($browserLanguageOrder, SORT_NUMERIC);

			foreach ($browserLanguageOrder as $browserLanguageKey => $preference) {
					// check if it is an active language
				foreach ($this->getActiveSysLanguageUids() as $languageUid) {
					if ($this->getLanguageIso($languageUid) == strtolower(substr($browserLanguageKey, 0, 2))) {
						$tmpLanguageList[$languageUid] = $browserLanguageKey;
					}
				}
			}

				// if any language fitted, take the first from the list
			if (count($tmpLanguageList)) {
				reset($tmpLanguageList);
				$returnLanguageUid = key($tmpLanguageList);
			} else {
				$returnLanguageUid = current($this->getActiveSysLanguageUids());
			}
		}

		return $returnLanguageUid;
	}

	/**
	 * This function gets the langauge informations from the database and stores it into the class variable $this->sysLanguages
	 *
	 * @return	void
	 */
	protected function getSysLanguages() {
		$this->sysLanguages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			"'0' as uid, '' as title, '' as flag, '" . $this->extensionConfig['defaultLanguageDisabledInMenu'] . "' as disabled_in_menu, lg_iso_2, lg_typo3, lg_collate_locale, lg_name_en, lg_name_local", // select fields
			"static_languages", // from table
			"lg_iso_2 = " . $GLOBALS['TYPO3_DB']->fullQuoteStr(strtoupper($this->extensionConfig['defaultLanguageIsoCode']), 'static_languages'), // where
			"", // group by
			"uid", // order by
			"", // limit
			"uid"); // index field

		if (!count($this->sysLanguages)) {
			throw new InvalidArgumentException('\'' . $this->extensionConfig['defaultLanguageIsoCode'] . '\' is not a valid language ISO code from static_languages', 1311682841);
		}

		$this->sysLanguages = array_merge($this->sysLanguages,
			$GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				"sy.uid, sy.title, sy.flag, sy.disabled_in_menu, st.lg_iso_2, st.lg_typo3, st.lg_collate_locale, st.lg_name_en, st.lg_name_local", // select fields
				"sys_language as sy INNER JOIN static_languages as st on sy.static_lang_isocode = st.uid", // from table
				"sy.hidden = 0", // where
				"", // group by
				"sy.uid", // order by
				"", // limit
				"uid")); // index field
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_language/lib/class.tx_ewlanguage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_language/lib/class.tx_ewlanguage.php']);
}

?>