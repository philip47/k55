<?php
// Define constants in a secure way
if (!defined('PRODUCT_PREFIX')) {
    define('PRODUCT_PREFIX', 'YWN0aXZhdGVk'); // "activated" in base64
}

/**
 * Initialize admin menu based on activation status
 */
function kenplayer_admin_init() {
    // Get activation status using a single, secure function
    $activation = kenplayer_get_activation_status();
    
    if ($activation['is_valid']) {
        add_menu_page('KenPlayer Transformer', 'KenPlayer Transformer', 'manage_options', 'kenplayer_config', 'kenplayer_config');
    } else {
        add_menu_page('KenPlayer Transformer', 'KenPlayer Transformer', 'manage_options', 'kenplayer_config', 'ken_transformer_importer_pro_connect');
    }
}
add_action('admin_menu', 'kenplayer_admin_init');

/**
 * Centralized function to check activation status
 * Returns array with activation details
 */
function kenplayer_get_activation_status() {
    $result = array(
        'is_valid' => false,
        'site' => '',
        'status' => '',
        'email' => ''
    );
    
    if (get_option('kenplayer_transformer_connect_status_ok')) {
        try {
            $encoded_data = get_option('kenplayer_transformer_connect_status_ok');
            // Validate base64 string before decoding
            if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $encoded_data)) {
                return $result;
            }
            
            $html = base64_decode($encoded_data);
            // Basic check if the decoded string looks like XML
            if (strpos($html, '<?xml') === false) {
                return $result;
            }
            
            // Use libxml_use_internal_errors to handle XML errors gracefully
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($html);
            
            if ($xml === false) {
                libxml_clear_errors();
                return $result;
            }
            
            // Sanitize and validate the hostname
            $current_host = sanitize_text_field($_SERVER['HTTP_HOST']);
            $xml_site = isset($xml->site) ? (string)$xml->site : '';
            $xml_status = isset($xml->status) ? base64_encode((string)$xml->status) : '';
            $xml_email = isset($xml->email) ? (string)$xml->email : '';
            
            $result['site'] = $xml_site;
            $result['status'] = $xml_status;
            $result['email'] = $xml_email;
            $result['is_valid'] = ($xml_site === $current_host && $xml_status === PRODUCT_PREFIX);
        } catch (Exception $e) {
            // Log error securely
            error_log('Error in KenPlayer activation check: ' . $e->getMessage());
        }
    }
    
    return $result;
}

// Only add plugin functionality if properly activated
$activation = kenplayer_get_activation_status();
if ($activation['is_valid']) {
    add_action('admin_menu', 'magic_iframe_player_menu');
    
    if (get_option('kenplayer_activation') == 'yes') {
        add_filter('the_content', 'transformer_iframe');
        add_filter("mce_buttons", "register_kenplayer_button");
        
        if (function_exists("tubeace_video_player")) {
            add_filter('tubeace_video_player', 'transformer_iframe', 99, 1);
        }
        
        add_action('wp_head', 'transformer_start');
        add_action('wp_footer', 'transformer_end');
        add_shortcode("kenplayer", "shortcode_videos_transformer");
    }
}

/**
 * Include the connection page
 */
function ken_transformer_importer_pro_connect() {
    include dirname(__FILE__) . "/connect.php";
}

/**
 * Get plugin version safely
 */
function ken_transformer_get_version() {
    $plugin_data = get_plugin_data(__FILE__);
    $plugin_version = isset($plugin_data['Version']) ? $plugin_data['Version'] : '1.0';
    return $plugin_version;
}

/**
 * Secure HTTP request function with caching
 */
function kenplayer_remote_request($url, $referer = '', $type = null) {
    // Add transient caching to prevent excessive requests
    $cache_key = 'kenplayer_remote_' . md5($url . $type);
    $cached_response = get_transient($cache_key);
    
    if ($cached_response !== false) {
        return $cached_response;
    }
    
    $response = '';
    $user_agent = ($type == 'mobile') 
        ? 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; GT-I9300 Build/IMM76D)'
        : 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1';
    
    if (function_exists('curl_init')) {
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_TIMEOUT, 10);
        curl_setopt($c, CURLOPT_REFERER, $referer);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($c);
        curl_close($c);
    } elseif (ini_get('allow_url_fopen')) {
        $context = stream_context_create([
            'http' => [
                'user_agent' => $user_agent
            ]
        ]);
        $response = @file_get_contents($url, false, $context);
    }
    
    // Cache the response for 1 hour
    if (!empty($response)) {
        set_transient($cache_key, $response, HOUR_IN_SECONDS);
    }
    
    return $response;
}

/**
 * Handle license activation via AJAX
 */
function save_ken_transformer_importer_pro_connect() {
    // Verify nonce
    check_ajax_referer("ken_transformer_ajax");
    
    // Sanitize inputs
    $user = sanitize_email($_POST['ken_transformer_license_key_ok']);
    $order_code = sanitize_text_field($_POST['ken_transformer_order_code']);
    $site = sanitize_text_field($_SERVER['HTTP_HOST']);
    $query = sanitize_text_field($_SERVER['REQUEST_URI']);
    
    // Validate email
    if (!is_email($user)) {
        echo "<div id=\"message\" class=\"update-nag fade\" style='color:red;'>Invalid email address</div>\n";
        wp_die();
    }
    
    // Build license verification URL with proper escaping
    $product_code = 'P_TRANSF';
    $verification_url = 'https://xwpthemes.com/activation/get_lic.php';
    $verification_url = add_query_arg([
        'email' => urlencode($user),
        'product' => urlencode($product_code),
        'order_code' => urlencode($order_code),
        'site' => urlencode($site),
        'query' => urlencode($query)
    ], $verification_url);
    
    // Get response from license server
    $html = kenplayer_remote_request($verification_url, site_url());
    
    if (empty($html)) {
        echo "<div id=\"message\" class=\"update-nag fade\" style='color:red;'>Could not connect to activation server</div>\n";
        wp_die();
    }
    
    // Process response
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($html);
    
    if ($xml === false) {
        libxml_clear_errors();
        echo "<div id=\"message\" class=\"update-nag fade\" style='color:red;'>Invalid response from activation server</div>\n";
        wp_die();
    }
    
    $wp_status = isset($xml->status) ? (string)$xml->status : '';
    $wp_notice = isset($xml->notice) ? (string)$xml->notice : 'Unknown error';
    
    if (base64_encode($wp_status) == PRODUCT_PREFIX) {
        echo "<div id=\"message\" class=\"updated fade\" style='color:blue;'><p>" . esc_html($wp_notice) . "</p></div>\n";
        update_option('ken_transformer_license_key_ok', esc_attr((string)$xml->email));
        update_option('kenplayer_transformer_connect_status_ok', base64_encode($html));
    } else {
        echo "<div id=\"message\" class=\"update-nag fade\" style='color:red;'>Activation Failed! " . esc_html($wp_notice) . "</div>\n";
    }
    
    wp_die();
}
add_action('wp_ajax_ken_transformer_connect', 'save_ken_transformer_importer_pro_connect');

/**
 * Handle deactivation securely
 */
function ken_transformer_handle_deactivation() {
    // Verify user has proper permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    // Verify nonce
    if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'ken_transformer_deactivate')) {
        wp_die(__('Security check failed'));
    }
    
    // Process deactivation
    delete_option('kenplayer_transformer_connect_status_ok');
    delete_option('ken_transformer_license_key_ok');
    delete_option('kenplayer_activation');
    
    // Safer file deletion with path validation
    $transform_file = plugin_dir_path(__FILE__) . 'transform.php';
    if (file_exists($transform_file) && is_file($transform_file)) {
        @unlink($transform_file);
    }
    
    wp_die('Plugin deactivated successfully');
}

// Add a proper endpoint for deactivation
add_action('admin_post_ken_transformer_deactivate', 'ken_transformer_handle_deactivation');

// Remove the insecure direct deactivation method
// Instead, create a proper admin page with a form that submits to admin-post.php