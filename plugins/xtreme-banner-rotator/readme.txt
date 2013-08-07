=== Xtreme Banner Rotator ===
Contributors: Flashtuning 
Tags: free banner rotator, free flash, as3, autoplay, banner rotator, buttons, effects, fullscreen, gallery, horizontal, image, mask, matrix, photo, transition, vertical, xml
Requires at least: 2.9.0
Tested up to: 3.0.1
Stable tag: trunk

The most advanced XML Banner Rotator application. No Flash Knowledge required to insert the Banner Rotator SWF inside the HTML page(s) of your site.

== Description ==

XML Image Banner Rotator / XML Banner Photo Gallery / XML Banner SlideShow.

**Features**

* No Flash Knowledge required to insert the Banner SWF inside the HTML page(s) of your site
* Fully customizable XML driven content
* Customizable width, height and item size
* Unlimited number of images ( JPG, PNG, BMP, GIF ) and SWF support
* Easy to use XML file for images / titles / descriptions and links
* View banner images by using the number buttons and left / right end arrow
* AutoPlay / Previous / Next with global or individual timer for each image
* Time period adjustable from the XML file (in seconds)
* Custom Horizontal / Vertical / Matrix orientation support for the navigation buttons
* Multiple mask transition effects such as Move, Fade, Tile, Radial Fade …
* Global or individual timer and transition definition for each text paragraph
* HTML / CSS driven description, text wrapping, font embedding, background and border color / size support
* Set URL links within the description text or when pressing on individual images
* Display the items in the order they appear in your XML file or in a random order
* Optional you can show / hide the navigation buttons and adjust each button position
* Optionally set the XML settings file path in HTML using FlashVars
* Full Screen mode support, you can use it as a banner and slidewhow and image gallery


== Installation ==

1. Download the plugin and upload it to the **/wp-content/plugins/** directory. Activate through the 'Plugins' menu in WordPress.
2. Download the [Free XtremeBannerRotator](http://www.flashtuning.net/flash-samples/XtremeBannerRotatorFree.zip "Xtreme Banner Rotator") and copy the content of the archive in **wp-content** folder. (e.g: "http://www.yoursite.com/wp-content/flashtuning/xtreme-banner-rotator")
3. Insert the swf into post or page using this tag: `[xtreme-banner-rotator]`. The default values for width and height are 595 300. If you want other values write the width and height attributes into the tag like so: `[xtreme-banner-rotator width="yourvalue" height="yourvalue"]`
4. To configure the banner rotator general parameters use the banner-settings.xml. For individual banner rotator items use the banner-contents.xml file. (image path, image link and more)
5. If you want to use multiple instances of Xtreme Banner Rotator on different pages. Follow this steps:
   a. There are 2 xml files in **wp-content/flashtuning/xtreme-banner-rotator** folder: **banner-settings.xml**, used for general settings, and **banner-content.xml**, used for individual items.
   b. Modify the 2 xml files according to your needs and rename them (eg.: **banner-settings2.xml**, **banner-content2.xml**)
   c. Open the **banner-settings2.xml**, search for this tag `< object param="contentXML"	value="banner-content.xml" />` and change the attribute **value** to **banner-content2.xml** .
   d. Copy the 2 modified xml files to **wp-content/flashtuning/xtreme-banner-rotator** .
   e. Use the **xml** attribute `[xtreme-banner-rotator xml="banner-settings2.xml"]` when you insert the banner rotator on a page.
6. Optionally for custom pages use this php function: `xtremeBannerRotatorEcho(width,height,xmlFile)` (e.g: **xtremeBannerRotatorEcho(595,420,'banner-settings.xml')** )

= Remove the Flashtuning.net logo =

 If you don’t want to have the Flashtuning.net logo on the top left corner, you'll have to purchase the [commercial package](http://www.flashtuning.net/flash-xml-image-viewers-galleries/x-treme-banner-rotator.html "FT Xtreme Banner Rotator"). You'll also have access to the fla file. After downloading the commercial archive, overwrite the swf file from the `/wp-content/flashtuning/xtreme-banner-rotator` directory.

== Screenshots ==

1. Fully customizable XML driven content. No Flash Knowledge required to insert the Banner Rotator SWF inside the HTML page(s) of your site.

