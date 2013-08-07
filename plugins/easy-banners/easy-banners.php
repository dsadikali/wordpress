<?php
/*
Plugin Name: Easy Banners
Plugin URI: http://www.bannerslide.com/easy-banners-for-wordpress.html
Description: Manage ad banners quickly with this simple interface. Edit your theme to use this function where you want banners displayed: <code>wp_easy_banner_display('left');</code> to display the "Left" banners. Other options are "right" and "header." View the <a href="../wp-content/plugins/easy-banners/readme.txt" target="_blank">readme.txt</a> file for more information.
Version: 1.3
Author: bannerslide
Author URI: http://www.bannerslide.com/
*/

if (!class_exists("EasyBanners")){
	class EasyBanners{
		
		var $table_name = '';
		var $init = 0;
		
		function EasyBanners(){
			//constructor
			
			global $table_prefix;
	
			$this->table_name = $table_prefix."easy_banners";
		}
		function Init(){
			//create table
			if ($this->CreateBannerTable() == false){
				return false;
			}
			$this->init = 1;
			
			return true;
		}
		function addHeader() {
			echo "<script type = 'text/javascript' src = '".get_bloginfo('wpurl')."/wp-content/plugins/easy-banners/javascript/handlers.js'></script>\n";
		}
		
		/**
		 * Creates banner table
		 */
		function CreateBannerTable() {
			
			$rs = @mysql_query("SHOW TABLES LIKE '$this->table_name'");
			$exists = @mysql_fetch_row($rs);
			if ( !$exists ) {
				$sql = "CREATE TABLE ".$this->table_name." (
						`id` int(11) NOT NULL auto_increment,      
						`name` varchar(250) default NULL,
						`imageURL` varchar(500), 
						`link` varchar(500), 
						`type` varchar(10) DEFAULT 'IMG',
						`target` varchar(10) DEFAULT '',       
						`width` smallint(6) DEFAULT 0,  
						`height` smallint(6) DEFAULT 0,
						`position` varchar(10) NOT NULL default 'RIGHT',
						PRIMARY KEY (`id`)
						);
						";
				@mysql_query($sql);
				return true;
			}
			return false;
		}
		/**
		 * insert new banner
		 */
		 function InsertBanner($name, $imageURL, $link, $type, $target, $width, $height, $position = 'RIGHT'){
		 	if (strpos($type, 'IMG')  === false && strrpos($type, 'SWF')  === false && strrpos($type, 'ADS')  === false){
				return;
			}
			$imageURL = str_replace("'", '"', $imageURL);
			$sqlString = "INSERT INTO $this->table_name(name, imageURL, link, type, target, width, height, position)".
			             "VALUES('$name', '$imageURL', '$link', '$type', '$target', $width, $height, '$position')";
			@mysql_query($sqlString);
		 }
	 
		 /**
		 * delete from banner table
		 */
		 function DeleteBanner($id){
		 	
			$sqlString = "DELETE FROM $this->table_name WHERE id = $id";
			@mysql_query($sqlString);
		 }
		 /**
		 * update banner
		 */
		 function UpdateBanner($id, $imageURL, $link, $type, $target, $width, $height, $position = 'RIGHT'){
		 	$imageURL = str_replace("'", '"', $imageURL);
			$sqlString = "UPDATE $this->table_name SET imageURL = '$imageURL', ".
						 "link = '$link', target = '$target', width = $width, height = $height, position  = '$position' WHERE id = $id";
			//echo $sqlString;
			@mysql_query($sqlString);
		 }
		 /**
		 * check banner name
		 */
		 function CheckBannerName($name){
			$sql = "SELECT COUNT(*) AS num FROM $this->table_name WHERE  name like '$name'";
			$rs = @mysql_query($sql);
			$numRS = @mysql_fetch_assoc($rs);

			if ($numRS['num'] > 0){
				return false;
			}
			return true;
		 }
		  /**
		 * check banner source url
		 */
		 function CheckBannerSource($srcURL, $is4Update = false, $oldID = 0){
		 	if ($is4Update == true){
				$sql = "SELECT COUNT(*) AS num FROM $this->table_name WHERE  imageURL like '$srcURL' AND id <> $oldID";
				//echo $sql;
			}else{
				$sql = "SELECT COUNT(*) AS num FROM $this->table_name WHERE  imageURL like '$srcURL'";
			}
			$rs = @mysql_query($sql);
			$numRS = @mysql_fetch_assoc($rs);

			if ($numRS['num'] > 0){
				return false;
			}
			return true;
		 }

		 function printAdminPage(){
		 	$msg = "";
		 	$task = '';
			$Error = true;
			
			
			if (isset($_POST['task'])){
				$task = $_POST['task'];
			}

			if ($task == 'INSERT_NEW' || $task == 'UPDATE_OLD'){
				$name = $_POST['name'];
				$srcURL = $_POST['srcURL'];
				$linkURL = $_POST['linkURL'];
				$openTarget = $_POST['openTarget'];
				$bannerType = $_POST['bannerType'];
				$bannerPosition = $_POST['bannerPos'];
				
				$name = trim($name);
				$name = str_replace("'", "\'", $name);
				$srcURL = trim($srcURL);
				$linkURL = trim($linkURL);
				$openTarget = trim($openTarget);
				$bannerType = trim($bannerType);
				
				
				$chk2Return = "";
				
				if ($name == ""){
					$msg .= "The Banner Name can't be NULL. ";
					$Error = false;
				}
				if ($srcURL == ""){
					$msg .= "The Banner Source URL of '$name' can't be NULL. ";
					$Error = false;
				}else if (strpos($srcURL, "'") !== false){
					$msg .= "The Banner Source URL of '$name' can't include any '. ";
					$Error = false;
				}
				if($bannerType == 'IMG'){ 
					if ($linkURL && strpos($linkURL, "'") !== false){
						$msg .= "The Link to URL of '$name' can't include any '. ";
						$Error = false;
					}
				}else if($bannerType == 'SWF'){
					if (function_exists(ckurl)){
						if (ckurl($srcURL) == false){
							$msg .= "The Banner Source URL of '$name' is invalid. ";
							$Error = false;
						}
					}
					$linkURL = "";
				}else if ($bannerType == 'ADS'){
					if (function_exists(ckurl)  && $_POST['checksrc'] == 1){
						if (ckurl($srcURL) == false){
							$msg .= "The Banner Source URL of '$name' is invalid. ";
							$Error = false;
						}
					}
					$linkURL = "";
				}
				
				if ($Error == false){
					//
				}else{
					//check if name, srcurl exist

					if ($Error == true && $task == 'INSERT_NEW'){
						if ($this->CheckBannerName($name) == false){
							$msg .= "Insert failed. The banner name: \"$name\" existed already.";
							$Error = false;
						}
						if ($bannerType != 'ADS'){
							if ($this->CheckBannerSource($srcURL) == false){
								$msg .= "Insert failed. The source url: \"$srcURL\" existed already.";
								$Error = false;
							}
						}
						if ($Error == true){
							$this->InsertBanner($name, $srcURL, $linkURL, $chk2Return.$bannerType, $openTarget, 300, 250, $bannerPosition);
							$msg = "New banner inserted!";
						}
					}else if ($Error == true && $task == 'UPDATE_OLD'){
						$oldID = $_POST['bannerID'];
						if (is_numeric($oldID) == false){
							$msg = "Update failed. Invalid banner ID.";
							$Error = false;
						}else if ($this->CheckBannerSource($srcURL, true, $oldID) == false){
							$msg .= "Update failed. The source url: \"$srcURL\" existed already.";
							$Error = false;
						}						
						if ($Error == true){
							$this->UpdateBanner($oldID, $srcURL, $linkURL, $bannerType, $openTarget, 300, 250, $bannerPosition);
							$msg = "Banner '$name' updated!";					
						}
					}
				}
			}else if ($task == 'DELETE_OLD'){
				$oldID = $_POST['bannerID'];
				if (is_numeric($oldID) == false){
					$msg = "Delete failed. Invalid banner ID.";
					$Error = false;
				}
				if ($Error == true){
					$sql = "SELECT * FROM $this->table_name WHERE id = $oldID";
					$rs = @mysql_query($sql);
					$row = @mysql_fetch_assoc($rs);
					$name = $row['name'];
					
					$this->DeleteBanner($oldID);
					$msg = "Banner '$name' deleted!";					
				}
			}
			//display all
			$sql = "SELECT * FROM $this->table_name";
			$rs = @mysql_query($sql);
			?>
            <?php $this->addHeader(); ?>
            <div id = "icon-options-general" class = "icon32"></div>
			<h1>Easy Banners</h1>
			
           <?php if ($msg) {
		   ?>
		    <div align = "center" class = "updated fade">
            	<font color = "<?php if($Error == false){ echo '#FF0000';} else { echo '#0000FF';} ?>">
                	<b><?php echo $msg ?></b>
                </font>
                </div>
            <hr />
			<?php } // end if ?>
			
            <h3>Installed banners</h3>
            <p>Developed by <a href="http://www.bannerslide.com" target="_blank">www.bannerslide.com</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.bannerslide.com/contact.html" target="_blank">Contact Us</a></p>
            <p>For more help please vist <a href="http://www.bannerslide.com/easy-banners-for-wordpress.html" target="_blank">http://www.bannerslide.com/easy-banners-for-wordpress.html</a></p>
            <table class="widefat">
              <thead><tr valign="top">
                <th width="3%">No.</th>
                <th width="10%">Banner Name</th>
                <th width="10%">Banner Type</th>
                <th width="10%">Banner Position</th>
                <th width="24%">Banner Source URL</th>
                <th width="15%">Link to URL</th>
                <th width="16%">Open Target</th>
                <th width="14%">Action</th>
              </tr></thead>
			  <tbody>
              <?php
			  		$iItem = 0;
			  		while ( $row = @mysql_fetch_assoc($rs) ) {
						$iItem++;
			  ?>
              <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
              <tr valign="top">
                <td class="topright">
					<?php echo $iItem; ?>
                    <input type="hidden" name="bannerID" id="bannerID" value="<?php echo $row['id']; ?>" />
                </td>
                <td class="topright">
					<?php echo $row['name']; ?>
                    <input type="hidden" name="name" id="name" value="<?php echo $row['name']; ?>" />
                </td>
                <td class="topright">
                	<?php 
						if ($row['type'] == 'SWF'){
							echo 'Flash'; 
						}else if ($row['type'] == 'IMG'){
							echo 'Image';
						}else if ($row['type'] == 'ADS'){
							echo 'Adsense';
						}
					?>
                    <input type="hidden" name="bannerType" id="bannerType" value="<?php echo $row['type']; ?>" />
                </td>
				<td class="topright">
                    <select name="bannerPos" id="bannerPos" autocomplete="off">
                    <option value="RIGHT"  <?php if ($row['position'] == 'RIGHT') echo 'selected="selected"'; ?>>Right</option>
                    <option value="HEADER" <?php if ($row['position'] == 'HEADER') echo 'selected="selected"'; ?>>Header</option>
                    <option value="LEFT" <?php if ($row['position'] == 'LEFT') echo 'selected="selected"'; ?>>Left</option>
                    </select>
                </td>
                <td class="topright">
       <textarea name="srcURL" id="srcURL" cols="45" rows="<?php if ($row['type'] == 'ADS') echo '10'; else echo '5'; ?> "><?php echo trim($row['imageURL']); ?></textarea>
                </td>
                <td class="topright">
                	<?php 
						if ($row['type'] == 'IMG'){
					?>
                	<textarea name="linkURL" id="linkURL" cols="25" rows="5"><?php echo $row['link']; ?></textarea>
                    <?php }else{ ?>
                    <input type="hidden" name="linkURL" id="linkURL" value="" />
                    <?php } ?>
                </td>
                <td class="topright">
					<?php if ($row['type'] == 'IMG'){ ?>
                        <select name="openTarget" id="openTarget">
                        <option value="_blank" <?php if ($row['target'] == '_blank') echo 'selected="selected"'; ?>>new window</option>
                        <option value="_self" <?php if ($row['target'] == '_self') echo 'selected="selected"'; ?>>same window</option>
                        </select>
                    <?php }else{ ?>
                        <input type="hidden" name="openTarget" id="openTarget" value="" />
                    <?php } ?>
                </td>
                <td class="top"><label>
                  <div align="left">
                  	<input type="hidden" name="task" id="task" value=""/>
              		<input type="button" name="Update" id="Update" value="Update" class="button-secondary" onclick="this.form.task.value='UPDATE_OLD';this.form.submit();" style="width: 70px;"/>
             		<br />
                    <input type="button" name="Delete" id="Delete" value="Delete" class="button-secondary" onclick="this.form.task.value='DELETE_OLD';this.form.submit();"  style="width: 70px;"/>
                    </div>
                </label></td>
              </tr>
              </form>
              <?php
			  		}//end of while ( $row = @mysql_fetch_assoc($rs) ) {

			  ?>
			</tbody>
            </table>
            <br />
            
			<hr />
            <h3>Add New Banner</h3>


            <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            <table class="widefat" style="width: 50%;">
              <tr valign="top">
                <th width="25%" class="botright"><label for="name">Banner Name</label></th>
                <td width="25%" class="bottom">
                <input type="text" name="name" id="name" size="55" maxlength="250" value="<?php echo @$_POST['name'];?>" />
                </td>
              </tr>
              <tr valign="top">
                <th class="botright"><label for="bannerType">Banner Type</label></th>
                <td class="bottom">
                <select name="bannerType" id="bannerType" onchange="onAddTypeChange(this)" autocomplete="off" >
                <option value="IMG" selected="selected">Image</option>
                <option value="SWF">Flash</option>
                <option value="ADS">Adsense</option>
                </select>
                </td>
              </tr>
              <tr valign="top">
                <th class="botright"><label for="bannerPos">Banner Position</label></th>
                <td class="bottom">
                <select name="bannerPos" id="bannerPos" autocomplete="off">
                <option value="RIGHT"  selected="selected">Right</option>
                <option value="HEADER">Header</option>
                <option value="LEFT">Left</option>
                </select>
                </td>
              </tr>
              <tr valign="top">
                <th class="botright"><label for="srcURL">Banner Source URL</label><br />
                <span id="imagePreviewWindow" style="display:block">
				<img src="<?php echo WP_PLUGIN_URL; ?>/easy-banners/images/spacer.gif" width="72" id="proxy" name="proxy" alt="Thumbnail" title="Thumbnail" border="0" align="top" style="padding-top: 10px;">
                </span>
				</th>
                <td class="bottom">
                <textarea name="srcURL" id="srcURL_addnew" cols="45" rows="5"><?php echo @$_POST['srcURL'];?></textarea>
                <span id="shoeMediaLibraryList" style="display:block">
				<?php show_media_library('image') ?>
                </span>
				<span id="shoeMediaLibraryListOnlySWF" style="display:none">
				<?php show_media_library('swf') ?>
                </span>
				</td>
				
              </tr>
              <tr valign="top">
                <th class="botright">
                	<span id="addLinkToURLDesc" style="display:block"><label for="linkURL">Link to URL</label></span>                </th>
                <td class="bottom">
                	<span id="addLinkToURText" style="display:block"><textarea name="linkURL" id="linkURL" cols="45" rows="5"><?php echo @$_POST['linkURL'];?></textarea></span>
                </td>
              </tr>
              <tr valign="top">
                <th class="botright">
                	<span id="addLinkToTargetDesc" style="display:block"><label for="openTarget">Open Target</label></span>                </th>
                <td class="bottom">
               		<span id="addLinkToTargetText" style="display:block">
                    	<select name="openTarget" id="openTarget" autocomplete="off">
                    	<option value="_blank" selected="selected">new window</option>
                   	 	<option value="_self">same window</option>
                    	</select>
                	</span>
                </td>
              </tr>
              <tr valign="top">
                <td width="20%" colspan="2" style="padding:3px;">
                	<div align="center">
                    <input type="hidden" name="task" id="task" value="INSERT_NEW" />
              		<input class="button-primary" type="Submit" name="Insert" id="Insert" value="Submit" /><br />
					</div>
                </td>
              </tr>
            </table>
            </form>
            <br />
            <br />
            <?php
			
		 }//end of function
		 
	}//end of class EasyBanners{
}

if (class_exists("EasyBanners")) {
	$hl_EasyBanners = new EasyBanners();
}

//Initialize the admin panel
if (!function_exists("EasyBanners_ap")) {
	function EasyBanners_ap() {
		global $hl_EasyBanners;
		if (!isset($hl_EasyBanners)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('Easy Banners', 'Easy Banners', 9, basename(__FILE__), array(&$hl_EasyBanners, 'printAdminPage'));
		}
	}	
}
if (isset($hl_EasyBanners)) {
	add_action('admin_menu', 'EasyBanners_ap');
	add_action('activate_easy-banners/easy-banners.php', array(&$hl_EasyBanners, 'Init'));
}

if (!function_exists("wp_easy_banner_display")) {
	function wp_easy_banner_display($position = 'RIGHT', $nameOfClass = 'sideimg', $readMore = 'read more>>') {
		
		global $table_prefix;
	
		$table_name = $table_prefix."easy_banners";
		$sql = "SELECT * FROM $table_name WHERE position = '$position'";
		
		$rs = @mysql_query($sql);
		while ( $row = @mysql_fetch_assoc($rs) ) {
			$iItem++;
  		?>
<div class = "<?php echo $nameOfClass ?>">
		<?php 
			if ($row['type'] == 'SWF'){ 
				$pos = strrpos($row['imageURL'], '.');
				if ($pos !== false){
					$title = substr($row['imageURL'], 0, $pos);
				}else{
					$title = $row['imageURL'];
				}
	   ?>
	<script type = "text/javascript">
	AC_FL_RunContent( 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version = 9,0,28,0','width','300','height','250','src','<?php echo $title; ?>','quality','high','pluginspage','http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version = ShockwaveFlash','movie','<?php echo $title; ?>' ); //end AC code
</script><noscript>
				<object classid = "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase = "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version = 9,0,28,0" width = "300" height = "250">
	  <param name = "movie" value = "<?php echo $row['imageURL']; ?>" />
	  <param name = "quality" value = "high" />
	  <embed src = "<?php echo $row['imageURL']; ?>" quality = "high" pluginspage = "http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version = ShockwaveFlash" type = "application/x-shockwave-flash" width = "300" height = "250"></embed>
	</object>
	</noscript>
		<?php }else if ($row['type'] == 'IMG') { ?>
        
			<a href = "<?php echo $row['link']; ?>" target = "<?php echo $row['target']; ?>"><img src = "<?php echo $row['imageURL']; ?>"/></a>
            <?php 
				if ($readMore != ''){
			?>
        	<p><?php echo $row['name']; ?>&nbsp;&nbsp;&nbsp;<a href = "<?php echo $row['link']; ?>"><?php echo $readMore ?></a></p>
            <?php
				}
			?>
		<?php }else if ($row['type'] == 'ADS') { ?>
        
			<?php echo $row['imageURL']; ?>
        
		<?php } ?>
</div>
  <?php
		}//end of while ( $row = @mysql_fetch_assoc($rs) ) {
	}//endo of function
}


// additions to improve layout
function admin_register_head() {
	$siteurl = get_option('siteurl');
	$url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/style.css';
	echo "<link rel = 'stylesheet' type = 'text/css' href = '$url' />\n";
}
add_action('admin_head', 'admin_register_head');
	
function show_media_library($type='image') {
	$dir =  '';
	
	if ($type == 'image'){
		$script="e = document.getElementById('clicklist');f=document.getElementById('srcURL_addnew'); if (e.selectedIndex != 0) { document.images.proxy.src=e.options[e.selectedIndex].value; f.value=e.options[e.selectedIndex].value; }else{ document.images.proxy.src=''; f.value=''; }";
		$clicklist = '<select id = "clicklist" name = "clicklist" onChange = "'. $script . '">';
	}else if ($type == 'swf'){
		$script="e = document.getElementById('clicklistSWF');f=document.getElementById('srcURL_addnew');if (e.selectedIndex != 0) { f.value=e.options[e.selectedIndex].value;}else{ f.value=''; }";
		$clicklist = '<select id = "clicklistSWF" name = "clicklistSWF" onChange = "'. $script . '">';
	}
	
	$clicklist .= '<option>Optional: Select a file from WP "uploads" folder...</option>';
	if ($type == 'image'){
		$filelist = listfiles($dir,'jpg,png,gif,jpeg');
	}else if ($type == 'swf'){
		$filelist = listfiles($dir,'swf');
	}
	for ($i = 0; $i < count($filelist); $i++) {
		$filename = $urlprefix . $filelist[$i];
		$shortname = substr($filename,strrpos($filename,'/')+1);
		$clicklist.= "\t<option value = '$filename'>$shortname</option>\n";
	}
	$clicklist.= '</select><br />';
	echo $clicklist;
}


function listfiles($dir,$spec) { 
	//change this if you're not looking in WP uploads:
	$rootdir=WP_CONTENT_DIR.'/uploads';
	$urlprefix=get_bloginfo('wpurl').'/wp-content/uploads';
	$specString = $spec;

	$spec = explode(',',$spec);
	$filelist = array();
	$opendir = opendir($rootdir . '/' . $dir); 
	while ($filename = readdir($opendir)) { 
		if (($filename == ".") || ($filename == ".."))  { continue;}
		
		$ext = substr($filename,strrpos($filename,'.')+1);
		if (in_array($ext,$spec)) { $filelist[] = $urlprefix . $dir . '/'. $filename;}			
		// recurse if this is a directory:
		if ( is_dir($rootdir .'/' . $dir . '/'. $filename )) {  $filelist = array_merge( $filelist,listfiles($dir.'/'.$filename, $specString) );}
	}
	// don't let the door hit you on the way out:
	closedir($opendir); 
	sort($filelist);
	return $filelist;
}


	
// quick debug
function ddprint_r($x) {
	echo "<pre>";
	print_r($x);
	echo "</pre>";
	}
	
?>