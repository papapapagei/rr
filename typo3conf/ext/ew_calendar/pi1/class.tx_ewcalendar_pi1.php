<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Elio Wahlen <vorname at vorname punkt de>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(t3lib_extMgm::extPath('ew_pibase'). 'class.tx_ewpibase.php');


/**
 * Plugin 'Calendar' for the 'ew_calendar' extension.
 *
 * @author	Elio Wahlen <vorname at vorname punkt de>
 * @package	TYPO3
 * @subpackage	tx_ewcalendar
 */
class tx_ewcalendar_pi1 extends tx_ewpibase {
	var $prefixId      = 'tx_ewcalendar_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_ewcalendar_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ew_calendar';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{
		$this->conf = $conf;
		$this->pi_loadLL();
		$this->loadTemplate('pi1/template.html');
		
		$content = $this->renderList();
		
		return $content;
	}
	
	function renderList() {
		$select = '*';
		$table = 'tx_ewcalendar_dates';
		// we try to get the default language entry (normal behaviour) or, if not possible, currently the needed language (fallback if no default language entry is available)
		$whereLanguage = '(sys_language_uid IN (-1,0) OR (sys_language_uid = ' .$GLOBALS['TSFE']->sys_language_uid. ' AND l10n_parent = 0))';
		// always (!) use TYPO3 default function for adding hidden = 0, deleted = 0, group and date statements
		$where  = $whereLanguage . $GLOBALS['TSFE']->sys_page->enableFields($table);
		$order = 'date DESC';
		$group = '';
		$limit = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $table, $where, $group, $order, $limit);
		
		$current_dates = array();
		$past_dates = array();
		$archive_year = '';
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			// check for language overlay if:
			// * row is valid
			// * row language is different from currently needed language
			// * sys_language_contentOL is set
			if (is_array($row) && $row['sys_language_uid'] != $GLOBALS['TSFE']->sys_language_content && $GLOBALS['TSFE']->sys_language_contentOL) {
				$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($table, $row,$GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL);
			}
			if ($row) {
				// get the additional information from the production element
				$rowProd = $this->fetchProductionInfos($row);
				
				$row['title'] = empty($row['title']) ? $rowProd['title'] : $row['title'];
				$facts = array_merge( preg_split('/\n/',$row['facts']), preg_split('/\n/',$rowProd['facts']) );
				
				// get correct language uid for translated realurl link
				$link_uid = ($row['_LOCALIZED_UID']) ? $row['_LOCALIZED_UID'] : $row['uid'];
				
				$time_criterion = mktime( 0, 0, 0, date("n"), date("d") ) - 2*60*60*24; // die vorstellungen von vorgestern sind noch dabei
				$bInPast = $row[date] < $time_criterion;
/*				if ( $bInPast && !$row['highlight'] ) { // nicht-highlights fliegen aus dem Archiv raus
					continue;
				}*/
				$date = getdate($row['date']);
				$this->addMarker('DAY',($row['only_month'] || $bInPast) ? '' : sprintf('%02d',$date['mday']) . $this->pi_getLL('day.delimiter'));
				$this->addMarker('MONTH',$this->pi_getLL('month.'.$date['mon']));
				$this->addMarker('YEAR',$date['year']);
				$this->addMarker('TITLE',$this->cObj->typolink($row['title'],array("parameter"=>$rowProd['link'])));
				$this->addMarker('INFO',$row['info']);
				$this->addMarker('INFO_FONT_SIZE', ($row['info_size'] == -1) ? 'smallFont' : (($row['info_size'] == -2) ? 'smallerFont' : ''));
				$details = $this->renderDetails($facts);
				$this->addMarker( 'NO', 0 );
				$this->addMarker( 'NO_DETAILS_START', $row['only_month'] ? '<!--' : '' );
				$this->addMarker( 'NO_DETAILS_END', $row['only_month'] ? '-->' : '' );
				$this->addMarker( 'DETAIL_LABEL', $this->pi_getLL('date') );
				$dayAndMonth = $row['only_month'] ? '' : sprintf('%02d',$date['mday']) . $this->pi_getLL('day.delimiter') . ' ';
				$dayAndMonth .= $this->pi_getLL('month.'.$date['mon']);
				$this->addMarker( 'DETAIL_VALUE', $dayAndMonth );
				$details = $this->renderSubpart( 'DETAIL' ) . $details;
				$this->addMarker( 'NO', 0 );
				$this->addMarker( 'DETAIL_LABEL', empty($row['link_title'])? $this->pi_getLL('more') : $row['link_title'] );
				$link_title = empty($row['link_text']) ? $row['link'] : $row['link_text'];
				$this->addMarker( 'DETAIL_VALUE', $this->cObj->typolink( $link_title, array( "parameter" => $row['link'], "extTarget" => "_blank") ) );
				$details .= empty($row['link']) ? '' : $this->renderSubpart( 'DETAIL' );
				$this->addMarker( 'DETAILS', $details );

				// if no image selected, get that of the selected production
				$imageFromProduction = ($row['image'] == 0) && ($rowProd['image'] > 0);
				$image = current($this->get_dam_images( $imageFromProduction ? $rowProd['uid'] : $row['uid'],
						'image',
						$imageFromProduction ? 'tx_ewcalendar_productions' : 'tx_ewcalendar_dates'));
				if ( ( $row['image'] > 0 ) || $imageFromProduction ) {
					$imageCode = $this->cObj->IMAGE(array('file'=>$image['path'],
						'file.' => array('width' => $this->conf['image.']['width'].'c','height' => $this->conf['image.']['height'].'c')));
				} else {
					$imageCode = '';
				}
				$this->addMarker('IMAGE',$this->cObj->typolink($imageCode,array("parameter"=>$rowProd['link'])));
				if ( $bInPast ) {
					if ( $archive_year != $date['year'] ) {
						$archive_year = $date['year'];
						$this->conf['archiveTitle.']['file.']['10.']['text'] = $this->pi_getLL('archive_title') . ' ' . $archive_year;
						$archive_title_code = $this->cObj->IMAGE($this->conf['archiveTitle.']);
						$this->addMarker('ARCHIVE_TITLE',$archive_title_code);
						$past_dates[] = $this->renderSubpart( 'ARCHIVE_YEAR' );
					}
					$past_dates[] = $this->renderSubpart( 'ARCHIVE_ITEM' );
				} else {
					$current_dates[] = $this->renderSubpart( 'ITEM' );
				}
			}
		}
		$current_dates = array_reverse($current_dates);
		$currentDatesString = '';
		foreach ( $current_dates as $date ) {
			$currentDatesString .= $date;
		}
		$pastDatesString = '';
		foreach ( $past_dates as $date ) {
			$pastDatesString .= $date;
		}
		$this->addMarker('CURRENT_DATES',$currentDatesString);
		$this->addMarker('ARCHIVE',$pastDatesString);
			
		$content = $this->renderSubpart( 'CALENDAR' );
		return $content;
	}
	
	function fetchProductionInfos($row) {
		$rowProd = array();
		if ($row['production'] > 0) {
			$whereLanguage = '(sys_language_uid IN (-1,0) OR (sys_language_uid = ' .$GLOBALS['TSFE']->sys_language_uid. ' AND l10n_parent = 0))';
			$prodSelect = '*';
			$prodTable = 'tx_ewcalendar_productions';
			$prodWhere  = 'uid = ' . $row['production'] . ' AND ' . $whereLanguage . $GLOBALS['TSFE']->sys_page->enableFields($prodTable);
			$prodOrder = '';
			$prodGroup = '';
			$prodLimit = '1';
			$resProd = $GLOBALS['TYPO3_DB']->exec_SELECTquery($prodSelect, $prodTable, $prodWhere, $prodGroup, $prodOrder, $prodLimit);
			if ( $rowProd = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resProd) ) {
				// check for language overlay if:
				// * row is valid
				// * row language is different from currently needed language
				// * sys_language_contentOL is set
				if (is_array($rowProd) && $rowProd['sys_language_uid'] != $GLOBALS['TSFE']->sys_language_content && $GLOBALS['TSFE']->sys_language_contentOL) {
					$rowProd = $GLOBALS['TSFE']->sys_page->getRecordOverlay($prodTable, $rowProd,$GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL);
				}
			}
		}
		return $rowProd;
	}
	
	function renderDetails($details) {
		$detailList = '';
		$i = 0;
		foreach( $details as $detail ) {
			if ( empty($detail) ) {
				continue;
			}
			$i++;
			$delimiterPos = strpos($detail,':');
			if ( $delimiterPos === false ) {
				$label = $detail;
				$value = '';
			} else {
				$label = substr( $detail, 0, $delimiterPos);
				$value = substr( $detail, $delimiterPos+1);
			}
			$this->addMarker( 'NO', $i );
			$this->addMarker( 'DETAIL_LABEL', $label );
			$this->addMarker( 'DETAIL_VALUE', $value );
			$detailList .= $this->renderSubpart( 'DETAIL' );
		}
		return $detailList;
	}

	/*
	 * This generates the Menu Ticker
	*/
	
	function getNextEvent($content,$conf) {
		$select = '*';
		$table = 'tx_ewcalendar_dates';
		// we try to get the default language entry (normal behaviour) or, if not possible, currently the needed language (fallback if no default language entry is available)
		$where = '(sys_language_uid IN (-1,0) OR (sys_language_uid = ' .$GLOBALS['TSFE']->sys_language_uid. ' AND l10n_parent = 0))';
		// always (!) use TYPO3 default function for adding hidden = 0, deleted = 0, group and date statements
		$where  .= $GLOBALS['TSFE']->sys_page->enableFields($table);
		// nur wenn es heute oder in zukunft stattfindet (und der genaue termin feststeht)
		$where .= ' AND date >= ' . mktime( 0, 0, 0 ) . ' AND NOT only_month AND NOT dont_teaser';
		$order = 'date ASC';
		$group = '';
		$limit = '1'; // nur 1 event
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $table, $where, $group, $order, $limit);
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$rowProd = $this->fetchProductionInfos($row);
			$row['title'] = empty($row['title']) ? $rowProd['title'] : $row['title'];
			
			$this->pi_loadLL();
			switch (strtolower($conf['type'])) {
				case 'date' :
					$date = getdate($row['date']);
					$dayAndMonth = sprintf('%02d',$date['mday']) . $this->pi_getLL('day.delimiter') . ' ' . $this->pi_getLL('month.'.$date['mon']);
					return $dayAndMonth;
				case 'title' :
					$title = $row['title'];
					return $title;
			}
		}
		return '';
	}
	
}

// this is the class for the TS-USERFUNC-INTERFACE (a dummy)
class user_tx_ewcalendar_pi1 extends tx_ewcalendar_pi1 {
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_calendar/pi1/class.tx_ewcalendar_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_calendar/pi1/class.tx_ewcalendar_pi1.php']);
}

?>