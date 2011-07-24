<?

/*

This file should be used to crop an image
	Input to this file:
	$_POST['image_ref']
	$_POST['x']
	$_POST['y']
	$_POST['width']
	$_POST['height']
	$_POST['convertTo']
	$_POST['percentSize']

*/

define("IMAGE_MAGICK_PATH","/imagemagick/");

if(isset($_POST['image_ref']) && isset($_POST['x']) && isset($_POST['y']) && isset($_POST['x']) && isset($_POST['width']) && isset($_POST['convertTo'])){
		
	// Use Imagemagick(www.imagemagick.org), Image Alchemy(Alchemy)
	
	$x = escapeshellarg($_POST['x']);
	$y = escapeshellarg($_POST['y']);
	$width = escapeshellarg($_POST['width']);
	$height = escapeshellarg($_POST['height']);
	$image_ref = escapeshellarg($_POST['image_ref']);
	$percentSize = escapeshellarg($_POST['percentSize']);
	$convertTo = escapeshellarg($_POST['convertTo']);
	
	$x = preg_replace("/[^0-9]/si","",$x);
	$y = preg_replace("/[^0-9]/si","",$y);
	$width = preg_replace("/[^0-9]/si","",$width);
	$height = preg_replace("/[^0-9]/si","",$height);
	$percentSize = preg_replace("/[^0-9]/si","",$percentSize);
	
	// You need to validate some of the variables above in case someone is calling this file directly from their browser and not from the crop script
	// This is some examples:
	$image_ref = str_replace("../","",$image_ref);
	if(substr($image_ref,0,1)=="/")exit;
	if($percentSize>200)$percentSize = 200;
	
	if(strlen($x) && strlen($y) && $width && $height && $percentSize){
	
		$convertParamAdd = "";
		if($percentSize!="100"){
			$convertParamAdd = " -resize ".$percentSize."x".$percentSize."%";
			$x = $x * ($percentSize/100);	
			$y = $y * ($percentSize/100);	
			$width = $width * ($percentSize/100);	
			$height = $height * ($percentSize/100);	
		}
		
		$destinationFile = "demo-images/nature_copy.jpg";	// Name of the converted file. 
		$convertString = IMAGE_MAGICK_PATH."convert $image_ref $convertParamAdd -crop ".$width."x".$height."+".$x."+".$y." $destinationFile";
		$convertString = str_replace(";","",$convertString);
		#exec($convertString);
		echo "alert('The image you will see in the next popup is only a demo image.\\nYou have to enable ImageMagick on your site in order to crop images\\n\\nPs! The script is tested with ImageMagick locally.');";
		echo "var w = window.open('$destinationFile','imageWin','width=630,height=330,resizable=yes');";
	}else{
		echo "alert('Error!');";
	}	
}


?>

