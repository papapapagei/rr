<?php
	class tx_imageedit
	{
		var $curCType;
		var $config;
		var $damLoaded = false;
		var $curImageID = 0;
		
		function init($PA, $fobj)
		{
			//INIT mapped php values!!!!
			$extPath = 'typo3conf/ext/go_imageedit_be/';
			$resPath = $extPath.'res/';
			$imgCrop = '../'.$resPath.'image-crop/';
			
			//UID des CElements
			$uid = $PA['row']['uid'];
			
			//CType für locale-typoscript-config
			// changed by Caspar, we need the complete CType, not just the extension key [substr($PA['row']['CType'], 0, -4)]
			$this->curCType = $PA['row']['CType'] != 'list' ? $PA['row']['CType'] : $PA['row']['list_type'];
			
			$this->config = $this->getTCA($this->curCType);
			$this->inheritFromDefaultTCA();
			
			//Array mit allen Bildern die vorhanden sind
			$this->damLoaded = t3lib_extMgm::isLoaded('dam');
			
			$tmpImage = '';
			$tmpPath = '';
			
			//nicht von dam verwaltete bilder + pfade
			$paths = array();
			$images = array();
			$tmpImage = $PA['row']['image'];
			$images = $this->getImageArray($tmpImage,$this->config['menu']['maxImages']);
			foreach ($images as $key => $value) {
				$paths[$key] = $this->config['rootImgPath'];
			}
			
			//von dam verwaltete bilder + pfade anhängen
			if ($this->damLoaded) {
				$result = tx_dam_db::getReferencedFiles('tt_content', $uid, '', 'tx_dam_mm_ref', 'tx_dam.uid,tx_dam.file_path,tx_dam.file_name', '', '', 'tx_dam_mm_ref.ident, tx_dam_mm_ref.sorting_foreign');
				
				foreach ($result['rows'] as $row) {
					// changed by Caspar, only add if it is an image... (no pdf, swf, etc...)
					if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],strtolower(substr($row['file_name'], strrpos($row['file_name'], '.')+1)))) {
						$tmpImage .= $row['file_name'] . ',';
						$tmpPath .= $row['file_path'] . ',';
					}
				}
				$tmpImage = trim($tmpImage, ',');
				$tmpPath = trim($tmpPath, ',');
				
				$images += $this->getImageArray($tmpImage,$this->config['menu']['maxImages']);
				$paths += $this->getImageArray($tmpPath,$this->config['menu']['maxImages']);
			}
			
			//Einträge von Bildern, die nicht mehr vorhanden sind, entfernen
			$saveConfig = $this->getImageValues($uid);
			$saveConfig = $this->cleanConfig($saveConfig, $images, $paths);
			$this->saveImageValues($uid, $saveConfig);
			
			//erstellt eine html-selectliste der Bilder
			$htmlForm = ($this->config['menu']['displayType']==1 ? $this->int_buildImageListForm($images) : $this->int_buildImageListForm2($images, $paths));
			
			//sucht das momentan zu bearbeitende Bild heraus und schreib den Wert in die Exemplarvariable $this->curImageID
			$this->getCurImageID();
			
			//Pfad zum Bild
			$this->config['imgPath'] = $paths[$this->curImageID] . $images[$this->curImageID];
			$imgPath = '../' . $this->config['imgPath'];
			
			//ZOOM des Dargestellten Bildes, <= 1
			$zoom = 1;
			
			if (file_exists($imgPath) && !is_dir($imgPath)) {
				// variable für Original-Pfad...
				$imgPathOrig = $imgPath;
				
				//	PER PFAD und IM Breite und Höhe ermitteln
				$imageSize = getimagesize($imgPath);
				
				// FÜR DIE GEMAPPTE PHP DATEI, BREITE UND HÖHE DER ARBEITSFLÄCHE
				list ($oriWidth, $oriHeight) = $imageSize;
				// FIXED ASPECT RATIO übergeben
				if ( !$this->config['selector']['allowCustomRatio'] && is_numeric($this->config['selector']['formatW'] ) && is_numeric($this->config['selector']['formatH'] ) )
					$fixedRatio = sprintf( '%f', $this->config['selector']['formatW'] / $this->config['selector']['formatH'] );
				else
					$fixedRatio = 'false';
				
				// maximale Anzeige-Grössen im Backend
				$dw = $this->config['adjustResolution']['maxDisplayedWidth'];
				$dh = $this->config['adjustResolution']['maxDisplayedHeight'];
				
				// Zur besseren Darstellung wird das Bild  auf die in $config['adjustRes']
				// festgelegten Groeßen heruntergerechnet, wenn es grösser ist...
				if ($this->config['adjustResolution']['enabled'] && ((!empty($dw) && $dw < $oriWidth) || (!empty($dh) && $dh < $oriHeight))) {
					$extInfo = pathinfo($imgPath);
					$extInfo = $extInfo['extension'];
					
					require_once('../typo3/sysext/cms/tslib/class.tslib_gifbuilder.php');
					$imageBuild = t3lib_div::makeInstance('tslib_gifBuilder');
					$imageBuild->init();
					$imageBuild->absPrefix = '../';
					$imgNew = $imageBuild->imageMagickConvert($imgPath, $extInfo, $dw.'m', $dh.'m');
					
					$imgPath = $imgNew[3];
					
					$tmp = getimagesize($imgPath);
					$zoom = $tmp[0] / $oriWidth;
					unset($tmp);
				}
				
				//	AUS DER DB WERTE ERMITTELN
				$values = $this->getImageValues($uid);
				$values = isset($values['files'][$this->config['imgPath']]) ? $values['files'][$this->config['imgPath']] : false;
				
				if (is_array($values) && !(empty($values[0]) && empty($values[1])))
				{
					$this->doAction('saveImageValues',$uid,$this->config['imgPath'],$values[2],$values[3],$values[0],$values[1]);
					$values = $this->getImageValues($uid);
					$values = isset($values['files'][$this->config['imgPath']]) ? $values['files'][$this->config['imgPath']] : false;
				}
				
				//$log .= '<div class="neben"><pre>dbValuesPre:<br />'.print_r($values,true).'</pre><div class="clleft">&nbsp;</div></div>';
				
				list ($selectorWidth, $selectorHeight, $selectorOffX, $selectorOffY, $lWidth, $lHeight, $adjust) = $values;
				
				//VALUES ist leer, oder $adjust = 1
				if ($values==false || $adjust==1)
				{
					$localWidth = $PA['row']['imagewidth'];
					$localHeight = $PA['row']['imageheight'];
					if ($adjust==1 && !empty($localWidth) && !empty($localHeight))
							$selectorSize = Array($localWidth,$localHeight);
							
					else if (is_numeric($this->config['selector']['formatW']) && is_numeric($this->config['selector']['formatH']))
							$selectorSize = Array($this->config['selector']['formatW'],$this->config['selector']['formatH']);
							
					else	$selectorSize = $imageSize;
					
					//	vorgegebenes Format auf das Image projezieren
					list($selectorWidth,$selectorHeight,$selectorOffX,$selectorOffY) = $this->scaleToFullWH($imageSize, $selectorSize);
				}
				
				$log .= '<div class="neben"><pre>compossed Config:<br />'.print_r($this->config,true).'</pre><div class="clleft">&nbsp;</div></div>';
				$log .= '<div class="neben">Zoom: '.$zoom.'<pre>dbValues: '.print_r($values,true).'</pre></div>';
				$log .= '<div class="neben"><pre>imageSize'.print_r($imageSize,true).'</pre></div>';
				$log .= '<div class="neben"><pre>selectorSize: '.print_r($selectorSize,true).'</pre></div>';
				$log .= '<div class="neben"><pre>scaleToFullWH: '.print_r($this->scaleToFullWH($imageSize, $selectorSize),true).'</pre></div>';
				
				//Gemappte php Datei einlesen
				include('res/image-crop/image-crop_jan_mapped.php');
				$content .= '
				crop_script_alwaysPreserveAspectRatio = '.(($this->config['selector']['allowCustomRatio'])?"false":"true").';
					//crop_script_fixedRatio = true;
					var imgname = "'.$imgPathOrig.'";
					pageuid = '.$uid.';
					minWidth = '.($this->config['selector']['minWidth']*$zoom).';
					minHeight = '.($this->config['selector']['minHeight']*$zoom).';
					var zoom = '.(1/$zoom).';
					init_imageCrop();
				</script>
				<link rel="stylesheet" href="../'.$extPath.'css/be.css">
				';
			}
			else {
				$content .= 'Keine Bilder vorhanden.';
			}
			
			return '<div class="imageedit">'.
				($this->config['debug'] ? '<div class="debug clearfix">'.$log.'</div>' : '').
				'<div class="content">'.(count($images) > 1 ? '<div class="selectorField">'.$htmlForm.'</div>' : '').$content.'</div>'.
				'</div>';
		}
		
		/******
		  *	BACKEND INTERFACE
		  *	INTERNE NAVIGATION (Multiimage)
		  ***/
		  
		/*	@param	imageArray	Array		das ImageArray das zuvor mit getImageArray() erstellt wurde
		  *	@return				String
		  *	Gibt einen String mit dem Controlpanel für die Bilderauswahl zurück.
		  */
		function int_buildImageListForm($imageArray)
		{
			$html = '<select name="ie_chooseImage" size="1" onChange="document.getElementsByName(\'editform\')[0].submit();">';
			for($i=0; $i<count($imageArray); $i++)
			{
				$html .= '<option value="'.$i.'" '.($this->curImageID==$i?'selected="selected"':'').'>'.$imageArray[$i].'</option>';
			}
			$html .= '</select>';
			return $html;
		}
		
		/*	@param	imageArray	Array		das ImageArray das zuvor mit getImageArray() erstellt wurde
		  *	@param	pathArray		Array		das PathArray das zuvor mit getImageArray() erstellt wurde
		  *	@return				String
		  *	Gibt einen String mit dem Controlpanel für die Bilderauswahl zurück.
		  */
		function int_buildImageListForm2($imageArray, $pathArray)
		{
			$html = '<input name="ie_chooseImage" type="hidden" value="'.$this->curImageID.'" />';
			
			for($i=0; $i<count($imageArray); $i++)
			{
				$imgPath = '../' . $pathArray[$i] . $imageArray[$i];
				$image = t3lib_BEfunc::getThumbNail('thumbs.php', $imgPath,'', $this->config['menu']['showThumbnail_size']);
				$size = getimagesize($imgPath);
				
				$btn = 	'<button name="ie_chooseImage" class="'.($this->curImageID==$i?'active':'').'" value="'.$i.'" onChange="document.getElementsByName(\'editform\')[0].submit();">'.
							//($nameConf==0 && $imageConf==0?$this->LANG->getLL('selectorField.label').' '.($i+1):'').
							($this->config['menu']['showThumbnail']==1?'<div class="thumbnail">'.$image.'</div>':'').
							($this->config['menu']['showImageName']==1?'<div class="thumbnail_text">'.$imageArray[$i].'</div>':'').
							($this->config['menu']['showResolution']==1?'<div class="size">'.$size[0].'x'.$size[1].'</div>':'').
						'</button><br />';
				$html .= $btn;
			}
			
			return '<br />'.$html;
		}
		
		/*
		  * Diese Funktion initialisiert die Exemplarvariable 'curImageID'
		  */
		function getCurImageID()
		{
			$tmp = t3lib_div::_GP('ie_chooseImage');
			$this->curImageID = (!empty($tmp) && is_numeric($tmp)) ? $tmp : 0;
		}
		
		/*********
		 *	IMAGEFORMAT BESTIMMEN
		 ******/
		
		/*	@param	imageSize  	array( w, h )
		  *	@param	selectorSize  	array( w, h )
		  *	@return				array
		  *	@access	public
		  *	Gibt die volle Breite Bzw. Höhe, OffsetX und OffsetY für den Selector auf dem Image aus
		  */
		function scaleToFullWH($imageSize, $selectorSize)
		{
			$imageFormat = $this->getImageFormat($imageSize, $selectorSize);
			$zustand = $imageFormat['zustand'];
			
			list ($oriWidth, $oriHeight) = $imageSize;
			$selectorWidth;
			$selectorHeight;
			$selectorOffX = 0;
			$selectorOffY = 0;
			
			if ($zustand==1 || $zustand==5 || $zustand==3)
			{
				$selectorHeight = $oriHeight;
				$selectorWidth = round($selectorHeight*$imageFormat['frontProp']);
				$selectorOffX = round($oriWidth/2)-round($selectorWidth/2);
			}
			else if ($zustand==2 || $zustand==4 ||  $zustand==6)
			{
				$selectorWidth = $oriWidth;
				$selectorHeight = round($selectorWidth*(1/$imageFormat['frontProp']));
				$selectorOffY = round($oriHeight/2)-round($selectorHeight/2);
			}
			else if ($zustand == 0)
			{
				$selectorWidth = $oriWidth;
				$selectorHeight = $oriHeight;
			}
			
			return Array($selectorWidth,$selectorHeight,$selectorOffX,$selectorOffY);
		}
		
		/*	@param	backProp 	integer	Input
		  *	@return			integer
		  *	Gibt je nachdem ob $backProp > < = 1 ist 
		  *	einen Zustand von 0 - 2 aus
		  */
		function checkProp($backProp)
		{
			$backPropZustand;
			if ($backProp == 1) // FORMAT 1:1
				$backPropZustand = 0;
			elseif ($backProp > 1)	// BREITBILD
				$backPropZustand = 1;
			elseif ($backProp < 1) // HOCHKANTBILD
				$backPropZustand = 2;
			
			return $backPropZustand;
		}
		
		/*	@param	back		array(w,h) 		Ausmaße der 1. Ebene
		  *	@param	front	array(w,h)  		Außmaße der 2. Ebene
		  *	@return			integer		Zustand
		  *	Ein Image ($back) wird mit einem Selector -Layer ($front) überdeckt.
		  *	Wertet die gegeben Formate aus und ordnet ihnen einen bestimmten Zustand zu.
		  *
		  *	Ausgabe: Ein Array, das die Proportion des Selectors und des Images beinhaltet, sowie den Zustand .
		  */
		function getImageFormat($back,$front)
		{
			if ($back[1]==0 || $front[1]==0)
				return false;
		
			//echo $back[0].'x'.$back[1].','.$front[0].'x'.$front[1].'<br />';
			
			$backProp = $back[0] / $back[1];
			$frontProp = $front[0] / $front[1];
			
			$bpz = $this->checkProp($backProp);
			$fpz = $this->checkProp($frontProp);
			
			(integer) $zustand;
			
			//BACK, FRONT selbes FORMAT
					if ($backProp==$frontProp)	$zustand = 0;
			// BACK ist BREIT, FRONT IST HOCH
			else	if ($bpz==1 && $fpz==2) 	$zustand = 1;
			// BACK ist HOCHKANT, FRONT ist BREIT
			else 	if ($bpz==2 && $fpz==1) 	$zustand = 4;
			// BACK, FRONT sind BREIT: WER IST BREITER?
			else 	if ($bpz==1 && $fpz==1)
			{
				if ($backProp < $frontProp)		$zustand = 2;
				else 							$zustand = 1;
			}
			// BACK, FRONT sind HOCHKANT: WER IST HOCHKANTIGER?
			else 	if ($bpz==2 && $fpz==2)
			{
				if ($backProp > $frontProp)		$zustand = 3;
				else 							$zustand = 4;
			}
			//BACK ist BREITBILD, FRONT 1:1
			else 	if ($bpz==1 && $fpz==0)		$zustand = 5;
			//BACK ist HOCHKANT, FRONT 1:1
			else 	if ($bpz==2 && $fpz==0)		$zustand = 6;
			//BACK 1:1
			else 	if ($bpz==0 && $fpz==1)		$zustand = 2;
			else 	if ($bpz==0 && $fpz==2)		$zustand = 3;
			else	return false;
			return Array(
						$backProp, 				$frontProp, 			$zustand,
						'backProp'=>$backProp, 'frontProp'=>$frontProp, 'zustand'=>$zustand
						);
		}
		
		/*********
		 *	CONFIG FUNCTIONS
		 ******/
		/*	@param	ext		string		Name der Extension ( Bsp: meine_extension)
		  *	@return			Array		gibt das im TCA befindliche Konfigurationsarray zurück
		  */
		function getTCA($ext = 'default')
		{
			t3lib_div::loadTCA('tt_content');
			if (isset($GLOBALS['TCA']['tt_content']['imageedit'][$ext]))
					$result = $GLOBALS['TCA']['tt_content']['imageedit'][$ext];
			else	$result = $GLOBALS['TCA']['tt_content']['imageedit']['default'];
			
			return $result;
		}
		
		/*	@param	arr		Array
		  *	@param	arrOR	Array
		  *	@return			Array
		  *	Ausgabe ist ein Array, das alle Indizes die bei <arr> nicht angegeben 
		  *	sind von arrOR übernimmt.
		  */
		function comparedOR ($arr,$arrOR)
		{
			foreach ($arrOR as $key => $value)
			{
				if (isset($arr[$key]) && is_array($arr[$key]) && is_array($arrOR[$key]))
				{
					$arr[$key] = $this->comparedOR($arr[$key],$arrOR[$key]);
				}
				elseif (!isset($arr[$key]))
				{
					$arr[$key] = $arrOR[$key];
				}
			}
			return $arr;
		}
		
		/*	Gleicht die im Objekt befindliche Konfiguration mit dem defaultTCA ab
		  *	und speichert das Ergebnis in der Object->config.
		  */
		function inheritFromDefaultTCA()
		{
			$this->config = $this->comparedOR($this->config,$this->getTCA());
		}
		
		/*	@param	imageField	Array
		  *	@param	sliceArray	Integer		Anzahl der Images die höchstens bearbeitet werden können
		  *	@return				Array		Array mit validen Imagenamen
		  *	Gibt ein Array zurück das alle gültigen Imagename Bzw. Images beinhaltet
		  */
		function getImageArray($imageField, $sliceArray = 20)
		{
			$images = explode(",", $imageField);
			if ((count($images) == 1) && ($images[0] == '')) {
				$return = array();
			}
			else {
				foreach ($images as $key => $image) {
					$only = explode("|",$image);
					$images[$key] = $only[0];
				}
				if (!is_numeric($sliceArray)) $sliceArray = 20;
				
				$return = array_slice($images,0,$sliceArray);
			}
			return $return;
		}
		
		//Entfernt Einträge für Bilder, die nicht mehr existieren
		function cleanConfig($config, $images, $paths)
		{
			$pathsImagesMD5 = array();
			$pathsImages = array();
			foreach ($images as $key => $value) {
				$extInfo = pathinfo($images[$key]);
				$extInfo = $extInfo['extension'];
				$pathsImagesMD5[$key] = $paths[$key].t3lib_div::shortMD5($images[$key],10).'.'.$extInfo;
				$pathsImages[$key] = $paths[$key].$images[$key];
			}
			if (is_array($config['files'])) {
				foreach ($config['files'] as $key => $value) {
					if (!in_array($key, $pathsImages) && !in_array($key, $pathsImagesMD5)) {
						unset($config['files'][$key]);
					}
				}
			}
			return $config;
		}
		
		
		/*********
		 *	FUNKTIONEN FÜR KOMMUNIKATION NACH AUSSEN
		 *	(auch von innerhalb des Objects aufrufbar)
		 ******/
		
		//	Eine Funktion die GET[action] ausliest sich mit der Datenbank verbindet  
		function doAction($action, $uid,  $imgname, $oX='', $oY='', $sw='', $sh='')
		{
			if (strlen($action))
			{
				if (!isset($GLOBALS['TYPO3_DB'])) $this->connectDB();
				
				if ($action == 'saveImageValues')
				{
					$oldImageValues = $this->getImageValues($uid);
					$oldImageValues = $oldImageValues['files'][$imgname];
					list($lw, $lh) = $this->getWH($uid);
					
					$adjust = (!empty($lw) && !empty($lh) && (($lw != $oldImageValues['localWidth']) || ($lh != $oldImageValues['localHeight']))) ? 1 : 0;
					
					$selectorProp = ($sh>0) ? $sw/$sh : '';
					
					$saveArray = Array(
							$sw,$sh,$oX,$oY,$lw,$lh,$adjust, $selectorProp,$imgname,
							'selectorWidth'=>$sw,
							'selectorHeight'=>$sh,
							'offsetX'=>$oX,
							'offsetY'=>$oY,
							'localWidth'=>$lw,
							'localHeight'=>$lh,
							'adjust'=>$adjust,
							'selectorProp' => $selectorProp,
							'name' => $imgname
							);
					
					$inDB = $this->getImageValues($uid);
					$inDB['files'][$imgname] = $saveArray;
					
					return $this->saveImageValues($uid,$inDB);
				}
			}
		}
		
		function connectDB()
		{
			$path = defined(PATH_typo3conf) ? PATH_typo3conf : '../../../typo3conf/';
			
			require_once($path.'localconf_db.php');
			mysql_connect ( $typo_db_host, $typo_db_username, $typo_db_password ) or die ( 'SERVER CONNECT ERROR' );
			mysql_select_db ( $typo_db ) or die ('DATABASE SELECT ERROR');
		}
		
		function getWH($uid)
		{
			if (!isset($GLOBALS['TYPO3_DB'])) {
				$row = @mysql_fetch_array(mysql_query('SELECT imagewidth, imageheight FROM tt_content WHERE uid = '.$uid));
			}
			else {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('imagewidth, imageheight', 'tt_content', 'uid = '.$uid);
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			}
			
			return Array($row['imagewidth'],$row['imageheight']);
		}
		
		function saveImageValues($uid,$array)
		{
			$serial = serialize($array);
			if (!isset($GLOBALS['TYPO3_DB'])) {
				mysql_query('UPDATE tt_content SET tx_goimageeditbe_croped_image = "'.addcslashes($serial,':{;"}').'" WHERE uid = '.$uid);
			}
			else {
				$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tt_content', 'uid = '.$uid, array('tx_goimageeditbe_croped_image' => $serial));
			}
			
			return;
		}
		
		function getImageValues($uid)
		{
			if (!isset($GLOBALS['TYPO3_DB'])) {
				$row = @mysql_fetch_array(mysql_query('SELECT tx_goimageeditbe_croped_image FROM tt_content WHERE uid = '.$uid));
			}
			else {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_goimageeditbe_croped_image', 'tt_content', 'uid = '.$uid);
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			}
			
			if (strlen($row['tx_goimageeditbe_croped_image'])>0) {
				$arr = unserialize($row['tx_goimageeditbe_croped_image']);
				if (!is_array($arr)) $arr = Array();
			}
			else $arr = Array();
			
			return $arr;
		}
		
		function writeLog($logText) {
			$log = fopen('./temp.log', 'a');
			fwrite($log, $logText."\n");
			fclose($log);
		}
	}
	
	if (defined ('TYPO3_MODE') && $TYPO3_MODE == 'BE' && isset($_GET['action']) && !empty($_GET['action'])) {
		$myImageEdit = new tx_imageedit;
		$myImageEdit->doAction($_GET['action'],$_GET['uid'],str_replace('../', '', @$_GET['imgname']),@$_GET['oX'],@$_GET['oY'],@$_GET['w'],@$_GET['h']);
	}
?>