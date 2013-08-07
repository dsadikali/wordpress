=== easy-banners ===
Contributors: bannersky
Donate link: http://www.bannerslide.com/easy-banners-for-wordpress.html
Plugin URI: http://www.bannerslide.com/easy-banners-for-wordpress.html
Tags: banners, header, right, Ads
Requires at least: 2.8.2
Tested up to: 3.0
Stable tag: 1.3


== Description ==

Manage all banners in your site. Tested on WordPress 2.8.2 and 2.8.4 and 2.8.5 and 2.9.2, 3.0 on bugs found.

== Installation ==

1. Upload `easy-banners.php` and all directories (javascript, php) with files in them to the `/wp-content/plugins/easy-banners` directory.  
2. Activate the plugin through the 'Plugins' menu in WordPress. 
3. You will see the 'Easy Banners' menu in the 'Settings' tab of the WordPress dashboard. You may add/delete/edit banners there. It is easy to use.
4. Call the function 'wp_easy_banner_display()' in your theme. All banners will be displayed.

== Frequently Asked Questions ==

= What are the parameters of the function 'wp_easy_banner_display' ?=
The function has three parameters: $position = 'RIGHT', $nameOfClass='sideimg', $readMore='read more>>'.
Every parameter has a default value but you can change them, of course

The parameter $position can only be 'RIGHT', 'HEADER', 'LEFT'. 
'RIGHT' means display all banners which you placed on the right.
'LEFT' means display all banners which should be placed on the left.
'HEADER' means display all banners for the header.

wp_easy_banner_display('HEADER') will display all "header" banners
wp_easy_banner_display('RIGHT') will display all "right" banners
wp_easy_banner_display('LEFT') will display all "left" banners


= What's the output format? =
The output format is as follows:

<div class="sideimg"><br />
<a href="xxxxxxxx.com" target="_blank"><img src="xxxxxxxxxx"/></a><br />
<p><a href="xxxxxxxxxxx">read more</a><p><br />
</div>

The class name "sideimg" can be changed when calling the function.

= How to customize the read more tag and the image? =

You can set them using the parameters of the function 'wp_easy_banner_display' 
wp_easy_banner_display('HEADER', 'yourClassName', 'your read more') . 
For this example the function will display all "header" banners and output will as follows:

<div class="yourClassName"><br />
<a href="xxxxxxxx.com" target="_blank"><img src="xxxxxxxxxx"/></a><br />
<p><a href="xxxxxxxxxxx">your read more</a><p><br />
</div>

== Changelog ==

= 1.3 =
* Remove check source and target url option.
= 1.2 =
* Added popup to show the wp-content/uploads folder medias, and standardized some of the elements. Fixed CURL error.
* The medias will be shown only image(jpg,png,gif,jpeg) or flash(swf).
* Thanks for Brian's advice and contribution.
= 1.1 =
* Fixed bug with 'LEFT' not working when chosen. The old will be setted HEADER when you chosen LEFT. Fixed now. 
= 1.0 =
* First version.
