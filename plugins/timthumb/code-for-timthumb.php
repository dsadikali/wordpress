<?php
if ($images = get_children(array(
'post_type' => 'attachment',
'numberposts' => 1,
'post_status' => null,
'post_parent' => $post->ID,)))

foreach($images as $image) {
$attachment=wp_get_attachment_image_src($image->ID, $size);
?><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $attachment[0]; ?>" alt="<?php the_title(); ?>" /></a>
<?php } else { ?>
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img src="<?php bloginfo('template_directory'); ?>/scripts/preview.jpg" alt=""/></a>
<?php } ?>
