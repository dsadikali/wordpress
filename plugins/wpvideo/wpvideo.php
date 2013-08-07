<?php
/*
Plugin Name: WPvideo
Plugin URI: http://www.skarcha.com/wp-plugins/wpvideo/
Description: Allows you to insert online videos in your post by providing the video url between the &lt;video&gt; tags. Also allows download the video (using <a href="http://downthisvideo.com">DownThisVideo!</a>) with a link below the video.
Version: 1.10
Date: Oct 28th, 2006
Author: Antonio Perez
Author URI: http://www.skarcha.com

--------------------------------------------------------------------------------
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
--------------------------------------------------------------------------------

Change Log:

1.10 - (10/28/2006) - Metacafe support.
                    - Description of the video can be showed.
                    - Video information can be placed at top or bottom.
                    - Fixed problems with hosting that report this error: "URL file-access is
                      disabled in the server configuration".
                    - CSS Styles included into source. You don't have to edit sytle.css.
                    - Bugfixes.

1.02 - (06/28/2006) - MyVideo.es support.

1.01 - (06/24/2006) - Now can use <video> or [video] tags. Last one is useful if you are using
                      the visual rich editor.

1.00 - (06/20/2006) - Updated Google Video functions.
					- Updated YouTube functions. Adapted to new changes in YouTube.
					- Partial Yahoo Videos support.
					- New Options page in Dashboard for setting default options.
					- Can set options for each video... in the form: <video download="yes" title="no"> ... </video>
					- Width and height set to 100%. You can resize video using CSS.
					- Link to video author profile when possible.
					- QuickTag button for editor.
					- Notify when new version is found.
					- Many bugfixes...
					- I can't remember more... O:)

0.60 - (04/15/2006) - This version show video information (title, author, duration and date) and now supports Google Video.
0.52 - (04/11/2006) - You can insert more than one videos in a post.
					- You can choose if you want to put the Download button, using dwbutton option or not: <video dwbutton>
					- Generated code is XHTML 1.0 Transitional.
0.51 - (04/10/2006) - Better support for YouTube URLs, and a new “Download” button..
0.50 - (04/09/2006) - In this first version, it only support YouTube videos.
*/ 

define(WPV_VERSION,'1.10');

function wpv_file_get_contents($url)
{
	if (function_exists('curl_init')) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
	}
	else
		$file_contents = file_get_contents($url);
	
	return $file_contents;
}

function wpv_youtubeinfo($url, $url_parts)
{
	$video_url = $url_parts['scheme'] . '://www.youtube.com';
	if (array_key_exists('query', $url_parts))
		$video_url .= '/' . str_replace('=', '/', $url_parts['query']);
	else {
		$video_url .= $url_parts['path'];
		$url = str_replace('/v/', '/watch?v=', $url);
	}

	$lines = wpv_file_get_contents($url);

	preg_match('/<h1 id="video_title">(.*?)<\/h1>/i', $lines, $matches);
	$video_info['title'] = trim($matches[1]);

	preg_match('/<span id="vidDescRemain">(.*?)<\/span>/i', $lines, $matches);
	$video_info['description'] = $matches[1];

	if (trim($video_info['description'] == '')) {
		preg_match('/<span id="vidDescBegin">(.*?)<\/span>/is', $lines, $matches);
		$video_info['description'] = $matches[1];
	}

	preg_match('/<b class="smallText">(.*)?<\/b><br>/i', $lines, $matches);
	$video_info['date'] = $matches[1];

	preg_match('/<b><a href="\/(user.*?) .*?">(.*?)<\/a><\/b>/i', $lines, $matches);
	$video_info['author'] = $matches[2];
	$video_info['author_profile'] = 'http://www.youtube.com/' . $matches[1];

	preg_match('/<span class="runtime">(.*?)<\/span>/i', $lines, $matches);
	$video_info['duration'] = $matches[1];

	$video_info['embedcode'] = '<object data="' . $video_url . '" type="application/x-shockwave-flash" width="100%" height="100%"><param name="movie" value="' . $video_url . '"></param></object>';

	return $video_info;
}

function wpv_googlevideoinfo($url)
{
	$lines = wpv_file_get_contents($url);

	preg_match('/docid=(.*)/i', $url, $matches);
	$docid = $matches[1];

	$video_info['embedcode'] = '<embed style="width:100%; height:100%" id="VideoPlayback" align="middle" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=' . $docid . '" allowScriptAccess="sameDomain" quality="best" bgcolor="#ffffff" scale="noScale" wmode="window" salign="TL"  FlashVars="playerMode=embedded"> </embed>';

	preg_match('/<div id="pvprogtitle">(.*?)<\/div>/i', $lines, $matches);
	$video_info['title'] = trim($matches[1]);

	preg_match('/<div id="description"> <font size="-1">(.*?)<\/font> <\/div>/is', $lines, $matches);
	$video_info['description'] = trim($matches[1]);

	preg_match('/<div id="durationetc">(.*?)([0-9]+\s+.*?)-(.*?)\s<.*?<\/div>/i', $lines, $matches);
	$video_info['author'] = trim(str_replace('<br>', '', $matches[1]));
	$video_info['author_profile'] = '';
	$video_info['duration'] = trim($matches[2]);
	$video_info['date'] = trim($matches[3]);

	return $video_info;
}

function wpv_yahoovideoinfo($url)
{
	$lines = wpv_file_get_contents($url);

	preg_match('/value="(<embed.*?<\/embed>)"/i', $lines, $matches);
	$video_info['embedcode'] = $matches[1];

	preg_match('/<h3 id="vt">(.*?)<\/h3>/i', $lines, $matches);
	$video_info['title'] = trim($matches[1]);

	preg_match('/<span>From <a href="(.*?)">(.*?)<\/a><\/span>/i', $lines, $matches);
	$video_info['author_profile'] = trim($matches[1]);
	$video_info['author'] = trim($matches[2]);

	preg_match('/Length:<\/dt><dd class=\'dlf2\'>(.*?)<\/dd><\/div><div><dt class=\'dlf2\'>Added:<\/dt><dd class=\'dlf2\'>(.*?)<\/dd>/i', $lines, $matches);
	$video_info['duration'] = trim($matches[1]);
	$video_info['date'] = trim($matches[2]);

	return $video_info;
}

function wpv_myvideoinfo($url)
{
	$lines = wpv_file_get_contents($url);

	preg_match('/<h1>(.*?)<\/h1>/i', $lines, $matches);
	$video_info['title'] = $matches[1];

	preg_match('/Subido el(.*?), por <a href="\.\.\/online\/(.*?)" .*?>(.*?)<\/a>/i', $lines, $matches);
	$video_info['date'] = $matches[1];
	$video_info['author'] = $matches[3];
	$video_info['author_profile'] = 'http://www.myvideo.es/online/' . $matches[2];

	preg_match('/Duraci.*?n: (.*?),/i', $lines, $matches);
	$video_info['duration'] = $matches[1];

	$video_url = str_replace('watch', 'movie', $url);

	$video_info['embedcode'] = '<object data="' . $video_url . '" type="application/x-shockwave-flash" width="425" height="350"><param name="movie" value="' . $video_url . '"></param></object>';

	return $video_info;
}

function wpv_metacafeinfo($url)
{
	preg_match('/www.metacafe.com\/watch\/(.*?)\//i', $url, $matches);
	$itemid = $matches[1];

	$lines = wpv_file_get_contents('http://www.metacafe.com/fplayer.php?itemID='.$itemid.'&t=embedded');

	preg_match('/<item.* title="(.*?)" .*?views="(.*?)" rank="(.*?)".*?length="(.*?)" description="(.*?)" /i', $lines, $matches);
	$video_info['title'] = $matches[1];
	$video_info['views'] = $matches[2];
	$video_info['rating'] = $matches[3];
	$video_info['duration'] = $matches[4] . ' secs';
	$video_info['description'] = $matches[5];

	preg_match('/id="rankValue">(.*?)<\/div><div class="views">(.*?) views<\/div>/i', $lines, $matches);

	preg_match('/value="&lt;embed.*?src=&quot;(.*?)&quot;/i', $lines, $matches);
	$video_info['embedcode'] = '<embed src="' . $matches[1] . '" width="100%" height="100%" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';

	return $video_info;
}

function wpv_video_options($options)
{
	preg_match_all('/((.*?)\s*=\s*"(.*?)")\s*/i', $options, $matches);

	for ($i = 0; $i < count($matches[2]); $i++)
		$aoptions[strtolower($matches[2][$i])] = strtolower($matches[3][$i]);

	return $aoptions;
}

function wpv_videoinfo($url)
{
	global $post;

	// First, we search for it in Post Custom Field.
	$video_info = get_post_meta($post->ID, 'wpv_'.$url, true);

	if (empty($video_info))
		$new = true;
	else
		$new = false;

	if (($new) || (WPV_VERSION != $video_info['version'])) {
		$video_info_old = $video_info;
		$url_parts = parse_url($url);

		if (strstr($url_parts['host'], 'video.google'))
			$url_parts['host'] = 'video.google.com';

		switch($url_parts['host']) {
			case 'www.youtube.com':
			case 'youtube.com':
				$video_info = wpv_youtubeinfo($url, $url_parts);
				break;

			case 'video.google.com':
				$video_info = wpv_googlevideoinfo($url);
				break;

			case 'video.yahoo.com':
				$video_info = wpv_yahoovideoinfo($url);
				break;

			case 'www.myvideo.es':
			case 'myvideo.es':
				$video_info = wpv_myvideoinfo($url);
				break;

			case 'www.metacafe.com':
			case 'metacafe.com':
				$video_info = wpv_metacafeinfo($url);
				break;
		}

		$video_info['version'] = WPV_VERSION;
		$video_info['url'] = $url;

		// Insert Post Custom Field.
		if ($new)
			add_post_meta($post->ID, 'wpv_'.$url, $video_info, true);
		else
			update_post_meta($post->ID, 'wpv_'.$url, $video_info, $video_info_old);
	}

	return $video_info;
}

function wpv_option_value($options, $option)
{
	global $wpv_options;

	// Default values for not defined options.
	$DEFAULT_VALUES['data_position'] = 'down';
	$DEFAULT_VALUES['download'] = 'yes';
	$DEFAULT_VALUES['downloadtext'] = 'Download!';
	$DEFAULT_VALUES['title'] = 'yes';

	if ((is_array($options)) && (array_key_exists($option, $options)))
		return $options[$option];
	else {
		if ((is_array($wpv_options)) && (array_key_exists($option, $wpv_options)))
			return $wpv_options[$option];
		else
			if (array_key_exists($option, $DEFAULT_VALUES))
				return $DEFAULT_VALUES["$option"];
	}
}

function wpv_insert_video($embedcode)
{
	$html = '<div class="wpv_video">';
	if (trim($embedcode) == '')
		$html .= 'NOT EMBEDABLE';
	else
		$html .= $embedcode;

	$html .= '</div>';

	return $html;
}

function wpv_insert_video_data($video_info)
{
	global $wpv_options;

	$html = '';

	if (((wpv_option_value($video_info['options'], 'rating') == 'yes') || (wpv_option_value($video_info['options'], 'views') == 'yes')) &&
	    ((trim($video_info['rating']) != '') || (trim($video_info['views'] != '')))) {
		$html .= '<div class="wpv_rating">';
		if (($video_info['rating'] != '') && (wpv_option_value($video_info['options'], 'rating') == 'yes'))
			$html .= 'Rating: ' . $video_info['rating'];
		if (($video_info['views'] != '') && (wpv_option_value($video_info['options'], 'views') == 'yes'))
			$html .= ' - Views: ' . $video_info['views'];
		$html .= '</div>';
	}

	if ((wpv_option_value($video_info['options'], 'title') == 'yes') || (wpv_option_value($video_info['options'], 'author') == 'yes')) {
		$html .= '<div class="wpv_titleauthor">';
		if (($video_info['title'] != '') && (wpv_option_value($video_info['options'], 'title') == 'yes'))
			$html .= $video_info['title'];
		if (($video_info['author'] != '') && (wpv_option_value($video_info['options'], 'author') == 'yes')) {
			$html .= ' - ';
			if ('' != trim($video_info['author_profile']))
				$html .= '<a href="' . $video_info['author_profile'] . '">'	. $video_info['author'] . '</a>';
			else
				$html .= $video_info['author'];
		}
		$html .= '</div>';
	}

	if ((wpv_option_value($video_info['options'], 'description') == 'yes') && ($video_info['description'] != ''))
		$html .= '<div class="wpv_description">' . $video_info['description'] . '</div>';

	if ((wpv_option_value($video_info['options'], 'duration') == 'yes') || (wpv_option_value($video_info['options'], 'date') == 'yes')) {
		$html .= '<div class="wpv_durationdate">';
		if (($video_info['duration'] != '') && (wpv_option_value($video_info['options'], 'duration') == 'yes'))
			$html .= $video_info['duration'];
		if (($video_info['date'] != '') && (wpv_option_value($video_info['options'], 'date') == 'yes'))
			$html .= ' - ' . $video_info['date'];
		$html .= '</div>';
	}

	if ((wpv_option_value($video_info['options'], 'download') == 'yes')) {
		$download_text = wpv_option_value($wpv_options, 'downloadtext');
		$html .= '<div class="wpv_download"><a target="_blank" href="http://downthisvideo.com/?url=' . $video_info['url'] . '">' . $download_text . '</a></div>';
	}

	return $html;
}

function wpvideo($text) {
	global $wpv_options;

	$video_pattern = '/([\[|<]video\s*(.*?)[\]|>](.*?)[\[|<]\/video[\]|>])/i';

	// Check for in-post <video> </video>
	if (preg_match_all ($video_pattern, $text, $matches)) {

		// Get WPvideo options.
		$wpv_options = get_option('wpv_options');

		for ($i = 0; $i < count($matches[0]); $i++) {
			$htmlcode = '';
			$url_video = $matches[3][$i];

			$video_info = wpv_videoinfo($url_video);
			// Get video options.
			$video_info['options'] = wpv_video_options($matches[2][$i]);

			$htmlcode .= '<div class="wpv_videoc">';
			$htmlcode .= '<div class="wpv_self"><a href="http://www.skarcha.com/wp-plugins/wpvideo/">WPvideo ' . WPV_VERSION . '</a></div>';

			if ((wpv_option_value($video_info['data_position'], 'data_position') == 'up')) {
				$htmlcode .= wpv_insert_video_data($video_info);
				$htmlcode .= wpv_insert_video($video_info['embedcode']);
			}
			else {
				$htmlcode .= wpv_insert_video($video_info['embedcode']);
				$htmlcode .= wpv_insert_video_data($video_info);
			}

			$htmlcode .= '</div>';

			$text = str_replace($matches[0][$i], $htmlcode, $text);
		}
	}
    
	return $text;
}

function wpv_VersionCheck()
{
	$string = '';
	$string = wpv_file_get_contents ('http://www.skarcha.com/wpvideo-version.txt');
	return 0+$string; // convert to float
}

function wpv_DisplayAvailUpdate($pi_vers=0.0)
{
	global $wpv_options;

	$pi_vers+=0.0;
	
	$wpv_options = get_option('wpv_options');
	if (isset($wpv_options)) {
		$check = $wpv_options['next_update_check'];
		// Debug
		// $check = time() - 1;
		if( time() > (integer)$check ){
			$next_week = time() + (7 * 24 * 60 * 60);
			$wpv_options['next_update_check'] = $next_week;
			$new_vers = wpv_VersionCheck();
			if( $new_vers != '' ){
				$wpv_options['latest_version'] = floatval($new_vers);
			}
			else {
				$wpv_options['latest_version'] = floatval($pi_vers);
			}
			update_option('wpv_options', $wpv_options);
		}
	}

	if (isset($wpv_options) && isset($wpv_options['latest_version'])){
		$new_vers = $wpv_options['latest_version'];
		if (floatval($wpv_options['latest_version']) > $pi_vers ){
			return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a style='font-weight:bold;color:#f00;'  href='http://www.skarcha.com/wp-plugins/wpvideo/' target='external' title='New WPvideo version available'>DOWNLOAD LATEST UPDATE (v$new_vers)</a>";
		}
	}else{
		return '';
	}
}

function wpv_manage_options()
{
	if (isset($_POST['update_wpv'])) {
		$options['data_position'] = $_POST['wpv_data_position'];
		$options['title'] = $_POST['wpv_title'];
		$options['author'] = $_POST['wpv_author'];
		$options['description'] = $_POST['wpv_description'];
		$options['rating'] = $_POST['wpv_rating'];
		$options['views'] = $_POST['wpv_views'];
		$options['duration'] = $_POST['wpv_duration'];
		$options['date'] = $_POST['wpv_date'];
		$options['download'] = $_POST['wpv_download'];
		$options['downloadtext'] = $_POST['wpv_downloadtext'];
		update_option('wpv_options', $options);
		?> <div class="updated"><p>Options saved!</p></div> <?php
	}
	else
		$options = get_option('wpv_options');
	?>

	<div class="wrap">
		<h2>Options for WPvideo v<?php echo WPV_VERSION . wpv_DisplayAvailUpdate(WPV_VERSION); ?></h2>
		<form method="post">
		<fieldset class="options">
		<table>
			<tr>
				<td>Video data position?:</td>
          		<td>
        			<select name="wpv_data_position" id="wpv_data_position">
        	  		<option <?php if($options['data_position'] == 'up') { echo 'selected'; } ?> value="up">Up</option>
					<option <?php if($options['data_position'] == 'down') { echo 'selected'; } ?> value="down">Down</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td>Show video title?:</td>
          		<td>
        			<select name="wpv_title" id="wpv_title">
        	  		<option <?php if($options['title'] == 'no') { echo 'selected'; } ?> value="no">No</option>
					<option <?php if($options['title'] == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td>Show video author?:</td>
          		<td>
        			<select name="wpv_author" id="wpv_author">
        	  		<option <?php if($options['author'] == 'no') { echo 'selected'; } ?> value="no">No</option>
					<option <?php if($options['author'] == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td>Show video description?:</td>
          		<td>
        			<select name="wpv_description" id="wpv_description">
        	  		<option <?php if($options['description'] == 'no') { echo 'selected'; } ?> value="no">No</option>
					<option <?php if($options['description'] == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td>Show video rating?:</td>
          		<td>
        			<select name="wpv_rating" id="wpv_rating">
        	  		<option <?php if($options['rating'] == 'no') { echo 'selected'; } ?> value="no">No</option>
					<option <?php if($options['rating'] == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td>Show video views?:</td>
          		<td>
        			<select name="wpv_views" id="wpv_views">
        	  		<option <?php if($options['views'] == 'no') { echo 'selected'; } ?> value="no">No</option>
					<option <?php if($options['views'] == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td>Show video duration?:</td>
          		<td>
        			<select name="wpv_duration" id="wpv_duration">
        	  		<option <?php if($options['duration'] == 'no') { echo 'selected'; } ?> value="no">No</option>
					<option <?php if($options['duration'] == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td>Show video date?:</td>
          		<td>
        			<select name="wpv_date" id="wpv_date">
        	  		<option <?php if($options['date'] == 'no') { echo 'selected'; } ?> value="no">No</option>
					<option <?php if($options['date'] == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td>Show download link?:</td>
          		<td>
        			<select name="wpv_download" id="wpv_download">
        	  		<option <?php if($options['download'] == 'no') { echo 'selected'; } ?> value="no">No</option>
					<option <?php if($options['download'] == 'yes') { echo 'selected'; } ?> value="yes">Yes</option>
					</select>
				</td> 
			</tr>
			<tr>
				<td><label for="wpv_downloadtext">"Download link" text?</label>:</td>
				<td><input name="wpv_downloadtext" type="text" id="wpv_downloadtext" value="<?php echo $options['downloadtext']; ?>" /></td>
			</tr>
		</table>
		</fieldset>

		<p><div class="submit"><input type="submit" name="update_wpv" value="<?php _e('Save!', 'update_wpv') ?>"  style="font-weight:bold;" /></div></p>
        
		</form>       
		
    </div>
    
<?php } 

// Creates QuickTags button for WPvideo in editor
function wpv_InsertWPVButton()
{
	$rich_editing = false;
	if (strpos($_SERVER['REQUEST_URI'], 'post.php') || strstr($_SERVER['PHP_SELF'], 'page-new.php')) {
		if (function_exists('get_user_option')) 
			$rich_editing = (get_user_option('rich_editing') == 'true');
?>
<script language="JavaScript" type="text/javascript"><!--
var i = edButtons.length;
edButtons[i] = 
new edButton('ed_video'
,'video'
,'<video>'
,'</video>'
,'v'
);
if (<?php echo (($rich_editing) ?  "false" : "true");?>) {
	if (document.getElementById('quicktags') != undefined){
		document.getElementById('quicktags').innerHTML += '<input type="button" id="' + edButtons[i].id + '" accesskey="' + edButtons[i].access + '" class="ed_button" onclick="edInsertTag(edCanvas, ' + i + ');" value="' + edButtons[i].display + '" />';
	}
}
//--></script>
<?php
	}
}
	
function wpv_add_options_page()
{
	add_options_page('WPvideo Options', 'WPvideo', 8, basename(__FILE__), 'wpv_manage_options');
}

function wpv_css()
{
	?>
<style type="text/css" media="screen">
/* Begin WPvideo CSS */
.wpv_videoc {
	text-align: center;
	display: block;
	margin-left: auto;
	margin-right: auto;
	width: 425px;
/* border: 1px solid #aaa; */
}

.wpv_video {
	display: block;
	margin-left: auto;
	margin-right: auto;
	padding: 4px 0 4px 0;
	width: 425px;
	height: 350px;
}

.wpv_rating {
	margin-left: auto;
	margin-right: auto;
	width: 95%;
	padding: 3px;
	border-top: 1px solid #aaa;
	font: 8pt "Lucida Grande", Verdana, Arial, 'Bitstream Vera Sans', sans-serif;
	text-align: right;	
}

.wpv_titleauthor, .wpv_durationdate, .wpv_description {
	display: block;
	margin-left: auto;
	margin-right: auto;
	width: 95%;
	font: bold 11px "Lucida Grande", Verdana, Arial, 'Bitstream Vera Sans', sans-serif;
	color: #666;
	padding: 3px;
	border-top: 1px solid #aaa;
}

.wpv_download {
	display: block;
	margin-left: auto;
	margin-right: auto;
	padding: 3px;
}

.wpv_download a {
	font: bold 11px "Lucida Grande", Verdana, Arial, 'Bitstream Vera Sans', sans-serif;
	color: #f44;
}

.wpv_download a:hover {
	text-decoration: none;
}

.wpv_download img {
	border: 0;
}

.wpv_self {
	text-align: left;
}

.wpv_self a {
	font: bold 9px "Lucida Grande", Verdana, Arial, 'Bitstream Vera Sans', sans-serif;
	color: #000;
}
/* End WPvideo CSS */
</style>

<?php

}

add_filter('admin_footer', 'wpv_InsertWPVButton');
add_action('admin_menu', 'wpv_add_options_page');
add_filter('wp_head', 'wpv_css');
add_filter('the_content', 'wpvideo', 1);
?>
