=== Plugin Name ===
Contributors: Dyasonhat
Tags: sort posts, sort categories, sort archives, sort, custom fields 
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: 2.1.2
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1793510

WP Smart sort offers advanced sorting of posts in your blog. Sort posts by Title, Date, Comment count, Custom Fields and more.

== Description ==

WP Smart Sort allow for advanced sorting of posts in your blog. The administrator can choose to include any field from the wp_posts table to sort by, any custom field and additionally can identify where a field is numeric or not (defaults to text).

The default sort direction for your blog can be changed to any of the chosen field either Ascending or Descending. "Sort By" widget enables your users to select from a drop down box which direction they wish to sort posts by. Demo site over at [wpsmartsort.dyasonat.com](http://wpsmartsort.dyasonhat.com/ "Demo")


For additional functionality for you blog including sort, filtering and advanced search of posts across your blog checkout out [WP Smart Sort Premium](http://dyasonhat.com/wp-smart-sort-premium/ "WP Smart Sort Premium") version.

Support this Plugin with your donation.

To View the changelog visit the plugin page.



== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Unzip the file and upload entire `wp-smart-sort-premium` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. View the plugin help menu once activated for further information on using WPSS

== Frequently Asked Questions ==

= Can I sort by Custom Fields =

Yes, you can sort by any field in the WP-posts table or the custom field table.

= Can I set the default Sort Direction for my blog =

Yes, in the options page you can choose a default sort direction from any of your selected fields.

= How do i include this sort drop down =

The plugin is widgetized and you can simply add the widget to your side bar. 

Alternatively you can call the sort dropdown any where in you theme by including the following.
‹?php if (class_exists('WP_Smart_Sort')) { 
        $wpss = new WP_Smart_Sort();
        if (method_exists($wpss,'placesort')) {
           $wpss->placesort();
        }
    } ?›
    
== Screenshots ==

None Yet