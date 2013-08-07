<?php
/*
Plugin Name: Xtreme Banner Rotator
Plugin URI: http://www.flashtuning.net/flash-xml-image-viewers-galleries/x-treme-banner-rotator.html
Description: The most advanced XML Banner Rotator application. No Flash Knowledge required to insert the Banner Rotator SWF inside the HTML page(s) of your site.
Version: 1.2
Author: Flashtuning 
Author http://www.flashtuning.net
*/

$xtreme_banner_swf_nr	= 0; 											

function xtremeBannerRotatorStart($xtreme_obj) {
	
	$txtP = preg_replace_callback('/\[xtreme-banner-rotator\s*(width="(\d+)")?\s*(height="(\d+)")?\s*(xml="([^"]+)")?\s*\]/i', 'xtremeBannerRotatorAddObj', $xtreme_obj); 
	
	return $txtP;
}

function xtremeBannerRotatorAddObj($xtreme_banner_param) {

    global $xtreme_banner_swf_nr; //number of swfs
	$xtreme_banner_swf_nr++;
	
	$xtreme_banner_rand = substr(rand(),0,3);
	
	$xtreme_banner_dir = WP_CONTENT_URL .'/flashtuning/xtreme-banner-rotator/';
	$xtreme_banner_swf = $xtreme_banner_dir.'banner.swf';
	$xtreme_banner_config = "swfobj2";
	
	if ($xtreme_banner_param[2] !=""){$xtreme_banner_width = $xtreme_banner_param[2];}
	else {$xtreme_banner_width = 592;}
	
	if ($xtreme_banner_param[4] !=""){$xtreme_banner_height = $xtreme_banner_param[4];}
	else {$xtreme_banner_height = 300;}

	if ($xtreme_banner_param[6] !=""){$xtreme_banner_xml = $xtreme_banner_dir.$xtreme_banner_param[6];}
	else {$xtreme_banner_xml = $xtreme_banner_dir.'banner-settings.xml';}
	
	
	/*
		quality - low | medium | high | autolow | autohigh | best
		bgcolor - hexadecimal RGB value
		wmode - Window | Opaque | Transparent
		allowfullscreen - true | false
		scale - noscale | showall | noborder | exactfit
		salign - L | R | T | B | TL | TR | BL | BR 
		allowscriptaccess - always | never | samedomain
	
	*/
	
	$xtreme_banner_param = array("quality" =>	"high", "bgcolor" => "", "wmode"	=>	"window", "version" =>	"9.0.0", "allowfullscreen"	=>	"true", "scale" => "noscale", "salign" => "TL", "allowscriptaccess" => "samedomain");
	
	if (is_feed()) {$xtreme_banner_config = "xhtml";}

	
	if ($xtreme_banner_config != "xhtml") {
		$xtreme_banner_output = "<div id=\"xtreme-dock-menu".$xtreme_banner_rand."\">Please install flash player.</div>";
	
	}
	
	switch ($xtreme_banner_config) {
	
		case "xhtml":
			$xtreme_banner_output.= "\n<object width=\"".$xtreme_banner_width."\" height=\"".$xtreme_banner_height."\">\n";
			$xtreme_banner_output.= "<param name=\"movie\" value=\"".$xtreme_banner_swf."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"quality\" value=\"".$xtreme_banner_param['quality']."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"bgcolor\" value=\"".$xtreme_banner_param['bgcolor']."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"wmode\" value=\"".$xtreme_banner_param['wmode']."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"allowFullScreen\" value=\"".$xtreme_banner_param['allowfullscreen']."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"scale\" value=\"".$xtreme_banner_param['scale']."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"salign\" value=\"".$xtreme_banner_param['salign']."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"allowscriptaccess\" value=\"".$xtreme_banner_param['allowscriptaccess']."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"base\" value=\"".$xtreme_banner_dir."\"></param>\n";
			$xtreme_banner_output.= "<param name=\"FlashVars\" value=\"setupXML=".$xtreme_banner_xml."\"></param>\n";
			
			
			$xtreme_banner_output.= "<embed type=\"application/x-shockwave-flash\" width=\"".$xtreme_banner_width."\" height=\"".$xtreme_banner_height."\" src=\"".$xtreme_banner_swf."\" ";
			$xtreme_banner_output.= "quality=\"".$xtreme_banner_param['quality']."\" bgcolor=\"".$xtreme_banner_param['bgcolor']."\" wmode=\"".$xtreme_banner_param['wmode']."\" scale=\"".$xtreme_banner_param['scale']."\" salign=\"".$xtreme_banner_param['salign']."\" allowScriptAccess=\"".$xtreme_banner_param['allowscriptaccess']."\" allowFullScreen=\"".$xtreme_banner_param['allowfullscreen']."\" base=\"".$xtreme_banner_dir."\" FlashVars=\"setupXML=".$xtreme_banner_xml."\"  ";
			
			$xtreme_banner_output.= "></embed>\n";
			$xtreme_banner_output.= "</object>\n";
			break;
	
		default:
		
			$xtreme_banner_output.= '<script type="text/javascript">';
			$xtreme_banner_output.= "swfobject.embedSWF('{$xtreme_banner_swf}', 'xtreme-dock-menu{$xtreme_banner_rand}', '{$xtreme_banner_width}', '{$xtreme_banner_height}', '{$xtreme_banner_param['version']}', '' , { setupXML: '{$xtreme_banner_xml}'}, {base: '{$xtreme_banner_dir}', wmode: '{$xtreme_banner_param['wmode']}', scale: '{$xtreme_banner_param['scale']}', salign: '{$xtreme_banner_param['salign']}', allowScriptAccess: '{$xtreme_banner_param['allowscriptaccess']}', allowFullScreen: '{$xtreme_banner_param['allowfullscreen']}'}, {});";
			$xtreme_banner_output.= '</script>';
	
			break;
					
	}
	return $xtreme_banner_output;
}


function  xtremeBannerRotatorEcho($xtreme_banner_width, $xtreme_banner_height, $xtreme_banner_xml) {
    echo xtremeBannerRotatorAddObj( array( null, null, $xtreme_banner_width, null, $xtreme_banner_height, null, $xtreme_banner_xml) );
}


function xtremeBannerRotatorAdmin() {

if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }


?>
		<div class="wrap">
			<h2>Xtreme Banner Rotator 1.2</h2>
					<table>
					<tr>
						<th valign="top" style="padding-top: 10px;color:#FF0000;">
							!IMPORTANT: Copy the free archive folder in the wp-content folder. (eg.: http://www.yoursite.com/wp-content/flashtuning/xtreme-banner-rotator/)
						</th>
					</tr>
                    
                    <tr>
						<td>
					      <ul>
					        <li>1. Insert the swf into post or page using this tag: <strong>[xtreme-banner-rotator]</strong>.</li>
                            <li>2. If you want to modify the width and height of the banner rotator insert this attributes into the tag: <strong>[xtreme-banner-rotator width="yourvalue" height="yourvalue"]</strong></li>
   					        <li>3. If you want to use multiple instances of Xtreme Banner Rotator on different pages. Follow this steps:
                            	<ul>
	                           <li>a. There are 2 xml files in <strong>wp-content/flashtuning/xtreme-banner-rotator</strong> folder: banner-settings.xml, used for general settings, and banner-content.xml, used for individual items.</li>
                                <li>b. Modify the 2 xml files according to your needs and rename them (eg.: banner-settings2.xml, banner-content2.xml) </li>
                                <li>c. Open the banner-settings2.xml, search for this tag <strong> < object param="contentXML"	value="banner-content.xml" /></strong> and change the attribute value to <em>banner-content2.xml</em> </li>
                                <li>d. Copy the 2 modified xml files to <strong>wp-content/flashtuning/xtreme-banner-rotator</strong></li>
                                <li>e. Use the <strong>xml</strong> attribute [xtreme-banner-rotator xml="banner-settings2.xml"] when you insert the banner rotator on a page. </li>
                                </ul>
                            <li>4. Optionally for custom pages use this php function: <strong>xtremeBannerRotatorEcho(width,height,xmlFile)</strong> (e.g: xtremeBannerRotatorEcho(595,420,'banner-settings.xml') )</li>                  
                            </ul>
						</td>
				  </tr>                   
                    
                    
					<tr>
						<td>
						  <p>Check out other useful links. If you have any questions / suggestions, please leave a comment on the component page. </p>
					      <ul>
					        <li><a href="http://www.flashtuning.net">Author Home Page</a></li>
			                <li><a href="http://www.flashtuning.net/flash-xml-image-viewers-galleries/x-treme-banner-rotator.html">Xtreme Banner Rotator Page</a> </li>
			           </ul>
						</td>
				  </tr>
				</table>
			
		</div>
		
<?php
}
function xtremeBannerRotatorAdminAdd() {
	
	add_options_page('Xtreme Banner Rotator Options', 'Xtreme Banner Rotator', 'manage_options','xtremebannerrotator', 'xtremeBannerRotatorAdmin');
}

function xtremeBannerRotatorSwfObj() {
		wp_enqueue_script('swfobject');
	}


add_filter('the_content', 'xtremeBannerRotatorStart');
add_action('admin_menu', 'xtremeBannerRotatorAdminAdd');
add_action('init', 'xtremeBannerRotatorSwfObj');
?>