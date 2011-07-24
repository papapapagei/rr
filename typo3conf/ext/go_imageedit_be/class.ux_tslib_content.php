<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Gosign media. Gmbh <caspar@gosign.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * Plugin 'ux_tslib_cObj' for the 'go_imageedit_be' extension.
 *
 * @author	Gosign media. Gmbh <caspar@gosign.de>
 * @package	TYPO3
 * @subpackage	tx_goimageeditbe
 */
class ux_tslib_cObj extends tslib_cObj {
	
	/**
	 * Returns a <img> tag with the image file defined by $file and processed according to the properties in the TypoScript array.
	 * Mostly this function is a sub-function to the IMAGE function which renders the IMAGE cObject in TypoScript. This function is called by "$this->cImage($conf['file'],$conf);" from IMAGE().
	 *
	 * @param	string		File TypoScript resource
	 * @param	array		TypoScript configuration properties
	 * @return	string		<img> tag, (possibly wrapped in links and other HTML) if any image found.
	 * @access private
	 * @see IMAGE()
	 */
	function cImage($file,$conf) {
		$info = $this->getImgResource($file,$conf['file.']);
		// added by Caspar Stuebs @ Gosign media.
		if(is_array($info) && (!empty($this->data['tx_goimageeditbe_croped_image']) || false)) {
			$editConfig = unserialize($this->data['tx_goimageeditbe_croped_image']);
			// render image with info from db
			$newFile = $this->renderGoImageEdit($info['origFile'], ($editConfig ? $editConfig : array()), &$conf);
			// create new image info
			$info = $this->getImgResource($newFile,$conf['file.']);
		}
		// end of add
		$GLOBALS['TSFE']->lastImageInfo=$info;
		if (is_array($info))	{
			$info[3] = t3lib_div::png_to_gif_by_imagemagick($info[3]);
			$GLOBALS['TSFE']->imagesOnPage[]=$info[3];		// This array is used to collect the image-refs on the page...

			if (!strlen($conf['altText']) && !is_array($conf['altText.']))	{	// Backwards compatible:
				$conf['altText'] = $conf['alttext'];
				$conf['altText.'] = $conf['alttext.'];
			}
			$altParam = $this->getAltParam($conf);
			$theValue = '<img src="'.htmlspecialchars($GLOBALS['TSFE']->absRefPrefix.t3lib_div::rawUrlEncodeFP($info[3])).'" width="'.$info[0].'" height="'.$info[1].'"'.$this->getBorderAttr(' border="'.intval($conf['border']).'"').(($conf['params'] || is_array($conf['params.']))?' '.$this->stdwrap($conf['params'],$conf['params.']):'').($altParam).' />';
			if ($conf['linkWrap'])	{
				$theValue = $this->linkWrap($theValue,$conf['linkWrap']);
			} elseif ($conf['imageLinkWrap']) {
				$theValue = $this->imageLinkWrap($theValue,$info['origFile'],$conf['imageLinkWrap.']);
			}
			return $this->wrap($theValue,$conf['wrap']);
		}
	}
	
	function renderGoImageEdit($origFile, $editConf, $imgConf) {
		$extInfo = pathinfo($origFile);
		$extInfo = $extInfo['extension'];
		
		$myExtType = ($this->data['CType'] != 'list' ? $this->data['CType'] : $this->data['list_type']);
		
		// get global config
		$globalConf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['go_imagedit_be.']['default.']['render.'];
		if (is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['go_imagedit_be.'][$myExtType.'.'])) {
			$globalConf = $this->array_merge_deep($globalConf, $GLOBALS['TSFE']->tmpl->setup['plugin.']['go_imagedit_be.'][$myExtType.'.']['render.']);
		}
		
		// override imgConf, if values are empty and values are defined in global config
		if(isset($globalConf['maxWidth']) && !empty($globalConf['maxWidth']) && (empty($imgConf['file.']['maxW']) || $imgConf['file.']['maxW'] > $globalConf['maxWidth'])) $imgConf['file.']['maxW'] = $globalConf['maxWidth'];
		if(isset($globalConf['maxHeight']) && !empty($globalConf['maxHeight']) && (empty($imgConf['file.']['maxH']) || $imgConf['file.']['maxH'] > $globalConf['maxHeight'])) $imgConf['file.']['maxH'] = $globalConf['maxHeight'];
		if(isset($globalConf['htmlProperties.']['alt']) && !empty($globalConf['htmlProperties.']['alt']) && empty($imgConf['altText']) && empty($imgConf['altText.'])) {
			$imgConf['altText'] = $globalConf['htmlProperties.']['alt'];
			unset($imgConf['altText.']);
		}
		if(isset($globalConf['htmlProperties.']['title']) && !empty($globalConf['htmlProperties.']['title']) && empty($imgConf['titleText']) && empty($imgConf['titleText.'])) {
			$imgConf['titleText'] = $globalConf['htmlProperties.']['title'];
			unset($imgConf['titleText.']);
		}
		
		// get config for file
		if(isset($editConf['files'][$origFile])) {
			$imgEdit = $editConf['files'][$origFile];
			$origImgInfo = getimagesize($origFile);
			
			if(!(round($imgEdit['offsetX']) == 0 && round($imgEdit['offsetY']) == 0 && round($imgEdit['selectorWidth']) == $origImgInfo[0] && round($imgEdit['selectorHeight']) == $origImgInfo[1])) {
				// create crop-params fom config
				$params = ' -crop '.$imgEdit['selectorWidth'].'x'.$imgEdit['selectorHeight'].'+'.$imgEdit['offsetX'].'+'.$imgEdit['offsetY'];
				if($globalConf['fixedAspectRatio'] && $globalConf['maxWidth'] && $globalConf['maxHeight']) {
					// if fixed aspect ratio, we will do resize ourselves, because we need to resize to absolute values x and y
					$params .= ' -resize '.$globalConf['maxWidth'].'x'.$globalConf['maxHeight'].'!';
				}
				// build new image
				$imageBuild = t3lib_div::makeInstance('tslib_gifBuilder');
				$imageBuild->init();
				if($extInfo == 'gif') {
					$imgNew = $imageBuild->imageMagickConvert($origFile, 'jpg', '', '', $params);
					$imgNew = $imageBuild->imageMagickConvert($imgNew[3], 'gif');
				}
				else $imgNew = $imageBuild->imageMagickConvert($origFile, $extInfo, '', '', $params);
				
				// unset file import
				if(isset($imgConf['file.']['import.'])) unset($imgConf['file.']['import.']);
			}
		}
		
		return is_array($imgNew) ? $imgNew[3] : $origFile;
	}
	
	function array_merge_deep($array1, $array2) {
		$array3 = $array1;
		foreach($array2 as $key => $value) {
			if (is_array($value)) {
				$array3[$key] = $this->array_merge_deep($array1[$key], $value);
			} else {
				$array3[$key] = $value;
			}
		}
		return $array3;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_imageedit_be/class.ux_tslib_content.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_imageedit_be/class.ux_tslib_content.php']);
}

?>