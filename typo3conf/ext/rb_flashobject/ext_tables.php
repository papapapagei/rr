<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_extMgm::allowTableOnStandardPages("tx_rbflashobject_movie");

$TCA["tx_rbflashobject_movie"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie",		
		"label" => "description",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_rbflashobject_movie.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, description, flashmovie, width, height, requiredversion, quality, displaymenu, alternativecontent, redirecturl, backgroundcolor, additionalparams, additionalvars",
	)
);


$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:rb_flashobject/flexform_ds.xml');



t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key,pages";


t3lib_extMgm::addPlugin(Array("LLL:EXT:rb_flashobject/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");
if (TYPO3_MODE=='BE')	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_rbflashobject_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_rbflashobject_pi1_wizicon.php';

#t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Flash Movie");
?>