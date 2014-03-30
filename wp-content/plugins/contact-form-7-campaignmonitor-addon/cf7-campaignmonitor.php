<?php
/*
Plugin Name: Contact Form 7 - Campaign Monitor Addon
Plugin URI: http://www.bettigole.us/published-work/wordpress-contributions/campaign-monitor-addon-for-contact-form-7/
Description: Add the power of CampaignMonitor to Contact Form 7
Author: Joshua Bettigole
Author URI: http://www.bettigole.us
Version: 0.99
*/

/*  Copyright 2010 Joshua Bettigole (email: joshua at bettigole.us)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define( 'WPCF7_CM_VERSION', '0.99' );

if ( ! defined( 'WPCF7_CM_PLUGIN_BASENAME' ) )
	define( 'WPCF7_CM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

add_action( 'wpcf7_after_save', 'wpcf7_cm_save_campaignmonitor' );

function wpcf7_cm_save_campaignmonitor($args)
{
	update_option( 'cf7_cm_'.$args->id, $_POST['wpcf7-campaignmonitor'] );
}


add_action( 'wpcf7_admin_after_mail_2', 'wpcf7_cm_add_campaignmonitor' );

function wpcf7_cm_add_campaignmonitor($args)
{
	if ( wpcf7_admin_has_edit_cap() ) 
	{
				$cf7_cm_defaults = array();
				$cf7_cm = get_option( 'cf7_cm_'.$args->id, $cf7_cm_defaults );
				?>

        <table class="widefat" style="margin-top: 1em;">
        <thead><tr><th scope="col" colspan="2"><?php echo esc_html( __( 'Campaign Monitor', 'wpcf7' ) ); ?> <span id="campaignmonitor-fields-toggle-switch"></span></th></tr></thead>

        <tbody>
        <tr>
        <td scope="col" colspan="2">
        <input type="checkbox" id="wpcf7-campaignmonitor-active" name="wpcf7-campaignmonitor[active]" value="1"<?php echo ( $cf7_cm['active'] ) ? ' checked="checked"' : ''; ?> />
        <label for="wpcf7-campaignmonitor-active"><?php echo esc_html( __( 'Use CampaignMonitor', 'wpcf7' ) ); ?></label>
        </td>
        </tr>

        <tr id="campaignmonitor-fields">
        <td scope="col" style="width: 50%;">

        <div class="mail-field">
        <label for="wpcf7-campaignmonitor-email"><?php echo esc_html( __( 'Subscriber Email:', 'wpcf7' ) ); ?></label><br />
        <input type="text" id="wpcf7-campaignmonitor-email" name="wpcf7-campaignmonitor[email]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['email'] ); ?>" />
        </div>

        <div class="mail-field">
        <label for="wpcf7-campaignmonitor-name"><?php echo esc_html( __( 'Subscriber Full Name:', 'wpcf7' ) ); ?></label><br />
        <input type="text" id="wpcf7-campaignmonitor-name" name="wpcf7-campaignmonitor[name]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['name'] ); ?>" />
        </div>

        <div class="mail-field">
        <label for="wpcf7-campaignmonitor-accept"><?php echo esc_html( __( 'Required Acceptance Field:', 'wpcf7' ) ); ?></label><br />
        <input type="text" id="wpcf7-campaignmonitor-accept" name="wpcf7-campaignmonitor[accept]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['accept'] ); ?>" />
        </div>

				<div class="mail-field">&nbsp;</div>
        </td>
        <td scope="col" style="width: 50%;">

        <div class="mail-field">
        <label for="wpcf7-campaignmonitor-api"><?php echo esc_html( __( 'API Key:', 'wpcf7' ) ); ?></label><br />
        <input type="text" id="wpcf7-campaignmonitor-api" name="wpcf7-campaignmonitor[api]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['api'] ); ?>" />
        </div>

        <div class="mail-field">
        <label for="wpcf7-campaignmonitor-client"><?php echo esc_html( __( 'Client ID:', 'wpcf7' ) ); ?></label><br />
        <input type="text" id="wpcf7-campaignmonitor-client" name="wpcf7-campaignmonitor[client]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['client'] ); ?>" />
        </div>

        <div class="mail-field">
        <label for="wpcf7-campaignmonitor-list"><?php echo esc_html( __( 'List ID:', 'wpcf7' ) ); ?></label><br />
        <input type="text" id="wpcf7-campaignmonitor-list" name="wpcf7-campaignmonitor[list]" class="wide" size="70" value="<?php echo esc_attr( $cf7_cm['list'] ); ?>" />
        </div>

				<div class="mail-field">&nbsp;</div>
        </td>
        </tr>
        </tbody>
        </table>

				<?php
	}
}


add_action( 'admin_print_scripts', 'wpcf7_cm_admin_enqueue_scripts' );

function wpcf7_cm_admin_enqueue_scripts ()
{
	global $plugin_page;

	if ( ! isset( $plugin_page ) || 'wpcf7' != $plugin_page )
		return;

	wp_enqueue_script( 'wpcf7-cm-admin', wpcf7_cm_plugin_url( 'scripts.js' ),
		array( 'jquery', 'wpcf7-admin' ), WPCF7_CM_VERSION, true );
}


add_action( 'wpcf7_before_send_mail', 'wpcf7_cm_subscribe' );

function wpcf7_cm_subscribe($obj)
{
	$cf7_cm = get_option( 'cf7_cm_'.$obj->id );
	if( $cf7_cm )
	{
		$subscribe = false;

		$regex = '/\[\s*([a-zA-Z_][0-9a-zA-Z:._-]*)\s*\]/';
		$callback = array( &$obj, 'mail_callback' );
	
		$email = preg_replace_callback( $regex, $callback, $cf7_cm['email'] );
		$name = preg_replace_callback( $regex, $callback, $cf7_cm['name'] );
		error_log('email: '.$email);
		error_log('name: '.$name);

		if( isset($cf7_cm['accept']) && strlen($cf7_cm['accept']) != 0 )
		{
			$accept = preg_replace_callback( $regex, $callback, $cf7_cm['accept'] );
			if($accept != $cf7_cm['accept'])
			{
				if(strlen($accept) > 0)
					$subscribe = true;
			}
		}
		else
		{
			$subscribe = true;
		}
	
		if($subscribe && $email != $cf7_cm['email'])
		{
			require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'campaignmonitor.php');
			$cm = new CampaignMonitor( $cf7_cm['api'], $cf7_cm['client'], '', $cf7_cm['list'] );
			if($name == $cf7_cm['name'])
				$name = '';
			$result = $cm->subscriberAdd($email, $name);
		}
	}
}

function wpcf7_cm_plugin_url( $path = '' ) {
	return plugins_url( $path, WPCF7_CM_PLUGIN_BASENAME );
}
