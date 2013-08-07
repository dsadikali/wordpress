<?php get_header(); ?>
<!-- Content -->
<div class="wrapper">
	<div id="content">
		<div class="widecolumn alignleft">
		<?php if (have_posts()) : ?> 	 
		<?php while (have_posts()) : the_post(); ?>
		<div class="mar_bottom">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<br />
<small><?php the_time('F jS, Y') ?> by <?php the_author_posts_link() ?> </small>
				<div class="entry">
					<?php the_excerpt() ?>
					<span><a href="<?php the_permalink() ?>" class="readmore" rel="bookmark" >read more</a></span>
				</div><div class="clear"></div>
<small>
			<?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in Category <?php the_category(', ') ?> | 
			<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
		</small>
				<br />
				
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
	<?php else :

		
		get_search_form();

	endif;
?>

	</div>
		<?php get_sidebar(); ?>
		<div class="clear"></div>
	</div>
</div>
<?php get_footer(); ?>

