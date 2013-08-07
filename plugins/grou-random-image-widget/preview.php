<?php
/*
 Code part of "Grou Random Image Widget" plugin for wordpress
 URL: http://wordpress.org/extend/plugins/grou-random-image-widget/
 or http://grou29.free.fr
 */
function error404($txt, $width, $height )
{
	die();
	$image_width = min(640, intval($width)) ;
	$image_height = min(480,intval($height));
	$image  = imagecreatetruecolor($image_width, $image_height);

	#Horizontal gradient
	for($i=0; $i<$image_height; $i++)
	{
		$color = floor($i * 255 / $image_height);
		$color = ImageColorAllocate($image, $color, $color, $color);
		imageline($image, 0, $image_height-$i-1, $image_width, $image_height-$i-1, $color);
	}
	$couleur = imagecolorallocate($image,0,0,0);
	$st = "Error loading "+$txt;
	imagestring($image, 2, 3, 10,"Error loading " , $couleur); //on écrit horizontalement
	imagestring($image, 2, 3	, 20, $txt , $couleur); //on écrit horizontalement

	# display the image and free memory
	Header("Content-type: image/jpeg");
	imagejpeg($image);
	imagedestroy($image);
	die();
}
function imagerectanglernd ($source1, $x,$y, $w, $h, $color,$first)
{
	imageline($source1, $x+2, $y, $x+$w-2, $y, $color);
	imageline($source1, $x+2, $y+$h, $x+$w-2, $y+$h, $color);
	imageline($source1, $x, $y+2, $x, $y+$h-2,$color);
	imageline($source1, $x+$w, $y+2, $x+$w, $y+$h-2,$color);


	imagesetpixel($source1, $x+1, $y+1,$color);
	imagesetpixel($source1, $x+$w-1, $y+1,$color);
	imagesetpixel($source1, $x+$w-1, $y+$h-1,$color);
	imagesetpixel($source1, $x+1, $y+$h-1,$color);

	if ($first != true)
	{
		imagesetpixel($source1, $x+1, $y+2,$color);
		imagesetpixel($source1, $x+2, $y+1,$color);

		imagesetpixel($source1, $x+$w-1, $y+2,$color);
		imagesetpixel($source1, $x+$w-2, $y+1,$color);

		imagesetpixel($source1, $x+$w-1, $y+$h-2,$color);
		imagesetpixel($source1, $x+$w-2, $y+$h-2,$color);

		imagesetpixel($source1, $x+1, $y+$h-2,$color);
		imagesetpixel($source1, $x+2, $y+$h-1,$color);

	}
}
// picture frame & size
$line=0;

foreach($_GET as $variable => $value) {
	$after[str_replace('amp;','',$variable)] = $value;
}


$foto = $after['f'];
// image name
$fz = $after['file'];
$f2= getenv  ("DOCUMENT_ROOT") .$fz;
// size
$dx = min($after['w'],640);
$dy = min($after['h'],800);
$rotate =0;

if ($foto=="")
{
	$foto=0;
} else {

	$rotate = $after['r'];
	//	rotation ?
	if(isset($rotate)==FALSE)
	{
		//random rotation
		mt_srand((double)microtime()*1000000); // seed for PHP < 4.2
		$rotate  = mt_rand(0,40)-20;
	}
}
/*------------------------------------------*/


if (($foto==0) || !(extension_loaded('gd') && function_exists('gd_info')))
{

	//header("Location: ".$_SERVER .$f2);
	header("Location: http://".	$_SERVER['HTTP_HOST'].$fz);

	exit();
} else {
	$border =0;
	$bordxg=0;
	$bordxd=0;
	$bordyg=0;
	$bordyd=0;
	/* size of frame offset foto.png*/
	switch ($foto)
	{
		case "1":
			$filename ="img/foto.png";
			$bordxg=10;
			$bordxd=17	;
			$bordyg=10;
			$bordyd=20;
			/*	$border =2;*/
			break;
		case "2":
			$filename ="img/foto2.png";
			$bordxg=43;
			$bordxd=344-299;
			$bordyg=39;
			$bordyd=277-229;
			break;
		case "3":
			$filename ="img/foto3.png";
			$bordxg=25;
			$bordxd=27;
			$bordyg=25;
			$bordyd=27;
			break;
		case "4":
			$filename ="img/foto4.png";
			$bordxg=25;
			$bordxd=33;
			$bordyg=29;
			$bordyd=32;
			$line=1;
			break;
		case "5":
			/* set to null=> will be pixel drawn */
			$filename = null;
			$bordxg=15;
			$bordxd=15;
			$bordyg=15;
			$bordyd=15;
			$line=1;
			break;
		case "6":
			$filename ="img/foto6.png";
			$bordxg=17;
			$bordxd=16;
			$bordyg=16;
			$bordyd=16;

			break;
		case "7":
			$filename ="img/foto7.png";
			$bordxg=22;
			$bordxd=22;
			$bordyg=20;
			$bordyd=18;

			break;
		case "8":
			$filename ="img/foto8.png";
			$bordxg=26;
			$bordxd=24;
			$bordyg=30;
			$bordyd=30;
			$line=1;
			break;
		case "9":
			$filename ="img/foto9.png";
			$bordxg=53;
			$bordxd=58;
			$bordyg=55;
			$bordyd=55;
			$line=1;
			break;
	}
}
/*----------------------------------*/
// load 2

$image = @getimagesize($f2);
if ($image==FALSE)
{
	error404($fz,$dx,$dy);
}
$image_type = $image['2'];

// create from type
if($image_type == "1") $source2 = @imagecreatefromgif($f2);
if($image_type == "2") $source2 = @imagecreatefromjpeg($f2);
if($image_type == "3") $source2 = @imagecreatefrompng($f2);
if($image_type == "6") $source2 = @imagecreatefromwbmp($f2);

if ($source2==FALSE)
{
	error404($fz,$dx,$dy);
}
imagealphablending($source2, true); // setting alpha blending on
imagesavealpha($source2, true); // save alphablending setting (important)
imageantialias  ( $source2  , true  );
$x2=imageSX($source2);
$y2=imageSY($source2);
// image loaded

// real size with frame
$ox = $x2 +   $bordxg + $bordxd+2*$border;
$oy = $y2 +   $bordyg + $bordyd+2*$border;
//----------------------
$w = $ox;
$h = $oy;

/* compute size after rotate */

$alpha =atan2  ( $h/2  , $w/2  );
$d=  $w/2 / cos($alpha);

$xx1 = $d *cos ($alpha + $rotate*M_PI/180);
$xx2 = $d *cos (-$alpha + $rotate*M_PI/180);
$yy1 = $d *sin ($alpha + $rotate*M_PI/180);
$yy2 = $d *sin (-$alpha + $rotate*M_PI/180);

// new size
$ox=  (max(abs($xx1),abs($xx2))*2);
$oy=  (max(abs($yy1),abs($yy2))*2);

//----------------------
// compute factor to scale down to good size on x et y
$c1= ($dx) / $ox;
$c2= ($dy) / $oy;

// biggest scale on ?
if ($c1<$c2)
{
	// x scale
	$coef = $c1;
} else {
	//y scale
	$coef = $c2;
}
// compute final size based on source size (not rotate one)
$w = $w*$coef;
$h = $h*$coef;

// create final image
$dstimage=imagecreatetruecolor($w,$h);
// setting alpha blending on
imagealphablending($dstimage, true);
// save alphablending setting (important)
imagesavealpha($dstimage, true);
imageantialias  ( $dstimage  , true  );

/*------------------------------------------*/
if ($foto==0)
{
	//copy frame picture with scale
	imagecopyresampled($dstimage,$source2,0,0,0,0, $w,$h,$x2,$y2);
	header('Content-type: image/png');
	header("Cache-Control: no-cache");
	header("Expires: -1");

	imagepng($dstimage);
} else {
	$border = $border+$after['fr'];
	$bg = $after['bg'];

	// convert color from string RRGGBB to r, g, b

	$r = hexdec(substr($bg, 0, 2));
	$g = hexdec(substr($bg, 2, 2));
	$b = hexdec(substr($bg, 4, 2));

	// alpha code
	$alpha =127;

	if ($rotate != 0)
	{
		// bug on rotate gd function. trnaparanct not working wil older version
		if (((GD_MAJOR_VERSION== 2) &&(GD_RELEASE_VERSION<= 34)) |
		((GD_MAJOR_VERSION <2)))
		{
			$alpha =0;
		}
	}
	/*$alpha =0;*/
	if ($filename == null)
	{
		$iw = $w;
		$ih = $h;

		$source1  = imagecreatetruecolor($iw, $ih);
		imagealphablending($source1, true); // setting alpha blending on
		imagesavealpha($source1, true); // save alphablending setting (important)
		//imageantialias  ( $source1  , true  );

		$trans = imagecolorallocatealpha($source1,0,0,0,127);
		// 	fill with transparent
		imagefill($source1, 0,0, $trans);
		$grey= imagecolorallocatealpha($source1,240,240,240,100);
		imagefilledrectangle($source1,5,5,
		$iw-10,$ih-10,
		$grey);

		$lev=180;
		// grey gradient for contour
		$grey= imagecolorallocatealpha($source1,$lev,$lev,$lev,0);
		imagerectanglernd ($source1,
		5,5,
		$iw-10,$ih-10,
		$grey, true);
		$grey= imagecolorallocatealpha($source1,$lev,$lev,$lev,90);
		imagerectanglernd ($source1,
		4,4,
		$iw-8,$ih-8,
		$grey, false);
		$grey= imagecolorallocatealpha($source1,$lev,$lev,$lev,110);
		imagerectanglernd ($source1,
		3,3,
		$iw-6,$ih-6	,
		$grey, false);
	} else {
		// load 1
		$source1 = imagecreatefrompng($filename);
		imagealphablending($source1, true); // setting alpha blending on
		imagesavealpha($source1, true); // save alphablending setting (important)
		imageantialias  ( $source1  , true  );
	}

	// get picture size
	$x1=imageSX($source1);
	$y1=imageSY($source1);
	// look for best scale
	// watchout, dx, dy are final picture size.
	// rotate can change that
	// conpute x et y scale for frame picture
	$coef2x = $w/$x1;
	$coef2y = $h/$y1;

	// allocate transparent color
	$trans = imagecolorallocatealpha($dstimage,$r,$g,$b,$alpha);
	// fill with transparent
	imagefill($dstimage, 0,0, $trans);

	//copy frame picture with scale
	imagecopyresampled($dstimage,$source1,0,0,0,0, $w,$h,$x1,$y1);
	//copy center image scale
	imagecopyresampled(	$dstimage, $source2,
	$bordxg*$coef2x + $border, $bordyg*$coef2y+$border, 0,0,
	$w-($bordxg+$bordxd)*$coef2x-2*$border, $h-($bordyg+$bordyd)*$coef2y-2*$border,
	$x2, $y2);

	imagedestroy($source1);
	imagedestroy($source2);
	// black rectangle (nicer)
	if ($line==1)
	{	
		$fond_noir = imagecolorallocatealpha($dstimage,0,0,0,0);
		$black = imagecolorallocatealpha($dstimage,0,0,0,100);
		imagerectangle ($dstimage,
		$bordxg*$coef2x+$border,$bordyg*$coef2y+$border,
		$w-($bordxd)*$coef2x-$border-1, $h-($bordyd+1)*$coef2y-$border,
		$black);
	}
	// rotation
	if ($rotate != 0)
	{
		$dstimage = imagerotate($dstimage,$rotate,$trans);

		imagealphablending($dstimage, true); // setting alpha blending on
		imagesavealpha($dstimage, true); // save alphablending setting (important)
		imageantialias  ( $dstimage  , true  );
	}
	// Output
	header('Content-type: image/png');
	header("Cache-Control: no-cache");
	header("Expires:" .gmdate('D, d M Y H:i:s', time()+ 5000).'GMT');  

	imagepng($dstimage);
	
	imagedestroy($dstimage);
}
?>