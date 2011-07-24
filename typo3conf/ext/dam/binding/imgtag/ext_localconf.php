<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  // Extending TypoScript from static template uid=43 to set up parsing of custom attribute "txdam" on img tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
	#******************************************************
	# Including library for processing of custom attribute "txdam" on img tag
	#******************************************************
	includeLibs.tx_dam_tsfeimgtag = EXT:dam/binding/imgtag/class.tx_dam_tsfeimgtag.php
	
	lib.parseFunc_RTE {
		tags.img = TEXT
		tags.img {
			current = 1
			preUserFunc = tx_dam_tsfeimgtag->renderTxdamAttribute
		}
		nonTypoTagStdWrap.HTMLparser.tags.img.fixAttrib {
			txdam.unset = 1
		}
	}
',43);

?>