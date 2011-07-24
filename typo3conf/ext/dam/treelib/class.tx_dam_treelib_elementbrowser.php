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



require_once(PATH_txdam.'treelib/class.tx_dam_treelib_ebtreeview.php');

/**
 * Implements a element browser for trees.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_treelib_elementbrowser extends browse_links {



	var $table = '';
	var $table_parentField = '';
	var $table_infoFields = '';
	var $table_orderBy = '';
	var $table_whereArr = array();

	var $MCONF_name = 'tx_dam_treelib_ebtreeview';


	var $MOD_MENU = array(
				'displayThumbs' => '',
				'extendedInfo' => '',
				'act' => '',
				'mode' => '',
				'bparams' => '',
				'expandFolder' => '',
				'expandPage' => '',
				'expandTree' => '',
				);

	/**
	 * Initializes GP variables
	 *
	 * @return	void
	 */
	function init()	{

		$this->table_parentField = $this->table_parentField ? $this->table_parentField : $GLOBALS['TCA'][$this->table]['ctrl']['treeParentField'];

		$this->table_infoFields = tx_dam_db::getTCAFieldListArray($this->table, true);

		$orderBy = ($GLOBALS['TCA'][$this->table]['ctrl']['sortby']) ? $GLOBALS['TCA'][$this->table]['ctrl']['sortby'] : $GLOBALS['TCA'][$this->table]['ctrl']['default_sortby'];
		$this->table_orderBy = $GLOBALS['TYPO3_DB']->stripOrderBy($orderBy);

		$this->table_whereArr = array();
		$this->table_whereArr['deleted'] = $this->table.'.deleted=0';
		$this->table_whereArr['pid'] = $this->table.'.pid IN ('.tx_dam_db::getPid().')';

		parent::init();
	}


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

		$pArr = explode('|', t3lib_div::_GP('bparams'));

		if ($type === 'db' AND $pArr[3]==$this->table) {
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
			case 'db':
				$this->act = 'dbtree'; // unused for now
				$content = $this->main();
			break;
			default:
			break;
		}

		return $content;
	}




	/**
	 * TYPO3 Element Browser: Showing a tree, allowing you to browse for records.
	 *
	 * @return	string		HTML content
	 */
	function main()	{
		global $BE_USER, $TYPO3_CONF_VARS;
		

			// Starting content:
		$content .= $this->doc->startPage('TBE category tree selector');

			// Making the browsable pagetree:
		$dbTree = t3lib_div::makeInstance('tx_dam_treelib_ebtreeview');
		$dbTree->setTable($this->table);
		$dbTree->thisScript =$this->thisScript;

		$tree = $dbTree->getBrowsableTree();

			// Making the list of elements, if applicable:
		$cElements = $this->recordList(intval($this->expandTree[$dbTree->treeName]));


		$content .= $this->formTag;

			// Putting the things together, side by side:
		$content.= '

			<!--
				Wrapper table for page tree / record list:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBrecords">
				<tr>
					<td class="c-wCell" valign="top">'.$this->barheader($GLOBALS['LANG']->sL($GLOBALS['TCA'][$this->table]['ctrl']['title']).':').$tree.'</td>
					<td class="c-wCell" valign="top">'.$cElements.'</td>
				</tr>
			</table>
			';

			// Add some space
		$content.='<br /><br />';

			// Ending page, returning content:
		$content.= $this->doc->endPage();
		$content = $this->doc->insertStylesAndJS($content);
		return $content;
	}




	/**
	 * Render list of records.
	 *
	 * @param	integer		$parentID
	 * @return	string		HTML output
	 */
	function recordList($parentID) {
		global $LANG, $BACK_PATH;

		$out='';

		$this->table_whereArr['parentField'] = $this->table.'.'.$this->table_parentField.'='.intval($parentID);
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(implode(',', $this->table_infoFields), $this->table, implode (' AND ', $this->table_whereArr), '', $this->table_orderBy);

		$rowParent = false;
		if ($parentID) {
			$this->table_whereArr['parentField'] = $this->table.'.uid='.intval($parentID);
			if ($rowParent = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(implode(',', $this->table_infoFields), $this->table, implode (' AND ', $this->table_whereArr))) {
				$rows = array_merge($rowParent, $rows);
			}
		}


			// Listing the records:
		if (is_array($rows) AND count($rows))	{

			$recCount = count($rows)-1;


				// Create headline
			$out .= $this->barheader($GLOBALS['LANG']->getLL('selectRecords').':');


				// Traverse the list:
			$lines=array();
			foreach($rows as $row)	{

				if (count($lines)==0) {
					$treeLine = '';
				} elseif (count($lines) < $recCount) {
					$LN = 'join';
					$treeLine = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/'.$LN.'.gif','width="18" height="16"').' alt="" />';
				} else {
					$LN = 'joinbottom';
					$treeLine = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/ol/'.$LN.'.gif','width="18" height="16"').' alt="" />';
				}


				$recIconFile = t3lib_iconWorks::getIcon($this->table, $row);
				$recIcon = t3lib_iconWorks::getIconImage($this->table, $row, $BACK_PATH,'');
				$recTitle = t3lib_BEfunc::getRecordTitle($this->table, $row, true);

				$aOnClick = "return insertElement('".$this->table."', '".$row['uid']."', 'db', ".t3lib_div::quoteJSvalue($recTitle).", '', '', '".$recIconFile."');";
				$ATag = '<a href="#" onclick="'.$aOnClick.'">';
				$ATag_alt = substr($ATag,0,-4).',\'\',1);">';
				$ATag_e = '</a>';

					// Combine the stuff:
				$IconAndTitle = $ATag_alt.$recIcon.$recTitle.$ATag_e;


				$lines[]='
					<tr class="bgColor4">
						<td nowrap="nowrap">'.$treeLine.$IconAndTitle.'&nbsp;</td>
						<td>'.$ATag.'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/plusbullet2.gif','width="18" height="16"').' title="'.$LANG->getLL('addToList',1).'" alt="" />'.$ATag_e.'</td>
					</tr>';

			}

				// Wrap all the rows in table tags:
			$out.='



		<!--
			Record listing
		-->
				<table border="0" cellpadding="0" cellspacing="1" id="typo3-tree">
					'.implode('',$lines).'
				</table>';
		}

			// Return accumulated content for listing:
		return $out;
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
			$MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, array(), $this->MCONF_name, '', 'expandTree');
		}
		if($key) {
			if ($key === 'expandTree') {
				$expandTree = unserialize($MOD_SETTINGS[$key]);
				return is_array($expandTree) ? $expandTree : array();
			} else {
				return $MOD_SETTINGS[$key];
			}
		} else {
			return $MOD_SETTINGS;
		}
	}

	/**
	 * Save $MOD_SETTINGS array
	 *
	 * @return	void
	 */
	function saveModSettings() {

			$settings = array();

				// save params in session
			if ($this->act) $settings['act'] = $this->act;
			if ($this->mode) $settings['mode'] = $this->mode;
			if ($this->bparams) $settings['bparams'] = $this->bparams;
			if ($this->expandFolder) $settings['expandFolder'] = $this->expandFolder;
			if ($this->expandPage) $settings['expandPage'] = $this->expandPage;
			if ($this->expandTree) $settings['expandTree'] = serialize($this->expandTree);
			if ($this->pointer) $settings['pointer'] = $this->pointer;
			if ($this->P) $settings['P'] = $this->P;
			if ($this->RTEtsConfigParams) $settings['RTEtsConfigParams'] = $this->RTEtsConfigParams;
			if ($this->PM) $settings['PM'] = $this->PM;

			t3lib_BEfunc::getModuleData($this->MOD_MENU, $settings, $this->MCONF_name, '', 'expandTree');
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

		$this->act = $this->getParam('act');
		$this->mode = $this->getParam('mode');
		$this->bparams = $this->getParam('bparams');
		$this->expandFolder = $this->getParam('expandFolder');
		$this->expandPage = $this->getParam('expandPage');
		$this->expandTree = array_merge($this->getModSettings('expandTree'), $this->getParam('expandTree'));

		$this->pointer = $this->getParam('pointer');
		$this->P = $this->getParam('P');
		$this->RTEtsConfigParams = $this->getParam('RTEtsConfigParams');
		$this->PM = $this->getParam('PM');

		$this->saveModSettings();

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
	 * Returns the value of a param was passed by GET OR POST
	 *
	 * @param string $paramName Param name
	 * @return string
	 */
	function getParam ($paramName) {
		return $this->isParamPassed ($paramName) ? t3lib_div::_GP($paramName) : $this->getModSettings($paramName);
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
		$GLOBALS['SOBE']->browser->expandPage = $GLOBALS['SOBE']->expandPage = $this->addParams['expandFolder'] = $this->expandPage;

		$this->formTag = '<form action="'.htmlspecialchars(t3lib_div::linkThisScript($this->addParams)).'" method="post" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/treelib/class.tx_dam_treelib_elementbrowser.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/treelib/class.tx_dam_treelib_elementbrowser.php']);
}

?>