<?php
/**
 * AJAX handlers.
 *
 * @package ChatWidgetFlowFunnel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// AJAX handler for Chat Widget number verification.
add_action( 'wp_ajax_verify_chatwidgetflowfunnel_number', 'chatwidgetflowfunnel_verify_number_handler' );

/**
 * Simulated number verification (placeholder for real service integration).
 */
function chatwidgetflowfunnel_verify_number_handler() {
	if ( ! check_ajax_referer( 'verify_chatwidgetflowfunnel_number', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid security token.', 'chat-widget-for-flowfunnel' ) ) );
	}

	$number = isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '';

	if ( empty( $number ) ) {
		wp_send_json_error( array( 'message' => __( 'Please provide a valid chat widget number.', 'chat-widget-for-flowfunnel' ) ) );
	}

	// Placeholder verification always true.
	$verified = true;

	if ( $verified ) {
		update_option( 'chatwidgetflowfunnel_number_verified', true );
		wp_send_json_success( array( 'message' => __( 'Number verified successfully.', 'chat-widget-for-flowfunnel' ) ) );
	} else {
		wp_send_json_error( array( 'message' => __( 'Could not verify chat widget number.', 'chat-widget-for-flowfunnel' ) ) );
	}
}
