<?php
/**
 * Plugin Name:       Chat Widget for flowfunnel
 * Plugin URI:        https://github.com/Mex-JR/RW-WP_Plugins-FlowFunnel
 * Description:       Customizable floating chat widget icon for your entire website with options.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            RiverWork IT LLC
 * Author URI:        https://riverworksit.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       chat-widget-for-flowfunnel
 * Domain Path:       /languages
 *
 * @package ChatWidgetFlowFunnel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin version constant (increment when releasing updates).
if ( ! defined( 'CHATWIDGETFLOWFUNNEL_VERSION' ) ) {
	define( 'CHATWIDGETFLOWFUNNEL_VERSION', '1.0.1' );
}



/**
 * Whitelist helper for country code.
 *
 * @param string $code Raw code.
 * @return string Sanitized/whitelisted code.
 */
function chatwidgetflowfunnel_sanitize_country_code( $code ) {
	$allowed = array( '+1', '+44', '+91', '+61', '+49', '+33', '+81', '+971', '+880', '+234', '+55', '+34', '+7', '+20', '+62', '+82', '+39', '+63', '+92', '+27', '+65', '+66', '+90', '+48', '+86', '+972', '+358', '+46', '+31', '+43', '+351', '+32', '+45', '+420', '+36', '+47', '+41', '+353', '+380', '+994', '+84', '+998', '+964' );
	return in_array( $code, $allowed, true ) ? $code : '+1';
}

/**
 * Sanitize widget position.
 */
function chatwidgetflowfunnel_sanitize_position( $position ) {
	$allowed = array( 'bottom-right', 'bottom-left' );
	return in_array( $position, $allowed, true ) ? $position : 'bottom-right';
}

/**
 * Sanitize icon style.
 */
function chatwidgetflowfunnel_sanitize_icon_style( $style ) {
	$allowed = array( 'style1', 'style3', 'style4' );
	return in_array( $style, $allowed, true ) ? $style : 'style1';
}

// Load Assets
function chatwidgetflowfunnel_enqueue_assets() {
	$font_awesome_url = plugin_dir_url( __FILE__ ) . 'assets/css/all.min.css';

	// Register & enqueue local Font Awesome (avoid remote loads for wp.org review).
	wp_register_style(
		'chatwidgetflowfunnel-font-awesome',
		$font_awesome_url,
		array(),
		'6.4.0',
		'all'
	);
	wp_enqueue_style( 'chatwidgetflowfunnel-font-awesome' );

	wp_enqueue_style(
		'chatwidgetflowfunnel-tailwind-style',
		plugin_dir_url( __FILE__ ) . 'assets/css/style.css',
		array( 'chatwidgetflowfunnel-font-awesome' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/style.css' )
	);

	wp_enqueue_style(
		'chatwidgetflowfunnel-style',
		plugin_dir_url( __FILE__ ) . 'assets/css/frontend.css',
		array( 'chatwidgetflowfunnel-font-awesome', 'chatwidgetflowfunnel-tailwind-style' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/frontend.css' )
	);

	wp_enqueue_script(
		'chatwidgetflowfunnel-script',
		plugin_dir_url( __FILE__ ) . 'assets/js/script.js',
		array( 'jquery' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/script.js' ),
		true
	);

	wp_localize_script(
		'chatwidgetflowfunnel-script',
		'chatwidgetflowfunnelData',
		array(
			'phoneNumber'     => get_option( 'chatwidgetflowfunnel_chat_number', '' ),
			'countryCode'     => get_option( 'chatwidgetflowfunnel_country_code', '+1' ),
			'inquiryOptions'  => get_option( 'chatwidgetflowfunnel_chat_options', array() ),
			'trackingEnabled' => get_option( 'chatwidgetflowfunnel_chat_tracking', 'no' ),
			'ajaxurl'         => admin_url( 'admin-ajax.php' ),
			'nonce'           => wp_create_nonce( 'easy_chat_track_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'chatwidgetflowfunnel_enqueue_assets' );

// Load Admin Assets
function chatwidgetflowfunnel_enqueue_admin_assets( $hook ) {
	if ( 'settings_page_chat-widget-for-flowfunnel-settings' !== $hook ) {
		return;
	}
	$font_awesome_url = plugin_dir_url( __FILE__ ) . 'assets/css/all.min.css';
	wp_enqueue_style(
		'chatwidgetflowfunnel-font-awesome',
		$font_awesome_url,
		array(),
		'6.4.0',
		'all'
	);
	wp_enqueue_style(
		'chatwidgetflowfunnel-tailwind-style',
		plugin_dir_url( __FILE__ ) . 'assets/css/style.css',
		array( 'chatwidgetflowfunnel-font-awesome' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/style.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'chatwidgetflowfunnel_enqueue_admin_assets' );

// Include Admin Settings & Analytics
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/analytics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax-handlers.php';

// Display Chat Widget Button.
function chatwidgetflowfunnel_button() {
	$number       = get_option( 'chatwidgetflowfunnel_chat_number' );
	$country_code = get_option( 'chatwidgetflowfunnel_country_code', '+1' );
	$country_code = chatwidgetflowfunnel_sanitize_country_code( $country_code );
	$full_number  = preg_replace( '/[^0-9]/', '', $country_code . $number );

	if ( ! empty( $number ) ) {
		$position        = chatwidgetflowfunnel_sanitize_position( get_option( 'chatwidgetflowfunnel_chat_position', 'bottom-right' ) );
		$icon_style      = chatwidgetflowfunnel_sanitize_icon_style( get_option( 'chatwidgetflowfunnel_chat_icon_style', 'style1' ) );
		$inquiry_options = get_option( 'chatwidgetflowfunnel_chat_options', array() );

		// Build base WhatsApp URL.
		$base_url = 'https://wa.me/' . rawurlencode( $full_number );
		?>
		<div class="chatwidgetflowfunnel-container <?php echo esc_attr( $position ); ?>">
			<div class="chatwidgetflowfunnel-button <?php echo esc_attr( $icon_style ); ?>" onclick="toggleChatWidgetFlowfunnelPopup()">
				<i class="fab fa-whatsapp" aria-hidden="true"></i>
				<span class="screen-reader-text"><?php esc_html_e( 'Open chat options', 'chat-widget-for-flowfunnel' ); ?></span>
			</div>
			<?php if ( ! empty( $inquiry_options ) ) : ?>
				<div class="chatwidgetflowfunnel-popup hidden" id="chatwidgetflowfunnel-popup">
					<div class="popup-content">
						<div class="popup-header">
							<div class="header-icon">
								<i class="fab fa-whatsapp" aria-hidden="true"></i>
							</div>
							<h3><?php esc_html_e( 'How can we help?', 'chat-widget-for-flowfunnel' ); ?></h3>
						</div>
						<div class="popup-options">
							<?php
							foreach ( (array) $inquiry_options as $option ) :
								$option_safe  = sanitize_text_field( $option );
								$whatsapp_url = esc_url( add_query_arg( 'text', rawurlencode( $option_safe ), $base_url ) );
								?>
								<a href="<?php echo esc_url( $whatsapp_url ); ?>"
									target="_blank"
									rel="noopener noreferrer"
									class="chat-option"
									onclick="trackChatWidgetFlowfunnelClick('<?php echo esc_js( $option_safe ); ?>')">
									<span class="option-text"><?php echo esc_html( $option_safe ); ?></span>
									<i class="fas fa-chevron-right" aria-hidden="true"></i>
								</a>
							<?php endforeach; ?>
						</div>
						<div class="chatwidgetflowfunnel-brand-name">
							<a href="https://flowfunnel.io" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'by flowfunnel.io', 'chat-widget-for-flowfunnel' ); ?>
							</a>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
add_action( 'wp_footer', 'chatwidgetflowfunnel_button' );

// Add settings link to plugins page
function chatwidgetflowfunnel_add_settings_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=chat-widget-for-flowfunnel-settings' ) ) . '">' . esc_html__( 'Settings', 'chat-widget-for-flowfunnel' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'chatwidgetflowfunnel_add_settings_link' );
