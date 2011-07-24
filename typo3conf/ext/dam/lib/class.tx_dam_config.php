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
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */





/**
 * Configuration class
 * This helps to get and set TSconfig style setup
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage BaseClass
 */
class tx_dam_config {



	/**
	 * Init dam config values - which means they are fetched from TSConfig
	 *
	 * @param	boolean $force Will force the initialization to be done again except definedTSconfig set by config_setValue
	 * @return void
	 */
	function init($force=false) {

		$config = & $GLOBALS['T3_VAR']['ext']['dam']['config'];

		$perfomMerge = false;
		if(!is_array($config)) {
			$config = array();
			$config['mergedTSconfig.'] = array();
			$config['definedTSconfig.'] = array();
		}
		if(($force OR !is_array($config['userTSconfig.'])) AND ($TSconfig = tx_dam_config::_getTSconfig())) {
			$config['pageUserTSconfig.'] = $config['userTSconfig.'] = $TSconfig;
			$perfomMerge = true;
		}

		if($force OR !is_array($config['pageTSconfig.'])) {
			if ($pid = tx_dam_db::getPid() AND ($TSconfig = tx_dam_config::_getTSconfig($pid))) {
				$config['pageTSconfig.'] = $TSconfig;
				$config['pageUserTSconfig.'] = t3lib_div::array_merge_recursive_overrule((array)$config['pageTSconfig.'], (array)$config['userTSconfig.']);
				$perfomMerge = true;
			}
		}

		if ($perfomMerge) {
			$config['mergedTSconfig.'] = t3lib_div::array_merge_recursive_overrule((array)$config['pageUserTSconfig.'], (array)$config['definedTSconfig.']);
		}
	}



	/***************************************
	 *
	 *   Configuration
	 *
	 ***************************************/



	/**
	 * Return configuration values which are mainly defined by TSconfig.
	 * The configPath must begin with "setup." or "mod."
	 * "setup" is mapped to tx_dam TSConfig key.
	 *
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'setup.selections.default'
	 * @param	boolean		$getProperties return the properties array instead of the value. Means to return the stuff set by a dot. Eg. setup.xxxx.xxx
	 * @return	mixed		Just the value or when $getProperties is set an array with the properties of the $configPath.
	 */
	function getValue($configPath='', $getProperties=false) {
		$configValues = false;

		$config = $GLOBALS['T3_VAR']['ext']['dam']['config'];

		if(!is_array($config)) {
			tx_dam_config::init();
		}

		if ($configPath) {
			$configValues = tx_dam_config::_getTSConfigObject($configPath, $config['mergedTSconfig.']);
		}

		if ($getProperties) {
			$configValues = $configValues['properties'];
		} else {
			$configValues = $configValues['value'];
		}

		return $configValues;
	}
	
	
	/**
	 * Check a config value if its enabled
	 * Anything except '' and 0 is true
	 * If the the option is not set the default value will be returned
	 *
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'setup.selections.default'
	 * @param	mixed 		$default Default value when option is not set, otherwise the value itself
	 * @return boolean
	 */
	function checkValueEnabled($configPath, $default=false) {
		
		$parts = t3lib_div::revExplode('.', $configPath, 2);
		$config = tx_dam_config::getValue($parts[0], true);
		return tx_dam_config::isEnabledOption($config, $parts[1], $default);
	}
	
	
	/**
	 * Set a dam config value
	 * The config path must begin with "setup." or "mod."
	 * "setup" is mapped to tx_dam TSConfig key.
	 *
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'setup.selections.default'
	 * @param	mixed 		$value Value to be set. Can be an array but must be in TSConfig format
	 * @return void
	 * @todo map user setup/options to dam setup?
	 */
	function setValue($configPath='', $value='') {

		$config = & $GLOBALS['T3_VAR']['ext']['dam']['config'];

		$perfomMerge = false;
		if(!is_array($config)) {
			tx_dam_config::init();
		}

		if ($configPath) {
			list ($baseKey, $options) = explode('.', $configPath, 2);
			$options = explode('.', $options);
			$lastOption = count ($options);
			if (!is_array($config['definedTSconfig.'][$baseKey.'.'])) {
				$config['definedTSconfig.'][$baseKey.'.'] = array();
			}
			$optionArrPath = & $config['definedTSconfig.'][$baseKey.'.'];
			$optCount = 0;
			foreach ($options as $optionValue) {
				$optCount++;
				if ($optCount < $lastOption) {
					$optionArrPath = & $optionArrPath[$optionValue.'.'];
				} else {
					$optionArrPath = & $optionArrPath[$optionValue.(is_array($value)?'.':'')];
				}

			}
			$optionArrPath = $value;
			$perfomMerge = true;
		}
		if ($perfomMerge) {
			$config['mergedTSconfig.'] = t3lib_div::array_merge_recursive_overrule((array)$config['pageUserTSconfig.'], (array)$config['definedTSconfig.']);
		}
	}


	/**
	 * Check a config value if its enabled
	 * Anything except '' and 0 is true
	 *
	 * @param	mixed 		$value Value to be checked
	 * @return mixed	Return false if value is empty or 0, otherwise the value itself
	 */
	function isEnabled($value) {
		if (t3lib_div::testInt($value)) {
			return intval($value) ? intval($value) : false;
		}
		return empty($value) ? false : $value;
	}
	
	
	/**
	 * Check a config value if its enabled
	 * Anything except '' and 0 is true
	 *
	 * @param	array 		$config Configuration array
	 * @param	string 		$option Option key. If not set in $config default value will be returned
	 * @param	mixed 		$default Default value when option is not set, otherwise the value itself
	 * @return boolean
	 */
	function isEnabledOption($config, $option, $default=false) {
	
		if (!isset($config[$option])) return $default;
		return tx_dam_config::isEnabled($config[$option]);
	}









	/***************************************
	 *
	 *   Internal
	 *
	 ***************************************/



	/**
	 * Return full internal configuration array
	 *
	 * @return	array
	 */
	function _getConfig() {

		if(!is_array($GLOBALS['T3_VAR']['ext']['dam']['config'])) {
			tx_dam_config::init();
		}

		return $GLOBALS['T3_VAR']['ext']['dam']['config'];
	}
	

	/**
	 * get TSConfig values for initialization
	 *
	 * @access private
	 * @param integer $pid If set page TSConfig will be fetched otherwise user TSConfig
	 * @return array
	 */
	function _getTSconfig ($pid=0) {
		global $TYPO3_CONF_VARS;

		$values = false;

		if (TYPO3_MODE === 'FE' AND is_object($GLOBALS['TSFE'])) {
			$TSconfig = '';
			if ($pid) {
				$TSconfig = $GLOBALS['TSFE']->getPagesTSconfig($pid);
			} else {
				$TSconfig = $GLOBALS['TSFE']->fe_user->getUserTSconf();
			}

				// get global config
			$TSConfValues = tx_dam_config::_getTSConfigObject('tx_dam', $TSconfig);
			$global = $TSConfValues['properties'];

				// get plugin config
			$plugin = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_dam.'];

			$values = array('setup.' => $global, 'plugin.' => $plugin);

			// mod. properties are not used for FE


		} elseif (is_object($GLOBALS['BE_USER'])) {
			$TSconfig = '';
			if ($pid) {
				require_once(PATH_t3lib.'class.t3lib_befunc.php');
				$TSconfig = t3lib_BEfunc::getPagesTSconfig($pid);
			}

				// get global config
			$TSConfValues = $GLOBALS['BE_USER']->getTSConfig('tx_dam', $TSconfig);
			$global = $TSConfValues['properties'];

				// get mod config of dam_* modules
			$TSConfValues = $GLOBALS['BE_USER']->getTSConfig('mod', $TSconfig);
			if (is_array($mod = $TSConfValues['properties'])) {
				foreach($mod as $key => $value) {
					if (!(substr($key, 0, 7)=='txdamM1')) {
						unset($mod[$key]);
					}
				}
			}
			$values = array('setup.' => $global, 'mod.' => $mod);
		}

		return $values;
	}


	/**
	 * Returns the value/properties of a TS-object as given by $objectString, eg. 'options.dontMountAdminMounts'
	 * Nice (general!) function for returning a part of a TypoScript array!
	 *
	 * @param	string		$objectString Pointer to an "object" in the TypoScript array, fx. 'options.dontMountAdminMounts'
	 * @param	array		$config TSconfig array
	 * @return	array		An array with two keys, "value" and "properties" where "value" is a string with the value of the object string and "properties" is an array with the properties of the object string.
	 */
	function _getTSConfigObject($objectString, $config)	{

		$TSConf=array();
		$parts = explode('.',$objectString,2);
		$key = $parts[0];
		if (trim($key))	{
			if (count($parts)>1 && trim($parts[1]))	{
				// Go on, get the next level
				if (is_array($config[$key.'.']))	$TSConf = tx_dam_config::_getTSConfigObject($parts[1],$config[$key.'.']);
			} else {
				$TSConf['value']=$config[$key];
				$TSConf['properties']=$config[$key.'.'];
			}
		}
		return $TSConf;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_config.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_config.php']);
}
?>