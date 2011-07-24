<?php
$content .= '

	<link rel="stylesheet" href="'.$imgCrop.'css/xp-info-pane.css">
	<link rel="stylesheet" href="'.$imgCrop.'css/image-crop.css">

	<script type="text/javascript" src="'.$imgCrop.'js/xp-info-pane.js"></script>
	<script type="text/javascript" src="'.$imgCrop.'js/ajax.js"></script>
	<script type="text/javascript">
	/************************************************************************************************************
	(C) www.dhtmlgoodies.com, April 2006
	
	This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	
	
	Terms of use:
	You are free to use this script as long as the copyright message is kept intact. However, you may not
	redistribute, sell or repost it without our permission.
	
	Thank you!
	
	www.dhtmlgoodies.com
	Alf Magne Kalleland
	
	************************************************************************************************************/	

	
	/* Variables you could modify */
	
	var crop_script_server_file = "crop_image.php";
	
	var cropToolBorderWidth = 1;	// Width of dotted border around crop rectangle
	var smallSquareWidth = 7;	// Size of small squares used to resize crop rectangle
	
	//zusätzliche Variablen für MOD
	var offsetx = 0;
	var offsety = 0;
	
	// Size of image shown in crop tool
	var crop_imageWidth = '.round($oriWidth * $zoom).';
	var crop_imageHeight = '.round($oriHeight * $zoom).';
	
	// Size of original image
	var crop_originalImageWidth = '.round($oriWidth * $zoom).';
	var crop_originalImageHeight = '.round($oriHeight * $zoom).';
	
	// by elio@gosign.de 17/09/09 - Fixed Aspect Ratio hier setzen, sonst entstehen beim skalieren Rundungsfehler
	var crop_script_fixedRatio = '.$fixedRatio.';
	
	var crop_minimumPercent = 10;	// Minimum percent - resize
	var crop_maximumPercent = 200;	// Maximum percent -resize
	
	var crop_minimumWidthHeight = 15;	// Minimum width and height of crop area
	
	var updateFormValuesAsYouDrag = false;	// This variable indicates if form values should be updated as we drag. This process could make the script work a little bit slow. That\'s why this option is set as a variable.
	if(!document.all)updateFormValuesAsYouDrag = false;	// Enable this feature only in IE
	
	/* End of variables you could modify */
	</script>
	
	<script type="text/javascript" src="'.$imgCrop.'js/image-crop.js"></script>
<div id="pageContent">
<!-- style="display:none;"-->
<div id="dhtmlgoodies_xpPane" style="display:none;">
	<div class="dhtmlgoodies_panel">
		<div>
			<!-- Start content of pane -->
			<form>
			<input type="hidden" id="input_image_ref" value="'.$imgPath.'">
			<table>
				<tr>
					<td>X:</td><td><input type="text" class="textInput" name="crop_x" id="input_crop_x" value="'.round($selectorOffX*$zoom).'"></td>
				</tr>
				<tr>
					<td>Y:</td><td><input type="text" class="textInput" name="crop_y" id="input_crop_y" value="'.round($selectorOffY*$zoom).'"></td>
				</tr>
				<tr>
					<td>Width:</td><td><input type="text" class="textInput" name="crop_width" id="input_crop_width" value="'.round($selectorWidth*$zoom).'"></td>
				</tr>
				<tr>
					<td>Height:</td><td><input type="text" class="textInput" name="crop_height" id="input_crop_height" value="'.round($selectorHeight*$zoom).'"></td>
				</tr>
				<tr>
					<td>Percent size:</td><td><input type="text" class="textInput" name="crop_percent_size" id="crop_percent_size" value="100"></td>
				</TR>					
				<tr>
					<td>Convert to:</td>
					<td>
						<select class="textInput" id="input_convert_to">
							<option value="gif">Gif</option>
							<option value="jpg" selected>Jpg</option>
							<option value="png">Png</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td id="cropButtonCell"><input type="button" onclick="cropScript_executeCrop(this)" value="Crop">

					</td>
				</tr>
			</table>
			<div id="crop_progressBar">
			
			</div>		
			</form>
			<!-- End content -->
		</div>	
	</div>
	<div class="dhtmlgoodies_panel">
		<div>
			<!-- Start content of pane -->
			<table>
				<tr>
					<td><b>Picture from Norway</b></td>
				</tr>
				<tr>
					<td>Dimension: <span id="label_dimension"></span></td>
				</tr>
			</table>
			<!-- End content -->
		</div>
	</div>
	<div class="dhtmlgoodies_panel">
		<div>
			<!-- Start content of pane -->
			
			To select crop area, drag and move the dotted rectangle or type in values directly into the form.
			
			<!-- End of content -->
		</div>		
	</div>
</div>

	<div class="crop_content">
		<div id="imageContainer">
			<img src="'.$imgPath.'">
		</div>
	</div>
</div>

<script type="text/javascript">
initDhtmlgoodies_xpPane(Array(\'Crop inspector\',\'Image details\',\'Instructions\'),Array(true,true),Array(\'pane1\',\'pane2\',\'pane3\'));
';
 ?>