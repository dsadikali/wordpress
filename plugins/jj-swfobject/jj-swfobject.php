<?php
/*
Plugin Name: JJ SwfObject
Description: Allows you to insert a swf file using a widget or a shortcode using the swfobject library.
Author: JJ Coder
Version: 1.0.2
*/

if ( ! defined( 'WPJJ_SWFOBJECT_PLUGIN_BASENAME' ) )
	define( 'WPJJ_SWFOBJECT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'WPJJ_SWFOBJECT_PLUGIN_NAME' ) )
	define( 'WPJJ_SWFOBJECT_PLUGIN_NAME', trim( dirname( WPJJ_SWFOBJECT_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'WPJJ_SWFOBJECT_PLUGIN_DIR' ) )
	define( 'WPJJ_SWFOBJECT_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPJJ_SWFOBJECT_PLUGIN_NAME );

function WPJJ_SWFOBJECT_set_plugin_meta($links, $file) 
{
  $plugin = WPJJ_SWFOBJECT_PLUGIN_BASENAME;  
  if ($file == $plugin) 
  {
    $links[] = '<a href="http://wordpress.org/extend/plugins/jj-swfobject/">' . 'Visit plugin site' . '</a>';    
    $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=PSCMPVDADYP74&lc=NZ&item_name=JJSwfObject&item_number=jjcoder_swfobject&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">' . 'Donate' . '</a>';
  }
  return $links;
}
if( is_admin() )
{
  add_filter( 'plugin_row_meta', 'WPJJ_SWFOBJECT_set_plugin_meta', 10, 2 );
}

require_once WPJJ_SWFOBJECT_PLUGIN_DIR . '/includes/application.php';