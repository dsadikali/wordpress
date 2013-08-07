<?php
/*
Plugin Name: Grou Random Image Widget
Plugin URI: http://wordpress.org/extend/plugins/grou-random-image-widget/
Description: Display a random image from a directory located on the webserver
Author: Grou
Version: 1.17
Author URI: http://grou29.free.fr
*/ 
/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_widgets_GrandomI' );
### Function: Enqueue PageNavi Stylesheets
add_action('wp_print_styles', 'GrandomI_stylesheets');
add_action('init', 'grip_textdomain');
add_thickbox();
function grip_textdomain() {
	load_plugin_textdomain('g-random-img',false,'grou-random-image-widget');
}
function GrandomI_stylesheets() {
	$myStyleUrl = WP_PLUGIN_URL . '/grou-random-image-widget/g-random-img.css';
    $myStyleFile = WP_PLUGIN_DIR . '/grou-random-image-widget/g-random-img.css';

    wp_enqueue_script( "grou-random-image-widget",WP_PLUGIN_URL . '/grou-random-image-widget/gr.js');   
	
	if ( file_exists($myStyleFile) ) {
            wp_register_style('GrandomI', $myStyleUrl);
			wp_enqueue_style('GrandomI', myStyleUrl, false, '1.0', 'all');   
        }
}
function load_widgets_GrandomI() {
	register_widget( 'GrandomI_Widget' );
}

class GrandomI_Widget extends WP_Widget {

	/**Widget setup.*/
	function GrandomI_Widget() {
		$widget_ops = array('classname' => 'widget_grandomImg', 'description' => __('Display a random image from a directory located on the webserver',"g-random-img"));
		$control_ops = array('width' => 600, 'height' => 350);
		$this->WP_Widget('widget_grandomImg', 'Grou Random Img', $widget_ops, $control_ops);
		load_plugin_textdomain('grou-random-image-widget',false,'g-random-img');
		//wp_register_script("grou-random-image-widget", WP_PLUGIN_URL . '/grou-random-image-widget/gr.js');
		
	  }
	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$Reload= $instance['Reload'];
		$ImgRel= $instance['ImgRel'];
		$Grand_ImgPath= $instance['Grand_ImgPath'];
		$Grand_Imgwidth= $instance['Grand_Imgwidth'];
		$Grand_Imgheight= $instance['Grand_Imgheight'];
		$rotate= $instance['rotate'];
		$framesize = $instance['framesize'];
		$show_nav = $instance['show_nav'];
		$photoNum= $instance['photoNum'];
		$randOrNext= $instance['randOrNext'];
		$FixeSize = isset( $instance['FixeSize'] ) ? $instance['FixeSize'] : false;
		$Grand_aspect = isset( $instance['Grand_aspect'] ) ? $instance['Grand_aspect'] : false;
		$Grand_bg=$instance['Grand_bg'];
		$Grand_slideTime=$instance['Grand_slideTime'];		
		$Grand_link_url=$instance['Grand_link_url'];
		$Grand_link=$instance['Grand_link'];
		$Grand_preload=$instance['Grand_preload'];
		$navbut=$instance['navbut'];
		$Grand_static=$instance['Grand_static'];
		$Grand_tooltip=$instance['Grand_tooltip'];
		$PrTooltip = isset( $instance['PrTooltip'] ) ? $instance['PrTooltip'] : false;
		$newPage=isset( $instance['newPage'] ) ? $instance['newPage'] : false; 
		$effect=$instance['effect'];
		/* correct new var not initialize by old setup */ 
		if (!$effect)
		{
			$effect=1;
		}
		if (!$Grand_tooltip)
		{
			$Grand_tooltip="1";
		}
		if (!$randOrNext)
		{
			$randOrNext="1";
		}
		/* Before widget (defined by themes). */
		echo $before_widget;
		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
?>		
		
<script  type="text/javascript"> 
function loadit_<?php  echo str_replace("-","_",$widget_id); ?>() 
{
	griwSetup("<?php echo 'http://'.getenv  ('SERVER_NAME')?>", "<?php echo plugins_url('grou-random-image-widget'); ?>");
<?php 
	$opt = null;		
	
	if (substr($Grand_bg, 0, 1)=="#") { 
			$opt = substr($Grand_bg, 1,6 );
		} else { 
			$opt = $Grand_bg;
		}
	if (($rotate!= null) && ($rotate != "")) {
		$opt = $opt."&amp;r=".$rotate;
	}
	
	echo 'griwAddImage("'.$widget_id.'", '. $Grand_Imgwidth.','. $Grand_Imgheight.',"'. $Grand_ImgPath.'"';
	if ($FixeSize == true)
	{
		echo ",1";
		} else {echo ",0";}
	if ($show_nav == "5")
	{	
		echo ",".$Grand_slideTime;
	} else {echo ",0";}	
	if ($Grand_aspect==true) {
		echo ",".$photoNum;
	} else {
		echo ",null";
		}
	echo ",".$framesize. ",'".$opt."'";
	if (($Grand_link!=null) &&
	   ($Grand_link!=2))
	{
		echo ",".$Grand_link;
	}else{
		echo ",'".$Grand_link_url."'";
	}
	
	if (($Grand_preload==true) &&
		( $Grand_link==1))
	{
		echo ",1";
	} else {
		echo ",0";
	}
	echo ",".$randOrNext;
	echo ",".$Grand_tooltip;
	echo ",".$effect;
	echo ");";
?>
}

addLoadEvent(loadit_<?php echo  str_replace("-","_",$widget_id); ?>); 
</script>
		<div class="Grou_image" id="<?php echo $widget_id; ?>frm" style='padding:0; width: <?php echo $Grand_Imgwidth.";"; ?>'			
			 align="left">		
			<div style="position:absolute; z-index:4; " id="<?php echo $widget_id.'img1d'; ?>" >
			<?php  if ($Grand_link!=4) {?>
				<a id="<?php echo $widget_id.'link1'; ?>" 
				<?php  if ($Grand_link==1)
				{
					echo " href='".plugins_url('grou-random-image-widget/img/loading.gif')."'";
				} else if ($Grand_link==3) {
					echo " href='.'" ;
				} else {
					echo " href='".$Grand_link_url."'" ;
				}
				
				if ($Grand_tooltip=="3")
				{
					echo ' title="'.$Grand_static.'" ' ;
				}
				if ($newPage==true)
				{
					echo ' target="_blank"';
				}
				?> >
			<?php } ?>
				<img class='Grou_image' alt="wait"   id="<?php echo $widget_id.'img1'; ?>" style=" opacity: 100; display: block;position:relative;" 
					src="<?php echo plugins_url('grou-random-image-widget/img/loading.gif') ?>" /> 				
			<?php  if ($Grand_link!=4) echo "</a>"; ?>
			
			</div>	
			<div style="position:absolute; z-index:3; " id="<?php echo $widget_id.'img2d'; ?>">
			<?php  if ($Grand_link!=4) {?>
				<a id="<?php echo $widget_id.'link2'; ?>" 
			    <?php  if ($Grand_link==1)
				{
					echo " href='".plugins_url('grou-random-image-widget/img/loading.gif')."'";
				} else if ($Grand_link==3) {
					echo " href='.'" ;
				} else {
					echo " href='".$Grand_link_url."'" ;
				}
				if ($Grand_tooltip=="3")
				{
					echo " title='".$Grand_static."'" ;
				}
				if ($newPage==true)
				{
					echo ' target="_blank"';
				}
				?> >
				<?php } ?>				
				<img class='Grou_image'  alt="wait"   id="<?php echo $widget_id.'img2'; ?>" style=" opacity: 100;display: block;position:relative;"  
					src="<?php echo plugins_url('grou-random-image-widget/img/loading.gif') ?>" />  
				
				<?php  if ($Grand_link!=4) echo "</a>"; ?>		
			</div>		
		</div>
		
		<?php if (($Grand_static!="1") && ($PrTooltip=="true")) {?>
			<div class="RI_Textzone" id="<?php echo $widget_id.'RI_Textzone'; ?>"> </div>
		<?php }?>
		
			<div class="griwNavigation" id="<?php echo $widget_id; ?>nav" align="left"  >			
				<table class="RI_Tab" width=<?php echo $Grand_Imgwidth; ?>px height="20px" >
				<tr>
				<td class="RI_Tab">
				    <?php if (($show_nav == "2") || ($show_nav == "4")) { ?>
						<a id="<?php echo $widget_id; ?>prevIm" href="" > <img title="previous" alt="&lt;&lt;" src="<?php echo plugins_url('grou-random-image-widget/img/back').$navbut.'.png'; ?>" /></a> 
					<?php }?></td>
				<td  class="RI_Tab" id="<?php echo $widget_id.'navzone'; ?>">
				<?php if (($show_nav == "3") || ($show_nav == "4")) {?>
						<a href="JavaScript:griwRefresh('<?php echo $widget_id; ?>',null);" ><?php echo $Reload; ?> </a>	
					<?php } ?></td>
				<td class="RI_Tab"><?php if (($show_nav == "2") | ($show_nav == "4")) { ?>			 
					 	<a id="<?php echo $widget_id; ?>nextIm" href="" > <img title="next" alt=">>" src="<?php echo plugins_url('grou-random-image-widget/img/next').$navbut.'.png'; ?>" /></a>
					 <?php }?></td>
					 </tr>
				</table>
			</div>		
<?php	
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
	
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['Reload'] = strip_tags( $new_instance['Reload'] );
		$instance['Grand_ImgPath'] = strip_tags( $new_instance['Grand_ImgPath'] );
		$instance['Grand_Imgheight'] = strip_tags( $new_instance['Grand_Imgheight'] );
		$instance['Grand_Imgwidth'] = strip_tags( $new_instance['Grand_Imgwidth'] );		
		$instance['framesize'] = strip_tags( $new_instance['framesize'] );
		$instance['rotate'] = strip_tags( $new_instance['rotate'] );
		$instance['Grand_bg'] = strip_tags( $new_instance['Grand_bg'] );
		$instance['Grand_slideTime'] = strip_tags( $new_instance['Grand_slideTime'] );	
		$instance['photoNum'] = strip_tags( $new_instance['photoNum'] );
		$instance['randOrNext'] = strip_tags( $new_instance['randOrNext'] );
		$instance['show_nav']= strip_tags( $new_instance['show_nav'] );
		$instance['navbut']= strip_tags( $new_instance['navbut'] );
		$instance['FixeSize']= (bool) strip_tags(stripslashes(  $new_instance['FixeSize']));
		$instance['Grand_aspect'] = (bool) strip_tags(stripslashes(  $new_instance['Grand_aspect']));
		$instance['Grand_link']=  strip_tags(stripslashes(  $new_instance['Grand_link']));
		$instance['Grand_link_url']=  strip_tags( $new_instance['Grand_link_url']);
		$instance['Grand_preload']= (bool) strip_tags( stripslashes($new_instance['Grand_preload']));
		$instance['Grand_static']= strip_tags( $new_instance['Grand_static'] );
		$instance['Grand_tooltip']= strip_tags( $new_instance['Grand_tooltip'] );
		$instance['PrTooltip']= (bool) strip_tags( stripslashes($new_instance['PrTooltip']));
		$instance['newPage'] = (bool) strip_tags(stripslashes(  $new_instance['newPage']));
		$instance['effect']= strip_tags( $new_instance['effect'] );
		
		return $instance;
	}
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {	
?>
		<script language="JavaScript" type="text/javascript"> 
		function checkAspect<?php echo $this->number; ?> ()
		{	
			if (document.getElementById("<?php echo $this->get_field_id( 'Grand_aspect' ); ?>").checked == true)
			{
				document.getElementById("<?php echo $this->get_field_id( 'Grand_bg' ); ?>").style.visibility ="visible";			
				document.getElementById("<?php echo $this->get_field_id( 'framesize' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo $this->get_field_id( 'rotate' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo $this->get_field_id( 'photoNum' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'photoNum' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Grand_bg' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'rotate' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'framesize' ); ?>").style.visibility ="visible";
			
			} else {
				document.getElementById("<?php echo $this->get_field_id( 'Grand_bg' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo $this->get_field_id( 'framesize' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo $this->get_field_id( 'rotate' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo $this->get_field_id( 'photoNum' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'photoNum' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Grand_bg' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'rotate' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'framesize' ); ?>").style.visibility ="hidden";
			
			}
			if (document.getElementById("<?php echo $this->get_field_id( 'show_nav' ); ?>").value == "5")
			{
				document.getElementById("<?php echo $this->get_field_id( 'Grand_slideTime' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Grand_slideTime' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'randOrNext' ); ?>").style.visibility ="visible";
				
			} else {
				document.getElementById("<?php echo $this->get_field_id( 'Grand_slideTime' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Grand_slideTime' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'randOrNext' ); ?>").style.visibility ="hidden";
			}
			if (document.getElementById("<?php echo $this->get_field_id( 'show_nav' ); ?>").value == "2") 
			{			
				document.getElementById("<?php echo $this->get_field_id( 'Reload' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Reload' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo $this->get_field_id( 'navbut' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'navbut' ); ?>").style.visibility ="visible";
			} else if (document.getElementById("<?php echo $this->get_field_id( 'show_nav' ); ?>").value == "3") 
			{
				document.getElementById("<?php echo $this->get_field_id( 'Reload' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Reload' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo $this->get_field_id( 'navbut' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'navbut' ); ?>").style.visibility ="hidden";
				
			} else if (document.getElementById("<?php echo $this->get_field_id( 'show_nav' ); ?>").value == "4") 
			{
				document.getElementById("<?php echo $this->get_field_id( 'Reload' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Reload' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo $this->get_field_id( 'navbut' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo "label".$this->get_field_id( 'navbut' ); ?>").style.visibility ="visible";
			}else {
				document.getElementById("<?php echo $this->get_field_id( 'Reload' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Reload' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo $this->get_field_id( 'navbut' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'navbut' ); ?>").style.visibility ="hidden";
			}	
			
			if (document.getElementById("<?php echo $this->get_field_id( 'Grand_link' ); ?>").value == 2)
			{
				document.getElementById("<?php echo $this->get_field_id( 'Grand_link_url' ); ?>").style.visibility ="visible";	
				
				document.getElementById("<?php echo $this->get_field_id( 'Grand_preload' ); ?>").style.visibility ="hidden";
				document.getElementById("<?php echo "label".$this->get_field_id( 'Grand_preload' ); ?>").style.visibility ="hidden";
						
			} else if (document.getElementById("<?php echo $this->get_field_id( 'Grand_link' ); ?>").value == 1)
				{
					document.getElementById("<?php echo $this->get_field_id( 'Grand_link_url' ); ?>").style.visibility ="hidden";
					if (document.getElementById("<?php echo $this->get_field_id( 'Grand_aspect' ); ?>").checked == true)
					{
						document.getElementById("<?php echo $this->get_field_id( 'Grand_preload' ); ?>").style.visibility ="visible";
						document.getElementById("<?php echo "label".$this->get_field_id( 'Grand_preload' ); ?>").style.visibility ="visible";
					} else {
						document.getElementById("<?php echo $this->get_field_id( 'Grand_preload' ); ?>").style.visibility ="hidden";
						document.getElementById("<?php echo "label".$this->get_field_id( 'Grand_preload' ); ?>").style.visibility ="hidden";
					}				
				} else {
					document.getElementById("<?php echo $this->get_field_id( 'Grand_link_url' ); ?>").style.visibility ="hidden";	
					
					document.getElementById("<?php echo $this->get_field_id( 'Grand_preload' ); ?>").style.visibility ="hidden";
					document.getElementById("<?php echo "label".$this->get_field_id( 'Grand_preload' ); ?>").style.visibility ="hidden";					
				}
				
			if (document.getElementById("<?php echo $this->get_field_id( 'Grand_tooltip' ); ?>").value == "3")
			{
				document.getElementById("<?php echo $this->get_field_id( 'Grand_static' ); ?>").style.visibility ="visible";	
			} else {
				document.getElementById("<?php echo $this->get_field_id( 'Grand_static' ); ?>").style.visibility ="hidden";
			}	
			if (document.getElementById("<?php echo $this->get_field_id( 'Grand_tooltip' ); ?>").value == "1")
			{
				document.getElementById("<?php echo $this->get_field_id( 'PrTooltip' );?>").style.visibility ="hidden";	
				document.getElementById("<?php echo $this->get_field_id( 'PrTooltip' ).label; ?>").style.visibility ="hidden";
			} else {
				
				document.getElementById("<?php echo $this->get_field_id( 'PrTooltip' ); ?>").style.visibility ="visible";
				document.getElementById("<?php echo $this->get_field_id( 'PrTooltip' ).label; ?>").style.visibility ="visible";
			}
		}
		</script>
		<?php 
		/* Set up some default widget settings. */
		$rep = str_replace("\\", "/",  WP_PLUGIN_DIR); 
		$rep2= str_replace("\\", "/",  $_SERVER['DOCUMENT_ROOT']);
		$rep = str_replace($rep2, "",  $rep); 
		$defaults = array( 	'title' => __('Example', 'g-random-img'), 'Reload' => __('Reload', 'g-random-img'), 
							'Grand_ImgPath' => $rep   ."/grou-random-image-widget/example" , 'show_nav' => '1' ,
							'Grand_Imgheight' => 200, 'Grand_Imgwidth' => 200, 'FixeSize'=>'false', 
							'Grand_aspect'=>true, 'framesize'=>'10', 'rotate'=>'4',
							'Grand_bg'=>"#FFFFFF",
							'Grand_slideTime'=>"10", 
							"photoNum"=>2, 
							"randOrNext"=>"1", 
							"navbut"=>"1", 
							"Grand_link"=>1,
							"Grand_link_url"=>'/',
							"Grand_static"=>"",
							"Grand_tooltip"=>"1",
							"Grand_preload"=>false,
							"effect"=>1,
							"PrTooltip"=>"false");
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<!-- Widget Title: Text Input -->
		
		<TABLE BORDER="1" >
  <TR>
  <TD> 
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'g-random-img'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" 
			        name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>		
		<!-- Image Path: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'Grand_ImgPath' ); ?>"><?php _e('Image path:', 'g-random-img'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'Grand_ImgPath' ); ?>" name="<?php echo $this->get_field_name( 'Grand_ImgPath' ); ?>" value="<?php echo $instance['Grand_ImgPath']; ?>" style="width:100%;" />
		</p>		
			<p>			
			<label for="<?php echo $this->get_field_id( 'show_nav' ); ?>"> 	
			<?php _e('typeaction', 'g-random-img'); ?>			
						<select class="widefat" 
							id=<?php echo $this->get_field_id( 'show_nav' ); ?> 
							name=<?php echo $this->get_field_name( 'show_nav' ); ?>
							for=<?php echo $this->get_field_id( 'show_nav' ); ?>
							onchange="checkAspect<?php echo $this->number; ?>()"							
						>
							<option value="1" <?php selected($instance['show_nav'],"1"); ?> > <?php _e("Simple",'g-random-img');?> </option>
							<option value="2" <?php selected($instance['show_nav'],"2"); ?> > <?php _e("WithNav",'g-random-img');?> </option>
							<option value="3" <?php selected($instance['show_nav'],"3"); ?>> <?php _e("WithRandom",'g-random-img');?> </option>
							<option value="4" <?php selected($instance['show_nav'],"4"); ?>> <?php _e("WithNavRandom",'g-random-img');?> </option>
							<option value="5" <?php selected($instance['show_nav'],"5"); ?>> <?php _e("SlideShow",'g-random-img');?> </option>												
						</select>
			</label>
		</p>		
		<div class="postbox">
		<!-- Random Text: slideshow timout Text -->
		<p>
			<label id="<?php echo "label".$this->get_field_id( 'Grand_slideTime' );?>"  for="<?php echo $this->get_field_id( 'Grand_slideTime' ); ?>"><?php _e('Slideshow wait (second):', 'g-random-img'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'Grand_slideTime' ); ?>" name="<?php echo $this->get_field_name( 'Grand_slideTime' ); ?>" value="<?php echo $instance['Grand_slideTime']; ?>" style="width:80%;" />
		</p>
		<!-- Random or next -->
		<label for="<?php echo $this->get_field_id( 'randOrNe2xt' ); ?>" id="<?php echo "label".$this->get_field_id( 'randOrNext' ); ?>"> 								
				<?php _e('randOrNext', 'g-random-img'); ?>	<br/>
				<input type="radio" name="<?php echo $this->get_field_name( 'randOrNext' ); ?>" value="1"' <?php checked( $instance['randOrNext'], 1 ); ?> 
					id="<?php echo $this->get_field_id( 'randOrNext' ); ?>">
					<?php _e('Rand','g-random-img'); ?></input> 
				<input type="radio" name="<?php echo $this->get_field_name( 'randOrNext' ); ?>" value="2"' <?php checked( $instance['randOrNext'], 2 ); ?> 
					id="<?php echo $this->get_field_id( 'randOrNext' ); ?>">
					<?php _e('Next','g-random-img'); ?></input>
		</label> 		
		<!-- Random Text: Reload Text -->
		<p>
			<label id="<?php echo "label".$this->get_field_id( 'Reload' );?>" for="<?php echo $this->get_field_id( 'Reload' ); ?>"><?php _e('Random text:', 'g-random-img'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'Reload' ); ?>" name="<?php echo $this->get_field_name( 'Reload' ); ?>" value="<?php echo $instance['Reload']; ?>" style="width:80%;" />
		</p>
		<!-- Nav button-->
		<label for="<?php echo $this->get_field_id( 'navbut2' ); ?>" id="<?php echo "label".$this->get_field_id( 'navbut' ); ?>"> 								
				<?php _e('navbut', 'g-random-img'); ?>	<br/>
				<input type="radio" name="<?php echo $this->get_field_name( 'navbut' ); ?>" value="1"' <?php checked( $instance['navbut'], 1 ); ?> 
					id="<?php echo $this->get_field_id( 'navbut' ); ?>">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/next1.png'); ?>"  />
					</input> 
				<input type="radio" name="<?php echo $this->get_field_name( 'navbut' ); ?>" value="2"' <?php checked( $instance['navbut'], 2 ); ?> 
					id="<?php echo $this->get_field_id( 'navbut' ); ?>">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/next2.png'); ?>"  />
					</input>
				<input type="radio" name="<?php echo $this->get_field_name( 'navbut' ); ?>" value="3"' <?php checked( $instance['navbut'], 3 ); ?> 
					id="<?php echo $this->get_field_id( 'navbut' ); ?>">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/next3.png'); ?>"  />
					</input>
				<input type="radio" name="<?php echo $this->get_field_name( 'navbut' ); ?>" value="4"' <?php checked( $instance['navbut'], 4 ); ?> 
					id="<?php echo $this->get_field_id( 'navbut' ); ?>">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/next4.png'); ?>"  />
					</input>
				<input type="radio" name="<?php echo $this->get_field_name( 'navbut' ); ?>" value="5"' <?php checked( $instance['navbut'], 5 ); ?> 
					id="<?php echo $this->get_field_id( 'navbut' ); ?>">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/next5.png'); ?>"  />
					</input>	
		</label> <br/>
		</div>
		<!-- Width: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'Grand_Imgwidth' ); ?>"><?php _e('Max. image width x height:', 'g-random-img'); ?></label><br/>
			<input class="widefat" id="<?php echo $this->get_field_id( 'Grand_Imgwidth' ); ?>" name="<?php echo $this->get_field_name( 'Grand_Imgwidth' ); ?>" value="<?php echo $instance['Grand_Imgwidth']; ?>" style="width:30%;" />
			x
			<input class="widefat" id="<?php echo $this->get_field_id( 'Grand_Imgheight' ); ?>" name="<?php echo $this->get_field_name( 'Grand_Imgheight' ); ?>" value="<?php echo $instance['Grand_Imgheight']; ?>" style="width:30%;" />
		</p>
		<!-- Show Nav? Checkbox -->		
		<!-- <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_nav'], true ); ?> id="<?php echo $this->get_field_id( 'show_nav' ); ?>" name="<?php echo $this->get_field_name( 'show_nav' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_nav' ); ?>"><?php _e('Show navigation (next/previous)', 'g-random-img'); ?></label>
		</p> -->		
		<!-- Fixe size? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['FixeSize'], true ); ?> id="<?php echo $this->get_field_id( 'FixeSize' ); ?>" name="<?php echo $this->get_field_name( 'FixeSize' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'FixeSize' ); ?>"><?php _e('Fixe size', 'g-random-img'); ?></label>
		</p>
		<p>		
		<label for="<?php echo $this->get_field_id( 'Grand_link' ); ?>"> 	
			<?php _e('Link To:', 'g-random-img'); ?>			
						<select class="widefat" 
							id=<?php echo $this->get_field_id( 'Grand_link' ); ?> 
							name=<?php echo $this->get_field_name( 'Grand_link' ); ?>
							for=<?php echo $this->get_field_id( 'Grand_link' ); ?>
							onchange="checkAspect<?php echo $this->number; ?>()"							
						>
							<option value="4" <?php selected($instance['Grand_link'],"4"); ?> > <?php _e("Disable",'g-random-img');?> </option>
							<option value="1" <?php selected($instance['Grand_link'],"1"); ?> > <?php _e("Full size image",'g-random-img');?> </option>
							<option value="2" <?php selected($instance['Grand_link'],"2"); ?> > <?php _e("Static URL",'g-random-img');?> </option>
							<option value="3" <?php selected($instance['Grand_link'],"3"); ?>> <?php _e("URL in description file (*.txt)",'g-random-img');?> </option>
							
						</select>
			</label>				
		<input class="widefat" id="<?php echo $this->get_field_id( 'Grand_link_url' ); ?>" name="<?php echo $this->get_field_name( 'Grand_link_url' ); ?>" value="<?php echo $instance['Grand_link_url']; ?>" style="width:100%;" />
		<!-- Fixe size? new window -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['newPage'], true ); ?> id="<?php echo $this->get_field_id( 'newPage' ); ?>" name="<?php echo $this->get_field_name( 'newPage' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'newPage' ); ?>"><?php _e('Open in new window', 'g-random-img'); ?></label>
		</p>		
				
					
		<p>
		<label for="<?php echo $this->get_field_id( 'Grand_tooltip' ); ?>"> 	
		<?php _e('Tooltip', 'g-random-img'); ?> </label><br/>
			
			<select class="widefat" 
				id=<?php echo $this->get_field_id( 'Grand_tooltip' ); ?> 
				name=<?php echo $this->get_field_name( 'Grand_tooltip' ); ?>
				for=<?php echo $this->get_field_id( 'Grand_tooltip' ); ?>
				onchange="checkAspect<?php echo $this->number; ?>()">
				<option value="1" <?php selected($instance['Grand_tooltip'],"1"); ?> > <?php _e("None",'g-random-img');?> </option>
				<option value="2" <?php selected($instance['Grand_tooltip'],"2"); ?> > <?php _e("Image description",'g-random-img');?> </option>
				<option value="3" <?php selected($instance['Grand_tooltip'],"3"); ?>> <?php _e("Static text",'g-random-img');?> </option>
			</select>	
		</p>
		<!--  static text -->
		<input class="widefat" id="<?php echo $this->get_field_id( 'Grand_static' ); ?>" name="<?php echo $this->get_field_name( 'Grand_static' ); ?>" value="<?php echo $instance['Grand_static']; ?>"  />
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['PrTooltip'], true ); ?> id="<?php echo $this->get_field_id( 'PrTooltip' ); ?>" name="<?php echo $this->get_field_name( 'PrTooltip' ); ?>" /> 
			<label id="<?php echo $this->get_field_id( 'PrTooltip' ).label; ?>"
			for="<?php echo $this->get_field_id( 'PrTooltip' ); ?>"><?php _e('Use tooltip for legend', 'g-random-img'); ?></label>
		</p>
		</TD>
 <TD width="20px"></TD><TD>  
		<!-- Fixe size? Checkbox -->
		<p>
		<!-- Random or next -->
			<p>
				<label for="<?php echo $this->get_field_id( 'effect' ); ?>"> 	
			<?php _e('Effect', 'g-random-img'); ?>			
						<select class="widefat" 
							id=<?php echo $this->get_field_id( 'effect' ); ?> 
							name=<?php echo $this->get_field_name( 'effect' ); ?>
							for=<?php echo $this->get_field_id( 'effect' ); ?>
							onchange="checkAspect<?php echo $this->number; ?>()"							
						>
							<option value="3" <?php selected($instance['effect'],"3"); ?> > <?php _e("None",'g-random-img');?> </option>
							<option value="1" <?php selected($instance['effect'],"1"); ?> > <?php _e("Fade",'g-random-img');?> </option>
							<option value="2" <?php selected($instance['effect'],"2"); ?> > <?php _e("Shrink",'g-random-img');?> </option>						
							<option value="4" <?php selected($instance['effect'],"4"); ?> > <?php _e("Vert. Slide ",'g-random-img');?> </option>						
							<option value="5" <?php selected($instance['effect'],"5"); ?> > <?php _e("Horiz. Slide",'g-random-img');?> </option>						
							
						</select>
			</label>
			</p> 
			
			<br/>
			<input class="checkbox" type="checkbox" <?php checked( $instance['Grand_aspect'], true ); ?> id="<?php echo $this->get_field_id( 'Grand_aspect' ); ?>" name="<?php echo $this->get_field_name( 'Grand_aspect' ); ?>" 
				onclick="checkAspect<?php echo $this->number; ?>()" /> 
			<label for="<?php echo $this->get_field_id( 'Grand_aspect' ); ?>"><?php _e('Picture aspect', 'g-random-img'); 
					
			_e(' (using Gd version: ', 'g-random-img'); 
			if (! ((extension_loaded('gd') && function_exists('gd_info'))))
			{ _e(' Not found ', 'g-random-img');}
			else {
				$s= gd_info();
				echo $s["GD Version"]  ;				
			} 
			echo ")";			
			?></label>
					
		<div class="postbox" id = "aspect<?php echo $this->number; ?>">
			<!-- Widget Title: frame size -->
			<table border=1>
			<tr><td>
			<p>
				<label for="<?php echo $this->get_field_id( 'framesize' ); ?>" id="<?php echo "label".$this->get_field_id( 'framesize' ); ?>" >
				<?php _e('Frame Size:', 'g-random-img'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'framesize' ); ?>" name="<?php echo $this->get_field_name( 'framesize' ); ?>" value="<?php echo $instance['framesize']; ?>" style="width:80%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'rotate' ); ?>" id="<?php echo "label".$this->get_field_id( 'rotate' ); ?>">
				<?php _e('Rotate angle: (empty=> random)', 'g-random-img'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'rotate' ); ?>" name="<?php echo $this->get_field_name( 'rotate' ); ?>" value="<?php echo $instance['rotate']; ?>" style="width:80%;" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id( 'Grand_bg' ); ?>" id ="<?php echo "label".$this->get_field_id( 'Grand_bg' ); ?>" ><?php _e('Background color:', 'g-random-img'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'Grand_bg' ); ?>" name="<?php echo $this->get_field_name( 'Grand_bg' ); ?>" value="<?php echo $instance['Grand_bg']; ?>" style="width:80%;" />
			</p>
			
			<p>			
			<label for="<?php echo $this->get_field_id( 'selimg' ); ?>" id="<?php echo "label".$this->get_field_id( 'photoNum' ); ?>"> 									
				<?php _e('Picture frame:', 'g-random-img'); ?>	<br/>
				<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="1"' <?php checked( $instance['photoNum'], 1 ); ?> 
					id="<?php echo $this->get_field_id( 'photoNum' ); ?>">
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto.png'); ?>" width="70" ></a></input> 
				<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="2"' <?php checked( $instance['photoNum'], 2 ); ?> 
					id="<?php echo $this->get_field_id( 'photoNum' ); ?>">
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto2.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto2.png'); ?>" width="70" ></a></input>
				<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="6"' <?php checked( $instance['photoNum'], 6 ); ?>
					id="<?php echo $this->get_field_id( 'photoNum' ); ?>">
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto6.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto6.png'); ?>" width="70" ></a></input>
			 	<br/>
			 	<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="3"' <?php checked( $instance['photoNum'], 3 ); ?> 
			 		id="<?php echo $this->get_field_id( 'photoNum' ); ?>">
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto3.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto3.png'); ?>" width="70" ></a></input> 
				<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="4"' <?php checked( $instance['photoNum'], 4 ); ?>
					id="<?php echo $this->get_field_id( 'photoNum' ); ?>"></input>
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto4.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto4.png'); ?>" width="70" ></a>
				<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="5"' <?php checked( $instance['photoNum'], 5 ); ?>
					id="<?php echo $this->get_field_id( 'photoNum' ); ?>">
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto5.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto5.png'); ?>" width="70" ></a>
					</input>
					<br/>
				<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="7"' <?php checked( $instance['photoNum'], 7 ); ?>
					id="<?php echo $this->get_field_id( 'photoNum' ); ?>">
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto7.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto7.png'); ?>" width="70" ></a>
					</input>
					<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="8"' <?php checked( $instance['photoNum'], 8 ); ?>
					id="<?php echo $this->get_field_id( 'photoNum' ); ?>">
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto8.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto8.png'); ?>" width="70" ></a>
					</input>
					<input type="radio" name="<?php echo $this->get_field_name( 'photoNum' ); ?>" value="9"' <?php checked( $instance['photoNum'], 9 ); ?>
					id="<?php echo $this->get_field_id( 'photoNum' ); ?>">
					<a class="thickbox"  href="<?php echo plugins_url('grou-random-image-widget/img/foto9.png');?>?TB_iframe=true">
					<img src="<?php echo plugins_url('grou-random-image-widget/img/foto9.png'); ?>" width="70" ></a>
					</input>
			</label>
			<br/>		
		</p>
		<p>
		<input class="checkbox" type="checkbox" <?php checked( $instance['Grand_preload'], true ); ?> id="<?php echo $this->get_field_id( 'Grand_preload' ); ?>" name="<?php echo $this->get_field_name( 'Grand_preload' ); ?>" 
				onclick="checkAspect<?php echo $this->number; ?>()" /> 
			<label id="<?php echo "label".$this->get_field_id( 'Grand_preload' );?>" for="<?php echo $this->get_field_id( 'Grand_preload' ); ?>"><?php _e('Pre-load full image', 'g-random-img'); ?></label>
		</p>	
		</td></tr>		
			</table>
			</div>
		</p>
		<p/>
		</p>
	</TD>
	 </TR>
</TABLE> 
	
		<script language="JavaScript" type="text/javascript"> 
		checkAspect<?php echo $this->number; ?>();
		</script>
	<?php
	}
}
?>
