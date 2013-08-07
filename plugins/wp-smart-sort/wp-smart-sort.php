<?php
/*
Plugin Name: WP Smart Sort
Plugin URI: http://dyasonhat.com/wp-smart-sort/
Description: Advanced posts sorting for wordpress. Sort by various fields including custom fields.
Author: dyasonhat
Version: 2.1.2
Author URI: http://dyasonhat.com/
*/
/*
Changelog
2.1.2
    Fixed pages dissapearing in the admin screen.
    Fixed posts repeating themselves.
2.1.1
    Fixed viewing individual tag pages bug.
2.1.0
    Admin UI upgraded to 2.7 style
    Support for all permalink varieties including default eg ?cat=4
    Set default sort direction for individual category pages.
    Fixed bug with static pages dissapearing with plugin activation.
    Updated readme.txt
2.0.3
    Update readme to indicate $wpss->placesort()
2.0.2
    Version number bump for svn issue
2.0.1
    Fix for Sort dropdown navigation and Safari Browser
2.0.0
    All new release.
*/     
class WP_Smart_Sort {
    

    function ss_init() {
        global $wp_rewrite;
        
           $wp_rewrite->flush_rules();
        
    }
    
    function ss_rewrite($wp_rewrite) {
        /*$keywords_structure, 
                $ep_mask = EP_NONE, 
                $page = true, 
                $feed = true, 
                $forcomments = false, 
                $walk_dirs = true); */
        /*
        * SORT REWRITE RULES
        */
        $sort_token = '%ssort%';
        $wp_rewrite->add_rewrite_tag($sort_token, '(.+?)', "ssort=");
        
        $keywords_structure = array();
        //index
        $keywords_structure[] = $wp_rewrite->root . "/";
        //categories
        $keywords_structure[] = $wp_rewrite->category_structure . "/";
        //tag
        $keywords_structure[] = $wp_rewrite->tag_structure . "/";
        //author
        $keywords_structure[] = $wp_rewrite->author_structure . "/";
        //date
        //year
        $keywords_structure[] = $wp_rewrite->get_year_permastruct() . "";
        //month
        $keywords_structure[] = $wp_rewrite->get_month_permastruct() . "";
        //day
        $keywords_structure[] = $wp_rewrite->get_day_permastruct() . "/";
            
        foreach ($keywords_structure as $keyword_structure) {
            $wp_rewrite->rules = $wp_rewrite->generate_rewrite_rules($keyword_structure . "sort/$sort_token", EP_NONE, true, true, false, false) + $wp_rewrite->rules;
        }

    }

    function ss_insert_my_rewrite_query_vars($vars) {
      $vars[] = 'ssort';
      $vars[] = 'sdir';
      return $vars;
    }
    
    function ss_get_term_id_by_slug ($slug, $taxonomy) {
        global $wpdb;
        
        
        return $id;
    }
    
    function ss_get_current_context_url(){
        global $wp_query;
        global $wpdb;

        $url = '';
        if (is_category()) {
            $cat_id = $wp_query->get('cat');
            $url = get_category_link($cat_id);
        }
        elseif (is_tag()) {
           $tag_name = $wp_query->get('tag');
           $sql = "SELECT ".$wpdb->terms.".term_id FROM $wpdb->term_taxonomy
                                    LEFT JOIN $wpdb->terms
                                    ON (".$wpdb->term_taxonomy.".term_id = ".$wpdb->terms.".term_id)
                                    WHERE ".$wpdb->terms.".slug = '$tag_name'
                                    AND ".$wpdb->term_taxonomy.".taxonomy = 'post_tag'
                                    LIMIT 1";
           $tag_id = $wpdb->get_var($sql);
           $url = get_tag_link($tag_id);
        }
        elseif (is_author()) {
            $author_id = $wp_query->get('author');
            $url = get_author_posts_url($author_id);
        }
        elseif (is_date()) {
            $year = $wp_query->get('year');
            $month = $wp_query->get('monthnum');
            $day = $wp_query->get('day');
            
            if ($wp_query->get('day')) {
                $url = get_day_link( $year,  $month,  $day);
            }
            elseif ($wp_query->get('monthnum')) {
                $url = get_month_link( $year,  $month,  $day);
            }
            elseif ($wp_query->get('year')) {
                $url = get_year_link( $year,  $month,  $day);
            }
        }
        else {
            $url = get_bloginfo('url');
        }
        
        if ($this->ss_use_permalinks()) {
            //ensure trailing slash
            if ("/" != substr($url, -1)) {
                $url = $url . "/";
            }
        }
        return $url;
    }
    
    function ss_use_permalinks () {
        
        $permalink = get_option("permalink_structure");
        
        if ($permalink == "") {
            return false;
        }
        else {
            return true;
        }
    }
    
    function ss_widget_sort ($args){
        extract($args);
        ?>
        <?php echo $before_widget; ?>
          <?php echo $before_title
              . 'Sort By'
              . $after_title; ?>
          <?php $this->placesort(); ?>
        <?php echo $after_widget;
    }
    
    function ss_plugins_loaded () {
        register_sidebar_widget("Smart Sort",array($this, "ss_widget_sort")); 
    }
    
    function placesort() {

        $sort_drop_title = get_option('ss_sort_drop_down_title');
        $saved_options = $this->ss_get_options();
        $ssort = $this->ss_open_ssort();
        $sorthtml = '<select name="ssort" onChange="document.location.href=this.options[this.selectedIndex].value;">';
        
        foreach ($saved_options as $option) {
            $selected = '';
            if ($option['nicekey']."-asc" == $ssort['sort'] . "-" . strtolower($ssort['dir'])) { 
                $selected = "selected"; 
            }
            $sorthtml .= "<option value='".$option['asclink']."' $selected onClick='window.location = \"".$option['asclink']."\"; return false;' >".$option['title']." ". __('ASC')."</option>";
            
            $selected = '';
            if ($option['nicekey']."-desc" == $ssort['sort'] . "-" . strtolower($ssort['dir'])) { 
                $selected = "selected";
            }
            $sorthtml .= "<option value='".$option['desclink']."' $selected onClick='window.location = \"".$option['desclink']."\"; return false;' >".$option['title']." ". __('DESC')."</option>";
        }
        $sorthtml .= '</select>';
        
        echo "$sorthtml";
    }
    
    function ss_add_pages() {
        // Add a new menu under Options:
        add_options_page('WP Smart Sort', 'WP Smart Sort', 8, __FILE__, array($this, 'ss_options_sort_page'));
    }

    function ss_orderby($sort) {

        $ssort = $this->ss_open_ssort();
        if ( (is_tag() OR is_category() OR is_author() OR is_date() OR is_home()) ) {
            
            $sort  = $ssort['sortfield'];
            if ($ssort['isnumerical']) {
                $sort = "(" . $sort . "+0)";
            }
            $dir    = $ssort['dir'];
            return " $sort $dir ";
        }
        
        return $sort;
    }

    function ss_join($jn){
        global $wpdb;
        
        $ssort = $this->ss_open_ssort();    
        
        if ( ($ssort['ismeta'] === true) AND (is_tag() OR is_category() OR is_author() OR is_date() OR is_home()) ) {
            return $jn . "LEFT JOIN $wpdb->postmeta ON ($wpdb->posts".".ID = $wpdb->postmeta".".post_id)";
        }                                                                                                     
                                                                                                              
        return $jn;
    }
    /*
    * Returns an array containing all the saved sort options
    */
    function ss_get_options () {
        
        if (get_option("ss_sort_item")) {
            $compound_options = get_option("ss_sort_item");
            $semi_compound_options = explode("---", $compound_options);
            
            $ss_options = array();
            
            foreach ($semi_compound_options as $semi_o) {
                $option['key'] = "";
                $option['nicekey'] = "";
                $option['numerical'] = "";
                $option['title'] = "";
                
                $compound_key = substr($semi_o,0, strpos($semi_o,"{"));
                
                $compound_key = explode("::", $compound_key);
                $option['nicekey'] = array_pop($compound_key);
                $option['key'] = implode("::", $compound_key);
                $key = $option['nicekey'];
                        
                $key_values = substr($semi_o, (strpos($semi_o,"{")+1), (strrpos($semi_o,"}") - strpos($semi_o,"{")-1) );
                $key_values = explode("::", $key_values);
                
                $option['numerical'] = array_pop($key_values);
                $option['title'] = implode("::", $key_values);
                
                if ($this->ss_use_permalinks()) {
                    $option['asclink'] = $this->ss_get_current_context_url() . 'sort/' . $option['nicekey'] . '-asc/';
                    $option['desclink'] = $this->ss_get_current_context_url() . 'sort/' . $option['nicekey'] . '-desc/';
                } else {
                    $url = parse_url($this->ss_get_current_context_url());
                    $url = $url['scheme'] . "://" . $url['host'] . $url['path'] . "?" . $url['query'];
                    $option['asclink'] = $url . '&ssort=' . $option['nicekey'] . '&sdir=asc';
                    $option['desclink'] = $url . '&ssort=' . $option['nicekey'] . '&sdir=desc';
                }
                $ss_options["$key"] = $option;
            }
        }
        else {
            add_option("ss_sort_item","");
            $ss_options = array();        
        }
            
        return $ss_options;
    }
    
    //Returns the default sort direction for the current context
    function ss_get_default_sort_direction () {
        global $wp_query;
        global $wpdb;
        if (is_category()) {
            
            $sort = $this->ss_multicontext_open_options("categories");
            $cat_id = $wp_query->get('cat');
            if (array_key_exists($cat_id, $sort)) {
                $sortvar = $sort[$cat_id]['sort'];
            }
            else {
                $sortvar = false;
            }
               
        }
        elseif (is_tag()) {
            
            $sort = $this->ss_multicontext_open_options("tags");
            $tag_name = $wp_query->get('tag');
            $sql = "SELECT ".$wpdb->terms.".term_id FROM $wpdb->term_taxonomy
                                    LEFT JOIN $wpdb->terms
                                    ON (".$wpdb->term_taxonomy.".term_id = ".$wpdb->terms.".term_id)
                                    WHERE ".$wpdb->terms.".slug = '$tag_name'
                                    AND ".$wpdb->term_taxonomy.".taxonomy = 'post_tag'
                                    LIMIT 1";
            $tag_id = $wpdb->get_var($sql);
            if (array_key_exists(tag_id, $sort)) {
                $sortvar = $sort[$tag_id]['sort'];
            }
            else {
                $sortvar = false;
            }
          
        }
        else {
            $sortvar = get_option('ss_default_sort_direction');
        }
        
        if ($sortvar === false) {
            $sortvar = get_option('ss_default_sort_direction');
        }        
        
        return $sortvar;
    }
    
    function ss_open_ssort () {
        global $wpdb;
        global $wp_query;
        
        $dir = "";    
        //Direction
        if ($wp_query->get('sdir')) {
           $dir = $wp_query->get('sdir');
        }
        else {
            $dir = "DESC";
        } 
        
        if ($wp_query->get('ssort')) {
           $sortvar = $wp_query->get('ssort');
        }
        else {
            $sortvar = $this->ss_get_default_sort_direction();
        }
        
        $str1 = substr($sortvar, strlen($sortvar)-4, 4);
        $str2 = substr($sortvar, strlen($sortvar)-5, 5);
        if ($str1 == '-asc') {
            $dir = "ASC";
            $sortby = substr($sortvar, 0, strlen($sortvar)-4);
        } elseif ($str2 == "-desc") { //ensures the ASC is at the end of the string which means ASC and not found in the string elsewhere eg sort=nascar-desc
            $dir = "DESC";
            $sortby = substr($sortvar, 0, strlen($sortvar)-5);
        }
        else {
            //$dir = false;
            $sortby = $sortvar;
        }
        
        $ssort['dir'] = $dir;
        $ssort['sort'] = $sortby;
        $ssort['isnumerical'] = false;
        
        $field_type = substr($sortby,-3);
        $saved_options = $this->ss_get_options();
        if ($field_type == "-pm") { // its a meta sort
            $metatable = $wpdb->postmeta;
            $cf_name = $saved_options["$sortby"]['key'];
            $ssort['nicekey'] = $saved_options["$sortby"]['nicekey'];
            $ssort['fieldkey'] = substr($cf_name, strlen($metatable)+1, strlen($cf_name) - strlen($metatable) - 1);
            $ssort['sortfield'] = $metatable . ".meta_value";
            $ssort['ismeta'] = true;
            if ($saved_options["$sortby"]['numerical'] == '1') {
                $ssort['isnumerical'] = true;
            }
        }
        elseif ($field_type == "-pp") { // its a post table sort
            $posttable = $wpdb->posts;
            $cf_name = $saved_options["$sortby"]['key'];
            $ssort['nicekey'] = $saved_options["$sortby"]['nicekey'];
            $ssort['fieldkey'] = substr($cf_name, strlen($posttable)+1, strlen($cf_name) - strlen($posttable) - 1);
            $ssort['sortfield'] = $posttable . "." . $ssort['fieldkey'];
            $ssort['ismeta'] = false;
            if ($saved_options["$sortby"]['numerical'] == '1') {
                $ssort['isnumerical'] = true;
            }
        }
        else {
            $ssort['sortfield'] = $sortby;
            $ssort['ismeta'] = false;
        }
       
        if (!$ssort['sortfield']) {$ssort['sortfield'] = "post_date"; }
        if (!$ssort['dir']) {$ssort['dir'] = "DESC"; }
        if ($ssort['ismeta'] !== true) {$ssort['ismeta'] = false; }
        
        return $ssort;
        
    }
    
    function ss_make_nice_key ($key) {
        global $wpdb;
        $metatable = $wpdb->prefix . "postmeta";
        $posttable = $wpdb->prefix . "posts";
        
        if (substr($key, 0, strlen($metatable)) == $metatable) { // is a meta table key
            $field = substr($key, strlen($metatable), strlen($key) - strlen($metatable) );
            $field = ereg_replace("[^A-Za-z0-9]", "_", $field );
            $new_key = $field . "-pm";
        }
        elseif (substr($key, 0, strlen($posttable)) == $posttable) { //is a post table key
            $field = substr($key, strlen($posttable), strlen($key) - strlen($posttable) );
            $field = ereg_replace("[^A-Za-z0-9]", "_", $field );
            $new_key = $field . "-pp";    
        }
        else { //dunno what it is
            $new_key = $key;
        } 
        
        return $new_key;
    }
    /*
    * Produces a single compounded option
    */
    function ss_make_option ($key, $title, $numerical) {
        $option = $key . "::" . $this->ss_make_nice_key($key) . "{" . $title . "::" . $numerical . "}";
        return $option;
    }

    /*
    * Adds an option to the sort options list
    */
    function ss_add_option ($option) {
        
        if (get_option("ss_sort_item") !== false){
            $current = get_option("ss_sort_item");
            if ($current != '') {
                $new = $current . "---" . $option;
            }
            else {
                $new = $option;
            }
            
            update_option("ss_sort_item", $new);
        }
        else {
            add_option("ss_sort_item", $option);
        }
        
    }
    /*
    * Deletes an option from the sort options list
    */
    function ss_delete_option ($nicekey) {
        
        $saved_options = $this->ss_get_options();
        $newitems = array();
        
        foreach($saved_options as $option){
            if ($option['nicekey'] != $nicekey){
                $newitems[] = $this->ss_make_option($option['key'],$option['title'],$option['numerical']);
            }
        }
        $this->ss_save_total_options($newitems);
        
    }
    /*
    * Saves the sort options to wp_options
    * @param options is an array.
    */
    function ss_save_total_options ($options) {
        
        $total_options = implode("---",$options);
        
        if (get_option("ss_sort_item")){
            update_option("ss_sort_item", $total_options);
        }
        else {
            add_option("ss_sort_item", $total_options);
        }
        
    }
    
    function ss_update_option($sortkey, $newoption) {

        $saved_options = $this->ss_get_options();
        $newitems = array();
        
        foreach($saved_options as $option){
            if ($option['key'] == $sortkey){
                $newitems[] = $newoption;
            }
            else {
                $newitems[] =$this-> ss_make_option($option['key'],$option['title'],$option['numerical']);
            }
        }
        $this->ss_save_total_options($newitems);
        
    }
    
    function ss_multicontext_open_options($type) {
        $all_multi = array();
        
         switch ($type) {
            case "categories":
                $glob = get_option("ss_category_sort_direction");
                
                $saved_options = explode("{}", $glob);
                foreach ($saved_options as $svd) {
                    $tmpvar = explode("~",$svd);
                    $id = $tmpvar[0];
                    $name = $tmpvar[1];
                    $sort = $tmpvar[2];
                    $tmpvar2 = array ( "id"    =>  $id, 
                                        "name"  =>  $name, 
                                        "sort"  =>  $sort
                                        );
                                            
                    $all_multi[$id] = $tmpvar2;            
                }
                break;
            case 'tags':
                $glob = get_option("ss_tag_sort_direction");
                
                $saved_options = explode("{}", $glob);
                foreach ($saved_options as $svd) {
                    $tmpvar = explode("~",$svd);
                    $id = $tmpvar[0];
                    $name = $tmpvar[1];
                    $sort = $tmpvar[2];
                    $tmpvar2 = array ( "id"    =>  $id, 
                                        "name"  =>  $name, 
                                        "sort"  =>  $sort
                                        );
                                            
                    $all_multi[$id] = $tmpvar2;            
                }
                break;
         }
        
        return $all_multi;
    }
    //Created the string to be saved in the dB.
    function ss_multicontext_close_options($type, $options) {
        $savestr = array();
        
        foreach ($options as $option) {
            $savestr[] = $option['id'] . "~" . $option['name'] . "~" . $option['sort'];
        }
        $savestr = implode("{}", $savestr);
        
        //echo "Saving: $savestr <br>";
        if (!(update_option("ss_".$type."_sort_direction",$savestr))) {
            add_option("ss_".$type."_sort_direction",$savestr);
        }
        
        
    }
    
    function ss_multicontext_sort_options ($type) {
        global $wpdb;
        
        $cxv = array();
        
        switch ($type) {
            case "categories":
                $sql = "SELECT term_taxonomy_id, slug, name FROM $wpdb->terms 
                    LEFT JOIN $wpdb->term_taxonomy ON 
                    ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
                    WHERE taxonomy = 'category'";
            
                $categories = $wpdb->get_results($sql);
                $saved = $this->ss_multicontext_open_options($type);
                
                foreach ($categories as $cat) {
                    $tmp = array();
                    $tmp["name"] = $cat->name;
                    $tmp["slug"] = $cat->slug;
                    $tmp["id"] = $cat->term_taxonomy_id;
                    
                    $tmp['sort'] = $this->ss_get_default_sort_direction();
                    foreach ($saved as $ct) {
                        if ($tmp['id'] == $ct['id']) {
                            $tmp['sort'] = $ct['sort'];
                        }
                    }
                    
                    $cxv[$tmp['id']] = $tmp;
                
                }
                
              break;
              
            case "tags":
                  $sql = "SELECT term_taxonomy_id, slug, name FROM $wpdb->terms 
                    LEFT JOIN $wpdb->term_taxonomy ON 
                    ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
                    WHERE taxonomy = 'post_tag'";
                  $tags = $wpdb->get_results($sql);
                  
                  $saved = $this->ss_multicontext_open_options($type);
                
                 foreach ($tags as $tag) {
                    $tmp = array();
                    $tmp["name"] = $tag->name;
                    $tmp["slug"] = $tag->slug;
                    $tmp["id"] = $tag->term_taxonomy_id;
                    
                    $tmp['sort'] = $this->ss_get_default_sort_direction();
                    foreach ($saved as $ct) {
                        if ($tmp['id'] == $ct['id']) {
                            $tmp['sort'] = $ct['sort'];
                        }
                    }
                    
                    $cxv[$tmp['id']] = $tmp;
                
                 }
              break;
         }
         
    
        return $cxv;
    }
    
    //creates the drop down menu for selecting default sort directions
    function ss_make_admin_sort_select($name, $selected) {
        $opt_fields = $this->ss_get_options(); 
        $dsort = "<select name='$name'>";
        
        foreach ($opt_fields as $field) {
            $nicekey = $field['nicekey'];
            $title = $field['title'];
            if ($nicekey . "-asc" == $selected) {
                $sel1 = "selected";
                $sel2 = "";
            }
            elseif ($nicekey . "-desc" == $selected) {
                $sel1 = "";
                $sel2 = "selected";
            }
            else {
                $sel1 = "";
                $sel2 = "";
            }
        
            $dsort .= "<option value='$nicekey-asc' $sel1 >$title ($nicekey) " . __('ASC'). "</option>";
            $dsort .= "<option value='$nicekey-desc' $sel2 >$title ($nicekey) ". __('DESC'). "</option>";
        }
        $dsort .= "</select>";
        
        return $dsort;
        
        
    }
    
    //OPTIONS Sort Page
    function ss_options_sort_page() {
        global $_POST;
        global $_POST;
        global $wpdb;
        
        
        $saved_options = $this->ss_get_options();
         
        if ($_POST['action'] == 'add'){
            if ($_POST['sortkey'] and $_POST['sorttitle']){
                
                $option = $this->ss_make_option($_POST['sortkey'],$_POST['sorttitle'],$_POST['numerical']);
                $this->ss_add_option ($option);
                
            }
        } else if ($_POST['action'] == 'delete'){
            if ($_POST['sortkey']){
                
                $this->ss_delete_option($this->ss_make_nice_key($_POST['sortkey']));
                
            }
        } else if ($_POST['action'] == 'update'){
            if ($_POST['sortkey']){
                
                $option =  $this->ss_make_option($_POST['sortkey'],$_POST['sorttitle'],$_POST['numerical']);
                $this->ss_update_option($_POST['sortkey'], $option);
            }
        } else if ($_POST['action'] == 'categories-update'){
            $options = array();
            foreach ($_POST as $p => $a) {
                if (($a != 'categories-update') AND ($a != __('Save Changes'))) {
                    $option = array();
                    
                    $id = substr($p, strrpos($p, "_") + 1, strlen($p) - (strrpos($p, "_") + 1 ));
                    $name = get_cat_name($id);
                    
                    $option['id'] = $id;
                    $option['name'] = $name;
                    $option['sort'] = $a;
                    
                    $options[] = $option;
                    
                }
            }
            $this->ss_multicontext_close_options('category', $options); 
        }
        
        
        /*
        * Get the available fields from "CUSTOM FIELDS"
        */
        $saved_options = $this->ss_get_options();
        $res    =   $wpdb->get_results("SELECT meta_key FROM $wpdb->postmeta GROUP BY meta_key");
        $meta_fields_html = ''; 
        foreach ($res as $des ) {
            
            $key = $wpdb->prefix."postmeta." . $des->meta_key;
            $showkey = ucwords($des->meta_key);
            $nicekey = $this->ss_make_nice_key($key);
            $html =    '<table class="form-table">';
            $html .=   '<tr valign="top">
                        <th scope="row">' . $showkey . '</th>';
            $html .=   '<td>';
            
            if ( array_key_exists($nicekey, $saved_options) ) {
                
                $html .='
                        <form method="post" action="">
                            <div style="float:right">
                                <input type="hidden" name="sortkey" value="'.$key.'" />
                                <input type="hidden" name="action" value="delete" />                            
                                <input type="submit" class="button"  name="Submit" value="'. __('Remove') .'" />
                            </div>
                        </form>
                        <form method="post" action="">
                            <div style="float:right">
                                <input type="hidden" name="sortkey" value="'.$key.'" />
                                <input type="hidden" name="action" value="update" />
                                <input type="submit" class="button"  name="Submit" value="'. __('Update') .'" />
                            </div>
                        ';
            } else {
                $html .='<form method="post" action="">
                            <div style="float:right">                    
                                <input type="hidden" name="sortkey" value="'.$key.'" />
                                <input type="hidden" name="action" value="add" />
                                <input type="submit" class="button" name="Submit" value="'. __('Add') .'" />
                            </div>
                        ';
            }
            
            
            $html .='<div>';
            $html .='<div>
                     <label for="sorttitle" />'. __('Display Text').'</label>
                     <input type="text" name="sorttitle" value="'.$saved_options[$nicekey]['title'].'" />
                     </div>';
            
            $checked = '';
            if ($saved_options[$nicekey]['numerical'] == '1') {
                $checked = 'checked="checked"';
            }
            
            $html .='<div>
                     <label for="numerical" />'. __('Is Numeric').'</label>
                     <input type="checkbox" name="numerical" value="1" '.$checked.'/>
                     </div>';
                     
            $html .='</div></form></td></tr></table>';    
            $meta_fields_html .= $html;
        }
        
        /*
        * Get the available fields from "Posts Table"
        */
        $res    =   $wpdb->get_results("DESCRIBE $wpdb->posts");    
        $posts_fields_html = ''; 
        foreach ($res as $des ) {
            
            $key = $wpdb->prefix."posts." . $des->Field;
            $showkey = ucwords($des->Field);
            $nicekey = $this->ss_make_nice_key($key);
            $html =    '<table class="form-table">';
            $html .=   '<tr valign="top">
                        <th scope="row">' . $showkey . '</th>';
            $html .=   '<td>';
            
            if ( array_key_exists($nicekey, $saved_options) ) {
                
                $html .='
                        <form method="post" action="">
                            <div style="float:right">
                                <input type="hidden" name="sortkey" value="'.$key.'" />
                                <input type="hidden" name="action" value="delete" />                            
                                <input type="submit" class="button"  name="Submit" value="'. __('Remove') .'" />
                            </div>
                        </form>
                        <form method="post" action="">
                            <div style="float:right">
                                <input type="hidden" name="sortkey" value="'.$key.'" />
                                <input type="hidden" name="action" value="update" />
                                <input type="submit" class="button"  name="Submit" value="'. __('Update') .'" />
                            </div>
                        ';
            } else {
                $html .='<form method="post" action="">
                            <div style="float:right">                    
                                <input type="hidden" name="sortkey" value="'.$key.'" />
                                <input type="hidden" name="action" value="add" />
                                <input type="submit" class="button" name="Submit" value="'. __('Add') .'" />
                            </div>
                        ';
            }
            
            
            $html .='<div>';
            $html .='<div>
                     <label for="sorttitle" />'. __('Display Text').'</label>
                     <input type="text" name="sorttitle" value="'.$saved_options[$nicekey]['title'].'" />
                     </div>';
            
            $checked = '';
            if ($saved_options[$nicekey]['numerical'] == '1') {
                $checked = 'checked="checked"';
            }
            
            $html .='<div>
                     <label for="numerical" />'. __('Is Numeric').'</label>
                     <input type="checkbox" name="numerical" value="1" '. $checked .'/>
                     </div>';
                     
            $html .='</div></form></td></tr></table>';    
            $posts_fields_html .= $html;
        }
        
        ?><div class='wrap'>
                <h2>WP Smart Sort</h2>
                <P align=center>
                <div class="metabox-holder" id="poststuff"> 
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('From Custom Fields'); ?></h3>
                            <div class="inside">
                                <?php echo $meta_fields_html; ?>
                            </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('From WP Posts Table'); ?></h3>
                            <div class="inside">
                                <?php echo $posts_fields_html; ?>
                            </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Other Options'); ?></h3>
                        <div class="inside">
                            <form method="post" action="options.php">
                            <?php wp_nonce_field('update-options'); ?>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('Default Sort Direction'); ?></th>
                                    <td><?php
                                    
                                            //$contexts = $this->ss_multicontext_sort_options();
                                            echo $this->ss_make_admin_sort_select('ss_default_sort_direction', $this->ss_get_default_sort_direction());
                                        ?></td>
                                </tr>
                            </table>

                            <input type="hidden" name="action" value="update" />
                            <input type="hidden" name="page_options" value="ss_default_sort_direction" />

                            <p class="submit">
                                <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
                            </p>
                            </form>
                            
                            <h4><?php _e('Set default sort for individual categories'); ?></h4>
                            <form method="post" action="">
                            <table class="form-table">
                                   <?php
                                        $categories = $this->ss_multicontext_sort_options("categories");
                                        foreach ($categories as $cat) {
                                            $cat_html .= "<tr valign='top'><th scope='row'>". $cat['name'] . "</th><td>";
                                            $cat_html .= $this->ss_make_admin_sort_select('ss_default_cat_sort_'.$cat['id'], $cat['sort']);
                                            $cat_html .= "</td></tr>";                                
                                        }
                                        echo $cat_html;
                                   ?>
                            </table>
                            <input type="hidden" name="action" value="categories-update" />
                            <p class="submit">
                                <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
                            </p>
                            </form>
                        </div>
                    </div>                                
                </div>
                </p>
            </div><?php 
    }

    function ss_where($where){
        global $wpdb, $wp_query;
        
        $ssort = $this->ss_open_ssort(); 
        
        if ( ($ssort['ismeta'] === true) AND (is_tag() OR is_category() OR is_author() OR is_date() OR is_home() ) ) {
            $where = $where ." AND ". $wpdb->postmeta.".meta_key = '".$ssort['fieldkey']."' ";
        }
        return $where;
    }
}


/*
ALL the ACTIONS / FILTERS / HOOKS
*/

$wpss = new WP_Smart_Sort();

add_action('admin_menu',    array($wpss, 'ss_add_pages'));
add_filter('posts_orderby', array($wpss, 'ss_orderby'));
add_filter('posts_where',   array($wpss, 'ss_where'));
add_filter('posts_join',    array($wpss, 'ss_join'));
add_action('init',          array($wpss, 'ss_init'));

add_action('plugins_loaded',         array($wpss, 'ss_plugins_loaded'));
add_filter('query_vars',             array($wpss, 'ss_insert_my_rewrite_query_vars'));
add_filter('generate_rewrite_rules', array($wpss, 'ss_rewrite'));

?>