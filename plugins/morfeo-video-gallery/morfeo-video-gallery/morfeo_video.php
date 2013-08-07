<?php
/*
	Plugin Name: Morfeo Video Gallery, wpvideoplugins.com
	Plugin URI: http://www.wpvideoplugins.com/morfeo-video-gallery/
	Description: With this plugin you can create video galleries with flv videos and/or youtube videos. USE: Install and activate the plugin. You will see a new button on your wordpress administrator, "Morfeo Video." Click here to create your videos galleries. To insert a gallery in your posts, type [morfeo_video X/], where X is the ID of the gallery. You can use Widgets with this plugin.
	Version: 0.1
	Author: wpvideoplugins.com
	Author URI: http://www.wpvideoplugins.com/
*/	
$contador=0;

$nombrebox="Webpsilon".rand(99, 99999);
function morfeo_video_head() {
	
	$site_url = get_option( 'siteurl' );

			
}
function morfeo_video($content){
	$content = preg_replace_callback("/\[morfeo_video ([^]]*)\/\]/i", "morfeo_video_render", $content);
	return $content;
	
}

function morfeo_video_render($tag_string){
$contador=rand(9, 9999999);
	$site_url = get_option( 'siteurl' );
global $wpdb; 	
$table_name = $wpdb->prefix . "morfeo_video";	


if(isset($tag_string[1])) {
	$auxi1=str_replace(" ", "", $tag_string[1]);
	$myrows = $wpdb->get_results( "SELECT * FROM $table_name WHERE id = ".$auxi1.";" );
}
if(count($myrows)<1) $myrows = $wpdb->get_results( "SELECT * FROM $table_name;" );
	$conta=0;
	$id= $myrows[$conta]->id;
	$video = $myrows[$conta]->video;
	$titles = $myrows[$conta]->titles;
	$width = $myrows[$conta]->width;
	$height = $myrows[$conta]->height;
	$tumb = $myrows[$conta]->tumb;
	$round = $myrows[$conta]->round;
	$controls = $myrows[$conta]->controls;
	$skin = $myrows[$conta]->skin;
	$columns = $myrows[$conta]->columns;
	$row= $myrows[$conta]->row;
	$color1 = $myrows[$conta]->color1;
	$color2 = $myrows[$conta]->color2;
	$autoplay = $myrows[$conta]->autoplay;

	$tags = $myrows[$conta]->tags;
	
	$texto='';
	
	

$texto='title='.$titles.'&controls='.$controls.'&color1='.$color1.'&color2='.$color2.'&round='.$round.'&autoplay='.$autoplay.'&skin='.$skin.'&youtube='.$youtube.'&columns='.$columns.'&rows='.$row.'&tumb='.$tumb.'&round='.$round;

$links = array();
$titlesa = array();
if($video!="") $links=preg_split ("/\n/", $video);
if($titles!="") $titlesa=preg_split ("/\n/", $titles);
$cont1=0;

while($cont1<count($links)) {
	$auxititle="";
	$auxivideo="";
	$auxtipo=0;
	if(isset($titlesa[$cont1])) $auxititle=$titlesa[$cont1];
	if(isset($links[$cont1])) $auxivideo=$links[$cont1];
	if($auxivideo!="") {
		$auxtipo=1;
		if(strstr($auxivideo, "http")) {
			if(strpos($auxivideo, "youtube")>0) {
				$auxivideo=getYTid($auxivideo);
				$auxtipo=2;
				
			}
			else $auxtipo=1;
		}
		else $auxtipo=2;
		

	}
	$texto.='&video'.$cont1.'='.$auxivideo.'&title'.$cont1.'='.$auxititle.'&tipo'.$cont1.'='.$auxtipo;
	$cont1++;
}
$texto.='&cantidad='.$cont1;
	
	

	
	$table_name = $wpdb->prefix . "morfeo_video";
	$saludo= $wpdb->get_var("SELECT id FROM $table_name ORDER BY RAND() LIMIT 0, 1; " );
	$output='
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$width.'" height="'.$height.'" id="Morfeo'.$id.'-'.$contador.'" title="'.$tags.'">
  <param name="movie" value="'.$site_url.'/wp-content/plugins/morfeo-video-gallery/morfeo_video.swf" />
  <param name="quality" value="high" />
  <param name="wmode" value="transparent" />
  	<param name="flashvars" value="'.$texto.'" />
	   <param name="allowFullScreen" value="true" />
  <param name="swfversion" value="9.0.45.0" />
  <!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don’t want users to see the prompt. -->
  <param name="expressinstall" value="'.$site_url.'/wp-content/plugins/morfeo-video-gallery/Scripts/expressInstall.swf" />
  <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
  <!--[if !IE]>-->
  <object type="application/x-shockwave-flash" data="'.$site_url.'/wp-content/plugins/morfeo-video-gallery/morfeo_video.swf" width="'.$width.'" height="'.$height.'">
    <!--<![endif]-->
    <param name="quality" value="high" />
    <param name="wmode" value="transparent" />
    	<param name="flashvars" value="'.$texto.'" />
		   <param name="allowFullScreen" value="true" />
    <param name="swfversion" value="9.0.45.0" />
    <param name="expressinstall" value="'.$site_url.'/wp-content/plugins/morfeo-video-gallery/Scripts/expressInstall.swf" />
    <!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
    <div>
      <h4>Content on this page requires a newer version of Adobe Flash Player.</h4>
      <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" width="112" height="33" /></a></p>
    </div>
    <!--[if !IE]>-->
  </object>
  <!--<![endif]-->
</object>
<script type="text/javascript">
<!--
swfobject.registerObject("Morfeo'.$id.'-'.$contador.'");
//-->
</script><br/>'.$ligtext;
	return $output;
}


function getYTid($ytURL) {
#
 
#
$ytvIDlen = 11; // This is the length of YouTube's video IDs
#
 
#
// The ID string starts after "v=", which is usually right after
#
// "youtube.com/watch?" in the URL
#
$idStarts = strpos($ytURL, "?v=");
#
 
#
// In case the "v=" is NOT right after the "?" (not likely, but I like to keep my
#
// bases covered), it will be after an "&":
#
if($idStarts === FALSE)
#
$idStarts = strpos($ytURL, "&v=");
#
// If still FALSE, URL doesn't have a vid ID
#
if($idStarts === FALSE)
#
die("YouTube video ID not found. Please double-check your URL.");
#
 
#
// Offset the start location to match the beginning of the ID string
#
$idStarts +=3;
#
 
#
// Get the ID string and return it
#
$ytvID = substr($ytURL, $idStarts, $ytvIDlen);
#
 
#
return $ytvID;
#
 
#
}


function morfeo_video_instala(){
	global $wpdb; 
	$table_name= $wpdb->prefix . "morfeo_video";
   $sql = " CREATE TABLE $table_name(
		id mediumint( 9 ) NOT NULL AUTO_INCREMENT ,
		video longtext NOT NULL ,
		titles longtext NOT NULL ,
		width tinytext NOT NULL ,
		height tinytext NOT NULL ,
		tumb tinytext NOT NULL ,
		round tinytext NOT NULL ,
		controls tinytext NOT NULL ,
		skin tinytext NOT NULL ,
		columns tinytext NOT NULL ,
		row tinytext NOT NULL ,
		color1 tinytext NOT NULL ,
		color2 tinytext NOT NULL ,
		autoplay tinytext NOT NULL ,
		tags tinytext NOT NULL ,
		PRIMARY KEY ( `id` )	
	) ;";

   	$id= $myrows[$conta]->id;
	$video = $myrows[$conta]->video;
	$titles = $myrows[$conta]->titles;
	$width = $myrows[$conta]->width;
	$height = $myrows[$conta]->height;
	$tumb = $myrows[$conta]->tumb;
	$round = $myrows[$conta]->round;
	$controls = $myrows[$conta]->controls;
	$skin = $myrows[$conta]->skin;
	$columns = $myrows[$conta]->columns;
	$row= $myrows[$conta]->row;
	$color1 = $myrows[$conta]->color1;
	$color2 = $myrows[$conta]->color2;
	$autoplay = $myrows[$conta]->autoplay;
   	$tags = $myrows[$conta]->tags;
   
	$wpdb->query($sql);
	$sql = "INSERT INTO $table_name (video, titles, width, height, tumb, round, controls, skin, columns, row, color1, color2, autoplay, tags) VALUES ('http://www.youtube.com/watch?v=7DwuVKfjctk\nhttp://www.youtube.com/watch?v=9W1dhqc-JBs\nhttp://www.youtube.com/watch?v=k-OOfW6wWyQ\nhttp://www.youtube.com/watch?v=niqrrmev4mA\nhttp://www.youtube.com/watch?v=7DwuVKfjctk\nhttp://www.youtube.com/watch?v=9W1dhqc-JBs\nhttp://www.youtube.com/watch?v=k-OOfW6wWyQ\nhttp://www.youtube.com/watch?v=niqrrmev4mA\nhttp://www.youtube.com/watch?v=7DwuVKfjctk\nhttp://www.youtube.com/watch?v=9W1dhqc-JBs\nhttp://www.youtube.com/watch?v=k-OOfW6wWyQ\nhttp://www.youtube.com/watch?v=niqrrmev4mA', 'Inception Trailer\nThe Last Airbender\nRANGO\nLady Gaga\nInception Trailer\nThe Last Airbender\nRANGO\nLady Gaga\nInception Trailer\nThe Last Airbender\nRANGO\nLady Gaga', '100%', '600px', '50', '14', '0',  '1', '3', '3', 'cccccc', '222222', '0', '');";
	$wpdb->query($sql);
}
function morfeo_video_desinstala(){
	global $wpdb; 
	$table_name = $wpdb->prefix . "morfeo_video";
	$sql = "DROP TABLE $table_name";
	$wpdb->query($sql);
}	
function morfeo_video_panel(){
	global $wpdb; 
	$table_name = $wpdb->prefix . "morfeo_video";	
	
	if(isset($_POST['crear'])) {
		$re = $wpdb->query("select * from $table_name");
//autos  no existe
if(empty($re))
{
  $sql = " CREATE TABLE $table_name(
		id mediumint( 9 ) NOT NULL AUTO_INCREMENT ,
		video longtext NOT NULL ,
		titles longtext NOT NULL ,
		width tinytext NOT NULL ,
		height tinytext NOT NULL ,
		tumb tinytext NOT NULL ,
		round tinytext NOT NULL ,
		controls tinytext NOT NULL ,
		skin tinytext NOT NULL ,
		columns tinytext NOT NULL ,
		row tinytext NOT NULL ,
		color1 tinytext NOT NULL ,
		color2 tinytext NOT NULL ,
		autoplay tinytext NOT NULL ,
		tags tinytext NOT NULL ,
		PRIMARY KEY ( `id` )
	) ;";
	$wpdb->query($sql);

}
		
	$sql = "INSERT INTO $table_name (video, titles, width, height, tumb, round, controls, skin, columns, row, color1, color2, autoplay, tags) VALUES ('http://www.youtube.com/watch?v=7DwuVKfjctk\nhttp://www.youtube.com/watch?v=9W1dhqc-JBs\nhttp://www.youtube.com/watch?v=k-OOfW6wWyQ\nhttp://www.youtube.com/watch?v=niqrrmev4mA\nhttp://www.youtube.com/watch?v=7DwuVKfjctk\nhttp://www.youtube.com/watch?v=9W1dhqc-JBs\nhttp://www.youtube.com/watch?v=k-OOfW6wWyQ\nhttp://www.youtube.com/watch?v=niqrrmev4mA\nhttp://www.youtube.com/watch?v=7DwuVKfjctk\nhttp://www.youtube.com/watch?v=9W1dhqc-JBs\nhttp://www.youtube.com/watch?v=k-OOfW6wWyQ\nhttp://www.youtube.com/watch?v=niqrrmev4mA', 'Inception Trailer\nThe Last Airbender\nRANGO\nLady Gaga\nInception Trailer\nThe Last Airbender\nRANGO\nLady Gaga\nInception Trailer\nThe Last Airbender\nRANGO\nLady Gaga', '100%', '600px', '50', '14', '0',  '1', '3', '3', 'cccccc', '222222', '0', '');";
	$wpdb->query($sql);
	}
	
if(isset($_POST['borrar'])) {
		$sql = "DELETE FROM $table_name WHERE id = ".$_POST['borrar'].";";
	$wpdb->query($sql);
	}
	if(isset($_POST['id'])){	


$sql= "UPDATE $table_name SET `video` = '".$_POST["video".$_POST['id']]."', `titles` = '".$_POST["titles".$_POST['id']]."', `width` = '".$_POST["width".$_POST['id']]."', `height` = '".$_POST["height".$_POST['id']]."', `tumb` = '".$_POST["tumb".$_POST['id']]."', `round` = '".$_POST["round".$_POST['id']]."', `controls` = '".$_POST["controls".$_POST['id']]."', `skin` = '".$_POST["skin".$_POST['id']]."', `columns` = '".$_POST["columns".$_POST['id']]."', `row` = '".$_POST["row".$_POST['id']]."', `color1` = '".$_POST["color1".$_POST['id']]."', `color2` = '".$_POST["color2".$_POST['id']]."', `autoplay` = '".$_POST["autoplay".$_POST['id']]."', `tags` = '".$_POST["tags".$_POST['id']]."' WHERE `id` =  ".$_POST["id"]." LIMIT 1";
			$wpdb->query($sql);
	}
	$myrows = $wpdb->get_results( "SELECT * FROM $table_name" );
$conta=0;

include('template/cabezera_panel.html');
while($conta<count($myrows)) {
	$id= $myrows[$conta]->id;
	$video = $myrows[$conta]->video;
	$titles = $myrows[$conta]->titles;
	$width = $myrows[$conta]->width;
	$height = $myrows[$conta]->height;
	$tumb = $myrows[$conta]->tumb;
	$round = $myrows[$conta]->round;
	$controls = $myrows[$conta]->controls;
	$skin = $myrows[$conta]->skin;
	$columns = $myrows[$conta]->columns;
	$row= $myrows[$conta]->row;
	$color1 = $myrows[$conta]->color1;
	$color2 = $myrows[$conta]->color2;
	$autoplay = $myrows[$conta]->autoplay;
	$tags = $myrows[$conta]->tags;
	include('template/panel.html');			
	$conta++;
	}

}


function widget_morfeo_video($args) {

 
  
    extract($args);
	
	  $options = get_option("widget_morfeo_video");
  if (!is_array( $options ))
{
$options = array(
      'title' => 'Morfeo Video',
	  'id' => '1'
      );
  }

	$aaux=array();
	$aaux[0]="Morfeo_video";
	
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  $aaux[1]=$options['id'];
 echo morfeo_video_render($aaux);
  echo $after_widget;

}



function morfeo_video_control()
{
  $options = get_option("widget_morfeo_video");
  if (!is_array( $options ))
{
$options = array(
      'title' => 'Morfeo Video',
	  'id' => '1'
      );
  }
 
  if ($_POST['morfeo-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['morfeo-WidgetTitle']);
	 $options['id'] = htmlspecialchars($_POST['morfeo-WidgetId']);
    update_option("widget_morfeo_video", $options);
  }
  
  
  global $wpdb; 
	$table_name = $wpdb->prefix . "morfeo_video";
	
	$myrows = $wpdb->get_results( "SELECT * FROM $table_name;" );

if(empty($myrows)) {
	
	echo '
	<p>First create a new gallery of videos, from the administration of MORFEO plugin.</p>
	';
}

else {
	$contaa1=0;
	$selector='<select name="morfeo-WidgetId" id="morfeo-WidgetId">';
	while($contaa1<count($myrows)) {
		
		
		$tt="";
		if($options['id']==$myrows[$contaa1]->id)  $tt=' selected="selected"';
		$selector.='<option value="'.$myrows[$contaa1]->id.'"'.$tt.'>'.$myrows[$contaa1]->id.'</option>';
		$contaa1++;
		
	}
	
	$selector.='</select>';
	
	
 
echo '
  <p>
    <label for="morfeo-WidgetTitle">Widget Title: </label>
    <input type="text" id="morfeo-WidgetTitle" name="morfeo-WidgetTitle" value="'.$options['title'].'" /><br/>
	<label for="morfeo-WidgetTitle">Morfeo Video Gallery ID: </label>
   '.$selector.'
    <input type="hidden" id="morfeo-Submit" name="morfeo-Submit" value="1" />
  </p>
';
}


}





function morfeo_video_init(){
	register_sidebar_widget(__('Morfeo Video'), 'widget_morfeo_video');
	register_widget_control(   'Morfeo Video', 'morfeo_video_control', 300, 300 );
}


function morfeo_video_add_menu(){	
	if (function_exists('add_options_page')) {
		//add_menu_page
		add_menu_page('morfeo_video', 'Morfeo Video', 8, basename(__FILE__), 'morfeo_video_panel');
	}
}
if (function_exists('add_action')) {
	add_action('admin_menu', 'morfeo_video_add_menu'); 
}
add_action('wp_head', 'morfeo_video_head');
add_filter('the_content', 'morfeo_video');
add_action('activate_morfeo_video/morfeo_video.php','morfeo_video_instala');
add_action('deactivate_morfeo_video/morfeo_video.php', 'morfeo_video_desinstala');
add_action("plugins_loaded", "morfeo_video_init");
?>