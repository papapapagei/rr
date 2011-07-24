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
 *  122: class tx_dam_listbase
 *
 *              SECTION: Setup
 *  301:     function tx_dam_listbase()
 *  312:     function __construct()
 *  328:     function clearColumns()
 *  340:     function addColumn($name, $label)
 *  356:     function removeColumn($name)
 *  370:     function setCurrentSorting ($sortField, $sortRev)
 *  383:     function setParameterName ($name, $value)
 *  396:     function setParameterNames ($sortField, $sortRev)
 *  408:     function setPointer($pointer)
 *
 *              SECTION: Set data
 *  429:     function addData($dataObject, $idName='')
 *
 *              SECTION: Table rendering
 *  451:     function getListTable()
 *  496:     function renderTable()
 *  515:     function renderHeader ()
 *  549:     function renderList()
 *  614:     function renderMultiActionBar ()
 *  695:     function renderFooter ()
 *
 *              SECTION: Column rendering
 *  713:     function getItemColumns ($item)
 *  750:     function getItemMultiAction ($item)
 *  761:     function getItemAction ($item)
 *  772:     function getItemIcon ($item)
 *
 *              SECTION: Row rendering
 *  812:     function addRow($setup, $position='')
 *  865:     function addRowRenderTR($setup, $td, $position='')
 *  897:     function addRowSpecialColumns($setup, &$td)
 *  942:     function addRowRaw ($content)
 *
 *              SECTION: Controls
 *  962:     function getHeaderControl()
 *  973:     function getHeaderColumnControl($field)
 *  984:     function getItemControl($item)
 *  996:     function getMultiActions()
 *
 *              SECTION: Browsing
 * 1014:     function addRowBrowse($type)
 * 1034:     function fwd_rwd_HTML($type)
 *
 *              SECTION: Link and title rendering
 * 1073:     function cropTitle ($title, $field)
 * 1089:     function linkWrapDir($title, $path)
 * 1114:     function linkWrapFile($title, $pathInfo)
 * 1147:     function linkWrapSort($title, $column)
 *
 *              SECTION: Misc
 * 1194:     function thumbnailPossible ($item)
 * 1207:     function getThumbnail($filepath, $addAtrr='', $size='')
 * 1217:     function getJsCode()
 * 1219:     function tx_dam_listbase_setCheckboxes(the_form)
 * 1246:     function getFilePermString ($perms)
 *
 *              SECTION: Clipboard
 * 1320:     function clipboard_linkHeaderIcon($string, $table, $cmd, $warning='')
 * 1332:     function clipboard_getItemControl($columns)
 * 1377:     function clipboard_getHeaderControl ()
 *
 * TOTAL FUNCTIONS: 42
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');



/**
 * Class for rendering of Media>File>List
 * The class is not really abstract but on a good way to become so ...
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_listbase {




	/**
	 * if set field wrap is enabled
	 */
	var $enableFieldWrap = false;

	/**
	 * if set sorting by clicking on the header is possible
	 */
	var $enableSorting = true;

	/**
	 * if set file links will be created to show the file in popup window
	 */
	var $enableFilePopup = true;

	/**
	 * if set browsing by clicking on (eg) folder titles is possible
	 */
	var $enableBrowsing = true;

	/**
	 * if set context menus will be added to items like icons
	 */
	var $enableContextMenus = false;

	/**
	 * If enabled titles will not be shortend to $titleLength but 200 and field wrap for title will be eneabled
	 */
	var $showfullTitle = false;

	/**
	 * max title length if field wrap is disabled
	 */
	var $titleLength = 30;
	
	/**
	 * enable display of thumbnails for images
	 */
	var $showThumbs = true;

	/**
	 * enable alternating background colors in table rows
	 */
	var $showAlternateBgColors = false;

	/**
	 * enable display of icon column which is the second
	 */
	var $showIcons = true;

	/**
	 * enable display of actions generally
	 */
	var $showControls = true;

	/**
	 * enable display of action column which is the first
	 */
	var $showActions = false;

	/**
	 * enable display of multi-actions which is a checkbox for each item and a bar below the list with options process for the selected items
	 */
	var $showMultiActions = false;

	/**
	 * environment array for actions
	 * @see setActionsEnv()
	 */
	var $actionsEnv = array();

	/**
	 * array of selected items for multi-actions
	 */
	var $recs = array();



	/**
	 * defines the columns to display and provide a language label
	 */
	var $columnList = array();

	/**
	 * defines the key of the title column for columnList
	 */
	var $titleColumnKey;

	/**
	 * The current sorting field
	 * Just for display
	 */
	var $sortField = '';

	/**
	 * Defines if reverse sorting is enabled or not
	 * Just for display
	 */
	var $sortRev = false;

	/**
	 * Keys are fieldnames and values are td-parameters to add in addRow();
	 */
	var $columnTDAttr = array();

	/**
	 * stores html table rows
	 */
	var $tableRows = array();

	/**
	 * array of parameter names used in links and names of form elements
	 */
	var $paramName = array(
		'form' => 'listform',
		'sortField' => 'sortField',
		'sortRev' => 'sortRev',
		'recs' => 'recs',
		'multi_action' => 'multi_action',
		'multi_action_target' => 'multi_action_target',
		'multi_action_submit' => 'multi_action_submit',
	);

	/**
	 * additional attributes for some elements
	 */
	 var $elementAttr = array(
	 	'table' => ' border="0" cellpadding="0" cellspacing="0" style="width:100%" class="typo3-dblist"',
		'headerTD' => ' nowrap="nowrap" class="c-headLine"',
		'itemTD' => ' class="typo3-dblist-item"',
		'multiActionTD' => ' width="1%" valign="top" align="left" nowrap="nowrap"',
		'multiActionBarTD' => ' nowrap="nowrap" class="c-actionBar"',
		'actionTD' => ' width="1%" valign="top" align="left" nowrap="nowrap"',
		'iconTD' => ' width="1%" valign="top" align="left" nowrap="nowrap"',
		'dataTD' => ' valign="top"',

	 );

	/**
	 * additional css styles for some elements
	 */
	 var $elementStyle = array(
	 	'table' => '',
		'headerTD' => 'border-bottom:1px solid #888;',
		'itemTD' => '',
		'multiActionTD' => 'padding: 3px 0px 0px 5px;',
		'multiActionBarTD' => '',
		'actionTD' => 'padding: 3px 0px 0px 5px;',
		'iconTD' => 'padding-left:5px;',
		'dataTD' => 'padding-left:5px;',

	 );


// todo Clipboard

	/**
	 * If true click menus are generated on files and folders
	 */
	var $clickMenus = false;
	var $clipBoard = false;
	var $CBnames = array();

##################



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
	function tx_dam_listbase()	{
		$this->__construct();
	}


	/**
	 * Initialize the object
	 * PHP5 constructor
	 *
	 * @return	void
	 */
	function __construct() {
		global $BE_USER;

		$this->showThumbs = $BE_USER->uc['thumbnailsByDefault'];

		$this->colorTRHover = $GLOBALS['SOBE']->doc->hoverColorTR ? $GLOBALS['SOBE']->doc->hoverColorTR : t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-20,-20,-20);
		$this->colorTREven = t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-5,-5,-5);
		$this->colorTROdd = t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-10,-10,-10);
	}


	/**
	 * Clears the list of columns for display
	 *
	 * @return	void
	 */
	function clearColumns() {
		$this->columnList = array();
	}


	/**
	 * Add a columns for display
	 *
	 * @param	string		$name Column field name
	 * @param	string		$label Language label for header
	 * @return	void
	 */
	function addColumn($name, $label) {
		$this->columnList[$name] = array(
				'name' => $name,
				'label' => $label,
			);
		reset($this->columnList);
		$this->titleColumnKey = key($this->columnList);
	}


	/**
	 * Removes a column
	 *
	 * @param	string		$name Column field name
	 * @return	void
	 */
	function removeColumn($name) {
		unset($this->columnList[$name]);
		reset($this->columnList);
		$this->titleColumnKey = key($this->columnList);
	}


	/**
	 * Set the current sorting definition
	 *
	 * @param	string		$sortField Column field name
	 * @param	boolean		$sortRev Forward or reverse sorting
	 * @return	void
	 */
	function setCurrentSorting ($sortField, $sortRev) {
		$this->sortField = $sortField;
		$this->sortRev = $sortRev;
	}


	/**
	 * Defines the a parameter name used for links
	 *
	 * @param	string		$sortField
	 * @param	string		$sortField
	 * @return	void
	 */
	function setParameterName ($name, $value) {
		$this->paramName[$name] = $value;
	}


	/**
	 * Defines the sorting parameter names used for links
	 *
	 * @param	string		$sortField
	 * @param	boolean		$sortRev
	 * @return	void
	 * @deprecated
	 */
	function setParameterNames ($sortField, $sortRev) {
		$this->paramName['sortField'] = $sortField;
		$this->paramName['sortRev'] = $sortRev;
	}


	/**
	 * Set the pointer object
	 *
	 * @param	object		$pointer
	 * @return	void
	 */
	function setPointer($pointer) {
		$this->pointer = $pointer;
	}


	/**
	 * Set action environment
	 *
	 * @param	array		$actionsEnv
	 * @return	void
	 */
	function setActionsEnv($actionsEnv) {
		$this->actionsEnv = $actionsEnv;
	}
	
	


	/***************************************
	 *
	 *	 Process params and commands
	 *
	 ***************************************/


	/**
	 * Returns a table with directories and files listed.
	 *
	 * @return	void
	 */
	function processParams()	{
		if ($this->paramName['recs'] AND !count($this->recs)) {
			$recs = t3lib_div::_GP($this->paramName['recs']);
			if ($this->table AND is_array($recs[$this->table])) {
				$this->recs = $recs[$this->table];
			} elseif (!$this->table AND is_array($recs)) {
				$this->recs = $recs;
			}
		}
	}


	/**
	 * Test submitted data if a command should be processed and return an array with command values
	 *
	 * @return mixed FALSE if nor action should be performed or an array with information about the action
	 */
	function getMultiActionCommand() {
		if (t3lib_div::_GP($this->paramName['multi_action_submit']) AND $action=t3lib_div::_GP($this->paramName['multi_action'])) {
			$processAction = array();

			list ($processAction['actionType'], $processAction['action']) = explode(':', $action, 2);

			if (t3lib_div::_GP($this->paramName['multi_action_target']) === 'all') {
				 $processAction['onItems'] = '_all';
			} else {
				 $processAction['onItems'] = implode(',', $this->recs);
			}
			return $processAction;
		}
		return false;
	}
	
	
	

	/***************************************
	 *
	 *	 Set data
	 *
	 ***************************************/


	/**
	 * Set data objects which provides the data to display
	 *
	 * @param	object		$dataObject data object. Eg. tx_dam_dir
	 * @param	mixed		$idName Key which is used to store the object in $this->dataObjects[$idName]
	 * @return	void
	 */
	function addData($dataObject, $idName='')	{
		$idName = $idName ? $idName : uniqid('tx_dam_listbase');
		$this->dataObjects[$idName] = $dataObject;
	}






	/***************************************
	 *
	 *	 Table rendering
	 *
	 ***************************************/


	/**
	 * Returns a table with directories and files listed.
	 *
	 * @return	string		HTML-table
	 */
	function getListTable()	{


			// add rewind browse button
		$this->addRowBrowse('rwd');

			// add item list or empty row
		if($this->pointer->countTotal) {
			$this->renderList();

			if ($this->showMultiActions)	{
				$this->renderMultiActionBar();
			}

		} else {
			$this->addRow(array(
							'data' => array($this->titleColumnKey => '&nbsp;',
							'tdStyle' => 'border-bottom:1px solid #888;'
						)));
		}

			// add forward browse button
		$this->addRowBrowse('fwd');

			// add bottom line
#		$this->addRow(array('tdStyle' => 'border-top:1px solid #888;'));


			// add header - column titles with sorting links
			// this is added after the list is rendered because we might need info's from the list
		$this->renderHeader();

			// add footer (eg. counter) info line
		$this->renderFooter();

			// wrap the table around and return HTML
		return $this->renderTable();
	}


	/**
	 * Returns a table with items listed.
	 *
	 * @return	string		HTML-table
	 */
	function renderTable()	{

		return '


		<!--
			list table:
		-->
			<table '.$this->elementAttr['table'].'>
				'.implode('', $this->tableRows).'
			</table>';
	}


	/**
	 * Adds a header row with column titles with sorting links
	 *
	 * @return	void
	 */
	function renderHeader () {
		global $LANG, $BACK_PATH;

		$columns = array();
		foreach($this->columnList as $field => $descr)	{
			if ($field === '_CLIPBOARD_' AND is_object($this->clipboard))	{
				$columns[$field] = $this->clipboard->getHeaderControl();
			} elseif ($field === '_CONTROL_')	{
				$columns[$field] = $this->getHeaderControl();
			} else {
				$columns[$field] = $this->getHeaderColumnControl($field);
				$columns[$field] .= $this->linkWrapSort($descr['label'], $field);
			}
			if ($columns[$field] == '') {
				$columns[$field] = '&nbsp;';
			}
		}
		if ($this->showMultiActions) {
			$multiAction = '<a href="#" onclick="tx_dam_listbase_setCheckboxes(\''.$this->paramName['form'].'\'); return false;"><img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/clip_select.gif','width="12" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.php:clip_markRecords',1).'" alt="" /></a>';
		}
		$this->addRow(array(
				'data' => $columns,
				'multiAction' => $multiAction,
				'tdAttribute' => $this->elementAttr['headerTD'],
				'tdStyle' => $this->elementStyle['headerTD'],
			), 'top');
	}


	/**
	 * This renders tablerows for the directory
	 *
	 * @return	void
	 */
	function renderList()	{
		$allItemCount = 0;
		$pageItemCounter = 0;

		foreach ($this->dataObjects as $list) {

			if ($list->count())	{

				$tdStyleAppend = '';

				while ($list->valid()) {

					$item = $list->current();

					$allItemCount++;

					if (($allItemCount > $this->pointer->firstItemNum) AND ($pageItemCounter < $this->pointer->itemsPerPage))	{

						$pageItemCounter++;

							// 	Columns rendering
						if ($this->showMultiActions)	$itemMultiAction = $this->getItemMultiAction ($item);
						if ($this->showAction)	$itemAction = $this->getItemAction ($item);
						if ($this->showIcon)	$itemIcon = $this->getItemIcon ($item);
						$itemColumns = $this->getItemColumns ($item);

						# $trStyle = '';
						$trStyle = ' background-color:'.$this->colorTREven.';';
						if ($this->showAlternateBgColors) {
							if ($allItemCount % 2) {
								$trStyle = ' background-color:'.$this->colorTREven.';';
							}
							else {
								$trStyle = ' background-color:'.$this->colorTROdd.';';
							}
						}

							// this is the last line which should have a line afterwards
						if (($allItemCount > $this->pointer->lastItemNum) OR ($pageItemCounter >= $this->pointer->itemsPerPage)) {
							$tdStyleAppend = ' border-bottom:1px solid #888;';
						}

						$this->addRow(	array(
								'multiAction' => $itemMultiAction,
								'action' => $itemAction,
								'icon' => $itemIcon,
								'data' => $itemColumns,
								'tdAttribute' => $this->elementAttr['itemTD'],
								'tdStyle' => $this->elementStyle['itemTD'].$tdStyleAppend,
								'trStyle' => $trStyle,
								'trHover' => true,
							));
					}
					$list->next();
				}
			}
		}
	}


	/**
	 * Adds a footer row with options for multi-actions
	 *
	 * @return	boolean Return true if an action bar was successfully rendered
	 */
	function renderMultiActionBar () {
		global $LANG, $BACK_PATH;

		if ($this->showMultiActions) {


			//
			// collect the valid actions
			//

			$actions = $this->getMultiActions();
			if (!$actions) return false;


			//
			// Create select boxes
			//

			$options = array();
			foreach ($actions as $action) {
				$options[] = '<option value="'.htmlspecialchars($action['action']).'">'.htmlspecialchars($action['label']).'</option>';
			}
			$actionSelect = '
							<select name="'.$this->paramName['multi_action'].'">
								<option value="">-- '.$GLOBALS['LANG']->getLL('actionSelect',true).' --</option>
								'.implode("\n", $options).'
							</select>';

			$options = array();
			$options[] = '<option value="selected">'.$GLOBALS['LANG']->getLL('doForSelected',true).'</option>';
			$options[] = '<option value="all">'.$GLOBALS['LANG']->getLL('doForFullList',true).'</option>';
			$actionSelectTarget = '
							<select name="'.$this->paramName['multi_action_target'].'">
								'.implode("\n", $options).'
							</select>';


			//
			// build table row
			//

			$td = array();

			$setup = array(
				'multiAction' => '',
				'tdAttribute' => $this->elementAttr['multiActionBarTD'],
				'tdStyle' => $this->elementStyle['multiActionBarTD'],
			);

				// add checkbox toggle icon
			$setup['multiAction'] = '<a href="#" onclick="tx_dam_listbase_setCheckboxes(\''.$this->paramName['form'].'\'); return false;"><img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/clip_select.gif','width="12" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.php:clip_markRecords',1).'" alt="" /></a>';
			$this->addRowSpecialColumns($setup, $td);

				// compile row content
			$onclick = 'if(document.forms[\''.$this->paramName['form'].'\'][\''.$this->paramName['multi_action'].'\'].options[0].selected!=true) {document.forms[\''.$this->paramName['form'].'\'][\''.$this->paramName['multi_action_submit'].'\'].value=1; document.forms[\''.$this->paramName['form'].'\'].submit()}; return false;';
			$submitButton = '<input type="button" class="button" onclick="'.htmlspecialchars($onclick).'" value="'.$GLOBALS['LANG']->getLL('submitMultiAction',true).'">';
			$submitButton .= '<input type="hidden" name="'.$this->paramName['multi_action_submit'].'" value="">';
			$content = sprintf ('%s &nbsp;&nbsp;'.$GLOBALS['LANG']->getLL('for',true).'&nbsp;&nbsp; %s %s', $actionSelect, $actionSelectTarget, $submitButton);

				// compile table row
			$tdAttribute = ' colspan="'.count($this->columnList).'"';
			$td[] = '
				<td '.$this->elementAttr['multiActionBarTD'].' style="'.$this->elementStyle['multiActionBarTD'].'"'.
				$tdAttribute.
				'><div>'.$content.'</div></td>';

				// finally add the row to table
			$this->addRowRenderTR($setup, $td);

			return true;

		}
		return false;
	}


	/**
	 * Add a footer to the table
	 *
	 * @return	void
	 */
	function renderFooter () {
	}



	/***************************************
	 *
	 *	 Column rendering
	 *
	 ***************************************/


	/**
	 * Renders the data columns
	 *
	 * @param	array		$item item array
	 * @return	array
	 */
	function getItemColumns ($item) {
		$columns = array();
		foreach($this->columnList as $field => $descr)	{

			switch($field)	{
				case '_CLIPBOARD_':
					if(is_object($this->clipboard)) {
						$columns[$field] = $this->clipboard->getItemControl($item);
					}
				break;
				case '_CONTROL_':
					 $columns[$field] = $this->getItemControl($item);
					 $this->columnTDAttr[$field] = ' nowrap="nowrap"';
				break;
				default:
					$columns[$field] = htmlspecialchars(t3lib_div::fixed_lgd_cs($item[$field], $this->titleLength));
				break;
			}
			if ($columns[$field] === '') {
				$columns[$field] = '&nbsp;';
			}
		}

			// Thumbsnails?
		if ($this->showThumbs AND $this->thumbnailPossible($item))	{
			$columns['title'] .= '<div style="margin:2px 0 2px 0;">'.$this->getThumbNail($item['file_path_absolute'].$item['file_name']).'</div>';
		}
		return $columns;
	}


	/**
	 * Renders the multi-action
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemMultiAction ($item) {
		return '';
	}


	/**
	 * Renders the action
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemAction ($item) {
		return '';
	}


	/**
	 * Renders the item icon
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemIcon ($item) {
		return '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/i/default_gray1.gif', 'width="18" height="16"').' alt="" />';
	}



	/***************************************
	 *
	 *	 Row rendering
	 *
	 ***************************************/


	/**
	 * Returns a table-row with the content from the fields in the input data array.
	 * OBS: $this->columnList MUST be set! (represents the list of fields to display)
	 *
	 * Paramater have to be passed as array:
	 * 	$setup = array(
	 * 	'action' => '',
	 * 	'icon' => '',
	 * 	'data' => '',
	 * 	'tdAttribute' => '',
	 * 	'tdStyle' => '',
	 * 	'trStyle' => '',
	 * 	'trHover' => '',
	 * 	);
	 *
	 * param	string		$action Could be a checkbox or button as action for this element. Leave blank if not needed. (global enable/disable with $this->showAction)
	 * param	string		$icon is the <img>+<a> of the entry.
	 * param	array		$data is the data array, record with the fields. Notice: These fields are (currently) NOT htmlspecialchar'ed before being wrapped in <td>-tags
	 * param	string		$tdAttribute is inserted in the <td>-tags.
	 * param	string		$tdStyle is inserted in the <td>-tags as additional css style.
	 * param	string		$trStyle is inserted in the <tr>-tag as additional css style. Might inlcude 'background-color' which will be detected for use as default color when tr-hover is enabled.
	 * param	boolean		$trHover If set hover color is enabled for the row;
	 *
	 * @param	array		$setupSetup array
	 * @param	string		$position If position is 'top' the line will be inserted on top of the table
	 * @return	string		HTML content for the table row
	 */
	function addRow($setup, $position='')	{

		$data = array();
		$tdAttribute = '';
		$tdStyle = '';
		$trStyle = '';
		$trHover = false;
		extract ($setup, EXTR_IF_EXISTS);


		$td = array();

		$this->addRowSpecialColumns($setup, $td);


		$tdAttribute = $tdAttribute ? ' '.$tdAttribute : '';


			// Traverse field array which contains the data to present:
		foreach($this->columnList as $field => $descr)	{

			if ($field=='_CONTROL_') {
				$tdAttribute .= ' width="1%"';
			}

			if(isset($this->columnNoWrap[$field])) {
				$noWrap = ($this->columnNoWrap[$field]) ? ' nowrap="nowrap"' : '';
			}
			else {
				$noWrap = ($this->enableFieldWrap) ? '' : ' nowrap="nowrap"';
			}

			$td[] = '
				<td '.$this->elementAttr['dataTD'].' style="'.$this->elementStyle['dataTD'].$tdStyle.'"'.
				$noWrap.
				$tdAttribute.
				$this->columnTDAttr[$field].
				'>'.($data[$field]==='' ? '<span><br /></span>' : (string)$data[$field]).'</td>';
		}

		$this->addRowRenderTR($setup, $td, $position);
	}


	/**
	 * Renders the td array as TR
	 *
	 *
	 * @param	array		$setupSetup array
	 * @param	string		$td The td array
	 * @param	string		$position If position is 'top' the line will be inserted on top of the table
	 * @return	string		HTML content for the table row
	 */
	function addRowRenderTR($setup, $td, $position='')	{

		$trStyle = '';
		$trHover = false;
		extract ($setup, EXTR_IF_EXISTS);

			// make hover for TR
		$match = array();
		preg_match('/background-color[ ]*:[ ]*(#[0-9a-f]+)/', $trStyle, $match);
		$trHover = $trHover ? (' onmouseover="this.style.backgroundColor = \''.$this->colorTRHover.'\';" onmouseout="this.style.backgroundColor = \''.$match[1].'\';"') : '';
		$trStyle = $trStyle ? ' style="'.$trStyle.'"' : '';

		$out='
		<!-- Element, begin: -->
		<tr'.$trStyle.$trHover.'>'.implode('', $td).'</tr>';

		if ($position === 'top') {
			array_unshift($this->tableRows, $out);
		} else {
			$this->tableRows[] = $out;
		}
	}


	/**
	 * Adds multi-actions, actions and icons to the td array
	 *
	 *
	 * @param	array		$setupSetup array
	 * @param	string		$td The td array as reference
	 * @return	string		HTML content for the table row
	 */
	function addRowSpecialColumns($setup, &$td)	{

		$multiAction = '';
		$action = '';
		$icon = '';
		$tdAttribute = '';
		$tdStyle = '';
		extract ($setup, EXTR_IF_EXISTS);

		$td = array();

		$tdAttribute = $tdAttribute ? ' '.$tdAttribute : '';

			// Show action checkbox
		if ($this->showMultiActions)	{
			$td[] = '
			<td'.$this->elementAttr['multiActionTD'].$tdAttribute.' style="'.$this->elementStyle['multiActionTD'].$tdStyle.'">'.
			($multiAction ? $multiAction : '<span><br /></span>').
			'</td>';
		}

			// Show action
		if ($this->showActions)	{
			$td[] = '
			<td'.$this->elementAttr['actionTD'].$tdAttribute.' style="'.$this->elementStyle['actionTD'].$tdStyle.'">'.
			($action ? $action : '<span><br /></span>').
			'</td>';
		}

			// Show icon
		if ($this->showIcons)	{
			$td[] = '
			<td'.$this->elementAttr['iconTD'].$tdAttribute.' style="'.$this->elementStyle['iconTD'].$tdStyle.'">'.
			($icon ? $icon : '<span><br /></span>').
			'</td>';
		}
	}


	/**
	 * Add a list row as raw eg HTML
	 *
	 * @param	string		$content
	 * @return	void
	 */
	function addRowRaw ($content) {
		$this->tableRows[] = $content;
	}





	/***************************************
	 *
	 *	 Controls
	 *
	 ***************************************/


	/**
	 * Creates the control panel for the header.
	 *
	 * @return	string		control panel (unless disabled)
	 */
	function getHeaderControl() {
		return '';
	}


	/**
	 * Creates the column control panel for the header.
	 *
	 * @param	string		$field Column key
	 * @return	string		control panel (unless disabled)
	 */
	function getHeaderColumnControl($field) {
		return '';
	}


	/**
	 * Creates the control panel for a single record in the listing.
	 *
	 * @param	array		The record for which to make the control panel.
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function getItemControl($item)	{
		return '';
	}


	/**
	 * Returns an array of multi actions to be rendered by renderMultiActionBar()
	 *
	 * @return array
	 * @see tx_dam_actionCall::renderMultiActions()
	 * @see renderMultiActionBar()
	 */
	function getMultiActions() {
		return false;
	}


	/***************************************
	 *
	 *	 Browsing
	 *
	 ***************************************/


	/**
	 * Creates a forward/reverse button based on the status of $this->pointer
	 *
	 * @param	string		$type Type name: fwd, rwd
	 * @return	void
	 */
	function addRowBrowse($type)	{
		$columns = array();
		if ($type=='fwd')	{
			if($this->pointer->lastItemNum < ($this->pointer->countTotal-1)) {
				$columns[$this->titleColumnKey] = $this->fwd_rwd_HTML('fwd');
				$this->addRow(array('data' => $columns));
			}
		} elseif ($this->pointer->page) {
			$columns[$this->titleColumnKey] = $this->fwd_rwd_HTML('rwd');
			$this->addRow(array('data' => $columns));
		}
	}


	/**
	 * Creates the button with link to either forward or reverse
	 *
	 * @param	string		Type: "fwd" or "rwd"
	 * @return	string		HTML
	 */
	function fwd_rwd_HTML($type)	{
		$content = '';

		switch($type)	{
			case 'fwd':
				$href = t3lib_div::linkThisScript(array($this->pointer->pagePointerParamName => $this->pointer->getPagePointer(1)));
				$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pildown.gif', 'width="14" height="14"').' alt="" />';
				$content = '&nbsp;<a href="'.htmlspecialchars($href).'">'.
						$icon.
						'</a> <i>['.($this->pointer->lastItemNum+1).' - '.min($this->pointer->lastItemNum + 1 + $this->pointer->itemsPerPage, $this->pointer->countTotal).']</i>';
			break;
			case 'rwd':
				$href = t3lib_div::linkThisScript(array($this->pointer->pagePointerParamName => $this->pointer->getPagePointer(-1)));
				$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pilup.gif', 'width="14" height="14"').' alt="" />';
				$content = '&nbsp;<a href="'.htmlspecialchars($href).'">'.
						$icon.
						'</a> <i>['.max(1, $this->pointer->firstItemNum - $this->pointer->itemsPerPage).' - '.($this->pointer->firstItemNum - 1).']</i>';
			break;
		}
		return $content;
	}




	/***************************************
	 *
	 *	 Link and title rendering
	 *
	 ***************************************/


	/**
	 * Crop the title to adefined lenght or set the wrapping off for long titles
	 *
	 * @param	string		$title Title string
	 * @param	string		$field Field name needed to disbale wrapping if needed
	 * @return	string		Title string
	 */
	function cropTitle ($title, $field) {
		$title = t3lib_div::fixed_lgd_cs($title, ($this->showfullTitle ? 200: $this->titleLength));
		if($this->showfullTitle) {
			$this->columnNoWrap[$field] = false;
		}
		return $title;
	}


	/**
	 * Wraps the directory-titles
	 *
	 * @param	string		$title String to be wrapped in links
	 * @param	string		$path Path
	 * @return	string		HTML
	 */
	function linkWrapDir($title, $path)	{

			// Sometimes $title contains HTML tags. In such a case the string should not be modified!
		$titleAttribute = '';
		if(!strcmp($title,strip_tags($title)))	{
			$title = htmlspecialchars($title);
			$titleAttribute = ' title="'.htmlspecialchars($title).'"';
		}

		if ($this->enableBrowsing) {
			$href = t3lib_div::linkThisScript(array($this->paramName['setFolder'] => $path));
			return '<a href="'.htmlspecialchars($href).'"'.$titleAttribute.'>'.$title.'</a>';
		} else {
			return $title;
		}
	}


	/**
	 * Wraps filenames in links which opens them in a window IF they are in web-path.
	 *
	 * @param	string		$title String to be wrapped in link
	 * @param	string		$pathInfo
	 * @return	string		A tag
	 */
	function linkWrapFile($title, $pathInfo)	{

		if (!$this->enableFilePopup) {
			return htmlspecialchars($title);
		}

		if(!isset($pathInfo['file_path_absolute'])) {
			$pathInfo['file_path_absolute'] = tx_dam::path_makeAbsolute($pathInfo['file_path']);
		}

		if (t3lib_div::isFirstPartOfStr($pathInfo['file_path_absolute'], PATH_site))	{

			$href = tx_dam::file_relativeSitePath ($pathInfo['file_path_absolute'].$pathInfo['file_name']);
			$aOnClick = "return top.openUrlInWindow('".t3lib_div::getIndpEnv('TYPO3_SITE_URL').$href."','WebFile');";

			if(!strcmp($title,strip_tags($title)))	{
				return '<a href="'.htmlspecialchars($href).'" onclick="'.htmlspecialchars($aOnClick).'" title="'.htmlspecialchars($title).'">'.htmlspecialchars($title).'</a>';
			} else	{
				return '<a href="'.htmlspecialchars($href).'" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
			}
		}

		return $title;
	}


	/**
	 * Wraps the directory-titles ($code) in a link to file_list.php (id = $path) and sorting commands...
	 *
	 * @param	string		$title String to be wrapped
	 * @param	string		$column Column field name
	 * @return	string		HTML
	 */
	function linkWrapSort($title, $column)	{
		$content = '';

		if ($this->enableSorting) {
			if ($this->sortField == $column AND !$this->sortRev)	{		// reverse sorting
				$params = array($this->paramName['sortField'] => $column, $this->paramName['sortRev'] => '1');
			} else {
				$params = array($this->paramName['sortField'] => $column, $this->paramName['sortRev'] => '0');
			}

			$href = t3lib_div::linkThisScript($params);
			$sortArrow = ($this->sortField == $column? '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/red'.($this->sortRev?'up':'down').'.gif', 'width="7" height="4"').' style="padding:0px 3px 0px 3px;" alt="" />':'');
			$content .= '<a href="'.htmlspecialchars($href).'">'.htmlspecialchars($title).'</a>'.$sortArrow;


				// remove sorting
			if ($this->sortField == $column) {

				$params = array($this->paramName['sortField'] => '-', $this->paramName['sortRev'] => '0');
				$href = t3lib_div::linkThisScript($params);
				$content .= '<a href="'.htmlspecialchars($href).'">'.'<img'.t3lib_iconWorks :: skinImg($GLOBALS['BACK_PATH'], 'gfx/close.gif', 'width="11" height="10"').' title="'.$GLOBALS['LANG']->getLL('defaultSorting',1).'" alt="" />'.'</a>';
			}
		} else {
			$content .= htmlspecialchars($title);
		}

		return $content;

	}





	/***************************************
	 *
	 *	 Misc
	 *
	 ***************************************/


	/**
	 * Checks if a thumbnail can be generated for a file
	 *
	 * @param	array		$item	Fileinfo array
	 * @return	boolean
	 */
	function thumbnailPossible ($item) {
		return tx_dam_image::isPreviewPossible($item);
	}


	/**
	 * Returns single image tag to thumbnail using a thumbnail script (like thumbs.php)
	 *
	 * @param	string		$filepath must be the proper reference to the file thumbs.php should show
	 * @param	string		$addAtrr are additional attributes for the image tag
	 * @param	integer		$size is the size of the thumbnail send along to "thumbs.php"
	 * @return	string		Image tag
	 */
	function getThumbnail($filepath, $addAtrr='', $size='')	{
		return tx_dam_image::previewImgTag($filepath, $size, $addAtrr);
	}


	/**
	 * Returns JavaScript code (a function) to set/unset all checkboxes with the elements name "recs[]" (means: $this->paramName['recs'])
	 *
	 * @return string JavaScript code
	 */
	function getJsCode()	{
		return '
		function tx_dam_listbase_setCheckboxes(the_form)
		{
			var elts      = document.forms[the_form].elements["'.$this->paramName['recs'].'['.$this->table.'][]"];
			var elts_cnt  = (typeof(elts.length) != "undefined")
						? elts.length
						: 0;

			if (elts_cnt) {
				do_check = !elts[0].checked;
				for (var i = 0; i < elts_cnt; i++) {
					elts[i].checked = do_check;
				}
			} else {
				elts.checked = !elts.checked;
			}
			return true;
		}';
	}





// todo Clipboard




	/***************************************
	 *
	 *	 Clipboard
	 *
	 ***************************************/




	/**
	 * Wrapping input string in a link with clipboard command.
	 *
	 * @param	string		String to be linked - must be htmlspecialchar'ed / prepared before.
	 * @param	string		table - NOT USED
	 * @param	string		"cmd" value
	 * @param	string		Warning for JS confirm message
	 * @return	string		Linked string
	 */
	function clipboard_linkHeaderIcon($string, $table, $cmd, $warning='')	{
		$onClickEvent = 'document.dblistForm.cmd.value=\''.$cmd.'\';document.dblistForm.submit();';
		if ($warning)	$onClickEvent = 'if (confirm('.$GLOBALS['LANG']->JScharCode($warning).')){'.$onClickEvent.'}';
		return '<a href="#" onclick="'.htmlspecialchars($onClickEvent).'return false;">'.$string.'</a>';
	}

	/**
	 * Creates the clipboard control pad
	 *
	 * @param	array		Array with information about the file/directory for which to make the clipboard panel for the listing.
	 * @return	string		HTML-table
	 */
	function clipboard_getItemControl($columns)	{
return;
		$cells=array();
		$fullIdent = $columns['path'].$columns['file'];
		$md5=t3lib_div::shortmd5($fullIdent);

			// For normal clipboard, add copy/cut buttons:
		if ($this->clipObj->current=='normal')	{
			$isSel = $this->clipObj->isSelected('_FILE', $md5);
			$cells[]='<a href="'.htmlspecialchars($this->clipObj->selUrlFile($fullIdent,1,($isSel=='copy'))).'">'.
						'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clip_copy'.($isSel=='copy'?'_h':'').'.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.copy',1).'" alt="" />'.
						'</a>';
			$cells[]='<a href="'.htmlspecialchars($this->clipObj->selUrlFile($fullIdent,0,($isSel=='cut'))).'">'.
						'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clip_cut'.($isSel=='cut'?'_h':'').'.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.cut',1).'" alt="" />'.
						'</a>';
		} else {	// For numeric pads, add select checkboxes:
			$n='_FILE|'.$md5;
			$this->CBnames[] = $n;

			$checked = ($this->clipObj->isSelected('_FILE', $md5)?' checked="checked"':'');
			$cells[]='<input type="hidden" name="CBH['.$n.']" value="0" />'.
					'<input type="checkbox" name="CBC['.$n.']" value="'.htmlspecialchars($fullIdent).'" class="smallCheckboxes"'.$checked.' />';
		}

			// Display PASTE button, if directory:
		$elFromTable = $this->clipObj->elFromTable('_FILE');
		if (@is_dir($fullIdent) AND count($elFromTable))	{
			$cells[]='<a href="'.htmlspecialchars($this->clipObj->pasteUrl('_FILE', $fullIdent)).'" onclick="return '.htmlspecialchars($this->clipObj->confirmMsg('_FILE', $fullIdent, 'into', $elFromTable)).'">'.
						'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clip_pasteinto.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_pasteInto',1).'" alt="" />'.
						'</a>';
		}

			// Compile items into a DIV-element:
		return '							<!-- CLIPBOARD PANEL: -->
											<div class="typo3-clipCtrl">
												'.implode('
												', $cells).'
											</div>';
	}

	/**
	 * Creates the clipboard header control
	 *
	 * @return	string HTML content
	 */
	function clipboard_getHeaderControl () {
		return '';
				$cells = array();
				$table = '_FILE';
				$elFromTable = $this->clipObj->elFromTable($table);
				if (count($elFromTable))	{
					$cells[]='<a href="'.htmlspecialchars($this->clipObj->pasteUrl('_FILE', $this->path)).'" onclick="return '.htmlspecialchars($this->clipObj->confirmMsg('_FILE', $this->path, 'into', $elFromTable)).'">'.
						'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clip_pasteafter.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_paste',1).'" alt="" /></a>';
				}
				if ($this->clipObj->current!='normal' AND $this->pointer->countTotal)	{
					$cells[] = $this->clipboard_linkHeaderIcon('<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clip_copy.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_selectMarked',1).'" alt="" />', $table, 'setCB');
					$cells[] = $this->clipboard_linkHeaderIcon('<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/garbage.gif', 'width="11" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_deleteMarked',1).'" alt="" />', $table, 'delete', $GLOBALS['LANG']->getLL('clip_deleteMarkedWarning'));
					$onClick = 'checkOffCB(\''.implode(',', $this->CBnames).'\'); return false;';
					$cells[]='<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
							'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clip_select.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_markRecords',1).'" alt="" />'.
							'</a>';
				}

				return implode('', $cells);
	}


}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listbase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listbase.php']);
}
?>