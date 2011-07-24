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
 * Contains the editor base class.
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
 *   64: class tx_dam_editorProcBase
 *   75:     function isValid($row, $size, $type, $conf=array())
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

/**
 * Contains the editor base class.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_editorBase extends t3lib_extobjbase {

	/**
	 * Returns true if this editor is able to handle the given file
	 *
	 * @param	mixed		$media Media object or itemInfo array. Currently the function have to work with a media object or an itemInfo array.
	 * @param	array		$conf Additional configuration values. Might be empty.
	 * @return	boolean		True if this is the right editor for the file
	 */
	function isValid ($media, $conf=array()) {
		return false;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		$iconFile = 'gfx/edit_file.gif';
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return '';
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return '';
	}


	/**
	 * Prepend a space to an tag attribute
	 *
	 * @param	string		$attribute
	 * @return	string
	 * @access private
	 */
	function _cleanAttribute($attribute) {
		return ($attribute ? ' '.$attribute : '');
	}
}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_editorbase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_editorbase.php']);
}
?>