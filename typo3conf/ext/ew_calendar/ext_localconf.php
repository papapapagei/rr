<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_ewcalendar_dates=1
	options.saveDocNew.tx_ewcalendar_productions=1
');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_ewcalendar_pi1.php', '_pi1', 'CType', 1);
?>