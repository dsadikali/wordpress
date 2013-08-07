<?php

/*
Plugin Name:    Topsy Retweet Button
Version:        1.2.7
Plugin URI:     http://labs.topsy.com/widgets/wordpress
Author:         Topsy Labs
Author URI:     http://topsy.com/
Description:    Provides a Twitter retweet button powered by <a href="http://topsy.com/">Topsy</a>. Can also provide automatic trackback comments when someone tweets about your posts.
Copyright:      Copyright 2009-2010 by Topsy Labs. This software is distributed under the terms of the GNU GPL as defined in LICENSE.txt.
*/

global $TOPSY_VERSION;
$TOPSY_VERSION = '1.2.7';
global $topsy_cache_time_tb, $topsy_cache_time_udata, $topsy_conn_timeout, $topsy_debug;
$topsy_cache_time_tb = 300;
$topsy_cache_time_udata = 3600;
$topsy_conn_timeout = 10.0;
$topsy_debug = 0;


function topsy_init() {
    load_plugin_textdomain('topsy', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) );
}

function shortcode_topsy_retweet_big() {
    return topsy_generate_retweet_button('big');
}
function shortcode_topsy_retweet_small() {
    return topsy_generate_retweet_button('small');
}

function topsy_action_admin_menu() {
    add_submenu_page('options-general.php', 'Topsy Options', 'Topsy', 9, 'topsy', 'topsy_settings_menu');
    if( !get_option('topsy_trackbacks_enabled') ) { topsy_settings_reset(); }
}

function topsy_activation() {
    add_option('topsy_trackbacks_enabled', 'off');
    add_option('topsy_trackbacks_infonly', 'off');
    add_option('topsy_rl_limit', '1');
    add_option('topsy_rl_remaining', '1');
    add_option('topsy_rl_reset', '1');
    add_option('topsy_linkify_hashtag', 'on');
    add_option('topsy_linkify_atuser', 'on');
    add_option('topsy_ignore_list', '');
    add_option('topsy_comment_type', 'trackback');
    $pages = array( 'home', 'page', 'post', 'category', 'archive' );
    foreach ($pages as $page) {
        add_option('topsy_button_' . $page . '_display',    'on');
        add_option('topsy_button_' . $page . '_position',   'before');
        add_option('topsy_button_' . $page . '_align',      'right');
        add_option('topsy_button_' . $page . '_style',      'big');
        add_option('topsy_button_' . $page . '_css',        'margin-left: 0.75em;');
    }
    add_option('topsy_add_button_to_rss', 'off');
    add_option('topsy_autonotify_enabled', 'on');
    add_option('topsy_smallbutton_order', 'count,badge,retweet');
    add_option('topsy_url_shortener', 'bitly');
    add_option('topsy_urlshortener_username', 'topsyplugin');
    add_option('topsy_urlshortener_authkey', 'R_0bb5fbe4ce1eb830679766704df41937');
    topsy_update_shortener_data();
    add_option('topsy_button_theme', 'blue');
    add_option('topsy_button_text_tweet', 'tweet');
    add_option('topsy_button_text_retweet', 'retweet');
    if (get_option('topsy_retweet_username')) {
        $rt_username = get_option('topsy_retweet_username');
        if (strrchr($rt_username, '@')) $rt_username = substr(strrchr($rt_username, '@'), 1);
        update_option('topsy_retweet_username', $rt_username);
    }
    add_option('topsy_suppress_categories', '');
    add_option('topsy_streaming', 'off');
    add_option('topsy_use_preloader', 'off');
    add_option('topsy_auto_hashtags', 'none');
}

function topsy_comment_exists($item) {
    global $post;
    foreach (get_comments("post_id=$post->ID") as $c) {
        if ($c->comment_author_url == $item->permalink_url) {
            return true;
        }
        if ($c->comment_author == $item->author->name && $c->comment_date == date('Y-m-d H:i:s', $item->date)) {
            return true;
        }
    }
    return false;
}

function topsy_deactivation() {
    // Placeholder, for now, but will keep error messages from happening.
}

function topsy_eval_braces($data) {
    $data = $data[1];
    $num = preg_match_all("/\\$([-\w]+)/", $data, $vars);
    foreach ($vars[1] as $x) {
        global ${$x};
    }
    global $post;
    if ($post) {
        $post_title = $post->post_title;
        $post_author = $post->post_author;
    }
    if (preg_match("/\)-\>/", $data)) {
        $data = preg_replace("/\)-\>/", ')SPLIT->', $data);
        list($obj, $attr) = explode('SPLIT', $data);
        eval("\$obj = $obj;");
        eval("\$ret = \$obj$attr;");
        return $ret;
    }
    if (preg_match("/^\w+$/", $data)) {
        $data = "get_option('$data')";
    }
    eval("\$ret = $data;");
    return $ret;
}
function topsy_eval_data($str) {
    $str = preg_replace_callback("/\[([^\]]+)\]/", "topsy_eval_braces", $str);
    return $str;
}

function topsy_filter_the_content($content) {
    if( is_home()       && get_option('topsy_button_home_display')      == 'off') { return $content; }
    if( is_page()       && get_option('topsy_button_page_display')      == 'off') { return $content; }
    if( is_single()     && get_option('topsy_button_post_display')      == 'off') { return $content; }
    if( is_category()   && get_option('topsy_button_category_display')  == 'off')  { return $content; }
    if( is_archive()    && get_option('topsy_button_archive_display')   == 'off' && !is_category() ) { return $content; }

    if (get_option('topsy_suppress_categories')) {
        $suppress_cats = explode(',', get_option('topsy_suppress_categories'));
        foreach ($suppress_cats as $cat_name) {
            $cat_id = get_cat_id($cat_name);
            if (in_category($cat_id)) { return $content; }
        }
    }
    
    global $post;
    if (get_post_meta($post->ID, 'topsy_button', true) == 'suppress') { return $content; }

    $page = null;
    if ( is_home() ) { $page = 'home'; }
    if ( is_page() ) { $page = 'page'; }
    if ( is_single() ) { $page = 'post'; }
    if ( is_category() ) { $page = 'category'; }
    if ( is_archive() && !is_category() ) { $page = 'archive'; }

    switch ( get_option('topsy_button_' . $page . '_align') ) {
        case 'left':    { $align_css = 'float: left;'; break; }
        case 'right':   { $align_css = 'float: right;'; break; }
        default:        { $align_css = ''; break; }
    }
    $button = topsy_generate_retweet_button(get_option('topsy_button_' . $page . '_style'), $align_css . get_option('topsy_button_'.$page.'_css'));

    switch ( get_option('topsy_button_' . $page . '_position') ) {
        case 'before':  { $content = $button . $content; break; }
        case 'after':   { $content = $content . $button; break; }
    }
    return "\n$content\n";
}

function topsy_filter_the_content_rss($content) {
    if (get_option('topsy_add_button_to_rss')) {
        return topsy_filter_the_content($content);
    }
    return $content;
}

function topsy_fn_backtrace($include_self = false) {
    $ret = array_map(create_function('$i', 'return $i["function"];'), debug_backtrace());
    if (! $include_self) {
        array_shift($ret);
    }
    return $ret;
}
function topsy_generate_retweet_button($size = 'small', $css_opts = '') {
    $which_class = 'data';
    if (! in_array('topsy_filter_the_content', topsy_fn_backtrace())) {
        $which_class = 'shortcode';
    }
    $hostname = 'button';
    $url = urlencode(get_permalink());
    if (preg_match("/[\?&]topsybeta=1/", $_SERVER['REQUEST_URI'], $match)) {
        $hostname = 'beta.button';
        $url .= $match[0];
    }
    global $post;
    $short_url = get_post_meta($post->ID, 'topsy_short_url', true);
    if ($short_url) $short_url = "\", \"shorturl\": \"$short_url";
    $title = str_replace('"', '\"', $post->post_title);
    
    $hashtags = get_option('topsy_auto_hashtags');
    if ($hashtags == 'post' && get_the_tags()) {
        foreach (get_the_tags() as $tag) {
            $title .= ' #' . $tag->name;
        }
    } elseif ($hashtags != 'none' && $hashtags != 'post') {
        foreach (preg_split("/[, ]+/", $hashtags) as $tag) {
            $title .= " #$tag";
        }
    }

    $ret = str_replace('&', '&amp;', "{ \"url\": \"$url$short_url\"") . ", \"style\": \"$size\", \"title\": \"$title\" }";
    $coded = rawurlencode($ret);
    if (get_option('topsy_use_preloader') == 'on') {
        $ret = str_replace('\"', '\\\\\"', $ret);
        $inner_script = "<script type=\"text/javascript\">topsyWidgetPreload($ret);</script>";
    } else {
        $inner_script = '';
    }
    if ($css_opts) {
        $css_opts = trim($css_opts);
        if (substr($css_opts, -1) != ';') $css_opts .= ';';
        $css_opts .= ' ';
    }
    $theme = topsy_simplify(get_option('topsy_button_theme'));
    return "<div class=\"topsy_widget_$which_class topsy_theme_$theme\" style=\"${css_opts}background: url(data:,$coded);\">$inner_script</div>\n";
}

function topsy_get_cache_time($url) {
    global $post;
    return intval(get_post_meta($post->ID, '_topsy_cache_timestamp', true), 10);
}

function topsy_get_plugin_comments($full = false) {
    global $wpdb;
    if ($full) {
        return $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_agent LIKE 'Topsy plugin for WordPress%'");
    } else {
        $comments = $wpdb->get_results("SELECT comment_ID FROM $wpdb->comments WHERE comment_agent LIKE 'Topsy plugin for WordPress%'", ARRAY_N);
        if (! is_array($comments)) return null;
        $comments = array_map(create_function('$i','return $i[0];'), $comments);
        return $comments;
    }
}

function topsy_get_ratelimit($check = 'percent') {
    $limit = get_option('topsy_rl_limit');
    $remaining = get_option('topsy_rl_remaining');
    $reset = get_option('topsy_rl_reset');
    if ($check = 'full') {
        return array(
            'limit' => $limit,
            'remaining' => $remaining,
            'reset' => $reset,
        );
    } elseif ($check == 'reset') {
        return $reset;
    } elseif ($check == 'remaining') {
        return $remaining;
    } elseif ($check == 'limit') {
        return $limit;
    } else {
        $percent = floor($remaining * 100 / $limit);
    }
}

function topsy_handle_single_trackback($item, $tb_url) {
    global $post, $wpdb;
    global $TOPSY_VERSION;
    if (topsy_comment_exists($item)) {
        return;
    }
    if (! isset($item->date) || $item->date < 1000000) {
        return;
    }
    $tweeter_username = $tweeter_realname = $item->author->name;
    $tweeter_url = $item->author->url;
    $tweet_url = $item->permalink_url;
    if (preg_match("/^https?:\/\/(?:[-\w]+\.)?twitter\.com\/(\w{1,15})/", $tweeter_url, $match)) {
        $tweeter_username = $match[1];
    }
    if (in_array(strtolower($tweeter_username), explode('|', strtolower(get_option('topsy_ignore_list'))))) {
        return;
    }
    $tweeter_on_topsy = $item->author->topsy_author_url;
    $tweet_text = $item->content;
    $comment_type = get_option('topsy_comment_type');
    if ($comment_type == 'comment') {
        $comment_type = '';
    }
    if (get_option('topsy_linkify_atuser') == 'on') {
        $tweet_text = topsy_linkify_atuser($tweet_text);
    }
    if (get_option('topsy_linkify_hashtag') == 'on') {
        $tweet_text = topsy_linkify_hashtag($tweet_text);
    }
    
    $comment_data = array(
        'comment_post_ID' => $post->ID,
        'comment_author' => $tweeter_realname,
        'comment_author_url' => $tweet_url,
        'comment_date' => date('Y-m-d H:i:s', $item->date),
        'comment_type' => $comment_type,
        'comment_content' => $wpdb->escape("<span class=\"topsy_trackback_comment\"><span class=\"topsy_twitter_username\"><span class=\"topsy_trackback_content\">$tweet_text</span></span>"),
        'comment_agent' => "Topsy plugin for WordPress v$TOPSY_VERSION",
    );
    $comment_id = wp_insert_comment($comment_data);
}

function topsy_hook_delete_post($post_id) {
    topsy_send_autonotification($post_id, 1);
}
function topsy_hook_publish_post($post_id, $retry = 0) {
    if ($retry > 10) return;
    if (! $retry) {
        topsy_send_autonotification($post_id);
    }
    $post = get_post($post_id);
    $link = get_permalink($post);
    $title = $post->post_title;
    if (get_post_meta($post_id, '_topsy_long_url', true) != $link) {
        $short_url = topsy_shorten_url($link);
        if (substr($short_url, 0, 5) == 'Error') {
            wp_schedule_single_event(time() + 601, 'topsy_retry_shorten_url', array($post_id, $retry + 1));
        } else {
            update_post_meta($post_id, '_topsy_long_url', $link);
            update_post_meta($post_id, 'topsy_short_url', $short_url);
        }
    }
}
function topsy_hook_unpublish_post($new_status = false, $old_status = false, $post = false) {
    if ($old_status == 'publish' && $new_status != 'publish' && $post) {
        topsy_send_autonotification($post->ID, 1);
    }
}

function topsy_http_get($url, $prev = array()) {
    global $topsy_conn_timeout, $TOPSY_VERSION;
    if (preg_match("/^http:\/\/([-\w\.]+)(:(\d+))?(\/[\S]+)?/", $url, $match)) {
        $hostname = $match[1];
        $portno = $match[3] ? $match[3] : 80;
        $remote_path = count($match) == 5 ? $match[4] : '/';
    } else {
        return array('error' => "Invalid URL!");
    }
    $fp = @fsockopen($hostname, $portno, $errno, $errstr, $topsy_conn_timeout);
    if (! $fp) {
        return array('error' => $errstr);
    }
    fwrite($fp, "GET $remote_path HTTP/1.1\r\n");
    fwrite($fp, "Host: $hostname\r\n");
    fwrite($fp, "Accept: application/xml, text/*\r\n");
    fwrite($fp, "Accept-Charset: us-ascii, utf-8, iso-8859-1, unicode-1-1\r\n");
    fwrite($fp, "User-Agent: Topsy plugin for WordPress v$TOPSY_VERSION (PHP v" . phpversion() . ")\r\n");
    fwrite($fp, "Connection: close\r\n");
    fwrite($fp, "\r\n");
    if ($prev == 'nowait') {
        fclose($fp);
        return;
    }
    
    $raw_response = '';
    while (!feof($fp)) {
        $raw_response .= fgets($fp);
    }
    fclose($fp);
    
    list($raw_headers, $raw_body) = preg_split("/\r?\n\r?\n/", $raw_response, 2);
    foreach (preg_split("/\r?\n/", $raw_headers) as $hdr) {
        if (preg_match("/^HTTP\/\d\.\d (\d{3}) (.+)/", $hdr, $match)) {
            $status_code = $match[1];
            $status_text = trim($match[2]);
        } elseif (preg_match("/^([-\w]+):\s+(.+)/", $hdr, $match)) {
            $headers[$match[1]] = trim($match[2]);
        }
    }
    if (substr($status_code, 0, 1) == '3' && array_key_exists('Location', $headers)) {
        if (in_array($headers['Location'], $prev)) {
            return array('error' => "HTTP redirects have looped back to " . $headers['Location']);
        }
        array_push($prev, $url);
        return topsy_http_get($headers['Location'], $prev);
    }
    $trimmed_body = preg_replace("/^[0-9a-fA-F]{4}\r?\n/", '', $raw_body);
    return array(
        'status' => "$status_code $status_text",
        'status_code' => $status_code,
        'status_text' => $status_text,
        'headers' => $headers,
        'body' => $trimmed_body,
        'raw_body' => $raw_body,
        'raw_response' => $raw_response,
        'error' => null,
    );
}

function topsy_linkify_atuser($str) {
    return preg_replace("/@(\w{1,15})/", "<a href=\"http://topsy.com/twitter/$1\">@$1</a>", $str);
}
function topsy_linkify_hashtag($str) {
    return preg_replace("/#(\w{1,20})/", "<a href=\"http://topsy.com/s/$1\">#$1</a>", $str);
}

function topsy_plugin_page_settings_link( $links, $file ) {
 	if( $file == 'topsy/topsy.php' && function_exists( "admin_url" ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=topsy' ) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}

function topsy_retweet_big() {
    print topsy_generate_retweet_button('big');
}
function topsy_retweet_small() {
    print topsy_generate_retweet_button('small');
}

function topsy_self_url() {
    $proto = $_SERVER['HTTPS'] ? 'https' : 'http';
    return "$proto://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function topsy_send_autonotification($post_id, $darken = false) {
    if (get_option('topsy_autonotify_enabled') != 'on') return;
    $post = get_post($post_id);
    $link = get_permalink($post);
    $title = $post->post_title;
    if ($darken) {
        $darken = '&darken=1';
    }
    # Don't send requests for autosaves
    if (preg_match("/(autosave|revision)/", $link)) return;
    # Also, each save triggers the save_post hook *twice*, and the first
    # time doesn't have the right externally-accessible permalink URL.
    # Silently return on that request, too.
    foreach (topsy_fn_backtrace() as $fn) {
        if (preg_match("/^_?wp_(put|save)_post_revision$/", $fn)) return;
    }
    topsy_http_get("http://otter.topsy.com/scrape?title=" . urlencode($title) . "&url=" . urlencode($link) . $darken, 'nowait');
}

function topsy_settings_menu() {
    if (isset($_POST['topsy_save']) && $_POST['topsy_save'])   { topsy_settings_save($_POST); }
    if (isset($_POST['topsy_clear']) && $_POST['topsy_clear'])  { topsy_settings_reset(); }
    if (isset($_POST['topsy_delete_comments']) && $_POST['topsy_delete_comments'] == 'yes') {
        $failed = array();
        $all_comments = topsy_get_plugin_comments();
        $total = count($all_comments);
        if ($total) {
            foreach ($all_comments as $comment_id) {
                if (! wp_delete_comment($comment_id)) {
                    $failed []= $comment_id;
                }
            }
            if (count($failed)) {
                $message = "Out of $total trackback comments, I was unable to delete the $failed comments with the following ID numbers: " . implode('; ', $failed);
            } else {
                $plugin_file = plugin_basename(__FILE__);
                $deact_link = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1', 'deactivate-plugin_' . $plugin_file) . '" title="' . __('Deactivate this plugin') . '">' . __('deactivate this plugin') . '</a>';
                $message = __("All $total trackback comments have been deleted. Note that if you don't $deact_link, the comments will return.", 'topsy');
            }
        } else {
            $message = "Your blog did not have any trackback comments yet. Are you sure you had the &quot;Enable trackback comments&quot; feature turned on?";
        }
        print('
            <div id="message" class="updated fade">
                <p>'.$message.'</p>
            </div>
        ');
    }
    global $TOPSY_VERSION;
    $topsy_nick = get_option('topsy_retweet_username');
    if (! strlen($topsy_nick)) $topsy_nick = 'TopsyRT';

    $all_smallbutton_orders = array(
        'count',
        'retweet',
        'count,retweet',
        'retweet,count',
        'count,badge',
        'badge,count',
        'retweet,badge',
        'badge,retweet',
        'count,retweet,badge',
        'count,badge,retweet',
        'retweet,count,badge',
        'retweet,badge,count',
        'badge,count,retweet',
        'badge,retweet,count',
    );
    $cur_smallbutton_order = get_option('topsy_smallbutton_order');

    if (! function_exists('json_decode') || ! class_exists('Services_JSON')) {
        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'JSON.php');
    }
    topsy_update_shortener_data();
    topsy_update_theme_list();

    $all_themes = get_option('topsy_button_theme_list');
    if (is_string($all_themes)) $all_themes = unserialize($all_themes);
    $cur_theme = get_option('topsy_button_theme');
    
    if (! function_exists('get_categories')) {
        function get_categories() {
            $tmp_ids = get_all_category_ids();
            foreach ($tmp_ids as $this_id) {
                $this_cat = get_category($this_id);
                $this_name = $this_cat->cat_name;
                $tmp_cats[$this_name] = $this_id;
            }
            ksort($tmp_cats);
            foreach ($tmp_cats as $this_name => $this_id) {
                $this_cat = get_category($this_id);
                $all_cats []= $this_cat;
            }
            return $all_cats;
        }
    }

    $all_cats = get_categories(array('orderby' => 'name', 'order' => 'ASC'));
    $suppress_cats = explode(',', get_option('topsy_suppress_categories'));

    $all_shorteners = explode(',', get_option('topsy_urlshortener_list'));
    $cur_shortener = get_option('topsy_url_shortener');
    
    foreach ($all_shorteners as $shortener) {
        $data = get_option('topsy_urlshortener_data_'. $shortener);
        if (! is_object($data)) $data = unserialize($data);
        $s_data[$shortener] = $data;
    }
    
    $topsy_auto_hashtags = get_option('topsy_auto_hashtags');
    if (! $topsy_auto_hashtags) $topsy_auto_hashtags = 'none';
    ?>

    <style type="text/css" media="screen">
        input.topsy_disabled {
            background-color: #ddd;
            border-color: #999;
        }
    </style>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script>
    <script type="text/javascript" src="http://cdn.topsy.com/topsy.js?init=topsyWidgetCreator"></script>
    <script type="text/javascript" id="topsy_global_settings">
        var topsy_theme = '<?php echo get_option('topsy_button_theme') ?>';
    </script>
    <script type="text/javascript">
        function topsyTrackbackSettingsDisplayer() {
            if ($("#topsy_trackbacks_enabled").is(':checked')) {
                $("#topsy_trackback_comments_config").slideDown(850);
            } else {
                $("#topsy_trackback_comments_config").slideUp(850);
            }
        }
        function topsyMockupRebuilder(n) {
            if (n == null) n = 0;
            if (n > 10) return;
            if (! $('#topsy_example_with_badge .topsy-sm')) {
                setTimeout(function(){topsyMockupRebuilder(n + 1);}, 1000);
                return;
            }
            var order = $('#topsy_smallbutton_order option:selected').val();
            topsyMockupMakeOrderBig(order, $('#topsy_theme_sample_big .topsy-big'));
            topsyMockupMakeOrderSmall(order, $('#topsy_theme_sample_small .topsy-sm'));
            topsyMockupMakeOrderBigBadge(order, $('#topsy_theme_sample_big_badge .topsy-big'));
            topsyMockupMakeOrderSmall(order, $('#topsy_theme_sample_small_badge .topsy-sm'));
        }
        function topsyMockupMakeOrderBig(order, branch) {
            branch = branch.get(0);
            if (! branch) return;
            var count = $(branch).find('.topsy-big-total').get(0);
            var retweet = $(branch).find('.topsy-big-retweet').get(0);
            var items = order.split(',');
            
            if (jQuery.inArray('count', items) == -1) { $(count).hide(); } else { $(count).show(); }
            if (jQuery.inArray('retweet', items) == -1) { $(retweet).hide(); } else { $(retweet).show(); }
        }
        function topsyMockupMakeOrderBigBadge(order, branch) {
            branch = branch.get(0);
            if (! branch) return;
            var count = $(branch).find('.topsy-big-total-badge').get(0);
            var retweet = $(branch).find('.topsy-big-retweet').get(0);
            var badge = $(branch).find('.topsy-big-badge').get(0);
            var items = order.split(',');
            
            if (jQuery.inArray('count', items) == -1) { $(count).hide(); } else { $(count).show(); }
            if (jQuery.inArray('retweet', items) == -1) { $(retweet).hide(); } else { $(retweet).show(); }
            if (jQuery.inArray('badge', items) == -1) { $(badge).hide(); } else { $(badge).show(); }
        }
        function topsyMockupMakeOrderSmall(order, branch) {
            branch = branch.get(0);
            if (! branch) return;
            
            var count = $(branch).find('.topsy-sm-total').get(0);
            var retweet = $(branch).find('.topsy-sm-retweet').get(0);
            var badge = $(branch).find('.topsy-sm-badge').get(0);

            $([count, retweet, badge]).each(function(n, item) {
                if (item) $(item).hide();
            });
            $(order.split(',')).each(function(n, item) {
                switch (item) {
                    case 'count':
                        branch.appendChild(count);
                        $(count).show();
                        break;
                    case 'retweet':
                        branch.appendChild(retweet);
                        $(retweet).show();
                        break;
                    case 'badge':
                        if (badge !== undefined) {
                            branch.appendChild(badge);
                            $(badge).show();
                        }
                        break;
                }
            });
        }
        function topsyNewCss(code) {
            if (document.getElementById(code) == null) {
                var head    = document.getElementsByTagName('head')[0];
                var link    = document.createElement('link');
                link.rel    = 'stylesheet';
                link.type   = 'text/css';
                link.id     = 'topsy-widget-css-' + code;
                link.href   = 'http://cdn.topsy.com/css/widget.' + code + '.css';
                head.appendChild(link);
            }
        }
        function topsySetButtonTheme() {
            var theme = $('#topsy_button_theme option:selected').val();
            $('#topsy_theme_sample_small').attr("className", function() { return this.className.replace(/topsy_theme_[a-z0-9-]+/, 'topsy_theme_' + theme); });
            $('#topsy_theme_sample_big').attr("className", function() { return this.className.replace(/topsy_theme_[a-z0-9-]+/, 'topsy_theme_' + theme); });
            $('#topsy_theme_sample_small_badge').attr("className", function() { return this.className.replace(/topsy_theme_[a-z0-9-]+/, 'topsy_theme_' + theme); });
            $('#topsy_theme_sample_big_badge').attr("className", function() { return this.className.replace(/topsy_theme_[a-z0-9-]+/, 'topsy_theme_' + theme); });
        }
        function topsyUpdateButtonText() {
            $('#topsy_theme_sample_small a.topsy-sm-retweet').text($('#topsy_button_text_retweet').val());
            $('#topsy_theme_sample_big a.topsy-big-retweet').text($('#topsy_button_text_retweet').val());
            $('#topsy_theme_sample_small_badge a.topsy-sm-retweet').text($('#topsy_button_text_retweet').val());
            $('#topsy_theme_sample_big_badge a.topsy-big-retweet').text($('#topsy_button_text_retweet').val());
        }
        function topsyCheckHashtags(page_init) {
            if ($("#topsy_auto_hashtags_custom").is(':checked')) {
                $("#topsy_auto_hashtags_text").attr('disabled', false);
                $("#topsy_auto_hashtags_text").removeClass('topsy_disabled');
                if (page_init !== true) $("#topsy_auto_hashtags_text").focus();
            } else {
                $("#topsy_auto_hashtags_text").attr('disabled', 'disabled');
                $("#topsy_auto_hashtags_text").addClass('topsy_disabled');
            }
        }
        $(document).ready(function() {
            $('.topsy_trackbacks_trigger').click(topsyTrackbackSettingsDisplayer);
            $('#topsy_smallbutton_order').mouseup(topsyMockupRebuilder);
            $('#topsy_smallbutton_order').keyup(topsyMockupRebuilder);
            $('#topsy_smallbutton_order').change(topsyMockupRebuilder);
            $('#topsy_button_theme').mouseup(topsySetButtonTheme);
            $('#topsy_button_theme').keyup(topsySetButtonTheme);
            $('#topsy_button_theme').change(topsySetButtonTheme);
            $('#topsy_auto_hashtags_none').click(topsyCheckHashtags);
            $('#topsy_auto_hashtags_post').click(topsyCheckHashtags);
            $('#topsy_auto_hashtags_custom').click(topsyCheckHashtags);
            $('#topsy_button_text_retweet').keyup(topsyUpdateButtonText);
            setTimeout(function() {
                topsyMockupRebuilder();
                topsyTrackbackSettingsDisplayer();
                topsySetButtonTheme();
                topsyCheckHashtags(true);
            }, 1500);
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
<?php foreach ($all_themes as $theme_name) { print "            topsyNewCss('". topsy_simplify($theme_name) . "');\n"; } ?>
        });
    </script>
    <script type="text/javascript">
        String.prototype.topsy_ucfirst = function(lowercase) {
            var str = this;
            if (lowercase) str = str.toLowerCase();
            return str.substr(0, 1).toUpperCase() + str.substr(1);
        };
        function topsy_require_text() {
            if (! $('#topsy_urlshortener_username').val()) {
                alert("You must enter your authentication data in the two fields under \"URL Shortener\".");
                return false;
            }
            if (! $('#topsy_urlshortener_authkey').val()) {
                alert("You must enter your authentication data in the two fields under \"URL Shortener\".");
                return false;
            }
        }
        function topsy_update_url_shorteners() {
<?php
// The new Services_JSON->encode() supplied with WordPress 2.9 tries to emit
// a Content-type: text/javascript header, causing a "Cannot modify header 
// information" warning message which breaks the JSON object.
$err_reporting = error_reporting(E_ERROR); ?>
            var s_data = <?php echo json_encode($s_data); ?>;
<?php error_reporting($err_reporting); ?>
            var shortener = $('#topsy_url_shortener').val();
            $('#topsy_urlshortener_username_label').text(s_data[shortener]['service_username_label'].topsy_ucfirst());
            $('#topsy_urlshortener_authkey_label').text(s_data[shortener]['service_authkey_label'].topsy_ucfirst());
            if (s_data[shortener]['service_auth_reqd']) {
                $('#topsy_urlshortener_auth_verbiage').html("This service requires authentication. You must enter your " + s_data[shortener]['service_username_label'] + " and " + s_data[shortener]['service_authkey_label'] + " below. If you're already logged into your " + s_data[shortener]['service_name'] + " account, you can find those by going to <a href=\"" + s_data[shortener]['service_userinfo_link'] + "\">" + s_data[shortener]['service_userinfo_link'] + "</a>.");
                $('#save').click(topsy_require_text);
            } else {
                $('#topsy_urlshortener_auth_verbiage').html("Optional: Enter your " + s_data[shortener]['service_username_label'] + " and " + s_data[shortener]['service_authkey_label'] + " below, so your short URLs will be associated with your account. (If you are already logged into your " + s_data[shortener]['service_name'] + " account, you can find these out by going to <a href=\"" + s_data[shortener]['service_userinfo_link'] + "\">" + s_data[shortener]['service_userinfo_link'] + "</a>.)");
                $('#save').unbind('click', topsy_require_text);
            }
            if (s_data[shortener]['service_auth_possible']) {
                $('#topsy_urlshortener_authdata').show();
            } else {
                $('#topsy_urlshortener_authdata').hide();
            }
        }
        $(document).ready(function() {
            topsy_update_url_shorteners();
            $('#topsy_url_shortener').change(topsy_update_url_shorteners);
        });
    </script>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <div class="wrap">
        <h2><?php _e('Topsy Retweet Button Setup','topsy') ?></h2>
        <p>You are using Topsy Retweet Button v<?php echo $TOPSY_VERSION ?>. You can check on Topsy's status at <a href="http://status.topsy.com/">status.topsy.com</a>. See <a href="http://wordpress.org/extend/plugins/topsy/faq/">the FAQ/user guide</a> for answers to common support questions.</p>

        <h3><?php _e('Retweet Options', 'topsy') ?></h3>
        <div style="margin-left: 2em;">
            
            <p>
                <strong>Retweet Username</strong>
            </p>
            <p>
                Retweet buttons on your blog will create tweets that attribute posts to: RT @<input type="text" name="topsy_retweet_username" value="<?php echo get_option('topsy_retweet_username');?>" />
            </p>
            <p>
                <strong>URL Shortener</strong>
            </p>
            <p>
                Topsy Widgets will automatically create short URLs for your posts. You can choose your preferred URL shortener here.
            </p>
            <p style="float: left;">
                Shortening Service: 
                <select name="topsy_url_shortener" id="topsy_url_shortener">
<?php
foreach ($s_data as $key => $data) {
    print "                    <option value=\"$key\"";
    if ($key == $cur_shortener) print " selected=\"selected\"";
    print ">$data->service_name&nbsp;&nbsp;&nbsp;</option>\n";
}
?>
                </select>
            </p>
            <p id="topsy_urlshortener_authdata" style="display: none; margin-left: 18em; padding-top: 1em;">
                <span id="topsy_urlshortener_auth_verbiage">stuff</span><br>
                <span id="topsy_urlshortener_username_label">User Name</span>: <input type="text" name="topsy_urlshortener_username" id="topsy_urlshortener_username" value="<?php echo get_option('topsy_urlshortener_username'); ?>"><br>
                <span id="topsy_urlshortener_authkey_label">Password</span>: <input type="text" name="topsy_urlshortener_authkey" id="topsy_urlshortener_authkey" value="<?php echo get_option('topsy_urlshortener_authkey'); ?>">
            </p>
            <p style="clear: left;">
                <strong>Automatic Hashtags</strong>
            </p>
            <p>
                You can have the Topsy Widget automatically add hashtags to your retweets:<br>
                <br>
                <label><input type="radio" name="topsy_auto_hashtags" id="topsy_auto_hashtags_none" value="none"<?php if ($topsy_auto_hashtags == 'none') { echo ' checked="checked"'; } ?> /> Do not add any automatic hashtags</label><br>
                <label><input type="radio" name="topsy_auto_hashtags" id="topsy_auto_hashtags_post" value="post"<?php if ($topsy_auto_hashtags == 'post') { echo ' checked="checked"'; } ?> <?php if (! function_exists('get_the_tags')) { echo 'disabled="disabled" '; } ?>/> <?php if (! function_exists('get_the_tags')) { echo '<span style="text-decoration: line-through;">'; } ?>Take hashtags from tags used by the post</label><?php if (! function_exists('get_the_tags')) { echo "</span> Sorry, this feature requires WordPress 2.3 or greater."; } ?><br>
                <label><input type="radio" name="topsy_auto_hashtags" id="topsy_auto_hashtags_custom" value="custom"<?php if ($topsy_auto_hashtags != "none" && $topsy_auto_hashtags != "post") { echo ' checked="checked"'; } ?> /> Always add the following tags (separated by commas)</label><br>
                <input type="text" name="topsy_auto_hashtags_text" id="topsy_auto_hashtags_text" value="<?php if ($topsy_auto_hashtags != "none" && $topsy_auto_hashtags != "post") { echo $topsy_auto_hashtags; } ?>" />
            </p>
        </div>


        <h3 style="clear: left;"><?php _e('Visual Appearance','topsy') ?></h3>

        <div style="margin-left: 2em;">
            <div style="float: right; margin-left: 2em; padding: 1em; border: thin groove #999;">
                Your Topsy buttons will look like this:<br><br>
                <div id="topsy_theme_sample_big" class="topsy_widget_data topsy_theme_<?php echo $cur_theme?>" style="float: left; margin-right: 3em;"><!-- { "url": "http://topsy.com", "retweet_text": "<?php echo get_option('topsy_button_text_retweet')?>", "style": "big" } --></div>
                <div id="topsy_theme_sample_small" class="topsy_widget_data topsy_theme_<?php echo $cur_theme?>" style="float: left; margin-right: 3em;"><!-- { "url": "http://topsy.com", "retweet_text": "<?php echo get_option('topsy_button_text_retweet')?>", "style": "small" } --></div>

                <br clear="all">
                <br>
                Versions with Toplinks badges:<br><br>
                <div id="topsy_theme_sample_big_badge" class="topsy_widget_data topsy_theme_<?php echo $cur_theme?>" style="float: left; margin-right: 3em;"><!-- { "url": "http://lifehacker.com/5401954/programmer-101-teach-yourself-how-to-code", "retweet_text": "<?php echo get_option('topsy_button_text_retweet')?>", "style": "big" } --></div>
                <div id="topsy_theme_sample_small_badge" class="topsy_widget_data topsy_theme_<?php echo $cur_theme?>" style="float: left; margin-right: 3em;"><!-- { "url": "http://lifehacker.com/5401954/programmer-101-teach-yourself-how-to-code", "retweet_text": "<?php echo get_option('topsy_button_text_retweet')?>", "style": "small" } --></div>

            </div>


            <p>
                <strong>Element Selection and Order</strong>
            </p>
            <p>
                Use this drop-down control to select which elements you want in your button (tweet count, retweet button, and Toplinks badge). You can also select the order you want the elements to appear in a small-size button.<br>
                <select name="topsy_smallbutton_order" id="topsy_smallbutton_order">
<?php foreach ($all_smallbutton_orders as $val) {
    print "                        <option value=\"$val\"";
    if ($cur_smallbutton_order == $val) print " selected=\"selected\"";
    print ">";
    print preg_replace("/,/", ', ', ucfirst($val)) . "</option>\n";
} ?>
                </select>
            </p>
            
            <p>
                <strong>Color Theme</strong>
            </p>
            <p>
                Select the color theme you want to use for your retweet buttons:<br>
                <select name="topsy_button_theme" id="topsy_button_theme">
<?php
foreach ($all_themes as $theme_name) {
    $theme_opt = topsy_simplify($theme_name);
    print "                    <option value=\"$theme_opt\"";
    if ($cur_theme == $theme_opt) print ' selected="selected"';
    print ">$theme_name</option>\n";
}
?>
                </select>
            </p>
            
            <p style="clear: left;">
                <strong>Streaming</strong>
            </p>
            <p>
                <label><input type="checkbox" name="topsy_streaming" id="topsy_streaming" <?php print (get_option('topsy_streaming') == 'on') ? 'checked="checked" ' : ''; ?>/> Automatically update retweet counts in real time</label>
            </p>
            
            <p>
                <strong>Retweet Button Text</strong>
            </p>
            <p>
                You can adjust the wording of the Retweet button (for example, "Retwittear" for a Spanish site, or "Twittern!" for a German one). The button will say "Tweet" when nobody has tweeted about it yet, and "Retweet" afterwards.
            </p>
            <p>
                <div style="float: left; margin-right: 2em;">
                    Text for "Tweet" button:<br>
                    <input type="text" name="topsy_button_text_tweet" id="topsy_button_text_tweet" value="<?php echo get_option('topsy_button_text_tweet')?>" size="30" />
                </div>
                <div style="float: left; margin-bottom: 2em;">
                    Text for "Retweet" button:<br>
                    <input type="text" name="topsy_button_text_retweet" id="topsy_button_text_retweet" value="<?php echo get_option('topsy_button_text_retweet')?>" size="30" />
                </div>
            </p>
            
            <p style="clear: left;">
                <strong>Preload Retweet Button</strong>
            </p>
            <p>
                <label><input type="checkbox" name="topsy_use_preloader" id="topsy_use_preloader" <?php print (get_option('topsy_use_preloader') == 'on') ? 'checked="checked" ' : ''; ?>/> Preload static retweet button before fetching count/badge data (Note: May cause raw "gibberish" code to appear in some themes; <a href="http://wordpress.org/extend/plugins/topsy/faq/">see the Topsy plugin FAQ</a> for details.)</label>
            </p>
        </div>



        <h3><?php _e('Button Placement', 'topsy') ?></h3>

        <div style="margin-left: 2em;">
            <p>
                You can add the Topsy button to your blog's theme with <code>topsy_retweet_small()</code> or <code>topsy_retweet_big()</code> codes&nbsp;&mdash; <a href="http://wordpress.org/extend/plugins/topsy/faq/">see the Topsy plugin FAQ</a> for details. <?php if (function_exists('add_shortcode')) { ?> You can also include it anywhere in your posts with the tags <code>[topsy_retweet_small]</code> or <code>[topsy_retweet_big]</code>.<?php } ?>
            </p>
            <p style="display: none;">The Retweet Button displays the total number of tweets associated with your content. The button also has a link to retweet your content. You can <?php if (function_exists('add_shortcode')) { ?> include it anywhere in your posts with the tags <code>[topsy_retweet_small]</code> or <code>[topsy_retweet_big]</code>. You can also <?php } ?> add it to themes with <code>&lt;?php echo topsy_retweet_small() ?&gt;</code> or <code>&lt;?php echo topsy_retweet_big() ?&gt;</code>.</p><?php if (! function_exists('add_shortcode')) { ?>
            <p><strong>Note:</strong> Your WordPress version is only <?php print get_bloginfo('version'); ?>, which is a bit behind the times. If you upgrade your WordPress software to at least version 2.5, you can use shortcodes&nbsp;&mdash; such as <code>[topsy_retweet_small]</code>&nbsp;&mdash; to place the Retweet Button anywhere inside your posts. You'll also be much more secure against attacks if you upgrade to the latest version.</p>
            <?php } ?>
            <table class="form-table">
                <tr valign="top">
                    <th><?php _e('Display on page','topsy') ?></th>
                    <th><?php _e('Position Button...','topsy') ?></th>
                    <th><?php _e('Float?','topsy') ?></th>
                    <th><?php _e('Button style','topsy') ?></th>
                    <th><?php _e('Additional CSS','topsy') ?></th>
                </tr>
                <?php 
                    $pages = array(
                        'home' => 'Main Blog Page',
                        'page' => 'Static Content Page',
                        'post' => 'Individual Entry',
                        'category' => 'Category Listing',
                        'archive' => 'Archive Page',
                    );
                    foreach ($pages as $page => $label) { 
                        ?>
                        <tr valign="top">
                            <td>
                                <label><input type="checkbox" name="topsy_button_<?php print $page ?>_display" <?php print (get_option('topsy_button_'. $page . '_display') == 'on') ? ' checked="checked"' : ''; ?>> <?php _e($label, 'topsy') ?></label>
                            </td>
                            <td>
                                <select name="topsy_button_<?php print $page ?>_position">
                                    <option value="before" <?php print (get_option('topsy_button_' . $page . '_position') == 'before') ? ' selected="selected"' : ''; ?>/> <?php _e('Before Content','topsy') ?></option>
                                    <option value="after" <?php print (get_option('topsy_button_' . $page . '_position') == 'after') ? ' selected="selected"' : ''; ?>/> <?php _e('After Content','topsy') ?></option>
                                </select>
                            </td>
                            <td>
                                <select name="topsy_button_<?php print $page ?>_align">
                                    <option value="none" <?php print (get_option('topsy_button_' . $page . '_align') == 'none') ? ' selected="selected"' : ''; ?>/> <?php _e('None','topsy') ?></option>
                                    <option value="left" <?php print (get_option('topsy_button_' . $page . '_align') == 'left') ? ' selected="selected"' : ''; ?>/> <?php _e('Left','topsy') ?></option>
                                    <option value="right" <?php print (get_option('topsy_button_' . $page . '_align') == 'right') ? ' selected="selected"' : ''; ?>/> <?php _e('Right','topsy') ?></option>
                                </select>
                            </td>
                            <td>
                                <select name="topsy_button_<?php print $page ?>_style" class="topsy_button_size_selector">
                                <option value="big"   <?php print (get_option('topsy_button_' . $page . '_style') == 'big')     ? ' selected="selected"' : ''; ?>/> <?php _e('Big','topsy') ?></option>
                                <option value="small" <?php print (get_option('topsy_button_' . $page . '_style') == 'small')   ? ' selected="selected"' : ''; ?>/> <?php _e('Small','topsy') ?></option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="topsy_button_<?php print $page ?>_css" value="<?php print get_option('topsy_button_'.$page.'_css') ?>"/>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
            <br>
            <p>
                <strong>Add Button to RSS Feeds?</strong>
            </p>
            <p>
                <label><input type="checkbox" name="topsy_add_button_to_rss" id="topsy_add_button_to_rss" <?php print (get_option('topsy_add_button_to_rss') == 'on') ? 'checked="checked" ' : ''; ?>/> Add the Retweets Button to RSS feeds as well as pages on the web site</label>
            </p>
        </div>
        
        
        
        <h3><?php _e('Exclude Categories', 'topsy') ?></h3>
        <div>
<?php if (count($all_cats) > 1 || $all_cats[0]->cat_name != 'Uncategorized') { ?>
            <p>
                If a post has any of the categories selected below, the Topsy button will not be displayed on it. Note: this only affects the automatic topsy button; shortcodes in the post's content will still be displayed.
            </p>
            <p style="margin-left: 2em;">
<?php foreach ($all_cats as $cat) { ?>
                <label><input type="checkbox" name="topsy_suppress_<?php print $cat->category_nicename; ?>" <?php if (in_array($cat->category_nicename, $suppress_cats)) { print 'checked="checked" '; } ?>/>&nbsp;<?php print $cat->cat_name; ?></label><br>
<?php } ?>
            <p>
<?php } else { ?>
            <p>
                You are only using the default "Uncategorized" category. Please add another category, or rename the current one to something else, in order to enable this feature.
            </p>
<?php } ?>
        </div>

<!--
        <h3><?php _e('Keep Topsy Updated', 'topsy') ?></h3>
        <div>
            <div>
                <input type="checkbox" name="topsy_autonotify_enabled" id="topsy_autonotify_enabled" <?php print (get_option('topsy_autonotify_enabled') == 'on') ? ' checked="checked"' : ''; ?> /> <label for="topsy_autonotify_enabled"><strong>Notify Topsy on post publication/deletion</strong></label>
            </div>
            <div style="margin-left: 2em;">
                <p>
                    Automatically notifies Topsy when you publish or unpublish a blog entry. This has two benefits: First, it ensures that the Retweet Button will create retweets with just the post title, not including your blog title. (For example: "RT @<?php echo $topsy_nick ?> My Latest Blog Post" instead of "RT @<?php echo $topsy_nick ?> My Latest Blog Post &mdash; <?php echo get_bloginfo('name') ?>".) <?php if (version_compare('2.3', get_bloginfo('version'), '<=')) { ?>Also, if you delete or unpublish a post, it will be immediately removed from Topsy's index.<?php } ?>
                </p>
                <p>
                    If you don't want Topsy to be updated when you publish or unpublish things, you can turn this off. However, your retweets will all have your blog title in them, as well as the article title.
                </p>
            </div>
        </div>
        
-->

        
        <h2><?php _e('Trackback Comments','topsy') ?></h2>
        <div>
            <div>
                <input type="checkbox" name="topsy_trackbacks_enabled" id="topsy_trackbacks_enabled" class="topsy_trackbacks_trigger" <?php print (get_option('topsy_trackbacks_enabled') == 'on') ? ' checked="checked"' : ''; ?> /> <label for="topsy_trackbacks_enabled" class="topsy_trackbacks_trigger"><strong>Enable trackback comments</strong></label>
            </div>
            <div id="topsy_trackback_comments_config">
                <div style="margin-left: 2em;">
                    This will add comments to your posts when people link to them in tweets.
                    <p>
                        <input type="radio" name="topsy_trackbacks_infonly" value="on" id="topsy_trackbacks_infonly_on"<?php print (get_option('topsy_trackbacks_infonly') == 'on') ? ' checked="checked"' : ''; ?> /><label for="topsy_trackbacks_infonly_on">&nbsp;Only create trackback comments from "influential" tweeters</label> <span style="font-size: 75%;">[<a href="http://labs.topsy.com/influence/">What's an "influential" person?</a>]</span><br>
                        <input type="radio" name="topsy_trackbacks_infonly" value="off" id="topsy_trackbacks_infonly_off"<?php print (get_option('topsy_trackbacks_infonly') == 'off') ? ' checked="checked"' : ''; ?> /><label for="topsy_trackbacks_infonly_off">&nbsp;Create trackback comments from anyone and everyone</label>
                    </p>
                    <p>
                        <strong>Comment Display Style</strong><br>
                        Some WordPress themes display trackbacks oddly. Displaying as comments may be preferable.
                    </p>
                    <p>
                        <input type="radio" name="topsy_comment_type" value="trackback" id="topsy_comment_type_trackback"<?php print (get_option('topsy_comment_type') == 'trackback') ? ' checked="checked"' : ''; ?> /><label for="topsy_comment_type_trackback">&nbsp;Display trackback comments as trackbacks</label><br>
                        <input type="radio" name="topsy_comment_type" value="comment" id="topsy_comment_type_comment"<?php print (get_option('topsy_comment_type') == 'comment') ? ' checked="checked"' : ''; ?> /><label for="topsy_comment_type_comment">&nbsp;Display trackback comments as comments</label>
                    </p>
                    <p>
                        <strong>Ignore tweets by the following Twitter usernames</strong><br>
                        (One username per line. You can put your own username here to keep from having trackbacks from your own tweets. You can also use this to block spammers. Just enter the username(s), with no @ signs.)<br>
                        <textarea name="topsy_ignore_list" id="topsy_ignore_list" style="width: 12em; height: 8em;"><?php echo str_replace('|', "\n", get_option('topsy_ignore_list')) ?></textarea>
                    </p>
                </div>
                <h3><?php _e('Delete Trackback Comments','topsy') ?></h3>
                <div style="margin-left: 2em;">
                    <p>
                        If your trackback comments have gotten messed up somehow, you can use this button to delete all Topsy-generated trackback comments on your blog. This will only affect comments created by the Topsy plugin. <strong>Please note</strong> that if you don't disable trackback comments, the comments will be recreated again over the next day or two.
                    </p>
                    <p style="margin-bottom: 2.5em;">
                        <input type="hidden" name="topsy_delete_comments" id="topsy_delete_comments" value="no" />
                        <a href="#" onclick="if (confirm('Are you sure you want to delete all trackback comments created by the Topsy plugin?')) { document.getElementById('topsy_delete_comments').value = 'yes'; document.forms[0].submit(); } else { return false; }" style="border-top: 2px solid #eee; border-left: 2px solid #eee; border-right: 2px solid #888; border-bottom: 2px solid #888; background-color: #ccc; text-decoration: none; color: black; padding: 2px 10px;">Delete Topsy-Created Trackback Comments</a>
                    </p>
                </div>
            </div>
        </div>
        <br/>
        <span class="submit">
            <input name="topsy_save" id="save" value="<?php _e('Save Changes', 'topsy') ?>" type="submit"/>
            <input name="topsy_clear" id="reset" value="<?php _e('Reset Options', 'topsy') ?>" type="submit"/>
        </span>
        </div>
    </form>
    
    <?php

}

function topsy_settings_reset() {
    update_option('topsy_trackbacks_enabled', 'on');
    update_option('topsy_trackbacks_infonly', 'off');
    update_option('topsy_linkify_hashtag', 'on');
    update_option('topsy_linkify_atuser', 'on');
    update_option('topsy_ignore_list', '');
    update_option('topsy_comment_type', 'trackback');
    $pages = array('home', 'page', 'post', 'category', 'archive');
    foreach ( $pages as $page ) {
        update_option('topsy_button_' . $page . '_display',     'on');
        update_option('topsy_button_' . $page . '_position',    'before');
        update_option('topsy_button_' . $page . '_align',       'right');
        update_option('topsy_button_' . $page . '_style',       'big');
        update_option('topsy_button_' . $page . '_css',         'margin-left: 0.75em');
    }
    update_option('topsy_add_button_to_rss', 'off');
    update_option('topsy_autonotify_enabled', 'on');
    update_option('topsy_smallbutton_order', 'count,badge,retweet');
    update_option('topsy_url_shortener', 'bitly');
    update_option('topsy_urlshortener_username', 'topsyplugin');
    update_option('topsy_urlshortener_authkey', 'R_0bb5fbe4ce1eb830679766704df41937');
    topsy_update_shortener_data();
    update_option('topsy_button_theme', 'blue');
    update_option('topsy_button_text_tweet', 'tweet');
    update_option('topsy_button_text_retweet', 'retweet');
    update_option('topsy_suppress_categories', '');
    update_option('topsy_streaming', 'off');
    update_option('topsy_use_preloader', 'off');
    update_option('topsy_auto_hashtags', 'none');
}

function topsy_settings_save($settings) {
    update_option('topsy_trackbacks_enabled', ($settings['topsy_trackbacks_enabled'] == 'on') ? 'on' : 'off' );
    update_option('topsy_trackbacks_infonly', ($settings['topsy_trackbacks_infonly'] == 'on') ? 'on' : 'off' );
    update_option('topsy_linkify_atuser', ($settings['topsy_linkify_atuser'] == 'on') ? 'on' : 'off' );
    update_option('topsy_linkify_hashtag', ($settings['topsy_linkify_hashtag'] == 'on') ? 'on' : 'off' );
    update_option('topsy_comment_type', $settings['topsy_comment_type']);
    update_option('topsy_ignore_list', implode('|', preg_split("/\s+/", trim($settings['topsy_ignore_list']))));
    $pages = array('home', 'page', 'post', 'category', 'archive');
    foreach ($pages as $page) {
        update_option('topsy_button_' . $page . '_display',     ($settings['topsy_button_' . $page . '_display'] == 'on') ? 'on' : 'off' );
        update_option('topsy_button_' . $page . '_position',     $settings['topsy_button_' . $page . '_position'] );
        update_option('topsy_button_' . $page . '_align',        $settings['topsy_button_' . $page . '_align'] );
        update_option('topsy_button_' . $page . '_style',        $settings['topsy_button_' . $page . '_style'] );
        update_option('topsy_button_' . $page . '_css',          $settings['topsy_button_' . $page . '_css'] );
    }
    update_option('topsy_add_button_to_rss', ($settings['topsy_add_button_to_rss'] == 'on') ? 'on' : 'off' );
    update_option('topsy_autonotify_enabled', ($settings['topsy_autonotify_enabled'] == 'on') ? 'on' : 'off' );
    update_option('topsy_smallbutton_order', $settings['topsy_smallbutton_order']);
    update_option('topsy_url_shortener', $settings['topsy_url_shortener']);
    update_option('topsy_urlshortener_username', $settings['topsy_urlshortener_username']);
    update_option('topsy_urlshortener_authkey', $settings['topsy_urlshortener_authkey']);
    update_option('topsy_button_theme', $settings['topsy_button_theme']);
    update_option('topsy_button_text_tweet', $settings['topsy_button_text_tweet']);
    update_option('topsy_button_text_retweet', $settings['topsy_button_text_retweet']);
    $rt_username = $settings['topsy_retweet_username'];
    if (strrchr($rt_username, '@')) $rt_username = substr(strrchr($rt_username, '@'), 1);
    update_option('topsy_retweet_username', $rt_username);
    $suppress_cats = array();
    foreach ($settings as $key => $value) {
        if (substr($key, 0, 15) == 'topsy_suppress_' && $value == 'on') {
            $suppress_cats []= substr($key, 15);
        }
    }
    update_option('topsy_suppress_categories', implode(',', $suppress_cats));
    update_option('topsy_streaming', ($settings['topsy_streaming'] == 'on') ? 'on' : 'off' );
    update_option('topsy_use_preloader', ($settings['topsy_use_preloader'] == 'on') ? 'on' : 'off' );
    if ($settings['topsy_auto_hashtags'] == 'custom') {
        update_option('topsy_auto_hashtags', $settings['topsy_auto_hashtags_text']);
    } else {
        update_option('topsy_auto_hashtags', $settings['topsy_auto_hashtags']);
    }
    
}

function topsy_shorten_url($long_url) {
    $shortener = get_option('topsy_url_shortener');
    $l_url = $long_url;
    
    # Need to globalize this var so topsy_eval_data/brackets can pick it up
    global $long_url;
    
    $long_url = $l_url;
    $s_data = get_option('topsy_urlshortener_data_'. $shortener);
    if (! is_object($s_data)) $s_data = unserialize($s_data);
    if ($s_data->http_method != 'get') {
        # We'll need to write code to perform POST requests if this ever happens.
        # For now, just bail out.
        return;
    }
    foreach ($s_data->http_reqd_vars as $r_var => $r_val) {
        $r_val = topsy_eval_data($r_val);
        $opts []= $r_var . '=' . urlencode($r_val);
    }
    foreach ($s_data->http_opt_vars as $r_var => $r_val) {
        $r_val = topsy_eval_data($r_val);
        if ($r_val) {
            $opts []= $r_var . '=' . urlencode($r_val);
        }
    }
    $all_opts = implode('&', $opts);
    
    $http_data = topsy_http_get($s_data->http_url . "?$all_opts");
    if (isset($http_data['error'])) return "Error: " . $http_data['error'];

    switch ($s_data->decode) {
        case 'json':
            if (! function_exists('json_decode') || ! class_exists('Services_JSON')) {
                require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'JSON.php');
            }
            # bit.ly sends the original URL as an object key, but the colon and
            # slashes make it impossible to dereference after it's been turned
            # into a PHP data structure. Just replace the URL with "ORIG_URL"
            # so we can actually access the damn thing.
            $preg_url = str_replace('/', '\/', preg_quote($long_url));
            $http_data['body'] = preg_replace('/"' . $preg_url . '"\s*:/', '"ORIG_URL" :', $http_data['body']);
            
            $web_data = json_decode($http_data['body']);
            break;
        case 'xml':
            # Warning: This will only work in PHP5+
            $web_data = new SimpleXMLElement($http_data['body']);
            break;
        default:
            # is.gd gives back HTML in "chunked" responses, as per RFC2616 section 3.6.1
            # (see http://tools.ietf.org/html/rfc2616.html#section-3.6.1).
            # Strip the leading and trailing numbers to get the real content.
            $web_data = preg_replace("/^\d+\s+/", '', preg_replace("/\s+\d+$/", '', trim($http_data['body'])));
    }
    
    if ($s_data->shorturl_key) {
        $s_key = '$web_data->' . $s_data->shorturl_key;
        eval("\$short_url = $s_key;");
    } else {
        $short_url = $web_data;
    }
    return $short_url;
}

function topsy_simplify($str) {
    $str = trim(strtolower($str));
    $str = preg_replace("/[^ \w-]/", '', $str);
    $str = preg_replace("/[^a-z0-9]+/", '-', $str);
    return $str;
}
function topsy_trackback_main() {
    if (get_option('topsy_trackbacks_enabled') != 'on') return;
    if (! is_single()) return;
    
    global $topsy_cache_time_tb, $post;
    $my_post_id = $post->ID;
    $my_url = topsy_self_url();
    if (time() - topsy_get_cache_time($my_url) < $topsy_cache_time_tb) {
        return;
    }
    if (topsy_get_ratelimit() < 5 && topsy_get_ratelimit('reset') > time()) {
        return;
    }
    
    # In WordPress versions < 2.7, get_comments() doesn't exist. Fall back
    # to get_approved_comments() instead.
    if (! function_exists('get_comments')) {
        function get_comments($arg) {
            if (substr($arg, 0, 8) == 'post_id=') {
                $arg = substr($arg, 8);
            }
            return get_approved_comments($arg);
        }
    }
    if (! function_exists('json_decode') || ! class_exists('Services_JSON')) {
        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'JSON.php');
    }
    $http_data = topsy_http_get(topsy_trackback_url());
    if ($http_data['error']) {
        if ($topsy_debug) echo "Couldn't get trackback data; HTTP request failed with: " . $http_data['error'];
        return;
    }
    if ($http_data['status_code'] == 503) {
        # Do we need to do something special here? for now, we can just let the
        # default error response occur.
    }
    if ($http_data['status_code'] < 200 || $http_data['status_code'] > 299) {
        if ($topsy_debug) echo "Couldn't get trackback data; queried " . topsy_trackback_url() . " and got back a " . $http_data['status']  . " in response.";
        return;
    }
    $http_json = json_decode($http_data['body']);
    # There's some kind of race condition if we just loop over the 
    # JSON->response->list on its own; the topsy_comment_exists() function 
    # doesn't seem to pick up on the existence of the new comment fast 
    # enough after initial creation. Instead, we'll use hash key collisions 
    # to force uniqueness.
    $items = array();
    foreach ($http_json->response->list as $item) {
        $items[$item->permalink_url] = $item;
    }
    foreach ($items as $item) {
        topsy_handle_single_trackback($item, $http_json->response->topsy_trackback_url);
    }
    topsy_update_ratelimit($http_data['headers']['X-RateLimit-Limit'], $http_data['headers']['X-RateLimit-Remaining'], $http_data['headers']['X-RateLimit-Reset']);
    topsy_update_cache();
}

function topsy_trackback_url($my_url = '') {
    if (! $my_url) $my_url = topsy_self_url();
    $tb_url = 'http://otter.topsy.com/trackbacks.json?url=' . htmlentities(urlencode($my_url));
    if (get_option('topsy_trackbacks_infonly') == 'on') {
        $tb_url .= "&infonly=1";
    }
    return $tb_url;
}

function topsy_update_cache() {
    global $post;
    $now = time();
    add_post_meta($post->ID, '_topsy_cache_timestamp', $now, true) || update_post_meta($post->ID, '_topsy_cache_timestamp', $now);
}

function topsy_update_ratelimit($limit, $remaining, $reset) {
    update_option('topsy_rl_limit', $limit);
    update_option('topsy_rl_remaining', $remaining);
    update_option('topsy_rl_reset', $reset);
}

function topsy_update_shortener_data() {
    global $topsy_cache_time_udata;
    if (time() - intval(get_option('topsy_urlshortener_data_cachetime'), 10) < $topsy_cache_time_udata) return;
    $data_resource_url = 'http://cdn.topsy.com/asset/latest/widget/url_shorteners.json';
    $http_data = topsy_http_get($data_resource_url);
    if (isset($http_data['error'])) return;
    if (! function_exists('json_decode') || ! class_exists('Services_JSON')) {
        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'JSON.php');
    }
    if ($http_data['status_code'] < 200 || $http_data['status_code'] > 299) {
        if ($topsy_debug) echo "Couldn't update URL shortener data; queried $data_resource_url and got back a " . $http_data['status']  . " in response.";
        return;
    }
    $http_json = json_decode($http_data['body']);
    foreach ($http_json as $key => $var) {
        $shortener_list []= $key;
        update_option('topsy_urlshortener_data_' . $key, serialize($var));
    }
    update_option('topsy_urlshortener_list', implode(',', $shortener_list));
    update_option('topsy_urlshortener_data_cachetime', time());
}

function topsy_update_theme_list() {
    global $topsy_cache_time_udata;
    if (time() - intval(get_option('topsy_button_theme_list_cachetime'), 10) < $topsy_cache_time_udata) return;
    $data_resource_url = 'http://cdn.topsy.com/asset/latest/widget/themes.json';
    $http_data = topsy_http_get($data_resource_url);
    if (isset($http_data['error'])) return;
    if (! function_exists('json_decode') || ! class_exists('Services_JSON')) {
        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'JSON.php');
    }
    if ($http_data['status_code'] < 200 || $http_data['status_code'] > 299) {
        if ($topsy_debug) echo "Couldn't update URL shortener data; queried $data_resource_url and got back a " . $http_data['status']  . " in response.";
        return;
    }
    $http_json = json_decode($http_data['body']);
    $cur_theme = get_option('topsy_button_theme');
    $cur_theme_included = false;
    foreach ($http_json as $theme) {
        if (topsy_simplify($theme) == $cur_theme) {
            $cur_theme_included = true;
            break;
        }
    }
    if (! $cur_theme_included) {
        $new_theme = ucfirst(preg_replace_callback("/-([a-z0-9])/", create_function('$match', 'return " " . strtoupper($match[1]);'), $cur_theme));
        array_push($http_json, $new_theme);
    }
    update_option('topsy_button_theme_list', serialize($http_json));
    update_option('topsy_button_theme_list_cachetime', time());
}
function topsy_wp_head() {
    $page = null;
    if ( is_home() ) { $page = 'home'; }
    if ( is_page() ) { $page = 'page'; }
    if ( is_single() ) { $page = 'post'; }
    if ( is_category() ) { $page = 'category'; }
    if ( is_archive() && !is_category() ) { $page = 'archive'; }
    $style = get_option('topsy_button_' . $page . '_style');
    $host = '';
    if (preg_match("/[\?&]topsybeta=1/", $_SERVER['REQUEST_URI'])) {
        $host = 'beta.static';
    } else {
        $host = 'cdn';
    }
    
    print "<script type=\"text/javascript\" id=\"topsy_global_settings\">\n";
    print "    var topsy_style = '$style';\n";
    print "    var topsy_nick = '" . get_option('topsy_retweet_username') . "';\n";
    print "    var topsy_order = '" . get_option('topsy_smallbutton_order') . "';\n";
    print "    var topsy_theme = '" . get_option('topsy_button_theme') . "';\n";
    print "    var topsy_tweet_text = '" . get_option('topsy_button_text_tweet') . "';\n";
    print "    var topsy_retweet_text = '" . get_option('topsy_button_text_retweet') . "';\n";
    if (get_option('topsy_streaming') == 'on') {
        print "    var topsy_streaming = 'on';\n";
    }
    print "</script>";
    print "<script type=\"text/javascript\" id=\"topsy-js-elem\" src=\"http://$host.topsy.com/topsy.js?init=topsyWidgetCreator\"></script>\n";
}


add_action('admin_menu', 'topsy_action_admin_menu');
add_action('deleted_post', 'topsy_hook_delete_post');
add_action('init', 'topsy_init');
add_action('transition_post_status', 'topsy_hook_unpublish_post');
add_action('wp', 'topsy_trackback_main');
add_action('wp_head', 'topsy_wp_head');

add_action('publish_post', 'topsy_hook_publish_post');
add_action('publish_phone', 'topsy_hook_publish_post');
add_action('xmlrpc_publish_post', 'topsy_hook_publish_post');

add_action('topsy_retry_shorten_url', 'topsy_hook_publish_post');

add_filter('the_content', 'topsy_filter_the_content');
add_filter('the_content_rss', 'topsy_filter_the_content_rss');
add_filter( 'plugin_action_links', 'topsy_plugin_page_settings_link', 10, 2 );

if (function_exists('add_shortcode')) {
    add_shortcode('topsy_retweet_big', 'shortcode_topsy_retweet_big');
    add_shortcode('topsy_retweet_small', 'shortcode_topsy_retweet_small');
}
register_activation_hook( __FILE__, 'topsy_activation' );
register_deactivation_hook( __FILE__, 'topsy_deactivation' );

?>
