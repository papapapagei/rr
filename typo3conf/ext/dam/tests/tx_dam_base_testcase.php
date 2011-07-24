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


class tx_dam_base_testcase extends tx_dam_testlib {




	/***************************************
	 *
	 *	 file_
	 *
	 ***************************************/






	/**
	 * tx_dam::file_compileInfo()
	 */
	public function test_file_compileInfo () {

		$filepath = $this->getFixtureFilename();
		$filename = tx_dam::file_basename($filepath);

		$ignoreExistence=true;
		for ($index = 0; $index < 2; $index++) {
			$fileinfo = tx_dam::file_compileInfo ($filepath, $ignoreExistence=false);

			self::assertEquals ($fileinfo['file_name'], $filename, 'Wrong file name: '.$fileinfo['file_name'].' ('.$filename.')');
			$testpath = tx_dam::path_makeAbsolute($fileinfo['file_path']).$fileinfo['file_name'];
			self::assertEquals ($testpath, $filepath, 'File path differs: '.$testpath.' ('.$filepath.')');
			$testpath = $fileinfo['file_path_absolute'].$fileinfo['file_name'];
			self::assertEquals ($testpath, $filepath, 'File path differs: '.$testpath.' ('.$filepath.')');

			$ignoreExistence=false;
		}

		self::assertTrue ($fileinfo['file_mtime']>0, 'No file_mtime');
		self::assertTrue ($fileinfo['file_ctime']>0, 'No file_ctime');
		self::assertTrue ($fileinfo['file_inode']>0, 'No file_inode');
		self::assertEquals ($fileinfo['file_size'], 2108, 'Wrong file size: '.$fileinfo['file_size'].' (2108)');
	}


	/**
	 * tx_dam::file_getType()
	 */
	public function test_file_getType () {

		$filepath = $this->getFixtureFilename('txt');
		$type = tx_dam::file_getType ($filepath);
		self::assertEquals ($type['file_type'], 'txt', 'Wrong file type: '.$type['file_type'].' (txt)');
		self::assertEquals ($type['file_mime_type'], 'text', 'Wrong mime type: '.$type['file_mime_type'].' (text)');
		self::assertEquals ($type['file_mime_subtype'], 'plain', 'Wrong mime sub type: '.$type['file_mime_subtype'].' (plain)');

		$filepath = $this->getFixtureFilename('jpg');
		$type = tx_dam::file_getType ($filepath);
		self::assertEquals ($type['file_type'], 'jpg', 'Wrong file type: '.$type['file_type'].' (jpg)');
		self::assertEquals ($type['file_mime_type'], 'image', 'Wrong mime type: '.$type['file_mime_type'].' (image)');
		self::assertEquals ($type['file_mime_subtype'], 'jpeg', 'Wrong mime sub type: '.$type['file_mime_subtype'].' (jpeg)');

		$filepath = $this->getFixtureFilename('nosuffix-jpg');
		$type = tx_dam::file_getType ($filepath);
		self::assertEquals ($type['file_type'], 'jpg', 'Wrong file type: '.$type['file_type'].' (jpg)');
		self::assertEquals ($type['file_mime_type'], 'image', 'Wrong mime type: '.$type['file_mime_type'].' (image)');
		self::assertEquals ($type['file_mime_subtype'], 'jpeg', 'Wrong mime sub type: '.$type['file_mime_subtype'].' (jpeg)');
	}

	/**
	 * tx_dam::file_getType()
	 */
	public function test_file_getType_indexed () {

		$fixture = $this->getFixtureRandomIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$type = tx_dam::file_getType ($filename);
		self::assertEquals ($type['file_type'], $meta['file_type'], 'Wrong file type: '.$type['file_type'].' ('.$meta['file_type'].')');
		self::assertEquals ($type['file_mime_type'], $meta['file_mime_type'], 'Wrong mime type: '.$type['file_mime_type'].' ('.$meta['file_mime_type'].')');
		self::assertEquals ($type['file_mime_subtype'], $meta['file_mime_subtype'], 'Wrong mime sub type: '.$type['file_mime_subtype'].' ('.$meta['file_mime_subtype'].')');
	}

	/**
	 * tx_dam::file_calcHash()
	 */
	public function test_file_calcHash() {

		$filename = $this->getFixtureFilename();

		$hash = array();

		$hash['file_calcHash'] = tx_dam::file_calcHash($filename);
		$compareHash = '4e231415019b6593f0266b99b7704bc2';


		if (function_exists('md5_file')) {
			$hash['md5_file'] = @md5_file($filename);
		}

		$cmd = t3lib_exec::getCommand('md5sum');
		$output = array();
		$retval = '';
		exec($cmd.' -b '.escapeshellcmd($filename), $output, $retval);
		$output = explode(' ',$output[0]);
		$match = array();
		if (preg_match('#[0-9a-f]{32}#', $output[0], $match)) {
			$hash['md5sum'] = $match[0];
		}

		$file_string = t3lib_div::getUrl($filename);
		$hash['md5'] = md5($file_string);


		foreach ($hash as $key => $value)  {
			self::assertEquals ($compareHash, $value, 'Wrong hash: '.$value.' ('.$key.')');
		}

	}


	/**
	 * tx_dam::file_absolutePath()
	 */
	public function test_file_absolutePath () {
		$GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'] = array();

		$filepath = $this->getFixtureFilename();
		$filename = tx_dam::file_basename($filepath);
		$testpath = tx_dam::file_dirname($filepath);


		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::file_absolutePath ($path.$filename);

		self::assertEquals ($path, $filepath, 'File path differs: '.$path.' ('.$filepath.')');


		$filepath = $this->getFixtureFilename();
		$fileinfo = tx_dam::file_compileInfo ($filepath, $ignoreExistence=false);

		$path = tx_dam::file_absolutePath ($fileinfo);

		self::assertEquals ($path, $filepath, 'File path differs: '.$path.' ('.$filepath.')');
	}

	/**
	 * tx_dam::file_relativeSitePath()
	 */
	public function test_file_relativeSitePath () {
		$GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'] = array();

		$filepath = $this->getFixtureFilename();
		$filename = tx_dam::file_basename($filepath);
		$testpath = tx_dam::file_dirname($filepath);

		$relPath = t3lib_extmgm::siteRelPath('dam').'tests/fixtures/'.$filename;

		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::file_relativeSitePath ($path.$filename);

		self::assertEquals ($path, $relPath, 'File path differs: '.$path.' ('.$relPath.')');


		$filepath = $this->getFixtureFilename();
		$fileinfo = tx_dam::file_compileInfo ($filepath, $ignoreExistence=false);

		$path = tx_dam::file_relativeSitePath ($fileinfo);

		self::assertEquals ($path, $relPath, 'File path differs: '.$path.' ('.$relPath.')');
	}

	/**
	 * tx_dam::file_makeCleanName()
	 */
	public function test_file_makeCleanName () {
		$GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'] = array();

		$tempSave = $GLOBALS['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'];
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'] = 25;
		
		$filename = 'abcdefghijklmnopqrstuvwxyz.abc';
		$filename = tx_dam::file_makeCleanName ($filename, true);
		self::assertEquals ($filename, 'abcdefghijklmnopqrstu.abc', 'File name differs: '.$filename.' (abcdefghijklmnopqrstu.abc)');
		
		$filename = 'abcdefghijKLMNOPQRSTUVWXYZ.abc';
		$filename = tx_dam::file_makeCleanName ($filename, true);
		self::assertEquals ($filename, 'abcdefghijKLMNOPQRSTU.abc', 'File name differs: '.$filename.' (abcdefghijKLMNOPQRSTU.abc)');
		
		$filename = 'a0-_.,;:#+*=()/!§$%&XYZ.abc';
		$filename = tx_dam::file_makeCleanName ($filename, true);
		self::assertEquals ($filename, 'a0-_.,;_#+_=()_!§$%&X.abc', 'File name differs: '.$filename.' (a0-_.,;_#+_=()_!$%&X.abc)');
		
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['maxFileNameLength'] = $tempSave;
	}



	/**
	 * tx_dam::file_dirname()
	 */
	public function test_file_dirname () {
		$path = 'hjfds fhsdjka/ fhjkasdhf das/djjghfgh/צהצüצה/hjfds/XXX/';
		$resultname = tx_dam::file_dirname ($path);
		self::assertEquals ($resultname, $path, 'Path not equal: '.$resultname.' - '.$path);
		
		$path = 'hjfds fhsdjka/ fhjkasdhf das/djjghfgh/צהצüצה/hjfds/XXX/';
		$filename = 'abcdefghijklmnopqrstuvwxyz.abc';
		$resultname = tx_dam::file_dirname ($path.$filename);
		self::assertEquals ($resultname, $path, 'Path not equal: '.$resultname.' - '.$path);
		
		$path = '/a/';
		$filename = 'abcdef ghijklmno pqrstuvwxyz.abc';
		$resultname = tx_dam::file_dirname ($path.$filename);
		self::assertEquals ($resultname, $path, 'Path not equal: '.$resultname.' - '.$path);
		
		$path = 'a/';
		$filename = 'צהü';
		$resultname = tx_dam::file_dirname ($path.$filename);
		self::assertEquals ($resultname, $path, 'Path not equal: '.$resultname.' - '.$path);
		
		$path = '/';
		$filename = 'צהü ';
		$resultname = tx_dam::file_dirname ($path.$filename);
		self::assertEquals ($resultname, $path, 'Path not equal: '.$resultname.' - '.$path);
		
		$path = '/';
		$filename = '-+#.123צהü xxx';
		$resultname = tx_dam::file_dirname ($path.$filename);
		self::assertEquals ($resultname, $path, 'Path not equal: '.$resultname.' - '.$path);
	}

	/**
	 * tx_dam::file_basename()
	 */
	public function test_file_basename () {
		$path = 'hjfds fhsdjka/ fhjkasdhf das/djjghfgh/צהצüצה/hjfds/XXX/';
		$filename = 'abcdefghijklmnopqrstuvwxyz.abc';
		$resultname = tx_dam::file_basename ($path.$filename);
		self::assertEquals ($resultname, $filename, 'File name not equal: '.$resultname.' - '.$filename);
		
		$filename = 'abcdef ghijklmno pqrstuvwxyz.abc';
		$resultname = tx_dam::file_basename ($path.$filename);
		self::assertEquals ($resultname, $filename, 'File name not equal: '.$resultname.' - '.$filename);
		
		$filename = 'צהü';
		$resultname = tx_dam::file_basename ($path.$filename);
		self::assertEquals ($resultname, $filename, 'File name not equal: '.$resultname.' - '.$filename);
		
		$filename = 'צהü ';
		$resultname = tx_dam::file_basename ($path.$filename);
		self::assertEquals ($resultname, $filename, 'File name not equal: '.$resultname.' - '.$filename);
		
		$filename = '-+#.123צהü xxx';
		$resultname = tx_dam::file_basename ($path.$filename);
		self::assertEquals ($resultname, $filename, 'File name not equal: '.$resultname.' - '.$filename);
	}
	
	

	/***************************************
	 *
	 *	 path_
	 *
	 ***************************************/



	/**
	 * tx_dam::path_basename()
	 */
	public function test_path_basename () {
		$path = 'hjfds fhsdjka/ fhjkasdhf das/djjghfgh/צהצüצה/hjfds/XXX/';
		$foldername = 'abcdefghijklmnopqrstuvwxyz.abc';
		$resultname = tx_dam::path_basename ($path.$foldername);
		self::assertEquals ($resultname, $foldername, 'Folder name not equal: '.$resultname.' - '.$foldername);
		
		$foldername = 'abcdef ghijklmno pqrstuvwxyz.abc';
		$resultname = tx_dam::path_basename ($path.$foldername.'/');
		self::assertEquals ($resultname, $foldername, 'Folder name not equal: '.$resultname.' - '.$foldername);
		
		$foldername = 'צהü';
		$resultname = tx_dam::path_basename ($path.$foldername.'/');
		self::assertEquals ($resultname, $foldername, 'Folder name not equal: '.$resultname.' - '.$foldername);
		
		$foldername = 'צהü ';
		$resultname = tx_dam::path_basename ($path.$foldername.'/');
		self::assertEquals ($resultname, $foldername, 'Folder name not equal: '.$resultname.' - '.$foldername);
		
		$foldername = '-+#.123צהü xxx';
		$resultname = tx_dam::path_basename ($path.$foldername);
		self::assertEquals ($resultname, $foldername, 'Folder name not equal: '.$resultname.' - '.$foldername);
	}
	

	/**
	 * tx_dam::path_makeXXX()
	 */
	public function test_path_makeXXX () {
		$GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'] = array();

		$filepath = $this->getFixtureFilename();
		$filename = tx_dam::file_basename($filepath);
		$testpath = tx_dam::file_dirname($filepath);

		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeAbsolute ($path);
		self::assertEquals ($path, $testpath, 'File path differs: '.$path.' ('.$testpath.')');

		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeAbsolute ($path);
		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeAbsolute ($path);
		self::assertEquals ($path, $testpath, 'Path differs: '.$path.' ('.$testpath.')');

		$testpath = '/aaa/../bbb/./ccc//ddd';
		$testpathClean = '/bbb/ccc/ddd/';
		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeAbsolute ($path);
		self::assertEquals ($path, $testpathClean, 'Path differs: '.$path.' ('.$testpathClean.')');

		$testpath = PATH_site.'/aaa/../bbb/./ccc//ddd';
		$testpathClean = 'bbb/ccc/ddd/';
		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		self::assertEquals ($path, $testpathClean, 'Path differs: '.$path.' ('.$testpathClean.')');
	}


	/**
	 * tx_dam::path_compileInfo()
	 */
	public function test_path_compileInfo () {
		$GLOBALS['T3_VAR']['ext']['dam']['pathInfoCache'] = array();

		$filepath = $this->getFixtureFilename();
		$filename = tx_dam::file_basename($filepath);
		$testpath = tx_dam::file_dirname($filepath);

		$pathInfo = tx_dam::path_compileInfo($testpath);
		self::assertTrue (is_array($pathInfo), 'Path not found: '.$testpath);
		self::assertTrue ((boolean)$pathInfo['dir_readable'], 'Path not readable: '.$testpath);
		self::assertFALSE ((boolean)$pathInfo['mount_id'], 'Impossible mount found: '.$pathInfo['mount_path'].' ('.$testpath.')');

		$pathInfo = tx_dam::path_compileInfo(PATH_site.'fileadmin/');
		self::assertTrue (is_array($pathInfo), 'Path not found: '.$testpath);
		self::assertTrue ((boolean)$pathInfo['dir_readable'], 'Path not readable: '.$testpath);
		self::assertTrue ((boolean)$pathInfo['mount_id'], 'No mount found: '.$pathInfo['mount_path'].' ('.$testpath.')');
	}


}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_testcase.php']);
//}
?>