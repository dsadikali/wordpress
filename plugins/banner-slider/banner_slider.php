<?php
/*
Plugin Name: Slider banner 
Plugin URI: http://ajtechbd.com/
Description: A banner  management component. Support multi banner transition. Use Shortcode [banner_slider] in post, page or text widget
Version: 1.0
Author: leonti Macario
Author URI: http://ajtech.com
License: GPL
*/
register_activation_hook(__FILE__,'on_activation');
function on_activation(){
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');		
	global $wpdb;
    $table_name = $wpdb->prefix . "banner";
	$sql = "CREATE TABLE `". $table_name."` (
           `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
           `image_path` TEXT NULL ,
           `link_title` TEXT NULL ,
           `link_url` TEXT NULL ,
           `image_order` INT( 10 ) NULL ,
            INDEX ( `image_order`)) ;";
	dbDelta($sql);
	update_option('aj_slideshow_width','636');
	update_option('aj_slideshow_height','434');

}
add_action('admin_menu', 'ajtech_banner_admin_menu');
function ajtech_banner_admin_menu(){
	add_menu_page( "Manage Banners", "Manage Banners", 10, "aj_manage_banners", "manage_banner" );
	add_submenu_page( "aj_manage_banners","Slider Option", "Slider Option", 10, "aj_slider_option", "aj_slider_option" );
}

//SLIDER OPTION PAGE
function aj_slider_option(){
?>
<div class="wrap">
<h2>Slider Option</h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="aj_slideshow_width,aj_slideshow_height,aj_slideshow_thumbnail_bar,aj_slideshow_thumb_width,aj_slideshow_thumb_height" />
<table class="form-table">
<tr valign="top">
<th width="150" align="left" scope="row">
Slideshow Width
</th>
<td align="left" width="200">
<input type="text" name="aj_slideshow_width" value="<?php echo get_option('aj_slideshow_width'); ?>">
</td>
<td align="left"></td>
</tr>
<tr valign="top">
<th width="150" align="left" scope="row">
Slideshow Height
</th>
<td align="left" width="200">
<input type="text" name="aj_slideshow_height" value="<?php echo get_option('aj_slideshow_height'); ?>">
</td>
<td align="left"></td>
</tr>
<tr valign="top">
<th width="150" align="left" scope="row">
Show Thumbnail Bar
</th>
<td align="left" width="200">
<label><input type="radio" <?php if(get_option('aj_slideshow_width')=='' OR get_option('aj_slideshow_width')=='1') echo 'checked="checked"'; ?> name="aj_slideshow_thumbnail_bar" value="1">Yes</label>  <label><input type="radio" <?php if(get_option('aj_slideshow_width')=='0') echo 'checked="checked"'; ?> name="aj_slideshow_thumbnail_bar" value="0">No</label>
</td>
<td align="left"></td>
</tr>

<tr valign="top">
<th width="150" align="left" scope="row">
Thumbnail Width
</th>
<td align="left" width="200">
<input type="text" name="aj_slideshow_thumb_width" value="<?php echo get_option('aj_slideshow_width'); ?>">
</td>
<td align="left"></td>
</tr>

<tr valign="top">
<th width="150" align="left" scope="row">
Thumbnail Height
</th>
<td align="left" width="200">
<input type="text" name="aj_slideshow_thumb_height" value="<?php echo get_option('aj_slideshow_height'); ?>">
</td>
<td align="left"></td>
</tr>

<tr valign="top">
<td align="left" colspan="2">
<input type="submit" name="submitBtn" class="button-primary" value="Save" />
</td>
<td align="left"></td>
</tr>

</table>
</form>
</div>
<?php	
}

//MANAGE BANNERS- add,edit,delete
function manage_banner(){
$task = $_REQUEST['task'];

switch($task){
	case 'banner_upload':
	banner_upload();
	break;
	case 'delete_banner':
	delete_banner();
	break;
	case 'edit_banner':
	edit_banner();
	break;
}
	?>
<div class="wrap">
<h2>Manage Banners</h2>
<script type="text/javascript" src="<?php bloginfo( 'url' ); ?>/wp-content/plugins/banner_slider/jquery-1.3.2.min.js"></script>

<div id="rotating_banner" class="borderThin" style="height:auto; overflow:hidden">
<style type="text/css">
	#show_images{
		padding-bottom:50px;
		width: 60%;
		height:auto;
		overflow: hidden;
	}
	#show_images span{
		display: block;
		width: 150px;
		height: 150px;
		float:left;
		margin: 10px;
	}
	#show_images span img{
		width: 150px;
		height: 150px;
		border: 1px solid #CCC
	}

/*#show_images table tr.head td{ color:#FFF; background-color: #00324A; padding:5px; font-weight: bold;}
#show_images table tr.odd td{ color: #333; background-color: #C5DCF5; padding:5px; font-weight: bold;}
#show_images table tr.even td{ color: #333; background-color: #93BDEC; padding:5px; font-weight: bold;}*/
</style>
<?php
$mode = $_REQUEST['mode'];

switch($mode){
	case '':
	case 'show_upload_form':
	show_banner_upload_form();
	break;
	case 'show_banner_edit_form':
	show_banner_edit_form();
	break;
}
?>
<div style="clear:both"></div><br /><br />
<?php
if(!empty($del_message))
{
?>
<div class="updated"><?php echo $del_message ?></div>
<?php
unset($del_message);
}
?>
<div style="clear:both"></div>
<br />
<div id="show_images" style="width:100%">
<?php				
	global $wpdb;
    $table_name = $wpdb->prefix . "banner";
	$banners = $wpdb->get_results("SELECT*FROM $table_name ORDER BY image_order ASC");
?>
<table width="100%"  class="widefat fixed">
<thead>
  <tr class="thead">
    <th width="15">ID</th>
    <th width="200">EXCERPT</th>
    <th width="200">BANNER URL</th>
    <th width="200">BANNER PATH</th>
    <th width="100">BANNER ORDER</th>
    <th width="150">ACTION</th>
  </tr>
</thead>
<tbody id="users" class="list:user user-list">
  <?php 
  if(count($banners)==0){
?>
  <tr>
    <td colspan="5">No banner inserted yet</td>
  </tr>
<?php	  
  }else{
  $i=0;
  foreach($banners as $bn){ 
	 $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
  
  ?>
  <tr <?php echo $style ?>>
    <td width="15"><?php echo $bn->id; ?></td>
    <td width="200"><?php echo $bn->link_title; ?></td>
    <td width="200"><?php echo $bn->link_url; ?></td>
    <td width="200"><img src="../wp-content/uploads/banners/<?php echo $bn->image_path; ?>" width="50" height="50" /></td>
    <td width="200"><?php echo $bn->image_order; ?></td>
    <td width="50"><a href="admin.php?page=aj_manage_banners&task=delete_banner&banner_id=<?php echo $bn->id ?>" onclick="">DELETE</a> | <a href="admin.php?page=aj_manage_banners&mode=show_banner_edit_form&banner_id=<?php echo $bn->id ?>" onclick="">EDIT</a></td>
  </tr>
  <?php $i++; }} ?>
</tbody>

</table>

</div>




        </div>
</div>
<?php	
}

//BANNER UPLOAD FORM
function show_banner_upload_form(){
?>


<script type="text/javascript">
function startUpload_banner(){
	//alert('uplaod start');
     /* document.getElementById('f1_upload_process').style.visibility = 'visible';*/
     // document.getElementById('f1_upload_form').style.visibility = 'hidden';
	 $('#f1_upload_process').show();
	 $('#f1_upload_process').addClass('updated');
     return false;
}

function stopUpload_banner(success){
      var result = '';

      if (success.status == 1){
         //result = '<span class="msg">'+success.filename+'!<\/span><br/><br/>';
		  $('#f1_upload_process').html(success.msg);
		  $('#show_images').html(success.output);
		  $('#f1_upload_process').show();
		  //$('#show_images').append(success.images);
		  //$('#show_images').show();		  
		  document.file_upload_banner.reset();
      }
      else {
         //result = '<span class="emsg">There was an error during file upload!<\/span><br/><br/>';
		  $('#f1_upload_process').html(success.error);
		  $('#f1_upload_process').removeClass('updated');
		  $('#f1_upload_process').addClass('error');
		  $('#f1_upload_process').show();
      } 

	 
      return true;   
}


</script>
<h2>Add new banner</h2>
<div style="float:left; width: 50%;">
<form  method="post" enctype="multipart/form-data" target="upload_target_banner"  name="file_upload_banner" onsubmit="startUpload_banner();" >
<div id="f1_upload_process" style="display:none">Loading...</div>
<div id="upload_form">
	<div id="upload_fields">
	<strong>File: </strong> <input name="myfile1" id="myfile1" type="file" size="50" />
	</div>
 	<div>
	<strong>Excerpt: </strong>    <br /><input name="link_title" id="link_title" type="text" size="50" />
	</div> 
 	<div>
	<strong>Banner URL:</strong>  <br /><input name="linkurl" id="linkurl" type="text" size="50" />
	</div> 
 	<div>
    <strong>Banner Order:</strong><br /> 
	<p><input name="order" id="order" type="text" size="15" value="0" /> </p>
	</div>           
    <br />
	
	<input type="submit" name="submitBtn" class="sbtn" value="Upload" />
    <input type="hidden" value="1" name="fldcount" id="fldcount">
    <input name="task" type="hidden" value="banner_upload">
	<iframe id="upload_target_banner" name="upload_target_banner" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
</div>
</form>
</div>

<?php
}

//BANNER EDIT FORM
function show_banner_edit_form(){
?>
<script type="text/javascript">
function startUpload_banner(){
	//alert('uplaod start');
     /* document.getElementById('f1_upload_process').style.visibility = 'visible';*/
     // document.getElementById('f1_upload_form').style.visibility = 'hidden';
	 $('#f1_upload_process').show();
	 $('#f1_upload_process').addClass('updated');
     return false;
}

function stopUpload_banner(success){
      var result = '';

      if (success.status == 1){
         //result = '<span class="msg">'+success.filename+'!<\/span><br/><br/>';
		  $('#f1_upload_process').html(success.msg);
		  $('#show_images').html(success.output);
		  $('#f1_upload_process').show();
		  //$('#show_images').append(success.images);
		  //$('#show_images').show();		  
      }
      else {
         //result = '<span class="emsg">There was an error during file upload!<\/span><br/><br/>';
		  $('#f1_upload_process').html(success.error);
		  $('#f1_upload_process').removeClass('updated');
		  $('#f1_upload_process').addClass('error');
		  $('#f1_upload_process').show();
      } 

	 
      return true;   
}


</script>
<h2>Edit banner info</h2>
<?php
	global $wpdb;
    $table_name = $wpdb->prefix . "banner";
	$bn_id = $_REQUEST['banner_id'];
	$bn = $wpdb->get_row("SELECT*FROM $table_name WHERE id=".$bn_id);
?>
<div style="float:left; width: 50%;">
<form  method="post" enctype="multipart/form-data" target="upload_target_banner"  name="file_upload_banner" onsubmit="startUpload_banner();" >
<div id="f1_upload_process" style="display:none">Loading...</div>
<div id="upload_form">
	<div id="upload_fields">
	<strong>File: </strong> <input name="myfile1" id="myfile1" type="file" size="50"  /> (Note:  To not changed the Banner leave it blank.)
	</div>
 	<div>
	<strong>Excerpt: </strong>    <br /><input name="link_title" id="link_title" value="<?php echo $bn->link_title; ?>" type="text" size="50" />
	</div> 
 	<div>
	<strong>Banner URL:</strong>  <br /><input name="linkurl" id="linkurl" type="text" value="<?php echo $bn->link_url; ?>" size="50" />
	</div> 
 	<div>
    <strong>Banner Order:</strong><br /> 
	<p><input name="order" id="order" type="text" size="15" value="<?php echo $bn->image_order; ?>"/> </p>
	</div>           
    <br />
	
	<input type="submit" name="submitBtn" class="sbtn" value="Update Banner Details" />
    <input type="hidden" value="<?php echo $_REQUEST['banner_id']?>" name="banner_id" id="banner_id">
    <input name="task" type="hidden" value="edit_banner">
	<iframe id="upload_target_banner" name="upload_target_banner" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
</div>
</form>
</div>
<?php	
}
//BANNER ADD/UPLOADING PROCESS
function banner_upload(){
	global $wpdb;
    $table_name = $wpdb->prefix."banner";
	//Check teh image type
	$type_array=array("image/jpg","image/jpeg","image/gif","image/png","image/bmp");
	if(empty($_FILES['myfile1']['name']))
	$result="{error:'No File is selected '}";
	
	else if(empty($_POST['link_title']))
	$result="{error:'Link title field is blank'}";
	
	else if(empty($_POST['linkurl']))
	$result="{error:'Link URL field is blank'}";
	
	else if(!in_array($_FILES['myfile1']['type'],$type_array))
	$result="{error:'Files type is not supported'}";
	
	else{
		
		if(!is_dir("../wp-content/uploads/banners"))
		mkdir("../wp-content/uploads/banners",0777, true);
		$full_img = time().strtolower(str_replace(' ', '-',$_FILES['myfile1']['name']));
		$userImage = "../wp-content/uploads/banners/".$full_img;
		$type = $_FILES['myfile1']['type'];
		if (!move_uploaded_file($_FILES['myfile1']['tmp_name'], $userImage))
		{
			$result="{msg:'Banner is not uploaded properly. Please try again later'}";
		}	
		else{
			$imagename = $full_img;
			//aj_slideshow_width,aj_slideshow_height,aj_slideshow_thumbnail_bar,aj_slideshow_thumb_width,aj_slideshow_thumb_height
			$imgname = insight_image_resize(get_option('aj_slideshow_width'),get_option('aj_slideshow_height'),$userImage,"../wp-content/uploads/banners/",$type,$full_img);
			$link_title= $_POST['link_title'];
			$link_url = $_POST['linkurl'];			
			$order = $_POST['order'];
			$sql= $wpdb->query("INSERT INTO `$table_name` (`image_path`, `link_title`, `link_url`, `image_order`) VALUES('$imgname', '$link_title', '$link_url', $order)");	


		$banners = $wpdb->get_results("SELECT*FROM $table_name ORDER BY image_order ASC");
		$output='<table width="100%"  class="widefat fixed"><thead> <tr class="thead"><th width="15">ID</th><th width="200">EXCERPT</th><th width="200">BANNER URL</th><th width="200">BANNER PATH</th><th width="100">BANNER ORDER</th><th width="150">ACTION</th></tr></thead><tbody id="users" class="list:user user-list">';
		if(count($banners)==0){
			$output.= '<tr><td colspan="5">No banner inserted yet or no table created</td></tr>';
		}else{


			  $i=0; 
			  foreach($banners as $bn){ 
			  $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';

			  $output.= '<tr '.$style.'"><td width="15">'.$bn->id.'</td><td width="200">'.$bn->link_title.'</td><td width="200">'.$bn->link_url.'</td><td width="200"><img src="../wp-content/uploads/banners/'.$bn->image_path.'" width="50" height="50" /></td><td width="200">'.$bn->image_order.'</td><td width="50"><a href="admin.php?page=aj_manage_banners&task=delete_banner&banner_id='.$bn->id.'" onclick="">DELETE</a> | <a href="admin.php?page=aj_manage_banners&mode=show_banner_edit_form&banner_id='.$bn->id .'" onclick="">EDIT</a></td></tr>';
 			$i++; }

		}
		$output.='</tbody></table>';
		$result="{status:1,msg:'Successfully banner uploaded',output:'$output'}";
		}		
	}
	

	
	//result = "{status:1,msg:'Files uploaded successfully',images:'".$output."'}";

?>
<script language="javascript" type="text/javascript">window.top.window.stopUpload_banner(<?php echo $result; ?>);</script>  
<?php
}

//BANNER EDIT PROCESS
function edit_banner(){
	global $wpdb;
    $table_name = $wpdb->prefix."banner";
	//Check teh image type
	$type_array=array("image/jpg","image/jpeg","image/gif","image/png","image/bmp");

	if(!in_array($_FILES['myfile1']['type'],$type_array) && $_FILES['myfile1']['name']!='')
	$result="{error:'Files type is not supported'}";		

	
	 else if(empty($_POST['link_title']))
	$result="{error:'Link title field is blank'}";
	
	else if(empty($_POST['linkurl']))
	$result="{error:'Link URL field is blank'}";
	
	else{
		
		if(!is_dir("../wp-content/uploads/banners"))
		mkdir("../wp-content/uploads/banners",0777, true);
		$full_img = time().strtolower(str_replace(' ', '-',$_FILES['myfile1']['name']));
		$userImage = "../wp-content/uploads/banners/".$full_img;
		$type = $_FILES['myfile1']['type'];
		$imagename = $full_img;
		$sql='';
			if (move_uploaded_file($_FILES['myfile1']['tmp_name'], $userImage))
			{	
				$imgname = insight_image_resize(get_option('aj_slideshow_width'),get_option('aj_slideshow_height'),$userImage,"../wp-content/uploads/banners/",$type,$full_img);
				$sql.="SET image_path='$imgname'";
			}
				
			
			$link_title= $_POST['link_title'];
			$link_url = $_POST['linkurl'];
			$order = $_POST['order'];		
			
			if(empty($sql))
			$sql.="SET link_title='$link_title', link_url='$link_url',image_order=$order";
			else
			$sql.=",link_title='$link_title', link_url='$link_url',image_order=$order";
			
			$sql= $wpdb->query("UPDATE `$table_name` $sql WHERE id=".$_POST['banner_id']);	
	
			$banners = $wpdb->get_results("SELECT*FROM $table_name ORDER BY image_order ASC");
		    $output='<table width="100%"  class="widefat fixed"><thead> <tr class="thead"><th width="15">ID</th><th width="200">EXCERPT</th><th width="200">BANNER URL</th><th width="200">BANNER PATH</th><th width="100">BANNER ORDER</th><th width="150">ACTION</th></tr></thead><tbody id="users" class="list:user user-list">';
			if(count($banners)==0){
				$output.= '<tr><td colspan="5">No banner inserted yet or no table created</td></tr>';
			}else{
				  $i=0;
				  foreach($banners as $bn){ 
			  $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';

			  $output.= '<tr '.$style.'"><td width="15">'.$bn->id.'</td><td width="200">'.$bn->link_title.'</td><td width="200">'.$bn->link_url.'</td><td width="200"><img src="../wp-content/uploads/banners/'.$bn->image_path.'" width="50" height="50" /></td><td width="200">'.$bn->image_order.'</td><td width="50"><a href="admin.php?page=aj_manage_banners&task=delete_banner&banner_id='.$bn->id.'" onclick="">DELETE</a> | <a href="admin.php?page=aj_manage_banners&mode=show_banner_edit_form&banner_id='.$bn->id .'" onclick="">EDIT</a></td></tr>';
				$i++; }
	
			
			}
			$output.='</tbody></table>';
			$result="{status:1,msg:'Changes saved',output:'$output'}";
			
	
			
		
	}
	

	
	//result = "{status:1,msg:'Files uploaded successfully',images:'".$output."'}";

?>
<script language="javascript" type="text/javascript">window.top.window.stopUpload_banner(<?php echo $result; ?>);</script>  
<?php
}

//BANNER DELETE PROCESS
function delete_banner(){

	$key= $_REQUEST['banner_id'];
	global $wpdb;
    $table_name = $wpdb->prefix . "banner";
	$del = $wpdb->get_results("DELETE FROM $table_name WHERE id=".$key);
	if($del)
	echo '<script>window.location="themes.php?page=aj_manage_banners"</script>';

}

//SHWO SLIDER FUNCTION
function show_banner($atts=''){
?>
<div class="ss-right_side_box">
	
		<?php	
	if($atts['effect']!='')
	$effect = $atts['effect'];
	else
	$effect= 'fade';
?>	
<script type="text/javascript" src="<?php bloginfo( 'url' ); ?>/wp-content/plugins/banner_slider/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'url' ); ?>/wp-content/plugins/banner_slider/jquery.nivo.slider.js"></script>
<script type="text/javascript">

$(window).load(function() {

	$('#slider_container').nivoSlider({effect:'<?php echo $effect; ?>',controlNav:false,animSpeed:500,pauseTime:3000,pauseOnHover:true});

});

</script>
		<style type="text/css">
        /*  SLIDER CSS*/
        .nivoSlider {
            position:relative;
            /*background:url(images/loading.gif) no-repeat 50% 50% #000;*/
            height:<?php echo get_option('aj_slideshow_height'); ?>px !important;
            width:<?php echo get_option('aj_slideshow_width'); ?>px !important;
            margin:20px 0 0 6px !important;
        }
        .nivoSlider img {
            position:absolute;
            top:0px;
            left:0px;
            display:none;
        }
        /* If an image is wrapped in a link */
        .nivoSlider a.nivo-imageLink {
            position:absolute;
            top:0px;
            left:0px;
            width:100%;
            height:100%;
            border:0;
            padding:0;
            margin:0;
            z-index:60;
            display:none;
        }
        /* The slices in the Slider */
        .nivo-slice {
            display:block;
            position:absolute;
            z-index:50;
            height:100%;
        }
        /* Caption styles */
        .nivo-caption {
            position:absolute;
            left:0px;
            bottom:0px;
            background:#000;
            color:#fff;
            opacity:0.8; /* Overridden by captionOpacity setting */
            width:100%;
            height: 50px;
            z-index:89;
            color: #fff;
        }
        .nivo-caption p {
            padding:5px;
            margin:0;
            color: #FFF
        }
        
        
        .nivo-directionNav a {
            background:transparent url(images/arrows.png) no-repeat scroll 0 0;
            border:0 none;
            display:block;
            height:30px;
            text-indent:-9999px;
            width:30px;
            position:absolute;
            top:45%;
            z-index:99;
            cursor:pointer;
        }
        a.nivo-nextNav {
            background-position:-30px 0;
            right:15px;
        }
        a.nivo-prevNav {
            left:15px;
        }
        
        
        .nivo-controlNav{
        bottom:-30px;
        left:47%;
        position:absolute;
        }
        .nivo-controlNav a {
            position:relative;
            z-index:99;
            cursor:pointer;
            background:transparent url(images/bullets.png) no-repeat scroll 0 0;
            
        }
        .nivo-controlNav a.active {
            font-weight:bold;
        }
        
        .nivo-controlNav {
        bottom:-30px;
        left:47%;
        position:absolute;
        }
        .nivo-controlNav a {
        background:transparent url(images/bullets.png) no-repeat scroll 0 0;
        border:0 none;
        display:block;
        float:left;
        height:10px;
        margin-right:3px;
        text-indent:-9999px;
        width:10px;
        }
        .nivo-controlNav a.active {
        background-position:-10px 0;
        }
        
        </style>
<div id="slider_container">

<?php
global $wpdb;

    $table_name = $wpdb->prefix."banner";	

	$banners = $wpdb->get_results("SELECT*FROM $table_name ORDER BY image_order ASC");

	foreach($banners as $bn){

		echo '<a href="'.$bn->link_url.'" class="nivo-imageLink"  target="_blank" style="display:none"><img src="'.get_bloginfo('url').'/wp-content/uploads/banners/'.$bn->image_path.'"  width="860" height="280"/></a>';

	}



?>

 </div>
 </div>
<?php
}

//IMAGE RESIZE FUNCTION
if(!function_exists('insight_image_resize')):
function insight_image_resize($new_width,$new_height,$userImage_path,$image_save_path,$file_type,$file_name){

				// Get dimensions and set new ones.
				list($width, $height) = getimagesize($userImage_path);
			
				// Resample
				$image_p = imagecreatetruecolor($new_width, $new_height);
				
				switch ($file_type)
				{
					case 'image/jpeg':
					case 'image/jpg': // added support for .jpg to the "default" support for .jpeg, so WLPE doesn't give a filetype error
					case 'image/pjpeg': // fix for IE6, which handles the .jpg filetype incorrectly
						$image = imagecreatefromjpeg($userImage_path);
						$ext = '.jpg';
						break;
						
					case 'image/gif':
						$image = imagecreatefromgif($userImage_path);
						imageSaveAlpha($image, true);
						imagesavealpha($image_p, true);
						$trans = imagecolorallocatealpha($image_p,255,255,255,127);
						imagefill($image_p,0,0,$trans);
						$ext = '.gif';
						break;
						
					case 'image/png':
						$image = imagecreatefrompng($userImage_path);
						imageSaveAlpha($image, true);
						imagesavealpha($image_p, true);
						$trans = imagecolorallocatealpha($image_p,255,255,255,127);
						imagefill($image_p,0,0,$trans);
						$ext = '.png';
						break;

				}
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		
				// Output
				$imgname = $new_width.'x'.$new_height.$file_name;
				$userImageFilePath = $image_save_path.$imgname;
				//$userImageFileURL = $modx->config['site_url'].'assets/snippets/webloginpe/userimages/'.str_replace(' ', '_', strtolower($currentWebUser['username'])).$ext;
				//$userImageFileURL = $img_path.str_replace(' ', '_', strtolower($imgname)).$ext;
				
				switch ($file_type)
				{
					case 'image/jpeg':
						imagejpeg($image_p, $userImageFilePath, 100);
						break;
						
					case 'image/gif':
						imagegif($image_p, $userImageFilePath);
						break;
						
					case 'image/png':
						imagepng($image_p, $userImageFilePath, 0);
						break;
						
					default	:
						imagejpeg($image_p, $userImageFilePath, 100);
				}	
				return $imgname;
}
endif;
add_shortcode('banner_slider', 'show_banner');
?>
