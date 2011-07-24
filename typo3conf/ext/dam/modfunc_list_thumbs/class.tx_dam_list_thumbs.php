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
 * Module extension (addition to function menu) 'thumbs' for the 'Media>List' module.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   60: class tx_dam_list_thumbs extends t3lib_extobjbase
 *   72:     function modMenu()
 *   88:     function head()
 *  126:     function main()
 *  221:     function getItemControl($item, $table='tx_dam')
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db_lang_ovl.php');

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');


/**
 * Module extension  'Media>List>Thumbnail'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
class tx_dam_list_thumbs extends t3lib_extobjbase {

	var $diaSize = 115;
	var $diaMargin = 10;

	var $calcPerms = 0;

	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

		return array(
			'tx_dam_list_thumbs_bigThumb' => '',
			'tx_dam_list_thumbs_showTitle' => '',
			'tx_dam_list_thumbs_showInfo' => '',
			'tx_dam_list_thumbs_showIcons' => '',
			'tx_dam_list_thumbs_sortField' => '',
			'tx_dam_list_thumbs_sortRev' => '',
		);
	}

	/**
	 * Do some init things and set some styles in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global $LANG;

		//
		// Init gui items and ...
		//

		$this->pObj->guiItems->registerFunc('getResultInfoBar', 'header');
#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'header');

#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'footer');
		$this->pObj->guiItems->registerFunc('getCurrentSelectionBox', 'footer');
		$this->pObj->guiItems->registerFunc('getSearchBox', 'footer', array('simple', true));
		$this->pObj->guiItems->registerFunc('getOptions', 'footer');
		$this->pObj->guiItems->registerFunc('getStoreControl', 'footer');

			// add some options
		$this->pObj->addOption('funcCheck', 'tx_dam_list_thumbs_bigThumb', $LANG->getLL('tx_dam_list_thumbs.bigThumb'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_thumbs_showTitle', $LANG->getLL('tx_dam_list_thumbs.showTitle'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_thumbs_showInfo', $LANG->getLL('tx_dam_list_thumbs.showInfo'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_thumbs_showIcons', $LANG->getLL('tx_dam_list_thumbs.showIcons'));

		if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_bigThumb']) {
			$this->diaSize = 200;
		}

		if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_showIcons']) {
			$this->showControls = true;
			require_once (PATH_txdam.'lib/class.tx_dam_actioncall.php');
		}
		$this->calcPerms = $GLOBALS['SOBE']->calcPerms;

	}

	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()    {
		global $BE_USER,$LANG,$BACK_PATH,$TCA;

		$content = '';


		$table = 'tx_dam';
		t3lib_div::loadTCA($table);

		// Get rid of wrapper form tag.
		$this->pObj->doc->form = null;

		//
		// set language query
		//


		$this->langRows = $this->pObj->getLanguages($this->pObj->defaultPid);
		$this->langCurrent = intval($this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']);
		if (!isset($this->langRows[$this->langCurrent])) {
			$this->langCurrent = $this->pObj->MOD_SETTINGS['tx_dam_list_langSelector'] = key($this->langRows);
		}


		$langQuery = '';
		$languageField = $TCA[$table]['ctrl']['languageField'];
		if ($this->langCurrent AND $this->pObj->MOD_SETTINGS['tx_dam_list_langOverlay']==='exclusive') {
		// if ($this->langCurrent) { This works but create NULL columns for non-translated records so we need to use language overlay anyway

			$lgOvlFields = tx_dam_db::getLanguageOverlayFields ($table, 'tx_dam_lgovl');

			$languageField = $TCA[$table]['ctrl']['languageField'];
			$transOrigPointerField = $TCA[$table]['ctrl']['transOrigPointerField'];

			$this->pObj->selection->setSelectionLanguage($this->langCurrent);

			$this->pObj->selection->qg->query['SELECT']['tx_dam as tx_dam_lgovl'] = implode(', ', $lgOvlFields).', tx_dam.uid as _dlg_uid, tx_dam.title as _dlg_title';
			$this->pObj->selection->qg->query['LEFT_JOIN']['tx_dam as tx_dam_lgovl'] = 'tx_dam.uid=tx_dam_lgovl.'.$transOrigPointerField;

			if ($this->pObj->MOD_SETTINGS['tx_dam_list_langOverlay']==='exclusive') {
				$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.'.$languageField] = 'AND tx_dam_lgovl.'.$languageField.'='.$this->langCurrent;
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.deleted'] = 'AND tx_dam_lgovl.deleted=0';
			} else {
				$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.'.$languageField] = 'AND (tx_dam_lgovl.'.$languageField.' IN ('.$this->langCurrent.',0,-1) )';
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam_lgovl.deleted'] = 'AND (tx_dam_lgovl.sys_language_uid=1 OR tx_dam.sys_language_uid=0 )';
			}

		} else {
			$this->pObj->selection->qg->query['WHERE']['WHERE']['tx_dam.'.$languageField] = 'AND tx_dam.'.$languageField.' IN (0,-1)';
		}


		//
		// set query and sorting
		//

		$allFields = tx_dam_db::getFieldListForUser($table);


		if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_sortField'])	{
			if (in_array($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_sortField'], $allFields))	{
				$orderBy = 'tx_dam.'.$this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_sortField'];
			}
		} else {
			$orderBy = $TCA[$table]['ctrl']['sortby'] ? $TCA[$table]['ctrl']['sortby'] : $TCA[$table]['ctrl']['default_sortby'];
			$orderBy = $GLOBALS['TYPO3_DB']->stripOrderBy($orderBy);
			$this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_sortField'] = $orderBy;
		}
		if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_sortRev'])	$orderBy.=' DESC';
		$this->pObj->selection->qg->addOrderBy($orderBy);


		//
		// Use the current selection to create a query and count selected records
		//

		$this->pObj->selection->addSelectionToQuery();
		$this->pObj->selection->execSelectionQuery(TRUE);


		//
		// output header: info bar, result browser, ....
		//

		$content.= $this->pObj->guiItems->getOutput('header');
		$content.= $this->pObj->doc->spacer(10);

			// any records found?
		if($this->pObj->selection->pointer->countTotal) {

			// TODO move to scbase (see tx_dam_browser too)

			if (is_array($allFields) && count($allFields)) {
				$fieldsSelItems=array();
				foreach ($allFields as $field => $title) {
					$fL = is_array($TCA[$table]['columns'][$field]) ? preg_replace('#:$#', '', $GLOBALS['LANG']->sL($TCA[$table]['columns'][$field]['label'])) : '['.$field.']';
					$fieldsSelItems[$field] = t3lib_div::fixed_lgd_cs($fL, 15);
				}
				$sortingSelector = $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:labelSorting',1).' ';
				$sortingSelector .= t3lib_befunc::getFuncMenu('', 'SET[tx_dam_list_thumbs_sortField]', $this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_sortField'], $fieldsSelItems);

				if($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_sortRev'])	{
					$href = t3lib_div::linkThisScript(array('SET[tx_dam_list_thumbs_sortRev]' => '0'));
					$sortingSelector .=  '<button name="SET[tx_dam_list_thumbs_sortRev]" type="button" onclick="self.location.href=\''.htmlspecialchars($href).'\'">'.
							'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/pil2up.gif','width="12" height="7"').' alt="" />'.
							'</button>';
				} else {
					$href = t3lib_div::linkThisScript(array('SET[tx_dam_list_thumbs_sortRev]' => '1'));
					$sortingSelector .=  '<button name="SET[tx_dam_list_thumbs_sortRev]" type="button" onclick="self.location.href=\''.htmlspecialchars($href).'\'">'.
							'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/pil2down.gif','width="12" height="7"').' alt="" />'.
							'</button>';
				}
				$sortingSelector = '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">'.$sortingSelector.'</form>';
			}

			$this->pObj->markers['LANGUAGE_SELECT'] = $this->pObj->languageSwitch($this->langRows, intval($this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']));

			$content.= $this->pObj->contentLeftRight($sortingSelector, '');
			$content.= $this->pObj->doc->spacer(10);


			//
			// creates thumbnail list
			//


				// limit query for browsing
			$this->pObj->selection->addLimitToQuery();
			$res = $this->pObj->selection->execSelectionQuery();

			$showElements = array();
			if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_showTitle']) {
				$showElements[] = 'title';
			}
			if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_showInfo']) {
				$showElements[] = 'info';
			}
			if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_showIcons']) {
				$showElements[] = 'actions';
			}

				// extra CSS code for HTML header
			$this->pObj->doc->inDocStylesArray['tx_dam_SCbase_dia'] = tx_dam_guiFunc::getDiaStyles($this->diaSize, $this->diaMargin, 5);

			$code = '';


			//
			// init iterator for query
			//

			$conf = array(	'table' => 'tx_dam',
							'countTotal' => $this->pObj->selection->pointer->countTotal	);
			if ($this->langCurrent>0 AND $this->pObj->MOD_SETTINGS['tx_dam_list_langOverlay']!=='exclusive') {
				$dbIterator = new tx_dam_iterator_db_lang_ovl($res, $conf);
				$dbIterator->initLanguageOverlay($table, $this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']);
			} else {
				$dbIterator = new tx_dam_iterator_db($res, $conf);
			}



			if ($dbIterator->count())	{

				while ($dbIterator->valid() AND $dbIterator->currentPointer < $this->pObj->selection->pointer->itemsPerPage) {

					$row = $dbIterator->current();

					$onClick = $this->pObj->doc->wrapClickMenuOnIcon('', $table, $row['uid'], $listFr=1,$addParams='',$enDisItems='', $returnOnClick=TRUE);
					$actions = $this->getItemControl($row);
					$code.= tx_dam_guiFunc::getDia($row, $this->diaSize, $this->diaMargin, $showElements, $onClick, true, $actions);

					$dbIterator->next();
				}
			}


			$content.= $this->pObj->doc->spacer(5);
			$content.= $this->pObj->doc->section('','<div style="line-height:'.($this->diaSize +7+8).'px;">'.$code.'</div><br style="clear:left" />',0,1);



		}


		return $content;
	}


	/**
	 * Creates the control panel for a single record in the listing.
	 *
	 * @param	array		The record for which to make the control panel.
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function getItemControl($item, $table='tx_dam')	{
		global $TYPO3_CONF_VARS;

		static $actionCall;

		$content = '';

		if($this->showControls) {
			if(!is_object($actionCall)) {
				$table = 'tx_dam';

				t3lib_div::loadTCA($table);

				if ($table === 'pages') {
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
				$actionCall->setRequest('control', array('__type' => 'record', '__table' => $table));
				$actionCall->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
				$actionCall->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
				$actionCall->setEnv('defaultEditScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_edit/index.php');
				$actionCall->setEnv('calcPerms', $this->calcPerms);
				$actionCall->setEnv('permsEdit', $permsEdit);
				$actionCall->setEnv('permsDelete', $permsDelete);
				$actionCall->setEnv(array(
						'currentLanguage' => $this->langCurrent,
						'allowedLanguages' => $this->langRows,
					));
				$actionCall->initActions(true);
			}

			$item['__type'] = 'record';
			$item['__table'] = $table;

			$actionCall->setRequest('control', $item);
			$actions = $actionCall->renderActionsHorizontal(true);

				// Compile items into a DIV-element:
			$content = '
											<!-- CONTROL PANEL: tx_dam:'.$item['uid'].' -->
											<div class="typo3-DBctrl">'.implode('', $actions).'</div>';
		}

		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_thumbs/class.tx_dam_list_thumbs.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_thumbs/class.tx_dam_list_thumbs.php']);
}

?>
