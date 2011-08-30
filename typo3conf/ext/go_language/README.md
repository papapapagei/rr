# General

This extension needs to have `realurl` and `static_info_tables` installed.

---

## Config Parameters

This extension looks in the database for language entries in `sys_languages` combines it with `static_languages` and sets:

* the language getVar (defined in realurl config array ['pagePath']['languageGetVar'] or default 'L')

* and the following config parameters:

    * `config.linkVars` = `L({list of numbers from activated languages e.g. 0|1|3|7})`

    * `config.language` = `static_languages.lg_typo3 { OR lowercase( static_languages.lg_iso_2 ) }`
    * `config.htmlTag_langKey` = `lowercase( static_languages.lg_iso_2 )`
    * `config.locale_all` = `static_languages.lg_collate_locale`

    * `config.sys_language_mode` = `$extensionConfig['sys_language_mode']`
    * `config.sys_language_overlay` = `$extensionConfig['sys_language_overlay']`

---

## Settings

The following settings has to be done to use the automatic set of the previous parameters:

**realurl\_conf.php:**

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'] = array(
		'_DEFAULT' => array(
			'pagePath' => array(
				'languageGetVar' => 'L',
			),
			'preVars' => array (
				0 => array (
					'GETvar' => 'L',
					'userFunc' => 'EXT:go_language/lib/class.tx_golanguage.php:&tx_golanguage->getRealurlLanguagePrevar',
				),
			),
		),
	);

**ext_localconf.php:**

	$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY] = array(
		'defaultLanguageIsoCode' => 'DE',
		'defaultLanguageDisabledInMenu' => 0,
		'defaultLanguageRealurlEncode' => 1,
		'redirectToPreferredBrowserLanguage' => 1,
		'sys_language_mode' => 'content_fallback',
		'sys_language_overlay' => 'hideNonTranslated',
	);

	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc']['tx_golanguage'] =
		'EXT:go_language/lib/class.tx_golanguage.php:&tx_golanguage->setPreferredBrowserLanguage';

	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tsparser.php']['preParseFunc']['getLanguageTSConfig'] =
		'EXT:go_language/lib/class.tx_golanguage.php:&tx_golanguage->getLanguageTSConfig';

---

## Frontend

This extension has a frontend plugin class ('piMenu/class.tx\_golanguage\_piMenu.php') with two functions to create a language menu or a language-link array.
This functions will work without the automatic settings.

---

## realurl

**ATTENTION:**

**I had to do a realurl hack to have the functionality I needed for our purposes. The following patch has to be applied, if a new Version of realurl is installed:**

### realurl patch

	diff --git a/typo3/ext/realurl/class.tx_realurl.php b/typo3/ext/realurl/class.tx_realurl.php
	index faa5304..b58aeb2 100644
	--- a/typo3/ext/realurl/class.tx_realurl.php
	+++ b/typo3/ext/realurl/class.tx_realurl.php
	@@ -684,8 +684,12 @@ class tx_realurl {
									);
									$prevVal = $GETvarVal;
									$GETvarVal = t3lib_div::callUserFunction($setup['userFunc'], $params, $this);
	-								$pathParts[] = rawurlencode($GETvarVal);
	-								$this->cHashParameters[$GETvar] = $prevVal;
	+								if ($GETvarVal === NULL) {
	+									$this->rebuildCHash |= $parameterSet;
	+								} else {
	+									$pathParts[] = rawurlencode($GETvarVal);
	+									$this->cHashParameters[$GETvar] = $prevVal;
	+								}
								} elseif (is_array($setup['lookUpTable'])) {
									$prevVal = $GETvarVal;
									$GETvarVal = $this->lookUpTranslation($setup['lookUpTable'], $GETvarVal);
