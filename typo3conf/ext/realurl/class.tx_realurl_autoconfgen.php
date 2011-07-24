<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2010 Dmitry Dulepov (dmitry@typo3.org)
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
 * Class for translating page ids to/from path strings (Speaking URLs)
 *
 * $Id: class.tx_realurl_advanced.php 5893 2007-07-09 13:41:07Z liels_bugs $
 *
 * @author	Dmitry Dulepov <dmitry@typo3.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   57: class tx_realurl_autoconfgen
 *   68:     function generateConfiguration()
 *   89:     function doGenerateConfiguration(&$fd)
 *  151:     function getTemplate()
 *  209:     function addLanguages(&$conf)
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Class for generating of automatic RealURL configuration
 *
 * @author	Dmitry Dulepov <dmitry@typo3.org>
 * @package realurl
 * @subpackage tx_realurl
 */
class tx_realurl_autoconfgen {

	/* @var $db t3lib_DB */
	var $db;
	var $hasStaticInfoTables;

	/**
	 * Generates configuration. Locks configuration file for exclusive access to avoid collisions. Will not be stabe on Windows.
	 *
	 * @return	void
	 */
	public function generateConfiguration() {
		$fileName = PATH_site . TX_REALURL_AUTOCONF_FILE;
		$fd = @fopen($fileName, 'a+');
		if ($fd) {
			@flock($fd, LOCK_EX);
			// Check size
			fseek($fd, 0, SEEK_END);
			if (ftell($fd) == 0) {
				$this->doGenerateConfiguration($fd);
			}
			@flock($fd, LOCK_UN);
			fclose($fd);
		}
	}

	/**
	 * Performs actual generation.
	 *
	 * @param	resource		$fd	FIle descriptor to write to
	 * @return	void
	 */
	protected function doGenerateConfiguration(&$fd) {

		if (!isset($GLOBALS['TYPO3_DB'])) {
			if (!TYPO3_db)	{
				return;
			}
			$this->db = t3lib_div::makeInstance('t3lib_db');
			if (!$this->db->sql_pconnect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password) ||
				!$this->db->sql_select_db(TYPO3_db)) {
					// Cannot connect to database
					return;
			}
		}
		else {
			$this->db = &$GLOBALS['TYPO3_DB'];
		}

		$this->hasStaticInfoTables = t3lib_extMgm::isLoaded('static_info_tables');

		$template = $this->getTemplate();

		// Find all domains
		$domains = $this->db->exec_SELECTgetRows('pid,domainName,redirectTo', 'sys_domain', 'hidden=0',
				'', '', '', 'domainName');
		if (count($domains) == 0) {
			$conf['_DEFAULT'] = $template;
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid', 'pages',
						'deleted=0 AND hidden=0 AND is_siteroot=1', '', '', '1');
			if (count($rows) > 0) {
				$conf['_DEFAULT']['pagePath']['rootpage_id'] = $rows[0]['uid'];
			}
		}
		else {
			foreach ($domains as $domain) {
				if ($domain['redirectTo'] != '') {
					// Redirects to another domain, see if we can make a shortcut
					$parts = parse_url($domain['redirectTo']);
					if (isset($domains[$parts['host']]) && ($domains['path'] == '/' || $domains['path'] == '')) {
						// Make a shortcut
						$conf[$domain['domainName']] = $parts['host'];
						continue;
					}
				}
				// Make entry
				$conf[$domain['domainName']] = $template;
				$conf[$domain['domainName']]['pagePath']['rootpage_id'] = $domain['pid'];
			}
		}

		$_realurl_conf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['realurl']);
		if ($_realurl_conf['autoConfFormat'] == 0) {
			fwrite($fd, '<' . '?php' . chr(10) . '$GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'realurl\']=' .
				'unserialize(\'' . str_replace('\'', '\\\'', serialize($conf)) . '\');' . chr(10) . '?' . '>'
			);
		}
		else {
			fwrite($fd, '<' . '?php' . chr(10) . '$GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'realurl\']=' .
				var_export($conf, true) . ';' . chr(10) . '?' . '>'
			);
		}
	}

	/**
	 * Creates common configuration template.
	 *
	 * @return	array		Template
	 */
	protected function getTemplate() {
		$confTemplate = array(
			'init' => array(
				'enableCHashCache' => true,
				'appendMissingSlash' => 'ifNotFile,redirect',
				'adminJumpToBackend' => true,
				'enableUrlDecodeCache' => true,
				'enableUrlEncodeCache' => true,
				'emptyUrlReturnValue' => t3lib_div::getIndpEnv('TYPO3_SITE_PATH')
			),
			'pagePath' => array(
				'type' => 'user',
				'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
				'spaceCharacter' => '-',
				'languageGetVar' => 'L',
			),
			'fileName' => array(
				'defaultToHTMLsuffixOnPrev' => 0,
				'acceptHTMLsuffix' => 1,
			)
		);

		// Add print feature if TemplaVoila is not loaded
		if (!t3lib_extMgm::isLoaded('templavoila')) {
			$confTemplate['fileName']['index']['print'] = array(
					'keyValues' => array(
						'type' => 98,
					)
				);
		}

		// Add respectSimulateStaticURLs if SimulateStatic is loaded
		if(t3lib_extMgm::isLoaded('simulatestatic')) {
			$confTemplate['init']['respectSimulateStaticURLs'] = true;
		}

		$this->addLanguages($confTemplate);

		// Add from extensions
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration'] as $extKey => $userFunc) {
				$params = array(
					'config' => $confTemplate,
					'extKey' => $extKey
				);
				$var = t3lib_div::callUserFunction($userFunc, $params, $this);
				if ($var) {
					$confTemplate = $var;
				}
			}
		}

		return $confTemplate;
	}

	/**
	 * Adds languages to configuration
	 *
	 * @param	array		$conf	Configuration (passed as reference)
	 * @return	void
	 */
	protected function addLanguages(&$conf) {
		if ($this->hasStaticInfoTables) {
			$languages = $this->db->exec_SELECTgetRows('t1.uid AS uid,t2.lg_iso_2 AS lg_iso_2', 'sys_language t1, static_languages t2', 't2.uid=t1.static_lang_isocode AND t1.hidden=0');
		}
		else {
			$languages = $this->db->exec_SELECTgetRows('t1.uid AS uid,t1.uid AS lg_iso_2', 'sys_language t1', 't1.hidden=0');
		}
		if (count($languages) > 0) {
			$conf['preVars'] = array(
				0 => array(
					'GETvar' => 'L',
					'valueMap' => array(
					),
					'noMatch' => 'bypass'
				),
			);
			foreach ($languages as $lang) {
				$conf['preVars'][0]['valueMap'][strtolower($lang['lg_iso_2'])] = $lang['uid'];
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurl/class.tx_realurl_autoconfgen.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurl/class.tx_realurl_autoconfgen.php']);
}
?>