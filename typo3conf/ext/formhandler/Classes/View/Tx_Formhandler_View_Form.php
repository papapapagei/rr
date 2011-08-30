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
 * $Id: Tx_Formhandler_View_Form.php 46386M 2011-04-08 14:26:07Z (local) $
 *                                                                        */

/**
 * A default view for Formhandler
 *
 * @author	Reinhard Führicht <rf@typoheads.at>
 * @package	Tx_Formhandler
 * @subpackage	View
 */
class Tx_Formhandler_View_Form extends Tx_Formhandler_AbstractView {

	/**
	 * Main method called by the controller.
	 *
	 * @param array $gp The current GET/POST parameters
	 * @param array $errors The errors occurred in validation
	 * @return string content
	 */
	public function render($gp, $errors) {

		//set GET/POST parameters
		$this->gp = $gp;

		//set template
		$this->template = $this->subparts['template'];
		if(!$this->template) {
			Tx_Formhandler_StaticFuncs::throwException('no_template_file');
		}

		$this->errors = $errors;

		//set language file
		if (!$this->langFiles) {
			$this->langFiles = Tx_Formhandler_Globals::$langFiles;
		}

			//fill Typoscript markers
		if (is_array($this->settings['markers.'])) {
			$this->fillTypoScriptMarkers();
		}

		//read master template
		if (!$this->masterTemplates) {
			$this->readMasterTemplates();
		}

		if (!empty($this->masterTemplates)) {
			$this->replaceMarkersFromMaster();
		}

		if (Tx_Formhandler_Globals::$ajaxHandler) {
			$markers = array();
			Tx_Formhandler_Globals::$ajaxHandler->fillAjaxMarkers($markers);
			$this->template = $this->cObj->substituteMarkerArray($this->template, $markers);
		}

		//fill Typoscript markers
		if (is_array($this->settings['markers.'])) {
			$this->fillTypoScriptMarkers();
		}

		$this->substituteHasTranslationSubparts();
		if (!$this->gp['submitted']) {
			$this->storeStartEndBlock();
		} elseif (intval(Tx_Formhandler_Globals::$session->get('currentStep')) !== 1) {
			$this->fillStartEndBlock();
		}

		if (intval($this->settings['fillValueMarkersBeforeLangMarkers']) === 1) {

			//fill value_[fieldname] markers
			$this->fillValueMarkers();
		}

		//fill LLL:[language_key] markers
		$this->fillLangMarkers();

		//substitute ISSET markers
		$this->substituteIssetSubparts();

		//fill default markers
		$this->fillDefaultMarkers();

		if (intval($this->settings['fillValueMarkersBeforeLangMarkers']) !== 1) {

			//fill value_[fieldname] markers
			$this->fillValueMarkers();
		}

		//fill selected_[fieldname]_value markers and checked_[fieldname]_value markers
		$this->fillSelectedMarkers();

		//fill LLL:[language_key] markers again to make language markers in other markers possible
		$this->fillLangMarkers();

		//fill error_[fieldname] markers
		if (!empty($errors)) {
			$this->fillIsErrorMarkers($errors);
			$this->fillErrorMarkers($errors);
		}

		//remove markers that were not substituted
		$content = Tx_Formhandler_StaticFuncs::removeUnfilledMarkers($this->template);
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Reads the translation file entered in TS setup.
	 *
	 * @return void
	 */
	protected function readMasterTemplates() {
		$this->masterTemplates = array();
		if (isset($this->settings['masterTemplateFile']) && !isset($this->settings['masterTemplateFile.'])) {
			array_push($this->masterTemplates, Tx_Formhandler_StaticFuncs::resolveRelPathFromSiteRoot($this->settings['masterTemplateFile']));
		} elseif (isset($this->settings['masterTemplateFile']) && isset($this->settings['masterTemplateFile.'])) {
			array_push($this->masterTemplates, Tx_Formhandler_StaticFuncs::getSingle($this->settings, 'masterTemplateFile'));
		} elseif (isset($this->settings['masterTemplateFile.']) && is_array($this->settings['masterTemplateFile.'])) {
			foreach ($this->settings['masterTemplateFile.'] as $key => $masterTemplate) {
				if (FALSE === strpos($key, '.')) {
					if (is_array($this->settings['masterTemplateFile.'][$key . '.'])) {
						array_push($this->masterTemplates, Tx_Formhandler_StaticFuncs::getSingle($this->settings['masterTemplateFile.'], $key));
					} else {
						array_push($this->masterTemplates, Tx_Formhandler_StaticFuncs::resolveRelPathFromSiteRoot($masterTemplate));
					}
				}
			}
		}
	}

	protected function replaceMarkersFromMaster() {
		$fieldMarkers = array();
		foreach ($this->masterTemplates as $idx => $masterTemplate) {
			$masterTemplateCode = t3lib_div::getURL($masterTemplate);
			$matches = array();
			preg_match_all('/###(field|master)_([^#]*)###/', $masterTemplateCode, $matches);
			if (!empty($matches[0])) {
				$subparts = array_unique($matches[0]);
				$subpartsCodes = array();
				if (is_array($subparts)) {
					foreach ($subparts as $index => $subpart) {
						$subpartKey = str_replace('#', '', $subpart);
						$subpartsCodes[$subpartKey] = $this->cObj->getSubpart($masterTemplateCode, $subpart);
					}
				}
				foreach ($subpartsCodes as $subpart=>$code) {
					$matchesSlave = array();
					preg_match_all('/###' . $subpart . '(###|_([^#]*)###)/', $this->template, $matchesSlave);
					if (!empty($matchesSlave[0])) {
						foreach ($matchesSlave[0] as $key=>$markerName) {
							$fieldName = $matchesSlave[2][$key];
							if ($fieldName) {
								$markers = array(
									'###fieldname###' => $fieldName,
									'###formValuesPrefix###' => Tx_Formhandler_Globals::$formValuesPrefix
								);
								$replacedCode = $this->cObj->substituteMarkerArray($code, $markers);
							} else {
								$replacedCode = $code;
							}
							$fieldMarkers[$markerName] = $replacedCode;
						}
					}
				}
			}
		}
		$this->template = $this->cObj->substituteMarkerArray($this->template, $fieldMarkers);
	}

	/**
	 * Helper method used by substituteIssetSubparts()
	 *
	 * @see Tx_Formhandler_StaticFuncs::substituteIssetSubparts()
	 * @author  Stephan Bauer <stephan_bauer(at)gmx.de>
	 * @return boolean
	 */
	protected function markersCountAsSet($conditionValue) {

		// Find first || or && or !
		$var = '[a-zA-Z0-9\-\|\[\]:]+';
		$pattern = "/(_*($var)_*(\|\||&&)_*([^_]+)_*)|(_*(!)_*($var))/";

		// recurse if there are more
		if ( preg_match($pattern, $conditionValue, $matches) ){
			$isset = $this->keyIsset($matches[2]);
			if ($matches[3] == '||' && $isset) {
				$return = TRUE;
			} elseif ($matches[3] == '||' && !$isset) {
				$return = $this->markersCountAsSet($matches[4]);
			} elseif ($matches[3] == '&&' && $isset) {
				$return = $this->markersCountAsSet($matches[4]);
			} elseif ($matches[3] == '&&' && !$isset) {
				$return = FALSE;
			} elseif ($matches[6] == '!' && !$isset) {
				$return = !$this->keyIsset($matches[7]);
			} elseif (Tx_Formhandler_Globals::$session->get('debug')) {
				Tx_Formhandler_StaticFuncs::debugMessage('invalid_isset', array($matches[2]), 2);
			}
		} else {

			// end of recursion
			$return = $this->keyIsset($conditionValue);
		}
		return $return;
	}

	/**
	 * Checks if a key in $this->gp exists. To find nested keys you can select them so:
	 * 
	 * <code title="Example isset markers for nested keys">
	 * <!-- ISSET_myarray:level1|level2 -->
	 * <!-- ISSET_myarray|level1|level2 -->
	 * <!-- ISSET_myarray[level1][level2]-->
	 * </code>
	 * 
	 * @param string the key/it's path
	 * @return boolean
	 */
	protected function keyIsset($key) {
		$key = str_replace(array(':', '[', ']'), array('|', '|', ''), $key);

		if (!strpos($key, '|')) {
			return !empty($this->gp[$key]);
		}

		$keys = explode('|', $key);

		$tmp = $this->gp[array_shift($keys)];
		foreach ($keys as $idx => $key) {
			if (empty($tmp[$key])) {
				return FALSE;
			}else {
				$tmp = $tmp[$key];
			}
		}
		return TRUE;
	}

	protected function substituteHasTranslationSubparts() {
		preg_match_all('/###has_translation_([^#]*)###/msi', $this->template, $matches);
		if (is_array($matches[0])) {

			$subparts = array_unique($matches[0]);
			$fields = array_unique($matches[1]);
			$subpartArray = array();
			foreach ($subparts as $key => $subpart) {
				$content = $this->cObj->getSubpart($this->template, $subpart);
				$translation = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, $fields[$key]);
				if (strlen($translation) === 0) {
					$content = '';
				}
				$this->template = $this->cObj->substituteSubpart($this->template, $subpart, $content);
			}
		}
	}

	/**
	 * Use or remove subparts with ISSET_[fieldname] patterns (thx to Stephan Bauer <stephan_bauer(at)gmx.de>)
	 *
	 * @author  Stephan Bauer <stephan_bauer(at)gmx.de>
	 * @return	string		substituted HTML content
	 */
	protected function substituteIssetSubparts() {
		$flags = array();
		$nowrite = FALSE;
		$out = array();
		$loopArr = explode(chr(10), $this->template);
		foreach ($loopArr as $idx => $line){

			// works only on it's own line
			$pattern = '/###isset_+([^#]*)_*###/i';

			// set for odd ISSET_xyz, else reset
			if (preg_match($pattern, $line, $matches)) {
				if (!$flags[$matches[1]]) { // set
					$flags[$matches[1]] = TRUE;

					// set nowrite flag if required until the next ISSET_xyz
					// (only if not already set by envelop)
					if ((!$this->markersCountAsSet($matches[1])) && (!$nowrite)) {
						$nowrite = $matches[1];
					}
				} else { // close it
					$flags[$matches[1]] = FALSE;
					if ($nowrite == $matches[1]) {
						$nowrite = 0;
					}
				}
			} elseif (!$nowrite) {
				$out[] = $line;
			}
		}
		$out = implode(chr(10), $out);
		$this->template = $out;
	}

	/**
	 * Copies the subparts ###FORM_STARTBLOCK### and ###FORM_ENDBLOCK### and stored them in session.
	 * This is needed to replace the markers ###FORM_STARTBLOCK### and ###FORM_ENDBLOCK### in the next steps.
	 *
	 * @return void
	 */
	protected function storeStartEndBlock() {
		$startblock = Tx_Formhandler_Globals::$session->get('startblock');
		$endblock = Tx_Formhandler_Globals::$session->get('endblock');
		if (empty($startblock)) {
			$startblock = $this->cObj->getSubpart($this->template, '###FORM_STARTBLOCK###');
		}
		if (empty($endblock)) {
			$endblock = $this->cObj->getSubpart($this->template, '###FORM_ENDBLOCK###');
		}
		Tx_Formhandler_Globals::$session->setMultiple(array ('startblock' => $startblock, 'endblock' => $endblock));
	}

	/**
	 * Fills the markers ###FORM_STARTBLOCK### and ###FORM_ENDBLOCK### with the stored values from session.
	 *
	 * @return void
	 */
	protected function fillStartEndBlock() {
		$markers = array (
			'###FORM_STARTBLOCK###' => Tx_Formhandler_Globals::$session->get('startblock'),
			'###FORM_ENDBLOCK###' => Tx_Formhandler_Globals::$session->get('endblock')
		);
		$this->template = $this->cObj->substituteMarkerArray($this->template, $markers);
	}

	/**
	 * Returns the global TypoScript settings of Formhandler
	 *
	 * @return array The settings
	 */
	protected function parseSettings() {
		return Tx_Formhandler_Globals::$session->get('settings');
	}

	/**
	 * Substitutes markers
	 * 		###selected_[fieldname]_[value]###
	 * 		###checked_[fieldname]_[value]###
	 * in $this->template
	 *
	 * @return void
	 */
	protected function fillSelectedMarkers() {
		$markers = array();

		if (is_array($this->gp)) {
			foreach ($this->gp as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $field => $value) {
						$markers['###checked_' . $k . '_' . $value . '###'] = 'checked="checked"';
						$markers['###selected_' . $k . '_' . $value . '###'] = 'selected="selected"';
					}
				} else {
					$markers['###checked_' . $k  .'_' . $v . '###'] = 'checked="checked"';
					$markers['###selected_' . $k . '_' . $v . '###'] = 'selected="selected"';
				}
			}
			$this->template = $this->cObj->substituteMarkerArray($this->template, $markers);
		}
	}

	/**
	 * Substitutes default markers in $this->template.
	 *
	 * @return void
	 */
	protected function fillDefaultMarkers() {
		$parameters = t3lib_div::_GET();
		if (isset($parameters['id'])) {
			unset($parameters['id']);
		}

		$path = $this->pi_getPageLink($GLOBALS['TSFE']->id, '', $parameters);
		$path = htmlspecialchars($path);
		$markers = array();
		$markers['###REL_URL###'] = $path;
		$markers['###TIMESTAMP###'] = time();
		$markers['###RANDOM_ID###'] = htmlspecialchars($this->gp['randomID']);
		$markers['###ABS_URL###'] = t3lib_div::locationHeaderUrl('') . $path;
		$markers['###rel_url###'] = $markers['###REL_URL###'];
		$markers['###timestamp###'] = $markers['###TIMESTAMP###'];
		$markers['###abs_url###'] = $markers['###ABS_URL###'];

		$name = 'submitted';
		if (Tx_Formhandler_Globals::$formValuesPrefix) {
			$name = Tx_Formhandler_Globals::$formValuesPrefix . '[submitted]';
		}
		$markers['###HIDDEN_FIELDS###'] = '
			<input type="hidden" name="id" value="' . $GLOBALS['TSFE']->id . '" />
			<input type="hidden" name="' . $name . '" value="1" />
		';
		
		$name = 'randomID';
		if (Tx_Formhandler_Globals::$formValuesPrefix) {
			$name = Tx_Formhandler_Globals::$formValuesPrefix . '[randomID]';
		}
		$markers['###HIDDEN_FIELDS###'] .= '
			<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($this->gp['randomID']) . '" />
		';

		$name = 'removeFile';
		if (Tx_Formhandler_Globals::$formValuesPrefix) {
			$name = Tx_Formhandler_Globals::$formValuesPrefix . '[removeFile]';
		}
		$markers['###HIDDEN_FIELDS###'] .= '
			<input type="hidden" id="removeFile-' . htmlspecialchars($this->gp['randomID']) . '" name="' . $name . '" value="" />
		';

		$name = 'removeFileField';
		if (Tx_Formhandler_Globals::$formValuesPrefix) {
			$name = Tx_Formhandler_Globals::$formValuesPrefix . '[removeFileField]';
		}
		$markers['###HIDDEN_FIELDS###'] .= '
			<input type="hidden" id="removeFileField-' . htmlspecialchars($this->gp['randomID']) . '" name="' . $name . '" value="" />
		';

		$name = 'submitField';
		if (Tx_Formhandler_Globals::$formValuesPrefix) {
			$name = Tx_Formhandler_Globals::$formValuesPrefix . '[submitField]';
		}
		$markers['###HIDDEN_FIELDS###'] .= '
			<input type="hidden" id="submitField-' . htmlspecialchars($this->gp['randomID']) . '" name="' . $name . '" value="" />
		';
		$markers['###formValuesPrefix###'] = Tx_Formhandler_Globals::$formValuesPrefix;

		if ($this->gp['generated_authCode']) {
			$markers['###auth_code###'] = $this->gp['generated_authCode'];
		}

		$markers['###ip###'] = t3lib_div::getIndpEnv('REMOTE_ADDR');
		$markers['###IP###'] = $markers['###ip###'];
		$markers['###submission_date###'] = date('d.m.Y H:i:s', time());
		$markers['###pid###'] = $GLOBALS['TSFE']->id;
		$markers['###PID###'] = $markers['###pid###'];

		// current step
		$currentStepFromSession = Tx_Formhandler_Globals::$session->get('currentStep');
		$markers['###curStep###'] = $currentStepFromSession;

		// maximum step/number of steps
		$markers['###maxStep###'] = Tx_Formhandler_Globals::$session->get('totalSteps');

		// the last step shown
		$markers['###lastStep###'] = Tx_Formhandler_Globals::$session->get('lastStep');

		$name = 'step-';
		$prefix = Tx_Formhandler_Globals::$formValuesPrefix;
		if ($prefix) {
			$name = $prefix . '[' . $name . '#step#-#action#]';
		} else {
			$name = $name . '#step#-#action#';
		}

		// submit name for next page
		$nextName = ' name="' . str_replace('#action#', 'next', $name) . '" ';
		$nextName = str_replace('#step#', $currentStepFromSession + 1, $nextName);
		$markers['###submit_nextStep###'] = $nextName;

		// submit name for previous page
		$prevName = ' name="' . str_replace('#action#', 'prev', $name) . '" ';
		$prevName = str_replace('#step#', $currentStepFromSession - 1, $prevName);
		$markers['###submit_prevStep###'] = $prevName;
		
			// submits for next/prev steps with template suffix
		preg_match_all('/###submit_nextStep_[^#]+?###/Ssm', $this->template, $allNextSubmits);
		foreach($allNextSubmits[0] as $nextSubmitSuffix){
			$nextSubmitSuffix = substr($nextSubmitSuffix, 19, -3);
			$nextName = ' name="' . str_replace('#action#', 'next', $name) . '['. $nextSubmitSuffix .']" ';
			$nextName = str_replace('#step#', $currentStepFromSession + 1, $nextName);
			$markers['###submit_nextStep_'. $nextSubmitSuffix .'###'] = $nextName;
		}

		preg_match_all('/###submit_prevStep_[^#]+?###/Ssm', $this->template, $allPrevSubmits);
		foreach($allPrevSubmits[0] as $prevSubmitSuffix){
			$prevSubmitSuffix = substr($prevSubmitSuffix, 19, -3);
			$prevName = ' name="' . str_replace('#action#', 'prev', $name) . '['. $prevSubmitSuffix .']" ';
			$prevName = str_replace('#step#', $currentStepFromSession + 1, $prevName);
			$markers['###submit_prevStep_'. $prevSubmitSuffix .'###'] = $prevName;
		}

		// submit name for reloading the same page/step
		$reloadName = ' name="' . str_replace('#action#', 'reload', $name) . '" ';
		$reloadName = str_replace('#step#', $currentStepFromSession, $reloadName);
		$markers['###submit_reload###'] = $reloadName;

		// step bar
		$prevName = str_replace('#action#', 'prev', $name);
		$prevName = str_replace('#step#', $currentStepFromSession - 1, $prevName);
		$nextName = str_replace('#action#', 'next', $name);
		$nextName = str_replace('#step#', $currentStepFromSession + 1, $nextName);
		$markers['###step_bar###'] = $this->createStepBar(
			$currentStepFromSession,
			Tx_Formhandler_Globals::$session->get('totalSteps'),
			$prevName,
			$nextName
		);
		$this->fillCaptchaMarkers($markers);
		$this->fillFEUserMarkers($markers);
		$this->fillFileMarkers($markers);

		if (!strstr($this->template, '###HIDDEN_FIELDS###')) {
			$this->template = str_replace(
				'</form>', 
				'<fieldset style="display: none;">' . $markers['###HIDDEN_FIELDS###'] . '</fieldset></form>', 
				$this->template
			);
		}

		$this->template = $this->cObj->substituteMarkerArray($this->template, $markers);
	}

	/**
	 * Fills the markers for the supported captcha extensions.
	 *
	 * @param array &$markers Reference to the markers array
	 * @return void
	 */
	protected function fillCaptchaMarkers(&$markers) {
		global $LANG;

		if (t3lib_extMgm::isLoaded('captcha')){
			$markers['###CAPTCHA###'] = '<img src="' . t3lib_extMgm::siteRelPath('captcha') . 'captcha/captcha.php" alt="" />';
			$markers['###captcha###'] = $markers['###CAPTCHA###'];
		}
		if (t3lib_extMgm::isLoaded('simple_captcha')) {
			require_once(t3lib_extMgm::extPath('simple_captcha') . 'class.tx_simplecaptcha.php');
			$simpleCaptcha_className = t3lib_div::makeInstanceClassName('tx_simplecaptcha');
			$this->simpleCaptcha = new $simpleCaptcha_className();
			$captcha = $this->simpleCaptcha->getCaptcha();
			$markers['###simple_captcha###'] = $captcha;
			$markers['###SIMPLE_CAPTCHA###'] = $captcha;
		}
		if (t3lib_extMgm::isLoaded('sr_freecap')){
			require_once(t3lib_extMgm::extPath('sr_freecap') . 'pi2/class.tx_srfreecap_pi2.php');
			$this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
			$markers = array_merge($markers, $this->freeCap->makeCaptcha());
		}
		if (t3lib_extMgm::isLoaded('jm_recaptcha')) {
			require_once(t3lib_extMgm::extPath('jm_recaptcha') . 'class.tx_jmrecaptcha.php');
			$this->recaptcha = new tx_jmrecaptcha();
			$markers['###RECAPTCHA###'] = $this->recaptcha->getReCaptcha();
			$markers['###recaptcha###'] = $markers['###RECAPTCHA###'];
		}

		if (t3lib_extMgm::isLoaded('wt_calculating_captcha')) {
			require_once(t3lib_extMgm::extPath('wt_calculating_captcha') . 'class.tx_wtcalculatingcaptcha.php');

			$captcha = t3lib_div::makeInstance('tx_wtcalculatingcaptcha');
			$markers['###WT_CALCULATING_CAPTCHA###'] = $captcha->generateCaptcha();
			$markers['###wt_calculating_captcha###'] = $markers['###WT_CALCULATING_CAPTCHA###'];
		}

		if (t3lib_extMgm::isLoaded('mathguard')) {
			require_once(t3lib_extMgm::extPath('mathguard') . 'class.tx_mathguard.php');

			$captcha = t3lib_div::makeInstance('tx_mathguard');
			$markers['###MATHGUARD###'] = $captcha->getCaptcha();
			$markers['###mathguard###'] = $markers['###MATHGUARD###'];
		}
	}

	/**
	 * Fills the markers ###FEUSER_[property]### with the data from $GLOBALS["TSFE"]->fe_user->user.
	 *
	 * @param array &$markers Reference to the markers array
	 * @return void
	 */
	protected function fillFEUserMarkers(&$markers) {
		if (is_array($GLOBALS["TSFE"]->fe_user->user)) {
			foreach ($GLOBALS["TSFE"]->fe_user->user as $k => $v) {
				$markers['###FEUSER_' . strtoupper($k) . '###'] = $v;
				$markers['###FEUSER_' . strtolower($k) . '###'] = $v;
				$markers['###feuser_' . strtoupper($k) . '###'] = $v;
				$markers['###feuser_' . strtolower($k) . '###'] = $v;
			}
		}
	}

	/**
	 * Fills the file specific markers:
	 *
	 *  ###[fieldname]_minSize###
	 *  ###[fieldname]_maxSize###
	 *  ###[fieldname]_allowedTypes###
	 *  ###[fieldname]_maxCount###
	 *  ###[fieldname]_fileCount###
	 *  ###[fieldname]_remainingCount###
	 *
	 *  ###[fieldname]_uploadedFiles###
	 *  ###total_uploadedFiles###
	 *
	 * @param array &$markers Reference to the markers array
	 * @return void
	 */
	public function fillFileMarkers(&$markers) {
		$settings = $this->parseSettings();

		$flexformValue = Tx_Formhandler_StaticFuncs::pi_getFFvalue($this->cObj->data['pi_flexform'], 'required_fields', 'sMISC');
		if ($flexformValue) {
			$fields = t3lib_div::trimExplode(',', $flexformValue);
			if (is_array($settings['validators.'])) {

				// Searches the index of Tx_Formhandler_Validator_Default
				foreach ($settings['validators.'] as $index => $validator) {
					if ($validator['class'] == 'Tx_Formhandler_Validator_Default') {
						break;
					}
				}
			} else {
				$index = 1;
			}

			// Adds the value.
			foreach ($fields as $idx => $field) {
				$settings['validators.'][$index . '.']['config.']['fieldConf.'][$field . '.']['errorCheck.'] = array();
				$settings['validators.'][$index . '.']['config.']['fieldConf.'][$field . '.']['errorCheck.']['1'] = 'required';
			}
		}

		$sessionFiles = Tx_Formhandler_Globals::$session->get('files');

		//parse validation settings
		if (is_array($settings['validators.'])) {
			foreach ($settings['validators.'] as $key => $validatorSettings) {
				if (is_array($validatorSettings['config.']) && is_array($validatorSettings['config.']['fieldConf.'])) {
					foreach ($validatorSettings['config.']['fieldConf.'] as $fieldname => $fieldSettings) {
						$replacedFieldname = str_replace('.', '', $fieldname);
						if (is_array($fieldSettings['errorCheck.'])) {
							foreach ($fieldSettings['errorCheck.'] as $key => $check) {
								switch ($check) {
									case 'fileMinSize':
										$minSize = $fieldSettings['errorCheck.'][$key . '.']['minSize'];
										$markers['###' . $replacedFieldname . '_minSize###'] = t3lib_div::formatSize($minSize, ' Bytes | KB | MB | GB');
										break;
									case 'fileMaxSize':
										$maxSize = $fieldSettings['errorCheck.'][$key . '.']['maxSize'];
										$markers['###' . $replacedFieldname . '_maxSize###'] = t3lib_div::formatSize($maxSize, ' Bytes | KB | MB | GB');
										break;
									case 'fileAllowedTypes':
										$types = $fieldSettings['errorCheck.'][$key . '.']['allowedTypes'];
										$markers['###' . $replacedFieldname . '_allowedTypes###'] = $types;
										break;
									case 'fileMaxCount':
										$maxCount = $fieldSettings['errorCheck.'][$key . '.']['maxCount'];
										$markers['###' . $replacedFieldname . '_maxCount###'] = $maxCount;
										
										$fileCount = count($sessionFiles[str_replace( '.', '', $fieldname)]);
										$markers['###' . $replacedFieldname . '_fileCount###'] = $fileCount;

										$remaining = $maxCount - $fileCount;
										$markers['###' . $replacedFieldname . '_remainingCount###'] = $remaining;
										break;
									case 'fileMinCount':
										$minCount = $fieldSettings['errorCheck.'][$key.'.']['minCount'];
										$markers['###' . $replacedFieldname . '_minCount###'] = $minCount;
										break;
									case 'required':case 'fileRequired':case 'jmRecaptcha':case 'captcha':case 'srFreecap':case 'mathguard':
										$requiredSign = Tx_Formhandler_StaticFuncs::getSingle($settings, 'requiredSign');
										if(strlen($requiredSign) === 0) {
											$requiredSign = '*';
										}
										$markers['###required_' . $replacedFieldname . '###'] = $requiredSign;
										break;
								}
							}
						}
					}
				}
			}
		}
		if (is_array($sessionFiles)) {
			$singleWrap = $settings['singleFileMarkerTemplate.']['singleWrap'];
			$totalMarkerSingleWrap = $settings['totalFilesMarkerTemplate.']['singleWrap'];
			$totalWrap = $settings['singleFileMarkerTemplate.']['totalWrap'];
			$totalMarkersTotalWrap = $settings['totalFilesMarkerTemplate.']['totalWrap'];
			foreach ($sessionFiles as $field => $files) {
				foreach ($files as $idx => $fileInfo) {
					$filename = $fileInfo['name'];
					$thumb = '';
					if (intval($settings['singleFileMarkerTemplate.']['showThumbnails']) === 1 || intval($settings['singleFileMarkerTemplate.']['showThumbnails']) === 2) {
						$imgConf['image.'] = $settings['singleFileMarkerTemplate.']['image.'];
						$thumb = $this->getThumbnail($imgConf, $fileInfo);
					}
					$text = Tx_Formhandler_StaticFuncs::getSingle($settings['files.'], 'customRemovalText');
					if(strlen($text) === 0) {
						$text = 'X';
					}
					$link = '';
					$uploadedFileName = $fileInfo['uploaded_name'];
					if (!$uploadedFileName) {
						$uploadedFileName = $fileInfo['name'];
					}				
					if (Tx_Formhandler_Globals::$ajaxHandler && $settings['files.']['enableAjaxFileRemoval']) {
						$link= Tx_Formhandler_Globals::$ajaxHandler->getFileRemovalLink($text, $field, $uploadedFileName);
					} elseif ($settings['files.']['enableFileRemoval']) {
						$submitName = 'step-' . Tx_Formhandler_Globals::$session->get('currentStep') . '-reload';
						if (Tx_Formhandler_Globals::$formValuesPrefix) {
							$submitName = Tx_Formhandler_Globals::$formValuesPrefix . '[' . $submitName . ']';
						}
						$onClick = "
							document.getElementById('removeFile-" . Tx_Formhandler_Globals::$randomID . "').value='" . $uploadedFileName . "';
							document.getElementById('removeFileField-" . Tx_Formhandler_Globals::$randomID . "').value='" . $field . "';
							document.getElementById('submitField-" . Tx_Formhandler_Globals::$randomID . "').name='" . $submitName . "';
							
						";
						
						if (Tx_Formhandler_Globals::$formID) {
							$onClick .= "document.getElementById('" . Tx_Formhandler_Globals::$formID . "').submit();";
						} else {
							$onClick .= 'document.forms[0].submit();';
						}
						
						$onClick .= 'return false;';
						
						$link = '<a 
								href="javascript:void(0)" 
								class="formhandler_removelink" 
								onclick="' . str_replace(array("\n", '	'), '', $onClick) . '"
								>' . $text . '</a>';
					}

					if (strlen($singleWrap) > 0 && strstr($singleWrap, '|')) {
						$wrappedFilename = str_replace('|', $filename . $link, $singleWrap);
						$wrappedThumb = str_replace('|', $thumb . $link, $singleWrap);
						$wrappedThumbFilename = str_replace('|', $thumb . ' ' . $filename . $link, $singleWrap);
					} else {
						$wrappedFilename = $filename . $link;
						$wrappedThumb = $thumb . $link;
						$wrappedThumbFilename = $thumb . ' ' . $filename . $link;
					}
					if (intval($settings['singleFileMarkerTemplate.']['showThumbnails']) === 1) {
						$markers['###' . $field . '_uploadedFiles###'] .= $wrappedThumb;
					} elseif (intval($settings['singleFileMarkerTemplate.']['showThumbnails']) === 2) {
						$markers['###' . $field . '_uploadedFiles###'] .= $wrappedThumbFilename;
					} else {
						$markers['###' . $field . '_uploadedFiles###'] .= $wrappedFilename;
					}
					$uploadedFileName = $fileInfo['name'];
					if (!$uploadedFileName) {
						$uploadedFileName = $fileInfo['uploaded_name'];
					}
					if (intval($settings['totalFilesMarkerTemplate.']['showThumbnails']) === 1 || intval($settings['totalFilesMarkerTemplate.']['showThumbnails']) === 2) {
						$imgConf['image.'] = $settings['totalFilesMarkerTemplate.']['image.'];
						if (!$imgconf['image.']) {
							$imgConf['image.'] = $settings['singleFileMarkerTemplate.']['image.'];
						}
						$thumb = $this->getThumbnail($imgConf, $fileInfo);
						
					}
					if (strlen($totalMarkerSingleWrap) > 0 && strstr($totalMarkerSingleWrap, '|')) {
						$wrappedFilename = str_replace('|', $filename . $link, $totalMarkerSingleWrap);
						$wrappedThumb = str_replace('|', $thumb . $link, $totalMarkerSingleWrap);
						$wrappedThumbFilename = str_replace('|', $thumb . ' ' . $filename . $link, $totalMarkerSingleWrap);
					} else {
						$wrappedFilename = $filename . $link;
						$wrappedThumb = $thumb . $link;
						$wrappedThumbFilename = $thumb . $filename . $link;
					}
					if (intval($settings['totalFilesMarkerTemplate.']['showThumbnails']) === 1) {
						$markers['###total_uploadedFiles###'] .= $wrappedThumb;
					} elseif (intval($settings['totalFilesMarkerTemplate.']['showThumbnails']) === 2) {
						$markers['###total_uploadedFiles###'] .= wrappedThumbFilename;
					} else {
						$markers['###total_uploadedFiles###'] .= $wrappedFilename;
					}
				}
				if (strlen($totalWrap) > 0 && strstr($totalWrap,'|')) {
					$markers['###' . $field . '_uploadedFiles###'] = str_replace('|', $markers['###' . $field . '_uploadedFiles###'],$totalWrap);
				}
				$markers['###' . $field . '_uploadedFiles###'] = '<div id="Tx_Formhandler_UploadedFiles_' . $field . '">' . $markers['###' . $field . '_uploadedFiles###'] . '</div>';
			}
			if (strlen($totalMarkersTotalWrap) > 0 && strstr($totalMarkersTotalWrap, '|')) {
				$markers['###total_uploadedFiles###'] = str_replace('|', $markers['###total_uploadedFiles###'], $totalMarkersTotalWrap);
			}
			$markers['###TOTAL_UPLOADEDFILES###'] = $markers['###total_uploadedFiles###'];
			$markers['###total_uploadedfiles###'] = $markers['###total_uploadedFiles###'];
		}

		$requiredSign = Tx_Formhandler_StaticFuncs::getSingle($settings, 'requiredSign');
		if (strlen($requiredSign) === 0) {
			$requiredSign = '*';
		}
		$markers['###required###'] = $requiredSign;
		$markers['###REQUIRED###'] = $markers['###required###'];
	}

	protected function getThumbnail(&$imgConf, &$fileInfo) {
		$filename = $fileInfo['name'];
		$imgConf['image'] = 'IMAGE';
		if (!$imgConf['image.']['altText']) {
			$imgConf['image.']['altText'] = $filename;
		}
		if (!$imgConf['image.']['titleText']) {
			$imgConf['image.']['titleText'] = $filename;
		}
		$relPath = substr(($fileInfo['uploaded_folder'] . $fileInfo['uploaded_name']), 1);

		$imgConf['image.']['file'] = $relPath;
		if (!$imgConf['image.']['file.']['width'] && !$imgConf['image.']['file.']['height']) {
			$imgConf['image.']['file.']['width'] = '100m';
			$imgConf['image.']['file.']['height'] = '100m';
		}
		$thumb = $this->cObj->IMAGE($imgConf['image.']);
		return $thumb;
	}

	/**
	 * Substitutes markers
	 * 		###is_error_[fieldname]###
	 * 		###is_error###
	 * in $this->template
	 *
	 * @return void
	 */
	protected function fillIsErrorMarkers(&$errors) {
		$markers = array();
		foreach ($errors as $field => $types) {
			if ($this->settings['isErrorMarker.'][$field]) {
				$errorMessage = Tx_Formhandler_StaticFuncs::getSingle($this->settings['isErrorMarker.'], $field);
			} elseif (strlen($temp = trim(Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'is_error_' . $field))) > 0) {
				$errorMessage = $temp;
			} elseif ($this->settings['isErrorMarker.']['default']) {
				$errorMessage = Tx_Formhandler_StaticFuncs::getSingle($this->settings['isErrorMarker.'], 'default');
			} elseif (strlen($temp = trim(Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'is_error_default'))) > 0) {
				$errorMessage = $temp;
			} 
			$markers['###is_error_' . $field . '###'] = $errorMessage;
		}
		if ($this->settings['isErrorMarker.']['global']) {
			$errorMessage = Tx_Formhandler_StaticFuncs::getSingle($this->settings['isErrorMarker.'], 'global');
		} elseif (strlen($temp = trim(Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'is_error'))) > 0) {
			$errorMessage = $temp;
		}
		$markers['###is_error###'] = $errorMessage;
		$this->template = $this->cObj->substituteMarkerArray($this->template, $markers);
	}

	/**
	 * Substitutes markers
	 * 		###error_[fieldname]###
	 * 		###ERROR###
	 * in $this->template
	 *
	 * @return void
	 */
	protected function fillErrorMarkers(&$errors) {
		$markers = array();
		$singleWrap = $this->settings['singleErrorTemplate.']['singleWrap'];
		foreach ($errors as $field => $types) {
			$errorMessages = array();
			$clearErrorMessages = array();
			$temp = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'error_' . $field);
			if (strlen($temp) > 0) {
				$errorMessage = $temp;
				if (strlen($singleWrap) > 0 && strstr($singleWrap, '|')) {
					$errorMessage = str_replace('|', $errorMessage, $singleWrap);
				}
				$errorMessages[] = $errorMessage;
			}
			if (!is_array($types)) {
				$types = array($types);
			}
			foreach ($types as $idx => $type) {
				$temp = t3lib_div::trimExplode(';', $type);
				$type = array_shift($temp);
				foreach ($temp as $subIdx => $item) {
					$item = t3lib_div::trimExplode('::', $item);
					$values[$item[0]] = $item[1];
				}

					//try to load specific error message with key like error_fieldname_integer
				$errorMessage = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'error_' . $field . '_' . $type);
				if (strlen($errorMessage) === 0) {
					$type = strtolower($type);
					$errorMessage = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'error_' . $field . '_' . $type);
				}
				if ($errorMessage) {
					if (is_array($values)) {
						foreach ($values as $key => $value) {
							$errorMessage = str_replace('###' . $key . '###', $value, $errorMessage);
						}
					}
					if (strlen($singleWrap) > 0 && strstr($singleWrap,'|')) {
						$errorMessage = str_replace('|', $errorMessage, $singleWrap);
					}
					$errorMessages[] = $errorMessage;
				} else {
					Tx_Formhandler_StaticFuncs::debugMessage('no_error_message', array('error_' . $field . '_' . $type), 2);
				}
			}
			$errorMessage = implode('', $errorMessages);
			$totalWrap = $this->settings['singleErrorTemplate.']['totalWrap'];
			if (strlen($totalWrap) > 0 && strstr($totalWrap, '|')) {
				$errorMessage = str_replace('|', $errorMessage, $totalWrap);
			}
			$clearErrorMessage = $errorMessage;
			if ($this->settings['addErrorAnchors']) {
				$errorMessage = '<a name="' . $field . '">' . $errorMessage . '</a>';
			}
			$langMarkers = Tx_Formhandler_StaticFuncs::getFilledLangMarkers($errorMessage, $this->langFiles);
			$errorMessage = $this->cObj->substituteMarkerArray($errorMessage, $langMarkers);
			$markers['###error_' . $field . '###'] = $errorMessage;
			$markers['###ERROR_' . strtoupper($field) . '###'] = $errorMessage;
			$errorMessage = $clearErrorMessage;
			if ($this->settings['addErrorAnchors']) {
				$errorMessage = '<a href="' . t3lib_div::getIndpEnv('REQUEST_URI') . '#' . $field . '">' . $errorMessage . '</a>';
			}

			//list settings
			$listSingleWrap = $this->settings['errorListTemplate.']['singleWrap'];
			if (strlen($listSingleWrap) > 0 && strstr($listSingleWrap, '|')) {
				$errorMessage = str_replace('|', $errorMessage, $listSingleWrap);
			}
			$markers['###ERROR###'] .= $errorMessage;
		}
		$totalWrap = $this->settings['errorListTemplate.']['totalWrap'];
		if (strlen($totalWrap) > 0 && strstr($totalWrap, '|')) {
			$markers['###ERROR###'] = str_replace('|', $markers['###ERROR###'], $totalWrap);
		}
		$langMarkers = Tx_Formhandler_StaticFuncs::getFilledLangMarkers($markers['###ERROR###'], $this->langFiles);
		$markers['###ERROR###'] = $this->cObj->substituteMarkerArray($markers['###ERROR###'], $langMarkers);
		$markers['###error###'] = $markers['###ERROR###'];
		$this->template = $this->cObj->substituteMarkerArray($this->template, $markers);
	}

	/**
	 * Substitutes markers defined in TypoScript in $this->template
	 *
	 * @return void
	 */
	protected function fillTypoScriptMarkers() {
		$markers = array();
		if (is_array($this->settings['markers.'])) {
			foreach ($this->settings['markers.'] as $name => $options) {
				if (!strstr($name, '.') && strstr($this->template, '###' . $name . '###')) {
					$markers['###' . $name . '###'] = Tx_Formhandler_StaticFuncs::getSingle($this->settings['markers.'], $name);
				}
			}
		}
		$this->template = $this->cObj->substituteMarkerArray($this->template, $markers);
	}

	/**
	 * Substitutes markers
	 * 		###value_[fieldname]###
	 * 		###VALUE_[FIELDNAME]###
	 * 		###[fieldname]###
	 * 		###[FIELDNAME]###
	 * in $this->template
	 *
	 * @return void
	 */
	protected function fillValueMarkers() {
		$markers = $this->getValueMarkers($this->gp);
		$this->template = $this->cObj->substituteMarkerArray($this->template, $markers);

		//remove remaining VALUE_-markers
		//needed for nested markers like ###LLL:tx_myextension_table.field1.i.###value_field1###### to avoid wrong marker removal if field1 isn't set
		$this->template = preg_replace('/###value_.*?###/i', '', $this->template);
	}

	protected function getValueMarkers($values, $level = 0, $prefix = 'value_') {
		$markers = array();
		
		$arrayValueSeparator = Tx_Formhandler_StaticFuncs::getSingle($this->settings, 'arrayValueSeparator');
		if(strlen($arrayValueSeparator) === 0) {
			$arrayValueSeparator = ',';
		}
		if (is_array($values)) {
			foreach ($values as $k => $v) {
				$currPrefix = $prefix;
				if ($level === 0) {
					$currPrefix .= $k;
				} else {
					$currPrefix .= '|' . $k;
				}
				if (is_array($v)) {
					$level++;
					$markers = array_merge($markers, $this->getValueMarkers($v, $level, $currPrefix));
					$v = implode($arrayValueSeparator, $v);
					$level--;
				}
				$v = trim($v);
				$markers['###' . $currPrefix . '###'] = htmlspecialchars($v);
				$markers['###' . strtoupper($currPrefix) . '###'] = htmlspecialchars($v);
			}
		}
		return $markers;
	}

	/**
	 * Substitutes markers
	 * 		###LLL:[languageKey]###
	 * in $this->template
	 *
	 * @return void
	 */
	protected function fillLangMarkers() {
		$langMarkers = array();
		if (is_array($this->langFiles)) {
			$aLLMarkerList = array();
			preg_match_all('/###LLL:[^#]+?###/Ssm', $this->template, $aLLMarkerList);
			foreach ($aLLMarkerList[0] as $idx => $LLMarker){
				$llKey = substr($LLMarker, 7, (strlen($LLMarker) - 10));
				$marker = $llKey;
				$message = '';
				foreach ($this->langFiles as $subIdx => $langFile) {
					$temp = trim($GLOBALS['TSFE']->sL('LLL:' . $langFile . ':' . $llKey));
					if (strlen($temp) > 0) {
						$message = $temp;
					}
				}
				$langMarkers['###LLL:' . $marker . '###'] = $message;
			}
		}
		$this->template = $this->cObj->substituteMarkerArray($this->template, $langMarkers);
	}

	/**
	 * improved copy from dam_index
	 * 
	 * Returns HTML of a box with a step counter and "back" and "next" buttons
	 * Use label "next"/"prev" or "next_[stepnumber]"/"prev_[stepnumber]" for specific step in language file as button text.
	 * 
	 * <code>
	 * #set background color
	 * plugin.Tx_Formhandler.settings.stepbar_color = #EAEAEA
	 * #use default CSS, written to temp file
	 * plugin.Tx_Formhandler.settings.useDefaultStepBarStyles = 1
	 * </code>
	 * 
	 * @author Johannes Feustel
	 * @param	integer	$currentStep current step (begins with 1)
	 * @param	integer	$lastStep last step
	 * @param	string	$buttonNameBack name attribute of the back button
	 * @param	string	$buttonNameFwd name attribute of the forward button
	 * @return 	string	HTML code
	 */
	protected function createStepBar($currentStep, $lastStep, $buttonNameBack = '', $buttonNameFwd = '') {

		//colors
		$bgcolor = '#EAEAEA';
		$bgcolor = $this->settings['stepbar_color'] ? $this->settings['stepbar_color'] : $bgcolor;

		$nrcolor = t3lib_div::modifyHTMLcolor($bgcolor, 30, 30, 30);
		$errorbgcolor = '#dd7777';
		$errornrcolor = t3lib_div::modifyHTMLcolor($errorbgcolor, 30, 30, 30);

		$classprefix = Tx_Formhandler_Globals::$formValuesPrefix . '_stepbar';

		$css = array();
		$css[] = '.' . $classprefix . ' { background:'  . $bgcolor . '; padding:4px;}';
		$css[] = '.' . $classprefix . '_error { background: ' . $errorbgcolor . ';}';
		$css[] = '.' . $classprefix . '_steps { margin-left:50px; margin-right:25px; vertical-align:middle; font-family:Verdana,Arial,Helvetica; font-size:22px; font-weight:bold; }';
		$css[] = '.' . $classprefix . '_steps span { color:'.$nrcolor.'; margin-left:5px; margin-right:5px; }';
		$css[] = '.' . $classprefix . '_error .' . $classprefix . '_steps span { color:' . $errornrcolor . '; margin-left:5px; margin-right:5px; }';
		$css[] = '.' . $classprefix . '_steps .' . $classprefix . '_currentstep { color:  #000;}';
		$css[] = '#stepsFormButtons { margin-left:25px;vertical-align:middle;}';

		$content = '';
		$buttons = '';

		for ($i = 1; $i <= $lastStep; $i++) {
			$class = '';
			if ($i == $currentStep) {
				$class =  'class="' . $classprefix . '_currentstep"';
			}
			$stepName = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'step-' . $i);
			if (strlen($stepName) === 0) {
				$stepName = $i;
			}
			$content.= '<span ' . $class . ' >' . $stepName . '</span>';
		}
		$content = '<span class="' . $classprefix . '_steps' . '">' . $content . '</span>';

		//if not the first step, show back button
		if ($currentStep > 1) {
			//check if label for specific step
			$buttonvalue = '';
			$message = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'prev_' . $currentStep);
			if (strlen($message) === 0) {
				$message = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'prev');
			}
			$buttonvalue = $message;
			$buttons .= '<input type="submit" name="' . $buttonNameBack . '" value="' . trim($buttonvalue) . '" class="button_prev" style="margin-right:10px;" />';
		}
		$buttonvalue = '';
		$message = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'next_' . $currentStep);
		if (strlen($message) === 0) {
			$message = Tx_Formhandler_StaticFuncs::getTranslatedMessage($this->langFiles, 'next');
		}
		$buttonvalue = $message;
		$buttons .= '<input type="submit" name="' . $buttonNameFwd . '" value="' . trim($buttonvalue) . '" class="button_next" />';

		$content .= '<span id="stepsFormButtons">' . $buttons . '</span>';

		//wrap
		$classes = $classprefix;
		if ($this->errors) {
			$classes = $classes . ' ' . $classprefix . '_error';
		}
		$content = '<div class="' . $classes . '" >' . $content . '</div>';
		
		//add default css to page
		if ($this->settings['useDefaultStepBarStyles']){
			$css = implode("\n", $css);
			$css = TSpagegen::inline2TempFile($css, 'css');
			if (version_compare(TYPO3_version, '4.3.0') >= 0) {
				$css = '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($css) . '" />';
			}
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_' . $classprefix] .= $css;
		}
		return $content;
	}
}
?>