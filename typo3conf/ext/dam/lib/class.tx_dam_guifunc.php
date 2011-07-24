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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   94: class tx_dam_guiFunc
 *
 *              SECTION: icons
 *  115:     function icon_getFileTypeImgTag($infoArr, $addAttrib='')
 *  135:     function icon_getMediaTypeImgTag($infoArr, $addAttrib='', $addTitleAttr=true)
 *  172:     function icon_getTitleAttribute($infoArr, $displayItems='')
 *  188:     function convert_mediaType($type)
 *
 *              SECTION: Small GUI elements
 *  218:     function getMediaTypeIconBox($infoArr, $iconPlusType=TRUE)
 *
 *              SECTION: Table GUI elements
 *  254:     function getRecordInfoHeader($row, $extraContentLeft='', $extraContentMiddle='', $extraContentRight='')
 *  310:     function getRecordInfoHeaderExtra($row, $extraContentLeft='', $extraContentMiddle='', $extraContentRight='')
 *  323:     function getReferencesTable($uidList, $displayColumns='page,content_element')
 *
 *              SECTION: Path related functions
 *  442:     function getFolderInfoBar($pathInfo, $maxLength=55)
 *  476:     function getPathBreadcrumbMenu($pathInfo, $browsable=FALSE, $maxLength=55, $param='SET[tx_dam_folder]')
 *
 *              SECTION: Thumbnail like a dia
 *  541:     function getDia($row, $diaSize=115, $diaMargin=10, $showElements='', $onClick=NULL, $makeIcon=TRUE, $actions='')
 *  638:     function getDiaStyles($diaSize=115, $diaMargin=10, $margin=0)
 *
 *              SECTION: Meta data related - prepare for output
 *  715:     function meta_compileHoverText ($row, $displayItems='', $implodeWith="\n")
 *  734:     function meta_compileInfoData ($row, $displayItems='', $formatData='')
 *
 *              SECTION: image scaling and thumbs
 *  851:     function thumbnail($fileInfo, $size='', $titleContent='', $imgAttributes='', $iconAttributes='', $onClick=NULL, $makeFileIcon=TRUE)
 *  895:     function image_thumbnailIconImgTag($fileInfo, $size='', $imgAttributes='', $iconAttributes='', $makeFileIcon=TRUE)
 *
 *              SECTION: Tools - used internally but might be useful for general usage
 *  945:     function getFieldLabel ($field, $hsc=true)
 *  962:     function tools_formatValue ($itemValue, $format, $config='')
 * 1051:     function tools_calcAge($seconds, $labels='min|hrs|days|yrs')
 * 1074:     function tools_explodeAttributes($attributeString)
 * 1092:     function tools_implodeAttributes($attributes, $hsc=true)
 * 1108:     function tools_insertWordBreak($content, $every)
 *
 *              SECTION: Misc stuff
 * 1132:     function getLabelFromItemlist($table,$col,$key)
 *
 * TOTAL FUNCTIONS: 23
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');


/**
 * Misc DAM BE GUI functions
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
class tx_dam_guiFunc {



	/***************************************
	 *
	 *	 icons
	 *
	 ***************************************/



	/**
	 * Returns a file or folder icon for a given (file)path as HTML img tag.
	 * A title attribute will be added by default.
	 *
	 * @param	array		$infoArr Record/info array: eg. $pathInfo = tx_dam::path_getInfo($path)
	 * @param	mixed		$addAttrib Additional attributes for the image tag.
	 * @return	string		Icon img tag
	 * @see tx_dam::path_getInfo()
	 */
	function icon_getFileTypeImgTag($infoArr, $addAttrib='')	{

		if (is_array($addAttrib)) {
			$addAttrib = tx_dam_guifunc::tools_implodeAttributes($addAttrib);
		}
		if (strpos($addAttrib, 'title=')===false) {
			$addAttrib .= tx_dam_guiFunc::icon_getTitleAttribute($infoArr);
		}
		return tx_dam::icon_getFileTypeImgTag($infoArr, $addAttrib);
	}


	/**
	 * Returns a big media type icon from a record
	 *
	 * @param	array		$infoArr Record/info array: eg. $pathInfo = tx_dam::path_getInfo($path)
	 * @param	mixed		$addAttrib Additional attributes for the image tag.
	 * @param	boolean		$addTitleAttr If set a title attribute will be added
	 * @return	string		Rendered icon img tag
	 */
	function icon_getMediaTypeImgTag($infoArr, $addAttrib='', $addTitleAttr=true) {
		global $LANG, $TYPO3_CONF_VARS;

		require_once(PATH_t3lib.'class.t3lib_iconworks.php');

		if (is_array($addAttrib)) {
			$addAttrib = tx_dam_guifunc::tools_implodeAttributes($addAttrib);
		}

		if($addTitleAttr) {
			$label = tx_dam_guifunc::getLabelFromItemlist('tx_dam', 'media_type', $infoArr['media_type']);
			$label = strtoupper(trim($LANG->sL($label)));
			$addAttrib .= ' title="'.htmlspecialchars($label).'"';
		}

		$iconname = tx_dam::convert_mediaType($infoArr['media_type']);
		$iconfile = 'i/media-'.$iconname.'.png';

 		if (TYPO3_MODE === 'FE') {
			$iconfile = PATH_txdam_siteRel.$iconfile;
		} else {
			$iconfile = PATH_txdam_rel.$iconfile;
		}

		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconfile, 'width="29" height="27"').' '.trim($addAttrib).' alt="" />';

		return $icon;
	}


	/**
	 * Returns title attribute from a record for use with icons
	 *
	 * @param	array		$infoArr Record/info array: eg. $pathInfo = tx_dam::path_getInfo($path)
	 * @param	string		$displayItems Item names for the hover text as comma list which are array keys or special names like "_dimensions". Format and option can be added (separated with ":") to call tx_dam::tools_formatValue().
	 * @return	string		title attribute
	 */
	function icon_getTitleAttribute($infoArr, $displayItems='') {

		$hoverText = tx_dam_guiFunc::meta_compileHoverText($infoArr, $displayItems);
		$titleAttrib = ' title="'.htmlspecialchars($hoverText).'"';

		return $titleAttrib;
	}


	/**
	 * Converts the media type code to a name.
	 * In comparison to tx_dam::convert_mediaType() this function returns a localized name if possible.
	 *
	 * @param	mixed		$type Media type name or media type code to convert. Integer or 'text','image','audio','video','interactive', 'service','font','model','dataset','collection','software','application'
	 * @return	mixed		Media type name or media type code
	 */
	function convert_mediaType($type) {
		global $LANG;

		if(!strcmp($type,intval($type)) AND is_object($LANG)) {
			$type = tx_dam_guifunc::getLabelFromItemlist('tx_dam', 'media_type', $type);
			$type = $LANG->sL($type);
		} else {
				// convert to code
			$type = tx_dam::convert_mediaType($type);
				// convert to localized name
			$type = tx_dam_guiFunc::convert_mediaType($type);
		}
		return $type;
	}


	/***************************************
	 *
	 *	 Small GUI elements
	 *
	 ***************************************/


	/**
	 * Returns a media type icon from a record
	 *
	 * @param	array		$infoArr Record array
	 * @param	boolean		$iconPlusType If set the name of the media type is printed below the icon
	 * @return	string		Rendered icon
	 */
	function getMediaTypeIconBox($infoArr, $iconPlusType=TRUE) {
		global $LANG, $BACK_PATH;

		$label = tx_dam_guifunc::getLabelFromItemlist('tx_dam', 'media_type', $infoArr['media_type']);
		$label = strtoupper(trim($LANG->sL($label)));

		$icon = tx_dam_guiFunc::icon_getMediaTypeImgTag($infoArr, '', !$iconPlusType);

		if($iconPlusType) {
			#$icon = '<div class="txdam-typeiconbox" style="display:compact;"><div class="txdam-typeiconbox-wrapper" style="text-align:center;">'.$icon.'<br /><span style="color:#555;">'.htmlspecialchars($label).'</span></div></div>';
			$icon = '<div class="txdam-typeiconbox-wrapper" style="text-align:center;">'.$icon.'<br /><span style="color:#555;">'.htmlspecialchars($label).'</span></div>';
		}

		return $icon;
	}




	/***************************************
	 *
	 *	 Table GUI elements
	 *
	 ***************************************/



	/**
	 * Returns a table with some info and a thumbnail from a record
	 *
	 * @param	array		$row Record array
	 * @param	string		$extraContentLeft Extra HTML content for left column
	 * @param	string		$extraContentMiddle Extra HTML content for middle column
	 * @param	string		$extraContentRight Extra HTML content for right column
	 * @return	string		HTML content
	 */
	function getRecordInfoHeader($row, $extraContentLeft='', $extraContentMiddle='', $extraContentRight='') {
		global $LANG;

		$content = '';

		$icon = tx_dam_guiFunc::getMediaTypeIconBox($row);

		$content.= '
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" width="1%" align="center">'.$icon.'<br />'.$extraContentLeft.'</td>
					<td valign="top" align="left" style="padding-left:20px;">';
		if (isset($row['title'])) {
			$content.=	'<div class="tableRow"><strong>'.$LANG->sL('LLL:EXT:lang/locallang_general.xml:LGL.title',1).'</strong><br />'.
						tx_dam_guiFunc::tools_insertWordBreak(htmlspecialchars($row['title']),35).'</div>';
		}
		$content.=	'<div class="tableRow"><strong>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name',1).'</strong><br />'.
					tx_dam_guiFunc::tools_insertWordBreak(htmlspecialchars($row['file_name']),35).'</div>';

		$content.=	'<div class="tableRow"><strong>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_path',1).'</strong><br />'.
					str_replace('/', '/<wbr>', htmlspecialchars($row['file_path'])).'</div>';

		if ($row['media_type'] == TXDAM_mtype_image) {
			$out = '';
			$out.= $row['hpixels'] ? $row['hpixels'].'x'.$row['vpixels'].' px, ' : '';
			$out.= t3lib_div::formatSize($row['file_size']);
			$out.= $row['color_space'] ? ', '.$LANG->sL(tx_dam_guifunc::getLabelFromItemlist('tx_dam','color_space',$row['color_space'])) : '';

			$content.=	'<div class="tableRow"><nobr>'.htmlspecialchars($out).'</nobr></div>';
		}

		$content.= $extraContentMiddle.'
					</td>';

		$thumb = tx_dam_guiFunc::getDia($row, 115, 5, $showElements='', $onClick=NULL, $makeIcon=FALSE);
		$content.= '
					<td valign="top" width="1%" class="extraContentRight">'.$thumb.$extraContentRight.'</td>';

		$content.= '
				</tr>
			</table>';

		return '<div class="recordInfoHeader">'.$content.'</div>';
	}


	/**
	 * Returns a table with some info and a thumbnail from a record
	 * May display additional buttons/icons like info button
	 *
	 * @param	array		$row Record array
	 * @param	string		$extraContentLeft Extra HTML content for left column
	 * @param	string		$extraContentMiddle Extra HTML content for middle column
	 * @param	string		$extraContentRight Extra HTML content for right column
	 * @return	string		HTML content
	 */
	function getRecordInfoHeaderExtra($row, $extraContentLeft='', $extraContentMiddle='', $extraContentRight='') {
		$extraContentLeft = $GLOBALS['SOBE']->btn_infoFile($row).'<br />'.$extraContentLeft;
		return tx_dam_guiFunc::getRecordInfoHeader($row, $extraContentLeft, $extraContentMiddle, $extraContentRight);
	}


	/**
	 * Render a table with referenced records
	 *
	 * @param 	mixed 	$uidList List of media uid's to get the references for
	 * @param	string	$displayColumns list of elements to display in the table. Available: page, content_element, content_age, media_element, media_element_age
	 * @return	string		Rendered Table
	 */
	function getReferencesTable($uidList, $displayColumns='page,content_element,content_field,softref_key')   {
			// File references
		$itemOut = '';
		//$itemOut .= '<h4>' . $GLOBALS['LANG']->sl('LLL:EXT:dam/lib/locallang.xml:fileReference') . '</h4>';
		$rows = tx_dam_db::getMediaUsageReferences($uidList);
		if ($rows) {
			$itemOut .= tx_dam_guiFunc::renderReferencesTable($rows, $displayColumns);
		} else {
			$itemOut .= $GLOBALS['LANG']->sl('LLL:EXT:dam/lib/locallang.xml:fileNotUsed');
		}
		return $itemOut;
	}
	
	/**
	 * Render a table with referenced records
	 *
	 * @param 	array 	$rows: Array of reference records
	 * @param	string	$displayColumns: list of elements to display in the table. Available: page, content_element, content_field, content_age, media_element, media_element_age
	 * @return	string	Rendered Table
	 */
	function renderReferencesTable($rows, $displayColumns='page,content_element,content_field,softref_key') {
		$content = '';
		require_once(PATH_txdam.'lib/class.tx_dam_listreferences.php');
		require_once (PATH_txdam.'lib/class.tx_dam_iterator_references.php');
		$referenceList = t3lib_div::makeInstance('tx_dam_listreferences');
		$referenceList->displayColumns = t3lib_div::trimExplode(',', $displayColumns, 1);
		$referenceList->init();
		$referenceList->enableSorting = false;
			// Build the references object
		$references = t3lib_div::makeInstance('tx_dam_iterator_references');
		$references->processEntries($rows, $referenceList->displayColumns);
		$references->sort('page');
			// Return rendered table
		if ($references->count()) {
			$referenceList->addData($references, 'references');
				// Make up a pointer
			require_once(PATH_txdam.'lib/class.tx_dam_listpointer.php');
			$pointer = t3lib_div::makeInstance('tx_dam_listPointer');
			$pointer->init(0, 40, 1);
			$referenceList->setPointer($pointer);
			$referenceList->pointer->setTotalCount($references->count());
			$content .= $referenceList->getListTable();
		}
		return $content;
	}

	/********************************
	 *
	 * Path related functions
	 *
	 ********************************/


	/**
	 * Makes the code for the foldericon in the top
	 *
	 * @param	array		$pathInfo Path info array: $pathInfo = tx_dam::path_getInfo($path)
	 * @param	integer		$maxLength Maximum Text length
	 * @return	string		HTML code
	 */
	function getFolderInfoBar($pathInfo, $maxLength=55)	{
		global $BACK_PATH, $LANG;

		if (is_array($pathInfo))	{

			$iconFolder = tx_dam::icon_getFolder($pathInfo);
			$elements['icon'] = '<img'.t3lib_iconWorks::skinImg($BACK_PATH, $iconFolder, 'width="18" height="16"').' alt="" />';

			$elements['path'] = tx_dam_guiFunc::getPathBreadcrumbMenu($pathInfo, false, $maxLength);

			$out = '

		<!--
			Page header for file list
		-->
				<table border="0" cellpadding="0" cellspacing="0" id="typo3-pathBreadcrumbBar">
					<tr><td>'.implode('</td><td>', $elements).'</td></tr>
				</table>';

		}
		return $out;
	}


	/**
	 * Returns a path with links for browsing.
	 * Is like a breadcrumb menu
	 *
	 * @param	array		$pathInfo tx_dam::path_compileInfo($path);
	 * @param	boolean		$browsable If set links are enabled
	 * @param	integer		$maxLength Maximum Text length
	 * @param	string		$param The name of the GET parameter. Default: SET[tx_dam_folder]
	 * @return	string		Linked Path
	 */
	 function getPathBreadcrumbMenu($pathInfo, $browsable=FALSE, $maxLength=55, $param='SET[tx_dam_folder]') {
	 	$pathArr = explode('/', $pathInfo['dir_path_from_mount']);
		array_pop($pathArr);
	 	$pathArrRev = array_reverse($pathArr, TRUE);

		$len = 0;

		$mountPart = '';
		if($pathInfo['mount_id']) {
			if ($browsable) {
				$mountTitle = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array($param => $pathInfo['mount_path']))).'">'.htmlspecialchars($pathInfo['mount_name']).'</a>';
			} else {
				$mountTitle = htmlspecialchars($pathInfo['mount_name']);
			}
			$mountPart = '['.$mountTitle.']: ';
	 		$len = strlen($pathInfo['mount_name'])+4;
		}

	 	$newPathArr = array();
	 	foreach ($pathArrRev as $key => $part) {

			$part = t3lib_div::fixed_lgd_cs($part, 20);
		 	if ($part) {
		 		$len += strlen($part)+1;
		 		if ($len > $maxLength) {
		 			$part = '...';
		 		}
			 	if ($browsable) {
		 			$linkPath = implode('/', $pathArr).'/';
					$newPathArr[] = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array($param => $pathInfo['mount_path'].$linkPath))).'">'.htmlspecialchars($part).'/</a>';
			 	} else {
					$newPathArr[] = htmlspecialchars($part.'/');
			 	}
		 	}
		 	if ($len > $maxLength) { break; }
			array_pop($pathArr);
	 	}
	 	$newPathArr = array_reverse($newPathArr);

		$BreadcrumbMenu = $mountPart.implode('', $newPathArr);
		return '<span class="typo3-pathBreadcrumbMenu">'.$BreadcrumbMenu.'</span>';
	}




	/***************************************
	 *
	 *	 Thumbnail like a dia
	 *
	 ***************************************/


	/**
	 * Returns a dia like thumbnail
	 *
	 * @param	array		$row tx_dam record
	 * @param	integer		$diaSize dia size
	 * @param	integer		$diaMargin dia margin
	 * @param	array		$showElements Extra elements to show: "title,info,icons"
	 * @param	string		$onClick: ...
	 * @param	boolean		$makeIcon: ...
	 * @param	string		$actions action content to be displayed
	 * @return	string		HTML output
	 */
	function getDia($row, $diaSize=115, $diaMargin=10, $showElements='', $onClick=NULL, $makeIcon=TRUE, $actions='') {

		if(!is_array($showElements)) {
			$showElements = t3lib_div::trimExplode(',', $showElements,1);
		}


			// extra CSS code for HTML header
		if(is_object($GLOBALS['SOBE']) AND !isset($GLOBALS['SOBE']->doc->inDocStylesArray['tx_dam_SCbase_dia'])) {
			$GLOBALS['SOBE']->doc->inDocStylesArray['tx_dam_SCbase_dia'] = tx_dam_guiFunc::getDiaStyles($diaSize, $diaMargin);
		}

// use css/stylesheet
		$iconBgColor = t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-10,-10,-10);
		$titleLen = ceil( (30*($diaSize-$diaMargin))/(200-$diaMargin) );

		$hpixels = $row['hpixels'];
		$vpixels = $row['vpixels'];
		if ($hpixels AND $vpixels) {
			list($hpixels, $vpixels) = tx_dam_image::calcSize($hpixels, $vpixels, $diaSize, $diaSize);
		} else {
			if($hpixels > $diaSize) {
				$hpixels = $diaSize;
			}
			if($vpixels > $diaSize) {
				$vpixels = $diaSize;
			}
		}

		$uid = $row['uid'];
		$imgAttributes = array();
		$imgAttributes['title'] = str_replace("\n", '', t3lib_div::fixed_lgd_cs($row['description'], 50));
		if ($hpixels) {
			$imgAttributes['style'] = 'margin-top:'.(ceil(($diaSize-$vpixels)/2)+$diaMargin).'px;';
		} else {
			$imgAttributes['style'] = 'margin-top:'.$diaMargin.'px;';
		}

		$imgAttributes['onclick'] = $onClick;
		$thumb = tx_dam_image::previewImgTag($row, $diaSize, $imgAttributes);

		if (!$makeIcon AND empty($thumb)) { return ''; }
		if (empty($thumb)) {
			$thumb = tx_dam_guiFunc::getMediaTypeIconBox($row);
			if ($onClick) {
				$thumb = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$thumb.'</a>';
			}
		}

		$descr = '';
		if (in_array('title', $showElements)) {
			$descr.= htmlspecialchars(t3lib_div::fixed_lgd_cs($row['title'], $titleLen)).'<br />';
		}
		if (in_array('info', $showElements)) {
			$code = strtoupper($row['file_type']).', ';
			$code.= $row['hpixels']? $row['hpixels'].'x'.$row['vpixels'].', ' :'';
			$code.= t3lib_div::formatSize($row['file_size']);
			$descr .= '<span class="txdam-descr">'.htmlspecialchars($code).'</span>';
		}
		if($descr) {
			$descr = '<div class="txdam-title">'.$descr.'</div>';
		}

		$icons  = '';
		$iconArr = array();
		if (in_array('icons', $showElements)) {
			// deprecated
			$iconArr[] = tx_dam_SCbase::icon_editRec('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::btn_editRec_inNewWindow('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::icon_infoRec('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::btn_removeRecFromSel('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
		}
		if (in_array('actions', $showElements)) {
			$actions;
		}
		$icons = $icons ? '<div style="margin:3px;">'.implode('<span style="width:40px;"></span>', $iconArr).'</div>' : '';
		$actions = $actions ? '<div style="margin:3px;">'.$actions.'</div>' : '';

		$diaCode = '
		<table class="txdam-dia" cellspacing="0" cellpadding="0" border="0">
		<tr><td><span class="txdam-dia">'.$thumb.'</span></td></tr>
		'. ( ($descr.$icons.$actions) ? '<tr><td align="center" bgcolor="'.$iconBgColor.'">'.$descr.$icons.$actions.'</td></tr>' : '').'
		</table> ';

		return $diaCode;
	}


	/**
	 * Return CSS code to be used with used with thumbnail created by getDia()
	 *
	 * @param	integer		$diaSize: ...
	 * @param	integer		$diaMargin: ...
	 * @param	integer		$margin: ...
	 * @return	string		CSS code
	 * @see getDia()
	 */
	function getDiaStyles($diaSize=115, $diaMargin=10, $margin=0) {
			// extra CSS code for HTML header
		$styles = '

			.txdam-title, .txdam-descr {
				font-family:verdana,sans-serif;
				font-size:9.5px;
				line-height:12px;
				margin:2px;
			}
			.txdam-descr {
				color:#777;
			}
			table.txdam-dia {
				float:left;
				margin-bottom:8px;
			}
			span.txdam-dia {
				float:left;
				width:'.($diaSize+($diaMargin*2)+2).'px;
				height:'.($diaSize+($diaMargin*2)+2).'px;

				text-align:center;
				vertical-align:middle;

				margin:'.$margin.'px;
				padding:0px;
				background-color:#fbfbfb;
				border:solid #999 1px;
				border-top:solid #ddd 1px;
				border-bottom:solid #000 1px;
			}

			span.txdam-dia a {
				text-decoration:none;
			}
			span.txdam-dia > a > img {
				border:solid 1px #ccc;
				margin:'.($diaMargin).'px;
				vertical-align:50%;
			}
			span.txdam-dia > a > div {
				border:solid 1px #ccc;
				margin:'.($diaMargin).'px;
				padding:'.($diaMargin).'px;
				width:'.($diaSize-$diaMargin-$diaMargin).'px;
				height:'.($diaSize-$diaMargin-$diaMargin).'px;
				vertical-align:middle;
			}
			span.txdam-dia .txdam-typeiconbox {
				line-height:2em; /* IE */
				margin-top: 2em; /* IE */
				border:none;
			}
			';
		return $styles;
	}





	/***************************************
	 *
	 *	 Meta data related - prepare for output
	 *
	 ***************************************/


	/**
	 * Compiles from a meta data array text to be used in title attributes.
	 *
	 * @param	array		$row Meta data record array
	 * @param	string		$displayItems Item names as comma list which are array keys or special names like "_dimensions". Format and option can be added (separated with ":") to call tx_dam::tools_formatValue().
	 * @param	string		$implodeWith String that is used to implode the content lines. If false the array will not be imploded and an array will be returned.
	 * @return	mixed		Info data string or non-imploded array
	 */
	function meta_compileHoverText ($row, $displayItems='', $implodeWith="\n") {
		$displayItems = $displayItems ? $displayItems : '_media_type:strtoupper, title, file_name, file_size:filesize, _dimensions';

		$infoData = tx_dam_guiFunc::meta_compileInfoData ($row, $displayItems, 'value-array');
		if (is_string($implodeWith)) {
			$infoData = implode($implodeWith, $infoData);
		}
		return $infoData;
	}


	/**
	 * Compiles from a meta data array human readable content.
	 *
	 * @param	array		$row Meta data record array
	 * @param	string		$displayItems Item names as comma list which are array keys or special names like "_dimensions". Format and option can be added (separated with ":") to call tx_dam::tools_formatValue().
	 * @param	string		$formatData If set the array wll be formatted as "paragraph" or "table".
	 * @return	array		Info data array
	 */
	function meta_compileInfoData ($row, $displayItems='', $formatData='') {

		$infoData = array();

		$displayItems = $displayItems ? $displayItems : 'title, file_name, file_size:filesize, _dimensions, description:truncate:50';
		$displayItems = t3lib_div::trimExplode(',', $displayItems, true);

		foreach ($displayItems as $item) {

			list($item, $format, $config) = t3lib_div::trimExplode(':', $item, true);

			$label = '';
			switch ($item) {



				case '_media_type':
					$infoData[$item]['value'] = tx_dam_guiFunc::convert_mediaType($row['media_type']);
					t3lib_div::loadTCA('tx_dam');
					$label = $GLOBALS['TCA']['tx_dam']['columns']['media_type']['label'];
				break;

				case '_dimensions':
					if ($row['media_type'] == TXDAM_mtype_image AND $row['hpixels']) {
						$infoData[$item]['value'] = $row['hpixels'].'x'.$row['vpixels'].' px';
						$label = 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.metrics';
					} elseif ($row['height_unit']) {
						$infoData[$item]['value'] = $row['width'].'x'.$row['height'].' '.$row['height_unit'];
						$label = 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.metrics';
					}
				break;
// todo _techinfo
//				case '_techinfo':
//					if ($row['media_type'] == TXDAM_mtype_image AND $row['color_space']) {
//						$value = tx_dam_guifunc::getLabelFromItemlist('tx_dam', 'color_space', $row['color_space']);
//						if (is_object($GLOBALS['LANG'])) {
//							$value = $LANG->sL($value);
//						}
//						$infoData[$item]['value'] = $value;
//					}
//				break;

				case '_caption':
					$infoData[$item]['value'] = $row['caption'] ? $row['caption'] : $row['description'];
				break;
				
				default:
					if (isset($row[$item])) {
						$infoData[$item]['value'] = $row[$item];
					}
				break;
			}
			if ($format AND isset($infoData[$item])) {
				$infoData[$item]['value'] = tx_dam_guiFunc::tools_formatValue ($infoData[$item]['value'], $format, $config);
			}
			if ($label AND is_object($GLOBALS['LANG'])) {
				$infoData[$item]['label'] = $GLOBALS['LANG']->sL($label);
			}
			if (isset($infoData[$item]) AND !isset($infoData[$item]['label']) AND is_object($GLOBALS['LANG'])) {
				t3lib_div::loadTCA('tx_dam');
				$infoData[$item]['label'] = $GLOBALS['LANG']->sL($GLOBALS['TCA']['tx_dam']['columns'][$item]['label']);
			}
		}


		switch ($formatData) {
			case 'p':
			case 'paragraph':
					$infoText = '';
					foreach($infoData as $val) {
						$infoText .= '<p><strong>'.htmlspecialchars($val['label']).'</strong> '.htmlspecialchars($val['value']).'</p>';
					}
					$infoData = $infoText;
			break;
			case 'table':
					$infoText = '';
					foreach($infoData as $val) {
						$infoText .= '<tr><td><strong>'.htmlspecialchars($val['label']).'</strong>&nbsp;</td><td>'.htmlspecialchars($val['value']).'</td></tr>';
					}
					$infoData = '<table>'.$infoText.'</table>';
			break;
			case 'value-array':
					$infoArr = array();;
					foreach($infoData as $item => $val) {
						$infoArr[$item] = $val['value'];
					}
					$infoData = $infoArr;
			break;
			case 'value-string':
					$infoArr = array();;
					foreach($infoData as $item => $val) {
						$infoArr[$item] = $val['value'];
					}
					$infoData = implode("\n", $infoArr);
			break;
			default:
			break;
		}

		return $infoData;
	}







	/***************************************
	 *
	 *   image scaling and thumbs
	 *
	 ***************************************/


	/**
	 * Returns a linked image-tag for thumbnail(s)
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$size Optional: $size is [w]x[h] of the thumbnail. 56 is default.
	 * @param	string		$titleContent Optional: Used as a title= attribute content
	 * @param	mixed		$imgAttributes Optional: is additional attributes for the image tags
	 * @param	mixed		$iconAttributes Optional: additional attributes for the image tags for file icons
	 * @param	string		$onClick Optional: If falso no A tag with onclick will be wrapped. If NULL top.launchView() will be used. If string it's value will be used as onclick value.
	 * @param	boolean		$makeFileIcon If true a file icon will be returned if no thumbnail is possible
	 * @return	string		Thumbnail image tag.
	 * @deprecated - really?
	 */
	function thumbnail($fileInfo, $size='', $titleContent='', $imgAttributes='', $iconAttributes='', $onClick=NULL, $makeFileIcon=TRUE)	{

			// get some file information
		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo($fileInfo);
		}

		if (!is_array($imgAttributes)) {
			$imgAttributes = tx_dam_guifunc::tools_explodeAttributes($imgAttributes);
		}
		$titleContent = $titleContent ? $titleContent : ($imgAttributes['title'] ? $imgAttributes['title'] : tx_dam_guiFunc::meta_compileHoverText($fileInfo, '', ' - '));
		$imgAttributes['title'] = $titleContent;

		if (!($onClick===false)) {
			$filepath = tx_dam::file_absolutePath($fileInfo);
			$imgAttributes['onclick'] = !is_null($onClick) ? $onClick : ((TYPO3_MODE === 'BE') ? 'top.launchView(\''.$filepath.'\',\'\',\''.$GLOBALS['BACK_PATH'].'\');return false;' : false);
		}

		if ($makeFileIcon) {
			if (!is_array($iconAttributes)) {
				$iconAttributes = tx_dam_guifunc::tools_explodeAttributes($iconAttributes);
			}
			$iconAttributes['title'] = isset($iconAttributes['title']) ? $iconAttributes['title'] : $titleContent;
		}

		$thumbnail = tx_dam_guiFunc::image_thumbnailIconImgTag($fileInfo, $size, $imgAttributes, $iconAttributes, $makeFileIcon);

		return $thumbnail;
	}


	/**
	 * Returns a image-tag for thumbnail(s)
	 * A file icon will be returned if no thumbnail is possible
	 * If 'href' and/or 'onlick' is set as attributes a A tag will be wrapped around with these.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$size Optional: $size is [w]x[h] of the thumbnail. 56 is default.
	 * @param	mixed		$imgAttributes Optional: is additional attributes for the image tags
	 * @param	mixed		$iconAttributes Optional: additional attributes for the image tags for file icons
	 * @param	string		$onClick Optional: If falso no A tag with onclick will be wrapped. If NULL top.launchView() will be used. If string it's value will be used as onclick value.
	 * @param	boolean		$makeFileIcon If true a file icon will be returned if no thumbnail is possible
	 * @return	string		Thumbnail image tag.
	 */
	function image_thumbnailIconImgTag($fileInfo, $size='', $imgAttributes='', $iconAttributes='', $makeFileIcon=TRUE)	{
		$thumbnail = '';

		if (!is_array($imgAttributes)) {
			$imgAttributes = tx_dam_guifunc::tools_explodeAttributes($imgAttributes);
		}
		$href = $imgAttributes['href'];
		$onclick = $imgAttributes['onclick'];
		unset($imgAttributes['href']);
		unset($imgAttributes['onclick']);

		$thumbnail = tx_dam_image::previewImgTag($fileInfo, $size, $imgAttributes);

		if (!$thumbnail AND $makeFileIcon) {

			if (!is_array($iconAttributes)) {
				$iconAttributes = tx_dam_guifunc::tools_explodeAttributes($iconAttributes);
			}
			$href = $iconAttributes['href'];
			$onclick = $iconAttributes['onclick'];
			unset($iconAttributes['href']);
			unset($iconAttributes['onclick']);
			$fileType = tx_dam::file_getType($fileInfo);
			$thumbnail = tx_dam_guiFunc::icon_getFileTypeImgTag($fileType, $iconAttributes);
		}

		if ($thumbnail AND ($onclick OR $href)) {
			$href = $href ? $href : '#';
			$thumbnail = '<a href="'.htmlspecialchars($href).'" onclick="'.htmlspecialchars($onclick).'">'.$thumbnail.'</a>';
		}

		return $thumbnail;
	}



	/***************************************
	 *
	 *   Tools - used internally but might be useful for general usage
	 *
	 ***************************************/


	/**
	 * Returns the TCA label for a field in the current language with the $LANG object
	 *
	 * @param string $field
	 * @param boolean $hsc
	 * @return string Field label
	 */
	function getFieldLabel ($field, $hsc=true) {
		global $LANG, $TCA;

		t3lib_div::loadTCA('tx_dam');
		return $LANG->sl($TCA['tx_dam']['columns'][$field]['label'], $hsc);
	}


	/**
	 * Format content of various types if $format is set to date, filesize, ...
	 *
	 * @param	string		$itemValue The value to display
	 * @param	array		$format Define format type like: date, datetime, truncate, ...
	 * @param	string		$config Additional configuration options for the format type
	 * @return	string		Formatted content
	 * @see t3lib_tceforms::formatValue()
	 */
	function tools_formatValue ($itemValue, $format, $config='')	{
		switch($format)	{
			case 'date':
				$config = $config ? $config : 'd-m-Y';
				$itemValue = date($config,$itemValue);
				break;
			case 'dateage':
				$config = $config ? $config : 'd-m-Y';
				$itemValue = date($config,$itemValue);
				$itemValue .= ' ('.tx_dam_guifunc::tools_calcAge((time()-$itemValue), $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.minutesHoursDaysYears')).')';
				break;
			case 'datetime':	// compatibility with "eval" (type "input")
				$itemValue = date('H:i d-m-Y',$itemValue);
				break;
			case 'time':	// compatibility with "eval" (type "input")
				$itemValue = date('H:i',$itemValue);
				break;
			case 'timesec':	// compatibility with "eval" (type "input")
				$itemValue = date('H:i:s',$itemValue);
				break;
			case 'year':	// compatibility with "eval" (type "input")
				$itemValue = date('Y',$itemValue);
				break;
			case 'int':
				$baseArr = array('dec'=>'d','hex'=>'x','HEX'=>'X','oct'=>'o','bin'=>'b');
				$base = trim($config);
				$format = $baseArr[$base] ? $baseArr[$base] : 'd';
				$itemValue = sprintf('%'.$format,$itemValue);
				break;
			case 'float':
				$precision = t3lib_div::intInRange($config,1,10,2);
				$itemValue = sprintf('%.'.$precision.'f',$itemValue);
				break;
			case 'number':
				$itemValue = sprintf('%'.$config,$itemValue);
				break;
			case 'md5':
				$itemValue = md5($itemValue);
				break;
			case 'filesize':
				$itemValue = t3lib_div::formatSize(intval($itemValue)).'B';
				break;
			case 'filesize+bytes':
				$itemValue = t3lib_div::formatSize(intval($itemValue)).'B';
				$itemValue .= ' ('.$itemValue.')';
				break;
			case 'truncate':
				$config = $config ? $config : 20;
				$itemValue = t3lib_div::fixed_lgd_cs($itemValue, $config);
				break;
			case 'strtoupper':
				$itemValue = strtoupper($itemValue);
				break;
			case 'strtolower':
				$itemValue = strtolower($itemValue);
				break;
			case 'shorten':
				$config = $config ? $config : 20;
				if (strlen($itemValue) > $config) {
					if (is_object($GLOBALS['LANG']))	{
						$itemValue = $GLOBALS['LANG']->csConvObj->crop($GLOBALS['LANG']->charSet, $itemValue, $config-3, '');
					} else {
						$itemValue = substr(t3lib_div::fixed_lgd_cs($itemValue, $config-3), 0, -3);
					}

					$pos = strrpos($itemValue, ' ');
					if ($pos > intval(strlen($itemValue)*0.7)) {
						$itemValue = substr($itemValue, 0 , $pos);
						$itemValue = $itemValue. ' ...';
					} else {
						$itemValue = $itemValue. '...';
					}
				}
				break;
			default:
			break;
		}

		return $itemValue;
	}

	/**
	 * Returns the "age" in minutes / hours / days / years of the number of $seconds inputted.
	 * Same as in t3lib_befunc (put here for FE usage).
	 *
	 * @param	integer		$seconds could be the difference of a certain timestamp and time()
	 * @param	string		$labels should be something like ' min| hrs| days| yrs'. This value is typically delivered by this function call: $GLOBALS["LANG"]->sL("LLL:EXT:lang/locallang_core.php:labels.minutesHoursDaysYears")
	 * @return	string		Formatted time
	 */
	function tools_calcAge($seconds, $labels='min|hrs|days|yrs')	{
		$labelArr = explode('|' ,$labels);
		$prefix = '';
		if ($seconds<0)	{$prefix='-'; $seconds=abs($seconds);}
		if ($seconds<3600)	{
			$seconds = round ($seconds/60).' '.trim($labelArr[0]);
		} elseif ($seconds<24*3600)	{
			$seconds = round ($seconds/3600).' '.trim($labelArr[1]);
		} elseif ($seconds<365*24*3600)	{
			$seconds = round ($seconds/(24*3600)).' '.trim($labelArr[2]);
		} else {
			$seconds = round ($seconds/(365*24*3600)).' '.trim($labelArr[3]);
		}
		return $prefix.$seconds;
	}


	/**
	 * Explode a string into a HTML tags attributes and it's values
	 *
	 * @param 	string 	$attributeString HTML tag or it's attributes
	 * @return array Attribute/value pairs
	 */
	function tools_explodeAttributes($attributeString) {
		$attributes = array();
		$attributeMatches = array();
		preg_match_all('# ([\w]+)="([^"]*)"#', $attributeString, $attributeMatches);

		if(count($attributeMatches[1])) {
			foreach($attributeMatches[2] as $name => $value) {
				$attributeMatches[2][$name] = htmlspecialchars_decode($value);
			}
			$attributes = array_combine($attributeMatches[1], $attributeMatches[2]);
		}
		return $attributes;
	}


	/**
	 * Implode aa array into a string to be used in HTML tags as attributes and it's values
	 *
	 * @param 	array 	$attributes Attribute name/value pairs
	 * @param 	boolean $hsc If set (default) all values will be htmlspecialchars()
	 * @return string 	attributes
	 */
	function tools_implodeAttributes($attributes, $hsc=true) {
		$attributeString = '';
		foreach($attributes as $name => $value) {
			$attributeString .= ' '.$name.'="'.($hsc ? htmlspecialchars($value) : $value).'"';
		}
		return $attributeString;
	}


	/**
	 * Inserts <wbr> in a string to make it possible to make a line break in a string without spaces.
	 *
	 * @param string $content
	 * @param integer $every After n chars a <wbr> will be inserted
	 * @return string
	 * @todo this could be done a little more intelligent: when a space is nearby don't insert <wbr>
	 */
	function tools_insertWordBreak($content, $every) {
		return chunk_split($content, $every, '<wbr>');
	}




	/***************************************
	 *
	 *   Misc stuff
	 *
	 ***************************************/


	/**
	 * Returns the label of the first found entry in an "items" array from $TCA (tablename=$table/fieldname=$col) where the value is $key
	 * Same as in t3lib_befunc (put here for FE usage).
	 *
	 * @param	string		Table name, present in $TCA
	 * @param	string		Field name, present in $TCA
	 * @param	string		items-array value to match
	 * @return	string		Label for item entry
	 */
	function getLabelFromItemlist($table,$col,$key)	{
		global $TCA;
			// Load full TCA for $table
		t3lib_div::loadTCA($table);

			// Check, if there is an "items" array:
		if (is_array($TCA[$table]) && is_array($TCA[$table]['columns'][$col]) && is_array($TCA[$table]['columns'][$col]['config']['items']))	{
				// Traverse the items-array...
			reset($TCA[$table]['columns'][$col]['config']['items']);
			while(list($k,$v)=each($TCA[$table]['columns'][$col]['config']['items']))	{
					// ... and return the first found label where the value was equal to $key
				if (!strcmp($v[1],$key))	return $v[0];
			}
		}
	}
}

// No XCLASS inclusion code: this class shouldn't be instantiated
//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_guifunc.php'])    {
//    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_guifunc.php']);
//}
?>
