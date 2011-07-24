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
 * $Id: Tx_Formhandler_Interceptor_IPBlocking.php 35672 2010-07-15 08:57:25Z reinhardfuehricht $
 *                                                                        */

/**
 * An interceptor checking if form got submitted too often by an IP address or globally.
 * Settings how often a form is allowed to be submitted and the period of time are set in TypoScript.
 *
 * This interceptor uses log entries made by Tx_Formhandler_Logger_DB.
 *
 * Example:
 * <code>
 * saveInterceptors.1.class = Tx_Formhandler_Interceptor_IPBlocking
 *
 * saveInterceptors.1.config.redirectPage = 17
 *
 * saveInterceptors.1.config.report.email = example@host.com,example2@host.com
 * saveInterceptors.1.config.report.subject = Submission limit reached
 * saveInterceptors.1.config.report.sender = somebody@otherhost.com
 * saveInterceptors.1.config.report.interval.value = 5
 * saveInterceptors.1.config.report.interval.unit = minutes
 *
 * saveInterceptors.1.config.ip.timebase.value = 5
 * saveInterceptors.1.config.ip.timebase.unit = minutes
 * saveInterceptors.1.config.ip.threshold = 2
 *
 * saveInterceptors.1.config.global.timebase.value = 5
 * saveInterceptors.1.config.global.timebase.unit = minutes
 * saveInterceptors.1.config.global.threshold = 30
 * </code>
 *
 * This example configuration says that the form is allowed to be submitted twice in a period of 5 minutes and 30 times in 5 minutes globally.
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @see Tx_Formhandler_Logger_DB
 * @package	Tx_Formhandler
 * @subpackage	Interceptor
 */
class Tx_Formhandler_Interceptor_IPBlocking extends Tx_Formhandler_AbstractInterceptor {

	/**
	 * The table where the form submissions are logged
	 *
	 * @access protected
	 * @var string
	 */
	protected $logTable = 'tx_formhandler_log';
	
	/**
	 * The main method called by the controller
	 *
	 * @return array The probably modified GET/POST parameters
	 */
	public function process() {
		
		$ipTimebaseValue = $this->settings['ip.']['timebase.']['value'];
		$ipTimebaseUnit = $this->settings['ip.']['timebase.']['unit'];
		$ipMaxValue = $this->settings['ip.']['threshold'];

		if($ipTimebaseValue && $ipTimebaseUnit && $ipMaxValue) {
			$this->check($ipTimebaseValue, $ipTimebaseUnit, $ipMaxValue, TRUE);
		}

		$globalTimebaseValue = $this->settings['global.']['timebase.']['value'];
		$globalTimebaseUnit = $this->settings['global.']['timebase.']['unit'];
		$globalMaxValue = $this->settings['global.']['threshold'];
		
		if($globalTimebaseValue && $globalTimebaseUnit && $globalMaxValue) {
			$this->check($globalTimebaseValue, $globalTimebaseUnit, $globalMaxValue, TRUE);
		}

		return $this->gp;
	}

	/**
	 * Checks if the form got submitted too often and throws Exception if TRUE.
	 *
	 * @param int Timebase value
	 * @param string Timebase unit (seconds|minutes|hours|days)
	 * @param int maximum amount of submissions in given time base.
	 * @param boolean add IP address to where clause
	 * @return void
	 */
	private function check($value, $unit, $maxValue, $addIPToWhere = TRUE) {
		$timestamp = Tx_Formhandler_StaticFuncs::getTimestamp($value, $unit);
		$where = 'crdate >= ' . $timestamp;
		if($addIPToWhere) {
			$where = 'ip=\'' . t3lib_div::getIndpEnv('REMOTE_ADDR') . '\' AND ' . $where;
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,ip,crdate,params', $this->logTable, $where);

		if($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res) >= $maxValue) {
			$this->log(TRUE);
			$message = 'You are not allowed to send more mails because form got submitted too many times ';
			if($addIPToWhere) {
				$message .= 'by your IP address ';
			}
			$message .= 'in the last ' . $value . ' '  .$unit . '!';
			if($this->settings['report.']['email']) {
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$rows[] = $row;
				}
				$intervalValue = $this->settings['report.']['interval.']['value'];
				$intervalUnit = $this->settings['report.']['interval.']['unit'];
				$send = TRUE;
				if($intervalUnit && $intervalValue) {
					$intervalTstamp = Tx_Formhandler_StaticFuncs::getTimestamp($intervalValue, $intervalUnit);
					$where = 'pid=' . $GLOBALS['TSFE']->id . ' AND crdate>' . $intervalTstamp;
					if($addIPToWhere) {
						$where .= ' AND ip=\'' . t3lib_div::getIndpEnv('REMOTE_ADDR') . '\'';
					}

					$res_log = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', $this->reportTable, $where);

					if($res_log && $GLOBALS['TYPO3_DB']->sql_num_rows($res_log) > 0) {
						$send = FALSE;
						$GLOBALS['TYPO3_DB']->sql_free_result($res_log);
					}
				}
				if($send) {
					if($addIPToWhere) {
						$this->sendReport('ip', $rows);
					} else {
						$this->sendReport('global', $rows);
					}
				} else {
					Tx_Formhandler_StaticFuncs::debugMessage('alert_mail_not_sent');
				}
				
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			if($this->settings['redirectPage']) {
				Tx_Formhandler_StaticFuncs::doRedirect($this->settings['redirectPage'], $this->settings['correctRedirectUrl']);
				Tx_Formhandler_StaticFuncs::debugMessage('redirect_failed');
				exit(0);
			} else {
				throw new Exception($message);
			}
		}
	}

	/**
	 * Sends a report mail to recipients set in TypoScript.
	 *
	 * @param string (ip|global) Defines the message sent
	 * @param array The select rows of log table
	 * @return void
	 */
	private function sendReport($type,&$rows) {
		$email = t3lib_div::trimExplode(',', $this->settings['report.']['email']);
		$sender = $this->settings['report.']['sender'];
		$subject = $this->settings['report.']['subject'];
		$message = '';
		if($type == 'ip') {
			$message = 'IP address "' . t3lib_div::getIndpEnv('REMOTE_ADDR') . '" has submitted a form too many times!';
		} else {
			$message = 'A form got submitted too many times!';
		}

		$message .= "\n\n" . 'This is the URL to the form: ' . t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
		if(is_array($rows)) {
			$message .= "\n\n" . 'These are the submitted values:' . "\n\n";
			foreach($rows as $row) {
				$message .= date('Y/m/d h:i:s' , $row['crdate']) . ":\n";
				$message .= 'IP: ' . $row['ip'] . "\n";
				$message .= 'Params:' . "\n";
				$params = unserialize($row['params']);
				foreach($params as $key=>$value) {
					if(is_array($value)) {
						$value = implode(',', $value);
					}
					$message .= "\t" . $key . ': ' . $value . "\n";
				}
				$message .= '---------------------------------------' . "\n";
			}
		}

		//init mailer object
		require_once(PATH_t3lib.'class.t3lib_htmlmail.php');
		$emailObj = t3lib_div::makeInstance('t3lib_htmlmail');
		$emailObj->start();

		//set e-mail options
		$emailObj->subject = $subject;
			
		$emailObj->from_email = $sender;
			
		$emailObj->setPlain($message);
			
		//send e-mails
		foreach($email as $mailto) {

			$sent = $emailObj->send($mailto);
			if($sent) {
				Tx_Formhandler_StaticFuncs::debugMessage('mail_sent', $mailto);
				Tx_Formhandler_StaticFuncs::debugMessage('mail_sender', $emailObj->from_email, FALSE);
				Tx_Formhandler_StaticFuncs::debugMessage('mail_subject', $emailObj->subject, FALSE);
				Tx_Formhandler_StaticFuncs::debugMessage('mail_message', $message, FALSE);
			} else {
				Tx_Formhandler_StaticFuncs::debugMessage('mail_not_sent', $mailto);
				Tx_Formhandler_StaticFuncs::debugMessage('mail_sender', $emailObj->from_email, FALSE);
				Tx_Formhandler_StaticFuncs::debugMessage('mail_subject', $emailObj->subject, FALSE);
				Tx_Formhandler_StaticFuncs::debugMessage('mail_message', $message, FALSE);

			}
		}
		
	}

}
?>