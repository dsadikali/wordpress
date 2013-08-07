=== Grou Random Image Widget ===
Contributors: Grou 
Donate link: http://grou29.free.fr/
Author URI: http://grou29.free.fr
Plugin URI: http://grou29.free.fr
Tags: random, image, images, picture, pictures, gallery, widget, widgets, sidebar, banner, image, ajax
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 1.17


Grou Random Image plug-in allow you to add a widget to your Wordpress site that display random Images from a specific folder on your server. 

== Description ==

Grou Random Image plug-in allow you to add a widget to your Wordpress site that display random Images from a specific folder on your server. It also allow viewer to cycle through images without reloading the full page.


Feature List

* Display random image from folder
* Navigation button to dynamically reload next/previous/random image (optional)
* Fixed widget size or dynamic (adjust on image)
* Smooth image transition (fadin/fadout)
* Compatible with lightbox plugin (click on image)
* Load image description from text file (image name + ".txt") if available
      exemple: for "house.jpg" a file "house.jpg.txt" should contains the image description
* Photo look (see screenshoot): need GD lib extension for php
* SlideShow option
* Choice of picture frame between 9 available
* Link to full image , fixed url or specific URL for each image(NEW)
* Option to preload full size image in background task
* W3C valid


<a href="http://lienssanslien.free.fr/?lang=en">Demo can be found here</a> 

<a href="http://grou29.free.fr/?p=1#comments">Leave a comment here</a>

== Installation ==


1. Unzip to plugin folder
2. Enable plugin in admin-panel
3. Drop widget in widget configuration panel
4. Configure local folder in widget options
6. Download pictures in folder (ftp)
7. Drop an email if you like/dislike the plugin

For every picture file, you can make an additional text file (same filename+".txt") containig a description or caption that will be displayed as tooltip over the image.
(NEW) In the same file you can also add an URL to use when you click on the image(choose the "Link to : Url in description file" in the widget configuration).
To do so, use the '|' to separate the description and the URL.
For example:  
Photo of my son |http://grou29.free.fr

If you are using an additional plugin like <a href="http://blog.moskis.net/downloads/plugins/fancybox-for-wordpress/" target="_blank">FancyBox</a>, this description will be displayed under the image.

Usage  
 
Options are:

* Image Path: Files server path from the Webserver root directory.
* Behavior
  * Simple: just display a random image
  * Navigation: give next/previous navigation button
  * Random:  a random button allow user to load a new random image
  * Navigation+random
  * Slideshow 
* Max. image width: Maximum width of thumbnail
* Max. image height:Maximum height of thumbnail
* Fixe size: Should widget size (height) will be fixe or should it adapt to thumbnail image size
* Picture aspect: Draw Photographic picture
* Frame Size: If Picture aspect is selected, size of the white frame around image
* Rotate angle: If Picture aspect is selected, rotate image of n degrees (0=> no rotation, empty=> random rotation)
* Background color: Depending of your GD php library, image rotation doesn't work well with transparent image. The widget try to minimize this bug by drawing a full background color instead.
* Picture frame choice
* Multiple instance of widget
 
== Frequently Asked Questions ==

* Why fading doesn't work with internet explorer ?
Internet explorer doesn't support transparent 24bit PNG and opacity change. The widget disable the fading when IE brower is detected.

== Screenshots ==

1. Usage example
2. Picture frame exemple
2. Widget configuration


== Changelog ==


= 1.17 =
* Removed ugly wait image
* [Add] new transition options: Shrink, vertical Slide, Horizontal slide
* [FIX] bug with transparent image and Internet explorer
* [FIX] Navigation button not displayed in Chrome
	
= 1.16 =
* [FIX] picture aspect not working with some php parser version

= 1.15 =
* [FIX] bug that stop fancybox to work with the plug-in
* [Add] option to open link in a new window
* [Add] No link option 

= 1.13 =
* Add option to link a specific URL in desciption file (use the'|' char to separate description and URL)

= 1.12.1 =
* [FIX] some strange behavior on IE 7
* [ADD] Image directory set by default to plugin example 

= 1.12 =
* [Add] Option to print tooltip below the preview image

= 1.11 =
* minor bug

= 1.10 =
* [Change] new widget configuration panel
* [Fix] bug with fancybox showing multiple images (arrow for next image)
* [Add] Option to select navigation icone 
* [Add] Option to select Image tooltip (none, description, static)
* [Add] 4 pictures aspect added
* [Add] Option for random or cycle slideshow


= 1.9 =
* Z-order of loading animation fixed
* Option to pre-load full size image
* Option to link to URL instead of full size image

= 1.8 =
* cleaner code and bugfix
* W3C html compliant
* Better error handling
* Better display on IE
* 1 picture frame added
* Multiple instance of widget

= 1.7 =
* New slideshow option
* Fixed javascript bug on IE
* Fixed cached file on IE that shouldn't 

= 1.6 =
* Picture frame effect with optional rotation

= 1.5 =
* minor change
= 1.4 =
* minor change
= 1.3 =
* Bug due to plugin rename
* French translation

= 1.2 =
* First released version



== License ==

This plugin is free for everyone. It's released under the GPL, you can use it free of charge on your personal or commercial blog. 
But if you enjoy this plugin, you can thank me and leave a [small donation](http://www.arnebrachhold.de/redir/sitemap-paypal "Donate with PayPal") for the time I've spent writing and supporting this plugin. And I really don't want to know how many hours of my life this plugin has already eaten ;)

== Translations ==

The plugin comes with various translations, please refer to the [WordPress Codex](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") for more information about activating the translation. If you want to help to translate the plugin to your language, please have a look at the sitemap.pot file which contains all definitions and may be used with a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) (Windows).