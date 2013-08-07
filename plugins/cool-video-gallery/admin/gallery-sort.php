<?php 
/**
 * Section to sort videos in a gallery
 * @author Praveen Rajan
 */
?>
<script src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>js/jquery.ui.core.js"></script>
<script src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>js/jquery.ui.widget.js"></script>
<script src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>js/jquery.ui.mouse.js"></script>
<script src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>js/jquery.ui.sortable.js"></script>

<?php echo '<link rel="stylesheet" href="'.trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))).'css/jquery.ui.all.css" type="text/css" />';?>

<script>
jQuery(function() {
	jQuery( "#sortable" ).sortable({
		placeholder: "ui-state-highlight",
		cursor:  "crosshair",
		opacity: 0.6,
		update: function(event, ui) { 
   			var result = jQuery('#sortable').sortable('toArray');
   			jQuery('#sortOrder').val('');
   			jQuery('#sortOrder').val(result); 
			}
	});
	jQuery( "#sortable" ).disableSelection();
		  
});
</script>	
<?php

if (isset ($_POST['updateSortOrder']))  {
	
	global $wpdb;
    $sub_name_videos = 'cvg_videos';
    $table_videos = $wpdb->prefix . $sub_name_videos;
	        
	$sortArray = explode(',', $_POST['sortOrder']);

	if (is_array($sortArray)){ 
		$sortindex = 1;
		foreach($sortArray as $pid) {		
			$wpdb->query("UPDATE $table_videos SET sortorder = '$sortindex' WHERE pid = $pid");
			$sortindex++;
		}
		CvgCore::show_video_message(__('Sort order updated successfully!'));
	} 
}
$gid = $_GET['gid'];
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'sortorder';

//Section if no gallery is selected.
if(!isset($gid)) { 
	?>
	<div class="wrap">
		<div class="icon32" id="icon-video"><br></div>
		<h2>Sort Gallery Videos</h2>
		<div class="clear"></div>
		<div class="versions">
	    	<p>
				Manage your video galleries at <a class="button rbutton" href="<?php echo admin_url('admin.php?page=cvg-gallery-manage');?>"><?php _e('Manage Gallery') ?></a>
			</p>
			<br class="clear" />
		</div> 
		<?php 	CvgCore::show_video_error( __('No gallery selected!') ); ?>
	</div> 
<?php 	
}else {
	
	$options = get_option('cvg_settings');
				

	$gallery = videoDB::find_gallery($gid);
	$title = __('Gallery to sort: '. $gallery->name);
	
	if (!$gallery)  
		CvgCore::show_video_error(__('Gallery not found.', 'nggallery'));
	
	if ($gallery) { 
			// look for pagination	
			
			$videolist = videoDB::get_gallery($gid, $orderBy, 'asc', $per_page, $start_page);
			$act_author_user = get_userdata( (int) $gallery->author );
			$base_url = admin_url('admin.php?page=cvg-gallery-sort&gid=' . $_GET['gid'] . '&order=') ;
			?>
						
			<div class="wrap">
				<div class="icon32" id="icon-video"><br></div>
				<h2><?php echo esc_html( $title ); ?></h2>
				<div class="clear" style="min-height:10px;"></div>
				
				<form id="updatevideos" method="POST" action="<?php echo admin_url('admin.php?page=cvg-gallery-sort&gid=' . $_GET['gid']); ?>" accept-charset="utf-8">
				
					<div class="tablenav">
						<div class="alignleft actions">
							<input type="submit" name="updateSortOrder" class="button-primary action"  value="<?php _e('Update Sort Order');?>" />
							<a class="button" style="padding:5px 10px;" href="<?php echo admin_url('admin.php?page=cvg-gallery-details&gid=' . $_GET['gid']); ?>"><?php _e('Back to Gallery'); ?></a>
						</div>
					</div>	
					
					<ul class="subsubsub">
						<li><?php _e('Sort By') ?> :</li>
						<li><a href="<?php echo $base_url . 'pid'; ?>" <?php if ($orderBy == 'pid') echo 'class="current"'; ?>><?php _e('Video ID') ?></a> |</li>
						<li><a href="<?php echo $base_url . 'filename'; ?>"  <?php if ($orderBy == 'filename') echo 'class="current"'; ?>><?php _e('Video Name') ?></a></li>
					</ul>
					<div class="clear"></div>
					
					<?php
						if($videolist) {
							
							$options = get_option('cvg_settings');
							$thumb_width = $options['cvg_preview_width'];
							$thumb_height = $options['cvg_preview_height'];
							$cv_zc = $options['cvg_zc'];
							$thumb_quality = $options['cvg_preview_quality'];
							?>
							<style>
								.ui-state-highlight { width: <?php echo ($thumb_width + 10)?>px; height: <?php echo ($thumb_height + 30) ?>px; }
							</style>
							<?php 
							echo '<ul id="sortable">';
							$pid_list = '';
							foreach($videolist as $video) {
								$pid = $video->pid;
								
								$video_name = $video->filename;
								$video_thumb_filename = $video->thumb_filename;
								$video_thumb_url = site_url() . '/' .  $video->path . '/thumbs/' . $video_thumb_filename;
								
								if(!file_exists(ABSPATH . $video->path . '/thumbs/' .$video->thumb_filename))
									$video_thumb_url  = WP_CONTENT_URL .  '/plugins/' . dirname(dirname( plugin_basename(__FILE__))) . '/images/default_video.png';
				
								$output =  '<div style="float:left;border:1px SOLID #CCCCCC;padding:5px;"><img src="' ;
								$output .= $video_thumb_url; 
								$output .=  '" style="width:' . $thumb_width . 'px;height:' . $thumb_height . 'px;" alt="preview"/></div>';
								$output .= '<div class="clear"></div><div style="text-align:center;">'. $video->alttext . '</div>';
								echo '<li class="ui-state-default" id="' . $pid . '">';
								echo $output;
								echo '</li>';
								$pid_list .= $pid . ',';
							}
							echo '</ul>';
						} else {
						}
						$pid_list = substr($pid_list, 0, (strlen($pid_list) - 1));
					?>	
			
				<input type="hidden" value="<?php echo $pid_list;?>" name="sortOrder" id="sortOrder" />
			</form>
		</div>
		
	<?php } ?>	
<?php } ?>