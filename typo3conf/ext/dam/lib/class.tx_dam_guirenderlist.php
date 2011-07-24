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
 * Part of the DAM (digital asset management) extension.
 *
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
 *   69: class tx_dam_guiRenderList
 *
 *              SECTION: Register
 *  106:     function clear($type)
 *  122:     function registerFunc($funcDef, $type, $argArr=array(), $position='')
 *  212:     function setParams($func, $argArr=array())
 *
 *              SECTION: Output
 *  244:     function getOutput($type='footer', $itemList='')
 *
 *              SECTION: Call function helper
 *  292:     function callUserFunction($func, &$obj)
 *  313:     function items_callFunc($funcDef)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */






/**
 * Methods can be registered in a list and later be rendered.
 * Used to render header and footer which can be registered by submodules.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_guiRenderList {


	/**
	 * Items to output before module output
	 */
	var $items_header = array();

	/**
	 * Items to output after module output
	 */
	var $items_footer = array();

	/**
	 * Configuration parameters for output items
	 */
	var $items_params = array();
	var $items_params_override = array();






	/********************************
	 *
	 * Register
	 *
	 ********************************/


	/**
	 * Clears all registered gui items
	 *
	 * @param	string		Type name: header, footer
	 * @return	void
	 */
	function clear($type)	{
		$type = 'items_'.$type;
		if(!is_array($this->$type)) return;
		$this->$type = array();
	}


	/**
	 * Register a gui function
	 *
	 * @param	mixed		Name of the function or an array like array(&$object, 'function_name')
	 * @param	string		Type name: header, footer
	 * @param	array		Array of parameters which should be passed to the function
	 * @param	string		$position can be used to set the position of the item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or blank which is default: bottom).
	 * @return	void
	 */
	function registerFunc($funcDef, $type, $argArr=array(), $position='')	{
		
		
		
		
		
		if (is_array($funcDef)) {
			$obj = $funcDef[0];
			$func = $funcDef[1];
			if (!is_object($obj)) {
				$obj = NULL;
			}
		} else {
			$obj = NULL;
			$func = $funcDef;
		}

		
		$GLOBALS['SOBE']->develAvailableGuiItems[$func] = $func;
	
		if (is_callable(array($GLOBALS['SOBE'], 'config_checkValueEnabled'))) {
// TODO this doesn't work with EB
			if (!$GLOBALS['SOBE']->config_checkValueEnabled('guiElements.'.$func, true)) return;
		}
		
		$prefix = is_object($obj) ? get_class($obj).'>' : '';


		$type = 'items_'.$type;
		if(!is_array($this->$type)) return;
		$itemArr = &$this->$type;

		$argArr = is_array($argArr) ? $argArr : array();
		$this->items_params[$prefix.$func] = $argArr;

		$newItem = array();
		$newItem[$prefix.$func] = array('obj'=>&$obj, 'func' => $func);

		$pointer = count($itemArr);
		if($position) {

			$posArr = t3lib_div::trimExplode(',', $position, 1);
			foreach($posArr as $pos) {
				list($place, $itemEntry) = t3lib_div::trimExplode(':', $pos, 1);

					// bottom
				$pointer = count($itemArr);

				$found=FALSE;

				if ($place) {
					switch(strtolower($place))	{
						case 'after':
						case 'before':
							if ($itemEntry) {
								$p=1;
								reset ($itemArr);
								while (true) {
									if (!strcmp(key($itemArr), $itemEntry))	{
										$pointer = $p;
										$found=TRUE;
										break;
									}
									if (!next($itemArr)) break;
									$p++;
								}
								if (!$found) break;

								if ($place === 'before') {
									$pointer--;
								} elseif ($place === 'after') {
								}
							}
						break;
						case 'top':
							$pointer = 0;
							$found=TRUE;
						break;
						default:
							$pointer = count($itemArr);
							$found=TRUE;
						break;
					}
				}
				if($found) break;
			}
		}

		$pointer=max(0, $pointer);
		$itemsBefore = array_slice($itemArr, 0, ($pointer?$pointer:0));
		$itemsAfter = array_slice($itemArr, $pointer);
		$itemArr = $itemsBefore + $newItem + $itemsAfter;

	}


	/**
	 * Set (override) parameters for a registered  gui function
	 *
	 * @param	mixed		Name of the user function or an array like array($object, 'function_name')
	 * @param	array		Array of parameters which should be passed to the function
	 * @return	void
	 */
	function setParams($func, $argArr=array())	{
		if (is_array($func)) {
			list($obj, $func) = each($func);
			if (!is_object($obj)) {
				$obj = NULL;
			}
		} else {
			$obj = NULL;
			$func = $func;
		}
		$prefix = is_object($obj) ? get_class($obj).'>' : '';
		$this->items_params[$prefix.$func] = $argArr;
	}





	/********************************
	 *
	 * Output
	 *
	 ********************************/


	/**
	 * Call gui item functions and return the output
	 *
	 * @param	string		Type name: header, footer
	 * @param	string		List of item function which should be called instead of the default defined
	 * @return	string		Items output
	 */
	function getOutput($type='footer', $itemList='')	{

		if (is_null($itemList)) return;

		if($itemList) {
			$itemListArr = t3lib_div::trimExplode(',', $itemList, 1);
		} else {
			$type = 'items_'.$type;

			if(!is_array($this->$type)) return;
			$itemListArr = array_keys($this->$type);
		}
		$elementList = & $this->$type;

		$out = '';
		foreach ($itemListArr as $item) {
			$content = $this->items_callFunc($elementList[$item]);
			$out .= '
				<!-- GUI element section: '.htmlspecialchars($item).' -->
					'.$content.'
				<!-- GUI element section end -->';
		}

		if ($type === 'items_footer' AND is_array($GLOBALS['SOBE']->debugContent) AND tx_dam::config_getValue('setup.devel')) {
						
			$content = '<div class="itemsFooter">'.'<h4>GUI Elements</h4>'.t3lib_div::view_array($GLOBALS['SOBE']->develAvailableGuiItems).'</div>';
			$content .= '<div class="itemsFooter">'.'<h4>Options</h4>'.t3lib_div::view_array($GLOBALS['SOBE']->develAvailableOptions).'</div>';
			$content .= '<div class="itemsFooter">'.'<h4>Registered actions (all)</h4>'.t3lib_div::view_array(array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['actionClasses'])).'</div>';
			
			$out.= $GLOBALS['SOBE']->buttonToggleDisplay('devel', 'Module Info', $content);
			
			$content = '<div class="itemsFooter">'.implode('', $GLOBALS['SOBE']->debugContent).'</div>';
			$out.= $GLOBALS['SOBE']->buttonToggleDisplay('debug', 'Debug output', $content);
		}

		return $out;
	}





	/********************************
	 *
	 * Call function helper
	 *
	 ********************************/


	/**
	 * Call a user func with variable parameter list
	 *
	 * @param	string		Name of the user function
	 * @param	string		Already existing object or NULL (using $this)
	 * @return	mixed		Function output
	 */
	function callUserFunction($func, &$obj)	{

		if (!is_object($obj)) {
			#$obj = &$this;
			$obj = &$GLOBALS['SOBE'];
		}
		if (@is_callable(array($obj, $func)))	{
			$arg_list = func_get_args();
			unset($arg_list[0]); //$func
			unset($arg_list[1]); //$obj
			return call_user_func_array(array($obj, $func), $arg_list);
		}
	}


	/**
	 * Call a gui function
	 *
	 * @param	mixed		Name of the user function or an array like array($object, 'function_name')
	 * @return	mixed		Function output
	 */
	function items_callFunc($funcDef)	{
		if (is_array($funcDef)) {
			$obj = $funcDef['obj'];
			$func = $funcDef['func'];
			if (!is_object($obj)) {
				$obj = NULL;
			}
		} else {
			$obj = NULL;
			$func = $funcDef;
		}

		$arg_list = array($func, &$obj);

		$prefix = is_object($obj) ? get_class($obj).'>' : '';

		if (is_array($this->items_params_override[$prefix.$func])) {
			$arg_list = $arg_list + $this->items_params_override[$prefix.$func];
		} elseif (is_array($this->items_params[$prefix.$func])) {
			$arg_list = array_merge($arg_list, $this->items_params[$prefix.$func]);
		}

		return call_user_func_array(array($this, 'callUserFunction'), $arg_list);
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_guirenderlist.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_guirenderlist.php']);
}

?>