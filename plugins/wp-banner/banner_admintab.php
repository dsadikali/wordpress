<?php
   define("WP_BANNER","/wp-banner/banner_admin.php");
   define("WP_BANNER_PATH",WP_PLUGIN_URL .'/wp-banner');
   
   $query = "?";
   if ($_POST['banner_position'] == "divTag")
   {
      $_POST['banner_position'] = "divTag,".$_POST['banner_layoutpos'].",".$_POST['banner_childpos'].",".$_POST['banner_leftpos'];
      unset($_POST['banner_layoutpos'],$_POST['banner_childpos'],$_POST['banner_leftpos']);
      
   }
  
   foreach ($_POST as $key => $value)
   {
      if ($key == "banner_startdate" || $key == "banner_enddate")
      { 
	 if(! $value = strtotime($value)){ $value = 0; }
      }
 
      if ($value) { $query .= "$key=" . urlencode($value) . "&"; }
   }
   
//   echo "$query";

?>

<div class="wrap">
<div id="bannertabs">
     <ul>
         <li><a href="<?php echo WP_PLUGIN_URL .WP_BANNER . $query; ?>"><span>Banner Admin</span></a></li>
         <li><a href="#bannerstat"><span>Statistic</span></a></li>
     </ul>
     
     <div id="bannerstat" style="height: 538px; background-repeat:no-repeat;background-image: url(<?php echo WP_BANNER_PATH . "/images/graph.png" ?>)">
      <h3> planned for future release</h3>
      <p>You can support the development with a donation</p>
      <p><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	 <input type="hidden" name="cmd" value="_s-xclick">
	 <input type="hidden" name="hosted_button_id" value="RD6AZC63ARPX4">
	 <input type="image" src="https://www.paypal.com/en_US/DE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	 <img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
	 </form>
      </p>
   </div>
</div>

</div>


<script>

   jQuery(function() {
      jQuery("#bannertabs").tabs();
   });

</script>