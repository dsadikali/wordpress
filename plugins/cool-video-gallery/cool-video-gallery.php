<?php
/*
Plugin Name: Cool Video Gallery
Description: Cool Video Gallery, a video gallery plugin to manage video galleries. Feature to upload videos and group them into galleries is available. Option to playback uploaded videos in a Shadowbox popup available. Supports '.flv', '.mp4', '.mov' and '.mp3' files playback. 
Version: 1.3
Author: Praveen Rajan
License: GPL2
	Copyright 2011  Praveen Rajan  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
global $wp_version;
if (version_compare ( $wp_version, "3.0", "<" )) { 
	wp_die("This plugin requires WordPress version 3.0.1 or higher.");
}

if ( !class_exists('CoolVideoGallery') ) {
	/**
	 * Class declaration for cool video gallery
	 * @author Praveen Rajan
	 *
	 */
	class CoolVideoGallery{
		
		// this variable will hold url to the plugin
		var $plugin_url;
		var $table_gallery;
		var $table_videos;
		var $default_gallery_path;
		var $winabspath;
		var $video_player_path;
		var $video_player_url;
		var $video_id;
		var $cvg_version = '1.3';
		
		// Initialize the plugin
		function CoolVideoGallery(){
			
			$this->plugin_url = trailingslashit( WP_PLUGIN_URL . '/' .	dirname( plugin_basename(__FILE__)));
			$this->video_player_url = trailingslashit( WP_PLUGIN_URL . '/' .	dirname( plugin_basename(__FILE__)) . '/cvg-player' );
			$this->video_player_path = trailingslashit( WP_CONTENT_DIR . '/plugins/' .	dirname( plugin_basename(__FILE__)) . '/cvg-player/' );
			
			$this->table_gallery = '';
			$this->table_videos = '';
			$this->video_id = '';
			
			if (function_exists('is_multisite') && is_multisite()) {
				$this->default_gallery_path = get_option('upload_path') . '/video-gallery/' ;
			}else{
				$this->default_gallery_path =  'wp-content/uploads/video-gallery/';
			}
		
			$this->winabspath =  str_replace("\\", "/", ABSPATH);
			
			$this->load_video_files();
			
			//adds scripts and css stylesheets
			add_action('wp_print_scripts', array(&$this, 'gallery_script'));
			
			//adds admin menu options to manage
			add_action('admin_menu', array(&$this, 'admin_menu'));
			
			//adds contextual help for all menus of plugin
			add_action('admin_init',  array(&$this, 'add_gallery_contextual_help'));
			
			//adds player options to head
			add_action('wp_head', array(&$this, 'addPlayerHeader'));
	 		add_action('admin_head', array(&$this, 'addPlayerHeader'));
	 		
	 		//adds filter for post/page content
	 		add_filter('the_content',  array(&$this, 'CVGVideo_Parse'));
	 		
	 		add_filter('the_content',  array(&$this, 'CVGGallery_Parse'));
	 		
		}
		
		/**
		 * Function to install cool video gallery plugin
		 * @author Praveen Rajan
		 */
		function cvg_install(){
			global $wpdb;
			
			if (function_exists('is_multisite') && is_multisite()) {
				// check if it is a network activation - if so, run the activation function for each blog id
				if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
					
					$old_blog = $wpdb->blogid;
					// Get all blog ids
					$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						$this->_cvg_activate();
					}
					switch_to_blog($old_blog);
					return;
				}
			}
			$this->_cvg_activate();
			
		}		
		
		/**
		 * Function to create database for plugin.
		 * @author Praveen Rajan
		 */
		function _cvg_activate() {
			
			global $wpdb;
			
	        $sub_name_gallery = 'cvg_gallery';
	        $sub_name_videos = 'cvg_videos';
	        
	        $this->table_gallery  = $wpdb->prefix . $sub_name_gallery;
	        $this->table_videos = $wpdb->prefix . $sub_name_videos;
	        
			if($wpdb->get_var("SHOW TABLES LIKE '$this->table_gallery'") != $this->table_gallery) {
			
				$sql = "CREATE TABLE " . $this->table_gallery . " (
						 	  `gid` bigint(20) NOT NULL auto_increment,
							  `name` varchar(255) NOT NULL,
							  `path` mediumtext,
							  `title` mediumtext,
							  `galdesc` mediumtext,
							  `author` bigint(20) NOT NULL default '0',
							  PRIMARY KEY  (`gid`)
						);";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$this->table_videos'") !=  $this->table_videos) {
				
					$sql_video = "CREATE TABLE " .  $this->table_videos . " (
							 		`pid` bigint( 20  ) NOT NULL AUTO_INCREMENT  ,
									`galleryid` bigint( 20 ) NOT NULL DEFAULT '0',
									`filename` varchar( 255 ) NOT NULL ,
									`thumb_filename` varchar( 255 ) NOT NULL ,
									`description` mediumtext,
									`sortorder` BIGINT( 20 ) NOT NULL DEFAULT '0',
									`alttext` mediumtext,
									`videodate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
									`meta_data` longtext,
									PRIMARY KEY ( `pid` )
							);";
					require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
					dbDelta($sql_video);
			}
			
			 $installed_ver = get_option( "cvg_version" );
			 
			 // For version 1.2
			 if (version_compare($installed_ver, '1.3', '<')) {
			 	$sql_update = "ALTER TABLE " .  $this->table_videos . " ADD `sortorder` BIGINT( 20 ) NOT NULL DEFAULT '0' AFTER `description`" ;
			 	$wpdb->query($sql_update);
			 }
			

			//Section to save gallery settings.
			$options = array();
			$options['max_cvg_gallery'] = 10;
			$options['max_vid_gallery'] = 10;
			$options['cvg_preview_height'] = 100;
			$options['cvg_preview_width'] = 100;
			$options['cvg_preview_quality'] = 70;
			$options['cvg_zc'] = 0;
			$options['cvg_slideshow'] = 7000;
			
			update_option('cvg_settings', $options);
			
			//Section to save player settings.
			$options_player = array();
			$options_player['cvgplayer_width'] = 400;
			$options_player['cvgplayer_height'] = 400;
			$options_player['cvgplayer_skin'] = '';
			$options_player['cvgplayer_volume'] = 70;
			$options_player['cvgplayer_fullscreen'] = 1;
			$options_player['cvgplayer_controlbar'] = 'bottom';
			$options_player['cvgplayer_autoplay'] = 1;
			$options_player['cvgplayer_mute'] = 0;
			$options_player['cvgplayer_stretching'] = 'none';
			
			update_option('cvg_player_settings', $options_player);
			update_option('cvg_version', $this->cvg_version);
		}
		
		/**
		 * Function to deactivate plugin
		 * @author Praveen Rajan
		 */
		function cvg_deactivate_empty() {
			
		}
		/**
		 * Function to uninstall plugin
		 * @author Praveen Rajan
		 */
		function cvg_uninstall(){
			
			global $wpdb;
			
			if (function_exists('is_multisite') && is_multisite()) {

				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_cvg_deactivate();
				}
				switch_to_blog($old_blog);
				return;
			}
			
			$this->_cvg_deactivate();
			
		}
		
		/**
		 * Function to delete tables of plugins
		 * @author Praveen Rajan
		 */
		function _cvg_deactivate() {
			
			global $wpdb;
			$sub_name_gallery = 'cvg_gallery';
	        $sub_name_videos = 'cvg_videos';
	        
	        $this->table_gallery  = $wpdb->prefix . $sub_name_gallery;
	        $this->table_videos = $wpdb->prefix . $sub_name_videos;
	        
		  	$wpdb->query("DROP TABLE $this->table_gallery");
		  	$wpdb->query("DROP TABLE $this->table_videos");
		  	
		  	if (function_exists('is_multisite') && is_multisite()) {
				$gallery_path = get_option('upload_path') . '/video-gallery/' ;
			}else{
				$gallery_path =  'wp-content/uploads/video-gallery/';
			}
			CvgCore::deleteDir( ABSPATH . $gallery_path ); 
			
		}
		
		/**
		 * Function to add main menu and submenus to admin panel
		 * @return adds menu
		 * @author Praveen Rajan
		 */
		function admin_menu() {
			
			add_menu_page('Video Gallery Overview', 'Video Gallery', 'manage_options', 'cvg-gallery-overview' , array(&$this, 'gallery_overview'), $this->plugin_url .'/images/video_small.png');
			add_submenu_page( 'cvg-gallery-overview', 'Video Gallery Overview', 'Overview', 'manage_options', 'cvg-gallery-overview',array(&$this, 'gallery_overview'));
			add_submenu_page( 'cvg-gallery-overview', 'Add Gallery / Upload Videos', 'Add Gallery / Videos', 'manage_options', 'cvg-gallery-add',array(&$this, 'gallery_add'));
			add_submenu_page( 'cvg-gallery-overview', 'Manage Video Gallery', 'Manage Gallery', 'manage_options', 'cvg-gallery-manage',array(&$this, 'gallery_manage'));
			add_submenu_page( 'cvg-gallery-overview', 'Gallery Details', 'Gallery Details', 'manage_options', 'cvg-gallery-details',array(&$this, 'gallery_details'));
			add_submenu_page( 'cvg-gallery-overview', 'Gallery Sort', 'Gallery Sort', 'manage_options', 'cvg-gallery-sort',array(&$this, 'gallery_sort'));
			add_submenu_page( 'cvg-gallery-overview', 'Gallery Settings', 'Gallery Settings', 'manage_options', 'cvg-gallery-settings',array(&$this, 'gallery_settings'));
			add_submenu_page( 'cvg-gallery-overview', 'Video Player Settings', 'Video Player Settings', 'manage_options', 'cvg-player-settings',array(&$this, 'player_settings'));
			add_submenu_page( 'cvg-gallery-overview', 'CVG Google XML Video Sitemap', 'Google XML Video Sitemap', 'manage_options', 'cvg-video-sitemap',array(&$this, 'video_sitemap'));
			add_submenu_page( 'cvg-gallery-overview', 'CVG Uninstall', 'Uninstall CVG', 'manage_options', 'cvg-plugin-uninstall',array(&$this, 'uninstall_plugin'));
		}
		
		/**
		 * Function to add contextual help for each menu of plugin page.
		 * @return contextual help content
		 * @author Praveen
		 */
		function add_gallery_contextual_help(){
			
			$help_array = array('toplevel_page_cvg-gallery-overview', 'video-gallery_page_cvg-gallery-add', 'video-gallery_page_cvg-gallery-manage', 'video-gallery_page_cvg-gallery-details', 'video-gallery_page_cvg-gallery-sort', 'video-gallery_page_cvg-gallery-settings', 'video-gallery_page_cvg-player-settings', 'video-gallery_page_cvg-plugin-uninstall', 'video-gallery_page_cvg-video-sitemap' );
			foreach($help_array as $help) {
				
				switch($help) {
					case 'toplevel_page_cvg-gallery-overview':
										$help_content = '<p><strong>Cool Video Gallery - Overview</strong></p>';
										$help_content .= '<p>This page shows a brief about the total number of gallery and videos added using this plugin. Server information is also provided to denote the maximum file upload limit of PHP. Inaddition to this it shows whether <b>FFMPEG</b> is installed in the webserver. Preview images are automatically generated for videos added if FFMPEG is installed. Otherwise images should be manually uploaded for videos added.</p>';
										$help_content .= '<p><b>Instructions to use <i>Cool Video Gallery</i>:</b></p>';
										$help_content .= '<p><ol><li> Add a gallery and upload some videos from the admin panel to that gallery.</li>'.
														 '<li>Use either `<b>CVG Slideshow</b>` or `<b>CVG Showcase</b>` widget to play slideshow of uploaded videos in a gallery.</li>'.	
														 '<li>Go to your post/page and enter the tag `<b>[cvg-video videoId=\'</b>vid<b>\' /]</b>` (where vid is video id) to add video '.
														 'or enter the tag `<b>[cvg-gallery galleryId=\'</b>gid<b>\' /]</b>` (where gid is gallery id) to add a complete gallery.</li>'.			
														 '<li>Inorder to use slideshow and showcase in custom templates created use the function `<b>cvgShowCaseWidget(</b>gid<b>)</b>` and `<b>cvgSlideShowWidget(</b>gid<b>)</b>` (where gid is gallery id).</li></ol></p>';
										
										break;
										
					case 'video-gallery_page_cvg-gallery-add':
										$help_content = '<p><strong>Cool Video Gallery -  Add Gallery / Upload Videos</strong></p>';
										$help_content .= '<p>This page provides two tabs to add gallery and upload videos. `Add new gallery` tab provides option to add new video galleries and `Upload videos` tab provides option to upload mulitple videos to a selected gallery.</p>';
										break;	
					case 'video-gallery_page_cvg-gallery-manage':
										$help_content = '<p><strong>Cool Video Gallery - Manage Video Gallery</strong></p>';
										$help_content .= '<p>Lists the different galleries created and shows a brief about each gallery denoting the no. of videos, author of gallery, description of gallery and option to delete a gallery. Option provided to perform bulk deletion of galleries. Pagination feature added for gallery listing.</p>';
										break;
					case 'video-gallery_page_cvg-gallery-details':
										$help_content = '<p><strong>Cool Video Gallery - Gallery Details</strong></p>';
										$help_content .= '<p>Displays the details of a particular gallery. Top section shows the name and description of the gallery which can be updated. Details of all the videos uploaded to a certain gallery is listed below this. Bulk deletion and sorting of videos is provided as other options.</p>';
										break;	
					case 'video-gallery_page_cvg-gallery-sort':
										$help_content = '<p><strong>Cool Video Gallery - Gallery Sorting</strong></p>';
										$help_content .= '<p>Options to sort videos in a gallery. Sort by Video ID, Video Name or drag-drop to change video order.</p>';
										break;
					case 'video-gallery_page_cvg-gallery-settings':
										$help_content = '<p><strong>Cool Video Gallery - Video Gallery Settings</strong></p>';
										$help_content .= '<p>Shows the different options available for listing and managing a gallery.</p>';
										break;	
					case 'video-gallery_page_cvg-player-settings':
										$help_content = '<p><strong>Cool Video Gallery - Video Player Settings</strong></p>';
										$help_content .= '<p>Options to manage different options of video player is provided here.</p>';
										break;
					case 'video-gallery_page_cvg-video-sitemap':
										$help_content = '<p><strong>Cool Video Gallery - Generate Google XML Video Sitemap</strong></p>';
										$help_content .= '<p>Option to generate XML Sitemap for videos.</p>';
										break;						
					case 'video-gallery_page_cvg-plugin-uninstall':
										$help_content = '<p><strong>Cool Video Gallery - Uninstall plugin</strong></p>';
										$help_content .= '<p>Option to uninstall plugin.</p>';
										break;	
															
				}
				
				add_contextual_help( $help, $help_content );
			}	
		}
		
		/**
		 * Function to include gallery overview page
		 * @return includes file content
		 * @author Praveen Rajan
		 */
		function gallery_overview() {
			include('admin/gallery-overview.php');
		}

		/**
		 * Function to include gallery add page
		 * @return includes file content
		 * @author Praveen Rajan
		 */
		function gallery_add() {
			include('admin/gallery-add.php');
		}
		
		/**
		 * Function to include gallery manage page
		 * @return includes file content
		 * @author Praveen Rajan
		 */
		function gallery_manage() {
			include('admin/gallery-manage.php');
		}
		
		/**
		 * Function to include gallery details page
		 * @return includes file content
		 * @author Praveen Rajan
		 */
		function gallery_details() {
			include('admin/gallery-details.php');
		}
		
		/**
		 * Function to include gallery details page
		 * @return includes file content
		 * @author Praveen Rajan
		 */
		function gallery_sort() {
			include('admin/gallery-sort.php');
		}
		
		
		/**
		 * Function to include gallery settings page
		 * @return includes file content
		 * @author Praveen
		 */
		function gallery_settings() {
			include('admin/gallery-settings.php');
		}
		
		/**
		 * Function to include player settings page
		 * @return includes file content
		 * @author Praveen Rajan
		 */
		function player_settings() {
			include('admin/player-settings.php');	
		}
		
		/**
		 * Function to include video xml sitemap page
		 * @return includes file content
		 * @author Praveen Rajan
		 */
		function video_sitemap() {
			include('admin/video-sitemap.php');	
		}
		/**
		 * Function to include plugin uninstall page
		 * @return includes file content
		 * @author Praveen Rajan
		 */
		function uninstall_plugin(){
			include('admin/plugin-uninstall.php');	
		} 
		
		/**
		 * Function to include scripts
		 * @author Praveen Rajan
		 */
		function gallery_script() {
			
			?>
			<!-- Cool Video Gallery Script starts here -->
			<?php 
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery.slideshow', $this->plugin_url . 'js/jquery.slideshow.js', 'jquery');
			echo '<link rel="stylesheet" href="'.$this->plugin_url.'css/cvg-styles.css" type="text/css" />';
			?>
			<!-- Cool Video Gallery Script ends here -->
			<?php 
		}
		
		/**
		 * Function to load required files.
		 * @author Praveen
		 */	
		function load_video_files() {
			require_once('lib/video-db.php');
			require_once('lib/core.php');
			require_once('widgets/widgets.php');	
		}
		
		/**
		 * Function to parse the video gallery format text.
		 * 
		 * @param $content - Syntax for the player.
		 * @return content of video player.
		 * @author Praveen Rajan
		 */
		function CVGGallery_Parse($content){
			
			$content = preg_replace_callback("/\[cvg-gallery ([^]]*)\/\]/i", array(&$this, 'CVGVideo_Gallery'), $content);
			return $content;
		}
		
		
		/**
		 * Function to render gallery.
		 * 
		 * @param $matches - input arguments.
		 * @return player code.
		 * @author Praveen Rajan
		 */
		function CVGVideo_Gallery($matches){
			
			global $post, $wpdb;
			$output = '';
			$thumbexists = false;
			$setathumb = false;
			
			preg_match_all('/([\.\w]*)=(.*?) /i', $matches[1], $attributes);
			$arguments = array();
	
			$arguments = CoolVideoGallery::splitargs($matches[1]);
			$gallery_id = $arguments['galleryId'];

			$output = CvgCore::videoShowGallery($gallery_id);
			
			return $output;
		} 
		
		/**
		 * Function to parse the video player format text.
		 * 
		 * @param $content - Syntax for the player.
		 * @return content of video player.
		 * @author Praveen Rajan
		 */
		function CVGVideo_Parse($content){
			
			$content = preg_replace_callback("/\[cvg-video ([^]]*)\/\]/i", array(&$this, 'CVGVideo_Render'), $content);
			return $content;
		}
		
		/**
		 * Function to render video player.
		 * 
		 * @param $matches - input arguments.
		 * @return player code.
		 * @author Praveen Rajan
		 */
		function CVGVideo_Render($matches){
			
			global $post, $wpdb;
			
			$output = '';
			$thumbexists = false;
			$setathumb = false;
			
			preg_match_all('/([\.\w]*)=(.*?) /i', $matches[1], $attributes);
			$arguments = array();
	
			$arguments = CoolVideoGallery::splitargs($matches[1]);
			$video_details = videoDB::find_video($arguments['videoId']);
			
			
			if(!is_array($video_details))
				return __('[Video not found]');
				
			$video = array();
			$video['filename'] = site_url()  . '/' . $video_details[0]->path . '/' . $video_details[0]->filename;
			$video['thumb_filename'] =  $video_details[0]->path . '/thumbs/' . $video_details[0]->thumb_filename;
			$video['title'] = $video_details[0]->description;
			$video['name']	= $video_details[0]->name;
			if ( !array_key_exists('filename', $video) ){
				return '<div style="background-color:#f99; padding:10px;">Error: Required parameter "filename" is missing!</div>';
				exit;
			}
					
			if(file_exists($video['thumb_filename'])) 
				$thumbexists = true;
			else 
				$thumbexists = false;

			$options_player = get_option('cvg_player_settings');
			
			$options = get_option('cvg_settings');
			$thumb_width = $options['cvg_preview_width'];
			$thumb_height = $options['cvg_preview_height'];
			$cv_zc = $options['cvg_zc'];
			$thumb_quality = $options['cvg_preview_quality'];
			
			if(!file_exists(ABSPATH . '/' .$video['thumb_filename']))
				$video['thumb_filename']  = WP_CONTENT_URL .  '/plugins/' . dirname( plugin_basename(__FILE__)) . '/images/default_video.png';
			else 
				$video['thumb_filename'] =	site_url() . '/' . $video['thumb_filename'];
				
			$output .=  '<a href="' . $video['filename'] . '" title="' . $video['title'] . '"  rel="shadowbox[' . $video['name'] . '];height=' . $options_player['cvgplayer_height'] .';width=' . $options_player['cvgplayer_width'] . '">' ;
			$output .=  '<img src="' .$video['thumb_filename'] . '" style="width:' . $thumb_width . 'px;height:' . $thumb_height .'px;" ' ;
			$output .=  'alt="' . htmlspecialchars('Click to Watch Video') . '"/></a>';	
			
			return $output;
		} 
		
		/**
		 * Function to add players files to header.
		 * 
		 * @return script and styles for video player
		 * @author Praveen Rajan
		 */		
		function addPlayerHeader(){
			
			$options_player = get_option('cvg_player_settings');
			
			if($options_player['cvgplayer_autoplay'])
				$autoplay = "true";
			else 
				$autoplay = "false";	
			
			if($options_player['cvgplayer_fullscreen'])
				$full_screen = "true";
			else 
				$full_screen = "false";	
				
			if($options_player['cvgplayer_mute'])
				$mute = "true";
			else 
				$mute = "false";

			$options_settings = get_option('cvg_settings');	
			?>
			
			<!-- Cool Video Gallery Script starts here -->
			<link rel="stylesheet" type="text/css" href="<?php echo $this->video_player_url ?>shadowbox.css">
			<script type="text/javascript" src="<?php echo $this->video_player_url ?>shadowbox.js"></script>
		
			<script type="text/javascript">
				jQuery(document).ready(function(){
					 var options = {
									overlayOpacity: '0.8',
									animSequence: "wh",
								    handleOversize: "drag",
								    modal: true,
								    continuous: true,
								    counterType: "default",
								    counterLimit: 1,
								    allowscriptaccess: "always",
								    showMovieControls: true,
								    autoplayMovies: <?php echo $autoplay?>,
								    loadingImage: "<?php echo $this->video_player_url ?>loading.gif",
								    flashParams: {
						    						 allowfullscreen: "<?php echo $full_screen?>",
											    	 wmode: "transparent"
											    	 },
								    flashVars: {
											    skin: "<?php echo $this->video_player_url . 'skins/' . $options_player['cvgplayer_skin'] . '.swf' ?>",
											    volume: <?php echo $options_player['cvgplayer_volume']; ?>, 
											    mute: "<?php echo $mute;?>",
												controlbar: "<?php echo $options_player['cvgplayer_controlbar']?>",
												stretching: "<?php echo $options_player['cvgplayer_stretching']?>"
								    	 		}
					};
					Shadowbox.init(options);

					if(jQuery('.slide').length != 0) {
						jQuery('.slide').each(function() {
							jQuery(this).s3Slider({
						      timeOut: <?php echo $options_settings['cvg_slideshow']; ?>
						   });
						}); 
					}	   
				});
			</script>
			<!-- Cool Video Gallery Script ends here -->
			<?php
		}
		
		/**
		 * Function to split arguments
		 * 
		 * @param $argument_string - arguments passed
		 * @return arugments parsed.
		 * @author Praveen Rajan
		 */
		function splitargs($argument_string){

			preg_match_all('/(?:[^ =]+?)=(?:["\'].+?["\']|[^ ]+)/', $argument_string, $items);
		    $args = array();
		
		    foreach ($items[0] as $item){
		        $parts = explode("=", $item);
		        $name = $parts[0];
		        $value = implode("=", array_slice($parts, 1));
		        $args[$name] = trim($value, "\"'");
		    }
		
		    return $args;
		}
	}

}else {
	exit ("Class CoolVideoGallery already declared!");
}

// create new instance of the class
$CoolVideoGallery = new CoolVideoGallery();

if (isset($CoolVideoGallery)){
	register_activation_hook( __FILE__, array(&$CoolVideoGallery,'cvg_install') );
	register_deactivation_hook(__FILE__,  array(&$CoolVideoGallery,'cvg_deactivate_empty'));
}
?>
