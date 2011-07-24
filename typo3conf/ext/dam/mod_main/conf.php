<?php

define('TYPO3_MOD_PATH', '../typo3conf/ext/dam/mod_main/');
$BACK_PATH='../../../../typo3/';
$MCONF['name']='txdamM1';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref']='LLL:EXT:dam/mod_main/locallang_mod.xml';

$MCONF['access']='user,group';
$MCONF['navFrameScript']='tx_dam_navframe.php';
$MCONF['defaultMod']='list';

?>