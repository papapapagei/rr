<?php

/**
 * Predefined TCA entries for usage in own extension.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3-DAM
 * @subpackage	tx_dam
 * @version	$Id$
 */


$GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_config'] = array (
	'form_type' => 'user',
	'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_typeMedia',

	'userProcessClass' => 'EXT:mmforeign/class.tx_mmforeign_tce.php:tx_mmforeign_tce',
	'type' => 'group',
	'internal_type' => 'db',
	'allowed' => 'tx_dam',
	'prepend_tname' => 1,
	'MM' => 'tx_dam_mm_ref',
	'MM_foreign_select' => 1, // obsolete in 4.1
	'MM_opposite_field' => 'file_usage',
	'MM_match_fields' => array('ident' => 'relation_field_or_other_ident'), #### has to be changed in table

	'allowed_types' => '',
	'disallowed_types' => 'php,php3',

	'max_size' => 10000,
	'show_thumbs' => 1,
	'size' => 5,
	'maxitems' => 200,
	'minitems' => 0,
	'autoSizeMax' => 30,
);


$GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_config'] = array (
	'form_type' => 'user',
	'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_typeMedia',

	'userProcessClass' => 'EXT:mmforeign/class.tx_mmforeign_tce.php:tx_mmforeign_tce',
	'type' => 'group',
	'internal_type' => 'db',
	'allowed' => 'tx_dam',
	'prepend_tname' => 1,
	'MM' => 'tx_dam_mm_ref',
	'MM_foreign_select' => 1, // obsolete in 4.1
	'MM_opposite_field' => 'file_usage',
	'MM_match_fields' => array('ident' => 'relation_field_or_other_ident'), #### has to be changed in table

	'allowed_types' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],

	'max_size' => '1000',
	'show_thumbs' => 1,
	'size' => 5,
	'maxitems' => 200,
	'minitems' => 0,
	'autoSizeMax' => 30,
);


if (t3lib_div::int_from_ver(TYPO3_branch)>=t3lib_div::int_from_ver('4.1')) {
	unset($GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_config']['userProcessClass']);
	unset($GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_config']['userProcessClass']);
}


$GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_field'] = array (
	'label' => 'LLL:EXT:cms/locallang_ttc.php:media',
	'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_config'],
);


$GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_field'] = array (
	'label' => 'LLL:EXT:lang/locallang_general.php:LGL.images',
	'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_config'],
);



$GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'] = array (
	'type' => 'select',
	'form_type' => 'user',
	'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_selectTree',

	'treeViewBrowseable' => true,
	'treeViewClass' => 'EXT:dam/components/class.tx_dam_selectionCategory.php:&tx_dam_selectionCategory', // don't work here: $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['selectionClasses']['txdamCat']
	'foreign_table' => 'tx_dam_cat',

	'size' => 6,
	'autoSizeMax' => 20,
	'minitems' => 0,
	'maxitems' => 2, // workaround - should be 1
	'default' => '',
);

$GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_field'] = array (
	'l10n_mode' => 'exclude',
	'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.category',
	'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'],
);



	// mountpoints field in be_groups, be_users
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['mountpoints_config'] = array (
		// a special format is stored - that's why 'passthrough'
		// see: flag TCEFormsSelect_prefixTreeName
		// see: tx_dam_treelib_tceforms::getMountsForTree()
	'type' => 'passthrough',
	'form_type' => 'user',
	'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_selectMounts',

	'treeViewBrowseable' => true,

	'size' => 10,
	'autoSizeMax' => 30,
	'minitems' => 0,
	'maxitems' => 20,
);


	// for tx_dam allowed only
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config'] = $GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'];
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['size'] = 6;
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['maxitems'] = 25;
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['MM'] = 'tx_dam_mm_cat';

$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_field'] = array (
	'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.category',
	'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config'],
);



/**
 * get TCA array for media fields/config
 *
 * @param	string		Name of the predefined TCA definition.
 * @param	string		Ident string for MM relations. Has to be set for field definitions that uses MM relations.
 */
function txdam_getMediaTCA($type, $MM_ident='') {
	$tcaDef = $GLOBALS['T3_VAR']['ext']['dam']['TCA'][$type];

	if($MM_ident && is_array($tcaDef)) {
		if(is_array($tcaDef['config'])) {
			$tcaDef['config']['MM_match_fields'] = array('ident' => $MM_ident);
		} else {
			$tcaDef['MM_match_fields'] = array('ident' => $MM_ident);
		}
	}

	return $tcaDef;
}

/**
 * Adds an entry to the "ds" array of the tt_content field "ce_flexform".
 *
 * @param	string		The same value as the key for the plugin
 * @param	string		Either a reference to a flex-form XML file (eg. "FILE:EXT:newloginbox/flexform_ds.xml") or the XML directly.
 * @return	void
 */
function txdam_addCTypeFlexFormValue($piKeyToMatch,$value,$field='ce_flexform')	{
	global $TCA;
	t3lib_div::loadTCA('tt_content');

	if (is_array($TCA['tt_content']['columns']) && is_array($TCA['tt_content']['columns'][$field]['config']['ds'])) {
		$TCA['tt_content']['columns'][$field]['config']['ds'][$piKeyToMatch] = $value;
	}
}



?>