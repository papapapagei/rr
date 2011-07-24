<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * DAM reference listing class
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
 *   78: class tx_dam_listfiles extends tx_dam_listbase
 *
 *              SECTION: Setup
 *  117:     function tx_dam_listreferences()
 *  127:     function __construct()
 *
 *              SECTION: Set data
 *  166:     function setPathInfo($pathInfo)
 *
 *              SECTION: Column rendering
 *  188:     function getItemColumns ($item)
 *
 *              SECTION: Column rendering
 *  272:     function getItemAction ($item)
 *  283:     function getItemIcon ($item)
 *
 *              SECTION: Controls
 *  349:     function getItemControl($item)
 *  390:     function getHeaderControl()
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once(PATH_txdam.'lib/class.tx_dam_listbase.php');


/**
 * Class for rendering of Media>Info
 * The class is not really abstract but on a good way to become so ...
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_listreferences extends tx_dam_listbase {

	/**
	 * stores two tx_dam_dir objects
	 */
	var $dataObjects = array();

	/**
	 * Columns to display among: page, content_element, softref_key, content_age, media_element, media_element_age
	 * The following is the default for show item.
	 */
	public $displayColumns = array('page', 'content_element', 'content_field', 'softref_key');

	/**
	 * Display refering page rootline
	 */
	public $showRootline = false;

	/**
	 * Enable alternating background colors in table rows
	 */
	public $showAlternateBgColors = true;

	/**
	 * Table name
	 */
	var $table = 'references';

	/***************************************
	 *
	 *	 Setup
	 *
	 ***************************************/

	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_listreferences()	{
		$this->__construct();
	}

	/**
	 * Initialization of object
	 * PHP5 constructor
	 *
	 * @return	void
	 */
	function __construct() {

		parent::__construct();

		$this->showMultiActions = false;
		$this->showAction = false;
		$this->showIcon = false;

		$this->paramName['setFolder'] = 'SET[tx_dam_folder]';
	}

	/***************************************
	 *
	 *	 Set data
	 *
	 ***************************************/

	/**
	 * Initialize the object
	 *
	 * @return	void
	 */
	function init() {
			// Table columns
		$this->clearColumns();
		foreach ($this->displayColumns as $element) {
			$this->addColumn($element, $GLOBALS['LANG']->sl('LLL:EXT:dam/lib/locallang.xml:' . $element));
		}
			// Table styling
		$this->elementAttr['table'] = ' border="0" cellpadding="0" cellspacing="0" style="width:100%;" class="typo3-dblist typo3-filelist"';

		$this->returnUrl = t3lib_div::_GP('returnUrl');
	}

	/***************************************
	 *
	 *	 Rendering
	 *
	 ***************************************/

	/**
	 * Renders the data columns
	 *
	 * @param	array		$item item array
	 * @return	array
	 */
	function getItemColumns ($item) {

			// Columns rendering
		$columns = array();
		foreach ($this->columnList as $field => $descr)	{
			switch($field)	{
				case 'page':
						// Create output item for pages record
					$pageRow = $item[$field];
					$rootline = t3lib_BEfunc::BEgetRootLine($pageRow['uid']);
					$pageOnClick = t3lib_BEfunc::viewOnClick($pageRow['uid'], $GLOBALS['BACK_PATH'], $rootline);
					$iconAltText = t3lib_BEfunc::getRecordIconAltText($pageRow, 'pages');
					$icon = t3lib_iconWorks::getIconImage('pages', $pageRow, $GLOBALS['BACK_PATH'], 'title="'.$iconAltText.'" align="top"');
					if ($this->showRootline) {
						$title = t3lib_BEfunc::getRecordPath($pageRow['uid'], '1=1', 0);
						$title = t3lib_div::fixed_lgd_cs($title, -($GLOBALS['BE_USER']->uc['titleLen']));
					} else {
						$title = t3lib_BEfunc::getRecordTitle('pages', $pageRow, TRUE);
					}
					if ($pageRow['doktype'] == 1 || $pageRow['doktype'] == 6) {
						if ($this->enableContextMenus) {
							$columns[$field] = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($icon, 'pages', $pageRow['uid'], 1, '', '+view,edit,info').$title;
						} else {
							$columns[$field] = '<a href="#" onclick="'.htmlspecialchars($pageOnClick).'">'.$icon.$title.'</a>';
						}
					} else {
						if ($this->enableContextMenus) {
							$columns[$field] = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($icon, 'pages', $pageRow['uid'], 1, '', '+edit,info').$title;
						} else {
							$columns[$field] = $icon.$title;
						}
					}
					break;
				case 'content_element':
						// Create output item for content record
					$refTable = $item['tablenames'];
					$refRow = $item[$field];
					if ($refTable == 'pages') {
							// The reference to the media is on a field of a page record
						if ($GLOBALS['BE_USER']->isInWebMount($refRow['uid']) && $GLOBALS['BE_USER']->doesUserHaveAccess($refRow, 1)) {
							$columns[$field] = tx_dam_SCbase::getRecordInfoEditLink($refTable, $refRow);
						} else {
							$pageRow = $refRow;
							$rootline = t3lib_BEfunc::BEgetRootLine($pageRow['uid']);
							$pageOnClick = t3lib_BEfunc::viewOnClick($pageRow['uid'], $GLOBALS['BACK_PATH'], $rootline);
							$iconAltText = t3lib_BEfunc::getRecordIconAltText($refRow, $refTable);
							$icon = t3lib_iconworks::getIconImage($refTable, $refRow, $GLOBALS['BACK_PATH'], 'class="c-recicon" align="top" title="'.$iconAltText.'"');
							$title = t3lib_BEfunc::getRecordTitle($refTable, $refRow, 1);
							if ($pageRow['doktype'] == 1 || $pageRow['doktype'] == 6) {
								$columns[$field] = '<a href="#" onclick="'.htmlspecialchars($pageOnClick).'">'.$icon.$title.'</a>';
							} else {
								$columns[$field] = $icon.$title;
							}
						}
					} else {
							// The reference to the media is on a field of a content element record
						if ($GLOBALS['BE_USER']->isInWebMount($pageRow['uid']) && $GLOBALS['BE_USER']->doesUserHaveAccess($pageRow, 1)) {
							$columns[$field] = tx_dam_SCbase::getRecordInfoEditLink($refTable, $refRow);
						} else {
							$pageRow = $item['page'];
							$rootline = t3lib_BEfunc::BEgetRootLine($pageRow['uid']);
							$pageOnClick = t3lib_BEfunc::viewOnClick($pageRow['uid'], $GLOBALS['BACK_PATH'], $rootline);
							$iconAltText = t3lib_BEfunc::getRecordIconAltText($refRow, $refTable);
							$icon = t3lib_iconworks::getIconImage($refTable, $refRow, $GLOBALS['BACK_PATH'], 'class="c-recicon" align="top" title="'.$iconAltText.'"');
							$title = t3lib_BEfunc::getRecordTitle($refTable, $refRow, 1);
							if ($pageRow['doktype'] == 1 || $pageRow['doktype'] == 6) {
								$columns[$field] = '<a href="#" onclick="'.htmlspecialchars($pageOnClick).'">'.$icon.$title.'</a>';
							} else {
								$columns[$field] = $icon.$title;
							}
						}
					}
					break;
				case 'content_field':
						// Create output item for reference field
					$columns[$field] = $item[$field];
					break;
				case 'softref_key':
						// Create output item for reference key
					$columns[$field] = $item['softref_key'] ? $GLOBALS['LANG']->sl('LLL:EXT:dam/lib/locallang.xml:softref_key_' . $item['softref_key']) : $GLOBALS['LANG']->sl('LLL:EXT:dam/lib/locallang.xml:softref_key_media');
					break;
				case 'content_age':
						// Create output text describing the age of the content element
					$columns[$field] = t3lib_BEfunc::dateTimeAge($item[$field], 1);
					break;
				case 'media_element':
						// Create output item for tx_dam record
					$columns[$field] = tx_dam_SCbase::getRecordInfoEditLink('tx_dam', $item);
					break;
				case 'media_element_age':
						// Create output text describing the tx_dam record age
					$columns[$field] = t3lib_BEfunc::dateTimeAge($item['tstamp'], 1);
					break;
				case '_CLIPBOARD_':
					$columns[$field] = $this->clipboard_getItemControl($item);
				break;
				case '_CONTROL_':
					 $columns[$field] = $this->getItemControl($item);
					 $this->columnTDAttr[$field] = ' nowrap="nowrap"';
				break;
				default:
					$content = $item[$field];
					$columns[$field] = htmlspecialchars(t3lib_div::fixed_lgd_cs($content, $this->titleLength));
				break;
			}
			if ($columns[$field] === '') {
				$columns[$field] = '&nbsp;';
			}
		}
			// Thumbsnails?
		if ($this->showThumbs AND $this->thumbnailPossible($item))	{
			$columns['media_element'] .= '<div style="margin:2px 0 2px 0;">'.$this->getThumbNail($item).'</div>';
		}
		return $columns;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listreferences.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listreferences.php']);
}
?>
