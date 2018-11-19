<?php


function wpbooklist_mobile_page_post_action_javascript() { 
	?>
  	<script type="text/javascript" >
  	"use strict";
  	jQuery(document).ready(function($) {
	  	$("#wpbooklist-mobile-admin-savesettings-button").click(function(event){

	  		$('.wpbooklist-spinner').animate({'opacity':'1'});

	  		var roles = '';
	  		$('.wpbooklist-mobile-rolescheckboxes').each(function(){
	  			if($(this).prop('checked')){
	  				roles = roles+$(this).val()+'---';
	  			}
	  		});

	  		var libs = '';
	  		$('.wpbooklist-mobile-libscheckboxes').each(function(){
	  			if($(this).prop('checked')){
	  				libs = libs+$(this).val()+'---';
	  			}
	  		});

		  	var data = {
				'action': 'wpbooklist_mobile_page_post_action',
				'security': '<?php echo wp_create_nonce( "wpbooklist_mobile_page_post_action_callback" ); ?>',
				'roles':roles,
				'libs':libs
			};
			console.log(data);

	     	var request = $.ajax({
			    url: ajaxurl,
			    type: "POST",
			    data:data,
			    timeout: 0,
			    success: function(response) {
			    	if(response == 1){
			    		$('#wpbooklist-mobile-success-div').html('<span id="wpbooklist-add-book-success-span">Success!</span><br/><br/> You\'ve updated your Mobile App Settings!<div id="wpbooklist-addstylepak-success-thanks">Thanks for using WPBooklist! If you happen to be thrilled with WPBookList, then by all means, <a id="wpbooklist-addbook-success-review-link" href="https://wordpress.org/support/plugin/wpbooklist/reviews/?filter=5">Feel Free to Leave a 5-Star Review Here!</a><img id="wpbooklist-smile-icon-1" src="http://evansclienttest.com/wp-content/plugins/wpbooklist/assets/img/icons/smile.png"></div>');
			    	}
			    	document.location.reload(true);
			    },
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
		            console.log(textStatus);
		            console.log(jqXHR);
				}
			});

			event.preventDefault ? event.preventDefault() : event.returnValue = false;
	  	});
	});
	</script>
	<?php
}

// Callback function for creating backups
function wpbooklist_mobile_page_post_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_mobile_page_post_action_callback', 'security' );
	$roles = filter_var($_POST['roles'],FILTER_SANITIZE_STRING);
	$libs = filter_var($_POST['libs'],FILTER_SANITIZE_STRING);

	$table_name = $wpdb->prefix.'wpbooklist_jre_mobile_table';

	$data = array(
		'rolesallowed' => $roles,
		'excludedlibs' => $libs
	);
	$format = array( '%s', '%s'); 
	$where = array( 'ID' => 1 );
	$where_format = array( '%d' );
	echo $wpdb->update( $table_name, $data, $where, $format, $where_format );
	wp_die();
}


/*
 * Below is a mobile ajax function and callback, 
 * complete with console.logs and echos to verify functionality
 */

/*
// For adding a book from the admin dashboard
add_action( 'admin_footer', 'wpbooklist_mobile_page_post_action_javascript' );
add_action( 'wp_ajax_wpbooklist_mobile_page_post_action', 'wpbooklist_mobile_page_post_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_mobile_page_post_action', 'wpbooklist_mobile_page_post_action_callback' );


function wpbooklist_mobile_page_post_action_javascript() { 
	?>
  	<script type="text/javascript" >
  	"use strict";
  	jQuery(document).ready(function($) {
	  	$("#wpbooklist-admin-addbook-button").click(function(event){

		  	var data = {
				'action': 'wpbooklist_mobile_page_post_action',
				'security': '<?php echo wp_create_nonce( "wpbooklist_mobile_page_post_action_callback" ); ?>',
			};
			console.log(data);

	     	var request = $.ajax({
			    url: ajaxurl,
			    type: "POST",
			    data:data,
			    timeout: 0,
			    success: function(response) {
			    	console.log(response);
			    },
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
		            console.log(textStatus);
		            console.log(jqXHR);
				}
			});

			event.preventDefault ? event.preventDefault() : event.returnValue = false;
	  	});
	});
	</script>
	<?php
}

// Callback function for creating backups
function wpbooklist_mobile_page_post_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_mobile_page_post_action_callback', 'security' );
	//$var1 = filter_var($_POST['var'],FILTER_SANITIZE_STRING);
	//$var2 = filter_var($_POST['var'],FILTER_SANITIZE_NUMBER_INT);
	echo 'hi';
	wp_die();
}*/




?>