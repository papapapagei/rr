<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	// Registering user-defined soft reference parsers
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['media'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['mediatag'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['typolink'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['typolink_tag'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['dam_mm_ref'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';
$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['softRefParser']['dam_file'] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:&tx_dam_softrefproc';

//Hooks for impexp
$TYPO3_CONF_VARS['SC_OPTIONS']['ext/impexp/class.tx_impexp.php']['before_processSoftReferences'][] = 'EXT:dam/binding/softref/class.tx_dam_softrefproc.php:tx_dam_softrefproc->impexpHookBeforeSoftrefUpdate';
?>