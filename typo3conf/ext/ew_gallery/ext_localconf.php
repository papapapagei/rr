<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_ewgallery_pi1.php', '_pi1', 'CType', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_ewgallery_pi2.php', '_pi2', 'CType', 1);
?>