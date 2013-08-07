<?php
/*
Plugin Name: AJAX Page Loader 1.5
Version: 1.5.2
Plugin URI: http://ajaxpageloader.yi.org/
Description: Load pages within blog without reloading page.  
Author: HappyApple, Luke Howell
Author URI: http://www.lukehowell.com/

---------------------------------------------------------------------
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
---------------------------------------------------------------------
*/

/*
Version 1.5.2 - Adds IE6 compatibility.

Version 1.5.1 - Revised coding to help make sure of compatibility. And changed plugin link to new site. Also confirmed it works with WP 2.8 .

Corrected huge link problem from when I was developing the public package, I'm sooo sorry it took me this LONG to realize that...

Version 1.5 - Jumped to 1.5 for the five new changes.

PHP code revised, now supports up to WordPress 2.7 .

Added a "Theme Support" guide to the readme in the FAQ section.

Included current version of jquery: 1.2.6-packed edition. (Now 32k compared to 1.0's 68k.) 

Included "querystring.js" javascript library which was missing from 1.0 .

Changed loading.gif to an animated throbber, found in AJAXed WP.

Version 1.0 First Release.
*/

if(!function_exists('get_option'))
  require_once('../../../wp-config.php');


// Set Hook for outputting JavaScript
add_action('wp_head','ajax_page_loader_js');
function ajax_page_loader_js() {?>
  <script type="text/javascript" src="<?php echo get_settings('home')?>/wp-content/plugins/ajax-page-loader-15/jquery.js"></script>
  <script type="text/javascript" src="<?php echo get_settings('home')?>/wp-content/plugins/ajax-page-loader-15/ajax-page-loader.js"></script>
  <script type="text/javascript" src="<?php echo get_settings('home')?>/wp-content/plugins/ajax-page-loader-15/querystring.js"></script>
  <script type="text/javascript">
    if (document.images){
      loadingIMG= new Image(16,16); 
      loadingIMG.src="<?php echo get_settings('home')?>/wp-content/plugins/ajax-page-loader-15/loading.gif";
    }
    var siteurl="<?php echo get_settings('siteurl');?>";
    var home="<?php echo get_settings('home')?>";
    if(window.location!=home+'/')
      window.location=home+'/';
  </script>
<?php }?>