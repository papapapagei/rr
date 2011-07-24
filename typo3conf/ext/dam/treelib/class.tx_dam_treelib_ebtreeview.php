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
 * Base class for element browser trees
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Treelib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   61: class tx_dam_treelib_ebtreeview extends t3lib_treeView
 *   78:     function setTable($table)
 *
 *              SECTION: element browser specific functions
 *  110:     function wrapTitle($title,$row)
 *  139:     function PM_ATagWrap($icon,$cmd,$bMark='')
 *  156:     function printTree($treeArr='')
 *  218:     function getJumpToParam($row)
 *  229:     function ext_isLinkable($row)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_treeview.php');



/**
 * Base class for element browser trees
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Treelib
 */
class tx_dam_treelib_ebtreeview extends t3lib_treeView {


	/**
	 * If true, no context menu is rendered on icons. If set to "titlelink" the icon is linked as the title is.
	 */
	var $ext_IconMode = true;


	/**
	 * Initialize the tree class. Needs to be overwritten
	 * Will set ->fieldsArray, ->backPath and ->clause
	 *
	 * @param	string		record WHERE clause
	 * @param	string		record ORDER BY field
	 * @return	void
	 */
	function setTable($table)	{
		global $TCA, $LANG;

		$this->thisScript = t3lib_div::getIndpEnv('SCRIPT_NAME');

		$this->table = $table;
		$this->parentField = $GLOBALS['TCA'][$this->table]['ctrl']['treeParentField'];
		$this->title = $LANG->sL($GLOBALS['TCA'][$this->table]['ctrl']['title']);

		parent::init();
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
	function wrapTitle($title,$row)	{
		if ($row['uid']) {
#			$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&bparams='.$GLOBALS['SOBE']->bparams.$this->getJumpToParam($row).'\');';
#			$title = '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';

			$aOnClick = "return insertElement('".$this->table."', '".$row['uid']."', 'db', ".t3lib_div::quoteJSvalue($title).", '', '', '');";
			$ATag = '<a href="#" onclick="'.$aOnClick.'">';
			$ATag_alt = substr($ATag,0,-4).',\'\',1);">';

#			$title = $ATag_alt.$title.'</a>';

			$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/plusbullet2.gif','width="18" height="16"').' title="'.$GLOBALS['LANG']->getLL('addToList',1).'" alt="" />';
			$title = $ATag_alt.$title.'&nbsp;'.$ATag.$icon.'</a>';


		}
		return $title;
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
	function printTree($treeArr='')	{
		global  $BE_USER;


		$titleLen=intval($BE_USER->uc['titleLen']);

		if (!is_array($treeArr))	$treeArr=$this->tree;

		$out='';
		$c=0;

		foreach($treeArr as $k => $v)	{
			$c++;
			$bgColorClass = ($c+1)%2 ? 'bgColor' : 'bgColor-10';

				// Creating blinking arrow, if applicable:
			if ($GLOBALS['SOBE']->browser->curUrlInfo['act'] === 'tree' && $GLOBALS['SOBE']->browser->curUrlInfo['treeid']==$v['row']['uid'] && $GLOBALS['SOBE']->browser->curUrlInfo['treeid'])	{
				$arrCol='<td><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/blinkarrow_right.gif','width="5" height="9"').' class="c-blinkArrowR" alt="" /></td>';
				$bgColorClass='bgColor4';
			} else {
				$arrCol='<td></td>';
			}

			$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?act='.$GLOBALS['SOBE']->browser->act.'&mode='.$GLOBALS['SOBE']->browser->mode.'&bparams='.$GLOBALS['SOBE']->browser->bparams.$this->getJumpToParam($v['row']).'\');';
			$cEbullet = $this->ext_isLinkable($v['row']) ?
						'<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/ol/arrowbullet.gif','width="18" height="16"').' alt="" /></a>' :
						'';
			$out.='
				<tr class="'.$bgColorClass.'">
					<td nowrap="nowrap"'.($v['row']['_CSSCLASS'] ? ' class="'.$v['row']['_CSSCLASS'].'"' : '').'>'.
					$v['HTML'].
					$this->wrapTitle($this->getTitleStr($v['row'],$titleLen),$v['row']).
					'</td>'.
					$arrCol.
					'<td>'.$cEbullet.'</td>
				</tr>';
		}



		$out='

			<!--
				record tree:
			-->
			<table border="0" cellpadding="0" cellspacing="0" class="typo3-browsetree" style="width:100%">
				'.$out.'
			</table>';
		return $out;
	}






	/**
	 * Returns jump-url parameter value.
	 *
	 * @param	array		$row The record array.
	 * @return	string		The jump-url parameter.
	 */
	function getJumpToParam($row) {
		return '&expandTree['.$this->treeName.']='.rawurlencode($row['uid']);
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




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/treelib/class.tx_dam_treelib_ebtreeview.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/treelib/class.tx_dam_treelib_ebtreeview.php']);
}

?>