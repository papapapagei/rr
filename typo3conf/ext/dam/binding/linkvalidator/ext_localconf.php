<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['linkvalidator']['checkLinks']['dam'] =  PATH_txdam . 'binding/linkvalidator/class.tx_linkvalidator_linktype_dam.php:tx_linkvalidator_linktype_Dam';

?>
