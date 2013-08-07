<?php 
/**
 * Section to display gallery settings.
 * @author Praveen Rajan
 */
$title = __('Video Gallery Settings'); 
?>
<div class="wrap">
	<div class="icon32" id="icon-video"><br></div>
	<h2><?php echo esc_html( $title ); ?></h2>
	<?php 
	//Section to save gallery settings
	if(isset($_POST['update_Settings']) && ($_POST['update_Settings'] == 'Save Gallery Settings')) {
		
		$options['max_cvg_gallery'] = $_POST['max_cvg_gallery'];
		$options['max_vid_gallery'] = $_POST['max_vid_gallery'];
		$options['cvg_preview_height'] = $_POST['cvg_preview_height'];
		$options['cvg_preview_width'] = $_POST['cvg_preview_width'];
		$options['cvg_preview_quality'] = $_POST['cvg_preview_quality'];
		$options['cvg_zc'] = $_POST['cvg_zc'];
		$options['cvg_slideshow']= $_POST['cvg_slideshow'];
		
		update_option('cvg_settings', $options);
		
		CvgCore::show_video_message(__('Gallery settings successfully updated.'));
	}
	$options = get_option('cvg_settings');
	?>

	<form method="post" action="<?php echo admin_url('admin.php?page=cvg-gallery-settings'); ?>">
			
			<div style="float:left;width:25%;">
				<h4>Max no. of Galleries listed per page:</h4>
			</div>
			<div style="float:left;padding-top:6px;">	
				<textarea name="max_cvg_gallery" COLS=10 ROWS=1><?php echo $options['max_cvg_gallery']?></textarea>
			</div>
			
			<div class="clear" ></div>
			<div style="float:left;width:25%;">	
				<h4>Max no. of Videos listed per page:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="max_vid_gallery" COLS=10 ROWS=1><?php echo $options['max_vid_gallery']?></textarea>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:25%;">	
				<h4>Width of preview image:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="cvg_preview_width" COLS=10 ROWS=1><?php echo $options['cvg_preview_width']?></textarea>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:25%;">	
				<h4>Height of preview image:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="cvg_preview_height" COLS=10 ROWS=1><?php echo $options['cvg_preview_height']?></textarea>
			</div>
			<div class="clear"></div>	
			
			<div style="float:left;width:25%;">	
				<h4>Quality of preview image:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="cvg_preview_quality" COLS=10 ROWS=1><?php echo $options['cvg_preview_quality']?></textarea>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:25%;">	
				<h4>Slideshow Speed:</h4>
			</div>
			<div style="float:left;padding-top:6px;">
				<textarea name="cvg_slideshow" COLS=10 ROWS=1><?php echo $options['cvg_slideshow']?></textarea>
			</div>
			<div class="clear"></div>
			
			<div style="float:left;width:25%;">	
				<h4>Enable Zoom-Crop:</h4>
			</div>
			<div style="float:left;padding-top:16px;">
				<input type="radio" id="zc_yes" value="1" name="cvg_zc" <?php echo ($options['cvg_zc'] == 1) ? 'checked' : '';?> /><label for="zc_yes">Yes</label>
				<input type="radio" id="zc_no" value="0" name="cvg_zc" <?php echo ($options['cvg_zc'] == 0) ? 'checked' : '';?> /><label for="zc_no">No</label>
			</div>
			<div class="clear"></div>	
			
			<div class="submit">
				<input type="submit" name="update_Settings" value="Save Gallery Settings" />
			</div>
	</form>		
</div>