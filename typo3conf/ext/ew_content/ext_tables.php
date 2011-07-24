<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$tempColumns = array (
	'tx_ewcontent_minimized' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:ew_content/locallang_db.xml:tt_content.tx_ewcontent_minimized',		
		'l10n_mode'   => 'exclude',
		'config' => array (
			'type' => 'check',
		)
	),
);


t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tt_content','tx_ewcontent_minimized;;;;1-1-1');
?>