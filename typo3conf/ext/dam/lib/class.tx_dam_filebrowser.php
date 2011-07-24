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
 * DAM file listing class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   65: class tx_dam_filebrowser extends tx_dam_listfiles
 *   78:     function getBrowseableFolderList($pathInfo, $renderNavHeader=true)
 *  106:     function getStaticFolderList($pathInfo, $renderFolderInfoBar=true)
 *  129:     function _filebrowser_makeDataList($pathInfo)
 *  174:     function _filebrowser_makePreset()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once (PATH_txdam.'lib/class.tx_dam_iterator_dir.php');
require_once(PATH_txdam.'lib/class.tx_dam_listfiles.php');
require_once(PATH_txdam.'lib/class.tx_dam_listpointer.php');


/**
 * Simple file browser
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_filebrowser extends tx_dam_listfiles {

	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_filebrowser() {
		$this->__construct();
	}


	/**
	 * Initialize the object
	 * PHP5 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function __construct() {
		
		parent::__construct();
		
		$this->SOBE = & $GLOBALS['SOBE'];
	}
	

	/**
	 * Creates a file/folder browser.
	 * The list does not include any actions (delete,rename,...) but can be used to show the files of a folder and select a folder as a starting point for some processing like indexing.
	 *
	 * @param	array		$pathInfo Path info array from tx_dam::path_compileInfo()
	 * @param	boolean		$renderNavHeader If set a header with the path will be rendered
	 * @return	string		HTML output
	 */
	function getBrowseableFolderList($pathInfo, $renderNavHeader=true)	{
		$content = '';

		$pathInfo = is_array($pathInfo) ? $pathInfo : tx_dam::path_compileInfo($pathInfo);

		$this->_filebrowser_makeDataList($pathInfo);
		$this->_filebrowser_makePreset();

			// enable browsing links
		$this->enableBrowsing = true;

		$content.= $this->getListTable();

		return $content;
	}


	/**
	 * Creates a file/folder listing.
	 * The list does not include any actions (delete,rename,...) but can be used to show the files of a folder. Browsing through folders is deactivated
	 *
	 * @param	array		$pathInfo Path info array from tx_dam::path_compileInfo()
	 * @param	boolean		$renderFolderInfoBar If set a header with the path will be rendered
	 * @return	string		HTML output
	 */
	function getStaticFolderList($pathInfo, $renderFolderInfoBar=true)	{
		$content = '';

		$pathInfo = is_array($pathInfo) ? $pathInfo : tx_dam::path_compileInfo($pathInfo);

		$this->_filebrowser_makeDataList($pathInfo);
		$this->_filebrowser_makePreset();

		if ($renderFolderInfoBar) {
			$content.= '<div class="typo3-foldernavbar">'.tx_dam_guiFunc::getFolderInfoBar($pathInfo).'</div>';
		}
		$content.= $this->getListTable();

		return $content;
	}


	/**
	 * Collect data for display and make setup
	 *
	 * @param	array		$pathInfo Path info array from tx_dam::path_compileInfo()
	 * @return void
	 */
	function _filebrowser_makeDataList($pathInfo) {
		global $TYPO3_CONF_VARS;
		
		//
		// fetches files and folder
		//

		$dirListFolder = t3lib_div::makeInstance('tx_dam_iterator_dir');
		$dirListFolder->read($pathInfo, 'dir,link');


		//
		// folder listing
		//

		$dirListFiles = t3lib_div::makeInstance('tx_dam_iterator_dir');
		$dirListFiles->read($pathInfo, 'file');


		//
		// initializes the pointer object for lists
		//

		$this->pointer = t3lib_div::makeInstance('tx_dam_listPointer');
		$this->pointer->init(0, 100);
		$this->pointer->setTotalCount($dirListFolder->count()+$dirListFiles->count());

		//
		// setup filelisting
		//

		$this->addData($dirListFolder, 'dir');
		$this->addData($dirListFiles, 'files');
		$this->setCurrentSorting($this->SOBE->MOD_SETTINGS['tx_dam_file_list_sortField'], $this->SOBE->MOD_SETTINGS['tx_dam_file_list_sortRev']);
		$this->setParameterName('sortField', 'SET[tx_dam_file_list_sortField]');
		$this->setParameterName('sortRev', 'SET[tx_dam_file_list_sortRev]');

		$this->setPointer($this->pointer);
	}


	/**
	 * Initialize setup
	 *
	 * @return void
	 */
	function _filebrowser_makePreset() {
		
		$this->removeColumn('_CONTROL_');
		$this->clickMenus = false;
		$this->clipBoard = false;

			// disable sorting links
		$this->enableSorting = false;
			// disable browsing links
		$this->enableBrowsing = false;

			// Enable/disable display of thumbnails
		$this->showThumbs = false;
			// Enable/disable display poups
		$this->enableFilePopup = false;
			// Enable/disable display of long titles
		$this->showfullTitle = false;
			// Enable/disable display of AlternateBgColors
		$this->showAlternateBgColors = is_object($this->SOBE) ? $this->SOBE->config_checkValueEnabled('alternateBgColors', true) : true;
			
			// Enable/disable display of unix like permission string
		$this->showUnixPerms = false;
			// Display file sizes in bytes or formatted
		$this->showDetailedSize = false;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_filebrowser.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_filebrowser.php']);
}


?>
