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
 * Module extension (addition to function menu) 'Upload' for the 'Media>File' module.
 * Part of the DAM (digital asset management) extension.
 *
 * The script will be called directly to return data that indicates the upload status
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage file
 */

/**
 * The script will be called directly to return data that indicates the upload status
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage file
 */
class tx_dam_upload_status {

	/**
	 * Formats the input integer $sizeInBytes as bytes/kilobytes/megabytes (-/K/M)
	 *
	 * @param	integer		Number of bytes to format.
	 * @param	string		Labels for bytes, kilo, mega and giga separated by vertical bar (|) and possibly encapsulated in "". Eg: " | K| M| G" (which is the default value)
	 * @return	string		Formatted representation of the byte number, for output.
	 */
	function formatSize($sizeInBytes,$labels='')	{

			// Set labels:
		if (strlen($labels) == 0) {
		    $labels = ' | K| M| G';
		} else {
		    $labels = str_replace('"','',$labels);
		}
		$labelArr = explode('|',$labels);

			// Find size:
		if ($sizeInBytes>900)	{
			if ($sizeInBytes>900000000)	{	// GB
				$val = $sizeInBytes/(1024*1024*1024);
				return number_format($val, (($val<20)?1:0), '.', '').$labelArr[3];
			}
			elseif ($sizeInBytes>900000)	{	// MB
				$val = $sizeInBytes/(1024*1024);
				return number_format($val, (($val<20)?1:0), '.', '').$labelArr[2];
			} else {	// KB
				$val = $sizeInBytes/(1024);
				return number_format($val, (($val<20)?1:0), '.', '').$labelArr[1];
			}
		} else {	// Bytes
			return $sizeInBytes.$labelArr[0];
		}
	}


	function getUploadFilesStat($tmpfolder) {
		$found = array();
		if(is_dir($tmpfolder)) {
			foreach (glob($tmpfolder.'/[p][h][p]*') as $filename) {
				if (filemtime($filename) >= (time()-5)) {
					$found[$filename] = array('size' => filesize($filename));
				}
			}
		}
		return $found;
	}

	/**
	 * @todo Does this work on windows?
	 * @todo PHP 5.1 have better upload handling
	 */
	function process() {

		$upload_tmp_dir = ini_get('upload_tmp_dir');
		$upload_tmp_dir = $upload_tmp_dir ? $upload_tmp_dir : '/tmp';


		$files = $this->getUploadFilesStat($upload_tmp_dir);

		$fileCount = 0;
		if (count($files)) {
			$fileCount = count($files);
			$bytesUploaded = 0;
			foreach ($files as $key => $fileStat) {
				$bytesUploaded +=$fileStat['size'];
			}
			$status = $this->formatSize($bytesUploaded);
		} else $status = 0;

		$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"'.chr(63).'>
		<response>
			<method>uploadprogress</method>
			<result>'.$status.'</result>';

		#$output .= '
		#	<fileCount>'.$fileCount.'</fileCount>
		#	<bla>'.print_r($files, 1).'</bla>';

		$output .= '
		</response>';

		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header('Content-Length: '.strlen($output));
		header('Content-Type: text/xml');

		echo $output;
	}
}


$progress = new tx_dam_upload_status;
$progress->process();


//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_file_upload/upload_status.php'])    {
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_file_upload/upload_status.php']);
//}

?>