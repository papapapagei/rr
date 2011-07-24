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
 * Base class for index rule plugins for the DAM.
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
 *   62: class tx_dam_indexRuleBase
 *   85:     function getTitle()
 *   94:     function getDescription()
 *  103:     function getOptionsForm()
 *  113:     function processOptionsForm()
 *  122:     function getOptionsInfo()
 *  132:     function preIndexing()
 *  148:     function postIndexing($indexedList=array())
 *  162:     function processMeta($meta, $absFile)
 *  173:     function postProcessMeta($meta, $absFile)
 *  181:     function getEnabledIcon()
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


/**
 * Base class for index rule plugins for the DAM
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage BaseClass
 */
class tx_dam_indexRuleBase {

	/**
	 * all data from the form
	 * [enabled] is reserved
	 * [shy] is reserved
	 *
	 * This set a index rule not to be shown but always enabled:
	 *
	 *	var $setup = array(
	 *		'enabled' => true,
	 *		'shy' => true,
	 *		);
	 */
	var $setup = array();



	/**
	 * Returns the title of the index rule
	 *
	 * @return	string	Title
	 */
	function getTitle()	{
		return 'No title';
	}

	/**
	 * Returns the description of the index rule
	 *
	 * @return	string	Description
	 */
	function getDescription()	{
		return '';
	}

	/**
	 * Returns the options form
	 *
	 * @return	string	HTML content
	 */
	function getOptionsForm()	{
		return '';
	}

	/**
	 * Can be used to process the form values.
	 * Results have to be stored in $this->setup.
	 *
	 * @return	void
	 */
	function processOptionsForm()	{
	}

	/**
	 * Returns some information what options are selected.
	 * This is for user feedback.
	 *
	 * @return	string	HTML content
	 */
	function getOptionsInfo()	{
		return '';
	}

	/**
	 * Will be called before the indexing.
	 * Can be used to initialize things
	 *
	 * @return	void
	 */
	function preIndexing()	{
	}


	/**
	 * Will be called after the indexing.
	 *
	 *	$indexedList[] = array(
	 *		'uid' => $meta['fields']['uid'],
	 *		'title' => $meta['fields']['title'],
	 *		'reindexed' => $meta['reindexed'],
	 *		);
	 *
	 * @param	array		List of meta record uid's of newly indexed files
	 * @return	void
	 */
	function postIndexing($indexedList=array())	{

	}


/* 	will be called if exists

	/ **
	 * For processing the meta data BEFORE the index is written
	 *
	 * @param	array		$meta Meta data array
	 * @param	string		$absFile Filename
	 * @return	array Processed meta data array
	 * /
	function processMeta($meta, $absFile)	{
		return $meta;
	}

	/ **
	 * For processing the meta data AFTER the index was written
	 *
	 * @param	array		$meta Meta data array
	 * @param	string		$absFile Filename
	 * @return	array Processed meta data array
	 * /
	function postProcessMeta($meta, $absFile)	{
		return $meta;
	}
*/

	/**
	 * @access private
	 */
	function getEnabledIcon() {
		return '&bull;&nbsp;';
	}

}

// No XCLASS inclusion code: this is a base class
//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexrulebase.php'])    {
//    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexrulebase.php']);
//}

?>