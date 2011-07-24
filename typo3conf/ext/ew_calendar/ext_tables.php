<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$TCA['tx_ewcalendar_dates'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l10n_parent',	
		'transOrigDiffSourceField' => 'l10n_diffsource',	
		'default_sortby' => 'ORDER BY date DESC',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ewcalendar_dates.gif',
	),
);
$TCA['tx_ewcalendar_productions'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_productions',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l10n_parent',	
		'transOrigDiffSourceField' => 'l10n_diffsource',	
		'default_sortby' => 'ORDER BY title ASC',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ewcalendar_productions.gif',
	),
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types'][$_EXTKEY . '_pi1']['showitem'] = 'CType;;4;button;1-1-1, header;;3;;2-2-2';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ew_calendar/locallang_db.xml:tt_content.CType_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'CType');

/*if (TYPO3_MODE == 'BE') {
    t3lib_extMgm::addModule('txewcalendarM1', '', 'after:layout', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}*/

?>