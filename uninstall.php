<?php
/**
 * Uninstall routine for Chat Widget for flowfunnel.io.
 *
 * Removes plugin-specific options created by this plugin.
 *
 * @package ChatWidgetFlowFunnel
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// List of option keys to remove.
$option_keys = array(
	'chatwidgetflowfunnel_chat_number',
	'chatwidgetflowfunnel_country_code',
	'chatwidgetflowfunnel_chat_options',
	'chatwidgetflowfunnel_chat_position',
	'chatwidgetflowfunnel_chat_icon_style',
	'chatwidgetflowfunnel_chat_tracking',
	'chatwidgetflowfunnel_number_verified',
);

foreach ( $option_keys as $key ) {
	delete_option( $key );
}
