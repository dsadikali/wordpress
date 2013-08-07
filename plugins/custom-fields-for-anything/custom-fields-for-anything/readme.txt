=== Custom Fields For Anything ===
Contributors: coffee2code
Donate link: http://coffee2code.com
Tags: meta, custom field, extra, data, developer, tool 
Requires at least: 2.2
Tested up to: 2.5
Stable tag: trunk
Version: 1.0

Tools to facilitate the assignment and retrieval of custom fields for anything.

== Description ==

Tools to facilitate the assignment and retrieval of custom fields for anything.

This plugin mimics the meta/custom field concept already utilized by WordPress.  Whereas WordPress only supports assignment of this additional information to posts and users (implementing each separately: separate database tables and separate functions to create/retrieve/update/delete the respective types of custom fields), this plugin abstracts the concept to utilize a single database table and a universally applicable set of functions to create/retrieve/update/delete custom fields.

Note: This plugin does not seek to integrate the existing post and user meta/custom fields data in any way.  While it would be possible to modify WordPress to rely solely on Custom Fields For Anything to handle ALL meta/custom fields, this plugin does not actively attempt to do that.  As such, you must use the WordPress provided functions to access post and user meta/custom fields (since they are in their separate databases not maintained by this plugin).  You could, of course, use Custom Fields For Anything to handle posts and user meta fields separately from the WordPress-managed tables and functions.

Note: This plugin is primarily for use by plugin developers to allow them to easily create and utilize meta fields for any number of uses.  Non-developers probably will not have immediate, direct use of this plugin except to enable it for use by another plugin, or to retrieve custom field data once some have been created.

== Installation ==

1. Unzip `custom-fields-for-anything.zip` inside the `/wp-content/plugins/` directory, or upload `custom-fields-for-anything.php` into `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. If you are a user, you shouldn't need to do anything else.  Another plugin can now use this plugin to provide additional functionality for you.  If you are a developer, you now have access to rich set of functions to help you utilize custom fields.

== Usage ==

This plugin provides the same set of functions as WordPress provides for post meta/custom fields.  A familiarity with those functions and their usage will directly translate to familiarity with most of the functions provided by Custom Fields For Anything.  The only thing you really have to be aware of is that you must now provide a $type argument to most functions.  This is a term of your choosing to represent a scope for custom fields.  For instance, you could define $type to be "categories" to then have custom fields for categories, or "links" to have link custom fields, or "product" for product custom fields, etc.

= Functions =

These are how Custom Fields For Anything functions correspond to existing WordPress post meta functions (please view the source of this plugin for actual arguments and consult WordPress documentation for usage on the post meta functions to get an idea of how to use the Custom Fields For Anything version):

* `c2c_add_any_meta()` => `add_post_meta()`
* `c2c_delete_any_meta()` => `delete_post_meta()`
* `c2c_get_any_meta_by_type()` => `get_post_meta_by_type()`
* `c2c_get_any_meta()` => `get_post_meta()`
* `c2c_update_any_meta()` => `update_post_meta()`
* `c2c_delete_any_meta_by_key()` => `delete_post_meta_by_key()`
* `c2c_get_any_custom()` => `get_post_custom()`
* `c2c_get_any_custom_keys()` => `get_post_custom_keys()`
* `c2c_get_any_custom_values()` => `get_post_custom_values()`
* `c2c_update_anymeta_cache()` => `update_postmeta_cache()`

= Arguments =

* `$type`
The scope for a given set of custom fields, i.e. "categories", "links".

* `$type_id`
The id of the object of the $type that you want to assign/retrieve custom field information for.

* `$key`
The custom field key.

* `$value`
The custom field value

= Examples = 

`// Assign a custom field of "image" to the category with the id of 25`
`c2c_add_any_meta(25, "category", "image", "/wp-content/images/house.png", true);`

`// Retrieve the field just created`
`$cat_image = c2c_get_any_meta(25, "category", "image", true);`

== Frequently Asked Questions ==

= What do I do with this plugin? =

This plugin is not of direct use to casual users.  It is intended to be used in conjunction with another plugin(s).  It provides plugin developers easy custom field functionality which they can take advantage of in their own plugins.
