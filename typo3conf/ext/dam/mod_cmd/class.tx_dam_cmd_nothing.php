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
 * Command module 'no command'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   56: class tx_dam_cmd_nothing extends t3lib_extobjbase
 *   64:     function head()
 *   76:     function main()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * "no command" module
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 */
class tx_dam_cmd_nothing extends t3lib_extobjbase {


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global $LANG, $BACK_PATH, $TYPO3_CONF_VARS;

		$GLOBALS['SOBE']->pageTitle = $LANG->getLL('tx_dam_cmd_nothing.title');
	}


	/**
	 * Main function
	 *
	 * @return	void
	 */
	function main()	{
		global $LANG;

		$content ='';


		$content.= $this->pObj->wrongCommandMessageBox();

		$content.= '<br /><br />'.$this->pObj->btn_back('',$this->pObj->returnUrl);


			// CSH:
#		$content.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_rename', $GLOBALS['BACK_PATH'],'<br/>');


		return $content;

	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_nothing.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_nothing.php']);
}


?>