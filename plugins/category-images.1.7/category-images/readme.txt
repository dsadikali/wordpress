=== Category Image(s) ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: category, categories, image, icon, post, coffee2code
Requires at least: 2.2
Tested up to: 3.0.1
Stable tag: 1.7
Version: 1.7

Display an image for each category associated with a post.


== Description ==

Display an image for each category associated with a post.

Notes:

This plugin provides the templates tag <code>c2c_the_category_image()</code> which basically reproduces and extends the functionality of WordPress's core function <code>the_category()</code> to add the ability to display a category image instead of the category name in the link to the category archive.  With its $image_extensions argument defaulted to <code>""</code>, the plugin could be a drop-in replacement for <code>the_category()</code>.  As is done in <code>the_category()</code>, the image or text for each category is also a link back to that category's archive.

Related info about the function:

* If $image_extensions is defined, but no image is found for the category, then nothing is displayed for that category.
* $image_extensions can be defined as a space separated list of image extensions.  Files will be checked with the image extensions in the order those extensions were provided; default is 'png gif jpg'.
* The number of category links/images displayed can be limited via the $limit argument
* Category image <img> tags are defined with class="catimage" for stylesheet manipulation.
* The result can be filtered via the filter named "c2c_the_category_image".
* Must be run inside "the loop".

`c2c_get_category_image_list()` is another provided function, which is identical to `c2c_the_category_image()` except that it doesn't echo the result.


== Installation ==

1. Unzip `category-images.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Create category image (.png unless you specify the extension in your call to `c2c_the_category_image()`) for each category you wish to have an image for, placing the image into the wp-content/images/ directory (unless you specify another location in your call to `c2c_the_category_image()`).  Remember to give the filename the nice-name version of the category name.
1. Make use of the `c2c_the_category_image()` template function in your template (see examples below).


== Template Tags ==

The plugin provides one optional template tag for use in your theme templates.

= Functions =

* `<?php function c2c_the_category_image( $seperator='', $parents='', $image_extensions='png gif jpg', $image_dir='/wp-content/images/', $use_name_if_no_image=false, $start_from='begin', $limit=999 ) ?>`

This displays the image associated with a post's categories.

= Arguments =

* `$separator`
Optional argument.  The text and/or HTML to appear between each of a post's category images.

* `$parents`
Optional argument.  Allows you to specify if you want ancestor categories of a post's category to be imaged and linked as well.  Valid options are 'multiple', 'single', and ''. Default is ''.

* `$image_extensions`
Optional argument.  A space-separated list of image extensions (case insensitive).  If defined as '' then no image is retrieved and the category name is displayed instead (a la `the_category()`).  Default is 'png gif jpg'.

* `$image_dir`
Optional argument.  The directory in which the category image(s) reside.  The value should be a location relative to the main URL of your blog.  Default is '/wp-content/images/'

* `$use_name_if_no_image`
Optional argument.  Boolean value (true or false) indicating if the name of the category should be displayed for each category that does not have an associated image.  Default is false.

* `$start_from`
Optional argument.  How to traverse the list of post's categories; either 'begin' or 'end'.  Default is 'begin'

* `$limit`
Optional argument.  The maximum number of category images to be displayed.  Default is 999 (basically, no limit)

* `$post_id`
Optional argument.  The Post ID.  If false, then use the current post (i.e. while in the loop).  Default is false.


== Examples ==

* You can opt to treat only certain categories as having a graphical representation by defining an image for them but not creating images for the non-graphically represented categories (as in you can have a subset of categories that dictate what image to be displayed for the post).

* You can take advantage of the $image_extension and/or $image_dir arguments to display different category icons under different contexts, i.e. if showing a $single post, choose to use a 'png' image, otherwise use 'gif'; or locate your images in different directories '/wp-content/images/cat-images/small/' and '/wp-content/images/cat-images/large/' and decide based on context where to get the image(s) from:

`  // In showing the post singularly, then use a larger image, else use a small image
  if ( is_single() ) {
	c2c_the_category_image('', '', 'gif', '/wp-content/images/cat-images/large/');
  } else {
	c2c_the_category_image('', '', 'gif', '/wp-content/images/cat-images/small/');
  }`

* Show all images in a comma-separated line:

`<?php c2c_the_category_image(', '); ?>`

* Show all images in an unordered list:

`<?php c2c_the_category_image();?>`

* Assuming one category per post, just show the category image without further HTML markup:

`<?php c2c_the_category_image(' ');	// note the space in the argument, necessary to turn off default <ul> markups ?>`


== Filters ==

The plugin exposes three filters for hooking.

= c2c_get_category_image (filter) =

The 'c2c_get_category_image' hook allows you to use an alternative approach to safely invoke `c2c_get_category_image()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

Arguments:

* same as for `c2c_get_category_image()`

Example:

Instead of:

    `<?php $cat_images = c2c_get_category_image( $cat ); ?>`

Do:

    `<?php $cat_images = apply_filters( 'c2c_get_category_image', $cat ); ?>`

= c2c_get_the_category_image_list (filter) =

The 'c2c_get_the_category_image_list' hook allows you to use an alternative approach to safely invoke `c2c_get_the_category_image_list()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

Arguments:

* same as for `c2c_get_the_category_image_list()`

Example:

Instead of:

    `<?php $cat_list = c2c_get_the_category_image_list(); ?>`

Do:

    `<?php $cat_list = apply_filters( 'c2c_get_the_category_image_list', '' ); ?>`

= c2c_the_category_image (filter) =

The 'c2c_the_category_image' hook allows you to use an alternative approach to safely invoke `c2c_the_category_image()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

Arguments:

* same as for `c2c_the_category_image()`

Example:

Instead of:

    `<?php c2c_the_category_image(); ?>`

Do:

    `<?php do_action( 'c2c_the_category_image' ); ?>`


== Changelog ==

= 1.7 =
* Rename filter 'c2c_the_category_image' to 'c2c_category_image_list'
* Add hooks 'c2c_get_category_image' (filter), 'c2c_get_the_category_image_list' (filter), and 'c2c_the_category_image' (action) to respond to the function of the same name so that users can use the apply_filters() or do_action() notation for invoking template tags
* Change to make leading and trailing forward slashes optional for $image_dir value
* Wrap each global function in function_exists() check
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Note compatibility with WP 2.9+, 3.0+
* Minor tweaks to code formatting (spacing)
* Add Changelog, Filters, and Upgrade Notice sections to readme.txt
* Add PHPDoc documentation
* Add package info to top of plugin file
* Update copyright date
* Remove trailing whitespace

= 1.6 (unreleased) ==
* Add optional arg $post_id (default of false) to c2c_get_the_category_image_list() to allow specifying a post
* Send $separator as arg in calls to get_category_parents()
* Use get_options() instead of get_settings()
* Note compatibility with WP 2.6+, 2.7+, 2.8+
* Update copyright date

= 1.5 =
* Move plugin into its own sub-directory
* Lots of changes to ensure compatibility with latest releases of WP
* Add function c2c_get_the_category_image_list() and move code from c2c_the_category_image() into it (but non-echoing though)
* Change c2c_the_category_image() to simply echo result of c2c_get_the_category_image_list()
* Use $cat->name instead $cat->cat_name
* Use term_id instead of cat_ID
* Allow filter of result via the filter named "c2c_the_category_image".
* Add readme.txt
* Minor code formatting changes
* Add installation instructions
* Change plugin description
* Change Plugin URI
* Change Author  URI
* Note compatibility with WP 2.3+, 2.5+
* Update copyright date

= 1.2 =
* Compatibility fix for WP1.5.1

= 1.1 =
* Same as v1.0 but compatible with WP1.5+
* Change to accommodate new get_category_link() arguments

= 1.0 =
* Last version compatible with WP 1.2
* Add argument $use_name_if_no_image=false to allow showing cat name if no image was located
* Tweaks to update c2c_the_category_image() with changes made to the_category() (mostly rel= stuff)
* Change default category images directory from /wp-images/ to /wp-content/images/

= 0.92 =
* Fix a pair of typos that prevented images from showing

= 0.91 =
* Now supports listing multiple image extensions
* Prepended all functions with “c2c_” to avoid potential function name collision with other plugins or future core functions… NOTE: If you are upgrading from an earlier version of the plugin, you’ll need to change your calls from the_category_image() to c2c_category_image()
* Change from BSD-new license to MIT license

= 0.9 =
* Initial public release


== Upgrade Notice ==

= 1.7 =
Recommended update. Highlights: added multiple hooks to allow customization; made leading and trailing slashes in paths optional; allow sending post_id arg to c2c_get_the_category_image_list(); verified WP 3.0 compatibility; other miscellaneous tweaks and fixes.