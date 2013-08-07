=== Cool Video Gallery ===
Contributors: Praveen Rajan
Tags: video gallery,videos,gallery,media,player,flash player,flash-player,skins,flash player skins,admin,post,pages,pictures,widgets,picture,video,cool-video-gallery,cool video gallery,ffmpeg,showcase,shadowbox,preview image,upload,flv,mp4,mov,mp3,H.264
Requires at least: 3.0.1
Tested up to: 3.0.5
Stable tag: 1.3

Cool Video Gallery is a Video Gallery plugin for WordPress with option to upload videos and manage them in multiple galleries. 

== Description ==

Cool Video Gallery is a Video Gallery plugin for WordPress with option to upload videos, manage them in multiple galleries and automatic preview image generation for uploaded videos.
Option also provided to upload images for video previews. Supports '.flv', '.mp4', '.mov' and '.mp3' video files presently. 

Support Forum Link:
<a href="http://wordpress.org/tags/cool-video-gallery?forum_id=10">Support Forum</a> 

= Features =
* Supports H.264 (.mp4, .mov), FLV (.flv) and MP3 (.mp3) files.
* Upload videos and manage videos in different galleries.
* Multiple video upload feature available.
* Automatic generation of preview images for videos using FFMPEG installed in webserver.
* Manual upload feature to upload preview image for videos if FFMPEG is not installed.
* Bulk deletion of videos/galleries.
* Option to add title/description for galleries.
* Playback feature for videos uploaded in a popup.
* Option to set width/height, zoom-crop, quality of preview images uploaded and other features available.
* Video player options like skin selection, default volume setting, autoplay feature and many other features available.
* Widgets for Slideshow and Showcase feature available.
* Shortcode feature integration for gallery/video with posts/pages. 
* Feature to scan gallery folders for newly added videos through FTP. 
* Feature to sort videos in a gallery.
* Play all videos in a gallery with navigation enabled in shadowbox popup. 
* Plugin Uninstall feature enabled.
* Google XML Video Sitemap generation feature integrated.


If you find this plugin useful please provide your valuable ratings.

= Check out my other plugin =
* <a href="http://wordpress.org/extend/plugins/attachment-file-icons">Attachment File Icons (AF Icons)</a> - A plugin to display file type icons adjacent to files added to pages/posts/widgets. Feature to upload icons for different file types provided.

== Installation ==

1. Upload `cool-video-gallery` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Add a gallery and upload some videos from the admin panel.
4. Use either `CVG Slideshow` or `CVG Showcase` widget to play slideshow of uploaded videos in a gallery. 
5. Go to your post/page and enter the tag `[cvg-video videoId='vid' /]` (where vid is video id) to add video or enter the tag `[cvg-gallery galleryId='gid' /]` (where gid is gallery id) to add a complete gallery.
6. Inorder to use slideshow and showcase in custom templates created use the function `cvgShowCaseWidget(gid)` and `cvgSlideShowWidget(gid)` (where gid is gallery id).

== Screenshots ==

1. Screenshot Admin Section - Add Galleries
2. Screenshot Admin Section - Upload Videos 
3. Screenshot Admin Section - Gallery Details
4. Screenshot Admin Section - Sort Videos in Gallery
5. Screenshot Admin Section - Gallery Settings
6. Screenshot Admin Section - Player Settings
7. Screenshot Slideshow Widget
8. Screenshot Video Player with gallery navigation

== Changelog ==

= 1.3 =
* '.mov' and '.mp3' media file supports added.
* Added patch for thumbnail generation.
* Added uninstall option for plugin.
* Added fix for plugin upgrade issue.

= 1.2 =
* Added feature to sort videos in a gallery
* Navigation feature enabled in shadowbox popup to move acrosss videos in current gallery selected.
* Issue with 'jpeg/jpg' extension thumbnail fixed. '.png' image files currently accepted for thumbnail images.

= 1.1 =
* Added feature to scan video gallery folder and add newly added videos through FTP access.
* Shortcode feature added to support video gallery in post/page content.

= 1.0 =
* Initial version