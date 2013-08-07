<?php
/*
Plugin Name: JJ NextGen JQuery Carousel
Description: Allows you to pick a gallery from the 'NextGen Gallery' plugin to use as a 'JQuery JCarousel'.
Author: JJ Coder
Version: 1.1.5
*/

if ( ! defined( 'WPJJNGGJ_CAROUSEL_PLUGIN_BASENAME' ) )
	define( 'WPJJNGGJ_CAROUSEL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'WPJJNGGJ_CAROUSEL_PLUGIN_NAME' ) )
	define( 'WPJJNGGJ_CAROUSEL_PLUGIN_NAME', trim( dirname( WPJJNGGJ_CAROUSEL_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'WPJJNGGJ_CAROUSEL_PLUGIN_DIR' ) )
	define( 'WPJJNGGJ_CAROUSEL_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPJJNGGJ_CAROUSEL_PLUGIN_NAME );

function WPJJNGGJ_CAROUSEL_set_plugin_meta($links, $file)
{
  $plugin = WPJJNGGJ_CAROUSEL_PLUGIN_BASENAME;
  if ($file == $plugin)
  {
    $links[] = '<a href="http://wordpress.org/extend/plugins/jj-nextgen-jquery-carousel/">' . 'Visit plugin site' . '</a>';
    $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=PSCMPVDADYP74&lc=NZ&item_name=JJNextGenJQueryCarousel&item_number=jjcoder_carousel&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">' . 'Donate' . '</a>';
  }
  return $links;
}
if( is_admin() )
{
  add_filter( 'plugin_row_meta', 'WPJJNGGJ_CAROUSEL_set_plugin_meta', 10, 2 );
}

require_once WPJJNGGJ_CAROUSEL_PLUGIN_DIR . '/includes/application.php';