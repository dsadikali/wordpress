<?php 
/*(1)Posts by category slug*/
	query_posts( array ( 'category_name' => 'my-category-slug') );

/*(2)Posts by category id*/
	query_posts( 'cat=1' );
	
/*(3)Exclude category by id*/
	query_posts( 'cat=-1' );

/*(4)Posts by single tag*/
	query_posts( 'tag=apples' );

/*(5)Posts by multiple tag*/
	query_posts( 'tag=apples+banana' );
	
/*(6)Post by id*/
	query_posts( 'p=5' );	
	
/*(7)Posts per page*/
	query_posts( array ( 'posts_per_page' => -1 ) );

/*(8)Posts order by date*/
	query_posts('orderby=date');
	/*options
	'orderby' => 'title'
	*/

/*(9)Posts by ascending order*/
	query_posts( array ( 'order' => 'ASC') );
	/*options
	'order' => 'DESC'
	*/
	
/*(10)Posts by custom post type*/
	query_posts( array ( 'post_type' => 'product') );
?>