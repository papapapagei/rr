<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	// Registering user-defined soft reference parsers
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['media'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['mediatag'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['typolink'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['typolink_tag'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
?>