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
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Treelib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   95: class tx_dam_treelib_tceforms
 *
 *              SECTION: Getter / Setter
 *  172:     function init($PA, &$fObj)
 *  194:     function setIFrameContentRendering ($IFrameContentRendering=true, $jsParent='parent.')
 *  208:     function isIFrameContentRendering ()
 *  219:     function isIFrameRendering ()
 *  230:     function setItemArray ($itemArray)
 *  240:     function getItemArrayProcessed ()
 *  250:     function getItemCountSelectable()
 *  260:     function getItemCountTrees()
 *  269:     function getTreeContent()
 *
 *              SECTION: Rendering
 *  292:     function renderBrowsableTrees ($browseTrees, $useMounts=true, $divTreeAttribute=' style="margin:5px"' )
 *  364:     function renderBrowsableMountTrees ($browseTrees, $divTreeAttribute=' style="margin:5px"' )
 *
 *              SECTION: Div-Frame specific stuff
 *  440:     function renderDivBox ($width=NULL, $height=NULL)
 *
 *              SECTION: IFrame specific stuff
 *  472:     function setIFrameTreeBrowserScript ($script)
 *  485:     function renderIFrame ($width=NULL, $height=NULL)
 *  513:     function getIFrameParameter ($table, $field, $uid)
 *
 *              SECTION: Rendering tools
 *  539:     function calcFrameSizeCSS($itemCountSelectable=NULL)
 *
 *              SECTION: Data tools
 *  578:     function getMountsForTree($treeName, $userMountField='tx_dam_mountpoints', $groupMountField='tx_dam_mountpoints')
 *  631:     function processItemArray($treeViewObj)
 *  664:     function getItemFormElValueIdArr ($itemFormElValue)
 *
 * TOTAL FUNCTIONS: 19
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */






/**
 * TCEforms functions for handling and rendering of trees for group/select elements
 *
 * If we want to display a browseable tree, we need to run the tree in an iframe element.
 * In consequence this means that the display of the browseable tree needs to be generated from an extra script.
 * This is the base class for such a script.
 *
 * The class itself do not render the tree but call tceforms to render the field.
 * In beforehand the TCA config value of treeViewBrowseable will be set to 'iframeContent' to force the right rendering.
 *
 * That means the script do not know anything about trees. It just set parameters and render the field with TCEforms.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Treelib
 */
class tx_dam_treelib_tceforms {

	/**
	 * count rendered tree items - just for frame height calculation
	 * @var integer
	 */
	var $treeItemC = 0;

	/**
	 * count rendered trees
	 * @var integer
	 */
	var $treesC = 0;

	/**
	 * Rendered trees as HTML
	 * @var string
	 * @access private
	 */
	var $treeContent = '';

	/**
	 * itemArray for usage in TCEforms
	 * This holds the original values
	 * @var array
	 * @access private
	 */
	var $itemArray = array();

	/**
	 * itemArray for usage in TCEforms
	 * This holds the processed values with titles/labels
	 * @var array
	 * @access private
	 */
	var $itemArrayProcessed = array();

	/**
	 * Defines if the content of the iframe should be rendered instead of the iframe itself.
	 * This is for iframe mode.
	 * @var boolean
	 * @access private
	 */
	var $iframeContentRendering = false;

	/**
	 * Defines the prefix used for JS code to call the parent window.
	 * This is for iframe mode.
	 * @var string
	 * @access private
	 */
	var $jsParent = '';



	var $tceforms;
	var $PA;
	var $table;
	var $field;
	var $row;
	var $config;


	/**********************************************************
	 *
	 * Getter / Setter
	 *
	 ************************************************************/


	/**
	 * Init
	 *
	 * @param	array		$PA An array with additional configuration options.
	 * @param	object		$fobj TCEForms object reference
	 * @return	void
	 */
	function init($PA, &$fObj)	{
		$this->tceforms = &$PA['pObj'];
		$this->PA = &$PA;

		$this->table = $PA['table'];
		$this->field = $PA['field'];
		$this->row = $PA['row'];
		$this->config = $PA['fieldConf']['config'];

			// set currently selected items
		$itemArray = t3lib_div::trimExplode(',', $this->PA['itemFormElValue'], true);
		$this->setItemArray($itemArray);

		$this->setIFrameContentRendering($this->config['treeViewBrowseable']==='iframeContent');
	}


	/**
	 * Enable the iframe content rendering mode
	 *
	 * @return void
	 */
	function setIFrameContentRendering ($IFrameContentRendering=true, $jsParent='parent.') {
		if ($this->iframeContentRendering = $IFrameContentRendering) {
			$this->jsParent = $jsParent;
		} else {
			$this->jsParent = '';
		}
	}


	/**
	 * Returns true if iframe content rendering mode is enabled
	 *
	 * @return boolean
	 */
	function isIFrameContentRendering () {
		return $this->iframeContentRendering;
	}



	/**
	 * Returns true if iframe content rendering mode is enabled
	 *
	 * @return boolean
	 */
	function isIFrameRendering () {
		return ($this->config['treeViewBrowseable'] && !$this->iframeContentRendering);
	}


	/**
	 * Set the selected items
	 *
	 * @param array $itemArray
	 * @return void
	 */
	function setItemArray ($itemArray) {
		$this->itemArray = $itemArray;
	}


	/**
	 * Return the processed aray of selected items
	 *
	 * @return array
	 */
	function getItemArrayProcessed () {
		return $this->itemArrayProcessed;
	}


	/**
	 * Return the count value of selectable items
	 *
	 * @return integer
	 */
	function getItemCountSelectable() {
		return $this->treeItemC;
	}


	/**
	 * Return the count value of rendered trees
	 *
	 * @return integer
	 */
	function getItemCountTrees() {
		return $this->treesC;
	}

	/**
	 * Returns the rendered trees (HTML)
	 *
	 * @return string
	 */
	function getTreeContent() {
		return $this->treeContent;
	}





	/**********************************************************
	 *
	 * Rendering
	 *
	 ************************************************************/


	/**
	 * Renders one ore more trees
	 *
	 * @param 	array 	$browseTrees Array of browse trees to display: array($treeName => $treeViewObj)
	 * @param	boolean	$useMounts If set mounts will be initialized
	 * @param 	string 	$divTreeAttribute Each tree is wrapped in a div-tag. This defines attributes for the tag.
	 * @return 	string the rendered trees (HTML)
	 */
	function renderBrowsableTrees ($browseTrees, $useMounts=true, $divTreeAttribute=' style="margin:5px"' ) {

		$this->treeItemC = 0;
		$this->treesC = 0;
		$this->treeContent = '';
		$this->itemArrayProcessed = array();

		if (is_array($browseTrees)) {
			foreach($browseTrees as $treeName => $treeViewObj)	{

				if ($treeViewObj->isTCEFormsSelectClass) {
					$treeViewObj->backPath = $this->tceforms->backPath;
					$treeViewObj->mode = 'tceformsSelect';
					$treeViewObj->TCEforms_itemFormElName = $this->PA['itemFormElName'];
					$treeViewObj->setRecs = true;
					$treeViewObj->jsParent = $this->jsParent;
					$treeViewObj->ext_IconMode = true; // no context menu on icons

						// this needs to be true for multiple trees but then TCE can't handle it as "select" or "group"
					$treeViewObj->TCEFormsSelect_prefixTreeName = false;


					if ($treeViewObj->supportMounts) {
						$mounts = $this->getMountsForTree($treeViewObj->getTreeName());

						if (count($mounts)) {
							$treeViewObj->setMounts($mounts);
						} else {
							continue;
						}
					}

					$tree = '';

					if ($this->isIFrameContentRendering()) {
						$treeViewObj->expandAll = false;
						$treeViewObj->thisScript = t3lib_div::getIndpEnv('SCRIPT_NAME');
						$treeViewObj->PM_addParam = $this->getIFrameParameter($this->table, $this->field, $this->row['uid']);

					} else {
						$treeViewObj->expandAll = true;
						$treeViewObj->expandFirst = true;
					}

					$treeViewObj->selectedIdArr = $this->getItemFormElValueIdArr($this->PA['itemFormElValue']);

					$tree = $treeViewObj->getBrowsableTree();
					$this->treeItemC += count($treeViewObj->ids)+1;

					if ($tree) {
						$this->treeContent .= '<div'.$divTreeAttribute.'>'.$tree.'</div>';
						$this->treesC += 1;
					}


						// process selected items - get names
					$this->processItemArray($treeViewObj);
				}
			}
		}
		return $this->treeContent;
	}



	/**
	 * Renders one ore more trees
	 *
	 * @param 	array 	$browseTrees Array of browse trees to display: array($treeName => $treeViewObj)
	 * @param 	string 	$divTreeAttribute Each tree is wrapped in a div-tag. This defines attributes for the tag.
	 * @return 	string the rendered trees (HTML)
	 */
	function renderBrowsableMountTrees ($browseTrees, $divTreeAttribute=' style="margin:5px"' ) {

		$this->treeItemC = 0;
		$this->treesC = 0;
		$this->treeContent = '';
		$this->itemArrayProcessed = array();

		if (is_array($browseTrees)) {
			foreach($browseTrees as $treeName => $treeViewObj)	{

				if ($treeViewObj->isTCEFormsSelectClass AND $treeViewObj->supportMounts) {
					$treeViewObj->backPath = $this->tceforms->backPath;
					$treeViewObj->mode = 'tceformsSelect';
					$treeViewObj->TCEforms_itemFormElName = $this->PA['itemFormElName'];
					$treeViewObj->setRecs = true;
					$treeViewObj->jsParent = $this->jsParent;
					$treeViewObj->ext_IconMode = true; // no context menu on icons

						// this needs to be true for multiple trees but then TCE can't handle it as "select" or "group" - so "passthrough" is needed
					$treeViewObj->TCEFormsSelect_prefixTreeName = true;

					$tree = '';

					if ((string)$treeViewObj->supportMounts=='rootOnly') {
						$tree = $treeViewObj->printRootOnly();
						$this->treeItemC += 1;

					} else {
						if ($this->isIFrameContentRendering()) {

							$treeViewObj->expandAll = false;
							$treeViewObj->thisScript = t3lib_div::getIndpEnv('SCRIPT_NAME');
							$treeViewObj->PM_addParam = $this->getIFrameParameter($this->table, $this->field, $this->row['uid']);

						} else {
							$treeViewObj->expandAll = true;
							$treeViewObj->expandFirst = true;
						}

						$tree = $treeViewObj->getBrowsableTree();
						$this->treeItemC += count($treeViewObj->ids)+1;
					}

					if ($tree) {
						$this->treeContent .= '<div'.$divTreeAttribute.'>'.$tree.'</div>';
						$this->treesC += 1;
					}


						// process selected items - get names
					$this->processItemArray($treeViewObj);
				}
			}
		}

		return $this->treeContent;
	}





	/**********************************************************
	 *
	 * Div-Frame specific stuff
	 *
	 ************************************************************/


	/**
	 * Returns div HTML code which includes the rendered tree(s).
	 *
	 * @param	string $width CSS width definition
	 * @param	string $height CSS height definition
	 * @return	string HTML content
	 */
	function renderDivBox ($width=NULL, $height=NULL) {
		if ($width==NULL) {
			list($width, $height) = $this->calcFrameSizeCSS();
		}

		$divStyle = 'position:relative; left:0px; top:0px; height:'.$height.'; width:'.$width.';border:solid 1px;overflow:auto;background:#fff;';
		$divFrame = '<div  name="'.$this->PA['itemFormElName'].'_selTree" style="'.htmlspecialchars($divStyle).'">';
		$divFrame .= $this->treeContent;
		$divFrame .= '</div>';

		return $divFrame;
	}






	/**********************************************************
	 *
	 * IFrame specific stuff
	 *
	 ************************************************************/


	/**
	 * Set the script to be called for the iframe tree browser.
	 *
	 * @param 	string 	$script Path to the script
	 * @return	void
	 * @see tx_dam_treelib_browser
	 */
	function setIFrameTreeBrowserScript ($script) {
		$this->treeBrowserScript = $script;
	}


	/**
	 * Returns iframe HTML code to call the tree browser script.
	 *
	 * @param	string $width CSS width definition
	 * @param	string $height CSS height definition
	 * @return	string HTML content
	 * @see tx_dam_treelib_browser
	 */
	function renderIFrame ($width=NULL, $height=NULL) {

		if(!$this->treeBrowserScript) {
			die ('tx_dam_treelib_tceforms: treeBrowserScript is not set!');
		}

		if ($width==NULL) {
			list($width, $height) = $this->calcFrameSizeCSS();
		}


		$table = $GLOBALS['TCA'][$this->table]['orig_table'] ? $GLOBALS['TCA'][$this->table]['orig_table'] : $this->table;

		$iFrameParameter = $this->getIFrameParameter($table, $this->field, $this->row['uid']);

		$divStyle = 'height:'.$height.'; width:'.$width.'; border:solid 1px #000; background:#fff;';
		$iFrame = '<iframe src="'.htmlspecialchars($this->treeBrowserScript.'?'.$iFrameParameter).'" name="'.$this->PA['itemFormElName'].'_selTree" border="1" style="'.htmlspecialchars($divStyle).'">';
		$iFrame .= '</iframe>';
		return $iFrame;
	}


	/**
	 * Returns GET parameter string to be passed to the tree browser script.
	 *
	 * @param 	string $table
	 * @param 	string $field
	 * @param 	string $uid
	 * @return 	string
	 * @see tx_dam_treelib_browser
	 */
	function getIFrameParameter ($table, $field, $uid) {
		$params = array();
			
		$config = '';
		if ($GLOBALS['TCA'][$table]['columns'][$field]['config']['type'] == 'flex') {
			$config = base64_encode(serialize($this->PA['fieldConf']));			
		}
		
		$params['table'] = $table;
		$params['field'] = $field;
		$params['uid'] = $uid;
		$params['elname'] = $this->PA['itemFormElName'];
		$params['config'] = $config;
		$params['seckey'] = t3lib_div::shortMD5(implode('|', $params).'|'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
		return t3lib_div::implodeArrayForUrl('', $params);
	}





	/**********************************************************
	 *
	 * Rendering tools
	 *
	 ************************************************************/



	/**
	 * calculate size of the tree frame
	 *
	 * @return array array($width, $height)
	 */
	function calcFrameSizeCSS($itemCountSelectable=NULL) {

		if ($itemCountSelectable===NULL) {
			$itemCountSelectable = max (1, $this->treeItemC + $this->treesC + 1);
		}

		$width = '240px';

		$this->config['autoSizeMax'] = t3lib_div::intInRange($this->config['autoSizeMax'], 0);
		$height = $this->config['autoSizeMax'] ? t3lib_div::intInRange($itemCountSelectable, t3lib_div::intInRange($this->config['size'], 1), $this->config['autoSizeMax']) : $this->config['size'];

			// hardcoded: 16 is the height of the icons
		$height = ($height*16).'px';
			// em height needs a factor - don't know why
		#$height = intval($height*1.5).'em';

		return array($width, $height);
	}




	/**********************************************************
	 *
	 * Data tools
	 *
	 ************************************************************/


	/**
	 * Returns the mounts for the selection classes stored in user and/or group fields.
	 * The storage format is different from TCE select fields. For each item the treeName is prefixed with ':'.
	 *
	 * @param	string		$treeName: ...
	 * @param	string		$userMountField: ...
	 * @param	string		$groupMountField: ...
	 * @return	array		Mount data array for usage with treeview class
	 * @see renderBrowsableMountTrees()
	 */
	function getMountsForTree($treeName, $userMountField='tx_dam_mountpoints', $groupMountField='tx_dam_mountpoints') {
		global $BE_USER;

		$mounts = array();

		if($GLOBALS['BE_USER']->user['admin']){
			$mounts = array(0 => 0);
			return $mounts;
		}

		if ($GLOBALS['BE_USER']->user[$userMountField]) {
			 $values = explode(',',$GLOBALS['BE_USER']->user[$userMountField]);
			 foreach($values as $mount) {
			 	list($k,$id) = explode(':', $mount);
			 	if ($k == $treeName) {
					$mounts[$id] = $id;
			 	}
			 }
		}

		if(is_array($GLOBALS['BE_USER']->userGroups)){
			foreach($GLOBALS['BE_USER']->userGroups as $group){

				if ($group[$groupMountField]) {
					$values = explode(',',$group[$groupMountField]);
					 foreach($values as $mount) {
					 	list($k,$id) = explode(':', $mount);
					 	if ($k == $treeName) {
							$mounts[$id] = $id;
					 	}
					 }
				}
			}
		}

			// if root is mount just set it and remove all other mounts
		if(isset($mounts[0])) {
			$mounts = array(0 => 0);
		}

		return $mounts;
	}


	/**
	 * Process selected items in $this->itemArray
	 * Get names for items, return and store the result in $this->itemArrayProcessed
	 *
	 * @param 	array 	$browseTrees Array of browse trees: array($treeName => $treeViewObj)
	 * @return 	array 
	 */
	function processItemArrayForBrowseableTrees ($browseTrees) {

		$this->itemArrayProcessed = array();

		if (is_array($browseTrees)) {
			foreach($browseTrees as $treeName => $treeViewObj)	{

				if ($treeViewObj->isTCEFormsSelectClass) {
					$treeViewObj->mode = 'tceformsSelect';

						// process selected items - get names
					$this->processItemArray($treeViewObj);
				}
			}
		}
		return $this->itemArrayProcessed;
	}


	/**
	 * Process selected items in $this->itemArray
	 * Get names for items and store the result in $this->itemArrayProcessed
	 *
	 * @param object Tree object
	 * @return void
	 */
	function processItemArray($treeViewObj) {
		foreach ($this->itemArray as $tk => $tv) {
			list($itemId, $itemName) = explode('|', $tv, 2);
			list($treeName, $treeId) = explode(':', $itemId);

			if ($itemName) {
				$this->itemArrayProcessed[$tk] = $tv;

			} else {
				if (is_null($treeId)) {

						// single tree
					# $itemName = $treeViewObj->getTitleStr($treeViewObj->recs[$itemId]);
					$itemName = $treeViewObj->getTitleStr($treeViewObj->getRecord($itemId));
					$this->itemArrayProcessed[$tk] = $itemId.'|'.$itemName;

				} else {

						// multiple trees
					$itemName = '';
					if($treeName==$treeViewObj->getTreeName()) {
						if(isset($treeViewObj->recs[$treeId]) OR $treeId==0) {
							$itemName = $treeViewObj->getTreeTitle();
							if ($treeId) {
								# $itemTitle = $treeViewObj->getTitleStr($treeViewObj->recs[$treeId]);
								$itemTitle = $treeViewObj->getTitleStr($treeViewObj->getRecord($treeId));
								$itemName .= $itemTitle ? ': '.$itemTitle : '';
							} else {
								$itemName .= ' (Root)';
							}
							$this->itemArrayProcessed[$tk] = $itemId.'|'.$itemName;
						}
					}
				}
			}
		}

	}



	/**
	 * Extracts the id's from $PA['itemFormElValue'] in standard TCE format.
	 *
	 * @return array
	 */
	function getItemFormElValueIdArr ($itemFormElValue) {
		$out = array();
		$tmp1 = t3lib_div::trimExplode(',', $itemFormElValue, true);
		foreach ($tmp1 as $value) {
			$tmp2 = t3lib_div::trimExplode('|', $value, true);
			$out[] = $tmp2[0];
		}
		return $out;
	}

}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/treelib/class.tx_dam_treelib_tceforms.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/treelib/class.tx_dam_treelib_tceforms.php']);
}



?>