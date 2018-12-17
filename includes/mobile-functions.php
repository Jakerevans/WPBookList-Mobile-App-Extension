<?php

/**
 * Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
 */
function wpbooklist_mobile_core_plugin_required() {

  // Require core WPBookList Plugin.
  if ( ! is_plugin_active( 'wpbooklist/wpbooklist.php' ) && current_user_can( 'activate_plugins' ) ) {

    // Stop activation redirect and show error.
    wp_die( 'Whoops! This WPBookList Extension requires the Core WPBookList Plugin to be installed and activated! <br><a target="_blank" href="https://wordpress.org/plugins/wpbooklist/">Download WPBookList Here!</a><br><br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
  }
}

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

  $table = $wpdb->prefix."wpbooklist_jre_mobile_table";
  if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {

    // Call this manually as we may have missed the init hook
    wpbooklist_jre_register_mobile_table_name();
    //Creating the table
    $sql_create_table1 = "CREATE TABLE {$wpdb->wpbooklist_jre_mobile_table} 
    (
          ID bigint(255) auto_increment,
          rolesallowed varchar(255) NOT NULL DEFAULT 'Everyone---',
          excludedlibs varchar(255),
          PRIMARY KEY  (ID),
            KEY rolesallowed (rolesallowed)
    ) $charset_collate; ";
    dbDelta( $sql_create_table1 );

    $table_name = $wpdb->prefix . 'wpbooklist_jre_mobile_table';
    $wpdb->insert( $table_name, array('ID' => 1)); 

  } else {
    $table = $wpdb->prefix."wpbooklist_jre_mobile_table";

    if($wpdb->query("SHOW COLUMNS FROM `$table` LIKE 'rolesallowed'") == 0){
       $wpdb->query("ALTER TABLE $table ADD rolesallowed varchar(255) NOT NULL DEFAULT 'Everyone---'");
    }

    if($wpdb->query("SHOW COLUMNS FROM `$table` LIKE 'excludedlibs'") == 0){
       $wpdb->query("ALTER TABLE $table ADD excludedlibs varchar(255)");
    }
  }
}

// Connection check and getter for all table names for the REST API 
add_action( 'rest_api_init', function () {
  register_rest_route( 'wpbooklistmobile/', '/connectcheck/(?P<notice>[a-z0-9\-]+)', array(
    'methods' => 'GET',
    'callback' => 'wpbooklist_jre_rest_api_connection_check',
  ) );
});

// Callback function for adding a book via REST API. Pass in the ISBN number and the table name.
function wpbooklist_jre_rest_api_connection_check( $data ){
  global $wpdb;

  $table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
  $db_row = $wpdb->get_results("SELECT * FROM $table_name");



  $table_mobile = $wpdb->prefix.'wpbooklist_jre_mobile_table';
  $row = $wpdb->get_row("SELECT * from $table_mobile");

  if(strpos($row->excludedlibs, '---') !== false){
    $excluded_libs_array = explode('---', $row->excludedlibs);
  } else {
    $excluded_libs_array = array();
  }

  $table_string = '';
  $default_flag = false;
  foreach($db_row as $key => $db){
    if(($db->user_table_name != "") || ($db->user_table_name != null)){


      $exclude_flag = false;
      foreach ($excluded_libs_array as $key => $excludedlib) {

        if($excludedlib == $db->user_table_name) {
          $exclude_flag = true;
        }

        if($excludedlib == 'wpbooklist_jre_saved_book_log'){
          $default_flag = true;
        }
      }
     

      if($exclude_flag == false){
        $table_string = $table_string.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.',';
      }
    }
  }


  if($default_flag == false){
    $table_string = $wpdb->prefix.'wpbooklist_jre_saved_book_log'.','.$table_string;
  }

  // Trimming any possible trailing whitespace or commas
  $table_string = rtrim($table_string);
  $table_string = ltrim($table_string);
  $table_string = rtrim($table_string, ',');
  $table_string = ltrim($table_string, ',');

  return $table_string;
}

// Connection check and getter for all table names for the REST API 
add_action( 'rest_api_init', function () {
  register_rest_route( 'wpbooklistmobile/', '/connectcheck/auth/(?P<username>[a-z0-9\@.]+)', array(
    'methods' => 'GET',
    'callback' => 'wpbooklist_jre_rest_api_connection_auth_check',
  ) );
});

// Callback function for adding a book via REST API. Pass in the ISBN number and the table name.
function wpbooklist_jre_rest_api_connection_auth_check( $data ){
  global $wpdb;

  // Getting user's role(s) based on their registered E-Mail address
  $username = filter_var($data['username'], FILTER_SANITIZE_EMAIL);
  $roles_string = '';
  $user = get_user_by( 'email', $username );

  if(!$user){
    $roles_string = 'Looks like we couldn\'t find a registered WordPress user with an E-mail address of '.$username.'!';
  } else {

    $user_id = $user->ID;
    $user_meta = get_userdata($user_id);
    $user_roles = $user_meta->roles; //array of roles the user is part of.

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

    foreach ($user_roles as $key => $value) {
        $roles_string = $roles_string.$value.'---';
    }

  }

  return $roles_string;




}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wpbooklist/v1', '/addbook/(?P<isbn>[a-z0-9\-]+)/(?P<page>[a-z0-9\-]+)/(?P<post>[a-z0-9\-]+)/(?P<table>[a-z0-9\_]+)', array(
      'methods' => 'GET',
      'callback' => 'wpbooklist_jre_rest_api_integration',
    ));
});

// Callback function for adding a book via REST API. Pass in the ISBN number and the table name.
function wpbooklist_jre_rest_api_integration( $data ){
  global $wpdb;

  $table_name_options = $wpdb->prefix . 'wpbooklist_jre_mobile_table';
  $options_results = $wpdb->get_row("SELECT * FROM $table_name_options");

  // Grab data from the REST API endpoint sent from the app
  $isbn = filter_var($data['isbn'], FILTER_SANITIZE_STRING);
  $page = filter_var($data['page'], FILTER_SANITIZE_STRING);
  $post = filter_var($data['post'], FILTER_SANITIZE_STRING);

  if($page == 'yes' || $page == 'Yes'){
    $page = 'true';
  }

  if($post == 'yes' || $post == 'Yes'){
    $post = 'true';
  }

  $which_table = filter_var($data['table'], FILTER_SANITIZE_STRING);
  $before_count = $wpdb->get_var("SELECT COUNT(*) FROM $which_table");

  $book_array = array(
      'library' => $which_table,
      'use_amazon_yes' => 'true',
      'amazonauth' => 'true',
      'isbn' => $isbn,
      'page_yes' => $page,
      'post_yes' => $post,
  );

  require_once(CLASS_BOOK_DIR.'class-wpbooklist-book.php');
  $book_class = new WPBookList_Book('add', $book_array, null);
  $insert_result = $book_class->add_result;
  $after_count = $wpdb->get_var("SELECT COUNT(*) FROM $which_table");

  if($after_count == ($before_count+1)){

    $new_book = $wpdb->get_row($wpdb->prepare("SELECT * FROM $which_table WHERE book_uid = %s", $book_class->book_uid));

    return json_encode($new_book);
  }
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wpbooklist/v1', '/getbooks/(?P<table>[a-z0-9\_]+)', array(
      'methods' => 'GET',
      'callback' => 'wpbooklist_jre_rest_api_grab_books',
    ));
});

// Callback function for adding a book via REST API. Pass in the ISBN number and the table name.
function wpbooklist_jre_rest_api_grab_books( $data ){
  global $wpdb;

  // Grab data from the REST API endpoint sent from the app
  $table = filter_var($data['table'], FILTER_SANITIZE_STRING);

  $all_books_count = $wpdb->get_var("SELECT count(*) FROM $table");

  // Get the Library with a limit of the first 20
  $all_books = $wpdb->get_results("SELECT * FROM $table LIMIT 20");

  return json_encode($all_books).'--sep--'.$all_books_count;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wpbooklist/v1', '/getbookswithoffset/(?P<table>[a-z0-9\_]+)/(?P<offset>[0-9\_]+)', array(
      'methods' => 'GET',
      'callback' => 'wpbooklist_jre_rest_api_grab_books_with_offset',
    ));
});

// Callback function for adding a book via REST API. Pass in the ISBN number and the table name.
function wpbooklist_jre_rest_api_grab_books_with_offset( $data ){
  global $wpdb;

  // Grab data from the REST API endpoint sent from the app
  $table = filter_var($data['table'], FILTER_SANITIZE_STRING);

  // Grab data from the REST API endpoint sent from the app
  $offset = filter_var($data['offset'], FILTER_SANITIZE_NUMBER_INT);

  // Get the Library
  $all_books = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table LIMIT 20 OFFSET %d", $offset));


  return json_encode($all_books);
}


// Connection check and getter for all table names for the REST API 
add_action( 'rest_api_init', function () {
  register_rest_route( 'wpbooklistmobile/', '/check/roles/(?P<notice>[a-z0-9\-]+)', array(
    'methods' => 'GET',
    'callback' => 'wpbooklist_jre_rest_api_role_call',
  ) );
});

// Callback function for adding a book via REST API. Pass in the ISBN number and the table name.
function wpbooklist_jre_rest_api_role_call( $data ){
  global $wpdb;
  $table = $wpdb->prefix.'wpbooklist_jre_mobile_table';
  $row = $wpdb->get_row("SELECT * from $table");
  return strtolower($row->rolesallowed);
}



?>