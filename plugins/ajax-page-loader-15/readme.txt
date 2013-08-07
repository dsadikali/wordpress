=== AJAX Page Loader 1.5 ===
Contributors: snumb130, bbodine1, HappyApple
Donate link: http://www.lukehowell.com/donate
Version 1.5.2
Tags: ajax, posts, pages, page, post
Requires at least: 2.0?
Tested up to: 2.8
Stable tag: 1.5.2

AJAX Page Loader will load posts, pages, etc. without reloading entire page.

== Description ==
Original:
AJAX Page Loader will load posts, pages, etc. without reloading entire page. This was my first plugin and is still a little quirky. There is problems working on some themes. I am working a little at a time on this but if anyone wants to contribute, feel free.

Description for 1.5:
This is the same plugin as http://wordpress.org/extend/plugins/ajax-page-loader/ . But with more of the bugs worked out.

Version 1.5.2 Adds IE6 compatibility.

Many thanks to Luke Howell, author of the original plugin. 

== Change Log ==
Version 1.5.2 - Adds IE6 compatibility.

Version 1.5.1 - orrected huge link problem from when I was developing the public package, I'm sooo sorry it took me this LONG to realize that. Revised coding to try to help make sure of compatibility. And changed the plugin link to new site. Also confirmed it works with WP 2.8 .

Version 1.5 - Jumped to 1.5 for the five new changes.

PHP code revised, now supports up to WordPress 2.7   .

Added a "Theme Support" guide to the readme in the FAQ section.

Included current version of jquery: 1.2.6-packed edition. (Now 32k compared to 1.0's 68k.) 

Included "querystring.js" javascript library which was missing from 1.0 .

Changed loading.gif to an animated throbber, found in AJAXed WP.

Version 1.0 - First release. 

== Installation ==

1. Upload `ajax-page-loader-15` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress. 

== Frequently Asked Questions ==

Q: The plugin isn't working right,  HHEEEELLLPPPP!!!!!!!!

A: You may need to use the Theme Support Guide in order to use AJAX Page Loader with your custom theme.

----Theme Support----

This edit may be required by some users with certain themes that cause AJAX Page Loader to reload the sidebar along with the content.

1. Open your theme's index.php file.
2.  find the "div" tag that contains the following inside a php tag: " if (have_posts()) : while (have_posts()) : the_post(); " . 
3. Give this "div" tag a unique ID. (Example: div id="blogcontent")
4. Edit "ajax-page-loader.js" and replace the word "content" inside every single or double quote marks with your new ID.

If you theme's search function stops working or causes the page to reload, then you'll need to edit your theme's "search.php" and "searchform.php" files.

1. Edit your theme's "search.php" file.
2. Find the "div" tag that containsthe following inside a php tag:
 "if (have_posts()) : "
3. Give this "div" the same unique ID as mentioned earlier. (Example:  div id="blogcontent")
4. Now edit your theme's "searchform.php" file.
5. Make sure the "form" tag has the ID of "searchform" .
6. Make sure the "input" tag has the ID of "s"  . 
