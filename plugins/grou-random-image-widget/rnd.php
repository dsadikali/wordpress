<?php
/*
 Code part of "Grou Random Image Widget" plugin for wordpress
 URL: http://wordpress.org/extend/plugins/grou-random-image-widget/
  or http://grou29.free.fr
*/ 

/* Random fonction */
header("Cache-Control: no-cache");
header("Expires: -1");

$folder = $_GET['path'];
$folderFull = getenv  ("DOCUMENT_ROOT").$_GET['path'];

//
//check folder exist
if (!is_dir($folderFull))
	return FALSE;

// list of extensions
$exts = 'jpg jpeg png gif';
$extList = explode(' ', $exts);

$fileh = opendir($folderFull);

$files = array();
$i = 0; // Initialize some variables

while (false !== ($file = readdir($fileh))) 
{
	foreach ($extList as $ext) 
	{ // for each extension check the extension
		if (preg_match('/\.'.$ext.'$/i', $file, $test)) 
		{ // faster than ereg, case insensitive
			$files[] = $file; // it's good
			++$i;
		}
	}
}
closedir($fileh); // We're not using it anymore

if ($i>0)
{
	mt_srand((double)microtime()*1000000); // seed for PHP < 4.2
	if(isset($_GET['num']))
	{
		$rand = $_GET['num'];
		if ($rand >= $i)
		{
			$rand = 0;
		}
		if ($rand < 0)
		{
			$rand = $i-1;
		}
	} else {
		$rand = mt_rand(0, $i-1); // $i was incremented as we went along
	}
	//perform scaling

	$img_info = @getimagesize($folderFull."/".$files[$rand]);  //image width is in element 0 and height is element 1
	$img_width = $img_info[0];
	$img_height = $img_info[1];

	/** try to open asociated test file */
	$myFile = $folderFull."/".$files[$rand].".txt";
	$theData="";
	if (file_exists($myFile)==true)
	{
		$fh = fopen($myFile, 'r');
		
		if ($fh != null)
		{
			$theData = fgets($fh);
			fclose($fh);
		}
	}
	echo "OK|".$folder."/".$files[$rand]."|".$img_width ."|".$img_height."|".$rand."|".$theData;
} else {
	echo "KO";
}
?>