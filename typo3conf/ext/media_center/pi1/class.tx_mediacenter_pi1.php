<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Patrick Rodacker <patrick.rodacker@the-reflection.de>
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

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Google Earth' for the 'media_center' extension.
 *
 * @author	Patrick Rodacker <patrick.rodacker@the-reflection.de>
 * @package	TYPO3
 * @subpackage	media_center
 */
class tx_mediacenter_pi1 extends tslib_pibase {
	var $prefixId      	= 'tx_mediacenter_pi1';		// Same as class name
	var $scriptRelPath 	= 'pi1/class.tx_mediacenter_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        	= 'media_center';	// The extension key.
	var $pi_checkCHash 	= true;

	var $enableDebugging = false; // flag to enable debugging
	var $fConf; // the flexform plugin configuration
	var $conf; // the configuration merged from typoscript and flexform config

	var $contentId; // id of the content element
	var $table = 'tx_mediacenter_item'; // table to get items from

	var $containerId; // css id of the container for the flash movie
	var $flashplayer; // path to the flashplayer
	var $requiredFlashMajorVersion = 9;
	var $requiredFlashMinorVersion = 0;
	var $requiredFlashRevision = 0;
	var $flashvars; // array to store the flashvars

	const COUNT_PLAYLIST_ITEMS = true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {

		// init flexform configuration and merge with typoscript configuration
		// flexform configuration takes precedence
		$this->initFlexformConfig();

		// merge configuration if renderingMode is not typoscript
		if ($conf['renderingMode'] != 'typoscript') {
			$this->mergeConfiguration($conf);
		} else {
			$this->conf = $conf;
		}

		$this->pi_loadLL();

		// set the unique id for the player container
		$this->contentId = $this->cObj->data['uid'];
		$this->containerId = 'media-center-'.$this->contentId;

		$container 	= '<div id="'.$this->containerId.'" class="media-center">'.$this->getNoFlashContent().'</div>';
		$script 	= $this->getFlashJavascriptCode();
		$content 	= $this->cObj->wrap($container, $this->conf['wrap']).$script;
		$content 	.= '<noscript>'.$this->cObj->wrap($this->getNoScriptContent(), $this->conf['noscriptInsideWrap']).'</noscript>';

		// some debug output if enabled
		if ($this->enableDebugging) {
			t3lib_div::debug(array(
				'conf' => $this->conf,
				'content' => $content));
		}
		return $content;
	}

	/**
	 * init the flexform configuration
	 * @return void
	 */
	function initFlexformConfig(){
		 $this->pi_initPIflexForm();
		 $this->fConf = array();
		 $piFlexForm = $this->cObj->data['pi_flexform'];
		 if ($piFlexForm['data']) {
			 foreach ( $piFlexForm['data'] as $sheet => $data ) {
			  	foreach ( $data as $lang => $value ) {
			   		foreach ( $value as $key => $val ) {
			   			if (($sheet == 'layout') || $sheet == 'behaviour' || $sheet == 'external') {
			   				if ($key == 'width' || $key == 'height') {
			   					$this->fConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
			   				} else {
			   					// logo, skin and aboutlin use the link wizards, so they need to be transformed to absolute urls
			   					if ($key == 'logo' || $key == 'skin' || $key == 'aboutlink') {
			   						$this->fConf['flashvars.'][$key] = $this->getAbsoluteUrl($this->pi_getFFvalue($piFlexForm, $key, $sheet));
			   					} else {
			   						$this->fConf['flashvars.'][$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
			   					}
			   				}
			   			} else {
				    		$this->fConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
			   			}
			 		}
				}
			}
		 }
	}

	/**
	 * merge the typoscript and flexform configuration
	 * @return void
	 */
	function mergeConfiguration($typoscriptConfiguration) {
		// merge flashvars
		if (is_array($this->fConf['flashvars.']) && is_array($typoscriptConfiguration['flashvars.'])) {
			$flashvars = t3lib_div::array_merge_recursive_overrule($typoscriptConfiguration['flashvars.'], $this->fConf['flashvars.'], 0, false);
		}
		// reset
		$this->fConf['flashvars.'] = null;
		$typoscriptConfiguration['flashvars.'] = null;

		// merge other settings
		if (is_array($this->fConf) && is_array($typoscriptConfiguration)) {
			$this->conf = t3lib_div::array_merge_recursive_overrule($typoscriptConfiguration, $this->fConf, 0, false);
		}

		// add flashvars
		$this->conf['flashvars.'] = $flashvars;
	}

	/**
	 * return the javascript code to embed the flashplayer using the swfobject
	 * will render configurable content if the detected flash version does not match the
	 * required version or if javascript has been disabled.
	 *
	 * @return string
	 */
	function getFlashJavascriptCode() {

		// set path to flashplayer object
		$this->flashplayer = t3lib_div::getIndpEnv('TYPO3_SITE_URL').t3lib_extMgm::siteRelpath($this->extKey).'lib/mediaplayer/player.swf';

		// path to swfobject lib
		$swfobjectPath = t3lib_extMgm::siteRelpath($this->extKey).'lib/swfobject/';

		// get the url to the playlist file if mode is set to normal
		// if mode is set to typoscript, get the file setting from configuration
		if ($this->conf['renderingMode'] == 'typoscript') {
			$file = $this->getFileUrl();
		} else {
			$file = $this->getPlaylistUrl();
		}

		// include detection and flv player script in <head> section
		$GLOBALS['TSFE']->additionalHeaderData ['tx_mediacenter'] = '<script type="text/javascript" src="'.$swfobjectPath.'swfobject.js"></script>';
		$javascriptCode .= '
			<script type="text/javascript">
				var flashvars = {};
					flashvars.file = \''.$file.'\';
					flashvars.id = \''.$this->containerId.'\';
					'.$this->getFlashvarsAsString().'
				var params = {};
// this causes bug (not calling firefox playerReady event)
	//				if ( !jQuery.browser.mozilla )
						params.wmode = "'.$this->conf['wmode'].'";
					params.scale = "noscale";
					params.allowfullscreen = "true";
					params.allowscriptaccess = "always";
					params.bgcolor = "'.$this->conf['bgcolor'].'";
				var attributes = {};
					attributes.id = "'.$this->containerId.'";
					attributes.name = "'.$this->containerId.'";
				swfobject.embedSWF(
					"'.$this->flashplayer.'",
					"'.$this->containerId.'",
					"'.$this->calculatePlayerWidth().'",
					"'.$this->calculatePlayerHeight().'",
					"'.$this->getRequiredFlashVersionAsString().'",
					"'.$swfobjectPath.'expressInstall.swf",
					flashvars,
					params,
					attributes);
			</script>';

		return $javascriptCode;
	}

	/**
	 * return the url to the playlist
	 *
	 * @return string
	 */
	function getPlaylistUrl() {
		return urlencode(t3lib_div::getIndpEnv('TYPO3_SITE_URL').'index.php?id='.$GLOBALS['TSFE']->id.'&type='.$this->conf['export.']['typeNum'].'&playerUid='.$this->cObj->data['uid']. '&' . $this->conf['export.']['languageParameter'] . '=' . $GLOBALS['TSFE']->sys_language_uid);
	}

	/**
	 * return the url to the file set in typoscript configuration
	 *
	 * @return string
	 */
	function getFileUrl() {
		if ($this->conf['streamer'] != '') {
			return urlencode($this->getAbsoluteUrl($this->conf['file']));
		} else {
			return ($this->conf['file']);
		}
	}

	/**
	 * calculates the width of the embed tag according to the configuration settings
	 * if the playlist is positioned on the right, the width will be the addition of the
	 * vide area width and the playlist width, otherwise the width of the video are will be returned
	 *
	 * @return integer
	 */
	function calculatePlayerWidth() {
		switch ($this->conf['flashvars.']['playlist']) {
			case 'right':
				$width = intval($this->conf['width']) + intval($this->conf['flashvars.']['playlistsize']);
				break;
			default:
				$width = intval($this->conf['width']);
				break;
		}
		// Hack by elio: Prozenangaben ermöglichen
		if ( $this->conf['width'] < 0 ) {
			$width = -intval($this->conf['width']).'%';
		}
		return $width;
	}

	/**
	 * return the height of the player
	 *
	 * @return integer
	 */
	function calculatePlayerHeight() {
		switch ($this->conf['flashvars.']['playlist']) {
			case 'bottom':
				$height = intval($this->conf['height']) + ($this->conf['flashvars.']['playlistsize']);
				break;
			case 'over':
			default:
				$height = intval($this->conf['height']);
				break;
		}
		// Hack by elio: Prozenangaben ermöglichen
		if ( $this->conf['height'] < 0 ) {
			$height = -intval($this->conf['height']).'%';
		}
		return $height;
	}

	/**
	 * build a string from the configured flashvars and return it
	 *
	 * @return string
	 */
	function getFlashvarsAsString() {
		$string = null;
		$flashvars = $this->conf['flashvars.'];
		if (count($flashvars) > 0) {
			foreach ($flashvars as $flashvar => $value) {
				if ($value == '0' || $value  == '') {
				} else {
					$string .= "flashvars['".str_replace('_DOT_','.',$flashvar)."'] = '".$value."';";
					//$string .= "flashvars.".$flashvar." = '".$value."';";
				}
			}
		}
		// remove trainling commata and return
		return substr($string, 0, strlen($string));
	}

	/**
	 * export the playlist for the given tt_content element uid
	 *
	 * TODO: clean up the playlist export!
	 * TODO: support differten export formats for e.g. iTunes integration
	 *
	 * @return string
	 */
	public function exportPlaylist($conf, $content) {
		global $TYPO3_CONF_VARS;

		// get the content element according to the playerUid
		$contentElement = $this->getContentElementFromPlayerUid();

		// create cObj
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->cObj->start($contentElement, 'tt_content');

		// init flexfrom configuration to set $this->fConf
		$this->initFlexformConfig();
		$this->mergeConfiguration($content);

		// create new xml document
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = true;

		// switch format
		switch ($format = $content['format']) {
			case 'mRSS':
				$dom = $this->getPlaylist_mRSS($dom, $conf, $content);
				break;
			case 'iRSS':
				$dom = $this->getPlaylist_iRSS($dom, $conf, $content);
				break;
			default:
				$dom = $this->getPlaylist_XSPF($dom, $conf, $content);
			break;
		}

		// set header to xml
		header('Content-Type: text/xml');
		return $dom->saveXML();
	}

	/**
	 * returns the playlist in mRSS format
	 *
	 * @param 	DOMDocument 	$dom 		the dom object
	 * @param 	Array			$conf		the export configuration
	 * @param 	Array 			$content 	content array
	 *
	 * @return string
	 */
	function getPlaylist_mRSS($dom, $conf, $content) {

		// create the root node
		$rss = $dom->createElement('rss');
		$rss->setAttribute('version', '2.0');
		$rss->setAttribute('xmlns:media', 'http://search.yahoo.com/mrss/');
		$rss = $dom->appendChild($rss);

		// create channel node
		$channel = $dom->createElement('channel');
		$channelNode = $rss->appendChild($channel);

		// create title node
		if ($content['channelTitle']) {
			$channelTitle = $dom->createElement('title', $this->escapeString($content['channelTitle']));
			$channelNode->appendChild($channelTitle);
		}

		// create link node
		if ($content['channelLink']) {
			$link = $dom->createElement('link', $content['channelLink']);
		} else {
			$link = $dom->createElement('link', t3lib_div::getIndpEnv('TYPO3_SITE_URL'));
		}
		$channelNode->appendChild($link);

		// get the sorted items from flexform config
		$items = $this->getPlaylistItems();

		foreach ($items as $singleItem) {

			// create item node
			$item = $dom->createElement('item');
			$itemNode = $channelNode->appendChild($item);

			// create title node
			$title = $dom->createElement('title', $this->escapeString($singleItem['title']));
			$itemNode->appendChild($title);

			// create enclosure node
			$enclosure = $dom->createElement('media:content');

			if ($singleItem['file'] != '') {
				// set file path
				$file = $this->getFilePath($singleItem['file']);

			} else {
				$file = $this->getAbsoluteUrl($singleItem['file_url']);
			}

			// add file and type
			$enclosure->setAttribute('url', $file);
			$enclosure->setAttribute('type', $this->getFileType($file));

			// set start tag
			if ($singleItem['start']) {
				$enclosure->setAttribute('start', $singleItem['start']);
			}

			// add enclosure node
			$itemNode->appendChild($enclosure);

			// create thumbnail node
			if ($singleItem['image']) {
				$image = $this->getFilePath($singleItem['image']);
				$thumbnail = $dom->createElement('media:thumbnail');
				$thumbnail->setAttribute('url', $image);
				$itemNode->appendChild($thumbnail);
			}

			// create description node
			if ($singleItem['description']) {
				$description = $dom->createElement('description', $this->escapeString($singleItem['description']));
				$itemNode->appendChild($description);
			}

			// create author node
			if ($singleItem['author']) {
				$author = $dom->createElement('media:credit', $this->escapeString($singleItem['author']));
				$author->setAttribute('role', 'author');
				$itemNode->appendChild($author);
			}

			// create link node
			if (!$singleItem['link']) {
				$singleItem['link'] = $GLOBALS['TSFE']->id;
			}
			$link = $dom->createElement('link', $this->getAbsoluteUrl($singleItem['link'], true));
			$itemNode->appendChild($link);

			// add item to channel
			$channelNode->appendChild($item);
		}

		return $dom;
	}

	/**
	 * returns the playlist in iRSS format
	 *
	 * @param 	DOMDocument 	$dom 		the dom object
	 * @param 	Array			$conf		the export configuration
	 * @param 	Array 			$content 	content array
	 *
	 * @return string
	 */
	function getPlaylist_XSPF($dom, $conf, $content) {

		// create the root node
		$playlist = $dom->createElement('playlist');
		$playlist->setAttribute('version', '1');
		$playlist->setAttribute('xmlns', 'http://xspf.org/ns/0/');
		$playlist = $dom->appendChild($playlist);

		// create title node
		if ($content['channelTitle']) {
			$channelTitle = $dom->createElement('title', $this->escapeString($content['channelTitle']));
			$playlist->appendChild($channelTitle);
		}

		// create info node
		if ($content['channelLink']) {
			$link = $dom->createElement('info', $content['channelLink']);
		} else {
			$link = $dom->createElement('info', t3lib_div::getIndpEnv('TYPO3_SITE_URL'));
		}
		$playlist->appendChild($link);

		// create tracklist node
		$tracklist = $dom->createElement('tracklist');
		$playlist = $playlist->appendChild($tracklist);

		// get the sorted items from flexform config
		$items = $this->getPlaylistItems();

		foreach ($items as $singleItem) {

			// create item node
			$track = $dom->createElement('track');
			$trackNode = $tracklist->appendChild($track);

			// create title node
			$title = $dom->createElement('title', $this->escapeString($singleItem['title']));
			$trackNode->appendChild($title);

			// create author node
			if ($singleItem['author']) {
				$author = $dom->createElement('creator', $this->escapeString($singleItem['author']));
				$trackNode->appendChild($author);
			}

			// create link node
			if (!$singleItem['link']) {
				$singleItem['info'] = $GLOBALS['TSFE']->id;
			}
			$link = $dom->createElement('info', $this->getAbsoluteUrl($singleItem['link'], true));
			$trackNode->appendChild($link);

			// create description node
			if ($singleItem['description']) {
				$annotation = $dom->createElement('annotation', $this->escapeString($singleItem['description']));
				$trackNode->appendChild($annotation);
			}

			if ($singleItem['file'] != '') {
				// set file path
				$file = $this->getFilePath($singleItem['file']);

			} else {
				$file = $this->getAbsoluteUrl($singleItem['file_url']);
			}
			$fileNode = $dom->createElement('location', $file);
			$trackNode->appendChild($fileNode);

			// creatw file type node
			$fileTypeNode = $dom->createElement('meta', $this->getFileType($file));
			$fileTypeNode->setAttribute('rel', 'type');
			$trackNode->appendChild($fileTypeNode);

			// create thumbnail node
			if ($singleItem['image']) {
				$image = $this->getFilePath($singleItem['image']);
				$thumbnail = $dom->createElement('image', $image);
				$trackNode->appendChild($thumbnail);
			}

			// create duration node
			if ($singleItem['duration']) {
				$duration = $dom->createElement('meta', $this->escapeString($singleItem['duration']));
				$duration->setAttribute('rel', 'duration');
				$trackNode->appendChild($duration);
			}

			// create captions node
			if ($singleItem['captions']) {
				$captions = $dom->createElement('meta', $this->getAbsoluteUrl($singleItem['captions']));
				$captions->setAttribute('rel', 'captions');
				$trackNode->appendChild($captions);
			}

			// add item to channel
			$tracklist->appendChild($trackNode);
		}

		return $dom;
	}

	/**
	 * returns the playlist in iRSS format
	 *
	 * @param 	DOMDocument 	$dom 		the dom object
	 * @param 	Array			$conf		the export configuration
	 * @param 	Array 			$content 	content array
	 *
	 * @return string
	 */
	function getPlaylist_iRSS($dom, $conf, $content) {

		// create the root node
		$rss = $dom->createElement('rss');
		$rss->setAttribute('version', '2.0');
		$rss->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
		$rss = $dom->appendChild($rss);

		// create channel node
		$channel = $dom->createElement('channel');
		$channelNode = $rss->appendChild($channel);

		// create title node
		if ($content['channelTitle']) {
			$channelTitle = $dom->createElement('title', $this->escapeString($content['channelTitle']));
			$channelNode->appendChild($channelTitle);
		}

		// create link node
		if ($content['channelLink']) {
			$link = $dom->createElement('link', $content['channelLink']);
		} else {
			$link = $dom->createElement('link', t3lib_div::getIndpEnv('TYPO3_SITE_URL'));
		}
		$channelNode->appendChild($link);

		// get the sorted items from flexform config
		$items = $this->getPlaylistItems();

		foreach ($items as $singleItem) {

			// create item node
			$item = $dom->createElement('item');
			$itemNode = $channelNode->appendChild($item);

			// create title node
			$title = $dom->createElement('title', $this->escapeString($singleItem['title']));
			$itemNode->appendChild($title);

			// create author node
			if ($singleItem['author']) {
				$author = $dom->createElement('itunes:author', $this->escapeString($singleItem['author']));
				$itemNode->appendChild($author);
			}

			// create link node
			if (!$singleItem['link']) {
				$singleItem['link'] = $GLOBALS['TSFE']->id;
			}
			$link = $dom->createElement('link', $this->getAbsoluteUrl($singleItem['link'], true));
			$itemNode->appendChild($link);

			// create description node
			if ($singleItem['description'] && $this->conf['playlistlayout'] == 'all') {
				$description = $dom->createElement('description', $this->escapeString($singleItem['description']));
				$itemNode->appendChild($description);
			}

			// create enclosure node
			$enclosure = $dom->createElement('enclosure');

			if ($singleItem['file'] != '') {
				// set file path
				$file = $this->getFilePath($singleItem['file']);

			} else {
				$file = $this->getAbsoluteUrl($singleItem['file_url']);
			}

			// add file and type
			$enclosure->setAttribute('url', $file);
			$enclosure->setAttribute('type', $this->getFileType($file));

			// add enclosure node
			$itemNode->appendChild($enclosure);

			// create duration node
			if ($singleItem['duration']) {
				$duration = $dom->createElement('itunes:duration', $this->escapeString($singleItem['duration']));
				$itemNode->appendChild($duration);
			}

			// add item to channel
			$channelNode->appendChild($item);
		}

		return $dom;
	}

	/**
	 * return the content elemtn according to the submitted playerUdi
	 */
	function getContentElementFromPlayerUid() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_content', 'uid='.intval(t3lib_div::_GP('playerUid')));
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
        	return $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}
		return null;
	}

	/**
	 * returns the mime type according to the file extension
	 *
	 * @param 	$file 	path ot a local or external file
	 * @return 	string
	 */
	function getFileType($file) {
		// get file extension
		$filetype = substr($file, strrpos($file, '.') + 1);
		// set type
		switch ($filetype) {
			case 'swf':
				$type =  'application/x-shockwave-flash';
				break;
			case 'aac':
			case 'flv':
			case 'mp4':
				$type =  'video';
				break;
			case 'mp3':
				$type =  'sound';
				break;
			case 'jpg':
			case 'jpeg':
			case 'jpe':
			case 'png':
			case 'gif':
				$type =  'image';
				break;

			default:
				// no supported direct file-type found
				// youtube link
				if (ereg('youtube',$file) && ereg('watch\?',$file)) {
					$type = 'video/x-flv';
				}
				break;
		}
		return $type;
	}

	/**
	 * returns the playlist items as array with ids as key and records as value
	 * $this->fConf has to be initialized before
	 *
	 * @param 	boolean 	$count 	set to true if only the number of entries should be returned
	 * @return 	array
	 */
	function getPlaylistItems($count = false) {
		if ($files = $this->fConf['files']) {
			$items = array();
			$files = t3lib_div::trimExplode(',',$files,1);
			$count = 0;
			foreach($files as $fileId)	{
				list($table, $id) = t3lib_div::revExplode('_', $fileId, 2);
				// get records from table pages
				if ($table === 'pages')	{
					// count only
					if ($count) {
					// elio: sys_language_uid = -1 (alle)
						$countRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(uid) as count', $this->table, 'pid ='.$id.' AND (sys_language_uid=' . $GLOBALS['TSFE']->sys_language_uid . ' OR sys_language_uid=-1) '.$this->cObj->enableFields($this->table), '', 'sorting');
						if ($GLOBALS['TYPO3_DB']->sql_error()) {
							t3lib_div::debug($GLOBALS['TYPO3_DB']->sql_error());
						}
						$countEntries = $countEntries + $GLOBALS['TYPO3_DB']->sql_affected_rows();
						break;
					} else {
					// elio: sys_language_uid = -1 (alle)
						$pageItemsRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->table, 'pid ='.$id.' AND (sys_language_uid=' . $GLOBALS['TSFE']->sys_language_uid .' OR sys_language_uid=-1) '.$this->cObj->enableFields($this->table), '', 'sorting');
						if ($GLOBALS['TYPO3_DB']->sql_error()) {
							t3lib_div::debug($GLOBALS['TYPO3_DB']->sql_error());
						}
						// fetch records
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pageItemsRes)) {
							$items[] = $row;
						}
					}
				// get single media center item
				} elseif($table === 'tx_mediacenter_item') {
					// count only
					if ($count) {
						// elio: sys_language_uid = -1 (alle)
						$countRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(uid)', $this->table, 'uid ='.$id.' AND (sys_language_uid=' . $GLOBALS['TSFE']->sys_language_uid .' OR sys_language_uid=-1) '.$this->cObj->enableFields($this->table));
						if ($GLOBALS['TYPO3_DB']->sql_error()) {
							t3lib_div::debug($GLOBALS['TYPO3_DB']->sql_error());
						}
						$countEntries = $countEntries + $GLOBALS['TYPO3_DB']->sql_affected_rows();
						break;
					} else {

						// elio: sys_language_uid = -1 (alle)
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->table, 'uid ='.$id.' AND (sys_language_uid=' . $GLOBALS['TSFE']->sys_language_uid .' OR sys_language_uid=-1) '.$this->cObj->enableFields($this->table));
						if ($GLOBALS['TYPO3_DB']->sql_error()) {
							t3lib_div::debug($GLOBALS['TYPO3_DB']->sql_error());
						}
						$items[] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}
				}
			}

			// return count or items
			if ($count) {
				return array('count' => $countEntries);
			} else {
				return $items;
			}
		} else {
			return array();
		}
	}

	/**
	 * returns the number of items in the playlist
	 *
	 * @return int
	 */
	function countPlaylistItems() {
		return $this->getPlaylistItems(COUNT_PLAYLIST_ITEMS);
	}

	/**
	 * return the required flash version as string
	 *
	 * @return string
	 */
	function getRequiredFlashVersionAsString() {
		return $this->requiredFlashMajorVersion.".".$this->requiredFlashMinorVersion.".".$this->requiredFlashRevision;
	}

	/**
	 * return the content which will be rendered, if the client does not have flash
	 * installed or has a version which does not match the required version of the plugin
	 *
	 * TODO: implement override of the defaul no-flash-text via content element
	 *
	 * @return string
	 */
	function getNoFlashContent() {
		if ($this->conf['noflash']) {
			$recordsConf['tables'] = 'tt_content';
			$recordsConf['source'] = $this->conf['noflash'];
			return $this->cObj->RECORDS($recordsConf);
		} else {
			return sprintf($this->pi_getLL('noflash-text'), $this->getRequiredFlashVersionAsString());
		}
	}

	/**
	 * return the content which will be rendered within the <noscript> tags,
	 * if the client does not support javascript
	 *
	 * TODO: implement override of the default noscript-text via content element
	 *
	 * @return string
	 */
	function getNoScriptContent() {
		if ($this->conf['noscript']) {
			$recordsConf['tables'] = 'tt_content';
			$recordsConf['source'] = $this->conf['noscript'];
			return $this->cObj->RECORDS($recordsConf);
		} else {
			return $this->pi_getLL('noscript-text');
		}
	}

	/**
	 * calls tslib_content::typoLink_Url and return the passed path as external or internal url
	 *
	 * @param 	String 	$path 	Path to a local or an external file
	 * @param		Boolean	$urlencode if set the url will be encoded using urlencode()
	 * @return 	string
	 */
	function getAbsoluteUrl($path, $urlencode = false) {
		if ($path != null) {
			// create cObj if it does not exist
			if (!is_object($this->cObj)) {
				$this->cObj = t3lib_div::makeInstance('tslib_cObj');
			}
			$linkConf = array();
			$linkConf['parameter'] = $path;
			$file = trim(utf8_encode($this->cObj->typoLink_URL($linkConf)));

			// if url is internal add TYPO3_SITE_URL
			if (!stristr( $file, 'http:')) {
				$file = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$file;
			}
			if ($urlencode) {
				return urlencode($file);
			} else {
				return $file;
			}
		} else {
			return null;
		}
	}

	/**
	 * return the path to an uploaded file at uploads/tx_mediacenter
	 *
	 * @param 	string 	$file
	 * @return	string
	 */
	function getFilePath($file) {
		return utf8_encode(t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_mediacenter/'.$file);
	}

	/**
	 * escapes the submitted string and encodes it to utf8
	 *
	 * @param 	string 	$content 	string to encode and htmlspecialchar
	 */
	function escapeString($content) {
		global $TYPO3_CONF_VARS;
		return htmlspecialchars(strtolower($TYPO3_CONF_VARS['BE']['forceCharset']) != 'utf-8' ? utf8_encode($content) : $content);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/media_center/pi1/class.tx_mediacenter_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/media_center/pi1/class.tx_mediacenter_pi1.php']);
}

?>