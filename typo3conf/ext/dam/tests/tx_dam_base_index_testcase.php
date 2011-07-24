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


class tx_dam_base_index_testcase extends tx_dam_testlib {

	/**
	 * tx_dam::file_isIndexed()
	 */
	public function test_file_isIndexed () {

		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$uid = tx_dam::file_isIndexed($filename);
		self::assertEquals ($meta['uid'], $uid, 'File index not found: '.$filename);
		$uid = tx_dam::file_isIndexed($meta);
		self::assertEquals ($meta['uid'], $uid, 'File index not found: '.$filename);

		$this->removeFixturesFromIndex();
		$filename = $this->getFixtureFilename();
		$uid = tx_dam::file_isIndexed($filename);
		self::assertFalse ($uid, 'File index found, but shouldn\'t');
	}
	
	
	/**
	 * tx_dam::index_check()
	 */
	public function test_index_check () {
		$this->removeFixturesFromIndex();
		$this->addFixturePathToFilemount();

		$filepath = $this->getFixtureFilename('iptc');
		$hash = tx_dam::file_calcHash($filepath);
		
		$status = tx_dam::index_check($filepath);
		self::assertEquals ($status['__status'], TXDAM_file_unknown, 'File: '.$filepath);
		
		$status = tx_dam::index_check($filepath, $hash);
		self::assertEquals ($status['__status'], TXDAM_file_unknown, 'File: '.$filepath);
		
		$status = tx_dam::index_check('', $hash);
		self::assertEquals ($status['__status'], TXDAM_file_unknown, 'File: '.$filepath);
	
// todo: check index_check for indexed file

		$this->removeFixturePathFromFilemount();
	}


	/**
	 * tx_dam::index_reconnect()
	 */
	public function test_index_reconnect() {
		$this->removeFixtureTempFiles();
		$this->removeIndexSetup();
		$this->removeFixturesFromIndex();
		$this->addFixturePathToFilemount();

		$filepath = $this->getFixtureTempFilename();

		$uid = tx_dam::file_isIndexed($filepath);
		self::assertEquals ($uid, false, 'File index found, but shouldn\'t');
		
		tx_dam::config_setValue('setup.indexing.auto', true);
		$indexed = tx_dam::index_autoProcess($filepath, $reindex=false);
		self::assertTrue (isset($indexed['isIndexed']), 'File not indexed');
	
		$uid = $indexed['fields']['uid'];
	
	
		$filepathNew = $filepath.'2';		
		rename($filepath, $filepathNew);

		$status = tx_dam::meta_updateStatus ($indexed['fields']);
		self::assertEquals ($status, TXDAM_status_file_missing, 'Wrong status: '.$status);

		$indexed2 = tx_dam::index_reconnect($filepathNew);

		self::assertEquals (intval($indexed2['meta']['uid']), intval($uid), 'Wrong uid: '.$uid);
		self::assertEquals ($indexed2['__status'], TXDAM_file_changed, 'Wrong status: '.$status);
		
		
		$this->removeFixturePathFromFilemount();
		$this->removeFixturesFromIndex();
		$this->removeIndexSetup();
		$this->removeFixtureTempFiles();
	}




	/**
	 * tx_dam::index_autoProcess()
	 */
	public function test_index_autoProcess() {
		$this->activateIndexSetup('set_keyword');
		$this->removeFixturesFromIndex();
		$this->addFixturePathToFilemount();

		$filepath = $this->getFixtureFilename('iptc');

		$uid = tx_dam::file_isIndexed($filepath);
		self::assertFalse ($uid, 'File index found, but shouldn\'t');		
		
		tx_dam::config_setValue('setup.indexing.auto', false);
		$indexed = tx_dam::index_autoProcess($filepath, $reindex=false);
		self::assertFalse (isset($indexed['isIndexed']), 'File IS indexed but shouldn\'t');	
		
		tx_dam::config_setValue('setup.indexing.auto', true);
		$indexed = tx_dam::index_autoProcess($filepath, $reindex=false);
		self::assertTrue (isset($indexed['isIndexed']), 'File not indexed');
		
		$meta = tx_dam::meta_getDataByUid ($indexed['fields']['uid'], '*');
			// keyword is set in auto indexing setup: dam/tests/fixtures/.indexing.setup.xml
		self::assertEquals ($meta['keywords'], 'TEST', 'Wrong keyword: '.$meta['keywords']);
		$date = date('d.m.Y', $meta['date_cr']);
		self::assertEquals ($date, '04.07.2006', 'Wrong date: '.$date);
		self::assertEquals ($meta['title'], 'Hummelflug', 'Wrong title: '.$meta['title']);
		self::assertEquals ($meta['file_hash'], '184e454250f6f606a1dba14b5c7b38c5', 'Wrong file_hash: '.$meta['file_hash']);
		self::assertEquals (intval($meta['media_type']), TXDAM_mtype_image, 'Wrong media_type: '.$meta['media_type']);
		self::assertEquals ($meta['file_type'], 'jpg', 'Wrong file_type: '.$meta['file_type']);
		self::assertEquals ($meta['file_mime_type'], 'image', 'Wrong file_mime_type: '.$meta['file_mime_type']);
		self::assertEquals ($meta['file_mime_subtype'], 'jpeg', 'Wrong file_mime_subtype: '.$meta['file_mime_subtype']);
		self::assertTrue ((boolean)$meta['hpixels'], 'Missing value');
		self::assertTrue ((boolean)$meta['vpixels'], 'Missing value');
		self::assertTrue ((boolean)$meta['hres'], 'Missing value');
		self::assertTrue ((boolean)$meta['vres'], 'Missing value');
		self::assertTrue ((boolean)$meta['width'], 'Missing value');
		self::assertTrue ((boolean)$meta['height'], 'Missing value');
		self::assertTrue ((boolean)$meta['height_unit'], 'Missing value');

		$this->removeFixturePathFromFilemount();
		$this->removeFixturesFromIndex();
		$this->removeIndexSetup();
	}


	/**
	 * tx_dam::index_process()
	 */
	public function test_index_process () {
		$this->removeIndexSetup();
		$this->removeFixturesFromIndex();
		$this->addFixturePathToFilemount();

		$filepath = $this->getFixtureFilename('iptc');

		$uid = tx_dam::file_isIndexed($filepath);
		self::assertFalse ($uid, 'File index found, but shouldn\'t');		
		
		$indexed = tx_dam::index_process($filepath);
		$indexed = current($indexed);
		self::assertTrue (isset($indexed['uid']), 'File not indexed');	
		
		$meta = tx_dam::meta_getDataByUid ($indexed['uid'], '*');
		$date = date('d.m.Y', $meta['date_cr']);
		self::assertEquals ($date, '04.07.2006', 'Wrong date: '.$date);
		self::assertEquals ($meta['title'], 'Hummelflug', 'Wrong title: '.$meta['title']);
		self::assertEquals ($meta['file_hash'], '184e454250f6f606a1dba14b5c7b38c5', 'Wrong file_hash: '.$meta['file_hash']);
		self::assertEquals (intval($meta['media_type']), TXDAM_mtype_image, 'Wrong media_type: '.$meta['media_type']);
		self::assertEquals ($meta['file_type'], 'jpg', 'Wrong file_type: '.$meta['file_type']);
		self::assertEquals ($meta['file_mime_type'], 'image', 'Wrong file_mime_type: '.$meta['file_mime_type']);
		self::assertEquals ($meta['file_mime_subtype'], 'jpeg', 'Wrong file_mime_subtype: '.$meta['file_mime_subtype']);
		self::assertTrue ((boolean)$meta['hpixels'], 'Missing value');
		self::assertTrue ((boolean)$meta['vpixels'], 'Missing value');
		self::assertTrue ((boolean)$meta['hres'], 'Missing value');
		self::assertTrue ((boolean)$meta['vres'], 'Missing value');
		self::assertTrue ((boolean)$meta['width'], 'Missing value');
		self::assertTrue ((boolean)$meta['height'], 'Missing value');
		self::assertTrue ((boolean)$meta['height_unit'], 'Missing value');
				
		$this->removeFixturePathFromFilemount();
		$this->removeFixturesFromIndex();

	}


	function dumpMetaArray ($meta) {
		$content = '
			$meta = array(';
		$start = false;
		foreach ($meta as $field => $value) {
			if ($field==='title') $start = true;
			if (!$start) continue;
			if (substr($field,0,6)==='t3ver_') continue;
			if (substr($field,0,5)==='l18n_') continue;
			if (substr($field,0,4)==='sys_') continue;
			if (substr($field,0,3)==='tx_') continue;
			if ($field==='meta') continue;
			
			$content .= '
				\''.$field.'\' => \''.addslashes($value).'\'';
			
		}
		$content .= '
			);';
		return $content;
	}

}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_index_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_index_testcase.php']);
//}
?>