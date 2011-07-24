<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Elio Wahlen <vorname at vorname punkt de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(t3lib_extMgm::extPath('ew_pibase'). 'class.tx_ewpibase.php');


/**
 * Plugin 'Production Facts' for the 'ew_facts' extension.
 *
 * @author	Elio Wahlen <vorname at vorname punkt de>
 * @package	TYPO3
 * @subpackage	tx_ewfacts
 */
class tx_ewfacts_pi1 extends tx_ewpibase {
	var $prefixId      = 'tx_ewfacts_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_ewfacts_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ew_facts';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{
		$this->conf = $conf;
		$this->pi_loadLL();
		$this->loadTemplate('pi1/template.html');

		$content = $this->renderFacts();
		
		return $content;
	}
	
	
	function renderFacts() {
		$facts_data = $this->cObj->data['bodytext'];
		$facts = preg_split('/\n/',$facts_data);
		$factList = '';
		$i = 0;
		foreach( $facts as $fact ) {
			$i++;
			$delimiterPos = strpos($fact,':');
			if ( $delimiterPos === false ) {
				$label = $fact;
				$value = '';
			} else {
				$label = substr( $fact, 0, $delimiterPos);
				$value = substr( $fact, $delimiterPos+1);
			}
			$this->addMarker( 'NO', $i );
			$this->addMarker( 'FACT_LABEL', $label );
			$this->addMarker( 'FACT_VALUE', $value );
			$factList .= $this->renderSubpart( 'ITEM' );
		}
		$this->addMarker( 'ITEMS', $factList );
		$content = $this->renderSubpart( 'FACTS' );
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_facts/pi1/class.tx_ewfacts_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_facts/pi1/class.tx_ewfacts_pi1.php']);
}

?>