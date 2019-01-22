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
	register_setting( 'spp-settings-group', 'wait_challange' );
	register_setting( 'spp-settings-group', 'token' );
	register_setting( 'spp-settings-group', 'challange' );
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
			<td><input type="text" name="wait_challange" value="<?php echo esc_attr( get_option('wait_challange') ); ?>" /></td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">Some Other Option</th>
			<td><input type="text" name="token" value="<?php echo esc_attr( get_option('token') ); ?>" /></td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Options, Etc.</th>
			<td><input type="text" name="challange" value="<?php echo esc_attr( get_option('challange') ); ?>" /></td>
			</tr>
		</table>
		
		<?php submit_button(); ?>
	
	</form>
	</div>
	<?php } 


   // returns challange payload 
   function sendChallangeResponse($data)
   {
	   $payload= array();
	   $payload['challenge']=$data["challange"];
	   return payload;
   }

   function im2post($data)
   {
	   /*
	    slack input
	   {
		"token": "one-long-verification-token",
		"team_id": "T061EG9R6",
		"api_app_id": "A0PNCHHK2",
		"event": {
			"type": "message",
			"channel": "D024BE91L",
			"user": "U2147483697",
			"text": "Hello hello can you hear me?",
			"ts": "1355517523.000005",
			"event_ts": "1355517523.000005",
			"channel_type": "im"
		},
		"type": "event_callback",
		"authed_teams": [
			"T061EG9R6"
		],
		"event_id": "Ev0PV52K21",
		"event_time": 1355517523
		}*/

		/*
		 WP post defaults
		 
		$defaults = array(
				'post_author' => $user_id,
				'post_content' => '',
				'post_content_filtered' => '',
				'post_title' => '',
				'post_excerpt' => '',
				'post_status' => 'draft',
				'post_type' => 'post',
				'comment_status' => '',
				'ping_status' => '',
				'post_password' => '',
				'to_ping' =>  '',
				'pinged' => '',
				'post_parent' => 0,
				'menu_order' => 0,
				'guid' => '',
				'import_id' => 0,
				'context' => '',
			);				 
		*/

		//this is just a demo. in real world example slack user should match wordpress user (by mapping o mail)
        $user=get_users()[0];

		$newpost=array(
			'post_content' =>$data['event']['text'],
			'post_author' =>  $user->ID,			
		);

		
		return $newpost;
   }

   //mange input messages
   function spp_post_message( $data ) {
	   if(get_option('wait_challange')===TRUE)
	   {
		   //avoid unwanted registration
		   set_option('wait_challange',TRUE);
		   return sendChallangeResponse($data);

	   }

	$newpost=im2post($data);
	wp_insert_post($newpost);
   
	return;
  }




	add_action( 'rest_api_init', function () {
		register_rest_route( 'slack-pusher/v1', '/postmessage', array(
		  'methods' => 'GET',
		  'callback' => 'spp_post_message',
		) );
	  } );


