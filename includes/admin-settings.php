<?php

// ...existing code...
function chatwidgetflowfunnel_menu()
{
    add_options_page('Chat Widget for flowfunnel.io', 'Chat Widget for flowfunnel.io', 'manage_options', 'chat-widget-flowfunnel-settings', 'chatwidgetflowfunnel_settings_page');
}
add_action('admin_menu', 'chatwidgetflowfunnel_menu');

// ...existing code...
add_action('admin_enqueue_scripts', 'chatwidgetflowfunnel_admin_scripts');

function chatwidgetflowfunnel_admin_scripts($hook)
{
    if ('settings_page_chat-widget-flowfunnel-settings' !== $hook) {
        return;
    }
    wp_enqueue_script(
        'chatwidgetflowfunnel-admin-settings',
        plugins_url('/assets/js/admin-settings.js', dirname(__FILE__)),
        array('jquery'),
        '1.0.0',
        true
    );
    // ...existing code...
    wp_localize_script(
        'chatwidgetflowfunnel-admin-settings',
        'chatwidgetflowfunnelAdminSettings',
        array(
            'nonce' => wp_create_nonce('verify_chatwidgetflowfunnel_number')
        )
    );
}

// ...existing code...
function chatwidgetflowfunnel_settings_page()
{
    if (!current_user_can('manage_options')) return;

    // Process form submission
    if (isset($_POST['chatwidgetflowfunnel_save'])) {
        // ...existing code...
        if (
            !isset($_POST['chatwidgetflowfunnel_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash($_POST['chatwidgetflowfunnel_nonce'])
                ),
                'chatwidgetflowfunnel_settings'
            )
        ) {
            wp_die('Invalid nonce specified', 'Error', array('response' => 403));
        }

        // ...existing code...
        $settings_updated = false;

        // ...existing code...
        if (isset($_POST['chatwidgetflowfunnel_chat_number'])) {
            update_option('chatwidgetflowfunnel_chat_number', sanitize_text_field(wp_unslash($_POST['chatwidgetflowfunnel_chat_number'])));
            $settings_updated = true;
        }
        // Save country code
        if (isset($_POST['chatwidgetflowfunnel_country_code'])) {
            update_option('chatwidgetflowfunnel_country_code', sanitize_text_field(wp_unslash($_POST['chatwidgetflowfunnel_country_code'])));
            $settings_updated = true;
        }

        // Process inquiry options with proper sanitization
        if (isset($_POST['chatwidgetflowfunnel_chat_options'])) {
            $raw_options = array_map('sanitize_text_field', wp_unslash($_POST['chatwidgetflowfunnel_chat_options']));
            if (is_array($raw_options)) {
                $sanitized_options = array_map('sanitize_text_field', $raw_options);
                $sanitized_options = array_filter($sanitized_options); // Remove empty values
                update_option('chatwidgetflowfunnel_chat_options', $sanitized_options);
                $settings_updated = true;
            }
        }

        // Process position
        if (isset($_POST['chatwidgetflowfunnel_chat_position'])) {
            update_option('chatwidgetflowfunnel_chat_position', sanitize_text_field(wp_unslash($_POST['chatwidgetflowfunnel_chat_position'])));
            $settings_updated = true;
        }

        // Process icon style
        if (isset($_POST['chatwidgetflowfunnel_chat_icon_style'])) {
            update_option('chatwidgetflowfunnel_chat_icon_style', sanitize_text_field(wp_unslash($_POST['chatwidgetflowfunnel_chat_icon_style'])));
            $settings_updated = true;
        }

        // Process tracking
        if (isset($_POST['chatwidgetflowfunnel_chat_tracking'])) {
            update_option('chatwidgetflowfunnel_chat_tracking', sanitize_text_field(wp_unslash($_POST['chatwidgetflowfunnel_chat_tracking'])));
            $settings_updated = true;
        }

        // Show success message if any setting was updated
        if ($settings_updated) {
            echo '<div id="toast-success" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 transform transition-all duration-300 opacity-0 translate-y-full">
                <div class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-lg" role="alert">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 text-sm font-normal">Settings saved successfully!</div>
                </div>
            </div>';

            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    const toast = document.getElementById("toast-success");
                    // Show toast
                    setTimeout(() => {
                        toast.classList.remove("opacity-0", "translate-y-full");
                    }, 100);
                    // Hide toast
                    setTimeout(() => {
                        toast.classList.add("opacity-0", "translate-y-full");
                        // Remove element after animation
                        setTimeout(() => {
                            toast.remove();
                        }, 3000);
                    }, 3000);
                });
            </script>';
        }
    }

    // Get saved options
    $chatwidget_number = get_option('chatwidgetflowfunnel_chat_number', '');
    $chatwidget_country_code = get_option('chatwidgetflowfunnel_country_code', '+1');
    $inquiry_options = get_option('chatwidgetflowfunnel_chat_options', array());
    if (!is_array($inquiry_options)) {
        $inquiry_options = array();
    }
    $inquiry_options = array_filter(array_map('trim', $inquiry_options));
    $selected_icon = get_option('chatwidgetflowfunnel_chat_icon_style', 'style1');

    // Get click stats for each option
    $total_clicks = 0;
    $option_clicks = array();
    foreach ($inquiry_options as $option) {
        $count = (int) get_option("easy_chat_clicks_$option", 0);
        $option_clicks[$option] = $count;
        $total_clicks += $count;
    }

?>
    <div class="wrap">
        <div class="py-6">
            <!-- Modern Header -->
            <div class="bg-gradient-to-r from-green-400 to-blue-500 -mt-6 -mx-4 px-8 py-8 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">Chat Widget for flowfunnel.io</h2>
                        <p class="text-green-50">Configure your chat widget settings</p>
                    </div>
                    <span class="px-4 py-2 bg-white/20 text-white rounded-full text-sm backdrop-blur-sm">Version 1.0</span>
                </div>
            </div>

            <form method="post" class="space-y-8">
                <?php wp_nonce_field('chatwidgetflowfunnel_settings', 'chatwidgetflowfunnel_nonce'); ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- WhatsApp Number Card -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xl font-semibold mb-6 text-gray-700 pb-4 border-b">Chat Widget Account</h3>
                        <div class="space-y-4">
                            <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
                                <div class="flex-grow">
                                    <label class="text-sm font-medium text-gray-700 mb-2 block">Chat Widget Number</label>
                                    <div class="flex gap-x-2">
                                        <select name="chatwidgetflowfunnel_country_code" id="chatwidgetflowfunnel_country_code" class="inline-flex items-center px-2 rounded-l-md border border-gray-300 border-r bg-gray-50 text-gray-700 sm:text-sm h-[35px] min-w-[80px] focus:ring-green-500 focus:border-green-500 mr-2">
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇺🇸 +1 (USA)</option>
                                            <option value="+44" <?php echo ($chatwidget_country_code == '+44') ? 'selected' : ''; ?>>🇬🇧 +44 (UK)</option>
                                            <option value="+91" <?php echo ($chatwidget_country_code == '+91') ? 'selected' : ''; ?>>🇮🇳 +91 (India)</option>
                                            <option value="+61" <?php echo ($chatwidget_country_code == '+61') ? 'selected' : ''; ?>>🇦🇺 +61 (Australia)</option>
                                            <option value="+49" <?php echo ($chatwidget_country_code == '+49') ? 'selected' : ''; ?>>🇩🇪 +49 (Germany)</option>
                                            <option value="+33" <?php echo ($chatwidget_country_code == '+33') ? 'selected' : ''; ?>>🇫🇷 +33 (France)</option>
                                            <option value="+81" <?php echo ($chatwidget_country_code == '+81') ? 'selected' : ''; ?>>🇯🇵 +81 (Japan)</option>
                                            <option value="+971" <?php echo ($chatwidget_country_code == '+971') ? 'selected' : ''; ?>>🇦🇪 +971 (UAE)</option>
                                            <option value="+880" <?php echo ($chatwidget_country_code == '+880') ? 'selected' : ''; ?>>🇧🇩 +880 (Bangladesh)</option>
                                            <option value="+234" <?php echo ($chatwidget_country_code == '+234') ? 'selected' : ''; ?>>🇳🇬 +234 (Nigeria)</option>
                                            <option value="+55" <?php echo ($chatwidget_country_code == '+55') ? 'selected' : ''; ?>>🇧🇷 +55 (Brazil)</option>
                                            <option value="+34" <?php echo ($chatwidget_country_code == '+34') ? 'selected' : ''; ?>>🇪🇸 +34 (Spain)</option>
                                            <option value="+7" <?php echo ($chatwidget_country_code == '+7') ? 'selected' : ''; ?>>🇷🇺 +7 (Russia)</option>
                                            <option value="+20" <?php echo ($chatwidget_country_code == '+20') ? 'selected' : ''; ?>>🇪🇬 +20 (Egypt)</option>
                                            <option value="+62" <?php echo ($chatwidget_country_code == '+62') ? 'selected' : ''; ?>>🇮🇩 +62 (Indonesia)</option>
                                            <option value="+82" <?php echo ($chatwidget_country_code == '+82') ? 'selected' : ''; ?>>🇰🇷 +82 (South Korea)</option>
                                            <option value="+39" <?php echo ($chatwidget_country_code == '+39') ? 'selected' : ''; ?>>🇮🇹 +39 (Italy)</option>
                                            <option value="+63" <?php echo ($chatwidget_country_code == '+63') ? 'selected' : ''; ?>>🇵🇭 +63 (Philippines)</option>
                                            <option value="+92" <?php echo ($chatwidget_country_code == '+92') ? 'selected' : ''; ?>>🇵🇰 +92 (Pakistan)</option>
                                            <option value="+27" <?php echo ($chatwidget_country_code == '+27') ? 'selected' : ''; ?>>🇿🇦 +27 (South Africa)</option>
                                            <option value="+65" <?php echo ($chatwidget_country_code == '+65') ? 'selected' : ''; ?>>🇸🇬 +65 (Singapore)</option>
                                            <option value="+66" <?php echo ($chatwidget_country_code == '+66') ? 'selected' : ''; ?>>🇹🇭 +66 (Thailand)</option>
                                            <option value="+90" <?php echo ($chatwidget_country_code == '+90') ? 'selected' : ''; ?>>🇹🇷 +90 (Turkey)</option>
                                            <option value="+48" <?php echo ($chatwidget_country_code == '+48') ? 'selected' : ''; ?>>🇵🇱 +48 (Poland)</option>
                                            <option value="+86" <?php echo ($chatwidget_country_code == '+86') ? 'selected' : ''; ?>>🇨🇳 +86 (China)</option>
                                            <option value="+972" <?php echo ($chatwidget_country_code == '+972') ? 'selected' : ''; ?>>🇮🇱 +972 (Israel)</option>
                                            <option value="+358" <?php echo ($chatwidget_country_code == '+358') ? 'selected' : ''; ?>>🇫🇮 +358 (Finland)</option>
                                            <option value="+46" <?php echo ($chatwidget_country_code == '+46') ? 'selected' : ''; ?>>🇸🇪 +46 (Sweden)</option>
                                            <option value="+31" <?php echo ($chatwidget_country_code == '+31') ? 'selected' : ''; ?>>🇳🇱 +31 (Netherlands)</option>
                                            <option value="+43" <?php echo ($chatwidget_country_code == '+43') ? 'selected' : ''; ?>>🇦🇹 +43 (Austria)</option>
                                            <option value="+351" <?php echo ($chatwidget_country_code == '+351') ? 'selected' : ''; ?>>🇵🇹 +351 (Portugal)</option>
                                            <option value="+32" <?php echo ($chatwidget_country_code == '+32') ? 'selected' : ''; ?>>🇧🇪 +32 (Belgium)</option>
                                            <option value="+45" <?php echo ($chatwidget_country_code == '+45') ? 'selected' : ''; ?>>🇩🇰 +45 (Denmark)</option>
                                            <option value="+420" <?php echo ($chatwidget_country_code == '+420') ? 'selected' : ''; ?>>🇨🇿 +420 (Czech Republic)</option>
                                            <option value="+36" <?php echo ($chatwidget_country_code == '+36') ? 'selected' : ''; ?>>🇭🇺 +36 (Hungary)</option>
                                            <option value="+47" <?php echo ($chatwidget_country_code == '+47') ? 'selected' : ''; ?>>🇳🇴 +47 (Norway)</option>
                                            <option value="+41" <?php echo ($chatwidget_country_code == '+41') ? 'selected' : ''; ?>>🇨🇭 +41 (Switzerland)</option>
                                            <option value="+353" <?php echo ($chatwidget_country_code == '+353') ? 'selected' : ''; ?>>🇮🇪 +353 (Ireland)</option>
                                            <option value="+380" <?php echo ($chatwidget_country_code == '+380') ? 'selected' : ''; ?>>🇺🇦 +380 (Ukraine)</option>
                                            <option value="+994" <?php echo ($chatwidget_country_code == '+994') ? 'selected' : ''; ?>>🇦🇿 +994 (Azerbaijan)</option>
                                            <option value="+84" <?php echo ($chatwidget_country_code == '+84') ? 'selected' : ''; ?>>🇻🇳 +84 (Vietnam)</option>
                                            <option value="+998" <?php echo ($chatwidget_country_code == '+998') ? 'selected' : ''; ?>>🇺🇿 +998 (Uzbekistan)</option>
                                            <option value="+964" <?php echo ($chatwidget_country_code == '+964') ? 'selected' : ''; ?>>🇮🇶 +964 (Iraq)</option>
                                            <option value="+7" <?php echo ($chatwidget_country_code == '+7') ? 'selected' : ''; ?>>🇰🇿 +7 (Kazakhstan)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇨🇦 +1 (Canada)</option>
                                            <option value="+52" <?php echo ($chatwidget_country_code == '+52') ? 'selected' : ''; ?>>🇲🇽 +52 (Mexico)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇯🇲 +1 (Jamaica)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇹🇹 +1 (Trinidad & Tobago)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇧🇸 +1 (Bahamas)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇧🇧 +1 (Barbados)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇩🇲 +1 (Dominica)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇬🇩 +1 (Grenada)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇦🇬 +1 (Antigua & Barbuda)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇰🇳 +1 (Saint Kitts & Nevis)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇱🇨 +1 (Saint Lucia)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇻🇨 +1 (Saint Vincent & Grenadines)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇸🇻 +1 (El Salvador)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇭🇳 +1 (Honduras)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>�� +1 (Nicaragua)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>�� +1 (Costa Rica)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>�� +1 (Panama)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>�� +1 (Guatemala)</option>
                                            <option value="+1" <?php echo ($chatwidget_country_code == '+1') ? 'selected' : ''; ?>>🇧🇿 +1 (Belize)</option>
                                        </select>
                                        <input type="text"
                                            placeholder="Enter your number"
                                            name="chatwidgetflowfunnel_chat_number"
                                            value="<?php echo esc_attr($chatwidget_number); ?>"
                                            class="flex-1 min-w-0 rounded-none rounded-r-md border-gray-300 focus:ring-green-500 focus:border-green-500 h-[35px] text-sm"
                                            required>
                                    </div>
                                </div>
                                <button type="submit"
                                    name="chatwidgetflowfunnel_save"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    Save Number
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Original verification code commented out -->
                    <?php /*
                    Original WhatsApp Account verification section code here
                    ...
                */ ?>

                    <!-- Quick Stats Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xl font-semibold mb-6 text-gray-700 pb-4 border-b">Quick Stats</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-600">Total Clicks</span>
                                <span class="text-lg font-semibold text-gray-900"><?php echo esc_html($total_clicks); ?></span>
                            </div>
                            <!-- <div class="space-y-2">
                                <span class="text-sm font-medium text-gray-600">Clicks by Option</span>
                                <ul class="list-disc pl-5 text-gray-700">
                                    <?php foreach ($option_clicks as $opt => $cnt): ?>
                                        <li><strong><?php echo esc_html($opt); ?>:</strong> <?php echo esc_html($cnt); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div> -->
                        </div>
                    </div>
                </div>

                <!-- Settings Container -->
                <div id="settings-container">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Basic Settings Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-xl font-semibold mb-6 text-gray-700 pb-4 border-b">Basic Settings</h3>
                            <div class="space-y-4">
                                <!-- Button Position -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700">Button Position</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="relative flex rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                                            <!-- Removed old WhatsApp option -->
                                            <input type="radio" name="chatwidgetflowfunnel_chat_position" value="bottom-right" <?php checked(get_option('chatwidgetflowfunnel_chat_position'), 'bottom-right'); ?> class="sr-only peer">
                                            <div class="flex items-center">
                                                <div class="text-sm">Bottom Right</div>
                                            </div>
                                            <div class="absolute inset-0 rounded-lg border-2 peer-checked:border-indigo-500 pointer-events-none"></div>
                                        </label>
                                        <label class="relative flex rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                                            <!-- Removed old WhatsApp option -->
                                            <input type="radio" name="chatwidgetflowfunnel_chat_position" value="bottom-left" <?php checked(get_option('chatwidgetflowfunnel_chat_position'), 'bottom-left'); ?> class="sr-only peer">
                                            <div class="flex items-center">
                                                <div class="text-sm">Bottom Left</div>
                                            </div>
                                            <div class="absolute inset-0 rounded-lg border-2 peer-checked:border-indigo-500 pointer-events-none"></div>
                                        </label>
                                    </div>
                                </div>

                                <!-- WhatsApp Icon Style -->
                                <div class="space-y-2">
                                    <!-- Removed old WhatsApp Icon Style label -->
                                    <label class="text-sm font-medium text-gray-700">Chat Widget Icon Style</label>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="style-option flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 <?php echo $selected_icon === 'style1' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'; ?>" data-style="style1">
                                            <i class="fab fa-whatsapp text-4xl mb-2 text-[#25D366]"></i>
                                            <span class="text-sm">Classic</span>
                                        </div>
                                        <div class="style-option flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 <?php echo $selected_icon === 'style3' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'; ?>" data-style="style3">
                                            <div class="relative w-10 h-10 bg-[#25D366] rounded-full flex items-center justify-center mb-2">
                                                <i class="fab fa-whatsapp text-2xl text-white"></i>
                                            </div>
                                            <span class="text-sm">Circle</span>
                                        </div>
                                        <div class="style-option flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 <?php echo $selected_icon === 'style4' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'; ?>" data-style="style4">
                                            <div class="relative w-10 h-10 bg-[#25D366] rounded-lg flex items-center justify-center mb-2">
                                                <i class="fab fa-whatsapp text-2xl text-white"></i>
                                            </div>
                                            <span class="text-sm">Modern</span>
                                        </div>
                                    </div>
                                    <!-- Removed old WhatsApp Icon Style input -->
                                    <input type="hidden" name="chatwidgetflowfunnel_chat_icon_style" id="selected_style" value="<?php echo esc_attr($selected_icon); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Inquiry Options Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6 pb-4 border-b">
                                <h3 class="text-xl font-semibold text-gray-700">Inquiry Options</h3>
                                <button type="button"
                                    onclick="addInquiryField()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    Add Option
                                </button>
                            </div>
                            <div id="inquiry-options" class="space-y-3">
                                <?php foreach ($inquiry_options as $option) : ?>
                                    <div class="flex items-center space-x-2 p-2 rounded-lg bg-gray-50">
                                        <input type="text"
                                            name="chatwidgetflowfunnel_chat_options[]"
                                            value="<?php echo esc_attr($option); ?>"
                                            class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        <button type="button"
                                            onclick="removeInquiryField(this)"
                                            class="inline-flex items-center p-2 border border-transparent rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <script>
                                function addInquiryField() {
                                    var container = document.getElementById('inquiry-options');
                                    var div = document.createElement('div');
                                    div.className = 'flex items-center space-x-2 p-2 rounded-lg bg-gray-50';
                                    div.innerHTML = `
                                    <input type="text" name="chatwidgetflowfunnel_chat_options[]" class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" />
                                    <button type="button" onclick="removeInquiryField(this)" class="inline-flex items-center p-2 border border-transparent rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                `;
                                    container.appendChild(div);
                                }

                                function removeInquiryField(btn) {
                                    btn.parentNode.remove();
                                }
                            </script>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit"
                        name="chatwidgetflowfunnel_save"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php
}

// Add new AJAX handler for code verification
