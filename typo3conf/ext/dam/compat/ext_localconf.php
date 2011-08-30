<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (t3lib_extMgm::isLoaded('rtehtmlarea')) {
	if (t3lib_div::int_from_ver( TYPO3_version ) < 4003000) {
		$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rtehtmlarea/mod4/class.tx_rtehtmlarea_select_image.php'] = PATH_txdam.'compat/class.ux_tx_rtehtmlarea_select_image.php';
		$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/browse_links.php']['browserRendering'][] = PATH_txdam.'compat/class.tx_dam_rtehtmlarea_select_image.php:&tx_dam_rtehtmlarea_select_image';
		$TYPO3_CONF_VARS['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook'][] =  PATH_txdam.'compat/class.tx_dam_rtehtmlarea_browse_links.php:&tx_dam_rtehtmlarea_browse_links';
	} else {
			// Hooks for images and links
		$TYPO3_CONF_VARS['SC_OPTIONS']['ext/rtehtmlarea/mod4/class.tx_rtehtmlarea_select_image.php']['browseLinksHook'][] =  PATH_txdam.'compat/class.tx_dam_rtehtmlarea_browse_media.php:&tx_dam_rtehtmlarea_browse_media';
		$TYPO3_CONF_VARS['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook'][] =  PATH_txdam.'compat/class.tx_dam_rtehtmlarea_browse_links.php:&tx_dam_rtehtmlarea_browse_links';
			// Configure additional attributes on links
			// htmlArea RTE MUST be installed before DAM for this to work...
		if ($TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes']) {
			$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'] .= ',txdam,usedamcolumn';
		} else {
			$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'] = 'txdam,usedamcolumn';
		}
	}
}

?>