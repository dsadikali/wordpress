<?php 
/* 
Plugin Name: TranslateThis Button
Plugin URI: http://translateth.is/wordpress
Description: Automatically translates any site into 52 languages leveraging the Google Language API
Version: 0.1
Author: Jon Raasch
Author URI: http://jonraasch.com/
Documentation: http://translateth.is/docs

Plugin released under the FreeBSD license, and leverages the TranslateThis Button script which has its own licensing.  For terms and conditions of use, see http://translateth.is/tos
*/

/*  Copyright 2010 Jon Raasch
*/


function translate_this_button($opts = null) {
    $these_opts = $opts['ttb_opts'] ? $opts['ttb_opts'] : get_option('ttb_opts');
    
    $alt_text = $these_opts['altText'];
    $js_opts = build_ttb_options( $these_opts );
    
    $out = <<<EOT

<!-- Begin TranslateThis Button for WordPress -->

<div id="translate-this"><a href="http://translateth.is/" class="translate-this-button">$alt_text</a></div>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://x.translateth.is/translate-this.js"></script>
<script type="text/javascript">
TranslateThis({
$js_opts
});
</script>

<!-- End TranslateThis Button for WordPress -->


EOT;
    
    if ( $opts['return'] ) return $out;
    
    echo $out;
}

function build_ttb_options( $opts ) {
    $out = '';
    
    // miscellaneous options
    if ( $opts['GA'] ) $out .= "GA : true,\r";
    if ( $opts['scope'] ) $out .= "scope : '" . $opts['scope'] . "',\r";
    //if ( $opts['wrapper'] ) $out .= "wrapper : '" . $opts['wrapper'] . "',\r";
    if ( !$opts['cookie'] ) $out .= "cookie : false,\r";
    
    // text options
    $out .= "undoText : '" . $opts['undoText'] . "',\r";
    $out .= "panelText : '" . $opts['panelText'] . "',\r";
    $out .= "moreText : '" . $opts['moreText'] . "',\r";
    $out .= "busyText : '" . $opts['busyText'] . "',\r";
    $out .= "cancelText : '" . $opts['cancelText'] . "',\r";
    
    if ( $opts['fromLang'] ) $out .= "fromLang : '" . $opts['fromLang'] . "',\r";
    
    // dropdown languages
    
    if ( !$opts['defaultDD'] ) {
        $out .= "ddLangs : [\r";
        for ( $i = 0; $i < count( $opts['ddLangs'] ); $i++ ) {
            $out .= "    '" . $opts['ddLangs'][$i] . "',\r";
        }
        $out .= "],\r";
    }
    
    // display options
    if ( $opts['noBtn'] ) $out .= "noBtn : true,\r";
    if ( $opts['btnImg'] ) $out .= "btnImg : '" . $opts['btnImg'] . "',\r";
    if ( $opts['btnHeight'] ) $out .= "btnHeight : " . $opts['btnHeight'] . ",\r";
    if ( $opts['btnWidth'] ) $out .= "btnWidth : " . $opts['btnWidth'] . ",\r";
    
    if ( $opts['noImg'] ) $out .= "noImg : true,\r";
    
    // remove trailing comma and return
    $out = substr( $out, 0, -2);
    
    return $out;
}

function ttb_menu_item() {
    $ttb_opts = get_option('ttb_opts');
    
    // Add new menu in Setting or Options tab:
    add_options_page('TranslateThis Button', 'TranslateThis Button', 10, 'Translate-This-Button', 'ttb_admin');
}

// js and css for ttb admin
function ttb_css_js() {
    $tthis_dir = get_option('home') . '/wp-content/plugins/translate-this-button/';
    
    ?>
<style type="text/css">
#icon-translate-this {
    background: url("<?=$tthis_dir; ?>images/translate-this-icon.png") no-repeat;
}

#ttb-dd-langs .lang-panel {
    width: 150px;
    float: left;
    padding: 2px 0;
}

</style>

<script type="text/javascript">
    function ttb_init() {
        function reset_form(ev) {
            ev.preventDefault();
            
            var answer = confirm('Are you sure you want to reset the TranslateThis Button options?');
            
            if ( answer ) return document.getElementById('reset_form').submit();
            else return false;
        }
    
        document.getElementById('tthis-opts-reset').onclick = reset_form;
        
        function ddDisplay() {
            ddOpts.style.display = defaultDD.checked ? 'none' : 'block';
        }
    
        var ddOpts = document.getElementById('ttb-dd-langs'),
        defaultDD = document.getElementById('defaultDD');
        
        ddDisplay();
        
        defaultDD.onclick = ddDisplay;
    }
    
    addLoadEvent( ttb_init );
</script>
    <?php
}

function ttb_admin() {
    global $ol_flash, $current_user;
    get_currentuserinfo();
    
    $ttb_opts = get_option('ttb_opts');
    
    // process posted options
    if (isset($_POST['scope']) && $current_user->allcaps['level_10']) {
        // set other options
        $ttb_opts['GA'] = $_POST['GA'] ? true : false;
        $ttb_opts['scope'] = $_POST['scope'] ? addslashes($_POST['scope']) : false;
        $ttb_opts['cookie'] = $_POST['cookie'] ? true : false;
    
        // text options
        $ttb_opts['undoText'] = addslashes($_POST['undoText']);
        $ttb_opts['panelText'] = addslashes($_POST['panelText']);
        $ttb_opts['moreText'] = addslashes($_POST['moreText']);
        $ttb_opts['busyText'] = addslashes($_POST['busyText']);
        $ttb_opts['cancelText'] = addslashes($_POST['cancelText']);
        
        // from language
        $ttb_opts['fromLang'] = $_POST['fromLang'] ? addslashes($_POST['fromLang']) : false;
        
        // dropdown languages
        $ttb_opts['defaultDD'] = $_POST['defaultDD'] ? true : false;
        
        $ttb_opts['ddLangs'] = array();
        
        if ( count( $_POST['ddLangs'] ) > 0 ) {
            foreach( $_POST['ddLangs'] as $slug=>$val ) array_push( $ttb_opts['ddLangs'], $slug );
        }
        else $ttb_opts['defaultDD'] = true;
        
        
        // display options
        $ttb_opts['noBtn'] = $_POST['noBtn'] ? true : false;
        $ttb_opts['btnImg'] = $_POST['btnImg'] ? addslashes($_POST['btnImg']) : false;
        $ttb_opts['altText'] = $_POST['altText'] ? $_POST['altText'] : false;
        $ttb_opts['btnHeight'] = $_POST['btnHeight'] ? (int) $_POST['btnHeight'] : false;
        $ttb_opts['btnWidth'] = $_POST['btnWidth'] ? (int) $_POST['btnWidth'] : false;
        
        $ttb_opts['noImg'] = $_POST['noImg'] ? true : false;
        
        // update options and flash success message
        update_option('ttb_opts', $ttb_opts);
            
        $ol_flash = "Your TranslateThis Button settings have been saved.";
    }
    else if ( isset($_POST['reset_all_opts']) ) {
        $ttb_opts = ttb_init_opts();
        
        $ol_flash = "The TranslateThis Button settings have been reset to the default";
    }
    
    $langs = array(
        array('af', 'Afrikaans'),
        array('sq', 'Albanian'),
        array('ar', 'Arabic'),
        array('be', 'Belarusian'),
        array('bg', 'Bulgarian'),
        array('ca', 'Catalan'),
        array('zh-CN', 'Chinese simplified'),
        array('zh-TW', 'Chinese traditional'),
        array('hr', 'Croatian'),
        array('cs', 'Czech'),
        array('da', 'Danish'),
        array('nl', 'Dutch'),
        array('en', 'English'),
        array('et', 'Estonian'),
        array('fi', 'Finnish'),
        array('fr', 'French'),
        array('gl', 'Gallician'),
        array('de', 'German'),
        array('el', 'Greek'),
        array('iw', 'Hebrew'),
        array('hi', 'Hindi'),
        array('hu', 'Hungarian'),
        array('is', 'Icelandic'),
        array('id', 'Indonesian'),
        array('ga', 'Irish'),
        array('it', 'Italian'),
        array('ja', 'Japanese'),
        array('ko', 'Korean'),
        array('lv', 'Latvian'),
        array('lt', 'Lithuanian'),
        array('mk', 'Macedonian'),
        array('ms', 'Malay'),
        array('mt', 'Maltese'),
        array('no', 'Norwegian'),
        array('fa', 'Persian'),
        array('pl', 'Polish'),
        array('pt-PT', 'Portuguese'),
        array('ro', 'Romanian'),
        array('ru', 'Russian'),
        array('sr', 'Serbian'),
        array('sk', 'Slovak'),
        array('sl', 'Slovenian'),
        array('es', 'Spanish'),
        array('sw', 'Swahili'),
        array('sv', 'Swedish'),
        array('tl', 'Tagalog (Filipino)'),
        array('th', 'Thai'),
        array('tr', 'Turkish'),
        array('uk', 'Ukranian'),
        array('vi', 'Vietnamese'),
        array('cy', 'Welsh'),
        array('yi', 'Yiddish'),
    );
    
    $fromLangOpts = '';
    $langDD = '';
    
    for ( $i = 0; $i < count($langs); $i++ ) {
        $langSlug = $langs[$i][0];
        $langName = $langs[$i][1];
        
        $fromLangOpts .= '<option value="' . $langSlug . '"' . ( $langSlug == $ttb_opts['fromLang'] ? ' selected' : '' ) . '>' . $langName . '</option>';
        
        $langDD .= '<div class="lang-panel"><input type="checkbox" name="ddLangs[' . $langSlug . ']"' . ( in_array($langSlug, $ttb_opts['ddLangs']) ? ' checked' : '' ) . '/> ' . $langName . '</div>';
    }
    
    // build the page output
    $out = '';
    
    if ($ol_flash) $out .= '<div id="message"class="updated fade"><p>' . $ol_flash . '</p></div>';
    
    $out .= '<div class="wrap">';
    
    $out .= '<div id="icon-translate-this" class="icon32"><br /></div>';
    
    $out .= '<h2>TranslateThis Button Settings</h2>';
    
    $out .= '<p><a href="http://translateth.is/wordpress" target="_blank">Plugin Homepage</a> | <a href="http://translateth.is/wordpress/docs" target="_blank">Documentation</a> | <a href="http://translateth.is/wordpress/docs#changelog" target="_blank">Changelog</a> | <a href="javascript:document.getElementById(\'donate_form\').submit()">Donate</a> | <a href="http://wordpress.org/extend/plugins/translate-this-button/">Vote for this plugin</a></p>';
    
    $out .= '<p>Note - The translation widget used by this plugin contains external "Powered by" links</p>';
    
    // only admin may edit TTB settings
    if ($current_user->allcaps['level_10']) {
        $out .= '<form action="" method="post" id="ttb_form">';
        
        // general settings
        $out .= '<h3>General Settings</h3>';
        
        $out .= '<table class="form-table"><tbody>';
        
        $out .= '<tr><th scope="row"><label>Enable Google Analytics Tracking:</label></th><td><input type="checkbox" name="GA" ' . ( $ttb_opts['GA'] ? 'checked ' : '') . ' /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Translation Scope:</label></th><td><select name="scope"><option value="0">Entire page</option><option value="content"' . ( $ttb_opts['scope'] == 'content' ? ' selected' : '' ) . '>Content area only (post + comments)</option></select></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Use cookie:</label></th><td><input type="checkbox" name="cookie" ' . ( $ttb_opts['cookie'] ? 'checked ' : '') . ' /></td></tr>';
        
        $out .= '</tbody></table>';
        
        $out .= '<h3>Language Settings</h3>';
        
        $out .= '<table class="form-table"><tbody>';
        
        $out .= 
        '<tr><th scope="row"><label>From Language:</label></th><td><select name="fromLang">
            <option value="">Unknown</option>';
            
        $out .= $fromLangOpts;
        
        $out .= '</select></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Main Panel Text:</label></th><td><input type="text" name="panelText" value="' . htmlentities($ttb_opts['panelText']) . '" size="50%" /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>More Languages Text:</label></th><td><input type="text" name="moreText" value="' . htmlentities($ttb_opts['moreText']) . '" size="50%" /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Busy Translating Text:</label></th><td><input type="text" name="busyText" value="' . htmlentities($ttb_opts['busyText']) . '" size="50%" /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Cancel Translation Text:</label></th><td><input type="text" name="cancelText" value="' . htmlentities($ttb_opts['cancelText']) . '" size="50%" /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Undo Text:</label></th><td><input type="text" name="undoText" value="' . htmlentities($ttb_opts['undoText']) . '" size="50%" /></td></tr>';
        
        $out .= '</tbody></table>';
        
        $out .= '<h3>Languages in Dropdown</h3>';
        
        $out .= '<p>Use default langauges in dropdown: <input type="checkbox" name="defaultDD"' . ( $ttb_opts['defaultDD'] ? ' checked' : '' ) . ' id="defaultDD" /></p>';
        
        $out .= '<div id="ttb-dd-langs">' . $langDD . '<br class="clear" /></div>';
        
        $out .= '<h3>Display Settings</h3>';
        
        $out .= '<table class="form-table"><tbody>';
        
        $out .= '<tr><th scope="row"><label>Disable Button Image (Use Text Only):</label></th><td><input type="checkbox" name="noBtn" ' . ( $ttb_opts['noBtn'] ? 'checked ' : '') . ' /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Alternate Button Text (if button image disabled):</label></th><td><input type="text" name="altText" value="' . htmlentities($ttb_opts['altText']) . '" size="50%" /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Alternate Button Image Path:</label></th><td><input type="text" name="btnImg" value="' . ( $ttb_opts['btnImg'] ? $ttb_opts['btnImg'] : '' ) . '" size="50%" /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Button Width (only set for alternate button image):</label></th><td><input type="text" name="btnWidth" value="' . $ttb_opts['btnWidth'] . '" size="50%" /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Button Height (only set for alternate button image):</label></th><td><input type="text" name="btnHeight" value="' . $ttb_opts['btnHeight'] . '" size="50%" /></td></tr>';
        
        $out .= '<tr><th scope="row"><label>Disable Flag Thumbnails:</label></th><td><input type="checkbox" name="noImg" ' . ( $ttb_opts['noImg'] ? 'checked ' : '' ) . ' /></td></tr>';
        
        //$out .= '</table>';

        
        //$out .= '<div class="submit"><input type="submit" value="Save Settings" /> <a href="javascript:reset_form()">Reset Options to Defaults</a></div></form>';
        $out .= '<tr valign="top"><th scope="row">&nbsp;</th><td>
                    <div class="submit"><input type="submit" class="button-primary" value="Save Settings" /> <a href="#" class="button" id="tthis-opts-reset">Reset Options to Defaults</a></div>
                </td>
            </tr>';
            
        $out .= '</tbody></table>';
        
        $out .= '</form>';
    }
    
    // reset form
    
    $out .= <<<EOT
    <form action="" method="post" id="reset_form">
    <input type="hidden" name="reset_all_opts" value="true" />
    
    <input type="submit" style="display: none;" />
    </form>
    
EOT;
    
    // donations
    
    $out .= '<hr />
    <h2>Do you like this plugin?</h2>
    
    <p>The best way to show your support is by <a href="http://wordpress.org/extend/plugins/translate-this-button/"><strong>voting it up on Wordpress.org</strong></a>.
    </p>
    
    <p>Or get the word out by blogging, <a href="http://twitter.com/home?status=TranslateThis+Button+for+WordPress%20-%20http%3A%2F%2Ftranslateth.is%2Fwordpress%20by%20@jonraasch">tweeting</a>, or telling a friend about it.</p>
    
    <p>If you\'re feeling particularly generous, please consider making a small donation.  Your donations support both the TranslateThis Button project and this WordPress plugin.</p>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="donate_form">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
        ">
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
    <br />';

    $out .= <<<EOT
    
    <hr />
    
    <h2>Not just for WordPress</h2>
    
    <p>
    Did you know that the <strong>TranslateThis Button</strong> can be easily installed on any website, not just WordPress blogs?<br /><a href="http://translateth.is/">More info here</a>
    </p>
    
    <br />
    
EOT;

    $out .= '</div>';

    echo $out;
}

include('ttb-sidebar-widget.php');

// init opts for first install, and resetting options

function ttb_init_opts() {
    $ttb_opts['GA'] = false;
    $ttb_opts['scope'] = false;
    $ttb_opts['cookie'] = true;
    
    $altText = array(
        'Translate',
        'Translation',
        'Translate This',
    );
    
    $ttb_opts['altText'] = $altText[ rand(0,2) ];
    $ttb_opts['undoText'] = 'Undo &raquo;';
    $ttb_opts['panelText'] = 'Translate Into:';
    $ttb_opts['moreText'] = '36 More Languages &raquo;';
    $ttb_opts['busyText'] = 'Translating page...';
    $ttb_opts['cancelText'] = 'cancel';
    
    $ttb_opts['fromLang'] = false;
    
    $ttb_opts['defaultDD'] = true;
    $ttb_opts['ddLangs'] = array();
    
    $ttb_opts['noBtn'] = false;
    $ttb_opts['btnImg'] = false;
    $ttb_opts['btnHeight'] = false;
    $ttb_opts['btnWidth'] = false;
    
    $ttb_opts['noImg'] = false;
    
    update_option('ttb_opts', $ttb_opts);
    
    return $ttb_opts;
}

$ttb_opts = get_option('ttb_opts');

// set defaults if first time
if (is_null($ttb_opts['GA'])) ttb_init_opts();

add_action('admin_menu', 'ttb_menu_item');
if ( $_GET['page'] == 'Translate-This-Button' ) add_action('admin_head', 'ttb_css_js');
