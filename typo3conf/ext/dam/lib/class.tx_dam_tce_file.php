<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skaarhoj (kasper@typo3.com)
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
 * Part of the DAM (digital asset management) extension.
 *
 * TCE (TYPO3 Core Engine) file-handling
 * This script serves as the fileadministration part of the TYPO3 Core Engine.
 * Basically it includes two libraries which are used to manipulate files on the server.
 *
 * For syntax and API information, see the document 'TYPO3 Core APIs'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Core
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  882: class tx_dam_extFileFunctions extends ux_t3lib_extFileFunctions
 *  894:     function initBE($mounts=NULL, $f_ext=NULL)
 *
 *
 *  923: class tx_dam_tce_file
 *  943:     function init($file='')
 *  979:     function overwriteExistingFiles($overwriteExistingFiles)
 *  990:     function setCmdmap($fileCmds)
 *  999:     function initClipboard()
 * 1023:     function process()
 * 1037:     function errors()
 * 1047:     function getLastError($getFullErrorLogEntry=FALSE)
 *
 * TOTAL FUNCTIONS: 19
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once (PATH_txdam.'binding/tce/class.tx_dam_tce_extfilefunc.php');




/**
 * @ignore
 */
class tx_dam_extFileFunctions extends ux_t3lib_extFileFunctions	{


	/**
	 * This function should be called to initialise the internal arrays $this->mounts and $this->f_ext
	 * In comparison to init() this method initializes for the current BE_USER automatically
	 *
	 * @param	array		Contains the paths of the file mounts for the current BE user. Normally $GLOBALS['FILEMOUNTS'] is passed. This variable is set during backend user initialization; $FILEMOUNTS = $BE_USER->returnFilemounts(); (see typo3/init.php)
	 * @param	array		Array with information about allowed and denied file extensions. Typically passed: $TYPO3_CONF_VARS['BE']['fileExtensions']
	 * @return	void
	 * @see init
	 */
	function initBE($mounts=NULL, $f_ext=NULL)	{
		global $FILEMOUNTS, $TYPO3_CONF_VARS, $BE_USER;

		if ($mounts==NULL) {
			$mounts = $FILEMOUNTS;
		}
		if ($f_ext==NULL) {
			$f_ext = $TYPO3_CONF_VARS['BE']['fileExtensions'];
		}

		$this->init($mounts, $f_ext);

		$this->init_actionPerms(tx_dam::getFileoperationPermissions());
	}
}


/**
 * TCE (TYPO3 Core Engine) file-handling
 *
 * Handling the calling of methods in the file admin classes.
 * This is a modified version for usage with the DAM. Might be merged with the TYPO3 core implementation at some point.
 * Used by the command modules in mod_cmd/.
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Core
 */
class tx_dam_tce_file {

		// Internal, static: GP var
	var $file;						// Array of file-operations.
	var $CB;						// Clipboard operations array
	var $overwriteExistingFiles;	// If existing files should be overridden.

		// Internal, dynamic:
	var $fileProcessor;				// File processor object: tx_dam_extFileFunctions

	var $error = FALSE;

	/**
	 * Registering Incoming data
	 *
	 * @param	array		$file: Command map. Default: t3lib_div::_GP('file')
	 * @return	string	$this->error
	 */
	function init($file='')	{
		global $FILEMOUNTS, $TYPO3_CONF_VARS, $BE_USER;

			// GP vars:
		$this->file = is_array($file) ? $file : t3lib_div::_GP('file');
		$this->overwriteExistingFiles = t3lib_div::_GP('overwriteExistingFiles');

			// Initializing:
		# $this->fileProcessor = t3lib_div::makeInstance('t3lib_extFileFunctions');
		$this->fileProcessor = t3lib_div::makeInstance('tx_dam_extFileFunctions');
		$this->fileProcessor->init($FILEMOUNTS, $TYPO3_CONF_VARS['BE']['fileExtensions']);
		$this->fileProcessor->init_actionPerms(tx_dam::getFileoperationPermissions());
		$this->fileProcessor->dontCheckForUnique = $this->overwriteExistingFiles ? 1 : 0;

		return $this->error;

	}

	/**
	 * Allow files to be overwritten
	 *
	 * @param 	boolean 	$overwriteExistingFiles If set files will be overwritten during upload for example.
	 * @return	void
	 */
	function overwriteExistingFiles($overwriteExistingFiles) {
		$this->fileProcessor->dontCheckForUnique = $overwriteExistingFiles;
	}


	/**
	 * Initializing file processing commands
	 *
	 * @param	array		The $file array with the commands to execute. See "TYPO3 Core API" document
	 * @return	void
	 */
	function setCmdmap($fileCmds)	{
		$this->file = $fileCmds;
	}

	/**
	 * Initialize the Clipboard. This will fetch the data about files to paste/delete if such an action has been sent.
	 *
	 * @return	void
	 */
	function initClipboard()	{
		global $TYPO3_CONF_VARS;

		$this->CB = t3lib_div::_GP('CB');
		
		if (is_array($this->CB))	{
			require_once(PATH_t3lib.'class.t3lib_clipboard.php');
			$clipObj = t3lib_div::makeInstance('t3lib_clipboard');
			$clipObj->initializeClipboard();
			if ($this->CB['paste'])	{
				$clipObj->setCurrentPad($this->CB['pad']);
				$this->file = $clipObj->makePasteCmdArray_file($this->CB['paste'],$this->file);
			}
			if ($this->CB['delete'])	{
				$clipObj->setCurrentPad($this->CB['pad']);
				$this->file = $clipObj->makeDeleteCmdArray_file($this->file);
			}
		}
	}

	/**
	 * Performing the file admin action:
	 * Initializes the objects, setting permissions, sending data to object.
	 *
	 * @return	array $this->fileProcessor->log
	 */
	function process()	{
		if (!$this->error) {
			$this->fileProcessor->start($this->file);
			$this->fileProcessor->processData();
			// should happen in module: t3lib_BEfunc::getSetUpdateSignal('updateFolderTree');
		}
		return $this->fileProcessor->log;
	}

	/**
	 * Check if an error occured while processing
	 *
	 * @return	integer		Number of errors
	 */
	function errors() {
		return $this->fileProcessor->errors();
	}

	/**
	 * Extract the last error message from the log
	 *
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 */
	function getLastError($getFullErrorLogEntry=FALSE) {
		return $this->fileProcessor->getLastError($getFullErrorLogEntry);
	}
	
	/**
	 * Index uploaded files
	 *
	 * @param	array		$fileList: List of files
	 * @return	void
	 */
	function indexUploadedFiles($fileList) {
		global $BACK_PATH, $LANG, $TYPO3_CONF_VARS;

		require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');
		$index = t3lib_div::makeInstance('tx_dam_indexing');
		$index->init();
		//$index->setDefaultSetup(tx_dam::path_makeAbsolute($this->pObj->path));
		$index->enableReindexing(2);
		$index->initEnabledRules();

		$index->setRunType('auto');

		$index->indexFiles($fileList, tx_dam_db::getPid());
	}	
	
	/**
	 * Handles the actual process from within the ajaxExec function
	 * therefore, it does exactly the same as the real typo3/tce_file.php
	 * but without calling the "finish" method, thus makes it simpler to deal with the
	 * actual return value
	 *
	 *
	 * @param string $params 	always empty.
	 * @param string $ajaxObj	The Ajax object used to return content and set content types
	 * @return void
	 */
	public function processAjaxRequest(array $params, TYPO3AJAX $ajaxObj) {
		$this->init();
		$this->process();
		$errors = $this->fileProcessor->getErrorMessages();
		$this->indexUploadedFiles($this->fileProcessor->internalUploadMap);
		
		if (count($errors)) {
			$ajaxObj->setError(implode(',', $errors));
		} else {
			$ajaxObj->addContent('result', $this->fileData);
			if ($this->redirect) {
				$ajaxObj->addContent('redirect', $this->redirect);
			}
			$ajaxObj->setContentFormat('json');
		}
	}	

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tce_file.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tce_file.php']);
}

?>
