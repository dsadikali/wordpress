<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>
</head>
<body>
<div align="center">
	<div class="wrapper">
<!-- Header -->
		<div id="header">
<!-- Logo -->
			<div class="alignleft">
<div id="logo"><a href="<?php echo get_option('home'); ?>">LOGO</a></div>
			</div>
			<div class="alignright top_links">
<!--<div class="client_login alignleft"><a href="#">Client Login</a></div>-->
<div class="blog alignleft"><a href="<?php echo get_option('home'); ?>/category/blog">Blog</a></div>
			</div>
		</div>
<!-- Navigation -->
		<div id="nav">
<ul>
	<li class="<?php if (is_category('e-commerce-development')) echo ' current_page_item'; ?>"><a href="<?php echo get_option('home'); ?>/category/sercies-at-iconiq/e-commerce-development">E-commerce</a> 
		<ul>			
			<?php $id = get_cat_ID('Ecommerce Development') ?>
			<?php listcatNoTitle('hide_empty=0&title_li=&child_of='.$id.''); ?>
		</ul>
	</li>
	<li class="<?php if (is_category('Mobile Application Development')) echo ' current_page_item'; ?>"><a href="<?php echo get_option('home'); ?>/category/sercies-at-iconiq/mobile-application-development">Mobile</a>
		<ul>
			<?php $id = get_cat_ID('Mobile Application Development') ?>
			<?php listcatNoTitle('hide_empty=0&orderby=ID&order=DESC&title_li=&child_of='.$id.''); ?>
			
			<?php
				//$id2 = get_cat_ID('Mobile Application Development');
			  //$categories2 = get_categories('hide_empty=0&child_of='.$id2.'');
			  //foreach ($categories2 as $cat2) {
			  //echo '<li><a href="'.get_category_link( $cat2->cat_ID ).'/"><span>'.$cat2->cat_name.'</span></a></li>';
			 // }
			?>
		</ul>
	</li>
	<li class="<?php if (is_category('web-design-development')) echo ' current_page_item'; ?>"><a href="<?php echo get_option('home'); ?>/category/sercies-at-iconiq/web-design-development">Websites</a></li> 
	<li class="<?php if (is_category('design')) echo ' current_page_item'; ?>"><a href="<?php echo get_option('home'); ?>/category/sercies-at-iconiq/design">Design</a>
	<ul>
		<?php $id = get_cat_ID('design') ?>
		<?php listcatNoTitle('hide_empty=0&title_li=&child_of='.$id.''); ?>	
	</ul>	
	</li>
	<li class="<?php if (is_page('portfolio')) echo ' current_page_item'; ?>"><a href="<?php echo get_option('home'); ?>/portfolio.html">Portfolio</a>
	<ul>
	<?php $outclient_link = get_option('home')."/portfolio.html";?>
	<?php 
	if(function_exists('the_ourclients_navigation_ourclients')) { the_ourclients_navigation_ourclients($outclient_link); }?>
	</ul>
	</li>
	
	<li class="<?php if (is_page('contact-us')) echo ' current_page_item'; ?>"><a href="<?php echo get_option('home'); ?>/contact.html">Contact</a></li>
</ul>
		</div>
	</div>
<!-- Banner -->
<div class="banner_bg">	
		<!--<div><img src="<?php //bloginfo('template_directory'); ?>/images/iq-banner.jpg" alt="" title="IQ PROTOTYPE BANNER" /></div>-->
		<?php if(is_category())
			{ 
				$valid =banner_cat($cat);		 
			
			}else
			{
					if(is_front_page())
					{
						?><li id="wp-banners">
						<?php if( function_exists('display_wp_banners')) display_wp_banners(); ?>
						</li><?php 
					}	else
					{
						$custom_field_keys = get_post_custom_keys($post->ID);
						//echo "<pre>";print_r($custom_field_keys);
						for($i=0;$i<count($custom_field_keys);$i++)
			 		 	{
				    		$valuet = trim($custom_field_keys[$i]);
				    		if ( '_' == $valuet{0} )
				    		continue;
				    		$banner_image = get_post_meta($post->ID, $custom_field_keys[$i], false);
						    $show_image = $banner_image[0];
						    if ($show_image != '') { ?>
						    <div><img src="<?php echo $show_image; ?>" /></div>
							<?php 	} 
			 		 	} 
			} 
		}?>
</div>
		
		
		
<?php function banner_cat($cat)
			{
			
				$this_category = get_category($cat);
				$parent=$this_category->category_parent;
				if($parent==6||$parent==0)
				{	
				$this_category1 = get_category($cat);
				 $id=$this_category1->cat_ID;?><div><img src="<?php ciii_category_images( 'category_ids='.$id ); ?>" alt="" title="IQ PROTOTYPE BANNER" /></div><?php 
				return $id;
				 
				}else
				{
					banner_cat($parent);
				}
			}	
	
	
	function listcatNoTitle($args) 
	{
			if ($args) 
			{
				$args .= '&echo=0';
			} else 
			{
				$args = 'echo=0';
			}
			
				$cat = wp_list_categories($args);
				$cat = preg_replace('/title=\"(.*?)\"/', '', $cat);
				echo $cat;
	}?>