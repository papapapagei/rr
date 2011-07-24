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
 * indexing lib
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
 *  134: class tx_dam_indexing
 *
 *              SECTION: Init and setup functions
 *  231:     function init()
 *  260:     function clearCollectedMeta()
 *  272:     function enableMetaCollect($metaCollect=TRUE)
 *  283:     function setIndexRun($tstamp=0)
 *  294:     function setRunType($type)
 *  307:     function setPath($path)
 *  320:     function setPathsList($pathlist)
 *  333:     function setRecursive($recursive=true)
 *  346:     function setPID($pid=0)
 *  363:     function setDryRun($dryRun=TRUE)
 *  380:     function enableReindexing($doReindexing=1)
 *  393:     function setOptionsFromRules()
 *  406:     function isDryRun()
 *
 *              SECTION: Setup re-storing functions
 *  427:     function serializeSetup($extraSetup='', $serializeData=true)
 *  454:     function restoreSerializedSetup($setup)
 *  498:     function getExtraSetup()
 *
 *              SECTION: Setting/searching for default setup, eg. from file setup
 *  524:     function findSetupInPath($path, $walkUp=true, $basePath='')
 *  545:     function setDefaultSetup($path=false, $walkUp=true, $basePath='')
 *
 *              SECTION: Main indexing functions
 *  582:     function indexUsingCurrentSetup($callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)
 *  606:     function indexFiles($files, $pid=NULL, $callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)
 *  661:     function skipThisFile($fileInfo)
 *  681:     function indexFile($pathname, $crdate=0, $pid=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL, $metaPreset=array())
 *
 *              SECTION: Indexing rules
 *  918:     function mergeRuleConf($ruleOpt='')
 *  947:     function initEnabledRules()
 *  976:     function initAvailableRules()
 * 1024:     function rulesCallback ($type, $meta, $pathname)
 *
 *              SECTION: Collecting file meta data
 * 1071:     function getFileMetaInfo($pathname, $meta)
 * 1171:     function getMetaLanguage($meta)
 * 1195:     function getFileNodeInfo($pathname, $calcHash=false)
 * 1223:     function getFileMimeType($pathname)
 * 1308:     function getFileTextExcerpt($pathname, $file_type, $limit=64000)
 * 1337:     function getWantedCharset()
 * 1352:     function processTextExcerpt($textExcerpt, $limit=64000)
 * 1376:     function getImageDimensions($pathname, $metaInfo=array())
 * 1466:     function getDefaultRecord($table='tx_dam')
 * 1492:     function makeTitleFromFilename ($title)
 * 1513:     function listBeautify($list)
 *
 *              SECTION: Files, folders and paths
 * 1540:     function getFilesInDir($path, $recursive=FALSE, $filearray=array(), $maxDirs=999)
 * 1570:     function collectFiles($path, $recursive=false, $filearray=array())
 * 1595:     function collectFilesByPathList($pathlist, $recursive)
 *
 *              SECTION: Rendering the option form and info
 * 1622:     function getIndexingOptionsForm()
 * 1643:     function getIndexingOptionsInfo()
 * 1670:     function formatOptionsFormRow ($varname, $setup, $title, $desc='', $options='')
 *
 *              SECTION: XML <> Array
 * 1735:     function array2xml($array, $options=array(), $level=0, $stackData=array())
 *
 *              SECTION: Collect some stats
 * 1878:     function statBegin()
 * 1893:     function statMeta($meta)
 * 1912:     function statEnd()
 * 1923:     function statClear()
 *
 *              SECTION: Locking
 * 1947:     function lock ($filename)
 * 1978:     function unlock($sem_id)
 *
 *              SECTION: Logging
 * 2008:     function writeLog($indexRun, $type, $message, $itemCount, $error)
 * 2033:     function log($message, $itemCount, $error)
 *
 * TOTAL FUNCTIONS: 52
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */





require_once(PATH_t3lib.'class.t3lib_exec.php');
require_once(PATH_t3lib.'class.t3lib_lock.php');


/**
 * Provide indexing functions
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_indexing {

	/**
	 * Should the whole thing be a dry run
	 */
	var $dryRun = FALSE;

	/**
	 * Should files be reindexed
	 */
	var $doReindexing = FALSE;


	/**
	 * indexing rules objects
	 */
	var $rules = array();

	/**
	 * indexing config
	 */
	var $ruleConf = array();

	/**
	 * values which can be overwritten while indexing
	 */
	var $dataPreset = array();

	/**
	 * this will be fixed values
	 */
	var $dataPostset = array();

	/**
	 * to be appended to data values
	 */
	var $dataAppend = array();
// TODO $dataAppend
	/**
	 * Pid of the sysfolder where the DAM records should be written
	 */
	var $pid = 0;

	/**
	 * the folder to index
	 */
	var $pathlist = array();
	var $recursive = false;

	/**
	 * Should the indexed meta data collected into $this->meta?
	 */
	var $collectMeta = false;

	/**
	 * used to collect meta data of the indexed files
	 */
	var $meta = array();

	/**
	 * used to collect uid's and titles of the indexed files
	 */
	var $infoList = array();


	/**
	 * index run type which will be written to log:
	 * man, auto, cron (4 chars max)
	 */
	 var $indexRunType = 'unkn';

	 /**
	  * Array of file extensions that define what files should be skipped while indexing
	  */
	var $skipFileTypes = array();

	/**
	 * used to collect some statistics
	 */
	var $stat = array();
	var $statmtime;
	var $fileLock;




	/***************************************
	 *
	 *	 Init and setup functions
	 *
	 ***************************************/


	/**
	 * Initializes.
	 *
	 * @return	void
	 */
	function init()	{
		global $TYPO3_CONF_VARS;
			// enable dev logging if set
		if ($TYPO3_CONF_VARS['SC_OPTIONS']['ext/dam/lib/class.tx_dam_indexing.php']['writeDevLog']) $this->writeDevLog = TRUE;
		if (TYPO3_DLOG) $this->writeDevLog = TRUE;

		if ($this->writeDevLog && !isset($TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['debugData']['pid'])) $TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['debugData']['pid'] = tx_dam_db::getPid();
		if ($this->writeDevLog) 	t3lib_div::devLog('## Beginning of dam indexing logging.', 'tx_dam_indexing');
		
		$this->setup['useInternalMediaTypeList'] = tx_dam::config_checkValueEnabled('setup.indexing.useInternalMediaTypeList', true);
		$this->setup['useInternalMimeList'] = tx_dam::config_checkValueEnabled('setup.indexing.useInternalMimeList', true);
		$this->setup['useMimeContentType'] = tx_dam::config_checkValueEnabled('setup.indexing.useMimeContentType', true);
		$this->setup['useFileCommand'] = tx_dam::config_checkValueEnabled('setup.indexing.useFileCommand', true);
		
		$this->defaultSetup = tx_dam::config_getValue('tx_dam.indexing.defaultSetup');
		

		$this->skipFileTypes = t3lib_div::trimExplode(',', tx_dam::config_getValue('setup.indexing.skipFileTypes'), true);


		$this->ruleConf = array();
		$this->dataPreset = array();
		$this->dataPostset = array();
		$this->dataAppend = array();
		$this->stat = array();
		$this->indexRun = time();


		$this->clearCollectedMeta();

		$this->initAvailableRules();
	}


	/**
	 * Clears all collected meta data
	 *
	 * @return	void
	 */
	function clearCollectedMeta()	{
		$this->meta = array();
		$this->infoList = array();
	}


	/**
	 * Should the indexed meta data collected into $this->meta?
	 *
	 * @param	boolean		$metaCollect If set the indexed meta data collected into $this->meta
	 * @return	void
	 */
	function enableMetaCollect($metaCollect=TRUE)	{
		$this->collectMeta = $metaCollect;
	}


	/**
	 * Set the index run time stamp
	 *
	 * @param	integer		$tstamp time stamp (time())
	 * @return	void
	 */
	function setIndexRun($tstamp=0)	{
		$this->indexRun = $tstamp ? $tstamp : time();
	}


	/**
	 * Set the index run type
	 *
	 * @param	string		$type man, auto, cron (4 chars max)
	 * @return	void
	 */
	function setRunType($type)	{
		$this->indexRunType = $type;

		if ($this->writeDevLog) 	t3lib_div::devLog('setRunType(): '.$this->indexRunType, 'tx_dam_indexing');
	}


	/**
	 * Set the folder to index
	 *
	 * @param	string		$paththe folder to index
	 * @return	void
	 */
	function setPath($path)	{
		$this->pathlist = array($path);

		if ($this->writeDevLog) 	t3lib_div::devLog('setPath(): '.$path, 'tx_dam_indexing');
	}


	/**
	 * Set the list of folders and files to index
	 *
	 * @param	array		$pathlist the list of folders and files to index
	 * @return	void
	 */
	function setPathsList($pathlist)	{
		$this->pathlist = $pathlist;

		if ($this->writeDevLog) 	t3lib_div::devLog('setPathsList()', 'tx_dam_indexing', 0, $this->pathlist);
	}


	/**
	 * Set the the paths to be be traversed recursivley (or not)
	 *
	 * @param	boolean		$recursive If set the paths will be traversed recursivley
	 * @return	void
	 */
	function setRecursive($recursive=true)	{
		$this->recursive = $recursive;

		if ($this->writeDevLog) 	t3lib_div::devLog('setRecursive(): '.($this->recursive?'true':'false'), 'tx_dam_indexing');
	}


	/**
	 * Set Pid of the sysfolder where the DAM records should be written
	 *
	 * @param	integer		$pid page id
	 * @return	void
	 */
	function setPID($pid=0)	{
		if ($pid==0 AND !$this->pid) {
			$this->pid = tx_dam_db::getPid();
		} elseif ($pid) {
			$this->pid = $pid;
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('setPID(): '.$this->pid, 'tx_dam_indexing');
	}


	/**
	 * Set dry run
	 *
	 * @param	boolean		$dryRun If set indexed data will not be written to db
	 * @return	void
	 */
	function setDryRun($dryRun=TRUE)	{
		$this->dryRun = $dryRun;

		if ($this->writeDevLog) 	t3lib_div::devLog('setDryRun(): '.($this->dryRun?'true':'false'), 'tx_dam_indexing');
	}


	/**
	 * Enable reindexing
	 * Modes:
	 * 1 = overwrite empty fields
	 * 2 = overwrite fields but preserve data when no new data is available
	 * 99 = overwrite index completely
	 * @todo use constants
	 *
	 * @param	integer		$doReindexing If set already indexed files will be reindexed. Value defines mode
	 * @return	void
	 */
	function enableReindexing($doReindexing=1)	{
		$this->doReindexing = $doReindexing;
		$this->ruleConf['tx_damindex_rule_doReindexing']['enabled'] = $doReindexing;
		$this->ruleConf['tx_damindex_rule_doReindexing']['mode'] = $doReindexing;
		if ($this->writeDevLog) 	t3lib_div::devLog('enableReindexing(): '.($this->doReindexing?'true':'false'), 'tx_dam_indexing');
	}


	/**
	 * Will set main options automatically from $this->ruleConf: doReindexing, dryRun, setRecursive
	 *
	 * @return	void
	 */
	function setOptionsFromRules()	{
		if ($this->writeDevLog) 	t3lib_div::devLog('setOptionsFromRules()', 'tx_dam_indexing');
		$this->enableReindexing(($this->ruleConf['tx_damindex_rule_doReindexing']['enabled'] ? intval($this->ruleConf['tx_damindex_rule_doReindexing']['mode']) : false));
		$this->setRecursive($this->ruleConf['tx_damindex_rule_recursive']['enabled']);
		$this->setDryRun($this->ruleConf['tx_damindex_rule_dryRun']['enabled']);
	}


	/**
	 * Get dry run status
	 *
	 * @return	boolean		If true this is a dry run
	 */
	function isDryRun()	{
		return $this->dryRun;
	}



	/***************************************
	 *
	 *	Setup re-storing functions
	 *
	 ***************************************/


	/**
	 * Returns a serialized setup
	 *
	 * @param	mixed		Any extra data that should be stored with the setup
	 * @param	boolean		If set the setup will returned as array and not serialized
	 * @return	string		serialized setup
	 */
	function serializeSetup($extraSetup='', $serializeData=true) {
		$setup = array(
			'pid' => $this->pid,
			'pathlist' => $this->pathlist,
			'recursive' => $this->recursive,
			'ruleConf' => $this->ruleConf,
			'dataPreset' => $this->dataPreset,
			'dataPostset' => $this->dataPostset,
			'dataAppend' => $this->dataAppend,
			'dryRun' => $this->dryRun,
			'doReindexing' => $this->doReindexing,
			'collectMeta' => $this->collectMeta,
			'extraSetup' => $extraSetup,
			);

		if ($this->writeDevLog) 	t3lib_div::devLog('serializeSetup', 'tx_dam_indexing', 0, $setup);

		return $serializeData ? t3lib_div::array2xml($setup) : $setup;
	}


	/**
	 * Restore a serialized setup
	 *
	 * @param	mixed		setup as string (serialized setup) or array
	 * @return	boolean		True if the restored setup seems to be ok and not garbage
	 */
	function restoreSerializedSetup($setup) {
		$isValid = false;

		$setup = is_array($setup) ? $setup : t3lib_div::xml2array($setup);

			// do a simple check if the setup is a valid one
#		if(is_array($setup) AND isset($setup['pid']) AND is_array($setup['pathlist'])) {
		if(is_array($setup)) {
			$isValid = true;

			if ($this->writeDevLog) 	t3lib_div::devLog('restoreSerializedSetup: valid', 'tx_dam_indexing', 0, $setup);

			$this->pid = $setup['pid'];
			$this->pathlist = $setup['pathlist'];

			$this->recursive = $setup['recursive'];
			$this->ruleConf = $setup['ruleConf'];
			$this->dataPreset = $setup['dataPreset'];
			$this->dataPostset = $setup['dataPostset'];
			$this->dataAppend = $setup['dataAppend'];
			$this->dryRun = $setup['dryRun'];
			$this->enableReindexing($setup['doReindexing']);
			$this->collectMeta = $setup['collectMeta'];

			$this->extraSetup = $setup['extraSetup'];

			if (!is_array($this->ruleConf)) $this->ruleConf = array();
			if (!is_array($this->dataPreset)) $this->dataPreset = array();
			if (!is_array($this->dataPostset)) $this->dataPostset = array();
			if (!is_array($this->dataAppend)) $this->dataAppend = array();

		} else {
			if ($this->writeDevLog) 	t3lib_div::devLog('restoreSerializedSetup: invalid', 'tx_dam_indexing', 1, $setup);
		}

		return $isValid;
	}


	/**
	 * Returns extra setup data that was stored with a serialized setup
	 *
	 * @return	mixed		Any extra data that was stored with the setup
	 */
	function getExtraSetup() {
		return $this->extraSetup;
	}







	/***************************************
	 *
	 *	Setting/searching for default setup, eg. from file setup
	 *
	 ***************************************/



	/**
	 * Fetches the nearest indexing setup in filesystem.
	 *
	 * @param 	string 		$path Path to search for indexing setup
	 * @param 	boolean 	$walkUp If set it will be searched for indexing setup in folders above the given
	 * @param 	string 		$basePath This absolute path is the limit for searching with $walkUp
	 * @return	string 		Setup file content
	 */
	function findSetupInPath($path, $walkUp=true, $basePath='') {

		$fileName = '.indexing.setup.xml';

		$path = tx_dam::path_makeAbsolute($path);
		$filepath = tx_dam::tools_findFileInPath($fileName, $path, $walkUp, $basePath);

		if ($this->writeDevLog) 	t3lib_div::devLog('findSetupInPath(): '.($filepath?'true':'false'), 'tx_dam_indexing', 0, $filepath);

		return $filepath;
	}


	/**
	 * Initialize indexing with a default set
	 *
	 * @param 	string 		$path If set this is the path to search for indexing setup
	 * @param 	boolean 	$walkUp If set it will be searched for indexing setup in folders above the given
	 * @param 	string 		$basePath This path is the limit for searching with $walkUp
	 * @return void
	 */
	function setDefaultSetup($path=false, $walkUp=true, $basePath='') {
		global $TYPO3_CONF_VARS;

		$setup = false;
		
		if ($path) {
			
			if (!$basePath) {
				if ($GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath'] && t3lib_div::isFirstPartOfStr($path,$GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath'])) {
					$basePath = $TYPO3_CONF_VARS['BE']['lockRootPath'];
				} else {
					$basePath = PATH_site;
				}
			}

			$setup = $this->findSetupInPath($path, $walkUp, $basePath);
		}

		if ($setup) {
			$this->restoreSerializedSetup($setup);
			if ($this->writeDevLog) 	t3lib_div::devLog('setDefaultSetup: used setup from folder', 'tx_dam_indexing', 0, $setup);

		} else {
			$this->restoreSerializedSetup($this->defaultSetup);
			if ($this->writeDevLog) 	t3lib_div::devLog('setDefaultSetup: used default setup', 'tx_dam_indexing', 0, $this->defaultSetup);
		}
	}



	/***************************************
	 *
	 *	Main indexing functions
	 *
	 ***************************************/


	/**
	 * Start an indexing process from the current setup
	 *
	 * @param	mixed		$callbackFunc Callback function for the finished indexed file.
	 * @param	mixed		$metaCallbackFunc Callback function which will be called during indexing to allow modifications to the meta data.
	 * @param	mixed		$filePreprocessingCallbackFunc Callback function for pre processing the to be indexed file.
	 * @return	void
	 */
	function indexUsingCurrentSetup($callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)	{
		
		if(is_array($this->pathlist) AND count($this->pathlist) AND $this->pid) {
			if ($this->writeDevLog) 	t3lib_div::devLog('indexUsingCurrentSetup: pathlist and pid, alright', 'tx_dam_indexing', 0, $this->pathlist);

			$files = $this->collectFilesByPathList($this->pathlist, $this->recursive);
			return $this->indexFiles($files, $this->pid, $callbackFunc, $metaCallbackFunc, $filePreprocessingCallbackFunc);

		} else {
			if ($this->writeDevLog) 	t3lib_div::devLog('indexUsingCurrentSetup: no pathlist or pid', 'tx_dam_indexing', 1, $this->serializeSetup());
		}
	}


	function getFilePath($pathname) {
		return is_array($pathname) ? $pathname['processFile'] : $pathname;
	}
	
	
	function getMetaFilePath($pathname) {
		return is_array($pathname) ? $pathname['metaFile'] : false;
	}
	

	/**
	 * Index files passed as array in format from getFilesInDir()
	 *
	 * @param	array		$files Array of file paths. The values can be file path or array: array('processFile' => 'path to file that should be indexed', 'metaFile' => 'additional file that holds meta data for the file to be indexed')
	 * @param	integer		$pid The PID where the records will be stored
	 * @param	mixed		$callbackFunc Callback function for the finished indexed file.
	 * @param	mixed		$metaCallbackFunc Callback function which will be called during indexing to allow modifications to the meta data.
	 * @param	mixed		$filePreprocessingCallbackFunc Callback function for pre processing the to be indexed file.
	 * @return	array		Info array about indexed files and meta data records.
	 * @see getFilesInDir()
	 */
	function indexFiles($files, $pid=NULL, $callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)	{

		if (is_array($files) && count($files)) {
			if ($this->writeDevLog) 	t3lib_div::devLog('indexFiles(): have files to index', 'tx_dam_indexing', 0, $files);

			$this->setIndexRun();

			$this->statBegin();

			$pid = is_null($pid) ? $this->pid : $pid;

			$this->initEnabledRules();

			foreach($this->rules as $ruleId => $setup)	{
				$this->rules[$ruleId]['obj']->preIndexing();
			}

			foreach($files as $key => $pathname) {

				if ($this->writeDevLog) 	t3lib_div::devLog('indexFiles(): '.$this->getFilePath($pathname), 'tx_dam_indexing');

// TODO search for default setup for THIS file path
// cache path setup in array
				$meta = $this->indexFile($pathname, $this->indexRun, $pid, $metaCallbackFunc, $filePreprocessingCallbackFunc);

				if($meta AND $callbackFunc) {
					call_user_func ($callbackFunc, 'postTrigger', $meta, $this->getFilePath($pathname), $key, $this);
					if ($this->writeDevLog) 	t3lib_div::devLog('indexFiles(): call_user_func: '.@get_class($callbackFunc[0]).'->'.$callbackFunc[1].' (postTrigger)', 'tx_dam_indexing');
				}

			}

			foreach($this->rules as $ruleId => $setup)	{
				$this->rules[$ruleId]['obj']->postIndexing($this->infoList);
			}

			$this->statEnd();

			if($this->stat['newIndexed']) {
				$this->log ('New files indexed', $this->stat['newIndexed'], 0);
			}
			if($this->stat['reIndexed']) {
				$this->log ('Files reindexed', $this->stat['reIndexed'], 0);
			}
		}
		return $this->infoList;
	}


	/**
	 * Detects if this file should be skipped while indexing
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return boolean
	 */
	function skipThisFile($fileInfo) {
		if (in_array($fileInfo['file_extension'], $this->skipFileTypes)) {
			return true;
		}
		return false;
	}


	/**
	 * Indexing a single file.
	 * Use indexUsingCurrentSetup() or indexFiles() instead.
	 *
	 * @param	string		$filepath: file path or array: array('processFile' => 'path to file that should be indexed', 'metaFile' => 'additional file that holds meta data for the file to be indexed')
	 * @param	integer		$crdate: timestamp of the index run
	 * @param	integer		$pid: The sysfolder to store the meta data record
	 * @param	mixed		$metaCallbackFunc Will be called to process the meta data
	 * @param	mixed		$filePreprocessingCallbackFunc Will be called to allow preprocessing of the file before indexing
	 * @param	array		$metaPreset: Meta data preset. $meta['fields'] has the record data.
	 * @return	array		Meta data array. $meta['fields'] has the record data.
	 */
	function indexFile($filepath, $crdate=0, $pid=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL, $metaPreset=array())	{

		global $TYPO3_CONF_VARS;
		
		$pathname = $this->getFilePath($filepath);
		$pathname = tx_dam::file_absolutePath($pathname);


			// locks the indexing for the current file
			// If the file is currently indexed this will return false
		if (!$this->lock($pathname)) return FALSE;


		$pid = is_null($pid) ? $this->pid : $pid;

		if ($filePreprocessingCallbackFunc) {
			call_user_func ($filePreprocessingCallbackFunc, 'filePreprocessing', $pathname, $this);

			if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): call filePreprocessingCallbackFunc', 'tx_dam_indexing', 0, $filePreprocessingCallbackFunc);
		}

		// might be possible to have $pathname call by reference and change the filename - usable for copying files before indexing??? Needs to be tested.
		// Answer: Note that the parameters for call_user_func() are not passed by reference.

		$meta = $this->getFileNodeInfo($pathname, true);
		if ($metaFile = $this->getMetaFilePath($filepath)) {
			$meta['metaFile'] = $metaFile;
		}

		if ($this->skipThisFile($meta['file'])) {

			unset($meta);
			$this->log ('Skipped file: '.$pathname, 1, 0);
			if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): file skipped: '.$pathname, 'tx_dam_indexing');

		} elseif (is_array($meta)) {
			if ($this->writeDevLog) 	t3lib_div::devLog('indexFile() - got file node info: '.$pathname, 'tx_dam_indexing', 0, $meta);

			if ($this->writeDevLog AND $metaPreset) 	t3lib_div::devLog('indexFile: use meta preset', 'tx_dam_indexing', 0, $metaPreset);
			$meta = t3lib_div::array_merge_recursive_overrule($metaPreset, $meta);

			$status = tx_dam::index_check ($meta['fields'], $meta['fields']['file_hash']);
			$uid = intval($status['meta']['uid']);

			if ($uid) {
				if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): file already indexed (uid:'.$uid.')', 'tx_dam_indexing', 0, $status);

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam', 'uid='.intval($uid));
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

				if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): fetch index data for reindexing', 'tx_dam_indexing', 0, $row);

					// this is needed for fields like group/MM
				require_once (PATH_t3lib.'class.t3lib_transferdata.php');
				$processData = t3lib_div::makeInstance('t3lib_transferData');
				$row = $processData->renderRecordRaw('tx_dam', $row['uid'], $row['pid'], $row);

				if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): call t3lib_transferdata->renderRecordRaw() for data from index', 'tx_dam_indexing', 0, $row);

					// index rule use 'row' for merging
				$meta['row'] = $row;
				$meta['indexExist'] = true;
				$meta['reindexed'] = $this->doReindexing;
			} else {
				$uid = 'NEW';
				$meta['indexExist'] = false;
				$meta['reindexed'] = false;
			}

// TODO handle TXDAM_file_missing and reconnect file to index

			if (($status['__status'] == TXDAM_file_unknown) OR (($status['__status'] > TXDAM_file_unknown) AND $this->doReindexing)) {

				$mimeType = array();
				$mimeType['fields'] = $this->getFileMimeType($pathname);

				$meta = t3lib_div::array_merge_recursive_overrule(array('fields' => $this->getDefaultRecord()), $meta);
				$meta = t3lib_div::array_merge_recursive_overrule($meta, $mimeType);

				$meta['fields']['uid'] = $uid;
				$meta['fields']['pid'] = $pid;

				$meta['fields']['index_type'] = $this->indexRunType;

				$meta = $this->getFileMetaInfo($pathname, $meta);

				if($meta['textExtract']) {
					$meta['textExtract'] = $this->processTextExcerpt($meta['textExtract']);
				} else {
					$meta['textExtract'] = $this->getFileTextExcerpt($pathname, $meta['fields']['file_type']);
				}

				$meta['fields']['search_content'] = $meta['textExtract'];

				$meta['fields']['abstract'] = $meta['fields']['abstract'] ? $meta['fields']['abstract'] : trim($meta['fields']['search_content']);


				$meta['fields']['language'] = $this->getMetaLanguage($meta);


				$meta['fields']['file_dl_name'] = $meta['fields']['file_dl_name'] ? $meta['fields']['file_dl_name'] : $meta['fields']['file_name'];

				$meta['fields']['crdate'] = $crdate ? $crdate : time();
				$meta['fields']['tstamp'] = time();
				$meta['fields']['cruser_id'] = intval($GLOBALS['BE_USER']->user['uid']);

				$meta['fields']['date_cr'] = $meta['fields']['date_cr'] ? $meta['fields']['date_cr'] : time();
				$meta['fields']['date_mod'] = $meta['fields']['date_mod'] ? $meta['fields']['date_mod'] : $meta['fields']['date_cr'];

// TODO category handling - merging?

			# $fieldsUpdated = tx_dam_db::getUpdateData($meta['fields'], $this->replaceData, $this->appendData);

				foreach ($this->dataPreset as $field => $value) {
					if ($value AND !$meta['fields'][$field]) {
						$meta['fields'][$field] = $value;
					}
				}

				$fieldsUpdated = tx_dam_db::getUpdateData($meta['fields'], $this->dataPostset, $this->dataAppend);
				$meta['fields'] = array_merge($meta['fields'], $fieldsUpdated);


				$meta = $this->rulesCallback('process', $meta, $pathname);
				if ($metaCallbackFunc) {
					$meta = call_user_func ($metaCallbackFunc, 'process', $meta, $pathname, $this);
					if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): call_user_func: '.@get_class($metaCallbackFunc[0]).'->'.$metaCallbackFunc[1].' (process)', 'tx_dam_indexing', 0, $meta);

				}

				$meta['failure'] = false;
				if (!$this->dryRun) {

					if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): call tx_dam_db::insertUpdateData()', 'tx_dam_indexing', 0, $meta['fields']);

					$meta['fields']['uid'] = tx_dam_db::insertUpdateData($meta['fields']);

					if (!intval($meta['fields']['uid'])) {
						list($error, $errorMsg) = tx_dam_db::getLastError();
						$meta['failure'] = 'Meta record could not be inserted: '.$errorMsg;
						$this->log ('Meta record could not be inserted: '.$pathname.'. '.$errorMsg, 1, 1);
					} else {
						if ($this->writeDevLog) 	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam', 'uid='.intval($meta['fields']['uid']));
						if ($this->writeDevLog) 	$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
						if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): written to index', 'tx_dam_indexing', 0, $row);
					}
				}

				if(intval($meta['fields']['uid'])) {
					$meta['isIndexed'] = true;
					$this->infoList[] = array(
						'uid' => $meta['fields']['uid'],
						'title' => $meta['fields']['title'],
						'file_name' => $meta['fields']['file_name'],
						'file_path' => $meta['fields']['file_path'],
						'reindexed' => $meta['reindexed'],
						);
					$this->stat['newIndexed'] += ($meta['reindexed'] ? 0 : 1);
					$this->stat['reIndexed'] += ($meta['reindexed'] ? 1 : 0);
				}

				$meta = $this->rulesCallback('post', $meta, $pathname);
				if ($metaCallbackFunc) {
					$meta = call_user_func ($metaCallbackFunc, 'post', $meta, $pathname, $this);
					if ($this->writeDevLog) 	t3lib_div::devLog('indexFile(): call_user_func: '.@get_class($metaCallbackFunc[0]).'->'.$metaCallbackFunc[1].' (post)', 'tx_dam_indexing', 0, $meta);
				}

				$currentUid = intval($meta['fields']['uid']) ? $meta['fields']['uid'] : '_NO_UID_'.(string)(intval($this->noIdCounter++));
				if ($this->collectMeta) {
					$this->meta[$currentUid] = $meta;
				}


					// todo: indexing of childs to this file - eg. images from a OpenOffice file
				if (is_array($meta['childs'])) {
					foreach ($meta['childs'] as $fileDef) {
						$pathname = $fileDef['pathname'];

						if (file_exists($pathname)) {
							if ($meta['fields']['file_hash'] AND $fileDef['fileStorageType'] === 'moveToInternal') {
								$storageFolder = PATH_site.'uploads/tx_dam/storage/'.$meta['fields']['file_hash'].'/';
								$targetFile = $storageFolder.tx_dam::file_basename($pathname);
								if(!is_dir($storageFolder)) {
									t3lib_div::mkdir ($storageFolder);
								}
								@unlink($targetFile);
								rename($pathname, $targetFile);
								$pathname = $targetFile;
							}
							$metaPreset = is_array($fileDef['metaPreset']) ? $fileDef['metaPreset'] : array();
							$metaPreset['fields']['parent_id'] = $currentUid;
							$this->indexFile($pathname, $crdate, $pid, $metaCallbackFunc, $filePreprocessingCallbackFunc, $metaPreset);
						}
					}
				}

				$this->statMeta($meta);

				$this->unlock();
				
				return $meta;


			} elseif (is_array($meta['row'])) {
				$meta['fields'] = $meta['row'];

				$this->statMeta($meta);

				$this->unlock();

				return $meta;
			}

		} else {
			if(!@is_file($pathname)) {
				$this->log ('Is not a file: '.$pathname, 1, 1);

			} elseif (!@is_readable($pathname)) {
				$this->log ('Is not readable: '.$pathname, 1, 1);
			}
		}

		$this->unlock();

		return FALSE;
	}




	/***************************************
	 *
	 *	 Indexing rules
	 *
	 ***************************************/


	/**
	 * Merge options from rule forms ($data['rules'])
	 *
	 * @param	array		$ruleOpt: ...
	 * @return	void
	 */
	function mergeRuleConf($ruleOpt='') {

		if ($this->writeDevLog AND $ruleOpt) 	t3lib_div::devLog('mergeRuleConf() conf passed', 'tx_dam_indexing', 0, $ruleOpt);

		$this->initAvailableRules();

		foreach($this->rules as $ruleId => $setup)	{

			if (is_array($ruleOpt) AND is_array($ruleOpt[$ruleId])) {
					// this is set in the class itself
				unset($ruleOpt[$ruleId]['shy']);
				unset($ruleOpt[$ruleId]['forceEnabled']);
				$this->rules[$ruleId]['obj']->setup = t3lib_div::array_merge_recursive_overrule($this->rules[$ruleId]['obj']->setup, $ruleOpt[$ruleId]);
			} else {
				$this->rules[$ruleId]['obj']->setup = t3lib_div::array_merge_recursive_overrule($this->rules[$ruleId]['obj']->setup, $this->ruleConf[$ruleId]);
			}
			$this->rules[$ruleId]['obj']->processOptionsForm();
			$this->ruleConf[$ruleId] = $this->rules[$ruleId]['obj']->setup;
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('mergeRuleConf()', 'tx_dam_indexing', 0, $this->ruleConf);
	}


	/**
	 * Initialize the available indexing rules.
	 * Creates the objects and init the objects with the user defined setup.
	 *
	 * @return	void
	 */
	function initEnabledRules() {
		global $TYPO3_CONF_VARS;

		$this->initAvailableRules();

		$this->rules = array();
		if (is_array($this->ruleConf))	{
			foreach($this->ruleConf as $ruleId => $setup)	{

				if (($setup['enabled'] OR $setup['forceEnabled']) AND is_object($obj = &t3lib_div::getUserObj($TYPO3_CONF_VARS['EXTCONF']['dam']['indexRuleClasses'][$ruleId],'user_',TRUE)))      {

					$this->rules[$ruleId]['obj'] = &$obj;
					if (is_array($this->ruleConf[$ruleId])) {
						$this->rules[$ruleId]['obj']->setup = array_merge($this->rules[$ruleId]['obj']->setup, $this->ruleConf[$ruleId]);
					}
					$this->rules[$ruleId]['shy'] = $this->rules[$ruleId]['obj']->setup['shy'];
				}
			}
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('initEnabledRules()', 'tx_dam_indexing', 0, $this->rules);
	}


	/**
	 * Initialize the available indexing rules
	 *
	 * @return	void
	 */
	function initAvailableRules() {
		global $TYPO3_CONF_VARS;

		if (is_array($this->rules) AND count($this->rules)) {
				// init already done
			return;
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('initAvailableRules()', 'tx_dam_indexing', 0, $TYPO3_CONF_VARS['EXTCONF']['dam']['indexRuleClasses']);

		$this->rules=array();

		if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam']['indexRuleClasses']))	{
			foreach($TYPO3_CONF_VARS['EXTCONF']['dam']['indexRuleClasses'] as $ruleId => $classfile)	{

				if ($this->writeDevLog) 	t3lib_div::devLog('initAvailableRules(): '.$ruleId.' '.$classfile, 'tx_dam_indexing');

				if (is_object($obj = &t3lib_div::getUserObj($classfile)))      {
					
					$obj->writeDevLog = $this->writeDevLog;

						// this is set in the class itself
					unset($this->ruleConf[$ruleId]['shy']);

					$this->rules[$ruleId]['obj'] = $obj;


					if (is_array($this->ruleConf[$ruleId])) {
						$this->rules[$ruleId]['obj']->setup = array_merge($this->rules[$ruleId]['obj']->setup, $this->ruleConf[$ruleId]);
					} else {
						$this->ruleConf[$ruleId] = $this->rules[$ruleId]['obj']->setup;
					}

						// visible
					$this->rules[$ruleId]['shy'] = $this->rules[$ruleId]['obj']->setup['shy'];
				}
			}
		}
	}


	/**
	 * Calls indexing rules
	 *
	 * @param	string		$type: "process" calls processMeta() and "post" postProcessMeta()
	 * @param	array		$meta     file meta information which should be extended
	 * @param	string		$pathname file with absolut path
	 * @return	array		file meta information
	 */
	function rulesCallback ($type, $meta, $pathname) {

		if ($this->writeDevLog) 	t3lib_div::devLog('rulesCallback(): '.$type, 'tx_dam_indexing', 0);

		if (is_array($this->rules)) {
			foreach($this->rules as $rule)	{
				switch ($type) {

					case 'process':
					default:
						if(is_callable(array($rule['obj'], 'processMeta'))) {
							if ($this->writeDevLog) 	t3lib_div::devLog('rulesCallback(): '.get_class($rule['obj']).'->processMeta', 'tx_dam_indexing', 0, $meta);
							$meta = $rule['obj']->processMeta($meta, $pathname, $this);
						}
					break;

					case 'post':
						if(is_callable(array($rule['obj'], 'postProcessMeta'))) {
							if ($this->writeDevLog) 	t3lib_div::devLog('rulesCallback(): '.get_class($rule['obj']).'->postProcessMeta', 'tx_dam_indexing', 0, $meta);
							$meta = $rule['obj']->postProcessMeta($meta, $pathname, $this);
						}
					break;
				}
			}
			if ($this->writeDevLog) 	t3lib_div::devLog('rulesCallback(): finished', 'tx_dam_indexing', 0, $meta);
		}
		return $meta;
	}





	/***************************************
	 *
	 *	 Collecting file meta data
	 *
	 ***************************************/


	/**
	 * get meta information from a file using the metaExtract service
	 *
	 * @param	string		file with absolut path
	 * @param	array		file meta information which should be extended
	 * @return	array		file meta information
	 * @todo what about using services in a chain?
	 */
	function getFileMetaInfo($pathname, $meta)	{
		global $TYPO3_CONF_VARS;
		

		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];

		$conf = array();
		$conf['wantedCharset'] = $this->getWantedCharset();

		if (is_file($pathname) && is_readable($pathname)) {

			$fileType = $meta['fields']['file_type'];


			if ($this->setup['useInternalMediaTypeList']) {
					// get media type from file type
				$meta['fields']['media_type'] = $TX_DAM['file2mediaCode'][$fileType];
					//  or from mime type
				$meta['fields']['media_type'] = $meta['fields']['media_type'] ? $meta['fields']['media_type'] :  tx_dam::convert_mediaType($meta['fields']['file_mime_type']);
	
			} else {
				$meta['fields']['media_type'] = tx_dam::convert_mediaType($meta['fields']['file_mime_type']);
			}
			
			$mediaType = tx_dam::convert_mediaType($meta['fields']['media_type']);

				// find a service for that file type
			if (!is_object($serviceObj = t3lib_div::makeInstanceService('metaExtract', $fileType))) {
					// find a global service for that media type
				$serviceObj = t3lib_div::makeInstanceService('metaExtract', $mediaType.':*');
			}
			if (is_object($serviceObj)) {
				$serviceObj->setInputFile($pathname, $fileType);
				$conf['meta'] = $meta;
				if ($serviceObj->process('', '', $conf) > 0 AND (is_array($svmeta = $serviceObj->getOutput()))) {
						$meta = t3lib_div::array_merge_recursive_overrule($meta, $svmeta);
				}
				$serviceObj->__destruct();
				unset($serviceObj);
			}



				// make simple image size detection if not yet done
			if ($meta['fields']['media_type'] == TXDAM_mtype_image AND intval($meta['fields']['hpixels']) == 0) {
				$imgsize = $this->getImageDimensions ($pathname, $meta);
				$meta = t3lib_div::array_merge_recursive_overrule($meta, $imgsize);
			}


			$metaExtractServices = array();
			$extraServiceTypes = array();
			
			if (!isset($meta['fields']['meta']['EXIF']) AND !$meta['exif_done']) {
				$metaExtractServices[TXDAM_mtype_image][] = 'image:exif';
			}
			if ((!isset($meta['fields']['meta']['IPTC']) AND !$meta['iptc_done']) AND (!isset($meta['fields']['meta']['XMP']) AND !$meta['xmp_done'])) {
				$metaExtractServices[TXDAM_mtype_image][] = 'image:iptc';
			}
			if ($extraServiceTypes) {
				$metaExtractServices[TXDAM_mtype_image] = t3lib_div::array_merge($metaExtractServices[TXDAM_mtype_image], implode(', ', $extraServiceTypes));
			}
			
// TODO should be possible to register other services too?!

					// read exif, iptc data
			if (is_array($metaExtractServices[$meta['fields']['media_type']])) {
				foreach ($metaExtractServices[$meta['fields']['media_type']] as $subType) {
					if ($serviceObj = t3lib_div::makeInstanceService('metaExtract', $subType)) {

						$serviceObj->setInputFile($pathname, $fileType);
						$conf['meta'] = $meta;
						if ($serviceObj->process('','',$conf)>0 AND (is_array($svmeta = $serviceObj->getOutput()))) {

							$meta = t3lib_div::array_merge_recursive_overrule($meta, $svmeta);

						}
						$serviceObj->__destruct();
						unset($serviceObj);
					}
				}
			}

				// convert extra meta data to xml
			if (is_array($meta['fields']['meta'])) {
					// content in array is expected as utf-8 because of xml functions
				$meta['fields']['meta'] = $this->array2xml($meta['fields']['meta']);
			}

			// If no title then the file-name is set as title. This will raise the hits considerably if the search matches the document name.
			if ($meta['fields']['title']=='')	{
				$meta['fields']['title']= $this->makeTitleFromFilename ($meta['fields']['file_name']);
			}

			$meta['fields']['keywords'] = $this->listBeautify($meta['fields']['keywords']);

		}

		if ($this->writeDevLog) 	t3lib_div::devLog('getFileMetaInfo()', 'tx_dam_indexing', 0, $meta);

		return $meta;
	}


	/**
	 * detect the language of the files text excerpt using the textLang service
	 *
	 * @param	array		file meta information which should be extended
	 * @return	string		language iso code
	 */
	function getMetaLanguage($meta)	{
		global $TYPO3_CONF_VARS;
		
		$language = '';

		if ($meta['fields']['search_content'] AND is_object($serviceObj = t3lib_div::makeInstanceService('textLang'))) {
			if ($this->writeDevLog) 	t3lib_div::devLog('getMetaLanguage() call service : '.$serviceObj->getServiceTitle().' ('.$serviceObj->getServiceKey().')', 'tx_dam_indexing');
			$serviceObj->process($meta['fields']['search_content']);
			$language = $serviceObj->getOutput();
			$serviceObj->__destruct();
			unset($serviceObj);
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('getMetaLanguage(): '.$language, 'tx_dam_indexing');

		return $language;
	}


	/**
	 * get basic file meta info
	 *
	 * @param	string		$pathname absolute path to file
	 * @param	boolean		$calcHash if true a hash of the file will be created
	 * @return	array		file information
	 */
	function getFileNodeInfo($pathname, $calcHash=false)	{

		$meta=false;

		$fileInfo = tx_dam::file_compileInfo ($pathname);

		if (is_array($fileInfo) && $fileInfo['__exists']) {
			$meta = array();
			if($calcHash) {
				$fileInfo['file_hash'] = tx_dam::file_calcHash($fileInfo);
			}
			$meta['fields'] = $fileInfo;
			$meta['file'] = $fileInfo;
		}

		if ($this->writeDevLog AND $meta) 	t3lib_div::devLog('getFileNodeInfo()', 'tx_dam_indexing', 0, $fileInfo);
		if ($this->writeDevLog AND !$meta) 	t3lib_div::devLog('getFileNodeInfo() failed', 'tx_dam_indexing', 1, $fileInfo);

		return $meta;
	}


	/**
	 * get the mime type of a file with full path
	 *
	 * @param	string		$pathname absolute path to file
	 * @return	array		file information
	 */
	function getFileMimeType($pathname)	{

			// this will be called from tx_dam therefore $pathname can be a fileInfo array
		$pathname = tx_dam::file_absolutePath($pathname);

		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];

		$mimeType = array();
		$mimeType['fulltype'] = '';
		$mimeType['file_mime_type'] = '';
		$mimeType['file_mime_subtype'] = '';
		$mimeType['file_type'] = '';

		$path_parts = t3lib_div::split_fileref($pathname);
			
		$mimeType['file_type'] = strtolower($path_parts['realFileext']);
			// cleanup bakup files extension
		$mimeType['file_type'] = preg_replace('#\~$#', '', $mimeType['file_type']);

		$this->setup['useInternalMimeList'] = tx_dam::config_checkValueEnabled('setup.indexing.useInternalMimeList', true);
		$this->setup['useMimeContentType'] = tx_dam::config_checkValueEnabled('setup.indexing.useMimeContentType', true);
		$this->setup['useFileCommand'] = tx_dam::config_checkValueEnabled('setup.indexing.useFileCommand', true);



			// try first to get the mime type by extension with own array
			// I made the experience that it is a bit safer than with 'file'
		if ($this->setup['useInternalMimeList'] AND $mimeType['file_type'] AND isset($TX_DAM['file2mime'][$mimeType['file_type']])) {

			$mt = $TX_DAM['file2mime'][$mimeType['file_type']];
			if ($this->writeDevLog) 	t3lib_div::devLog('getFileMimeType(): used builtin conversion table', 'tx_dam_indexing');

			// next try
		} elseif($this->setup['useMimeContentType'] AND function_exists('mime_content_type')) {
				// available in PHP 4.3.0
			$mt = mime_content_type($pathname);
			if ($this->writeDevLog) 	t3lib_div::devLog('getFileMimeType(): used mime_content_type()', 'tx_dam_indexing');
		} 
		
			// last chance
		if ($this->setup['useFileCommand'] AND (!$mt OR $mt==='application/octet-stream')) {
			$osType = TYPO3_OS;
			if (!($osType === 'WIN')) {

				if($cmd = t3lib_exec::getCommand('file')) {
					$dummy = array();
					$ret = false;
					$mimeTypeTxt = trim (exec($cmd.' --mime '.escapeshellarg($pathname), $dummy, $ret));
					if (!$ret AND strstr ($mimeTypeTxt,tx_dam::file_basename($pathname).':')) {
						$a = explode (':', $mimeTypeTxt);
						$a = explode (';', trim($a[1]));
						//a[1]: text/plain, English; charset=iso-8859-1
						$a = explode (',', trim($a[0]));
						$a = explode (' ', trim($a[0]));
						$mt = trim($a[0]);
					}
				}
			}
			if ($this->writeDevLog) 	t3lib_div::devLog('getFileMimeType(): used t3lib_exec::getCommand(\'file\')', 'tx_dam_indexing');
		}

		$mtarr = explode ('/', $mt);
		if (is_array($mtarr) && count($mtarr)==2) {

			$mimeType['fulltype'] = $mt;
			$mimeType['file_mime_type'] = $mtarr[0];
			$mimeType['file_mime_subtype'] = $mtarr[1];
		}

		if ($mimeType['file_type'] == '') {
			$mimeType['file_type'] = array_search($mimeType['fulltype'], $TX_DAM['file2mime'], true);
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('getFileMimeType()', 'tx_dam_indexing', 0, $mimeType);

		unset($mimeType['fulltype']);

		return $mimeType;
	}


	/**
	 * get an excerpt from a text file using the textExtract service
	 *
	 * @param	string		file with absolut path
	 * @param	string		file type like 'jpg'
	 * @param	integer		limits the lenght of the text excerpt to $limit bytes
	 * @return	string		text excerpt of false
	 */
	function getFileTextExcerpt($pathname, $file_type, $limit=64000) {
		global $TYPO3_CONF_VARS;
		

		$textExcerpt = FALSE;

		if (is_object($serviceObj = t3lib_div::makeInstanceService('textExtract',$file_type))) {
			if ($this->writeDevLog) 	t3lib_div::devLog('getFileTextExcerpt() call service : '.$serviceObj->getServiceTitle().' ('.$serviceObj->getServiceKey().')', 'tx_dam_indexing');

			$conf = array();

			if ($limit) {
				$conf['limitOutput'] = $limit+3000;
			}
			$conf['wantedCharset'] = $this->getWantedCharset();

			$serviceObj->setInputFile($pathname, $file_type);
			$serviceObj->process('', '', $conf);
			$textExcerpt = $this->processTextExcerpt($serviceObj->getOutput());

			unset($serviceObj);
		}
		return $textExcerpt;
	}


	/**
	 * get the charset that is used for storage of meta data
	 *
	 * @return	string		charset eg utf-8 or iso-8859-1
	 */
	function getWantedCharset() {
		global $TYPO3_CONF_VARS;

		$wantedCharset = $TYPO3_CONF_VARS['BE']['forceCharset'] ? $TYPO3_CONF_VARS['BE']['forceCharset'] : 'iso-8859-1';
		return $wantedCharset;
	}


	/**
	 * get an excerpt from a text file using the textExtract service
	 *
	 * @param	string		content
	 * @param	integer		limits the lenght of the text excerpt to $limit bytes
	 * @return	string		text excerpt
	 */
	function processTextExcerpt($textExcerpt, $limit=64000) {

		$textExcerpt = trim($textExcerpt);

			// double linebreak is enough
		while (strpos($textExcerpt, "\n\n\n")) {
			$textExcerpt = str_replace("\n\n\n", "\n\n", $textExcerpt);
		}
		if ($limit) {
			$textExcerpt = substr($textExcerpt, 0, $limit);
		}

		return $textExcerpt;
	}


	/**
	 * get the image size of an file in pixels
	 *
	 * @param	string		$pathnamefile with absolut path
	 * @param	array		$metaInfo
	 * @return	array
	 * @todo use service?
	 */
	function getImageDimensions($pathname, $metaInfo=array()) {
		$meta = array();

		if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['im'])	{
			$frame = $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_noFramePrepended']?'':'[0]';
			$format = 'magic:%m \npage_size:%P \npage_geometry:%g \nsize:%wx%h \nx-resolution:%x \ny-resolution:%y \nimage_depth:%z \nquantum_depth:%q \ncolorspace:%r \nbounding_box:%@';
			$cmd = t3lib_div::imageMagickCommand('identify', '-format '.escapeshellarg($format).' '.escapeshellarg($pathname).$frame);
			$output = array();
			exec($cmd, $output);
			foreach($output as $line)	{
				list($key, $value) = explode(':', $line);
				$value = trim($value);
				if ($value) {
					switch (trim($key)) {
						case 'size':
									$size = explode('x', $value);
									if (intval($size[0]) && intval($size[1]))	{
										$meta['fields']['hpixels'] = $size[0];
										$meta['fields']['vpixels'] = $size[1];
									}
							break;
						case 'x-resolution':
									$res = explode(' ', $value);
									$meta['fields']['hres'] = intval($res[0]);
							break;
						case 'y-resolution':
									$res = explode(' ', $value);
									$meta['fields']['vres'] = intval($res[0]);
							break;
						case 'colorspace':
									if (preg_match('#[a-z]+Class[a-z]+#i', $value, $matches)) {
										switch ($matches[0]) {
											case 'DirectClassGray':
											case 'DirectClassGrayMatte':
												$meta['fields']['color_space'] = 'grey';
												break;
											case 'DirectClassCMYK':
												$meta['fields']['color_space'] = 'CMYK';
												break;
											case 'PseudoClassRGB':
												$meta['fields']['color_space'] = 'indx';
												break;

											case 'DirectClassRGB':
											case 'DirectClassRGBMatte':
											default:
												$meta['fields']['color_space'] = 'RGB';
												break;
										}
									}
							break;

						default:
							break;
					}
				}
			}
		}

		if(!$meta['fields']['hpixels'] AND function_exists('getimagesize')) {
			$size = @getImageSize($pathname);
			$meta['fields']['hpixels'] = $size[0];
			$meta['fields']['vpixels'] = $size[1];
			$meta['fields']['hres'] = 72;
			$meta['fields']['vres'] = 72;
			if ($metaInfo['fields']['file_type'] === 'gif') {
				$meta['fields']['color_space'] = 'indx';
			} else {
				$meta['fields']['color_space'] = 'RGB';
			}
		}

		if (!$meta['fields']['height_unit'] AND $meta['fields']['hres'] AND $meta['fields']['vres']) {
			$meta['fields']['height_unit'] = 'mm';
			$meta['fields']['width'] = intval(round($meta['fields']['hpixels']/$meta['fields']['hres']*25.4));
			$meta['fields']['height'] = intval(round($meta['fields']['vpixels']/$meta['fields']['vres']*25.4));
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('getImageDimensions(): '.$meta['fields']['hpixels'].'x'.$meta['fields']['vpixels'], 'tx_dam_indexing', 0, $meta);

		return $meta;
	}


	/**
	 * Gets default record.
	 *
	 * @param	string		Database Tablename
	 * @return	array		"default" row.
	 */
	function getDefaultRecord($table='tx_dam')	{
		global $TCA;

		$row = array();
		if ($TCA[$table])	{
			t3lib_div::loadTCA($table);

			foreach($TCA[$table]['columns'] as $field => $info)	{
				if (isset($info['config']['default']))	{
					$row[$field] = $info['config']['default'];
				}
			}
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('getDefaultRecord()', 'tx_dam_indexing', 0, $row);

		return $row;
	}


	/**
	 * convert/cleans a file name to be more usable as title
	 *
	 * @param	string		Filename or similar
	 * @return	string		Title string
	 */
	function makeTitleFromFilename ($title) {
		$orgTitle = $title;
		$extpos = strrpos($title,'.');
		$title = $extpos ? substr($title, 0, $extpos) : $title; // remove extension
		$title = str_replace('_',' ',$title);	// Substituting "_" for " " because many filenames may have this instead of a space char.
		$title = str_replace('%20',' ',$title);
			// studly caps: add spaces
		$title = preg_replace('#([a-z])([A-Z])#', '\\1 \\2', $title);

		if ($this->writeDevLog) 	t3lib_div::devLog('makeTitleFromFilename(): '.$orgTitle.' > '.$title, 'tx_dam_indexing');

		return $title;
	}


	/**
	 * Removes emty entries from a comma list
	 *
	 * @param	string		$list: comma list
	 * @return	string		cleaned list
	 */
	function listBeautify($list) {
		if (!is_array($list)) {
			$list = t3lib_div::trimExplode(',', $list, 1);
		}
		return implode(',', $list);
	}





	/***************************************
	 *
	 *	 Files, folders and paths
	 *
	 ***************************************/


	/**
	 * Returns an array with the names of files in a specific path
	 *
	 * @param	string		Path to start to collect files
	 * @param	boolean		Go recursive into subfolder?
	 * @param	array		Array of file paths
	 * @param	integer		$maxDirs limit the read directories
	 * @return	array		Array of file paths
	 */
	function getFilesInDir($path, $recursive=FALSE, $filearray=array(), $maxDirs=999)	{
		if ($path)	{
			$absPath = tx_dam::path_makeAbsolute($path);
			$d = @dir($absPath);
			if (is_object($d))	{
				while($entry=$d->read()) {
					if (@is_file($absPath.$entry))	{
						if (!preg_match('/^\./',$entry) && !preg_match('/~$/',$entry)) {
							$key = md5($absPath.$entry);
							$filearray[$key] = $absPath.$entry;
						}
					} elseif ($recursive && $maxDirs>0 && @is_dir($absPath.$entry) && !preg_match('/^\./',$entry) && $entry!='CVS')	{
						$filearray = $this->getFilesInDir($absPath.$entry, true, $filearray, $maxDirs-1);
					}
				}
				$d->close();
			}
		}
		return $filearray;
	}


	/**
	 * Returns an array with the names of files in a specific path
	 *
	 * @param	string		Path to start to collect files. If it is a file itself it will be added to the list too
	 * @param	boolean		Go recursive into subfolder?
	 * @param	array		Array of file paths
	 * @return	array		Array of file paths
	 */
	function collectFiles($path, $recursive=false, $filearray=array())	{
		if ($path) {

			$pathname = $this->getFilePath($path);
			$pathname = tx_dam::file_absolutePath($path);

			if(@is_file($pathname))	{
				if ($metaFile = $this->getMetaFilePath($path)) {
					$filearray[md5($pathname)] = array('processFile' => $pathname, 'metaFile' => $metaFile);
				} else {
					$filearray[md5($pathname)] = $pathname;
				}
			} else {
				$filearray = $this->getFilesInDir($path, $recursive, $filearray);
			}
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('collectFiles(): '.$path, 'tx_dam_indexing', 0, $filearray);

		return $filearray;
	}


	/**
	 * Returns an array with files collected from a list (array) of paths and files
	 *
	 * @param	array		Path/file list
	 * @param	boolean		Go recursive into subfolder?
	 * @return	array		Array of file paths
	 */
	function collectFilesByPathList($pathlist, $recursive)	{
		$filearray = array();

		foreach($pathlist as $path) {
			$filearray = $this->collectFiles($path, $recursive, $filearray);
		}

		if ($this->writeDevLog) 	t3lib_div::devLog('collectFilesByPathList()', 'tx_dam_indexing', 0, $pathlist);

		return $filearray;
	}




	/*******************************************************
	 *
	 * Rendering the option form and info
	 *
	 *******************************************************/


	/**
	 * Returns the form of indexing options
	 *
	 * @return	string HTML content
	 */
	function getIndexingOptionsForm() {
			// walk through the index rules
		$this->initAvailableRules();
		$optContent='';
		foreach($this->rules as $ruleId => $setup)	{
			$options = $this->rules[$ruleId]['obj']->getOptionsForm();
			$optContent .= $this->formatOptionsFormRow ('[rules]['.$ruleId.']',
										$this->rules[$ruleId]['obj']->setup,
										$this->rules[$ruleId]['obj']->getTitle(),	$this->rules[$ruleId]['obj']->getDescription(),
										$options);
		}
		return $optContent;
	}

// todo: move section to own class?

	/**
	 * Returns the info of indexing options that are activated
	 *
	 * @return	string HTML content
	 */
	function getIndexingOptionsInfo() {
			// walk through the index rules
		$optContent = '';
		$this->initEnabledRules();
		foreach($this->rules as $ruleId => $setup)	{

			if(!$this->rules[$ruleId]['shy']) {
				$optContent .= $this->formatOptionsFormRow ('info',
											array(),
											$this->rules[$ruleId]['obj']->getTitle(),	'',
											$this->rules[$ruleId]['obj']->getOptionsInfo());
			}
		}
		return $optContent;
	}


	/**
	 * Returns a row of indexing options
	 *
	 * @param	string		$varname: ...
	 * @param	array		$setup: ...
	 * @param	string		$title: ...
	 * @param	string		$desc: ...
	 * @param	string		$options: ...
	 * @return	string HTML content
	 */
	function formatOptionsFormRow ($varname, $setup, $title, $desc='', $options='') {

		$out = '';
		$tdone='';

		$enabled = $setup['enabled'];

		if($setup['shy']) {
			$out .= '<input type="hidden" name="data'.$varname.'[enabled]" value="'.($enabled?'1':'0').'" />';
		} else {
			$out .= '<tr bgcolor="'.$GLOBALS['SOBE']->doc->bgColor5.'">';

			if($varname!='info') {

				$tdone='<td>&nbsp;</td>';
				$out .= '<td bgcolor="'.$GLOBALS['SOBE']->doc->bgColor4.'" width="1%"><input type="hidden" name="data'.$varname.'[enabled]" value="0" />'.
					'<input type="checkbox" name="data'.$varname.'[enabled]"'.($enabled?' checked="checked"':'').' value="1" />'.
					'</td>';
			}

			$out .= '<td bgcolor="'.$GLOBALS['SOBE']->doc->bgColor5.'"><strong>'.$title.'</strong></td>'.
				'</tr>';

			if($desc) {
				$out .= '
				<tr>'.$tdone.'<td bgcolor="'.$GLOBALS['SOBE']->doc->lgBgColor5.'">'.$desc.'</td></tr>';
			}

			if($options) {
				$out .= '
				<tr>'.$tdone.'<td bgcolor="'.$GLOBALS['SOBE']->doc->bgColor3.'" style="border-bottom:2px '.$GLOBALS['SOBE']->doc->bgColor5.' solid;">'.$options.'</td></tr>';
			}

			$out .= '<tr height="5" bgcolor="'.$GLOBALS['SOBE']->doc->bgColor.'">'.$tdone.'<td></td></tr>';
		}
		return $out;
	}




	/***************************************
	 *
	 *	 XML <> Array
	 *
	 ***************************************/



	/**
	 * Converts a PHP array into an XML string.
	 *
	 * The XML output is optimized for readability since associative keys are used as tagnames.
	 * This also means that only alphanumeric characters are allowed in the tag names AND only keys NOT starting with numbers (so watch your usage of keys!). However there are options you can set to avoid this problem.
	 * Numeric keys are stored with the default tagname "numIndex" but can be overridden to other formats)
	 * The function handles input values from the PHP array in a binary-safe way; All characters below 32 (except 9,10,13) will trigger the content to be converted to a base64-string
	 * The PHP variable type of the data IS preserved as long as the types are strings, arrays, integers and booleans. Strings are the default type unless the "type" attribute is set.
	 *
	 * @param	array		The input PHP array with any kind of data; text, binary, integers. Not objects though.
	 * @param	array		Options for the compilation. Key "useNindex" => 0/1 (boolean: whether to use "n0, n1, n2" for num. indexes); Key "useIndexTagForNum" => "[tag for numerical indexes]"; Key "useIndexTagForAssoc" => "[tag for associative indexes"; Key "parentTagMap" => array('parentTag' => 'thisLevelTag')
	 * @param	integer		Current recursion level. Don't change, stay at zero!
	 * @param	string		Stack data. Don't touch.
	 * @return	string		An XML string made from the input content in the array.
	 * @see xml2array()
	 */
	function array2xml($array, $options=array(), $level=0, $stackData=array())	{
		$docTag='phparray';
		// tag-prefix, eg. a namespace prefix like "T3:"
		$NSprefix='';
		// If set, the number of spaces corresponding to this number is used for indenting, otherwise a single chr(9) (TAB) is used
		$spaceInd=0;

			// The list of byte values which will trigger binary-safe storage. If any value has one of these char values in it, it will be encoded in base64
		$binaryChars = chr(0).chr(1).chr(2).chr(3).chr(4).chr(5).chr(6).chr(7).chr(8).
						chr(11).chr(12).chr(14).chr(15).chr(16).chr(17).chr(18).chr(19).
						chr(20).chr(21).chr(22).chr(23).chr(24).chr(25).chr(26).chr(27).chr(28).chr(29).
						chr(30).chr(31);
			// Set indenting mode:
		$indentChar = $spaceInd ? ' ' : chr(9);
		$indentN = $spaceInd>0 ? $spaceInd : 1;

			// Init output variable:
		$output='';

			// Traverse the input array
		if (is_array($array))	{
			foreach($array as $k=>$v)	{
				$attr = '';
				$tagName = $k;

					// Construct the tag name.
				if(isset($options['grandParentTagMap'][$stackData['grandParentTagName'].'/'.$stackData['parentTagName']])) {		// Use tag based on grand-parent + parent tag name
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = (string)$options['grandParentTagMap'][$stackData['grandParentTagName'].'/'.$stackData['parentTagName']];
				}elseif(isset($options['parentTagMap'][$stackData['parentTagName'].':_IS_NUM']) && t3lib_div::testInt($tagName)) {		// Use tag based on parent tag name + if current tag is numeric
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = (string)$options['parentTagMap'][$stackData['parentTagName'].':_IS_NUM'];
				}elseif(isset($options['parentTagMap'][$stackData['parentTagName'].':'.$tagName])) {		// Use tag based on parent tag name + current tag
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = (string)$options['parentTagMap'][$stackData['parentTagName'].':'.$tagName];
				} elseif(isset($options['parentTagMap'][$stackData['parentTagName']])) {		// Use tag based on parent tag name:
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = (string)$options['parentTagMap'][$stackData['parentTagName']];
				} elseif (!strcmp(intval($tagName),$tagName))	{	// If integer...;
					if ($options['useNindex']) {	// If numeric key, prefix "n"
						$tagName = 'n'.$tagName;
					} else {	// Use special tag for num. keys:
						$attr.=' index="'.$tagName.'"';
						$tagName = $options['useIndexTagForNum'] ? $options['useIndexTagForNum'] : 'numIndex';
					}
				} elseif($options['useIndexTagForAssoc']) {		// Use tag for all associative keys:
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = $options['useIndexTagForAssoc'];
				}

					// The tag name is cleaned up so only alphanumeric chars (plus -_) are in there and not longer than 100 chars either.
				$tagName = str_replace(':','-',$tagName);
				$tagName = substr(preg_replace('/[^[:alnum:]_-]/', '', $tagName), 0, 100);

					// If the value is an array then we will call this function recursively:
				if (is_array($v))	{

						// Sub elements:
					if ($options['alt_options'][$stackData['path'].'/'.$tagName])	{
						$subOptions = $options['alt_options'][$stackData['path'].'/'.$tagName];
						$clearStackPath = $subOptions['clearStackPath'];
					} else {
						$subOptions = $options;
						$clearStackPath = FALSE;
					}

					$content = chr(10).
								$this->array2xml(
									$v,
									$subOptions,
									$level+1,
									array(
										'parentTagName' => $tagName,
										'grandParentTagName' => $stackData['parentTagName'],
										'path' => $clearStackPath ? '' : $stackData['path'].'/'.$tagName,
									)
								).
								str_pad('',($level+1)*$indentN,$indentChar);
					if ((int)$options['disableTypeAttrib']!=2)	{	// Do not set "type = array". Makes prettier XML but means that empty arrays are not restored with xml2array
						$attr.=' type="array"';
					}
				} else {	// Just a value:

						// Look for binary chars:
					if (strcspn($v,$binaryChars) != strlen($v))	{	// Go for base64 encoding if the initial segment NOT matching any binary char has the same length as the whole string!
							// If the value contained binary chars then we base64-encode it an set an attribute to notify this situation:
						$content = chr(10).chunk_split(base64_encode($v));
						$attr.=' base64="1"';

					} elseif(preg_match('#(<xml|<rdf|xmlns:)#', $v)) {
						// just embed XML
						$content = '<xml>'.chr(10).$v.chr(10).'</xml>';

					} else {
							// Otherwise, just htmlspecialchar the stuff:
						$content = htmlspecialchars($v);
						$dType = gettype($v);
						if ($dType!='string' && !$options['disableTypeAttrib'])	{ $attr.=' type="'.$dType.'"'; }
					}
				}

					// Add the element to the output string:
				$output.=str_pad('',($level+1)*$indentN,$indentChar).'<'.$NSprefix.$tagName.$attr.'>'.$content.'</'.$NSprefix.$tagName.'>'.chr(10);
			}
		}

			// If we are at the outer-most level, then we finally wrap it all in the document tags and return that as the value:
		if (!$level)	{

				// Figure out charset if not given explicitly:
			if (!$charset = $options['charset'])	{
				if ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'])	{	// First priority: forceCharset! If set, this will be authoritative!
					$charset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];
				} elseif (is_object($GLOBALS['LANG']))	{
					$charset = $GLOBALS['LANG']->charSet;	// If "LANG" is around, that will hold the current charset
				} else {
					$charset = 'utf-8';	// THIS is just a hopeful guess!
				}
			}

			$output = '<?xml version="1.0" encoding="'.htmlspecialchars($charset).'" standalone="yes" ?>'.chr(10).
				'<'.$docTag.'>'.chr(10).
				$output.
				'</'.$docTag.'>';
		}

		return $output;
	}



	/***************************************
	 *
	 *	 Collect some stats
	 *
	 ***************************************/


	/**
	 * Init statistics
	 *
	 * @return	void
	 */
	function statBegin() {
		$this->statmtime = t3lib_div::milliseconds();
		$this->stat['totalStartTime'] = $this->stat['totalStartTime'] ? $this->stat['totalStartTime'] : $this->statmtime;
		$this->stat['newIndexed'] = 0;
		$this->stat['reIndexed'] = 0;

		if ($this->writeDevLog) 	t3lib_div::devLog('statBegin() :'.$this->stat['totalStartTime'], 'tx_dam_indexing', 0, $this->stat);
	}

	/**
	 * Add item to statistics
	 *
	 * @param	array		$meta: Meta data
	 * @return	void
	 */
	function statMeta($meta) {
		$this->statmtime = t3lib_div::milliseconds()-$this->statmtime;
		$this->stat['totalTime'] = t3lib_div::milliseconds()-$this->stat['totalStartTime'];

		$this->stat['mediaTypeCount'][$meta['fields']['media_type']]++;
		$this->stat['mediaTypeTime'][$meta['fields']['media_type']] += $this->statmtime;
		if($meta['fields']['search_content']) {
			$this->stat['textExtract']++;
		}
		$this->stat['totalCount']++;

		if ($this->writeDevLog) 	t3lib_div::devLog('statMeta(): '.$this->stat['totalTime'], 'tx_dam_indexing', 0, $this->stat);
	}

	/**
	 * End statistics
	 *
	 * @return	void
	 */
	function statEnd() {
		$this->stat['totalTime'] = t3lib_div::milliseconds()-$this->stat['totalStartTime'];

		if ($this->writeDevLog) 	t3lib_div::devLog('statMeta(): '.$this->stat['totalTime'], 'tx_dam_indexing', 0, $this->stat);
	}

	/**
	 * Clear statistics
	 *
	 * @return	void
	 */
	function statClear() {
		$this->stat = array();

		if ($this->writeDevLog) 	t3lib_div::devLog('statClear()', 'tx_dam_indexing');
	}






	/************************************
	 *
	 * Locking
	 *
	 ************************************/


	/**
	 * Obtain exclusive lock
	 *
	 * @param		string		Filename which we want to lock
	 * @return		bool		Success or failure of operation
	 */
	function lock($filename) {
		try {
			if (!is_object($this->fileLock)) {
				if (t3lib_div::compat_version('4.3')) {
					$this->fileLock = t3lib_div::makeInstance('t3lib_lock', 'tx_dam_indexing_' . md5($filename), 
						$GLOBALS['TYPO3_CONF_VARS']['SYS']['lockingMode'], 60, 10);
				} else {
					$className = t3lib_div::makeInstanceClassName('t3lib_lock');
					$this->fileLock = new $className('tx_dam_indexing_' . md5($filename), 
						$GLOBALS['TYPO3_CONF_VARS']['SYS']['lockingMode'], 60, 10);
				}
			}

			$success = false;
			if (is_object($this->fileLock)) {
					// true = Page could get locked without blocking
					// false = Page could get locked but process was blocked before
				$success = $this->fileLock->acquire() || $GLOBALS['TYPO3_CONF_VARS']['SYS']['lockingMode'] == 'disable';
				if ($this->fileLock->getLockStatus()) {
					if ($this->writeDevLog) 	t3lib_div::devLog('lock(): lock aquired ' . $filename, 'tx_dam_indexing');
				} else {
					if ($this->writeDevLog) 	t3lib_div::devLog('lock(): lock failed ' . $filename, 'tx_dam_indexing');
				}
			}
		} catch (Exception $e) {
			if ($this->writeDevLog) 	t3lib_div::devLog('lock(): Exception caught ' . $e->getMessage(), 'tx_dam_indexing');
		}
		return $success;
	}


	/**
	 * Release lock
	 *
	 * @return void
	 */
	function unlock() {
		if (is_object($this->fileLock)) {
			try {		
				$this->fileLock->release();
				if ($this->writeDevLog) 	t3lib_div::devLog('unlock()', 'tx_dam_indexing');
				unset ($this->fileLock);
			} catch (Exception $e) {
				if ($this->writeDevLog) 	t3lib_div::devLog('unlock(): failed to unlock: ' . $e->getMessage(), 'tx_dam_indexing');
			}
		}
	}



	/************************************
	 *
	 * Logging
	 *
	 ************************************/


	/**
	 * Writes an entry in the logfile
	 *
	 * @param	integer		$indexRun: The time stamp of the index run
	 * @param	string		$type: man(ual), auto, cron
	 * @param	string		$message: short description
	 * @param	integer		$itemCount: number of elements indexed (is 1 for error entry)
	 * @param	integer		$error: flag. 0 = message, 1 = error (user problem), 2 = System Error (which should not happen)
	 * @return	integer		uid of the inserted log entry
	 */
	function writeLog($indexRun, $type, $message, $itemCount, $error) {

		$fields_values = array (
			'pid' => intval($this->pid),
			'cruser_id' => intval($GLOBALS['BE_USER']->user['uid']),
			'tstamp' => $GLOBALS['EXEC_TIME'],
			'crdate' => intval($indexRun),
			'error' => intval($error),
			'type' => substr($type, 0, 4),
			'message' => $message,
			'item_count' => intval($itemCount),
		);

		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_log_index', $fields_values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}

	/**
	 * Writes an entry in the logfile
	 *
	 * @param	string		$message: short description
	 * @param	integer		$itemCount: number of elements indexed (is 1 for error entry)
	 * @param	integer		$error: flag. 0 = message, 1 = error (user problem), 2 = System Error (which should not happen)
	 * @return	integer		uid of the inserted log entry
	 */
	function log($message, $itemCount, $error) {
		if (!$this->dryRun) {
		return $this->writeLog($this->indexRun, $this->indexRunType, $message, $itemCount, $error);
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexing.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexing.php']);
}


 ?>
