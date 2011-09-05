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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Gallery (background)' for the 'ew_gallery' extension.
 * Renders the big Background Gallery
 *
 * @author	Elio Wahlen <vorname at vorname punkt de>
 * @package	TYPO3
 * @subpackage	tx_ewgallery
 */
class tx_ewgallery_pi2 extends tx_ewpibase {
	var $prefixId      = 'tx_ewgallery_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_ewgallery_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ew_gallery';	// The extension key.
	var $pi_checkCHash = true;
		
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{
		$this->conf = $conf;
		$this->pi_loadLL();
		$this->loadTemplate('pi2/template.html');
		
		$videos = $this->get_dam_images( $this->cObj->data['uid'], 'tx_ewgallery_video');
		$this->videoFiles = '';
		foreach( $videos as $video ) {
			$type = $this->getMimeType(PATH_site.$video['path']);
			$this->videoFiles .= '<input type="hidden" value="'.$video['path'].'" name="'.$type.'" />';
		}
		$this->addMarker('VIDEO_FILES',$this->videoFiles);

		$this->video_button = current($this->get_dam_images( $this->cObj->data['uid'], 'tx_ewgallery_video_button'));
		$this->autostartVideo = $this->cObj->data['tx_ewgallery_video_autostart'];
		
		$this->addMarker('VIDEO_AUTOSTART',$this->autostartVideo);
		$this->addMarker('VIDEO_BUTTON',empty($videos) ? '' : $this->cObj->IMAGE(array('file' => $this->video_button['path'])));
		$this->addMarker('VIDEO_LINK_HIDDEN',empty($videos) ? 'display:none;' : '');
		$content = $this->renderSubpart( 'GALLERY' );
		
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_gallery/pi2/class.tx_ewgallery_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_gallery/pi2/class.tx_ewgallery_pi2.php']);
}

?>