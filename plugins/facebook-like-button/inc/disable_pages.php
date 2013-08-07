<?php

class LikeDisablePage
{
	
	public function DisablePages()
	{
		
		if(function_exists('add_meta_box'))
		{
			add_meta_box('fb-like', 'Disable Facebook Like Button', array('LikeDisablePage', 'LikeBoxLayout'), 'page', 'side', 'default', null );
			
			}else
			
			{
				    add_action('dbx_page_advanced', array('LikeDisablePage', 'LikeBoxLayout'));
				
				}
		
		}
		
	public function LikeBoxLayout()
	{

		$Disable_Status = get_option('disable_like_status_'.$_GET['post']);
		$Check_Status = ($Disable_Status == true) ? 'CHECKED' : '';
		echo '<table><tr><td>Check To Disable:</td><td> <input type="checkbox" name="disable_like_button" id="disable_like_check" '.$Check_Status.'/></td></tr></table>';
		
		}
	
	public function DoLikeDisable($page_id)
	{
		
		$Check_Status = $_POST['disable_like_button'];
		
		add_option('disable_like_status_'.$page_id, '');
		
		if($Check_Status == 'on')
		{
			update_option('disable_like_status_'.$page_id, true);
			
			}
		
		else
		{
			update_option('disable_like_status_'.$page_id, false);
			
			}
		
		}
	
	}
	
	
	
add_action('admin_menu', array('LikeDisablePage', 'DisablePages'));
add_action('pre_post_update',  array('LikeDisablePage', 'DoLikeDisable'));


?>