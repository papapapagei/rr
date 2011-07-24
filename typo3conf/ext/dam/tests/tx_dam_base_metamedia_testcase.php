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


class tx_dam_base_metamedia_testcase extends tx_dam_testlib {



	
	/***************************************
	 *
	 *	 meta_
	 *
	 ***************************************/


	/**
	 * tx_dam::meta_getDataForFile()
	 */
	public function test_meta_getDataForFile () {
		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$data = tx_dam::meta_getDataForFile($filename);
		self::assertEquals ($data['uid'], $meta['uid'], 'Wrong index for '.$filename);
	}

	/**
	 * tx_dam::meta_getDataByUid()
	 */
	public function test_meta_getDataByUid () {
		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$data = tx_dam::meta_getDataByUid($meta['uid']);
		self::assertEquals ($data['file_name'], $meta['file_name'], 'Wrong index for '.$filename);
	}

	/**
	 * tx_dam::meta_getDataByHash()
	 */
	public function test_meta_getDataByHash () {
		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$data = tx_dam::meta_getDataByHash($meta['file_hash']);
		self::assertEquals ($data[$meta['uid']]['uid'], $meta['uid'], 'Wrong index for '.$filename);
	}

	/**
	 * tx_dam::meta_findDataForFile()
	 */
	public function test_meta_findDataForFile () {
		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$data = tx_dam::meta_findDataForFile($filename);
		self::assertEquals ($data[$meta['uid']]['uid'], $meta['uid'], 'Wrong index for '.$filename);
// todo: test hash
	}


	/***************************************
	 *
	 *	 media_
	 *
	 ***************************************/

	/**
	 * tx_dam::media_getForFile()
	 */
	public function test_media_getForFile () {
		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$media = tx_dam::media_getForFile($meta);

		self::assertTrue (is_object($media), 'Object not created for '.$filename);
		self::assertTrue ($media->isIndexed);
	}

	/**
	 * tx_dam::media_getByUid()
	 */
	public function test_media_getByUid () {
		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$media = tx_dam::media_getByUid($meta['uid']);

		self::assertTrue (is_object($media), 'Object not created for '.$filename);
		self::assertTrue ($media->isIndexed);
	}

	/**
	 * tx_dam::media_getByHash()
	 */
	public function test_media_getByHash () {
		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$mediaList = tx_dam::media_getByHash($meta['file_hash']);

		self::assertTrue (is_array($mediaList), 'Is not Object array for '.$filename);
		$media = current($mediaList);
		self::assertTrue (is_object($media), 'Object not created for '.$filename);
		self::assertTrue ($media->isIndexed);
	}


}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_metamedia_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_metamedia_testcase.php']);
//}
?>