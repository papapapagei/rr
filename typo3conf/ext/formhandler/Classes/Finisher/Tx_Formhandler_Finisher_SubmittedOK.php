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
 * $Id: Tx_Formhandler_Finisher_SubmittedOK.php 27790 2009-12-17 09:28:42Z reinhardfuehricht $
 *                                                                        */

/**
 * A finisher showing the content of ###TEMPLATE_SUBMITTEDOK### replacing all common Formhandler markers
 * plus ###PRINT_LINK###, ###PDF_LINK### and ###CSV_LINK###.
 *
 * The finisher sets a flag in session, so that Formhandler will only call this finisher and nothing else if the user reloads the page.
 *
 * A sample configuration looks like this:
 * <code>
 * finishers.3.class = Tx_Formhandler_Finisher_SubmittedOK
 * finishers.3.config.returns = 1
 * finishers.3.config.pdf.class = Tx_Formhandler_Generator_TcPdf
 * finishers.3.config.pdf.exportFields = firstname,lastname,interests,pid,ip,submission_date
 * finishers.3.config.pdf.export2File = 1
 * finishers.3.config.csv.class = Tx_Formhandler_Generator_Csv
 * finishers.3.config.csv.exportFields = firstname,lastname,interests
 * </code>
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	Finisher
 */
class Tx_Formhandler_Finisher_SubmittedOK extends Tx_Formhandler_AbstractFinisher {

	/**
	 * The main method called by the controller
	 *
	 * @return array The probably modified GET/POST parameters
	 */
	public function process() {

		//read template file
		$this->templateFile = Tx_Formhandler_Globals::$templateCode;

		//set view
		$view = $this->componentManager->getComponent('Tx_Formhandler_View_SubmittedOK');

		//show TEMPLATE_SUBMITTEDOK
		$view->setTemplate($this->templateFile, ('SUBMITTEDOK' . Tx_Formhandler_Globals::$templateSuffix));
		if (!$view->hasTemplate()) {
			$view->setTemplate($this->templateFile, 'SUBMITTEDOK');
			if (!$view->hasTemplate()) {
				Tx_Formhandler_StaticFuncs::debugMessage('no_submittedok_template', array(), 3);
			}
		}

		$view->setSettings(Tx_Formhandler_Globals::$session->get('settings'));
		$view->setComponentSettings($this->settings);
		return $view->render($this->gp, array());
	}

}
?>
