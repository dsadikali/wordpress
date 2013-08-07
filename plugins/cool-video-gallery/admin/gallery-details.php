<?php 
/**
 * Section to display gallery details.
 * @author Praveen Rajan
 */
?>
<script src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>js/jquery.multifile.js"></script>
<script src="<?php echo trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))) ?>js/thickbox.js"></script>

<?php echo '<link rel="stylesheet" href="'.trailingslashit( WP_PLUGIN_URL . '/' .	dirname(dirname( plugin_basename(__FILE__)))).'css/thickbox.css" type="text/css" />';?>
<script type="text/javascript">
	/*
	 * Section to initialise multiple file upload
	 */
	jQuery(document).ready(function(){
		jQuery('#preview_image').MultiFile({
			STRING: {
		    	remove:'[<?php _e('remove') ;?>]',
		    	denied:'File type not permitted.',
  			},
  			accept : 'png,PNG',
  			max: 1,
	 	});
	});
</script>

<?php
//Section to update video gallery details
if (isset ($_POST['updatevideogallery']))
	if(videoDB::update_gallery()) 
		CvgCore::show_video_message(__('Gallery details successfully updated.'));

//Section to update video details.
if(isset($_POST['updatevideos'])) 
	if(CvgCore::update_videos())
		echo '<div class="clear"><div class="wrap"><div id="message" class="updated fade below-h2"><p>Details of Video(s) updated successfully.</p></div></div></div>';	 

//Section to delete video list.
if(isset($_POST['TB_gallerylist']) && !empty($_POST['TB_gallerylist']) && $_POST['TB_DeleteVideo'] == 'OK') {
	
	$pids = explode(',', $_POST['TB_gallerylist']);
	foreach($pids as $pid) {
		CvgCore::delete_video_files($pid);
		videoDB::delete_video($pid);
	}
	echo '<div class="clear"><div class="wrap"><div id="message" class="updated fade below-h2"><p>Video(s) deleted successfully.</p></div></div></div>';
}

//Section to delete a single video.
if(isset($_POST['TB_videosingle']) && !empty($_POST['TB_videosingle']) && $_POST['TB_DeleteSingleVideo'] == 'OK') {
	
	$pid = $_POST['TB_videosingle'];
	CvgCore::delete_video_files($pid);
	videoDB::delete_video($pid);
	echo '<div class="clear"><div class="wrap"><div id="message" class="updated fade below-h2"><p>Video deleted successfully.</p></div></div></div>';
}

//Section to upload preview imaage for video
if(isset($_POST['TB_previewimage_single']) && !empty($_POST['TB_previewimage_single']) && trim($_POST['TB_UploadPreviewImage']) == 'Upload Image' && is_array($_FILES['preview_image'])){
	
	if(trim($_FILES['preview_image']['error'][0]) == 4)
		CvgCore::show_video_error(__('No preview images uploaded'));
	else 	
		CvgCore::upload_preview();
}

//Section to scan videos from folder and add to gallery
if(isset($_POST['scanVideos']) && trim($_POST['scanVideos'] == 'Scan Videos')) {
	if(empty($_POST['galleryId']))
		CvgCore::show_video_error(__('No Gallery selected'));
	else
		CvgCore::scan_upload_videos($_POST['galleryId']);
}

$gid = $_GET['gid'];

//Section if no gallery is selected.
if(!isset($gid)) { 
	?>
	<div class="wrap">
		<div class="icon32" id="icon-video"><br></div>
		<h2>Gallery Details</h2>
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
	$per_page = $options['max_vid_gallery'];

	$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
	if ( empty($pagenum) )
		$pagenum = 1;

	/*Start and end page settings for pagination.*/
	$start_page = ($pagenum - 1) * $per_page;
	$end_page = $start_page + $per_page;
	

	$total_num_pages = count(videoDB::get_gallery($gid));

	$total_value = ceil($total_num_pages / $per_page);
	$defaults = array(
					'base' => add_query_arg( 'paged', '%#%' ), // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
					'format' => '?paged=%#%', // ?page=%#% : %#% is replaced by the page number
					'total' => $total_value,
					'current' => $pagenum,
					'show_all' => false,
					'prev_next' => true,
					'prev_text' => __('&laquo;'),
					'next_text' => __('&raquo;'),
					'end_size' => 1,
					'mid_size' => 2,
					'type' => 'plain',
					'add_fragment' => ''
				);
	$page_links = paginate_links( $defaults );			

	$gallery = videoDB::find_gallery($gid);
	$title = __('Gallery: '. $gallery->name);
	
	if (!$gallery)  
		CvgCore::show_video_error(__('Gallery not found.', 'nggallery'));
	
	if ($gallery) { 
			// look for pagination	
			if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
				$_GET['paged'] = 1;
			
			$videolist = videoDB::get_gallery($gid, 'sortorder', 'asc', $per_page, $start_page);
			$act_author_user = get_userdata( (int) $gallery->author );
			
			?>
			<script type="text/javascript"> 

				jQuery(document).ready(function(){
					jQuery('#gallerydiv').addClass('closed');
					jQuery('#gallery_open').click(function(){
						if(jQuery('#gallerydiv').attr('class') == 'postbox closed') 
							jQuery('#gallerydiv').removeClass('closed');
						else	
							jQuery('#gallerydiv').addClass('closed');
					} );
				});	
	
				// Function is to check all
				function checkAll(form){
					for (i = 0, n = form.elements.length; i < n; i++) {
						if(form.elements[i].type == "checkbox") {
							if(form.elements[i].name == "doaction[]") {
								if(form.elements[i].checked == true)
									form.elements[i].checked = false;
								else
									form.elements[i].checked = true;
							}
						}
					}
				}

				// Function is to get checked numbers
				function getNumChecked(form){
					var num = 0;
					for (i = 0, n = form.elements.length; i < n; i++) {
						if(form.elements[i].type == "checkbox") {
							if(form.elements[i].name == "doaction[]")
								if(form.elements[i].checked == true)
									num++;
						}
					}
					return num;
				}
			
				// Function check for a the number of selected videos, sumbmit false when no one selected
				function checkSelected() {
			
					var numchecked = getNumChecked(document.getElementById('updatevideos'));
					 
					if(numchecked < 1) { 
						alert('<?php echo esc_js(__('No videos selected')); ?>');
						return false; 
					} 
					
					actionId = jQuery('#bulkaction').val();
			
					switch (actionId) {
						case "no_action":
							return true;	
							break;
						case "delete_videos":
							showDialog('delete_gallery', 120);
							return false;
							break;
					}
					
					return confirm('<?php echo sprintf(esc_js(__("You are about to start the bulk edit for %s images \n \n 'Cancel' to stop, 'OK' to proceed.",'nggallery')), "' + numchecked + '") ; ?>');
				}

				// Function is to get show popup
				function showDialog( windowId, height ) {
					var form = document.getElementById('updatevideos');
					var elementlist = "";
					for (i = 0, n = form.elements.length; i < n; i++) {
						if(form.elements[i].type == "checkbox") {
							if(form.elements[i].name == "doaction[]")
								if(form.elements[i].checked == true)
									if (elementlist == "")
										elementlist = form.elements[i].value
									else
										elementlist += "," + form.elements[i].value ;
						}
					}
					jQuery("#" + windowId + "_bulkaction").val(jQuery("#bulkaction").val());
					jQuery("#" + windowId + "_deletelist").val(elementlist);
					tb_show("", "#TB_inline?width=200&height=" + height + "&inlineId=" + windowId + "&modal=true", false);
				}

				//	Function to show popup for deleting video
				function showDialogDelete(id) {
					jQuery("#delete_video_single_value").val(id);
					tb_show("", "#TB_inline?width=200&height=100&inlineId=delete_video_single&modal=true", false);
				}

				//	Function to show popup for uploading preview image
				function showUploadImage(id) {
					jQuery('#preview_video_single').val(id);
					tb_show("", "#TB_inline?width=450&height=180&inlineId=upload_video_image&modal=true", false);
				}
				
			</script>
			
			<div class="wrap">
				<div class="icon32" id="icon-video"><br></div>
				<h2><?php echo esc_html( $title ); ?></h2>
				<div class="clear" style="min-height:10px;"></div>
				<form id="updategallery" method="POST" action="" accept-charset="utf-8">
					<input type="hidden" name="gid" value="<?php echo $_GET['gid'];?>" />
					<div id="poststuff">
						<div id="gallerydiv" class="postbox" >
							<h3 id="gallery_open"><?php _e('Gallery Details') ?><span style="float:right;font-size:10px;font-weight:normal;"><i>Click here to view details</i></span></h3>
							<div class="inside">
								<table class="form-table" >
									<tr>
										<td align="left"><?php _e('Title') ?>:</td>
										<td align="left"><input type="text" size="50" name="title" value="<?php echo $gallery->title; ?>" style="width:381px;" /></td>
									</tr>
									<tr>
										<td align="left"><?php _e('Description') ?>:</td> 
										<td align="left"><textarea  name="gallerydesc" cols="30" rows="3" style="width: 60%" ><?php echo $gallery->galdesc; ?></textarea></td>
									</tr>
								</table>
								<div class="submit" style="padding-left:5px;">
									<input type="submit" class="button-primary action" name="updatevideogallery" value="<?php _e("Save Changes"); ?>" />
								</div>
							</div>
						</div>
					</div> <!-- poststuff -->
				</form>	
				<form id="scanFolders" method="POST" action="<?php echo admin_url('admin.php?page=cvg-gallery-details&gid=' . $_GET['gid']); ?>" accept-charset="utf-8">
					<input type="hidden" name="galleryId" value="<?php echo $_GET['gid'] ?>" />
					<input type="hidden" name="scanVideos" value="<?php _e('Scan Videos'); ?>"  />
				</form>
				<form id="updatevideos" method="POST" action="<?php echo admin_url('admin.php?page=cvg-gallery-details&gid=' . $_GET['gid']); ?>" accept-charset="utf-8">
				
					<div class="tablenav">
						<div class="alignleft actions">
						<?php  if($videolist) { ?>
						<select id="bulkaction" name="bulkaction">
							<option value="no_action" ><?php _e("No action"); ?></option>
							<option value="delete_videos" ><?php _e("Delete Videos"); ?></option>
						</select>
						<input class="button-secondary" type="submit" name="showThickbox" value="<?php _e('Apply'); ?>" onclick="if ( !checkSelected() ) return false;" />
						<?php }?>
						<input class="button-secondary" id="scanVideos" type="button" name="scanVideos" value="<?php _e('Scan Gallery Folder'); ?>"  />
						<?php  if($videolist) { ?>
						<a class="button" style="padding:5px 10px;" href="<?php echo admin_url('admin.php?page=cvg-gallery-sort&gid=' . $_GET['gid']); ?>"><?php _e('Sort Gallery Videos'); ?></a>
						<input type="submit" name="updatevideos" class="button-primary action"  value="<?php _e('Save Changes');?>" />
						<?php }?>
						</div>
						<?php if ( $page_links ) { ?>
							<div class="tablenav-pages">
								<?php
									$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
														number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
														number_format_i18n( min( $pagenum * $per_page, $total_num_pages ) ),
														number_format_i18n( $total_num_pages ),
														$page_links
														);
									echo $page_links_text;
								?>
							</div>
						<?php }?>
					</div>	
					
					<table id="listvideos" class="widefat fixed" cellspacing="0" >
					<thead>
						<tr>
							<th scope="col" class="column-cb" style="width:3%;" >
								<input type="checkbox" onclick="checkAll(document.getElementById('updatevideos'));" name="checkall"/>
							</th>
							<th scope="col" style="width:3%;" ><?php _e('ID'); ?></th>
							<th scope="col" ><?php _e('Video Preview Image'); ?></th>
							<th scope="col" ><?php _e('Filename'); ?></th>
							<th scope="col" ><?php _e('Video Description'); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th scope="col" class="column-cb" style="width:3%;" >
								<input type="checkbox" onclick="checkAll(document.getElementById('updatevideos'));" name="checkall"/>
							</th>
							<th scope="col" style="width:3%;" ><?php _e('ID'); ?></th>
							<th scope="col" style="width:10%;"><?php _e('Video Preview Image'); ?></th>
							<th scope="col" style="width:20%;"><?php _e('Filename'); ?></th>
							<th scope="col" style="width:20%;"><?php _e('Video Description'); ?></th>
						</tr>
					</tfoot>
					<tbody style="width:100%;">
					<?php
							if($videolist) {
								
								foreach($videolist as $video) {
									
									$class = ( !isset($class) || $class == 'class="alternate"' ) ? '' : 'class="alternate"';
									$pid = $video->pid;
									$video_name = $video->filename;
									$video_thumb_filename = $video->thumb_filename;
									$video_thumb_url = site_url() . '/' .  $video->path . '/thumbs/' . $video_thumb_filename;
									$video_url =  site_url() . '/' . $video->path . '/' . $video_name;
									if($video->meta_data != '')
										$video_meta_data = unserialize($video->meta_data);
									else
										$video_meta_data = '';	
									
									$date = mysql2date(get_option('date_format'), $video->videodate);
									$time = mysql2date(get_option('time_format'), $video->videodate);		
									
									?>
									<tr id="gallery-<?php echo $pid ?>" <?php echo $class; ?> >
										<th scope="row" class="cb column-cb" style="width:3%;">
											<input name="doaction[]" type="checkbox" value="<?php echo $pid ?>" />
										</th>
										
										<th scope="row" style="width:3%;">
											<span><?php echo $pid; ?></span>
											<input type="hidden" name="pid[]" value="<?php echo $pid ?>" />
										</th>
										
										<td style="width:10%;">
											<?php
											echo CoolVideoGallery::CVGVideo_Parse('[cvg-video videoId='. $pid . ' /]');
											?>
										</td>
										<td style="width:20%;">
											<strong>
												
												<?php echo  $video->filename; ?>
											</strong>
											<br /><?php echo $date; ?>
											<br />
											<?php if($video_meta_data != '') {
													 _e('Video Duration: ');  echo $video_meta_data['videoDuration']; 
												   } 
										   ?>
											<p>
												<?php
												$actions = array();
												$actions['edit']  = '<a class="uploadeImage" onclick="showUploadImage(' . $pid . ');" href="#" >Edit Preview Image</a>'; 
												$actions['delete'] = '<a class="submitdelete" onclick="showDialogDelete(' . $pid . ');" href="#" >' . __('Delete') . '</a>'; 
												$action_count = count($actions);
												$i = 0;
												echo '<div class="row-actions">';
												foreach ( $actions as $action => $link ) {
													++$i;
													( $i == $action_count ) ? $sep = '' : $sep = ' | ';
													echo "<span class='$action'>$link$sep</span>";
												}
												echo '</div>';
												?>
											</p>
										</td>
										<td style="width:20%;">
											<textarea name="description[<?php echo $pid ?>]" style="width:95%; margin-top: 2px;" rows="2" ><?php echo stripslashes($video->description) ?></textarea>
										</td>
									</tr>
									<?php
								}
							} else {
								echo '<tr><td colspan="5" align="center"><strong>' . __('No entries found') . '</strong></td></tr>';
							}
							?>	
					</tbody>
				</table>	
			</form>
				
			<!-- #video_delete -->
			<div id="delete_gallery" style="display: none;" >
				<form id="form-delete-gallery" method="POST" accept-charset="utf-8" action="<?php echo admin_url('admin.php?page=cvg-gallery-details&gid=' . $_GET['gid']); ?>">
				<input type="hidden" id="delete_gallery_deletelist" name="TB_gallerylist" value="" />
				<input type="hidden" id="delete_gallery_bulkaction" name="TB_bulkaction" value="" />
				<input type="hidden" name="page" value="manage-galleries" />
				<table width="100%" border="0" cellspacing="3" cellpadding="3" >
					<tr valign="top">
						<td>
							<strong><?php _e('Delete Video(s)'); ?>:</strong> 
						</td>
					</tr>
				  	<tr align="center">
				    	<td colspan="2" class="submit">
				    		<input class="button-primary" type="submit" name="TB_DeleteVideo" value="<?php _e('OK'); ?>" />
				    		&nbsp;
				    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel'); ?>&nbsp;" onclick="tb_remove()"/>
				    	</td>
					</tr>
				</table>
				</form>
			</div>
			<!-- /#video_delete -->
			
			
			<!-- #video_delete_single -->
			<div id="delete_video_single" style="display: none;" >
				<form id="form-delete-video_single" method="POST" accept-charset="utf-8" action="<?php echo admin_url('admin.php?page=cvg-gallery-details&gid=' . $_GET['gid']); ?>">
					<input type="hidden" id="delete_video_single_value" name="TB_videosingle" value="" />
					<table width="100%" border="0" cellspacing="3" cellpadding="3" >
						<tr valign="top">
							<td><strong><?php _e('Delete Video?'); ?></strong></td>
						</tr>
					  	<tr align="center">
					    	<td colspan="2" class="submit">
					    		<input class="button-primary" type="submit" name="TB_DeleteSingleVideo" value="<?php _e('OK'); ?>" />
					    		&nbsp;
					    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel'); ?>&nbsp;" onclick="tb_remove()"/>
					    	</td>
						</tr>
					</table>
				</form>
			</div>
			<!-- /#video_delete_single -->
			
			<!-- #upload_video_image -->
			<div id="upload_video_image" style="display: none;" >
				<form name="uploadsinglepreview" id="uploadsingle_preview" method="POST" enctype="multipart/form-data" action="<?php echo admin_url('admin.php?page=cvg-gallery-details&gid=' . $_GET['gid']); ?>" accept-charset="utf-8" >
					<input type="hidden" id="preview_video_single" name="TB_previewimage_single" value="" />
					<table width="100%" border="0" cellspacing="3" cellpadding="3" >
						<tr valign="top">
							<td><strong><?php _e('Upload Preview Image'); ?></strong></td>
							<td><span id='spanButtonPlaceholder'></span><input type="file" name="preview_image[]" id="preview_image" size="35" class="preview_image"/>
							<br/>
							<i>( <?php _e('Allowed format: png') ;?> )</i></td>
						</tr>
					  	<tr align="right">
					    	<td colspan="2" class="submit">
					    		<input class="button-primary" type="submit" name="TB_UploadPreviewImage" id="TB_UploadPreviewImage" value="<?php _e('Upload Image'); ?>" />
					    		&nbsp;
					    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel'); ?>&nbsp;" onclick="previewUploadCancel();"/>
					    	</td>
						</tr>
					</table>
				</form>
			</div>
			<!-- /#upload_video_image -->
		</div>
		
	<?php } ?>	
<?php } ?>
<script type="text/javascript">
/*
 * Function to reintialise multiple file upload feature
 */
function previewUploadCancel() {
	jQuery('.preview_image').MultiFile('reset');
	tb_remove();
}

jQuery(document).ready(function(){
	jQuery('#scanVideos').click(function(){
		jQuery('#scanFolders').submit();
	});
});
</script>