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
 *   78: class tx_dam_listfiles extends tx_dam_listbase
 *
 *              SECTION: Setup
 *  117:     function tx_dam_listfiles()
 *  127:     function __construct()
 *
 *              SECTION: Set data
 *  166:     function setPathInfo($pathInfo)
 *
 *              SECTION: Column rendering
 *  188:     function getItemColumns ($item)
 *
 *              SECTION: Column rendering
 *  272:     function getItemAction ($item)
 *  283:     function getItemIcon ($item)
 *
 *              SECTION: Controls
 *  349:     function getItemControl($item)
 *  390:     function getHeaderControl()
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_listbase.php');
require_once (PATH_txdam.'lib/class.tx_dam_actioncall.php');


/**
 * Class for rendering of Media>File>List
 * The class is not really abstract but on a good way to become so ...
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_listfiles extends tx_dam_listbase {


	/**
	 * stores two tx_dam_dir objects
	 */
	var $dataObjects = array();

	/**
	 * Display file sizes in bytes or formatted
	 */
	var $showDetailedSize = false;


	/**
	 * Dummy table name
	 */
	var $table = 'files';
	

	/***************************************
	 *
	 *	 Setup
	 *
	 ***************************************/


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_listfiles()	{
		$this->__construct();
	}


	/**
	 * Initialization of object
	 * PHP5 constructor
	 *
	 * @return	void
	 */
	function __construct() {
		
		parent::__construct();

		$this->paramName['setFolder'] = 'SET[tx_dam_folder]';

		$this->showMultiActions = false;
		$this->showAction = false;
		$this->showIcon = true;

		$this->clearColumns();
		$this->addColumn('title', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_file'));
		$this->addColumn('file_type', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_fileext'));
		$this->addColumn('mtime', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_tstamp'));
		$this->addColumn('size', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_size'));
		$this->columnTDAttr['size'] = ' align="right"';
		$this->addColumn('perms', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_rw'));
		$this->addColumn('_CONTROL_', '');
#		$this->addColumn('_CLIPBOARD_', '');
// todo Clipboard

		$this->elementAttr['table'] = ' border="0" cellpadding="0" cellspacing="0" style="width:100%" class="typo3-dblist typo3-filelist"';
	}




	/***************************************
	 *
	 *	 Set data
	 *
	 ***************************************/



	/**
	 * Initialize the object
	 *
	 * @return	void
	 */
	function init() {
		$this->returnUrl = t3lib_div::_GP('returnUrl');

		$this->processParams();
	}







	/***************************************
	 *
	 *	 Rendering
	 *
	 ***************************************/


	/**
	 * Renders the data columns
	 *
	 * @param	array		$item item array
	 * @return	array
	 */
	function getItemColumns ($item) {
		$type = $item['__type'];

			// 	Columns rendering
		$columns = array();
		foreach($this->columnList as $field => $descr)	{

			switch($field)	{
				case 'perms':
					if ($this->showUnixPerms) {
						$columns[$field] = $this->getFilePermString($item[$type.'_perms']);
					}
					else {
						$columns[$field] = (($item[$type.'_readable']) ? 'R' : '').(($item[$type.'_writable']) ? 'W' : '');
					}
				break;
				case 'size':
					if ($type === 'file') {
						$columns[$field] = (string)($item[$type.'_size']);
					}
					else {
						$columns[$field] = '';
					}
				break;
				case 'file_type':
					$columns[$field] = strtoupper($item[$field]);
				break;
				case 'mtime':
					$columns[$field] = date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'], $item[$type.'_mtime']);
				break;
				case 'title':
					if ($type === 'file') {
						$columns[$field] = $this->linkWrapFile($this->cropTitle($item[$type.'_title'], $field), $item);
					}
					else {
						$columns[$field] = $this->linkWrapDir($this->cropTitle($item[$type.'_title'], $field), $item[$type.'_path_absolute']);
					}
				break;
				case '_CLIPBOARD_':
					$columns[$field] = $this->clipboard_getItemControl($item);
				break;
				case '_CONTROL_':
					 $columns[$field] = $this->getItemControl($item);
					 $this->columnTDAttr[$field] = ' nowrap="nowrap"';
				break;
				default:
					if(isset($item[$type.$field])) {
						$content = $item[$type.$field];
					}
					else {
						$content = $item[$field];
					}
					$columns[$field] = htmlspecialchars(t3lib_div::fixed_lgd_cs($content, $this->titleLength));
				break;
			}
			if ($columns[$field] === '') {
				$columns[$field] = '&nbsp;';
			}
		}

			// Thumbsnails?
		if ($this->showThumbs AND $this->thumbnailPossible($item))	{
			$columns['title'] .= '<div style="margin:2px 0 2px 0;">'.$this->getThumbNail($item).'</div>';
		}
		if (!$this->showDetailedSize) {
			$columns['size'] = t3lib_div::formatSize($columns['size']);
		}
		return $columns;
	}


	/***************************************
	 *
	 *	 Column rendering
	 *
	 ***************************************/


	/**
	 * Renders the multi-action
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemMultiAction ($item) {
		$multiAction = '';

		if ($item['__type'] =='file') {
			$multiActionID = $item[$item['__type'].'_path_absolute'].$item['file_name'];
			$multiActionSelected = in_array($multiActionID, $this->recs);

			$multiAction = '<input type="checkbox" name="'.$this->paramName['recs'].'['.$this->table.'][]" value="'.htmlspecialchars($multiActionID).'"'.($multiActionSelected?' checked="checked"':'').' />';
		}
		
		return $multiAction;
	}



	/**
	 * Renders the item icon
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemIcon ($item) {
		static $titleNotIndexed;
		static $iconNotIndexed;

		if(!$iconNotIndexed) {
			$titleNotIndexed = 'title="'.$GLOBALS['LANG']->getLL('fileNotIndexed').'"';
			$iconNotIndexed = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/required_h.gif', 'width="10" height="10"').' '.$titleNotIndexed.' alt="" />';
		}

		$type = $item['__type'];

		if ($type == 'file') {
			$titleAttr = '';
			$attachToIcon = '';
			
			if(!$item['__isIndexed'] AND !($uid = tx_dam::file_isIndexed($item))) {
				$attachToIcon = $iconNotIndexed;
				$titleAttr = $titleNotIndexed;
			}
				
			$iconTag = tx_dam::icon_getFileTypeImgTag($item, $titleAttr);
			if ($this->enableContextMenus) $iconTag = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($iconTag, tx_dam::file_absolutePath($item));
			$iconTag .= $attachToIcon;
		}
		else {

			$titleAttr = 'title="'.htmlspecialchars($item[$type.'_title']).'"';
			$iconTag = tx_dam::icon_getFileTypeImgTag($item, $titleAttr);
			if ($this->enableContextMenus) $iconTag = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($iconTag, tx_dam::path_makeAbsolute($item));
		}
		return $iconTag;
	}



	/***************************************
	 *
	 *	 Controls
	 *
	 ***************************************/



	/**
	 * Creates the control panel for the path: create folder etc.
	 *
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function getHeaderControl() {
		global $TYPO3_CONF_VARS;
		
		static $actionCall = array();

		$content = '';

		if ($this->showControls) {
			$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');
			//$actionCall->setRequest('globalcontrol', $this->actionsEnv['pathInfo']);
			$actionCall->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
			$actionCall->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
			$actionCall->setEnv($this->actionsEnv);
			$actionCall->initActions(true);

			$actions = $actionCall->renderActionsHorizontal(true);

				// Compile items into a DIV-element:
			$content = '
												<!-- CONTROL PANEL: path -->
												<div class="typo3-DBctrlGlobal">'.implode('', $actions).'</div>';
		}

		return $content;
	}

	/**
	 * Creates the control panel for a single record in the listing.
	 *
	 * @param	array		The record for which to make the control panel.
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function getItemControl($item)	{
		global $TYPO3_CONF_VARS;
		
		static $actionCall = array();

		$content = '';

		if ($this->showControls) {
			if (!is_object($actionCall[$item['__type']])) {
				$actionCall[$item['__type']] = t3lib_div::makeInstance('tx_dam_actionCall');
				$actionCall[$item['__type']]->setRequest('control', array('__type' => $item['__type']));
				$actionCall[$item['__type']]->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
				$actionCall[$item['__type']]->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
				$actionCall[$item['__type']]->setEnv('defaultEditScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_edit/index.php');
				$actionCall[$item['__type']]->setEnv($this->actionsEnv);
				$actionCall[$item['__type']]->initActions(true);
			} elseif ($actionCall[$item['__type']]->itemInfo['__type']!=$item['__type']){
				$actionCall[$item['__type']]->setRequest('control', array('__type' => $item['__type']));
				$actionCall[$item['__type']]->initActions(true);
			}

			$actionCall[$item['__type']]->setRequest('control', $item);
			$actions = $actionCall[$item['__type']]->renderActionsHorizontal(true);
			$content = implode('&nbsp;', $actions);
		}

		return $content;
	}



	/**
	 * Returns an array of multi actions to be rendered by renderMultiActionBar()
	 *
	 * @return array
	 * @see tx_dam_actionCall::renderMultiActions()
	 * @see renderMultiActionBar()
	 */
	function getMultiActions() {
		global $TYPO3_CONF_VARS;
		
		$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');
		$actionCall->setRequest('multi', array('__type' => 'file'));
		$actionCall->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
		$actionCall->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
		$actionCall->setEnv($this->actionsEnv);
		$actionCall->initActions(true);

		$actions = $actionCall->renderMultiActions();
		return $actions;
	}





	/***************************************
	 *
	 *	 Misc
	 *
	 ***************************************/




	/**
	 * Returns unix like string of file permission
	 *
	 * @param	integer		$perms Permissions eg from fileperms()
	 * @return	string		Eg. rwxr-x---
	 */
	function getFilePermString ($perms) {
		if (($perms & 0xC000) == 0xC000) {
			// Socket
			$info = 's';
		 } elseif (($perms & 0xA000) == 0xA000) {
			// Symbolic Link
			$info = 'l';
		 } elseif (($perms & 0x8000) == 0x8000) {
			// Regular
			$info = '-';
		 } elseif (($perms & 0x6000) == 0x6000) {
			// Block special
			$info = 'b';
		 } elseif (($perms & 0x4000) == 0x4000) {
			// Directory
			$info = 'd';
		 } elseif (($perms & 0x2000) == 0x2000) {
			// Character special
			$info = 'c';
		 } elseif (($perms & 0x1000) == 0x1000) {
			// FIFO pipe
			$info = 'p';
		 } else {
			// Unknown
			$info = 'u';
		 }

		 // Owner
		 $info .= (($perms & 0x0100) ? 'r' : '-');
		 $info .= (($perms & 0x0080) ? 'w' : '-');
		 $info .= (($perms & 0x0040) ?
					(($perms & 0x0800) ? 's' : 'x' ) :
					(($perms & 0x0800) ? 'S' : '-'));

		 // Group
		 $info .= (($perms & 0x0020) ? 'r' : '-');
		 $info .= (($perms & 0x0010) ? 'w' : '-');
		 $info .= (($perms & 0x0008) ?
					(($perms & 0x0400) ? 's' : 'x' ) :
					(($perms & 0x0400) ? 'S' : '-'));

		 // World
		 $info .= (($perms & 0x0004) ? 'r' : '-');
		 $info .= (($perms & 0x0002) ? 'w' : '-');
		 $info .= (($perms & 0x0001) ?
					(($perms & 0x0200) ? 't' : 'x' ) :
					(($perms & 0x0200) ? 'T' : '-'));
		return $info;
	}

}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listfiles.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listfiles.php']);
}
?>
