<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// CALENDAR
$TCA['tx_ewcalendar_dates'] = array (
	'ctrl' => $TCA['tx_ewcalendar_dates']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,title,info,date,only_month,dont_teaser,image,facts,link_title,link'
	),
	'feInterface' => $TCA['tx_ewcalendar_dates']['feInterface'],
	'columns' => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_ewcalendar_dates',
				'foreign_table_where' => 'AND tx_ewcalendar_dates.pid=###CURRENT_PID### AND tx_ewcalendar_dates.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => '',
			)
		),
		'production' => array (		
			'exclude'     => 0,
			'label'       => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.production',
			'l10n_mode'   => 'exclude',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_ewcalendar_productions',
			)
		),
		'highlight' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.highlight',		
			'l10n_mode'   => 'exclude',
			'config' => array (
				'type' => 'check',
			)
		),
		'info' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.info',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'info_size' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.info_size',		
			'config' => array (
				'type' => 'select',	
				'items' => Array (
					Array('LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.info_size.normal', ''),
					Array('LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.info_size.small', '-1'),
					Array('LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.info_size.smaller', '-2'),
				)
			)
		),
		'date' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.date',		
			'l10n_mode'   => 'exclude',
			'config' => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0'
			)
		),
		'only_month' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.only_month',		
			'l10n_mode'   => 'exclude',
			'config' => array (
				'type' => 'check',
			)
		),
		'dont_teaser' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.dont_teaser',		
			'l10n_mode'   => 'exclude',
			'config' => array (
				'type' => 'check',
			)
		),
		'image' => txdam_getMediaTCA('image_field', 'image'),
		'facts' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.facts',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'link_title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.link_title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'link_text' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.link_text',		
			'config' => array (
				'type' => 'input',	
				'size' => '20',
			)
		),
		'link' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.link',		
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;, title;;2;;2-2-2, date;;3, image, facts, link_title;;4')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime'),
		'2' => array('showitem' => 'production,info,info_size'),
		'3' => array('showitem' => 'highlight,only_month,dont_teaser'),
		'4' => array('showitem' => 'link,link_text'),
	)
);
$TCA['tx_ewcalendar_dates']['columns']['image']['label'] = 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_dates.image';
$TCA['tx_ewcalendar_dates']['columns']['image']['config']['maxitems'] = 1;
$TCA['tx_ewcalendar_dates']['columns']['image']['config']['size'] = 1;
$TCA['tx_ewcalendar_dates']['columns']['image']['l10n_mode'] = 'exclude';

// PRODUCTIONS

$TCA['tx_ewcalendar_productions'] = array (
	'ctrl' => $TCA['tx_ewcalendar_productions']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,title,link,image,facts'
	),
	'feInterface' => $TCA['tx_ewcalendar_productions']['feInterface'],
	'columns' => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_ewcalendar_productions',
				'foreign_table_where' => 'AND tx_ewcalendar_productions.pid=###CURRENT_PID### AND tx_ewcalendar_productions.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_productions.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'link' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_productions.link',		
			'l10n_mode'   => 'exclude',
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		'image' => txdam_getMediaTCA('image_field', 'image'),
		'facts' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_productions.facts',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;, title;;1;;2-2-2, image, facts')
	),
	'palettes' => array (
		'1' => array('showitem' => 'link'),
	)
);
$TCA['tx_ewcalendar_productions']['columns']['image']['label'] = 'LLL:EXT:ew_calendar/locallang_db.xml:tx_ewcalendar_productions.image';
$TCA['tx_ewcalendar_productions']['columns']['image']['config']['maxitems'] = 1;
$TCA['tx_ewcalendar_productions']['columns']['image']['config']['size'] = 1;
$TCA['tx_ewcalendar_productions']['columns']['image']['l10n_mode'] = 'exclude';

?>