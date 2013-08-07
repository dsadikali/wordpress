<section class="widecolumn alignleft">

  <?php query_posts(array('posts_per_page'=>1)); ?>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
  $latest= get_the_ID(); 
  ?>
  
  <article class="toppost-block">
				<div class="toppost-innerblock cf">
					<h2 class="post-heading"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
      <?php the_title(); ?></a></h2>
					<div class="post-cat cf">
						<span>Kategorie: </span><span class="first"><?php the_category(', '); ?></span>&frasl;<?php comments_popup_link( __( 'Kommentare <em>(0)</em>', 'twentyten' ), __( 'Kommentare <em>(1)</em>', 'twentyten' ), __( 'Kommentare <em>(%)</em>', 'twentyten' ) ); ?>
                    </div>
					<div class="post-content cf">
						 <?php if(has_post_thumbnail()): ?>
                         	<a href="<?php the_permalink(); ?>" title="<?php the_title() ?>" class="post-fullimg"><?php the_post_thumbnail('full') ?></a>
						  <?php endif; ?>
						<?php the_excerpt(); ?>
					</div>
					<span class="post-date"><strong><?php  the_time('d')  ?></strong><span><?php  the_time('F')  ?></span></span>
				</div>
			</article>
  
  <?php endwhile; else: ?>
  <p>Sorry, no posts matched your criteria.</p>
  <?php endif; wp_reset_query();?>

<!-- #content -->

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
   if(get_the_ID()!=$latest){
  ?>
  <article class="toppost-block">
				<div class="toppost-innerblock cf">
					<div class="post-content cf">
						<?php if(has_post_thumbnail()): ?>
	   						 <a class="post-thumbimg alignleft" href="<?php the_permalink(); ?>" title="<?php the_title() ?>"><?php the_post_thumbnail('thumbnail') ?> </a>
    					<?php endif; ?>
						<div class="post-details alignleft">
							<h2 class="post-heading"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title() ?></a></h2>
							<div class="post-cat cf">
								<span>Kategorie: </span><span class="first"><?php the_category(', '); ?></span>&frasl;<?php comments_popup_link( __( 'Kommentare <em>(0)</em>', 'twentyten' ), __( 'Kommentare <em>(1)</em>', 'twentyten' ), __( 'Kommentare <em>(%)</em>', 'twentyten' ) ); ?></a>
							</div>
                            <?php the_excerpt(); ?>
                            
						</div>
					</div>
					<span class="post-date"><strong><?php the_time('d') ?></strong><span><?php the_time('F') ?></span></span>
				</div>
			</article>
  
  
  <!-- closes the first div box -->
  
  <?php } endwhile; else: ?>
  <p>Sorry, no posts matched your criteria.</p>
  <?php endif;    wp_pagenavi(); ?>
 
</section>