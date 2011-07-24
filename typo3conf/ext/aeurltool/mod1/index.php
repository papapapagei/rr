<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Andreas Eberhard <aeberhard@users.sourceforge.net>
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
 * Module 'UrlTool' for the 'aeurltool' extension.
 *
 * @author	Andreas Eberhard <aeberhard@users.sourceforge.net>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:aeurltool/mod1/locallang.xml");
require_once (PATH_t3lib."class.t3lib_scbase.php");
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_aeurltool_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $extKey = "aeurltool";	// The extension key.

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$this->localconfFile=PATH_typo3conf.'localconf.php';
		$this->urltool404File=PATH_typo3conf.'urltoolconf_404.php';
		$this->urltoolrealurlFile=PATH_typo3conf.'urltoolconf_realurl.php';
		$this->urltoolrealurlDefaultFile=PATH_typo3conf.'ext/aeurltool/mod1/defaultrealurl.txt';
		$this->errorPrefix='<font color="#cc0000"><strong>';
		$this->errorPostfix='</strong></font><br />';
		$this->okPrefix='<font color="#006600"><strong>';
		$this->okPostfix='</strong></font><br />';
		$this->phperror='';

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"1" => $LANG->getLL("function404"),
				"2" => $LANG->getLL("functionRealurlConfig"),
				"3" => $LANG->getLL("functionRealurlCache"),
				"4" => $LANG->getLL("functionPathsegment"),
				"5" => $LANG->getLL("functionHelp"),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance("noDoc");
			$this->doc->backPath = $BACK_PATH;

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
					function openLinkBrowser()	{	//
						var url = "'.$this->doc->backPath.'browse_links.php?act=page&mode=wizard&P[itemName]=urltoolurl&P[formName]=editform";
						browserWin = window.open(url,"aeurltoolTypo3WinBrowser","height=350,width=600,status=0,menubar=0,resizable=1,scrollbars=1");
						browserWin.focus();
					}
				</script>
			';

			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = '<img'.t3lib_iconWorks::skinImg('','moduleicon.gif','width="16" height="16"').' title="" alt="" style="margin-right:10px;" />';
			$headerSection .= $LANG->getLL("description");

			$this->content.= $this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));

			// Render content:
			$this->moduleContent();

			$this->content.=$this->doc->spacer(20);

			// Debug
			//$this->content.="GET:".t3lib_div::view_array($_GET)."<br />";
			//$this->content.="POST:".t3lib_div::view_array($_POST)."<br />";

			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(10).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
	global $LANG;
		switch((string)$this->MOD_SETTINGS["function"])	{
			case 1:
				$this->content.=$this->config404Error();
			break;
			case 2:
				$this->content.=$this->configRealurl();
			break;
			case 3:
				$this->content.=$this->clearRealurlCache();
			break;
			case 4:
				$this->content.=$this->getPathsegmentContent();
			break;
			case 5:
				$this->content.=$this->getHelpContent();
			break;
		}
	}


	/**
	 * Syntax-Check with eval
	 *
	 * @return	boolean
	 */
	function eval_code($code) {
		ob_start();
		ini_set("display_errors", true);
		$output = eval("?" . chr(62) . $code . chr(60) . "?");
		ini_set("display_errors", false);
		if ( $output === FALSE ) {
			$this->phperror = ob_get_contents();
			ob_end_clean();		
			return false;
		}
		ob_end_clean();			
		return true;
	}


	/**
	 * Generates the configuration-Form for 404-Error-Handling
	 * Save the configuration
	 *
	 * @return	string
	 */
	function config404Error() {
		global $TYPO3_CONF_VARS, $LANG;

		$content = '';
					
		// Save the 404-Configuration
		if (t3lib_div::_GP('action')=='save404config') {

			$content .= $this->doc->spacer(10);

			$errurl = trim(t3lib_div::_GP('urltoolurl'));
			$numurl = isset($errurl) ? $errurl+0 : 0;			
			if ( $numurl ) {
				$errurl = "index.php?id=".$errurl;
			}
			
			$filecontent = "<?php \n";
			$filecontent .= '$'."TYPO3_CONF_VARS['FE']['pageNotFound_handling'] = ". "'".$errurl."';\n";
			$filecontent .= '$'."TYPO3_CONF_VARS['FE']['pageNotFound_handling_statheader'] = ". "'".t3lib_div::_GP('urltool_header')."';\n";
			$filecontent .= "?>";
			// eval code
			if ( $this->eval_code($filecontent) === TRUE ) {    
				// write urltoolconf_404.php
				$fh = @fopen($this->urltool404File,'w');
				if (@fwrite($fh,$filecontent)) {
					$content .= $this->okPrefix.$LANG->getLL("msg404confSaved").$this->okPostfix;
					@fclose($fh);
				} else {
					$content .= $this->errorPrefix.$LANG->getLL("msgWriteError").$this->urltool404File.$this->errorPostfix;
					$errswitch = true;
				}
			} else {
				// error in syntax !
				$content .= $this->errorPrefix.$LANG->getLL("msg404confError").$this->errorPostfix;
				$errswitch = true;
			}

			// Update localconf.php
			if (!$errswitch) {
				// remove include/php-end-tag in localconf.php
				$filecontent=t3lib_div::getURL($this->localconfFile);		
				$lines=explode("\n",$filecontent);
				foreach ($lines as $line_num => $line) {
					if (strpos($line,'404-Handling inserted by extension aeurltool')) {
						unset($lines[$line_num]);
					}
					if (trim($line)=='?>') {
						unset($lines[$line_num]);
						if( trim($lines[$line_num-1])=='' )
							unset($lines[$line_num-1]);
						if( trim($lines[$line_num-2])=='' )
							unset($lines[$line_num-2]);
						if( trim($lines[$line_num-3])=='' )
							unset($lines[$line_num-3]);
					}
				}			
			
				// add include to localconf.php
				$filecontent=implode("\n",$lines);
				if (t3lib_div::_GP('urltool_apply')=='on' and (is_file($this->urltool404File)) ) {
					$filecontent.="\n".'@include(PATH_typo3conf.\'urltoolconf_404.php\'); // 404-Handling inserted by extension aeurltool';
					$filecontent.="\n?>\n";
				} else {
					$filecontent.="\n?>\n";
				}

				// eval new localconf.php
				if ( $this->eval_code($filecontent) === TRUE ) {    
					// write new localconf.php
					$fh = @fopen($this->localconfFile,'w');
					if (@fwrite($fh,$filecontent)) {
						if (t3lib_div::_GP('urltool_apply')=='on' and (is_file($this->urltool404File)) ) {
							$content .= $this->okPrefix.$LANG->getLL("msg404confActivated").$this->okPostfix;
						}
						@fclose($fh);
					} else {
						$content .= $this->errorPrefix.$LANG->getLL("msgWriteError").$this->localconfFile.$this->errorPostfix;
					}
				} else {
					$content .= $this->errorPrefix.$LANG->getLL("msgLocalconfError").$this->errorPostfix;
				}
			}

		}

		// check localconf.php for include
		$filecontent=t3lib_div::getURL($this->localconfFile);		
		if (strpos($filecontent,'include(PATH_typo3conf.\'urltoolconf_404.php\')')===false) {
			$urltoolactive='';		
		} else {
			$urltoolactive='checked';	
		}		
		
		// get settings for Error-Handling from urltoolconf_404.php
		if (is_file($this->urltool404File)) {
			$filecontent=t3lib_div::getURL($this->urltool404File);		
			ob_start();
			ini_set("display_errors", true);
			$output = eval("?" . chr(62) . $filecontent . chr(60) . "?");
			ini_set("display_errors", false);
			$erroutput = ob_get_contents();
			ob_end_clean();
		}
	
		// Output Form-Data
		$content .= '<form action="index.php?id='.$this->id.'" method="POST" name="editform">';
		$content .= '<input type="hidden" name="action" value="save404config" />';

		$content .= '<br /><table border="0" cellspacing="3" cellpadding="3">';

		$tempapply = '<input name="urltool_apply" type="checkbox" '.$urltoolactive.' />';
		$content .= '<tr><td>'.$LANG->getLL("label404Apply").'</td><td>'.$tempapply.'</td></tr>';

		if (t3lib_div::_GP('action')=='save404config' and $errswitch) {
			$tempurl = '<input name="urltoolurl" type="text" value="'.t3lib_div::_GP('urltoolurl').'" ';
			$tempurl .= 'style="width:400px;" />';
		} else {
			$tempurl = '<input name="urltoolurl" type="text" value="'.$TYPO3_CONF_VARS['FE']['pageNotFound_handling'].'" ';
			$tempurl .= 'style="width:400px;" />';
		}
		$tempurlsel = '<a href="#" onclick="openLinkBrowser();return false;">'.
					'<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/link_popup.gif','').' title="" alt="" />'.
					'</a>';
		$content .= '<tr><td>'.$LANG->getLL("label404Url").'</td><td>'.$tempurl.'</td><td>'.$tempurlsel.'</td></tr>';

		if (t3lib_div::_GP('action')=='save404config' and $errswitch) {
			$tempheader = '<input name="urltool_header" type="text" value="'.t3lib_div::_GP('urltool_header').'" ';
			$tempheader .= 'style="width:400px;" />';
		} else {
			$tempheader = '<input name="urltool_header" type="text" value="'.$TYPO3_CONF_VARS['FE']['pageNotFound_handling_statheader'].'" ';
			$tempheader .= 'style="width:400px;" />';
		}
		$content .= '<tr><td>'.$LANG->getLL("label404Header").'</td><td>'.$tempheader.'</td></tr>';

		$content .= '</table>';

//		$content .= '<input type="image" '.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/savedok.gif','').' class="c-inputButton" title="'.$LANG->getLL("buttonSaveTitle").'" />';
		$content .= '<br /><input type="submit" value="'.$LANG->getLL("buttonSaveTitle").'" title="'.$LANG->getLL("buttonSaveTitle").'" />';
		$content .= '</form>';

		$content .= '<br /><br /><img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/zoom.gif','').' title="" alt="" />';
		$content .= ' <a target="_blank" href="'.'../../../../index.php?id=123456789087654321123456789087654321">404-Testlink</a>';

		$content = $this->doc->section($LANG->getLL("section404"),$content,1,1);
		return $content;
	}


	/**
	 * Edit realurl-configuration
	 * Save the configuration
	 *
	 * @return	string
	 */
	function configRealurl() {
		global $TYPO3_CONF_VARS, $LANG;

		$content = '';

		// Save the Realurl-Configuration
		if (t3lib_div::_GP('action')=='saverealurlconfig') {

			$content .= $this->doc->spacer(10);
		
			$filecontent = t3lib_div::_GP('realurlconfig');
			// eval code
			if ( $this->eval_code($filecontent) === TRUE ) {    
				// write urltoolconf_realurl.php
				$fh = @fopen($this->urltoolrealurlFile,'w');
				if (@fwrite($fh,$filecontent)) {
					$content .= $this->okPrefix.$LANG->getLL("msgRealurlconfSaved").$this->okPostfix;
					@fclose($fh);
				} else {
					$content .= $this->errorPrefix.$LANG->getLL("msgWriteError").$this->urltoolrealurlFile.$this->errorPostfix;
					$errswitch = true;
				}
			} else {
				// error in syntax !
				$content .= $this->errorPrefix.$LANG->getLL("msgRealurlconfError").$this->errorPostfix;
				$errswitch = true;
				$content .= $this->phperror;
			}
			
			// Update localconf.php
			if (!$errswitch) {
				// remove include/php-end-tag in localconf.php
				$filecontent=t3lib_div::getURL($this->localconfFile);		
				$lines=explode("\n",$filecontent);
				foreach ($lines as $line_num => $line) {
					if (strpos($line,'RealUrl-Configuration inserted by extension aeurltool')) {
						unset($lines[$line_num]);
					}
					if (trim($line)=='?>') {
						unset($lines[$line_num]);
						if( trim($lines[$line_num-1])=='' )
							unset($lines[$line_num-1]);
						if( trim($lines[$line_num-2])=='' )
							unset($lines[$line_num-2]);
						if( trim($lines[$line_num-3])=='' )
							unset($lines[$line_num-3]);
					}
				}			
			
				// add include to localconf.php
				$filecontent=implode("\n",$lines);
				if (t3lib_div::_GP('urltool_apply')=='on' and (is_file($this->urltoolrealurlFile)) ) {
					$filecontent.="\n".'@include(PATH_typo3conf.\'urltoolconf_realurl.php\'); // RealUrl-Configuration inserted by extension aeurltool';
					$filecontent.="\n?>\n";
				} else {
					$filecontent.="\n?>\n";
				}

				// eval new localconf.php
				if ( $this->eval_code($filecontent) === TRUE ) {    
					// write new localconf.php
					$fh = @fopen($this->localconfFile,'w');
					if (@fwrite($fh,$filecontent)) {
						if (t3lib_div::_GP('urltool_apply')=='on' and (is_file($this->urltool404File)) ) {
							$content .= $this->okPrefix.$LANG->getLL("msgRealurlconfActivated").$this->okPostfix;
						}
						@fclose($fh);
					} else {
						$content .= $this->errorPrefix.$LANG->getLL("msgWriteError").$this->localconfFile.$this->errorPostfix;
					}
				} else {
					$content .= $this->errorPrefix.$LANG->getLL("msgLocalconfError").$this->errorPostfix;
				}
			}
		}

		// check localconf.php for include
		$filecontent=t3lib_div::getURL($this->localconfFile);		
		if (strpos($filecontent,'include(PATH_typo3conf.\'urltoolconf_realurl.php\')')===false) {
			$realurlactive='';		
		} else {
			$realurlactive='checked';	
		}	
		
		// get realurl-config
		$filecontent='';
		$filecontent=t3lib_div::getURL($this->urltoolrealurlFile);		

		// get default-config
		if (t3lib_div::_GP('action')==realurldefault) {
			$filecontent=t3lib_div::getURL($this->urltoolrealurlDefaultFile);		
			$content .= $this->okPrefix.$LANG->getLL("msgRealurlDefaultLoaded").$this->okPostfix;
		}
		
		// Output Form-Data
		$content .= '<form action="index.php?id='.$this->id.'" method="POST" name="editform">';
		$content .= '<input type="hidden" name="action" value="saverealurlconfig" />';

		$content .= '<br /><table border="0" cellspacing="2" cellpadding="2">';

		$tempapply = '<input name="urltool_apply" type="checkbox" '.$realurlactive.' />';
		$tempdefault = '<a href="index.php?id='.$this->id.'&action=realurldefault">'.$LANG->getLL("labelRealurlDefault").'</a>';
		$content .= '<tr><td nowrap>'.$LANG->getLL("labelRealurlApply").'</td><td width="100%">'.$tempapply.'</td><td nowrap>'.$tempdefault.'</td></tr>';

		if (t3lib_div::_GP('action')=='saverealurlconfig' and $errswitch) {
			$tempcode = '<textarea name="realurlconfig" wrap="off" class="fixed-font enable-tab" rows="25" cols="80" style="width:100%;">'.t3lib_div::_GP('realurlconfig').'</textarea>';
		} else {
			$tempcode = '<textarea name="realurlconfig" wrap="off" class="fixed-font enable-tab" rows="25" cols="80" style="width:100%;">'.$filecontent.'</textarea>';
		}

		$content .= '<tr><td colspan="3"><strong>'.$LANG->getLL("labelRealurlCode").'</strong> ('.$this->urltoolrealurlFile.')'.'</td></tr>';
		$content .= '<tr><td colspan="3">'.$tempcode.'</td></tr>';

		$content .= '</table>';

		$content .= '<br /><input type="submit" value="'.$LANG->getLL("buttonSaveTitle").'" title="'.$LANG->getLL("buttonSaveTitle").'" />';
		$content .= '</form>';

		$content = $this->doc->section($LANG->getLL("sectionRealurlConfig"),$content,1,1);
		return $content;
	}


	/**
	 * Clear Cache-Tables
	 *
	 * @return	string
	 */
	function clearRealurlCache() {
		global $TYPO3_CONF_VARS, $LANG;

		$content = '';

		// Check for installed realurl-Extension
		if (!t3lib_extMgm::isLoaded('realurl',0)) {
			$content .= $this->doc->spacer(10);
			$content .= $this->errorPrefix.$LANG->getLL("msgRealurlNotInstalled").$this->errorPostfix;
			$content .= $this->doc->spacer(10);
			$content = $this->doc->section($LANG->getLL("sectionRealurlCache"),$content,0,1);
			return $content;			
		}

		// Clear Cache-Tables
		if (t3lib_div::_GP('action')=='clear') {

			// Clear Realurl-Cache
			if (t3lib_div::_GP('urltool_clear_realurl')=='on') {
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_chashcache ','');
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_pathcache','');
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_redirects','');
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_uniqalias','');
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_urldecodecache','');
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_urlencodecache','');
			}	

			// Clear Realurl-Errorlog
			if (t3lib_div::_GP('urltool_clear_realurlerror')=='on') {
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_errorlog','');
			}

			// Clear FE-Cache
			if (t3lib_div::_GP('urltool_clear_fe')=='on') {
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_pages','');
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_pagesection','');	
			}

			$content .= $this->doc->spacer(10);
			$content .= $this->okPrefix.$LANG->getLL("msgCacheCleared").$this->okPostfix;
			$content .= $this->doc->spacer(10);
			
		}
		
		// Output Form-Data
		$content .= '<form action="index.php?id='.$this->id.'" method="POST" name="editform">';
		$content .= '<input type="hidden" name="action" value="clear" />';		

		$content .= '<table border="0">';
			
		$content .= '<tr>';
		$content .= '<td><input type="checkbox" name="urltool_clear_realurl" checked></td>';
		$content .= '<td>'.$LANG->getLL("labelClearRealurl").'</td>';
		$content .= '</tr>';

		$content .= '<tr>';
		$content .= '<td><input type="checkbox" name="urltool_clear_realurlerror" checked></td>';
		$content .= '<td>'.$LANG->getLL("labelClearRealurlError").'</td>';
		$content .= '</tr>';

		$content .= '<tr>';
		$content .= '<td><input type="checkbox" name="urltool_clear_fe" checked></td>';
		$content .= '<td>'.$LANG->getLL("labelClearFeCache").'</td>';
		$content .= '</tr>';

		$content .= '</table>';

		$content .= '<br /><input type="submit" value="'.$LANG->getLL("buttonClearTitle").'" title="'.$LANG->getLL("buttonClearTitle").'" />';
		$content .= '</form>';
		$content .= $this->doc->spacer(10);
	
		$content = $this->doc->section($LANG->getLL("sectionRealurlCache"),$content,1,1);
		 
		// list realurl errorlog		
		$temp = '';
		$bgCol = '';	

		$sql = "SELECT * FROM tx_realurl_errorlog order by tstamp desc";
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);		

		if ($res) {
			$temp .= $this->doc->spacer(10);
			$temp .= '<table border="0" cellspacing="0" cellpadding="2" width="100%">';
			$temp .= '<tr class="bgColor2">';
			$temp .= '<td><strong>'.$LANG->getLL("theaderUrl").'</strong></td>';
			$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
			$temp .= '<td width="100%"><strong>'.$LANG->getLL("theaderErrormessage").'</strong></td>';
			$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
			$temp .= '<td><strong>'.$LANG->getLL("theaderDate").'</strong></td>';
			$temp .= '</tr>';
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$bgCol=$bgCol==''?' class="bgColor-20"':'';
				$temp .= '<tr '.$bgCol.'>';
				$temp .= '<td valign="top">'.$row[url].'</td>';
				$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
				$temp .= '<td valign="top" nowrap>'.$row[error].'</td>';
				$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
				$temp .= '<td nowrap>'.date("D d M Y H:i:s",$row[tstamp]).'</td>';
				$temp .= '</tr>';
			}	
			$temp .= '</table>';
		}
		
		$content .= $this->doc->section($LANG->getLL("sectionRealurlErrorlog"),$temp,1,1);		

		return $content;
	}

	
	/**
	 * Generates the List of Pages with pathsegment/alias
	 *
	 * @return	string
	 */
	function getPathsegmentContent() {
		global $TYPO3_CONF_VARS, $LANG;

		$content = '';
		$temp = '';
		$bgCol = '';	

		// list pages with pathsegment		
		$sql = "SELECT * FROM pages WHERE tx_realurl_pathsegment > '' order by uid asc ";
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);		

		if ($res) {
			$temp .= $this->doc->spacer(10);
			$temp .= '<table border="0" cellspacing="0" cellpadding="2" width="100%">';
			$temp .= '<tr class="bgColor2">';
			$temp .= '<td nowrap><strong>'.$LANG->getLL("theaderPage").'</strong></td>';
			$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
			$temp .= '<td><strong>'.$LANG->getLL("theaderTitle").'</strong></td>';
			$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
			$temp .= '<td width="100%"><strong>'.$LANG->getLL("theaderSpeaking").'</strong></td>';
			$temp .= '</tr>';
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$bgCol=$bgCol==''?' class="bgColor-20"':'';
				$temp .= '<tr '.$bgCol.'>';
				$temp .= '<td align="center"><a target="_blank" href="'.'../../../../index.php?id='.$row[uid].'">'.$row[uid].'</a></td>';
				$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
				$temp .= '<td>'.$row[title].'</td>';
				$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
				$temp .= '<td>'.$row[tx_realurl_pathsegment].'</td>';
				$temp .= '</tr>';
			}	
			$temp .= '</table>';
		} else {
			$content .= $this->doc->spacer(10);
			$content .= $this->errorPrefix.$LANG->getLL("msgNoPathsegment").$this->errorPostfix;
			$content .= $this->doc->spacer(10);
		}

		$content .= $this->doc->section($LANG->getLL("sectionPathsegment"),$temp,1,1);
		$content .= $this->doc->spacer(10);

		// list pages with alias			
		$temp = '';
		$bgCol = '';	
		
		$sql = "SELECT * FROM pages WHERE alias > '' order by uid asc ";
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);		

		if ($res) {
			$temp .= $this->doc->spacer(10);
			$temp .= '<table border="0" cellspacing="0" cellpadding="2" width="100%">';
			$temp .= '<tr class="bgColor2">';
			$temp .= '<td nowrap><strong>'.$LANG->getLL("theaderPage").'</strong></td>';
			$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
			$temp .= '<td><strong>'.$LANG->getLL("theaderTitle").'</strong></td>';
			$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
			$temp .= '<td width="100%"><strong>'.$LANG->getLL("theaderAlias").'</strong></td>';
			$temp .= '</tr>';
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$bgCol=$bgCol==''?' class="bgColor-20"':'';
				$temp .= '<tr '.$bgCol.'>';
				$temp .= '<td align="center"><a target="_blank" href="'.'../../../../index.php?id='.$row[uid].'">'.$row[uid].'</a></td>';
				$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
				$temp .= '<td>'.$row[title].'</td>';
				$temp .= '<td><img src="../../../clear.gif" width="10" height="1" /></td>';
				$temp .= '<td>'.$row[alias].'</td>';
				$temp .= '</tr>';
			}	
			$temp .= '</table>';
		} else {
			$content .= $this->doc->spacer(10);
			$content .= $this->errorPrefix.$LANG->getLL("msgNoAlias").$this->errorPostfix;
			$content .= $this->doc->spacer(10);
		}

		$content .= $this->doc->section($LANG->getLL("sectionAlias"),$temp,1,1);

		return $content;
	}
	
	/**
	 * Display Help / Information
	 *
	 * @return	string
	 */
	function getHelpContent() {
		global $TYPO3_CONF_VARS, $LANG;
		
		$content = '';
		$temp = '';

		$content .= $this->doc->section($LANG->getLL("sectionHelp"),$temp,1,1);
		$content .= $this->doc->spacer(10);			

		$temp .= '<br />'.$LANG->getLL("help00").'<br /><br />';
		$temp .= '<ul>';			
		$temp .= '<li>'.$LANG->getLL("help01").'<br /><br /></li>';
		$temp .= '<li>'.$LANG->getLL("help02").'<br /><br /></li>';
		$temp .= '<li>'.$LANG->getLL("help03").'<br /><br /></li>';
		$temp .= '<li>'.$LANG->getLL("help04").'<br /><br /></li>';
		$temp .= '</ul>';
		
		$content .= $this->doc->section($LANG->getLL("sectionHelp404"),$temp,1,0);

		$temp = '';
		$temp .= '<br />'.$LANG->getLL("helpR00").'<br /><br />';
		$temp .= '<ul>';			
		$temp .= '<li>'.$LANG->getLL("helpR01").'<br /><br /></li>';
		$temp .= '<li>'.$LANG->getLL("helpR02").'<br /><br /></li>';
		$temp .= '<li>'.$LANG->getLL("helpR03").'<br /><br /></li>';
		$temp .= '<li>'.$LANG->getLL("helpR04").'<br /><br /></li>';
		$temp .= '</ul>';

		$content .= $this->doc->section($LANG->getLL("sectionHelpRealurl"),$temp,1,0);
		
		$temp = $this->doc->spacer(10);
		$temp .= 'Andreas Eberhard, '.'<a href="mailto:aeberhard@users.sourceforge.net">aeberhard@users.sourceforge.net</a>';
		$content .= $this->doc->section('Author',$temp,1,0);

		return $content; 
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aeurltool/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/aeurltool/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_aeurltool_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>