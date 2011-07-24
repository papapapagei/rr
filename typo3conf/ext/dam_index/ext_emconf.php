<?php

########################################################################
# Extension Manager/Repository config file for ext "dam_index".
#
# Auto generated 24-09-2010 02:05
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Media>Indexing',
	'description' => 'Provides a Media submodule for mass indexing of files.',
	'category' => 'module',
	'shy' => 0,
	'version' => '1.1.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'The DAM development team',
	'author_email' => 'typo3-project-dam@lists.netfielders.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.0.0-0.0.0',
			'typo3' => '3.8.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:13:{s:9:"ChangeLog";s:4:"5c06";s:21:"ext_conf_template.txt";s:4:"a5f0";s:12:"ext_icon.gif";s:4:"6103";s:14:"ext_tables.php";s:4:"c388";s:14:"doc/manual.sxw";s:4:"ec3d";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"42d3";s:14:"mod1/index.php";s:4:"c8d4";s:18:"mod1/locallang.xml";s:4:"37b0";s:22:"mod1/locallang_mod.xml";s:4:"6cab";s:19:"mod1/moduleicon.gif";s:4:"4cbf";s:41:"modfunc_index/class.tx_damindex_index.php";s:4:"b953";s:27:"modfunc_index/locallang.xml";s:4:"ed32";}',
);

?>