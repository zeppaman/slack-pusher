<?php
/*
Plugin Name: Slack Pusher Plugin
Plugin URI:  https://github.com/zeppaman/slack-pusher/
Description: Publish your post from slack messages
Version:     0.4a
Author:      Daniele Fontani
Author URI:  https://github.com/zeppaman/
*/


/*
This module is creade using Ian Dunn skeleton plugin as reference
https://github.com/iandunn/WordPress-Plugin-Skeleton
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
	//register_setting( 'spp-settings-group', 'challange' );
}



function spp_options_page()
{
	?>
	<div class="wrap">
	<h1>Slack Pusher</h1>
	
	<form method="post" action="options.php">
		<?php settings_fields( 'spp-settings-group' ); ?>
		<?php do_settings_sections( 'spp-settings-group' ); ?>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Wating for challange</th>
			<td><input type="checkbox" name="wait_challange" value="1" <?php checked( '1', get_option( 'wait_challange' ) ); ?>  /></td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">Token</th>
			<td><input type="text" name="token" value="<?php echo esc_attr( get_option('token') ); ?>" disabled /></td>
			</tr>
			
			<!-- <tr valign="top">
			<th scope="row">Challange</th>
			<td><input type="text" name="challange" value="<?php echo esc_attr( get_option('challange') ); ?>"   disabled/></td>
			</tr> -->
		</table>
		
		<?php submit_button(); ?>
	
	</form>
	</div>
	<?php } 



   function renderHtml($text)
   {
	   

   $rendermap=array(
	'/((?<!\\*)\\*(?!\\*))(.*)((?<!\\*)\*(?!\\*))/i'=> '<b>$2</b>',
    '/((?<!_)_(?!_))(.*)((?<!_)_(?!_))/i'=>'<i>$2</i>',
    '/((?<!\\~)\\~(?!\\~))(.*)((?<!\\~)\\~(?!\\~))/i'=> '<strike>$2</strike>',    
   );


		foreach($rendermap as $wrapper=>$html_tag)
		{
			$text=preg_replace($wrapper,$html_tag,$text);
			//error_log("w=>$wrapper  t=>$html_tag  result=> $text");

		}
		return $text;
   }

   // returns challange payload 
   function sendChallangeResponse($data)
   {
	   $payload= array();
	   $payload['challenge']=$data["challenge"];
	   return $payload;
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

		//error_log(get_users());
		
		$text=$data['event']['text'];

		$text=renderHtml($text);
		error_log($text);

		$lines = explode("\n", $text);



		$newpost=array(
			'post_content' => '<p>'. ((sizeof($lines)<2)? '' : implode('<br>',$lines) ).'</p>',
			'post_author' =>  $user->ID,
			'post_title' => $lines[0]
		);


		//error_log($data);
		
		return $newpost;
   }

   //mange input messages
   function spp_post_message( $data ) {
	   error_log("hit post message by slack");

	  // error_log( print_r($data, TRUE));

	  error_log($data['token']);
	  
	   $enabled=get_option('wait_challange');

	   if(isset($data['challenge']))
	   {
		
            error_log("entering challenge, enabled:".$enabled);
			if($enabled==TRUE)
			{
				error_log("accepting challenge");
				//avoid unwanted registration
				update_option('wait_challange',FALSE);
				update_option('token',$data["token"]);
				return sendChallangeResponse($data);

			}
			else
			{
				//unhautorized
				error_log("unauthorized challenge");
				echo("unauthorized");
				exit;
			}
	 }
	 else
	 {
		 
		 if(get_option("token") != $data['token'])
		 {
			//unhautorized
			error_log("unauthorized challenge");
			echo("unauthorized");
			exit;
		}

	 	$newpost=im2post($data);
	 	wp_insert_post($newpost);
	}
	return;
  }




	add_action( 'rest_api_init', function () {
		register_rest_route( 'slack-pusher/v1', '/postmessage', array(
		  'methods' => 'POST',
		  'callback' => 'spp_post_message',
		) );
	  } );


