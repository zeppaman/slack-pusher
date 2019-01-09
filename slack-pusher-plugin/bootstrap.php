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


function spp_settings_init() {
	// register a new setting for "wporg" page
	register_setting( 'wporg', 'wporg_options' );
	
	// register a new section in the "wporg" page
	add_settings_section(
	'spp_section_developers',
	__( 'The Matrix has you.', 'wporg' ),
	'spp_section_developers_cb',
	'spp'
	);
	
	// register a new field in the "wporg_section_developers" section, inside the "wporg" page
	add_settings_field(
	'spp_field_pill', // as of WP 4.6 this value is used only internally
	// use $args' label_for to populate the id inside the callback
	__( 'Pill', 'spp' ),
	'spp_field_pill_cb',
	'spp',
	'spp_section_developers',
	[
	'label_for' => 'wporg_field_pill',
	'class' => 'wporg_row',
	'spp_custom_data' => 'custom',
	]
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


