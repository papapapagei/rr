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
 *   90: class tx_dam_actionBase
 *
 *              SECTION: Initialization
 *  148:     function setItemInfo ($itemInfo)
 *  160:     function setEnv ($env)
 *
 *              SECTION: Get information
 *  183:     function isTypeValid ($type, $itemInfo=NULL, $env=NULL)
 *  206:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  219:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  230:     function getWantedPosition ($type)
 *  241:     function getWantedDivider ($type)
 *
 *              SECTION: Get values/content
 *  260:     function getIdName ()
 *  273:     function getIcon ($addAttribute='')
 *  282:     function getLabel ()
 *  291:     function getDescription ()
 *
 *              SECTION: Rendering
 *  312:     function render ($type, $disabled=false)
 *
 *              SECTION: Internal
 *  361:     function _getCommand()
 *  383:     function _renderControl ($iconImgTag, $hoverText, $command)
 *  413:     function _renderButton ($iconImgTag, $label, $hoverText, $command)
 *  449:     function _renderContext ($iconImgTag, $label, $command)
 *  471:     function _renderMulti ($label, $command)
 *  487:     function _addTitleToImg ($iconImgTag, $hoverText)
 *  502:     function _cleanAttribute($attribute)
 *
 * TOTAL FUNCTIONS: 19
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




/**
 * Action base class
 *
 * A action is something that renders buttons, control icons, ..., which executes command for an item.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage BaseClass
 * @see tx_dam_actionCall
 * @see tx_dam_action_viewFile
 * @example ../components/class.tx_dam_actionsFile.php
 */
class tx_dam_actionBase {

	/**
	 * ID string which identifies the action.By default the class name is used and don't have to be set.
	 * @var string
	 */
	var $idName = '';

	/**
	 * Stores the currently requested action type:
	 * icon, button, control, context
	 * @var string
	 */
	var $type;

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array();

	/**
	 * Environment
	 */
	 var $env = array(
	 	'returnUrl' => '',
	 	'defaultCmdScript' => '',
	 	'defaultEditScript' => '',
	 	'backPath' => '',
	 	);

	/**
	 * Information about the item the action should be performed on.
	 */
	 var $itemInfo = array();

	/**
	 * Defines if the item should be rendered disabled like a greyed icon.
	 * @access private
	 * @var boolean
	 */
	var $disabled;




	/***************************************
	 *
	 *	 Initialization
	 *
	 ***************************************/


	/**
	 * Set information array about the item the action should be performed on.
	 *
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @return	void
	 */
	function setItemInfo ($itemInfo) {
		$this->itemInfo = $itemInfo;
	}


	/**
	 * Set the environment which has additional information for the action.
	 * The environment must match the requested action type.
	 *
	 * @param	array		$env Environment array
	 * @return	void
	 */
	function setEnv ($env) {
		$this->env = $env;
	}





	/***************************************
	 *
	 *	 Get information
	 *
	 ***************************************/


	/**
	 * Returns true if the action is of the wanted type.
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isTypeValid ($type, $itemInfo=NULL, $env=NULL) {
		$this->type = $type;
		if ($itemInfo) {
			$this->setItemInfo($itemInfo);
		}
		if ($env) {
			$this->setEnv($env);
		}
		return in_array($type, $this->typesAvailable);
	}


	/**
	 * Returns true if the action is of the wanted type and works for the item.
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		return $this->isTypeValid ($type, $itemInfo, $env);
	}


	/**
	 * Returns true if the action is valid for the item. Strict check will be done.
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		return $this->isTypeValid ($type, $itemInfo, $env);
	}


	/**
	 * Tells the wanted position for a list of actions
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: after:tx_dam_newFolder;before:tx_other_item
	 */
	function getWantedPosition ($type) {
		return '';
	}


	/**
	 * Tells if a spacer/margin is wanted before/after the action
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @return	string		Example: "divider:spacer". Divider before and spacer after
	 */
	function getWantedDivider ($type) {
		return '';
	}



	/***************************************
	 *
	 *	 Get values/content
	 *
	 ***************************************/


	/**
	 * Returns an ID string which identifies the action.
	 * Used also for positioning action in a list like context menu
	 *
	 * @return	string
	 */
	function getIdName () {
		$this->idName = $this->idName ? $this->idName : get_class($this);
		return $this->idName;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
	}


	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $this->getLabel();
	}




	/***************************************
	 *
	 *	 Rendering
	 *
	 ***************************************/


	/**
	 * Returns the rendered action
	 *
	 * @param	string		$type Says what type of action is wanted
	 * @param	boolean		$disabled Will render a item a disabled. Eg. a greyed icon without link.
	 * @return	string
	 */
	function render ($type, $disabled=false) {
		$this->type = $type;
		$this->disabled = $disabled;

		$content = '';

		switch ($type) {
			case 'icon':
 				$content = $this->_renderControl ($this->getIcon(), $this->getDescription(), $this->_getCommand());
				break;

			case 'button':
 				$content = $this->_renderButton ($this->getIcon(), $this->getLabel(), $this->getDescription(), $this->_getCommand());
				break;

			case 'control':
			case 'globalcontrol':
 				$content = $this->_renderControl ($this->getIcon(), $this->getDescription(), $this->_getCommand());
				break;

			case 'context':
 				$content = $this->_renderContext ($this->getIcon(), $this->getLabel(), $this->_getCommand());
				break;

			case 'multi':
 				$content = $this->_renderMulti ($this->getLabel(), $this->_getCommand());
				break;

			default:
				break;
		}
		return $content;
	}




	/***************************************
	 *
	 *	 Internal
	 *
	 ***************************************/


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {
		$commands = array(
				'href' => '',
				'onclick' => '',
				'aTagAttribute' => '',
				'preHTML' => '',
				'postHTML' => '',
			);

		return $commands;
	}


	/**
	 * Render a linked icon
	 *
	 * @param	string		$iconImgTagReady Icon image tag. Might contain title="", if not the hover text will be inserted.
	 * @param	string		$hoverText The hover text.
	 * @param	string		$command Comand array
	 * @return	string		Button as HTML
	 * @access private
	 */
	function _renderControl ($iconImgTag, $hoverText, $command) {

		if (($this->disabled) OR ($command['href'] === false AND $command['onclick'] === false)) {
			$aTags = array('', '');
		}
		else {
			$onclick = '';
			if ($command['onclick']) {
				$onclick = ' onclick="'.htmlspecialchars($command['onclick']).'"';
				$command['href'] = $command['href'] ? $command['href'] : '#';
			}
			$aTags[0] = '<a href="'.htmlspecialchars($command['href']).'"'.$onclick.$this->_cleanAttribute($command['aTagAttribute']).'>';
			$aTags[1] = '</a>';
		}
		$iconImgTag = $this->_addTitleToImg ($iconImgTag, $hoverText);

		return $command['preHTML'].$aTags[0].$iconImgTag.$aTags[1].$command['postHTML'];
	}


	/**
	 * Render a GUI button in HTML
	 *
	 * @param	string		$iconImgTagReady Icon image tag. Might contain title="", if not the hover text will be inserted.
	 * @param	string		$label The button label.
	 * @param	string		$hoverText The hover text
	 * @param	string		$command Comand array
	 * @return	string		Button as HTML
	 * @access private
	 */
	function _renderButton ($iconImgTag, $label, $hoverText, $command) {

		if ($this->disabled OR ($command['href'] === false AND $command['onclick'] === false)) {
			$aTags = array('', '');
		}
		else {
			$onclick = '';
			if ($command['onclick']) {
				$onclick = ' onclick="'.htmlspecialchars($command['onclick']).'"';
				$command['href'] = $command['href'] ? $command['href'] : '#';
			}
			$aTags[0] = '<a href="'.htmlspecialchars($command['href']).'"'.$onclick.$this->_cleanAttribute($command['aTagAttribute']).'>';
			$aTags[1] = '</a>';
		}
		$iconImgTag = $this->_addTitleToImg ($iconImgTag, $hoverText);
		$hoverText = $hoverText ? ' title="'.htmlspecialchars($hoverText).'" ' : '';
		return $command['preHTML'].$aTags[0].$iconImgTag.$aTags[1].$command['postHTML'];
	}


	/**
	 * Render a context menu entry
	 *
	 * The return array has following format. Values can be used to pass to clickmenu::linkItem()
	 * command array + array (
	 * ['label']	string		The label
	 * ['icon']		string		<img>-tag for the icon
	 * ['onlyCM']	boolean		==1 and the element will NOT appear in clickmenus in the topframe (unless clickmenu is totally unavailable)! ==2 and the item will NEVER appear in top frame. (This is mostly for "less important" options since the top frame is not capable of holding so many elements horizontally)
	 *
	 * @param	string		$iconImgTagReady Icon image tag. Might contain title="", if not the hover text will be inserted.
	 * @param	string		$label The cm label. Expected to be already htmlspecialchars().
	 * @param	string		$command Comand array
	 * @return	array		Command data to be processed outside
	 * @see alt_clickmenu.php
	 * @access private
	 */
	function _renderContext ($iconImgTag, $label, $command) {
		$cm = $command;
		$cm['label'] = $label;
		$cm['icon'] = $iconImgTag;

		return $cm;
	}


	/**
	 * Render a context menu entry
	 *
	 * The return array has following format. Values can be used to create select options
	 * command array + array (
	 * ['label']	string		The label
	 *
	 * @param	string		$label The cm label
	 * @param	string		$command Comand array
	 * @return	array		Command data to be processed outside
	 * @see alt_clickmenu.php
	 * @access private
	 */
	function _renderMulti ($label, $command) {
		$cm = $command;
		$cm['label'] = $label;

		return $cm;
	}


	/**
	 * Insert a title to to an img tag if not yet there
	 *
	 * @param	string		$iconImgTagReady Icon image tag. Might contain title="", if not the hover text will be inserted.
	 * @param	string		$hoverText The hover text
	 * @return	string		img tag
	 * @access private
	 */
	function _addTitleToImg ($iconImgTag, $hoverText) {
		if ($hoverText AND stripos($iconImgTag, 'title="') === false) {
			$iconImgTag = str_ireplace('<img ', '<img title="'.htmlspecialchars($hoverText).'" ', $iconImgTag);
		}
		return $iconImgTag;
	}


	/**
	 * Prepend a space to an tag attribute
	 *
	 * @param	string		$attribute
	 * @return	string
	 * @access private
	 */
	function _cleanAttribute($attribute) {
		return ($attribute ? ' '.$attribute : '');
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_actionbase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_actionbase.php']);
}
?>
