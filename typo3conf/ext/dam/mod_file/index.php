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
 * Module 'Media>File'
 * Part of the DAM (digital asset management) extension.
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
 *   74: class tx_dam_mod_file extends tx_dam_SCbase
 *   88:     function init()
 *  111:     function main()
 *  201:     function printContent()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

$BE_USER->modAccess($MCONF,1);

require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');
require_once(PATH_txdam.'lib/class.tx_dam_guirenderlist.php');

require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');


$LANG->includeLLFile('EXT:dam/mod_file/locallang.xml');



/**
 * Script class for the DAM file module
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage file
 */
class tx_dam_mod_file extends tx_dam_SCbase {

	/**
	 * t3lib_basicFileFunctions object
	 */
	var $basicFF;

	var $formName = 'editform';

	/**
	 * Initializes the backend module
	 *
	 * @return	void
	 */
	function init()	{
		global  $TYPO3_CONF_VARS, $FILEMOUNTS;

		parent::init();


			// Init guiRenderList object:

		$this->guiItems = t3lib_div::makeInstance('tx_dam_guiRenderList');


			// Init basic-file-functions object:

		$this->basicFF = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$this->basicFF->init($FILEMOUNTS,$TYPO3_CONF_VARS['BE']['fileExtensions']);
	}


	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $FILEMOUNTS, $TYPO3_CONF_VARS;

		//
		// Initialize the template object
		//
		$this->doc = t3lib_div::makeInstance('template'); 
		$this->doc->backPath = $BACK_PATH;
		$this->doc->setModuleTemplate(t3lib_extMgm::extRelPath('dam') . 'res/templates/mod_file_list.html');
		$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('dam') . 'res/css/stylesheet.css';
		$this->doc->docType = 'xhtml_trans';


		//
		// There was access to this file path, continue ...
		//

		if ($this->pathAccess)	{

			//
			// Output page header
			//


			$this->addDocStyles();
			$this->addDocJavaScript();
			
				// include the initialization for the flash uploader
			if ($GLOBALS['BE_USER']->uc['enableFlashUploader']) {
				$this->addFlashUploader();
			}				

			$this->extObjHeader();

			$this->doc->form = $this->getFormTag();

			//
			// Output tabmenu if not a single function was forced
			//

			if (!$this->forcedFunction AND count($this->MOD_MENU['function'])>1) {
				$this->markers['FUNC_MENU'] = $this->getTabMenu($this->addParams,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function']);
			}
			else {
				$this->markers['FUNC_MENU'] = '';
			}

			//
			// Call submodule function
			//

			$this->extObjContent();



			//
			// output footer: search box, options, store control, ....
			//

			$this->content.= $this->doc->spacer(10);
			$this->content.= $this->guiItems->getOutput('footer');


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->markers['SHORTCUT'] = $this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']);
			}

			$this->content.= $this->doc->spacer(10);

			$this->markers['CONTENT'] = $this->content;
			$this->markers['TITLE'] = ''; // Needed?
			$docHeaderButtons = array(
				'NEW' => $this->markers['NEW'],
				'UPLOAD' => $this->markers['UPLOAD'],
				'FOLDER' => $this->markers['FOLDER'],
				'REFRESH' => $this->markers['REFRESH'], 
				'LEVEL_UP' => $this->markers['LEVEL_UP'], 
				'RECORD_LIST' => $this->markers['RECORD_LIST'],
				'SHORTCUT' => $this->markers['SHORTCUT'],
			);
			$this->markers['CSH'] = ''; // TODO
				// Build the <body> for the module
			$this->content = $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $this->markers);
			$this->content.= $this->doc->endPage();

		} else {
				// If no access or no path
			$this->content.= $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->header($LANG->getLL('title'));
			$this->content.= $this->doc->spacer(5);
			if($this->pathInfo) {
				$this->content.= $this->doc->section('', $LANG->getLL('pathNoAccess'));
			}
			else {
				$this->content.= $this->doc->section('', $LANG->getLL('pathNotExists'));
			}
			$this->content.= '<p>'.htmlspecialchars($this->path).'</p>';
			$this->content.= $this->doc->spacer(10);
		}
	}
	
	function addFlashUploader() {
		$this->doc->JScodeArray['flashUploader'] = '
			if (top.TYPO3.FileUploadWindow.isFlashAvailable()) {
				document.observe("dom:loaded", function() {
						// monitor the button
					$("button-upload").observe("click", initFlashUploader);

					function initFlashUploader(event) {
							// set the page specific options for the flashUploader
						var flashUploadOptions = {
							uploadURL:           top.TS.PATH_typo3 + "ajax.php",
							uploadFileSizeLimit: "' . t3lib_div::getMaxUploadFileSize() . '",
							uploadFileTypes: {
								allow:  "' . $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']['webspace']['allow'] . '",
								deny: "' . $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']['webspace']['deny'] . '"
							},
							uploadFilePostName:  "upload_1",
							uploadPostParams: {
								"file[upload][1][target]": "' . tx_dam::path_makeAbsolute($this->path) . '",
								"file[upload][1][data]": 1,
								"file[upload][1][charset]": "utf-8",
								"ajaxID": "TYPO3_tcefile::process"
							}
						};

							// get the flashUploaderWindow instance from the parent frame
						var flashUploader = top.TYPO3.FileUploadWindow.getInstance(flashUploadOptions);
						// add an additional function inside the container to show the checkbox option
						var infoComponent = new top.Ext.Panel({
							autoEl: { tag: "div" },
							height: "auto",
							bodyBorder: false,
							border: false,
							hideBorders: true,
							cls: "t3-upload-window-infopanel",
							id: "t3-upload-window-infopanel-addition",
							html: \'<label for="overrideExistingFilesCheckbox"><input id="overrideExistingFilesCheckbox" type="checkbox" onclick="setFlashPostOptionOverwriteExistingFiles(this);" />\' + top.String.format(top.TYPO3.LLL.fileUpload.infoComponentOverrideFiles) + \'</label>\'
						});
						flashUploader.add(infoComponent);

							// do a reload of this frame once all uploads are done
						flashUploader.on("totalcomplete", function() {
							window.location.reload();
						});

							// this is the callback function that delivers the additional post parameter to the flash application
						top.setFlashPostOptionOverwriteExistingFiles = function(checkbox) {
							var uploader = top.TYPO3.getInstance("FileUploadWindow");
							if (uploader.isVisible()) {
								uploader.swf.addPostParam("overwriteExistingFiles", (checkbox.checked == true ? 1 : 0));
							}
						};

						event.stop();
					};
				});
			}
		';
	}


	/**
	 * Prints out the module HTML
	 *
	 * @return	string		HTML
	 */
	function printContent()	{
		$this->content = $this->doc->insertStylesAndJS($this->content);

		echo $this->content;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_file/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_file/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dam_mod_file');
$SOBE->init();


// Include files?
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{include_once($INC_FILE);}
$SOBE->checkExtObj();	// Checking for first level external objects

// Repeat Include files! - if any files has been added by second-level extensions
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{include_once($INC_FILE);}
$SOBE->checkSubExtObj();	// Checking second level external objects


$SOBE->main();
$SOBE->printContent();

?>
