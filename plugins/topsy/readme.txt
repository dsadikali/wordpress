=== Plugin Name ===
Contributors: topsylabs
Tags: topsy, twitter, widget, trackback, trackbacks, comment, comments, tweetbacks, tweet, tweets, retweet, retweets, otterapi
Requires at least: 2.0
Tested up to: 2.9.2
Stable tag: 1.2.7

Provides a Twitter retweet button for your posts that shows all tweets, enables retweeting, and adds tweets as comments.

== Description ==

This plugin integrates the [Topsy Retweet Button](http://labs.topsy.com/button/) with WordPress blogs. The Topsy Retweet Button provides the following features:

* A Twitter retweet button that shows tweet counts and allows retweeting.
* Highly customizable buttons can be displayed in various colors.
* Automatic URL shortening using bit.ly, tr.im, and others including support for your own shortener API key or username where available.
* Option to add tweets as comments or trackbacks.
* Designed for high volume sites.
* "Streaming" feature allows retweet buttons on your site to automatically update the tweet count as more people retweet your posts.
* Made to load fast, and to not interfere with your page's loading or display.
* Works on all modern browsers (MSIE 7 and up, Firefox 3 and up, google Chrome, and Safari), and works on major smartphone browsers (iPhone, Android, and Palm Pr&#275;).
* Displays Topsy TopLinks badges for posts that are in the Topsy Top5k or above - http://labs.topsy.com/toplinks
* Topsy doesn't delete old tweets, so the plugin will work on all of your historical posts.
* Free analytics via Topsy trackback pages - for example, see  http://topsy.com/tb/www.techcrunch.com or http://topsy.com/tb/www.techcrunch.com/2009/11/03/topsy-twitter-bit-ly-tweetmeme-retweet/
* Allows you to choose the username used as the source for retweets from your blog - e.g. "RT @&lt;your Twitter account&gt; ..."
* Allows you to select hashtags to be used with retweets, or have hashtags created based on the post's WordPress tags.
* Buttons can be placed on main blog page, static content page, individual entries, category listings, and archive pages. Can skip buttons from particular categories, or from individual posts.
* Small and large size buttons and button position customization via settings, shortcodes, and manual positioning.
* Supports WordPress 2.0, PHP4 and above.

== Installation ==

1. Download and extract the plugin.
2. Copy the "topsy" folder to your "/wp-content/plugins/" directory.
3. Activate the plugin through the "Plugins" menu.
4. Look at the "Topsy Widgets" Settings page. Activate trackback comments, if desired; adjust retweet button settings as desired, etc.

= Upgrade Instructions =

When the plugin is updated, you'll be notified in your dashboard. Use the "upgrade automatically" link to upgrade. If that fails, you can download and extract the new plugin and copy the new "topsy" folder into your plugins directory.

== Frequently Asked Questions ==

= How do I check Topsy service status? =

Topsy provides service status updates via [http://status.topsy.com](http://status.topsy.com)

= How do I get support? =

If you have questions or technical problems, please email support@topsy.com.

= How do I turn off the Topsy button on just one category? =

You must have more than just the standard "Uncategorized" category for this feature to work.

When you have more than one category, all of your categories will be listed in the "Exclude Categories" section of the Topsy settings page. turn on the check box for any category that you want to suppress buttons on.

= How do I turn off the button on a single post? =

Give the post a [custom field](http://codex.wordpress.org/Custom_Fields) with the key "topsy_button", and the value "suppress". You can do this for as many posts as you want. If you remove the custom field, the button will re-appear on that post.

Note that the `[topsy_retweet_small]` or `[topsy_retweet_big]` shortcodes are not affected by this custom field.

= Using version 1.2.3 or greater, I see code in my pages that looks like topsyWidgetData({[lots of gibberish here]}). =

Certain themes use calls to the PHP `strip_tags()` function, which conflicts with the plugin's "preload retweet button" feature. The most popular of these themes are Redtime, Thesis, and Arras. If you're using one of those themes -- or if you see topsyWidgetData gibberish code in your posts -- you should turn off preloading. Look under "Visual Appearance" in the Topsy settings page, and uncheck the "Preload static retweet button" feature.

= My pages look fine until the first trackback comment, then everything after that is all messed up! =

There is a bug in versions 1.2.5 and lower: The trackback comments open three &lt;span&gt; tags, but close only two of them. If your blog theme wraps comments in some other tag, you may not be affected by this problem.

To fix it: Upgrade to the latest version. Then click the "Delete Topsy-Created Trackback Comments" button at the bottom of your Topsy settings page. This will clear out the old trackback comments, and new ones will be created over the next day or so, as users and search engines visit your site.

= If I deactivate or remove the plugin, what happens to my trackback comments? =

Trackback comments are inserted into the WP comments database after passing moderation. Once they are in the WP database, they exist on their own. Deactivating or removing the plugin will have no effect on them.

If you want to remove all trackback comments, you can use the "Delete Trackback Comments" button. This will not affect comments not posted by the plugin.

= When someone links to a post of mine, how quickly will the trackback comment appear? =

Each time a blog page is loaded, Topsy checks for new tweets about that page and adds them as comments.

= I deleted one of the trackback comments, but it keeps coming back. Help! =

While the plugin is activated, it will re-generate trackback comments every time a blog page is loaded.

If you want to block a comment from appearing, you can use the "Unapprove" feature in the WordPress dashboard. An unapproved comment will not be displayed.

= My trackback comments don't show up. I just see the author's name, which is a hyperlink to the original tweet... but the tweet text doesn't show up on my blog. =

Some WordPress themes omit the text of trackbacks, and if you're using one of those themes, trackbacks may not display properly. In this case, you can turn on full-text trackback comments by enabling the "Display trackback comments as comments" option on the Topsy Widgets Settings page.

After changing the setting for comments, you can remove existing blank trackbacks with the "Delete Trackback Comments" button. When the plugin regenerates the comments, they should appear as full text comments.

= I want to put the Topsy buttons into my blog's theme, so they display in places other than at the beginnings and endings of posts. =

You can place the Topsy buttons anywhere you want by using the PHP command `<?php echo topsy_retweet_small() ?>` or `<?php echo topsy_retweet_big() ?>`. However, if you deactivate or remove the plugin, those commands will fail. To make it so these commands don't mess things up, try using the following versions:

`<?php if (function_exists('topsy_retweet_big')) echo topsy_retweet_big(); ?>`

to produce a large button, or

`<?php if (function_exists('topsy_retweet_small')) echo topsy_retweet_small(); ?>`

for a small one. These versions will produce nothing (not even a "Fatal Error" message) if you deactivate or remove the plugin.

= I want to use a shortcode button and have my blog text wrap around it. How can I style a shortcode button, like the "Additional CSS" option in the main buttons? =

Shortcode buttons will automatically be contained in a DIV with the class "topsy_widget_shortcode". You can modify your blog's CSS file(s) to apply styling to that class. For example, the CSS declaration:

    div.topsy_widget_shortcode {
        border: 3px solid #090;
        display: inline
    }

...will make your widget sit in amongst the rest of your paragraph text, and give it a green border. (This is not guaranteed to look good. It's just an example.)

= I upgraded to plugin version 1.2.x, and all my Topsy buttons went away. =

There are two possible problems:

1. **You're using an HTML-minifying or -compressing plugin, or a WordPress theme that uses strip\_tags() calls**

    Version 1.2.0 of the Topsy plugin works adding invisible <script> tags to your blog's code, while by version 1.2.1 uses HTML comments. Some themes strip out <script> tags, and there are a variety of plugins that try to speed up page loads by stripping comments from the HTML.
    
    These problems are fixed in Topsy plugin version 1.2.2. If you are using 1.2.0 or 1.2.1, **please upgrade to the latest version** instead. If the buttons still don't show up, clear your browser's cache.

1. **Your WordPress theme doesn't include the `wp_head()` function**

    The `wp-head()` function is required for many, many plugins, including the Topsy Retweet Button. Look in your theme's header.php file. If you don't find the text "wp_head()" anywhere in there, you'll need to either switch to another theme, or try to add the function yourself.
    
    To add it, just add a new line of text right before the `</head>` tag. Make it say:
    
    `<? wp_head(); ?>`
    
    Save the header.php file and refresh your blog pages in your browser. The Topsy buttons should now be visible.

= I used one of the 0.9.x releases, and have trackback comments in old styles (with "Original tweet" and "Topsy page" links). How can I make my old comments like the new ones? =

You can remove old style comments with the "Delete Trackback Comments" button. The plugin will automatically regenerate comments in the new style.

== Screenshots ==

1. Small trackback/retweet button on http://www.techcrunch.com
2. Large trackback/retweet button on [http://crunchies2009.techcrunch.com/vote/](http://crunchies2009.techcrunch.com/vote/)
3. Many colors of buttons are available (both large and small).
4. Trackback comment created by the Topsy Retweet Button.

== Changelog ==

= 1.2.7 - 2010-05-12 =

Bug-fix release: Improved resilience against Topsy server failures/slowdowns (this resolves the "unable to connect to otter.topsy.com:80" problem). Improved settings page text regarding "Preload" feature.

= 1.2.6 - 2010-05-04 =

Bug-fix release: Trackback comments now have all tags closed properly.

= 1.2.5 - 2010-04-28 =

Bug-fix release: Resolved issue where posts with quotation marks in their titles would have no tweet count if preloading was turned on.

= 1.2.4 - 2010-04-26 =

Minor bug-fix release. Auto-hashtag from post tags no longer creates #post hashtag when post has no tags. Fixed cosmetic issue on settings page when is.gd URL shortener is used. "Reset" button now works. Fixed default setting for "Element Selection and Order".

= 1.2.3 - 2010-04-13 =

Many, many new features! Streaming: Retweet count can update in real time as more people retweet. Retweet button preloading. Buttons can now be omitted from entire categories, or from individual posts. Can also omit any sub-part of widget (count, retweet button, or badge). Can automatically include hashtags based on post tags.

= 1.2.2 - 2010-03-02 =

Bug-fix release: Topsy plugin is now compatible with HTML compressor/minifier plugins. New feature: "Settings" link now shows up in main Plugins dashboard. All 1.2.x users are advised to upgrade to this release.

= 1.2.1 - 2010-02-22 =

Immediate bug-fix release to resolve issue where some themes would display raw JSON code.

= 1.2.0 - 2010-02-22 =

Major back-end changes: buttons will no longer delay page loading. Also fixed "call_user_func_array()" warning message when deactivating plugin.

= 1.1.3 - 2009-12-24 =

Provides support for WordPress 2.9; resolves fatal error "cannot redeclare class services_json". Plugin name changed from Topsy Widgets to Topsy Retweet Button.

= 1.1.2 - 2009-12-22 =

Bug-fix release, resolving multiple issues: Blog titles in retweets are no longer truncated if they contain ampersands. CSS now applies correctly to buttons produced by shortcodes. Retweet button URLs now pass XHTML strict validation. Retweet Username control now auto-corrects data when saving form data.

= 1.1.1 - 2009-12-17 =

New feature: Customizable Retweet buttons! Users can now select button color and change button text. Also improved post title detection ability.

= 1.1.0 - 2009-12-04 =

Major new feature: Automatic URL shortening using bit.ly, tr.im and others including support for your own shortener API key or username where available. Default CSS is now fully XHTML-compliant. Fix for "require_once(/JSON.php) failed to open stream: No such file or directory" error on Windows servers. "Ignore tweets by username" is now case insensitive.

= 1.0.3 - 2009-11-13 =

Final resolution for "Missing argument 2 for topsy\_unpublish\_post\_hook()" warning message; auto-notify of unpublishing an article now works properly. Added explicit licensing under GNU GPL. Added ability to reorder elements in small retweet button.

= 1.0.2 - 2009-11-11 =

Bug fix release: temporary fix for "Missing argument 2 for topsy\_unpublish\_post\_hook()" warning message. Also enabled support for PHP4.

= 1.0.1 - 2009-11-09 =

Can now put retweet button on RSS feeds as well as web pages. Added option to notify Topsy of post publication/deletion. Improved handling of duplicate detection. Better messaging regarding trackbacks vs. comments. Fixed backward-compatibility issue caused by shortcodes.

= 1.0.0 - 2009-11-03 =

Major release! Added retweet button. Added "Delete Trackback Comments" button. Fixed duplicate trackbacks bug. Removed "Original tweet/Topsy page" links, and changed default settings so user must explicitly activate trackback comments.

= 0.9.4 - 2009-11-02 =

Test to verify WordPress distribution mechanisms.

= 0.9.3 - 2009-10-22 =

Bug fix: Resolved issue where "Topsy page" trackback link from pages with ? or + in their URL showed no trackbacks.

= 0.9.2 - 2009-10-16 =

Added "ignore list" feature. Added option to display trackbacks as comments, not just trackbacks. Added auto-linking of @usernames and #hashtags. General code cleanup.

= 0.9.1 - 2009-10-13 =

Revised format of trackback comment text to remove "On Twitter..." lead-in. Added rate limiting support.

= 0.9.0 - 2009-10-09 =

Initial release.