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
 * Update the media types table for browsing
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage DB-Trigger
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   56: class tx_dam_dbTriggerMediaTypes
 *   65:     function insertMetaTrigger($meta)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




/**
 * Update the media types table for browsing
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage DB-Trigger
 */
class tx_dam_dbTriggerMediaTypes  {

	/**
	 * Update the media types table for browsing
	 * Will be called when a meta data record was inserted.
	 *
	 * @param	array		$meta meta data. $meta['media_type'] and $meta['file_type'] have to be set
	 * @return	void
	 */
	function insertMetaTrigger($meta)	{

		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];

		$mediaType = intval($meta['media_type']);

			// check if media type exists
		if ($typeStr = tx_dam::convert_mediaType($mediaType)) {

				// get the id of the media type record
			$media_id = false;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam_metypes_avail', 'type='.$GLOBALS['TYPO3_DB']->fullQuoteStr($mediaType,'tx_dam_metypes_avail'));

			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$media_id = $row['uid'];
			}
				// no record - then create one
			if (!$media_id) {
				$sorting = $TX_DAM['code2sorting'][$mediaType];
				$sorting = $sorting ? $sorting : 10000;

				$fields_values = array();
				$fields_values['pid'] = tx_dam_db::getPid();
				$fields_values['parent_id'] = 0;
				$fields_values['tstamp'] = time();
				$fields_values['title'] = $typeStr;
				$fields_values['type'] = $mediaType;
				$fields_values['sorting'] = $sorting;
				$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_metypes_avail', $fields_values);
				$media_id = $GLOBALS['TYPO3_DB']->sql_insert_id();
			}

				// get file type record
			$type_id = false;
			if ($media_id) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam_metypes_avail', 'title='.$GLOBALS['TYPO3_DB']->fullQuoteStr($meta['file_type'],'tx_dam_metypes_avail').' AND parent_id='.$GLOBALS['TYPO3_DB']->fullQuoteStr($media_id,'tx_dam_metypes_avail'));
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
					$type_id = $row['uid'];
				}
			}
				// no record - then create one
			if (!$type_id) {
				$fields_values = array();
				$fields_values['pid'] = tx_dam_db::getPid();
				$fields_values['parent_id'] = $media_id;
				$fields_values['tstamp'] = time();
				$fields_values['title'] = $meta['file_type'] ? $meta['file_type'] : 'n/a';
				$fields_values['type'] = $mediaType;
				$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_metypes_avail', $fields_values);
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_dbTriggerMediaTypes.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_dbTriggerMediaTypes.php']);
}
?>
