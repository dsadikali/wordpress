<!-- Footer -->
	<div id="footer">
		<div class="wrapper">
<!-- About ICONIQ -->
<div class="ftr_link alignleft">
	<ul>
		<li><h2>About ICONIQ</h2></li>
		<li><a href="<?php echo get_option('home'); ?>/about-iconiq">About Iconiq</a></li>
		<li><a href="<?php echo get_option('home'); ?>/contact.html">Contact Us</a></li>
	</ul>
	<!--<ul><li><h2><a href="<?php echo get_option('home'); ?>/testimonials">testimonials</a></h2></li></ul>-->
</div>

<!-- Sercies at ICONIQ -->
<div class="ftr_link alignleft">
	<ul>
		<li><h2>Services at ICONIQ</h2></li>
		<li>
			<?php   $id = get_cat_ID('Services at ICONIQ') ?>
			<?php
			wp_list_categories('depth=1&use_desc_for_title=0&title_li=&hide_empty=0&child_of='.$id.'');
		 ?>
		</li>
	</ul>
</div>
<!-- Quick Links -->
<div class="ftr_link alignleft">
	<ul>
		<li><h2>General</h2></li>
		<li><a href="<?php echo get_option('home'); ?>/sitemap">Site map</a></li>
		<li><a href="<?php echo get_option('home'); ?>/testimonials">Testimonials</a></li>
		<li><a href="#">Blog</a></li>
		
		
		<!--<li><h2>Quick Links</h2></li>-->
		<?php //wp_list_bookmarks('depth=1&title_li'); ?>		
	</ul>
</div>
<!-- LOGO -->
<div class="ftr_logo alignleft"><a href="<?php echo get_option('home'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/iq-ftr-logo.gif" alt="" title="" /></a></div>
<div class="clear"></div>
		</div>
	</div>
<!-- Copyrights -->
	<div class="copyright">
		<div class="wrapper">
<div class="alignleft"><a href="<?php echo get_option('home'); ?>/terms-of-use">Terms of Use</a>  |  <a href="<?php echo get_option('home'); ?>/privacy-policy">Privacy Policy</a></div>
<div class="alignright"> &copy; <?php the_time('Y') ?> <a href="http://www.iconiqinteractive.com/"><?php bloginfo('name'); ?></a>  All Rights Reserved.</div>
<div class="clear"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-12451890-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
<?php

/// function listcatNoTitle($args) {
//			if ($args) {
//				$args .= '&echo=0';
//			} else {
//			$args = 'echo=0';
//			}
//			print_r($args);exit;
//			$cat = wp_list_categories($args);
//			$cat = preg_replace('/title=\"(.*?)\"/', '', $cat);
//			echo $cat;
//			}		?>