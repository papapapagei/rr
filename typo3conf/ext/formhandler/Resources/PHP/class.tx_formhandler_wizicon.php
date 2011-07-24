<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabien Udriot (fabien@ecodev.ch)
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
 * Adds the wizard icon.
 *
 * @author	Fabien Udriot <fabien@udriot.net>
 */
class tx_formhandler_wizicon {
	
	/**
	 * Adds the formhandler wizard icon
	 *
	 * @param	array		Input array with wizard items for plugins
	 * @return	array		Modified input array, having the item for formhandler
	 * pi1 added.
	 */
	function proc($wizardItems)	{
		global $LANG;

		$LL = $this->includeLocalLang();

		$wizardItems['plugins_tx_formhandler_pi1'] = array(
			'icon'        => t3lib_extMgm::extRelPath('formhandler').'Resources/Images/ce_wiz_pi1.png',
			'title'       => $LANG->getLLL('wizard_pi1.title',$LL),
			'description' => $LANG->getLLL('tt_content.pi1_plus_wiz_description',$LL),
			'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=formhandler_pi1'
		);
		
		$wizardItems['plugins_tx_formhandler_pi2'] = array(
			'icon'        => t3lib_extMgm::extRelPath('formhandler').'Resources/Images/ce_wiz_pi2.png',
			'title'       => $LANG->getLLL('wizard_pi2.title',$LL),
			'description' => $LANG->getLLL('tt_content.pi2_plus_wiz_description',$LL),
			'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=formhandler_pi2'
		);

		return $wizardItems;
	}
	
	/**
	 * Includes the locallang file for the 'formhandler' extension
	 *
	 * @return	array		The LOCAL_LANG array
	 */
	function includeLocalLang()	{
		$llFile     = t3lib_extMgm::extPath('formhandler').'Resources/Language/locallang_db.xml';
		$LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS['LANG']->lang);
		
		return $LOCAL_LANG;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formhandler/Resources/PHP/class.tx_formhandler_wizicon.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formhandler/Resources/PHP/class.tx_formhandler_wizicon.php']);
}

?>