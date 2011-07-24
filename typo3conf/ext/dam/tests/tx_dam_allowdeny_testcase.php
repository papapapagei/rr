<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */

require_once (PATH_txdam.'lib/class.tx_dam_allowdeny.php');
require_once (PATH_txdam.'tests/class.tx_dam_testlib.php');

class tx_dam_allowdeny_testcase extends tx_dam_testlib {


	public function test_allowdeny01 () {
		$fixture = $this->getFixture('allowdeny01');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertFalse ($allowDeny->isAllowed('action_example0'), 'action_example0');
	}
	
	public function test_allowdeny02 () {
		$fixture = $this->getFixture('allowdeny02');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example1'), 'action_example1');
		self::assertTrue ($allowDeny->isAllowed('action_example2'), 'action_example2');
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertFalse ($allowDeny->isAllowed('action_example0'), 'action_example0');
	}
	
	public function test_allowdeny03 () {
		$fixture = $this->getFixture('allowdeny03');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example1'), 'action_example1');
		self::assertTrue ($allowDeny->isAllowed('action_example2'), 'action_example2');
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertFalse ($allowDeny->isAllowed('action_example0'), 'action_example0');
	}
	
	public function test_allowdeny04 () {
		$fixture = $this->getFixture('allowdeny04');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example1'), 'action_example1');
		self::assertTrue ($allowDeny->isAllowed('action_example2'), 'action_example2');
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertFalse ($allowDeny->isAllowed('action_example0'), 'action_example0');
	}
	
	public function test_allowdeny05 () {
		$fixture = $this->getFixture('allowdeny05');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertFalse ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertFalse ($allowDeny->isAllowed('action_example0'), 'action_example0');
	}
		
		
		
	public function test_denyallow01 () {
		$fixture = $this->getFixture('denyallow01');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertFalse ($allowDeny->isAllowed('action_example0'), 'action_example0');
	}
		
	public function test_denyallow02 () {
		$fixture = $this->getFixture('denyallow02');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertFalse ($allowDeny->isAllowed('action_example0'), 'action_example0');
	}
		
	public function test_denyallow03 () {
		$fixture = $this->getFixture('denyallow03');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertTrue ($allowDeny->isAllowed('action_example0'), 'action_example0');
		self::assertTrue ($allowDeny->isAllowed('action_example4'), 'action_example4');
	}
		
	public function test_denyallow04 () {
		$fixture = $this->getFixture('denyallow04');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertTrue ($allowDeny->isAllowed('action_example0'), 'action_example0');
		self::assertTrue ($allowDeny->isAllowed('action_example4'), 'action_example4');
	}
	
	
	
	public function test_explicit01 () {
		$fixture = $this->getFixture('explicit01');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example3'), 'action_example3');
		self::assertFalse ($allowDeny->isAllowed('action_example4'), 'action_example4');
		self::assertFalse ($allowDeny->isAllowed('action_example0'), 'action_example0');
	}
	
	public function test_explicit02 () {
		$fixture = $this->getFixture('explicit02');
		$allowDeny = new tx_dam_allowdeny(NULL, $fixture['allowDeny.']);
		self::assertTrue ($allowDeny->isAllowed('action_example4'), 'action_example4');
		self::assertFalse ($allowDeny->isAllowed('action_example3'), 'action_example3');
	}





	/***************************************
	 *
	 *	 Fixtures
	 *
	 ***************************************/


	/**
	 *
	 * @return array
	 */
	private function getFixture ($name) {
		global $TYPO3_CONF_VARS;
		
		switch ($name) {
			case 'explicit01':
				$setupTxt = '
					allowDeny {
						order = explicit
						allow = action_example3
						deny = action_example4
					}';
				break;
			case 'explicit02':
				$setupTxt = '
					allowDeny {
						order = explicit
						allow = action_example4
					}';
				break;
				
				
			case 'allowdeny01':
				$setupTxt = '
					allowDeny {
						order = allow,deny
						allow = action_example2, action_example3
						deny = action_example4
					}';
				break;
			case 'allowdeny02':
				$setupTxt = '
					allowDeny {
						order = allow,deny
						allow = action_example2, action_example3
						deny = 
						allow.somethingSpecial {
							user = 999999999,888888888,'.$GLOBALS['BE_USER']->user['uid'].',777777777
							usergroup = 999999999,888888888
							item = action_example1
						}
					}';
				break;
			case 'allowdeny03':
				$setupTxt = '
					allowDeny {
						order = allow,deny
						allow = action_example2, action_example3
						deny = 
						allow.somethingSpecial {
							usergroup = 999999999,888888888,'.intval($GLOBALS['BE_USER']->user->usergroups).',777777777
							admin = '.($GLOBALS['BE_USER']->isAdmin() ? 1 : 0).'
							item = action_example1
						}
					}';
				break;
			case 'allowdeny04':
				$setupTxt = '
					allowDeny {
						order = allow,deny
						allow = action_example2, action_example3
						deny = 
						allow.somethingSpecial {
							usergroup = *
							item = action_example1
						}
					}';
				break;
			case 'allowdeny05':
				$setupTxt = '
					allowDeny {
						order = allow,deny
						allow = action_example2, action_example3
						deny = *
					}';
				break;
				
				
			case 'denyallow01':
				$setupTxt = '
					allowDeny {
						// default: order = deny,allow
						allow = action_example2, action_example3
						deny = *
					}';
				break;
			case 'denyallow02':
				$setupTxt = '
					allowDeny {
						order = deny,allow
						allow = action_example2, action_example3
						deny = *
					}';
				break;
			case 'denyallow03':
				$setupTxt = '
					allowDeny {
						order = deny,allow
						allow = *
						deny = action_example4
					}';
				break;
			case 'denyallow04':
				$setupTxt = '
					allowDeny {
						order = deny,allow
						allow = *
						deny = action_example4
						deny.somethingDenied {
							from = 192.168.*.*,127.0.0.1,10.0.*.*
							item = *
						}
					}';
				break;
			default:
				$setupTxt = '';
				break;
		}

		require_once(PATH_t3lib.'class.t3lib_tsparser.php');
		$parseObj = t3lib_div::makeInstance('t3lib_TSparser');
		$parseObj->parse($setupTxt);
		$conf = $parseObj->setup;
		
		return $conf;
	}



}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_allowdeny_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_allowdeny_testcase.php']);
//}
?>