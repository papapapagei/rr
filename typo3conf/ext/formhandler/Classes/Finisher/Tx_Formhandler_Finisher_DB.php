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
 * $Id: Tx_Formhandler_Finisher_DB.php 36838 2010-08-16 13:52:00Z mabolek $
 *                                                                        */

/**
 * This finisher stores the submitted values into a table in the TYPO3 database according to the configuration
 *
 * Example configuration:
 *
 * <code>
 * finishers.1.class = Tx_Formhandler_Finisher_DB
 *
 * #The table to store the records in
 * finishers.1.config.table = tt_content
 *
 * #The uid field. Default: uid
 * finishers.1.config.key = uid
 *
 * #Do not insert the record, but update an existing one.
 * #The uid of the existing record must exist in Get/Post
 * finishers.1.config.updateInsteadOfInsert = 1
 *
 * #map a form field to a db field.
 * finishers.1.config.fields.header.mapping = name
 *
 * #if form field is empty, insert this
 * finishers.1.config.fields.header.if_is_empty = None given
 * finishers.1.config.fields.bodytext.mapping = interests
 *
 * #if form field is an array, implode using this separator. Default: ,
 * finishers.1.config.fields.bodytext.separator = ,
 *
 * #add static values for some fields
 * finishers.1.config.fields.hidden = 1
 * finishers.1.config.fields.pid = 39
 *
 * #add special values
 * finishers.1.config.fields.subheader.special = sub_datetime
 * finishers.1.config.fields.crdate.special = sub_tstamp
 * finishers.1.config.fields.tstamp.special = sub_tstamp
 * finishers.1.config.fields.imagecaption.special = ip
 * </code>
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	Finisher
 */
class Tx_Formhandler_Finisher_DB extends Tx_Formhandler_AbstractFinisher {

	/**
	 * The name of the table to put the values into.
	 *
	 * @access protected
	 * @var string
	 */
	protected $table;

	/**
	 * The field in the table holding the primary key.
	 *
	 * @access protected
	 * @var string
	 */
	protected $key;

	/**
	 * A flag to indicate if to insert the record or to update an existing one
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $doUpdate;

	/**
	 * The main method called by the controller
	 *
	 * @return array The probably modified GET/POST parameters
	 */
	public function process() {

		$evaluation = TRUE;
		if(isset($this->settings['condition'])) {
			$condition = $this->parseCondition($this->settings['condition']);
			eval('$evaluation = ' . $condition . ';');
			$evaluationMessage = ($evaluation === TRUE) ?  'TRUE' : 'FALSE';
			Tx_Formhandler_StaticFuncs::debugMessage('condition', $evaluationMessage, $condition);
				
		}

		if($evaluation) {
			Tx_Formhandler_StaticFuncs::debugMessage('data_stored');
				
			//set fields to insert/update
			$queryFields = $this->parseFields();
				
			//query the database
			$this->save($queryFields);
			
			if(!is_array($this->gp['saveDB'])) {
				$this->gp['saveDB'] = array();
			}
			
			//Get DB info, including UID
			if(!$this->doUpdate) {
				$this->gp['inserted_uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
				$this->gp[$this->table . '_inserted_uid'] = $this->gp['inserted_uid'];
				$info = array(
					'table' => $this->table,
					'uid' => $this->gp['inserted_uid'],
					'uidField' => $this->key
				);
				array_push($this->gp['saveDB'], $info);
			} else {
				$uid = $this->getUpdateUid();
				$info = array(
					'table' => $this->table,
					'uid' => $uid,
					'uidField' => $this->key
				);
				array_push($this->gp['saveDB'], $info);
			}
			
			//Insert the data written to DB into GP array
			$dataKeyName = $this->table;
			$dataKeyIndex = 1;
			while(isset($this->gp['saveDB'][$dataKeyName])) {
				$dataKeyIndex++;
				$dataKeyName = $this->table . '_' . $dataKeyIndex;
			}
			$this->gp['saveDB'][$dataKeyName] = $queryFields;
		}

		return $this->gp;
	}

	/**
	 * Parses condition statement given by the typoscript configuration. Replaces field with real content for a proper PHP eval().
	 *
	 * @author	Fabien Udriot <fabien.udriot@ecodev.ch>
	 * @param	string	$condition: the condition given by the typoscript configuration.
	 * @return	string	$condition: the condition formated for evaluation.
	 */
	protected function parseCondition($condition) {
		$pattern = '/[\w]+/is';
		preg_match_all($pattern, $condition, $fields);
		foreach($fields[0] as $fieldName) {
			if (isset($this->gp[$fieldName])) {
					
				// Formats the value by surrounding it with '.
				$value = "'" . str_replace("'", "\'", $this->gp[$fieldName]) . "'";

				// Replace conditions
				$condition = str_replace($fieldName, $value, $condition);
			} else {
                $condition = str_replace($fieldName, 'FALSE', $condition);
			}
		}
		return $condition;
	}

	/**
	 * Method to query the database making an insert or update statement using the given fields.
	 *
	 * @param array &$queryFields Array holding the query fields
	 * @return void
	 */
	protected function save(&$queryFields) {

		//insert query
		if(!$this->doUpdate) {

			$query = $GLOBALS['TYPO3_DB']->INSERTquery($this->table, $queryFields);
			Tx_Formhandler_StaticFuncs::debugMessage('sql_request', $query);
			$res = $GLOBALS['TYPO3_DB']->sql_query($query);
			if($GLOBALS['TYPO3_DB']->sql_error()) {
				Tx_Formhandler_StaticFuncs::debugMessage($GLOBALS['TYPO3_DB']->sql_error());
			}
			
			//update query
		} else {
				
			//check if uid of record to update is in GP
			$uid = $this->getUpdateUid();
						
			if($uid) {
				$query = $GLOBALS['TYPO3_DB']->UPDATEquery($this->table, $this->key . '=' . $uid, $queryFields);
				Tx_Formhandler_StaticFuncs::debugMessage('sql_request', $query);
				$res = $GLOBALS['TYPO3_DB']->sql_query($query);
			} else {
				Tx_Formhandler_StaticFuncs::debugMessage('no_update_possible');
			}
		}
	}

	/**
	 * Inits the finisher mapping settings values to internal attributes.
	 *
	 * @return void
	 */
	public function init($gp, $settings) {
		parent::init($gp, $settings);

		//set table
		$this->table = $this->settings['table'];
		if(!$this->table || !is_array($this->settings['fields.'])) {
			Tx_Formhandler_StaticFuncs::throwException('no_table', 'Tx_Formhandler_Finisher_DB');
			return;
		}

		//set primary key field
		$this->key = Tx_Formhandler_StaticFuncs::getSingle($this->settings, 'key');
		if(!$this->key) {
			$this->key = 'uid';
		}

		//check whether to update or to insert a record
		$this->doUpdate = FALSE;
		if($this->settings['updateInsteadOfInsert']) {
			$this->doUpdate = TRUE;
		}
	}

	/**
	 * Parses mapping settings and builds an array holding the query fields information.
	 *
	 * @return array The query fields
	 */
	protected function parseFields() {
		$queryFields = array();

		//parse mapping
		foreach($this->settings['fields.'] as $fieldname => $options) {
			$fieldname = str_replace('.', '', $fieldname);
				
			if(isset($options) && is_array($options) && !isset($options['special'])) {

				$mapping = $options['mapping'];
				//if no mapping default to the name of the form field
				if(!$mapping) {
					$mapping = $fieldname;
				}
				
				
				$fieldValue = $this->gp[$mapping];
				
				if($options['mapping.']) {
					$fieldValue = Tx_Formhandler_StaticFuncs::getSingle($options, 'mapping');
				}

				//pre process the field value. e.g. to format a date
				if(is_array($options['preProcessing.'])) {
					$options['preProcessing.']['value'] = $fieldValue;
					$fieldValue = Tx_Formhandler_StaticFuncs::getSingle($options, 'preProcessing');
				}

				if($options['mapping.']) {
					$queryFields[$fieldname] = Tx_Formhandler_StaticFuncs::getSingle($options, 'mapping');
				} else {
					$queryFields[$fieldname] = $fieldValue;
				}

				//process empty value handling
				if($options['ifIsEmpty'] && strlen($this->gp[$options['mapping']]) == 0) {
						
					//if given settings is a TypoScript object
					if(isset($options['ifIsEmpty.']) && is_array($options['ifIsEmpty.'])) {
						$queryFields[$fieldname] = Tx_Formhandler_StaticFuncs::getSingle($options, 'ifIsEmpty');
					} else {
						$queryFields[$fieldname] = $options['ifIsEmpty'];
					}
				}

                if($options['nullIfEmpty'] && strlen($this->gp[$options['mapping']]) == 0) {
                    unset($queryFields[$fieldname]);
                }

                if($options['zeroIfEmpty'] && strlen($this->gp[$options['mapping']]) == 0) {
                    $queryFields[$fieldname] = 0;
                }

				//process array handling
				if(isset($this->gp[$options['mapping']]) && is_array($this->gp[$options['mapping']])) {
					$separator = ',';
					if($options['separator']) {
						$separator = $options['separator'];
					}
					$queryFields[$fieldname] = implode($separator, $this->gp[$options['mapping']]);
				}

				//process uploaded files
				$files = Tx_Formhandler_Session::get('files');
				if(isset($files[$fieldname]) && is_array($files[$fieldname])) {
					$queryFields[$fieldname] = $this->getFileList($fieldname);				
				}

				//special mapping
			} elseif(isset($options) && is_array($options) && isset($options['special'])) {
				switch($options['special']) {
					case 'sub_datetime':
						$now = date('Y-m-d H:i:s', time());
						$queryFields[$fieldname] = $now;
						break;
					case 'sub_tstamp':
						$queryFields[$fieldname] = time();
						break;
					case 'ip':
						$queryFields[$fieldname] = t3lib_div::getIndpEnv('REMOTE_ADDR');
						break;
					case 'inserted_uid':
						$table = $options['special.']['table'];
						if(is_array($this->gp['saveDB'])) {
							foreach($this->gp['saveDB'] as $info) {
								if($info['table'] == $table) {
									$queryFields[$fieldname] = $info['uid'];
								}
							}
						}
						break;
				}
			} else {
				$queryFields[$fieldname] = $options;
			}
			
			//post process the field value after formhandler did it's magic.
			if(is_array($options['postProcessing.'])) {
				$options['postProcessing.']['value'] = $queryFields[$fieldname];
				$queryFields[$fieldname] = Tx_Formhandler_StaticFuncs::getSingle($options, 'postProcessing');
			}
		}
		return $queryFields;
	}

	/**
	 * returns a list of uploaded files from given field.
	 * @return string list of filenames
	 * @param string $fieldname
	 */
	protected function getFileList($fieldname){
		$filenames = array();
		$files = Tx_Formhandler_Session::get('files');
		foreach ($files[$fieldname] as $file) {
			array_push($filenames, $file['uploaded_name']);
		}
		return implode(',', $filenames);
	}
	
	/**
	 * Returns current UID to use for updating the DB.
	 * @return int UID
	 */
	protected function getUpdateUid() {
		$uid = Tx_Formhandler_StaticFuncs::getSingle($this->settings, 'key_value');
		if(!$uid) {
			$uid = $this->gp[$this->key];
		}
		if(!$uid) {
			$uid = $this->gp['inserted_uid'];
		}
		return $uid;
	}
	
}
?>
