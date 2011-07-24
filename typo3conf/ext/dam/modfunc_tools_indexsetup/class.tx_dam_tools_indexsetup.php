<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Module extension (addition to function menu) 'Media>Tools>Indexing Setup'
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
 *   61: class tx_dam_tools_indexsetup extends tx_damindex_index
 *   70:     function modMenu()
 *   90:     function head()
 *  109:     function getCurrentFunc()
 *  130:     function moduleContent($header='', $description='', $lastStep=4)
 *  320:     function makeSetupfilenameForPath($path)
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');

require_once(t3lib_extMgm::extPath('dam_index').'modfunc_index/class.tx_damindex_index.php');


$LANG->includeLLFile('EXT:dam_index/modfunc_index/locallang.xml');

/**
 * Module 'Media>Tools>Indexing Setup'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_tools_indexsetup extends tx_damindex_index {

	var $cronUploadsFolder = 'uploads/tx_dam/cron/';

	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

		$menu = array();

		$menu = array(
			'tx_damindex_index_func' => array(
				'index' => $LANG->getLL('tx_dam_tools_indexsetup.func_defindex'),
				'cron_info' => $LANG->getLL('tx_dam_tools_indexsetup.func_cron_info'),
				'info' => $LANG->getLL('tx_damindex_index.func_info'),
			),
		);
		if (!t3lib_extMgm::isLoaded('dam_cron')) {
			unset($menu['tx_damindex_index_func']['cron_info']);
		}

		return $menu;

	}

	function head() {
		global $TYPO3_CONF_VARS;
		
		global  $TYPO3_CONF_VARS, $FILEMOUNTS;

		if(!is_object($GLOBALS['SOBE']->basicFF)) {
			$GLOBALS['SOBE']->basicFF = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$GLOBALS['SOBE']->basicFF->init($FILEMOUNTS,$TYPO3_CONF_VARS['BE']['fileExtensions']);
		}

		$this->pObj->guiCmdIconsDeny[] = 'popup';

		$this->cronUploadsFolder = PATH_site.$this->cronUploadsFolder;
		if(!is_dir($this->cronUploadsFolder)) {
			t3lib_div::mkdir ($this->cronUploadsFolder);
		}

		return parent::head();
	}


	function getCurrentFunc() {

		$func = parent::getCurrentFunc();

		if (t3lib_div::_GP('indexSave')) {
			$func = 'indexSave';
		}

		if ($func === 'indexSave' AND t3lib_div::_GP('setuptype') === 'cron' AND !($file = t3lib_div::_GP('filename'))) {
			$func = 'indexStart';
		}

		return $func;
	}


	/**
	 * Generates the module content
	 *
	 * @return	string		HTML content
	 */
	function moduleContent($header='', $description='', $lastStep=4)    {
		global  $BE_USER, $LANG, $BACK_PATH, $FILEMOUNTS;

		$content = '';


		switch($this->getCurrentFunc())    {
			case 'index':
			case 'index1':
// TODO make it possible to read preset if present in current folder
				$content.= parent::moduleContent($LANG->getLL('tx_dam_tools_indexsetup.start_point'), '<p style="margin:0.8em 0 1.2em 0">'.$LANG->getLL('tx_dam_tools_indexsetup.desc', true).'</p>');
			break;

			//
			// setup summary
			//

			case 'index4':

				$step=4;

				$content.= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, FALSE, $this->cmdIcons);
				$content.= $this->pObj->doc->spacer(10);

				$header = $LANG->getLL('tx_damindex_index.setup_summary');

				$stepsBar = $this->getStepsBar($step,$lastStep, '' ,'', '', $LANG->getLL('tx_dam_tools_indexsetup.finish'));
				$content.= $this->pObj->doc->section($header,$stepsBar,0,1);

				$content.= '<strong>Set Options:</strong><table border="0" cellspacing="0" cellpadding="4" width="100%">'.$this->index->getIndexingOptionsInfo().'</table>';

				$content.= $this->pObj->doc->spacer(10);

				$rec = array_merge($this->index->dataPreset,$this->index->dataPostset);

				$fixedFields = array_keys($this->index->dataPostset);
				$content.= '<strong>Meta data preset:</strong><br /><table border="0" cellpadding="4" width="100%"><tr><td bgcolor="'.$this->pObj->doc->bgColor3dim.'">'.
								$this->showPresetData($rec, $fixedFields).
								'</td></tr></table>';

				$content.= $this->pObj->doc->spacer(10);

			break;


			case 'indexStart':

				$content.= $this->pObj->doc->section('Indexing default setup','',0,1);

				$filename = '.indexing.setup.xml';

				$path = tx_dam::path_makeAbsolute($this->pObj->path);


				$content .= '</form>';
				$content .= $this->pObj->getFormTag();
				if (is_file($path.$filename) AND is_readable($path.$filename)) {

					$content.= '<br /><strong>Overwrite existing default indexer setup to this folder:</strong><br />'.htmlspecialchars($this->pObj->path).'<br />';
					$content.= '<br /><input type="submit" name="indexSave" value="Overwrite" />';
				} else {
					$content.= '<br /><strong>Save default indexer setup for this folder:</strong><br />'.htmlspecialchars($this->pObj->path).'<br />';
					$content.= '<br /><input type="submit" name="indexSave" value="Save" />';
				}
				$content.= '<input type="hidden" name="setuptype" value="folder">';


				$content.= $this->pObj->doc->spacer(10);


				if (t3lib_extMgm::isLoaded('dam_cron')) {
					$content.= $this->pObj->doc->section('CRON','',0,1);

					$path = $this->cronUploadsFolder;

					$filename = $this->makeSetupfilenameForPath($this->pObj->path);
					$content .= '</form>';
					$content .= $this->pObj->getFormTag();
					$content.= '<input type="hidden" name="setuptype" value="cron">';
					$content.= '<br /><strong>Save setup as cron indexer setup:</strong><br />'.htmlspecialchars($path).'<br />
								<input type="text" size="25" maxlength="25" name="filename" value="'.htmlspecialchars($filename).'"> .xml';
					$content.= '<br /><input type="submit" name="indexSave" value="Save" />';

					$files = t3lib_div::getFilesInDir($path,'xml',0,1);

					$out = '';
					foreach ($files as $file) {
						$out.= htmlspecialchars($file).'<br />';
					}
					if($out) {
						$content.= '<br /><br /><strong>Existing cron setups:</strong><div style="border-top:1px solid grey;border-bottom:1px solid grey;">'.$out.'</div><br />';
					}
				}


				$extraSetup = '';

				$this->index->setPath($this->pObj->path);
				$this->index->setOptionsFromRules();
				$this->index->setPID($this->pObj->defaultPid);
				$this->index->enableMetaCollect(TRUE);
				$setup = $this->index->serializeSetup($extraSetup, false);



				$content.= $this->pObj->doc->section('Set Options',t3lib_div::view_array($setup),0,1);


				$content.= '<br /><textarea style="width:100%" rows="15">'.htmlspecialchars(str_replace('{', "{\n",$this->index->serializeSetup($extraSetup))).'</textarea>';


			break;

			case 'indexSave':
				$content.= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, FALSE, $this->cmdIcons);
				$content.= $this->pObj->doc->spacer(10);
				$content.= '<div style="width:100%;text-align:right;">'.$this->pObj->btn_back().'</div>';

				if (t3lib_div::_GP('setuptype') === 'folder') {
					$path = tx_dam::path_makeAbsolute($this->pObj->path);
					$filename = $path.'.indexing.setup.xml';
				} else {
					$path = $this->cronUploadsFolder;
					$filename = t3lib_div::_GP('filename');
					$filename = $filename ? $filename : $this->makeSetupfilenameForPath($this->pObj->path);
					$filename = $path.$filename.'.xml';
				}

				$this->index->setPath($this->pObj->path);
				$this->index->setOptionsFromRules();
				$this->index->setPID($this->pObj->defaultPid);
				$this->index->enableMetaCollect(TRUE);
				$setup = $this->index->serializeSetup($extraSetup);

				if ($handle = fopen($filename, 'wb')) {
					if (fwrite($handle, $setup)) {
						 $content.= 'Setup written to file<br />'.htmlspecialchars($filename);
					} else {
						 $content.= 'Can\'t write to file '.htmlspecialchars($filename);
					}
					fclose($handle);
					t3lib_div::fixPermissions($filename);
				} else {
					 $content.= 'Can\'t open file '.htmlspecialchars($filename);
				}
			break;

			case 'cron_info':

				$content.= $this->pObj->getHeaderBar('', implode('&nbsp;',$this->cmdIcons));
				$content.= $this->pObj->doc->spacer(10);

				$files = t3lib_div::getFilesInDir($this->cronUploadsFolder,'xml',1,1);

				$out = '';
				foreach ($files as $file) {
					if($file==$filename) {
						$out.= '<strong>'.htmlspecialchars($file).'</strong><br />';
					} else {
						$out.= htmlspecialchars($file).'<br />';
					}
				}
				$filename = $filename ? $filename : $file;

				if($out) {
					$content.= '<br /><br /><strong>Existing setups:</strong><div style="border-top:1px solid grey;border-bottom:1px solid grey;">'.$out.'</div><br />';
				} else {
					$content.= '<br /><br /><strong>No setups available.</strong><br />';
				}

				if($out) {
					$cronscript = t3lib_extMgm::extPath('dam_cron').'cron/dam_indexer.php';
					$content.= '<br /><strong>Call indexer script example:</strong><br />';
					$content.= '<span style="font-family: monaco,courier,monospace;">/usr/bin/php '.htmlspecialchars($cronscript).' --setup='.htmlspecialchars($filename).'</span>';
				}
			break;

			case 'doIndexing':
			break;

			default:
				$content.= parent::moduleContent($header, $description, $lastStep);
		}
		return $content;
	}


	/**
	 * Creates a filename for a setup from pathname
	 *
	 * @param string $path path
	 * @return string filename
	 */
	function makeSetupfilenameForPath($path) {
		$filename = preg_replace('#[^a-zA-Z0-9]#','_',$path);
		$filename = preg_replace('#_$#','',$filename);
		$filename = preg_replace('#^_#','',$filename);
		return $filename;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_indexsetup/class.tx_dam_tools_indexsetup.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_indexsetup/class.tx_dam_tools_indexsetup.php']);
}

?>