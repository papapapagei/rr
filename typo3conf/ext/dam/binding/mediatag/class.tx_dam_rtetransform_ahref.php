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


class tx_dam_rtetransform_ahref {

	var $transformationKey;
	
	/**
	 * Transformation handler: 'ts_links' / direction: "db"
	 * Processing linked images inserted in the RTE.
	 * This is used when content goes from the RTE to the database.
	 * Images inserted in the RTE has an absolute URL applied to the src attribute. This URL is converted to a media uid
	 *
	 * @param	string		The content from RTE going to Database
	 * @return	string		Processed content
	 */
	function transform_db($value, &$pObj) {
		
			// Split content into <a> tag blocks and process:
		$blockSplit = $pObj->splitIntoBlock('A',$value);
		foreach($blockSplit as $k => $v)	{
			if ($k%2)	{	// If an A-tag was found:
				$attribArray = $pObj->get_tag_attributes_classic($pObj->getFirstTag($v),1);
				$info = $pObj->urlInfoForLinkTags($attribArray['href']);

				$uid = false;
				if (isset($attribArray['txdam']))	{
					$uid = intval($attribArray['txdam']);
				}
				if (!$uid AND $info['relUrl'])	{
					$info['relUrl'] = rawurldecode($info['relUrl']);
					$uid = tx_dam::file_isIndexed($info['relUrl']);
				}

					// found an id, so convert the a tag to a media tag
				if ($uid)	{
					unset($attribArray['title']);
					$bTag='<media '.$uid.($attribArray['target']?' '.$attribArray['target']:(($attribArray['class'] || $attribArray['title'])?' -':'')).($attribArray['class']?' '.$attribArray['class']:($attribArray['title']?' -':'')).($attribArray['title']?' "'.$attribArray['title'].'"':'').'>';
					$eTag='</media>';
					$blockSplit[$k] = $bTag.$this->transform_db($pObj->removeFirstAndLastTag($blockSplit[$k]),$pObj).$eTag;
					
					
				} else {
						// just rebuild the tag so it can be processed by t3lib_parsehtml_proc::TS_links_db
					$bTag='<a '.t3lib_div::implodeAttributes($attribArray,1).'>';
					$eTag='</a>';
					$blockSplit[$k] = $bTag.$this->transform_db($pObj->removeFirstAndLastTag($blockSplit[$k]),$pObj).$eTag;
				}
			}
		}

		$value = implode('',$blockSplit);
				
		$value = $pObj->TS_links_db($value);
		
		return $value;
	}
	
	/**
	 * Transformation handler: 'ts_links' / direction: "rte"
	 * Processing linked images from database content going into the RTE.
	 * Processing includes converting the src attribute to an absolute URL.
	 *
	 * @param	string		Content input
	 * @return	string		Content output
	 */	
	function transform_rte($value, &$pObj) {
		
		$value = $pObj->TS_links_rte($value);
							
		return $value;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/mediatag/class.tx_dam_rtetransform_ahref.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/mediatag/class.tx_dam_rtetransform_ahref.php']);
}
?>