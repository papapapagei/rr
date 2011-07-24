<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  // Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'setup','

	plugin.tx_dam_tsfemediatag {
			procFields {
				file_size = 1
			}
			tag {
				current = 1
				typolink.parameter.data = parameters : allParams
				typolink.extTarget = {$styles.content.links.extTarget}
				typolink.target = {$styles.content.links.target}
				typolink.title { 
					dataWrap = { field : txdam_file_name } ({ field : txdam_file_size })
					htmlspecialchars = 1
				}
				parseFunc.constants =1
			}
		}

	lib.parseFunc.tags.media = < plugin.tx_dam_tsfemediatag
	lib.parseFunc_RTE.tags.media = < plugin.tx_dam_tsfemediatag
	// content-default?: tt_content.text.20.parseFunc.tags.media = < plugin.tx_dam_tsfemediatag
',43);


	// register rendering plugin
t3lib_extMgm::addPItoST43($_EXTKEY,'binding/mediatag/class.tx_dam_tsfemediatag.php','_tsfemediatag','',1);


	// Add default Page TSonfig RTE configuration for enabling media links
t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/binding/mediatag/pageTSConfig.txt">');


	// register RTE transformation for media tag
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_parsehtml_proc.php']['transformation']['txdam_media'] = 'EXT:dam/binding/mediatag/class.tx_dam_rtetransform_mediatag.php:&tx_dam_rtetransform_mediatag';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_parsehtml_proc.php']['transformation']['ts_links'] = 'EXT:dam/binding/mediatag/class.tx_dam_rtetransform_ahref.php:&tx_dam_rtetransform_ahref';


?>