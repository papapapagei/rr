<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2008 Kasper Skaarhoj (kasper@typo3.com)
*  (c) 2004-2008 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * This script extends class tx_rtehtmlarea_select_image for operation with DAM and TYPO3 4.2
 * This script becomes deprecated and is not used when operating with TYPO3 4.3 (see file ext_localconf.php)
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Stanislas Rolland <typo3(arobas)sjbr.ca>
 *
 * $Id: class.ux_tx_rtehtmlarea_select_image.php 3439 2008-03-16 19:16:51Z flyguide $  *
 */

require_once(t3lib_extMgm::extPath('rtehtmlarea').'mod4/class.tx_rtehtmlarea_select_image.php');

class ux_tx_rtehtmlarea_select_image extends tx_rtehtmlarea_select_image {
	
	public $sys_language_content;
	public $thisConfig;
	protected $RTEImageStorageDir;
	protected $imgObj;  // Instance object of t3lib_stdGraphic
	
	public function initVariables() {
	
				// Main GPvars:
		$this->act = t3lib_div::_GP('act');
		$this->editorNo = t3lib_div::_GP('editorNo');
		$this->sys_language_content = t3lib_div::_GP('sys_language_content');
		$this->expandPage = t3lib_div::_GP('expandPage');
		$this->expandFolder = t3lib_div::_GP('expandFolder');

			// Find "mode"
		$this->mode = t3lib_div::_GP('mode');
		if (!$this->mode)	{
			$this->mode='rte';
		}

			// Site URL
		$this->siteURL = t3lib_div::getIndpEnv('TYPO3_SITE_URL');	// Current site url

			// the script to link to
		$this->thisScript = t3lib_div::getIndpEnv('SCRIPT_NAME');

		if (!$this->act)	{
			$this->act='magic';
		}
	}
	
	/**
	 * Provide the additional parameters to be included in the template body tag
	 *
	 * @return	string		the body tag additions
	 */
	public function getBodyTagAdditions() {
		return 'onLoad="initDialog();"';
	}
	
	/**
	 * Get the path to the folder where RTE images are stored
	 *
	 * @return	string		the path to the folder where RTE images are stored
	 */
	protected function getRTEImageStorageDir()	{
		return ($this->imgPath ? $this->imgPath : $GLOBALS['TYPO3_CONF_VARS']['BE']['RTE_imageStorageDir']);
	}

	/**
	 * Insert the image in the editing area
	 *
	 * @return	void
	 */
	protected function insertImage()	{
		if (t3lib_div::_GP('insertImage'))	{
			$filepath = t3lib_div::_GP('insertImage');
			$imgInfo = $this->getImageInfo($filepath);
			switch ($this->act) {
				case 'magic':
					$this->insertMagicImage($filepath, $imgInfo);
					exit;
					break;
				case 'plain':
					$this->insertPlainImage($imgInfo);
					exit;
					break;
			}
		}
	}
	
	/**
	 * Get the information on the image file identified its path
	 *
	 * @param	string		$filepath: the path to the image file
	 *
	 * @return	array		a 4-elements information array about the file
	 */
	public function getImageInfo($filepath) {
		$this->imgObj = t3lib_div::makeInstance('t3lib_stdGraphic');
		$this->imgObj->init();
		$this->imgObj->mayScaleUp = 0;
		$this->imgObj->tempPath = PATH_site.$this->imgObj->tempPath;
		return $this->imgObj->getImageDimensions($filepath);
	}
	
	/**
	 * Insert a magic image
	 *
	 * @param	string		$filepath: the path to the image file
	 * @param	array		$imgInfo: a 4-elements information array about the file
	 * @param	string		$altText: text for the alt attribute of the image
	 * @param	string		$titleText: text for the title attribute of the image
	 * @param	string		$additionalParams: text representing more HTML attributes to be added on the img tag
	 * @return	void
	 */
	public function insertMagicImage($filepath, $imgInfo, $altText='', $titleText='', $additionalParams='') {
		if (is_array($imgInfo) && count($imgInfo)==4 && $this->RTEImageStorageDir)	{
			$fI = pathinfo($imgInfo[3]);
			$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$basename = $fileFunc->cleanFileName('RTEmagicP_'.$fI['basename']);
			$destPath =PATH_site.$this->RTEImageStorageDir;
			if (@is_dir($destPath))	{
				$destName = $fileFunc->getUniqueName($basename,$destPath);
				@copy($imgInfo[3],$destName);
				t3lib_div::fixPermissions($destName);
				$cWidth = t3lib_div::intInRange(t3lib_div::_GP('cWidth'), 0, $this->magicMaxWidth);
				$cHeight = t3lib_div::intInRange(t3lib_div::_GP('cHeight'), 0, $this->magicMaxHeight);
				if (!$cWidth)	$cWidth = $this->magicMaxWidth;
				if (!$cHeight)	$cHeight = $this->magicMaxHeight;

				$imgI = $this->imgObj->imageMagickConvert($filepath,'WEB',$cWidth.'m',$cHeight.'m');	// ($imagefile,$newExt,$w,$h,$params,$frame,$options,$mustCreate=0)
				if ($imgI[3])	{
					$fI=pathinfo($imgI[3]);
					$mainBase='RTEmagicC_'.substr(basename($destName),10).'.'.$fI['extension'];
					$destName = $fileFunc->getUniqueName($mainBase,$destPath);
					@copy($imgI[3],$destName);
					t3lib_div::fixPermissions($destName);
					$destName = dirname($destName).'/'.rawurlencode(basename($destName));
					$iurl = $this->siteURL.substr($destName,strlen(PATH_site));
					$this->imageInsertJS($iurl, $imgI[0], $imgI[1], $altText, $titleText, $additionalParams);
				}
			}
		}
	}
	
	/**
	 * Insert a plain image
	 *
	 * @param	array		$imgInfo: a 4-elements information array about the file
	 * @param	string		$altText: text for the alt attribute of the image
	 * @param	string		$titleText: text for the title attribute of the image
	 * @param	string		$additionalParams: text representing more HTML attributes to be added on the img tag
	 * @return	void
	 */
	public function insertPlainImage($imgInfo, $altText='', $titleText='', $additionalParams='') {
		if (is_array($imgInfo) && count($imgInfo)==4)	{
			$iurl = $this->siteURL.substr($imgInfo[3],strlen(PATH_site));
			$this->imageInsertJS($iurl, $imgInfo[0], $imgInfo[1], $altText, $titleText, $additionalParams);
		}
	}
	
	/**
	 * Echo the HTML page and JS that will insert the image
	 *
	 * @param	string		$url: the url of the image
	 * @param	integer		$width: the width of the image
	* @param	integer		$height: the height of the image
	 * @param	string		$altText: text for the alt attribute of the image
	 * @param	string		$titleText: text for the title attribute of the image
	 * @param	string		$additionalParams: text representing more html attributes to be added on the img tag
	 * @return	void
	 */
	protected function imageInsertJS($url, $width, $height, $altText='', $titleText='', $additionalParams='') {
		echo'
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Untitled</title>
</head>
<script type="text/javascript">
/*<![CDATA[*/
	var dialog = window.opener.HTMLArea.Dialog.TYPO3Image;
	var plugin = dialog.plugin;
	function insertImage(file,width,height,alt,title,additionalParams)	{
		plugin.insertImage(\'<img src="\'+file+\'" width="\'+parseInt(width)+\'" height="\'+parseInt(height)+\'"\''  . ($this->defaultClass?('+\' class="'.$this->defaultClass.'"\''):'') .
			'+(alt?\' alt="\'+alt+\'"\':\'\')+(title?\' title="\'+title+\'"\':\'\')+(additionalParams?\' \'+additionalParams:\'\')+\' />\');
	}
/*]]>*/
</script>
<body>
<script type="text/javascript">
/*<![CDATA[*/
	insertImage('.t3lib_div::quoteJSvalue($url,1).','.$width.','.$height.','.t3lib_div::quoteJSvalue($altText,1).','.t3lib_div::quoteJSvalue($titleText,1).','.t3lib_div::quoteJSvalue($additionalParams, 1).');
/*]]>*/
</script>
</body>
</html>';
	}

	/**
	 * Generate JS code to be used on the image insert/modify dialogue
	 *
	 * @param	string		$act: the action to be performed
	 * @param	string		$editorNo: the number of the RTE instance on the page
	 * @param	string		$sys_language_content: the language of the content element
	 *
	 * @return	string		the generated JS code
	 */
	function getJSCode($act, $editorNo, $sys_language_content)	{
		global $LANG, $TYPO3_CONF_VARS;
		
		$removedProperties = array();
		if (is_array($this->buttonConfig['properties.'])) {
			if ($this->buttonConfig['properties.']['removeItems']) {
				$removedProperties = t3lib_div::trimExplode(',',$this->buttonConfig['properties.']['removeItems'],1);
			}
		}
		
		if ($this->thisConfig['classesImage']) {
			$classesImageArray = t3lib_div::trimExplode(',', $this->thisConfig['classesImage'], 1);
			$classesImageJSOptions = '<option value=""></option>';
			foreach ($classesImageArray as $class) {
				$classesImageJSOptions .= '<option value="' .$class . '">' . $class . '</option>';
			}
		}
		
		$lockPlainWidth = 'false';
		$lockPlainHeight = 'false';
		if (is_array($this->thisConfig['proc.']) && $this->thisConfig['proc.']['plainImageMode']) {
			$plainImageMode = $this->thisConfig['proc.']['plainImageMode'];
			$lockPlainWidth = ($plainImageMode == 'lockDimensions')?'true':'false';
			$lockPlainHeight = ($lockPlainWidth || $plainImageMode == 'lockRatio' || ($plainImageMode == 'lockRatioWhenSmaller'))?'true':'false';
		}

		$JScode='
			var dialog = window.opener.HTMLArea.Dialog.TYPO3Image;
			var plugin = dialog.plugin;
			var HTMLArea = window.opener.HTMLArea;

			function initDialog() {
				window.dialog = window.opener.HTMLArea.Dialog.TYPO3Image;
				window.plugin = dialog.plugin;
				window.HTMLArea = window.opener.HTMLArea;
				dialog.captureEvents("skipUnload");
			}
			
			function jumpToUrl(URL,anchor)	{
				var add_act = URL.indexOf("act=")==-1 ? "&act='.$act.'" : "";
				var add_editorNo = URL.indexOf("editorNo=")==-1 ? "&editorNo='.$editorNo.'" : "";
				var add_sys_language_content = URL.indexOf("sys_language_content=")==-1 ? "&sys_language_content='.$sys_language_content.'" : "";
				var RTEtsConfigParams = "&RTEtsConfigParams='.rawurlencode(t3lib_div::_GP('RTEtsConfigParams')).'";

				var cur_width = selectedImageRef ? "&cWidth="+selectedImageRef.style.width : "";
				var cur_height = selectedImageRef ? "&cHeight="+selectedImageRef.style.height : "";

				var theLocation = URL+add_act+add_editorNo+add_sys_language_content+RTEtsConfigParams+cur_width+cur_height+(anchor?anchor:"");
				window.location.href = theLocation;
				return false;
			}
			function insertImage(file,width,height)	{
				plugin.insertImage(\'<img src="\'+file+\'"' . ($this->defaultClass?(' class="'.$this->defaultClass.'"'):'') . ' width="\'+parseInt(width)+\'" height="\'+parseInt(height)+\'" />\');
			}
			function launchView(url) {
				var thePreviewWindow="";
				thePreviewWindow = window.open("'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').TYPO3_mainDir.'show_item.php?table="+url,"ShowItem","height=300,width=410,status=0,menubar=0,resizable=0,location=0,directories=0,scrollbars=1,toolbar=0");
				if (thePreviewWindow && thePreviewWindow.focus)	{
					thePreviewWindow.focus();
				}
			}
			function getCurrentImageRef() {
				if (plugin.image) {
					return plugin.image;
				} else {
					return null;
				}
			}
			function printCurrentImageOptions() {
				var classesImage = ' . ($this->thisConfig['classesImage']?'true':'false') . ';
				if (classesImage) var styleSelector=\'<select id="iClass" name="iClass" style="width:140px;">' . $classesImageJSOptions  . '</select>\';
				var floatSelector=\'<select id="iFloat" name="iFloat"><option value="">' . $LANG->getLL('notSet') . '</option><option value="none">' . $LANG->getLL('nonFloating') . '</option><option value="left">' . $LANG->getLL('left') . '</option><option value="right">' . $LANG->getLL('right') . '</option></select>\';
				var bgColor=\' class="bgColor4"\';
				var sz="";
				sz+=\'<table border=0 cellpadding=1 cellspacing=1><form action="" name="imageData">\';
				'.(in_array('class', $removedProperties)?'':'
				if(classesImage) {
					sz+=\'<tr><td\'+bgColor+\'><label for="iClass">'.$LANG->getLL('class').': </label></td><td>\'+styleSelector+\'</td></tr>\';
				}')
				.(in_array('width', $removedProperties)?'':'
				if (!(selectedImageRef && selectedImageRef.src.indexOf("RTEmagic") == -1 && '. $lockPlainWidth .')) {
					sz+=\'<tr><td\'+bgColor+\'><label for="iWidth">'.$LANG->getLL('width').': </label></td><td><input type="text" id="iWidth" name="iWidth" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).' /></td></tr>\';
				}')
				.(in_array('height', $removedProperties)?'':'
				if (!(selectedImageRef && selectedImageRef.src.indexOf("RTEmagic") == -1 && '. $lockPlainHeight .')) {
					sz+=\'<tr><td\'+bgColor+\'><label for="iHeight">'.$LANG->getLL('height').': </label></td><td><input type="text" id="iHeight" name="iHeight" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).' /></td></tr>\';
				}')
				.(in_array('border', $removedProperties)?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iBorder">'.$LANG->getLL('border').': </label></td><td><input type="checkbox" id="iBorder" name="iBorder" value="1" /></td></tr>\';')
				.(in_array('float', $removedProperties)?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iFloat">'.$LANG->getLL('float').': </label></td><td>\'+floatSelector+\'</td></tr>\';')
				.(in_array('paddingTop', $removedProperties)?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iPaddingTop">'.$LANG->getLL('padding_top').': </label></td><td><input type="text" id="iPaddingTop" name="iPaddingTop" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).'></td></tr>\';')
				.(in_array('paddingRight', $removedProperties)?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iPaddingRight">'.$LANG->getLL('padding_right').': </label></td><td><input type="text" id="iPaddingRight" name="iPaddingRight" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).' /></td></tr>\';')
				.(in_array('paddingBottom', $removedProperties)?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iPaddingBottom">'.$LANG->getLL('padding_bottom').': </label></td><td><input type="text" id="iPaddingBottom" name="iPaddingBottom" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).' /></td></tr>\';')
				.(in_array('paddingLeft', $removedProperties)?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iPaddingLeft">'.$LANG->getLL('padding_left').': </label></td><td><input type="text" id="iPaddingLeft" name="iPaddingLeft" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).' /></td></tr>\';')
				.(in_array('title', $removedProperties)?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iTitle">'.$LANG->getLL('title').': </label></td><td><input type="text" id="iTitle" name="iTitle"'.$GLOBALS['TBE_TEMPLATE']->formWidth(20).' /></td></tr>\';')
				.(in_array('alt', $removedProperties)?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iAlt">'.$LANG->getLL('alt').': </label></td><td><input type="text" id="iAlt" name="iAlt"'.$GLOBALS['TBE_TEMPLATE']->formWidth(20).' /></td></tr>\';')
				.((!$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['enableClickEnlarge'] || in_array('clickenlarge', $removedProperties))?'':'
				sz+=\'<tr><td\'+bgColor+\'><label for="iClickEnlarge">'.$LANG->sL('LLL:EXT:cms/locallang_ttc.php:image_zoom',1).' </label></td><td><input type="checkbox" name="iClickEnlarge" id="iClickEnlarge" value="0" /></td></tr>\';').'
				sz+=\'<tr><td><input type="submit" value="'.$LANG->getLL('update').'" onClick="return setImageProperties();"></td></tr>\';
				sz+=\'</form></table>\';
				return sz;
			}
			function setImageProperties() {
				var classesImage = ' . ($this->thisConfig['classesImage']?'true':'false') . ';
				if (selectedImageRef)	{
					if (document.imageData.iWidth) {
						if (document.imageData.iWidth.value && parseInt(document.imageData.iWidth.value)) {
							selectedImageRef.style.width = "";
							selectedImageRef.width = parseInt(document.imageData.iWidth.value);
						}
					}
					if (document.imageData.iHeight) {
						if (document.imageData.iHeight.value && parseInt(document.imageData.iHeight.value)) {
							selectedImageRef.style.height = "";
							selectedImageRef.height = parseInt(document.imageData.iHeight.value);
						}
					}
					if (document.imageData.iPaddingTop) {
						if (document.imageData.iPaddingTop.value != "" && !isNaN(parseInt(document.imageData.iPaddingTop.value))) {
							selectedImageRef.style.paddingTop = parseInt(document.imageData.iPaddingTop.value) + "px";
						} else {
							selectedImageRef.style.paddingTop = "";
						}
					}
					if (document.imageData.iPaddingRight) {
						if (document.imageData.iPaddingRight.value != "" && !isNaN(parseInt(document.imageData.iPaddingRight.value))) {
							selectedImageRef.style.paddingRight = parseInt(document.imageData.iPaddingRight.value) + "px";
						} else {
							selectedImageRef.style.paddingRight = "";
						}
					}
					if (document.imageData.iPaddingBottom) {
						if (document.imageData.iPaddingBottom.value != "" && !isNaN(parseInt(document.imageData.iPaddingBottom.value))) {
							selectedImageRef.style.paddingBottom = parseInt(document.imageData.iPaddingBottom.value) + "px";
						} else {
							selectedImageRef.style.paddingBottom = "";
						}
					}
					if (document.imageData.iPaddingLeft) {
						if (document.imageData.iPaddingLeft.value != "" && !isNaN(parseInt(document.imageData.iPaddingLeft.value))) {
							selectedImageRef.style.paddingLeft = parseInt(document.imageData.iPaddingLeft.value) + "px";
						} else {
							selectedImageRef.style.paddingLeft = "";
						}
					}
					if (document.imageData.iTitle) {
						selectedImageRef.title=document.imageData.iTitle.value;
					}
					if (document.imageData.iAlt) {
						selectedImageRef.alt=document.imageData.iAlt.value;
					}

					if (document.imageData.iBorder) {
						selectedImageRef.style.borderStyle = "";
						selectedImageRef.style.borderWidth = "";
						selectedImageRef.style.border = "";  // this statement ignored by Mozilla 1.3.1
						selectedImageRef.style.borderTopStyle = "";
						selectedImageRef.style.borderRightStyle = "";
						selectedImageRef.style.borderBottomStyle = "";
						selectedImageRef.style.borderLeftStyle = "";
						selectedImageRef.style.borderTopWidth = "";
						selectedImageRef.style.borderRightWidth = "";
						selectedImageRef.style.borderBottomWidth = "";
						selectedImageRef.style.borderLeftWidth = "";
						if(document.imageData.iBorder.checked) {
							selectedImageRef.style.borderStyle = "solid";
							selectedImageRef.style.borderWidth = "thin";
						}
						selectedImageRef.removeAttribute("border");
					}

					if (document.imageData.iFloat) {
						var iFloat = document.imageData.iFloat.options[document.imageData.iFloat.selectedIndex].value;
						if (iFloat || selectedImageRef.style.cssFloat || selectedImageRef.style.styleFloat) {
							if (document.all) {
								selectedImageRef.style.styleFloat = (iFloat != "none") ? iFloat : "";
							} else {
								selectedImageRef.style.cssFloat = (iFloat != "none") ? iFloat : "";
							}
						}
					}

					if (classesImage && document.imageData.iClass) {
						var iClass = document.imageData.iClass.options[document.imageData.iClass.selectedIndex].value;
						if (iClass || (selectedImageRef.attributes["class"] && selectedImageRef.attributes["class"].value)) {
							selectedImageRef.className = iClass;
						} else {
							selectedImageRef.className = "";
						}
					}

					if (document.imageData.iClickEnlarge) {
						if (document.imageData.iClickEnlarge.checked) {
							selectedImageRef.setAttribute("clickenlarge","1");
						} else {
							selectedImageRef.removeAttribute("clickenlarge");
						}
					}
					dialog.close();
				}
				return false;
			}
			function insertImagePropertiesInForm()	{
				var classesImage = ' . ($this->thisConfig['classesImage']?'true':'false') . ';
				if (selectedImageRef)	{
					var styleWidth, styleHeight, padding;
					if (document.imageData.iWidth) {
						styleWidth = selectedImageRef.style.width ? selectedImageRef.style.width : selectedImageRef.width;
						styleWidth = parseInt(styleWidth);
						if (!(isNaN(styleWidth) || styleWidth == 0)) {
							document.imageData.iWidth.value = styleWidth;
						}
					}
					if (document.imageData.iHeight) {
						styleHeight = selectedImageRef.style.height ? selectedImageRef.style.height : selectedImageRef.height;
						styleHeight = parseInt(styleHeight);
						if (!(isNaN(styleHeight) || styleHeight == 0)) {
							document.imageData.iHeight.value = styleHeight;
						}
					}
					if (document.imageData.iPaddingTop) {
						var padding = selectedImageRef.style.paddingTop ? selectedImageRef.style.paddingTop : selectedImageRef.vspace;
						var padding = parseInt(padding);
						if (isNaN(padding) || padding <= 0) { padding = ""; }
						document.imageData.iPaddingTop.value = padding;
					}
					if (document.imageData.iPaddingRight) {
						padding = selectedImageRef.style.paddingRight ? selectedImageRef.style.paddingRight : selectedImageRef.hspace;
						var padding = parseInt(padding);
						if (isNaN(padding) || padding <= 0) { padding = ""; }
						document.imageData.iPaddingRight.value = padding;
					}
					if (document.imageData.iPaddingBottom) {
						var padding = selectedImageRef.style.paddingBottom ? selectedImageRef.style.paddingBottom : selectedImageRef.vspace;
						var padding = parseInt(padding);
						if (isNaN(padding) || padding <= 0) { padding = ""; }
						document.imageData.iPaddingBottom.value = padding;
					}
					if (document.imageData.iPaddingLeft) {
						var padding = selectedImageRef.style.paddingLeft ? selectedImageRef.style.paddingLeft : selectedImageRef.hspace;
						var padding = parseInt(padding);
						if (isNaN(padding) || padding <= 0) { padding = ""; }
						document.imageData.iPaddingLeft.value = padding;
					}
					if (document.imageData.iTitle) {
						document.imageData.iTitle.value = selectedImageRef.title;
					}
					if (document.imageData.iAlt) {
						document.imageData.iAlt.value = selectedImageRef.alt;
					}
					if (document.imageData.iBorder) {
						if((selectedImageRef.style.borderStyle && selectedImageRef.style.borderStyle != "none" && selectedImageRef.style.borderStyle != "none none none none") || selectedImageRef.border) {
							document.imageData.iBorder.checked = 1;
						}
					}
					if (document.imageData.iFloat) {
						var fObj=document.imageData.iFloat;
						var value = (selectedImageRef.style.cssFloat ? selectedImageRef.style.cssFloat : selectedImageRef.style.styleFloat);
						var l=fObj.length;
						for (var a=0;a<l;a++)	{
							if (fObj.options[a].value == value) {
								fObj.selectedIndex = a;
							}
						}
					}

					if (classesImage && document.imageData.iClass) {
						var fObj=document.imageData.iClass;
						var value=selectedImageRef.className;
						var l=fObj.length;
						for (var a=0;a < l; a++)	{
							if (fObj.options[a].value == value)	{
								fObj.selectedIndex = a;
							}
						}
					}
					if (document.imageData.iClickEnlarge) {
						if (selectedImageRef.getAttribute("clickenlarge") == "1") {
							document.imageData.iClickEnlarge.checked = 1;
						} else {
							document.imageData.iClickEnlarge.checked = 0;
						}
					}
					return false;
				}
			}

			var selectedImageRef = getCurrentImageRef();';	// Setting this to a reference to the image object.
		return $JScode;
	}
	
	/**
	 * Initializes the configuration variables
	 *
	 * @return	void
	 */
	 public function initConfiguration() {
		$this->thisConfig = $this->getRTEConfig();
		$this->buttonConfig = $this->getButtonConfig();
		$this->imgPath = $this->getImgPath();
		$this->RTEImageStorageDir = $this->getRTEImageStorageDir();
		$this->defaultClass = $this->getDefaultClass();
		$this->setMaximumImageDimensions();
	 }
	
	/**
	 * Get the RTE configuration from Page TSConfig
	 *
	 * @return	array		RTE configuration array
	 */
	protected function getRTEConfig()	{
		global $BE_USER;
		
		$RTEtsConfigParts = explode(':',t3lib_div::_GP('RTEtsConfigParams'));
		$RTEsetup = $BE_USER->getTSConfig('RTE',t3lib_BEfunc::getPagesTSconfig($RTEtsConfigParts[5]));
		return t3lib_BEfunc::RTEsetup($RTEsetup['properties'],$RTEtsConfigParts[0],$RTEtsConfigParts[2],$RTEtsConfigParts[4]);
	}
	
	/**
	 * Get the path of the image to be inserted or modified
	 *
	 * @return	string		path to the image
	 */
	protected function getImgPath()	{
		$RTEtsConfigParts = explode(':',t3lib_div::_GP('RTEtsConfigParams'));
		return $RTEtsConfigParts[6];
	}
	
	/**
	 * Get the configuration of the image button
	 *
	 * @return	array		the configuration array of the image button
	 */
	protected function getButtonConfig()	{
		return ((is_array($this->thisConfig['buttons.']) && is_array($this->thisConfig['buttons.']['image.'])) ? $this->thisConfig['buttons.']['image.'] : array());
	}
	
	/**
	 * Get the allowed items or tabs
	 *
	 * @param	string		$items: initial list of possible items
	 * @return	array		the allowed items
	 */
	public function getAllowedItems($items)	{
		$allowedItems = explode(',', $items);
		$clientInfo = t3lib_div::clientInfo();
		if ($clientInfo['BROWSER'] !== 'opera') {
			$allowedItems[] = 'dragdrop';
		}
		if (is_array($this->buttonConfig['options.']) && $this->buttonConfig['options.']['removeItems']) {
			$allowedItems = array_diff($allowedItems, t3lib_div::trimExplode(',', $this->buttonConfig['options.']['removeItems'], 1));
		} else {
			$allowedItems = array_diff($allowedItems, t3lib_div::trimExplode(',', $this->thisConfig['blindImageOptions'], 1));
		}
		return $allowedItems;
	}
	
	/**
	 * Get the default image class
	 *
	 * @return	string		the default class, if any
	 */
	protected function getDefaultClass() {
		$defaultClass = '';
		if (is_array($this->buttonConfig['properties.'])) {
			if (is_array($this->buttonConfig['properties.']['class.']) && trim($this->buttonConfig['properties.']['class.']['default'])) {
				$defaultClass = trim($this->buttonConfig['properties.']['class.']['default']);
			}
		}
		return $defaultClass;
	}
	
	/**
	 * Set variables for maximum image dimensions
	 *
	 * @return	void
	 */
	protected function setMaximumImageDimensions() {
		if ($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['plainImageMaxWidth']) $this->plainMaxWidth = $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['plainImageMaxWidth'];
		if ($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['plainImageMaxHeight']) $this->plainMaxHeight = $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['plainImageMaxHeight'];
		if (is_array($this->buttonConfig['options.']) && is_array($this->buttonConfig['options.']['plain.'])) {
			if ($this->buttonConfig['options.']['plain.']['maxWidth']) $this->plainMaxWidth = $this->buttonConfig['options.']['plain.']['maxWidth'];
			if ($this->buttonConfig['options.']['plain.']['maxHeight']) $this->plainMaxHeight = $this->buttonConfig['options.']['plain.']['maxHeight'];
		}
		if (!$this->plainMaxWidth) $this->plainMaxWidth = 640;
		if (!$this->plainMaxHeight) $this->plainMaxHeight = 680;
		if (is_array($this->buttonConfig['options.']) && is_array($this->buttonConfig['options.']['magic.'])) {
			if ($this->buttonConfig['options.']['magic.']['maxWidth']) $this->magicMaxWidth = $this->buttonConfig['options.']['magic.']['maxWidth'];
			if ($this->buttonConfig['options.']['magic.']['maxHeight']) $this->magicMaxHeight = $this->buttonConfig['options.']['magic.']['maxHeight'];
		}
			// These defaults allow images to be based on their width - to a certain degree - by setting a high height. Then we're almost certain the image will be based on the width
		if (!$this->magicMaxWidth) $this->magicMaxWidth = 300;
		if (!$this->magicMaxHeight) $this->magicMaxHeight = 1000;
	}
	
	/**
	 * Get the help message to be displayed on a given tab
	 *
	 * @param	string	$act: the identifier of the tab
	 * @return	string	the text of the message
	 */
	public function getHelpMessage($act) {
		global $LANG;
		switch ($act)	{
			case 'plain':
				return sprintf($LANG->getLL('plainImage_msg'), $this->plainMaxWidth, $this->plainMaxHeight);
				break;
			case 'magic':
				return sprintf($LANG->getLL('magicImage_msg'));
				break;
			default:
				return '';
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_tx_rtehtmlarea_select_image.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_tx_rtehtmlarea_select_image.php']);
}

?>