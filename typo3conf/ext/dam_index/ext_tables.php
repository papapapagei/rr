<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{


	$tempSetup =  unserialize($_EXTCONF);

	if ($tempSetup['add_media_file_indexing']) {

		t3lib_extMgm::insertModuleFunction(
			'txdamM1_file',
			'tx_damindex_index',
			t3lib_extMgm::extPath($_EXTKEY).'modfunc_index/class.tx_damindex_index.php',
			'LLL:EXT:dam_index/modfunc_index/locallang.xml:tx_damindex_index.title'
		);
	}

	if ($tempSetup['add_media_indexing']) {
		t3lib_extMgm::addModule('txdamM1','index','before:tools',t3lib_extMgm::extPath($_EXTKEY).'mod1/');

		t3lib_extMgm::insertModuleFunction(
			'txdamM1_index',
			'tx_damindex_index',
			t3lib_extMgm::extPath($_EXTKEY).'modfunc_index/class.tx_damindex_index.php',
			'LLL:EXT:dam_index/modfunc_index/locallang.xml:tx_damindex_index.title'
		);
	}

}

?>