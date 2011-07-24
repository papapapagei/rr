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

class tx_dam_media_testcase extends tx_dam_testlib {




	/**
	 *
	 */
	public function test_base () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		self::assertTrue ((boolean)($media->isIndexed));
		self::assertEquals ($media->isAvailable, @is_file($filename));
		$title = $media->getMeta ('title');
		self::assertTrue (!is_null($title), 'No title get from object');
	}


	/***************************************
	 *
	 *	 Get Meta data
	 *
	 ***************************************/


	/**
	 * media->getTypeAll()
	 */
	public function test_getTypeAll () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$typeFile = tx_dam::file_getType ($filename);
		$type = $media->getTypeAll ();

		self::assertEquals ($type['file_type'], $typeFile['file_type'], 'Wrong file type: '.$type['file_type'].' ('.$typeFile['file_type'].')');
		self::assertEquals ($type['file_mime_type'], $typeFile['file_mime_type'], 'Wrong mime type: '.$type['file_mime_type'].' ('.$typeFile['file_mime_type'].')');
		self::assertEquals ($type['file_mime_subtype'], $typeFile['file_mime_subtype'], 'Wrong mime sub type: '.$type['file_mime_subtype'].' ('.$typeFile['file_mime_subtype'].')');
	}

	/**
	 * media->getType()
	 */
	public function test_getType () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$typeFile = tx_dam::file_getType ($filename);
		$type = $media->getType ();

		self::assertEquals ($type, $typeFile['file_type'], 'Wrong file type: '.$type.' ('.$typeFile['file_type'].')');
	}

	/**
	 * media->getMeta()
	 */
	public function test_getMeta () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$title = $media->getMeta ('title');
		self::assertEquals ($title, $meta['title'], 'Wrong data: '.$title.' ('.$meta['title'].')');

		$media->setMeta ('title', 'XXX');
		$title = $media->getMeta ('title');
		self::assertEquals ($title, 'XXX', 'Wrong data: '.$title.' (XXX)');
	}

	/**
	 * media->getContent()
	 */
	public function test_getContent () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$title = $media->getContent ('title');
		self::assertEquals ($title, $meta['title'], 'Wrong data: '.$title.' ('.$meta['title'].')');

		$media->setMeta ('title', 'XXX');
		$title = $media->getContent ('title');
		self::assertEquals ($title, 'XXX', 'Wrong data: '.$title.' (XXX)');
	}

	/**
	 * media->getDownloadName()
	 */
	public function test_getDownloadName () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$title = $media->getDownloadName ();
		self::assertEquals ($title, $meta['file_dl_name'], 'Wrong data: '.$title.' ('.$meta['file_dl_name'].')');

		$media->setMeta ('file_dl_name', 'XXX');
		$title = $media->getDownloadName ();
		self::assertEquals ($title, 'XXX', 'Wrong data: '.$title.' (XXX)');
	}

	/**
	 * media->filepath
	 */
	public function test_getPathAbsolute () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$filepath = tx_dam::file_absolutePath ($filename);
		$path = $media->filepath;
		self::assertEquals ($path, $filepath, 'File path differs: '.$path.' ('.$filepath.')');
	}




	/***************************************
	 *
	 *	 Set Meta data
	 *
	 ***************************************/


	/**
	 * media->setMeta()
	 */
	public function test_setMeta () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$media->setMeta ('title', 'XXX');
		$title = $media->getMeta ('title');
		self::assertEquals ($title, 'XXX', 'Wrong data: '.$title.' (XXX)');
	}



}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_media_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_media_testcase.php']);
//}
?>