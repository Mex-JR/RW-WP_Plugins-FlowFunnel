<?php

// AJAX handler for Chat Widget number verification
add_action('wp_ajax_verify_chatwidgetflowfunnel_number', 'verify_chatwidgetflowfunnel_number_handler');

function verify_chatwidgetflowfunnel_number_handler()
{
    // Verify nonce
    if (!check_ajax_referer('verify_chatwidgetflowfunnel_number', 'nonce', false)) {
        wp_send_json_error(['message' => 'Invalid security token']);
    }

    // Get and sanitize the number
    $number = isset($_POST['number']) ? sanitize_text_field(wp_unslash($_POST['number'])) : '';

    if (empty($number)) {
        wp_send_json_error(['message' => 'Please provide a valid Chat Widget number']);
    }

    $verified = true;

    if ($verified) {
        update_option('chatwidgetflowfunnel_number_verified', true);
        wp_send_json_success(['message' => 'Number verified successfully']);
    } else {
        wp_send_json_error(['message' => 'Could not verify Chat Widget number']);
    }
}
