<?php
if (!file_exists('../../../wp-config.php')) die ('wp-config.php not found');
require_once('../../../wp-config.php');


function banner_clients()
{
   global $wpdb, $table_prefix;
    
    $query = "SELECT banner_clientname FROM ". $table_prefix."banner";
    $result = $wpdb->get_results($query);
//     echo json_encode($result);
    return(json_encode($result));
}
 function banner_update()
 {
   global $wpdb, $table_prefix, $start_date, $end_date;

   bannerCheckImageType();
   
   $cli =  $_GET['client_name'];
   
   $query= "UPDATE ".$table_prefix."banner SET banner_clientname='".$cli."', banner_clickurl='".$_GET['click_url']."', banner_impurchased='".$_GET['impressions_purchased']."', banner_startdate='".$_GET['banner_startdate']."', banner_enddate='".$_GET['banner_enddate']."',banner_active='   ".$_GET['banner_enabled']."', banner_url='".$_GET['banner_url']."', banner_size='".$_GET['banner_size']."',banner_type='".$_GET['banner_type']."',banner_position='".$_GET['banner_position']."' WHERE banner_id='".$_GET['eid']."' ";
  
   $wpdb->query($query);
 }
 
 function banner_new()
 {
    global $wpdb,  $table_prefix, $start_date, $end_date;
    
    bannerCheckImageType();
    
    $cli =  $_GET['client_name'];
    
    $query = "INSERT INTO ".$table_prefix."banner VALUES(0, '".$cli."', '".$_GET['click_url']."', '".$_GET['impressions_purchased']."', '$start_date', '$end_date', '".$_GET['banner_enabled']."', 0, 0, '".$_GET['banner_url']."','".$_GET['banner_size']."','".$_GET['banner_type']."','".$_GET['banner_position']."')";
    
       $wpdb->query($query);
 }
 
 function bannerGetData()
 {
    global $wpdb, $table_prefix;
    
    $query = "SELECT * FROM ". $table_prefix."banner";
    $result = $wpdb->get_results($query, ARRAY_A);
    
   if ( is_array($result)){   return $result;} else{  return false;}
 }

 
 function bannerGetClientData($client)
 {
        global $wpdb, $table_prefix;
        $query = "SELECT * FROM ". $table_prefix."banner WHERE banner_clientname =\"$client\"";
        $result = $wpdb->get_row($query, ARRAY_A);
        
         if ( is_array($result)){   return $result;} else{  return false;}
 }
 
 function bannerGetPosts()
 {
   global $wpdb, $table_prefix;
   $query = "SELECT ID FROM ". $table_prefix."posts WHERE post_status=\"publish\" AND post_type=\"post\" ORDER BY ID LIMIT 0,6";
   $result = $wpdb->get_results($query, ARRAY_A);
   if ( is_array($result)){   return $result;} else{  return false;}
 }
 
 function bannerDelete()
 {
    global $wpdb, $table_prefix;
    
   $query = "DELETE FROM ".$table_prefix."banner WHERE banner_id =  \"".$_GET['eid']."\"";
   $wpdb->query($query);
 }
 
 
?>