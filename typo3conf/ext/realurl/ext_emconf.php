<?php

########################################################################
# Extension Manager/Repository config file for ext "realurl".
#
# Auto generated 09-11-2010 11:10
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'RealURL: speaking paths for TYPO3',
	'description' => 'Creates nice looking URLs for TYPO3 pages: converts http://example.com/index.phpid=12345&L=2 to http://example.com/path/to/your/page/. Please, ask for free support in TYPO3 mailing lists or contact the maintainer for paid support.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.10.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages,sys_domain,pages_language_overlay,sys_template',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Dmitry Dulepov',
	'author_email' => 'dmitry@typo3.org',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.2.6-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:9:"ChangeLog";s:4:"9019";s:10:"_.htaccess";s:4:"a6b1";s:20:"class.ext_update.php";s:4:"69d5";s:20:"class.tx_realurl.php";s:4:"1706";s:29:"class.tx_realurl_advanced.php";s:4:"a1c1";s:32:"class.tx_realurl_autoconfgen.php";s:4:"5226";s:28:"class.tx_realurl_tcemain.php";s:4:"93e1";s:21:"ext_conf_template.txt";s:4:"c890";s:12:"ext_icon.gif";s:4:"ea80";s:17:"ext_localconf.php";s:4:"e297";s:14:"ext_tables.php";s:4:"e895";s:14:"ext_tables.sql";s:4:"a7e9";s:17:"locallang_csh.xml";s:4:"f624";s:16:"locallang_db.xml";s:4:"5092";s:12:"doc/TODO.txt";s:4:"b8cb";s:14:"doc/manual.sxw";s:4:"562d";s:38:"modfunc1/class.tx_realurl_modfunc1.php";s:4:"8dc3";s:41:"modfunc1/class.tx_realurl_pagebrowser.php";s:4:"99cb";s:22:"modfunc1/locallang.xml";s:4:"70b9";}',
);

?>