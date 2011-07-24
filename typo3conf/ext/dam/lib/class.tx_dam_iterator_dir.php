<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   83: class tx_dam_iterator_dir extends tx_dam_iterator_base
 *  138:     function tx_dam_iterator_dir()
 *  148:     function __construct()
 *
 *              SECTION: Iterator functions
 *  166:     function rewind()
 *  176:     function valid()
 *  187:     function next()
 *  198:     function seek($offset)
 *  211:     function key()
 *  221:     function current()
 *  235:     function count ()
 *
 *              SECTION: allow/Exclude functions
 *  254:     function resetAllowExclude ()
 *  276:     function allowByRegex ($allow, $ignoreCase=true)
 *  296:     function excludeByRegex ($exclude, $ignoreCase=true)
 *  312:     function allowByFileTypes ($allow)
 *  327:     function excludeByFileTypes ($exclude)
 *
 *              SECTION: Reading/sorting directory
 *  352:     function read($path, $allowTypes='file')
 *  424:     function sort($sortBy='', $sortReverse=false)
 *
 * TOTAL FUNCTIONS: 16
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_iterator_base.php');




/**
 * Collect data for a file list and provides iterator.
 * Files and folders can be read at the same time but sorting will not work then!
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
class tx_dam_iterator_dir extends tx_dam_iterator_base {


	/**
	 * Stores the file/dir entries.
	 */
	var $entries = array();

	/**
	 * Used for sorting the entries.
	 */
	var $sorting = array();

	/**
	 * Used to define the current entry.
	 */
	var $currentKey = 0;

	/**
	 * Used to count the total bytes for files.
	 * Excluded files are not included in count.
	 */
	var $countBytes = 0;

	/**
	 * if set files will be checked if index anf if not it will be done in place
	 */
	var $enableAutoIndexing = false;

	/**
	 * if set >0 autindexing will stop after the amount of files
	 */
	var $maxAutoIndexingItems = 0;
	
	/**
	 * List of allow regex
	 *
	 * @access private
	 */
	var $allowRegex = array();

	/**
	 * List of allowed file types
	 *
	 * @access private
	 */
	var $allowFileTypes = array();

	/**
	 * List of exclude regex
	 *
	 * @access private
	 */
	var $excludeRegex = array();



	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_iterator_dir() {
		$this->__construct();
	}


	/**
	 * Constructor
	 *
	 * @return	void
	 */
	function __construct() {
		$this->resetAllowExclude();
	}



	/***************************************
	 *
	 *	 Iterator functions
	 *
	 ***************************************/


	/**
	 * Set the internal pointer to its first element.
	 *
	 * @return	void
	 */
	function rewind() {
		reset($this->sorting);
	}


	/**
	 * Return true is the current element is valid.
	 *
	 * @return	boolean
	 */
	function valid() {
		$key = key($this->sorting);
		return isset($key);
	}


	/**
	 * Advance the internal pointer
	 *
	 * @return	void
	 */
	function next() {
		next($this->sorting);
	}


	/**
	 * Set the internal pointer to the offset
	 *
	 * @param	integer		$offset
	 * @return	void
	 */
	function seek($offset) {
		$this->rewind();
		for ($index = 0; $index < $offset; $index++) {
			$this->next();
		}
	}


	/**
	 * Return the pointer to the current element
	 *
	 * @return	mixed
	 */
	function key() {
		return key($this->sorting);
	}


	/**
	 * Return the current element
	 *
	 * @return	array
	 */
	function current() {
		$this->currentData = $this->entries[$this->key()];
		if (is_callable($this->conf['callbackCurrentData'])) {
			call_user_func ($this->conf['callbackCurrentData'], $this);
		}
		return $this->currentData;
	}


	/**
	 * Count elements
	 *
	 * @return	integer
	 */
	function count () {
		return count($this->entries);
	}




	/***************************************
	 *
	 *	 allow/Exclude functions
	 *
	 ***************************************/


	/**
	 * Reset the allow
	 *
	 * @return	void
	 */
	function resetAllowExclude () {
		$this->allowRegex = array();
		$this->excludeRegex = array();
		$this->allowFileTypes = array();
		$this->excludeFileTypes = array();

			// always exclude dot directories
		$this->excludeRegex[] = '/^\.$/';
		$this->excludeRegex[] = '/^\.\.$/';
	}


	/**
	 * Add allow as regex (PCRE)
	 *
	 * example:
	 * $allow='mp[23]', $ignoreCase=true: '/mp3[23]/i'
	 *
	 * @param	mixed		$allow List for matching allow files. Is array or comma list.
	 * @param	boolean		$ignoreCase If set character case will be ignored
	 * @return	void
	 */
	function allowByRegex ($allow, $ignoreCase=true) {
		$allow = is_array($allow) ? $allow : explode(',', $allow);
		$ignoreCase = $ignoreCase ? 'i' : '';

		foreach ($allow as $key => $expr) {
			$this->allowRegex[] = '/'.$expr.'/'.$ignoreCase;
		}
	}


	/**
	 * Add exclude as regex (PCRE)
	 *
	 * example:
	 * $allow='php[345]', $ignoreCase=true: '/php[345]/i'
	 *
	 * @param	mixed		$exclude List for matching exclude files. Is array or comma list.
	 * @param	boolean		$ignoreCase If set character case will be ignored
	 * @return	void
	 */
	function excludeByRegex ($exclude, $ignoreCase=true) {
		$exclude = is_array($exclude) ? $exclude : explode(',', $exclude);
		$ignoreCase = $ignoreCase ? 'i' : '';

		foreach ($exclude as $key => $expr) {
			$this->excludeRegex[] = '/'.$expr.'/'.$ignoreCase;
		}
	}


	/**
	 * Add allow as file type (txt, mp3, ...)
	 *
	 * @param	mixed		$allow List for matching allow file types. Is array or comma list.
	 * @return	void
	 */
	function allowByFileTypes ($allow) {
		$allow = is_array($allow) ? $allow : explode(',', $allow);

		foreach ($allow as $fileType) {
			$this->allowFileTypes[] = $fileType;
		}
	}


	/**
	 * Add exclude as file type (html, php, ...)
	 *
	 * @param	mixed		$exclude List for matching exclude file types. Is array or comma list.
	 * @return	void
	 */
	function excludeByFileTypes ($exclude) {
		$exclude = is_array($exclude) ? $exclude : explode(',', $exclude);

		foreach ($exclude as $fileType) {
			$this->excludeFileTypes[] = $fileType;
		}
	}




	/***************************************
	 *
	 *	 Reading/sorting directory
	 *
	 ***************************************/


	/**
	 * Returns an array with file/dir items + an array with the sorted items
	 *
	 * @param	string		Path (absolute) to read
	 * @param	mixed		$allowTypes List or array of allow directory entry types: file, dir, link. Empty is all kinds of stuff.
	 * @return	void
	 */
	function read($path, $allowTypes='file')	{

		$allowTypes = is_array($allowTypes) ? $allowTypes : t3lib_div::trimExplode(',', $allowTypes, true);

		if($path)	{
			$tempArray = array();


			$path = tx_dam::path_makeAbsolute($path);

			if (is_object($d = @dir($path))) {
				while($entry = $d->read()) {
					$filepath = $path.$entry;

						// check for allow file types: eg. file, dir, link
					if (@file_exists($filepath) && (!$allowTypes || in_array(($type=filetype($filepath)), $allowTypes)))	{

							// if filename matches exclude list this file is skipped
						foreach ($this->excludeRegex as $expr) {
							if(preg_match($expr, $entry)) {
								continue 2;
							}
						}

							// if filename don't matches allow list this file is skipped
						foreach ($this->allowRegex as $expr) {
							if(!preg_match($expr, $entry)) {
								continue 2;
							}
						}

						if($type === 'file') {
							$fileInfo = tx_dam::file_compileInfo($filepath);

							if (is_array($meta = tx_dam::meta_getDataForFile($fileInfo))) {
									// the newer stat data will be merged over the stored meta data
								$fileInfo = array_merge($meta, $fileInfo);
							}
							else {
								$mimeType = tx_dam::file_getType($filepath);
								$fileInfo = array_merge($fileInfo, $mimeType);
							}

							if (count($this->excludeFileTypes) AND in_array($fileInfo['file_type'], $this->excludeFileTypes)) {
								continue;
							}

							if (count($this->allowFileTypes) AND !in_array($fileInfo['file_type'], $this->allowFileTypes)) {
								continue;
							}
							
							if ($this->enableAutoIndexing) {
								$this->autoIndex($fileInfo);
							}
						}
						elseif($type === 'dir' OR $type === 'link') {
							$fileInfo = tx_dam::path_compileInfo($filepath);
						}
							// the file is valid so we add it to the list
						$this->entries[] = $fileInfo;
						$this->countBytes += $fileInfo['file_size'];
					}
				}
				$d->close();
				$this->sort();
				$this->rewind();
			}
		}
	}



	/**
	 * Sort the collected file list by a fileInfo field.
	 * HINT: sorting will not work when file and dir entries exist both: dir_mtime/file_mtime!!
	 *
	 * @param	string		$sortBy Field name of a fileInfo array. If empty/false the sorting will be set to default.
	 * @param	boolean		$sortReverse If set the sorting will be reversed.
	 * @return	void
	 */
	function sort($sortBy='', $sortReverse=false) {

		$this->sorting = array();

		foreach($this->entries as $fileInfo)	{
			if ($sortBy)	{
				$this->sorting[] = strtoupper($fileInfo[$sortBy]);
			} else {
				$this->sorting[] = '';
			}
		}
			// Sort if required
		if ($sortBy)	{
			if ($sortReverse)	{
				arsort($this->sorting);
			} else {
				asort($this->sorting);
			}
		}
		$this->rewind();
	}


	/**
	 * Processes auto indexing if the file is not yet indexed
	 * 
	 * @param array $item
	 */
	function autoIndex(&$item) {
		static $indexed = 0;

		if ($this->maxAutoIndexingItems AND ($indexed >= $this->maxAutoIndexingItems)) return;

			// we don't index indexing setup files
		if ($item['file_name'] === '.indexing.setup.xml') {

		} elseif(!($uid = tx_dam::file_isIndexed($item))) {
			if ($metaRow = tx_dam::index_autoProcess($item)) {
				$item = $metaRow['fields'];
				$item['__isIndexed'] = true;
				$indexed ++;
			}
		}
	}

}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_dir.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_dir.php']);
}
?>