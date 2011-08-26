<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
// TT_CONTENT TCA
$contentColumns = array(
	'tx_ewgallery_smallimage' => txdam_getMediaTCA('image_field', 'tx_ewgallery_smallimage'),
	'tx_ewgallery_bigimage' => txdam_getMediaTCA('image_field', 'tx_ewgallery_bigimage'),
	'tx_ewgallery_video' => txdam_getMediaTCA('media_field', 'tx_ewgallery_video'),
	'tx_ewgallery_video_button' => txdam_getMediaTCA('image_field', 'tx_ewgallery_video_button'),
	'tx_ewgallery_video_title' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_video_title',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',	
			'eval' => '',
		)
	),

	'tx_ewgallery_video_autostart' => array (        
		'exclude' => 0,        
		'label' => 'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_video_autostart',        
		'config' => array (
			'type' => 'check',
		)
	),
	'tx_ewgallery_type' => array (		
		'exclude' => 1,
		'label'  => 'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_type',
		'config' => array (
			'type' => 'select',
			'items' => array(
				array('LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_type.0', 0),
				array('LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_type.1', 1)
			)
		)
	)
);

$contentColumns['tx_ewgallery_smallimage']['label'] = 'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_smallimage';
$contentColumns['tx_ewgallery_smallimage']['config']['maxitems'] = 1;
$contentColumns['tx_ewgallery_smallimage']['config']['size'] = 1;
$contentColumns['tx_ewgallery_bigimage']['label'] = 'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_bigimage';
$contentColumns['tx_ewgallery_bigimage']['config']['maxitems'] = 1;
$contentColumns['tx_ewgallery_bigimage']['config']['size'] = 1;
$contentColumns['tx_ewgallery_video']['label'] = 'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_video';
$contentColumns['tx_ewgallery_video']['config']['allowed_types'] = 'flv,f4v,mp4,mov,ogg,ogv,webm';
$contentColumns['tx_ewgallery_video']['config']['disallowed_types'] = '';
$contentColumns['tx_ewgallery_video']['config']['maxitems'] = 6;
$contentColumns['tx_ewgallery_video']['config']['size'] = 6;
$contentColumns['tx_ewgallery_video_button']['label'] = 'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_video_button';
$contentColumns['tx_ewgallery_video_button']['config']['maxitems'] = 1;
$contentColumns['tx_ewgallery_video_button']['config']['size'] = 1;

// PAGES TCA
$pageColumns = array(
	'tx_ewgallery_image' => txdam_getMediaTCA('image_field', 'tx_ewgallery_image'),
	'tx_ewgallery_video' => txdam_getMediaTCA('media_field', 'tx_ewgallery_video'),
	'tx_ewgallery_image_x' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:ew_gallery/locallang_db.xml:pages.tx_ewgallery_image_x',
		'config' => array (
			'type' => 'input',
			'eval' => 'int',
			'size' => '4',
		)
	),
	'tx_ewgallery_image_y' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:ew_gallery/locallang_db.xml:pages.tx_ewgallery_image_y',
		'config' => array (
			'type' => 'input',
			'eval' => 'int',
			'size' => '4',
		)
	),
);

$pageColumns['tx_ewgallery_video']['config']['allowed_types'] = 'flv,f4v,mp4,mov';
$pageColumns['tx_ewgallery_video']['config']['disallowed_types'] = '';
$pageColumns['tx_ewgallery_image']['config']['maxitems'] = 1;
$pageColumns['tx_ewgallery_image']['config']['size'] = 1;

t3lib_extMgm::addTCAcolumns('tt_content',$contentColumns,1);
t3lib_extMgm::addTCAcolumns('pages',$pageColumns,1);

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types'][$_EXTKEY . '_pi1']['showitem'] = 'CType;;4;button;1-1-1, tx_ewgallery_type,tx_ewgallery_smallimage,tx_ewgallery_bigimage,tx_dam_images;LLL:EXT:ew_gallery/locallang_db.xml:tt_content.tx_ewgallery_list,tx_ewgallery_video,tx_ewgallery_video_title,tx_ewgallery_video_button';
// integrate BACKEND IMAGE EDIT
$TCA['tt_content']['types'][$_EXTKEY . '_pi1']['showitem'] .= ',--div--;LLL:EXT:go_imageedit_be/locallang_db.xml:tabLabel,tx_goimageeditbe_croped_image,';

// FOR THE PAGE VIDEO / IMAGE
$TCA['pages']['types'][1]['showitem'] .= ',--div--;LLL:EXT:ew_gallery/locallang_db.xml:pages.tx_ewgallery_header,tx_ewgallery_image;LLL:EXT:ew_gallery/locallang_db.xml:pages.tx_ewgallery_image;100;;,tx_ewgallery_video;LLL:EXT:ew_gallery/locallang_db.xml:pages.tx_ewgallery_video';

$TCA['pages']['palettes']['100']['showitem']  = 'tx_ewgallery_image_x, tx_ewgallery_image_y';  

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.CType_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'CType');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types'][$_EXTKEY . '_pi2']['showitem'] = 'CType;;4;button;1-1-1,tx_ewgallery_video,tx_ewgallery_video_button';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ew_gallery/locallang_db.xml:tt_content.CType_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'CType');

t3lib_extMgm::addStaticFile($_EXTKEY,'static//', 'TypoScript Configuration');

?>