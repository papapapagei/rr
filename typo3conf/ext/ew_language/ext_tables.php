<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['sys_language']['columns']['disabled_in_menu'] = array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:ew_language/locallang_db.xml:disabled_in_menu',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		);

$TCA['sys_language']['types'][1]['showitem'] .= ',disabled_in_menu';

if (TYPO3_MODE == 'BE') {
	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'sys_language', '');
	if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
		t3lib_extMgm::addModulePath('web_txewlanguageM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');

		t3lib_extMgm::addModule('web', 'txewlanguageM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	}
}
?>