<?php
/*
Plugin Name: banner
Plugin URI: http://www.bibuweb.de/
Description: Advertise banner plugin
Version: 1.0.1
Author: Alfredo Cubitos
E-Mail: cubito@users.sourceforge.net 
Author URI: http://www.bibuweb.de
*/

/*  Copyright 2005 Alfredo Cubitos

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    For a copy of the GNU General Public License, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
define("WP_BANNER_VERSION","1.0.1");
define("WP_BANNER_PATH",WP_PLUGIN_URL .'/wp-banner');


function banner_add_style()
{
   wp_enqueue_script('jquery');
   wp_register_style('wpbannerstyle', WP_BANNER_PATH . '/styles/default.css');
   wp_enqueue_style('wpbannerstyle');
}


class WPbannerWidget extends WP_Widget 
{
    
    function WPbannerWidget() 
    {
        parent::WP_Widget(false, $name = 'WP Banner Widget');	
    }
    
    function widget($args, $instance) {	
	
        extract( $args );
        
        $title = $instance['title'];
        $client = $instance['client'];
        
         echo $before_widget;
         if ( $title )
	 {
            echo $before_title . $title . $after_title; 
	    echo getWidgetBannerUrl($client);
	 }
			
         echo $after_widget; 
        
    }
    
    function update($new_instance, $old_instance) {				
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['client'] = strip_tags($new_instance['client']);
        return $instance;
    }

   function form($instance) 
   {
      global $wpdb,$table_prefix;
      $title = esc_attr($instance['title']);
      $client = esc_attr($instance['client']);
      
      $default = "Advertisement";

      $query = "SELECT  * FROM ".$table_prefix."banner WHERE banner_active=1 AND (banner_startdate=0 OR banner_startdate<=".time().") AND (banner_enddate=0 OR banner_enddate>".time().") AND (banner_impurchased=0 OR banner_impressions<=banner_impurchased) AND banner_position='widget'";
      $banner = $wpdb -> get_results($query);
      
      echo '<label for="bannerTitle">Title: </label>';
      echo '<input id="bannerTitle" name="'.$this->get_field_name('title').'" type="text" size="15" value="'.(strlen($title) > 0 ? $title : $default ).'"><br />';
      echo '<label for="bannerClient">Client: </label>';
      echo '<select id="bannerClient" onchange="wpWidgets.save(jQuery(jQuery(this)).closest(\'div.widget\'), 0, 1, 0 );" name="'.$this->get_field_name('client').'">';
      echo "<option></option>";
      foreach ($banner as $ad)
      {
	 echo "<option value='" .$ad->banner_id. "'"; 
	 if ( $client == $ad->banner_id) 
	 {
	    echo " selected='selected'";
	    $selected = $ad->banner_id;
	    $url = $ad->banner_url;
	    $name = $ad->banner_clientname;
	 }
	  
	  echo ">".$ad->banner_clientname.'</option>';
	
      }
      echo '</select>';
      
      
      if ($client == $selected)
      {
	 echo "<img src='$url' alt='$name' style='border:0' >";
      }

   }
  

}


function getWidgetBannerUrl($id)
{
      global $wpdb,$table_prefix;
      
      $query = "SELECT  * FROM ".$table_prefix."banner WHERE banner_id=$id AND (banner_startdate=0 OR banner_startdate<=".time().") AND (banner_enddate=0 OR banner_enddate>".time().") AND (banner_impurchased=0 OR banner_impressions<=banner_impurchased)";
      $client = $wpdb -> get_row($query);
      if($client)
      {
	 if ($client->banner_type == "Flash")
	 {
		list($width,$height) = split("x",$client->banner_size);
	       $url = "<a href=\"".$ad->banner_clickurl."\" TARGET=\"_blank\" onclick=\"BannerClick(".$client->banner_id.")\">
		<OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000 width=\"$width\" height=\"$height\">
		<PARAM NAME=\"movie\" VALUE=\"".$client->banner_url."\">
		<PARAM NAME=\"quality\" VALUE=\"high\">
		<EMBED src=\"".$client->banner_url."\"quality=\"high\"width=\"$width\" height=\"$height\" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED>
		</OBJECT>
		</a>\n";
	}else{
		  $url = "<a href=\"".$client->banner_clickurl."\" TARGET=\"_blank\" id=\"bannerclient".$client->banner_id."\"> <img src=\"".$client->banner_url."\" alt=\"".$client->banner_clientname."\" style='border:0' ></a>";
	}
      }else{
	 $url = "<div>Place your Ad here</div>";
      }
      return ($url);
}

function banner()
{
   global $wpdb,$table_prefix;
   
        $query = "SELECT * FROM ".$table_prefix."banner WHERE banner_active=1 AND (banner_startdate=0 OR banner_startdate<=".time().") AND (banner_enddate=0 OR banner_enddate>".time().") AND (banner_impurchased=0 OR banner_impressions<=banner_impurchased) AND banner_position like 'divTag%' ORDER BY RAND()";
        $banner = $wpdb -> get_results($query);
        $layoutid = array();
        /**
        * check if there are double position ids
        * and get rid of them
        **/
        foreach($banner as $ad)
        {
	     list($tmp,$pos) = explode(",",$ad->banner_position);
	     if (! in_array($pos,$layoutid))
	     {
	       $layoutid[] = $pos;
	       
	     }else{
	       array_shift($banner);
	     }
        }
        
        $divID = 0;
        foreach ($banner as $ad)
        {
	    $wpdb->query("UPDATE ".$table_prefix."banner SET banner_impressions=banner_impressions+1 WHERE banner_id=\"".$ad->banner_id."\"");
	    list($width,$height) = split("x",$ad->banner_size);
	    
	    list($tmp,$pos,$jq,$left) = explode(",",$ad->banner_position);
	    
	    if ($ad->banner_type == "Flash")
	    {
		
	       $text .= "<div id=\"bannerdiv$divID\" style=\"padding-left:$left\"><a href=\"".$ad->banner_clickurl."\" TARGET=\"_blank\" onclick=\"BannerClick(".$ad->banner_id.")\">
		<OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000 width=\"$width\" height=\"$height\">
		<PARAM NAME=\"movie\" VALUE=\"".$ad->banner_url."\">
		<PARAM NAME=\"quality\" VALUE=\"high\">
		<EMBED src=\"".$ad->banner_url."\"quality=\"high\"width=\"$width\" height=\"$height\" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED>
		</OBJECT>
		</a>
		</div>\n";
	    }else{
		$text .= "<div id=\"bannerdiv$divID\" style=\"padding-left:$left%;\"><a href=\"".$ad->banner_clickurl."\" TARGET=\"_blank\" id=\"bannerclient".$ad->banner_id."\"> <img src=\"".$ad->banner_url."\" alt=\"".$ad->banner_clientname."\" style=\"border:0;\" ></a></div>\n";
	    }
	    $pos != "body" ? $pos = "#$pos" : $pos;
	    $script .= "jQuery(\"$pos\").$jq(jQuery(\"#bannerdiv$divID\"));\n";
	    
	   $divID++;
        }
        
        echo "$text";
        echo "<script type=\"text/javascript\">"; ?>
        
		jQuery('a[id^=bannerclient]')
		     .click(function(){
		     var id = jQuery(this).attr('id');
		     id = id.substring(id.length -1)
		     jQuery.get('<?php echo WP_BANNER_PATH .'/banner_clicks.php'?>',{banner_id:id});
		   });
		 
	      
		  
	<?php
        echo 	$script;
        echo "</script>";
}

if (!function_exists(banner_admin_init))
{
   function banner_admin_init()
   {
      
      wp_register_script('bannerJqueryUiCore',WP_BANNER_PATH . '/js/jquery.ui.core.min.js');
      wp_register_script('bannerJqueryUiWidget',WP_BANNER_PATH . '/js/jquery.ui.widget.min.js');
      wp_register_script('bannerMouse', WP_BANNER_PATH . '/js/jquery.ui.mouse.min.js');
      wp_register_script('bannerJqueryUiTabs',WP_BANNER_PATH . '/js/jquery.ui.tabs.min.js');
      wp_register_script('bannerCalendar', WP_BANNER_PATH . '/js/jquery.ui.datepicker.js');
      wp_register_script('bannerDialog', WP_BANNER_PATH . '/js/jquery.ui.dialog.min.js');
      wp_register_script('bannerDialogValidate', WP_BANNER_PATH . '/js/jquery.validate.min.js');
      wp_register_script('bannerSlider', WP_BANNER_PATH . '/js/jquery.ui.slider.min.js');
      wp_register_script('bannerDrag', WP_BANNER_PATH . '/js/jquery.ui.draggable.min.js');
      wp_register_script('bannerResize', WP_BANNER_PATH . '/js/jquery.ui.resizable.min.js');
      wp_register_script('bannerPosition', WP_BANNER_PATH . '/js/jquery.ui.position.min.js');
      wp_register_script('bannerTooltip', WP_BANNER_PATH . '/js/jquery.ui.tooltip.js');
      
      
      wp_register_style('banner-jQui', WP_BANNER_PATH . '/styles/jquery/humanity/jquery-ui-1.8.2.custom.css');
      wp_enqueue_style('banner-jQui');
      wp_register_style('banner-jQuiTooltip', WP_BANNER_PATH . '/styles/jquery/jquery.ui.tooltip.css');
      wp_enqueue_style('banner-jQuiTooltip');
      wp_register_style('bannerAdmin', WP_BANNER_PATH . '/styles/banneradmin.css');
      wp_enqueue_style('bannerAdmin');
      
   }

}

if (!function_exists(banner_admin_scripts))
{
   function banner_admin_scripts()
   {
      wp_enqueue_script('bannerJqueryUiCore');
      wp_enqueue_script('bannerJqueryUiWidget');
      wp_enqueue_script('bannerMouse');
      wp_enqueue_script('bannerJqueryUiTabs');
      wp_enqueue_script('bannerCalendar');
      wp_enqueue_script('bannerDialog');
      wp_enqueue_script('bannerDialogValidate');
      wp_enqueue_script('bannerSlider');
      wp_enqueue_script('bannerDrag');
      wp_enqueue_script('bannerResize');
      wp_enqueue_script('bannerPosition');
      wp_enqueue_script('bannerTooltip');
   }
}

if (!function_exists(banner_admin_menu))
{
    function banner_admin_menu()
    {
        add_submenu_page('plugins.php', 'Banner Menu', 'Banner Admin',9, '/wp-banner/banner_admintab.php');
    }
}



function banner_install()
  {
     global $wpdb, $table;
     $table = $wpdb->prefix."banner";
      $sql = "CREATE TABLE " . $table ."  (
  				banner_id int(10) unsigned NOT NULL auto_increment,
  				banner_clientname varchar(100) NOT NULL default '',
  				banner_clickurl varchar(150) NOT NULL default '',
  				banner_impurchased int(10) unsigned NOT NULL default '0',
  				banner_startdate int(10) unsigned NOT NULL default '0',
  				banner_enddate int(10) unsigned NOT NULL default '0',
  				banner_active tinyint(1) unsigned NOT NULL default '0',
  				banner_clicks int(10) unsigned NOT NULL default '0',
  				banner_impressions int(10) unsigned NOT NULL default '0',
  				banner_url text NOT NULL,
  				banner_size varchar(10) NOT NULL default '',
  				banner_type varchar(10) NOT NULL default '',
  				banner_position varchar(25)  NOT NULL default 'divTag',
  				PRIMARY KEY  (`banner_id`)
				);";
				
     
     	dbDelta($sql);
     
     $welcome_name = "WP Banner";
     $welcome_text = "Congratulations, you just completed the installation!";
     add_option("wpbanner_version",  WP_BANNER_VERSION); 
  }
  
	add_action('wp_print_styles','banner_add_style');
	add_action('wp_footer','banner');
	add_action('admin_init', 'banner_admin_init');
	add_action( "admin_print_scripts-wp-banner/banner_admintab.php", 'banner_admin_scripts' );
	add_action('admin_menu', 'banner_admin_menu');
	add_action('widgets_init', create_function('', 'return register_widget("WPbannerWidget");'));
	register_activation_hook(__FILE__,'banner_install');
	
?>