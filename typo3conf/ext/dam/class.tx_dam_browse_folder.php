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


//$tempColumns = Array (
//	'paths' => Array (
//		'exclude' => 1,
//		'label' => 'LLL:EXT:dam/lib/locallang.xml:folders',
//		'config' => Array (
//			'type' => 'passthrough',
//			'form_type' => 'user',
//			'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tcefunc->getSingleField_typeFolder',
//			'size' => 5,
//			'minitems' => 0,
//			'maxitems' => 20,
//			'wrap' => 'off',
//			'cols' => '25',
//			'rows' => '4',
//		)
//	),
//);
//
//
//t3lib_div::loadTCA('be_users');
//t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
//t3lib_extMgm::addToAllTCAtypes('be_users','tx_dam_paths', '', 'after:file_mountpoints');
//t3lib_extMgm::addLLrefForTCAdescr('be_users','EXT:dam/locallang_csh.xml');

/**
 * Implements a folder element browser.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 */
class tx_dam_browse_folder extends browse_links {




	/**
	 * Check if this object should be rendered.
	 *
	 * @param	string		$type Type: "file", ...
	 * @param	object		$pObj Parent object.
	 * @return	boolean
	 * @see SC_browse_links::main()
	 */
	function isValid($type, &$pObj)	{
		$isValid = false;

#		$pArr = explode('|', t3lib_div::_GP('bparams'));

		if ($type === 'tx_dam_folder') {
			$isValid = true;
		}

		return $isValid;
	}





	/**
	 * Rendering
	 * Called in SC_browse_links::main() when isValid() returns true;
	 *
	 * @param	string		$type Type: "file", ...
	 * @param	object		$pObj Parent object.
	 * @return	string		Rendered content
	 * @see SC_browse_links::main()
	 */
	function render($type, &$pObj)	{
		global $LANG, $BE_USER;

		$this->pObj = &$pObj;

			// init class browse_links
		$this->init();

		$this->getModSettings();

		$this->processParams();


		$content = '';

		switch((string)$this->mode)	{

			case 'tx_dam_folder':
				$this->act = 'folder'; // unused for now
				$content = $this->main_folder();
			break;
			default:
			break;
		}


			// debug output
		if (false) {

			$bparams = explode('|', $this->bparams);

			$debugArr = array(
				'act' => $this->act,
				'mode' => $this->mode,
				'thisScript' => $this->thisScript,
				'bparams' => $bparams,
				'allowedTables' => $this->allowedTables,
				'allowedFileTypes' => $this->allowedFileTypes,
				'disallowedFileTypes' => $this->disallowedFileTypes,
				'addParams' => $this->addParams,
			);

			$content.=  t3lib_div::view_array($debugArr);

		}

		return $content;
	}




	/**
	 * TYPO3 Element Browser: Showing a folder tree, allowing you to browse for files.
	 *
	 * @return	string		HTML content for the module
	 */
	function main_folder()	{
		global $BE_USER, $TYPO3_CONF_VARS;
		

			// Starting content:
		$content.=$this->doc->startPage('TBE file selector');

			// Init variable:
		$pArr = explode('|',$this->bparams);

			// Create upload/create folder forms, if a path is given:
		$fileProcessor = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
		$path=$this->expandFolder;
		if (!$path || !@is_dir($path))	{
			$path = $fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		if ($path!='/' && @is_dir($path))	{
#			$createFolder=$this->createFolder($path);
		} else {
			$createFolder='';
		}


		$noThumbs = true;

			// Create folder tree:
		$foldertree = t3lib_div::makeInstance('TBE_FolderTree');
		$foldertree->thisScript=$this->thisScript;
		$foldertree->ext_noTempRecyclerDirs = ($this->mode === 'filedrag');
		$tree=$foldertree->getBrowsableTree();

		list(,,$specUid) = explode('_',$this->PM);

		$files = $this->listFolder($foldertree->specUIDmap[$specUid],$pArr[3],$noThumbs);

		$content .= $this->formTag;

			// Putting the parts together, side by side:
		$content.= '

			<!--
				Wrapper table for folder tree / file list:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBfiles">
				<tr>
					<td class="c-wCell" valign="top">'.$this->barheader($GLOBALS['LANG']->getLL('folderTree').':').$tree.'</td>
					<td class="c-wCell" valign="top">'.$files.'</td>
				</tr>
			</table>
			';

			// Adding create folder + upload forms if applicable:
		if ($BE_USER->isAdmin() || $BE_USER->getTSConfigVal('options.createFoldersInEB'))	$content.=$createFolder;

			// Add some space
		$content.='<br /><br />';

			// Ending page, returning content:
		$content.= $this->doc->endPage();
		$content = $this->doc->insertStylesAndJS($content);
		return $content;
	}









	/**
	 * For TYPO3 Element Browser: Expand folder of files.
	 *
	 * @param	string		The folder path to expand
	 * @param	boolean		Whether to show thumbnails or not. If set, no thumbnails are shown.
	 * @return	string		HTML output
	 */
	function listFolder($expandFolder=0,$noThumbs=0)	{
		global $LANG;

		$expandFolder = $expandFolder ? $expandFolder : $this->expandFolder;
		$out='';
		if ($expandFolder && $this->checkFolder($expandFolder))	{
				// Listing the files:
			$folder = $this->getFolderInDir($expandFolder,1,1);	// $prependPath=0,$order='')
			$out.= $this->folderList($folder, $expandFolder, $noThumbs);
		}

			// Return accumulated content for filelisting:
		return $out;
	}

	/**
	 * Render list of files.
	 *
	 * @param	array		List of folder. See $this->getFolderInDir()
	 * @param	string		If set a header with a folder icon and folder name are shown
	 * @param	boolean		Whether to show thumbnails or not. If set, no thumbnails are shown.
	 * @return	string		HTML output
	 */
	function folderList($folder, $folderName='', $noThumbs=0) {
		global $LANG, $BACK_PATH;

		$out='';

			// Listing the files:
		if (is_array($folder))	{

				// Create headline (showing number of files):
			$out.=$this->barheader(sprintf($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:file_newfolder.php.folders').' (%s):',count($folder)));

			$titleLen=intval($GLOBALS['BE_USER']->uc['titleLen']);

			$folderIcon='<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/i/_icon_webfolders.gif','width="18" height="16"');

// todo: use modes?
#			 $fileadminDir = PATH_site.$GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'];


			$fcount = count($folder);

			$folder = array_merge(
					array(md5($folderName) => $folderName),
					$folder
				);

				// Traverse the file list:
			$lines=array();
			foreach($folder as $filepath)	{
				$path_parts = t3lib_div::split_fileref($filepath);


				# $shortFilepath = preg_replace('#^'.preg_quote($fileadminDir).'#','', $filepath);
				$shortFilepath = preg_replace('#^'.preg_quote(PATH_site).'#','', $filepath);

				if (count($lines)==0) {
					$treeLine = '';
				} elseif (count($lines) < $fcount) {
					$LN = 'join';
					$treeLine = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/'.$LN.'.gif','width="18" height="16"').' alt="" />';
				} else {
					$LN = 'joinbottom';
					$treeLine = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/'.$LN.'.gif','width="18" height="16"').' alt="" />';
				}


					// Create folder icon:
				$icon = $folderIcon.' title="'.htmlspecialchars($path_parts['file']).'" class="absmiddle" alt="" />';

					// Create links for adding the file:
				if (strstr($filepath,',') || strstr($filepath,'|'))	{	// In case an invalid character is in the filepath, display error message:
					$eMsg = $LANG->JScharCode(sprintf($LANG->getLL('invalidChar'),', |'));
					$ATag = $ATag_alt = "<a href=\"#\" onclick=\"alert(".$eMsg.");return false;\">";
				} else {	// If filename is OK, just add it:
					$ATag = "<a href=\"#\" onclick=\"return insertElement('','".t3lib_div::shortMD5($filepath)."', 'file', '".rawurlencode($shortFilepath)."', unescape('".rawurlencode($shortFilepath)."'), '".''."', '".''."');\">";
					$ATag_alt = substr($ATag,0,-4).",'',1);\">";
				}
				$ATag_e='</a>';

					// Combine the stuff:
				$filenameAndIcon=$ATag_alt.$icon.htmlspecialchars(t3lib_div::fixed_lgd_cs($path_parts['file'],$titleLen)).$ATag_e;


				$lines[]='
					<tr class="bgColor4">
						<td nowrap="nowrap">'.$treeLine.$filenameAndIcon.'&nbsp;</td>
						<td>'.$ATag.'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/plusbullet2.gif','width="18" height="16"').' title="'.$LANG->getLL('addToList',1).'" alt="" />'.$ATag_e.'</td>
					</tr>';

			}

				// Wrap all the rows in table tags:
			$out.='



		<!--
			File listing
		-->
				<table border="0" cellpadding="0" cellspacing="1" id="typo3-fileList">
					'.implode('',$lines).'
				</table>';
		}

			// Return accumulated content for filelisting:
		return $out;
	}




	/**
	 * Returns an array with the names of files in a specific path
	 * Usage: 18
	 *
	 * @param	string		$path: Is the path to the file
	 * @param	boolean		If set, then the path is prepended the filenames. Otherwise only the filenames are returned in the array
	 * @param	string		$order is sorting: 1= sort alphabetically, 'mtime' = sort by modification time.
	 * @return	array		Array of the files found
	 */
	function getFolderInDir($path,$prependPath=0,$order='')	{

			// Initialize variabels:
		$filearray = array();
		$sortarray = array();
		$path = preg_replace('/\/$/', '', $path);

			// Find files+directories:
		if (@is_dir($path))	{
			$d = dir($path);
			if (is_object($d))	{
				while($entry=$d->read()) {
					if($entry === '.' OR $entry === '..') {
						continue;
					}
					if (@is_dir($path.'/'.$entry))	{
						$entry = $entry.'/';
						$key = md5($path.'/'.$entry);	// Don't change this ever - extensions may depend on the fact that the hash is an md5 of the path! (import/export extension)
						    $filearray[$key]=($prependPath?$path.'/':'').$entry;
							if ($order === 'mtime') {$sortarray[$key]=filemtime($path.'/'.$entry);}
								elseif ($order)	{$sortarray[$key]=$entry;}
					}
				}
				$d->close();
			} else return 'error opening path: "'.$path.'"';
		}

			// Sort them:
		if ($order) {
			asort($sortarray);
			reset($sortarray);
			$newArr=array();
			while(list($k,$v)=each($sortarray))	{
				$newArr[$k]=$filearray[$k];
			}
			$filearray=$newArr;
		}

			// Return result
		reset($filearray);
		return $filearray;
	}









	/***************************************
	 *
	 *	 Tools
	 *
	 ***************************************/





	/**
	 * Return $MOD_SETTINGS array
	 *
	 * @param 	string	$key Returns $MOD_SETTINGS[$key] instead of $MOD_SETTINGS
	 * @return	array $MOD_SETTINGS
	 */
	function getModSettings($key='') {
		static $MOD_SETTINGS=NULL;

		if ($MOD_SETTINGS==NULL) {
			$MOD_MENU = array(
				'displayThumbs' => '',
				'extendedInfo' => '',
				'act' => '',
				'mode' => '',
				'bparams' => '',
				);
			$MCONF['name']='tx_dam_browse';
			$settings = t3lib_div::_GP('SET');
				// save params in session
			if ($this->act) $settings['act'] = $this->act;
			if ($this->mode) $settings['mode'] = $this->mode;
			if ($this->bparams) $settings['bparams'] = $this->bparams;

			$MOD_SETTINGS = t3lib_BEfunc::getModuleData($MOD_MENU, $settings, $MCONF['name']);
		}
		if($key) {
			return $MOD_SETTINGS[$key];
		} else {
			return $MOD_SETTINGS;
		}
	}


	/**
	 * Processes bparams parameter
	 * Example value: "data[pages][39][bodytext]|||tt_content|" or "data[tt_content][NEW3fba56fde763d][image]|||gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai|"
	 *
	 * Values:
	 * 0: form field name reference
	 * 1: old/unused?
	 * 2: old/unused?
	 * 3: allowed types. Eg. "tt_content" or "gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai"
	 * 4: allowed file types when tx_dam table. Eg. "gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai"
	 *
	 * @return void
	 */
	function processParams() {

		$this->act = $this->isParamPassed('act') ? $this->act : $this->getModSettings('act');
		$this->mode = $this->isParamPassed('mode') ? $this->mode : $this->getModSettings('mode');
		$this->bparams = $this->isParamPassed('bparams') ? $this->bparams : $this->getModSettings('bparams');
		$this->expandFolder = $this->isParamPassed('expandFolder') ? $this->expandFolder : $this->getModSettings('expandFolder');

		$this->reinitParams();

		$pArr = explode('|', $this->bparams);
		$this->formFieldName = $pArr[0];

		switch((string)$this->mode)	{
			case 'rte':
			break;
			case 'db':
				$this->allowedTables = $pArr[3];
				if ($this->allowedTables === 'tx_dam') {
					$this->allowedFileTypes = $pArr[4];
					$this->disallowedFileTypes = $pArr[5];
				}
			break;
			case 'file':
			case 'filedrag':
				$this->allowedTables = $pArr[3];
				$this->allowedFileTypes = $pArr[3];
			break;
			case 'wizard':
			break;
		}
	}


	/**
	 * Check if a param was passed by GET OR POST
	 *
	 * @param string $paramName Param name
	 * @return boolean
	 */
	function isParamPassed ($paramName) {
		return isset($_POST[$paramName]) ? true : isset($_GET[$paramName]);
	}


	/**
	 * Set some variables with the current parameters
	 *
	 * @return void
	 */
	function reinitParams() {
		global $TYPO3_CONF_VARS;

			// needed for browsetrees and just to be save
		$this->addParams = array();
		$GLOBALS['SOBE']->browser->act = $GLOBALS['SOBE']->act = $this->addParams['act'] = $this->act;
		$GLOBALS['SOBE']->browser->mode = $GLOBALS['SOBE']->mode = $this->addParams['mode'] = $this->mode;
		$GLOBALS['SOBE']->browser->bparams = $GLOBALS['SOBE']->bparams = $this->addParams['bparams'] = $this->bparams;
		$GLOBALS['SOBE']->browser->expandFolder = $GLOBALS['SOBE']->expandFolder = $this->addParams['expandFolder'] = $this->expandFolder;

		$this->formTag = '<form action="'.htmlspecialchars(t3lib_div::linkThisScript($this->addParams)).'" method="post" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_folder.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_folder.php']);
}

?>