<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: WPBookList Mobile Extension
Plugin URI: https://www.jakerevans.com
Description: The WPBookList Mobile App Extension - Add your books quickly and easily by simply scanning your book's barcode with your smartphone!
Version: 1
Author: Jake Evans - Forward Creation
Author URI: https://www.jakerevans.com
License: GPL2
*/ 

#INSTRUCTIONS
// 1. Choose a 1 word descriptor for this extension - no spaces, underscores, or hyphens	
// 2. Replace every instance of 'Mobile', both lowercase and caps, with a your one-word descriptor.
// 3. Replace every instance of 'Mobile' in the file and foldernames with your one-word descriptor.
// 4. Remove the '(submenu)' from the folder name
// 
//		
// 5. Upload and active like any other plugin

global $wpdb;
require_once('includes/mobile-functions.php');
require_once('includes/mobile-ajaxfunctions.php');

// Root plugin folder directory.
if ( ! defined('WPBOOKLIST_VERSION_NUM' ) ) {
	define( 'WPBOOKLIST_VERSION_NUM', '6.1.2' );
}

// This Extension's Version Number.
define( 'WPBOOKLIST_MOBILE_VERSION_NUM', '6.1.2' );

// Root plugin folder URL of this extension
define('MOBILE_ROOT_URL', plugins_url().'/wpbooklist-mobile/');

// Grabbing dasubmenuase prefix
define('MOBILE_PREFIX', $wpdb->prefix);

// Root plugin folder directory for this extension
define('MOBILE_ROOT_DIR', plugin_dir_path(__FILE__));

// Root WordPress Plugin Directory.
define( 'MOBILE_ROOT_WP_PLUGINS_DIR', str_replace( '/wpbooklist-mobile', '', plugin_dir_path( __FILE__ ) ) );

// Root WPBL Dir.
if ( ! defined('ROOT_WPBL_DIR' ) ) {
	define( 'ROOT_WPBL_DIR', MOBILE_ROOT_WP_PLUGINS_DIR . 'wpbooklist/' );
}

// Root WPBL Url.
if ( ! defined('ROOT_WPBL_URL' ) ) {
	define( 'ROOT_WPBL_URL', plugins_url() . '/wpbooklist/' );
}

// Root WPBL Classes Dir.
if ( ! defined('ROOT_WPBL_CLASSES_DIR' ) ) {
	define( 'ROOT_WPBL_CLASSES_DIR', ROOT_WPBL_DIR . 'includes/classes/' );
}

// Root WPBL Transients Dir.
if ( ! defined('ROOT_WPBL_TRANSIENTS_DIR' ) ) {
	define( 'ROOT_WPBL_TRANSIENTS_DIR', ROOT_WPBL_CLASSES_DIR . 'transients/' );
}

// Root WPBL Translations Dir.
if ( ! defined('ROOT_WPBL_TRANSLATIONS_DIR' ) ) {
	define( 'ROOT_WPBL_TRANSLATIONS_DIR', ROOT_WPBL_CLASSES_DIR . 'translations/' );
}

// Root WPBL Root Img Icons Dir.
if ( ! defined('ROOT_WPBL_IMG_ICONS_URL' ) ) {
	define( 'ROOT_WPBL_IMG_ICONS_URL', ROOT_WPBL_URL . 'assets/img/icons/' );
}

// Root WPBL Root Utilities Dir.
if ( ! defined('ROOT_WPBL_UTILITIES_DIR' ) ) {
	define( 'ROOT_WPBL_UTILITIES_DIR', ROOT_WPBL_CLASSES_DIR . 'utilities/' );
}

// Root WPBL Dir.
if ( ! defined('ROOT_WPBL_DIR' ) ) {
	define( 'ROOT_WPBL_DIR', COMMENTS_ROOT_WP_PLUGINS_DIR . 'wpbooklist/' );
}

// Root Image Icons URL of this extension
define('MOBILE_ROOT_IMG_ICONS_URL', MOBILE_ROOT_URL.'assets/img/');

// Root Classes Directory for this extension
define('MOBILE_CLASS_DIR', MOBILE_ROOT_DIR.'includes/classes/');

// Root CSS URL for this extension
define('MOBILE_ROOT_CSS_URL', MOBILE_ROOT_URL.'assets/css/');

// Adding the front-end ui css file for this extension
add_action('wp_enqueue_scripts', 'wpbooklist_jre_mobile_frontend_ui_style');

// Adding the admin css file for this extension
add_action('admin_enqueue_scripts', 'wpbooklist_jre_mobile_admin_style' );

// Registers table names
add_action( 'init', 'wpbooklist_jre_register_mobile_table_name', 1 );

// Creates tables upon activation
register_activation_hook( __FILE__, 'wpbooklist_jre_create_mobile_tables' );

// For saving the page/post creation options
add_action( 'admin_footer', 'wpbooklist_mobile_page_post_action_javascript' );
add_action( 'wp_ajax_wpbooklist_mobile_page_post_action', 'wpbooklist_mobile_page_post_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_mobile_page_post_action', 'wpbooklist_mobile_page_post_action_callback' );

// Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
register_activation_hook( __FILE__, 'wpbooklist_mobile_core_plugin_required' );

/*
 * Function that utilizes the filter in the core WPBookList plugin, resulting in a new submenu. Possible options for the first argument in the 'Add_filter' function below are:
 *  - 'wpbooklist_add_submenu_books'
 *  - 'wpbooklist_add_submenu_display'
 *
 *
 *
 * The instance of "Mobile" in the $extra_submenu array can be replaced with whatever you want - but the 'mobile' instance MUST be your one-word descriptor.
*/
add_filter('wpbooklist_add_sub_menu', 'wpbooklist_mobile_submenu');
function wpbooklist_mobile_submenu($submenu_array) {
 	$extra_submenu = array(
		'Mobile'
	);
 
	// combine the two arrays
	$submenu_array = array_merge($submenu_array,$extra_submenu);
	return $submenu_array;
}

?>