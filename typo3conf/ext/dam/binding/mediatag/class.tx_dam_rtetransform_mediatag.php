<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2007 Rene Fritz (r.fritz@colorcube.de)
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
 *
 *
 */


class tx_dam_rtetransform_mediatag {

	var $transformationKey;
	
	/**
	 * Transformation handler: 'txdam_media' / direction: "db"
	 * Processing linked images inserted in the RTE.
	 * This is used when content goes from the RTE to the database.
	 * Images inserted in the RTE has an absolute URL applied to the src attribute. This URL is converted to a media uid
	 *
	 * @param	string		The content from RTE going to Database
	 * @return	string		Processed content
	 */
	function transform_db($value, &$pObj) {
				
		return $value;
	}
	
	/**
	 * Transformation handler: 'txdam_media' / direction: "rte"
	 * Processing linked images from database content going into the RTE.
	 * Processing includes converting the src attribute to an absolute URL.
	 *
	 * @param	string		Content input
	 * @return	string		Content output
	 */	
	function transform_rte($value, &$pObj) {
				
		
			// Split content by the TYPO3 pseudo tag "<media>":
		$blockSplit = $pObj->splitIntoBlock('media',$value,1);

		foreach($blockSplit as $k => $v)	{
			$error = '';
			if ($k%2)	{	// block:
				$tagCode = t3lib_div::unQuoteFilenames(trim(substr($pObj->getFirstTag($v),0,-1)),true);
				$link_param = $tagCode[1];
				$href = '';
				$useDAMColumn = FALSE;


				// Checking if the id-parameter is int and get meta data
				if (t3lib_div::testInt($link_param))	{
					$meta = tx_dam::meta_getDataByUid($link_param);
				}
				
				if (is_array($meta))	{
					$href = tx_dam::file_url($meta);
					
					if (!$tagCode[4]) {
						require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');
						$displayItems = '';
						if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'], 'usedamcolumn') && $pObj->thisConfig['buttons.']['link.']['media.']['properties.']['title.']['useDAMColumn']) {
							$displayItems = $pObj->thisConfig['buttons.']['link.']['media.']['properties.']['title.']['useDAMColumn.']['displayItems'] ? $pObj->thisConfig['buttons.']['link.']['media.']['properties.']['title.']['useDAMColumn.']['displayItems'] : '';
							$useDAMColumn = TRUE;
						}
						$tagCode[4] = tx_dam_guiFunc::meta_compileHoverText($meta, $displayItems, ', ');
					}
				} else {
					$href = $link_param;
					$error = 'No media file found: '.$link_param;
				}

				// Setting the A-tag:
				$bTag = '<a href="'.htmlspecialchars($href).'" txdam="'.htmlspecialchars($link_param).'"'.
							($tagCode[2]&&$tagCode[2]!='-' ? ' target="'.htmlspecialchars($tagCode[2]).'"' : '').
							($tagCode[3]&&$tagCode[3]!='-' ? ' class="'.htmlspecialchars($tagCode[3]).'"' : '').
							($tagCode[4] ? ' title="'.htmlspecialchars($tagCode[4]).'"' : '').
							($useDAMColumn ? ' usedamcolumn="true"' : '').
							($error ? ' rteerror="'.htmlspecialchars($error).'" style="background-color: yellow; border:2px red solid; color: black;"' : '').	// Should be OK to add the style; the transformation back to databsae will remove it...
							'>';
				$eTag = '</a>';
				$blockSplit[$k] = $bTag.$this->transform_rte($pObj->removeFirstAndLastTag($blockSplit[$k]),$pObj).$eTag;
				
			}
		}

		$value = implode('',$blockSplit);
		
		return $value;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/mediatag/class.tx_dam_rtetransform_mediatag.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/mediatag/class.tx_dam_rtetransform_mediatag.php']);
}
?>