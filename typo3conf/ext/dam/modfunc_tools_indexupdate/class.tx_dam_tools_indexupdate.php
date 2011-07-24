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
 * Module extension (addition to function menu) 'File Relation Check' for the 'Media>Info' module..
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage tools
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   84: class tx_dam_tools_indexupdate extends t3lib_extobjbase
 *   92:     function modMenu()
 *  120:     function main()
 *  140:     function moduleContent()
 *  347:     function statisticsLostRecords()
 *  406:     function statisticsMediaType($addFileTypes=true)
 *  515:     function statisticsFileType($media_type)
 *  553:     function checkIndex($indexSessionID)
 *  648:     function checkUploads($indexSessionID)
 *  742:     function getFilesInDir($path, $recursive=FALSE, $filearray=array(), $maxDirs=999)
 *  777:     function infoIcon ($type)
 *  808:     function indexing_getProgessTable()
 *  813:     function progress_bar_update(intCurrentPercent)
 *  824:     function addTableRow(cells)
 *  846:     function setMessage(msg)
 *  851:     function finished()
 *  900:     function indexing_progressBar($intCurrentCount = 100, $intTotalCount = 100)
 *  921:     function indexing_addTableRow($contentArr)
 *  935:     function indexing_setMessage($msg)
 *  944:     function indexing_finished()
 *  953:     function indexing_flushNow()
 *
 *              SECTION: indexSession
 *  975:     function indexSessionClear()
 *  985:     function indexSessionNew($totalFilesCount, $data='')
 * 1002:     function indexSessionFetch()
 * 1011:     function indexSessionWrite($indexSession)
 *
 * TOTAL FUNCTIONS: 24
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');


/**
 * Module 'Media>Tools>Update Index'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage tools
 */
class tx_dam_tools_indexupdate extends t3lib_extobjbase {


	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

		return array(
			'tx_dam_tools_indexupdate.age' => array(
				'0' => $LANG->getLL('tx_dam_tools_indexupdate.age_all'),
				'86400' => $LANG->getLL('tx_dam_tools_indexupdate.age_day'),
				'604800' => $LANG->getLL('tx_dam_tools_indexupdate.age_week'),
				'2419200' => $LANG->getLL('tx_dam_tools_indexupdate.age_month'),
			),
			'tx_dam_tools_indexupdate.deleteMissing' => '',
			'tx_dam_tools_indexupdate.func' => array(
				'index' => $LANG->getLL('tx_dam_tools_indexupdate.index_check'),
				'uploads' => $LANG->getLL('tx_dam_tools_indexupdate.uploads_check'),
				'lost_records' => $LANG->getLL('tx_dam_tools_indexupdate.lost_records_check'),
				'cleanup_meta' => $LANG->getLL('tx_dam_tools_indexupdate.cleanup_meta'),
				'statistics' => $LANG->getLL('tx_dam_tools_indexupdate.statistics'),
			),
		);
	}



	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()	{



		$content = '';
		$content.=  $this->pObj->getHeaderBar('', t3lib_BEfunc::getFuncMenu($this->pObj->id,'SET[tx_dam_tools_indexupdate.func]',$this->pObj->MOD_SETTINGS['tx_dam_tools_indexupdate.func'],$this->pObj->MOD_MENU['tx_dam_tools_indexupdate.func']));
		$content.= $this->pObj->doc->spacer(10);
		$content.= $this->moduleContent();

		return $content;
	}




	/**
	 * Generates the module content
	 *
	 * @return	string		HTML content
	 */
	function moduleContent()    {
		global  $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS;

		$content = '';

		$func = $this->pObj->MOD_SETTINGS['tx_dam_tools_indexupdate.func'];

		if (t3lib_div::_GP('start')) {
			$func .= '.start';
		}
		if (t3lib_div::_GP('process')) {
			$func .= '.process';
		}


			// reload at this time
		$max_execution_time = ini_get('max_execution_time');
		$max_execution_time = intval(($max_execution_time/3)*2);
		$this->indexEndtime = time()+$max_execution_time;


		switch($func)    {

			case 'lost_records':

				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.lost_records_check'), $LANG->getLL('tx_dam_tools_indexupdate.lost_records_description',1),0,1);
				$content.= $this->pObj->doc->spacer(10);

				if (t3lib_div::_GP('collect_lost_records')) {
					$values = array ('pid' => $this->pObj->defaultPid);
					$mediaTables = tx_dam::register_getEntries('mediaTable');
					foreach ($mediaTables as $table) {
						$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $table.'.pid NOT IN ('.tx_dam_db::getPidList().')', $values);
					}
				}


				list($lostRecordsFound, $statisticsLostRecords) = $this->statisticsLostRecords();

				if ($lostRecordsFound) {

					$content.= $statisticsLostRecords;
					$content.= $this->pObj->doc->spacer(10);
					$content.= '<p><input type="submit" name="collect_lost_records" value="'.$LANG->getLL('tx_dam_tools_indexupdate.lost_records_collect').'" /></p>';

				} else {

					$content.= '<p><strong>'.$LANG->getLL('tx_dam_tools_indexupdate.lost_records_all_fine').'</strong></p>';
					$content.= $statisticsLostRecords;
					$content.= $this->pObj->doc->spacer(10);
					$content.= '<p><input type="submit" name="" value="'.$LANG->getLL('tx_dam_tools_indexupdate.lost_records_check_again').'" /></p>';
				}

			break;

			case 'cleanup_meta':
				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.cleanup_meta'), $LANG->getLL('tx_dam_tools_indexupdate.cleanup_meta_description',1),0,1);
				$content.= $this->pObj->doc->spacer(10);

				if (t3lib_div::_GP('cleanup_meta')) {
					$countTotal = 0;

					$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'color_space='.$GLOBALS['TYPO3_DB']->fullQuoteStr('sRGB', 'tx_dam'), array('color_space' => 'RGB'));
					$countTotal += $GLOBALS['TYPO3_DB']->sql_affected_rows();

					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,title,file_name', 'tx_dam', 'title=\'\'');
					foreach ($rows as $row) {
						$title = tx_dam_indexing::makeTitleFromFilename($row['file_name']);
						$values = array('title' => $title);
						$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.$row['uid'], $values);
						$countTotal ++;
					}
					$content.= '<h4>Processing: '.$countTotal. ' changes made</h4>';
				}

				$content.= '<h4>Available functions:</h4>';
				$content.= '<ul>';
				$content.= '<li>Create title for empty titles</li>';
				$content.= '<li>Change unused color space \'sRGB\' to \'RGB\'</li>';
				$content.= '</ul>';
				$content.= $this->pObj->doc->spacer(10);
				$content.= '<p><input type="submit" name="cleanup_meta" value="'.$LANG->getLL('tx_dam_tools_indexupdate.cleanup_meta').'" /></p>';
			break;

			case 'statistics':

				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.statistics'), $LANG->getLL('tx_dam_tools_indexupdate.statistics_description',1),0,1);
				$content.= $this->pObj->doc->spacer(10);
				$content.= $this->statisticsMediaType();
				$content.= $this->pObj->doc->spacer(10);
				$content.= '<p><input type="submit" name="update" value="'.$LANG->getLL('tx_dam_tools_indexupdate.update').'" /></p>';
			break;

			case 'index':
				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.index_check'), $LANG->getLL('tx_dam_tools_indexupdate.index_description',1),0,1);
				$content.= $this->pObj->doc->spacer(10);

				$code = '';
				$ageMenu = t3lib_BEfunc::getFuncMenu('','SET[tx_dam_tools_indexupdate.age]',$this->pObj->MOD_SETTINGS['tx_dam_tools_indexupdate.age'],$this->pObj->MOD_MENU['tx_dam_tools_indexupdate.age']);
				$ageMenu = preg_replace('#onchange="[^"]*"#', '', $ageMenu);
				$code.= '<p>'.$LANG->getLL('tx_dam_tools_indexupdate.age').': '.$ageMenu.'<br /><span class="typo3-dimmed">'.$LANG->getLL('tx_dam_tools_indexupdate.age_descr').'</span></p>';
				$code.= '<p><br /></p>';

				$code.= '<p>';
				$code.= '<input type="hidden" name="SET[tx_dam_tools_indexupdate.deleteMissing]" value="0">';
				$code.= '<input type="checkbox" '.($this->pObj->MOD_SETTINGS['tx_dam_tools_indexupdate.deleteMissing']?'checked="checked"':'').' name="SET[tx_dam_tools_indexupdate.deleteMissing]" value="1"> '.
						$LANG->getLL('tx_dam_tools_indexupdate.deleteMissing',1);
				$code.= '<br /><span class="typo3-dimmed">'.$LANG->getLL('tx_dam_tools_indexupdate.deleteMissing_descr').'</span></p>';
				$code.= '</p>';
				$code.= '<p><br /></p>';

				$code.= '<p><input type="submit" name="start" /></p>';
				$content.= $code;

				$code = '';
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('COUNT(uid) as count', 'tx_dam', $where_clause='');
				reset($rows);
				$row = current($rows);
				$countTotal = $row['count'];
				$code = sprintf($LANG->getLL('tx_dam_tools_indexupdate.count_elements',1), $countTotal);
				$content.= $this->pObj->doc->spacer(10);
				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.statistics'), $code,0,1);

			break;

			case 'index.start':
				$code = '';
				$this->pObj->addParams['process'] = 1;
				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.updatedFiles'),'',0,1);
				$content.= $this->pObj->doc->spacer(10);
				$code.= $this->indexing_getProgessTable();
				$content.= $this->pObj->doc->section('',$code,0,1);
			break;

			case 'index.process':
				$this->pObj->addParams['process'] = 1;

				echo '<head>
					<title>indexing</title>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					</head>
					<body>';

					$this->checkIndex(t3lib_div::_GP('indexSessionID'));

				echo '</body>
					</html>';
				exit;
			break;



			case 'uploads':
				$content.= $this->pObj->doc->spacer(10);
				$content.= $LANG->getLL('tx_dam_tools_indexupdate.description',1);

				$code = '';
				$code.= $LANG->getLL('tx_dam_tools_indexupdate.uploads_description',1);
				$code.= $this->pObj->doc->spacer(10);
				$code.= '<p><input type="submit" name="start" /></p>';
				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.uploads_check'), $code,0,1);

				$code = '';
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('COUNT(uid) as count', 'tx_dam_file_tracking', $where_clause='');
				reset($rows);
				$row = current($rows);
				$countTotal = $row['count'];
				$code.= sprintf($LANG->getLL('tx_dam_tools_indexupdate.count_elements',1), $countTotal);
				$content.= $this->pObj->doc->spacer(10);
				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.statistics'), $code,0,1);
			break;

			case 'uploads.start':
				$code = '';
				$this->pObj->addParams['process'] = 1;
				$content.= $this->pObj->doc->section($LANG->getLL('tx_dam_tools_indexupdate.updatedFiles'),'',0,1);
				$content.= $this->pObj->doc->spacer(10);
				$code.= $this->indexing_getProgessTable();
				$content.= $this->pObj->doc->section('',$code,0,1);
			break;

			case 'uploads.process':
				$this->pObj->addParams['process'] = 1;

				echo '<head>
					<title>indexing</title>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					</head>
					<body>';

					$this->checkUploads(t3lib_div::_GP('indexSessionID'));

				echo '</body>
					</html>';
				exit;
			break;
		}

		return $content;
	}


	/**
	 * Render statistics about lostrecords
	 *
	 * @return	array		array($countTotal, $content)
	 */
	function statisticsLostRecords() {
		global $LANG, $TCA;

		$content = '';
		$countTotal = 0;

			// init table layout
		$tableLayout = array(
			'table' => array('<table border="0" cellspacing="1" cellpadding="2" style="width:auto;">', '</table>'),
			'0' => array(
				'tr' => array('<tr class="bgColor2">','</tr>'),
				'defCol' => array('<td align="center">','</td>'),
			),
			'defRow' => array(
				'tr' => array('<tr class="bgColor3-20">','</tr>'),
				'1' => array('<td align="center">','</td>'),
				'defCol' => array('<td>','</td>'),
			)
		);

		$tableOutput = array();
		$tr = 0;

			// add header row
		$tableOutput[$tr][] = 'Table';
		$tableOutput[$tr][] = 'Count';


		$mediaTables = tx_dam::register_getEntries('mediaTable');

		foreach ($mediaTables as $table) {
			$count = 0;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid) as count', $table, $table.'.pid NOT IN ('.tx_dam_db::getPidList().')');
			if ($res) {
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$count = $row['count'];
				$countTotal += $count;
			}
			$title = is_array($TCA[$table]) ? $GLOBALS['LANG']->sl($TCA[$table]['ctrl']['title']) : $table;

			$icon = t3lib_iconWorks::getIconImage($table, array(), $GLOBALS['BACK_PATH'],' align="top"');

				// add row to table
			$tr++;
			$tableOutput[$tr][] = $icon.$title.' ('.$table.')';
			$tableOutput[$tr][] = $count;
		}

		$content .= $this->pObj->doc->table($tableOutput, $tableLayout);

		return array($countTotal, $content);
	}


	/**
	 * Render statistics about indexed media types
	 *
	 * @return	string		HTML output
	 */
	function statisticsMediaType($addFileTypes=true) {
		global $LANG;

		$content = '';

			// init table layout
		$tableLayout = array(
			'table' => array('<table border="0" cellspacing="1" cellpadding="2" style="width:auto;">', '</table>'),
			'0' => array(
				'tr' => array('<tr class="bgColor2">','</tr>'),
				'defCol' => array('<td align="center">','</td>'),
			),
			'defRow' => array(
				'tr' => array('<tr class="bgColor3-20">','</tr>'),
				'1' => array('<td align="center">','</td>'),
				'4' => array('<td align="right" style="padding-right:0.5em;">','</td>'),
				'5' => array('<td align="right" style="padding-right:0.5em;">','</td>'),
				'defCol' => array('<td>','</td>'),
			)
		);

		$table = array();
		$tr = 0;

			// add header row
		$table[$tr][] = '&nbsp';
		$table[$tr][] = $LANG->getLL('mediaTypes',1);
		$table[$tr][] = '&nbsp';
		$table[$tr][] = '&nbsp';
		$table[$tr][] = 'Count';
		$table[$tr][] = 'Total';

		$totalCount = 0;

		foreach ($GLOBALS['T3_VAR']['ext']['dam']['media2code'] as $media_type) {


			$whereClauses = array();
			$whereClauses['media_type'] = 'media_type='.$media_type;
			$rows = tx_dam_db::getDataWhere ('COUNT(uid) as count', $whereClauses);
			reset($rows);
			$row = current($rows);
			$count = $row['count'];

			$totalCount += $count;

			$icon = tx_dam_guiFunc::icon_getMediaTypeImgTag(array('media_type' => $media_type), '', false);
			$mediaTypeName = tx_dam_guiFunc::convert_mediaType($media_type);


				// add row to table
			$tr++;
			$table[$tr][] = $icon;
			$table[$tr][] = '<strong>'.htmlspecialchars($mediaTypeName).'</strong>';
			$table[$tr][] = '&nbsp;';
			$table[$tr][] = '&nbsp;';
			$table[$tr][] = '&nbsp;';
			$table[$tr][] = '<strong>'.$count.'</strong>';

			$tableLayout[$tr]['tr'] = array('<tr class="bgColor4">','</tr>');

			if ($addFileTypes) {
				if ($contentFileTypes = $this->statisticsFileType($media_type)) {
					foreach ($contentFileTypes as $fileTypeRow) {
							// add row to table
						$tr++;
						$table[$tr][] = '&nbsp';
						$table[$tr][] = '&nbsp';
						$table[$tr][] = $fileTypeRow[0];
						$table[$tr][] = $fileTypeRow[1];
						$table[$tr][] = $fileTypeRow[2];
						$table[$tr][] = '&nbsp';

					}
				}
			}
			// add row to table
		$tr++;
		$table[$tr][] = '<span></span>';
		$table[$tr][] = '<span></span>';
		$table[$tr][] = '<span></span>';
		$table[$tr][] = '<span></span>';
		$table[$tr][] = '<span></span>';
		$table[$tr][] = '<span></span>';
		$tableLayout[$tr]['tr'] = array('<tr class="bgColor" style="height:0.5em;">','</tr>');

		}

			// add row to table
		$tr++;
		$table[$tr][] = '&nbsp;';
		$table[$tr][] = '&nbsp;';
		$table[$tr][] = '&nbsp;';
		$table[$tr][] = '&nbsp;';
		$table[$tr][] = '&nbsp;';
		$table[$tr][] = '<strong>'.$totalCount.'</strong>';
		$tableLayout[$tr]['tr'] = array('<tr class="bgColor4-20">','</tr>');

		$content .= $this->pObj->doc->table($table, $tableLayout);
		return $content;
	}



	/**
	 * Render statistics about indexed media types
	 *
	 * @return	string		HTML output
	 */
	function statisticsFileType($media_type) {

		$table = array();
		$tr = 0;

		$whereClauses = array();
		$whereClauses['media_type'] = 'media_type='.$media_type;
		$rows = tx_dam_db::getDataWhere ('DISTINCT file_type', $whereClauses);

		foreach ($rows as $row) {
			$file_type = $row['file_type'];
			$whereClauses['file_type'] = 'file_type='.$GLOBALS['TYPO3_DB']->fullQuoteStr($file_type, 'tx_dam');
			$rowsCount = tx_dam_db::getDataWhere ('COUNT(uid) as count', $whereClauses);
			reset($rowsCount);
			$rowsCount = current($rowsCount);
			$count = $rowsCount['count'];

			$icon = tx_dam::icon_getFileTypeImgTag(array('media_type' => $media_type, 'file_type' => $file_type), '', false);

				// add row to table
			$tr++;
			$table[$tr][] = $icon;
			$table[$tr][] = htmlspecialchars(strtoupper($file_type));
			$table[$tr][] = $count;

		}

		return $table;
	}



	/**
	 * Do the file indexing
	 * Read files from a directory index them and output a result table
	 *
	 * @return	string		HTML content
	 */
	function checkIndex($indexSessionID) {
		global $LANG, $TYPO3_CONF_VARS;


			// makes sense? Was a hint on php.net
		ob_end_flush();

			// get session data - which might have left files stored
		$indexSession = $this->indexSessionFetch();

		$where = array();
		if ($age = intval($this->pObj->MOD_SETTINGS['tx_dam_tools_indexupdate.age'])) {
			$where['tstamp'] = 'tstamp<'.(time()-$age);
		}

		if($indexSessionID=='' OR !isset($indexSession['ID']) OR !($indexSession['ID']==$indexSessionID) OR $indexSession['currentCount']==0 ) {

			$rows = tx_dam_db::getDataWhere('COUNT(uid) as count', $where);
			reset($rows);
			$row = current($rows);
			$countTotal = $row['count'];

			$indexSession = $this->indexSessionNew($countTotal);

		}

		$files_at_a_time = 200;

		$rows = tx_dam_db::getDataWhere('', $where, '', '', intval($indexSession['currentCount']).','.$files_at_a_time);

		if ($rows) {

			$c = 0;
			foreach ($rows as $meta) {
				$c ++;

					// increase progress bar
				$indexSession['currentCount']++;

				if(is_array($meta)) {

					$status = tx_dam::meta_updateStatus ($meta, $this->pObj->MOD_SETTINGS['tx_dam_tools_indexupdate.deleteMissing']);

					$ctable = array();
					switch ($status) {
						case TXDAM_status_file_changed:
								$ctable[] = $this->infoIcon(2);
							break;
						case TXDAM_status_file_missing:
								$ctable[] = $this->infoIcon(3);
							break;

						default:
								$ctable[] = $this->infoIcon(-1);
							break;
					}
					$ctable[] = tx_dam::icon_getFileTypeImgTag($meta,'align="top"').'&nbsp;'.htmlspecialchars(t3lib_div::fixed_lgd_cs($meta['file_name'],30));
					$ctable[] = htmlspecialchars(t3lib_div::fixed_lgd_cs($meta['file_path'],-30));

					$this->indexing_addTableRow($ctable);
					$msg = $LANG->getLL('tx_dam_tools_indexupdate.updatedMessage',1);
					$code = sprintf($msg, $indexSession['currentCount'], $indexSession['totalFilesCount']);
					$this->indexing_setMessage($code);
				}

				$this->indexing_progressBar($indexSession['currentCount'], $indexSession['totalFilesCount']);
				$this->indexing_flushNow();

				$this->indexSessionWrite($indexSession);

				if (($this->indexEndtime < time() OR $c == $files_at_a_time) AND ($indexSession['currentCount'] < $indexSession['totalFilesCount'])) {
					$params = $this->pObj->addParams;
					$params['indexSessionID'] = $indexSession['ID'];
					echo '
						<script type="text/javascript">  window.location.href = unescape("'.t3lib_div::rawUrlEncodeJS(tx_dam_SCbase::linkThisScriptStraight($params)).'"); </script>';
					exit;
				}

			}

		} elseif ($indexSession['totalFilesCount']==0) {
			$code = $LANG->getLL('tx_dam_tools_indexupdate.no_files');
			$this->indexing_setMessage($code);

		}

		$this->indexing_finished();

			// finished - clear session
		$this->indexSessionClear();
	}


	/**
	 * Do the file indexing
	 * Read files from a directory index them and output a result table
	 *
	 * @return	string		HTML content
	 */
	function checkUploads($indexSessionID) {
		global $LANG, $TYPO3_CONF_VARS;


			// makes sense? Was a hint on php.net
		ob_end_flush();

			// get session data - which might have left files stored
		$indexSession = $this->indexSessionFetch();


		$where = array();
		if ($age = intval($this->pObj->MOD_SETTINGS['tx_dam_tools_indexupdate.age'])) {
			$where['tstamp'] = 'tstamp<'.(time()-$age);
		}

		if($indexSessionID=='' OR !isset($indexSession['ID']) OR !($indexSession['ID']==$indexSessionID) OR $indexSession['currentCount']==0 ) {

			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_file_tracking', '');
			$files = array();
			$files = $this->getFilesInDir(PATH_site.'uploads/', true, $files);
			$countTotal = count($files);

			$indexSession = $this->indexSessionNew($countTotal, $files);

		}

		if (is_array($indexSession['data'])) {

			$damIndexing = t3lib_div::makeInstance('tx_dam_indexing');
			$damIndexing->init();
			$damIndexing->dryRun=TRUE;

			foreach ($indexSession['data'] as $key => $file) {

					// increase progress bar
				$indexSession['currentCount']++;

				$fileHash = tx_dam::file_calcHash($file);
				$fileInfo = tx_dam::file_compileInfo($file);

				$fields_values = array (
					'tstamp' => time(),
					'file_name' => $fileInfo['file_name'],
					'file_path' => $fileInfo['file_path'],
					'file_size' => $fileInfo['file_size'],
					'file_ctime' => min ($fileInfo['file_ctime'], $fileInfo['file_mtime']),
					'file_hash' => $fileHash,
				);
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_file_tracking', $fields_values);

				$fileInfo=array_merge($fileInfo,$damIndexing->getFileMimeType($file));
				$fileInfo['media_type']=tx_dam::convert_mediaType($fileInfo['file_mime_type']);

				$ctable = array();
				$ctable[] = '&nbsp;';
				$ctable[] = tx_dam::icon_getFileTypeImgTag($fileInfo,'align="top"').'&nbsp;'.htmlspecialchars(t3lib_div::fixed_lgd_cs($fileInfo['file_name'],30));
				$ctable[] = htmlspecialchars(t3lib_div::fixed_lgd_cs($fileInfo['file_path'],-30));

				$this->indexing_addTableRow($ctable);
				$msg = $LANG->getLL('tx_dam_tools_indexupdate.updatedMessage',1);
				$code = sprintf($msg, $indexSession['currentCount'], $indexSession['totalFilesCount']);
				$this->indexing_setMessage($code);

				$this->indexing_progressBar($indexSession['currentCount'], $indexSession['totalFilesCount']);
				$this->indexing_flushNow();

				$this->indexSessionWrite($indexSession);

				if (($this->indexEndtime < time()) AND ($indexSession['currentCount'] < $indexSession['totalFilesCount'])) {
					$params = $this->pObj->addParams;
					$params['indexSessionID'] = $indexSession['ID'];
					echo '
						<script type="text/javascript">  window.location.href = unescape("'.t3lib_div::rawUrlEncodeJS(tx_dam_SCbase::linkThisScriptStraight($params)).'"); </script>';
					exit;
				}

			}

		} elseif ($indexSession['totalFilesCount']==0) {
			$code = $LANG->getLL('tx_dam_tools_indexupdate.no_files');
			$this->indexing_setMessage($code);

		}

		$this->indexing_finished();

			// finished - clear session
		$this->indexSessionClear();
	}


	/**
	 * Returns an array with the names of files in a specific path
	 *
	 * @param	string		Path to start to collect files
	 * @param	boolean		Go recursive into subfolder?
	 * @param	array		Array of file paths
	 * @param	integer		$maxDirs limit the read directories
	 * @return	array		Array of file paths
	 */
	function getFilesInDir($path, $recursive=FALSE, $filearray=array(), $maxDirs=999)	{
		if ($path)	{
			$path = preg_replace('#/$#','',$path);
			$absPath = tx_dam::path_makeAbsolute($path);
			$d = @dir($absPath);
			if (is_object($d))	{
				while($entry=$d->read()) {
					if (@is_file($absPath.'/'.$entry))	{
						if (!preg_match('/^\./',$entry) && !preg_match('/~$/',$entry)) {
							$key = md5($absPath.'/'.$entry);
							$filearray[$key] = $absPath.'/'.$entry;
						}
					} elseif ($recursive && $maxDirs>0 && @is_dir($absPath.'/'.$entry) && !preg_match('/^\./',$entry) && $entry!='CVS')	{
						$filearray = $this->getFilesInDir($path.'/'.$entry, true, $filearray, $maxDirs-1);
					}
				}
				$d->close();
			}
		}
		return $filearray;
	}


	/**
	 * Returns an image-tag with an 18x16 icon of the following types:
	 *
	 * $type:
	 * -1:	OK icon (Check-mark)
	 * 1:	Notice (Speach-bubble)
	 * 2:	Warning (Yellow triangle)
	 * 3:	Fatal error (Red stop sign)
	 *
	 * @param	integer		See description
	 * @return	string		HTML image tag (if applicable)
	 */
	function infoIcon ($type)	{
		global $BACK_PATH;

		$title = '';

		switch($type)	{
			case '3':
				$icon = 'gfx/icon_fatalerror.gif';
				$title = 'File does not exist!';
			break;
			case '2':
				$icon = 'gfx/icon_warning.gif';
				$title = 'File changed';
			break;
			case '1':
				$icon = 'gfx/icon_note.gif';
			break;
			case '-1':
				$icon = 'gfx/icon_ok.gif';
			break;
			default:
			break;
		}
		if ($icon)	{
			return '<img'.t3lib_iconWorks::skinImg($BACK_PATH,$icon,'width="18" height="16"').' class="absmiddle" title="'.htmlspecialchars($title).'" alt="" />';
		}
	}

	/**
	 *
	 */
	function indexing_getProgessTable() {
		global  $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS;

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

				document.getElementById("progressTable").style.visibility = "visible";

				var tbody = document.getElementById("progressTable").getElementsByTagName("tbody")[0];
				var row = document.createElement("TR");

				row.style.backgroundColor = "#D9D5C9";

				for (var cellId in cells) {
					var tdCell = document.createElement("TD");
					tdCell.innerHTML = cells[cellId];
					row.appendChild(tdCell);
				}
				var header = document.getElementById("progressTableheader");
				var headerParent = header.parentNode;
				headerParent.insertBefore(row,header.nextSibling);

				// tbody.appendChild(row);
				// tbody.insertBefore(row,document.getElementById("progressTableheader"));
			}

			function setMessage(msg) {
				var messageCnt = document.getElementById("message");
				messageCnt.innerHTML = msg;
			}

			function finished() {
				progress_bar_update(100);
				document.getElementById("progress_bar_left").innerHTML = "'.$LANG->getLL('tx_dam_tools_indexupdate.finished',1).'";
				// document.getElementById("btn_back").style.visibility = "visible";
			}
		');


		if(tx_dam::config_getValue('setup.devel')) {
			$iframeSize = 'width="100%" height="300" border="1" scrolling="yes" frameborder="1"';
		} else {
			$iframeSize = 'width="0" height="0" border="0" scrolling="no" frameborder="0"';
		}

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


		$code.= '
			 <div id="message"></div>
			 <table id="progressTable" style="visibility:hidden" cellpadding="1" cellspacing="1" border="0" width="100%">
			 <tr id="progressTableheader" bgcolor="'.$this->pObj->doc->bgColor5.'">
				 <th></th>
				 <th>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name',1).'</th>
				 <th>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_path',1).'</th>
			</tr>
			</table>
		';

		return $code;
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
			$contentArr[$key] = t3lib_div::slashJS((string)$val, false, '"');
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
	function indexSessionNew($totalFilesCount, $data='') {
		$indexSession = array();
		$indexSession['ID'] = uniqid('tx_dam_tools_indexupdate');
		$indexSession['indexRun'] = time();
		$indexSession['currentCount'] = 0;
		$indexSession['totalFilesCount'] = $totalFilesCount;
		$indexSession['data'] = $data;
		$this->indexSessionWrite($indexSession);
		return $indexSession;
	}


	/**
	 * Returns the index session data
	 *
	 * @return mixed
	 */
	function indexSessionFetch() {
		return $GLOBALS['BE_USER']->getSessionData('tx_damindexUpdateSessionData');
	}

	/**
	 * Writes the index session
	 *
	 * @return void
	 */
	function indexSessionWrite($indexSession) {
		$GLOBALS['BE_USER']->setAndSaveSessionData('tx_damindexUpdateSessionData', $indexSession);
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_indexupdate/class.tx_dam_tools_indexupdate.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_indexupdate/class.tx_dam_tools_indexupdate.php']);
}

?>
