<?php
/*
Plugin Name: Allgemeine Site-Einstellungen
Plugin URI: http://www.avantiprinting.com/
Description: Ein Generic Website Einstellung Plugin..
Version: 1
Author: Avantipritng
Author URI: http://www.avantiprinting.com
*/
ob_start();
?>
<?php
function mt_general_settings()
{
	if($_REQUEST['cp_update_general_setting']=="Update Settings")
	{
		$general_post_id = get_option("cp_footer_msg");
		if(!empty($_REQUEST['cp_footer_msg']))
		{
			if(empty($general_post_id))
				add_option("cp_footer_msg",$_REQUEST['cp_footer_msg'], '', 'yes');
			else
				update_option("cp_footer_msg", $_REQUEST['cp_footer_msg']);
		}
		
	
		
		
	
	}
	?>

<div class="wrap">
<h2><?php echo $vstitle;?>General Settings<br>
</h2>
<br>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<table class="form-table">
		<tr valign="top">
			<th width="100px"><label for="teaser post">Footer Content</label></th>
			<td><input  name="cp_footer_msg" type="text" id="cp_footer_msg" value="<?php echo get_option("cp_footer_msg");?>" />
				<span class="description"></span></td>
		</tr>		
	
        
		<tr valign="top">
			<th scope="row"></th>
			<td><input style="padding:9px 13px 9px 13px;" type="submit" name="cp_update_general_setting" value="<?php _e('Update Settings', 'cp_update_general_setting') ?>" /></td>
		</tr>
	</table>
</form>
<?php	
}
function general_manage() {
 	add_submenu_page( 'options-general.php', 'Menu set', 'Site Einstellungen', 'administrator', 'allgemeine-site-einstellungen', 'mt_general_settings');
	}
add_action('admin_menu','general_manage');
?>