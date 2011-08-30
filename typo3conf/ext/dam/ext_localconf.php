<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

if (!defined ('PATH_txdam_rel')) {
	define('PATH_txdam_rel', t3lib_extMgm::extRelPath('dam'));
}

if (!defined ('PATH_txdam_siteRel')) {
	define('PATH_txdam_siteRel', t3lib_extMgm::siteRelPath('dam'));
}


	// that's the base API
require_once(PATH_txdam.'lib/class.tx_dam.php');
	// include basic image stuff because it's used so often
require_once(PATH_txdam.'lib/class.tx_dam_image.php');


	// get extension setup
$TYPO3_CONF_VARS['EXTCONF']['dam']['setup'] = unserialize($_EXTCONF);


if ($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['tsconfig']==='default')	{
	t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:dam/tsconfig/default.txt">');
} elseif ($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['tsconfig']==='minimal')	{
	t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:dam/tsconfig/minimal.txt">');
} elseif ($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['tsconfig']==='example')	{
	t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:dam/tsconfig/example.txt">');
}


	// set some config values from extension setup
tx_dam::config_setValue('setup.devel', $TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['devel']);

	// register default icons
tx_dam::register_fileIconPath(PATH_txdam.'i/18/', 'FE');
tx_dam::register_fileIconPath(PATH_txdam.'i/18/', 'BE');


	// would register TYPO3's default file icons
# tx_dam::register_fileIconPath(PATH_typo3.'gfx/fileicons/', 'BE');


	// field templates for usage in other tables to link media records
require_once(PATH_txdam.'tca_media_field.php');



	// register XCLASS of t3lib_extfilefunc to pipe all TCE stuff through DAM version
$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_extfilefunc.php'] = PATH_txdam.'lib/class.tx_dam_tce_file.php';

	// setup interface to htmlArea RTE
if ($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['htmlAreaBrowser']) {
	require_once(PATH_txdam.'compat/ext_localconf.php');
}


	// register show item rendering
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/show_item.php']['typeRendering'][] = 'EXT:dam/binding/be/class.tx_dam_show_item.php:&tx_dam_show_item';
	// register element browser rendering
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/browse_links.php']['browserRendering'][] = 'EXT:dam/class.tx_dam_browse_media.php:&tx_dam_browse_media';
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/browse_links.php']['browserRendering'][] = 'EXT:dam/class.tx_dam_browse_category.php:&tx_dam_browse_category';
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/browse_links.php']['browserRendering'][] = 'EXT:dam/class.tx_dam_browse_folder.php:&tx_dam_browse_folder';
	// register secure downloads processor
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][] = 'EXT:dam/lib/class.tx_dam_tsfe.php:&tx_dam_tsfe';

tx_dam::register_indexingRule ('tx_damindex_rule_recursive', 'EXT:dam/components/class.tx_dam_index_rules.php:&tx_dam_index_rule_recursive');
tx_dam::register_indexingRule ('tx_damindex_rule_folderAsCat', 'EXT:dam/components/class.tx_dam_index_rules.php:&tx_dam_index_rule_folderAsCat');
tx_dam::register_indexingRule ('tx_damindex_rule_doReindexing', 'EXT:dam/components/class.tx_dam_index_rules.php:&tx_dam_index_rule_doReindexing');
tx_dam::register_indexingRule ('tx_damindex_rule_titleFromFilename', 'EXT:dam/components/class.tx_dam_index_rules.php:&tx_dam_index_rule_titleFromFilename');
tx_dam::register_indexingRule ('tx_damindex_rule_dryRun', 'EXT:dam/components/class.tx_dam_index_rules.php:&tx_dam_index_rule_dryRun');

if($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['devel']) {
	tx_dam::register_indexingRule ('tx_damindex_rule_devel', 'EXT:dam/components/class.tx_dam_index_rules.php:&tx_dam_index_rule_devel');
}

	// register navigation tree and select rule for nav tree.
tx_dam::register_selection ('txdamFolder',    'EXT:dam/components/class.tx_dam_selectionFolder.php:&tx_dam_selectionFolder');
tx_dam::register_selection ('txdamCat',       'EXT:dam/components/class.tx_dam_selectionCategory.php:&tx_dam_selectionCategory');
tx_dam::register_selection ('txdamMedia',     'EXT:dam/components/class.tx_dam_selectionMeTypes.php:&tx_dam_selectionMeTypes');
tx_dam::register_selection ('txdamStatus',    'EXT:dam/components/class.tx_dam_selectionStatus.php:&tx_dam_selectionStatus');
tx_dam::register_selection ('txdamIndexRun',  'EXT:dam/components/class.tx_dam_selectionIndexRun.php:&tx_dam_selectionIndexRun');
tx_dam::register_selection ('txdamStrSearch', 'EXT:dam/components/class.tx_dam_selectionStringSearch.php:&tx_dam_selectionStringSearch');
tx_dam::register_selection ('txdamRecords',   'EXT:dam/components/class.tx_dam_selectionRecords.php:&tx_dam_selectionRecords');

	// register DAM internal db change trigger
tx_dam::register_dbTrigger ('tx_dam_dbTriggerMediaTypes', 'EXT:dam/components/class.tx_dam_dbTriggerMediaTypes.php:&tx_dam_dbTriggerMediaTypes');

	// register special TCE tx_dam processing
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:dam/binding/tce/class.tx_dam_tce_process.php:&tx_dam_tce_process';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:dam/binding/tce/class.tx_dam_tce_process.php:&tx_dam_tce_process';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:dam/binding/tce/class.tx_dam_tce_filetracking.php:&tx_dam_tce_filetracking';


	// <media> tag for BE and FE
if ($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['mediatag']) {
	require_once(PATH_txdam.'binding/mediatag/ext_localconf.php');
}

	// user-defined soft reference parsers
require_once(PATH_txdam.'binding/softref/ext_localconf.php');

	// txdam attribute on img tag for FE
require_once(PATH_txdam.'binding/imgtag/ext_localconf.php');

	// txdam linkvalidator support
if (t3lib_extMgm::isLoaded('linkvalidator')) {
	require_once(PATH_txdam.'binding/linkvalidator/ext_localconf.php');
}

	// FE stuff

$pluginContent = t3lib_div::getUrl(PATH_txdam.'pi/setup.txt');
t3lib_extMgm::addTypoScript('dam', 'setup','
# Setting dam plugin TypoScript
'.$pluginContent);
unset($pluginContent);

$TYPO3_CONF_VARS['BE']['AJAX']['TYPO3_tcefile::process'] = PATH_txdam.'lib/class.tx_dam_tce_file.php:tx_dam_tce_file->processAjaxRequest';

?>
