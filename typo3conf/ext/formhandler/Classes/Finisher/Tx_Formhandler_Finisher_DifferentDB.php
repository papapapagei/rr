<?php
/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *
 * $Id: Tx_Formhandler_Finisher_DifferentDB.php 34736 2010-06-22 12:33:15Z reinhardfuehricht $
 *                                                                        */

/**
 * This finisher stores the submitted values into a table in a different database than the TYPO3 database according to the configuration.
 * This class uses the extension 'adodb' to query the database.
 *
 * Example configuration:
 *
 * <code>
 * finishers.1.class = Tx_Formhandler_Finisher_DifferentDB
 * finishers.1.config.host = 127.0.0.1
 * finishers.1.config.port = 666
 * finishers.1.config.db = typo3_421
 * finishers.1.config.username = root
 * finishers.1.config.password = rootpass
 * finishers.1.config.driver = oci8
 * </code>
 *
 * Further configuration equals the configuration of Tx_Formhandler_Finisher_DB.
 *
 * @package	Tx_Formhandler
 * @subpackage	Finisher
 * @see Tx_Formhandler_Finisher_DB
 */
class Tx_Formhandler_Finisher_DifferentDB extends Tx_Formhandler_Finisher_DB {

	/**
	 * The name of the database driver to use.
	 *
	 * @access protected
	 * @var string
	 */
	protected $driver;

	/**
	 * The name of the database host.
	 *
	 * @access protected
	 * @var string
	 */
	protected $host;

	/**
	 * The port the database listens.
	 *
	 * @access protected
	 * @var integer
	 */
	protected $port;

	/**
	 * The name of the database.
	 *
	 * @access protected
	 * @var string
	 */
	protected $db;

	/**
	 * The username to use.
	 *
	 * @access protected
	 * @var string
	 */
	protected $user;

	/**
	 * The password to use.
	 *
	 * @access protected
	 * @var string
	 */
	protected $password;

	/**
	 * Method to query the database making an insert or update statement using the given fields.
	 *
	 * @see Tx_Formhandler_Finisher_DB::save()
	 * @param array &$queryFields Array holding the query fields
	 * @return void
	 */
	protected function save(&$queryFields) {

		//if adodb is installed
		if(t3lib_extMgm::isLoaded('adodb')) {
			require_once(t3lib_extMgm::extPath('adodb') . 'adodb/adodb.inc.php');
				
			//build sql
				
			//insert query
			if(!$this->doUpdate) {
					
				$query = $GLOBALS['TYPO3_DB']->INSERTquery($this->table, $queryFields);
				Tx_Formhandler_StaticFuncs::debugMessage('sql_request', $query);

				//update query
			} else {

				//check if uid of record to update is in GP
				$uid = $this->gp[$this->key];
				if($uid) {
					$query = $GLOBALS['TYPO3_DB']->UPDATEquery($this->table, $this->key . '=' . $uid, $queryFields);
					Tx_Formhandler_StaticFuncs::debugMessage('sql_request', $query);
				} else {
					Tx_Formhandler_StaticFuncs::debugMessage('no_update_possible');
				}
			}
				
			//open connection
			$conn = &NewADOConnection($this->driver);
			$host = $this->host;
			if($this->port) {
				$host .= ':' . $this->port;
			}
			if($this->db) {
				$conn->Connect($host, $this->user, $this->password, $this->db);
			} else {
				$conn->Connect($host, $this->user, $this->password);
			}
				
			//insert data
			$conn->Execute($query);
				
			//close connection
			$conn->Close();
		} else {
			Tx_Formhandler_StaticFuncs::throwException('extension_required', 'adodb', 'Tx_Formhandler_Finisher_DifferentDB');
		}
	}

	/**
	 * Inits the finisher mapping settings values to internal attributes.
	 *
	 * @see Tx_Formhandler_Finisher_DB::init
	 * @return void
	 */
	public function init($gp, $settings) {
		parent::init($gp, $settings);
		
		$this->driver = $this->settings['driver'];
		$this->db = $this->settings['db'];
		$this->host = $this->settings['host'];
		$this->port = $this->settings['port'];
		$this->user = $this->settings['username'];
		$this->password = $this->settings['password'];
		if(!$this->driver) {
			throw new Exception('No driver given!');
		}
	}

}
?>
