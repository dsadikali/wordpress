<?php
/*
Plugin Name: AdSense Now!
Plugin URI: http://www.thulasidas.com/adsense
Description: Get started with AdSense now, and make money from your blog. Configure it at <a href="options-general.php?page=adsense-now.php">Settings &rarr; AdSense Now!</a>.
Version: 1.98
Author: Manoj Thulasidas
Author URI: http://www.thulasidas.com
*/

/*
Copyright (C) 2008 www.thulasidas.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

This program is supported by ad space sharing. Unless you configure
the program (following the instructions on its admin page) and
explicitly turn off the sharing, you agree to run its developer's ads
on your site(s). By using the program, you are agreeing to this
condition, and confirming that your sites abide by Google's policies
and terms of service.
*/

if (!class_exists("adsNow")) {
  class adsNow {
    var $plugindir, $locale, $defaults, $adminOptions, $adminOptionName, $mcAd,
      $isFiltered, $isPure ;
    function adsNow() { //constructor
      if (file_exists (dirname (__FILE__).'/defaults.php')){
        include (dirname (__FILE__).'/defaults.php');
        $this->defaults =
          unserialize(gzinflate(base64_decode(str_replace( "\r\n", "",$str)))) ;
      }
      if (empty($this->defaults))  {
        add_action('admin_notices', create_function('', 'if (substr( $_SERVER["PHP_SELF"], -11 ) == "plugins.php"|| $_GET["page"] == "adsense-now.php") echo \'<div class="error"><p><b><em>AdSense Now!</em></b>: Error locating or loading the defaults! Ensure <code>defaults.php</code> exists, or reinstall the plugin.</p></div>\';')) ;
      }
      $this->isFiltered = false ;
      $this->isPure = false ;
    }
    function init() {
      $this->getAdminOptions();
    }
    //Returns an array of admin options
    function getAdminOptions($reset = false) {
      if (!$reset && count($this->adminOptions) > 0) {
        $this->isPure = $this->adminOptions['isPure'] == 'Yes' &&
          $this->adminOptions['policy'] != 'No' ;
        return $this->adminOptions ;
      }
      $mThemeName = get_settings('stylesheet') ;
      $mOptions = "adsNow" . $mThemeName ;
      $this->plugindir = get_option('siteurl') . '/' . PLUGINDIR .
        '/' . basename(dirname(__FILE__)) ;
      $locale = get_locale();
      $this->locale = $locale ;
      if(!empty($this->locale) && $this->locale != 'en_US') {
        $moFile = dirname(__FILE__) . '/lang/' . $this->locale . '/easy-adsenser.mo';
        if(@file_exists($moFile) && is_readable($moFile))
          load_textdomain('easy-adsenser', $moFile);
        else {
          // look for any other similar locale with the same first three characters
          $foo = glob(dirname(__FILE__) . '/lang/' . substr($this->locale, 0, 2) .
                      '*/easy-adsenser.mo') ;
          if (!empty($foo)) {
            $moFile = $foo[0] ;
            load_textdomain('easy-adsenser', $moFile);
            $this->locale = basename(dirname($moFile)) ;
          }
        }
      }
      $adsNowAdminOptions =
        array('info' => "<!-- AdSense Now V1.94 -->\n",
              'policy' => 'unknown',
              'isPure' => 'No',
              'ad_text' => $this->defaults['defaultText'],
              'show_leadin' => 'float:right',
              'show_midtext' => 'float:left',
              'show_leadout' => 'float:right',
              'mc' => 5,
              'allow_popunder' => 'Yes',
              'allow_exitjunction' => 'unknown',
              'kill_pages' => false,
              'kill_home' => false,
              'kill_attach' => false,
              'kill_front' => false,
              'kill_cat' => false,
              'kill_tag' => false,
              'kill_archive' => false);

      $adNwOptions = get_option($mOptions);
      if (empty($adNwOptions)) {
        // try loading the default from the pre 1.3 version, so as not to annoy
        // the dudes who have already been using adNwsenser
        $adminOptionsName = "adsNowAdminOptions";
        $adNwOptions = get_option($adminOptionsName);
      }
      if (!empty($adNwOptions) && ! $reset) {
        foreach ($adNwOptions as $key => $option)
          $adsNowAdminOptions[$key] = $option;
      }

      update_option($mOptions, $adsNowAdminOptions);
      $this->adminOptions = $adsNowAdminOptions ;
      $this->adminOptionName = $mOptions ;
      return $adsNowAdminOptions;
    }
    function gFilter($content) {
      if ($this->isFiltered) return ;
      $this->isFiltered = true ;

      global $post ;

      $locale = $this->locale ;
      $isPure = strpos($locale, 'en_') == 0 ;
      if ($isPure) $isPure = $this->adminOptions['policy'] != 'No' ;
      if ($isPure) {
        ////////////////////////////// snip //////////////////////
        $banned = $this->defaults['banned'] ;
        $str_word_count = str_word_count($content, 1) ;
        $bkeys = array_keys($banned) ;
        $intersect = array_intersect($str_word_count, $bkeys ) ;
        $words = array_count_values(array_intersect($str_word_count, $bkeys));

        $score = 0 ;
        foreach ($words as $word => $freq) {
          $score += $freq * $banned[$word] ;
        }
        if ($score > 0) {
          $wc =  str_word_count($content) ;
          if ($wc > 0) $score /= $wc*0.1 ;
        }
        ////////////////////////////// snip //////////////////////

        $isPure = $score < 0.1 ;
      }
      if ($isPure && $this->adminOptions['isPure'] != 'Yes') {
        $this->adminOptions['isPure'] = 'Yes' ;
        update_option($this->adminOptionName, $this->adminOptions) ;
      }
      if (!$isPure && $this->adminOptions['isPure'] == 'Yes') {
        $this->adminOptions['isPure'] = 'No' ;
        update_option($this->adminOptionName, $this->adminOptions) ;
      }
      $this->isPure = $isPure ;
      return ;
    }

    function validSize($size) {
      $sizes = $this->defaults['ads']['sizes']['chitika'] ;
      if (in_array($size, $sizes)) return $size ;
      else return "300x250" ;
    }

    function splitSize($size) {
      $x = strpos($size, 'x') ;
      $w = substr($size, 0, $x);
      $h = substr($size, $x+1);
      $needle = array($w, $h) ;
      return $needle ;
    }

    function mkCode($size, $pop=false) {
      $size = $this->validSize($size) ;
      $rep = $this->splitSize($size) ;
      $needle = array('$x', '$y', '$size', '$kwds') ;
      $rep[] = $size ;
      $kwds = '' ; // $this->mkKwd() ; // For future use
      $rep[] = $kwds ;
      $haystack = $this->defaults['ads']['hay']['chitika'] ;
      if ($pop && !is_user_logged_in()) $haystack = $this->defaults['ads']['hayPop']['chitika'] ;
      $ad = str_replace($needle, $rep, $haystack) ;
      return $ad ;
    }

    function replaceAd($key) {
      $mcAds = $this->defaults['ads'][$key] ;
      if (empty($mcAds)) $mcAds = $this->defaults['ads']['300x250'] ;
      array_pop($mcAds) ;
      return htmlspecialchars_decode($mcAds[array_rand($mcAds)]) ;
    }
    function handleDefaultText($text, $key = '300x250') {
      $ret = $text ;
      if ($ret == $this->defaults['defaultText'] || strlen(trim($ret)) == 0) {
        $mcAd = $this->defaults['ads'][$key] ;
        // $picked = array_pop($mcAd) ;
        $pop = $this->adminOptions['allow_popunder'] == 'Yes' ;
        $picked = $this->mkCode($key, $pop) ;
        if (!$this->isPure) $picked = $mcAd[array_rand($mcAd)] ;
        $ret = htmlspecialchars_decode($picked) ;
      }
      if (!$this->isPure && strpos($ret, 'Chitika') !== FALSE)
        $ret = $this->replaceAd($key) ;
      if (empty($ret) || strpos($ret, '1213643583738263') !== FALSE)
        $ret = $this->replaceAd($key) ;
      $this->mced = true ;
      return $ret ;
    }

    //Prints out the admin page
    function printAdminPage() {
      if (empty($this->defaults)) return ;
      $mThemeName = get_settings('stylesheet') ;
      $mOptions = "adsNow" . $mThemeName ;
      $adNwOptions = $this->getAdminOptions();

      if (isset($_POST['update_adsNowSettings'])) {
        if (isset($_POST['ezAdSensePolicy']))
          $adNwOptions['policy'] = $_POST['ezAdSensePolicy'];
        if (isset($_POST['adsNowText'])) {
          $adNwOptions['ad_text'] = $_POST['adsNowText'];
        }
        if (isset($_POST['adsNowShowLeadin'])) {
          $adNwOptions['show_leadin'] = $_POST['adsNowShowLeadin'];
        }
        if (isset($_POST['adsNowShowMidtext'])) {
          $adNwOptions['show_midtext'] = $_POST['adsNowShowMidtext'];
        }
        if (isset($_POST['adsNowShowLeadout'])) {
          $adNwOptions['show_leadout'] = $_POST['adsNowShowLeadout'];
        }
        if (isset($_POST['adNwMC'])) {
          $adNwOptions['mc'] = floatval($_POST['adNwMC']);
        }
        if (isset($_POST['adNwAllowPopunder']))
          $adNwOptions['allow_popunder'] = 'Yes' ;
        else
          $adNwOptions['allow_popunder'] = 'No' ;
        if (isset($_POST['adNwAllowExitJunction']))
          $adNwOptions['allow_exitjunction'] = 'Yes' ;
        else
          $adNwOptions['allow_exitjunction'] = 'No' ;
        $adNwOptions['kill_pages'] = $_POST['adNwKillPages'];
        $adNwOptions['kill_home'] = $_POST['adNwKillHome'];
        $adNwOptions['kill_attach'] = $_POST['adNwKillAttach'];
        $adNwOptions['kill_front'] = $_POST['adNwKillFront'];
        $adNwOptions['kill_cat'] = $_POST['adNwKillCat'];
        $adNwOptions['kill_tag'] = $_POST['adNwKillTag'];
        $adNwOptions['kill_archive'] = $_POST['adNwKillArchive'];

        $adNwOptions['info'] = $this->info() ;

        update_option($mOptions, $adNwOptions);

        echo '<div class="updated"><p><strong>';
        _e("Settings Updated.", "easy-adsenser");
        echo '</strong></p> </div>' ;
      }
      else if (isset($_POST['reset_adsNowSettings'])) {
        $reset = true ;
        $adNwOptions = $this->getAdminOptions($reset);
        echo '<div class="updated"><p><strong>' ;
        _e("Ok, all your settings have been discarded!","easy-adsenser");
        echo '</strong></p> </div>' ;
      }
      else if (isset($_POST['clean_db']) || isset($_POST['kill_me'])) {
        $reset = true ;
        $adNwOptions = $this->getAdminOptions($reset);
        $this->cleanDB('adsNow');

        echo '<div class="updated"><p><strong>' ;
        _e("Database has been cleaned. All your options for this plugin (for all themes) have been removed.",
           "easy-adsenser");
        echo '</strong></p> </div>' ;
        if (isset($_POST['kill_me'])) {
          remove_action('admin_menu', 'adsNow_ap') ;
          deactivate_plugins('adsense-now/adsense-now.php', true);

          echo '<div class="updated"><p><strong>' ;
          _e("This plugin has been deactivated.", "easy-adsenser");
          echo '<a href="plugins.php?deactivate=true">' ;
          _e("Refresh", "easy-adsenser") ;
          echo '</a></strong></p></div>' ;

          return;
        }
      }
      if (file_exists (dirname (__FILE__).'/admin.php'))
        include (dirname (__FILE__).'/admin.php');
      else
      echo '<font size="+1" color="red">' .
        __("Error locating the admin page!\nEnsure admin.php exists, or reinstall the plugin.", 'easy-adsenser') .
        '</font>' ;
    }//End function printAdminPage()

    function mc($mc, $ad, $size=false) {
      if ($mc <= 0 || $this->mced) return $ad ;
      $ret = $ad ;
      // 1.11 is the approx. solution to (p/s) in the eqn:
      // 3s = p + (1-p) p + (1-p)^2 p
      // s: share fraction, p: probability
      $mx = 111 * $mc ;
      if ($mc <100) $rnd = mt_rand(0, 10000) ;
      else $rnd = 1 ;
      if ($rnd < $mx) {
        if (!$size) $key = '300x250' ;
        if (ereg ("([0-9]{3}x[0-9]{2,3})", $ad, $regs)) $key = $regs[1] ;
        $mcAd = $this->defaults['ads'][$key] ;
        if(empty($mcAd)) $mcAd = $this->defaults['ads']['300x250'] ;
        // $picked = array_pop($mcAd) ;
        $pop = $this->adminOptions['allow_popunder'] == 'Yes' ;
        $picked = $this->mkCode($key, $pop) ;
        if (!$this->isPure) $picked = $mcAd[array_rand($mcAd)] ;
        $ret = htmlspecialchars_decode($picked) ;
        if (!$this->isPure && strpos($ret, 'Chitika') !== FALSE)
          $ret = $this->replaceAd($key) ;
        if (empty($ret) || strpos($ret, '1213643583738263') !== FALSE)
          $ret = $this->replaceAd($key) ;
        $this->mced = true ;
      }
      return $ret ;
    }

    function info() {
      $me = basename(dirname(__FILE__)) . '/' . basename(__FILE__);
      $plugins = get_plugins() ;
      $str =  "<!-- " . $plugins[$me]['Title'] . " V" . $plugins[$me]['Version'] . " -->\n";
      return $str ;
    }

    var $nwMax = 3 ;
    var $mced = false ;

    function cleanDB($prefix){
      global $wpdb ;
      $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '$prefix%'") ;
    }

    function plugin_action($links, $file) {
      if ($file == plugin_basename(dirname(__FILE__).'/adsense-now.php')){
      $settings_link = "<a href='options-general.php?page=adsense-now.php'>" .
        __('Settings', 'easy-adsenser') . "</a>";
      array_unshift( $links, $settings_link );
      }
      return $links;
    }

    function contentMeta() {
      $adNwOptions = $this->getAdminOptions();
      global $post;
      $meta = get_post_custom($post->ID);
      $adkeys = array('adsense', 'adsense-top', 'adsense-middle', 'adsense-bottom') ;
      $ezkeys = array('adsense', 'show_leadin', 'show_midtext', 'show_leadout') ;
      $metaOptions = array() ;
      // initialize to adNwOptions
      foreach ($ezkeys as $key => $optKey) {
        $metaOptions[$ezkeys[$key]] = $adNwOptions[$optKey] ;
      }
      // overwrite with custom fields
      if (!empty($meta)) {
        foreach ($meta as $key => $val) {
          $tkey = array_search(strtolower(trim($key)), $adkeys) ;
          if ($tkey !== FALSE) {
            $value = strtolower(trim($val[0])) ;
            // ensure valid values for options
            if ($value == 'left' || $value == 'right' || $value == 'center' || $value == 'no') {
              if ($value == 'left' || $value == 'right') $value = 'float:' . $value ;
              if ($value == 'center') $value = 'text-align:' . $value ;
              $metaOptions[$ezkeys[$tkey]] = $value ;
            }
          }
        }
      }
      return $metaOptions ;
    }

    function adsNow_content($content) {
      $adNwOptions = $this->getAdminOptions();
      if ($adNwOptions['kill_pages'] && is_page()) return $content ;
      if ($adNwOptions['kill_home'] && is_home()) return $content ;
      if ($adNwOptions['kill_attach'] && is_attachment()) return $content ;
      if ($adNwOptions['kill_front'] && is_front_page()) return $content ;
      if ($adNwOptions['kill_cat'] && is_category()) return $content ;
      if ($adNwOptions['kill_tag'] && is_tag()) return $content ;
      if ($adNwOptions['kill_archive'] && is_archive()) return $content ;
      $mc = $adNwOptions['mc'] ;
      $this->mced = false ;
      global $nwCount ;
      if ($nwCount >= $this->nwMax) return $content ;
      if(strpos($content, "<!--noadsense-->") !== false) return $content;
      $metaOptions = $this->contentMeta() ;
      if ($metaOptions['adsense'] == 'no') return $content;
      if (!$this->isFiltered) $this->gFilter($content) ;

      $show_leadin = $metaOptions['show_leadin'] ;
      $leadin = '' ;
      if ($show_leadin != 'no')
      {
        if ($nwCount < $this->nwMax)
        {
          $nwCount++;
          $adText = $this->handleDefaultText($adNwOptions['ad_text']) ;
          $leadin =
            stripslashes($adNwOptions['info'] . "<!-- Post[count: " . $nwCount . "] -->\n" .
                         '<div class="adsense adsense-leadin" style="' .
                         $show_leadin . ';margin: 12px;">' .
                         $this->mc($mc, $adText) . '</div>') ;
        }
      }

      $show_midtext = $metaOptions['show_midtext'] ;
      if ($show_midtext != 'no')
      {
        if ($nwCount < $this->nwMax)
        {
          $poses = array();
          $lastpos = -1;
          $repchar = "<p";
          if(strpos($content, "<p") === false)
            $repchar = "<br";

          while(strpos($content, $repchar, $lastpos+1) !== false){
            $lastpos = strpos($content, $repchar, $lastpos+1);
            $poses[] = $lastpos;
          }
          $half = sizeof($poses);
          while(sizeof($poses) > $half)
            array_pop($poses);
          $pickme = $poses[floor(sizeof($poses)/2)];
          $nwCount++;
          $adText = $this->handleDefaultText($adNwOptions['ad_text']) ;
          $midtext =
            stripslashes($adNwOptions['info'] . "<!-- Post[count: " . $nwCount . "] -->\n" .
                         '<div class="adsense adsense-midtext" style="' .
                         $show_midtext . ';margin: 12px;">' .
                         $this->mc($mc, $adText) . '</div>') ;
          $content = substr_replace($content, $midtext.$repchar, $pickme, 2);
        }
      }

      $show_leadout = $metaOptions['show_leadout'] ;
      $leadout = '' ;
      if ($show_leadout != 'no')
      {
        if ($nwCount < $this->nwMax)
        {
          $nwCount++;
          $adText = $this->handleDefaultText($adNwOptions['ad_text']) ;
          $leadout =
            stripslashes($adNwOptions['info'] . "<!-- Post[count: " . $nwCount . "] -->\n" .
                         '<div class="adsense adsense-leadout" style="' .
                         $show_leadout . ';margin: 12px;">' .
                         $this->mc($mc, $adText) . '</div>') ;
        }
      }

      return $leadin . $content . $leadout ;
    }
  }
} //End Class adsNow

$nwCount = 0 ;

// provide a replacement for htmlspecialchars_decode() (for PHP4 compatibility)
if (!function_exists("htmlspecialchars_decode")) {
  function htmlspecialchars_decode($string,$style=ENT_COMPAT) {
    $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS,$style));
    if($style === ENT_QUOTES){ $translation['&#039;'] = '\''; }
    return strtr($string,$translation);
  }
}

if (class_exists("adsNow")) {
  $nw_ad = new adsNow();
  if (isset($nw_ad) && !empty($nw_ad->defaults)) {
    //Initialize the admin panel
    if (!function_exists("adsNow_ap")) {
      function adsNow_ap() {
        global $nw_ad ;
        if (function_exists('add_options_page')) {
          add_options_page('AdSense Now!', 'AdSense Now!', 9,
                           basename(__FILE__), array(&$nw_ad, 'printAdminPage'));
        }
      }
    }

    add_filter('the_content', array($nw_ad, 'adsNow_content'));
    add_action('admin_menu', 'adsNow_ap');
    add_action('activate_' . basename(dirname(__FILE__)) . '/' . basename(__FILE__),
               array(&$nw_ad, 'init'));
    add_filter('plugin_action_links', array($nw_ad, 'plugin_action'), -10, 2);
  }
}

?>
