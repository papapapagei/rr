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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   94: class tx_dam_actionCall
 *
 *              SECTION: Constructor / Initialization
 *  170:     function tx_dam_actionCall($classes=NULL)
 *  181:     function __construct($classes=NULL)
 *  194:     function initClasses($classes=NULL)
 *  207:     function registerAction ($idName, $class)
 *  220:     function setEnv ($param1, $param2=NULL)
 *  241:     function setRequest ($type, $itemInfo, $mode='', $moduleName='')
 *
 *              SECTION: Iterator functions
 *  264:     function rewind()
 *  274:     function valid()
 *  285:     function next()
 *  295:     function key()
 *  305:     function &current()
 *  320:     function count ()
 *
 *              SECTION: Rendering
 *  341:     function renderActionsHorizontal($checkValidStrict=false, $showDisabled=true)
 *  376:     function renderActionsContextMenu($checkValidStrict=false, $showDisabled=false)
 *  413:     function renderMultiActions($checkValidStrict=false, $showDisabled=false)
 *  452:     function checkItemValid (&$item)
 *
 *              SECTION: Init item list
 *  497:     function initActions ($checkForPossiblyValid=false, $keepInvalid=false)
 *  509:     function initItems()
 *  547:     function addItem($idName, $position='', $divider='')
 *
 *              SECTION: Objects
 *  616:     function initObjects ($checkForPossiblyValid=false, $keepInvalid=false)
 *  656:     function &getByIDName ($idName)
 *  667:     function makeObject ($idName)
 *
 * TOTAL FUNCTIONS: 22
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */






/**
 * Action calling
 *
 * A action is something that renders buttons, control icons, ..., which executes command for an item.
 * This class can be used to find the right actions for an item and call the actions.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 * @see tx_dam_actionbase
 */
class tx_dam_actionCall {

	/**
	 * defines the output/render type
	 *
	 * Possible values:
	 * icon, button, control, context
	 */
	 var $type;

	/**
	 * holds the data of the item a action should be called for. for example just record data.
	 *
	 * two special fields are defined:
	 * __type: record, dir, file
	 * __table: table name for records
	 *
	 * These fields have to be defined if no real data is available yet.
	 * dir and file data has the format of tx_dam::file_compileInfo();
	 */
	 var $itemInfo = array(
	 		'__type' => '',
	 		'__table' => '',
	 	);


	/**
	 * stores action class references by idName keys
	 */
	var $classes = array();

	/**
	 * stores action objects
	 */
	var $objects = array();

	/**
	 * stores a sorted list of actions and spacers
	 */
	var $items = array();

	/**
	 * Environment
	 */
	 var $env = array(
	 	'returnUrl' => '',
	 	'defaultCmdScript' => '',
	 	'defaultEditScript' => '',
	 	'backPath' => '',
	 	);

	/**
	 * If set divider will be rendered, otherwise suppressed
	 */
	var $enableDivider = true;

	/**
	 * If set spacer will be rendered, otherwise suppressed
	 */
	var $enableSpacer = true;



	/***************************************
	 *
	 *	 Constructor / Initialization
	 *
	 ***************************************/


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_actionCall($classes=NULL) {
		$this->__construct($classes);
	}


	/**
	 * Constructor
	 *
	 * @param	array		$classes Class reference array
	 * @return	void
	 */
	function __construct($classes=NULL) {
		$this->classes = is_array($classes) ? $classes : $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['actionClasses'];
		$this->classes = is_array($this->classes) ? $this->classes : array();
		$this->env['backPath'] = $GLOBALS['BACK_PATH'];
		tx_dam::config_init();
	}


	/**
	 * Initializes with own action classes
	 *
	 * @param	array		$classes Class reference array
	 * @return	void
	 */
	function initClasses($classes=NULL) {
		$this->__construct($classes);
	}


	/**
	 * Register a "action" class locally.
	 * This means it is known only by the instance of this class and not to the system.
	 *
	 * @param	string		$idName This is the ID of the action. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. See t3lib_div::callUserFunction().
	 * @return	void
	 */
	function registerAction ($idName, $class) {
		$this->classes[$idName] = $class;
	}


	/**
	 * Remove a "action" class locally.
	 * This means the action is removed from the instance of this class and not from the system.
	 *
	 * @param	string		$idName This is the ID of the action. Chars allowed only: [a-zA-z]
	 * @return	void
	 */
	function removeAction ($idName) {
		unset($this->classes[$idName]);
	}


	/**
	 * Set values for the local environment.
	 * The environment are special keys/values that gives the action information what to do.
	 *
	 * @param	mixed		$param1 If array it is the env array. If string it is the key for the second paramater.
	 * @param	string		$param2 Is value if the first paramater is a string which is the key for this value.
	 * @return	void
	 */
	 function setEnv ($param1, $param2=NULL) {
	 	$env = array();

	 	if (is_array($param1)) {
	 		foreach ($param1 as $key => $value) {
		 		if (is_string($value) AND preg_match('#^http://#', $value)) {
		 				// this append ? to any url so easily &params can be appended
		 			$value = strpos($value, '?') ? $value : $value.'?';
		 		}
		 		$env[$key] = $value;
	 		}
	 		
	 	}
	 	elseif (!is_array($param1) AND !is_null($param2)) {
	 		$env[$param1] = $param2;
	 	}
	 	$this->env = t3lib_div::array_merge_recursive_overrule($this->env, $env);
	 }


	/**
	 * Define what type of action are requested
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	string		$mode unused for now
	 * @param	string		$moduleName Module name, eg. $GLOBALS['MCONF']['name']
	 * @return	void
	 */
	function setRequest ($type, $itemInfo, $mode='', $moduleName='') {
		$this->type = $type;
		$this->itemInfo = $itemInfo;
 		$this->mode = $mode;
 		$this->moduleName = $moduleName ? $moduleName : $GLOBALS['MCONF']['name'].'.'.$GLOBALS['SOBE']->extClassConf['name'];

 		$this->env['mode'] = $this->mode;
 		$this->env['moduleName'] = $this->moduleName;
	}


	/***************************************
	 *
	 *	 Iterator functions
	 *
	 ***************************************/


	/**
	 * Set the internal pointer to its first element.
	 *
	 * @return	void
	 */
	function rewind() {
		reset($this->items);
	}


	/**
	 * Return true is the current element is valid.
	 *
	 * @return	boolean
	 */
	function valid() {
		$key = key($this->items);
		return isset($key);
	}


	/**
	 * Advance the internal pointer
	 *
	 * @return	void
	 */
	function next() {
		next($this->items);
	}


	/**
	 * Return the pointer to the current element
	 *
	 * @return	mixed
	 */
	function key() {
		return key($this->items);
	}


	/**
	 * Return the current element
	 *
	 * @return	array
	 */
	function &current() {
		$item = current($this->items);
		if (substr($item,0,2) === '__') {
				// returning a reference is not nice but there's no way
			return $item;
		}
		return $this->objects[$item];
	}


	/**
	 * Count elements
	 *
	 * @return	integer
	 */
	function count () {
		return count($this->items);
	}



	/***************************************
	 *
	 *	 Rendering
	 *
	 ***************************************/


	/**
	 * Walk through the list of actions and render them.
	 * Dividers and spacer are rendered for horizontal use.
	 *
	 * @param	boolean		$checkValidStrict Perform a strict valid test with isValid() for each action.
	 * @param	boolean		$showDisabled Will render disabled items for non-valid actions. Eg. a greyed icon without link.
	 * @return	array		Array of rendered items. Can be imploded for example.
	 */
	function renderActionsHorizontal($checkValidStrict=false, $showDisabled=true) {
		$actions = array();
		$this->rewind();
		while ($valid = $this->valid()) {
			$item = $this->current();

			if ($this->enableSpacer AND $item === '__spacer') {
				$actions[] = '&nbsp; &nbsp;';
			}
			elseif ($this->enableDivider AND $item === '__divider') {
				$actions[] = '&nbsp;<span class="actionHorizontal">&nbsp;</span>';
			}
			elseif (is_object($item)) {
				if ($checkValidStrict) {
					$valid = $this->checkItemValid($item);
				}

				if ($valid OR $showDisabled) {
					$actions[] = $item->render($this->type, !$valid);
				}
			}
			$this->next();
		}
		return $actions;
	}


	/**
	 * Walk through the list of actions and render them.
	 * Dividers and spacer are rendered for horizontal use.
	 *
	 * @param	boolean		$checkValidStrict Perform a strict valid test with isValid() for each action.
	 * @param	boolean		$showDisabled Will render disabled items for non-valid actions. Eg. a greyed icon without link.
	 * @return	array		Array of rendered items. Can be imploded for example.
	 */
	function renderActionsContextMenu($checkValidStrict=false, $showDisabled=false) {
		$actions = array();
		$divider = 0;
		$this->rewind();
		while ($valid = $this->valid()) {
			$item = $this->current();

			if ($this->enableSpacer AND $item === '__spacer') {
				$actions['adivider'.(++$divider)]['isDivider'] = true;
			}
			elseif ($this->enableDivider AND $item === '__divider') {
				$actions['adivider'.(++$divider)]['isDivider'] = true;
			}
			elseif (is_object($item)) {
				if ($checkValidStrict) {
					$valid = $this->checkItemValid($item);
				}

				if ($valid OR $showDisabled) {
					$actions[$item->idName] = $item->render($this->type, !$valid);
					$actions[$item->idName]['valid'] = $valid;
				}
			}
			$this->next();
		}
		return $actions;
	}


	/**
	 * Walk through the list of actions and render them.
	 * Dividers are rendered for list use.
	 *
	 * @param	boolean		$checkValidStrict Perform a strict valid test with isValid() for each action.
	 * @param	boolean		$showDisabled Will render disabled items for non-valid actions. Eg. a greyed icon without link.
	 * @return	array		Array of rendered items. Can be imploded for example.
	 */
	function renderMultiActions($checkValidStrict=false, $showDisabled=false) {
		$actions = array();
		$divider = 0;
		$this->rewind();
		while ($valid = $this->valid()) {
			$item = $this->current();

			if ($this->enableSpacer AND $item === '__spacer') {
				$actions['adivider'.(++$divider)]['isDivider'] = true;
			}
			elseif ($this->enableDivider AND $item === '__divider') {
				$actions['adivider'.(++$divider)]['isDivider'] = true;
			}
			elseif (is_object($item)) {
				if ($checkValidStrict) {
					$valid = $this->checkItemValid($item);
				}

				if ($valid OR $showDisabled) {
					$actions[$item->idName] = $item->render($this->type, !$valid);
					$actions[$item->idName]['valid'] = $valid;

				}
			}
			$this->next();
		}

		return $actions;
	}


	/**
	 * Function calls the actions own ->isValid function.
	 * If that returns true - meaning that the action is accessible a hook taking effect which allows external validation of the
	 * action.
	 *
	 * @param	object		$item Reference to the action object currently in process
	 * @return	boolean		returns true or false
	 */
	function checkItemValid (&$item) {
		global $TYPO3_CONF_VARS;

		$valid = $item->isValid($this->type, $this->itemInfo, $this->env);

		if ($valid) {
			$item->getIdName();

				// hook
			if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam']['actionValidation']) AND count($TYPO3_CONF_VARS['EXTCONF']['dam']['actionValidation']))	{
				foreach($TYPO3_CONF_VARS['EXTCONF']['dam']['actionValidation'] as $classKey => $classRef)	{
					if (strtolower($classKey) == strtolower($item->idName)) {
						if (is_object($obj = &t3lib_div::getUserObj($classRef)))	{
							if (method_exists($obj, 'isTypeValid')) {
								$valid = $obj->isTypeValid($item->idName, $this->itemInfo);
								if ($valid === false) {
									break;
								}
							}
						}
					}
				}
			}
		}

		return $valid;
	}



	/***************************************
	 *
	 *	 Init item list
	 *
	 ***************************************/



	/**
	 * Initializes the action objects.
	 *
	 * @param	boolean		$checkForPossiblyValid If set invalid will be done with isPossiblyValid().
	 * @param	boolean		$keepInvalid If set invalid actions will not removed
	 * @return	void
	 */
	function initActions ($checkForPossiblyValid=false, $keepInvalid=false) {
		$this->initObjects ($checkForPossiblyValid, $keepInvalid);
		$this->initItems ();
		$this->rewind();
	}


	/**
	 * Initializes the action item list including sorting
	 *
	 * @return	void
	 */
	function initItems() {
		$this->items = array();

		foreach ($this->objects as $idName => $action) {
			$this->addItem ($idName, $action->getWantedPosition($this->type), $action->getWantedDivider($this->type));
		}
			// remove first and last spacer etc
		while (substr($this->items[0],0,2) === '__') {
			unset($this->items[0]);
		}	// remove first and last spacer etc
		while (substr(end($this->items),0,2) === '__') {
			unset($this->items[key($this->items)]);
		}
			// remove double spacer etc
		$last = NULL;
		foreach ($this->items as $key => $item) {
			if ($last) {
				if ($this->items[$last] == $item)
					unset ($this->items[$last]);
				if ($this->items[$last] === '__spacer' AND $item === '__divider')
					unset ($this->items[$last]);
				if ($this->items[$last] === '__divider' AND $item === '__spacer')
					unset ($this->items[$key]);
			}
			$last = $key;
		}
	}



	/**
	 * Adds a module (main or sub) to the backend interface
	 *
	 * @param	string		$idName
	 * @param	string		$position can be used to set the position of the action within the list of existing action items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @param	string		$divider Diver before and after the element can be defined. Example: "spacer:divider". Spacer before, divider after.
	 * @return	void
	 */
	function addItem($idName, $position='', $divider='')	{

		$position .= ';bottom';
		$posList = t3lib_div::trimExplode(';', $position, 1);

		list($dividerBefore, $dividerAfter) = explode(':', $divider);
		$dividerBefore = $dividerBefore ? '__'.$dividerBefore : false;
		$dividerAfter = $dividerAfter ? '__'.$dividerAfter : false;

		$element = t3lib_div::trimExplode(';', $dividerBefore.';'.$idName.';'.$dividerAfter, 1);

		$placed = false;

		foreach ($posList as $posDef) {
			list($place, $itemRef) = t3lib_div::trimExplode(':', $posDef, 1);
			switch($place)	{
				case 'after':
				case 'before':
					$found = false;
					$pointer = 0;
					foreach($this->items as $k => $m)	{
						if (!strcmp($m, $itemRef))	{
							$pointer = $place === 'after' ? $k+1 : $k;
							$found = true;
						}
					}
					if ($found) {

						$element = (count($element)>1) ? $element : $idName;
						array_splice(
							$this->items,
							$pointer,
							0,
							$element
						);
						$placed = true;
					}
				break;
				case 'top':
					$this->items = array_merge($element, $this->items);
					$placed = true;
				break;
				default:
						// append to the list
					$this->items = array_merge($this->items, $element);
					$placed = true;
				break;
			}
			if ($placed) break;
		}
	}




	/***************************************
	 *
	 *	 Objects
	 *
	 ***************************************/


	
	/**
	 * Initializes the action objects.
	 *
	 * @param	boolean		$checkForPossiblyValid If set invalid will be done with isPossiblyValid().
	 * @param	boolean		$keepInvalid If set invalid actions will not removed
	 * @return	void
	 */
	function initObjects ($checkForPossiblyValid=false, $keepInvalid=false) {
		
		$setupAllowDeny = tx_dam::config_getValue('mod.txdamM1_SHARED.actions', true);
		$setupAllowDeny = isset($setupAllowDeny[$this->type.'.']) ? $setupAllowDeny[$this->type.'.'] : $setupAllowDeny['shared.'];
		$setupAllowDenyShared = tx_dam_allowdeny_list::transformSimpleSetup ($setupAllowDeny);
	
		
		list($modName, $modFuncName) = explode('.', $this->moduleName);
		
		$setupAllowDeny = tx_dam::config_getValue('mod.'.$modName.'.actions', true);
		$setupAllowDeny = isset($setupAllowDeny[$this->type.'.']) ? $setupAllowDeny[$this->type.'.'] : $setupAllowDeny['shared.'];
		$setupAllowDenyMod = tx_dam_allowdeny_list::transformSimpleSetup ($setupAllowDeny);
		
		if ($modFuncName) {
			$setupAllowDeny = tx_dam::config_getValue('mod.'.$modName.'.modfunc.'.$modFuncName.'.actions', true);
			$setupAllowDeny = isset($setupAllowDeny[$this->type.'.']) ? $setupAllowDeny[$this->type.'.'] : $setupAllowDeny['shared.'];
			$setupAllowDenyModfunc = tx_dam_allowdeny_list::transformSimpleSetup ($setupAllowDeny);
		}
		$allowDeny = new tx_dam_allowdeny_list(array_keys($this->classes), $setupAllowDenyModfunc, $setupAllowDenyMod, $setupAllowDenyShared);

		foreach ($this->classes as $idName => $classRef) {
			if ($allowDeny->isAllowed($idName) AND $this->makeObject($idName)) {
				$this->objects[$idName]->setItemInfo($this->itemInfo);
				$this->objects[$idName]->setEnv($this->env);
				$this->objects[$idName]->getIdName();
				if ($checkForPossiblyValid) {
					$valid = $this->objects[$idName]->isPossiblyValid($this->type);
				}
				else {
					$valid = $this->objects[$idName]->isValid($this->type);
				}
				if (!$keepInvalid AND !$valid) {
					unset ($this->objects[$idName]);
				}
			}
		}
	}






	/**
	 * Get an object by it's idName
	 *
	 * @param	string		$idName
	 * @return	object
	 */
	function &getByIDName ($idName) {
		return (is_object($this->objects[$idName]) ? $this->objects[$idName] : NULL);
	}


	/**
	 * Initialize an object by it's idName
	 *
	 * @param	string		$idName
	 * @return	boolean
	 */
	function makeObject ($idName) {
		if (!isset($this->objects[$idName]) AND isset($this->classes[$idName])) {
			if (!is_object($this->objects[$idName] = t3lib_div::getUserObj($this->classes[$idName]))) {
				unset($this->objects[$idName]);
			}
		}
		return is_object($this->objects[$idName]);
	}


}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_actioncall.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_actioncall.php']);
}
?>