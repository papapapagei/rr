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
 * @package DAM-Component
 * @subpackage BaseClass
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  101: class tx_dam_selBrowseTree extends t3lib_treeView
 *  163:     function getJumpToParam($row, $command='SELECT')
 *  176:     function wrapTitle($title,$row,$bank=0)
 *  200:     function getControl($title,$row)
 *  232:     function PM_ATagWrap($icon,$cmd,$bMark='')
 *  249:     function getIcon($row)
 *  270:     function getRootIcon($row)
 *  293:     function wrapIcon($icon,$row)
 *  323:     function printTree($treeArr='')
 *  385:     function printRootOnly()
 *  402:     function setMounts($mountpoints)
 *  417:     function getTreeTitle()
 *  426:     function getDefaultIcon()
 *  436:     function getTreeName()
 *
 *              SECTION: DAM specific functions
 *  455:     function selection_getItemTitle($id)
 *  473:     function selection_getItemIcon($id, $value)
 *  494:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *  525:     function tceformsSelect_wrapTitle($title, $row)
 *
 *              SECTION: element browser specific functions
 *  569:     function eb_wrapTitle($title,$row)
 *  588:     function eb_PM_ATagWrap($icon,$cmd,$bMark='')
 *  605:     function eb_printTree($treeArr='')
 *  661:     function ext_isLinkable($row)
 *
 *
 *  676: class tx_dam_browseTree extends tx_dam_selBrowseTree
 *
 *
 *  695: class tx_dam_selProcBase
 *  715:     function tx_dam_selProcBase()
 *  735:     function init()
 *  744:     function getTreeTitle()
 *  753:     function getDefaultIcon()
 *  763:     function getTreeName()
 *
 *              SECTION: Selection specific functions
 *  783:     function selection_getItemTitle($id, $value)
 *  795:     function selection_getItemIcon($id, $value)
 *  815:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *
 * TOTAL FUNCTIONS: 29
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_treeview.php');



/**
 * Base class for selection tree classes
 *
 * In principle this is a more advanced version of tx_dam_selProcBase which includes a treeview.
 * This is a little mixed and might be splitted in the future.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage BaseClass
 * @see tx_dam_selProcBase
 * @see tx_dam_selection
 */
class tx_dam_selBrowseTree extends t3lib_treeView {

	/**
	 * is able to generate a browasable tree
	 */
	var $isTreeViewClass = TRUE;

	/**
	 * is able to generate a tree for a select field in TCEForms
	 */
	var $isTCEFormsSelectClass = false;
	var $TCEFormsSelect_prefixTreeName = false;


	/**
	 * is able to handle mount points (be_users)
	 */
	var $supportMounts = false;

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
	 * indicates if we need to output a root icon
	 */
	var $rootIconIsSet = false;





	/**
	 * If true, no context menu is rendered on icons. If set to "titlelink" the icon is linked as the title is.
	 */
	var $ext_IconMode = true;







	/**
	 * Returns jump-url parameter value.
	 *
	 * @param	array		$row The record array.
	 * @param	string		$command SLCMD['.$command.']...
	 * @return	string		The jump-url parameter.
	 */
	function getJumpToParam($row, $command='SELECT') {
		return '&SLCMD['.$command.']['.$this->treeName.']['.rawurlencode($row['uid']).']=1';
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
		global $BACK_PATH;

		if ($this->mode === 'elbrowser') {
			return $this->eb_wrapTitle($title,$row);

		} elseif ($this->mode === 'tceformsSelect') {
			return $this->tceformsSelect_wrapTitle($title,$row);

		} elseif($row['uid'] OR ($row['uid'] == '0' AND $this->linkRootCat)) {
			return parent::wrapTitle($title,$row);
		}
		return $title;
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
		global $BACK_PATH;
		$control = '';

		if ($this->modeSelIcons
			AND !($this->mode === 'tceformsSelect')
			AND ($row['uid'] OR ($row['uid'] == '0' AND $this->linkRootCat))) {
			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'OR').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/plus.gif', 'width="8" height="11"').' alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'AND').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/equals.gif', 'width="8" height="11"').' alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'NOT').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/minus.gif', 'width="8" height="11"').' alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
		}
		return $control;
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
			$cmd .= $this->PM_addParam;
			return parent::PM_ATagWrap($icon,$cmd,$bMark);
		}
	}


	/**
	 * Get icon for the row.
	 * If $this->iconPath and $this->iconName is set, try to get icon based on those values.
	 *
	 * @param	array		Item row.
	 * @return	string		Image tag.
	 */
	function getIcon($row) {
		if ($this->iconPath && $this->iconName) {
			if (!$this->iconPath_cleaned) {
				$this->iconPath = preg_replace('#^'.preg_quote($GLOBALS['BACK_PATH']).'#', '',$this->iconPath);
				$this->iconPath_cleaned = true;
			}
			$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],$this->iconPath.$this->iconName,'width="18" height="16"').' alt="" />';
			$icon = $this->wrapIcon($icon,$row);
		} else {
			$icon = parent::getIcon($row);
		}

		return $icon;
	}

	/**
	 * Returns the root icon for a tree/mountpoint (defaults to the globe)
	 *
	 * @param	array		Record for root.
	 * @return	string		Icon image tag.
	 */
	function getRootIcon($row) {
		global $BACK_PATH;

		if($this->rootIcon) {
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],$this->rootIcon, 'width="18" height="16"').' alt="" />';
			$icon = $this->wrapIcon($icon,$row);
		} else {
			$icon =  parent::getRootIcon($row);
		}
		$this->rootIconIsSet = true;

		return $icon;
	}


	/**
	 * Wrapping the image tag, $icon, for the row, $row (except for mount points)
	 *
	 * @param	string		The image tag for the icon
	 * @param	array		The row for the current element
	 * @return	string		The processed icon input value.
	 * @access private
	 */
	function wrapIcon($icon,$row)	{
		global $TYPO3_CONF_VARS;

			// Add title attribute to input icon tag
		$theIcon = $this->addTagAttributes($icon,($this->titleAttrib ? $this->titleAttrib.'="'.$this->getTitleAttrib($row).'"' : ''));

			// Wrap icon in click-menu link.
		if (!$this->ext_IconMode)	{
			$theIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($theIcon, $this->table, $this->getId($row), 0);
		} elseif (!strcmp($this->ext_IconMode,'titlelink'))	{
// unused for now
			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row).'\',this,\''.$this->domIdPrefix.$this->getId($row).'_'.$this->bank.'\');';
			$theIcon='<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$theIcon.'</a>';
		}
		return $theIcon;
	}


	/**
	 * Compiles the HTML code for displaying the structure found inside the ->tree array
	 *
	 * @param	array		"tree-array" - if blank string, the internal ->tree array is used.
	 * @return	string		The HTML code for the tree
	 */
	function printTree($treeArr='')	{

			// 0 - show root icon always
		if(!$this->rootIconIsSet AND count($treeArr)) {
				// Artificial record for the tree root, id=0
			$rootRec = $this->getRootRecord(0);
			$firstHtml =$this->getRootIcon($rootRec);

			$treeArr = array_merge(array(array('HTML' => $firstHtml,'row' => $rootRec,'bank'=>0)), $treeArr);
		}

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
						<td  width="1%" id="'.$idAttr.'Control" class="typo3-browsetree-control">'.
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
	 * Return the tree root icon with label
	 *
	 * @return	string
	 */
	function printRootOnly() {
			// Artificial record for the tree root, id=0
		$rootRec = $this->getRootRecord(0);
		$firstHtml =$this->getRootIcon($rootRec);
		$treeArr[] = array('HTML' => $firstHtml,'row' => $rootRec,'bank'=>0);
		$this->rootIconIsSet = true;

		return $this->printTree($treeArr);
	}


	/**
	 * Set mointpoints for the tree
	 *
	 * @param	array		$mountpoints: ...
	 * @return	void
	 */
	function setMounts($mountpoints) {

		if (is_array($mountpoints)) {
			$this->MOUNTS = $mountpoints;
		}
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


	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/



	/**
	 * Returns the title of an item
	 *
	 * @param	string		$id The id of the item
	 * @return	string
	 */
	function selection_getItemTitle($id)	{
		$itemTitle = $id;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',',$this->fieldArray), $this->table, 'uid='.intval($id));
		if($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$itemTitle = $this->getTitleStr($row);
		}
		return $itemTitle;
	}


	/**
	 * Returns the icon of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemIcon($id, $value)	{
		if($icon =	$this->getDefaultIcon()) {
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $icon, 'width="18" height="16"').' class="typo3-icon" alt="" />';
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
	 * @see tx_dam_SCbase::getWhereClausePart()
	 */
	function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)      {
#		return array($queryType,$query);
	}





	/********************************
	 *
	 * TCEForms specific functions
	 *
	 ********************************/


	/**
	 * used inside of select fields (TCEForms)
	 */
	var $TCEforms_itemFormElName='';
	var $TCEforms_nonSelectableItemsArray=array();


	/**
	 * Wrapping $title in a-tags.
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 * @access private
	 */
	function tceformsSelect_wrapTitle($title, $row)	{

		if ($this->parentField AND in_array($row[$this->parentField],$this->TCEforms_nonSelectableItemsArray)) {
			$this->TCEforms_nonSelectableItemsArray[] = $row['uid'];
			$out = '<span class="titleWrap">'.$title.'</span>';

		} elseif (in_array($row['uid'],$this->TCEforms_nonSelectableItemsArray)) {
			$out = '<span class="titleWrap">'.$title.'</span>';

		} else {
			if ($row['uid']) {
				$selectTitle = $this->TCEFormsSelect_prefixTreeName ? $this->getTreeTitle(). ': '.$title : $title;
			} else {
				$selectTitle = $this->getTreeTitle(). ' (Root)';
			}
			$id = $this->TCEFormsSelect_prefixTreeName ? $this->treeName.':'.$row['uid'] : $row['uid'];
			$aOnClick = $this->jsParent.'setFormValueFromBrowseWin(\''.$this->TCEforms_itemFormElName.'\',\''.$id.'\',\''.t3lib_div::slashJS($selectTitle).'\'); return false;';
			if (is_array($this->selectedIdArr) AND in_array($row['uid'], $this->selectedIdArr)) {
				$title = '<span class="titleWrap">'.$title.'</span>';
			}
			$out = '<a style="vertical-align:top;" href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
		}

		return $out;
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
	 * @access private
	 */
	function eb_wrapTitle($title,$row)	{
		if ($row['uid']) {
			$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&bparams='.$GLOBALS['SOBE']->bparams.$this->getJumpToParam($row).'\');';
			return '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
		} else {
			return $title;
		}
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

		$selection = $GLOBALS['SOBE']->browser->damSC->selection->sl->sel;
		if (is_array($selection['SELECT'][$this->treeName])) {
			$cmpItem = key($selection['SELECT'][$this->treeName]);
		}

			// Traverse rows for the tree and print them into table rows:
		foreach($treeArr as $k => $v)	{
			$c++;
			$bgColorClass=($c+1)%2 ? 'bgColor' : 'bgColor-10';
				// Creating blinking arrow, if applicable:
			if ($cmpItem && $GLOBALS['SOBE']->act === 'file' && $cmpItem==$v['row']['uid'])	{
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
					<td nowrap="nowrap">'.$v['HTML'].$this->wrapTitle($this->getTitleStr($v['row'],$BE_USER->uc['titleLen']),$v['row']).'</td>
					'.$arrCol.'
					<td width="1%">'.$cEbullet.'</td>
				</tr>';
		}

		$out='

			<!--
				DAM browse tree:
			-->
			<table border="0" cellpadding="0" cellspacing="0" class="typo3-browsetree" style="width:100%">
				'.$out.'
			</table>';
		return $out;
	}


	/**
	 * Check if the item can be linked
	 *
	 * @param	array		$row: ...
	 * @return	boolean
	 */
	function ext_isLinkable($row) {
		return $row['uid'] ? true : false;
	}
}



/**
 * Base class for selection tree classes
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage BaseClass
 * @deprecated
 */
class tx_dam_browseTree extends tx_dam_selBrowseTree {
}





/**
 * Base class for selection classes
 *
 * This type of selection class do not provide any browse tree. It is just with any GUI.
 * Selection classes are triggered to generate a SQL query part.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage BaseClass
 * @see tx_dam_selection
 * @example ../components/class.tx_dam_selectionStringSearch.php
 */
class tx_dam_selProcBase {


	/**
	 * Defines that it is a pure selection class (without browsetree)
	 */
	var $isPureSelectionClass = TRUE;

	/**
	 * Defines the deselect magic value
	 */
	var $deselectValue = 0;



	/**
	 * constructor
	 *
	 * @return	void
	 */
	function tx_dam_selProcBase()	{
#		global $LANG, $BACK_PATH;

#		$this->isTreeViewClass = FALSE;
#		$this->isPureSelectionClass = TRUE;

#		$this->title=$LANG->sL('LLL:EXT:dam/lib/locallang.xml:mediaTypes');
#		$this->treeName='txdamStrSearch';

#		$this->iconName = 'mediatype.gif';
#		$this->iconPath = $BACK_PATH.PATH_txdam_rel.'i/';

	}


	/**
	 * Initialize the selection class. Can be overwritten
	 *
	 * @return	void
	 */
	function init()	{
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


	/********************************
	 *
	 * Selection specific functions
	 *
	 ********************************/



	/**
	 * Returns the title of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemTitle($id, $value)	{
		return $id;
	}


	/**
	 * Returns the icon of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemIcon($id, $value)	{
		if($icon =	$this->getDefaultIcon()) {
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],$icon, 'width="18" height="16"').' class="typo3-icon" alt="" />';
		}
		return $icon;
	}


	/**
	 * Function, processing the query part for selecting/filtering records in DAM
	 * Called from DAM
	 *
	 * @param	string		$queryType Query type: AND, OR, ...
	 * @param	string		$operator Operator, eg. '!=' - see DAM Documentation
	 * @param	string		$cat Category - corresponds to the "treename" used for the category tree in the nav. frame
	 * @param	string		$id The select value/id
	 * @param	string		$value The select value (true/false,...)
	 * @return	string		where clause
	 * @see tx_dam_SCbase::getWhereClausePart()
	 */
	function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)      {
#		$query= 'tx_dam.tx_damdemo_customcategory';
#		if($operator === '!=') {
#			$query.= ' NOT';
#		}
#		$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike($id,'tx_dam');
#		$query.= ' LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr,'tx_dam');
#
#		return array($queryType,$query);
	}
}


// No XCLASS inclusion code: this is a base class
//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selprocbase.php'])    {
//    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selprocbase.php']);
//}

?>