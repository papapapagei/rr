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
 *  104: class tx_dam_media
 *
 *              SECTION: Initialization
 *  226:     function tx_dam_media ()
 *  241:     function __construct($file = null, $hash=false, $autoIndex=true)
 *  257:     function setMode($mode=TYPO3_MODE)
 *  268:     function setWantedVariant($conf='auto')
 *  298:     function fetchIndexFromFilename ($file, $hash=false, $autoIndex=true)
 *  320:     function fetchIndexFromMetaUID ($uid)
 *  342:     function fetchFileinfo ($fileInfo=NULL, $ignoreExistence=true)
 *
 *              SECTION: Meta data
 *  383:     function fetchFullMetaData ($uid=NULL)
 *  403:     function setMetaData ($meta)
 *
 *              SECTION: Get Meta data
 *  434:     function getID ()
 *  456:     function getTypeAll ()
 *  475:     function getType ()
 *  491:     function getMimeContentType ()
 *  511:     function getInfo ($field)
 *  525:     function getMeta ($field)
 *  542:     function getMetaInfo ($field)
 *  568:     function getContent ($field, $conf=array())
 *  655:     function getFormatedValue ($field, $format, $config='')
 *  671:     function getDownloadName ()
 *  682:     function getPathForSite ()
 *  694:     function getMetaArray ()
 *  708:     function getInfoArray ()
 *  722:     function getMetaInfoArray ()
 *
 *              SECTION: Misc ouput stuff
 *  743:     function getFieldLabel ($field, $removeColon=false, $hsc=true)
 *
 *              SECTION: Set Meta data
 *  769:     function setMeta ($field, $value)
 *
 *              SECTION: Update DB meta data
 *  791:     function updateIndex ()
 *  802:     function updateIndexFileInfo ()
 *  817:     function updateAuto ()
 *  827:     function updateHash ()
 *
 *              SECTION: Indexing
 *  847:     function index ()
 *  854:     function reindex ()
 *  861:     function autoIndex()
 *
 * TOTAL FUNCTIONS: 32
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam.php');
require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');


/**
 * DAM media object
 *
 * This is an object representing a file/media item.
 * This is the prefered method to access media items from the DAM.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_media {

	/**
	 * Holds the meta data record from DB
	 */
	var $meta = NULL;

	/**
	 * Holds the meta data which was changed/set/updated.
	 */
	var $metaUpdated = array();

	/**
	 * If the current meta data is the complete record or just an excerpt.
	 * @see fetchFullMetaData()
	 */
	var $isFullMetaData = NULL;

	/**
	 * The file info like ctime, mtime.
	 * Mainly the same format like in the meta array
	 */
	var $fileInfo = NULL;

	/**
	 * filename (basename)
	 */
	var $filename = NULL;

	/**
	 * filename with absolute path
	 */
	var $filepath = NULL;

	/**
	 * Path to file in normalized format which is relative if possible and is like the stored path in the meta data.
	 */
	var $pathNormalized = NULL;

	/**
	 * The absolute path to the file.
	 */
	var $pathAbsolute = NULL;



	/**
	 * TYPO3_MODE to be used. Has effect for database queries and the so called 'enableFields'.
	 */
	var $mode = TYPO3_MODE;

	/**
	 * Define which variant to be fetched
	 */
	var $variantConf = array();



	/**
	 * TRUE if the file exists and/or an index entry exists and accordingly to $this->mode and corresponding enableFields the database query found an entry.
	 */
	var $isAvailable = NULL;

	/**
	 * If the file is already indexed or not.
	 */
	var $isIndexed = NULL;



	/**
	 * If set the file info will be updated in the index automatically.
	 */
	var $doAutoFileInfoUpdate = true;

	/**
	 * If set the meta data will be updated automatically if needed.
	 */
	var $doAutoMetaUpdate = false;



// TODO what todo with non-existing files??


	/***************************************
	 *
	 *	 Initialization
	 *
	 ***************************************/


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_media () {
			// call php5 constructor
		$args = func_get_args();
		return call_user_func_array(array(&$this, '__construct'), $args);
	}


	/**
	 * Initialize the object by a given filename
	 *
	 * @param	string		$file Filepath to file. Should probably be absolute. If not set the object is undefined but can be initialized by UID or meta data record with initFrom* methods.
	 * @param	string		$hash If set the hash value can be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @param	boolean		$autoIndex If set (default) the file will be indexed automatically.
	 * @return	void
	 */
	function __construct($file = null, $hash=false, $autoIndex=true) {
		$this->setMode();
		$this->setWantedVariant();
		if($file) {
			$this->fetchIndexFromFilename ($file, $hash, $autoIndex);
			return $this->isAvailable;
		}
	}


	/**
	 * Set the internally used TYPO3_MODE, which is FE or BE. This has effect for database queries and the so called 'enableFields'.
	 *
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'.
	 * @return	void
	 */
	function setMode($mode=TYPO3_MODE) {
		$this->mode = $mode;
	}


	/**
	 * Set the wanted data variant which can be versions and languages
	 *
	 * @param	array		$conf Configuration array that defines the wanted variant
	 * @return	void
	 */
	function setWantedVariant($conf='auto') {
		
		if ($this->mode === 'FE' AND $conf === 'auto') {
			$this->variantConf = array();
			$this->variantConf['auto'] = true;
			// no need to set this, will be done automatically
			// $this->variantConf['sys_language_uid'] = $GLOBALS['TSFE']->sys_language_content;
			// $this->variantConf['lovl_mode'] = $GLOBALS['TSFE']->sys_language_contentOL;
			
		} elseif (is_array($conf)) {
			$this->variantConf = $conf;
		}
	}


	/**
	 * Fetch the wanted data variant which can be versions and languages
	 *
	 * @return	void
	 */
	function fetchVariant() {
		if (count($this->variantConf)) {
			$metaVariant = tx_dam::meta_getVariant ($this->getMetaArray(), $this->variantConf, $this->mode);
			$this->setMetaData ($metaVariant);
		}
	}
	
	
	/**
	 * Initialize the object by a given filename
	 *
	 * @param	string		$file Filepath to file. Should probably be absolute.
	 * @param	string		$hash If set the hash value can be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @param	boolean		$autoIndex If set (default) the file will be indexed automatically.
	 * @return	void
	 */
	function fetchIndexFromFilename ($file, $hash=false, $autoIndex=true) {

		$this->fetchFileInfo($file);
		if ($this->isAvailable) {
			if ($row = tx_dam::meta_getDataForFile($this->fileInfo, '*', true, $this->mode)) {
				$this->setMetaData ($row);
				$this->fetchVariant();
				$this->isFullMetaData = true;
				$this->isIndexed = true;
			} elseif ($autoIndex) {
// TODO search for hash
				$this->autoIndex();
			}
		}
	}


	/**
	 * Init the object by the UID of the meta data record
	 *
	 * @param	integer		$uid UID of the wanted meta data record.
	 * @return	void
	 */
	function fetchIndexFromMetaUID ($uid) {
		if ($row = tx_dam::meta_getDataByUid($uid, '*', $this->mode)) {
			$this->setMetaData ($row);
			$this->fetchVariant();
			$this->isFullMetaData = true;
			$this->isIndexed = true;
			$this->isAvailable = file_exists($this->filepath);
		} else {
			$this->isAvailable = false;
		}
	}



	/**
	 * Collects physical informations about the file.
	 * This means the file must be existent.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().  Default from getPathAbsolute().
	 * @param	boolean		$ignoreExistence The existence of the file will not be checked and only the file path will be splitted.
	 * @return	boolean		If the file exists and the info could be fetched.
	 * @see tx_dam::file_compileInfo()
	 */
	function fetchFileinfo ($fileInfo=NULL, $ignoreExistence=true) {
		$this->isAvailable = false;

		$info = false;
		if (is_array($fileInfo) AND $fileInfo['file_name'] AND $fileInfo['file_path_absolute']) {
			$info = $fileInfo;

		} else {
			$fileInfo = $fileInfo ? $fileInfo : $this->filepath;
			$info = tx_dam::file_compileInfo ($fileInfo, $ignoreExistence);
		}

		if (is_array($info)) {
			$this->fileInfo = $info;
			$this->filename = $this->fileInfo['file_name'];
			$this->pathNormalized = $this->fileInfo['file_path'];
			$this->pathAbsolute = $this->fileInfo['file_path_absolute'];
			$this->filepath = $this->pathAbsolute.$this->filename;
			$this->isAvailable = $this->fileInfo['__exists'];
			$this->update();
		}

		return $this->isAvailable;
	}




	/***************************************
	 *
	 *	 Meta data
	 *
	 ***************************************/


	/**
	 * Reads all data from the index.
	 * Only the limited amount of fields called "info fields" might be fetched from the index.
	 *
	 * @param	integer		$uid Optional UID of the wanted meta data record. Default: $this->meta['uid']
	 * @return	void
	 */
	function fetchFullMetaData ($uid=NULL) {
		if (!$this->isFullMetaData) {
			$uid = $uid ? $uid : $this->meta['uid'];
			if ($uid) {
				if ($row = tx_dam::meta_getDataByUid($this->meta['uid'], '*', $this->mode)) {
					$this->setMetaData ($row);
					$this->fetchVariant();
					$this->isFullMetaData = true;
				}
			}
		}
	}


	/**
	 * Init the object by a passed meta data record array.
	 * It is assumed that the data is really from the index and therefore the file "isIndexed".
	 *
	 * @param	array		$uid $meta Array of a meta data record
	 * @return	void
	 */
	function setMetaData ($meta) {
		if (is_array($meta)) {
			$this->meta = $meta;
			$this->isIndexed = is_array($meta);

			$this->filename = $this->meta['file_name'];
			$this->pathNormalized = $this->meta['file_path'];
			$this->pathAbsolute = tx_dam::path_makeAbsolute($this->meta['file_path']);
			$this->filepath = $this->pathAbsolute.$this->filename;
			if ($this->isAvailable==NULL) {
				$this->fetchFileInfo();
			}
		}
	}





	/***************************************
	 *
	 *	 Get Meta data
	 *
	 ***************************************/


	/**
	 * Returns the index ID
	 *
	 * @return	integer
	 */
	function getID () {
		$uid = false;

		if ($this->meta) {
			$uid = $this->meta['uid'];
		}

		return $uid;
	}


	/**
	 * Returns an array which describes the type of a file.
	 *
	 * example:
	 * $mimeType = array();
	 * $mimeType['file_mime_type'] = 'audio';
	 * $mimeType['file_mime_subtype'] = 'x-mpeg';
	 * $mimeType['file_type'] = 'mp3';
	 *
	 * @return	array		Describes the type of a file
	 */
	function getTypeAll () {
		$mimeType = false;

		if ($this->meta) {
			$mimeType = array();
			$mimeType['file_mime_type'] = $this->meta['file_mime_type'];
			$mimeType['file_mime_subtype'] = $this->meta['file_mime_subtype'];
			$mimeType['file_type'] = $this->meta['file_type'];
			
		} elseif ($this->fileInfo) {
			$mimeType = tx_dam::file_getType($this->fileInfo);
		}
		return $mimeType;
	}


	/**
	 * Returns just the file type like mp3, txt, pdf.
	 *
	 * @return	string		The file type like mp3, txt, pdf.
	 */
	function getType () {
		$type = false;

		if ($this->meta) {
			$type = $this->meta['file_type'];
		}

		return $type;
	}


	/**
	 * Returns a mime content type like: 'image/jpeg'
	 *
	 * @return	string
	 */
	function getMimeContentType () {
		$mimeContentType = '';

		if ($mimeType = $this->getTypeAll()) {
			if ($mimeType['file_mime_type'] AND $mimeType['file_mime_subtype']) {
				$mimeContentType = $mimeType['file_mime_type'].'/'.$mimeType['file_mime_subtype'];
			}
		}
		return $mimeContentType;
	}


	/**
	 * Returns raw file info data fetched directly from the file system
	 *
	 * @param	string		$field Field name to get from the file info array.
	 * @return	mixed		file info value.
	 * @see tx_dam::file_compileInfo()
	 */
	function getInfo ($field) {
		if ($this->fileInfo == NULL) {
			$media->fetchFileinfo();
		}
		return $this->fileInfo[$field];
	}


	/**
	 * Returns raw meta data from the database record.
	 *
	 * @param	string		$field Field name to get meta data from. These are database fields.
	 * @return	mixed		Meta data value.
	 */
	function getMeta ($field) {
		$value = false;
		if (isset($this->metaUpdated[$field])) {
			$value = $this->metaUpdated[$field];
		} elseif(is_array($this->meta)) {
			$value = $this->meta[$field];
		}
		return $value;
	}


	/**
	 * Returns raw meta data from the database record or from fileInfo.
	 *
	 * @param	string		$field Field name to get meta data from. These are database fields or entries from fileInfo.
	 * @return	mixed		Meta data value or entry from fileInfo.
	 */
	function getMetaInfo ($field) {
		$value = false;
		if (isset($this->metaUpdated[$field])) {
			$value = $this->metaUpdated[$field];
		} elseif (is_array($this->meta) AND isset($this->meta[$field])) {
			$value = $this->meta[$field];
		} else {
			$value = $this->getInfo($field);
		}
		return $value;
	}




	/**
	 * Returns meta data which might be processed.
	 * That means some fields are known and will be substituted by other fields values if the requested field is empty.
	 * Example if you request a caption but the field is empty you will get the description field value.
	 * This function will be improved by time and the processing will be configurable.
	 *
	 * @param	string		$field Field name to get meta data from. These are database fields.
	 * @param	array		$conf Additional configuration options for the field rendering (if supported for field)
	 * @return	mixed		Meta data value.
	 * @todo getContent(): more fields and user fields
	 */
	function getContent ($field, $conf=array())	{
		global $TYPO3_CONF_VARS;
		
		require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');

		$content = '';
		$hsc = true;

		switch ($field) {
			case 'file_size':
				if (!$conf['format']) $conf['format'] = 'filesize';
				$content = $this->getMetaInfo($field);
				break;

			case '__image_thumbnailImgTag':
				$content = tx_dam_image::previewImgTag($this->getMetaInfoArray(), $conf['size'], $conf['imgAttributes']);
				$hsc = false;
				break;

			case '__image_thumbnailImgUrl':
				$content = tx_dam_image::previewImgUrl($this->getMetaInfoArray(), $conf['size'], $conf['imgAttributes']);
				$hsc = false;
				break;

			case '__image_thumbnailImg':
				$content = tx_dam_image::preview($this->getMetaInfoArray(), $conf['size'], $conf['imgAttributes']);
					// This is an array - return directly
				return $content;
				break;

			case '__icon_fileTypeImgTag':
				$addAttrib = $conf['imgAttributes'];
				if ($conf['createTitleAttribute'] AND strpos($addAttrib, 'title=')===false) {
					$addAttrib .= tx_dam_guiFunc::icon_getTitleAttribute($this->getMetaInfoArray());
				}
				$content = tx_dam::icon_getFileTypeImgTag($this->getMetaInfoArray(), $conf['imgAttributes']);
				$hsc = false;
				break;

			case 'caption':
				$caption = $this->getMeta('caption');
				if (!$caption) $caption = $this->getMeta('description');
				$content = $caption;
				break;

			case 'alt_text':
				$alt_text = $this->getMeta('alt_text');
				if (!$alt_text) $alt_text = $this->getMeta('title');
				$content = $alt_text;
				break;

			case 'media_type':
				$content = tx_dam_guifunc::convert_mediaType($this->getMeta($field));
				break;

// TODO set substitution rules externally
// TODO allow user functions

			default:
				$content = $this->getMetaInfo($field);
				break;
		}

		if ($conf['format'] AND $content) {
			$content = tx_dam_guifunc::tools_formatValue($content, $conf['format'], $conf['formatConf']);
		}

		if ($conf['stdWrap.']) {
			$lcObj = t3lib_div::makeInstance('tslib_cObj');
			$lcObj->start($this->getMetaArray(), 'tx_dam');

			$content = $lcObj->stdWrap($content, $conf['stdWrap.']);
			$hsc = false;

		} else {
		}

		if (isset($conf['htmlSpecialChars']) AND !$conf['htmlSpecialChars']) {
			$hsc = false;
		}
		if ($hsc OR $conf['htmlSpecialChars']) {
			$content = htmlspecialchars($content);
		}

		return $content;
	}


	/**
	 * Returns meta data processed with format functions.
	 * Format content of various types if $format is set to date, filesize, ...
	 *
	 * @param	mixed		$field Field name to get meta data from. These are database fields.
	 * @param	array		$format Define format type like: date, datetime, truncate, ...
	 * @param	string		$config Additional configuration options for the format type
	 * @return	string		Formatted content
	 * @see tx_dam_guifunc::tools_formatValue()
	 */
	function getFormatedValue ($field, $format, $config='')	{
		require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');

		$value = tx_dam_guifunc::tools_formatValue($this->getMeta($field), $format, $config);

		return $value;
	}


	/**
	 * Returns the download name for the file.
	 * This don't have to be the real file name. For usage with "Content-Disposition" HTTP header.
	 * header("Content-Disposition: attachment; filename=$downloadFilename");
	 *
	 * @return	string		File name for download.
	 */
	function getDownloadName () {
		
		/*
		 * A secure download framework is in preparation which will be used here
		 */		
		
		$dlName = $this->getMeta('file_dl_name');
		return $dlName ? $dlName : $this->filename;
	}


	/**
	 * Returns a file path relative to PATH_site or getIndpEnv('TYPO3_SITE_URL').
	 *
	 * @return	string		Relative path to file
	 */
	function getPathForSite () {

		/*
		 * A secure download framework is in preparation which will be used here
		 */		
		
		$file_path = tx_dam::file_relativeSitePath ($this->getPathAbsolute());
		return $file_path;
	}


	/**
	 * Returns an absolute file path
	 *
	 * @return	string		Absolute path to file
	 */
	function getPathAbsolute () {

		return $this->filepath;
	}


	/**
	 * Returns a URL that can be used eg. for direct download.
	 * This is for files managed by the DAM only. Other files may fail.
	 *
	 * @param	array		$conf Additional configuration
	 * @return	string		URL to file
	 */
	function getURL ($conf=array()) {

		/*
		 * A secure download framework is in preparation which will be used here
		 */
		 

		if ($this->mode='FE') {
			$prefix = $GLOBALS['TSFE']->absRefPrefix;
		} else {
			$prefix = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		}

		$file_url = $prefix.$this->getPathForSite();

		return $file_url;
	}


	/**
	 * Returns raw meta data array of the database record.
	 * Updated data will be merged with original table data.
	 *
	 * @return	array		Meta data value.
	 */
	function getMetaArray () {
		if (is_array($this->meta)) {
			return array_merge($this->meta, $this->metaUpdated);
		}
		return $this->metaUpdated;
	}


	/**
	 * Returns raw file info data fetched directly from the file system
	 *
	 * @return	array		file info array.
	 * @see tx_dam::file_compileInfo()
	 */
	function getInfoArray () {
		if ($this->fileInfo == NULL) {
			$this->fetchFileinfo();
		}
		return $this->fileInfo;
	}


	/**
	 * Returns file info data and raw meta data array of the database record merged in one array
	 * Updated data will be merged with original table data.
	 *
	 * @return	array		Meta data value.
	 */
	function getMetaInfoArray () {
		return array_merge($this->getInfoArray(), $this->getMetaArray());
	}




	/***************************************
	 *
	 *	 Misc ouput stuff
	 *
	 ***************************************/


	/**
	 * Returns the TCA label for a field in the current language with the $LANG object
	 *
	 * @param string $field
	 * @param boolean $hsc
	 * @return string Field label
	 */
	function getFieldLabel ($field, $removeColon=false, $hsc=true) {
		global $LANG, $TCA;

		t3lib_div::loadTCA('tx_dam');
		$label = $LANG->sl($TCA['tx_dam']['columns'][$field]['label'], $hsc);
		$label = $removeColon ? preg_replace('#:$#', '', $label) : $label;
		return $label;
	}



	/***************************************
	 *
	 *	 Set Meta data
	 *
	 ***************************************/


	/**
	 * Raw meta data can be set for database storage.
	 * The data will be written when update() will be called.
	 *
	 * @param	string		$field Field name to get meta data from. These are database fields.
	 * @param	mixed		$value Meta data value.
	 * @return	void
	 */
	function setMeta ($field, $value) {
// TODO check read only
		$this->metaUpdated[$field] = $value;
	}






	/***************************************
	 *
	 *	 Update DB meta data
	 *
	 ***************************************/


	/**
	 * Updates the index when meta data was changed.
	 *
	 * @return	void
	 */
	function updateIndex () {
		if (count($this->metaUpdated)) {
			tx_dam::meta_putData ($this->getID, $this->metaUpdated);
// TODO  (respect variants)
		}
	}

	/**
	 * Updates the fileinfo is not in sync.
	 *
	 * @return	void
	 * @todo Check if fileinfo was changed?
	 */
	function updateFileInfo () {
		tx_dam::meta_putData ($this->getID, $this->fileInfo);
	}


	/**
	 * Update/Cleanup the index for the object.
	 * This can be following:
	 * - update index
	 * - auto index for new file
	 * - reconnect index with moved/renamed file
	 * - reconnect file with removed (auto deleted) index entry: recover
	 *
	 * @return	void
	 */
	function update () {
		if ($this->isIndexed) {
			if ($this->doAutoMetaUpdate) {
				$this->updateIndex ();
			}
			if ($this->doAutoFileInfoUpdate) {
				$this->updateFileInfo ();
			}
		}
	}


	/**
	 * Calculates a hash value from a file and updates the database.
	 * The hash is used to identify file changes.
	 *
	 * @return	void
	 */
	function updateHash () {
		if ($hash = tx_dam::file_calcHash($this->getPathAbsolute())) {
			$this->metaUpdated['file_hash'] = $hash;
		}
		$this->updateIndex();
	}




	/***************************************
	 *
	 *	 Indexing
	 *
	 ***************************************/


	/**
	 *
	 */
	function reindex () {
		if ($meta = tx_dam::index_autoProcess($this->getPathAbsolute(), true)) {
			$this->setMetaData ($meta);
		}
	}


	/**
	 *
	 */
	function autoIndex() {
		if ($meta = tx_dam::index_autoProcess($this->getPathAbsolute())) {
			$this->setMetaData ($meta);
		}
	}






}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_media.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_media.php']);
}
?>