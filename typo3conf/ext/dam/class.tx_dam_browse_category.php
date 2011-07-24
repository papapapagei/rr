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
 * Implements a element browser for category trees.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */



require_once(PATH_txdam.'treelib/class.tx_dam_treelib_elementbrowser.php');


/**
 * Implements a element browser for category trees.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class tx_dam_browse_category extends tx_dam_treelib_elementbrowser {

	var $table = 'tx_dam_cat';

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_category.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_category.php']);
}

?>