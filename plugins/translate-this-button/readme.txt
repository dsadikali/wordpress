=== Translate This Button ===
Contributors: JonRaasch
Author: JonRaasch (Jon Raasch)
Author URI: http://jonraasch.com/
Plugin URI: http://translateth.is/wordpress/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=4URDTZYUNPV3J&lc=US&item_name=Jon%20Raasch&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags: translate, widget, translation, languages, international, internationalize, language, AJAX, javascript, multi-lingual
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: 0.1

Installs the TranslateThis Button, a Javascript/AJAX translation widget.  Provides 52 languages of translation with many customizable options.

== Description ==

The TranslateThis Button for WordPress installs a [Javascript translation widget](http://translateth.is) that provides translation into 52 different languages using the Google Language API.  The widget can be included either using template tags or the widgetized sidebar.

The plugin provides a variety of display, language and functionality options for the TranslateThis Button.  In the settings page you can:

*   Use custom imagery or disable imagery altogether
*   Modify the UI text
*   Enable Google Analytics tracking
*   Restrict the scope of the translation
*   Disable the cookie that automatically translates entire site
*   Change the languages used in the dropdown
*   And much more

Read the [complete documentation](http://translateth.is/wordpress/docs "Read the complete documentation") for more information.

== Installation ==

= Basic installation of The TranslateThis Button for WordPress is simple: =

1. Upload the folder `translate-this-button` into the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Navigate to Appearance > Widgets, and drag the TranslateThis Button widget into your widgetized sidebar.

4. Alternately use the template tag `<?php translate_this_button(); ?>` anywhere in your template files (if not using the sidebar widget)

5. A variety of options can be changed in the Plugins > TranslateThis Button Settings menu


For details on the TranslateThis Button Settings, read the [complete documentation](http://dev.jonraasch.com/yafpp/docs "Read the complete documentation")

== Frequently Asked Questions ==

= Why does the text link show before the button loads =

The TranslateThis Button has to wait for the page to load completely before it can activate the translation widget.  This means that while the page loads, a text link will show instead of the button.  This only becomes noticeable if the page loads slowly, but to avoid this, attach this class via CSS:

`#translate-this .translate-this-button { visibility: hidden; }`


= Why does the TranslateThis Button translate all the pages on my site? =

By default the TranslateThis Button uses a cookie to translate all the pages on your site, once a user has selected to translate a given page.  If the user cancels or undoes the translation, this cookie is removed. 

If you would like to disable this feature, simply uncheck "Use Cookie" in the TranslateThis Button Setting page.  This will make it so that the widget only translates the given page that a user selects to translate, and not the subsequent pages they visit on your site.

== Screenshots ==

1. The TranslateThis Button Dropdown - you can customize the languages
2. The all languages overlay (52 languages)
3. TranslateThis Button Settings Page
4. Additional dropdown options - select supported languages

== Changelog ==

= 0.1 =

First release of the TranslateThis Button for WordPress.  Contains a variety of display, language and functionality options as well as a sidebar widget.

= Javascript Widget Changelog =

The changelog for the Javascript widget used by this plugin is [available here](http://translateth.is/docs#changelog).

== License ==

Copyright 2010 Jon Raasch - The TranslateThis Button for WordPress is released under the FreeBSD License - [License details](http://translateth.is/wordpress/docs#license)

This plugin leverages the TranslateThis Button script, which has it's own licensing and terms of use.  [Please see here for details](http://translateth.is/tos).