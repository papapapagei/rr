<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');



// todo TCA field: file_status






$TCA['tx_dam'] = array(
	'ctrl' => $TCA['tx_dam']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,media_type,title,file_type',
// todo TCA excludeFieldList: clean this list or remove
		'excludeFieldList' => 'active,t3ver_label', // non-standard - will hide fields from field selector box in list module
	),
	'feInterface' => $TCA['tx_dam']['feInterface'],
	'txdamInterface' => $TCA['tx_dam']['txdamInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),



// todo TCA: remove active field? Has no function yet.
//		'active' => array(
//			'exclude' => '1',
//			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
//			'config' => array(
//				'type' => 'check',
//				'default' => '0'
//			)
//		),
		'starttime' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => array(
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
//		'fe_group' => array(
//			'exclude' => '1',
//			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
//			'config' => array(
//				'type' => 'select',
//				'items' => array(
//					array('', 0),
//					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
//					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
//					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
//				),
//				'foreign_table' => 'fe_groups'
//			)
//		),
		'fe_group' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => array (
				'type' => 'select',
				'size' => 5,
				'maxitems' => 20,
				'items' => array (
					array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups'
			)
		),

		/*
		 * LANGUAGE
		 */
		'sys_language_uid' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dam',
				'foreign_table_where' => 'AND tx_dam.uid=###REC_FIELD_l18n_parent### AND tx_dam.sys_language_uid IN (-1,0)',
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,

					'edit' => array(
							'type' => 'popup',
							'title' => 'edit default language version of this record ',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=600,width=700,status=0,menubar=0,scrollbars=1,resizable=1',
					),
				),
			)
		),
		'l18n_diffsource' => array(
			'config'=>array(
				'type' => 'passthrough'
			)
		),


		/*
		 * VERSIONING
		 */
		't3ver_label' => array(
			'displayCond' => 'EXT:version:LOADED:true',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '30',
			)
		),



		/*
		 * TITLE ...
		 */

		'media_type' => array(
			'exclude' => '1',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.media_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:dam/locallang_db.xml:media_type.text', '1'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.image', '2'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.audio', '3'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.video', '4'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.dataset', '9'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.interactive', '5'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.software', '11'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.model', '8'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.font', '7'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.collection', '10'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.service', '6'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.application', '12'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.undefined', '0'),
				),

				'form_type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->tx_dam_mediaType',
				'noTableWrapping' => TRUE,
				'readOnly' => true,
			)
		),
		'title' => array(
			'exclude' => '1',
			'l10n_mode' => '',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),

		/*
		 * FILE ###########################################
		 */

		'file_name' => array(
			'exclude' => 0,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'size' => '15',
				'max' => '255',
				'eval' => 'required',
				'softref' => 'dam_file',
			)
		),
		'file_path' => array(
			'exclude' => 0,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_path',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'size' => '25',
				'max' => '4096',
				'eval' => 'required',
			)
		),
		'file_dl_name' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_dl_name',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'file_type' => array(
			'exclude' => 0,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_type',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'size' => '5',
				'max' => '5',
				'eval' => 'trim',
			)
		),


		'file_type_version' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_type_version',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'size' => '6',
				'max' => '9',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'file_size' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_size',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'format' => 'filesize',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'file_orig_location' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_orig_location',
			'exclude' => '1',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'size' => '45',
				'max' => '255',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'file_orig_loc_desc' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_orig_loc_desc',
			'exclude' => '1',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'size' => '45',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),

		'file_creator' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_creator',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'size' => '30',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),

		'file_mime_type' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_mime_type',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'form_type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->tx_dam_file_mime_type',
				'size' => '20',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),

		'file_mime_subtype' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_mime_subtype',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'size' => '20',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),

		'file_ctime' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_ctime',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'format' => 'datetime',
				'size' => '11',
				'max' => '20',
				'eval' => 'datetime',
				'default' => '0',
			)
		),
		'file_mtime' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_mtime',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'readOnly' => true,
				'format' => 'datetime',
				'size' => '11',
				'max' => '20',
				'eval' => 'datetime',
				'default' => '0',
			)
		),

		'file_usage' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_usage',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'hideDiff',
			'config' => array(
				// 'allowed' => '*', // this might be needed for bidi MM relations sometimes
				'type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->tx_dam_fileUsage',
			)
		),




		/*
		 * COPYRIGHT ###########################################
		 */

		'ident' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.ident',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'size' => '15',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'creator' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.creator',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'size' => '35',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'publisher' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.publisher',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'size' => '35',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'copyright' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.copyright',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'size' => '35',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),


		/*
		 * META DESCRIPTION ###########################################
		 */

		'keywords' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.keywords',
			'config' => array(
				'type' => 'input',
				'size' => '45',
				'eval' => 'trim',
				'appendType' => 'comma'
			)
		),
		'description' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.description',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '3'
			)
		),
		'caption' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.caption',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '3'
			)
		),
		'alt_text' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.alt_text',
			'config' => array(
				'type' => 'input',
				'size' => '45',
				'max' => '255',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'instructions' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.instructions',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '2'
			)
		),
		'abstract' => array(
			'exclude' => '1',
			'l10n_mode' => 'hideDiff',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.abstract',
			'config' => array(
				'type' => 'text',
				'readOnly' => true,
				'cols' => '45',
				'rows' => '3',
				'eval' => 'trim',
			)
		),
		'date_cr' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.date_cr',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
			)
		),
		'date_mod' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.date_mod',
			'exclude' => '0',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
			)
		),
		'loc_desc' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.loc_desc',
			'exclude' => '0',
			'l10n_mode' => '',
			'config' => array(
				'type' => 'input',
				'size' => '45',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'loc_country' => array(
			'exclude' => 0,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.loc_country',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('',0),
				),
//				'foreign_table' => 'static_countries',
//				'rootLevel' => '1',
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array(
					'table' => 'static_countries',
					'indexField' => 'cn_iso_3',
					'prependHotlist' => 1,
					//	defaults:
					//'hotlistLimit' => 8,
					//'hotlistSort' => 1,
					//'hotlistOnly' => 0,
					'hotlistApp' => 'dam',
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'default' => ''
			)
		),

		'loc_city' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.loc_city',
			'exclude' => '0',
			'l10n_mode' => '',
			'config' => array(
				'type' => 'input',
				'size' => '15',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'language' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.language',
			'exclude' => '0',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('',''),
				),
				'size' => '1',
				'maxitems' => '1',
				'default' => '',
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array(
					'table' => 'static_languages',
					'indexField' => 'lg_iso_2',
					'prependHotlist' => 1,
					'hotlistApp' => 'dam',
				),
			)
		),




		/*
		 * METRICS ###########################################
		 */

		'hres' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.hres',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'vres' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.vres',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'hpixels' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.hpixels',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'vpixels' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.vpixels',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'color_space' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.color_space',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', ''),
					array('RGB', 'RGB'),
// This is not a colorspace but a color profile					array('sRGB', 'sRGB'),
					array('CMYK', 'CMYK'),
					array('CMY', 'CMY'),
					array('YUV', 'YUV'),
					array('Grey', 'grey'),
					array('indexed', 'indx'),
				),
				'default' => ''
			)
		),
		'width' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.width',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '',
				'eval' => '',
				'default' => ''
			)
		),
		'height' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.height',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '',
				'eval' => '',
				'default' => ''
			)
		),
		'height_unit' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.height_unit',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', ''),
					array('px', 'px'),
					array('mm', 'mm'),
					array('cm', 'cm'),
					array('m', 'm'),
					array('p', 'p'),
				),
				'default' => ''
			)
		),
		'pages' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.pages',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),

		'meta' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.meta',
			'exclude' => '1',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => array(
				'type' => 'user',
				'readOnly' => true,
				'form_type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->tx_dam_meta',
				'cols' => '45',
				'rows' => '40',
			)
		),

		/*
		 * CATEGORY
		 */

#		'category' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_field'],
		'category' => array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.category',
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config'],
		),

// todo group handling
		'parent_id' => array(
			'label' => 'parent_id',
			'exclude' => '1',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type'=>'passthrough',
//				'type' => 'input',
//				'size' => '8',
//				'max' => '',
//				'eval' => '',
				'default' => ''
			)
		),



		/*
		 * displayed in debug mode only
		 */


		'deleted' => array(
			'label' => 'Deleted',
			'exclude' => '1',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'index_type' => array(
			'label' => 'Index type:',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'input',
				'size' => '4',
				'max' => '4',
			)
		),
		'file_inode' => array(
			'label' => 'File inode:',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'eval' => 'int',
			)
		),
		'file_hash' => array(
			'label' => 'File hash:',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'input',
				'size' => '32',
				'max' => '32',
			)
		),
		'file_status' => array(
			'label' => 'File status code:',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'eval' => 'int',
			)
		),
		'tstamp' => array(
			'exclude' => '1',
			'label' => 'Timestamp:',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'input',
				'size' => '15',
				'max' => '20',
				'eval' => 'int',
			)
		),

	),

	'types' => array(
	),

	'palettes' => array(
		'1' => array('showitem' => 'hidden,starttime, endtime,', 'canNotCollapse' => '1'),
		'2' => array('showitem' => 'fe_group'),

		'3' => array('showitem' => 'loc_desc', 'canNotCollapse' => '1'),
		'4' => array('showitem' => 'hpixels,vpixels', 'canNotCollapse' => '1'),
		'5' => array('showitem' => 'loc_country,loc_city', 'canNotCollapse' => '1'),
		'6' => array('showitem' => 'file_name,file_path', 'canNotCollapse' => '1'),
		'7' => array('showitem' => 'file_size,file_type,file_mime_type', 'canNotCollapse' => '1'),
		'8' => array('showitem' => 'file_ctime,file_mtime', 'canNotCollapse' => '1'),
		'9' => array('showitem' => 'creator,publisher', 'canNotCollapse' => '1'),
		'10' => array('showitem' => 'width,height,height_unit', 'canNotCollapse' => '1'),
		'12' => array('showitem' => 'file_creator,file_type_version', 'canNotCollapse' => '1'),
		'13' => array('showitem' => 'date_cr,date_mod', 'canNotCollapse' => '1'),
		'14' => array('showitem' => 'hres,vres', 'canNotCollapse' => '1'),
		'15' => array('showitem' => '', 'canNotCollapse' => '1'),
	)
);

$tx_dam_header = '--div--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.div_overview, media_type;;;;3-3-3, thumb, ';


#$tx_dam_descr = 'title;;;;3-3-3,       l18n_parent,sys_language_uid,              keywords, description, --palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.date_pheader;13;;, ';
$tx_dam_descr = 'title;;;;3-3-3, keywords, description, --palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.date_pheader;13;;, ';
$tx_dam_descr_abstract = 'title;;;;3-3-3, keywords, description, abstract;;;;3-3-3, --palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.date_pheader;13;;, ';
$tx_dam_descr_txt = 'title;;;;3-3-3, keywords, description, abstract;;;;3-3-3, language, pages, --palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.date_pheader;13;;, ';
$tx_dam_descr_img = 'title;;;;3-3-3, keywords, description, --palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.loc_pheader;5;;, --palette--;;3;;, --palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.date_pheader;13;;, ';

$tx_dam_metrics_img = '--div--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.div_metrics, color_space;;;;4-4-4, --palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.metrics;4, --palette--;;10;;, --palette--;;14;;, ';
$tx_dam_metrics_txt = '--div--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.div_metrics, --palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.metrics;10;;4-4-4, ';

$tx_dam_file = '--palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_pheader;6;;3-3-3, --palette--;;7;;, --palette--;;8;;, --palette--;;12;;, file_orig_location;;;;, file_orig_loc_desc, ';

$tx_dam_copyright = '--div--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.div_copyright, creator;;;;3-3-3, publisher, copyright, ident, ';
$tx_dam_category = 'category;;;;4-4-4, ';

#$tx_dam_frontend = '--palette--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.frontend_pheader;1;;1-1-1, caption, alt_text, file_dl_name, ';
$tx_dam_frontend = 'caption;;;;1-1-1, alt_text, file_dl_name, ';

$tx_dam_usage = '--div--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.div_usage, instructions;;;;3-3-3, file_usage, ';

$tx_dam_feaccess = '--palette--;LLL:EXT:lang/locallang_general.php:LGL.fe_group;1;;1-1-1, fe_group, ';

$tx_dam_footer = $tx_dam_category.$tx_dam_feaccess;

$tx_dam_common = $tx_dam_frontend.$tx_dam_file.$tx_dam_footer.$tx_dam_copyright.$tx_dam_meta.$tx_dam_usage;

$tx_dam_meta = '--div--;LLL:EXT:dam/locallang_db.xml:tx_dam_item.div_extraMeta, meta,';

$tx_dam_extra = '';

// for development:
if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['setup']['debug']) {
	$tx_dam_extra .= '--div--;[Devel], deleted, tstamp, index_type, file_hash, file_inode, file_status, sys_language_uid;;;;3-3-3, l18n_parent, l18n_diffsource, t3ver_label;;;;3-3-3,';
}

$TCA['tx_dam']['types'] = array(
	/* undefined */
	'0' =>  array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* text */
	'1' =>  array('showitem' => $tx_dam_header.$tx_dam_descr_txt.$tx_dam_frontend.$tx_dam_file.$tx_dam_footer.$tx_dam_metrics_txt.$tx_dam_copyright.$tx_dam_meta.$tx_dam_usage.$tx_dam_extra),
	/* image */
	'2' =>  array('showitem' => $tx_dam_header.$tx_dam_descr_img.$tx_dam_frontend.$tx_dam_file.$tx_dam_footer.$tx_dam_metrics_img.$tx_dam_copyright.$tx_dam_meta.$tx_dam_usage.$tx_dam_extra),
	/* audio */
	'3' =>  array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* video */
	'4' =>  array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* interactive */
	'5' =>  array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* service */
	'6' =>  array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* font */
	'7' =>  array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* model */
	'8' =>  array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* dataset */
	'9' =>  array('showitem' => $tx_dam_header.$tx_dam_descr_abstract.$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* collection */
	'10' => array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* software */
	'11' => array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
	/* application */
	'12' => array('showitem' => $tx_dam_header.$tx_dam_descr.		$tx_dam_common.$tx_dam_meta.$tx_dam_extra),
);




$TCA['tx_dam_cat'] = array(
	'ctrl' => $TCA['tx_dam_cat']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,fe_group,title',
	),
	'feInterface' => $TCA['tx_dam_cat']['feInterface'],

	'columns' => array(
		'hidden' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'fe_group' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => array (
				'type' => 'select',
				'size' => 5,
				'maxitems' => 20,
				'items' => array (
					array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'nav_title' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:cms/locallang_tca.xml:pages.nav_title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '256',
				'checkbox' => '',
				'eval' => 'trim'
			)
		),
		'subtitle' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:cms/locallang_tca.xml:pages.subtitle',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '256',
				'eval' => ''
			)
		),
		'keywords' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.keywords',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '3'
			)
		),
		'description' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.description',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '3'
			)
		),
		'parent_id' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_cat_item.parent_id',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'],
		),

		/*
		 * LANGUAGE
		 */
		'sys_language_uid' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dam_cat',
				'foreign_table_where' => 'AND tx_dam_cat.uid=###REC_FIELD_l18n_parent### AND tx_dam_cat.sys_language_uid IN (-1,0)',
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,

					'edit' => array(
							'type' => 'popup',
							'title' => 'edit default language version of this record ',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=600,width=700,status=0,menubar=0,scrollbars=1,resizable=1',
					),
				),
			)
		),
		'l18n_diffsource' => array(
			'config'=>array(
				'type'=>'passthrough')
		),



	),
	'types' => array(
		'1' => array(
			'showitem' => 'title;;2;;,description,keywords,parent_id,--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access;1;;1-1-1, fe_group'
		)
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'2' => array('showitem' => 'hidden,subtitle,nav_title', 'canNotCollapse' => '1'),
	)

);



$TCA['tx_dam_metypes_avail'] = array(
	'ctrl' => $TCA['tx_dam_metypes_avail']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'title,type',
	),
	'columns' => array(
        'type' => array (
            'exclude' => 1,
            'label' => 'LLL:EXT:dam/lib/locallang.xml:mediaTypes',
            'config' => array (
                'type' => 'select',
                'items' => array (
                    array('LLL:EXT:dam/lib/locallang.xml:undefined', '0'),
                    array('LLL:EXT:dam/lib/locallang.xml:text', '1'),
                    array('LLL:EXT:dam/lib/locallang.xml:image', '2'),
                    array('LLL:EXT:dam/lib/locallang.xml:audio', '3'),
                    array('LLL:EXT:dam/lib/locallang.xml:video', '4'),
                    array('LLL:EXT:dam/lib/locallang.xml:interactive', '5'),
                    array('LLL:EXT:dam/lib/locallang.xml:service', '6'),
                    array('LLL:EXT:dam/lib/locallang.xml:font', '7'),
                    array('LLL:EXT:dam/lib/locallang.xml:model', '8'),
                    array('LLL:EXT:dam/lib/locallang.xml:dataset', '9'),
                    array('LLL:EXT:dam/lib/locallang.xml:collection', '10'),
                    array('LLL:EXT:dam/lib/locallang.xml:software', '11'),
                    array('LLL:EXT:dam/lib/locallang.xml:application', '12'),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
		'title' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'parent_id' => array(
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_cat_item.parent_id',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_dam_metypes_avail',
				'size' => '3',
				'maxitems' => '1',
				'minitems' => '0',
				'show_thumbs' => '1'
			)
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'title,type,parent_id'
		)
	),

);






$TCA['tx_dam_selection'] = array(
	'ctrl' => $TCA['tx_dam_selection']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,type,title,definition'
	),
	'feInterface' => $TCA['tx_dam_selection']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => array(
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'fe_group' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'type' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_selection.type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:dam/locallang_db.xml:tx_dam_selection.type.I.0', '0'),
					array('LLL:EXT:dam/locallang_db.xml:tx_dam_selection.type.I.1', '1'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'title' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'definition' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_selection.definition',
			'config' => array(
				'type' => 'text',
				'wrap' => 'OFF',
				'cols' => '30',
				'rows' => '5',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, type, title;;;;2-2-2, definition;;;;3-3-3')
	),
	'palettes' => array(
		'1' => array('showitem' => 'starttime, endtime, fe_group')
	)
);


$TCA['tx_dam_media_types'] = array(
	'ctrl' => $TCA['tx_dam_media_types']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'ext, mime, type, icon'
	),
	'feInterface' => $TCA['tx_dam_media_types']['feInterface'],
	'columns' => array(
		'ext' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_media_types.ext',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'eval' => 'unique,required',
			)
		),
		'mime' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_media_types.mime',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),		
		'type' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.media_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:dam/locallang_db.xml:media_type.text', '1'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.image', '2'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.audio', '3'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.video', '4'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.dataset', '9'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.interactive', '5'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.software', '11'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.model', '8'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.font', '7'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.collection', '10'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.service', '6'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.application', '12'),
					array('LLL:EXT:dam/locallang_db.xml:media_type.undefined', '0'),
				),
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->tx_dam_mediaType',
				'noTableWrapping' => TRUE,
			)
		),
		'icon' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_media_types.icon',
			'config' => array(
				'form_type'		=> 'user',
				'userFunc'		=> 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_typeMedia',
				'type'			=> 'group',
				'internal_type'	=> 'db',
				'allowed'		=> 'tx_dam',
				'allowed_types'	=> 'gif,jpg,jpeg,png',
				'max_size'		=> '1',
				'show_thumbs'	=> '1',
				'size'			=> '1',
				'maxitems'		=> '1',
				'minitems'		=> '0',
				'autoSizeMax'	=> '1',
			)
		),						
	),
	'types' => array(
		'0' => array('showitem' => 'ext, mime, type, icon')
	)
);



?>
