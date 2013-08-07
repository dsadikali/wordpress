<?php
/*
Template Name: Links
*/
?>

<?php get_header(); ?>
<!-- Content -->
<div class="wrapper">
	<div id="content">
		<div class="widecolumn alignleft">

<h2>Links:</h2>
<ul>
<?php wp_list_bookmarks(); ?>
</ul>

</div>
		<?php get_sidebar(); ?>
		<div class="clear"></div>
	</div>
</div>
<?php get_footer(); ?>

