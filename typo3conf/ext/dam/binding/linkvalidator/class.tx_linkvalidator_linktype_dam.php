<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2005 - 2010 Jochen Rieger (j.rieger@connecta.ag)
 *  (c) 2010 - 2011 Michael Miousse (michael.miousse@infoglobe.ca)
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
 * This class provides Check Internal Links plugin implementation.
 *
 * @author Dimitri König <dk@cabag.ch>
 * @author Michael Miousse <michael.miousse@infoglobe.ca>
 * @package TYPO3
 * @subpackage linkvalidator
 */
class tx_linkvalidator_linktype_Dam extends tx_linkvalidator_linktype_Abstract {

	const DELETED = 'deleted';
	const HIDDEN = 'hidden';
	const MOVED = 'moved';
	const NOTEXISTING = 'notExisting';

	/**
	 * All parameters needed for rendering the error message.
	 *
	 * @var array
	 */
	protected $errorParams = array();

	/**
	 * Result of the check, if the current media uid is valid or not.
	 *
	 * @var boolean
	 */
	protected $responseMedia = TRUE;
	
	public function __construct(){
		$GLOBALS['LANG']->includeLLFile('EXT:dam/binding/linkvalidator/locallang.xml');
	}

	/**
	 * type fetching method, based on the type that softRefParserObj returns.
	 *
	 * @param   array	  $value: reference properties
	 * @param   string	 $type: current type
	 * @param   string	 $key: validator hook name
	 * @return  string	 fetched type
	 */
	public function fetchType($value, $type, $key) {
		$item = explode(':',$value['recordRef']);
		if ($item[0] == 'tx_dam') {
			$type = 'dam';
		}
		return $type;
	}
	
	/**
	 * Checks a given URL + /path/filename.ext for validity
	 *
	 * @param   string	  $url: url to check as media-id or media-id#anchor (if anchor is present)
	 * @param	 array	  $softRefEntry: the softref entry which builds the context of that url
	 * @param   object	  $reference:  parent instance of tx_linkvalidator_Processor
	 * @return  string	  TRUE on success or FALSE on error
	 */
	public function checkLink($url, $softRefEntry, $reference) {
		$media = '';
		$anchor = '';
		$response = TRUE;
		
			// Might already contain values - empty it.
		unset($this->errorParams);


			// defines the linked media and anchor (if any).
		if (strpos($url, '#c') !== FALSE) {
			$parts = explode('#c', $url);
			$media = $parts[0];
			$anchor = $parts[1];
		} else {
			$media = $url;
		}

			// Check if the linked media is OK.
		$this->responseMedia = $this->checkMedia($media, $softRefEntry, $reference);


		if ((is_array($this->errorParams['media']) && !$this->responseMedia)) {
			$this->setErrorParams($this->errorParams);
		}

		return $this->responseMedia;
	}

	/**
	 * Checks a given media uid for validity
	 *
	 * @param   string	  $media: media uid to check
	 * @param	 array	  $softRefEntry: the softref entry which builds the context of that url
	 * @param   object	  $reference:  parent instance of tx_linkvalidator_Processor
	 * @return  string	  TRUE on success or FALSE on error
	 */
	protected function checkMedia($media, $softRefEntry, $reference) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, title, deleted, hidden, starttime, endtime',
			'tx_dam',
			'uid = ' . intval($media)
		);
		$this->responseMedia = TRUE;

		if ($rows[0]) {
			if ($rows[0]['deleted'] == '1') {
				$this->errorParams['errorType']['media'] = DELETED;
				$this->errorParams['media']['title'] = $rows[0]['title'];
				$this->errorParams['media']['uid'] = $rows[0]['uid'];
				$this->responseMedia = FALSE;
			} elseif ($rows[0]['hidden'] == '1'
				|| $GLOBALS['EXEC_TIME'] < intval($rows[0]['starttime'])
				|| ($rows[0]['endtime'] && intval($rows[0]['endtime']) < $GLOBALS['EXEC_TIME'])) {
				$this->errorParams['errorType']['media'] = HIDDEN;
				$this->errorParams['media']['title'] = $rows[0]['title'];
				$this->errorParams['media']['uid'] = $rows[0]['uid'];
				$this->responseMedia = FALSE;
			}
		} else {
			$this->errorParams['errorType']['media'] = NOTEXISTING;
			$this->errorParams['media']['uid'] = intval($media);
			$this->responseMedia = FALSE;
		}

		return $this->responseMedia;
	}


	/**
	 * Generate the localized error message from the error params saved from the parsing.
	 *
	 * @param   array    all parameters needed for the rendering of the error message
	 * @return  string    validation error message
	 */
	public function getErrorMessage($errorParams) {
		$errorType = $errorParams['errorType'];

		if (is_array($errorParams['media'])) {
			switch ($errorType['media']) {
				case DELETED:
					$response = $GLOBALS['LANG']->getLL('list.report.damdeleted');
					$response = str_replace('###title###', $errorParams['media']['title'], $response);
					$response = str_replace('###uid###', $errorParams['media']['uid'], $response);
					break;

				case HIDDEN:
					$response = $GLOBALS['LANG']->getLL('list.report.damnotvisible');
					$response = str_replace('###title###', $errorParams['media']['title'], $response);
					$response = str_replace('###uid###', $errorParams['media']['uid'], $response);
					break;

				default:
					$response = $GLOBALS['LANG']->getLL('list.report.damnotexisting');
					$response = str_replace('###uid###', $errorParams['media']['uid'], $response);
			}
		}
		return $response;
	}

	/**
	 * Url parsing
	 *
	 * @param   array	   $row: broken link record
	 * @return  string	  parsed broken url
	 */
	public function getBrokenUrl($row) {
		
		return 'media record with id:' . $row['url'] ;
	}
}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/linkvalidator/classes/linktypes/class.tx_linkvalidator_linktypes_internal.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/linkvalidator/classes/linktypes/class.tx_linkvalidator_linktypes_internal.php']);
}

?>
