<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Elio Wahlen <vorname at vorname punkt de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(t3lib_extMgm::extPath('ew_pibase'). 'class.tx_ewpibase.php');

/**
 * Plugin 'Gallery (normal)' for the 'ew_gallery' extension.
 * Renders the normal Content Element Gallery
 *
 * @author	Elio Wahlen <vorname at vorname punkt de>
 * @package	TYPO3
 * @subpackage	tx_ewgallery
 */
class tx_ewgallery_pi1 extends tx_ewpibase {
	var $prefixId      = 'tx_ewgallery_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_ewgallery_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ew_gallery';	// The extension key.
	var $pi_checkCHash = true;
	var $maxW = 300;
	var $maxH = 0;
	var $width = 300;
	var $height = 200;
	var $bgMaxW = 1000;
	var $bgMaxH = 800;
	var $bgParams = '';
	var $fadeDuration = 0;
	var $bgFadeDuration = 0;
	var $slideDuration = 10;
	var $autostartVideo;
	var $conf;
	var $firstImage;
	var $imageList;
	var $video;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{
		//$content = $this->cObj->data['tx_ewgallery_smallimage'];
		$this->conf = $conf;
		$this->pi_loadLL();
		$this->loadTemplate('pi1/template.html');

		$this->fetchMedia();
		$content .= $this->renderGallery();
		$content .= $this->renderBackgroundGallery();
		// sets the page's content-area background image via css
		$this->setPageContentBackground();
		
		return $content;
	}
	
	function fetchMedia() {
		$this->maxW = $this->conf['maxW'] ? $this->conf['maxW'] : $this->maxW;
		$this->maxH = $this->conf['maxH'] ? $this->conf['maxH'] : $this->maxH;
		$this->width = $this->conf['width'] ? $this->conf['width'] : $this->width;
		$this->height = $this->conf['height'] ? $this->conf['height'] : $this->height;
		$this->bgMaxW = $this->conf['bgMaxW'] ? $this->conf['bgMaxW'] : $this->bgMaxW;
		$this->bgMaxH = $this->conf['bgMaxH'] ? $this->conf['bgMaxH'] : $this->bgMaxH;
		$this->bgParams = $this->conf['bgParams'] ? $this->conf['bgParams'] : $this->bgParams;
		$this->fadeDuration = $this->conf['fadeDuration'] ? $this->conf['fadeDuration'] : $this->fadeDuration;
		$this->autostartVideo = $this->cObj->data['tx_ewgallery_video_autostart'];
		$this->bgFadeDuration = $this->conf['bgFadeDuration'] ? $this->conf['bgFadeDuration'] : $this->bgFadeDuration;
		$this->slideDuration = $this->conf['slideDuration'] ? $this->conf['slideDuration'] : $this->slideDuration;
		$this->firstImage = current($this->get_dam_images( $this->cObj->data['uid'], 'tx_ewgallery_smallimage'));
		$this->firstBackgroundImage = current($this->get_dam_images( $this->cObj->data['uid'], 'tx_ewgallery_bigimage'));
		$this->imageList = $this->get_dam_images( $this->cObj->data['uid'], 'tx_dam_images');
		$this->video = current($this->get_dam_images( $this->cObj->data['uid'], 'tx_ewgallery_video'));
		$this->video_button = current($this->get_dam_images( $this->cObj->data['uid'], 'tx_ewgallery_video_button'));
	}
	
	function renderGallery() {
		$content = '';
		// if only background gallery
		$this->addMarker( 'DISPLAY', ($this->cObj->data['tx_ewgallery_type'] == 1) ? 'display:none;' : '');
		$this->addMarker( 'UID', $this->cObj->data['uid'] );
		// first, render the small first image
		$i = 0;
		$this->addMarker( 'NO', $i );
		$this->addMarker( 'STYLE', '' );
		$this->addMarker( 'IMAGE', $this->cObj->IMAGE(array('file'=>$this->firstImage['path'],
			'file.' => array('width' => $this->width.'c','height' => $this->height.'c')) ) );
		$firstImage = $this->renderSubpart( 'ITEM' );
		// then, render the list
		$imageList = '';
		foreach( $this->imageList as $file ) {
			$i++;
			$this->addMarker( 'NO', $i );
			$this->addMarker( 'STYLE', 'display:none;' );
			$this->addMarker( 'IMAGE', $this->cObj->IMAGE(array('file'=>$file['path'],
				'file.' => array('width' => $this->width.'c','height' => $this->height.'c'))) );
			$imageList .= $this->renderSubpart( 'ITEM' );
		}
		$this->addMarker('FIRST_IMAGE',$firstImage);
		$this->addMarker('IMAGE_LIST',$imageList);
		$this->addMarker('WIDTH',$this->width);
		$this->addMarker('HEIGHT',$this->height);
		$this->addMarker('FADE_DURATION',$this->fadeDuration);
		$this->addMarker('VIDEO_AUTOSTART',$this->autostartVideo);
		$this->addMarker('LANGID',$GLOBALS['TSFE']->sys_language_uid);
		$galleryText = $this->pi_getLL('watchGallery');
		$this->addMarker('GALLERY_TEXT',$galleryText);
		$teaserText = $this->pi_getLL('watchTrailer');
		$this->addMarker('VIDEO_TEXT',$teaserText);
		$this->addMarker('VIDEO_FILE',empty($this->video) ? '' : $this->video['path']);
		$this->addMarker('VIDEO_LINK_HIDDEN',empty($this->video) ? 'display:none;' : '');
		$content = $this->renderSubpart( 'GALLERY' );
		return $content;
	}
	
	function renderBackgroundGallery() {
		$content = '';
		$this->addMarker( 'UID', $this->cObj->data['uid'] );
		// first, render the small first image
		$i = 0;
		$this->addMarker( 'NO', $i );
		$this->addMarker( 'STYLE', '' );
		$this->addMarker( 'IMAGE', $this->cObj->IMAGE(array('file'=>$this->firstBackgroundImage['path'],
			'file.' => array('maxW' => $this->bgMaxW,'maxH' => $this->bgMaxH,'params' => $this->bgParams)) ) );
		$this->addMarker( 'CREDITS',empty($this->firstBackgroundImage['copyright']) ? '' : 'by '.$this->firstBackgroundImage['copyright']);
		$this->addMarker( 'DISPLAY_CREDITS', '' );
		$firstImage = $this->renderSubpart( 'BACKGROUND_ITEM' );
		// then, render the list
		$imageList = '';
		foreach( $this->imageList as $file ) {
			$i++;
			$this->addMarker( 'NO', $i );
			$this->addMarker( 'STYLE', 'visibility:hidden;' );
			$this->addMarker( 'IMAGE', $this->cObj->IMAGE(array('file'=>$file['path'],
				'file.' => array('maxW' => $this->bgMaxW,'maxH' => $this->bgMaxH,'params' => $this->bgParams))) );
			$this->addMarker( 'CREDITS',empty($file['copyright']) ? '' : 'by '.$file['copyright']);
			$this->addMarker( 'DISPLAY_CREDITS', 'display: none;' );
			$imageList .= $this->renderSubpart( 'BACKGROUND_ITEM' );
		}
		$this->addMarker('FADE_DURATION',$this->bgFadeDuration);
		$this->addMarker('SLIDE_DURATION',$this->slideDuration);
		$this->addMarker('FIRST_IMAGE',$firstImage);
		$this->addMarker('IMAGE_LIST',$imageList);
		$content = $this->renderSubpart( 'BACKGROUND_GALLERY' );
		return $content;
	}
	
	/*
	 * THIS RENDERS THE GLOBAL PAGE'S VIDEO LIST
	*/
	
	function renderPageVideoList($content, $conf) {
		$this->pi_loadLL();
		$this->loadTemplate('pi1/template.html');
		
		$this->pageVideos = $this->get_dam_images( $GLOBALS['TSFE']->id, 'tx_ewgallery_video', 'pages');
		$videoTags = '';
		foreach( $this->pageVideos as $video ) {
			$videoTags .= '<input type="hidden" value="'.$video['path'].'" />';
		}
		$this->addMarker('VIDEOS',$videoTags);
		$content = $this->renderSubpart( 'PAGE_VIDEOS' );
		return $content;
	}
	
	function setPageContentBackground() {
		$backgroundImage = current($this->get_dam_images( $GLOBALS['TSFE']->id, 'tx_ewgallery_image', 'pages'));
		$url = $backgroundImage['path'];
		if ( !file_exists( $url ) ) {
			return;
		}
		$x = $GLOBALS['TSFE']->page['tx_ewgallery_image_x'];
		$y = $GLOBALS['TSFE']->page['tx_ewgallery_image_y'];
		// hintergrund-bild und höhe (auch für ie)
		$GLOBALS['TSFE']->additionalHeaderData['576'] = '<style tyle="text/css"><!-- #contentWrap { background-image:url('.
			$url.'); background-repeat: no-repeat; background-position: '.$x.'px '.$y.'px; } --></style>';
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_gallery/pi1/class.tx_ewgallery_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_gallery/pi1/class.tx_ewgallery_pi1.php']);
}

?>