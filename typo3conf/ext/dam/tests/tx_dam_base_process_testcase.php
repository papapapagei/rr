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

require_once (PATH_txdam.'tests/class.tx_dam_testlib.php');


class tx_dam_base_process_testcase extends tx_dam_testlib {

	/**
	 * tx_dam::process_createFile()
	 */
	public function test_process_createFile () {
		$this->removeFixtureTempFiles();
		$this->removeIndexSetup();
		$this->removeFixturesFromIndex();
		$this->addFixturePathToFilemount();

		$filepath = $this->getFixtureTempFilename('txt');
		unlink($filepath);
		$content = $this->getFixtureContent('txt');
		
		tx_dam::process_createFile($filepath, $content);
		
		$uid = tx_dam::file_isIndexed($filepath);
		self::assertUID ($uid, 'File index not found');
		
		$data = tx_dam::meta_getDataByUid($uid, 'abstract');
		
		self::assertEquals (substr($data['abstract'],0,100), substr($content,0,100));
		
		$this->removeFixturePathFromFilemount();
		$this->removeFixturesFromIndex();
		$this->removeIndexSetup();
		$this->removeFixtureTempFiles();
	}


	/**
	 * tx_dam::process_editFile()
	 */
	public function test_process_editFile () {
		$this->removeFixtureTempFiles();
		$this->removeIndexSetup();
		$this->removeFixturesFromIndex();
		$this->addFixturePathToFilemount();

		$filepath = $this->getFixtureTempFilename('txt');
		
		$newContent = md5(time());
		
		tx_dam::process_editFile($filepath, $newContent);
		
		$uid = tx_dam::file_isIndexed($filepath);
		self::assertUID ($uid, 'File index not found');
		
		$data = tx_dam::meta_getDataByUid($uid, 'abstract');
		
		self::assertEquals ($newContent, $newContent);
		
		$this->removeFixturePathFromFilemount();
		$this->removeFixturesFromIndex();
		$this->removeIndexSetup();
		$this->removeFixtureTempFiles();
	}


	/**
	 * tx_dam::process_renameFile()
	 */
	public function test_process_renameFile () {
		$this->removeFixtureTempFiles();
		$this->removeIndexSetup();
		$this->removeFixturesFromIndex();
		$this->addFixturePathToFilemount();

		$filepath = $this->getFixtureTempFilename();

		$uid = tx_dam::file_isIndexed($filepath);
		self::assertNoUID ($uid, 'File index found, but shouldn\'t');
		
		tx_dam::config_setValue('setup.indexing.auto', true);
		$indexed = tx_dam::index_autoProcess($filepath, $reindex=false);
		self::assertTrue (isset($indexed['isIndexed']), 'File not indexed');
	
		$uid = $indexed['fields']['uid'];
	
		$filepathNew = $filepath.'.doc';
		$error = tx_dam::process_renameFile($filepath, tx_dam::file_basename($filepathNew));
		if ($error) debug($error);
		
		self::assertTrue (is_file($filepathNew), 'File not renamed');
		
		$uid2 = tx_dam::file_isIndexed($filepathNew);
		self::assertUID ($uid2, 'File index not found');
		self::assertEquals (intval($uid), intval($uid2), 'Wrong uid: '.$uid.' - '.$uid2);
		

		$data = tx_dam::meta_getDataByUid($uid);
		self::assertEquals ($data['file_name'], tx_dam::file_basename($filepathNew), 'Wrong file name '.$indexed['fields']['file_name'].' != '.tx_dam::file_basename($filepathNew));
		
		
		$this->removeFixturePathFromFilemount();
		$this->removeFixturesFromIndex();
		$this->removeIndexSetup();
		$this->removeFixtureTempFiles();
	}

}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_process_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_process_testcase.php']);
//}
?>