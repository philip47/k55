<?php
/*
Plugin Name: KenPlayer Transformer
Plugin URI: http://xwpthemes.com
Description: KenPlayer Transformer - Transforms embedded video players from adult video sites
Version: 3.0.0
Author: Xwpthemes
Author URI: http://xwpthemes.com
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 7.4
Network: false
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: kenplayer-transformer
Domain Path: /languages
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KENPLAYER_VERSION', '3.0.0');
define('KENPLAYER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KENPLAYER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KENPLAYER_PLUGIN_BASENAME', plugin_basename(__FILE__));

if (!defined('PRODUCT_PREFIX')) {
    define('PRODUCT_PREFIX', 'YWN0aXZhdGVk');
}

/**
 * Add admin menu
 */
function magic_iframe_player_menu() {
    add_action('admin_init', 'update_ken_transformer');
}

/**
 * Set default settings on plugin activation
 */
function kenplayer_set_my_default_settings() {
    $defaults = array(
        'kenplayer_logo' => 'http://i.imgur.com/8ZQrXIK.png',
        'kenplayer_activation' => 'yes',
        'kenplayer_jwplayer' => 'yes',
        'kenplayer_responsive' => 'no',
        'kenplayer_xvideos' => 'on',
        'kenplayer_redtube' => 'on',
        'kenplayer_youporn' => 'on',
        'kenplayer_pornhub' => 'on',
    );
    
    foreach ($defaults as $key => $value) {
        update_option($key, $value);
    }
}
register_activation_hook(__FILE__, 'kenplayer_set_my_default_settings');

/**
 * Secure HTTP request implementation with proper validation and caching
 */
if (!function_exists("ken_connect_curl")) {
    function ken_connect_curl($url, $timeout = 10) {
        // Validate URL and ensure it's HTTPS when possible
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Check if URL is from allowed domains for security
        $allowed_domains = array(
            'xvideos.com',
            'pornhub.com', 
            'redtube.com',
            'youporn.com',
            'xhamster.com',
            'youtube.com',
            'drive.google.com'
        );
        
        $parsed_url = parse_url($url);
        $domain = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $domain = preg_replace('/^www\./', '', $domain);
        
        if (!in_array($domain, $allowed_domains)) {
            return false;
        }
        
        // Cache functionality removed to reduce database space
        
        // Use WordPress HTTP API for better security and compatibility
        $args = array(
            'timeout' => $timeout,
            'user-agent' => 'KenPlayer/' . KENPLAYER_VERSION . ' (WordPress)',
            'sslverify' => true,
            'headers' => array(
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate'
            )
        );
        
        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $data = wp_remote_retrieve_body($response);
        
        // Cache functionality removed to reduce database space
        
        return $data;
    }
}

/**
 * Register plugin settings
 */
if (!function_exists("update_ken_transformer")) {
    function update_ken_transformer() {
        $settings = array(
            'kenplayer_logo', 'kenplayer_logo_url', 'kenplayer_ads',
            'kenplayer_poster', 'kenplayer_seconds', 'kenplayer_cache',
            'kenplayer_activation', 'kenplayer_customfield', 'kenplayer_jwplayer',
            'kenplayer_xvideos', 'kenplayer_redtube', 'kenplayer_youporn',
            'kenplayer_pornhub', 'kenplayer_youtube', 'kenplayer_xhamster',
            'kenplayer_responsive'
        );
        
        foreach ($settings as $setting) {
            register_setting('kenplayer_config', $setting, 'sanitize_kenplayer_options');
        }
    }
}

/**
 * Sanitize plugin options with enhanced validation
 */
function sanitize_kenplayer_options($input) {
    if (is_string($input)) {
        // Special handling for URLs
        if (filter_var($input, FILTER_VALIDATE_URL)) {
            return esc_url_raw($input);
        }
        // Special handling for HTML content (ads)
        if (strpos($input, '<') !== false) {
            return wp_kses($input, array(
                'a' => array('href' => array(), 'target' => array()),
                'img' => array('src' => array(), 'alt' => array(), 'width' => array(), 'height' => array()),
                'div' => array('class' => array(), 'id' => array()),
                'span' => array('class' => array(), 'id' => array()),
                'script' => array('src' => array(), 'type' => array()),
                'iframe' => array('src' => array(), 'width' => array(), 'height' => array(), 'frameborder' => array())
            ));
        }
        return sanitize_text_field($input);
    }
    return $input;
}

/**
 * Enqueue responsive script with proper versioning and security
 */
function kenplayer_enqueue_scripts() {
    // Only load on frontend
    if (is_admin()) {
        return;
    }
    
    // Check if responsive mode is enabled
    if (get_option('kenplayer_responsive') !== 'yes') {
        return;
    }
    
    // Enqueue fluidvids script with proper versioning
    wp_enqueue_script(
        'kenplayer-fluidvids', 
        KENPLAYER_PLUGIN_URL . 'js/fluidvids.js', 
        array(), 
        KENPLAYER_VERSION, 
        true
    );
    
    // Safely add inline script with proper escaping
    $host = isset($_SERVER['HTTP_HOST']) ? esc_js(sanitize_text_field($_SERVER['HTTP_HOST'])) : '';
    $inline_script = "
        if (typeof fluidvids !== 'undefined') {
            fluidvids.init({
                selector: ['iframe'],
                players: ['" . $host . "']
            });
        }
    ";
    
    wp_add_inline_script('kenplayer-fluidvids', $inline_script);
}

/**
 * Add TinyMCE button with proper capability checks
 */
function kenplayer_add_tinymce_button($plugin_array) {
    // Only add for users who can edit posts
    if (!current_user_can('edit_posts')) {
        return $plugin_array;
    }
    
    $plugin_array["kenplayer_button_plugin"] = KENPLAYER_PLUGIN_URL . "js/tinymce.js";
    return $plugin_array;
}

/**
 * Register button in TinyMCE with capability checks
 */
function kenplayer_register_tinymce_button($buttons) {
    // Only add for users who can edit posts
    if (!current_user_can('edit_posts')) {
        return $buttons;
    }
    
    array_push($buttons, "kenplayer");
    return $buttons;
}

/**
 * Initialize plugin hooks and actions
 */
function kenplayer_init() {
    // Load text domain for translations
    load_plugin_textdomain('kenplayer-transformer', false, dirname(KENPLAYER_PLUGIN_BASENAME) . '/languages');
    
    // Add responsive script if enabled
    add_action('wp_enqueue_scripts', 'kenplayer_enqueue_scripts');
    
    // Add TinyMCE integration for editors
    add_filter("mce_external_plugins", "kenplayer_add_tinymce_button");
    add_filter("mce_buttons", "kenplayer_register_tinymce_button");
    
    // Always register the shortcode so it works even if activation is pending
    add_shortcode("kenplayer", "shortcode_videos_transformer");
}
add_action('init', 'kenplayer_init');

/**
 * Settings validation callback
 */
function kenplayer_validate_settings($input) {
    $validated = array();
    
    // Validate each setting
    if (isset($input['kenplayer_license'])) {
        $validated['kenplayer_license'] = sanitize_text_field($input['kenplayer_license']);
    }
    
    if (isset($input['kenplayer_player'])) {
        $validated['kenplayer_player'] = sanitize_text_field($input['kenplayer_player']);
    }
    
    if (isset($input['kenplayer_width'])) {
        $validated['kenplayer_width'] = absint($input['kenplayer_width']);
    }
    
    if (isset($input['kenplayer_height'])) {
        $validated['kenplayer_height'] = absint($input['kenplayer_height']);
    }
    
    return $validated;
}

/**
 * Admin settings page with enhanced security
 */
if (!function_exists("kenplayer_config")) {
    function kenplayer_config() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'kenplayer-transformer'));
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('KenPlayer Transformer Settings', 'kenplayer-transformer'); ?></h1>
            
            <?php settings_errors(); ?>
            
            <form method="post" action="options.php">
                <?php 
                settings_fields('kenplayer_config');
                do_settings_sections('kenplayer_config');
                
                // Check for cURL and HTTP capabilities
                $curl_installed = function_exists('curl_init');
                $wp_http_available = function_exists('wp_remote_get');
                ?>
                
                <div class="notice notice-info">
                    <p>
                        <strong><?php esc_html_e('Server Capabilities:', 'kenplayer-transformer'); ?></strong><br>
                        <?php if ($curl_installed): ?>
                            <?php esc_html_e('cURL:', 'kenplayer-transformer'); ?> <span style="color:green"><?php esc_html_e('Available', 'kenplayer-transformer'); ?></span><br>
                        <?php else: ?>
                            <?php esc_html_e('cURL:', 'kenplayer-transformer'); ?> <span style="color:red"><?php esc_html_e('Not Available', 'kenplayer-transformer'); ?></span><br>
                        <?php endif; ?>
                        
                        <?php if ($wp_http_available): ?>
                            <?php esc_html_e('WordPress HTTP API:', 'kenplayer-transformer'); ?> <span style="color:green"><?php esc_html_e('Available', 'kenplayer-transformer'); ?></span>
                        <?php else: ?>
                            <?php esc_html_e('WordPress HTTP API:', 'kenplayer-transformer'); ?> <span style="color:red"><?php esc_html_e('Not Available', 'kenplayer-transformer'); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Active the transformation?</th>
                        <td>
                            <select name="kenplayer_activation">
                                <option value="no" <?php selected(get_option('kenplayer_activation'), 'no'); ?>>Deactive</option>
                                <option value="yes" <?php selected(get_option('kenplayer_activation'), 'yes'); ?>>Active</option>
                            </select>
                            <p class="description">Active the feature transform all default embed code to videojs player?</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Custom player</th>
                        <td>
                            <select name="kenplayer_jwplayer">
                                <option value="no" <?php selected(get_option('kenplayer_jwplayer'), 'no'); ?>>VideoJS</option>
                                <option value="yes" <?php selected(get_option('kenplayer_jwplayer'), 'yes'); ?>>JWPlayer</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Affect with:</th>
                        <td>
                            <label>
                                <input type="checkbox" name="kenplayer_xvideos" <?php checked(get_option('kenplayer_xvideos'), 'on'); ?>>
                                Xvideos
                            </label><br>
                            
                            <label>
                                <input type="checkbox" name="kenplayer_youporn" <?php checked(get_option('kenplayer_youporn'), 'on'); ?>>
                                Youporn
                            </label><br>
                            
                            <label>
                                <input type="checkbox" name="kenplayer_redtube" <?php checked(get_option('kenplayer_redtube'), 'on'); ?>>
                                Redtube
                            </label><br>
                            
                            <label>
                                <input type="checkbox" name="kenplayer_pornhub" <?php checked(get_option('kenplayer_pornhub'), 'on'); ?>>
                                Pornhub(test)
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">URL of LOGO:</th>
                        <td>
                            <input type="text" name="kenplayer_logo" value="<?php echo esc_attr(get_option('kenplayer_logo')); ?>"/>
                            <p class="description">Include http://</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">LOGO link to URL:</th>
                        <td>
                            <input type="text" name="kenplayer_logo_url" value="<?php echo esc_attr(get_option('kenplayer_logo_url')); ?>"/>
                            <p class="description">Include http:// (leave blank to link to homepage)</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Default poster:</th>
                        <td>
                            <input type="text" name="kenplayer_poster" value="<?php echo esc_attr(get_option('kenplayer_poster')); ?>"/>
                            <p class="description">Include http://</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Insert advertising HTML code:</th>
                        <td>
                            <textarea style="width: 250px; height: 150px;" name="kenplayer_ads"><?php echo esc_textarea(get_option('kenplayer_ads')); ?></textarea>
                            <p class="description">Leave it blank to deactivate.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Seconds display advertising:</th>
                        <td>
                            <input type="text" name="kenplayer_seconds" value="<?php echo esc_attr(get_option('kenplayer_seconds')); ?>" placeholder="10"/>
                            <p class="description">If left blank by default is 10 seconds.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Custom field of embed code:</th>
                        <td>
                            <input type="text" name="kenplayer_customfield" value="<?php echo esc_attr(get_option('kenplayer_customfield')); ?>" placeholder="embed_code"/>
                            <p class="description">Put the custom field name of the embed code if you use embed code in custom field.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Active the Responsive with Fluidvids?</th>
                        <td>
                            <select name="kenplayer_responsive">
                                <option value="no" <?php selected(get_option('kenplayer_responsive'), 'no'); ?>>Default theme option</option>
                                <option value="yes" <?php selected(get_option('kenplayer_responsive'), 'yes'); ?>>Responsive with Fluidvids</option>
                            </select>
                            <p class="description">Apply responsive with Fluidvids to videojs player?</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

/**
 * Transform iframe embeds with enhanced security and performance
 */
function transformer_iframe($content) {
    // Early return if transformation is disabled
    if (get_option('kenplayer_activation') !== 'yes') {
        return $content;
    }
    
    // Cache functionality removed to reduce database space
    
    // Normalize URLs safely
    $content = str_replace("redtube.com?id=", "redtube.com/?id=", $content);
    $content = str_replace("www.xvideos.com/embedframe", "flashservice.xvideos.com/embedframe", $content);
    
    // Define patterns for supported video sites with improved regex
    $tubeservices = array(
        '\/\/flashservice\.([a-zA-Z0-9\-\.]+)\.com\/embedframe\/([0-9]+)', // xvideos.com
        '\/\/www\.([a-zA-Z0-9\-\.]+)\.com\/embed\/([0-9]+)\/', // youporn.com
        '\/\/www\.([a-zA-Z0-9\-\.]+)\.com\/embed\/([A-Za-z0-9\-_]+)', // pornhub.com
        '\/\/embed\.([a-zA-Z0-9\-\.]+)\.com\/\?id=([0-9]+)', // redtube.com
    );
    
    // Try to match a supported video embed with better validation
    $pattern = '/(' . implode('|', $tubeservices) . ')/';
    preg_match($pattern, $content, $result);
    
    if (empty($result)) {
        // Cache functionality removed to reduce database space
        return $content;
    }
    
    // Extract and validate video information
    $result = array_values(array_filter($result));
    
    if (!empty($result) && count($result) >= 3) {
        // Sanitize and validate extracted data
        $tubeserver = sanitize_text_field(str_replace(array('www.', 'embed.', 'flashservice.'), '', $result[1]));
        $video = sanitize_text_field($result[2]);
        
        // Validate video ID format
        if (empty($video) || !preg_match('/^[A-Za-z0-9\-_]+$/', $video)) {
            // Cache functionality removed to reduce database space
            return $content;
        }
        
        $original = "";
        $allowed_servers = array('xvideos', 'youporn', 'pornhub', 'youtube', 'redtube');
        
        // Check if this video source should be transformed
        if (in_array($tubeserver, $allowed_servers)) {
            switch ($tubeserver) {
                case 'xvideos':
                    if (get_option('kenplayer_xvideos') === 'on') {
                        $original = '//flashservice.xvideos.com/embedframe/' . $video;
                    }
                    break;
                case 'youporn':
                    if (get_option('kenplayer_youporn') === 'on') {
                        $original = '//www.youporn.com/embed/' . $video;
                    }
                    break;
                case 'pornhub':
                    if (get_option('kenplayer_pornhub') === 'on') {
                        $original = '//www.pornhub.com/embed/' . $video;
                    }
                    break;
                case 'youtube':
                    if (get_option('kenplayer_youtube') === 'on') {
                        $original = '//www.youtube.com/embed/' . $video;
                    }
                    break;
                case 'redtube':
                    if (get_option('kenplayer_redtube') === 'on') {
                        $original = '//embed.redtube.com/?id=' . $video;
                    }
                    break;
            }
        }
        
        // If we have a match, replace with our custom player
        if (!empty($original)) {
            // Generate secure player URL with nonce
            $nonce = wp_create_nonce('kenplayer_video_' . $video);
            
            // Determine which player to use
            if (get_option('kenplayer_jwplayer') === 'yes') {
                $newplayer = add_query_arg(array(
                    'tubeserver' => $tubeserver,
                    'id' => $video,
                    'nonce' => $nonce
                ), KENPLAYER_PLUGIN_URL . 'jwplayer/player.php');
                
                if ($tubeserver === 'youtube') {
                    $newplayer = add_query_arg(array(
                        'tubeserver' => base64_encode('https://www.youtube.com/watch?v=' . $video),
                        'nonce' => $nonce
                    ), KENPLAYER_PLUGIN_URL . 'jwplayer/player-drive.php');
                }
            } else {
                $newplayer = add_query_arg(array(
                    'tubeserver' => $tubeserver,
                    'id' => $video,
                    'nonce' => $nonce
                ), KENPLAYER_PLUGIN_URL . 'player/player.php');
                
                if ($tubeserver === 'youtube') {
                    $newplayer = add_query_arg(array(
                        'tubeserver' => base64_encode('https://www.youtube.com/watch?v=' . $video),
                        'nonce' => $nonce
                    ), KENPLAYER_PLUGIN_URL . 'player/player-drive.php');
                }
            }
            
            // Replace the original embed with our custom player
            $content = str_replace($original, esc_url($newplayer), $content);
        }
        
        // Add security attributes to iframes and make URLs protocol-relative
        $content = preg_replace(
            '/<iframe([^>]*?)src=(["\'])([^"\']*?)\2([^>]*?)>/i',
            '<iframe$1src=$2$3$2$4 allowfullscreen="true" sandbox="allow-scripts allow-same-origin allow-forms">',
            $content
        );
        
        // Make URLs protocol-relative for better HTTPS compatibility
        $content = preg_replace('/https?:\/\//', '//', $content);
    }
    
    // Cache functionality removed to reduce database space
    
    return $content;
}

/**
 * Safer implementation of output buffering
 */
function transformer_start() {
    // Only start output buffering if transformation is enabled
    if (get_option('kenplayer_activation') == 'yes') {
        ob_start();
    }
}

function transformer_end() {
    // Only process output buffer if transformation is enabled
    if (get_option('kenplayer_activation') == 'yes' && ob_get_level()) {
        $content = ob_get_clean();
        echo transformer_iframe($content);
    } else if (ob_get_level()) {
        ob_end_flush();
    }
}

/**
 * Shortcode for embedding videos
 * Usage: [kenplayer url="VIDEO_URL" width="735" height="400"]
 */
function shortcode_videos_transformer($atts, $link = null) {
    // Extract attributes
    $atts = shortcode_atts(array(
        'url' => $link,
        'width' => '735',
        'height' => '400'
    ), $atts);
    
    // Debug: If no URL provided, show usage instructions
    if (empty($atts['url']) && empty($link)) {
        return '<div style="border: 2px dashed #ccc; padding: 15px; margin: 10px 0; background: #f9f9f9;">
            <strong>KenPlayer Shortcode Usage:</strong><br>
            <code>[kenplayer url="VIDEO_URL"]</code><br>
            <small>Supported sites: XVideos, Pornhub, RedTube, YouPorn, XHamster, direct MP4/FLV/WebM files, Google Drive, YouTube</small><br>
            <small>Optional parameters: width="735" height="400"</small>
        </div>';
    }
    
    $link = $atts['url'];
    $width = intval($atts['width']);
    $height = intval($atts['height']);
    
    // Validate dimensions
    if ($width < 100 || $width > 1920) $width = 735;
    if ($height < 100 || $height > 1080) $height = 400;
    
    // List of supported video services
    $datas = array(
        'drive.google.com',
        'www.youtube.com',
    );
    
    $tubeserver = '';
    $video = '';
    $urlPlayer = '';
    
    // Parse video URL to extract service and video ID
    if (stristr($link, 'xvideos.com')) {
        // Updated regex to handle both numeric and alphanumeric video IDs
        // Matches: /video123456/ or /video.abc123def/ or /video123456/title
        if (preg_match('/\/video\.?([a-zA-Z0-9_\-\.]+)(?:\/|$)/', $link, $idxvideos)) {
            $tubeserver = 'xvideos';
            $video = $idxvideos[1];
        }
    } elseif (stristr($link, 'pornhub.com')) {
        // Handle both viewkey parameter and embed URLs
        if (strpos($link, '?viewkey=') !== false) {
            $viewkey_pos = strpos($link, '?viewkey=') + 9;
            $video_id = substr($link, $viewkey_pos);
            // Remove any additional parameters
            if (strpos($video_id, '&') !== false) {
                $video_id = substr($video_id, 0, strpos($video_id, '&'));
            }
            $tubeserver = 'pornhub';
            $video = $video_id;
        } elseif (preg_match('/\/embed\/([a-zA-Z0-9]+)/', $link, $matches)) {
            $tubeserver = 'pornhub';
            $video = $matches[1];
        }
    } elseif (stristr($link, 'redtube.com')) {
        // Handle RedTube URLs: /123456 or /123456/title
        if (preg_match('/redtube\.com\/([0-9]+)(?:\/|$)/', $link, $matches)) {
            $tubeserver = 'redtube';
            $video = $matches[1];
        }
    } elseif (stristr($link, 'youporn.com') || stristr($link, 'youporngay.com')) {
        // Handle YouPorn URLs: /watch/123456/ or /watch/123456/title
        if (preg_match('/\/watch\/([0-9]+)(?:\/|$)/', $link, $idyouporn)) {
            $tubeserver = 'youporn';
            $video = $idyouporn[1];
        }
    } elseif (stristr($link, 'xhamster.com')) {
        // Add XHamster support: /videos/title-123456 or /movies/123456/title
        if (preg_match('/\/(?:videos|movies)\/(?:[^\/]*-)?([0-9]+)(?:\/|$)/', $link, $matches)) {
            $tubeserver = 'xhamster';
            $video = $matches[1];
        }
    } elseif (endsWith($link, '.mp4') || endsWith($link, '.flv') || endsWith($link, '.webm') || endsWith($link, '.m4v')) {
        $encoded_link = base64_encode($link);
        $nonce = wp_create_nonce('kenplayer_video_direct');
        if (get_option('kenplayer_jwplayer') == 'yes') {
            $urlPlayer = plugins_url("jwplayer/player-direct.php", __FILE__) . 
                "?tubeserver=" . urlencode($encoded_link) . "&nonce=" . urlencode($nonce);
        } else {
            $urlPlayer = plugins_url("player/player-direct.php", __FILE__) . 
                "?tubeserver=" . urlencode($encoded_link) . "&nonce=" . urlencode($nonce);
        }
    }
    
    // Generate player URL based on video service
    if (empty($urlPlayer)) {
        if (get_option('kenplayer_jwplayer') == 'yes') {
            if ($tubeserver != "" && $video != "") {
                $nonce = wp_create_nonce('kenplayer_video_' . $video);
                $urlPlayer = plugins_url("jwplayer/player.php", __FILE__) . 
                    "?tubeserver=" . urlencode($tubeserver) . 
                    "&id=" . urlencode($video) . 
                    "&nonce=" . urlencode($nonce);
            }
            
            // Check for other supported services
            foreach ($datas as $data) {
                if (stristr($link, $data)) {
                    $encoded_link = base64_encode($link);
                    $nonce = wp_create_nonce('kenplayer_video_drive');
                    $urlPlayer = plugins_url("jwplayer/player-drive.php", __FILE__) . 
                        "?tubeserver=" . urlencode($encoded_link) . 
                        "&nonce=" . urlencode($nonce);
                    break;
                }
            }
        } else {
            if ($tubeserver != "" && $video != "") {
                $nonce = wp_create_nonce('kenplayer_video_' . $video);
                $urlPlayer = plugins_url("player/player.php", __FILE__) . 
                    "?tubeserver=" . urlencode($tubeserver) . 
                    "&id=" . urlencode($video) . 
                    "&nonce=" . urlencode($nonce);
            }
            
            // Check for other supported services
            foreach ($datas as $data) {
                if (stristr($link, $data)) {
                    $encoded_link = base64_encode($link);
                    $nonce = wp_create_nonce('kenplayer_video_drive');
                    $urlPlayer = plugins_url("player/player-drive.php", __FILE__) . 
                        "?tubeserver=" . urlencode($encoded_link) . 
                        "&nonce=" . urlencode($nonce);
                    break;
                }
            }
        }
    }
    
    // Return iframe with player
    if (!empty($urlPlayer)) {
        return '<iframe src="' . esc_url($urlPlayer) . '" frameborder="0" allowfullscreen mozallowfullscreen webkitallowfullscreen msallowfullscreen width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"></iframe>';
    }
    
    // Debug: Show what URL was provided and why it failed
    $debug_info = '';
    if (WP_DEBUG) {
        $debug_info = '<br><small>Debug: tubeserver="' . esc_html($tubeserver) . '", video="' . esc_html($video) . '"</small>';
    }
    
    return '<div style="border: 2px solid #ff6b6b; padding: 15px; margin: 10px 0; background: #ffe6e6; color: #d63031;">
        <strong>KenPlayer Error:</strong> Unsupported video URL<br>
        <small>URL provided: ' . esc_html($link) . '</small><br>
        <small>Supported sites: XVideos, Pornhub, RedTube, YouPorn, XHamster, direct MP4/FLV/WebM files, Google Drive, YouTube</small>
        ' . $debug_info . '
    </div>';
}

/**
 * Helper function to check if string ends with a specific substring
 */
function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    
    return (substr($haystack, -$length) === $needle);
}

/**
 * Plugin cleanup on uninstall
 */
function kenplayer_uninstall() {
    // Remove all plugin options
    $options = array(
        'kenplayer_logo', 'kenplayer_logo_url', 'kenplayer_ads',
        'kenplayer_poster', 'kenplayer_seconds', 'kenplayer_cache',
        'kenplayer_activation', 'kenplayer_customfield', 'kenplayer_jwplayer',
        'kenplayer_xvideos', 'kenplayer_redtube', 'kenplayer_youporn',
        'kenplayer_pornhub', 'kenplayer_youtube', 'kenplayer_xhamster',
        'kenplayer_responsive', 'kenplayer_transformer_connect_status_ok',
        'ken_transformer_license_key_ok'
    );
    
    foreach ($options as $option) {
        delete_option($option);
    }
    
    // Clear all plugin transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_kenplayer_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_kenplayer_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_video_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_video_%'");
}
register_uninstall_hook(__FILE__, 'kenplayer_uninstall');

/**
 * Plugin deactivation cleanup
 */
function kenplayer_deactivate() {
    // Clear scheduled events if any
    wp_clear_scheduled_hook('kenplayer_cleanup_cache');
    
    // Clear transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_kenplayer_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_kenplayer_%'");
}
register_deactivation_hook(__FILE__, 'kenplayer_deactivate');

/**
 * Add security headers for player pages
 */
function kenplayer_add_security_headers() {
    if (isset($_GET['kenplayer']) || strpos($_SERVER['REQUEST_URI'], '/player/') !== false) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'kenplayer_add_security_headers');

// Include additional functionality
include dirname(__FILE__) . "/create_tag_func.php";

/**
 * Get post thumbnail
 */
function kenplayer_get_thumbnail($size = 'thumbnail', $attributes = '') {
    global $post;
    $ketqua = "";
    
    if (has_post_thumbnail($post->ID)) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
        $ketqua = $image[0];
    }
    
    return $ketqua;
}

/**
 * Get first image from post content
 */
function kenplayer_get_first_image() {
    global $post;
    $first_img = '';
    
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    if (isset($matches[1][0])) {
        $first_img = $matches[1][0];
    }
    
    return $first_img;
}

/**
 * Get thumbnail image from various sources
 */
function kenplayer_get_thumb_image() {
    global $post;
    $thumb = "";
    
    $thumb = get_post_meta(get_the_ID(), "thumb", true);
    
    if (empty($thumb)) {
        $thumb = kenplayer_get_thumbnail();
    }
    
    if (empty($thumb)) {
        $thumb = kenplayer_get_first_image();
    }
    
    return $thumb;
}

/**
 * Filter for custom field with embed code
 */
function kenplayer_custom_field_filter($metadata, $object_id, $meta_key, $single) {
    global $post, $wpdb;
    
    // Only process if we have a custom field set and it matches
    $custom_field = get_option('kenplayer_customfield');
    if (!empty($custom_field) && $meta_key == $custom_field) {
        // Get the meta value securely
        $value = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
            $object_id,
            $meta_key
        ));
        
        if (!empty($value)) {
            $video = trim($value);
            
            // Handle direct video files
            if (endsWith($video, '.mp4') || endsWith($video, '.flv')) {
                if (get_option('kenplayer_jwplayer') == 'yes') {
                    $urlPlayer = plugins_url("jwplayer/player-direct.php", __FILE__) . 
                        "?tubeserver=" . urlencode($video);
                } else {
                    $urlPlayer = plugins_url("player/player-direct.php", __FILE__) . 
                        "?tubeserver=" . urlencode($video);
                }
                
                $content = '<iframe src="' . esc_url($urlPlayer) . '" frameborder="0" allowfullscreen mozallowfullscreen webkitallowfullscreen msallowfullscreen width="735" height="400"></iframe>';
            } else {
                // Transform embed code
                $content = transformer_iframe($video);
            }
            
            return $content;
        }
    }
    
    return $metadata;
}