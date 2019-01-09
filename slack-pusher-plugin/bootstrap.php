<?php
/*
Plugin Name: Slack Pusher Plugin
Plugin URI:  https://github.com/iandunn/WordPress-Plugin-Skeleton
Description: The skeleton for an object-oriented/MVC WordPress plugin
Version:     0.4a
Author:      Ian Dunn
Author URI:  http://iandunn.name
*/

/*
 * This plugin was built on top of WordPress-Plugin-Skeleton by Ian Dunn.
 * See https://github.com/iandunn/WordPress-Plugin-Skeleton for details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

define( 'WPPS_NAME',                 'Slack Pusher Plugin' );
define( 'WPPS_REQUIRED_PHP_VERSION', '5.3' );                          // because of get_called_class()
define( 'WPPS_REQUIRED_WP_VERSION',  '4.9' );                          // because of esc_textarea()

/**
 * Checks if the system requirements are met
 *
 * @return bool True if system requirements are met, false if not
 */
function wpps_requirements_met() {
	global $wp_version;
	//require_once( ABSPATH . '/wp-admin/includes/plugin.php' );		// to get is_plugin_active() early

	if ( version_compare( PHP_VERSION, WPPS_REQUIRED_PHP_VERSION, '<' ) ) {
		return false;
	}

	if ( version_compare( $wp_version, WPPS_REQUIRED_WP_VERSION, '<' ) ) {
		return false;
	}

	/*
	if ( ! is_plugin_active( 'plugin-directory/plugin-file.php' ) ) {
		return false;
	}
	*/

	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 */
function wpps_requirements_error() {
	global $wp_version;

	require_once( dirname( __FILE__ ) . '/views/requirements-error.php' );
}
add_action( 'admin_menu', 'spp_add_admin_menu' );
add_action( 'admin_init', 'spp_settings_init' );






   function spp_add_admin_menu(  ) { 

	add_options_page( 'Slack Pusher Plugin', 'Slack Pusher Plugin', 'manage_options', 'slack_pusher_plugin', 'spp_options_page' );

}


function spp_settings_init(  ) { 

	register_setting( 'pluginPage', 'spp_settings' );

	add_settings_section(
		'spp_pluginPage_section', 
		__( 'Your section description', 'wordpress' ), 
		'spp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'spp_text_field_0', 
		__( 'Settings field description', 'wordpress' ), 
		'spp_text_field_0_render', 
		'pluginPage', 
		'spp_pluginPage_section' 
	);

	add_settings_field( 
		'spp_checkbox_field_1', 
		__( 'Settings field description', 'wordpress' ), 
		'spp_checkbox_field_1_render', 
		'pluginPage', 
		'spp_pluginPage_section' 
	);

	add_settings_field( 
		'spp_radio_field_2', 
		__( 'Settings field description', 'wordpress' ), 
		'spp_radio_field_2_render', 
		'pluginPage', 
		'spp_pluginPage_section' 
	);

	add_settings_field( 
		'spp_textarea_field_3', 
		__( 'Settings field description', 'wordpress' ), 
		'spp_textarea_field_3_render', 
		'pluginPage', 
		'spp_pluginPage_section' 
	);

	add_settings_field( 
		'spp_select_field_4', 
		__( 'Settings field description', 'wordpress' ), 
		'spp_select_field_4_render', 
		'pluginPage', 
		'spp_pluginPage_section' 
	);


}


   function spp_test_api( $data ) {
	$posts = get_posts( array(
	  'author' => $data['id'],
	) );
   
	if ( empty( $posts ) ) {
	  return null;
	}
   
	return $posts[0]->post_title;
  }




	add_action( 'rest_api_init', function () {
		register_rest_route( 'slack-pusher/v1', '/post', array(
		  'methods' => 'GET',
		  'callback' => 'spp_test_api',
		) );
	  } );


