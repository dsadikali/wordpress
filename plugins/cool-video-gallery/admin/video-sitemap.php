<?php 
/**
 * Section to display gallery overview
 * @author Praveen Rajan
 */
$title = __('CVG Google XML Sitemap Generator');
?>

<style type="text/css">
#dashboard-widgets a {
	text-decoration:none;
}
</style>
<div class="wrap">
	<div class="icon32" id="icon-video"><br></div>
	<h2><?php echo esc_html( $title ); ?></h2>
	
	<?php 
		//Section to save gallery settings
		if(isset($_POST['generatexml'])) {
			
			CvgCore::xml_sitemap();
		}
	?>

	<div id="dashboard-widgets-wrap">
	
	<form method="post" action="<?php echo admin_url('admin.php?page=cvg-video-sitemap'); ?>">
		<div id="dashboard-widgets" class="metabox-holder">
			<div style="width: 100%;" class="postbox-container">
				<div class="meta-box-sortables ui-sortable" id="left-sortables"  style="min-height:0px;">
					
					<div class="postbox" id="server_settings" >	
						<div class="inside" style="margin:10px;">
							Generate your Google XML Video Sitemap here. 
						</div>
						
					
					<div class="submit" style="padding-left:5px;">
						<input type="submit" class="button-primary action" name="generatexml" value="<?php _e("Generate Video Sitemap"); ?>" />
					</div>
					</div>
				</div>	
			</div>
			
		</div>
	</form>	
			
	</div>
</div>
