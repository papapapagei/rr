<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Richard Bausek (office[at]artibella.com)
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
 * Plugin 'Flash Movie' for the 'rb_flashobject' extension.
 *
 * @author	Richard Bausek <office[at]artibella.com>
 */


require_once(PATH_tslib."class.tslib_pibase.php");

class tx_rbflashobject_pi1 extends tslib_pibase {
	var $prefixId = "tx_rbflashobject_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_rbflashobject_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "rb_flashobject";	// The extension key.
	
	var $allowCaching;
	var $includeJS;
	var $params = array();
	var $flashVars = array();
	var $dbTable = 'tx_rbflashobject_movie';
	var $movieFolder = 'uploads/tx_rbflashobject/';
	var $idString;
	var $jsObj;
	var $movieIDPrefix = 'swf_';
	var $altContentIDPrefix = 'alt_';
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		//$extPath = t3lib_extMgm::extPath($this->extKey);
		//$extPath = str_replace('../','', $extPath);
		$this->init($conf);
		if($this->conf['tsMode'] == 0){
			$output = $this->writeFlashMovie();
		}else {
			$output = $this->writeFlashMovieTS();
		}
	  	return $output;	  
	}
	
	function init($conf) {

		$this->conf = $conf; //store configuration
		$this->pi_loadLL(); // Loading language-labels
		$this->pi_setPiVarDefaults(); // Set default piVars from TS
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin

		// Configure caching
		$this->allowCaching = $this->conf['allowCaching']?1:0;
		if (!$this->allowCaching) {
			$GLOBALS['TSFE']->set_no_cache();
		}
		//prefix for flash movies (DOM ID)
		$this->movieIDPrefix = (isset($this->conf['movieIDPrefix'])) ? $this->conf['movieIDPrefix'] : $this->movieIDPrefix;
		//prefix for alternative content (DOM ID)
		$this->altContentIDPrefix = (isset($this->conf['altContentIDPrefix'])) ? $this->conf['altContentIDPrefix'] : $this->altContentIDPrefix;		
		//include JS file
		$this->includeJS = $this->conf['includeJSFile']?1:0;
		if ($this->includeJS) {
			$this->includeJSFile();
		}
		//set id of container div
		#$this->idString = isset($this->cObj->data['uid']) ? $this->cObj->data['uid'] : time();
		$this->idString = substr(md5 (uniqid (rand())), 0, 10);
		$this->jsObj = 'swfObj_' . $this->idString;		
	}
	
	function writeFlashMovie (){
		//get flexform values
		$ffVars =array();
	  	$ffVars['selection'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'movieSelection', 'sDEF'); 

	  	//load the movie data
	  	$singleWhere = $this->dbTable.'.uid=' . intval($ffVars['selection']);
	  	$singleWhere .= $this->cObj->enableFields($this->dbTable);
	  	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dbTable, $singleWhere);
	  	$movieValues = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    	$output = '';
	  	if(is_array($movieValues)){
	  		$movieValues['flashmovie'] = $this->movieFolder.$movieValues['flashmovie'];	  
	  		$movieValues['quality'] = (!$movieValues['quality']) ? 'high' : 'low';	  
	  		$movieValues['displaymenu'] = $movieValues['displaymenu']?'true':'false';
	  		$this->addFlashParam('menu',$movieValues['displaymenu']);	  
	  		$output .= $this->writeAlternativeContent($movieValues);
	  		$output .= $this->writeFlashJS($movieValues);
	  	}else{
	  		//no flash movie record is there, display error message
			$output = $this->errorMessage();
	  	}
	  	return $output;	
	}
	
	function writeFlashMovieTS (){
		$output = '';
		$movieValues = $this->conf['ts_content.'];
		if(is_array($movieValues)){
			$movieValues['displaymenu'] = $movieValues['displaymenu']?'true':'false';
			$this->addFlashParam('menu',$movieValues['displaymenu']);
			$output .= $this->writeAlternativeContent($movieValues);
	  		$output .= $this->writeFlashJS($movieValues);
		}else{
	  		//no flash movie record is there, display error message
			$output = $this->errorMessage();
	  	}
		return $output;
	}
	
	function includeJSFile(){
		$extPath = substr(t3lib_extMgm::extPath($this->extKey), strlen(PATH_site));
		$jsFilePath = $extPath . 'js/swfobject.js';
		$extraJS = '<script src="' . $jsFilePath . '" type="text/javascript"><!-- //--></script>';
		 $GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = $extraJS;	
	}
	
	function writeAlternativeContent ($contentConf){	
		$this->conf['altContent.']['source'] = isset($contentConf['alternativecontent']) ? $contentConf['alternativecontent'] : $this->conf['default.']['altContentUid'] ;
		$altContent = '<div' . $this->pi_classParam("swf_altcontent") . ' id="' . $this->movieIDPrefix . $this->idString . '">';
		//$altContent .= $this->cObj->stdWrap($this->cObj->RECORDS($this->conf['altContent.']), $this->conf['altContent_stdWrap.']);
		$altContent .= $this->cObj->stdWrap($this->cObj->cObjGetSingle($this->conf['altContent'],$this->conf['altContent.']), $this->conf['altContent_stdWrap.']);
		$altContent .= '</div>';
		return $altContent;
	}
	
	function writeFlashJS ($contentConf){
		//get background color
		$bgCol = ($contentConf['backgroundcolor']) ? $contentConf['backgroundcolor'] : $this->conf['default.']['bgColor'];
		//get flash version
		$version = ($contentConf['requiredversion']) ? $contentConf['requiredversion'] : $this->conf['default.']['version'];
		//get the link of the redirect url
		$redirectUid = $contentConf['redirecturl'] ? $contentConf['redirecturl'] : $this->conf['default.']['redirecturl'];
		$this->conf['redirecturl.']['parameter'] = $redirectUid;
		$redirect = $this->cObj->typolink('', $this->conf['redirecturl.']);

		//add the params
		$this->addFlashParams($contentConf);
		//add the variables
		$this->addFlashVars($contentConf);
		$flashObj = 'flashObj_' . $this->idString;
		$jsCode = chr(10).'<script type="text/javascript">' . chr(10);
		$jsCode .= 'var '. $this->jsObj . ' = new SWFObject("'. $contentConf['flashmovie'] .'", "swf_'. $this->idString .'", "'.$contentConf['width'].'", "'.$contentConf['height'].'", "'.$version.'", "'. $bgCol .'","","'.$contentConf['quality'] .'","","'. $redirect .'","'. $this->conf['detectkey'] .'");' . chr(10);
   		$jsCode .= $this->writeFlashParams();
   		$jsCode .= $this->writeFlashVars();
   		$jsCode .= $this->jsObj.'.write("' . $this->movieIDPrefix . $this->idString .'");' . chr(10);   		
   		$jsCode .= '</script>' . chr(10);
		return $jsCode;
	}
	
	
	function addFlashVars ($contentConf){
		#debug($contentConf['additionalvars']);
		$flashVars = t3lib_div::trimExplode(chr(10), $contentConf['additionalvars'], 1);
		if(is_array($flashVars)){		
			while (list(, $val) = each($flashVars)) {
				$var = explode('|', $val, 2);
				if(is_array($var)){
					$this->addFlashVar(trim($var[0]), trim($var[1]));
				}	
			}
		}
		
	}
	
	function addFlashVar ($key, $value){
		
		//check if data is a getText or a regular string
		$gTStr = 'GT:';
		$gTStrLength = strlen($gTStr);

		if(substr(strtoupper($value), 0,$gTStrLength) == $gTStr){
			$gT = substr($value,$gTStrLength);
			$this->flashVars[$key] = $this->cObj->getData(trim($gT),'');
		}else{
			$this->flashVars[$key] = $value;
		}
	}
	
	function writeFlashVars (){
		$content = '';
		foreach($this->flashVars as $k => $v){
			$content .= $this->jsObj.'.addVariable("'.urlencode($k).'", "'. urlencode($v) .'");' . chr(10);
		}
		return $content;
	}

	function addFlashParams ($contentConf){
		#debug($contentConf['additionalparams']);
		$flashParams = t3lib_div::trimExplode(chr(10), $contentConf['additionalparams'], 1);
		//add base param
		$this->addFlashParam('base', t3lib_div::getIndpEnv ('TYPO3_REQUEST_DIR'));
		if(is_array($flashParams)){		
			while (list(, $val) = each($flashParams)) {
				$var = t3lib_div::trimExplode('|', $val, 1);
				$this->addFlashParam($var[0], $var[1]);
			}
		}		
	}
	
	function addFlashParam ($key, $value){
		$this->params[$key] = $value;
	}
	
	function writeFlashParams (){
		$content = '';
		foreach($this->params as $k => $v){
			$content .= $this->jsObj.'.addParam("'.htmlspecialchars($k).'", "'. htmlspecialchars($v) .'");' . chr(10);
		}
		return $content;
	}
	
	function errorMessage(){
		$output = '<div' . $this->pi_classParam("error") . '>';
	  	$output .= $this->pi_getLL('error_msg','No Flash movie to display');
	  	$output .= '</div>';
	  	return $output;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/rb_flashobject/pi1/class.tx_rbflashobject_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/rb_flashobject/pi1/class.tx_rbflashobject_pi1.php"]);
}

?>