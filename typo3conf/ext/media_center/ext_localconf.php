<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// add user ts config
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_mediacenter_item = 1');

// add the plugin pi1
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_mediacenter_pi1.php', '_pi1', 'list_type', 0);
?>