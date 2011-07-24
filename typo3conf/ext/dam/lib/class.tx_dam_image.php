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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   69: class tx_dam_image
 *
 *              SECTION: image scaling and thumbs
 *   87:     function isPreviewPossible ($fileInfo)
 *  109:     function previewImgTag($fileInfo, $size='', $imgAttributes='')
 *  125:     function previewImgUrl($fileInfo, $size='', $imgAttributes='')
 *  142:     function preview($fileInfo, $size='', $imgAttributes='')
 *
 *              SECTION: Image size calculations
 *  272:     function parseSize ($size)
 *  286:     function convertSize ($size)
 *  301:     function getDefinedSizes ($mode=TYPO3_MODE)
 *  315:     function calcSize($sourceX, $sourceY, $destMaxX, $destMaxY)
 *
 *              SECTION: Tools - used internally but might be useful for general usage
 *  345:     function tools_explodeAttributes($attributeString)
 *  363:     function tools_implodeAttributes($attributes, $hsc=true)
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




/**
 * Generates an image preview and scales images
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_image {




	/***************************************
	 *
	 *   image scaling and thumbs
	 *
	 ***************************************/


	/**
	 * Checks if a image preview can be generated for a file
	 *
	 * @param	mixed		$fileInfo	Fileinfo array or file type
	 * @return	boolean
	 */
	function isPreviewPossible ($fileInfo) {
		$thumbnailPossible = false;

		$file_type = is_array($fileInfo) ? $fileInfo['file_type'] : $fileInfo;

		// font rendering is buggy so it's deactivated here   # if ($file_type === 'ttf' ||
		if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], $file_type)) {
		#if( ($fileInfo['media_type']==TXDAM_mtype_image) OR t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], $file_type)) {
			$thumbnailPossible = true;
		}
		return $thumbnailPossible;
	}


	/**
	 * Returns a image-tag for a image preview (with url to thumbs.php or just a reference to the scaled image)
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$size Optional: $size is [w]x[h] of the image preview. 56 is default.
	 * @param	mixed		$imgAttributes additional attributes for the image tag
	 * @return	string		Thumbnail image tag.
	 */
	function previewImgTag($fileInfo, $size='', $imgAttributes='')	{

		$thumb = tx_dam_image::preview($fileInfo, $size, $imgAttributes);

		return $thumb['img'];
	}


	/**
	 * Returns an image URL for a image preview (with url to thumbs.php or just a reference to the scaled image)
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$size Optional: $size is [w]x[h] of the image preview. 56 is default.
	 * @param	mixed		$imgAttributes additional attributes for the image tag
	 * @return	array		Thumbnail image url and attributes with width/height: array('url' => $url, 'attributes' => $imgAttributes);
	 */
	function previewImgUrl($fileInfo, $size='', $imgAttributes='')	{

		$thumb = tx_dam_image::preview($fileInfo, $size, $imgAttributes);

		return $thumb['url'];
	}



	/**
	 * Returns a image-tag, URL and attributes for a image preview
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$size Optional: $size is [w]x[h] of the image preview. 56 is default.
	 * @param	mixed		$imgAttributes additional attributes for the image tag
	 * @return	array		Thumbnail image tag, url and attributes with width/height
	 * @todo calc width/height from cm size or similar when hpixels not available
	 * @todo return false when file is missing?
	 */
	function preview($fileInfo, $size='', $imgAttributes='')	{

			// get some file information
		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo($fileInfo);
		}
		$filepath = tx_dam::file_absolutePath($fileInfo);

		if ($filepath AND @file_exists($filepath) AND @is_file($filepath))	{

			$fileType = tx_dam::file_getType($fileInfo);

			if (!tx_dam_image::isPreviewPossible($fileType)) {
				return array();
			}

			if (!is_array($imgAttributes)) {
				$imgAttributes = tx_dam_image::tools_explodeAttributes($imgAttributes);
			}

			$imgAttributes['alt'] = isset($imgAttributes['alt']) ? $imgAttributes['alt'] : ($fileInfo['alt_text'] ? $fileInfo['alt_text'] : $fileInfo['title']);
			$imgAttributes['title'] = isset($imgAttributes['title']) ? $imgAttributes['title'] : $imgAttributes['alt'];


				// Check and parse the size parameter
			$size = trim($size);
			list($sizeX, $sizeY) = tx_dam_image::parseSize($size);
			$sizeMax = $max = max($sizeX, $sizeY);


				// get maximum image pixel size
			$maxImageSize = 0;
			if ($fileInfo['hpixels']) {
				$maxImageSize = max($fileInfo['hpixels'], $fileInfo['vpixels']);

			} elseif (t3lib_div::inList('gif,jpg,png', $fileType['file_type'])) {
				if (is_array($imgInfo = @getimagesize($filepath)))	{
					$fileInfo['hpixels'] = $imgInfo[0];
					$fileInfo['vpixels'] = $imgInfo[1];
					$maxImageSize = max($fileInfo['hpixels'], $fileInfo['vpixels']);
				}
			}


				// calculate the image preview size
			$useOriginalImage = false;
			if ($maxImageSize AND $maxImageSize<=$sizeMax)	{
				$useOriginalImage = true;
				$thumbSizeX = $fileInfo['hpixels'];
				$thumbSizeY = $fileInfo['vpixels'];
				$imgAttributes['width'] = $thumbSizeX;
				$imgAttributes['height'] = $thumbSizeY;

			} elseif ($maxImageSize) {
				list($thumbSizeX, $thumbSizeY) = tx_dam_image::calcSize($fileInfo['hpixels'], $fileInfo['vpixels'], $sizeX, $sizeY);
				$imgAttributes['width'] = $thumbSizeX;
				$imgAttributes['height'] = $thumbSizeY;
				$size = $thumbSizeX.'x'.$thumbSizeY;

			} elseif (tx_dam_image::isPreviewPossible($fileType)) {
				$thumbSizeX = $sizeX;
				$thumbSizeY = $sizeY;
				$size = $thumbSizeX.'x'.$thumbSizeY;

			} else {
				$thumbSizeX = 0;
				$thumbSizeY = 0;
			}

			$url = '';
			$thumbnail = '';

			if ($thumbSizeX) {

					// use the original image if it's size fits to the image preview size
				if ($useOriginalImage)	{
					if (TYPO3_MODE === 'FE') {
						$url = preg_replace ('#^'.preg_quote(PATH_site).'#', '', $filepath);
					} else {
						$url = $GLOBALS['BACK_PATH'].'../'.preg_replace ('#^'.preg_quote(PATH_site).'#', '', $filepath);
					}

					// use thumbs.php script
				} else {

					$check = basename($filepath).':'.filemtime($filepath).':'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
					$url = 'thumbs.php?';
					$url .= '&file='.rawurlencode($filepath);
					$url .= $size?'&size='.$size:'';
					$url.= '&md5sum='.t3lib_div::shortMD5($check);
					$url .= '&dummy='.$GLOBALS['EXEC_TIME'];

			 		if (TYPO3_MODE === 'FE') {
						$url = TYPO3_mainDir.$url;
					} else {
						$url = $GLOBALS['BACK_PATH'].$url;
					}
				}
			}

			$imgAttributes = tx_dam_image::tools_implodeAttributes($imgAttributes);
			$thumbnail = '<img src="'.htmlspecialchars($url).'"'.$imgAttributes.' />';

		}

		return array(
			'img' => $thumbnail,
			'url' => $url,
			'attributes' => $imgAttributes,
			'sizeX' => $thumbSizeX,
			'sizeY' => $thumbSizeY,
			);
	}




	/***************************************
	 *
	 *   Image size calculations
	 *
	 ***************************************/



	/**
	 * Check and parse the size parameter like '64x64' or 'small'
	 *
	 * @param	string $size Image size
	 * @return array array($sizeX, $sizeY);
	 */
	function parseSize ($size) {
		$size = trim($size);
		list($sizeX, $sizeY) = explode('x', $size.'x'.$size);
		if(!intval($sizeX)) list($sizeX, $sizeY) = tx_dam_image::convertSize($size);
		return array($sizeX, $sizeY);
	}


	/**
	 * Convert a text image preview size "default, icon, xx-small, x-small, small, medium, large, x-large, xx-large" into pixel sizes.
	 *
	 * @param	string $size Image size
	 * @return array array($sizeX, $sizeY);
	 */
	function convertSize ($size) {
		$thumbSizes = tx_dam_image::getDefinedSizes();
		$size = $thumbSizes[$size];
		if (!$size) $size = $thumbSizes['default'];
		list($sizeX, $sizeY) = explode('x', $size.'x'.$size);
		return array($sizeX, $sizeY);
	}


	/**
	 * Returns an array with image preview sizes
	 *
	 * @param	string $mode Wanted TYPO3_MODE
	 * @return array
	 */
	function getDefinedSizes ($mode=TYPO3_MODE) {
		return $GLOBALS['T3_VAR']['ext']['dam']['thumbsizes'][$mode];
	}


	/**
	 * Calculate a image preview size
	 *
	 * @param	integer $sourceX Source image size
	 * @param	integer $sourceY Source image size
	 * @param	integer $destMaxX Destination maximum image size
	 * @param	integer $destMaxY Destination maximum image size
	 * @return array Destination image size: $thumbSizeX, $thumbSizeY
	 */
	function calcSize($sourceX, $sourceY, $destMaxX, $destMaxY) {
		if ($sourceX>$sourceY) {
			$thumbSizeX = $destMaxX;
			$thumbSizeY = intval(round($sourceY*$destMaxX/$sourceX));
		} else {
			$thumbSizeX = intval(round($sourceX*$destMaxY/$sourceY));
			$thumbSizeY = $destMaxY;
		}
		return array($thumbSizeX, $thumbSizeY);
	}






	/***************************************
	 *
	 *   Tools - used internally but might be useful for general usage
	 *
	 ***************************************/



	/**
	 * Explode a string into a HTML tags attributes and it's values
	 *
	 * @param 	string 	$attributeString HTML tag or it's attributes
	 * @return array Attribute/value pairs
	 */
	function tools_explodeAttributes($attributeString) {
		$attributes = array();
		$attributeMatches = array();
		preg_match_all('# ([\w]+)="([^"]*)"#', $attributeString, $attributeMatches);

		if(count($attributeMatches[1])) {
			foreach($attributeMatches[2] as $name => $value) {
				$attributeMatches[2][$name] = htmlspecialchars_decode($value);
			}
			$attributes = array_combine($attributeMatches[1], $attributeMatches[2]);
		}
		return $attributes;
	}


	/**
	 * Implode aa array into a string to be used in HTML tags as attributes and it's values
	 *
	 * @param 	array 	$attributes Attribute name/value pairs
	 * @param 	boolean $hsc If set (default) all values will be htmlspecialchars()
	 * @return string 	attributes
	 */
	function tools_implodeAttributes($attributes, $hsc=true) {
		$attributeString = '';
		foreach($attributes as $name => $value) {
			$attributeString .= ' '.$name.'="'.($hsc ? htmlspecialchars($value) : $value).'"';
		}
		return $attributeString;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_image.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_image.php']);
}
?>