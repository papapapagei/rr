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
 * **************************************************************/
/**
 * DAM db listing class
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
 *   80: class tx_dam_listrecords extends tx_dam_listbase
 *
 *              SECTION: Setup
 *  130:     function tx_dam_listrecords()
 *  140:     function __construct()
 *  166:     function init($table)
 *  181:     function setDataObject($dbObj)
 *
 *              SECTION: Process params and commands
 *  199:     function processParams()
 *  214:     function getMultiActionCommand()
 *
 *              SECTION: Rendering
 *  246:     function renderList()
 *
 *              SECTION: Column rendering
 *  393:     function getItemMultiAction ($item)
 *  410:     function getItemAction ($item)
 *  441:     function getItemIcon (&$item)
 *  470:     function isEditableColumn($field)
 *  495:     function getHeaderColumnControl($field)
 *  522:     function getHeaderControl()
 *  554:     function getItemControl($item)
 *  613:     function getMultiActions()
 *
 * TOTAL FUNCTIONS: 15
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam_listbase.php');
require_once (PATH_txdam.'lib/class.tx_dam_actioncall.php');

 /**
  * DAM db listing class
  *
  * @author	Rene Fritz <r.fritz@colorcube.de>
  * @package DAM-BeLib
  * @subpackage Lib
  */
class tx_dam_listrecords extends tx_dam_listbase {


	var $table = ''; // set to the tablename
	
	var $returnUrl = '';
	
	var $allItemCount = 0; // Counting the elements no matter what...
	
	var $calcPerms = 0;
	
	var $currentTable = array();



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
	function tx_dam_listrecords()	{
		$this->__construct();
	}

	/**
	 * Initialization of object
	 * PHP5 constructor
	 *
	 * @return	void
	 */
	function __construct() {
		global $BE_USER;

		parent::__construct();

		$this->elementAttr['table'] = ' border="0" cellpadding="0" cellspacing="0" style="width:100%" class="typo3-dblist"';

		$this->showMultiActions = false;
		$this->showAction = true;
		$this->showIcon = true;

		$this->calcPerms = $GLOBALS['SOBE']->calcPerms;
	}




	/***************************************
	 *
	 *	 Set data
	 *
	 ***************************************/



	/**
	 * Initialize the object
	 *
	 * @param	string		$table: ...
	 * @return	void
	 */
	function init($table) {

		$this->table = $table;
		$this->returnUrl = t3lib_div::_GP('returnUrl');

		$this->processParams();
	}


	/**
	 * Set the data iterator object
	 *
	 * @param	object		$dbObj Data object (iterator)
	 * @return	void
	 */
	function setDataObject($dbObj) {
		$this->dataObjects['db'] = $dbObj;

	}





	/***************************************
	 *
	 *	 Rendering
	 *
	 ***************************************/


	/**
	 * Creates the listing of records from a single table
	 *
	 * @return	string		HTML table with the listing for the record.
	 */
	function renderList() {
		global $TCA, $BACK_PATH, $LANG;



		if ($this->pointer->countTotal) {



			// Fixing a order table for sortby tables
			$this->currentTable = array();
			$doSort = ($TCA[$this->table]['ctrl']['sortby'] && !$this->sortField);

			$prevUid = 0;
			$prevPrevUid = 0;
			$accRows = array(); // Accumulate rows here



			foreach ($this->dataObjects as $list) {

				if ($list->count())	{

					while ($list->valid() AND $list->currentPointer < $this->pointer->itemsPerPage) {

						$row = $list->current();


						$accRows[] = $row;
						$this->currentTable['idList'][] = $row['uid'];
						if ($doSort) {
							if ($prevUid) {
								$this->currentTable['prev'][$row['uid']] = $prevPrevUid;
								$this->currentTable['next'][$prevUid] = '-'.$row['uid'];
								$this->currentTable['prevUid'][$row['uid']] = $prevUid;
							}
							$prevPrevUid = isset ($this->currentTable['prev'][$row['uid']]) ? - $prevUid : $row['pid'];
							$prevUid = $row['uid'];
						}
						$list->next();
					}
				}
			}
			unset($this->dataObjects);



				// render item rows
			$this->allItemCount = $this->pointer->firstItemNum;
			$itemCount = count($accRows);
			$itemCurrentCount = 0;

			$tdStyleAppend = '';

			foreach ($accRows as $item) {

				$itemCurrentCount ++;

				$item['__type'] = 'record';
				$item['__table'] = $this->table;

					// 	Columns rendering
				if ($this->showMultiActions)	$itemMultiAction = $this->getItemMultiAction ($item);
				if ($this->showAction)	$itemAction = $this->getItemAction ($item);
				if ($this->showIcon)	$itemIcon = $this->getItemIcon ($item);

				#$itemColumns = $this->getItemColumns ($item);

				$itemColumns = array();
				foreach($this->columnList as $fCol => $dummy) {
					if ($fCol == $this->titleColumnKey) {

						$recTitle = t3lib_BEfunc::getRecordTitle($this->table, $item, 1);
						$recTitle = $this->linkWrapFile($recTitle, $item);

						$thumbImg = '';
						if ($this->showThumbs) {
							$thumbImg = '<div style="margin:2px 0 2px 0;">'.$this->getThumbNail($item).'</div>';
						}

						$itemColumns[$fCol] = $recTitle.$thumbImg;
					}
					elseif ($fCol  === 'pid') {
						$itemColumns[$fCol] = $item[$fCol];
					}
					elseif ($fCol  === '_CONTROL_') {
						$itemColumns[$fCol] = $this->getItemControl($item);
					} else {
						$itemColumns[$fCol] = t3lib_BEfunc::getProcessedValueExtra($this->table, $fCol, $item[$fCol], 100, $item['uid']);
						
#				$theData[$fCol] = $this->linkUrlMail(htmlspecialchars(t3lib_BEfunc::getProcessedValueExtra($table,$fCol,$row[$fCol],100,$row['uid'])),$row[$fCol]);
					}
				}




				$trStyle = ' background-color:'.$this->colorTREven.';';
				if ($this->showAlternateBgColors) {
					if ($this->allItemCount % 2) {
						$trStyle = ' background-color:'.$this->colorTREven.';';
					}
					else {
						$trStyle = ' background-color:'.$this->colorTROdd.';';
					}
				}

					// this is the last line which should have a line afterwards
				if($itemCurrentCount==$itemCount) {
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

				$this->allItemCount++;
			}


		}
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

		$multiActionID = $item['uid'];
		$multiActionSelected = in_array($multiActionID, $this->recs);

		$multiAction = '<input type="checkbox" name="'.$this->paramName['recs'].'['.$this->table.'][]" value="'.$multiActionID.'"'.($multiActionSelected?' checked="checked"':'').' />';

		return $multiAction;
	}


	/**
	 * Renders the action
	 *
	 * @param	array		$item item array
	 * @return	string
	 * @todo abstraction
	 */
	function getItemAction ($item) {
		global $LANG;

		$itemAction = '';

		if ($this->table  === 'tx_dam') {

			if($GLOBALS['SOBE']->selection->sl->sel['NOT']['txdamRecords'][$item['uid']]) {
				$params = 'SLCMD[NOT][txdamRecords]['.$item['uid'].']=0';
				$actionIcon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/button_reselect.gif', 'width="11" height="10"').' title="'.$LANG->getLL('reselect').'" alt="" />';
				$itemAction = '<a href="index.php?'.$params.'">'.$actionIcon.'</a>';
			} else {
				$params='SLCMD[NOT][txdamRecords]['.$item['uid'].']=1';
				$actionIcon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/button_deselect.gif', 'width="11" height="10"').' title="'.$LANG->getLL('deselect').'" alt="" />';
				$itemAction = '<a href="index.php?'.$params.'">'.$actionIcon.'</a>';
			}
		}


		return $itemAction;
	}


	/**
	 * Renders the item icon
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemIcon (&$item) {
		static $iconNotExists;

		if(!$iconNotExists) {
			$titleNotExists = 'title="'.$GLOBALS['LANG']->getLL('fileNotExists', true).'"';
			$iconNotExists = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/error_h.gif', 'width="10" height="10"').' '.$titleNotExists.' alt="" />';
		}

		$titletext = t3lib_BEfunc::getRecordIconAltText($item, $this->table);
		$itemIcon = tx_dam::icon_getFileTypeImgTag($item, 'title="'.$titletext.'"');
		if ($this->enableContextMenus) $itemIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($itemIcon, $this->table, $item['uid']);

		if (!is_file(tx_dam::file_absolutePath($item))) {
			$item['file_status'] = TXDAM_status_file_missing;
			$itemIcon.= $iconNotExists;
		}

		return $itemIcon;
	}




	/***************************************
	 *
	 *	 Controls
	 *
	 ***************************************/


	function isEditableColumn($field) {
		global $TCA;

		$editable = false;
		$permsEdit = $this->calcPerms & ($this->table  === 'pages' ? 2 : 16);
		if (
			$permsEdit AND
			!$TCA[$this->table]['ctrl']['readOnly'] AND
			$TCA[$this->table]['columns'][$field] AND
			!($TCA[$this->table]['columns'][$field]['config']['type']=='none') AND
			!($TCA[$this->table]['columns'][$field]['config']['form_type']=='none') AND
			!($TCA[$this->table]['columns'][$field]['config']['readOnly'])
		) {
				$editable = true;
		}
		return $editable;
	}


	/**
	 * Creates the column control panel for the header.
	 *
	 * @param 	string 	$field Column key
	 * @return	string		control panel (unless disabled)
	 */
	function getHeaderColumnControl($field) {

		$content = '';

		if ($this->isEditableColumn($field) AND
			is_array($this->currentTable['idList'])  AND
			$this->showControls
			) {

			$editIdList = implode(',', $this->currentTable['idList']);
			$params = '&edit['.$this->table.']['.$editIdList.']=edit&columnsOnly='.$field.'&disHelp=1';
			$iTitle = sprintf($GLOBALS['LANG']->getLL('editThisColumn'), preg_replace('#:$#', '', trim($GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel($this->table, $field)))));
			$content = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif', 'width="11" height="12"').' title="'.htmlspecialchars($iTitle).'" alt="" />';
			$onClick = t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'],-1);
			$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$content.'</a>';
		}

		return $content;
	}


	/**
	 * Creates the control panel for the header.
	 *
	 * @return	string		control panel (unless disabled)
	 */
	function getHeaderControl() {
		global $TCA;

		$permsEdit = $this->calcPerms & ($this->table  === 'pages' ? 2 : 16);

		if (
				$permsEdit AND
				!$TCA[$this->table]['ctrl']['readOnly'] AND
				is_array($this->currentTable['idList']) AND
				$this->showControls
			) {

			$editIdList = implode(',', $this->currentTable['idList']);
			$columnsOnly = implode(',', array_keys($this->columnList));
			$params = '&edit['.$this->table.']['.$editIdList.']=edit&columnsOnly='.$columnsOnly.'&disHelp=1';
			$content = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif', 'width="11" height="12"').' title="'.$GLOBALS['LANG']->getLL('editShownColumns').'" />';
			$onClick = t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'], -1);
			$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$content.'</a>';
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
		
		static $actionCall;

		$content = '';

		if($this->showControls) {
			if(!is_object($actionCall)) {

				t3lib_div::loadTCA($this->table);

				if ($this->table  === 'pages') {
						// If the listed table is 'pages' we have to request the permission settings for each page:
					$localCalcPerms = $GLOBALS['BE_USER']->calcPerms($item);
					$permsEdit = ($localCalcPerms & 2);
					$permsDelete = ($localCalcPerms & 4);
				} else {
						// This expresses the edit permissions for this particular element:
					$permsEdit = ($this->calcPerms & 16);
					$permsDelete = ($this->calcPerms & 16);
				}

				$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');
				$actionCall->setRequest('control', array('__type' => 'record', '__table' => $this->table));
				$actionCall->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
				$actionCall->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
				$actionCall->setEnv('defaultEditScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_edit/index.php');
				$actionCall->setEnv('calcPerms', $this->calcPerms);
				$actionCall->setEnv('permsEdit', $permsEdit);
				$actionCall->setEnv('permsDelete', $permsDelete);
				$actionCall->setEnv($this->actionsEnv);
				$actionCall->initActions(true);
			}

			$item['__type'] = 'record';
			$item['__table'] = $this->table;

			$actionCall->setRequest('control', $item);
			$actions = $actionCall->renderActionsHorizontal(true);
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
		

		$permsEdit = ($this->calcPerms & 16);
		$permsDelete = ($this->calcPerms & 16);

		$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');
		$actionCall->setRequest('multi', array('__type' => 'record', '__table' => $this->table));
		$actionCall->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
		$actionCall->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
		$actionCall->setEnv('calcPerms', $this->calcPerms);
		$actionCall->setEnv('permsEdit', $permsEdit);
		$actionCall->setEnv('permsDelete', $permsDelete);
		$actionCall->setEnv($this->actionsEnv);
		$actionCall->initActions(true);

		$actions = $actionCall->renderMultiActions();
		return $actions;
	}





// todo Clipboard





}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listrecords.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listrecords.php']);
}
?>
