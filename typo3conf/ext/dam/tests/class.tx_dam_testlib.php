<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
 
require_once (PATH_txdam.'lib/class.tx_dam_db.php');
require_once (PATH_txdam.'lib/class.tx_dam.php');

class tx_dam_testlib extends tx_t3unit_testcase {


	/***************************************
	 *
	 *	 Assert
	 *
	 ***************************************/


    /**
    * Asserts that a condition is a positive integer.
    *
    * @param  boolean $condition
    * @param  string  $message
    * @access public
    * @static
    */
    public static function assertUID($condition, $message = '') {
        if (!intval($condition)) {
                self::fail($message);
        }
    }
    
    /**
    * Asserts that a condition is false or zero.
    *
    * @param  boolean $condition
    * @param  string  $message
    * @access public
    * @static
    */
    public static function assertNoUID($condition, $message = '') {
        if ($condition!==false OR intval($condition)) {
                self::fail($message);
        }
    }    

	/***************************************
	 *
	 *	 Fixtures
	 *
	 ***************************************/


	/**
	 * 
	 */
	protected function addFixturePathToFilemount () {		
		$filepath = $this->getFixtureFilename();
		$filename = tx_dam::file_basename($filepath);
		$testpath = tx_dam::file_dirname($filepath);
		
		$this->tempSave['fileadminDir'] = $GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'];
		$GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'] = tx_dam::path_makeRelative($testpath);
		
		$GLOBALS['FILEMOUNTS']['__unittest'] = array(
			'name' => (basename($testpath).'/'),
			'path'=> $testpath,
			'type' => '',
		);
	}	
	
	/**
	 * 
	 */
	protected function removeFixturePathFromFilemount () {
		global $FILEMOUNTS;
		
		$GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'] = $this->tempSave['fileadminDir'];	
		
		unset ($GLOBALS['FILEMOUNTS']['__unittest']);
	}
	
	/**
	 * Returns an fixture from an example file which contains some utf-8 characters.
	 */
	protected function getFixtureContent($type='txt') {
		$content = file_get_contents($this->getFixtureFilename($type));
		return $content;
	}

	/**
	 * Returns an fixture from an example file which 
	 * 
	 * txt: contains some utf-8 characters.
	 */
	protected function getFixtureFilename($type='txt') {
		switch ($type) {
			case 'nosuffix':
			case 'nosuffix-jpg':
				$testFile = PATH_txdam.'tests/fixtures/IMG_0511';
			break;
			case 'jpg':
			case 'iptc':
				$testFile = PATH_txdam.'tests/fixtures/IMG_2971_.JPG';
			break;
			case 'txt':
			default:
				$testFile = PATH_txdam.'tests/fixtures/example-content.txt';
			break;
		}
		return $testFile;
	}


	/**
	 * 
	 */
	protected function getFixtureTempSrcPath() {
		return PATH_txdam.'tests/fixtures/temp_a/';
	}
	
	/**
	 * 
	 */
	protected function getFixtureTempDestPath() {
		return PATH_txdam.'tests/fixtures/temp_b/';
	}
	
	/**
	 * 
	 */
	protected function getFixtureTempFilename($type='txt') {
		$filename = $this->getFixtureFilename($type);
		$destFile = $this->getFixtureTempSrcPath().tx_dam::file_basename($filename);
		copy($filename, $destFile);
		return $destFile;
	}	
	
	/**
	 * 
	 */
	protected function removeFixtureTempFiles() {
		foreach (glob($this->getFixtureTempSrcPath().'*.*') as $filename) {
        	unlink($filename);
		}
		foreach (glob($this->getFixtureTempDestPath().'*.*') as $filename) {
        	unlink($filename);
		}
	}		
	
	/**
	 * 
	 */
	protected function activateIndexSetup($type='set_keyword') {
		@unlink(PATH_txdam.'tests/fixtures/.indexing.setup.xml');
		copy(PATH_txdam.'tests/fixtures/indexing.setup-'.$type, PATH_txdam.'tests/fixtures/.indexing.setup.xml');
	}
	
	
	/**
	 * 
	 */
	protected function removeIndexSetup() {
		@unlink(PATH_txdam.'tests/fixtures/.indexing.setup.xml');
	}


	/**
	 * Removes testfiles from index
	 */
	protected function removeFixturesFromIndex() {
		$path = tx_dam::path_makeRelative(PATH_txdam.'tests/fixtures/');
		
		$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike($path, 'tx_dam');		
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam', 'file_path LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr.'%', 'tx_dam'));
	}


	/**
	 * Removes a meta record from index
	 */
	protected function removeFixtureUIDFromIndex($uid) {
		if ($uid=intval($uid)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam', 'uid='.$uid);
		}
	}


	/**
	 * Returns an fixture which is a random already indexed file.
	 * @return mixed Is false when no indexed file available or: array('meta' => $row, 'filename' => $filename)
	 */
	protected function getFixtureRandomIndexedFilename() {
		$select_fields = '*'; #uid,file_name,file_path,file_hash';

		$where = array();
		$where['deleted'] = 'deleted=0';
		$where['pidList'] = 'pid IN ('.tx_dam_db::getPidList().')';
		$where['file_hash'] = 'file_hash';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
												$select_fields,
												'tx_dam',
												implode(' AND ', $where),
												'',
												'RAND()',
												50
											);

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if(is_file($filename = tx_dam::file_absolutePath ($row))) {
				return array('meta' => $row, 'filename' => $filename);
			}
		}

		return false;
	}


	/**
	 *
	 * @return mixed Is false when no indexed file available or: array('meta' => $row, 'filename' => $filename)
	 */
	protected function getFixtureMedia () {
		if ($fixture = $this->getFixtureRandomIndexedFilename()) {
			$fixture['media'] = tx_dam::media_getByUid($fixture['meta']['uid']);
		}
		return $fixture;
	}


}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_testlib.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_testlib.php']);
//}
?>