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

//if (defined(PATH_tslib)) {
	require_once(PATH_tslib.'class.tslib_pibase.php');
//} else {
//	require_once('sysext/cms/tslib/class.tslib_pibase.php');
//}


/**
 * Plugin 'PiBase' for the 'ew_pibase' extension.
 * Extends the PiBase Functions
 *
 * @author	Elio Wahlen <vorname at vorname punkt de>
 * @package	TYPO3
 * @subpackage	tx_ewpibase
 */
class tx_ewpibase extends tslib_pibase {
	var $prefixId      = 'tx_ewpibase';		// Same as class name
	var $scriptRelPath = 'class.tx_ewpibase.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ew_pibase';	// The extension key.
	var $pi_checkCHash = true;
	var $templateFile;
	var $templateCode;
	var $markerArray = array();
	
	
	// Template Parsing Functions
	function loadTemplate($templateFile) {
		$this->templateFile = 'EXT:' . $this->extKey . '/' . $templateFile;
		$this->templateCode = $this->cObj->fileResource($this->templateFile);
	}
	
	function addMarker( $marker, $value ) {
		$this->markerArray['###'.strtoupper($marker).'###'] = $value;
	}
	
	function renderSubpart( $subpart ) {
        // Extract subparts from the template
        $subpart = $this->cObj->getSubpart($this->templateCode  , '###'.strtoupper($subpart).'###');
		$subpart = $this->cObj->substituteMarkerArray($subpart,$this->markerArray);
		return $subpart;
	}
	
	function get_dam_images( $id, $ident, $table ) {
		$table = empty($table) ? 'tt_content' : $table;
		$where = array();
		$images = tx_dam_db::getReferencedFiles($table, $id, $ident, 'tx_dam_mm_ref', 'tx_dam.*,tx_dam_mm_ref.*', $where, '', 'tx_dam_mm_ref.ident, tx_dam_mm_ref.sorting_foreign');
		// generate a better readable list
		$better_list = array();
		foreach( $images['rows'] as $uid => $image ) {
			$better_list[$uid] = $image;
			$better_list[$uid]['path'] = $image['file_path'].$image['file_name'];
		}
		return $better_list;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_pibase/class.tx_ewpibase_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_pibase/class.tx_ewpibase_pi1.php']);
}

?>