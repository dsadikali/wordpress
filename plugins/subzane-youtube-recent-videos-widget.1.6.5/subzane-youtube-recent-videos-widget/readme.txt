=== SubZane YouTube Plugin ===
Contributors: Andreas Norman
Donate link: 
Tags: widget, rss, youtube, video, feed
Requires at least: 2.5
Tested up to: 3.0.4
Stable tag: 1.6.5

This plugin can allows you to display a thumbnail list of YouTube videos in your sidebar.
You can also add custom lists to your posts and pages using shortcode.

== Description ==

This plugin can allows you to display a thumbnail list of YouTube videos in your sidebar.
You can also add custom lists to your posts and pages using shortcode.

*	The number of recent videos displayed can be configured
*	List videos uploaded by a specific user.
*	List videos tagged as favorites by a specific user.
*	Search for videos and display the results in a widget or a post/page.
*	Order the results by published, viewCount, rating or relevance.
*	Lightbox support with Shadowbox JS http://wordpress.org/extend/plugins/shadowbox-js/
*	Use short codes to add youtube video listings into your posts.

== Installation ==

1. Upload the folder `subzane_youtube_plugin` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the widget to your sidebar
4. For lightbox support download and install Shadowbox JS http://wordpress.org/extend/plugins/shadowbox-js/
5. To add listing inside posts use the shortcode [sz-youtube]:

= Shortcode syntax =

Write like this [sz-youtube value="subzane" type="favorites" max="5" sortorder="viewCount"]

Params:
* value = a username or a tag
* type = tag/user/favorites
* max = Max number of videos to list.
* sortorder = published, relevance, viewCount or rating
* autoplay = Autoplay videos in lightbox or not (1/0)
* related = Display related videos or not (1/0)
* lightbox = Use lightbox or not (1/0)
* aspect = Aspect ratio (4:3, 16:9 or 16:10)
* width = Width of the video. Height is calculated from width and aspect.
* hd = HD Video if available (1/0)

== Changelog ==

= 1.6.5 = 
* Fixed bug that caused giant thumbnails

= 1.6.4 = 
* More bug fixes regarding line 85 and 88 that's been posted on the blog.
* Added option to list the full feed by setting the "Max number of videos" to 0 (zero).

= 1.6.3 = 
* Fixed another bug with playlists not working.  
* Still has issues with listing fewer videos than set when videos in list are unavailable.

= 1.6.2 = 
* Fixed bug with playlists not working.  


== Screenshots ==

1. Example of video listing.
2. Widget configuration


== FAQ ==

For any questions or suggestions regarding the plugin please visit http://www.andreasnorman.se