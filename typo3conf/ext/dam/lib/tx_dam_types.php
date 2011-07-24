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


/**
 * @access private
 */
$GLOBALS['T3_VAR']['ext']['dam']['file2mime'] = array(

		'au'	=> 'audio/basic',
		'snd'	=> 'audio/basic',
		'mid'	=> 'audio/midi',
		'midi'	=> 'audio/midi',
		'kar'	=> 'audio/midi',
		'mpga'	=> 'audio/mpeg',
		'mpega'	=> 'audio/mpeg',
		'm3u'	=> 'audio/mpegurl',
		'sid'	=> 'audio/prs.sid',
		'aifc'	=> 'audio/x-aiff',
		'aif'	=> 'audio/x-aiff',
		'aiff'	=> 'audio/x-aiff',
		'faif'	=> 'audio/x-aiff',
		'pae'	=> 'audio/x-epac',
		'gsm'	=> 'audio/x-gsm',
		'uni'	=> 'audio/x-mod',
		'mtm'	=> 'audio/x-mod',
		'mod'	=> 'audio/x-mod',
		's3m'	=> 'audio/x-mod',
		'it'	=> 'audio/x-mod',
		'stm'	=> 'audio/x-mod',
		'ult'	=> 'audio/x-mod',
		'xm'	=> 'audio/x-mod',
		'mp2'	=> 'audio/x-mpeg',
		'mp3'	=> 'audio/x-mpeg',
		'wax'	=> 'audio/x-ms-wax',
		'wma'	=> 'audio/x-ms-wma',
		'pac'	=> 'audio/x-pac',
		'ram'	=> 'audio/x-pn-realaudio',
		'ra'	=> 'audio/x-pn-realaudio',
		'rm'	=> 'audio/x-pn-realaudio',
		'wav'	=> 'audio/x-wav',
		'm4a'	=> 'audio/x-m4a',
		'm4b'	=> 'audio/mp4a-latm',
		'm4p'	=> 'audio/mp4a-latm',
		'm4r'	=> 'audio/aac',		

		'z' 	=> 'encoding/x-compress',
		'gz'	=> 'encoding/x-gzip',

		'bmp'	=> 'image/bitmap',
		'gif'	=> 'image/gif',
		'ief'	=> 'image/ief',
		'jpg'	=> 'image/jpeg',
		'jpeg'	=> 'image/jpeg',
		'jpe'	=> 'image/jpeg',
		'pcx'	=> 'image/pcx',
		'png'	=> 'image/png',
		'tiff'	=> 'image/tiff',
		'tif'	=> 'image/tiff',
		'wbmp'	=> 'image/vnd.wap.wbmp',
		'ras'	=> 'image/x-cmu-raster',
		'cdr'	=> 'image/x-coreldraw',
		'pat'	=> 'image/x-coreldrawpattern',
		'cdt'	=> 'image/x-coreldrawtemplate',
		'cpt'	=> 'image/x-corelphotopaint',
#		'eps'	=> 'image/x-eps',
		'jng'	=> 'image/x-jng',
		'pcd'	=> 'image/x-photo-cd',
		'pnm'	=> 'image/x-portable-anymap',
		'pbm'	=> 'image/x-portable-bitmap',
		'pgm'	=> 'image/x-portable-graymap',
		'ppm'	=> 'image/x-portable-pixmap',
		'rgb'	=> 'image/x-rgb',
		'xbm'	=> 'image/x-xbitmap',
		'xpm'	=> 'image/x-xpixmap',
		'xwd'	=> 'image/x-xwindowdump',
		'svg'	=> 'image/svg+xml',
		'xcf'	=> 'image/xcf',

		'iges'	=> 'model/iges',
		'igs'	=> 'model/iges',
		'msh'	=> 'model/mesh',
		'silo'	=> 'model/mesh',
		'mesh'	=> 'model/mesh',
		'vrml'	=> 'model/vrml',
		'wrl'	=> 'model/vrml',

		'vfb'	=> 'text/calendar',
		'ifb'	=> 'text/calendar',
		'ics'	=> 'text/calendar',
		'csv'	=> 'text/comma-separated-values',
		'css'	=> 'text/css',
		'patch'	=> 'text/diff',
		'html'	=> 'text/html',
		'xhtml'	=> 'text/html',
		'htm'	=> 'text/html',
		'shtml'	=> 'text/html',
		'mml'	=> 'text/mathml',
		'log'	=> 'text/plain',
		'txt'	=> 'text/plain',
		'po'	=> 'text/plain',
		'asc'	=> 'text/plain',
		'diff'	=> 'text/plain',
		'text'	=> 'text/plain',
		'rtx'	=> 'text/richtext',
		'sgml'	=> 'text/sgml',
		'sgm'	=> 'text/sgml',
		'tsv'	=> 'text/tab-separated-values',
		'wml'	=> 'text/vnd.wap.wml',
		'wmls'	=> 'text/vnd.wap.wmlscript',
		'hxx'	=> 'text/x-c++hdr',
		'hpp'	=> 'text/x-c++hdr',
		'h++'	=> 'text/x-c++hdr',
		'hh'	=> 'text/x-c++hdr',
		'cc'	=> 'text/x-c++src',
		'c++'	=> 'text/x-c++src',
		'cpp'	=> 'text/x-c++src',
		'cxx'	=> 'text/x-c++src',
		'h' 	=> 'text/x-chdr',
		'c' 	=> 'text/x-csrc',
		'java'	=> 'text/x-java',
		'pas'	=> 'text/x-pascal',
		'p' 	=> 'text/x-pascal',
		'etx'	=> 'text/x-setext',
		'tk'	=> 'text/x-tcl',
		'ltx'	=> 'text/x-tex',
		'sty'	=> 'text/x-tex',
		'cls'	=> 'text/x-tex',
		'vcs'	=> 'text/x-vcalendar',
		'vcf'	=> 'text/x-vcard',
		'xsl'	=> 'text/xml',
		'xml'	=> 'text/xml',

		'dl'	=> 'video/dl',
		'gl'	=> 'video/gl',
		'mpg'	=> 'video/mpeg',
		'mpeg'	=> 'video/mpeg',
		'mpe'	=> 'video/mpeg',
		'qt'	=> 'video/quicktime',
		'mov'	=> 'video/quicktime',
		'mxu'	=> 'video/vnd.mpegurl',
		'iff'	=> 'video/x-anim',
		'anim3'	=> 'video/x-anim',
		'anim7'	=> 'video/x-anim',
		'anim'	=> 'video/x-anim',
		'anim5'	=> 'video/x-anim',
		'flc'	=> 'video/x-flc',
		'fli'	=> 'video/x-fli',
		'flv'	=> 'video/x-flv',
		'mng'	=> 'video/x-mng',
		'asx'	=> 'video/x-ms-asf',
		'asf'	=> 'video/x-ms-asf',
		'wm'	=> 'video/x-ms-wm',
		'wmv'	=> 'video/x-ms-wmv',
		'wmx'	=> 'video/x-ms-wmx',
		'wvx'	=> 'video/x-ms-wvx',
		'avi'	=> 'video/x-msvideo',
		'avx'	=> 'video/x-rad-screenplay',
		'mv'	=> 'video/x-sgi-movie',
		'movi'	=> 'video/x-sgi-movie',
		'movie'	=> 'video/x-sgi-movie',
		'vcr'	=> 'video/x-sunvideo',
		'mp4'	=> 'video/mp4v-es',
		'm4v'	=> 'video/x-m4v',
		'mp4v'	=> 'video/mp4v-es',

		'ez'	=> 'application/andrew-inset',
		'cu'	=> 'application/cu-seeme',
		'csm'	=> 'application/cu-seeme',
		'tsp'	=> 'application/dsptype',
		'fif'	=> 'application/fractals',
		'spl'	=> 'application/futuresplash',
		'hqx'	=> 'application/mac-binhex40',
		'mdb'	=> 'application/msaccess',
		'xls'	=> 'application/msexcel',
		'xlw'	=> 'application/msexcel',
		'hlp'	=> 'application/mshelp',
		'ppt'	=> 'application/mspowerpoint',
		'mpx'	=> 'application/msproject',
		'mpw'	=> 'application/msproject',
		'mpp'	=> 'application/msproject',
		'mpt'	=> 'application/msproject',
		'mpc'	=> 'application/msproject',
		'doc'	=> 'application/msword',
		'so'	=> 'application/octet-stream',
		'bin'	=> 'application/octet-stream',
		'exe'	=> 'application/octet-stream',
		'oda'	=> 'application/oda',
		'pdf'	=> 'application/pdf',
		'pgp'	=> 'application/pgp-signature',
		'eps'	=> 'application/postscript',
		'ai'	=> 'application/postscript',
		'ps'	=> 'application/postscript',
		'rtf'	=> 'application/rtf',
		'smi'	=> 'application/smil',
		'smil'	=> 'application/smil',
		'xlb'	=> 'application/vnd.ms-excel',
		'pot'	=> 'application/vnd.ms-powerpoint',
		'pps'	=> 'application/vnd.ms-powerpoint',
		'sxc'	=> 'application/vnd.sun.xml.calc',
		'stc'	=> 'application/vnd.sun.xml.calc.template',
		'sxd'	=> 'application/vnd.sun.xml.draw',
		'std'	=> 'application/vnd.sun.xml.draw.template',
		'sxi'	=> 'application/vnd.sun.xml.impress',
		'sti'	=> 'application/vnd.sun.xml.impress.template',
		'sxm'	=> 'application/vnd.sun.xml.math',
		'sxw'	=> 'application/vnd.sun.xml.writer',
		'sxg'	=> 'application/vnd.sun.xml.writer.global',
		'stw'	=> 'application/vnd.sun.xml.writer.template',
		'vsd'	=> 'application/vnd.visio',
		'wbxml'	=> 'application/vnd.wap.wbxml',
		'wmlc'	=> 'application/vnd.wap.wmlc',
		'wmlsc'	=> 'application/vnd.wap.wmlscriptc',
		'wp5'	=> 'application/wordperfect5.1',
		'wk'	=> 'application/x-123',
		'aw'	=> 'application/x-applix',
		'bcpio'	=> 'application/x-bcpio',
		'vcd'	=> 'application/x-cdlink',
		'pgn'	=> 'application/x-chess-pgn',
		'Z' 	=> 'application/x-compress',
		'cpio'	=> 'application/x-cpio',
		'csh'	=> 'application/x-csh',
		'deb'	=> 'application/x-debian-package',
		'dcr'	=> 'application/x-director',
		'dxr'	=> 'application/x-director',
		'dir'	=> 'application/x-director',
		'dms'	=> 'application/x-dms',
		'dot'	=> 'application/x-dot',
		'dvi'	=> 'application/x-dvi',
		'fmr'	=> 'application/x-fmr',
		'pcf'	=> 'application/x-font',
		'pcf.Z'	=> 'application/x-font',
		'gsf'	=> 'application/x-font',
		'pfb'	=> 'application/x-font',
		'pfa'	=> 'application/x-font',
		'fr'	=> 'application/x-fr',
		'gnumeric'	=> 'application/x-gnumeric',
		'tgz'	=> 'application/x-gtar',
		'gtar'	=> 'application/x-gtar',
		'hdf'	=> 'application/x-hdf',
		'pht'	=> 'application/x-httpd-php',
		'php'	=> 'application/x-httpd-php',
		'phtml'	=> 'application/x-httpd-php',
		'php3'	=> 'application/x-httpd-php3',
		'php3p'	=> 'application/x-httpd-php3-preprocessed',
		'phps'	=> 'application/x-httpd-php3-source',
		'php4'	=> 'application/x-httpd-php4',
		'ica'	=> 'application/x-ica',
		'class'	=> 'application/x-java',
		'js'	=> 'application/x-javascript',
		'chrt'	=> 'application/x-kchart',
		'kil'	=> 'application/x-killustrator',
		'skd'	=> 'application/x-koan',
		'skt'	=> 'application/x-koan',
		'skp'	=> 'application/x-koan',
		'skm'	=> 'application/x-koan',
		'kpr'	=> 'application/x-kpresenter',
		'kpt'	=> 'application/x-kpresenter',
		'ksp'	=> 'application/x-kspread',
		'kwt'	=> 'application/x-kword',
		'kwd'	=> 'application/x-kword',
		'latex'	=> 'application/x-latex',
		'lha'	=> 'application/x-lha',
		'lzh'	=> 'application/x-lzh',
		'lzx'	=> 'application/x-lzx',
		'frm'	=> 'application/x-maker',
		'book'	=> 'application/x-maker',
		'fbdoc'	=> 'application/x-maker',
		'fm'	=> 'application/x-maker',
		'frame'	=> 'application/x-maker',
		'fb'	=> 'application/x-maker',
		'maker'	=> 'application/x-maker',
		'mif'	=> 'application/x-mif',
		'mi'	=> 'application/x-mif',
		'wmd'	=> 'application/x-ms-wmd',
		'wmz'	=> 'application/x-ms-wmz',
		'bat'	=> 'application/x-msdos-program',
		'com'	=> 'application/x-msdos-program',
		'dll'	=> 'application/x-msdos-program',
		'msi'	=> 'application/x-msi',
		'nc'	=> 'application/x-netcdf',
		'cdf'	=> 'application/x-netcdf',
		'proxy'	=> 'application/x-ns-proxy-autoconfig',
		'o' 	=> 'application/x-object',
		'ogg'	=> 'application/x-ogg',
		'oza'	=> 'application/x-oz-application',
		'perl'	=> 'application/x-perl',
		'pm'	=> 'application/x-perl',
		'pl'	=> 'application/x-perl',
		'qxd'	=> 'application/x-quark-xpress-3',
		'rpm'	=> 'application/x-redhat-package-manager',
		'sh'	=> 'application/x-sh',
		'shar'	=> 'application/x-shar',
		'swf'	=> 'application/x-shockwave-flash',
		'swfl'	=> 'application/x-shockwave-flash',
		'sit'	=> 'application/x-stuffit',
		'tar'	=> 'application/x-tar',
		'tcl'	=> 'application/x-tcl',
		'tex'	=> 'application/x-tex',
		'gf'	=> 'application/x-tex-gf',
		'pk'	=> 'application/x-tex-pk',
		'PK'	=> 'application/x-tex-pk',
		'texinfo'	=> 'application/x-texinfo',
		'texi'	=> 'application/x-texinfo',
		'tki'	=> 'application/x-tkined',
		'tkined'	=> 'application/x-tkined',
		'%' 	=> 'application/x-trash',
		'sik'	=> 'application/x-trash',
		'~' 	=> 'application/x-trash',
		'old'	=> 'application/x-trash',
		'bak'	=> 'application/x-trash',
		'tr'	=> 'application/x-troff',
		'roff'	=> 'application/x-troff',
		't' 	=> 'application/x-troff',
		'man'	=> 'application/x-troff-man',
		'me'	=> 'application/x-troff-me',
		'ms'	=> 'application/x-troff-ms',
		'zip'	=> 'application/x-zip-compressed',
		'xht'	=> 'application/xhtml+xml',
		'psd'	=> 'application/photoshop',
		'odt'	=> 'application/vnd.oasis.opendocument.text',
		'otf'	=> 'application/vnd.oasis.opendocument.formula-template',
		'ott'	=> 'application/vnd.oasis.opendocument.text-template',
		'oth'	=> 'application/vnd.oasis.opendocument.text-web',
		'odm'	=> 'application/vnd.oasis.opendocument.text-master',
		'odg'	=> 'application/vnd.oasis.opendocument.graphics',
		'otg'	=> 'application/vnd.oasis.opendocument.graphics-template',
		'odp'	=> 'application/vnd.oasis.opendocument.presentation',
		'otp'	=> 'application/vnd.oasis.opendocument.presentation-template',
		'ods'	=> 'application/vnd.oasis.opendocument.spreadsheet',
		'ots'	=> 'application/vnd.oasis.opendocument.spreadsheet-template',
		'odc'	=> 'application/vnd.oasis.opendocument.chart',
		'odf'	=> 'application/vnd.oasis.opendocument.formula',
		'odb'	=> 'application/vnd.oasis.opendocument.database',
		'odi'	=> 'application/vnd.oasis.opendocument.image',
		'oxt'	=> 'application/vnd.openofficeorg.extension',
		'docm'	=> 'application/vnd.ms-word.document.macroEnabled.12',
		'docx'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'dotm'	=> 'application/vnd.ms-word.template.macroEnabled.12',
		'dotx'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'ppsm'	=> 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
		'ppsx'	=> 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
		'pptm'	=> 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
		'pptx'	=> 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'xlsb'	=> 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
		'xlsm'	=> 'application/vnd.ms-excel.sheet.macroEnabled.12',
		'xlsx'	=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xps'	=> 'application/vnd.ms-xpsdocument',
	);

/**
 * here are file types defined were the mime time doesn't fit to the media type
 *
 * @access private
 */
$GLOBALS['T3_VAR']['ext']['dam']['file2mediaCode'] = array(
		'ogg'=> 'audio',

		'doc'=> 'text',
		'docx'=> 'text',
		'docm'=> 'text',
		'dot'=> 'text',
		'pdf'=> 'text',
		'ps'=> 'text',
		'wp5'=> 'text',
		'rtf'=> 'text',
		'dvi'=> 'text',
		'odt'=> 'text',

		'ai'=> 'image',
		'eps'=> 'image',
		'psd'=> 'image',
		'svg'=> 'image',

		'csv'=> 'dataset',
		'xls'=> 'dataset',
		'xlb'=> 'dataset',
		'mdb'=> 'dataset',
		'zip'=> 'dataset',
		'wk'=> 'dataset',

		'ttf'=> 'font',
		'otf'=> 'font',
		'pfa'=> 'font',
		'pfb'=> 'font',
		'gsf'=> 'font',
		'pcf'=> 'font',
		'pcf.Z'=> 'font',

		'max'=> 'model',
		'3ds'=> 'model',

		'gtar'=> 'collection',
		'tgz'=> 'collection',
		'tar'=> 'collection',
		'lha'=> 'collection',
		'lzh'=> 'collection',
		'lzx'=> 'collection',
		'hqx'=> 'collection',
		'rpm'=> 'collection',
		'shar'=> 'collection',
		'sit'=> 'collection',
		'deb'=> 'collection',

		'com'=> 'software',
		'exe'=> 'software',
		'bat'=> 'software',
		'dll'=> 'software',
		'pl'=> 'software',
		'pm'=> 'software',

		'swf'=> 'interactive',
		'swfl'=> 'interactive',
		'ppt'=> 'interactive',
		'pps'=> 'interactive',
		'pot'=> 'interactive',
	);


	// convert media name to code number
foreach($GLOBALS['T3_VAR']['ext']['dam']['file2mediaCode'] as $key => $val){
	$GLOBALS['T3_VAR']['ext']['dam']['file2mediaCode'][$key] = $GLOBALS['T3_VAR']['ext']['dam']['media2code'][$val];
}

/*
	function _prettyPrintarray($arr) {
		$out='';
		foreach($arr as $key => $val) {
			$out.="\t\t'".$key."'\t=> '".$val."',\n";
		}
		return $out;
	}

ksort($types->file2mime);
asort($types->file2mime);
$out = $types->_prettyPrintarray($types->file2mime);
echo nl2br($out);
*/

?>
