<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2008 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Soft Reference processing class
 * "Soft References" are references to database elements, files, email addresses, URls etc.
 * which are found in-text in content. The <link [page_id]> tag from typical bodytext fields
 * are an example of this.
 * This class contains DAM parsers for types media and mediatag
 * and extends processing for types typolink and typolink_tag.
 * The Soft Reference parsers are used by the system to find these references and process them accordingly in import/export actions and copy operations.
 *
 * $Id: class.tx_dam_softrefproc.php 4069 2008-09-07 22:00:49Z flyguide $
 *
 * @author	Stanislas Rolland <typo3@sjbr.ca>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  116: class t3lib_softrefproc
 *  137:     function findRef($table, $field, $uid, $content, $spKey, $spParams, $structurePath='')
 *  213:     function findRef_media($content, $spParams)
 *  280:     function findRef_medialink($content, $spParams)
 *  317:     function findRef_mediatag($content, $spParams)
 *  539:     function findRef_extension_fileref($content, $spParams)
 *
 *              SECTION: Helper functions
 *  591:     function fileadminReferences($content, &$elements)
 *  634:     function getTypoLinkParts($typolinkValue)
 *  718:     function setTypoLinkPartsElement($tLP, &$elements, $content, $idx)
 *
 * TOTAL FUNCTIONS: 14
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Example of usage
 *				// Soft References:
 *			if ($conf['softref'] && strlen($value))	{	// Check if a TCA configured field has softreferences defined (see TYPO3 Core API document)
 *				$softRefs = t3lib_BEfunc::explodeSoftRefParserList($conf['softref']);		// Explode the list of softreferences/parameters
 *				foreach($softRefs as $spKey => $spParams)	{	// Traverse soft references
 *					$softRefObj = &t3lib_BEfunc::softRefParserObj($spKey);	// create / get object
 *					if (is_object($softRefObj))	{	// If there was an object returned...:
 *						$resultArray = $softRefObj->findRef($table, $field, $uid, $softRefValue, $spKey, $spParams);	// Do processing
 *
 * Result Array:
 * The Result array should contain two keys: "content" and "elements".
 * "content" is a string containing the input content but possibly with tokens inside.
 *		Tokens are strings like {softref:[tokenID]} which is a placeholder for a value extracted by a softref parser
 *		For each token there MUST be an entry in the "elements" key which has a "subst" key defining the tokenID and the tokenValue. See below.
 * "elements" is an array where the keys are insignificant, but the values are arrays with these keys:
 *		"matchString" => The value of the match. This is only for informational purposes to show what was found.
 * 		"error"	=> An error message can be set here, like "file not found" etc.
 * 		"subst" => array(	// If this array is found there MUST be a token in the output content as well!
 *			"tokenID" => The tokenID string corresponding to the token in output content, {softref:[tokenID]}. This is typically an md5 hash of a string defining uniquely the position of the element.
 *			"tokenValue" => The value that the token substitutes in the text. Basically, if this value is inserted instead of the token the content should match what was inputted originally.
 *			"type" => file / db / string	= the type of substitution. "file" means it is a relative file [automatically mapped], "db" means a database record reference [automatically mapped], "string" means it is manually modified string content (eg. an email address)
 *			"relFileName" => (for "file" type): Relative filename. May not necessarily exist. This could be noticed in the error key.
 *			"recordRef" => (for "db" type) : Reference to DB record on the form [table]:[uid]. May not necessarily exist.
 *			"title" => Title of element (for backend information)
 *			"description" => Description of element (for backend information)
 *		)
 *
 */

require_once(PATH_t3lib.'class.t3lib_softrefproc.php');

/**
 * Class for processing of the DAM soft reference types:
 *
 * - 'media' : HTML <img> tags for RTE images with txdam attribute / images from fileadmin/
 * - 'medialink' : references to file, possibly with target, possibly comma-separated list.
 * - 'mediatag' : As media tag
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
class tx_dam_softrefproc extends t3lib_softrefproc {

		// external configuration
	var $fileAdminDir = 'fileadmin';


		// Internal:
	var $tokenID_basePrefix = '';

	/**
	 * Main function through which all processing happens
	 *
	 * @param	string		Database table name
	 * @param	string		Field name for which processing occurs
	 * @param	integer		UID of the record
	 * @param	string		The content/value of the field
	 * @param	string		The softlink parser key. This is only interesting if more than one parser is grouped in the same class. That is the case with this parser.
	 * @param	array		Parameters of the softlink parser. Basically this is the content inside optional []-brackets after the softref keys. Parameters are exploded by ";"
	 * @param	string		If running from inside a FlexForm structure, this is the path of the tag.
	 * @return	array		Result array on positive matches, see description above. Otherwise false
	 */
	function findRef($table, $field, $uid, $content, $spKey, $spParams, $structurePath='')	{

		$retVal = FALSE;

		$this->tokenID_basePrefix = $table.':'.$uid.':'.$field.':'.$structurePath.':'.$spKey;

		switch($spKey)	{
			case 'media':
				$retVal = $this->findRef_media($content, $spParams);
			break;
			case 'mediatag':
				$retVal = $this->findRef_mediatag($content, $spParams);
			break;
			case 'typolink':
				$retVal = $this->findRef_typolink($content, $spParams);
			break;
			case 'typolink_tag':
				$retVal = $this->findRef_typolink_tag($content, $spParams);
			break;
			case 'dam_mm_ref':
				$retVal = $this->findRef_dam_mm_ref($table, $field, $uid, $content, $spParams);
			break;
			case 'dam_file':
				$retVal = $this->findRef_dam_file($uid, $content, $spParams);
			break;
			default:
				$retVal = FALSE;
			break;
		}

		return $retVal;
	}

	/**
	 * Finding image tags with txdam attribute in the content.
	 * All images that have txdam attribute will be returned with an info text
	 *
	 * @param	string		The input content to analyse
	 * @param	array		Parameters set for the softref parser key in TCA/columns
	 * @return	array		Result array on positive matches, see description above. Otherwise false
	 */
	function findRef_media($content, $spParams) {

			// Start HTML parser and split content by image tag:
		$htmlParser = t3lib_div::makeInstance('t3lib_parsehtml');
		$imgTags = $htmlParser->splitTags('img', $content);
		$elements = array();

			// Traverse splitted parts:
		foreach ($imgTags as $k => $v) {
			if ($k%2) {
					// Get file reference:
				$attribs = $htmlParser->get_tag_attributes($v);
				$mediaId = $attribs[0]['txdam'];

					// If there is a mediaId, continue. Otherwise ignore it.
				if ($mediaId) {
						// Initialize the element entry with info text here:
					$tokenID = $this->makeTokenID($k);
					$elements[$k] = array();
					$elements[$k]['matchString'] = $v;

						// Token and substitute value:
					$imgTags[$k] = str_replace('txdam="' . $mediaId . '"', 'txdam="{softref:'.$tokenID.'}"', $imgTags[$k]);
					$elements[$k]['subst'] = array(
						'type' => 'db',
						'recordRef' => 'tx_dam:'.$mediaId,
						'tokenID' => $tokenID,
						'tokenValue' => $mediaId,
					);
				}
			}
		}
			// Return result:
		if (count($elements))	{
			$resultArray = array(
				'content' => implode('', $imgTags),
				'elements' => $elements
			);
			return $resultArray;
		}
	}

	/**
	 * Media tag processing.
	 * Will search for <media ...> tags in the content string and process any found.
	 *
	 * @param	string		The input content to analyse
	 * @param	array		Parameters set for the softref parser key in TCA/columns
	 * @return	array		Result array on positive matches, see description above. Otherwise false
	 * @see tslib_content::typolink(), getTypoLinkParts()
	 */
	function findRef_mediatag($content, $spParams) {

			// Parse string for special DAM <media> tag:
		$htmlParser = t3lib_div::makeInstance('t3lib_parsehtml');
		$mediaTags = $htmlParser->splitTags('media', $content);

			// Traverse result
		$elements = array();
		foreach ($mediaTags as $k => $v) {
			if ($k%2) {
					// Checking if the id-parameter is integer. Otherwise ignore this tag.
				$tagCode = t3lib_div::trimExplode(' ', substr($htmlParser->getFirstTag($v),0,-1), 1);
				$mediaId = $tagCode[1];
				if (t3lib_div::testInt($mediaId)) {

						// Initialize the element entry with info text here:
					$tokenID = $this->makeTokenID($k);
					$elements[$k] = array();
					$elements[$k]['matchString'] = $v;

						// Substitute value
					$mediaTags[$k] = preg_replace('/(<media[[:space:]]+)' . $mediaId . '/i', '$1{softref:' . $tokenID . '}', $mediaTags[$k]);
					$elements[$k]['subst'] = array(
						'type' => 'db',
						'recordRef' => 'tx_dam:'.$mediaId,
						'tokenID' => $tokenID,
						'tokenValue' => $mediaId,
					);
				}
			}
		}
			// Return output:
		if (count($elements)) {
			$resultArray = array(
				'content' => implode('', $mediaTags),
				'elements' => $elements
			);
			return $resultArray;
		}
	}

	/**
	 * Finding relations in table tx_dam_mm_ref.
	 *
	 * @param	string	The name of the related table
	 * @param	string	The name of the field refering to tx_dam
	 * @param	string	The uid of the record from $table
	 * @param	string	The (expected) number of relations
	 * @param	array	Parameters set for the softref parser key in TCA/columns (currently unused)
	 * @return	array	Result array on positive matches, see description above. Otherwise false
	 */
	function findRef_dam_mm_ref($table, $field, $uid, $count, $spParams) {

		$elements = array();
		$tokenList = array();

		$whereClauses=array('deleted'=>'');
		$fileList = tx_dam_db::getReferencedFiles($table, $uid, $field, '', '', $whereClauses);
		$uids = array_keys($fileList['rows']);

		foreach ($uids as $key => $uid) {
				// Initialize the element entry with info text here:
			$tokenID = $this->makeTokenID($key);
			$elements[$key] = array();
			$elements[$key]['matchString'] = $uid;

				// Token and substitute value:
			$tokenList[$key] = "{softref:$tokenID}";
			$elements[$key]['subst'] = array(
				'type' => 'db',
				'recordRef' => 'tx_dam:' . $uid,
				'tokenID' => $tokenID,
				'tokenValue' => $uid,
			);
		}
		if(count($elements) > 0) {
			$resultArray = array(
				'content' => implode(',', $tokenList),
				'elements' => $elements
			);
			return $resultArray;
		} else {
			return FALSE;
		}
	}

	/**
	 * "Finding" the files belonging to tx_dam records.
	 *
	 * @param	string	The uid of the record from $table
	 * @param	string	The filename (usually found in field "file_name")
	 * @param	array	Parameters set for the softref parser key in TCA/columns (currently unused)
	 * @return	array	Result array on positive matches, containing one or null results
	 */
	function findRef_dam_file($uid, $fileName, $spParams) {

		$resultArray = array();
		$fileInfo = tx_dam::meta_getDataByUid(
			$uid,
				// enhance default field list by "description"
			tx_dam_db::getMetaInfoFieldList() . ', description'
		);

		if ($fileInfo) {
			$file = $fileInfo['file_path'].$fileInfo['file_name'];
			$tokenID = $this->makeTokenID(0);
			$resultArray = array(
				'content' => '{softref:'.$tokenID.'}',
				'elements' => array(
					0 => array(
						'matchString' => $fileName,
						'subst' => array(
							'type' => 'file',
							'tokenID' => $tokenID,
							'tokenValue' => $fileName,
							'relFileName' => $file,
							'title' => $fileInfo['title'],
							'description' => $fileInfo['description']
						)
					)
				)
			);
			if (!@is_file(tx_dam::file_absolutePath($fileInfo))) {	// Finally, notice if the file does not exist.
				$resultArray['elements'][0]['error'] = 'File does not exist!';
			}
		}

		return $resultArray;
	}


	/*************************
	 *
	 * Helper functions
	 *
	 *************************/

	/**
	 * Recompile a TypoLink value from the array of properties made with getTypoLinkParts() into an elements array
	 *
	 * @param	array		TypoLink properties
	 * @param	array		Array of elements to be modified with substitution / information entries.
	 * @param	string		The content to process.
	 * @param	integer		Index value of the found element - user to make unique but stable tokenID
	 * @return	string		The input content, possibly containing tokens now according to the added substitution entries in $elements
	 * @see getTypoLinkParts()
	 */
	function setTypoLinkPartsElement($tLP, &$elements, $content, $idx)	{

			// Based on link type, maybe do more:
		switch ((string)$tLP['LINK_TYPE'])	{
			case 'file':
					// Initialize, set basic values. In any case a link will be shown
				$tokenID = $this->makeTokenID('setTypoLinkPartsElement:'.$idx);
				$elements[$tokenID.':'.$idx] = array();
				$elements[$tokenID.':'.$idx]['matchString'] = $content;
					// Process files found in fileadmin directory:
				if (!$tLP['query'])	{	// We will not process files which has a query added to it. That will look like a script we don't want to move.
					if (t3lib_div::isFirstPartOfStr($tLP['filepath'],$this->fileAdminDir.'/'))	{	// File must be inside fileadmin/
							// Let see if the file is indexded by DAM
						$mediaId = tx_dam::file_isIndexed(rawurldecode($tLP['filepath']));
							// If file is indexed by DAM continue. Otherwise ignore this link.
						if ($mediaId) {
								// Set up the basic token and token value for the relative file:
							$elements[$tokenID.':'.$idx]['subst'] = array(
								'type' => 'db',
								'recordRef' => 'tx_dam:'.$mediaId,
								'tokenID' => $tokenID,
								'tokenValue' => $mediaId,
							);
						} else {
								// Set up the basic token and token value for the relative file:
							$elements[$tokenID.':'.$idx]['subst'] = array(
								'type' => 'file',
								'relFileName' => $tLP['filepath'],
								'tokenID' => $tokenID,
								'tokenValue' => $tLP['filepath'],
							);
						}

							// Depending on whether the file exists or not we will set the
						$absPath = t3lib_div::getFileAbsFileName(PATH_site.$tLP['filepath']);
						if (!@is_file($absPath))	{
							$elements[$tokenID.':'.$idx]['error'] = 'File does not exist!';
						}

							// Output content will be the token instead
						$content = '{softref:'.$tokenID.'}';
					} else return $content;
				} else return $content;
			break;
			default:
				return parent::setTypoLinkPartsElement($tLP, $elements, $content, $idx);
			break;
		}

			// Finally, for all entries that was rebuild with tokens, add target and class in the end:
		if (strlen($content) && strlen($tLP['target']))	{
			$content.= ' '.$tLP['target'];
			if (strlen($tLP['class']))	{
				$content.= ' '.$tLP['class'];
			}
		}

			// Return rebuilt typolink value:
		return $content;
	}

	/**
	 * Hook in ImpExp before updating softrefs in tcemain
	 *
	 * @param array $params array('tce' => instance of tcemain, 'data' => data for insert/update
	 * @param tx_impexp $parent  instance of tx_impexp
	 * @return void
	 */
	function impexpHookBeforeSoftrefUpdate($params, $parent) {
		if (is_array($params['data']['tx_dam'])) {
			foreach ($params['data']['tx_dam'] as $key => $damRecord) {
				$file = $damRecord['file_name'];
				$params['data']['tx_dam'][$key]['file_name'] = basename($file);
				$params['data']['tx_dam'][$key]['file_path'] = dirname($file) . '/';
			}
		}
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/softref/class.tx_dam_softrefproc.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/softref/class.tx_dam_softrefproc.php']);
}
?>
