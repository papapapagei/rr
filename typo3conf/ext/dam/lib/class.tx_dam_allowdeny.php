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
 * @package DAM-Component
 * @subpackage BaseClass
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   77: class tx_dam_allowdeny
 *
 *              SECTION: Constructor / Initialization
 *  103:     function tx_dam_allowdeny($items, $conf)
 *  115:     function __construct($items, $conf)
 *
 *              SECTION: Check
 *  165:     function isAllowed ($item)
 *  200:     function getAllowed ()
 *  219:     function getAllowedList ()
 *  237:     function allowDeny($type, $item)
 *  264:     function _evalCondition($type, $matchList, $item)
 *
 *              SECTION: Tools
 *  329:     function _processRules($type)
 *
 *
 *  357: class tx_dam_allowdeny_list
 *  372:     function tx_dam_allowdeny_list ()
 *  393:     function isAllowed ($item)
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */





/**
 * Allow/Deny class
 * This helps to set allowed items in a list
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage BaseClass
 */
class tx_dam_allowdeny {


	var $conf = array();
	var $item = NULL;
	var $rules = array();

	var $mode;
	var $user;
	var $loginUser;
	var $usergroups;

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
	function tx_dam_allowdeny($items, $conf) {
		$this->__construct($items, $conf);
	}


	/**
	 * Constructor
	 *
	 * @param	array		$items Items that should be allowed/denied
	 * @param	array		$conf	The config array
	 * @return	void
	 */
	function __construct($items, $conf) {

		$this->conf = is_array($conf) ? $conf : array();

		if (is_array($items)) {
			$this->items = $items;
		} elseif ($items) {
			$items = implode(',', explode("\n", $items));
			$this->items = t3lib_div::trimExplode(',', $items, true);
		}

		$this->mode = TYPO3_MODE;
		if ($this->mode === 'FE') {
			$this->user = & $GLOBALS['TSFE']->fe_user->user['uid'];
			$this->isAdmin = false;
			$this->usergroups = $GLOBALS['TSFE']->gr_list;
			$this->loginUser = $GLOBALS['TSFE']->loginUser;
		} elseif ($this->mode === 'BE') {
			$this->user = & $GLOBALS['BE_USER']->user['uid'];
			$this->isAdmin = & $GLOBALS['BE_USER']->isAdmin();
			$this->usergroups = $GLOBALS['BE_USER']->user->usergroups;
			$this->loginUser = defined('TYPO3_cliMode') ? false : true;
		}

		$this->rules['allow'] = $this->_processRules('allow');
		$this->rules['deny'] = $this->_processRules('deny');

		if (!$this->conf['order']) {
			$this->conf['order'] = 'deny,allow';
		}
	}






	/***************************************
	 *
	 *	 Check
	 *
	 ***************************************/


	/**
	 * Check allow/deny
	 *
	 * @param string $item Item to check if is allowed
	 * @return boolean
	 */
	function isAllowed ($item) {

        $allowed = true;
        if ($this->conf['order'] === 'allow,deny') {
        	$allowed = false;
            if ($this->allowDeny('allow', $item)) {
                $allowed = true;
            }
            if ($this->allowDeny('deny', $item)) {
                $allowed = false;
            }
        } else if ($this->conf['order'] === 'deny,allow') {
            if ($this->allowDeny('deny', $item)) {
                $allowed = false;
            }
            if ($this->allowDeny('allow', $item)) {
                $allowed = true;
            }
        } elseif ($this->conf['order'] === 'explicit') {
            if ($this->allowDeny('allow', $item)
                && !$this->allowDeny('deny', $item)) {
                $allowed = true;
            } else {
                $allowed = false;
            }
        }
		return $allowed;
	}


	/**
	 * Return the list of allowed items as array
	 *
	 * @return array
	 */
	function getAllowed () {
		$itemList = array();
		if ($this->items) {
			foreach ($this->items as $item) {
				if ($this->isAllowed ($item)) {
					$itemList[] = $item;
				}
			}
		}

		return $itemList;
	}


	/**
	 * Return the list of allowed items as comma list
	 *
	 * @return string comma list
	 */
	function getAllowedList () {
		$itemList = '';
		if ($this->items) {
			$itemList = implode(',', $this->getAllowedArray());
		}

		return $itemList;
	}


	/**
	 * Runs through Allow/Deny rules
	 *
	 * @param   string $type 'allow' | 'deny' type of rule to match
	 * @param 	string $item Item to match
	 *
	 * @return  bool   Matched a rule ?
	 */
	function allowDeny($type, $item) {

	    foreach ($this->rules[$type] as $rule) {
	    		// check if the rule is for the current item
			if ($this->_evalCondition('item', $rule['item'], $item)) {
					// a rule with just not other condition - then it matches
				if (count($rule)==1) return true;
				unset($rule['item']);
		    	foreach ($rule as $type => $value) {
					if ($this->_evalCondition($type, $value, $item)) {
						return true;
					}
		    	}
		    }
	    }

	    return false;
	}


	/**
	 * Evaluates a condition
	 *
	 * @param	string		$type The condition type
	 * @param	string		$value The condition value to match
	 * @return	boolean		Returns true or false based on the evaluation.
	 */
	function _evalCondition($type, $matchList, $item)	{
		$type = trim($type);
		$matchList = str_replace(' ', '', trim($matchList));
		switch ($type) {
			case 'from':
				if (t3lib_div::inList($matchList, 'all')) 	{return true;}
				$values = t3lib_div::trimExplode(',',$matchList, true);
				foreach($values as $test)	{
					if (preg_match('#^[0-9.*]+$#', $test))	{
						if (t3lib_div::cmpIP(t3lib_div::getIndpEnv('REMOTE_ADDR'), $matchList))	{return true;}
					} else {
						if (t3lib_div::cmpFQDN(t3lib_div::getIndpEnv('REMOTE_ADDR'), $matchList))  {return true;}
					}
				}
			case 'user':
				if (t3lib_div::inList($matchList, '*')) 	{return true;}
				if ($this->loginUser)	{
					if (t3lib_div::inList($matchList, $this->user)) 	{return true;}
				}
			break;
			case 'usergroup':
				if (t3lib_div::inList($matchList, '*')) 	{return true;}
				if ($this->usergroups!='0,-1')	{		// '0,-1' is the default usergroupss when not logged in!
					$values = t3lib_div::trimExplode(',',$matchList, true);
					foreach($values as $test)	{
						if ($test=='*' || t3lib_div::inList($this->usergroups, $test))	{return true;}
					}
				}
			break;
			case 'admin':
				return (intval($matchList) AND $this->isAdmin);
			break;
			case 'item':
				if (t3lib_div::inList($matchList, '*')) 	{return true;}
				if (t3lib_div::inList($matchList, $item)) 	{return true;}
			break;
			case 'userFunc':
// todo: test userFunc
				$values = split('\(|\)',$matchList);
				$funcName=trim($values[0]);
				$funcValue = t3lib_div::trimExplode(',',$values[1]);
				if (function_exists($funcName) && call_user_func($funcName, $funcValue[0]))	{
					return true;
				}
			break;
		}

		return false;
	}



	/***************************************
	 *
	 *	 Tools
	 *
	 ***************************************/


	/**
	 * convert TS setup/config to internal setup
	 *
	 * @param string $type allow / deny
	 * @return array
	 */
	function _processRules($type) {
		$rules = array();
		if ($confArr = $this->conf[$type.'.']) {
	    	foreach ($confArr as $key => $conf) {
				$rules[] = $conf;
	    	}

		}

		if ($this->conf[$type]) {
			$rules[]['item'] = $this->conf[$type];
		}

		return $rules;
	}

}



/**
 * Allow/Deny iterator class
 * This helps to set allowed items in a list with multiple configurations
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage BaseClass
 */
class tx_dam_allowdeny_list {

	var $obj = array();

	/**
	 * Constructor
	 * This is more or less an iterator for tx_dam_allowdeny
	 * Multiple configurations can be passed
	 *
	 * @param	array		$items Items that should be allowed/denied
	 * @param	array		$conf1	The config array
	 * @param	array		$conf2	The config array
	 * @param	array		$conf3	...
	 * @return	void
	 */
	function tx_dam_allowdeny_list () {

		$numargs = func_num_args();

		if ($numargs > 1) {
			$arg_list = func_get_args();
			$items = $arg_list[0];

		    for ($i = 1; $i < $numargs; $i++) {
		    	if (is_array($arg_list[$i])) {
					$this->obj[] = new tx_dam_allowdeny($items, $arg_list[$i]);
		    	}
		    }
		}
	}


	/**
	 * Check allow/deny
	 *
	 * @param string $item Item to check if is allowed
	 * @return boolean
	 */
	function isAllowed ($item) {
		foreach ($this->obj as $key => $obj) {
			if (!$this->obj[$key]->isAllowed($item)) {
				return false;
			}
		}
		return true;
	}
	
	

	/***************************************
	 *
	 *	 Tools
	 *
	 ***************************************/


	/**
	 * convert TS setup/config to allowDeny setup when needed
	 * 
	 * Example:
	 *   tx_dam_action_viewFileRec = 0
	 *   tx_dam_action_editRec = 0
	 * 
	 * will be converted to
	 *   order = allow,deny 
  	 *   allow = * 
  	 *   deny = ,tx_dam_action_viewFileRec,tx_dam_action_editRec
	 *
	 * @param array $setupAllowDeny TS that includes 'allowDeny.' or just a simple list of keys and values
	 * @return array
	 */	
	function transformSimpleSetup ($setupAllowDeny) {
		if (is_array($setupAllowDeny['allowDeny.'])) {
			$setupAllowDenyResult = $setupAllowDeny['allowDeny.'];
		} elseif (is_array($setupAllowDeny)) {
			$setupAllowDenyResult = array (
						'order' => 'allow,deny',
						'allow' => '*',
						'deny' => '',
			);
			foreach ($setupAllowDeny as $name => $value) {
				if (!tx_dam_config::isEnabled($value)) {
					$setupAllowDenyResult['deny'] .= ','.$name;
				}
			}
		}
		return $setupAllowDenyResult;
	}	
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_allowdeny.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_allowdeny.php']);
}
?>