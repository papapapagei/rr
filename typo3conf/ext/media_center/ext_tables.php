<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


// add media center item object and allow to be included on standard pages
t3lib_extMgm::allowTableOnStandardPages('tx_mediacenter_item');
t3lib_extMgm::addToInsertRecords('tx_mediacenter_item');

/**
 * TCA basic definition for the foal table
 *
 * @author Patrick Rodacker <patrick.rodacker@the-reflection.de>
 */
$TCA['tx_mediacenter_item'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioning' => '1',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'default_sortby' => 'ORDER BY crdate DESC',
		'useColumnsForDefaultValues' => 'sys_language_uid',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon.gif',
		'dividers2tabs' => 1,
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, description, author, file, duration, link, image, start',
	)
);

// remove some unused fields
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';

// add flexform for the plugin
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform.xml');

// add the plugin pi1
t3lib_extMgm::addPlugin(array('LLL:EXT:media_center/locallang_db.xml:list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

// add wizicon for pi1
if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_mediacenter_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_mediacenter_pi1_wizicon.php';
}

// add static file for the pi1 plugin
t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/', 'Media Center');



?>