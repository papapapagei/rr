<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2006 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Manage storing and restoring of $GLOBALS['SOBE']->MOD_SETTINGS settings.
 * Provides a presets box for BE modules.
 *
 * inspired by t3lib_fullsearch
 *
 * $Id: class.tx_dam_selstorage.php,v 1.1 2005/09/27 21:39:35 cvsrene Exp $
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  121: class tx_dam_selStorage
 *
 *              SECTION: Init / setup
 *  166:     function init()
 *
 *              SECTION: Process storage array
 *  190:     function initStorage($uidList='', $pidList='')
 *  226:     function cleanupStorageArray($storedSettings)
 *  248:     function compileEntry($data)
 *  272:     function processStoreControl()
 *
 *              SECTION: GUI
 *  354:     function getStoreControl($showElements='load,remove,save', $useOwnForm=TRUE)
 *
 *              SECTION: Misc
 *  456:     function processEntry($storageArr)
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




/**
 * usage inside of scbase class
 *
 * ....
 *
 * $this->MOD_MENU = array(
 * 	'function' => array(
 * 		'xxx ...
 * 	),
 * 	'tx_dam_select_storedSettings' => '',
 *
 * ....
 *
 * function main()	{
 * 	// reStore settings
 * $store = t3lib_div::makeInstance('tx_dam_selStorage');
 * $store->init('tx_dam_select');
 * $store->setStoreList('tx_dam_select');
 * $store->processStoreControl();
 *
 * 	// show control panel
 * $this->content.= $this->doc->section('Settings',$store->getStoreControl(),0,1);
 *
 *
 *
 * Format of saved settings
 *
 *	$GLOBALS['SOBE']->MOD_SETTINGS[$this->prefix.'_storedSettings'] = serialize(
 *		array(
 *			'any id' => array(
 *					'title' => 'title for saved settings',
 *					'desc' => 'descritpion text, not mandatory',
 *					'data' => array(),	// data from MOD_SETTINGS
 *					'user' => NULL, // can be used for extra data used by the application to identify this entry
 *					'tstamp' => 12345, // time()
 *				),
 *			'another id' => ...
 *
 *			) );
 *
 */



/**
 * Manage storing and restoring of $GLOBALS['SOBE']->MOD_SETTINGS settings.
 * Provides a presets box for BE modules.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 * @todo localization
 */
class tx_dam_selStorage {


	/**
	 * The stored settings array
	 */
	var $storedSettings = array();

	/**
	 * Message from the last storage command
	 */
	var $msg = '';


	/**
	 * Name of the form. Needed for JS
	 */
	var $formName = 'selStoreControl';


	/**
	 * Name of the storage table
	 */
	var $table = 'tx_dam_selection';



	var $writeDevLog = 0; 				// write messages into the devlog?




	/********************************
	 *
	 * Init / setup
	 *
	 ********************************/



	/**
	 * Initializes the object
	 *
	 * @return	void
	 */
	function init()	{
			// enable dev logging if set
		if ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.tx_dam_selStorage.php']['writeDevLog']) $this->writeDevLog = TRUE;
		if (TYPO3_DLOG) $this->writeDevLog = TRUE;
	}






	/********************************
	 *
	 * Process storage array
	 *
	 ********************************/



	/**
	 * Get the stored settings from MOD_SETTINGS and set them in $this->storedSettings
	 *
	 * @return	void
	 */
	function initStorage($uidList='', $pidList='')	{

		$pidList = $pidList ? $GLOBALS['TYPO3_DB']->cleanIntList($pidList) : '';
		$pidList = $pidList ? $pidList : tx_dam_db::getPid();
		$pidList = $this->table.'.pid IN ('.$pidList.')';

		$uidList = $uidList ? $GLOBALS['TYPO3_DB']->cleanIntList($uidList) : '';
		$uidList = $uidList ? ' AND '.$this->table.'.uid IN ('.$uidList.')' :  '';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$this->table,
					$pidList.$uidList.' AND '.tx_dam_db::deleteClause($this->table),
					'',
					$this->table.'.title'
				);

		$this->storedSettings = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			#$row['definition'] = t3lib_div::xml2array($row['definition']);
			$this->storedSettings[$row['uid']] = $row;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		$this->storedSettings = $this->cleanupStorageArray($this->storedSettings);
	}



	/**
	 * Remove corrupted data entries from the stored settings array
	 *
	 * @param	array		$storedSettings
	 * @return	array		$storedSettings
	 */
	function cleanupStorageArray($storedSettings)	{

		$storedSettings = is_array($storedSettings) ? $storedSettings : array();

			// clean up the array
		foreach($storedSettings as $id => $sdArr)	{
			if (!is_array($sdArr)) unset($storedSettings[$id]);
			if (!$sdArr['definition']) unset($storedSettings[$id]);
			if (!trim($sdArr['title']))	$storedSettings[$id]['title'] = '[no title]';
		}

		return $storedSettings;
	}


	/**
	 * Creates an entry for the stored settings array
	 * Collects data from MOD_SETTINGS selected by the storeList
	 *
	 * @param	array		Should work with data from _GP('selStoreControl'). This is ['title']: Title for the entry. ['desc']: A description text. Currently not used by this class
	 * @return	array		$storageArr: entry for the stored settings array
	 */
	function compileEntry($data)	{
		global $BE_USER;

		$storageArr = array(
						'title' => $data['title'],
						'definition' => $GLOBALS['SOBE']->selection->sl->serialize(),
						'tstamp' => time(),
						'crdate' => time(),
						'pid' => $GLOBALS['SOBE']->defaultPid,
						'cruser_id' => $BE_USER->user['uid'],
					);
		$storageArr = $this->processEntry($storageArr);

		return $storageArr;
	}



	/**
	 * Processing of the storage command LOAD, SAVE, REMOVE
	 *
	 * @param	string		Name of the module to store the settings for. Default: $GLOBALS['SOBE']->MCONF['name'] (current module)
	 * @return	string		Storage message. Also set in $this->msg
	 */
	function processStoreControl()	{

		$this->initStorage();


		$storeControl = t3lib_div::_GP('selStoreControl');
		$storeIndex = $storeControl['STORE'];

		if ($this->writeDevLog) {
			t3lib_div::devLog('Store command: '.(is_array($storeControl) ? t3lib_div::arrayToLogString($storeControl) : $storeControl), 'tx_dam_selStorage', 0);
		}

		$msg = '';
		$saveSettings = FALSE;
		$writeArray = array();

		if (is_array($storeControl)) {

			//
			// processing LOAD
			//

			if ($storeControl['LOAD'] AND $storeIndex)	{
					$GLOBALS['SOBE']->selection->sl->setFromSerialized($this->storedSettings[$storeIndex]['definition']);

					$msg = "'".$this->storedSettings[$storeIndex]['title']."' preset loaded!";

			//
			// processing SAVE
			//

			} elseif ($storeControl['SAVE'])	{
				if (trim($storeControl['title'])) {

						// get the data to store
					$newEntry = $this->compileEntry($storeControl);

					$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->table, $newEntry);

					$this->initStorage();

					if ($this->writeDevLog) t3lib_div::devLog('Settings stored:'.$this->msg, 'tx_dam_selStorage', 0);

					$msg = "'".$newEntry['title']."' preset saved!";

				} else {
					$msg = 'Please enter a name for the preset!';
				}

			//
			// processing REMOVE
			//

			} elseif ($storeControl['REMOVE'] AND $storeIndex)	{
					// Removing entry
				$msg = "'".$this->storedSettings[$storeIndex]['title']."' preset entry removed!";

				$GLOBALS['TYPO3_DB']->exec_DELETEquery($this->table, 'uid='.$storeIndex);
			}


			$this->msg = $msg;
		}
		return $this->msg;
	}




	/********************************
	 *
	 * GUI
	 *
	 ********************************/



	/**
	 * Returns the storage control box
	 *
	 * @param	string		List of elemetns which should be shown: load,remove,save
	 * @param	boolean		If set the box is wrapped with own form tag
	 * @return	string		HTML code
	 */
	function getStoreControl($showElements='load,remove,save', $useOwnForm=TRUE)	{
		global $TYPO3_CONF_VARS;

		$showElements = t3lib_div::trimExplode(',', $showElements, 1);

		$this->initStorage();

			// Preset selector
		$opt=array();
		$opt[] = '<option value="0">   </option>';
		foreach($this->storedSettings as $id => $v)	{
			$opt[] = '<option value="'.$id.'">'.htmlspecialchars($v['title']).'</option>';
		}
		$storedEntries = count($opt)>1;



		$codeTD = array();
		$codeTD[] = '<td width="1%">Preset:</td>';


			// LOAD, REMOVE, but also show selector so you can overwrite an entry with SAVE
		if($storedEntries AND (count($showElements))) {

				// selector box
			$onChange = 'document.forms[\''.$this->formName.'\'][\'selStoreControl[title]\'].value= this.options[this.selectedIndex].value!=0 ? this.options[this.selectedIndex].text : \'\';';
			$code = '
					<select name="selStoreControl[STORE]" onChange="'.htmlspecialchars($onChange).'">
					'.implode('
						', $opt).'
					</select>';

				// load button
			if(in_array('load', $showElements)) {
					$code.= '
					<input type="submit" name="selStoreControl[LOAD]" value="Load" /> ';
			}

				// remove button
			if(in_array('remove', $showElements)) {
					$code.= '
					<input type="submit" name="selStoreControl[REMOVE]" value="Remove" /> ';
			}
			$codeTD[] = '<td nowrap="nowrap">'.$code.'&nbsp;&nbsp;</td>';
		}


			// SAVE
		if(in_array('save', $showElements)) {
			$onClick = (!$storedEntries) ? '' : 'if (document.forms[\''.$this->formName.'\'][\'selStoreControl[STORE]\'].options[document.forms[\''.$this->formName.'\'][\'selStoreControl[STORE]\'].selectedIndex].value<0) return confirm(\'Are you sure you want to overwrite the existing entry?\');';
			$code = '<input name="selStoreControl[title]" value="" type="text" max="80" size="15" /> ';
			$code.= '<input type="submit" name="selStoreControl[SAVE]" value="Save" onclick="'.htmlspecialchars($onClick).'" />';
			$codeTD[] = '<td nowrap="nowrap">'.$code.'</td>';
		}


		$codeTD = implode ('
			', $codeTD);

		if (trim($code)) {
			$code = '
			<!--
				Store control
			-->
			<table border="0" cellpadding="3" cellspacing="0" width="100%">
				<tr class="bgColor4">
				'.$codeTD.'
				</tr>
			</table>
			';
		}

		if ($this->msg)	{
			$code.= '
			<div><strong>'.htmlspecialchars($this->msg).'</strong></div>';
		}
// todo need to add parameters
		if ($useOwnForm AND trim($code)) {
			$code = '
		<form action="'.t3lib_div::getIndpEnv('SCRIPT_NAME').'" method="post" name="'.$this->formName.'" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">'.$code.'</form>';
		}

		return $code;
	}




	/********************************
	 *
	 * Misc
	 *
	 ********************************/


	/**
	 * Processing entry for the stored settings array
	 * Can be overwritten by extended class
	 *
	 * @param	array		$storageData: entry for the stored settings array
	 * @return	array		$storageData: entry for the stored settings array
	 */
	function processEntry($storageArr) {
		return $storageArr;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selstorage.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selstorage.php']);
}
?>