<?php
/*
Plugin Name: Custom Fields For Anything
Version: 1.0
Plugin URI: http://coffee2code.com/wp-plugins/custom-fields-for-anything
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Tools to facilitate the assignment and retrieval of custom fields for anything.  

This plugin mimics the meta/custom field concept already utilized by WordPress.  Whereas WordPress only 
supports assignment of this additional information to posts and users (implementing each separately:
separate database tables and separate functions to create/retrieve/update/delete the respective types of
custom fields), this plugin abstracts the concept to utilize a single database table and a universally
applicable set of functions to create/retrieve/update/delete custom fields.

Note: This plugin does not seek to integrate the existing post and user meta/custom fields data in any way.
While it would be possible to modify WordPress to rely solely on Custom Fields For Anything to handle ALL 
meta/custom fields, this plugin does not actively attempt to do that.  As such, you must use the WordPress
provided functions to access post and user meta/custom fields (since they are in their separate databases not
maintained by this plugin).  You could, of course, use Custom Fields For Anything to handle posts and user
meta fields separately from the WordPress-managed tables and functions.

Note: This plugin is primarily for use by plugin developers to allow them to easily create and utilize meta
fields for any number of uses.  Non-developers probably will not have immediate, direct use of this plugin
except to enable it for use by another plugin, or to retrieve custom field data once some have been created.

Compatible with WordPress 2.2+, 2.3+, and 2.5.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates


Installation:

1. Download the file http://coffee2code.com/wp-plugins/custom-fields-for-anything.zip and unzip it into your 
/wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. If you are a user, you shouldn't need to do anything else.  Another plugin can now use this plugin to provide
additional functionality for you.  If you are a developer, you now have access to rich set of functions to help
you utilize custom fields.


Usage:

This plugin provides the same set of functions as WordPress provides for post meta/custom fields.  A familiarity
with those functions and their usage will directly translate to familiarity with most of the functions provided
by Custom Fields For Anything.  The only thing you really have to be aware of is that you must now provide a
$type argument to most functions.  This is a term of your choosing to represent a scope for custom fields.  For
instance, you could define $type to be "categories" to then have custom fields for categories, or "links" to
have link custom fields, or "product" for product custom fields, etc.

These are the arguments you'll encounter in the functions provided by the plugin:
  $type : The scope for a given set of custom fields, i.e. "categories", "links"
  $type_id : The id of the object of the $type that you want to assign/retrieve custom field information for
  $key : The custom field key
  $value : The custom field value

These are how Custom Fields For Anything functions correspond to existing WordPress post meta functions (please
view the source of this plugin for actual arguments and consult WordPress documentation for usage on the post
meta functions to get an idea of how to use the Custom Fields For Anything version):

  c2c_add_any_meta() => add_post_meta()
  c2c_delete_any_meta() => delete_post_meta()
  c2c_get_any_meta_by_type() => get_post_meta_by_type()
  c2c_get_any_meta() => get_post_meta()
  c2c_update_any_meta() => update_post_meta()
  c2c_delete_any_meta_by_key() => delete_post_meta_by_key()
  c2c_get_any_custom() => get_post_custom()
  c2c_get_any_custom_keys() => get_post_custom_keys()
  c2c_get_any_custom_values() => get_post_custom_values()
  c2c_update_anymeta_cache() => update_postmeta_cache()


Examples:
	// Assign a custom field of "image" to the category with the id of 25
	c2c_add_any_meta(25, "category", "image", "/wp-content/images/house.png", true);
 	// Retrieve the field just created
	$cat_image = c2c_get_any_meta(25, "category", "image", true);

*/

/*
Copyright (c) 2007-2008 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


$wpdb->anymeta = $wpdb->prefix . 'anymeta';

/* Developers: check for the existence of this function if you plan to use this plugin in conjunction with yours */
function c2c_has_any_meta() {
	return true;
}

/* Function to install anymeta table */
function c2c_any_meta_install() {
	global $wpdb;
	$sql = "SHOW TABLES LIKE '{$wpdb->anymeta}'";
	$already_exists = $wpdb->query($sql);
	if ($already_exists) return;
	$sql = "CREATE TABLE {$wpdb->anymeta} (
			meta_id		bigint(20) unsigned NOT NULL auto_increment,
			type_id		bigint(20) unsigned NOT NULL default '0',
			type		varchar(100) NOT NULL,
			meta_key 	varchar(255) default NULL,
			meta_value 	longtext,
			PRIMARY KEY	(meta_id),
			KEY type_id	(type_id),
			KEY type	(type),
			KEY meta_key (meta_key)
		);";
	$wpdb->query($sql);
}

/* Equivalent to WP's add_post_meta().  See documentation above for explanation of arguments. */
function c2c_add_any_meta($type_id, $type, $key, $value, $unique = false) {
	global $wpdb, $any_meta_cache, $blog_id;

	$type_id = (int) $type_id;

	if ( $unique ) {
		if ( $wpdb->get_var("SELECT meta_key FROM $wpdb->anymeta WHERE meta_key = '$key' AND type_id = '$type_id' AND type = '$type'") ) {
			return false;
		}
	}

	$any_meta_cache[$blog_id][$type][$type_id][$key][] = $value;

	$value = maybe_serialize($value);
	$value = $wpdb->escape($value);

	$wpdb->query("INSERT INTO $wpdb->anymeta (type_id,type,meta_key,meta_value) VALUES ('$type_id','$type','$key','$value')");

	return true;
}

/* Equivalent to WP's delete_post_meta().  See documentation above for explanation of arguments. */
function c2c_delete_any_meta($type_id, $type, $key = '', $value = '') {
	global $wpdb, $any_meta_cache, $blog_id;

	$type_id = (int) $type_id;

	if ( empty($key) ) {
		if ( empty($value) ) {
			$wpdb->query("DELETE FROM $wpdb->anymeta WHERE type_id = '$type_id' AND type = '$type'");
			unset($any_meta_cache[$blog_id][$type][$type_id]);
		} else {
			$wpdb->query("DELETE FROM $wpdb->anymeta WHERE type_id = '$type_id' AND type = '$type' AND meta_value = '$value'");
			unset($any_meta_cache[$blog_id][$type][$type_id]); // not worth iterating to find the records that match $value
		}
			
	}
	
	if ( empty($value) ) {
		$meta_id = $wpdb->get_var("SELECT meta_id FROM $wpdb->anymeta WHERE type_id = '$type_id' AND type = '$type' AND meta_key = '$key'");
	} else {
		$meta_id = $wpdb->get_var("SELECT meta_id FROM $wpdb->anymeta WHERE type_id = '$type_id' AND type = '$type' AND meta_key = '$key' AND meta_value = '$value'");
	}

	if ( !$meta_id )
		return false;

	if ( empty($value) ) {
		$wpdb->query("DELETE FROM $wpdb->anymeta WHERE type_id = '$type_id' AND type = '$type' AND meta_key = '$key'");
		unset($any_meta_cache[$blog_id][$type][$type_id][$key]);
	} else {
		$wpdb->query("DELETE FROM $wpdb->anymeta WHERE type_id = '$type_id' AND type = '$type' AND meta_key = '$key' AND meta_value = '$value'");
		$cache_key = $any_meta_cache[$blog_id][$type][$type_id][$key];
		if ($cache_key) foreach ( $cache_key as $index => $data )
	    	if ( $data == $value )
	    		unset($any_meta_cache[$blog_id][$type][$type_id][$key][$index]);
	}

	unset($any_meta_cache[$blog_id][$type][$type_id][$key]);

	return true;
}

function c2c_get_any_meta_by_type($type, $key, $value = '') {
	//TODO: Do this better
	global $wpdb, $any_meta_cache, $blog_id;
	$sql = "SELECT * FROM $wpdb->anymeta WHERE type = '$type' and meta_key = '$key'";
	if (! empty($value) )
		$sql .= " AND meta_value = '$value'";
	return $wpdb->get_results($sql);
	
//	if ( isset($any_meta_cache[$blog_id][$type]) ) {
//		return;
//	}
}

/* Equivalent to WP's get_post_meta().  See documentation above for explanation of arguments. */
function c2c_get_any_meta($type_id, $type, $key, $single = false) {
	global $wpdb, $any_meta_cache, $blog_id;

	$type_id = (int) $type_id;

	if ( isset($any_meta_cache[$blog_id][$type][$type_id][$key]) ) {
		if ( $single ) {
			return maybe_unserialize( $any_meta_cache[$blog_id][$type][$type_id][$key][0] );
		} else {
			return maybe_unserialize( $any_meta_cache[$blog_id][$type][$type_id][$key] );
		}
	}

	if ( !isset($any_meta_cache[$blog_id][$type][$type_id]) )
		c2c_update_anymeta_cache($type, $type_id);

	if ( $single ) {
		if ( isset($any_meta_cache[$blog_id][$type][$type_id][$key][0]) )
			return maybe_unserialize($any_meta_cache[$blog_id][$type][$type_id][$key][0]);
		else
			return '';
	} else {
		return maybe_unserialize($any_meta_cache[$blog_id][$type][$type_id][$key]);
	}
}

/* Equivalent to WP's update_post_meta().  See documentation above for explanation of arguments. */
function c2c_update_any_meta($type_id, $type, $key, $value, $prev_value = '') {
	global $wpdb, $any_meta_cache, $blog_id;

	$type_id = (int) $type_id;

	$original_value = $value;
	$value = maybe_serialize($value);
	$value = $wpdb->escape($value);

	$original_prev = $prev_value;
	$prev_value = maybe_serialize($prev_value);
	$prev_value = $wpdb->escape($prev_value);

	if (! $wpdb->get_var("SELECT meta_key FROM $wpdb->anymeta WHERE meta_key = '$key' AND type_id = '$type_id' AND type = '$type'") ) {
		return false;
	}

	if ( empty($prev_value) ) {
		$wpdb->query("UPDATE $wpdb->anymeta SET meta_value = '$value' WHERE meta_key = '$key' AND type_id = '$type_id' AND type = '$type'");
		$cache_key = $any_meta_cache[$blog_id][$type][$type_id][$key];
		if ( !empty($cache_key) )
			foreach ($cache_key as $index => $data)
				$any_meta_cache[$blog_id][$type][$type_id][$key][$index] = $original_value;
	} else {
		$wpdb->query("UPDATE $wpdb->anymeta SET meta_value = '$value' WHERE meta_key = '$key' AND type_id = '$type_id' AND type= '$type' AND meta_value = '$prev_value'");
		$cache_key = $any_meta_cache[$blog_id][$type][$type_id][$key];
		if ( !empty($cache_key) )
			foreach ($cache_key as $index => $data)
				if ( $data == $original_prev )
					$any_meta_cache[$blog_id][$type][$type_id][$key][$index] = $original_value;
	}

	return true;
}

/* Equivalent to WP's delete_post_meta_by_key().  See documentation above for explanation of arguments. */
function c2c_delete_any_meta_by_key($type, $any_meta_key) {
	global $wpdb, $any_meta_cache, $blog_id;
	$any_meta_key = $wpdb->escape($any_meta_key);
	if ( $wpdb->query("DELETE FROM $wpdb->anymeta WHERE type = '$type' AND meta_key = '$any_meta_key'") ) {
		unset($any_meta_cache[$blog_id]); // not worth doing the work to iterate through the cache
		return true;
	}
	return false;
}

/* Equivalent to WP's get_post_custom().  See documentation above for explanation of arguments. */
function c2c_get_any_custom($type_id, $type) {
	global $any_meta_cache, $wpdb, $blog_id;

	$type_id = (int) $type_id;

	if ( !isset($any_meta_cache[$blog_id][$type][$type_id]) )
		c2c_update_anymeta_cache($type, $type_id);

	return $any_meta_cache[$blog_id][$type][$type_id];
}

/* Equivalent to WP's get_post_custom_keys().  See documentation above for explanation of arguments. */
function c2c_get_any_custom_keys( $type_id, $type ) {
	$custom = c2c_get_any_custom( $type_id, $type );

	if ( !is_array($custom) )
		return;

	if ( $keys = array_keys($custom) )
		return $keys;
}

/* Equivalent to WP's get_post_custom_values().  See documentation above for explanation of arguments. */
function c2c_get_any_custom_values( $key, $type_id, $type ) {
        $custom = c2c_get_any_custom($type_id, $type);

        return $custom[$key];
}

/* Equivalent to WP's update_postmeta_cache().  See documentation above for explanation of arguments.  Intended for internal use. */
function c2c_update_anymeta_cache($type, $type_id_list = '') {
	global $wpdb, $any_meta_cache, $blog_id;

	// We should validate this comma-separated list for the upcoming SQL query
	$type_id_list = preg_replace('|[^0-9,]|', '', $type_id_list);

	if ( empty( $type_id_list ) )
		return false;

	// we're marking each post as having its meta cached (with no keys... empty array), to prevent posts with no meta keys from being queried again
	// any posts that DO have keys will have this empty array overwritten with a proper array, down below
	$type_id_array = (array) explode(',', $type_id_list);
	$count = count( $type_id_array);
	for ( $i = 0; $i < $count; $i++ ) {
		$type_id = $type_id_array[ $i ];
		if ( isset( $any_meta_cache[$blog_id][$type][$type_id] ) ) { // If the meta is already cached
			unset( $type_id_array[ $i ] );
			continue;
		}
		$any_meta_cache[$blog_id][$type][$type_id] = array();
	}
	if ( count( $type_id_array ) == 0 )
		return;
	$type_id_list = join( ',', $type_id_array ); // with already cached stuff removed

	// Get any-meta info
	if ( $meta_list = $wpdb->get_results("SELECT type_id, type, meta_key, meta_value FROM $wpdb->anymeta WHERE type_id IN($type_id_list) AND type = '$type' ORDER BY type_id, meta_key", ARRAY_A) ) {

		// Change from flat structure to hierarchical:
		if ( !isset($any_meta_cache) )
			$any_meta_cache[$blog_id] = array();

		foreach ($meta_list as $metarow) {
			$mpid = (int) $metarow['type_id'];
			$mkey = $metarow['meta_key'];
			$mval = $metarow['meta_value'];

			// Force subkeys to be array type:
			if ( !isset($any_meta_cache[$blog_id][$type][$mpid]) || !is_array($any_meta_cache[$blog_id][$type][$mpid]) )
				$any_meta_cache[$blog_id][$type][$mpid] = array();
			if ( !isset($any_meta_cache[$blog_id][$type][$mpid]["$mkey"]) || !is_array($any_meta_cache[$blog_id][$type][$mpid]["$mkey"]) )
				$any_meta_cache[$blog_id][$type][$mpid]["$mkey"] = array();

			// Add a value to the current pid/key:
			$any_meta_cache[$blog_id][$type][$mpid][$mkey][] = $mval;
		}
	}
}

register_activation_hook( __FILE__, 'c2c_any_meta_install' );

?>