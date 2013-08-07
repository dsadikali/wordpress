<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<ul class="category_images_ii">
<?php foreach( $categories AS & $category ) { ?>
	<li class="category_image"><?php if ( $link_images ) : ?><a href="<?php echo esc_attr( get_category_link( $category[ 'id' ] ) ); ?>"><?php endif; ?><img src="<?php echo $category[ 'thumbnail' ]; ?>" alt="<?php echo $category[ 'name' ]; ?>" /><?php if ( $link_images ) : ?></a><?php endif; ?></li>
<?php } ?>
</ul>