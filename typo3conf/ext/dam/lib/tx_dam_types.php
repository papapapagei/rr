<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Types for the 'dam'extension.
 * Part of the DAM (digital asset management) extension.
 *
 * Shouldn't be used directly. Use functions instead (tx_dam, tx_dam_indexing, ....)
 *
 * Inspired by dublin core
 *
 * Image, Text, Sound, Dataset (cvs, xml),
 * Interactive (flash), Software (exe, zip),
 * Collection (div data in zip),
 * Service (uri,...)
 * and so on
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Constants
 */



if(!defined('TXDAM_mtype_undefined')) {


	/**
	 *  Return values for some index related functions
	 */

	/**
	 * file is not indexed and not found in filesystem
	 */
	define ('TXDAM_file_notfound', -2);
	/**
	 * file is not yet indexed
	 */
	define ('TXDAM_file_unknown', -1);
	/**
	 * File is indexed and up to date
	 */
	define ('TXDAM_file_ok', 1);
	/**
	 * file is indexed but has changed in the file system
	 */
	define ('TXDAM_file_changed', 2);
	/**
	 * file is indexed but not found in filesystem
	 */
	define ('TXDAM_file_missing', -0xFF);



	/**
	 * status values for file_status field
	 */

	/**
	 * file status is fine
	 *
	 * status values for file_status field
	 */
	define ('TXDAM_status_file_ok', 0);

	/**
	 * file has changed, means was modified
	 *
	 * status values for file_status field
	 */
	define ('TXDAM_status_file_changed', 1);

	/**
	 * file status is missing
	 *
	 * status values for file_status field
	 * @package DAM-Core
	 * @subpackage Constants
	 */
	define ('TXDAM_status_file_missing', 0xFF);


	/**
	 * media types
	 */

	/**
	 * media type: unknown undefined
	 */
	define('TXDAM_mtype_undefined', 0);

	/**
	 * media type: text, documents, PDF
	 */
	define('TXDAM_mtype_text', 1);

	/**
	 * media type: image
	 */
	define('TXDAM_mtype_image', 2);

	/**
	 * media type: audio, wac, mp3
	 */
	define('TXDAM_mtype_audio', 3);

	/**
	 * media type: video
	 */
	define('TXDAM_mtype_video', 4);

	/**
	 * media type: interactive application. Eg Flash
	 */
	define('TXDAM_mtype_interactive', 5);

	/**
	 * media type: describes a location for a server. Eg. URI
	 */
	define('TXDAM_mtype_service', 6);

	/**
	 * media type: font file
	 */
	define('TXDAM_mtype_font', 7);

	/**
	 * media type: 3D data
	 */
	define('TXDAM_mtype_model', 8);

	/**
	 * media type: data like CSV
	 */
	define('TXDAM_mtype_dataset', 9);

	/**
	 * media type: archives like zip
	 */
	define('TXDAM_mtype_collection', 10);

	/**
	 * media type: software/executables
	 */
	define('TXDAM_mtype_software', 11);

	/**
	 * media type: special format file that needs a program to be processed or include not just one media. Example: InDesign or Scribus file
	 */
	define('TXDAM_mtype_application', 12);

}

/**
 * @access private
 */
$GLOBALS['T3_VAR']['ext']['dam']['media2code'] = array(
		'undefined'=> '0',
		'text'=> '1',
		'image'=> '2',
		'audio'=> '3',
		'video'=> '4',
		'interactive'=> '5',
		'service'=> '6',
		'font'=> '7',
		'model'=> '8',
		'dataset'=> '9',
		'collection'=> '10',
		'software'=> '11',
		'application'=> '12',
	);

/**
 * @access private
 */
$GLOBALS['T3_VAR']['ext']['dam']['code2media'] = array(
		'0'=> 'undefined',
		'1'=> 'text',
		'2'=> 'image',
		'3'=> 'audio',
		'4'=> 'video',
		'5'=> 'interactive',
		'6'=> 'service',
		'7'=> 'font',
		'8'=> 'model',
		'9'=> 'dataset',
		'10'=> 'collection',
		'11'=> 'software',
		'12'=> 'application',
	);

/**
 * @access private
 */
$GLOBALS['T3_VAR']['ext']['dam']['code2sorting'] = array(
		'1'=> 200, //'text',
		'2'=> 300, //'image',
		'3'=> 400, //'audio',
		'4'=> 500, //'video',
		'9'=> 600, //'dataset',
		'7'=> 700, //'font',
		'8'=> 800, //'model',
		'12'=> 900, //'application',
		'5'=> 1000, //'interactive',
		'6'=> 1100, //'service',
		'10'=> 1200, //'collection',
		'11'=> 1300, //'software',
		'0'=> 5000, //'undefined',
	);


/**
 * @access private
 */
if (!is_array($GLOBALS['T3_VAR']['ext']['dam']['thumbsizes']['BE'])) {
$GLOBALS['T3_VAR']['ext']['dam']['thumbsizes']['BE'] = array(
		'default'=> '56x56',
		'icon'=> '18x16',
		'xx-small'=> '56x56',
		'x-small'=> '64x64',
		'small'=> '96x96',
		'medium'=> '192x192',
		'large'=> '256x256',
		'x-large'=> '384x384',
		'xx-large'=> '512x512',
	);
}

/**
 * @access private
 */
if (!is_array($GLOBALS['T3_VAR']['ext']['dam']['thumbsizes']['FE'])) {
$GLOBALS['T3_VAR']['ext']['dam']['thumbsizes']['FE'] = array(
		'default'=> '96x96',
		'icon'=> '18x16',
		'xx-small'=> '64x64',
		'x-small'=> '96x96',
		'small'=> '128x128',
		'medium'=> '192x192',
		'large'=> '384x384',
		'x-large'=> '512x512',
		'xx-large'=> '800x600',
	);
}
?>
