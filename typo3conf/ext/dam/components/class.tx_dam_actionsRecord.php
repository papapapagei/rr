<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Action
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  134: class tx_dam_action_recordBase extends tx_dam_actionbase
 *  159:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  175:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *
 *
 *  200: class tx_dam_action_cmSubFile extends tx_dam_action_recordBase
 *  214:     function getLabel ()
 *  225:     function _getCommand()
 *
 *
 *  246: class tx_dam_action_editRec extends tx_dam_action_recordBase
 *  266:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  282:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  303:     function getIcon ($addAttribute='')
 *  322:     function getLabel ()
 *  332:     function getDescription ()
 *  343:     function _getCommand()
 *
 *
 *  367: class tx_dam_action_editRecPopup extends tx_dam_action_editRec
 *  376:     function getIcon ($addAttribute='')
 *  397:     function getDescription ()
 *  408:     function _getCommand()
 *
 *
 *  435: class tx_dam_action_viewFileRec extends tx_dam_actionbase
 *  457:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  474:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  496:     function getIcon ($addAttribute='')
 *  512:     function getLabel ()
 *  522:     function getDescription ()
 *  533:     function _getCommand()
 *
 *
 *  557: class tx_dam_action_infoRec extends tx_dam_action_recordBase
 *  575:     function getIcon ($addAttribute='')
 *  593:     function getLabel ()
 *  603:     function getDescription ()
 *  614:     function getWantedDivider ($type)
 *  628:     function _getCommand()
 *
 *
 *  657: class tx_dam_action_revertRec extends tx_dam_action_recordBase
 *  666:     function getIcon ($addAttribute='')
 *  685:     function getLabel ()
 *  695:     function getDescription ()
 *  706:     function getWantedDivider ($type)
 *  720:     function _getCommand()
 *
 *
 *  743: class tx_dam_action_hideRec extends tx_dam_action_recordBase
 *  756:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  780:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  801:     function getIcon ($addAttribute='')
 *  825:     function getLabel ()
 *  839:     function getDescription ()
 *  858:     function _getCommand()
 *
 *
 *  887: class tx_dam_action_deleteRec extends tx_dam_action_recordBase
 *  899:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  916:     function getIcon ($addAttribute='')
 *  935:     function getLabel ()
 *  945:     function getDescription ()
 *  956:     function _getCommand()
 *
 *
 *  984: class tx_dam_action_deleteQuickRec extends tx_dam_action_recordBase
 *  993:     function _getCommand()
 *
 *
 * 1016: class tx_dam_action_lockWarningRec extends tx_dam_action_recordBase
 * 1031:     function getIcon ($addAttribute='')
 * 1047:     function getDescription ()
 * 1058:     function _getCommand()
 *
 *
 * 1066: class tx_dam_actionsRecord
 *
 * TOTAL FUNCTIONS: 44
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once (PATH_txdam.'lib/class.tx_dam_actionbase.php');
require_once (PATH_txdam.'components/class.tx_dam_actionsFile.php');



/**
 * Edit record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_recordBase extends tx_dam_actionbase {

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control', 'context');

	/**
	 * If set the action is for items with edit permissions only
	 * @access private
	 */
	var $editPermsNeeded = true;

	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] === 'record'); # AND ($this->itemInfo['__table'] === 'tx_dam');
		}
		return $valid;
	}


	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		global $TCA;

		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid)	{
			$valid = (($this->itemInfo['__type'] === 'record') AND $this->itemInfo['__table']);
			if ($valid AND $this->editPermsNeeded) {
			 	$valid = ($this->env['permsEdit'] AND !$TCA[$this->itemInfo['__table']]['ctrl']['readOnly']);
			}
		}
		return $valid;
	}

}



/**
 * Context menu action that triggers a 'file ...' sub menu
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_cmSubFile extends tx_dam_action_recordBase {

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array();


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:cm_file_sub');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$filename = tx_dam::file_absolutePath($this->itemInfo);
		$params = '&subname=tx_dam_cm_file&parentname=tx_dam_cm_record&txdamFile='.$filename;

		$commands['onclick'] = 'top.loadTopMenu(\''.t3lib_div::linkThisScript().'&cmLevel=1'.$params.'\');return false;';
		$commands['dontHide'] = true;

		return $commands;
	}
}


/**
 * Edit record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_editRec extends tx_dam_action_recordBase {


	/**
	 * If set the action is for items with edit permissions only
	 * @access private
	 */
	var $editPermsNeeded = true;

	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 * @todo move language check here if uid is available
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] === 'record'); # AND ($this->itemInfo['__table'] === 'tx_dam');
		}
		return $valid;
	}


	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		global $TCA;

		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid)	{
			$valid = (($this->itemInfo['__type'] === 'record') AND $this->itemInfo['__table']);
			if ($valid AND $this->editPermsNeeded) {
			 	$languageField = $TCA[$this->itemInfo['__table']]['ctrl']['languageField'];
			 	$valid = ($this->env['permsEdit'] 
			 			AND !$TCA[$this->itemInfo['__table']]['ctrl']['readOnly']	 	
			 	 		AND (!isset($this->itemInfo[$languageField]) OR $GLOBALS['BE_USER']->checkLanguageAccess($this->itemInfo[$languageField])));
			}
		}
		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

#		if ($this->disabled OR $TCA[$this->itemInfo['__table']]['ctrl']['readOnly']) {
#			$iconFile = PATH_txdam_rel.'i/view_readonly.gif';
			
		if ($this->disabled) {
			$iconFile = 'gfx/edit2_i.gif';
		} else {
			$iconFile = 'gfx/edit2'. (!$TCA[$this->itemInfo['__table']]['ctrl']['readOnly'] ? '' : '_d').'.gif';
			
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="11" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.edit');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:edit');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$uid = $this->itemInfo['_LOCALIZED_UID'] ? $this->itemInfo['_LOCALIZED_UID'] : $this->itemInfo['uid'];
		$params = '&edit['.$this->itemInfo['__table'].']['.$uid.']=edit';

		if ($this->type === 'context') {
			$commands['url'] = 'alt_doc.php?'.$params;
		} else {
			$onClick = t3lib_BEfunc::editOnClick($params, $this->env['backPath'], -1);
			$commands['onclick'] = $onClick;
		}

		return $commands;
	}
}




/**
 * Localize record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_localizeRec extends tx_dam_action_editRec {
	
	
	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] === 'record' AND $this->env['currentLanguage']>0); # AND ($this->itemInfo['__table'] === 'tx_dam');
		}
		return $valid;
	}
	

	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		global $TCA;

		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid)	{
			$valid = (($this->itemInfo['__type'] === 'record') AND $this->itemInfo['__table']);
			if ($valid AND $this->editPermsNeeded) {
				
				$languageField = $TCA[$this->itemInfo['__table']]['ctrl']['languageField'];
		
			 	$valid = ($this->env['permsEdit'] 
				 		AND !$TCA[$this->itemInfo['__table']]['ctrl']['readOnly'] 
				 		AND ($this->itemInfo[$languageField] != $this->env['currentLanguage'])
			 		);
			}
		}
		return $valid;
	}
	
	
	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		if ($this->disabled) {
			$iconFile = PATH_txdam_rel.'i/localize_i.gif';
		} else {
			$iconFile = PATH_txdam_rel.'i/localize'. (!$TCA[$this->itemInfo['__table']]['ctrl']['readOnly'] ? '' : '_d').'.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="18" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}
	

	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:langCreateTrans');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:langCreateTrans');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$uid = $this->itemInfo['_BASE_REC_UID'] ? $this->itemInfo['_BASE_REC_UID'] : $this->itemInfo['uid'];
		$href = $GLOBALS['TBE_TEMPLATE']->issueCommand(
							'&cmd['.$this->itemInfo['__table'].']['.$uid.'][localize]='.$this->env['currentLanguage'],
							$this->env['returnUrl'].'&justLocalized='.rawurlencode($this->itemInfo['__table'].':'.$uid.':'.$this->env['currentLanguage'])
						);
						// justLocalized unused for now
						// should force the list module to redirect to alt_dec.php after localization. See web list
							

		if ($this->type === 'context') {
			$commands['url'] = $href;
		} else {
			$commands['href'] = $this->env['backPath'].$href;
		}

		return $commands;
	}
	
}



/**
 * Edit record in popup window action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_editRecPopup extends tx_dam_action_editRec {

	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		if ($this->disabled) {
			$iconFile = PATH_txdam_rel.'i/edit_popup_i.gif';
		} else {
			$iconFile = PATH_txdam_rel.'i/edit_popup'. (!$TCA[$this->itemInfo['__table']]['ctrl']['readOnly'] ? '' : '_d').'.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="16" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}



	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.openInNewWindow');
	}
	
	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/locallang_db.xml:cm.edit_popup');
	}	


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$uid = $this->itemInfo['_LOCALIZED_UID'] ? $this->itemInfo['_LOCALIZED_UID'] : $this->itemInfo['uid'];
		
		$params = array();
		$params['edit['.$this->itemInfo['__table'].']['.$uid.']'] = 'edit';
		$params['noView'] = 1;
		$params['returnUrl'] = PATH_txdam_rel.'close.html';
		$onClick = 'vHWin=window.open(\''.t3lib_div::linkThisUrl($this->env['backPath'].'alt_doc.php', $params).'\',\''.md5(t3lib_div::getIndpEnv('TYPO3_REQUEST_SCRIPT')).'\',\''.($GLOBALS['BE_USER']->uc['edit_wideDocument']?'width=670,height=550':'width=600,height=550').',status=0,menubar=0,scrollbars=1,resizable=1\');vHWin.focus();return false;';

		if ($this->type === 'context') {
			$commands['onclick'] = $onClick.' return hideCM();';
		} else {
			$commands['onclick'] = 'return '.$onClick;
		}

		return $commands;
	}
}


/**
 * View file (popup)
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_viewFileRec extends tx_dam_actionbase {

// see tx_dam_action_viewFile

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control', 'context');


	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] === 'record' AND $this->itemInfo['__table'] === 'tx_dam');
		}

		return $valid;
	}


	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		$valid = ($this->isTypeValid ($type, $itemInfo, $env) AND ($itemInfo['file_status'] != TXDAM_status_file_missing));
		if ($valid) {
// more simpler access check is needed
			if ($this->itemInfo['file_path_absolute'])	{
				$valid = (t3lib_div::isFirstPartOfStr($this->itemInfo['file_path_absolute'], PATH_site));
			} else 	{
				$this->itemInfo['file_path_absolute'] = tx_dam::path_makeAbsolute ($this->itemInfo['file_path']);
				$valid = (t3lib_div::isFirstPartOfStr($this->itemInfo['file_path_absolute'], PATH_site));
			}
		}
		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		if ($this->disabled) {
			$iconFile = 'gfx/zoom_i.gif';
		} else {
			$iconFile = 'gfx/zoom.gif';
		}
		$icon =	'<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';
		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.view');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:viewFile');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$href = tx_dam::file_relativeSitePath ($this->itemInfo);
		$onClick = "top.openUrlInWindow('".t3lib_div::getIndpEnv('TYPO3_SITE_URL').$href."','WebFile');";

		if ($this->type === 'context') {
			$commands['onclick'] = $onClick.' return hideCM();';
		} else {
			$commands['onclick'] = 'return '.$onClick;
		}

		return $commands;
	}

}

/**
 * Info record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_infoRec extends tx_dam_action_recordBase {


	/**
	 * If set the action is for items with edit permissions only
	 * @access private
	 */
	var $editPermsNeeded = false;



	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		if ($this->disabled) {
			$iconFile = 'gfx/zoom2_i.gif';
		} else {
			$iconFile = 'gfx/zoom2.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}

	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.info');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:showInfo');
	}
	
	/**
	 * Tells the wanted position for a list of actions
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: after:tx_dam_newFolder;before:tx_other_item
	 */
	function getWantedPosition ($type) {
		if($type === 'context') {
			return 'before:tx_dam_action_editFileRec';
		}
		return '';
	}	


	/**
	 * Tells if a spacer/margin is wanted before/after the action
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: "divider:spacer". Divider before and spacer after
	 */
	function getWantedDivider ($type) {
		$divider = '';
		if ($type === 'context') {
			$divider = ':divider';
		}
		return $divider;
	}

	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		// what we want is to have info about the file
		// and while DAM registered an info viewer for file we use that instead of the record info view
		// $commands['onclick'] = 'top.launchView(\''.$this->itemInfo['__table'].'\', \''.$this->itemInfo['uid'].'\'); return false;';

		$filename = tx_dam::file_absolutePath($this->itemInfo);
		$onClick = 'top.launchView(\''.$filename.'\', \'\');';

		if ($this->type === 'context') {
			$commands['onclick'] = $onClick.' return hideCM();';
		} else {
			$commands['onclick'] = $onClick.' return false;';
		}

		return $commands;
	}
}



/**
 * Revert record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_revertRec extends tx_dam_action_recordBase {

	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		if ($this->disabled) {
			$iconFile = 'gfx/history2_i.gif';
		} else {
			$iconFile = 'gfx/history2.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="13" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_misc.xml:CM_history');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:history');
	}


	/**
	 * Tells if a spacer/margin is wanted before/after the action
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: "divider:spacer". Divider before and spacer after
	 */
	function getWantedDivider ($type) {
		$divider = '';
		if ($type === 'context') {
			$divider = 'divider:';
		}
		return $divider;
	}

	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$uid = $this->itemInfo['_LOCALIZED_UID'] ? $this->itemInfo['_LOCALIZED_UID'] : $this->itemInfo['uid'];
		$param = 'element='.rawurlencode($this->itemInfo['__table'].':'.$uid);

		if ($this->type === 'context') {
			$commands['url'] = 'show_rechis.php?'.$param;
		} else {
			$commands['onclick'] = 'return jumpExt(\''.$this->env['backPath'].'show_rechis.php?'.$param.'\',\'#latest\');';
		}
		return $commands;
	}
}


/**
 * Hide/Unhide record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_hideRec extends tx_dam_action_recordBase {

	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		global $TCA;

		$valid = false;
		if (parent::isPossiblyValid ($type, $itemInfo, $env)) {
			if ($this->itemInfo['__table']) {
				$this->_hiddenField = $TCA[$this->itemInfo['__table']]['ctrl']['enablecolumns']['disabled'];
				if ($this->env['permsEdit'] && $this->_hiddenField && $TCA[$this->itemInfo['__table']]['columns'][$this->_hiddenField]
					&& (!$TCA[$this->itemInfo['__table']]['columns'][$this->_hiddenField]['exclude'] || $GLOBALS['BE_USER']->check('non_exclude_fields', $this->itemInfo['__table'].':'.$this->_hiddenField))) {
						$valid = true;
				}
			}
		}
		return $valid;
	}

	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		global $TCA;

		$valid = parent::isValid ($type, $itemInfo, $env);
		if ($valid)	{
			if ($this->env['permsEdit'] && $this->_hiddenField && $TCA[$this->itemInfo['__table']]['columns'][$this->_hiddenField]
				&& (!$TCA[$this->itemInfo['__table']]['columns'][$this->_hiddenField]['exclude'] || $GLOBALS['BE_USER']->check('non_exclude_fields', $this->itemInfo['__table'].':'.$this->_hiddenField))) {
					$valid = true;
			}
		}
		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		$prefix = '';
		if ($this->itemInfo[$this->_hiddenField]) {
			$prefix = 'un';
		}

		if ($this->disabled) {
			$iconFile = 'gfx/button_'.$prefix.'hide_i.gif';
		} else {
			$iconFile = 'gfx/button_'.$prefix.'hide.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="11" height="10"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		$name = 'hide';
		if ($this->itemInfo[$this->_hiddenField]) {
			$name = 'unhide';
		}
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.'.$name);
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		$content = '';

		$prefix = 'hide';
		if ($this->itemInfo[$this->_hiddenField]) {
			$prefix = 'unHide';
		}
		$content = $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:'.$prefix.($this->itemInfo['__table'] === 'pages' ? 'Page' : ''));

		return $content;
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		if ($this->itemInfo[$this->_hiddenField]) {
			$params = '0';
		} else {
			$params = '1';
		}
		$uid = $this->itemInfo['_BASE_REC_UID'] ? $this->itemInfo['_BASE_REC_UID'] : $this->itemInfo['uid'];
		$params = '&data['.$this->itemInfo['__table'].']['.$uid.']['.$this->_hiddenField.']='.$params;

		if ($this->type === 'context') {
			$commands['url'] = $GLOBALS['SOBE']->doc->issueCommand($params, -1);
		} else {
			$commands['onclick'] = 'return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params, -1).'\');';
		}

		return $commands;
	}
}



/**
 * Delete record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_deleteRec extends tx_dam_action_recordBase {

	var $cmd = 'tx_dam_cmd_filedelete';

	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		global $TCA;

		$valid = parent::isValid ($type, $itemInfo, $env);
		if ($valid)	{
			 $valid = ($this->env['permsDelete']);
		}
		return $valid;
	}

	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		if ($this->disabled) {
			$iconFile = 'gfx/delete_record_i.gif';
		} else {
			$iconFile = 'gfx/delete_record.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete');
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$uid = $this->itemInfo['_BASE_REC_UID'] ? $this->itemInfo['_BASE_REC_UID'] : $this->itemInfo['uid'];

		$script = $this->env['defaultCmdScript'];
		$script .= '?CMD='.$this->cmd;
		$script .= '&vC='.$GLOBALS['BE_USER']->veriCode();
		$script .= '&record['.$this->itemInfo['__table'].']='.$uid;

		if ($this->type === 'context') {
			$commands['url'] = $script;
		} else {
			$script .= '&returnUrl='.rawurlencode($this->env['returnUrl']);
			$commands['href'] = $script;
		}

		return $commands;
	}
	
	/**
	 * Tells the wanted position for a list of actions
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: after:tx_dam_newFolder;before:tx_other_item
	 */
	function getWantedPosition ($type) {
		if($type === 'context') {
			return 'after:tx_dam_action_replaceFileRec';
		}
		return '';
	}	
}



/**
 * Delete without notification - record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_deleteQuickRec extends tx_dam_action_recordBase {


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$uid = $this->itemInfo['_BASE_REC_UID'] ? $this->itemInfo['_BASE_REC_UID'] : $this->itemInfo['uid'];
		$params = '&cmd['.$this->itemInfo['__table'].']['.$uid.'][delete]=1';
		$title = $this->itemInfo['title'].' ('.$this->itemInfo['file_name'].')';
//		$onClick = 'if (confirm('.$GLOBALS['LANG']->JScharCode(sprintf($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:mess.delete'), $title)).')) {jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params, -1).'\');} return false;';
		$onClick = 'jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params, -1).'\'); return false;';

		$commands['onclick'] = $onClick;

		return $commands;
	}
}

/**
 * Rename file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_renameFileRec extends tx_dam_action_renameFile {

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control', 'context');
	
	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] === 'record' AND $this->itemInfo['__table'] === 'tx_dam');
		}
		return $valid;
	}
	
	/**
	 * Tells the wanted position for a list of actions
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: after:tx_dam_newFolder;before:tx_other_item
	 */
	function getWantedPosition ($type) {
		if($type === 'context') {
			return 'after:tx_dam_action_editFileRec;after:tx_dam_action_infoRec';
		}
		return '';
	}

}

/**
 * Rename file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_replaceFileRec extends tx_dam_action_replaceFile {

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control', 'context');
	
	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] === 'record' AND $this->itemInfo['__table'] === 'tx_dam');
		}
		return $valid;
	}
	
	/**
	 * Tells the wanted position for a list of actions
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: after:tx_dam_newFolder;before:tx_other_item
	 */
	function getWantedPosition ($type) {
		if($type === 'context') {
			return 'after:tx_dam_action_renameFileRec';
		}
		return '';
	}	
}


/**
 * Record locking warning - record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_lockWarningRec extends tx_dam_action_recordBase {

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');

	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		$uid = $this->itemInfo['_LOCALIZED_UID'] ? $this->itemInfo['_LOCALIZED_UID'] : $this->itemInfo['uid'];
		if ($this->_lockInfo = t3lib_BEfunc::isRecordLocked($this->itemInfo['__table'], $uid)) {
			$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], 'gfx/recordlock_warning3.gif', 'width="17" height="12"').' title="'.htmlspecialchars($this->getDescription ()).'" alt="" />';
		}

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $this->_lockInfo['msg'] ? $this->_lockInfo['msg']: '';
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {
		$commands['onclick'] = 'alert('.$GLOBALS['LANG']->JScharCode($this->_lockInfo['msg']).');return false;';

		return $commands;
	}
}





	/***************************************
	 *
	 *   editors
	 *
	 ***************************************/






/**
 * Edit file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_editFileRec extends tx_dam_actionbase {

	var $cmd = '';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control', 'context');


	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] === 'record' AND $this->itemInfo['__table'] === 'tx_dam');
		}
		return $valid;
	}

	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		static $editorList=array();
		
		$valid = false;

		if ($this->isTypeValid ($type, $itemInfo, $env) AND $this->itemInfo['__type'] === 'record' AND $this->itemInfo['__table'] === 'tx_dam') {

			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['editorClasses']))	{
				foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['editorClasses'] as $idName => $classRessource)	{
					if (!is_object($editorList[$idName])) {
						$editorList[$idName] = t3lib_div::getUserObj($classRessource);
					}
					if (is_object($editorList[$idName])) {
						if ($editorList[$idName]->isValid($itemInfo)) {
							$valid = true;
							break;
						}
					}
				}
			}
		}

		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			// edit_file_i.gif does not work - icon processing is broken, default.gif will be returned
			$iconFile = PATH_txdam_rel.'i/edit_file_i_.gif';
		} else {
			$iconFile = PATH_txdam_rel.'i/edit_file.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($this->env['backPath'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/mod_edit/locallang.xml:tx_dam_edit.title');
	}
	
	
	/**
	 * Tells the wanted position for a list of actions
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: after:tx_dam_newFolder;before:tx_other_item
	 */
	function getWantedPosition ($type) {
		if($type === 'context') {
			return 'before:tx_dam_action_renameFileRec';
		}
		return '';
	}			
	

	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$uid = $this->itemInfo['_BASE_REC_UID'] ? $this->itemInfo['_BASE_REC_UID'] : $this->itemInfo['uid'];

		$script = $this->env['defaultEditScript'];
		$script .= '?CMD='.$this->cmd;
		$script .= '&vC='.$GLOBALS['BE_USER']->veriCode();
		$script .= '&record['.$this->itemInfo['__table'].']='.$uid;

		if ($this->type === 'context') {
			$commands['url'] = $script;
		} else {
			$script .= '&returnUrl='.rawurlencode($this->env['returnUrl']);
			$commands['href'] = $script;
		}

		return $commands;
	}
}






class tx_dam_actionsRecord {
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_actionsRecord.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_actionsRecord.php']);
}

?>
