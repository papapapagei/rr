<?php
/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *
 * $Id: Tx_Formhandler_Configuration.php 35676 2010-07-15 09:26:51Z reinhardfuehricht $
 *                                                                        */

/**
 * The configuration of the Formhandler
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	Controller
 */
class Tx_Formhandler_Configuration implements ArrayAccess {

	/**
	 * The package key
	 *
	 * @var string
	 */
	const PACKAGE_KEY = 'Formhandler';

	/**
	 * The TS setup
	 *
	 * @access protected
	 * @var array
	 */
	protected $setup;

	/**
	 * The constructor reading the TS setup into the according attribute
	 *
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @return void
	 */
	public function __construct() {
		if(TYPO3_MODE === 'FE') {
			$this->setup = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->getPrefixedPackageKey() . '.'];
			if(!is_array($this->setup)) {
				Tx_Formhandler_StaticFuncs::throwException('missing_config');
			}
			if(is_array(Tx_Formhandler_Globals::$overrideSettings)) {
				$this->setup = t3lib_div::array_merge_recursive_overrule($this->setup, Tx_Formhandler_Globals::$overrideSettings);
			}
		}
	}

	/**
	 * Merges the values of $setup with plugin.[xxx].settings
	 *
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @param array $setup
	 * @return void
	 */
	public function merge($setup) {
		if (isset($setup) && is_array($setup)) {
			$settings = $this->setup['settings.'];
			$settings = t3lib_div::array_merge_recursive_overrule($settings, $setup);
			$this->setup['settings.'] = $settings;
		}
	}

	public function offsetGet($offset) {
		return $this->setup['settings.'][$offset];
	}

	public function offsetSet($offset, $value) {
		$this->setup['settings.'][$offset] = $value;
	}

	public function offsetExists($offset) {
		if (isset($this->setup['settings.'][$offset])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function offsetUnset($offset) {
		$this->setup['settings.'][$offset] = NULL;
	}

	/**
	 * Returns the TS settings for formhandler.
	 *
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @return array The settings
	 */
	public function getSettings() {
		return $this->setup['settings.'];
	}

	/**
	 * Returns the sources config for formhandler
	 *
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @return array The config
	 */
	public function getSourcesConfiguration() {
		return $this->setup['sources.'];
	}

	/**
	 * Returns the package key
	 *
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @return string
	 */
	public function getPackageKey() {
		return self::PACKAGE_KEY;
	}

	/**
	 * Returns the package key in lower case
	 *
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @return string
	 */
	public function getPackageKeyLowercase() {
		return strtolower(self::PACKAGE_KEY);
	}

	/**
	 * Returns the prefixed package key
	 *
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @return string
	 */
	public function getPrefixedPackageKey() {
		return Tx_GimmeFive_Component_Manager::PACKAGE_PREFIX . '_' . self::PACKAGE_KEY;
	}

	/**
	 * Returns the prefixed package key in lower case
	 *
	 * @author Reinhard Führicht <rf@typoheads.at>
	 * @return string
	 */
	public function getPrefixedPackageKeyLowercase() {
		return strtolower(Tx_GimmeFive_Component_Manager::PACKAGE_PREFIX . '_' . self::PACKAGE_KEY);
	}
}
?>
