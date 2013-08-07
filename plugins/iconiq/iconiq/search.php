<?php get_header(); ?>
<!-- Content -->
<div class="wrapper">
	<div id="content">
		<div class="widecolumn alignleft">

	<?php if (have_posts()) : ?>

		<h1 class="pagetitle">Search Results</h1>

		<?php while (have_posts()) : the_post(); ?>

			<div class="mar_bottom">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>			
				<div class="entry">
					<?php the_excerpt(); ?>
				</div>
				<div class="clear"></div>				
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h1 class="pagetitle">No posts found. Try a different search?</h1>
		<?php get_search_form(); ?>

	<?php endif; ?>

	</div>
		<?php get_sidebar(); ?>
		<div class="clear"></div>
	</div>
</div>
<?php get_footer(); ?>
