<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_rbflashobject_movie"] = Array (
	"ctrl" => $TCA["tx_rbflashobject_movie"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,description,flashmovie,width,height,requiredversion,quality,displaymenu,alternativecontent,redirecturl,backgroundcolor,additionalparams,additionalvars"
	),
	"feInterface" => $TCA["tx_rbflashobject_movie"]["feInterface"],
	"columns" => Array (
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.description",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"flashmovie" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.flashmovie",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => 'swf',	
				"max_size" => 100000,	
				"uploadfolder" => "uploads/tx_rbflashobject",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"width" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.width",		
			"config" => Array (
				"type" => "input",	
				"size" => "10",	
				"eval" => "required",
			)
		),
		"height" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.height",		
			"config" => Array (
				"type" => "input",	
				"size" => "10",	
				"eval" => "required",
			)
		),
		"requiredversion" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.requiredversion",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",
			)
		),
		"quality" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.quality",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.quality.I.0", "0"),
					Array("LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.quality.I.1", "1"),
				),
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"displaymenu" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.displaymenu",		
			"config" => Array (
				"type" => "check",
			)
		),
		"alternativecontent" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.alternativecontent",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tt_content",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"redirecturl" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.redirecturl",		
			"config" => Array (
				"type" => "input",		
				"size" => "15",
				"max" => "255",
				"checkbox" => "",
				"eval" => "trim",
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
		"backgroundcolor" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.backgroundcolor",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"wizards" => Array(
					"_PADDING" => 2,
					"color" => Array(
						"title" => "Color:",
						"type" => "colorbox",
						"dim" => "10x10",
						"tableStyle" => "border:solid 1px black;",
						"script" => "wizard_colorpicker.php",
						"JSopenParams" => "height=300,width=360,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
		"additionalparams" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.additionalparams",		
	        'config' => Array (
	            'type' => 'text',
	            'wrap' => 'OFF',
	            'cols' => '30',    
	            'rows' => '6',
	        )
		),
		"additionalvars" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:rb_flashobject/locallang_db.php:tx_rbflashobject_movie.additionalvars",		
	        'config' => Array (
	            'type' => 'text',
	            'wrap' => 'OFF',
	            'cols' => '30',    
	            'rows' => '6',
	        )
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;;;1-1-1, description;;;;1-1-1, flashmovie, width, height, requiredversion;;;;1-1-1, quality, displaymenu, alternativecontent, redirecturl, backgroundcolor, additionalparams, additionalvars")
	),
	"palettes" => Array (
	)
);
?>