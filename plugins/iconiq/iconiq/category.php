<?php get_header(); ?>
<!-- Content -->
<div class="wrapper">
	<div id="content">
		<div class="widecolumn alignleft">
		
		
		<?php 
		//echo $cat;
		$child_cats = (array) get_term_children($cat, 'category');
		//print_r($child_cats);
		query_posts(array('category__not_in' => array_merge( $child_cats),cat=>$cat,showposts=>1));
	 	while (have_posts()) : the_post(); ?>
       <h1 class="pagetitle"><?php the_title(); ?></h1>
          <div class="entry">
					<?php the_content(); ?>
				</div>
				
        <?php endwhile;
         wp_reset_query(); ?>
         
         <?php
		global $ancestor;
		$childcats = get_categories('child_of=' . $cat . '&hide_empty=0');
		foreach ($childcats as $childcat) 
		{
		  	if (cat_is_ancestor_of($ancestor, $childcat->cat_ID) == false)
		  	{
		  		echo '<div class="specification">';
		  	
			  	if (function_exists('get_cat_icon'))
			     {
			         get_cat_icon('cat='.$childcat->cat_ID.'&fit_width=&fit_height=&small=true&class=alignleft');
			     }
		         echo "<div><h3 class='h3_title_margin'><a href='".get_category_link($childcat->cat_ID)."'>".$childcat->cat_name."</a></h3>";
		         echo " <div class='inr_content'><p>";
		         echo $childcat->category_description;
		         echo "<a href='".get_category_link($childcat->cat_ID)."' class='readmore'>read more</a><p></div>";
		         echo '</div><div class="clear"></div></div>';
		    	$ancestor = $childcat->cat_ID;
	  		}
	 }
?> 
					
	</div>
		<?php get_sidebar(); ?>
		<div class="clear"></div>
	</div>
</div>
<?php get_footer(); 


function excluded_child_cats($id) {
	
	return $result . ' ';
}?>

