<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(PATH_typo3conf."ext/go_imageedit_be/class.imageedit.php");

$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['tslib/class.tslib_content.php'] = t3lib_extMgm::extPath('go_imageedit_be').'class.ux_tslib_content.php';

?>