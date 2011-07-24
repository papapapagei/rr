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
 * Contains standard mp3 previewer.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Previewer
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   59: class tx_dam_previewerMP3 extends tx_dam_previewerProcBase
 *   70:     function isValid($row, $size, $type, $conf=array())
 *   94:     function render($row, $size, $type, $conf=array())
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam_previewerprocbase.php');



/**
 * Contains standard mp3 previewer.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Previewer
 */
class tx_dam_previewerMP3 extends tx_dam_previewerProcBase {

	/**
	 * Returns true if this previewer is able to render a preview for the given file
	 *
	 * @param	array		$row Meta data array
	 * @param	integer		$size The maximum size of the previewer
	 * @param	string		$type The wanted previewer type
	 * @param	array		$conf Additional configuration values. Might be empty.
	 * @return	boolean		True if this is the right previewer for the file
	 */
	function isValid($row, $size, $type, $conf=array()) {
		$valid = false;

		if ($row['file_type'] === 'mp3'
			AND $size <= '200') {
			 $valid = true;
		}

		return $valid;
	}


	/**
	 * Returns rendered previewer
	 * used player:
	 * http://aktuell.de.selfhtml.org/artikel/grafik/flashmusik/
	 * http://loudblog.de/index.php?s=download
	 *
	 * @param	array		$row Meta data array
	 * @param	integer		$size The maximum size of the previewer
	 * @param	string		$type The wanted previewer type
	 * @param	array		$conf Additional configuration values. Might be empty.
	 * @return	array		True if this is the right previewer for the file
	 */
	function render($row, $size, $type, $conf=array()) {

		$outArr = array(
			'htmlCode' => '',
			'headerCode' => ''
			);

		$absFile = tx_dam::file_absolutePath($row['file_path'].$row['file_name']);
		$siteUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		$fileRelPath = tx_dam::file_relativeSitePath($row['file_path'].$row['file_name']);
		$playerRelPath = str_replace(PATH_site, '', PATH_txdam);
		#$size = 'width="200" height="55"';
		#$playerRelPath .= 'res/emff_lila.swf';
		$size = 'width="120" height="37"';
		$playerRelPath .= 'res/emff_inx.swf';

		$outArr['htmlCode'] = '<div class="previewMP3">
			<object type="application/x-shockwave-flash" data="'.htmlspecialchars($siteUrl.$playerRelPath).'?streaming=yes&src='.htmlspecialchars($siteUrl.t3lib_div::rawUrlEncodeFP($fileRelPath)).'" '.$size.'>
			<param name="movie" value="'.htmlspecialchars($siteUrl.$playerRelPath).'?streaming=yes&src='.htmlspecialchars($siteUrl.t3lib_div::rawUrlEncodeFP($fileRelPath)).'" />
			<param name="quality" value="high" />
			</object>
			</div>';


		return $outArr;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_previewerMP3.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_previewerMP3.php']);
}
?>