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
 * Show available index services.
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
 *   62: class tx_dam_svlist
 *   88:     function tx_dam_svlist()
 *  107:     function serviceTypeList_loaded()
 *  165:     function serviceListRowHeader($type, $bgColor,$cells,$import=0)
 *  191:     function serviceListRow($type,$eKey,$eConf,$info,$cells,$bgColor="",$inst_list=array(),$import=0,$altLinkUrl='')
 *  244:     function showSearchPaths()
 *  272:     function wrapEmail($str,$email)
 *  287:     function getExtPath($extKey,$conf)
 *  303:     function includeEMCONF($path,$_EXTKEY)
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



/**
 * Show available index services.
 * This includes some code from the EM
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
class tx_dam_svlist {

	var $pObj;

	var $typePaths = array();
	var $typeBackPaths = array();

	var $typeRelPaths = array(
		'S' => 'sysext/',
		'G' => 'ext/',
		'L' => '../typo3conf/ext/',
	);

	var $svDef = array(
		'metaExtract' => array(
			'desc' => 'Read meta data from files.',
			),
		'textExtract' => array(
			'desc' => 'Extract pure text out of files like doc, pdf, rtf, ...',
			),
		'textLang' => array(
			'desc' => 'Detects languages by example text.',
			),
	);


	function tx_dam_svlist()	{
		$this->typePaths = array(
			'S' => TYPO3_mainDir.'sysext/',
			'G' => TYPO3_mainDir.'ext/',
			'L' => 'typo3conf/ext/'
		);
		$this->typeBackPaths = array(
			'S' => '../../../',
			'G' => '../../../',
			'L' => '../../../../'.TYPO3_mainDir
		);
	}


	/**
	 * Listing of loaded (installed) services types
	 *
	 * @return	string HTML content
	 */
	function serviceTypeList_loaded()	{
		global $T3_SERVICES, $TYPO3_LOADED_EXT;

		$content = '';
		$lines=array();
		$modType='typeList';

		if (is_array($T3_SERVICES))	{

			foreach($this->svDef as $serviceType => $def)	{

				$lines[]='<tr><td colspan='.(6).'><br /></td></tr>';
				$lines[]=$this->serviceListRowHeader($modType, $this->pObj->doc->bgColor5,'');

				if(is_array($T3_SERVICES[$serviceType])) {
						// just get one to display service type
					$serviceKey = key($T3_SERVICES[$serviceType]);
					$info = $T3_SERVICES[$serviceType][$serviceKey];
					$eKey = $info['extKey'];
					$emConf = $this->includeEMCONF(PATH_site.$TYPO3_LOADED_EXT[$eKey]['siteRelPath'].'/ext_emconf.php',$eKey);
					$this->info[$serviceKey]['sv'] = $info;
					$this->info[$serviceKey]['type']=$TYPO3_LOADED_EXT[$eKey]['type'];
					$this->info[$serviceKey]['EM_CONF'] = $emConf;
					$lines[]=$this->serviceListRow($modType,$eKey,$TYPO3_LOADED_EXT[$eKey],$this->info[$serviceKey],'');

						// now comes the services itself
					foreach($T3_SERVICES[$serviceType] as $serviceKey => $info)	{
						$eKey = $info['extKey'];
						$emConf = $this->includeEMCONF(PATH_site.$TYPO3_LOADED_EXT[$eKey]['siteRelPath'].'/ext_emconf.php',$eKey);
						$this->info[$serviceKey]['sv'] = $info;
						$this->info[$serviceKey]['type']=$TYPO3_LOADED_EXT[$eKey]['type'];
						$this->info[$serviceKey]['EM_CONF'] = $emConf;
						$lines[]=$this->serviceListRow($modType.'Single',$eKey,$TYPO3_LOADED_EXT[$eKey],$this->info[$serviceKey],'');
					}
				} else {
					$info['sv']['serviceType'] = $serviceType;
					$lines[]=$this->serviceListRow($modType,$dummy='',$dummy='',$info,'');
					$lines[]='<tr bgColor="'.$this->pObj->doc->bgColor4.'"><td>&nbsp;</td><td colspan='.(5).'>No service of this type available.</td></tr>';
				}
			}
			$content.= '<table border="0" cellpadding="2" cellspacing="1">'.implode('',$lines).'</table><br />';

		} else {
			$content='Currently are no services installed.';
		}

		return $content;
	}

	/**
	 * Prints the header row for the various listings
	 *
	 * @param	string		$type: ... unused
	 * @param	string		$bgColor: ...
	 * @param	array		$cells: ...
	 * @param	boolean		$import: ... unused
	 * @return	string HTML content
	 */
	function serviceListRowHeader($type, $bgColor,$cells,$import=0)	{

		$cells[]='<td></td>';
		$cells[]='<td><strong>Services:</strong></td>';
		$cells[]='<td><strong>Types:</strong></td>';
		$cells[]='<td><strong>OS:</strong></td>';
		$cells[]='<td><strong>External:</strong></td>';
		$cells[]='<td><strong>Avail.:</strong></td>';

		return '<tr bgColor="'.$bgColor.'">'.implode('',$cells).'</tr>';
	}

	/**
	 * Prints a row with data for the various service listings
	 *
	 * @param	string		$type: ...
	 * @param	string		$eKey: ...
	 * @param	string		$eConf: ... unused
	 * @param	array		$info: ...
	 * @param	array		$cells: ...
	 * @param	string		$bgColor: ...
	 * @param	array		$inst_list: ...
	 * @param	boolean		$import: ...
	 * @param	string		$altLinkUrl: ...
	 * @return	string HTML content
	 */
	function serviceListRow($type,$eKey,$eConf,$info,$cells,$bgColor="",$inst_list=array(),$import=0,$altLinkUrl='')	{

		$svKey = $info['sv']['serviceKey'];

		$imgInfo = @getImageSize($this->getExtPath($eKey,$info).'/ext_icon.gif');

		if (is_array($imgInfo))	{
			$extIcon='<td valign="top"><img src="'.$GLOBALS['BACK_PATH'].$this->typeRelPaths[$info['type']].$eKey.'/ext_icon.gif'.'" '.$imgInfo[3].'></td>';
		} elseif ($info['_ICON']) {
			$extIcon='<td valign="top">'.$info['_ICON'].'</td>';
		} else {
			$extIcon='<td><img src="clear.gif" width=1 height=1></td>';
		}


		$bgColor = t3lib_div::modifyHTMLcolor($this->pObj->doc->bgColor4,20,20,20);

		if ($type === 'typeList') {
			$bgColor = '#F6CA96';
			$cells[]=$extIcon;
			$title='<strong>'.$info['sv']['serviceType'].' (Service Type)</strong>';
			$cells[]='<td valign="top">'.$title.'<br />'.htmlspecialchars(t3lib_div::fixed_lgd_cs($this->svDef[$info['sv']['serviceType']]['desc'],400)).'</td>';
			$cells[]='<td nowrap="nowrap" valign="top"></td>';
			$cells[]='<td nowrap="nowrap" valign="top"></td>';
			$cells[]='<td nowrap="nowrap" valign="top"></td>';
			$icon='';
		} else {
			$cells[]='<td><img src="clear.gif" width=1 height=1></td>';
			$title='<strong>'.$info['sv']['title'].'</strong><br />['.$info['sv']['serviceKey'].']';
			$cells[]='<td valign="top">'.$title.'<div style="margin-top:6px;">'.htmlspecialchars(t3lib_div::fixed_lgd_cs($info['sv']['description'],400)).'<div></td>';
			$cells[]='<td valign="top">'.implode($info['sv']['serviceSubTypes'],', ').'</td>';
			$cells[]='<td nowrap="nowrap" valign="top">'.$info['sv']['os'].'</td>';
			$cells[]='<td nowrap="nowrap" valign="top">'.$info['sv']['exec'].'</td>';

			if (t3lib_extmgm::findService($svKey,'*')) {
				$icon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_ok.gif').' vspace="4" />';
			} else {
				$icon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_fatalerror.gif').' vspace="4" />';
				$bgColor = t3lib_div::modifyHTMLcolor($this->pObj->doc->bgColor2,30,30,30);
			}
		}
		$cells[]='<td nowrap="nowrap" valign="top" align="center">'.$icon.'</td>';


		$bgColor = ' bgColor="'.($bgColor?$bgColor:$this->pObj->doc->bgColor4).'"';
		return '<tr'.$bgColor.'>'.implode('',$cells).'</tr>';
	}

	/**
	 * Display paths where binaries are searched for
	 *
	 * @return	string HTML content
	 */
	function showSearchPaths() {

		if(is_callable(array('t3lib_exec','getPaths'))) { // v 3.8
			if(count($paths = t3lib_exec::getPaths(true))) {
				$lines = array();
				$lines[] = '<tr class="bgColor5"><td><strong>Path:</strong></td><td><strong>valid:</strong></td></tr>';
				foreach($paths as $path => $valid) {
					if ($valid) {
						$icon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_ok.gif').' vspace="4" />';
					} else {
						$icon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_fatalerror.gif').' vspace="4" />';
					}
					$lines[] = '<tr class="bgColor'.($valid?'4':'2').'"><td>'.htmlspecialchars($path).'</td><td align="center">'.$icon.'</td></tr>';
				}

				$content.= '<table border="0" cellpadding="2" cellspacing="1">'.implode('',$lines).'</table><br />';
			}
		}
		return $content;
	}

	/**
	 * Wraps an email into an A tag
	 *
	 * @param	string		$str Label to be wrapped
	 * @param	string		$email Email address
	 * @return	string HTML content
	 */
	function wrapEmail($str,$email)	{
		if ($email)	{
			$str='<a href="mailto:'.$email.'">'.$str.'</a>';
		}
		return $str;
	}


	/**
	 * Returns the path of an available extension based on "type" (SGL)
	 *
	 * @param	string		$extKey: ...
	 * @param	array		$conf: ...
	 * @return	boolean
	 */
	function getExtPath($extKey,$conf)	{
		$typeP = $this->typePaths[$conf['type']];
		if ($typeP)	{
			$path = PATH_site.$typeP.$extKey.'/';
			return @is_dir($path) ? $path : '';
		}
	}


	/**
	 * Returns the $EM_CONF array from an extensions ext_emconf.php file
	 *
	 * @param	string		$path: ...
	 * @param	string		$_EXTKEY: ...
	 * @return	array		$EM_CONF array
	 */
	function includeEMCONF($path,$_EXTKEY)	{
		$EM_CONF = array();
		include($path);
		return $EM_CONF[$_EXTKEY];
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_svlist.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_svlist.php']);
}


?>