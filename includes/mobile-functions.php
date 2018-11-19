<?php

// Adding the front-end ui css file for this extension
function wpbooklist_jre_mobile_frontend_ui_style() {
    wp_register_style( 'wpbooklist-mobile-frontend-ui', MOBILE_ROOT_CSS_URL.'mobile-frontend-ui.css' );
    wp_enqueue_style('wpbooklist-mobile-frontend-ui');
}

// Code for adding the general admin CSS file
function wpbooklist_jre_mobile_admin_style() {
  if(current_user_can( 'administrator' )){
      wp_register_style( 'wpbooklist-mobile-admin-ui', MOBILE_ROOT_CSS_URL.'mobile-admin-ui.css');
      wp_enqueue_style('wpbooklist-mobile-admin-ui');
  }
}



// Function to add table names to the global $wpdb
function wpbooklist_jre_register_mobile_table_name() {
    global $wpdb;
    $wpdb->wpbooklist_jre_mobile_table = "{$wpdb->prefix}wpbooklist_jre_mobile_table";
}

// Runs once upon plugin activation and creates tables
function wpbooklist_jre_create_mobile_tables() {
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  global $wpdb;
  global $charset_collate; 

  // Call this manually as we may have missed the init hook
  wpbooklist_jre_register_mobile_table_name();
  //Creating the table
  $sql_create_table1 = "CREATE TABLE {$wpdb->wpbooklist_jre_mobile_table} 
  (
        ID bigint(255) auto_increment,
        createpage varchar(255),
        createpost varchar(255),
        PRIMARY KEY  (ID),
          KEY createpage (createpage)
  ) $charset_collate; ";
  dbDelta( $sql_create_table1 );

  $table_name = $wpdb->prefix . 'wpbooklist_jre_mobile_table';
  $wpdb->insert( $table_name, array('ID' => 1)); 
}

// Simple connection check for the REST API 
add_action( 'rest_api_init', function () {
  register_rest_route( 'wpbooklistmobile/', '/connectcheck/(?P<notice>[a-z0-9\-]+)', array(
    'methods' => 'GET',
    'callback' => 'wpbooklist_jre_rest_api_connection_check',
  ) );
});

// Callback function for adding a book via REST API. Pass in the ISBN number and the table name.
function wpbooklist_jre_rest_api_connection_check( $data ){
  return 1;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'myplugin/v1', '/author/(?P<isbn>\d+)/(?P<default>[a-z0-9\-]+)/(?P<table>[a-z0-9\-]+)', array(
      'methods' => 'GET',
      'callback' => 'wpbooklist_jre_rest_api_integration',
    ));
});

// Callback function for adding a book via REST API. Pass in the ISBN number and the table name.
function wpbooklist_jre_rest_api_integration( $data ){
  global $wpdb;

  $table_name_options = $wpdb->prefix . 'wpbooklist_jre_mobile_table';
  $options_results = $wpdb->get_row("SELECT * FROM $table_name_options");

  $isbn = filter_var($data['isbn'], FILTER_SANITIZE_NUMBER_INT);

  // Inserting final values into the WordPress database
  if(filter_var($data['default'], FILTER_SANITIZE_STRING) == 'false'){
    $which_table = $wpdb->prefix.'wpbooklist_jre_saved_book_log';
  } else {
    $which_table = $wpdb->prefix.'wpbooklist_jre_'.filter_var($data['table'], FILTER_SANITIZE_STRING);
  }

  $book_array = array(
      'amazon_auth_yes' => $amazon_auth_yes,
      'library' => $which_table,
      'use_amazon_yes' => 'true',
      'amazon_auth_yes' => 'true',
      'isbn' => $isbn,
      'page_yes' => $options_results->createpage,
      'post_yes' => $options_results->createpost,
  );

  require_once(CLASS_DIR.'class-book.php');
  $book_class = new WPBookList_Book('add', $book_array, null);
  $insert_result = $book_class->add_result;
}

?>