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
 * Contains standard selection trees/rules.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   81: class tx_dam_selectionFolder extends t3lib_folderTree
 *  122:     function tx_dam_selectionFolder()
 *  140:     function getId ($row)
 *  152:     function getJumpToParam($row, $command='SELECT')
 *  166:     function PM_ATagWrap($icon,$cmd,$bMark='')
 *  192:     function wrapTitle($title,$row,$bank=0)
 *  215:     function getControl($title,$row)
 *  242:     function printTree($treeArr='')
 *  293:     function setMounts($mountpoints)
 *  306:     function getTreeTitle()
 *  315:     function getDefaultIcon()
 *  325:     function getTreeName()
 *
 *              SECTION: DAM specific functions
 *  344:     function selection_getItemTitle($id)
 *  356:     function selection_getItemIcon($id, $value)
 *  377:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *
 *              SECTION: element browser specific functions
 *  406:     function eb_wrapTitle($title,$row)
 *  421:     function eb_PM_ATagWrap($icon,$cmd,$bMark='')
 *  437:     function eb_printTree($treeArr='')
 *  496:     function ext_isLinkable($v)
 *
 * TOTAL FUNCTIONS: 18
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_t3lib.'class.t3lib_foldertree.php');



/**
 * folder tree class
 *
 * This is customized to behave like a selection class.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
class tx_dam_selectionFolder extends t3lib_folderTree  {

	/**
	 * Definies if this class can render a browse tree
	 */
	var $isTreeViewClass = true;

	/**
	 * Defines if a browsetree for TCEForms can be rendered
	 */
	var $isTCEFormsSelectClass = false;

	/**
	 * If mounts are supported (be_users)
	 */
	var $supportMounts = false;	// done automatically

	/**
	 * element browser mode
	 */
	var $mode = 'browse';

	/**
	 * enables selection icons: + = -
	 */
	var $modeSelIcons = true;

	/**
	 * Defines the deselect magic value
	 */
	var $deselectValue = 0;





	/**
	 * constructor
	 *
	 * @return	void
	 */
	function tx_dam_selectionFolder() {
		$this->title='Folder tree';
		$this->treeName='txdamFolder';
		$this->domIdPrefix=$this->treeName;
		$this->MOUNTS = $GLOBALS['FILEMOUNTS'];
		$this->iconPath = PATH_txdam_rel.'i/18/';
		$this->iconName = 'folder_web_ro.gif';
		$this->rootIcon = PATH_txdam_rel.'i/18/folder_mount.gif';
		$this->ext_IconMode = '1'; // no context menu on icons
	}
	
	/**
	 * Initialize the tree class. Needs to be overwritten
	 * Will set ->fieldsArray, ->backPath and ->clause
	 *
	 * @param	string		record WHERE clause
	 * @param	string		record ORDER BY field
	 * @return	void
	 */
	function init($clause='', $orderByFields='', $excludeReadOnlyMounts=false) {
		parent::init($clause, $orderByFields);
		if ($excludeReadOnlyMounts) {
			foreach ($this->MOUNTS as $key => $val) {
				if ($val['type'] == 'readonly') {
					unset($this->MOUNTS[$key]);
				}
			}
		}
	}

	/**
	 * Returns the id from the record (typ. uid)
	 *
	 * @param	array		Record array
	 * @return	integer		The "uid" field value.
	 */
	function getId ($row) {
		return rawurlencode($this->treeName.$row['path']);
	}


	/**
	 * Returns jump-url parameter value.
	 *
	 * @param	array		$row The record array.
	 * @param	string		$command: SELECT, ...
	 * @return	string		The jump-url parameter.
	 */
	function getJumpToParam($row, $command='SELECT') {
		return '&SLCMD['.$command.']['.$this->treeName.']['.rawurlencode($row['path']).']=1';
	}


	/**
	 * Wrap the plus/minus icon in a link
	 *
	 * @param	string		HTML string to wrap, probably an image tag.
	 * @param	string		Command for 'PM' get var
	 * @param	boolean		If set, the link will have a anchor point (=$bMark) and a name attribute (=$bMark)
	 * @return	string		Link-wrapped input string
	 * @access private
	 */
	function PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($this->mode === 'elbrowser') {
			return $this->eb_PM_ATagWrap($icon,$cmd,$bMark);
		} else {
			if ($bMark)	{
				$anchor = '#'.$bMark;
				$name=' name="'.$bMark.'"';
			}
			$aUrl = $this->thisScript.'?PM='.$cmd;
			if (t3lib_div::_GP('folderOnly')) {
				$aUrl .= '&folderOnly=1';
			}
			$aUrl .= $this->PM_addParam;
			return '<a href="'.htmlspecialchars($aUrl.$anchor).'"'.$name.'>'.$icon.'</a>';
		}
	}


	/**
	 * Wrapping $title in a-tags.
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 */
	function wrapTitle($title,$row,$bank=0)	{

		if ($this->mode === 'elbrowser') {
			return $this->eb_wrapTitle($title,$row);

		} elseif ($this->mode === 'tceformsSelect') {
			return $this->tceformsSelect_wrapTitle($title,$row);

		} else {
			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row).'\',this,\''.$this->domIdPrefix.$this->getId($row).'_'.$bank.'\');';
			return '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a> ';
		}
	}


	/**
	 * Return a control (eg. selection icons) for the element
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 */
	function getControl($title,$row) {
		$control = '';

		if (!t3lib_div::_GP('folderOnly') AND $this->modeSelIcons) {
			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'OR').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/plus.gif', 'width="8" height="11"').' border="0" alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'AND').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/equals.gif', 'width="8" height="11"').' border="0" alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'NOT').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/minus.gif', 'width="8" height="11"').' border="0" alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
		}

		return $control;
	}


	/**
	 * Compiles the HTML code for displaying the structure found inside the ->tree array
	 *
	 * @param	array		"tree-array" - if blank string, the internal ->tree array is used.
	 * @return	string		The HTML code for the tree
	 */
	function printTree($treeArr='')	{
		if($this->mode === 'elbrowser') {
			return $this->eb_printTree($treeArr);
		} else {
			$titleLen = intval($this->BE_USER->uc['titleLen']);

			$out='';

				// put a table around it with IDs to access the rows from JS
				// not a problem if you don't need it
				// In XHTML there is no "name" attribute of <td> elements - but Mozilla will not be able to highlight rows if the name attribute is NOT there.
			$out .= '

				<!--
				  TYPO3 tree structure.
				-->
				<table cellpadding="0" cellspacing="0" border="0" class="typo3-browsetree">';


			$this->colorTRHover = $GLOBALS['SOBE']->doc->hoverColorTR ? $GLOBALS['SOBE']->doc->hoverColorTR : t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-20,-20,-20);
			$trHover = $this->colorTRHover ? (' onmouseover="this.style.backgroundColor = \''.$this->colorTRHover.'\';" onmouseout="this.style.backgroundColor = \'\'"') : '';

			foreach($treeArr as $k => $v)	{
				$idAttr = htmlspecialchars($this->domIdPrefix.$this->getId($v['row']).'_'.$v['bank']);
				$title = $this->getTitleStr($v['row'], $titleLen);
				$control = $this->getControl($title, $v['row'], $v['bank']);
				$out.='
					<tr'.$trHover.'>
						<td id="'.$idAttr.'">'.
							$v['HTML'].
							$this->wrapTitle($title, $v['row'], $v['bank']).
						'</td>
						<td width="1%" id="'.$idAttr.'Control" class="typo3-browsetree-control">'.
							($control ? $control : '<span></span>').
						'</td>
					</tr>
				';
			}
			$out .= '
				</table>';
			return $out;
		}
	}


	/**
	 * Set mointpoints for the tree
	 *
	 * @param	array		$mountpoints: ...
	 * @return	void
	 */
	function setMounts($mountpoints) {
// set automatically
//		if (is_array($mountpoints)) {
//			$this->MOUNTS = $mountpoints;
//		}
	}


	/**
	 * Returns the title for the tree
	 *
	 * @return	string
	 */
	function getTreeTitle()	{
		return $this->title;
	}

	/**
	 * Returns the defailt icon file
	 *
	 * @return	string
	 */
	function getDefaultIcon()	{
		return $this->iconPath.$this->iconName;
	}


	/**
	 * Returns the treename (used for storage of expanded levels)
	 *
	 * @return	string
	 */
	function getTreeName()	{
		return $this->treeName;
	}

	/**
	 * Will create and return the HTML code for a browsable tree of folders.
	 * Is based on the mounts found in the internal array ->MOUNTS (set in the constructor)
	 *
	 * @return	string		HTML code for the browsable tree
	 */
	function getBrowsableTree()	{

			// Get stored tree structure AND updating it if needed according to incoming PM GET var.
		$this->initializePositionSaving();

			// Init done:
		$titleLen=intval($this->BE_USER->uc['titleLen']);
		$treeArr=array();

			// Traverse mounts:
		foreach($this->MOUNTS as $key => $val)	{
			$md5_uid = md5($val['path']);
			$specUID=hexdec(substr($md5_uid,0,6));
			$this->specUIDmap[$specUID]=$val['path'];

				// Set first:
			$this->bank=$val['nkey'];
			$isOpen = $this->stored[$val['nkey']][$specUID] || $this->expandFirst;
			$this->reset();

				// Set PM icon:
			$cmd=$this->bank.'_'.($isOpen?'0_':'1_').$specUID.'_'.$this->treeName;
			$icon='<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/ol/'.($isOpen?'minus':'plus').'only.gif','width="18" height="16"').' alt="" />';
			$firstHtml= $this->PM_ATagWrap($icon,$cmd);

			$pathInfo = array(
				'dir_name' =>  $val['path'],
				'mount_type' => $val['type'],
				'mount_path' => $val['path'],
				'dir_path_absolute' => $val['path'],
				'dir_readonly' => ($val['type'] == 'readonly')
			);
		
				// Preparing rootRec for the mount
			$firstHtml.=$this->wrapIcon(tx_dam::icon_getFileTypeImgTag($pathInfo),$val);
				$row=array();
				$row['path']=$val['path'];
				$row['uid']=$specUID;
				$row['title']=$val['name'];

				// Add the root of the mount to ->tree
			$this->tree[]=array('HTML'=>$firstHtml,'row'=>$row,'bank'=>$this->bank);

				// If the mount is expanded, go down:
			if ($isOpen)	{
					// Set depth:
				$depthD='<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/ol/blank.gif','width="18" height="16"').' alt="" />';
				$this->getFolderTree($val['path'],999,$depthD);
			}

				// Add tree:
			$treeArr=array_merge($treeArr,$this->tree);
		}
		return $this->printTree($treeArr);
	}

	/**
	 * Fetches the data for the tree
	 *
	 * @param	string		Abs file path
	 * @param	integer		Max depth (recursivity limit)
	 * @param	string		HTML-code prefix for recursive calls.
	 * @return	integer		The count of items on the level
	 * @see getBrowsableTree()
	 */
	function getFolderTree($files_path, $depth=999, $depthData='')	{

			// This generates the directory tree
		$dirs = t3lib_div::get_dirs($files_path);

		$c=0;
		if (is_array($dirs))	{
			$depth=intval($depth);
			$HTML='';
			$a=0;
			$c=count($dirs);
			natcasesort($dirs);

			foreach($dirs as $key => $val)	{
				$a++;
				$this->tree[]=array();		// Reserve space.
				end($this->tree);
				$treeKey = key($this->tree);	// Get the key for this space
				$LN = ($a==$c)?'blank':'line';

				$val = preg_replace('/^\.\//', '', $val);
				$title = $val;
				$path = $files_path.$val.'/';

				$md5_uid = md5($path);
				$specUID=hexdec(substr($md5_uid,0,6));
				$this->specUIDmap[$specUID]=$path;
				$row=array();
				$row['path']=$path;
				$row['uid']=$specUID;
				$row['title']=$title;

				if ($depth>1 && $this->expandNext($specUID))	{
					$nextCount=$this->getFolderTree(
						$path,
						$depth-1,
						$this->makeHTML ? $depthData.'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/ol/'.$LN.'.gif','width="18" height="16"').' alt="" />' : ''
					);
					$exp=1;		// Set "did expand" flag
				} else {
					$nextCount=$this->getCount($path);
					$exp=0;		// Clear "did expand" flag
				}

					// Set HTML-icons, if any:
				if ($this->makeHTML) {
					$HTML=$depthData.$this->PMicon($row,$a,$c,$nextCount,$exp);
					$pathInfo = tx_dam::path_compileInfo($path);
					$HTML.=$this->wrapIcon(tx_dam::icon_getFileTypeImgTag($pathInfo),$row);
				}

					// Finally, add the row/HTML content to the ->tree array in the reserved key.
				$this->tree[$treeKey] = Array(
					'row'=>$row,
					'HTML'=>$HTML,
					'bank'=>$this->bank
				);
			}
		}
		return $c;
	}

	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/


	/**
	 * Returns the title of an item
	 *
	 * @param	string		$id
	 * @return	string
	 */
	function selection_getItemTitle($id)	{
		return tx_dam::path_makeRelative ($id);
	}


	/**
	 * Returns the icon of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemIcon($id, $value)	{
		if($icon = $this->getDefaultIcon()) {
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],$icon, 'width="18" height="16"').' class="typo3-icon" alt="" />';
		}
		return $icon;
	}


	/**
	 * Function, processing the query part for selecting/filtering records in DAM
	 * Called from DAM
	 *
	 * @param	string		Query type: AND, OR, ...
	 * @param	string		Operator, eg. '!=' - see DAM Documentation
	 * @param	string		Category - corresponds to the "treename" used for the category tree in the nav. frame
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @param	object		Reference to the parent DAM object.
	 * @return	string
	 * @see tx_dam_selection::getWhereClausePart()
	 */
	function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)      {
		$query= $damObj->sl->getFieldMapping('tx_dam', 'file_path');
		if($queryType === 'NOT') {
			$query.= ' NOT';
		}
		$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike(tx_dam::path_makeRelative($id), 'tx_dam');
		$query.= ' LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr.'%', 'tx_dam');

		return array($queryType,$query);
	}




	/********************************
	 *
	 * element browser specific functions
	 *
	 ********************************/


	/**
	 * Wrapping $title in a-tags.
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 */
	function eb_wrapTitle($title,$row)	{
		$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&bparams='.$GLOBALS['SOBE']->bparams.$this->getJumpToParam($row).'\');';
		return '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
	}


	/**
	 * Wrap the plus/minus icon in a link
	 *
	 * @param	string		HTML string to wrap, probably an image tag.
	 * @param	string		Command for 'PM' get var
	 * @param	boolean		If set, the link will have a anchor point (=$bMark) and a name attribute (=$bMark)
	 * @return	string		Link-wrapped input string
	 * @access private
	 */
	function eb_PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($bMark)	{
			$anchor = '#'.$bMark;
			$name=' name="'.$bMark.'"';
		}
		$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?PM='.$cmd.'&act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&bparams='.$GLOBALS['SOBE']->bparams.'\',\''.$anchor.'\');';
		return '<a href="#"'.$name.' onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
	}


	/**
	 * Create the folder navigation tree in HTML
	 *
	 * @param	mixed		Input tree array. If not array, then $this->tree is used.
	 * @return	string		HTML output of the tree.
	 */
	function eb_printTree($treeArr='')	{
		global  $BE_USER;

		if (!is_array($treeArr))	$treeArr=$this->tree;

		$out='';
		$c=0;
		$cmpItem = '';
		$cmpPath = '';

		$selection = $GLOBALS['SOBE']->browser->damSC->selection->sl->sel;
		if (is_array($selection['SELECT'][$this->treeName])) {
			$cmpItem = key($selection['SELECT'][$this->treeName]);
			$cmpPath = tx_dam::path_makeAbsolute($cmpItem);
		}

			// Traverse rows for the tree and print them into table rows:
		foreach($treeArr as $k => $v)	{
			$c++;
			$bgColorClass=($c+1)%2 ? 'bgColor' : 'bgColor-10';

				// Creating blinking arrow, if applicable:
			if ($cmpPath && ($GLOBALS['SOBE']->act === 'file' || $GLOBALS['SOBE']->act === 'upload') && $cmpPath==$v['row']['path']) {
				$arrCol='<td><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/blinkarrow_right.gif','width="5" height="9"').' class="c-blinkArrowR" alt="" /></td>';
				$bgColorClass='bgColor4';
			} else {
				$arrCol='<td></td>';
			}
				// Create arrow-bullet for file listing (if folder path is linkable):
			$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&bparams='.$GLOBALS['SOBE']->bparams.$this->getJumpToParam($v['row']).'\');';
			$cEbullet = $this->ext_isLinkable($v['row']) ? '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/ol/arrowbullet.gif','width="18" height="16"').' alt="" /></a>' : '';

				// Put table row with folder together:
			$out.='
				<tr class="'.$bgColorClass.'">
					<td nowrap="nowrap">'.$v['HTML'].$this->wrapTitle(t3lib_div::fixed_lgd_cs($v['row']['title'], $BE_USER->uc['titleLen']), $v['row']).'</td>
					'.$arrCol.'
					<td width="1%">'.$cEbullet.'</td>
				</tr>';
		}

		$out='

			<!--
				Folder tree:
			-->
			<table border="0" cellpadding="0" cellspacing="0" class="typo3-browsetree" style="width:100%">
				'.$out.'
			</table>';
		return $out;
	}


	/**
	 * Returns true if the input "record" contains a folder which can be linked.
	 *
	 * @param	array		Array with information about the folder element. Contains keys like title, uid, path, _title
	 * @return	boolean		True is returned if the path is found in the web-part of the the server and is NOT a recycler or temp folder
	 */
	function ext_isLinkable($v)	{
		$webpath=t3lib_BEfunc::getPathType_web_nonweb($v['path']);	// Checking, if the input path is a web-path.
		if (strstr($v['path'],'_recycler_') || strstr($v['path'],'_temp_') || $webpath!='web')	{
			return 0;
		}
		return 1;
	}

}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionFolder.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionFolder.php']);
}
?>