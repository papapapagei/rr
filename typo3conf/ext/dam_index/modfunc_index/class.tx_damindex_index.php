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
 * Module extension (addition to function menu) 'Index' for the 'Media>Index' module..
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   91: class tx_damindex_index extends t3lib_extobjbase
 *  107:     function modMenu()
 *  124:     function head()
 *  168:     function main()
 *  186:     function getCurrentFunc()
 *  207:     function moduleContent($header='', $description='', $lastStep=4)
 *  311:     function showProgress()
 *  368:     function progress_bar_update(intCurrentPercent)
 *  379:     function addTableRow(cells)
 *  401:     function setMessage(msg)
 *  406:     function finished()
 *
 *              SECTION: Rendering the forms etc
 *  550:     function getPresetForm ($rec, $fixedFields, $langKeyDesc)
 *  622:     function showPresetData ($rec,$fixedFields)
 *  678:     function doIndexing($indexSessionID)
 *  735:     function doIndexingCallback($type, $meta, $absFile, $fileArrKey, &$pObj)
 *  806:     function indexing_progressBar($intCurrentCount = 100, $intTotalCount = 100)
 *  827:     function indexing_addTableRow($contentArr)
 *  841:     function indexing_setMessage($msg)
 *  850:     function indexing_finished()
 *  859:     function indexing_flushNow()
 *
 *              SECTION: indexSession
 *  881:     function indexSessionClear()
 *  891:     function indexSessionNew($filesTodo)
 *  908:     function indexSessionFetch()
 *  917:     function indexSessionWrite($indexSession)
 *
 *              SECTION: GUI
 *  939:     function getStepsBar($currentStep, $lastStep, $onClickBack='' ,$onClickFwd='', $buttonNameBack='', $buttonNameFwd='')
 *
 *              SECTION: this and that
 *  984:     function processIndexSetup()
 * 1041:     function saveSettings()
 *
 * TOTAL FUNCTIONS: 26
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');

/**
 * Module 'Media>Index>Index'
 * Module 'Media>File>Index'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_damindex_index extends t3lib_extobjbase {



	/**
	 * indexing object
	 */
	var $index;



	/**
	 * Do some init things and aet some styles in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global  $LANG, $TYPO3_CONF_VARS, $FILEMOUNTS;


		#TODO
		$this->pObj->doc->bgColor3dim = t3lib_div::modifyHTMLcolor($this->pObj->doc->bgColor3,-5,-5,-5);
		$this->pObj->doc->bgColor5lg = t3lib_div::modifyHTMLcolor($this->pObj->doc->bgColor5,25,25,25);


		//
		// doc and header init
		//

		$this->pObj->doc->form = $this->pObj->getFormTag();
		#TODO ??? onSubmit="return TBE_EDITOR_checkSubmit(1);"

		$this->pObj->doc->form.= '<input type="hidden" name="SET[tx_dam_folder]" value="'.$this->pObj->path.'" />';


		//
		// Init gui items and ...
		//

		$this->pObj->guiItems->registerFunc('getOptions', 'footer');


		//
		// Init indexing object
		//

		$this->index = t3lib_div::makeInstance('tx_dam_indexing');
		$this->index->init();
		$this->index->setRunType('man');


			// initialize indexing setup
		$this->processIndexSetup();
	}


	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()	{
		global  $BE_USER, $LANG, $BACK_PATH;

		//$content = '';

		//$this->pObj->markers['FUNC_MENU'] = $this->pObj->getTabMenu($this->pObj->addParams,'SET[tx_damindex_index_func]',$GLOBALS['SOBE']->MOD_SETTINGS['tx_damindex_index_func'],$GLOBALS['SOBE']->MOD_MENU['tx_damindex_index_func']);

		#$content.= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, TRUE, $this->cmdIcons);
		#$content.= $this->pObj->doc->section('',$this->pObj->doc->funcMenu('',$this->cmdIcons['funcMenu']));


			// Render content:
		$content.= $this->moduleContent();

		return $content;
	}


	function getCurrentFunc() {
		$func = 'index';
		if ($step = t3lib_div::_GP('indexStep')) {
			$step = max(1,key($step));
			$func = 'index'.$step;
		}
		if (t3lib_div::_GP('indexStart')) {
			$func = 'indexStart';
		}
		if (t3lib_div::_GP('doIndexing')) {
			$func = 'doIndexing';
		}
		return $func;
	}


	/**
	 * Generates the module content
	 *
	 * @return	string		HTML content
	 */
	function moduleContent($header='', $description='', $lastStep=4)    {
		global  $BE_USER, $LANG, $BACK_PATH, $FILEMOUNTS, $TYPO3_CONF_VARS;

		$content = '';

		switch($this->getCurrentFunc())    {

			//
			// select start folder
			//

			case 'index':
			case 'index1':

				$step = 1;

				$content.= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, TRUE, $this->cmdIcons);
				$content.= $this->pObj->doc->spacer(10);

				$content.= $description;

				$store = t3lib_div::makeInstance('t3lib_modSettings');
				$store->init('tx_damindex');
				$store->type = 'perm';
				$store->setStoreList('tx_damindex_indexSetup');
				$store->processStoreControl();

				if ($code = $store->getStoreControl('load,remove')) {
					$this->content.= $this->pObj->doc->section($LANG->getLL('tx_damindex_index.choose_preset'),$code,0,1);
					$this->content.=$this->pObj->doc->spacer(10);
				}

				$header = $header ? $header : $LANG->getLL('tx_damindex_index.index_begin');
				$content.= $this->pObj->doc->section($header,$this->getStepsBar($step,$lastStep),0,1);
				$content.= $this->pObj->doc->section('',$LANG->getLL('tx_damindex_index.choose_start_folder'),0,1);
				$content.= $this->pObj->doc->spacer(10);


				$content.= $this->pObj->getBrowseableFolderList($this->pObj->path);
			break;


			//
			// select indexing options
			//

			case 'index2':

				$step = 2;

				$content.= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, FALSE, $this->cmdIcons);
				$content.= $this->pObj->doc->spacer(10);

				$content.= $description;

				$header = $header ? $header : $LANG->getLL('options');
				$content.= $this->pObj->doc->section($header,$this->getStepsBar($step,$lastStep),0,1);
				$content.= $this->pObj->doc->spacer(5);

				$code = '<table border="0" cellspacing="0" cellpadding="4" width="100%">'.$this->index->getIndexingOptionsForm().'</table>';
				$content.= $this->pObj->doc->section($LANG->getLL('options').':',$code,1,0);

			break;


			//
			// field predefinition
			//

			case 'index3':

				$step = 3;

				$content.= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, FALSE, $this->cmdIcons);
				$content.= $this->pObj->doc->spacer(10);

				$content.= $description;

				$header = $header ? $header : $LANG->getLL('tx_damindex_index.index_fields_preset');
				$stepsBar = $this->getStepsBar($step,$lastStep, '' ,'return TBE_EDITOR_checkSubmit(1);');
				$content.= $this->pObj->doc->section($header,$stepsBar,0,1);
				$content.= $LANG->getLL('tx_damindex_index.preset_desc');
				$content.= $this->pObj->doc->spacer(10);

				$rec = array_merge($this->index->dataPreset,$this->index->dataPostset);
				$fixedFields = array_keys($this->index->dataPostset);

				$code = '<table border="0" cellpadding="4" width="100%"><tr>
					<td bgcolor="'.$this->pObj->doc->bgColor3dim.'">'.$this->getPresetForm($rec,$fixedFields,'tx_damindex_index.fixed_desc').'</td>
					</tr></table>';
				$content.= $this->pObj->doc->section('',$code,0,1);
			break;


			//
			// setup summary
			//

			case 'index4':

				$step = 4;

					// JavaScript
				$this->pObj->doc->JScode = $this->pObj->doc->wrapScriptTags('
					function showProgress() {

						if(document.all) {
							document.all.stepsFormButtons.style.visibility = "hidden";
							document.all.summaryInfoDiv.style.visibility = "hidden";
						} else {
							document.getElementById("stepsFormButtons").style.visibility = "hidden";
							document.getElementById("summaryInfoDiv").style.visibility = "hidden";
						}
					}
				');


				$content.= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, FALSE, $this->cmdIcons);
				$content.= $this->pObj->doc->spacer(10);

				$content.= $description;

				$header = $header ? $header : $LANG->getLL('tx_damindex_index.setup_summary');

				$stepsBar = $this->getStepsBar($step,$lastStep, '' ,'showProgress();return true;', '', $LANG->getLL('tx_damindex_index.start'));
				$content.= $this->pObj->doc->section($header,$stepsBar,0,1);

				$content.= '<div id="summaryInfoDiv">';
				$content.= '<strong>'.$LANG->getLL('tx_damindex_index.set_options').'</strong><table border="0" cellspacing="0" cellpadding="4" width="100%">'.$this->index->getIndexingOptionsInfo().'</table>';

				$content.= $this->pObj->doc->spacer(10);

				$rec = array_merge($this->index->dataPreset,$this->index->dataPostset);

				$fixedFields=array_keys($this->index->dataPostset);
				$content.= '<strong>'.$LANG->getLL('tx_damindex_index.meta_data_preset').'</strong><br /><table border="0" cellpadding="4" width="100%"><tr><td bgcolor="'.$this->pObj->doc->bgColor3dim.'">'.
								$this->showPresetData($rec,$fixedFields).
								'</td></tr></table>';

				$content.= $this->pObj->doc->spacer(10);

//				$store = t3lib_div::makeInstance('t3lib_modSettings');
//				$store->init('tx_damindex');
//				$store->setStoreList('tx_damindex_indexSetup');
//				$store->processStoreControl();
// TODO getStoreControl
//				if ($code = $store->getStoreControl('save')) {
//					$this->content.= $this->pObj->doc->section($LANG->getLL('tx_damindex_index.store_preset'),$code,0,1);
//					$this->content.=$this->pObj->doc->spacer(10);
//				}
				$content.= '</div>';
				$content.= $this->pObj->doc->spacer(10);

			break;


			case 'indexStart':
				$this->cmdIcons['funcMenu'] = '<div id="btn_back" style="visibility:hidden">'.$this->pObj->btn_back(array('indexStep[1]'=>1)).'</div>';

					// JavaScript
				$this->pObj->doc->JScode = $this->pObj->doc->wrapScriptTags('
					function progress_bar_update(intCurrentPercent) {
						document.getElementById("progress_bar_left").style.width = intCurrentPercent+"%";
						document.getElementById("progress_bar_left").innerHTML = intCurrentPercent+"&nbsp;%";

						document.getElementById("progress_bar_left").style.background = "#448e44";
						if(intCurrentPercent >= 100) {
							document.getElementById("progress_bar_right").style.background = "#448e44";
						}
					}


					function addTableRow(cells) {

						document.getElementById("table1").style.display = "block";

						var tbody = document.getElementById("table1").getElementsByTagName("tbody")[0];
						var row = document.createElement("TR");

						row.style.backgroundColor = "'.$this->pObj->doc->bgColor4.'";

						for (var cellId in cells) {
							var tdCell = document.createElement("TD");
							tdCell.innerHTML = cells[cellId];
							row.appendChild(tdCell);
						}
						var header = document.getElementById("table1header");
						var headerParent = header.parentNode;
						headerParent.insertBefore(row,header.nextSibling);

						// tbody.appendChild(row);
						// tbody.insertBefore(row,document.getElementById("table1header"));
					}

					function setMessage(msg) {
						var messageCnt = document.getElementById("message");
						messageCnt.innerHTML = msg;
					}

					function finished() {
						progress_bar_update(100);
						document.getElementById("progress_bar_left").innerHTML = "'.$LANG->getLL('tx_damindex_index.finished',1).'";
						document.getElementById("btn_back").style.visibility = "visible";
					}
				');


				$content.= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, FALSE, $this->cmdIcons);
				$content.= $this->pObj->doc->spacer(10);

				$content.= $description;

				$content.= $this->pObj->doc->section($LANG->getLL('tx_damindex_index.indexed_files'),'',0,1);
				$content.= $this->pObj->doc->spacer(10);

				if(tx_dam::config_getValue('setup.devel')) {
					$iframeSize = 'width="100%" height="300" border="1" scrolling="yes" frameborder="1"';
				} else {
					$iframeSize = 'width="0" height="0" border="0" scrolling="no" frameborder="0"';
				}


				$this->pObj->addParams['doIndexing'] = 1;
				$code = '';
				$code.= '
					<table width="300px" border="0" cellpadding="0" cellspacing="0" id="progress_bar" summary="progress_bar" align="center" style="border:1px solid #888">
					<tbody>
					<tr>
					<td id="progress_bar_left" width="0%" align="center" style="background:#eee; color:#fff">&nbsp;</td>
					<td id="progress_bar_right" style="background:#eee;">&nbsp;</td>
					</tr>
					</tbody>
					</table>

					<iframe src="'.htmlspecialchars(t3lib_div::linkThisScript($this->pObj->addParams)).'" name="indexiframe" '.$iframeSize.'>
					Error!
					</iframe>
					<br />
				';

				if ($this->index->ruleConf['tx_damindex_rule_dryRun']['enabled']) {
					$code.= '<div><strong class="diff-r">'.$LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:dryRun.title').'!</strong></div>';
				}

				$code.= '
					<div id="message" style="margin-top:1em;"></div>
					<div id="table1" style="display:none;margin-top:1em;">
					<table cellpadding="1" cellspacing="1" border="0" width="100%">
					<tr id="table1header" class="bgColor5">
						<th>&nbsp;</th>
						<th>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name',1).'</th>
						<th>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_type',1).'</th>
						<th>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.abstract',1).'</th>
						<th>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_path',1).'</th>
					</tr>
					</table>
					</div>
				';
				$content.= $this->pObj->doc->section('',$code,0,1);
			break;


			case 'doIndexing':

				$this->pObj->addParams['doIndexing'] = 1;

					// reload at this time
				$max_execution_time = ini_get('max_execution_time');
								$max_execution_time = intval($max_execution_time) ? intval(($max_execution_time/3)*2) : 60;
				$this->indexEndtime = time()+$max_execution_time;

				echo '<head>
					<title>indexing</title>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					</head>
					<body>';

					$this->doIndexing(t3lib_div::_GP('indexSessionID'));

				echo '</body>
					</html>';
				exit;
			break;
		}

#		 $content.= '<br /><span style="margin-left:20px;"><input type="submit" name="indexStep['.$step.']" value="update" /></span><br />';

		return $content;
	}





	/*******************************************************
	 *
	 * Rendering the forms etc
	 *
	 *******************************************************/


	/**
	 * Returns the form to preset values
	 *
	 * @param	array		$rec preset record data
	 * @param	array		$fixedFields fields which are preset as fixed fields
	 * @param	string		$langKeyDesc Language key for description. Will be resolved with $LANG->getLL().
	 * @return	string
	 * @params  string
	 */
	function getPresetForm ($rec, $fixedFields, $langKeyDesc) {
		global $LANG, $BACK_PATH, $TCA, $TYPO3_CONF_VARS;



		$content = '';
		$editForm = '';

		if(!is_array($rec)) $rec = array();
		if(!is_array($fixedFields)) $fixedFields = array();

		require_once (PATH_txdam.'lib/class.tx_dam_simpleforms.php');
		$form = t3lib_div::makeInstance('tx_dam_simpleForms');
		$form->initDefaultBEmode();
		$form->setVirtualTable('tx_dam_simpleforms', 'tx_dam');
		$form->removeRequired($TCA['tx_dam_simpleforms']);
		$form->removeMM($TCA['tx_dam_simpleforms']);
		$form->tx_dam_fixedFields = $fixedFields;

// this is not needed, is it?
//		require_once (PATH_t3lib.'class.t3lib_transferdata.php');
//		$processData = t3lib_div::makeInstance('t3lib_transferData');
//		$rec = $processData->renderRecordRaw('tx_dam', $rec['uid'], $rec['pid'], $rec);

		$rec['uid'] = 1;
		$rec['pid'] = 0;
		$rec['media_type'] = TXDAM_mtype_undefined;
		$rec = tx_dam_db::evalData('tx_dam', $rec);


		$columnsOnly = $TCA['tx_dam_simpleforms']['txdamInterface']['index_fieldList'];

		if ($columnsOnly)	{
			$editForm.= $form->getListedFields('tx_dam_simpleforms',$rec,$columnsOnly);
		} else {
			$editForm.= $form->getMainFields('tx_dam_simpleforms',$rec);
		}


			// add message for checkboxes
		$editForm='<tr bgcolor="'.$this->pObj->doc->bgColor4.'">
				<td nowrap="nowrap" valign="middle">'.
				'<span style="padding: 0 10px 0 10px">'.
				'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pil2down.gif', 'width="12" height="7"').' alt="" />'.
				'</span></td>
				<td valign="top">'.$LANG->getLL($langKeyDesc).'</td>
			</tr>
			<tr>
				<td colspan="2" style="height:8px"><span></span></td>
			</tr>'.
			$editForm;


		$editForm=$form->wrapTotal($editForm,$rec,'tx_dam_simpleforms');

		$this->pObj->doc->JScode.= '
		'.$form->printNeededJSFunctions_top();
		$content.= $editForm.$form->printNeededJSFunctions();

		$form->removeVirtualTable('tx_dam_simpleforms');

		return $content;
	}


	/**
	 * Returns the non-editable form of preset values
	 *
	 * @param	array		preset record data
	 * @param	array		fields which are preset as fixed fields
	 * @return	string
	 */
	function showPresetData ($rec,$fixedFields) {
		global $LANG, $BACK_PATH, $TCA, $TYPO3_CONF_VARS;


		$content = '';

		if(!is_array($rec)) $rec = array();


		require_once (PATH_txdam.'lib/class.tx_dam_simpleforms.php');
		$form = t3lib_div::makeInstance('tx_dam_simpleForms');
		$form->initDefaultBEmode();
		$form->setVirtualTable('tx_dam_simpleforms', 'tx_dam');
		$form->prependFormFieldNames='ignore';
		$form->removeRequired($TCA['tx_dam_simpleforms']);
		$form->removeMM($TCA['tx_dam_simpleforms']);
		$form->setNonEditable($TCA['tx_dam_simpleforms']);
		$form->tx_dam_fixedFields = $fixedFields;


		$rec['uid'] = 1;
		$rec['pid'] = 0;
		$rec['media_type'] = TXDAM_mtype_undefined;
		$rec = tx_dam_db::evalData('tx_dam', $rec);

			// workaround for the dumb formatValue function in tceforms which do not handle empty strings
		$rec['date_cr'] = intval($rec['date_cr']);
		$rec['date_mod'] = intval($rec['date_mod']);

		$columnsOnly=$TCA['tx_dam']['txdamInterface']['index_fieldList'];

		if ($columnsOnly)	{
			$content.= $form->getListedFields('tx_dam_simpleforms', $rec, $columnsOnly);
		} else {
			$content.= $form->getMainFields('tx_dam_simpleforms', $rec);
		}

		$content = $form->wrapTotal($content, $rec, 'tx_dam_simpleforms');

		$form->removeVirtualTable('tx_dam_simpleforms');

		return $content;
	}








	/**
	 * Do the file indexing
	 * Read files from a directory index them and output a result table
	 *
	 * @return	string		HTML content
	 */
	function doIndexing($indexSessionID) {
		global $LANG, $TYPO3_CONF_VARS;


			// makes sense? Was a hint on php.net
		ob_end_flush();

			// get session data - which might have left files stored
		$indexSession = $this->indexSessionFetch();

		if($indexSessionID=='' OR !isset($indexSession['ID']) OR !($indexSession['ID']==$indexSessionID) OR $indexSession['currentCount']==0 ) {

			$code = $LANG->getLL('tx_damindex_index.search_files');
			$this->indexing_setMessage($code);
			$this->indexing_flushNow();

				// fetching file names is still without callback - billions of files will cause a timeout - ever?
			$filesTodo = $this->index->collectFiles($this->pObj->path, $this->index->ruleConf['tx_damindex_rule_recursive']['enabled']);
			$indexSession = $this->indexSessionNew($filesTodo);

		} else {
			$this->index->stat = $indexSession['indexStat'];
			$this->index->infoList = is_array($indexSession['infoList']) ? $indexSession['infoList'] : array();
		}

		if(tx_dam::config_getValue('setup.devel')) {
			t3lib_div::print_array($indexSession);
		}

		$this->index->setDryRun($this->index->ruleConf['tx_damindex_rule_dryRun']['enabled']);

		if ($indexSession['totalFilesCount']) {

			$this->index->collectMeta = TRUE;
			$this->index->setIndexRun($indexSession['indexRun']);
			$this->index->indexFiles($indexSession['filesTodo'], $this->pObj->defaultPid, array(&$this, 'doIndexingCallback'));

			if (!$this->index->stat['totalCount']) {
				$code = $LANG->getLL('tx_damindex_index.no_new_files');
				$this->indexing_setMessage($code);
			}
			$this->indexing_finished();

		} else {
			$code = $LANG->getLL('tx_damindex_index.no_files');
			$this->indexing_setMessage($code);

		}

			// finished - clear session
		$this->indexSessionClear();
	}


	/**
	 *
	 */
	function doIndexingCallback($type, $meta, $absFile, $fileArrKey, $pObj) {
		global $LANG, $TYPO3_CONF_VARS;

			// get session data
		$indexSession = $this->indexSessionFetch();

			// increase progress bar
		$indexSession['currentCount']++;

		if(is_array($meta) AND is_array($meta['fields'])) {

			if(tx_dam::config_getValue('setup.devel')) {
				t3lib_div::print_array(array(
						'file_name' => $meta['fields']['file_name'],
						'indexExist' => $meta['indexExist'],
						'reindexed' => $meta['reindexed'],
						'isIndexed' => $meta['isIndexed'],
					));
			}

			if ($meta['isIndexed']) {
				$failure = '';
				$openRecPopup = '';
				if ($meta['failure']) {
					$failure .= '<br /><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_fatalerror.gif','width="18" height="16"').' class="absmiddle" alt="" />';
					$failure .= ' '.htmlspecialchars($meta['failure']);
				} else {
					$openRecPopup = $GLOBALS['SOBE']->btn_editRec_inNewWindow('tx_dam', $meta['fields']['uid']);
				}

				$ctable = array();
				$ctable[] = $openRecPopup;
				$ctable[] = '<span style="white-space:nowrap;">'.tx_dam::icon_getFileTypeImgTag($meta['fields'],'align="top"').'&nbsp;'.htmlspecialchars(t3lib_div::fixed_lgd_cs($meta['fields']['file_name'],23)).'</span>'.$failure;
				$ctable[] = strtoupper($meta['fields']['file_type']);
				$ctable[] = '<span style="white-space:nowrap;">'.htmlspecialchars(str_replace("\n", ' ', t3lib_div::fixed_lgd_cs($meta['fields']['abstract'],14))).'</span>';
				$ctable[] = htmlspecialchars(t3lib_div::fixed_lgd_cs($meta['fields']['file_path'],-15));

				$this->indexing_addTableRow($ctable);
				$msg = $LANG->getLL('tx_damindex_index.indexed_message',1);
				$code = sprintf($msg, $this->index->stat['totalCount'], max(1,ceil($this->index->stat['totalTime']/1000)));
				$this->indexing_setMessage($code);
			}
		}

		$this->indexing_progressBar($indexSession['currentCount'], $indexSession['totalFilesCount']);
		$this->indexing_flushNow();

			// one step further - save session data
		unset($indexSession['filesTodo'][$fileArrKey]);
		$indexSession['indexStat'] = $this->index->stat;
		$indexSession['infoList'] =	$this->index->infoList;

		$this->indexSessionWrite($indexSession);

		if (($this->indexEndtime < time()) AND ($indexSession['currentCount'] < $indexSession['totalFilesCount'])) {
			$params = $this->pObj->addParams;
			$params['indexSessionID'] = $indexSession['ID'];
			echo '
				<script type="text/javascript">  window.location.href = unescape("'.t3lib_div::rawUrlEncodeJS(tx_dam_SCbase::linkThisScriptStraight($params)).'"); </script>';
			exit;
		}
	}






	/**
	 *
	 */
	function indexing_progressBar($intCurrentCount = 100, $intTotalCount = 100) {

		static $intNumberRuns = 0;
		static $intDisplayedCurrentPercent = 0;
		$strProgressBar = '';
		$dblPercentIncrease = (100 / $intTotalCount);
		$intCurrentPercent = intval($intCurrentCount * $dblPercentIncrease);
		$intNumberRuns ++;

		if (($intNumberRuns>1) AND ($intDisplayedCurrentPercent <> $intCurrentPercent))  {
			$intDisplayedCurrentPercent = $intCurrentPercent;
			$strProgressBar = '
				<script type="text/javascript" language="javascript"> parent.progress_bar_update('.$intCurrentPercent.'); </script>';
		}
		echo $strProgressBar;
	}


	/**
	 *
	 */
	function indexing_addTableRow($contentArr) {
		foreach ($contentArr as $key => $val) {
			$contentArr[$key] = t3lib_div::slashJS($val, false, '"');
		}
		$jsArr = '"'.implode('","', $contentArr).'"';
		$jsArr = 'new Array('.$jsArr.')';
		echo '
			<script type="text/javascript" language="javascript"> parent.addTableRow('.$jsArr.');</script>';
	}


	/**
	 *
	 */
	function indexing_setMessage($msg) {
		echo '
			<script type="text/javascript" language="javascript"> parent.setMessage("'.t3lib_div::slashJS($msg, false, '"').'")</script>';
	}


	/**
	 *
	 */
	function indexing_finished() {
		echo '
			<script type="text/javascript" language="javascript"> parent.finished()</script>';
	}


	/**
	 *
	 */
	function indexing_flushNow() {
		flush();
		ob_flush();
	}






	/*******************************************************
	 *
	 * indexSession
	 *
	 *******************************************************/


	/**
	 * Clears the index session
	 *
	 * @return void
	 */
	function indexSessionClear() {
		$this->indexSessionWrite(array());
	}


	/**
	 * Creates new index session and returns the data
	 *
	 * @return mixed
	 */
	function indexSessionNew($filesTodo) {
		$indexSession = array();
		$indexSession['filesTodo'] = $filesTodo;
		$indexSession['ID'] = uniqid('tx_dam_damindex_index');
		$indexSession['indexRun'] = time();
		$indexSession['currentCount'] = 0;
		$indexSession['totalFilesCount'] = count($filesTodo);
		$this->indexSessionWrite($indexSession);
		return $indexSession;
	}


	/**
	 * Returns the index session data
	 *
	 * @return mixed
	 */
	function indexSessionFetch() {
		return $GLOBALS['BE_USER']->getSessionData('tx_damindexSessionData');
	}

	/**
	 * Writes the index session
	 *
	 * @return void
	 */
	function indexSessionWrite($indexSession) {
		$GLOBALS['BE_USER']->setAndSaveSessionData('tx_damindexSessionData', $indexSession);
	}





	/*******************************************************
	 *
	 * GUI
	 *
	 *******************************************************/


	/**
	 * Returns HTML of a box with a step counter and "back" and "next" buttons
	 *
	 * @param	integer		current step (begins with 1)
	 * @param	integer		last step
	 * @return	string
	 */
	function getStepsBar($currentStep, $lastStep, $onClickBack='' ,$onClickFwd='', $buttonNameBack='', $buttonNameFwd='') {
		global $LANG;

		$bgcolor = t3lib_div::modifyHTMLcolor($this->pObj->doc->bgColor,-15,-15,-15);
		$nrcolor = t3lib_div::modifyHTMLcolor($bgcolor,30,30,30);

		$buttonNameBack = $buttonNameBack ? $buttonNameBack : $LANG->getLL('tx_damindex_index.back');
		$buttonNameFwd = $buttonNameFwd ? $buttonNameFwd : $LANG->getLL('tx_damindex_index.next');

		$content='';
		$buttons='';

		for ($i = 1; $i <= $lastStep; $i++) {
			$color = ($i == $currentStep) ? '#000' : $nrcolor ;
			$content.= '<span style="margin-left:5px; margin-right:5px; color:'.$color.';">'.$i.'</span>';
		}
		$content = '<span style="margin-left:50px; margin-right:25px; vertical-align:middle; font-family:Verdana,Arial,Helvetica; font-size:22px; font-weight:bold;">'.$content.'</span>';

		if($currentStep > 1) {
			$buttons.= '<input type="submit" name="indexStep['.($currentStep-1).']" onclick="'.htmlspecialchars($onClickBack).'" value="'.htmlspecialchars($buttonNameBack).'" style="margin-right:10px;" />';
		}


		if($currentStep < $lastStep) {
			$buttons.= '<input type="submit" name="indexStep['.($currentStep+1).']" onclick="'.htmlspecialchars($onClickFwd).'" value="'.htmlspecialchars($buttonNameFwd).'" />';
		} else {
			$buttons.= '<input type="submit" name="indexStart" value="'.htmlspecialchars($buttonNameFwd).'" onclick="'.htmlspecialchars($onClickFwd).'" style="font-weight:bold;background-color: #6b6;" />';
		}
		$content.= '<span id="stepsFormButtons" style="margin-left:25px;vertical-align:middle;">'.$buttons.'</span>';

		return '<div style="padding:4px; background:'.$bgcolor.';">'.$content.'</div><br />';
	}


	/***************************************
	 *
	 *	 this and that
	 *
	 ***************************************/

	/**
	 * Processes the submitted data for the indexing setup
	 *
	 * @return	void
	 */
	function processIndexSetup()	{
		global  $BE_USER, $LANG, $BACK_PATH;

			// get stored indexing setup from last page view or last session
		$storedSetup = unserialize($GLOBALS['SOBE']->MOD_SETTINGS['tx_damindex_indexSetup']);
		if(is_array($storedSetup['ruleConf'])) {
			$this->index->ruleConf = t3lib_div::array_merge_recursive_overrule($this->index->ruleConf, $storedSetup['ruleConf']);
		}
		if(is_array($storedSetup['dataPreset'])) {
			$this->index->dataPreset = t3lib_div::array_merge_recursive_overrule($this->index->dataPreset, $storedSetup['dataPreset']);
		}
		if(is_array($storedSetup['dataPostset'])) {
			$this->index->dataPostset = t3lib_div::array_merge_recursive_overrule($this->index->dataPostset, $storedSetup['dataPostset']);
		}

			// merging values to the current indexing setup
		$data = t3lib_div::_POST('data');

		if (is_array($data['rules'])) {
			$this->index->mergeRuleConf($data['rules']);
		} else {
			$this->index->mergeRuleConf();
		}


			// preset form
		if (is_array($data['tx_dam_simpleforms'][1])) {

				// get which fields are fixed
			$fixedFieldsArr = t3lib_div::_POST('data_fixedFields');
			$fixedFields=array();
			if (is_array($fixedFieldsArr['tx_dam_simpleforms'][1])) {
				foreach($fixedFieldsArr['tx_dam_simpleforms'][1] as $field => $isFixed) {
					if($isFixed) $fixedFields[] = $field;
				}
			}

				// split data to preset and fixed
			foreach($data['tx_dam_simpleforms'][1] as $field => $value) {
				if(in_array($field,$fixedFields)) {
					$this->index->dataPostset[$field] = $value;
				} else {
					$this->index->dataPreset[$field] = $value;
				}
			}
		}

		$this->index->setOptionsFromRules();

		$this->saveSettings();
	}

	/**
	 * Save preset in module settings
	 *
	 * @return	void
	 */
	function saveSettings() {
		$setup = array(
			'ruleConf' => $this->index->ruleConf,
			'dataPreset' => $this->index->dataPreset,
			'dataPostset' => $this->index->dataPostset,
			);

		$newSettings = array(
			'tx_damindex_indexSetup' => serialize($setup),
			'tx_dam_folder' => $this->pObj->path,
		);
		$GLOBALS['SOBE']->MOD_SETTINGS = t3lib_BEfunc::getModuleData($GLOBALS['SOBE']->MOD_MENU, $newSettings, $GLOBALS['SOBE']->MCONF['name'], $GLOBALS['SOBE']->modMenu_type, $GLOBALS['SOBE']->modMenu_dontValidateList, $GLOBALS['SOBE']->modMenu_setDefaultList);
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_index/modfunc_index/class.tx_damindex_index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam_index/modfunc_index/class.tx_damindex_index.php']);
}

?>
