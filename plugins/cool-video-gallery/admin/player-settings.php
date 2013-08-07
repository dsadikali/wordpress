<?php 
/**
 * Section to display video player settings
 * @author Praveen Rajan
 */
$title = __('Video Player Settings');
$plugin_path = WP_CONTENT_DIR.'/plugins/'.plugin_basename( dirname(dirname(__FILE__)) );
$plugin_url = WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(dirname(__FILE__)) );

require_once( $plugin_path . '/cvg-player/core-functions.php');

if(isset($_POST['update_CVGSettings'])){
	$options_player = $_POST['options_player'];
	update_option('cvg_player_settings', $options_player);
	echo '<div class="updated"><p><strong>' . __('Options saved.') . '</strong></p></div>';
}

$options_player = get_option('cvg_player_settings');
?>

<div class="wrap">
	<div class="icon32" id="icon-video"><br></div>
	<h2><?php echo esc_html( $title ); ?></h2>
	
	<form method="post" action="<?php echo admin_url('admin.php?page=cvg-player-settings'); ?>">
		<div class="clear" style="min-height:10px;"></div>
		
		<div style="float:left;width:25%;">
			<h4>Width of video player:</h4>
		</div>
		<div style="float:left;padding-top:6px;">	
			<textarea name="options_player[cvgplayer_width]" COLS=10 ROWS=1><?php echo $options_player['cvgplayer_width']?></textarea>
		</div>
		
		<div class="clear" ></div>
		<div style="float:left;width:25%;">	
			<h4>Height of video player:</h4>
		</div>
		<div style="float:left;padding-top:6px;">
			<textarea name="options_player[cvgplayer_height]" COLS=10 ROWS=1><?php echo $options_player['cvgplayer_height']?></textarea>
		</div>
		<div class="clear"></div>
			
		<div style="float:left;width:25%;">	
			<h4>Choose skin for video player:</h4>	
		</div>
		
		<?php 
		$skins = CVGPlayer::get_dir_skin( dirname(dirname((__FILE__))) . "/cvg-player/skins/", ".swf", "", false);
		
		$option = '';
		foreach ($skins as $value){
			$option .= '<option value="' . $value . '" '; 
			if ($options_player['cvgplayer_skin'] ==  $value ){
				$option .=  'SELECTED >' . $value .'</option>';
			}else{
				$option .=  '>' . $value .'</option>';
			}
		}
		if($options_player['cvgplayer_skin'] == ''){
			$option .=  '<option value="" SELECTED >No Skin</option>';
		}		
		if(!is_array($skins)){
			$option =  '<option>No Skin</option>';
		}
		?>
		<div style="float:left;padding-top:15px;">		
			<select name="options_player[cvgplayer_skin]" style="width:120px;">
				<?php echo $option;?>				
			</select>
		</div>	
		<div class="clear"></div>

		<div style="float:left;width:25%;">	
			<h4>Default Volume:</h4>
		</div>			
		<div style="float:left;padding-top:6px;">		
			<textarea name="options_player[cvgplayer_volume]" COLS=10 ROWS=1><?php echo $options_player['cvgplayer_volume']?></textarea>
		</div>
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Allow Fullscreen</h4>
		</div>	
		<div style="float:left;padding-top:6px;">	
			<p>
				<label for="fullscreen_true"><input type="radio" id="fullscreen_true" name="options_player[cvgplayer_fullscreen]" value="1" <?php if ($options_player['cvgplayer_fullscreen']) { _e('checked="checked"'); }?>/> True</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="fullscreen_false"><input type="radio" id="fullscreen_false" name="options_player[cvgplayer_fullscreen]" value="0" <?php if (!$options_player['cvgplayer_fullscreen']) { _e('checked="checked"'); }?>/> False</label>
			</p>
		</div>
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Autoplay:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">
			<p>
				<label for="autoplay_true"><input type="radio" id="autoplay_true" name="options_player[cvgplayer_autoplay]" value="1" <?php if ($options_player['cvgplayer_autoplay']) { _e('checked="checked"'); }?>/> True</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="autoplay_false"><input type="radio" id="autoplay_false" name="options_player[cvgplayer_autoplay]" value="0" <?php if (!$options_player['cvgplayer_autoplay']) { _e('checked="checked"'); }?>/> False</label>
			</p>
		</div>	
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Mute Volume:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">
			<p>
				<label for="mute_true"><input type="radio" id="mute_true" name="options_player[cvgplayer_mute]" value="1" <?php if ($options_player['cvgplayer_mute']) { _e('checked="checked"'); }?>/> True</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="mute_false"><input type="radio" id="mute_false" name="options_player[cvgplayer_mute]" value="0" <?php if (!$options_player['cvgplayer_mute']) { _e('checked="checked"'); }?>/> False</label>
			</p>
		</div>	
		<div class="clear"></div>
		
			<div style="float:left;width:25%;">	
			<h4>Control bar Location:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">	
			<p>
				<label for="controlbar_bottom"><input type="radio" id="controlbar_bottom" name="options_player[cvgplayer_controlbar]" value="bottom" <?php if ($options_player['cvgplayer_controlbar'] == "bottom") { _e('checked="checked"'); }?>/> Bottom</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="controlbar_top"><input type="radio" id="controlbar_top" name="options_player[cvgplayer_controlbar]" value="top" <?php if ($options_player['cvgplayer_controlbar'] == "top") { _e('checked="checked"'); }?>/>Top</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="controlbar_over"><input type="radio" id="controlbar_over" name="options_player[cvgplayer_controlbar]" value="over" <?php if ($options_player['cvgplayer_controlbar'] == "over") { _e('checked="checked"'); }?>/>Over</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="controlbar_none"><input type="radio" id="controlbar_none" name="options_player[cvgplayer_controlbar]" value="none" <?php if ($options_player['cvgplayer_controlbar'] == "none") { _e('checked="checked"'); }?>/>None</label>&nbsp;&nbsp;&nbsp;&nbsp;
			</p>
		</div>	
		<div class="clear"></div>
		
		<div style="float:left;width:25%;">	
			<h4>Video Display:</h4>
		</div>	
		<div style="float:left;padding-top:6px;">	
			<p>
				<label for="display_none"><input type="radio" id="display_none" name="options_player[cvgplayer_stretching]" value="none" <?php if ($options_player['cvgplayer_stretching'] == "none") { _e('checked="checked"'); }?>/> None</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="display_exactfit"><input type="radio" id="display_exactfit" name="options_player[cvgplayer_stretching]" value="exactfit" <?php if ($options_player['cvgplayer_stretching'] == "exactfit") { _e('checked="checked"'); }?>/> Exact fit</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="display_uniform"><input type="radio" id="display_uniform" name="options_player[cvgplayer_stretching]" value="uniform" <?php if ($options_player['cvgplayer_stretching'] == "uniform") { _e('checked="checked"'); }?>/> Uniform</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="display_fill"><input type="radio" id="display_fill" name="options_player[cvgplayer_stretching]" value="fill" <?php if ($options_player['cvgplayer_stretching'] == "fill") { _e('checked="checked"'); }?>/> Fill</label>&nbsp;&nbsp;&nbsp;&nbsp;
			</p>
		</div>	
		<div class="clear"></div>
		
		<div class="submit">
			<input type="submit" name="update_CVGSettings" value="Save Player Settings" />
		</div>
	</form>
	
 </div>