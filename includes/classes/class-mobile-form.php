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

    global $wpdb;
    $table = $wpdb->prefix.'wpbooklist_jre_mobile_table';
    $row = $wpdb->get_row("SELECT * from $table");

		$string1 = '<div id="wpbooklist-mobile-div">
                <p>To begin using the <span class="wpbooklist-color-orange-italic">Official WPBookList Companion App</span>, simply download the app from the <a target="_blank" href="https://itunes.apple.com/us/app/wordpress-book-list/id1169658155?mt=8">App Store</a> or the <a target="_blank" href="https://play.google.com/store/apps/details?id=com.ionicframework.wpbooklist395839&amp;hl=en">Google Play Store</a>, follow the simple instructions within, and start adding books to your library!</p>
                <div id="wpbooklist-mobile-page-post">
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
                </div>
                  <a href="https://play.google.com/store/apps/details?id=com.ionicframework.wpbooklist395839&amp;hl=en&amp;utm_source=global_co&amp;utm_medium=prtnr&amp;utm_content=Mar2515&amp;utm_campaign=PartBadge&amp;pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1"><img width="150" alt="Get it on Google Play" src="https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png"></a>
                  <a target="_blank" href="https://itunes.apple.com/us/app/wordpress-book-list/id1169658155?mt=8"><img style="position:relative; bottom:9px;" src="'.ROOT_IMG_URL.'appstore.svg"></a>
                </div>';
			            
    		return $string1.$string2.$string3.$string4.$string5;
	}
}

endif;