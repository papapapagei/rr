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
 * Contains standard image previewer.
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
 *   59: class tx_dam_previewerImage extends tx_dam_previewerProcBase
 *   70:     function isValid($row, $size, $type, $conf=array())
 *   97:     function render($row, $size, $type, $conf=array())
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam_previewerprocbase.php');



/**
 * Contains standard image previewer.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Previewer
 */
class tx_dam_previewerImage extends tx_dam_previewerProcBase {

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

		if ($size === '200' AND $type === 'topright') {
			if (($row['media_type'] == TXDAM_mtype_image AND $row['hpixels'] AND $row['vpixels'])
				OR $row['media_type'] == TXDAM_mtype_font
				OR $row['file_type'] === 'pdf'
				OR $row['file_type'] === 'ps'
				OR $row['file_type'] === 'eps'
				 ) {
				$valid = true;
			}
		}

		return $valid;
	}


	/**
	 * Returns rendered previewer
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


		if ($row['hpixels']>160 OR $row['vpixels']>160) {

			$width = $row['hpixels'] > 160 ? 160 : $row['hpixels'];
			$height = intval($row['vpixels'] * $width / $row['hpixels']);
			if($height>160) {
				$height = 160;
				$width = intval($row['hpixels'] * $height / $row['vpixels']);
			}

			$widthZoom = $width * 2;
			$widthZoom = $row['hpixels'] > $widthZoom ? $widthZoom : $row['hpixels'];
			$margin = 4;
			$diaPadding = 9;


			$outArr['headerCode'] = '
				<style type="text/css">

				/* Photo-Zoom */

				/* MSIE z-index work-a-round */
				/* reversing natural z-index */
				.txdamPZWrapper { position:relative; z-index:900; }

				/* Mozilla z-index bliss */
				.txdamPZWrapper a { z-index:0; }
				.txdamPZWrapper a:hover { position:absolute; z-index:900; }
				.txdamPZWrapper .txdamPZ { position:relative;  }
				.txdamPZWrapper .txdamPZ a:hover { border:0; background:none; text-decoration:none; }
				.txdamPZ a { position:absolute; cursor:default; }
				.txdamPZ img { align: left; width:'.$width.'px; height:'.$height.'px; }

				/* ZoomOpen Positions */
				.txdamPZ a:hover,.txdamPZ a:hover img { width:'.$widthZoom.'px; height:auto;}
				.txdamPZWrapper .txdamPZ a:hover { left:-'.($widthZoom-$width).'px; } /*MSIE-specific*/
				.txdamPZWrapper>.txdamPZ a:hover { left:-'.($widthZoom-$width).'px; } /*Mozilla-specific*/
				/* End Photo-Zoom */

				.txdamPZ img { padding:8px; background-color:#fff; border:solid #888 1px; }

				</style>
				';

			$outArr['htmlCode'] = '
					<!-- start Photo-Zoom code -->
					 <div class="txdamPZDummy" style="height:'.($height+$diaPadding+$diaPadding+$margin+$margin).'px; width:'.($width+$diaPadding+$diaPadding+$margin+$margin).'px; "></div>
					 <div class="txdamPZWrapper" style="text-align:left;top:-'.($height+$diaPadding).'px; margin-left:'.$margin.'px; margin-top:-1em; ">
					   <p class="txdamPZ" >
						 <a href="javascript:void(0)">'.
									t3lib_BEfunc::getThumbNail('thumbs.php', $absFile,'',$widthZoom).
									'</a>
					   </p>
					 </div>
					<br />
					<!-- end Photo-Zoom code -->
					';


		} else {
			$outArr['htmlCode'] = '<div class="previewThumb">'.
				t3lib_BEfunc::getThumbNail('thumbs.php', $absFile,' align="middle" style="border:solid 1px #ccc;"',160).
				'</div>';
		}

		return $outArr;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_previewerImage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_previewerImage.php']);
}
?>