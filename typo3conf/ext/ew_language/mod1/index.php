<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Elio Wahlen <vorname at vorname.de>
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
 * Hint: use extdeveval to insert/update function index above.
 */


$LANG->includeLLFile('EXT:ew_language/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'WebLanguage' for the 'ew_language' extension.
 *
 * @author	Elio Wahlen <vorname at vorname.de>
 * @package	TYPO3
 * @subpackage	tx_gositelang
 */
class  tx_ewlanguage_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
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
						'function' => Array (
							'1' => $LANG->getLL('function1'),
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
				
					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;
						$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
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
					switch((string)$this->MOD_SETTINGS['function'])	{
						case 1:
						$this->content.=$this->doc->section($LANG->getLL('title'),$content,0,1);
					
						/*****
						***	WENN POST[lang] gefüllt und POST[submit] gestzt ist übernehme EINSTELLUNGEN
						*****/
						
						//print_r($_POST);
						
						/*****
						***	WHERE KLAUSEL BAUEN
						*****/
						$where = "'".implode("','", (is_array($_POST['lang']) ? $_POST['lang'] : array()))."'";

						/*****
						**	EINSTELLUNGEN ÜBERNEHMEN
						*****/
						if ($_POST['submit']==1) {
							//alle Hidden felder auf 1 setzten dessen IDs in POST[lang] vorkommen
							$OK = $GLOBALS['TYPO3_DB']->exec_UPDATEquery ("sys_language",'uid IN('.$where.')',array('disabled_in_menu' => '1'));
							//alle hidden felder auf 0 setzten wenn dem nicht so ist
							$OK2 = $GLOBALS['TYPO3_DB']->exec_UPDATEquery ("sys_language",'NOT( uid IN('.$where.'))',array('disabled_in_menu'=>'0'));
							//if ($OK==1 && $OK2==1)
							//	$this->content .= $LANG->getLL('success');
						}
						
						/*****
						**	Vorhandene Sprachen abfragen
						*****/
						$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*", "sys_language", "", "");

						/******
						***	FORM UND TABELLE ERSTELLEN
						*****/
						$this->content .= '
						<form action="" method="post" name="languages">
						<table cellspacing="5" cellpadding="5">
						<tr><td>'.$LANG->getLL('lang').'</td><td>'.$LANG->getLL('disabled').'</td></tr>';

						/*****
						***	Gehe jede Sprache durch
						*****/
						while ($row = mysql_fetch_assoc($query))
						{
							//print_r($row);
							$flagge = '<img height="12" width="20" border="0" alt="'.$row['flag'].'" title="'.$row['flag'].'" src="../typo3/gfx/flags/'.$row['flag'].'" />';
							$this->content .= '
												<tr>
													<td>'.$row['title'].'&nbsp;'.$flagge.'</td>
													<td><input type="checkbox" name="lang[]" value="'.$row['uid'].'" '.($row['disabled_in_menu']==1?'checked="checked"':'').'"></td>
												</tr>';
						}
						$this->content .= '</table><button type="submit" name="submit" value="1">'.$LANG->getLL('ok').'</button></form>';
						break;
					}
				}
				
		}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_language/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ew_language/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_ewlanguage_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>