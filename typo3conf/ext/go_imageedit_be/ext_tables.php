<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');

$TCA['tt_content']['columns']['tx_goimageeditbe_croped_image'] = Array(
			'label' => 'LLL:EXT:go_imageedit_be/locallang_db.xml:elementTypeTitle',
			'config' => Array (
				'type' => 'user',
				'userFunc' => 'tx_imageedit->init'
			)
);
$TCA['tt_content']['imageedit']['default']= Array
											(
											"debug" => 0,						//gibt einige Debugwerte aus
											"imgPath" => '../uploads/pics/', 	// vom Backend aus gesehen
											"rootImgPath" => 'uploads/pics/', 	// vom Frontend aus
											
											//Backend
											"selector" => Array(
												"allowCustomRatio" => 1,		//dieses Flag lässt den benutzer 
																				//das Format des Selectors frei bestimmen
																			
												"lockWH" => 0,					//sperrt die Aktuelle Höhe und Breite
												"formatW" => '',				//Aus den Werten <FormatW>, <FormatH> wird beim erstmaligen angucken
												"formatH" => '',				// das Selector-Format berechnet
												
												"minHeight" => 500,
												"minWidth" => 500
											),
											
											"menu" => Array(					
												"displayType" => 0,					// 	1 : HTML-SELECT-BOX;  	
																					//	0 : BUTTONS (nachfolgende Einstellungen)
												"showImageName" => 0,				//Zeigt den Namen des Bildes an
												"showThumbnail" => 1,				//Zeigt ein Thumbnail 
												"showThumbnail_size" => "150x120",	//diesen Ausmaßes
												"showResolution" => 1,				//Zeigt die Auflösung der Bilder im Selector an
												
												"maxImages" =>1000,
											),
											
											"adjustResolution" => Array(
												"enabled" => 1,					//Bild runterrechnen ( 1 ) wenn > maxDisplayedWidth & maxDisplayedHeight
												"maxDisplayedWidth" => "700",		//hoechste unangetastete im Backend Angezeigte Auflösung
												"maxDisplayedHeight" => "400",
											),
											);

$goImageEditShowitem = $TCA['tt_content']['types']['textpic']['showitem'];
$goImageEditShowitem = substr_replace($goImageEditShowitem, '--div--;LLL:EXT:go_imageedit_be/locallang_db.xml:tabLabel,tx_goimageeditbe_croped_image,', strrpos($goImageEditShowitem, '--div--'), 0);
$TCA['tt_content']['types']['textpic']['showitem']= $goImageEditShowitem;

$goImageEditShowitem = $TCA['tt_content']['types']['image']['showitem'];
$goImageEditShowitem = substr_replace($goImageEditShowitem, '--div--;LLL:EXT:go_imageedit_be/locallang_db.xml:tabLabel,tx_goimageeditbe_croped_image,', strrpos($goImageEditShowitem, '--div--'), 0);
$TCA['tt_content']['types']['image']['showitem']= $goImageEditShowitem;

?>