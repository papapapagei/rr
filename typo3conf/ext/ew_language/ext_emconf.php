<?php

########################################################################
# Extension Manager/Repository config file for ext: "ew_language"
#
# Auto generated 31-07-2009 10:32
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Language Menu',
	'description' => 'This extension creates an language menu based on the table sys_language.
It also checks the default browser-langauges and switch the page language to the browser language',
	'category' => 'fe',
	'author' => 'Elio Wahlen',
	'author_email' => 'vorname at vorname.de',
	'shy' => '',
	'dependencies' => 'static_info_tables, realurl',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'sys_language',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.9.1',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-0.0.0',
			'typo3' => '4.5.0-0.0.0',
			'realurl' => '1.11.1-0.0.0',
			'static_info_tables' => '2.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>