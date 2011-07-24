<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003, 2004, 2005 Kasper Skaarhoj (kasper@typo3.com)
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
 * templavoila module cm2
 *
 * $Id$
 *
 * @author		Kasper Skaarhoj <kasper@typo3.com>
 * @co-author	Robert Lemke <robert@typo3.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   67: class tx_templavoila_cm2 extends t3lib_SCbase
 *   80:     function main()
 *  113:     function printContent()
 *  125:     function markUpXML($str)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:templavoila/cm2/locallang.xml');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once (PATH_t3lib.'class.t3lib_flexformtools.php');
require_once (PATH_t3lib.'class.t3lib_tcemain.php');
require_once (PATH_t3lib.'class.t3lib_diff.php');

require_once (t3lib_extMgm::extPath('templavoila') . 'classes/class.tx_templavoila_div.php');




/**
 * Class for displaying color-marked-up version of FlexForm XML content.
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @package TYPO3
 * @subpackage tx_templavoila
 */
class tx_templavoila_cm2 extends t3lib_SCbase {

		// External, static:
	var $option_linenumbers = TRUE;		// Showing linenumbers if true.

		// Internal, GPvars:
	var $viewTable = array();		// Array with tablename, uid and fieldname
	var $returnUrl = '';			// (GPvar "returnUrl") Return URL if the script is supplied with that.

	/**
	 * Main function, drawing marked up XML.
	 *
	 * @return	void
	 */
	function main()	{
		global $LANG,$BACK_PATH;

			// Check admin: If this is changed some day to other than admin users we HAVE to check if there is read access to the record being selected!
		if (!$GLOBALS['BE_USER']->isAdmin())	die('no access.');

			// Draw the header.
		$this->doc = t3lib_div::makeInstance('noDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->docType = 'xhtml_trans';

		$this->returnUrl = tx_templavoila_div::sanitizeLocalUrl(t3lib_div::_GP('returnUrl'));

		$this->content.=$this->doc->startPage($LANG->getLL('title'));
		$this->content.=$this->doc->header($LANG->getLL('title'));
		$this->content.=$this->doc->spacer(5);

		if ($this->returnUrl)	{
			$this->content.='<a href="' . htmlspecialchars($this->returnUrl) . '" class="typo3-goBack">' .
				'<img' . t3lib_iconWorks::skinImg($this->doc->backPath, 'gfx/goback.gif', 'width="14" height="14"') . ' alt="" />' .
				$LANG->sL('LLL:EXT:lang/locallang_misc.xml:goBack', 1) .
				'</a><br/><br/>';
		}

			// XML code:
		$this->viewTable = t3lib_div::_GP('viewRec');

		$record = t3lib_BEfunc::getRecordWSOL($this->viewTable['table'], $this->viewTable['uid']);	// Selecting record based on table/uid since adding the field might impose a SQL-injection problem; at least the field name would have to be checked first.
		if (is_array($record))	{

				// Set current XML data:
			$currentXML = $record[$this->viewTable['field_flex']];

				// Clean up XML:
			$cleanXML = '';
			if ($GLOBALS['BE_USER']->isAdmin())	{
				if ('tx_templavoila_flex' == $this->viewTable['field_flex'])	{
					$flexObj = t3lib_div::makeInstance('t3lib_flexformtools');
					if ($record['tx_templavoila_flex'])	{
						$cleanXML = $flexObj->cleanFlexFormXML($this->viewTable['table'],'tx_templavoila_flex',$record);

							// If the clean-button was pressed, save right away:
						if (t3lib_div::_POST('_CLEAN_XML'))	{
							$dataArr = array();
							$dataArr[$this->viewTable['table']][$this->viewTable['uid']]['tx_templavoila_flex'] = $cleanXML;

								// Init TCEmain object and store:
							$tce = t3lib_div::makeInstance('t3lib_TCEmain');
							$tce->stripslashes_values=0;
							$tce->start($dataArr,array());
							$tce->process_datamap();

								// Re-fetch record:
							$record = t3lib_BEfunc::getRecordWSOL($this->viewTable['table'], $this->viewTable['uid']);
							$currentXML = $record[$this->viewTable['field_flex']];
						}
					}
				}
			}

			if (md5($currentXML)!=md5($cleanXML))	{
					// Create diff-result:
				$t3lib_diff_Obj = t3lib_div::makeInstance('t3lib_diff');
				$diffres = $t3lib_diff_Obj->makeDiffDisplay($currentXML,$cleanXML);

				$xmlContentMarkedUp = '
				<b>'.$this->doc->icons(1).$LANG->getLL('needsCleaning',1).'</b>
				<table border="0">
					<tr class="bgColor5 tableheader">
						<td>'.$LANG->getLL('current',1).'</td>
					</tr>
					<tr>
						<td>'.$this->markUpXML($currentXML).'<br/><br/></td>
					</tr>
					<tr class="bgColor5 tableheader">
						<td>'.$LANG->getLL('clean',1).'</td>
					</tr>
					<tr>
						<td>'.$this->markUpXML($cleanXML).'</td>
					</tr>
					<tr class="bgColor5 tableheader">
						<td>'.$LANG->getLL('diff',1).'</td>
					</tr>
					<tr>
						<td>'.$diffres.'
						<br/><br/><br/>

						<form action="'.t3lib_div::getIndpEnv('REQUEST_URI').'" method="post">
							<input type="submit" value="'.$LANG->getLL('cleanUp',1).'" name="_CLEAN_XML" />
						</form>

						</td>
					</tr>
				</table>

				';
			} else {
				$xmlContentMarkedUp = '';
				if ($cleanXML)	{
					$xmlContentMarkedUp.= '<b>'.$this->doc->icons(-1).$LANG->getLL('XMLclean',1).'</b><br/>';
				}
				$xmlContentMarkedUp.= $this->markUpXML($currentXML);
			}

			$this->content.=$this->doc->section('',$xmlContentMarkedUp,0,1);
		}

			// Add spacer:
		$this->content.=$this->doc->spacer(10);
	}

	/**
	 * Prints module content.
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.=$this->doc->middle();
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Mark up XML content
	 *
	 * @param	string		XML input
	 * @return	string		HTML formatted output, marked up in colors
	 */
	function markUpXML($str)	{
		require_once(PATH_t3lib.'class.t3lib_syntaxhl.php');

			// Make instance of syntax highlight class:
		$hlObj = t3lib_div::makeInstance('t3lib_syntaxhl');

			// Check which document type, if applicable:
		if (strstr(substr($str,0,100),'<T3DataStructure'))	{
			$title = 'Syntax highlighting <T3DataStructure> XML:';
			$formattedContent = $hlObj->highLight_DS($str);
		} elseif (strstr(substr($str,0,100),'<T3FlexForms'))	{
			$title = 'Syntax highlighting <T3FlexForms> XML:';
			$formattedContent = $hlObj->highLight_FF($str);
		} else {
			$title = 'Unknown format:';
			$formattedContent = '<span style="font-style: italic; color: #666666;">'.htmlspecialchars($str).'</span>';
		}

			// Check line number display:
		if ($this->option_linenumbers)	{
			$lines = explode(chr(10),$formattedContent);
			foreach($lines as $k => $v)	{
				$lines[$k] = '<span style="color: black; font-weight:normal;">'.str_pad($k+1,4,' ',STR_PAD_LEFT).':</span> '.$v;
			}
			$formattedContent = implode(chr(10),$lines);
		}

			// Output:
		return '
			<h3>'.htmlspecialchars($title).'</h3>
			<pre class="ts-hl">'.$formattedContent.'</pre>
			';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/cm2/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/cm2/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_templavoila_cm2');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>
