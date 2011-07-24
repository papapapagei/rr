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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module extension (addition to function menu) 'List' for the 'dam_file' extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage file
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   65: class tx_dam_file_list extends t3lib_extobjbase
 *   73:     function modMenu()
 *   92:     function head()
 *  128:     function main()
 *
 *              SECTION: Rendering
 *  289:     function renderInfo ($bytes)
 *  304:     function getActions()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once (PATH_t3lib.'class.t3lib_clipboard.php');


require_once (PATH_txdam.'lib/class.tx_dam_iterator_dir.php');
require_once (PATH_txdam.'lib/class.tx_dam_listfiles.php');

/**
 * Module extension 'Media>File>List'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage file
 */
class tx_dam_file_list extends t3lib_extobjbase {


	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()	{
		global $LANG;

		return array(
			'tx_dam_file_list_showThumb' => '',
			'tx_dam_file_list_showMultiActions' => '',
			'tx_dam_file_list_showfullTitle' => '',
			'tx_dam_file_list_showUnixPerms' => '',
			'tx_dam_file_list_showDetailedSize' => '',
			'tx_dam_file_list_sortField' => '',
			'tx_dam_file_list_sortRev' => '',
		);
	}

	/**
	 * Initialize the class and set some HTML header code
	 *
	 * @return	void
	 */
	function head()	{
		global $LANG;

		//
		// Init gui items and ...
		//

		$this->pObj->guiItems->registerFunc('getResultInfoBar', 'header');
		$this->pObj->guiItems->registerFunc(array(&$this, 'getActions'), 'header');
#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'header');

		$this->pObj->guiItems->registerFunc('getOptions', 'footer');

		$this->pObj->addOption('funcCheck', 'tx_dam_file_list_showThumb', $LANG->getLL('showThumbnails'));
		$this->pObj->addOption('funcCheck', 'tx_dam_file_list_showfullTitle', $LANG->getLL('showfullTitle'));
		if ($GLOBALS['BE_USER']->isAdmin()) {
			$this->pObj->addOption('funcCheck', 'tx_dam_file_list_showUnixPerms', $LANG->getLL('showUnixPerms'));
		} else {
			$this->pObj->MOD_SETTINGS['tx_dam_file_list_showUnixPerms'] = false;
		}
		$this->pObj->addOption('funcCheck', 'tx_dam_file_list_showDetailedSize', $LANG->getLL('showDetailedSize'));
		$this->pObj->addOption('funcCheck', 'tx_dam_file_list_showMultiActions', $LANG->getLL('showMultiAction'));

			// This will return content necessary for the context sensitive clickmenus to work: bodytag events, JavaScript functions and DIV-layers.
//		$CMparts = $this->pObj->doc->getContextMenuCode();
//		$this->pObj->doc->bodyTagAdditions = $CMparts[1];
//		$this->pObj->doc->JScode.= $CMparts[0];
//		$this->pObj->doc->postCode.= $CMparts[2];

	}

	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()	{
		global $FILEMOUNTS,$BE_USER,$LANG,$BACK_PATH, $TYPO3_CONF_VARS;
		

		$content = '';

		//
		// Create filelisting object
		//

		$filelist = t3lib_div::makeInstance('tx_dam_listfiles');
		$filelist->init();
		
		$filelist->setActionsEnv(array(
				'pathInfo' => $this->pObj->pathInfo,
			));


			
		//
		// fetches folder
		//

		$dirListFolder = t3lib_div::makeInstance('tx_dam_iterator_dir');
		$dirListFolder->read($this->pObj->pathInfo['dir_path_absolute'], 'dir,link');
		$sortField = str_replace('dir_dir_', 'dir_', 'dir_'.$this->pObj->MOD_SETTINGS['tx_dam_file_list_sortField']);
		$dirListFolder->sort($sortField, $this->pObj->MOD_SETTINGS['tx_dam_file_list_sortRev']);


		//
		// fetches files
		//

		$dirListFiles = t3lib_div::makeInstance('tx_dam_iterator_dir');

		$dirListFiles->enableAutoIndexing = tx_dam::config_checkValueEnabled('setup.indexing.auto', true);
		$dirListFiles->maxAutoIndexingItems = tx_dam::config_checkValueEnabled('setup.indexing.autoMaxInteractive', 5);

		$displayDotFiles = $this->pObj->config_checkValueEnabled('filesDisplayDotFiles', $BE_USER->isAdmin());
		if (!$displayDotFiles) {
			$dirListFiles->excludeByRegex ('^\.');
		}
		
		if ($displayExcludeByRegex = $this->pObj->config_checkValueEnabled('filesDisplayExcludeByRegex')) {
			$dirListFiles->excludeByRegex ($displayExcludeByRegex);
		}
		
		$dirListFiles->read($this->pObj->pathInfo['dir_path_absolute'], 'file');
		$sortField = str_replace('file_file_', 'file_', 'file_'.$this->pObj->MOD_SETTINGS['tx_dam_file_list_sortField']);
		$dirListFiles->sort($sortField, $this->pObj->MOD_SETTINGS['tx_dam_file_list_sortRev']);

		$this->pObj->selection->pointer->setTotalCount($dirListFolder->count()+$dirListFiles->count());
		

		//
		// process multi action if needed
		//

		if ($processAction = $filelist->getMultiActionCommand()) {

			if ($processAction['onItems'] === '_all') {

				$uidList = array();
				if ($dirListFiles->count())	{
	
					while ($dirListFiles->valid()) {
	
						$item = $dirListFiles->current();
						$uidList[] = tx_dam::file_absolutePath($item);
	
						$dirListFiles->next();
					}
				}
			} else {
				
				$uidList = t3lib_div::trimExplode(',', $processAction['onItems'], true);
			}

			if ($uidList) {
				$itemList = '';
				foreach ($uidList as $file) {
					$itemList .= '&file[]='.rawurlencode($file);
				}
				switch ($processAction['actionType']) {
					case 'url':
						$url = str_replace('###PARAMLIST###', $itemList, $processAction['action']);
						header('Location: '.$url);
						exit;
					break;
// TODO
//					case 'tce-data':
//						$params = '';
//						foreach ($uidList as $uid) {
//							$params .= str_replace('###UID###', $uid, $processAction['action']);
//						}
//						$url = $GLOBALS['SOBE']->doc->issueCommand($params, -1);
//
//
//						$url = $BACK_PATH.'tce_file.php?&redirect='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI')).'&vC='.$BE_USER->veriCode().'&prErr=1&uPT=1'.$params;
//
//						header('Location: '.$url);
//						exit;
//					break;
				}
			}
		}
			

				
		$filelist->setParameterName('form', $this->pObj->formName);

			// enable display of multi actions
		$filelist->showMultiActions = $this->pObj->MOD_SETTINGS['tx_dam_file_list_showMultiActions'];
			// Enable/disable display of thumbnails
		$filelist->showThumbs = $this->pObj->MOD_SETTINGS['tx_dam_file_list_showThumb'];
			// Enable/disable display of long titles
		$filelist->showfullTitle = $this->pObj->MOD_SETTINGS['tx_dam_file_list_showfullTitle'];
			// Enable/disable display of AlternateBgColors
		$filelist->showAlternateBgColors = $this->pObj->config_checkValueEnabled('alternateBgColors', true);
			// Enable/disable display of unix like permission string
		$filelist->showUnixPerms = $this->pObj->MOD_SETTINGS['tx_dam_file_list_showUnixPerms'];
			// Display file sizes in bytes or formatted
		$filelist->showDetailedSize = $this->pObj->MOD_SETTINGS['tx_dam_file_list_showDetailedSize'];
			// enable context menus
		$filelist->enableContextMenus = $this->pObj->config_checkValueEnabled('contextMenuOnListItems', true);


// todo Clipboard
$filelist->clipBoard = $this->pObj->MOD_SETTINGS['clipBoard'];


		$filelist->addData($dirListFolder, 'dir');
		$filelist->addData($dirListFiles, 'files');
		$filelist->setCurrentSorting($this->pObj->MOD_SETTINGS['tx_dam_file_list_sortField'], $this->pObj->MOD_SETTINGS['tx_dam_file_list_sortRev']);
		$filelist->setParameterName('sortField', 'SET[tx_dam_file_list_sortField]');
		$filelist->setParameterName('sortRev', 'SET[tx_dam_file_list_sortRev]');
		$filelist->setPointer($this->pObj->selection->pointer);
		
		$this->pObj->doc->JScodeArray['filelist-JsCode'] = $filelist->getJsCode();


		$fileListTable = $filelist->getListTable();






			// Create clipboard object and initialize that
//		$filelist->clipObj = t3lib_div::makeInstance('t3lib_clipboard');
//		$filelist->clipObj->backPath = $BACK_PATH;
//		$filelist->clipObj->fileMode = 1;
//		$filelist->clipObj->initializeClipboard();
//
//		$CB = $HTTP_GET_VARS['CB'];
//		if (t3lib_div::_GP('cmd') === 'setCB') $CB['el'] = $filelist->clipObj->cleanUpCBC(array_merge(t3lib_div::_POST('CBH'), t3lib_div::_POST('CBC')), '_FILE');
//		if (!$this->pObj->MOD_SETTINGS['clipBoard'])	$CB['setP'] = 'normal';
//		$filelist->clipObj->setCmd($CB);
//		$filelist->clipObj->cleanCurrent();
//		$filelist->clipObj->endClipboard();	// Saves

			// If the "cmd" was to delete files from the list (clipboard thing), do that:
//		if (t3lib_div::_GP('cmd') === 'delete')	{
//			$items = $filelist->clipObj->cleanUpCBC($HTTP_POST_VARS['CBC'], '_FILE', 1);
//			if (count($items))	{
//					// Make command array:
//				$FILE = array();
//				reset($items);
//				while(list(, $v) = each($items))	{
//					$FILE['delete'][] = array('data' => $v);
//				}
//
//					// Init file processing object for deleting and pass the cmd array.
//				$fileProcessor = t3lib_div::makeInstance('t3lib_extFileFunctions');
//				$fileProcessor->init($FILEMOUNTS, $TYPO3_CONF_VARS['BE']['fileExtensions']);
//				$fileProcessor->init_actionPerms(tx_dam::getFileoperationPermissions());
//				$fileProcessor->dontCheckForUnique = t3lib_div::_GP('overwriteExistingFiles') ? 1 : 0;
//				$fileProcessor->start($FILE);
//				$fileProcessor->processData();
//
//					// Redirect to the status file.
//				Header('Location: '.t3lib_div::locationHeaderUrl($BACK_PATH.'status_file.php'));
//				exit;
//			}
//		}




			// Set top JavaScript:
#		$this->pObj->doc->JScode.= $this->pObj->doc->wrapScriptTags('
#			if (top.fsMod) top.fsMod.recentIds["file"] = unescape("'.rawurlencode($this->pObj->id).'");
#
#		'.$filelist->CBfunctions()	// ... and add clipboard JavaScript functions
#		);








		//
		// output header: info bar, result browser, ....
		//

		$content.= $this->pObj->guiItems->getOutput('header');


		$infoPath = $this->pObj->getFolderNavBar($this->pObj->pathInfo);
		$infoBytes = $this->renderInfo($dirListFiles->countBytes);
		$content.= '<div class="typo3-foldernavbar">'.$this->pObj->contentLeftRight($infoPath, $infoBytes).'</div>';

		$content.= $fileListTable;


				// Set clipboard:
#		if ($this->pObj->MOD_SETTINGS['clipBoard'])	$content.= $filelist->clipObj->printClipboard();


		return $content;
	}



	/***************************************
	 *
	 *	 Rendering
	 *
	 ***************************************/


	/**
	 * Add a footer to the table
	 *
	 * @return	void
	 */
	function renderInfo ($bytes) {
		$content = '';
		$content = $this->pObj->selection->pointer->countTotal.' '.($this->pObj->selection->pointer->countTotal==1 ? $GLOBALS['LANG']->getLL('file',1) : $GLOBALS['LANG']->getLL('files',1));
		if ($bytes) {
			$content .= ', '.t3lib_div::formatSize($bytes).'bytes';
		}
		return $content;
	}


	/**
	 * Return action buttons like create folder and file
	 *
	 * @return HTML
	 */
	function getActions() {
		global $TYPO3_CONF_VARS;
		
		$content = '';

		if($this->pObj->pathInfo['dir_writable']) {
			$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');
			$actionCall->setRequest('button', $this->pObj->pathInfo);
			$actionCall->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
			$actionCall->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
			$actionCall->setEnv('defaultEditScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_edit/index.php');
			$actionCall->setEnv('pathInfo', $this->pObj->pathInfo);
			$actionCall->initActions();
			$actions = $actionCall->renderActionsHorizontal();
			$this->pObj->markers['FOLDER'] = $actions[0];
			
			if (array_key_exists('tx_dam_file_upload', $this->pObj->MOD_MENU['function'])) {
				if ($GLOBALS['BE_USER']->uc['enableFlashUploader']) {
					$this->pObj->markers['UPLOAD'] = '<a href="#" id="button-upload"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/upload.gif') . ' title="'.$GLOBALS['LANG']->sL('LLL:EXT:dam/modfunc_file_upload/locallang.xml:tx_dam_file_upload.title',1).'" alt="" height="16" width="16"></a>';
				} else {
					$this->pObj->markers['UPLOAD'] = '<a href="#" onclick="jumpToUrl(\'index.php?&amp;id=1&amp;SET[function]=tx_dam_file_upload\',this); return false;"><img' . t3lib_iconWorks::skinImg($this->pObj->doc->backPath, 'gfx/upload.gif') . ' title="'.$GLOBALS['LANG']->sL('LLL:EXT:dam/modfunc_file_upload/locallang.xml:tx_dam_file_upload.title',1).'" alt="" height="16" width="16"></a>';
				}
			}
			$this->pObj->markers['NEW'] = $actions[1];
		}
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_file_list/class.tx_dam_file_list.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_file_list/class.tx_dam_file_list.php']);
}

?>
