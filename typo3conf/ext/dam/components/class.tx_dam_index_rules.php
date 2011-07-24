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
 * Index rule plugins for the DAM.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Index-Rule
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   85: class tx_dam_index_rules
 *
 *
 *   99: class tx_dam_index_rule_recursive extends tx_dam_indexRuleBase
 *  106:     function getTitle()
 *
 *
 *  122: class tx_dam_index_rule_folderAsCat extends tx_dam_indexRuleBase
 *  129:     function getTitle()
 *  139:     function getDescription()
 *  149:     function getOptionsForm()
 *  165:     function getOptionsInfo()
 *  180:     function processMeta($meta)
 *
 *
 *  214: class tx_dam_index_rule_doReindexing extends tx_dam_indexRuleBase
 *  221:     function getTitle()
 *  231:     function getDescription()
 *  241:     function getOptionsForm()
 *  265:     function getOptionsInfo()
 *  284:     function processMeta($meta, $absFile, $idxObj)
 *
 *
 *  319: class tx_dam_index_rule_dryRun extends tx_dam_indexRuleBase
 *  326:     function getTitle()
 *  336:     function getDescription()
 *
 *
 *  350: class tx_dam_index_rule_titleFromFilename extends tx_dam_indexRuleBase
 *  352:     function getTitle()
 *  357:     function getDescription()
 *  362:     function processMeta($meta)
 *
 *
 *  379: class tx_dam_index_rule_devel extends tx_dam_indexRuleBase
 *  386:     function getTitle()
 *  396:     function getDescription()
 *  407:     function preIndexing()
 *
 * TOTAL FUNCTIONS: 19
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once(PATH_txdam.'lib/class.tx_dam_indexrulebase.php');



class tx_dam_index_rules {
	// dummy for extmgm not to throw errors
}



/**
 * Index rule plugin for the DAM
 * Recursive
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Index-Rule
 */
class tx_dam_index_rule_recursive extends tx_dam_indexRuleBase {

	/**
	 * Returns the title of the index rule
	 *
	 * @return	string	Title
	 */
	function getTitle()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:recursive.title');
	}

}


/**
 * Index rule plugin for the DAM
 * Folder as category
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Index-Rule
 */
class tx_dam_index_rule_folderAsCat extends tx_dam_indexRuleBase {

	/**
	 * Returns the title of the index rule
	 *
	 * @return	string	Title
	 */
	function getTitle()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:folderAsCat.title');
	}

	/**
	 * Returns the description of the index rule
	 *
	 * @return	string	Description
	 */
	function getDescription()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:folderAsCat.desc');
	}

	/**
	 * Returns the options form
	 *
	 * @return	string	HTML content
	 */
	function getOptionsForm()	{
		global $LANG;
		$code = array();
		$code[1][1] = 	'<input type="hidden" name="data[rules][tx_damindex_rule_folderAsCat][fuzzy]" value="0" />'.
						'<input type="checkbox" name="data[rules][tx_damindex_rule_folderAsCat][fuzzy]"'.($this->setup['fuzzy'] ? ' checked="checked"' : '').' value="1" />&nbsp;';
		$code[1][2] = $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:folderAsCat.use_fuzzy');
		$code[2][1] =	'<input type="hidden" name="data[rules][tx_damindex_rule_folderAsCat][createCategory]" value="0" />'.
						'<input type="checkbox" name="data[rules][tx_damindex_rule_folderAsCat][createCategory]"'.($this->setup['createCategory'] ? ' checked="checked"' : '').' value="1" />&nbsp;';
		$code[2][2] = $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:folderAsCat.createCategory');
		return $GLOBALS['SOBE']->doc->table($code);
	}

	/**
	 * Returns some information what options are selected.
	 * This is for user feedback.
	 *
	 * @return	string	HTML content
	 */
	function getOptionsInfo()	{
		global $LANG;
		if($this->setup['fuzzy']) {
			$out .= $this->getEnabledIcon().$LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:folderAsCat.use_fuzzy');
		}
		return $out;
	}

	/**
	 * For processing the meta data BEFORE the index is written
	 *
	 * @param	array		$meta Meta data array
	 * @param	string		$absFile Filename
	 * @return	array Processed meta data array
	 */
	function processMeta($meta)	{
		if ($this->writeDevLog) 	t3lib_div::devLog('processMeta(): setup', 'tx_dam_index_rule_folderAsCat', 0, $this->setup);

		$folderArr = explode('/', $meta['fields']['file_path']);
		if ($folderArr[count($folderArr)-1] == '') {
			array_pop($folderArr);
		}

		// skip first folder, because $meta['fields']['file_path'] starts in the typo3-folder, 
		// so the first entry would be e.g. fileadmin, which should not be a category itself
		array_shift($folderArr);		
		
		$nextParentId = 0;
		$categoryId = FALSE;

		foreach ($folderArr as $folder) {
			$res = FALSE;
			$row = FALSE;
			$parentId = $nextParentId;
						
			if ($this->setup['fuzzy']) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam_cat', 'parent_id = '.$parentId.' and title LIKE "%'.$GLOBALS['TYPO3_DB']->quoteStr($folder, 'tx_dam_cat').'%" and deleted = "0"');
			} else {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam_cat', 'parent_id = '.$parentId.' and title="'.$GLOBALS['TYPO3_DB']->quoteStr($folder, 'tx_dam_cat').'" and deleted = "0"');
			}			
			
			if ($res) {
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			}
			if (is_array($row)) {
				$categoryId = $row['uid'];
				$nextParentId = $row['uid'];
			} else {
				$categoryId = FALSE;
				
				// Create a new Category if category is not present and createCategory is selected;
				// otherwise stop indexing the current file, since there is no corresponding category available
				if ($this->setup['createCategory']) {
					$nextParentId = $this->createCategory($folder, $parentId);
				} else {
					$skipThisFile = TRUE;
					break;				
				}
			}
		}
		
		if (!$skipThisFile) {
			$meta['fields']['category'].= ',tx_dam_cat_'.$nextParentId;	
		} else {
			$skipThisFile = FALSE;
		}

		if ($this->writeDevLog) {
			$devLog = array ('folder' => $folder, 'uid' => $row['uid'], 'category' => $meta['fields']['category']);
			t3lib_div::devLog('processMeta(): category', 'tx_dam_index_rule_folderAsCat', 0, $devLog);
		}
		
		return $meta;
	}

	/**
	 * Creates a new category
	 *
	 * @param	[string]		$folder: Foldername for creating the category
	 * @param	[integer]		$parentId: Parent ID
	 * @return	[integer]		uid of created category
	 */
		function createCategory($folder, $parentId='0') {
        	$tce = t3lib_div::makeInstance('t3lib_TCEmain');
        	$NEW_id = substr(md5((time()-98*123123).$folder),0,8);
			$newdata['tx_dam_cat']['NEW'.$NEW_id] = array(
				'pid' => tx_dam_db::getPid(),
				'title' => $folder,
				'parent_id' => $parentId
			);
			$tce->start($newdata,array());
			$tce->process_datamap();

			return $tce->substNEWwithIDs['NEW'.$NEW_id];
		}

}
		

/**
 * Index rule plugin for the DAM
 * Reindexing
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Index-Rule
 */
class tx_dam_index_rule_doReindexing extends tx_dam_indexRuleBase {

	/**
	 * Returns the title of the index rule
	 *
	 * @return	string	Title
	 */
	function getTitle()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:doReindexing.title');
	}

	/**
	 * Returns the description of the index rule
	 *
	 * @return	string	Description
	 */
	function getDescription()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:doReindexing.desc');
	}

	/**
	 * Returns the options form
	 *
	 * @return	string	HTML content
	 */
	function getOptionsForm()	{
		global $LANG;

		$this->setup['mode'] = $this->setup['mode'] ? $this->setup['mode'] : 1;

		$code = array();
		$code[1][1] = '<input type="radio" name="data[rules][tx_damindex_rule_doReindexing][mode]"'.(($this->setup['mode']==1)?' checked="checked"':'').' value="1" />&nbsp;';
		$code[1][2] = $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:doReindexing.overwriteEmptyFields');

		$code[2][1] = '<input type="radio" name="data[rules][tx_damindex_rule_doReindexing][mode]"'.(($this->setup['mode']==2)?' checked="checked"':'').' value="2" />&nbsp;';
		$code[2][2] = $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:doReindexing.reindexPreserve');

		$code[3][1] = '<input type="radio" name="data[rules][tx_damindex_rule_doReindexing][mode]"'.(($this->setup['mode']==99)?' checked="checked"':'').' value="99" />&nbsp;';
		$code[3][2] = $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:doReindexing.reindexFull');

		return $GLOBALS['SOBE']->doc->table($code);
	}

	/**
	 * Returns some information what options are selected.
	 * This is for user feedback.
	 *
	 * @return	string	HTML content
	 */
	function getOptionsInfo()	{
		global $LANG;
		if ($this->setup['mode']==1) {
			$out .= $this->getEnabledIcon().$LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:doReindexing.use_overwriteEmptyFields');
		} elseif ($this->setup['mode']==2) {
			$out .= $this->getEnabledIcon().$LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:doReindexing.use_reindexPreserve');
		} elseif ($this->setup['mode']==99) {
			$out .= $this->getEnabledIcon().$LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:doReindexing.use_reindexFull');
		}
		return $out;
	}

	/**
	 * For processing the meta data BEFORE the index is written
	 *
	 * @param	array		$meta Meta data array
	 * @param	string		$absFile Filename
	 * @return	array Processed meta data array
	 */
	function processMeta($meta, $absFile, $idxObj)	{
		$mode = intval($this->setup['mode']);
		if ($mode AND $mode < 99)	{
			if (is_array($meta['row']))	{

				if ($this->setup['mode']==1) {
						// overwrite empty fields
					$meta['fields'] = t3lib_div::array_merge_recursive_overrule($meta['fields'],$meta['row'], FALSE, FALSE);

				} elseif ($this->setup['mode']==2) {
						// preserve old data if new is empty
					$meta['fields'] = t3lib_div::array_merge_recursive_overrule($meta['row'],$meta['fields'], FALSE, FALSE);
				}

					// no matter what the mode is, the new file info (esp. mtime) will be used
				$meta['fields'] = t3lib_div::array_merge_recursive_overrule($meta['fields'],$meta['file'], FALSE, FALSE);
			}
		}

		return $meta;
	}

}




/**
 * Index rule plugin for the DAM
 * Dry run
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Index-Rule
 */
class tx_dam_index_rule_dryRun extends tx_dam_indexRuleBase {

	/**
	 * Returns the title of the index rule
	 *
	 * @return	string	Title
	 */
	function getTitle()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:dryRun.title');
	}

	/**
	 * Returns the description of the index rule
	 *
	 * @return	string	Description
	 */
	function getDescription()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:dryRun.desc');
	}
}

/**
 * Index rule plugin for the DAM
 * Demo
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Index-Rule
 */
class tx_dam_index_rule_titleFromFilename extends tx_dam_indexRuleBase {

	function getTitle()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:titleFromFilename.title');
	}

	function getDescription()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:titleFromFilename.desc');
	}

	function processMeta($meta)	{

		$meta['fields']['title'] = tx_dam_indexing::makeTitleFromFilename ($meta['fields']['file_name']);

		return $meta;
	}

}

/**
 * Index rule plugin for the DAM
 * Devel
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Index-Rule
 */
class tx_dam_index_rule_devel extends tx_dam_indexRuleBase {

	/**
	 * Returns the title of the index rule
	 *
	 * @return	string	Title
	 */
	function getTitle()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:devel.title');
	}

	/**
	 * Returns the description of the index rule
	 *
	 * @return	string	Description
	 */
	function getDescription()	{
		global $LANG;
		return $LANG->sL('LLL:EXT:dam/components/locallang_indexrules.xml:devel.desc');
	}

	/**
	 * Will be called before the indexing.
	 * Can be used to initialize things
	 *
	 * @return	void
	 */
	function preIndexing()	{
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam', '');
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_dam_metypes_avail', '');
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_index_rules.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_index_rules.php']);
}

?>