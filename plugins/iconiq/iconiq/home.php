<?php get_header(); ?>
<!-- Content -->
<div class="wrapper">
	<div id="content">
		<div class="widecolumn alignleft">

<?php if (have_posts()) : ?>

	
<!-- Welcome Post -->
	
  <?php query_posts('category_name=welcome-post&showposts=1'); ?>
  <?php while (have_posts()) : the_post(); ?>
 	<h1><span>Welcome to</span> ICONiQ Interactive</h1>

  	<div class="entry"><?php the_content(); ?></div>
  <?php endwhile; ?>
	<?php wp_reset_query(); ?>
	
<!-- Our Specializations are in -->
<h1><span>Our </span>Specializations are in</h1>	
<div class="specification">
<!-- Ecommerce Development -->
	<div class="alignleft width">
<?php query_posts('category_name=e-commerce-development');
if (function_exists('get_cat_icon'))
   {
   	 ?> 
<div class="alignleft"> <?php get_cat_icon('cat=10&fit_width=77&fit_height=89&small=true&calss=alignleft'); ?></div><?php }?>
<div class="alignleft entry margin_top">
	<h2>
		<a href="<?php echo get_option('home'); ?>/category/sercies-at-iconiq/e-commerce-development"><?php
			if (is_category( )) {
			$cat = get_query_var('cat');
			$yourcat = get_category ($cat);
			echo ''. $yourcat->name .'';
			}
		?></a>
	</h2>
	<p>
		<?php $id = get_cat_ID('Ecommerce Development'); if($id!=''){ ?> 
		<?php $desc = category_description($id); ?>
		<?php echo $desc ?><?php } ?>
	</p>
</div>
<?php wp_reset_query(); ?>
<div class="clear"></div>
	</div>
<!-- Mobile Application Development -->
	<div class="alignleft width">
<?php query_posts('category_name=consulting');
 ?>
 <div class="alignleft"><img src="<?php bloginfo('template_directory'); ?>/images/iq-consulting-icn.gif" alt="" title="Consulting Banner" width=77 height="89"/></div>
<div class="alignleft entry margin_top">
	<h2>
		<a href="<?php echo get_option('home'); ?>/category/sercies-at-iconiq/consulting"><?php
			if (is_category( )) {
			$cat = get_query_var('cat');
			$yourcat = get_category ($cat);
			echo ''. $yourcat->name .'';
			}
		?></a>
	</h2>
	<p>
		<?php $id = get_cat_ID('consulting'); if($id!=''){ ?> 
		<?php $desc = category_description($id); ?>
		<?php echo $desc ?><?php } ?>
	</p>
</div>
<?php wp_reset_query(); ?>
<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<div class="specification">
<!-- Flash / Flex Development -->
	<div class="alignleft width">
<?php query_posts('category_name=mobile-application-development'); ?>
<?php if (function_exists('get_cat_icon')){?>
<div class="alignleft"><?php  get_cat_icon('cat=13&fit_width=77&fit_height=89&small=true&class=alignleft'); ?></div><?php  }?>
<div class="alignleft entry margin_top">
	<h2>
		<a href="<?php  get_option('home'); ?>/category/sercies-at-iconiq/mobile-application-development"><?php
			if (is_category( )) {
			$cat = get_query_var('cat');
			$yourcat = get_category ($cat);
			echo $yourcat->name;
			}
		?></a>
	</h2>
	<p>
		<?php $id = get_cat_ID('Mobile Application Development'); if($id!=''){ ?> 
		<?php $desc = category_description($id); ?>
		<?php echo $desc ?><?php } ?>
	</p>
</div>
<?php wp_reset_query(); ?>
<div class="clear"></div>
	</div>
	<!-- Web Design & Development -->
	<div class="alignleft width">
<?php query_posts('category_name=web-and-graphic-design'); ?>
 <?php 
 if (function_exists('get_cat_icon'))
   {?>
 <div class="alignleft"><?php get_cat_icon('cat=8&fit_width=77&fit_height=89&small=true&class=alignleft'); ?></div><?php } ?>
<div class="alignleft entry margin_top">
	<h2>
		<a href="<?php echo get_option('home'); ?>/category/sercies-at-iconiq/web-and-graphic-design">
		<?php if (is_category( )) {
			$cat = get_query_var('cat');
			$yourcat = get_category ($cat);
			echo $yourcat->name;
			
			}
		?></a>
	</h2>
	<p>
		<?php $id = $yourcat->cat_ID; if($id!=''){ ?> 
		<?php $desc = category_description($yourcat->cat_ID);  ?>
		<?php echo $desc ?><?php } ?>
	</p>
</div>
<?php wp_reset_query(); ?>
<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>

<?php else : ?>

	<h2 class="center">Not Found</h2>
	<p class="center">Sorry, but you are looking for something that isn't here.</p>
	<?php get_search_form(); ?>

<?php endif; ?>

</div>
		<?php get_sidebar(); ?>
		<div class="clear"></div>
	</div>
</div>
<?php //wp_list_categories();?>
<?php get_footer(); ?>
