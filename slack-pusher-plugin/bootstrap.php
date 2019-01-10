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







   function spp_add_admin_menu(  ) { 

	add_options_page( 'Slack Pusher Plugin', 'Slack Pusher Plugin', 'manage_options', 'slack_pusher_plugin', 'spp_options_page' );

	//call register settings function
	add_action( 'admin_init', 'register_spp_settings' );
}


function register_spp_settings() {
	//register our settings
	register_setting( 'spp-settings-group', 'new_option_name' );
	register_setting( 'spp-settings-group', 'some_other_option' );
	register_setting( 'spp-settings-group', 'option_etc' );
}



function spp_options_page()
{
	?>
	<div class="wrap">
	<h1>Your Plugin Name</h1>
	
	<form method="post" action="options.php">
		<?php settings_fields( 'spp-settings-group' ); ?>
		<?php do_settings_sections( 'spp-settings-group' ); ?>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">New Option Name</th>
			<td><input type="text" name="new_option_name" value="<?php echo esc_attr( get_option('new_option_name') ); ?>" /></td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">Some Other Option</th>
			<td><input type="text" name="some_other_option" value="<?php echo esc_attr( get_option('some_other_option') ); ?>" /></td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Options, Etc.</th>
			<td><input type="text" name="option_etc" value="<?php echo esc_attr( get_option('option_etc') ); ?>" /></td>
			</tr>
		</table>
		
		<?php submit_button(); ?>
	
	</form>
	</div>
	<?php } 


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


