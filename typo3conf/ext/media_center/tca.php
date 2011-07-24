<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


/**
 * TCA  definition for the mediacenter_item data object
 *
 * @author Patrick Rodacker <patrick.rodacker@the-reflection.de>
 */
$TCA['tx_mediacenter_item'] = Array (
	'ctrl' => $TCA['tx_mediacenter_item']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'sys_language_uid,l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, description, author, file, file_url, duration, link, image, start'
	),
	'feInterface' => $TCA['tx_mediacenter_item']['feInterface'],
	'columns' => Array (
		'sys_language_uid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_mediacenter_item',
				'foreign_table_where' => 'AND tx_mediacenter_item.uid=###REC_FIELD_l18n_parent### AND tx_mediacenter_item.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array (
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.title',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'eval' => 'required',
				'max' => '256',
			)
		),
		'description' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.description',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '256',
			)
		),
		'author' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.author',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '256',
			)
		),
		'file' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.file',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				// 'allowed' => $GLOBALS['TYPO3_CONF_VARS']['EXT'][$_EXTKEY]['allowdMediaFiles'],
				'allowed' => 'swf,flv,aac,mp3,jpg,jpeg,gif,png',
				'max_size' => 100000,
				'uploadfolder' => 'uploads/tx_mediacenter',
				'show_thumbs' => 1,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'file_url' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.file_url',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '250',
				'wizards' => Array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
		'captions' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.captions',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '250',
				'wizards' => Array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
		'duration' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.duration',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
			)
		),
		'link' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.link',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '250',
				'wizards' => Array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
		'image' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.image',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => 10000,
				'uploadfolder' => 'uploads/tx_mediacenter',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'show_thumbs' => 1,
			)
		),
		'start' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.start',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'eval' => 'int',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => '--div--;LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.sheet_file, file, file_url, captions, duration, link, start, --div--;LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.sheet_description, title, image, description, author, --div--;LLL:EXT:media_center/locallang_db.php:tx_mediacenter_item.sheet_misc, sys_language_uid, l18n_parent, l18n_diffsource, hidden;;1, status')
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'starttime, endtime, fe_group')
	)
);

?>