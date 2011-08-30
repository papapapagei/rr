<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'piMenu/class.tx_ewlanguage_piMenu.php', '_piMenu', '', 0);

$TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY] = array(
	'defaultLanguageIsoCode' => 'DE',
	'defaultLanguageDisabledInMenu' => 0,
	'defaultLanguageRealurlEncode' => 1,
	'redirectToPreferredBrowserLanguage' => 1,
	'sys_language_mode' => 'content_fallback',
	'sys_language_overlay' => 'hideNonTranslated',
);

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc']['tx_ewlanguage'] = 'EXT:ew_language/lib/class.tx_ewlanguage.php:&tx_ewlanguage->setPreferredBrowserLanguage';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tsparser.php']['preParseFunc']['getLanguageTSConfig'] = 'EXT:ew_language/lib/class.tx_ewlanguage.php:&tx_ewlanguage->getLanguageTSConfig';

?>