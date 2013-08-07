 <?php
 require_once("banner_db.php");
 require_once(ABSPATH . WPINC."/pluggable.php");
 if(! is_user_logged_in())
 {
   auth_redirect();
 }
 define("WP_BANNER_PATH",WP_PLUGIN_URL .'/wp-banner');
   //global $bannerData;
 if($_GET['createbanner'] ){
      
     if (isset($_GET['eid']) )
     {
           banner_update();   
     }else{
          banner_new();
     }
    
}

 if (isset($_GET['deletebanner']))  { bannerDelete(); }
 
 banner_admin();
 
 function banner_admin()
 {
   global $wpdb, $bannerData, $clientData, $bannerSize, $bannerType, $bannerPosition;
   
 	$bannerSize = array('480x60','728x90','234x60','120x600','120x60','88x31','122x80','277x134','208x102','104x51');
 	$bannerType = array('Image','Flash');
 	$bannerPosition = array('divTag','widget');
   
   $bannerData = bannerGetData();
   
   if ($_GET['banner_client'])
   {
        $clientData = bannerGetClientData($_GET['banner_client']);
        $_GET['eid'] = $clientData['banner_id'];
        
        if (substr($clientData['banner_position'],0,6) == "divTag")
        {
	    list($clientData['banner_position'],$clientData['banner_layoutpos'],$clientData['banner_childpos'],$clientData['banner_leftpos']) = explode(",",$clientData['banner_position']);
        }
   }
    
 }
 
 function bannerCheckImageType()
 {
    $url = $_GET['banner_url'];
    $fh = fopen($url,"r");
    $MagicNumber = fread($fh,3);
    fclose($fh);
    if($MagicNumber == "FWS" or $MagicNumber == "CWS")
    {
        $_GET['banner_type'] = "Flash";
    }else{
        $_GET['banner_type'] = "Image";
    }
 }
 
 function getThemeIds()
 {
   $url = get_option("home");
   $fh = fopen($url,"r");
   $struc = array("body");
   $xTags = array("embed","object");
   
   if($fh)
   {
     
      while(! feof($fh))
      {
	
	 $line = trim(fgets($fh));
	 
	 preg_match_all("/<(\w+) [^>]* *id=\"(.+)\"[^>]*>/U",$line,$match,PREG_PATTERN_ORDER);
	 
	   foreach($match[1] as $tag)
	    {
	       if (in_array(strtolower($tag),$xTags)) continue;
	       
	       foreach($match[2] as $id)
	       {
		  if(count($id) > 0)
		  {
		     if(substr($id,0,6) != "banner" )
			$struc[] =  $id;
		  }
	       }
	    }
      }
   
      return $struc;
   }
   
 }
 
 ?>
 
 <div id="addbanner" style="width:650px;">
 <form id="bannerform" method="post" action="">
 <?php
    if (is_array($clientData))
    {
        _e ("<h3>Update Banner</h3>");
    }else{
        _e ("<h3>Create New Banner</h3>");
    }
?>
<fieldset class="banner-fieldset">
<legend>Client Data</legend>
<table class="banner-maintable">
<tr>
<td>
<?php _e("Existing&nbsp;client"); ?>
</td>
<td><select name="banner_client"  onChange="document.forms[0].submit();">
<option>(clear)</option>
<?php
       
    if ($bannerData)
    {
        foreach ( $bannerData as $key)
        {
            if ($key['banner_clientname'] == $_GET['banner_client'])
            {
                echo "<option selected=\"selected\">".$key['banner_clientname']."</option>";
             }else{
                echo "<option>".$key['banner_clientname']."</option>";
             }
        }
    }else{
           _e( "<option>No Data</option>");
    }
?>
</select> 
</td></tr>
<tr id="bannerclientname"><td><?php _e ("Client&nbsp;name"); ?></td>
<td>
   <p class="banner-nospace"><input type="text" size="30" maxlength="100" name="client_name" class="required" value="<?php echo $clientData['banner_clientname']; ?>"></p>
   <input type="hidden" name="eid" value="<?php echo $clientData['banner_id']; ?>">
</td>   
</tr>
<tr id="bannerurl">
<td>Banner URL</td>
<td>
   <p class="banner-nospace"><input type="text" size="55"  class="required url" name="banner_url" value="<?php echo $clientData['banner_url']; ?>">
   <div id="showButton" title="open Banner-Window"><input type="image" src="<?php echo WP_BANNER_PATH; ?>/images/openwin.png"></div>
   </p>
</td>
</tr>
<tr id="clickurl">
<td>Click URL</td>
<td>
   <p class="banner-nospace"><input type="text" size="55"  class="url" name="click_url" value="<?php echo $clientData['banner_clickurl'] ?>"></p>
</td>
</tr>
</table>
</fieldset>

<?php
   if($clientData['banner_url']) {
      if ($clientData['banner_type'] != "Flash")
      {
	 echo '<div id="showBanner" title="'.$clientData['banner_clientname'].'">';
	 echo '<img src="'.$clientData['banner_url'].'" id="bannerImage">';
	 echo '</div>';
      }else{
	 list($width,$height) = split("x",$clientData['banner_size']);
	 echo '<div id="showBanner" title="'.$clientData['banner_clientname'].'">';
	 echo '<OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000 width="'.$width.'" height="'.$height.'">
		<PARAM NAME="movie" VALUE="'.$clientData['banner_url'].'">
		<PARAM NAME="quality" VALUE="high">
		<EMBED src='.$clientData['banner_url'].'"quality="high" width="'.$width.'" height="'.$height.'" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
		</OBJECT>';
	 echo '</div>';
      }
   }
?>

<div></div>
<fieldset class="banner-fieldset">
<legend>Ad Banner Data</legend>
<table cellspacing="0" class="banner-maintable">
<tr>
<td width="170px">Banner Size</td>
<td>
   <select name="banner_size" title="necessary when using Flash">
   <option></option>
   <?php
        
         if ($bannerSize)
	 {
	    foreach ( $bannerSize as $key => $value)
	    {
		if ($clientData['banner_size'] == $value)
		{
		  echo "<option selected=\"selected\">".$value."</option>";
		}else{
		  echo "<option>".$value."</option>";
		}
	     }
	 }else{
		_e( "<option>No Data</option>");
	 }
      ?>
</td>
</tr>
<tr>
<td><?php _e("Impressions Purchased");?></td>
<td>
<input type="text" title="0 = unlimited" size="10" maxlength="10" name="impressions_purchased" value="<?php echo $clientData['banner_impurchased']; ?>">
</td>
</tr>

<tr>
<td><?php _e("Start Date");?></td>
<td><?php
      if ($clientData['banner_startdate'] > 0)
      {
            $tmp = getdate($clientData['banner_startdate']);
            echo '<input type="text" name="banner_startdate" readonly id="startdate" size="10" maxlength="10" value="'.$tmp['mon'].'/'.$tmp['mday'].'/'.$tmp['year'].'">';
      }else{
	   $tmp = getdate();
	   echo '<input type="text" name="banner_startdate" readonly id="startdate" size="10" maxlength="10" value="'.$tmp['mon'].'/'.$tmp['mday'].'/'.$tmp['year'].'">';
      }
    ?>

</td></tr>

<tr>
<td><?php _e("End Date"); ?></td>
<td>
<?php
   if ($clientData['banner_enddate'] > 0)
   {
      $tmp = getdate($clientData['banner_enddate']);
      echo '<input type="text" readonly name="banner_enddate" id="enddate" size="10" maxlength="10" value="'.$tmp['mon'].'/'.$tmp['mday'].'/'.$tmp['year'].'">';
   }else{
      echo '<input title="blank = no limit" type="text" readonly name="banner_enddate" id="enddate" size="10" maxlength="10">';
   }
?>

</td>
</tr>

<tr style="background:#F4E6DD;">

   <td><?php _e('Position');?></td>
   <td>
      <table>
	<?php
		
		echo '<tr><td><input type="radio" name="banner_position" value="widget"';
		if ("widget" == $clientData['banner_position']) echo "  checked";
		echo "></td><td>in a widget</td></tr>";
		echo '<tr><td><input type="radio" name="banner_position" value="divTag"';
		if ("divTag" == $clientData['banner_position']) echo "  checked";
		echo "></td><td>in layout</td></tr>";
		
	?>
      </table>
      <div class="banner-inlayout">
	 <table class="banner-table">
	 <th colspan="3">Place your ad on your blog</th>
	 <tr style="background-color:#DBE8FF;"><td>WP Div ID</td>
	     <td colspan="2">
		  <div id="adcontainer">
		     <div class="banner-radiopos"></div>
		     <div class="banner-radiopos banner-radiopospad"></div>
		     <div class="banner-radiopos"></div>
		  </div>
	     </td>
	 </tr>
	 <tr><td valign="top" >
	 <select name="banner_layoutpos">
	 <?php
	   $layoutOptions = getThemeIds();
	    foreach ($layoutOptions as $option)
	    {
	       echo "<option";
	       if ($clientData['banner_layoutpos'] == "$option") echo " selected";
	       echo ">$option</option>";
	    }
	    
	  ?>
	 </select>
	 </td>
	 <td valign="top">
	    <input type="radio"  name="banner_childpos" value="append" <?php if($clientData['banner_childpos'] == 'append' || ! $clientData['banner_childpos']) echo "checked"?>> Append<br/>
	    <input type="radio"  name="banner_childpos" value="prepend" <?php if($clientData['banner_childpos'] == 'prepend') echo 'checked'?>> Prepend<br/>
	    <input type="radio"  name="banner_childpos" value="after" <?php if($clientData['banner_childpos'] == 'after') echo 'checked'?>> After<br/>
	 </td>
	 <td valign="top">
	    <input id="banneramount" readonly name="banner_leftpos" type="text" maxlength="3" size="3" 
		     <?php if(isset($clientData['banner_leftpos'])){ echo "value=\"". $clientData['banner_leftpos']."\"";} else{ echo " value=\"25\"";}?>>in <b>%</b>
	    <div id="slider-vertical" style="height:100px;float:right;"></div>
	 </td>
	 </tr>
	 <tr><td colspan="3"><i>This option works only with wp standard templates!</i></td></tr>
	 </table>
	 
      </div>
   </td>
</tr>
<tr>
<td>
<?php _e("Active"); ?>
</td>
<td>
<input name="banner_enabled" type="radio" value="1" <?php if($clientData['banner_active'] == 1) echo "checked"?> > <?php  _e("Yes"); ?>
&nbsp;&nbsp;<input name="banner_enabled" type="radio" value="0" <?php if(! $clientData['banner_active'] == 1) echo "checked"?> > <?php _e("No"); ?>

</tr>

</table>
</fieldset>
<table class="banner-maintable">
<tr>
   <?php
    if (is_array($clientData))
    {
            echo "<td style=\"text-align:center\" ><input class=\"button\" onclick=\"return confirm('really delete this banner?');\" style=\"background:#997777;\" type=\"submit\" name=\"deletebanner\" value=\"";
            _e("Delete Banner");
            echo "\"></td>";   
    }
 ?>
    <td style="text-align:center" >
      <input class="button" type="submit" name="createbanner" value="<?php _e("Update Banner");?>">
   </td>
   
 </tr> 
</table>
</form>
<script type="text/javascript">

jQuery(function() {

	 <?php
	 $marginL = (isset($clientData['banner_leftpos']) ? $clientData['banner_leftpos'] : "25" );
	 $divParam = "id='rpred' class='banner-radiopos' style='background:#E85752; margin-top:5px;  margin-left:$marginL%;";
	 echo 'jQuery("#rpred").detach();';
	
	   switch($clientData['banner_childpos']){
	       case "append":	    
		     echo 'jQuery("#adcontainer").append("<div '.$divParam.'\'></div>");';
		     break;
	       case "prepend":
		     echo 'jQuery("#adcontainer").prepend("<div '.$divParam.' margin-bottom:5px; \'></div>");';
		     break;
	       case "after":
		    echo 'jQuery("#adcontainer").after("<div '.$divParam.'\'></div>");';
		    break;
	    }
	 ?>
	 
	 
	if (jQuery("input[name=client_name]").val() == "")
	{
	    jQuery("input[name=createbanner]").val("Create New");
	}
	jQuery("input[value=divTag]")
	    .click(function () {
	       jQuery(".banner-inlayout")
		  .slideDown("slow");
	    })
	    .attr("checked",function(){
	       if (this.checked ) jQuery(".banner-inlayout").slideDown("slow");
	     });
	jQuery("input[value=post],input[value=widget]")
	    .click(function(){
		     jQuery(".banner-inlayout").slideUp("slow");
		     jQuery("select[name=banner_layoutpos] option:selected").attr("selected",false);
	     });
	jQuery("input[value=append]")
	    .click(function(){
	       jQuery("#rpred").detach();
	       jQuery("#adcontainer").append('<div id="rpred" class="banner-radiopos" style="background:#E85752; margin-top:5px"></div>');
	     });
	jQuery("input[value=prepend]")
	    .click(function(){
	       jQuery("#rpred").detach();
	       jQuery("#adcontainer").prepend('<div id="rpred" class="banner-radiopos" style="background:#E85752; margin-top:5px;margin-bottom:5px"></div>');
	     });
	jQuery("input[value=after]")
	    .click(function(){
	       jQuery("#rpred").detach();
	       jQuery("#adcontainer").after('<div id="rpred" class="banner-radiopos" style="background:#E85752; margin-top:5px; margin-left:25%"></div>');
	     });
	 
	jQuery("#enddate").click(function(){jQuery(this).val("")});
	jQuery("#startdate").datepicker({
	    showOn: 'button',
	    minDate: '0',
	    buttonImage: '<?php echo WP_BANNER_PATH . '/images/calendar.gif' ?>',
	    buttonImageOnly: true,
	    buttonText: 'Date of campaign start'
	    });
	jQuery("#enddate").datepicker({
	    showOn: 'button',
	    minDate: '0',
	    buttonImage: '<?php echo WP_BANNER_PATH . '/images/calendar.gif' ?>',
	    buttonImageOnly: true,
	    buttonText: 'Date of campaign end. Leave field empty if its a never ending story'
	    });
	    
	jQuery("#bannerform").validate({
					  onkeyup: false,
					  onclick: false,
					  errorClass: "error",
					  highlight: function(element, errorClass){
							  jQuery(element).closest("p").attr("class",errorClass);
						     },
					  submitHandler: function(form) {
					  form.submit();
					  }
				       });
	
	jQuery("#slider-vertical").slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: <?php if(isset($clientData['banner_leftpos'])){  echo $clientData['banner_leftpos'];} else{ echo "25";} ?>,
			slide: function(event, ui) {
				jQuery("#banneramount").val(ui.value);
				jQuery("#rpred").css("margin-left",ui.value + "%")
			}
		});
	
	jQuery("#bannerclientname").bind("dialogbeforeclose", function(event, ui) {alert("close")});
	jQuery("[title]").tooltip();
	
	jQuery("#showBanner").dialog({ autoOpen: false,
				       width: function(){
						return document.getElementById('bannerImage').width + 30;
					     }
				    });
	
	
	
	
	if ( jQuery("input[name=banner_url]").val() != "")
	{
	    jQuery("#showButton").fadeIn("slow");
	    
	     jQuery("#showButton").click(function(){
				    if (! jQuery("#showBanner").dialog("isOpen"))
				    {
				       jQuery(this).attr("title","close Banner-Window");
				       jQuery(this).tooltip("destroy");
				       jQuery(this).tooltip();
				       jQuery("#showBanner").dialog("open");
				       return false;
				    }else{
				       jQuery(this).attr("title","open Banner-Window");
				       jQuery(this).tooltip("destroy");
				       jQuery(this).tooltip();
				       jQuery("#showBanner").dialog("close");
				       return false;
				    }
				   });
	    
	
	   
	}


	
	
	

});
</script>