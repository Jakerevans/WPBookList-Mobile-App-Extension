<?php
/**
 * WPBookList WPBookList_Mobile_Form Submenu Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Mobile_Form', false ) ) :
/**
 * WPBookList_Mobile_Form Class.
 */
class WPBookList_Mobile_Form {

	public static function output_mobile_form(){

    $string1 = '';
    $string2 = '';
    $string3 = '';
    $string4 = '';
    $string5 = '';

    global $wpdb;
    $table = $wpdb->prefix.'wpbooklist_jre_mobile_table';
    $row = $wpdb->get_row("SELECT * from $table");


    // Getting saved values and formatting for use
    if(strpos($row->rolesallowed, '---') !== false){
      $roles_allowed_array = explode('---', $row->rolesallowed);
    } else {
      $roles_allowed_array = array();
    }

    if(strpos($row->excludedlibs, '---') !== false){
      $excluded_libs_array = explode('---', $row->excludedlibs);
    } else {
      $excluded_libs_array = array();
    }

    // Getting all available Roles
    global $wp_roles;
    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);


    // building the Roles checbox section
    if(array_search('Everyone', $roles_allowed_array) !== false){
      $checkboxes = '<div><input checked class="wpbooklist-mobile-rolescheckboxes" type="checkbox" value="Everyone"></input>'.'<label>Everyone</label></div>';
    } else {
      $checkboxes = '<div><input class="wpbooklist-mobile-rolescheckboxes" type="checkbox" value="Everyone"></input>'.'<label>Everyone</label></div>';
    }

    foreach ($editable_roles as $key => $value) {

        $selected = '';
        foreach ($roles_allowed_array as $key2 => $value2) {
          if($value2 == $value['name']){
            $selected = 'checked';
            break;
          }
        }

        $checkboxes = $checkboxes.'<div><input class="wpbooklist-mobile-rolescheckboxes" '.$selected.' type="checkbox" value="'.$value['name'].'"></input>'.'<label>'.$value['name'].'s</label></div>';
    }

    // Getting all user-created libraries
    $table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
    $db_row = $wpdb->get_results("SELECT * FROM $table_name");


    if(array_search('wpbooklist_jre_saved_book_log', $excluded_libs_array) !== false){
      $libraries = '<div><input class="wpbooklist-mobile-libscheckboxes" checked type="checkbox" value="wpbooklist_jre_saved_book_log"></input>'.'<label>The Default Library</label></div>';
    } else {
      $libraries = '<div><input class="wpbooklist-mobile-libscheckboxes" type="checkbox" value="wpbooklist_jre_saved_book_log"></input>'.'<label>The Default Library</label></div>';
    }
    
    foreach($db_row as $db){

        $selected = '';
        foreach ($excluded_libs_array as $key3 => $value3) {
          if($value3 == $db->user_table_name){
            $selected = 'checked';
            break;
          }
        }

        $libraries = $libraries.'<div><input class="wpbooklist-mobile-libscheckboxes" '.$selected.' type="checkbox" value="'.$db->user_table_name.'"></input>'.'<label>'.$db->user_table_name.'</label></div>';
    }



		$string1 = '<div id="wpbooklist-mobile-div">
                <p>To begin using the <span class="wpbooklist-color-orange-italic">Official WPBookList Companion App</span>, simply download the app from the <a target="_blank" href="https://itunes.apple.com/us/app/wordpress-book-list/id1169658155?mt=8">App Store</a> or the <a target="_blank" href="https://play.google.com/store/apps/details?id=com.ionicframework.wpbooklist395839&amp;hl=en">Google Play Store</a>, follow the simple instructions within, and start adding books to your library!</p>
                <div id="wpbooklist-mobile-page-post">

                <div id="wpbooklist-mobile-users-div">
                  <label class="wpbooklist-mobile-title">Who should be allowed to add books with the WPBookList Mobile App?</label>
                  <div id="wpbooklist-mobile-users-check-div">
                    <div id="wpbooklist-mobile-users-check-div-checkboxes">
                      '.$checkboxes.'
                    </div>
                  </div>
                </div>
                <div id="wpbooklist-mobile-users-div">
                  <label class="wpbooklist-mobile-title">Which Libraries should be <em>excluded</em> from appearing in the WPBookList Mobile App?</label>
                  <div id="wpbooklist-mobile-users-check-div">
                    <div id="wpbooklist-mobile-users-check-div-checkboxes">
                      '.$libraries.'
                    </div>
                  </div>
                </div>
                <button type="button" id="wpbooklist-mobile-admin-savesettings-button">'.__('Save Settings','wpbooklist').'</button>
                <div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
                <div id="wpbooklist-settings-success-div" data-bookid="" data-booktable="">

                </div>
                </div>
                  <a href="https://play.google.com/store/apps/details?id=com.ionicframework.wpbooklist395839&amp;hl=en&amp;utm_source=global_co&amp;utm_medium=prtnr&amp;utm_content=Mar2515&amp;utm_campaign=PartBadge&amp;pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1"><img width="150" alt="Get it on Google Play" src="https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png"></a>
                  <a target="_blank" href="https://itunes.apple.com/us/app/wordpress-book-list/id1169658155?mt=8"><img style="position:relative; bottom:9px;" src="'.ROOT_IMG_URL.'appstore.svg"></a>
                </div>';
			            
    		return $string1.$string2.$string3.$string4.$string5;
	}
}



/*
<div>
<label>Create a Page for each Book Scanned?</label>';

if($row->createpage != null && $row->createpage != 'false'){
  $string2 = '<input checked id="wpbooklist-jre-mobile-create-page" name="wpbooklist-jre-mobile-create-page" type="checkbox">';
} else {
  $string2 = '<input id="wpbooklist-jre-mobile-create-page" name="wpbooklist-jre-mobile-create-page" type="checkbox">';
} 

$string3 = '</div>
<div>
<label>Create a Post for each Book Scanned?</label>';

if($row->createpost != null && $row->createpost != 'false'){
  $string4 = '<input checked id="wpbooklist-jre-mobile-create-post" name="wpbooklist-jre-mobile-create-post" type="checkbox">';
} else {
  $string4 = '<input id="wpbooklist-jre-mobile-create-post" name="wpbooklist-jre-mobile-create-post" type="checkbox">';
} 

$string5 =  '</div> 
<button id="wpbooklist-submit-mobile" type="button">Save Mobile App Settings</button> 
<div class="wpbooklist-spinner" id="wpbooklist-spinner-mobile"></div>
<div id="wpbooklist-mobile-success-div"></div>  

*/

endif;