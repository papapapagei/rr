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
 *  154: class tx_dam
 *
 *              SECTION: File related function
 *  173:     function file_isIndexed($fileInfo)
 *  194:     function file_compileInfo ($filename, $ignoreExistence=false)
 *  243:     function file_getType ($fileInfo)
 *  272:     function file_calcHash ($fileInfo)
 *  305:     function file_normalizePath ($filename)
 *  320:     function file_absolutePath ($fileInfo)
 *  341:     function file_relativeSitePath ($fileInfo)
 *  365:     function file_url ($fileInfo)
 *  381:     function file_makeCleanName ($filename, $crop=true)
 *
 *              SECTION: Path related function
 *  425:     function path_makeRelative ($path, $mountpath=NULL)
 *  444:     function path_makeAbsolute ($path, $mountpath=NULL)
 *  468:     function path_makeClean ($path)
 *  507:     function path_compileInfo ($path)
 *
 *              SECTION: Access check related function
 *  602:     function access_checkPath($path)
 *  624:     function access_checkFile ($fileInfo, $ignoreMissingFile=false)
 *  647:     function access_checkFileOperation ($action, $setup=NULL)
 *  703:     function access_isProtectedFile ($fileInfo)
 *
 *              SECTION: Meta data related function
 *  734:     function meta_getDataForFile($fileInfo, $fields='', $ignoreExistence=false, $mode=TYPO3_MODE)
 *  774:     function meta_findDataForUploadsFile($fileInfo, $uploadsPath='', $fields='', $mode=TYPO3_MODE)
 *  809:     function meta_getDataByUid ($uid, $fields='', $mode=TYPO3_MODE)
 *  837:     function meta_getDataByHash ($hash, $fields='', $mode=TYPO3_MODE)
 *  865:     function meta_findDataForFile($fileInfo, $hash='', $fields='', $mode=TYPO3_MODE)
 *  947:     function meta_getVariant ($row, $conf, $mode=TYPO3_MODE)
 * 1011:     function meta_updateStatus ($meta, $markMissingDeleted=true)
 *
 *              SECTION: Media objects functions
 * 1051:     function media_getForFile($fileInfo, $hash=false, $mode=TYPO3_MODE)
 * 1070:     function media_getByUid ($uid, $mode=TYPO3_MODE)
 * 1090:     function media_getByHash ($hash, $mode=TYPO3_MODE)
 * 1122:     function media_getForUploadsFile($file, $uploadPath='', $mode=TYPO3_MODE)
 *
 *              SECTION: Indexing functions
 * 1156:     function index_check ($fileInfo, $hash='')
 * 1202:     function index_reconnect($fileInfo, $hash='')
 * 1248:     function index_autoProcess($filename, $reindex=false)
 * 1307:     function index_process ($filename, $setup=NULL, $callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)
 *
 *              SECTION: process file or folder changes like rename
 * 1355:     function process_deleteFile($filename, $getFullErrorLogEntry=FALSE)
 * 1405:     function process_renameFile($oldPath, $newName, $additionalMeta='', $getFullErrorLogEntry=FALSE)
 * 1476:     function process_moveFile($oldFilePath, $newPath, $allowNewName=FALSE, $getFullErrorLogEntry=FALSE)
 * 1529:     function process_createFile($filename, $content='', $getFullErrorLogEntry=FALSE)
 * 1579:     function process_editFile($filename, $content='', $getFullErrorLogEntry=FALSE)
 * 1631:     function process_replaceFile($meta, $upload_data, $getFullErrorLogEntry=FALSE)
 * 1717:     function process_renameFolder($oldPath, $newName, $getFullErrorLogEntry=FALSE)
 * 1768:     function process_deleteFolder($path, $getFullErrorLogEntry=FALSE)
 * 1808:     function _callProcessPostTrigger()
 *
 *              SECTION: Notify the DAM about file or folder changes
 * 1844:     function notify_fileChanged ($filename)
 * 1862:     function notify_fileMoved ($src, $dest)
 * 1903:     function notify_fileDeleted ($filename, $recyclerPath='')
 *
 *              SECTION: Converter for names and codes of data formats
 * 1967:     function convert_mediaType($type)
 *
 *              SECTION: Icon functions
 * 1998:     function icon_getFileType ($mimeType, $absolutePath=false)
 * 2067:     function icon_getFolder($pathInfo, $absolutePath=false)
 * 2113:     function icon_getFileTypeImgTag($infoArr, $addAttrib='')
 *
 *              SECTION: Misc Tools
 * 2152:     function tools_findFileInPath($fileName, $path, $walkUp=true, $basePath='')
 *
 *              SECTION: Register functions like selection classes, indexing rules, viewer, editors
 * 2202:     function register_dbTrigger ($idName, $class, $position='')
 * 2219:     function register_fileTrigger ($idName, $class, $position='')
 * 2231:     function register_selection ($idName, $class, $position='')
 * 2244:     function register_indexingRule ($idName, $class, $position='')
 * 2258:     function register_fileType ($fileExtension, $mimeType, $mediaType='')
 * 2279:     function register_previewer ($idName, $class, $position='')
 * 2293:     function register_editor ($idName, $class, $position='')
 * 2307:     function register_action ($idName, $class, $position='')
 * 2318:     function register_fileIconPath ($path)
 * 2336:     function register_mediaTable ($table, $position='')
 * 2347:     function register_getEntries($type)
 *
 *              SECTION: Configuration
 * 2386:     function config_getValue($configPath='', $getProperties=false)
 * 2418:     function config_setValue($configPath='', $value='')
 * 2460:     function config_init($force=false)
 *
 *              SECTION: Internal
 * 2509:     function _getTSconfig ($pid=0)
 * 2570:     function _getTSConfigObject($objectString, $config)
 * 2598:     function _addItem($idName, $value, &$items, $position='')
 *
 * TOTAL FUNCTIONS: 66
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/tx_dam_types.php');
require_once(PATH_txdam.'lib/class.tx_dam_db.php');
require_once (PATH_txdam.'lib/class.tx_dam_allowdeny.php');


/**
 * DAM API functions
 *
 * This is the official API to access DAM functions.
 * If possible no other functions shall be used.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam {



	/***************************************
	 *
	 *	 File related function
	 *
	 ***************************************/


	/**
	 * Like basename() this returns the filename without path.
	 * This works NOT with a path to get a folder name.
	 * (needed because pathinfo is broken on some php5.x versions using utf-8 chars)
	 *
	 * @param	string		$filename The file name with path
	 * @return	string		A file name
	 */
	function file_basename ($filename) {
		$path_parts = t3lib_div::split_fileref($filename);
		return $path_parts['file'];
	}
	
	
	/**
	 * Like dirname() this returns the path but the path has a trailing slash.
	 * This works NOT with a path without trailing slash.
	 * (needed because pathinfo is broken on some php5.x versions using utf-8 chars)
	 *
	 * @param	string		$filename The file name with path
	 * @return	string		A file path with trailing slash
	 */
	function file_dirname ($filename) {
		$path_parts = t3lib_div::split_fileref($filename);
		return $path_parts['path'];
	}

	
	/**
	 * Checks if the file is already indexed.
	 * Returns the UID of the meta data record if the file is indexed already or false if the file is not indexed yet.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	mixed		UID of the meta data record or false.
	 * @see tx_dam::file_compileInfo()
	 */
	function file_isIndexed($fileInfo) {
		$uid = false;

		if(is_array($row = tx_dam::meta_getDataForFile ($fileInfo, 'uid'))) {
			$uid = $row['uid'];
		}

		return $uid;
	}


	/**
	 * Collects and returns an array with physical informations about the file.
	 * This means the file must be existent.
	 * If $ignoreExistence is set the path will be split no matter if the file exists.
	 * File node info will be compiled only if the file exists - of course.
	 *
	 * @param	string		$filename The file name with path
	 * @param	boolean		$ignoreExistence The existence of the file will not be checked and only the file path will be splitted.
	 * @return	array		A file info array with all physical data about the file
	 */
	function file_compileInfo ($filename, $ignoreExistence=false) {
		$fileInfo = false;

		$filename = tx_dam::file_absolutePath ($filename);

		if ($filename AND ($ignoreExistence OR @is_file($filename)) ) {
			$fileInfo = array();
			$fileInfo['__type'] = 'file';
			$fileInfo['__exists'] = @is_file($filename);
			
			// don't handle utf8: $path_parts = pathinfo($fileInfo);
			$path_parts = t3lib_div::split_fileref($filename);
			$fileInfo['file_name'] = $path_parts['file'];
			$fileInfo['file_path_absolute'] = $path_parts['path'];
			$fileInfo['file_extension'] = $path_parts['realFileext'];
			$fileInfo['file_title'] = $fileInfo['file_name'];
			$fileInfo['file_path'] = tx_dam::path_makeRelative ($fileInfo['file_path_absolute']);
			$fileInfo['file_path_relative'] = $fileInfo['file_path'];

			$pathInfo = tx_dam::path_compileInfo ($fileInfo['file_path_absolute']);
			if (isset($pathInfo['dir_accessable'])) {
				$fileInfo['file_accessable'] = $pathInfo['dir_accessable'];
			}

			if ($fileInfo['__exists'] OR $ignoreExistence) {
				$fileInfo['file_mtime'] = @filemtime($filename);
				$fileInfo['file_ctime'] = @filectime($filename);
				$fileInfo['file_inode'] = @fileinode($filename);
				$fileInfo['file_size'] = @filesize($filename);
				$fileInfo['file_owner'] = @fileowner($filename);
				$fileInfo['file_perms'] = @fileperms($filename);
				$fileInfo['file_writable'] = @is_writable($filename);
				$fileInfo['file_readable'] = @is_readable($filename);
			}
		}
		return $fileInfo;
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
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	array		Describes the type of a file
	 */
	function file_getType ($fileInfo) {
		global $TYPO3_CONF_VARS;

		$mimeType = array();

		if (is_array($fileInfo) AND $fileInfo['file_mime_type']) {
			$mimeType = array();
			$mimeType['file_mime_type'] = $fileInfo['file_mime_type'];
			$mimeType['file_mime_subtype'] = $fileInfo['file_mime_subtype'];
			$mimeType['file_type'] = $fileInfo['file_type'];
		} elseif($uid = tx_dam::file_isIndexed($fileInfo)) {
			$mimeType = tx_dam::meta_getDataByUid($uid, 'file_mime_type,file_mime_subtype,file_type,media_type');
		} else {
			require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');
			$mimeType = tx_dam_indexing::getFileMimeType($fileInfo);
		}

		return $mimeType;
	}


	/**
	 * Returns the plain file path by a given uid.
	 *
	 * @param	integer		$uid
	 * @return	string		file path
	 */
	function file_getPathByUid ($uid) {
		$path = false;
		if ($meta = tx_dam::meta_getDataByUid ($uid, 'file_name,file_path')) {
			$path = tx_dam::file_absolutePath($meta);
		}
		return $path;
	}
	
	
	/**
	 * Calculates a hash value from a file.
	 * The hash is used to identify file changes or a file itself.
	 * Remember that a file can occur multiple times in the file system, therefore you can detect only that it is the same file. But you have to take the location (path) into account to identify the right file.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	string		hash value
	 */
	function file_calcHash ($fileInfo) {
		$hash = false;

		$filename = tx_dam::file_absolutePath($fileInfo);
		if (function_exists('md5_file')) {
			$hash = @md5_file($filename);
		} else {
			if(filesize ($filename) > 0xfffff ) {	// 1MB
				$cmd = t3lib_exec::getCommand('md5sum');
				$output = array();
				$retval = 0;
				exec($cmd.' -b '.escapeshellcmd($filename), $output, $retval);
				$output = explode(' ',$output[0]);
				$match = array();
				if (preg_match('#[0-9a-f]{32}#', $output[0], $match)) {
					$hash = $match[0];
				}
			} else {
				$file_string = t3lib_div::getUrl($filename);
				$hash = md5($file_string);
			}
		}

		return $hash;
	}


	/**
	 * Convert a file path to the format stored in the meta data which is a relative path if possible.
	 *
	 * @param	string		$filename The file name with path
	 * @return	string		Normalized path to file
	 */
	function file_normalizePath ($filename) {
		$path_parts = t3lib_div::split_fileref($filename);
		$file_name = $path_parts['file'];
		$file_path = tx_dam::path_makeRelative($path_parts['path']);

		return $file_path.$file_name;
	}


	/**
	 * Convert/returns a file path to a absolute path if possible.
	 * This is for files managed by the DAM only. Other files may fail.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	string		Absolute path to file
	 */
	function file_absolutePath ($fileInfo) {
		if (is_array($fileInfo)) {
			$file_name = $fileInfo['file_name'];
			$file_path = $fileInfo['file_path_absolute'] ? $fileInfo['file_path_absolute'] : tx_dam::path_makeAbsolute ($fileInfo['file_path']);
		} elseif ($fileInfo) {
			$path_parts = t3lib_div::split_fileref($fileInfo);
			$file_name = $path_parts['file'];
			$file_path = tx_dam::path_makeAbsolute($path_parts['path']);
		}

		return $file_path.$file_name;
	}


	/**
	 * Convert a file path to a relative path to PATH_site or getIndpEnv('TYPO3_SITE_URL').
	 * This is for files managed by the DAM only. Other files may fail.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	string		Relative path to file
	 */
	function file_relativeSitePath ($fileInfo) {

		if (is_array($fileInfo)) {
			$file_name = $fileInfo['file_name'];
			$file_path = $fileInfo['file_path_absolute'] ? $fileInfo['file_path_absolute'] : tx_dam::path_makeAbsolute ($fileInfo['file_path']);
		} else {
			$path_parts = t3lib_div::split_fileref($fileInfo);
			$file_name = $path_parts['file'];
			$file_path = tx_dam::path_makeAbsolute($path_parts['path']);
		}

			// for now path_makeRelative() do what we want but that may change
		$file_path = tx_dam::path_makeRelative ($file_path, PATH_site);

		return $file_path.$file_name;
	}


	/**
	 * Convert a file path to a URL that can be used eg. for direct download.
	 * This is for files managed by the DAM only. Other files may fail.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	array		$conf Additional configuration
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	string		URL to file
	 */
	function file_url ($fileInfo, $conf=array(), $mode=TYPO3_MODE) {

		/*
		 * A secure download framework is in preparation which will be used here
		 */

		$file_url = t3lib_div::getIndpEnv('TYPO3_SITE_URL').tx_dam::file_relativeSitePath ($fileInfo);

		return $file_url;
	}


	/**
	 * Returns a string where any invalid character of a filename is substituted by '_'. By the way this can be used to clean folder names as well.
	 * This function don't do any charset conversion for good reasons. Most file systems don't have charset support. TYPO3 may use a different charset than the system locale setting. So the safest ist to set $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'], which is then the charset for filenames automatically.
	 *
	 * @param	string		$filename filename (without path)
	 * @param	string		$crop If true the name will be shortened if needed
	 * @return	string		Output string with any invalid characters is substituted by '_'
	 */
	function file_makeCleanName ($filename, $crop=false)	{
	
		$filename = trim($filename);

		if (TYPO3_OS === 'WIN') {
			$filename = str_replace('[', '(', $filename);
			$filename = str_replace(']', ')', $filename);
			$filename = str_replace('+', '_', $filename);
		}
		
			// chars like ?"*:<> are allowed on some filesystems but will be removed to secure shell commands
		$filename = preg_replace('#[/|\\?"*:<>]#', '_', trim($filename));
		if ($filename === '.' OR $filename === '..') {
			$filename .= '_';
		}

			// handle UTF-8 characters
		if ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] == 'utf-8' && $GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem'])	{
				// allow ".", "-", 0-9, a-z, A-Z and everything beyond U+C0 (latin capital letter a with grave)
			$filename = preg_replace('/[\x00-\x2C\/\x3A-\x3F\x5B-\x60\x7B-\xBF]/u','_', $filename);
		}
		
		$maxFileNameLength = $GLOBALS['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'] ? $GLOBALS['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'] : 60;
		if ($crop AND strlen($filename) > $maxFileNameLength) {
			$path_parts = t3lib_div::split_fileref($filename);
			if ($extLen = strlen($path_parts['realFileext'])) {
				$extLen += 1;
				$filename = substr ($path_parts['filebody'], 0, $maxFileNameLength-$extLen).'.'.$path_parts['realFileext'];
			} else {
				$filename = substr ($path_parts['filebody'], 0, $maxFileNameLength);
			}
		}

		return $filename;
	}



	/***************************************
	 *
	 *	 Path related function
	 *
	 ***************************************/


	/**
	 * Returns the last part from a path.
	 * If the last part is a filename the filename will be returned.
	 * (needed because pathinfo is broken on some php5.x versions using utf-8 chars)
	 * Examples:
	 * example/folder/ -> folder
	 * example/folder -> folder
	 * example/folder/filename -> filename
	 *
	 * @param	string		$path 
	 * @return	string		The name of the folder
	 */
	function path_basename ($path) {		
		preg_match ('#([^/]+)/{0,1}$#', $path, $match);
		return $match[1];
	}


	/**
	 * Convert a path to a relative path if possible.
	 * The result is normally a relative path to PATH_site (but don't have to).
	 * It might be possible that back paths '../' will be supported in the future.
	 *
	 * @param	string		$path Path to convert
	 * @param	string		$mountpath Path which will be used as base path. Otherwise PATH_site is used.
	 * @return	string		Relative path
	 */
	function path_makeRelative ($path, $mountpath=NULL) {

		$path = tx_dam::path_makeAbsolute ($path, $mountpath);

		$mountpath = is_null($mountpath) ? PATH_site : tx_dam::path_makeClean ($mountpath);

			// remove the site path from the beginning to make the path relative
			// all other's stay absolute
		return preg_replace('#^'.preg_quote($mountpath).'#','',$path);
	}


	/**
	 * Convert a path to an absolute path
	 *
	 * @param	string		$path Path to convert
	 * @param	string		$mountpath Path which will be used as base path. Otherwise PATH_site is used.
	 * @return	string		Absolute path
	 */
	function path_makeAbsolute ($path, $mountpath=NULL) {

		if ($path) {
			if (is_array($path)) {
				if (isset($path['dir_name'])) {
					$path = $path['dir_path_absolute'] ? $path['dir_path_absolute'] : $path['dir_path'];
				} else {
					$path = $path['file_path_absolute'] ? $path['file_path_absolute'] : $path['file_path'];
				}
			}
	
			$path = tx_dam::path_makeClean ($path);
	
			if(t3lib_div::isAbsPath($path)) {
				return $path;
			}
			$mountpath = is_null($mountpath) ? PATH_site : tx_dam::path_makeClean ($mountpath);
			$path = $mountpath ? $mountpath.$path : '';
		}
		return $path;
	}


	/**
	 * Cleans a path
	 * - resolve back paths '../'
	 * - append '/' to the path if missing
	 *
	 * @param	string		$path Path to clean
	 * @return	string		Cleaned path
	 */
	function path_makeClean ($path) {
		if ($path) {
			$path = str_replace('/./', '/', $path);
			$path = t3lib_div::resolveBackPath($path);
			$path = preg_replace('#[\/\. ]*$#', '', $path).'/';
			$path = str_replace('//', '/', $path);
		}
		return $path;
	}



	/**
	 * Collects and returns an array with info's about the given path/folder.
	 * Returns false if the path is not a folder.
	 *
	 * Example:
	 * __type => dir
	 * dir_path => /var/www/dam/fileadmin//test/
	 * dir_path_from_mount => test/
	 * dir_path_relative => fileadmin/test/
	 * dir_name => test
	 * dir_title => test
	 * dir_size => 115
	 * dir_tstamp => 1132751825
	 * dir_writable => 1
	 * dir_readable => 1
	 * dir_owner => 1000
	 * dir_perms => 16895
	 * mount_id => 875349e03c95ae6bc79dc22c0b7c2f7c
	 * mount_name => fileadmin/
	 * mount_path => /var/www/dam/fileadmin/
	 * mount_type =>
	 * web_nonweb => web
	 *
	 * @param	string		$path Path to a folder (not file)
	 * @return	array		Info array
	 * @todo localization of TEMP, RECYCLER
	 */
	function path_compileInfo ($path) {
		global $FILEMOUNTS, $TYPO3_CONF_VARS;

			// cache entries - static would work too but couldn't be flushed then
		if (isset($GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'][$path])) {
			return $GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'][$path];
		}


		$pathInfo = false;

		require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');

		$basicFF = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$basicFF->init($FILEMOUNTS, $TYPO3_CONF_VARS['BE']['fileExtensions']);

		$path = tx_dam::path_makeAbsolute($path);

		$path = $basicFF->is_directory($path);
		$path = $path ? $path.'/' : '';

		if($path) {

			$pathInfo = array();
			$pathInfo['__type'] = 'dir';
			$pathInfo['__exists'] = @is_dir($path);
			$pathInfo['__protected'] = @is_file($path.'.htaccess');
			$pathInfo['__protected_type'] = $pathInfo['__protected'] ? 'htaccess' : '';
			$pathInfo['dir_ctime'] = @filectime($path);
			$pathInfo['dir_mtime'] = @filemtime($path);
			$pathInfo['dir_size'] = @filesize($path);
			$pathInfo['dir_type'] = @filetype($path);
			$pathInfo['dir_owner'] = @fileowner($path);
			$pathInfo['dir_perms'] = @fileperms($path);
				// I have no idea why these are negated in t3lib_basicfilefunc
			$pathInfo['dir_writable'] = @is_writable($path);
			$pathInfo['dir_readable'] = @is_readable($path);

				// find mount
			$pathInfo['mount_id'] = $basicFF->checkPathAgainstMounts($path);
			$pathInfo['mount_path'] =  $FILEMOUNTS[$pathInfo['mount_id']]['path'];
			$pathInfo['mount_name'] =  $FILEMOUNTS[$pathInfo['mount_id']]['name'];
			$pathInfo['mount_type'] =  $FILEMOUNTS[$pathInfo['mount_id']]['type'];
			$pathInfo['dir_readonly'] = ($pathInfo['mount_type'] == 'readonly');
			// $pathInfo['web_nonweb'] = t3lib_BEfunc::getPathType_web_nonweb($path); // prevent using t3lib_BEfunc
			$pathInfo['web_nonweb'] = t3lib_div::isFirstPartOfStr($path, t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT')) ? 'web' : '';
			$pathInfo['web_sys'] = $pathInfo['web_nonweb'] ? 'web' : 'sys';

			if (TYPO3_MODE === 'BE') {
				$pathInfo['dir_accessable'] = $pathInfo['mount_id'] ? true : false;
			}

				// extra path info
			$pathInfo['dir_name'] = basename($path);
			$pathInfo['dir_title'] = $pathInfo['dir_name'];
			$pathInfo['dir_path_absolute'] = $path;
			$pathInfo['dir_path_relative'] = tx_dam::path_makeRelative($path);
			$pathInfo['dir_path'] = $pathInfo['dir_path_relative'];
			$pathInfo['dir_path_normalized'] = $pathInfo['dir_path_relative'];
			$pathInfo['dir_path_from_mount'] = tx_dam::path_makeRelative($path, $pathInfo['mount_path']);

			// ksort($pathInfo);

			if ($pathInfo['dir_name'] === '_temp_')	{
				$pathInfo['dir_title'] = 'TEMP';
			}
			if ($pathInfo['dir_name'] === '_recycler_')	{
				$pathInfo['dir_title'] = 'RECYCLER';
			}

		}
		$GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'][$path] = $pathInfo;

		return $pathInfo;
	}





	/***************************************
	 *
	 *	 Access check related function
	 *
	 ***************************************/


	/**
	 * Check if a path is accessable.
	 * This includes if the path exist and if the user has access to it.
	 * For further information, like readable, see path_compileInfo()
	 *
	 * Currently for BE usage only
	 *
	 * @param string $path The path
	 * @return boolean True if path exist and user has access otherwise false.
	 * @see tx_dam::path_compileInfo()
	 */
	function access_checkPath($path) {
		if ($pathInfo = tx_dam::path_compileInfo ($path)) {
			if ($pathInfo['dir_accessable'])  {
				return true;
			}
		}
		return false;
	}


	/**
	 * Check if a file is accessable.
	 * This includes if the file exist and if the user has access to it.
	 * For further information, like readable, see file_compileInfo()
	 *
	 * Currently for BE usage only
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param 	boolean 	$ignoreMissingFile If true it is ignored if a file exists.
	 * @return 	boolean 	True if file exist and user has access otherwise false.
	 * @see tx_dam::path_compileInfo()
	 */
	function access_checkFile ($fileInfo, $ignoreMissingFile=false) {

		if (!is_array($fileInfo) OR !isset($fileInfo['file_accessable'])) {
			$fileInfo = tx_dam::file_compileInfo($fileInfo, $ignoreMissingFile);
		}

		if ($fileInfo['__exists'] OR $ignoreMissingFile)	{
			return (boolean)$fileInfo['file_accessable'];
		}
		return false;
	}


	/**
	 * Check if a user is allowed to process a file action like rename or delete.
	 *
	 * Currently for BE usage only
	 *
	 * @param 	string 		$action Action name: deleteFile, moveFolder, ... .If empty the whole permission array will be returned.
	 * @param	integer		$setup File permission integer from BE_USER object.
	 * @return	mixed		Returns an array with action permissions if $action is empty. Otherwise true or false will be returned depending if the action is allowed.
	 * @see t3lib_extFileFunctions::init_actionPerms()
	 * @todo implement for FE
	 */
	function access_checkFileOperation ($action='', $setup=NULL) {

		if (!is_object($GLOBALS['BE_USER'])) {
			return false;
		}

		if ($action AND $GLOBALS['BE_USER']->isAdmin()) {
			return true;
		}


		$setup = $setup ? $setup : self::getFileoperationPermissions();

		if (($setup&1)==1)	{		// Files: Upload,Copy,Move,Delete,Rename
			$actionPerms['uploadFile'] = true;
			$actionPerms['copyFile'] = true;
			$actionPerms['moveFile'] = true;
			$actionPerms['deleteFile'] = true;
			$actionPerms['renameFile'] = true;
			$actionPerms['editFile'] = true;
			$actionPerms['newFile'] = true;
		}
		if (($setup&2)==2)	{		// Files: Unzip
			$actionPerms['unzipFile'] = true;
		}
		if (($setup&4)==4)	{		// Directory: Move,Delete,Rename,New
			$actionPerms['moveFolder'] = true;
			$actionPerms['deleteFolder'] = true;
			$actionPerms['renameFolder'] = true;
			$actionPerms['newFolder'] = true;
		}
		if (($setup&8)==8)	{		// Directory: Copy
			$actionPerms['copyFolder'] = true;
		}
		if (($setup&16)==16)	{		// Directory: Delete recursively (rm -Rf)
			$actionPerms['deleteFolderRecursively'] = true;
		}

		if ($action) {
			return $actionPerms[$action];
		} else {
			return $actionPerms;
		}
	}


	/**
	 * Check if a file is protected (eg. by .htaccess) and need a special download handling.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return 	mixed 	protection type string otherwise false.
	 */
	function access_isProtectedFile ($fileInfo) {

		$path = tx_dam::path_makeAbsolute($path);

		$htaccessPath = tx_dam::tools_findFileInPath('.htaccess', $path);

		/*
		 * A secure download framework is in preparation which will be used here
		 */

		return false;
	}

	/**
	 * Fetches the permissions on file operations of the current user.
	 * The behaviour in TYPO3 4.2 and 4.3 are different, thus this method is used as wrapper.
	 *
	 * @param	t3lib_extFileFunctions	$fileProcessor: The file processor object
	 * @return	integer					File permission integer from BE_USER
	 */
	static public function getFileoperationPermissions() {
		if (method_exists($GLOBALS['BE_USER'], 'getFileoperationPermissions')) {
			$result = $GLOBALS['BE_USER']->getFileoperationPermissions();
		} else {
			$result = $GLOBALS['BE_USER']->user['fileoper_perms'];
		}

		return $result;
	}





	/***************************************
	 *
	 *	 Meta data related function
	 *
	 ***************************************/


	/**
	 * Fetches the meta data from the index by a given file path or file info array.
	 * The field list to be fetched can be passed.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @param	boolean		$ignoreExistence The existence of the file will not be checked if filename is passed.
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	array
	 */
	function meta_getDataForFile($fileInfo, $fields='', $ignoreExistence=false, $mode=TYPO3_MODE) {
		$meta = false;

		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo ($fileInfo, $ignoreExistence);
		}

		if (is_array($fileInfo)) {
			$fields = $fields ? $fields : tx_dam_db::getMetaInfoFieldList();

			$where = array();
			$where['file_name'] = 'file_name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_name'],'tx_dam');
			$where['file_path'] = 'file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_path'],'tx_dam');
			$where['enableFields'] = tx_dam_db::enableFields('tx_dam', '', $mode);

			if ($rows = tx_dam_db::getDataWhere($fields, $where, '', '', '1')) {
				reset ($rows);
				$meta = current($rows);
			}
		}

		return $meta;
	}


	/**
	 * Fetches the meta data from the index by a given file path or file info array.
	 * This function tries to find an index entry for a file from uploads/. Files in uploads/ are copies from files from fileadmin/ or direct uploads.
	 * The meta data that might be found is not directly meant for the uploads-file but normally it matches the file and you get what you expect.
	 * But for example the file might be placed in fileadmin/ twice with different meta data. Then you can't say which meta data you will get for the uploads-file.
	 *
	 * IMPORTANT
	 * The meta data does NOT include data of the uploads file itself but a matching file which is placed in fileadmin/!
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param 	string 		$uploadsPath Uploads path. will be prepended if $fileInfo is a path (string).
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	array
	 */
	function meta_findDataForUploadsFile($fileInfo, $uploadsPath='', $fields='', $mode=TYPO3_MODE) {
		$meta = false;

		if (!is_array($fileInfo)) {
			$uploadsPath = tx_dam::path_makeClean($uploadsPath);
			$fileInfo = tx_dam::file_compileInfo ($uploadsPath.$fileInfo);
		}

		if (is_array($fileInfo) AND $fileInfo['__exists']) {

			$fields = $fields ? $fields : tx_dam_db::getMetaInfoFieldList();

			$where = array();
			$where['enableFields'] = tx_dam_db::enableFields('tx_dam', '', $mode);

			$fileList = tx_dam::file_relativeSitePath($fileInfo);
			if ($rows = tx_dam_db::getMetaForUploads ($fileList, '', $fields)) {
				reset ($rows);
				$meta = current($rows);
			}
		}

		return $meta;
	}


	/**
	 * Fetches the meta data from the index by a given UID.
	 * The field list to be fetched can be passed.
	 *
	 * @param	integer		$uid UID of the meta data record
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	array		Meta data array or false
	 */
	function meta_getDataByUid ($uid, $fields='', $mode=TYPO3_MODE) {
		$row = false;

		if($uid = intval($uid)) {
			$where = array();
			$where['file_name'] = 'tx_dam.uid='.$uid;
			$where['enableFields'] = tx_dam_db::enableFields('tx_dam', '', $mode);
			if ($rows = tx_dam_db::getDataWhere($fields, $where, '', '', '1')) {
				reset ($rows);
				$row = current($rows);
			}
		}

		return $row;
	}


	/**
	 * Fetches the meta data from the index by a given file hash.
	 * The field list to be fetched can be passed.
	 * This function returns an array of meta data arrays because it's possible to match more than one index entry!
	 * To get meta data for a file use meta_getDataForFile() instead.
	 *
	 * @param	string		$hash Hash value for the file
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	array		Array of Meta data arrays or false.
	 */
	function meta_getDataByHash ($hash, $fields='', $mode=TYPO3_MODE) {
		$rows = false;

		if($hash) {
			$where = array();
			$where['file_hash'] = 'file_hash='.$GLOBALS['TYPO3_DB']->fullQuoteStr($hash,'tx_dam');
			$where['enableFields'] = tx_dam_db::enableFields('tx_dam', '', $mode);
			$rows = tx_dam_db::getDataWhere($fields, $where);
		}

		return $rows;
	}


	/**
	 * Fetches the meta data from the index by a given file path or file info array and a hash value.
	 * This can be used to find the meta data for a "lost" file so the file and meta data can be reconnected.
	 * Index entries which have a current valid file but have the same hash value will be removed from the result.
	 * It will be searched in deleted records too to find a related index entry.
	 * This function returns an array of meta data arrays because it's possible to match more than one index entry!
	 * The returned records may be related to an existing file! In that case changing the index entry will create a lost file again!
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$hash The hash value will be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	array		Array of Meta data arrays or false.
	 */
	function meta_findDataForFile($fileInfo, $hash='', $fields='', $mode=TYPO3_MODE) {
		$rows = false;

		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo ($fileInfo, true);
		}

		if (is_array($fileInfo)) {

			if (!$hash AND @is_file(tx_dam::file_absolutePath($fileInfo))) {
				$hash = tx_dam::file_calcHash ($fileInfo);
			}

			$fields = $fields ? $fields : tx_dam_db::getMetaInfoFieldList();

			$where = array();
			$where['enableFields'] = '';
			$where['file_name'] = 'file_name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_name'],'tx_dam');
			$where['file_path'] = 'file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_path'],'tx_dam');
			if ($hash) {
				$where['hash'] = 'file_hash='.$GLOBALS['TYPO3_DB']->fullQuoteStr($hash,'tx_dam');
			}

				// max 3 tries to find the record
				// 1: hash, name and path
				// 2: hash, name
				// 3: by hash only
			for ($index = 0; $index < 3; $index++) {

				$rowsResult = tx_dam_db::getDataWhere($fields, $where, '', '', '1');
				if($rowsResult) {

					$rows = array();
					foreach ($rowsResult as $row) {
							// the file itself - we're done
						if($row['file_name']==$fileInfo['file_name'] AND $row['file_path']==$fileInfo['file_path']) {
							$rows[$row['uid']] = $row;
							break;
						}
							// a lost file
						if(!(@is_file(tx_dam::file_absolutePath($row)))) {
							$rows[$row['uid']] = $row;
							continue;
						}
					}
					break;

				} else {
					if (!$where['hash']) {
						// just leave the for-loop if there's no hash
						break;
					}
					switch ($index) {
						case 0:
							// search for filename AND hash
							unset($where['file_path']);
							break;
						case 1:
							// search for hash only
							unset($where['file_name']);
							break;

						default:
							break 2;
					}
				}
			}
		}

		return $rows;
	}


	/**
	 * Fetches the meta data variant from the index by a given meta data record array. The result will have the same fields selected as the given record.
	 * For now different languages can be fetched.
	 * Later this function may support versions too.
	 * 
	 * $conf = array(
	 * 		'sys_language_uid' // sys_language uid of the wanted language
	 * 		'lovl_mode' // Overlay mode. If "hideNonTranslated" then records without translation will not be returned un-translated but false
	 * )
	 * 
	 * In FE mode sys_language_uid and lovl_mode will be get from TSFE automatically
	 * 
	 * The return record has still the same uid.
	 * sys_language_uid is set from the overlay record
	 * Additionally following keys are set:
	 * _BASE_REC_UID - the uid from the base record which is the non-overlaid record
	 * _LOCALIZED_UID - the uid from the overlay record
	 * 
	 * IMPORTANT
	 * Localized records itself does NOT contain valid data except the translated content. That means the field 'file_name' for example may contain data but could be invalid.
	 * Valid meta data is in the record with sys_language_uid=0 only!
	 *
	 * @param	array		$row Meta data record array. Must containt uid, pid and sys_language_uid
	 * @param	integer		$conf Configuration array that defines the wanted overlay
	 * @param	boolean		$mode TYPO3_MODE (FE, BE)
	 * @return	array		Meta data array or false
	 * @see tx_dam_db::getRecordOverlay()
	 */
	function meta_getVariant ($row, $conf, $mode=TYPO3_MODE) {

		$row = tx_dam_db::getRecordOverlay('tx_dam', $row, $conf, $mode);

		return $row;
	}


	/**
	 * Updates the meta data in the index for a given UID.
	 *
	 * @param	integer		$uid UID of the meta data record
	 * @param	array		$row Meta data record array with field/value pairs to be updated
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	boolean		True when index was updated
	 */
	function meta_putData ($uid, $meta, $mode=TYPO3_MODE) {

		if($uid = intval($uid)) {
			$meta['uid'] = $uid;
			return tx_dam_db::insertUpdateData($meta);
		}

		return false;
	}
	

	/**
	 * Checks if a file was changed or if it's missing and updates the status accordingly
	 *
	 * @param	array		$row Meta data record array with 'uid' field
	 * @param	boolean		$markMissingDeleted If set (default) a record will be marked deleted if a file is missing
	 * @return	integer		New status value eg. TXDAM_status_file_changed, TXDAM_status_file_missing
	 */
	function meta_updateStatus ($meta, $markMissingDeleted=true) {
		$status = TXDAM_status_file_ok;

		if (!$meta['uid']) return false;

		$filepath = tx_dam::file_absolutePath ($meta);
		$fileInfo = tx_dam::file_compileInfo ($filepath);
		if($fileInfo['__exists']) {
			$hash = tx_dam::file_calcHash ($fileInfo);
			if (!($fileInfo['file_mtime']==$meta['file_mtime']) OR !($hash==$meta['file_hash'])) {
				$status = TXDAM_status_file_changed;
				tx_dam_db::updateStatus($meta['uid'], $status, $fileInfo, $hash);
			}
		} else {
			$status = TXDAM_status_file_missing;
			tx_dam_db::updateStatus($meta['uid'], $status, NULL, NULL, ($markMissingDeleted ? 1 : NULL));
		}

		return $status;
	}




	/***************************************
	 *
	 *	 Media objects functions
	 *
	 ***************************************/



	/**
	 * Returns a media object by a given file path or file info array.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$hash If set the hash value can be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	object		media object or false
	 * @see tx_dam_media
	 */
	function media_getForFile($fileInfo, $hash=false, $mode=TYPO3_MODE) {
		global $TYPO3_CONF_VARS;

		require_once(PATH_txdam.'lib/class.tx_dam_media.php');
		$media = t3lib_div::makeInstance('tx_dam_media');
		$media->setMode($mode);
		$media->fetchIndexFromFilename ($fileInfo, $hash);
		return $media;
	}


	/**
	 * Fetches a media object from the index by a given UID.
	 *
	 * @param	integer		$uid UID of the meta data record
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	object		media object or false
	 * @see tx_dam_media
	 */
	function media_getByUid ($uid, $mode=TYPO3_MODE) {
		global $TYPO3_CONF_VARS;

		require_once(PATH_txdam.'lib/class.tx_dam_media.php');
		$media = t3lib_div::makeInstance('tx_dam_media');
		$media->setMode($mode);
		$media->fetchIndexFromMetaUID ($uid);
		return $media;
	}


	/**
	 * Returns media objects from the index by a given file hash.
	 * This function returns an array of media objects because it's possible to match more than one index entry!
	 *
	 * @param	string		$hash Hash value for the file
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	array		Array of media objects or false.
	 * @see tx_dam_media
	 */
	function media_getByHash ($hash, $mode=TYPO3_MODE) {
		global $TYPO3_CONF_VARS;

		require_once(PATH_txdam.'lib/class.tx_dam_media.php');
		$mediaArr = false;
		if ($rows = tx_dam::meta_getDataByHash ($hash, '*', $mode)) {
			$mediaArr = array();
			foreach ($rows as $row) {
				$mediaArr[$row['uid']] = t3lib_div::makeInstance('tx_dam_media');
				$mediaArr[$row['uid']]->setMode($mode);
				$mediaArr[$row['uid']]->setMetaData ($row);
			}
		}
		return $mediaArr;
	}


	/**
	 * Returns a media object by a given file path or file info array for a file placed in uploads/.
	 * Files in uploads/ are copies from files from fileadmin/ or direct uploads.
	 * The meta data that might be found is not directly meant for the uploads-file but normally it matches the file and you get what you expect.
	 * But for example the file might be placed in fileadmin/ twice with different meta data. Then you can't say which meta data you will get for the uploads-file.
	 *
	 * IMPORTANT
	 * The media object does NOT handle the uploads file itself but a matching file which is placed in fileadmin/. Any operation will not affect the uploads-file but the fileadmin-file.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param 	string 		$uploadsPath Uploads path. will be prepended if $fileInfo is a path (string).
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	object		media object or false
	 * @see tx_dam_media
	 */
	function media_getForUploadsFile($file, $uploadPath='', $mode=TYPO3_MODE) {
		global $TYPO3_CONF_VARS;

		require_once(PATH_txdam.'lib/class.tx_dam_media.php');
		$media = t3lib_div::makeInstance('tx_dam_media');
		$media->setMode($mode);

		if ($row = tx_dam::meta_findDataForUploadsFile($file, $uploadPath, 'tx_dam.uid')) {
			$media->fetchIndexFromMetaUID ($row['uid']);
		}

		return $media;
	}




	/***************************************
	 *
	 *	 Indexing functions
	 *
	 ***************************************/



	/**
	 * Do a check if a file is already indexed and have an entry in the DAM table
	 * This function return a status value and the meta data array, while file_isIndexed() just returns the uid.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$hash The hash value will be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @return	array		status: array('__status' => TXDAM_file_notfound,'meta' => array(...));
	 * @see file_isIndexed()
	 */
	function index_check ($fileInfo, $hash='') {

		$status = array(
				'__status' => TXDAM_file_unknown,
				'meta' => array(),
			);

		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo ($fileInfo, true);
		}
		if (!$hash) {
			$hash = $fileInfo['file_hash'];
		}
// FIXME $hash is not used - what is the concept?
		if ($fileInfo) {

			$where = array();
			$where['file_name'] = 'file_name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_name'],'tx_dam');
			$where['file_path'] = 'file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_path'],'tx_dam');
			if ($rows = tx_dam_db::getDataWhere('', $where, '', '', '1')) {
				reset($rows);
				$row = current($rows);
				$status['meta'] = $row;

				if (!$fileInfo['__exists']) {
					$status['__status'] = TXDAM_file_missing;
				} elseif ($row['file_mtime']==$fileInfo['file_mtime']) {
					$status['__status'] = TXDAM_file_ok;
				} else {
					$status['__status'] = TXDAM_file_changed;
				}
			}
		}
		return $status;
	}


	/**
	 * Tries to find a lost index entry for a lost file and reconnect these items
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$hash The hash value will be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @return	array		status: array('__status' => TXDAM_file_notfound,'meta' => array(...));
	 */
	function index_reconnect($fileInfo, $hash='') {

		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo ($fileInfo, true);
		}

		$status = array(
				'__status' => TXDAM_file_unknown,
				'meta' => array(),
			);

		$metaArr = tx_dam::meta_findDataForFile($fileInfo, $hash, 'uid,file_name,file_path,deleted');

		foreach ($metaArr as $meta) {

			$srcInfo = tx_dam::file_compileInfo ($meta, true);

			if (!$srcInfo['__exists']) {

				if ($meta['deleted']) {
						// setting the record undeleted right away ...
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'tx_dam.uid='.$meta['uid'], array('deleted' => 0));
				}

				tx_dam::notify_fileMoved (tx_dam::file_absolutePath($srcInfo), tx_dam::file_absolutePath($fileInfo));
				$meta = tx_dam::meta_getDataByUid($meta['uid']);
				$status['meta'] = $meta;
				$status['__status'] = TXDAM_file_changed;

				break;
			}
		}
		return $status;
	}



	/**
	 * Process auto indexing for the given file.
	 * This should be used to index files easily.
	 * When auto indexing is disabled the indexing will not be processed.
	 *
	 * @param	string		$filename Filename with path
	 * @param	boolean		$reindex If set already indexed files will be reindexed
	 * @return	array		Meta data array. $meta['fields'] has the record data. Returns false when nothing was indexed but file might be in index already.
	 */
	function index_autoProcess($filename, $reindex=false) {
		global $TYPO3_CONF_VARS;


			// disable auto indexing by setup
		if(!tx_dam::config_getValue('setup.indexing.auto')) {
			return false;
		}

		$filename = tx_dam::file_absolutePath($filename);

		if (!@is_file($filename)) {
			return false;
		}

			// we don't index indexing setup files
		if (basename($filename)=='.indexing.setup.xml') {
			return false;
		}


		if(!$reindex AND tx_dam::file_isIndexed($filename)) {
			return false;
		}


		require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');
		$index = t3lib_div::makeInstance('tx_dam_indexing');
		$index->init();
		$index->setDefaultSetup(tx_dam::file_dirname($filename));
		$index->initEnabledRules();
		$index->enableReindexing($reindex);

			// overrule some parameter from setup
		$index->setPath($filename);
		$index->setPID(tx_dam_db::getPid());
		$index->setRunType('auto');
		$index->enableMetaCollect();

			// indexing ...
		$index->indexUsingCurrentSetup();

		return current($index->meta);
	}


	/**
	 * Process indexing for the given file, folder or a list of files and folders.
	 * This function can be used if a setup for indexing is available of callback function shall be used.
	 * But simply a file name can be passed to and everything goes automatically.
	 *
	 * @param	mixed		$filename A single filename or folder path or a list of files and paths as array. If it is an array the values can be file path or array: array('processFile' => 'path to file that should be indexed', 'metaFile' => 'additional file that holds meta data for the processFile')
	 * @param	mixed		$setup Setup as string (serialized setup) or array. See tx_dam_indexing::restoreSerializedSetup()
	 * @param	mixed		$callbackFunc Callback function for the finished indexed file.
	 * @param	mixed		$metaCallbackFunc Callback function which will be called during indexing to allow modifications to the meta data.
	 * @param	mixed		$filePreprocessingCallbackFunc Callback function for pre processing the to be indexed file.
	 * @return	array		Info array about indexed files and meta data records.
	 * @todo how to set run type???
	 */
	function index_process ($filename, $setup=NULL, $callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL) {
		global $TYPO3_CONF_VARS;

		require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');
		$index = t3lib_div::makeInstance('tx_dam_indexing');
		$index->init();
		$index->setRunType('man');
		$index->setDefaultSetup();
		if ($setup) {
			$index->restoreSerializedSetup($setup);
		} elseif(!is_array($filename)) {
			$index->setDefaultSetup(tx_dam::file_dirname($filename));
		} elseif($filename['processFile']) {
			$index->setDefaultSetup(tx_dam::file_dirname($filename['processFile']));
		}
		$index->setPID();
		$index->initEnabledRules();
		$index->setOptionsFromRules();

		if($filename['processFile']) {
			$index->setPathsList(array($filename));
		} elseif(is_array($filename)) {
			$index->setPathsList($filename);
		} else {
			$index->setPath($filename);
		}

		return $index->indexUsingCurrentSetup($callbackFunc, $metaCallbackFunc, $filePreprocessingCallbackFunc);
	}








	/***************************************
	 *
	 *   process file or folder changes like rename
	 *
	 ***************************************/


	/**
	 * Delete a file and process DB update
	 *
	 * @param	string		$filename File path
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_deleteFile($filename, $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;

		$error = false;

		if ($filename = tx_dam::file_absolutePath($filename)) {
	
			 if(!@file_exists($filename)){
				 tx_dam::notify_fileDeleted($filename);
	
			 } else {
	
					// Init TCE-file-functions object:
				require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
				$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
				$TCEfile->init();
	
					// Processing rename folder
				$cmd = array();
				$cmd['delete']['NONE']['data'] = $filename;
	
				$TCEfile->setCmdmap($cmd);
				$TCEfile->process();
	
				if ($TCEfile->errors()) {
					$error = $TCEfile->getLastError($getFullErrorLogEntry);
				}
			}
	
			if (!$error) {
				$info = array(
						'target_file' => $filename,
					);
				tx_dam::_callProcessPostTrigger('deleteFile', $info);
			}
		}

		return $error;
	}


	/**
	 * Rename a file and process DB update
	 *
	 * @param	string		$oldPath File path
	 * @param	string		$newName New file name
	 * @param	array		$additionalMeta Additional meta data that can be set. Can be used to set the download name too, for example. If the file don't have an index entry this will be ignored.
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_renameFile($oldPath, $newName, $additionalMeta='', $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;

		$error = false;

		$oldPath = tx_dam::file_absolutePath($oldPath);
		$path_parts = t3lib_div::split_fileref($oldPath);
		$oldName = $path_parts['file'];
		$newName = tx_dam::file_makeCleanName($newName, true);
		$newPath = $path_parts['path'].$newName;

		if ($oldPath !== $newPath) {

				// Init TCE-file-functions object:
			require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
			$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
			$TCEfile->init();

				// Processing rename folder
			$cmd = array();
			$cmd['rename']['NONE']['target'] = $oldPath;
			$cmd['rename']['NONE']['data'] = $newName;

			$TCEfile->setCmdmap($cmd);
			$TCEfile->process();
			if ($TCEfile->errors()) {
				$error = $TCEfile->getLastError($getFullErrorLogEntry);
			}
			// already done in tx_dam_tce_file:
			// tx_dam::notify_fileMoved($oldPath, $newPath);
		}

		if (!$error) {
			$meta = tx_dam::meta_getDataForFile($newPath);

			if (!$additionalMeta['file_dl_name'] AND $meta['file_dl_name']===$oldName) {
				$additionalMeta['file_dl_name'] = $newName;
			} elseif ($additionalMeta['file_dl_name']===$meta['file_dl_name']) {
				$additionalMeta['file_dl_name'] = $newName;
			}

			if($meta['uid'] AND is_array($additionalMeta)) {
				unset($additionalMeta['file_name']);
				$additionalMeta['uid'] = $meta['uid'];
				tx_dam_db::insertUpdateData($additionalMeta);
			}
		}

		if (!$error) {
			$info = array(
					'uid' => $meta['uid'],
					'target_file' => $oldPath,
					'new_name' => $newName,
					'new_file' => $newPath,
				);
			tx_dam::_callProcessPostTrigger('renameFile', $info);
		}

		return $error;
	}


	/**
	 * Move a file and process DB update
	 *
	 * @param	string		$oldFilePath File path
	 * @param	string		$newPath New path. This is a directory path where the file should be moved to.
	 * @param	boolean		$allowNewName If set and the target already exists a new name will be created.
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_moveFile($oldFilePath, $newPath, $allowNewName=FALSE, $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;

		$error = true;

		$oldFilePath = tx_dam::file_absolutePath($oldFilePath);
		$newPath = tx_dam::path_makeAbsolute($newPath);
		$oldFileName = tx_dam::file_basename($oldFilePath);;
				
		if ($oldFilePath !== $newPath) {

			$error = false;

				// Init TCE-file-functions object:
			require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
			$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
			$TCEfile->init();

				// Processing rename folder
			$cmd = array();
			$cmd['move']['NONE']['data'] = $oldFilePath;
			$cmd['move']['NONE']['target'] = $newPath;
			$cmd['move']['NONE']['altName'] = $allowNewName;

			$TCEfile->setCmdmap($cmd);
			$TCEfile->process();
			if ($TCEfile->errors()) {
				$error = $TCEfile->getLastError($getFullErrorLogEntry);
			}
			// already done in tx_dam_tce_file:
			// tx_dam::notify_fileMoved($oldPath, $newPath);
		}


		if (!$error) {
			$info = array(
					'target_file' => $oldFilePath,
					'new_file' => $newPath.$oldFileName,
				);
			tx_dam::_callProcessPostTrigger('moveFile', $info);
		}

		return $error;
	}


	/**
	 * Copy a file and process DB update
	 *
	 * @param	string		$oldFilePath File path
	 * @param	string		$newPath New path. This is a directory path where the file should be copied to.
	 * @param	boolean		$allowNewName If set and the target already exists a new name will be created.
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_copyFile($oldFilePath, $newPath, $allowNewName=FALSE, $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;

		$error = true;

		$oldFilePath = tx_dam::file_absolutePath($oldFilePath);
		$newPath = tx_dam::path_makeAbsolute($newPath);
		$oldFileName = tx_dam::file_basename($oldFilePath);
				
		if ($oldFilePath !== $newPath) {

			$error = false;

				// Init TCE-file-functions object:
			require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
			$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
			$TCEfile->init();

				// Processing rename folder
			$cmd = array();
			$cmd['copy']['NONE']['data'] = $oldFilePath;
			$cmd['copy']['NONE']['target'] = $newPath;
			$cmd['copy']['NONE']['altName'] = $allowNewName;

			$TCEfile->setCmdmap($cmd);
			$TCEfile->process();
			if ($TCEfile->errors()) {
				$error = $TCEfile->getLastError($getFullErrorLogEntry);
			}
			// already done in tx_dam_tce_file:
			// tx_dam::notify_fileCopied($oldPath, $newPath);
		}


		if (!$error) {
			$info = array(
					'target_file' => $oldFilePath,
					'new_file' => $newPath.$oldFileName,
				);
			tx_dam::_callProcessPostTrigger('copyFile', $info);
		}

		return $error;
	}


	/**
	 * Creates a new file
	 *
	 * @param 	array 	$filename Filename
	 * @param 	array 	$content Content for the new file
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_createFile($filename, $content='', $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;

		$error = false;

		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
		$TCEfile->init();

		$path_parts = t3lib_div::split_fileref($filename);
		
			// Processing create new file
		$cmd = array();
		$cmd['newfile']['NONE']['target'] = tx_dam::path_makeAbsolute($path_parts['path']);
		$cmd['newfile']['NONE']['data'] = $path_parts['file'];
		$cmd['newfile']['NONE']['content'] = $content;

		$TCEfile->setCmdmap($cmd);
		$log = $TCEfile->process();

		if ($TCEfile->errors()) {

			$error = $TCEfile->getLastError();

		} else {

				// index the file
			$setup = array(
				'recursive' => false,
				);
			$filepath = tx_dam::file_absolutePath($filename);
			tx_dam::index_process ($filepath, $setup);
		}

		if (!$error) {
			$info = array(
					'target_file' => tx_dam::file_absolutePath($filename),
				);
			tx_dam::_callProcessPostTrigger('createFile', $info);
		}

		return $error;
	}


	/**
	 * Updates a text files content
	 *
	 * @param 	array 	$filename Filename
	 * @param 	string 	$content Content for the file
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_editFile($filename, $content='', $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;
		

		$error = false;

		$filepath = tx_dam::file_absolutePath($filename);

		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
		$TCEfile->init();

			// Processing create new file
		$cmd = array();
		$cmd['editfile']['NONE']['target'] = $filepath;
		$cmd['editfile']['NONE']['content'] = $content;

		$TCEfile->setCmdmap($cmd);
		$log = $TCEfile->process();

		if ($TCEfile->errors()) {

			$error = $TCEfile->getLastError();

		} else {

				// index the file
			$setup = array(
				'recursive' => false,
				'doReindexing' => tx_dam::config_checkValueEnabled('indexing.editFile.reindexingMode', 1), // reindexPreserve - preserve old data if new is empty
				);
			tx_dam::index_process ($filepath, $setup);
		}

		if (!$error) {
			$info = array(
					'target_file' => $filepath,
				);
			tx_dam::_callProcessPostTrigger('editFile', $info);
		}

		return $error;
	}


	/**
	 * Replace a file with an uploaded file and process indexing and DB update
	 * Important: $meta['uid'] have to be used in the upload data like this $upload_data['upload'][$meta['uid']]
	 *
	 * @param 	array 	$meta Meta data array
	 * @param 	array 	$upload_data Form upload data for $TCEfile->setCmdmap($upload_data)
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_replaceFile($meta, $upload_data, $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;
		

		$error = false;

		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
		$TCEfile->init();
			// allow overwrite
		$TCEfile->fileProcessor->dontCheckForUnique = true;


// FIXME overwrite only original file not others
// dontCheckForUnique=true allow overwriting any file
// dontCheckForUnique have to be false and the file have to be replaced afterwards?


		if($id = $meta['uid'] AND is_array($upload_data['upload'][$id])) {

				// Processing uploads
			$TCEfile->setCmdmap($upload_data);
			$log = $TCEfile->process();

			if ($TCEfile->errors()) {

				$error = $TCEfile->getLastError();

			} else {

				$newFile = $log['cmd']['upload'][$id]['target_file'];
				$newFile = tx_dam::file_absolutePath($newFile);
				$new_filename = tx_dam::file_basename($newFile);

					// new file name - so we need to update some stuff
				if ($new_filename !== $meta['file_name']) {
						// rename meta data fields
					$fields_values = array();
					$fields_values['file_name'] = $new_filename;
					$fields_values['file_dl_name'] = $new_filename;
					$fields_values['uid'] = $meta['uid'];
					tx_dam_db::insertUpdateData($fields_values);

						// delete the old file
					$oldFile = tx_dam::file_absolutePath($meta);
					@unlink($oldFile);
					if (@is_file($oldFile)) {
						$error = 'File '.$meta['file_name'].' could not be deleted.';
						if ($getFullErrorLogEntry) {
							$error = array('msg' => $error);
						}
					}
				}

					// reindex the file
				$setup = array(
						'recursive' => false,
						'doReindexing' => tx_dam::config_checkValueEnabled('indexing.replaceFile.reindexingMode', 2), // reindexPreserve - preserve old data if new is empty
					);
				tx_dam::index_process ($newFile, $setup);

			}
		} else {
			$error = true;
		}

		if (!$error) {
			$info = array(
					'uid' => $meta['uid'],
					'new_file' => $newFile,
					'old_file' => $oldFile,
				);
			tx_dam::_callProcessPostTrigger('replaceFile', $info);
		}

		return $error;
	}


	/**
	 * Rename a folder and process DB update
	 *
	 * @param	string		$oldPath Folder path
	 * @param	string		$newName New folder name
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_renameFolder($oldPath, $newName, $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;

		$error = false;

		$oldPath = tx_dam::path_makeAbsolute($oldPath);
		$newName = tx_dam::file_makeCleanName($newName, true);
		$newPath = dirname($oldPath).$newName.'/';

			// Init TCE-file-functions object:
		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
		$TCEfile->init();

			// Processing rename folder
		$cmd = array();
		$cmd['rename']['NONE']['target'] = preg_replace('#/$#', '', $oldPath);
		$cmd['rename']['NONE']['data'] = $newName;

		$TCEfile->setCmdmap($cmd);
		$TCEfile->process();

		if ($TCEfile->errors()) {
			$error = $TCEfile->getLastError($getFullErrorLogEntry);
		}
		//} else {
			// already done in tx_dam_tce_file:
			// tx_dam::notify_fileMoved($oldPath, $newPath);
		//}

		if (!$error) {
			$info = array(
					'target_path' => $oldPath,
					'new_name' => $newName,
					'new_path' => $newPath,
				);
			tx_dam::_callProcessPostTrigger('renameFolder', $info);
		}

		return $error;
	}
	
// TODO tx_dam::process_moveFolder($fspath, $getFullErrorLogEntry=FALSE);
	
	/**
	 * Move a folder and process DB update
	 *
	 * @param	string		$oldPath Folder path
	 * @param	string		$newPath New folder path. This is a directory path where the folder should be moved to.
	 * @param	boolean		$allowNewName If set and the target already exists a new name will be created.
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_moveFolder($oldPath, $newPath, $allowNewName=FALSE, $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;

		$error = true;

		$oldPath = tx_dam::path_makeAbsolute($oldPath);
		$newPath = tx_dam::path_makeAbsolute($newPath);
		$oldFolderName = tx_dam::path_basename($oldPath);
				
		if ($oldPath !== $newPath) {

			$error = false;

				// Init TCE-file-functions object:
			require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
			$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
			$TCEfile->init();

				// Processing rename folder
			$cmd = array();
			$cmd['copy']['NONE']['data'] = $oldPath;
			$cmd['copy']['NONE']['target'] = $newPath;
			$cmd['copy']['NONE']['altName'] = $allowNewName;

			$TCEfile->setCmdmap($cmd);
			$TCEfile->process();
			if ($TCEfile->errors()) {
				$error = $TCEfile->getLastError($getFullErrorLogEntry);
			}
			// already done in tx_dam_tce_file:
			// tx_dam::notify_fileMoved($oldPath, $newPath);
		}


		if (!$error) {
			$info = array(
					'target_folder' => $oldPath,
					'new_folder' => $newPath.$oldFolderName,
				);
			tx_dam::_callProcessPostTrigger('moveFolder', $info);
		}


		return $error=true;
	}


	/**
	 * Move a folder and process DB update
	 *
	 * @param	string		$oldFolderPath Folder path
	 * @param	string		$newPath New path. This is a directory path where the folder should be copied to.
	 * @param	boolean		$allowNewName If set and the target already exists a new name will be created.
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_copyFolder($oldFolderPath, $newPath, $allowNewName=FALSE, $getFullErrorLogEntry=FALSE) {
		global $TYPO3_CONF_VARS;

		$error = true;

		$oldFolderPath = tx_dam::path_makeAbsolute($oldFolderPath);
		$newPath = tx_dam::path_makeAbsolute($newPath);
		$oldFolderName = tx_dam::path_basename($oldFolderPath);
				
		if ($oldFolderPath !== $newPath) {

			$error = false;

				// Init TCE-file-functions object:
			require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
			$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
			$TCEfile->init();

				// Processing rename folder
			$cmd = array();
			$cmd['move']['NONE']['data'] = $oldFolderPath;
			$cmd['move']['NONE']['target'] = $newPath;
			$cmd['move']['NONE']['altName'] = $allowNewName;

			$TCEfile->setCmdmap($cmd);
			$TCEfile->process();
			if ($TCEfile->errors()) {
				$error = $TCEfile->getLastError($getFullErrorLogEntry);
			}
			// already done in tx_dam_tce_file:
			// tx_dam::notify_fileCopied($oldPath, $newPath);
		}


		if (!$error) {
			$info = array(
					'target_folder' => $oldFolderPath,
					'new_folder' => $newPath.$oldFolderName,
				);
			tx_dam::_callProcessPostTrigger('copyFolder', $info);
		}

		return $error;
	}
	

	/**
	 * Creates a folder
	 *
	 * @param	string		$path Folder path
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_createFolder($path, $getFullErrorLogEntry=FALSE){
		global $TYPO3_CONF_VARS;

		$error = false;

		$path = tx_dam::path_makeAbsolute($path);
		$folderName = tx_dam::path_basename($path);
		$pathDest =  preg_replace('#/$#', '', dirname($path));
		
			// Init TCE-file-functions object:
		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
		$TCEfile->init();

			// Processing delete folder
		$cmd = array();
		$cmd['newfolder']['NONE']['target'] = $pathDest;
		$cmd['newfolder']['NONE']['data'] = $folderName;
		$TCEfile->setCmdmap($cmd);
		$log = $TCEfile->process();

		if ($TCEfile->errors()) {
			$error = $TCEfile->getLastError($getFullErrorLogEntry);
		}

		if (!$error) {
			$info = array(
					'target_path' => $path,
				);
			tx_dam::_callProcessPostTrigger('createFolder', $info);
		}

		return $error;
	}
	
	
	/**
	 * Deletes a folder and it's files and process DB update
	 *
	 * @param	string		$path Folder path
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 * @see tx_dam_tce_file::getLastError()
	 */
	function process_deleteFolder($path, $getFullErrorLogEntry=FALSE){
		global $TYPO3_CONF_VARS;

		$error = false;

		$path = tx_dam::path_makeAbsolute($path);

			// Init TCE-file-functions object:
		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$TCEfile = t3lib_div::makeInstance('tx_dam_tce_file');
		$TCEfile->init();

			// Processing delete folder
		$cmd = array();
		$cmd['delete']['NONE']['data'] = $path;
		$TCEfile->setCmdmap($cmd);
		$log = $TCEfile->process();

		if ($TCEfile->errors()) {
			$error = $TCEfile->getLastError($getFullErrorLogEntry);
		}

		if (!$error) {
			$info = array(
					'target_path' => $path,
				);
			tx_dam::_callProcessPostTrigger('deleteFolder', $info);
		}

		return $error;
	}


	/**
	 * Calls registered 'processPostTrigger' functions for process_* functions
	 *
	 * @param mixed may be a variable list of parameters. The first have to be an identifier string of the action like 'replaceFile'
	 * @return void
	 * @access private
	 */
	function _callProcessPostTrigger() {
		global $TYPO3_CONF_VARS;

			// hook
		if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam']['processTriggerClasses']) AND count($TYPO3_CONF_VARS['EXTCONF']['dam']['processTriggerClasses']))	{
			foreach($TYPO3_CONF_VARS['EXTCONF']['dam']['processTriggerClasses'] as $classKey => $classRef)	{
				if (is_object($obj = &t3lib_div::getUserObj($classRef)))	{
					if (method_exists($obj, 'processPostTrigger')) {
    					$args = func_get_args();
						call_user_func_array(array(&$obj, 'processPostTrigger'), $args);
						#$obj->processPostTrigger('replaceFile', ...);
					}
				}
			}
		}
	}





	/***************************************
	 *
	 *   Notify the DAM about file or folder changes
	 *
	 ***************************************/



	/**
	 * Notifies the DAM about (external) changes/update of a file.
	 * This will update the file related meta data of the file like date and size.
	 *
	 * @param	string		$filename Filename with path
	 * @return	void
	 */
	function notify_fileChanged ($filename) {
		if (is_array($meta = tx_dam::meta_getDataForFile($filename))) {
			tx_dam::meta_updateStatus ($meta);
		} else {
				// file is not yet indexed
			tx_dam::index_autoProcess($filename);
		}
	}


	/**
	 * Notifies the DAM about (external) changes to names and movements about files or folders.
	 * This will update all related meta data
	 *
	 * @param	string		$src File/folder name with path of the source that was changed.
	 * @param	string		$dest File/folder name with path of the destination which is a new name or/and a new location.
	 * @return	void
	 */
	function notify_fileMoved ($src, $dest) {

		if (@is_file($dest)) {

			if ($meta = tx_dam::meta_getDataForFile($src, 'uid,file_name,file_dl_name', true)) {

				$fileInfo = tx_dam::file_compileInfo ($dest);

				$values = array();
				$values['uid'] = $meta['uid'];
				$values['deleted'] = '0';
				$values['file_name'] = $fileInfo['file_name'];
				$values['file_path'] = $fileInfo['file_path'];
				$values['file_mtime'] = $fileInfo['file_mtime'];
				if ($meta['file_dl_name']==$meta['file_name']) {
					$values['file_dl_name'] = $fileInfo['file_name'];
				}

				tx_dam_db::insertUpdateData($values);

			} else {
					// file is not yet indexed
				tx_dam::index_autoProcess($dest);
			}

			// the item is a folder
		} elseif (@is_dir($dest)) {
			tx_dam_db::updateFilePath($src, $dest);
		}
		// else unknown
	}


	/**
	 * Notifies the DAM about (external) changes to names and movements about files or folders.
	 * This will update all related meta data
	 *
	 * @param	string		$src File/folder name with path of the source that was changed.
	 * @param	string		$dest File/folder name with path of the destination which is a new name or/and a new location.
	 * @return	void
	 */
	function notify_fileCopied ($src, $dest) {

		if (@is_file($dest)) {

			if ($meta = tx_dam::meta_getDataForFile($src, '*', true)) {

				$fileInfo = tx_dam::file_compileInfo ($dest);

				$values = $meta;
				$values['uid'] = 'NEW';
				$values['deleted'] = '0';
				$values['file_name'] = $fileInfo['file_name'];
				$values['file_path'] = $fileInfo['file_path'];
				$values['file_mtime'] = $fileInfo['file_mtime'];
				if ($meta['file_dl_name']==$meta['file_name']) {
					$values['file_dl_name'] = $fileInfo['file_name'];
				}

				tx_dam_db::insertUpdateData($values);

			} else {
					// file is not yet indexed
				tx_dam::index_autoProcess($dest);
			}

			// the item is a folder
		} elseif (@is_dir($dest)) {
			tx_dam_db::cloneFilePath($src, $dest);
		}
		// else unknown
	}


	/**
	 * Notifies the DAM about a deleted file or folder.
	 * This will remove the file(s) from the index.
	 *
	 * @param	string		$filename Filename with path or a folder which have to have a trailing slash.
	 * @param	string		$recyclerPath New path when item is moved to recycler.
	 * @return	void
	 */
	function notify_fileDeleted ($filename, $recyclerPath='') {

		if(is_array($row = tx_dam::meta_getDataForFile ($filename, 'uid', true))) {
			$uid = $row['uid'];
		}

		if ($uid) {
			$fields_values = array();
			$fields_values['uid'] = $uid;
			$fields_values['deleted'] = '1';

				// file was moved to recycler
			if ($recyclerPath) {
				
				$org_filename = tx_dam::file_basename($filename);
				$path_parts = t3lib_div::split_fileref($recyclerPath);
				$new_filename = $path_parts['file'];
				$new_path = $path_parts['path'];

				if ($org_filename != $new_filename) {
					$fields_values['file_name'] = $new_filename;
				}
				if ($new_path) {
					$fields_values['file_path'] = tx_dam::path_makeRelative($new_path);
				}

			} else {
					// delete MM relations
				$GLOBALS['TYPO3_DB']->exec_DELETEquery( 'tx_dam_mm_ref', 'tx_dam_mm_ref.uid_local='.$uid);
			}

			tx_dam_db::insertUpdateData($fields_values);
			
			
				// set language overlays deleted
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'l18n_parent='.$uid, array('deleted' => 1));
			# $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam', 'l18n_parent='.$uid);


		// todo: replace with full supported group concept --------------------
			// todo: delete child elements and their MM-relation
			// files stay at their physical storage position (usually uploads/tx_dam/storage/_uid_/)

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam', 'parent_id='.intval($uid));
			while ($childRow = $GLOBALS['TYPO3_DB']->sql_fetch_row($res)) {
				$childUid = $childRow[0];
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.$childUid, array('deleted' => 1));
				$GLOBALS['TYPO3_DB']->exec_DELETEquery( 'tx_dam_mm_ref', 'tx_dam_mm_ref.uid_local='.$childUid);
			}
		// ------------------------------------

		} elseif (preg_match('#/$#', $filename)) {
			tx_dam_db::updateFilePathSetDeleted($filename);
		}
	}





	/***************************************
	 *
	 *   Converter for names and codes of data formats
	 *
	 ***************************************/



	/**
	 * Converts the media type name to integer and vice versa.
	 *
	 * @param	mixed		$type Media type name or media type code to convert. Integer or 'text','image','audio','video','interactive', 'service','font','model','dataset','collection','software','application'
	 * @return	mixed		Media type name or media type code
	 */
	function convert_mediaType($type) {

		if(!strcmp($type,intval($type))) {
			$type = $GLOBALS['T3_VAR']['ext']['dam']['code2media'][$type];
		} else {
			$type = $GLOBALS['T3_VAR']['ext']['dam']['media2code'][$type];
		}
		return $type;
	}





	/***************************************
	 *
	 *	 Icon functions
	 *
	 ***************************************/


	/**
	 * Returns the icon file path for a file type icon for a given file.
	 * $mimeType = tx_dam::file_getType($filename);
	 *
	 * @param	array		$mimeType Describes the type of a file. Can be meta record array or array from tx_dam::file_getType().
	 * @param	boolean		$absolutePath If set the path to the icon is absolute. By default it's relative to typo3/ folder.
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	string		Icon image file path
	 * @see tx_dam::file_getType()
	 */
	function icon_getFileType ($mimeType, $absolutePath=false, $mode=TYPO3_MODE) {
		static $iconCache = array();
		static $iconCacheRel = array();

		$iconfile = false;

			// first see if the icon is in the icon cache
		if (is_array($mimeType)) {

			if (!$absolutePath && $cached = $iconCacheRel[$mimeType['file_type']]) {
				return $cached;

			} elseif (!$absolutePath && $cached = $iconCacheRel['_mtype_'.$mimeType['media_type']]) {
				return $cached;

			} elseif ($cached = $iconCache[$mimeType['file_type']]) {
				$iconfile = $cached;

			} elseif ($cached = $iconCache['_mtype_'.$mimeType['media_type']]) {
				$iconfile = $cached;

			} else {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths_'.$mode] as $pathIcons ) {
						// first try PNG
					if (@file_exists($pathIcons.$mimeType['file_type'].'.png')) {
						$iconfile = $pathIcons.$mimeType['file_type'].'.png';
						$cacheKey = $mimeType['file_type'];
						$iconCache[$cacheKey] = $iconfile;
						break;
					}

						// then go for GIF
					if (@file_exists($pathIcons.$mimeType['file_type'].'.gif')) {
						$iconfile = $pathIcons.$mimeType['file_type'].'.gif';
						$cacheKey = $mimeType['file_type'];
						$iconCache[$cacheKey] = $iconfile;
						break;
					}
				}

				if(!$iconfile && $mediaType = tx_dam::convert_mediaType($mimeType['media_type'])) {
					foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths_'.$mode] as $pathIcons ) {
							// first try PNG
						if (@file_exists($pathIcons.'mtype_'.$mediaType.'.png')) {
							$iconfile = $pathIcons.'mtype_'.$mediaType.'.png';
							$cacheKey = '_mtype_'.$mimeType['media_type'];
							$iconCache[$cacheKey] = $iconfile;
							break;
						}

							// then go for GIF
						if (@file_exists($pathIcons.'mtype_'.$mediaType.'.gif')) {
							$iconfile = $pathIcons.'mtype_'.$mediaType.'.gif';
							$cacheKey = '_mtype_'.$mimeType['media_type'];
							$iconCache[$cacheKey] = $iconfile;
							break;
						}
					}
				}

				if (!$iconfile) {
					$iconfile = PATH_txdam.'i/18/'.'mtype_undefined.gif';
					$cacheKey = '__undefined';
				}
			}
		}

		if (!$absolutePath) {
			$iconfile = preg_replace('#^'.preg_quote(PATH_site).'#', '', $iconfile);
	 		if (TYPO3_MODE === 'BE') {
				$iconfile = '../'.$iconfile;
				$iconCacheRel[$cacheKey] = $iconfile;
			}
		}

		return $iconfile;
	}


	/**
	 * Returns the icon file path for a folder icon for a given path.
	 *
	 * @param	array		$pathInfo Path info array: $pathInfo = tx_dam::path_getInfo($path)
	 * @param	boolean		$absolutePath If set the path to the icon is absolute. By default it's relative to typo3/ folder.
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	string		Icon path
	 * @see tx_dam::path_getInfo()
	 * @todo caching
	 */
	function icon_getFolder($pathInfo, $absolutePath=false, $mode=TYPO3_MODE)	{


		if ($pathInfo['mount_path'] === $pathInfo['dir_path_absolute']) {
			switch($pathInfo['mount_type'])	{
				case 'user':
					$iconfile = 'folder_mount_user.gif';
					break;
				case 'group':
					$iconfile = 'folder_mount_group.gif';
					break;
				case 'readonly':
					$iconfile = 'folder_mount_readonly.gif';
					break;
				default:
					$iconfile = 'folder_mount.gif';
					break;
			}
		}
		else {
			if($pathInfo['__protected']) {
				$iconfile = 'folder_'.$pathInfo['web_sys'].'_protected.gif';
			}
			elseif ($pathInfo['dir_name'] === '_temp_')	{
				$iconfile = 'folder_temp.gif';
			}
			elseif ($pathInfo['dir_name'] === '_recycler_')	{
				$iconfile = 'folder_recycler.gif';
			}
			// what is this - makes it sense?
			elseif ($pathInfo['mount_id'] === '_temp_')	{
				$iconfile = 'folder_mount.gif';
			} else {
				$iconfile = 'folder_'.($pathInfo['web_sys']?$pathInfo['web_sys']:'web').(($pathInfo['dir_writable'] && !$pathInfo['dir_readonly'])?'':'_ro').'.gif';
			}
		}

		foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths_'.$mode] as $pathIcons ) {
			if (@is_file($pathIcons.$iconfile)) {
				$iconfile = $pathIcons.$iconfile;
				break;
			}
		}
		
		if (!$absolutePath) {
			$iconfile = preg_replace('#^'.preg_quote(PATH_site).'#', '', $iconfile);
	 		if (TYPO3_MODE === 'BE') {
				$iconfile = '../'.$iconfile;
			}
		}
		
		return $iconfile;
	}


	/**
	 * Returns a file or folder icon for a given (file)path as HTML img tag.
	 *
	 * @param	array		$infoArr info array: eg. $pathInfo = tx_dam::path_getInfo($path)
	 * @param	boolean		$addAttrib Additional attributes for the image tag..
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	string		Icon img tag
	 * @see tx_dam::path_getInfo()
	 */
	function icon_getFileTypeImgTag($infoArr, $addAttrib='', $mode=TYPO3_MODE)	{
		global $TYPO3_CONF_VARS, $TCA;
		static $iconCacheSize = array();

		require_once(PATH_t3lib.'class.t3lib_iconworks.php');

		if (isset($infoArr['dir_name'])) {
			$iconfile = tx_dam::icon_getFolder ($infoArr);
		}
		elseif (isset($infoArr['file_name']) OR isset($infoArr['file_type']) OR isset($infoArr['media_type'])) {
			$iconfile = tx_dam::icon_getFileType ($infoArr);
			
			if ($mode === 'BE') {
				$tca_temp_typeicons = $TCA['tx_dam']['ctrl']['typeicons'];
				$tca_temp_iconfile = $TCA['tx_dam']['ctrl']['iconfile'];
				unset($TCA['tx_dam']['ctrl']['typeicons']);
				$TCA['tx_dam']['ctrl']['iconfile'] = $iconfile;
				
				$iconfile = t3lib_iconWorks::getIcon('tx_dam', $infoArr, $shaded=false);
				
				$TCA['tx_dam']['ctrl']['iconfile'] = $tca_temp_iconfile;
				$TCA['tx_dam']['ctrl']['typeicons'] = $tca_temp_typeicons;
			}
		}
		
		if (!$iconCacheSize[$iconfile]) {
				// Get width/height:
			$iInfo = @getimagesize(t3lib_div::resolveBackPath(PATH_site.TYPO3_mainDir.$iconfile));
			$iconCacheSize[$iconfile] = $iInfo[3];
		}
		
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconfile, $iconCacheSize[$iconfile]).' class="typo3-icon"  alt="" '.trim($addAttrib).' />';

		return $icon;
	}





	/***************************************
	 *
	 *   Misc Tools
	 *
	 ***************************************/



	/**
	 * Search for a file and walk up the path if not found in current dir.
	 *
	 * @param 	string 		$fileName File name to search for
	 * @param 	string 		$path Path to search for file
	 * @param 	boolean 	$walkUp If set it will be searched for the file in folders above the given
	 * @param 	string 		$basePath This absolute path is the limit for searching with $walkUp
	 * @return	string 		file content
	 */
	function tools_findFileInPath($fileName, $path, $walkUp=true, $basePath='') {

		$basePath = $basePath ? $basePath : PATH_site;

		$path = tx_dam::path_makeAbsolute($path);

		if (is_file($path.$fileName) AND is_readable($path.$fileName)) {

			$setup = t3lib_div::getUrl($path.$fileName);
			return $setup;
		}

		if (!$walkUp OR ($path == $basePath)) {
			return false;
		}

		if (tx_dam::path_makeRelative($path) == '') {
			return false;
		}

		if (!($path=dirname($path))) {
			return false;
		}

		return tx_dam::tools_findFileInPath($fileName, $path, $walkUp, $basePath);
	}





	/***************************************
	 *
	 *   Register functions like selection classes, indexing rules, viewer, editors
	 *
	 ***************************************/



	/**
	 * Register a meta data trigger class
	 * The class will be called when the meta data of a file was changed.
	 *
	 * @param	string		$idName This is the ID of the selection. Chars allowed only: [a-zA-z]
	 * @param	string		$class reference, '[file-reference":"]["&"]class'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 * @see tx_dam_db
	 * @see tx_dam_dbTriggerMediaTypes
	 */
	function register_dbTrigger ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['dbTriggerClasses'], $position);
	}

	/**
	 * Register a file trigger class
	 * The class will be called when the a file was changed. This can be the name or the file was moved or deleted.
	 * Two different methods are supported
	 * filePostTrigger() - see lib/class.tx_dam_tce_file.php
	 * processPostTrigger() - see lib/class.tx_dam.php
	 *
	 * @param	string		$idName This is the ID of the selection. Chars allowed only: [a-zA-z]
	 * @param	string		$class reference, '[file-reference":"]["&"]class'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 * @see tx_dam_db
	 */
	function register_fileTrigger ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileTriggerClasses'], $position);
	}

	/**
	 * Register a selection class
	 *
	 * @param	string		$idName This is the ID of the selection. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_selection ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['selectionClasses'], $position);
	}


	/**
	 * Register an indexing rule class
	 *
	 * @param	string		$idName This is the ID of the indexing rule. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_indexingRule ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['indexRuleClasses'], $position);
	}


	/**
	 * Register a file type
	 * This extends or overwrite the internal list of mime types which is used to detect a media type and a file type.
	 *
	 * @param	string		$fileExtension File extension. Eg. jpg, pdf, ...
	 * @param	string		$mimeType Eg. image/tiff, audio/x-mpeg
	 * @param	string		$mediaType Optional, if mime type is different. Eg. 'text','image','audio','video','interactive', 'service','font','model','dataset','collection','software','application'
	 * @return	void
	 */
	function register_fileType ($fileExtension, $mimeType, $mediaType='') {
		$fileExtension = strtolower($fileExtension);
		$GLOBALS['T3_VAR']['ext']['dam']['file2mime'][$fileExtension] = $mimeType;
		if ($mediaType) {
			$GLOBALS['T3_VAR']['ext']['dam']['file2mediaCode'][$fileExtension] = tx_dam::convert_mediaType($mediaType);
		}
	}


	/**
	 * Register a class which renders a "previewer".
	 * A previewer will render a - yes - preview of a file. This can be a thumbnail or a small embedded mp3 player.
	 * A previewer can be used in thumbnail view or at the top of a record.
	 *
	 * @param	string		$idName This is the ID of the previewer. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 * @todo register name as LANG resource? Or put it into the class?
	 * @todo make it possible to register media_types?
	 */
	function register_previewer ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['previewerClasses'], $position);
	}


	/**
	 * Register a class which renders a "editor".
	 * A editor is something that modifies a file. This can be a text editor or a module to crop an image.
	 *
	 * @param	string		$idName This is the ID of the editor. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_editor ($idName, $classRef, $position='') {
		tx_dam::_addItem($idName, $classRef, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['editorClasses'], $position);
		
			// an editor is also and module function so we register it here
		if (strstr($classRef,':'))	{
			list($file,$class) = t3lib_div::revExplode(':',$classRef,2);
			$requireFile = t3lib_div::getFileAbsFileName($file);
		} else {
			$class = $classRef;
		}

			// Check for persistent object token, "&"
		if (substr($class,0,1)=='&')	{
			$class = substr($class,1);
		}
		
			// register module function
		t3lib_extMgm::insertModuleFunction(
			'txdamM1_edit',
			$class,
			$requireFile,
			'LLL:EXT:dam/mod_edit/locallang.xml:tx_dam_edit.title'
		);		
	}


	/**
	 * Register a "action" class.
	 * A action is something that renders buttons, control icons, ..., which executes command for an item.
	 *
	 * @param	string		$idName This is the ID of the action. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_action ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['actionClasses'], $position);
	}


	/**
	 * Register a path to additional file type icons
	 *
	 * @param	string		$path Path to icon files ("EXT:" prefix will be resolved)
	 * @param	string		$mode TYPO3_MODE to be used: 'FE', 'BE'. Constant TYPO3_MODE is default.
	 * @return	void
	 */
	function register_fileIconPath ($path, $mode=TYPO3_MODE) {

		$path = tx_dam::path_makeClean(t3lib_div::getFileAbsFileName($path));

		if(!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths_'.$mode])) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths_'.$mode] = array();
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths_'.$mode][] = $path;
		} else {
			array_unshift ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths_'.$mode], $path);
		}
	}


	/**
	 * Register a table that is related to a media folder and should be stored there.
	 * The meaning of registering a table is that management functions know which tables are related to a media folder.
	 *
	 * @param	string		$table This is the table name
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_mediaTable ($table, $position='') {
		tx_dam::_addItem($table, $table, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['mediaTables'], $position);
	}


	/**
	 * Returns the registry array from for a type like action, editor, mediaTable. Except fileType.
	 *
	 * @param string $type Registry name: mediaTable, fileIconPath, action, editor, ... (from register_XXX())
	 * @param string $select Some special parameter. Depends on the entry to be fetched
	 * @return array
	 */
	function register_getEntries($type, $select=NULL) {
		$registry = array();
		switch ($type) {
			case 'mediaTable':
					$registry = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['mediaTables'];
				break;
			case 'fileIconPath':
					$mode = $select ? $select : TYPO3_MODE;
					$registry = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths_'.$mode];
				break;
			default:
					if (!is_array($registry = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam'][$type.'Classes'])) {
						$registry = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['registry'][$type];
					}
				break;
		}
		if (!is_array($registry)) {
			$registry = array();
		}
		return $registry;
	}


	/***************************************
	 *
	 *   Configuration
	 *
	 ***************************************/



	/**
	 * Return configuration values which are mainly defined by TSconfig.
	 * The configPath must begin with "setup." or "mod."
	 * "setup" is mapped to tx_dam TSConfig key.
	 *
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'setup.selections.default'
	 * @param	boolean		$getProperties return the properties array instead of the value. Means to return the stuff set by a dot. Eg. setup.xxxx.xxx
	 * @return	mixed		Just the value or when $getProperties is set an array with the properties of the $configPath.
	 */
	function config_getValue($configPath='', $getProperties=false) {
		require_once(PATH_txdam.'lib/class.tx_dam_config.php');
		return tx_dam_config::getValue($configPath, $getProperties);
	}
	
	
	/**
	 * Check a config value if its enabled
	 * Anything except '' and 0 is true
	 * If the the option is not set the default value will be returned
	 *
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'setup.selections.default'
	 * @param	mixed 		$default Default value when option is not set, otherwise the value itself
	 * @return boolean
	 */
	function config_checkValueEnabled($configPath, $default=false) {
		require_once(PATH_txdam.'lib/class.tx_dam_config.php');
		return tx_dam_config::checkValueEnabled($configPath, $default);
	}
	
	
	/**
	 * Set a dam config value
	 * The config path must begin with "setup." or "mod."
	 * "setup" is mapped to tx_dam TSConfig key.
	 *
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'setup.selections.default'
	 * @param	mixed 		$value Value to be set. Can be an array but must be in TSConfig format
	 * @return void
	 * @todo map user setup/options to dam setup?
	 */
	function config_setValue($configPath='', $value='') {
		require_once(PATH_txdam.'lib/class.tx_dam_config.php');
		return tx_dam_config::setValue($configPath, $value);
	}


	/**
	 * Check a config value if its enabled
	 * Anything except '' and 0 is true
	 *
	 * @param	mixed 		$value Value to be checked
	 * @return mixed	Return false if value is empty or 0, otherwise the value itself
	 */
	function config_isEnabled($value) {
		require_once(PATH_txdam.'lib/class.tx_dam_config.php');
		return tx_dam_config::isEnabled($value);
	}
	
	
	/**
	 * Check a config value if its enabled
	 * Anything except '' and 0 is true
	 *
	 * @param	array 		$config Configuration array
	 * @param	string 		$option Option key. If not set in $config default value will be returned
	 * @param	mixed 		$default Default value when option is not set, otherwise the value itself
	 * @return boolean
	 */
	function config_isEnabledOption($config, $option, $default=false) {
		require_once(PATH_txdam.'lib/class.tx_dam_config.php');
		return tx_dam_config::isEnabledOption($config, $option, $default);
	}


	/**
	 * Init dam config values - which means they are fetched from TSConfig
	 *
	 * @param	boolean $force Will force the initialization to be done again except definedTSconfig set by config_setValue
	 * @return void
	 */
	function config_init($force=false) {
		require_once(PATH_txdam.'lib/class.tx_dam_config.php');
		tx_dam_config::init($force);
	}







	/***************************************
	 *
	 *   Internal
	 *
	 ***************************************/




	/**
	 * Adds a an item to an array with the possibility to request a position before/after another item
	 *
	 * @access private
	 * @param	string		$idName
	 * @param	mixed		$value
	 * @param	array		$items
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function _addItem($idName, $value, &$items, $position='')	{

		$position .= ';bottom';
		$posList = t3lib_div::trimExplode(';', $position, 1);

		$element = array($idName => $value);

		$placed = false;

		foreach ($posList as $posDef) {
			list($place, $itemRef) = t3lib_div::trimExplode(':', $posDef, 1);
			switch($place)	{
				case 'after':
				case 'before':
					$pointer = false;
					if (isset($items[$itemRef])) {
						$pointer = $itemRef;
					}
					if ($pointer) {
						$newArr = array();
						foreach($items as $key => $v) {
							if ($pointer == $key AND $place === 'before') {
								$newArr[$idName] = $value;
							}
							$newArr[$key] = $v;
							if ($pointer == $key AND $place === 'after') {
								$newArr[$idName] = $value;
							}
						}
						$placed = true;
						$items = $newArr;
					}
				break;
				case 'top':
					$items = array_merge($element, $items);
					$placed = true;
				break;
				default:
						// append to the list
					$items[$idName] = $value;
					$placed = true;
				break;
			}
			if ($placed) break;
		}
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam.php']);
}
?>
