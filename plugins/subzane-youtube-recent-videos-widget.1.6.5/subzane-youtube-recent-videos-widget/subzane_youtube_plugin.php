<?php
/*
Plugin Name: SubZane YouTube Plugin
Plugin URI: http://www.andreasnorman.se/wordpress-plugins/sz-youtube-plugin/
Description: This plugin can allows you to display a thumbnail list of YouTube videos in your sidebar. You can also add custom lists to your posts and pages using shortcode.
Author: Andreas Norman
Version: 1.6.5
Author URI: http://www.andreasnorman.se
*/

function subzane_youtube_plugin_init() {

	if ( !function_exists('register_sidebar_widget') )
		return;

	function subzane_youtube_plugin_widget($args) {
		extract($args);

		$options = get_option('subzane_youtube_plugin_widget');
		
		$lightbox = empty($options['lightbox']) ? 0 : $options['lightbox'];
		$target = empty($options['target']) ? '' : $options['target'];
		$width = empty($options['width']) ? 425 : $options['width'];
		$height = empty($options['height']) ? 344 : $options['height'];
		$sortorder = empty($options['sortorder']) ? 'published' : $options['sortorder'];
		$autoplay = empty($options['autoplay']) ? 0 : $options['autoplay'];
		$fullscreen = empty($options['fullscreen']) ? 0 : $options['fullscreen'];
		$hd = empty($options['hd']) ? 0 : $options['hd'];
		$related = empty($options['related']) ? 0 : $options['related'];
		$title = empty($options['title']) ? 'YouTube Feed' : $options['title'];
		$num = empty($options['num']) ? 0 : $options['num'];
		$url = $options['url'];
		$type = empty($options['type']) ? 'user' : $options['type'];
		
		$videos = subzane_youtube_plugin_getVideos($num, $url, $type, $sortorder);


		echo $before_widget;
		echo $before_title . $title . $after_title;
		if ($videos != null) {
			echo '<ul class="sz-videolisting">';
			foreach ($videos as $video) {
				if ($lightbox == 1) {
					echo  '<li><a rel="shadowbox;width='.$width.';height='.$height.'" href="'.subzane_youtube_plugin_fixlink($video['url'], $autoplay, $related, $fullscreen, $hd).'">';
				} else {
					if (!empty($target)) {
						echo  '<li><a target="'.$target.'" href="'.$video['url'].'">';
					} else {
						echo  '<li><a href="'.$video['url'].'">';
					}
				}
				echo  '<img alt="'.$video['title'].'" src="'.$video['thumb'].'" /><span>'.$video['title'].'</span></a></li>';
			}
			echo  '</ul>';
		} else {
			echo  '<p>No videos found</p>';
		}
		echo $after_widget;
	}
	
	function subzane_youtube_plugin_fixKeywords($keywords) {
		return str_replace(',', '-', $keywords);
	}
	
	function subzane_youtube_plugin_getVideos($num, $url, $type, $sortorder = 'published') {
		if ($num > 0) {
			$num_param = '&max-results='.$num;
		} else {
			$num_param = '';
		}
		
		if (!empty($url)) {
			if ($type=='user') {
				$url = 'http://gdata.youtube.com/feeds/api/videos?v=2&author='.$url.$num_param.'&orderby='.$sortorder;
			} else if ($type=='favorites') {
				$url = 'http://gdata.youtube.com/feeds/api/users/'.$url.'/favorites?v=2'.$num_param.'&orderby='.$sortorder;
				} else if ($type=='playlist') {
					$url = 'http://gdata.youtube.com/feeds/api/playlists/'.$url.'?v=2'.$num_param;
			} else {
				$url = 'http://gdata.youtube.com/feeds/api/videos?q='.$url.'&orderby='.$sortorder.$num_param.'&v=2';
			}

			$sxml = simplexml_load_file($url);
			$i = 0;
			$videoobj;

			foreach ($sxml->entry as $entry) {
				if ($i == $num && !empty($num_param)) {
					break;
				}
				// get nodes in media: namespace for media information
				$media = $entry->children('http://search.yahoo.com/mrss/');

				if ($media->group->player && $media->group->player->attributes() && $media->group->thumbnail && $media->group->thumbnail[0]->attributes()) {
					// get video player URL
					$attrs = $media->group->player->attributes();
					$videoobj[$i]['url'] = (string) $attrs['url'];
	
					// get video thumbnail
					$attrs = $media->group->thumbnail[0]->attributes();

					$videoobj[$i]['thumb'] = (string) $attrs['url']; 
					$videoobj[$i]['title'] = (string) $media->group->title;
					$i++;
				}
	    }
		} else {
			return null;
		}
		return $videoobj;
	}
	
	function subzane_youtube_plugin_sc($atts) {
		$options = get_option('subzane_youtube_plugin_widget');
		
		extract(shortcode_atts(array(
			'max' => '10',
			'type' => 'tag',
			'autoplay' => '0',
			'related' => '0',
			'fullscreen' => '0',
			'lightbox' => '0',
			'aspect' => '4:3',
			'width' => '425',
			'hd' => '0',
			'value' => '',
			'sortorder' => 'published',
		), $atts));
		$videos = subzane_youtube_plugin_getVideos($max, $value, $type, $sortorder);
		if ($aspect == '4:3') {
			$height = ceil($width / 1.333)+25;
		} else if ($aspect == '16:9') {
			$height = ceil($width / 1.778)+25;
		} else if ($aspect == '16:10') {
			$height = ceil($width / 1.6)+25;
		}
		
		$str = '';
		
		foreach ($videos as $video) {
			if ($lightbox == 1) {
				$str .= '<li><div><a rel="shadowbox;width='.$width.';height='.$height.'" href="'.subzane_youtube_plugin_fixlink($video['url'], $autoplay, $related, $fullscreen, $hd).'">';
			} else {
				$str .= '<li><div><a href="'.$video['url'].'">';
			}
			$str .= '<img alt="'.$video['title'].'" src="'.$video['thumb'].'" /><p>'.$video['title'].'</p></a></div></li>';
		}
		
		return '
		<div class="sz-youtube-list">
			<ul>
			'.$str.'
			</ul>
		</div>
		';
	}

	function subzane_youtube_plugin_fixlink($url, $autoplay = 0, $related = 0, $fullscreen = 0, $hd = 0) {
		return 'http://www.youtube.com/v/'.substr($url, strpos($url, '=')+1).'&autoplay='.$autoplay.'&rel='.$related.'&fs='.$fullscreen.'&hd='.$hd;
	}
	
	function subzane_youtube_plugin_widget_control() {
		$options = get_option('subzane_youtube_plugin_widget');
		if ( !is_array($options) )
			$options = array('title'=>'YouTube Feed', 'num'=>1);
		if ( $_POST['youtube-rss-widget-submit'] ) {
			if ($_POST['youtube-rss-widget-type'] == 'tag') {
				$url = subzane_youtube_plugin_fixKeywords($_POST['youtube-rss-widget-url']);
			} else {
				$url = str_replace(' ', '', $_POST['youtube-rss-widget-url']);
			}
			
			if ($_POST['youtube-rss-widget-aspect'] == '4:3') {
				$height = ceil($_POST['youtube-rss-widget-width'] / 1.333)+25;
			} else if ($_POST['youtube-rss-widget-aspect'] == '16:9') {
				$height = ceil($_POST['youtube-rss-widget-width'] / 1.778)+25;
			} else if ($_POST['youtube-rss-widget-aspect'] == '16:10') {
				$height = ceil($_POST['youtube-rss-widget-width'] / 1.6)+25;
			}

			$options['title'] = strip_tags(stripslashes($_POST['youtube-rss-widget-title']));
			$options['url'] = strip_tags(stripslashes($url));
			$options['num'] = strip_tags(stripslashes($_POST['youtube-rss-widget-num']));
			$options['sortorder'] = strip_tags(stripslashes($_POST['youtube-rss-widget-sortorder']));
			$options['type'] = strip_tags(stripslashes($_POST['youtube-rss-widget-type']));
			$options['lightbox'] = strip_tags(stripslashes($_POST['youtube-rss-widget-lightbox']));
			$options['fullscreen'] = strip_tags(stripslashes($_POST['youtube-rss-widget-fullscreen']));
			$options['aspect'] = strip_tags(stripslashes($_POST['youtube-rss-widget-aspect']));
			$options['hd'] = strip_tags(stripslashes($_POST['youtube-rss-widget-hd']));
			$options['related'] = strip_tags(stripslashes($_POST['youtube-rss-widget-related']));
			$options['autoplay'] = strip_tags(stripslashes($_POST['youtube-rss-widget-autoplay']));
			$options['width'] = strip_tags(stripslashes($_POST['youtube-rss-widget-width']));
			$options['height'] = $height;
			$options['target'] = strip_tags(stripslashes($_POST['youtube-rss-widget-target']));
			
			update_option('subzane_youtube_plugin_widget', $options);
		}

		$title = empty($options['title']) ? 'YouTube Feed' : $options['title'];
		$num = empty($options['num']) ? 0 : $options['num'];
		$type = empty($options['type']) ? 'user' : $options['type'];
		$sortorder = empty($options['sortorder']) ? 'published' : $options['sortorder'];
		$url = htmlspecialchars($options['url'], ENT_QUOTES);
		$fullscreen = empty($options['fullscreen']) ? 0 : $options['fullscreen'];
		$aspect = empty($options['aspect']) ? '4:3' : $options['aspect'];
		$hd = empty($options['hd']) ? 0 : $options['hd'];
		$lightbox = empty($options['lightbox']) ? 0 : $options['lightbox'];
		$related = empty($options['related']) ? 0 : $options['related'];
		$autoplay = empty($options['autoplay']) ? 0 : $options['autoplay'];
		$width = empty($options['width']) ? 425 : $options['width'];
		//$height = empty($options['height']) ? 344 : $options['height'];
		$target = empty($options['target']) ? '' : $options['target'];
				
		if ( $order == 'random' ) echo 'selected="selected"';
		echo '
			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-title">
				' . __('Title:') . '<br/>
				<input style="width: 200px;" id="youtube-rss-widget-title" name="youtube-rss-widget-title" type="text" value="'.$title.'" />
			</label>

			<label style="line-height: 35px; display: block;">
				' . __('Find videos by:') . '<br/>
				<select name="youtube-rss-widget-type" id="youtube-rss-widget-type">
					<option value="tag" '.($type=='tag'?'selected="selected"':'').' >Keywords</option>
					<option value="playlist" '.($type=='playlist'?'selected="selected"':'').' >Playlist</option>
					<option value="user" '.($type=='user'?'selected="selected"':'').'>Specific Username</option>
					<option value="favorites" '.($type=='favorites'?'selected="selected"':'').'>Favorites</option>
				</select>
			</label>
			
			<label style="line-height: 35px; display: block;">
				' . __('Sort order:') . '<br/>
				<select name="youtube-rss-widget-sortorder" id="youtube-rss-widget-sortorder">
					<option value="published" '.($sortorder=='published'?'selected="selected"':'').' >When published</option>
					<option value="relevance" '.($sortorder=='relevance'?'selected="selected"':'').' >Relevance</option>
					<option value="viewCount" '.($sortorder=='viewCount'?'selected="selected"':'').'>By viewCount</option>
					<option value="rating" '.($sortorder=='rating'?'selected="selected"':'').'>By rating</option>
				</select>
			</label>
			
			<h3>Info</h3>
			<p>
				<b>Keywords:</b> Will search all video metadata for videos matching the term. Video metadata includes titles, keywords, descriptions, authors usernames, and categories.<br/>
				<b>Specific username:</b> A YouTube username<br/>
				<b>Playlist:</b> The ID of a specific playlist<br/>
				<b>Favorites:</b> The Favorites of a specific user<br/>
			</p>

			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-url">
			' . __('Keywords, Username or Playlist ID:') . '<br/>
				<input style="width: 150px;" id="youtube-rss-widget-url" name="youtube-rss-widget-url" type="text" value="'.$url.'" />
			</label>

			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-num">
				' . __('Max number of videos (set 0 to get full feed):') . '<br/>
				<input style="width: 50px;" id="youtube-rss-widget-num" name="youtube-rss-widget-num" type="text" value="'.$num.'" />
			</label>

			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-lightbox">
			<input type="checkbox" id="youtube-rss-widget-lightbox" '.($lightbox==1?'checked="checked"':'').' name="youtube-rss-widget-lightbox" type="text" value="1" />
				' . __('Lightbox support') . '
			</label>

			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-autoplay">
			<input type="checkbox" id="youtube-rss-widget-autoplay" '.($autoplay==1?'checked="checked"':'').' name="youtube-rss-widget-autoplay" type="text" value="1" />
				' . __('Autoplay video') . '
			</label>

			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-related">
			<input type="checkbox" id="youtube-rss-widget-related" '.($related==1?'checked="checked"':'').' name="youtube-rss-widget-related" type="text" value="1" />
				' . __('Show related videos') . '
			</label>

			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-hd">
			<input type="checkbox" id="youtube-rss-widget-hd" '.($hd==1?'checked="checked"':'').' name="youtube-rss-widget-hd" type="text" value="1" />
				' . __('Show videos in HD when available') . '
			</label>
			';
			/*
			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-fullscreen">
			<input type="checkbox" id="youtube-rss-widget-fullscreen" '.($fullscreen==1?'checked="checked"':'').' name="youtube-rss-widget-fullscreen" type="text" value="1" />
				' . __('Show fullscreen button') . '
			</label>
			*/
			echo '
			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-target">
				' . __('Target window') . '<br/>
				<select name="youtube-rss-widget-target" id="youtube-rss-widget-target">
					<option value="" '.($target==''?'selected="selected"':'').' >None</option>
					<option value="_blank" '.($target=='_blank'?'selected="selected"':'').' >New window (_blank)</option>
					<option value="_self" '.($target=='_self'?'selected="selected"':'').' >_self</option>
					<option value="_top" '.($target=='_top'?'selected="selected"':'').'>_top</option>
				</select>
			</label>
			
			<label style="line-height: 35px; display: block;">
				' . __('Aspect ratio (Only for Lightbox):') . '<br/>
				<select name="youtube-rss-widget-aspect" id="youtube-rss-widget-aspect">
					<option value="4:3" '.($aspect=='4:3'?'selected="selected"':'').' >4:3</option>
					<option value="16:9" '.($aspect=='16:9'?'selected="selected"':'').' >16:9</option>
					<option value="16:10" '.($aspect=='16:10'?'selected="selected"':'').'>16:10</option>
				</select>
			</label>
			

			<label style="line-height: 35px; display: block;" for="youtube-rss-widget-width">
				' . __('Width (Only for Lightbox):') . '<br/>
				<input style="width: 50px;" id="youtube-rss-widget-width" name="youtube-rss-widget-width" type="text" value="'.$width.'" />
			</label>

			<h3>Info</h3>
			<p>
				<b>The height</b> will automatically be calculated depending on the aspect ration and width you define above.<br/>
			</p>

		
		<input type="hidden" id="youtube-rss-widget-submit" name="youtube-rss-widget-submit" value="1" />
		';
	}
	
	function subzane_youtube_plugin_styles () {
		$plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$css = $plugin_url . 'style.css';
		
		wp_register_style('subzane_youtube_plugin_css', $css);
		wp_enqueue_style( 'subzane_youtube_plugin_css');
	}
	
	
	register_sidebar_widget(array('SZ YouTube Widget', 'widgets'), 'subzane_youtube_plugin_widget');
	register_widget_control(array('SZ YouTube Widget', 'widgets'), 'subzane_youtube_plugin_widget_control', 350, 150);

	add_shortcode('sz-youtube', 'subzane_youtube_plugin_sc');
	add_action('wp_print_styles', 'subzane_youtube_plugin_styles');
	
}

add_action('plugins_loaded', 'subzane_youtube_plugin_init');

?>