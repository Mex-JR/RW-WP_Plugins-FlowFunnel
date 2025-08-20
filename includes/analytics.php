<?php
/**
 * Front-end analytics handling.
 *
 * @package ChatWidgetFlowFunnel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Track option click via AJAX.
 */
function chatwidgetflowfunnel_track_chat_click() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'chatwidgetflowfunnel_track_nonce' ) ) {
		wp_send_json_error( __( 'Invalid security token.', 'chat-widget-for-flowfunnel' ) );
	}

	if ( isset( $_POST['option'] ) ) {
		$option = sanitize_text_field( wp_unslash( $_POST['option'] ) );
		$key    = 'chatwidgetflowfunnel_clicks_' . sanitize_key( $option );
		$clicks = (int) get_option( $key, 0 );
		update_option( $key, $clicks + 1 );
		wp_send_json_success( __( 'Click tracked successfully.', 'chat-widget-for-flowfunnel' ) );
	} else {
		wp_send_json_error( __( 'Missing option parameter.', 'chat-widget-for-flowfunnel' ) );
	}
}
add_action( 'wp_ajax_chatwidgetflowfunnel_track_chat_click', 'chatwidgetflowfunnel_track_chat_click' );
add_action( 'wp_ajax_nopriv_chatwidgetflowfunnel_track_chat_click', 'chatwidgetflowfunnel_track_chat_click' );
