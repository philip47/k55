<?php
/*
Plugin Name: KenPlayer Transformer
Plugin URI: http://xwpthemes.com
Description: KenPlayer Transformer - Transforms embedded video players
Version: 2.1
Author: Xwpthemes
Author URI: http://xwpthemes.com
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

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
 * Secure curl implementation
 */
if (!function_exists("ken_connect_curl")) {
    function ken_connect_curl($url) {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "XWPCHECKER");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); // Add timeout
        
        $data = curl_exec($curl);
        curl_close($curl);
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
 * Sanitize plugin options
 */
function sanitize_kenplayer_options($input) {
    if (is_string($input)) {
        return sanitize_text_field($input);
    }
    return $input;
}

/**
 * Enqueue responsive script
 */
function script_js() {
    wp_enqueue_script('kenplayer-fluidvids', plugins_url('js/fluidvids.min.js', __FILE__), array(), '1.0.0', true);
    
    $host = sanitize_text_field($_SERVER['HTTP_HOST']);
    wp_add_inline_script('kenplayer-fluidvids', "
        fluidvids.init({
            selector: ['iframe'],
            players: ['{$host}']
        });
    ");
}

/**
 * Add TinyMCE button
 */
function shortcode_button($plugin_array) {
    $plugin_array["kenplayer_button_plugin"] = plugin_dir_url(__FILE__) . "js/tinymce.js";
    return $plugin_array;
}
add_filter("mce_external_plugins", "shortcode_button");

/**
 * Register button in TinyMCE
 */
function register_kenplayer_button($buttons) {
    array_push($buttons, "kenplayer");
    return $buttons;
}

// Add responsive script if enabled
if (get_option('kenplayer_responsive') == 'yes') {
    add_action('wp_footer', 'script_js', 100);
}

/**
 * Admin settings page
 */
if (!function_exists("kenplayer_config")) {
    function kenplayer_config() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
        <div class="wrap">
            <h2>KenPlayer Transformer</h2>
            
            <form method="post" action="options.php">
                <?php 
                settings_fields('kenplayer_config');
                do_settings_sections('kenplayer_config');
                
                // Check for cURL and file_get_contents
                $curl_installed = in_array('curl', get_loaded_extensions());
                $fopen_enabled = ini_get('allow_url_fopen');
                ?>
                
                <div class="notice notice-info">
                    <p>
                        <?php if ($curl_installed): ?>
                            cURL is <span style="color:blue">installed</span> on this server.
                        <?php else: ?>
                            cURL is NOT <span style="color:red">installed</span> on this server.
                        <?php endif; ?>
                        
                        <?php if ($fopen_enabled): ?>
                            file_get_content <span style="color:blue">Enabled</span>
                        <?php else: ?>
                            file_get_content <span style="color:red">disabled</span>
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
 * Transform iframe embeds
 */
function transformer_iframe($content) {
    // Early return if transformation is disabled
    if (get_option('kenplayer_activation') != 'yes') {
        return $content;
    }
    
    // Normalize URLs
    $content = str_replace("redtube.com?id=", "redtube.com/?id=", $content);
    $content = str_replace("www.xvideos.com/embedframe", "flashservice.xvideos.com/embedframe", $content);
    
    // Define patterns for supported video sites
    $tubeservices = array(
        '\/\/flashservice.(.*).com\/embedframe\/([0-9]+)', /*xvideos.com*/
        '\/\/www.(.*).com\/embed\/([0-9]+)\/', /*youporn.com*/
        '\/\/www.(.*).com\/embed\/([A-z0-9]+)', /*pornhub.com*/
        '\/\/embed.(.*).com\/\?id=([0-9]+)', /*redtube.com*/
    );
    
    // Try to match a supported video embed
    preg_match('/'. implode('|', $tubeservices) .'/', $content, $result);
    $result = array_values(array_filter($result));
    
    if (!empty($result)) {
        $tubeserver = str_replace(array('www.', 'embed.', 'flashservice.'), '', $result[1]);
        $video = $result[2];
        
        $original = "";
        
        // Check if this video source should be transformed
        if (($tubeserver == 'xvideos') && (get_option('kenplayer_xvideos') == 'on')) {
            $original = '//flashservice.xvideos.com/embedframe/' . $video;
        } elseif (($tubeserver == 'youporn') && (get_option('kenplayer_youporn') == 'on')) {
            $original = '//www.youporn.com/embed/' . $video;
        } elseif (($tubeserver == 'pornhub') && (get_option('kenplayer_pornhub') == 'on')) {
            $original = '//www.pornhub.com/embed/' . $video;
        } elseif (($tubeserver == 'youtube') && (get_option('kenplayer_youtube') == 'on')) {
            $original = '//www.youtube.com/embed/' . $video;
        } elseif (($tubeserver == 'redtube') && (get_option('kenplayer_redtube') == 'on')) {
            $original = '//embed.redtube.com/?id=' . $video;
        }
        
        // If we have a match, replace with our custom player
        if ($original != '') {
            // Determine which player to use
            if (get_option('kenplayer_jwplayer') == 'yes') {
                $newplayer = plugins_url('/jwplayer/player.php', __FILE__) . 
                    '?tubeserver=' . urlencode($tubeserver) . 
                    '&id=' . urlencode($video) . 
                    '&etc=';
                
                if (($tubeserver == 'youtube') && (get_option('kenplayer_youtube') == 'on')) {
                    $newplayer = plugins_url('/jwplayer/player-drive.php', __FILE__) . 
                        '?tubeserver=' . urlencode(base64_encode('https://www.youtube.com/watch?v=' . $video));
                }
            } else {
                $newplayer = plugins_url('/player/player.php', __FILE__) . 
                    '?tubeserver=' . urlencode($tubeserver) . 
                    '&id=' . urlencode($video) . 
                    '&etc=';
                
                if (($tubeserver == 'youtube') && (get_option('kenplayer_youtube') == 'on')) {
                    $newplayer = plugins_url('/player/player-drive.php', __FILE__) . 
                        '?tubeserver=' . urlencode(base64_encode('https://www.youtube.com/watch?v=' . $video));
                }
            }
            
            // Replace the original embed with our custom player
            $content = str_replace($original, $newplayer, $content);
        }
        
        // Make URLs protocol-relative and add security attributes to iframes
        $content = str_replace(array('http:', 'https:'), '', $content);
        $content = str_replace(
            'iframe src', 
            'iframe allowfullscreen="true" sandbox="allow-scripts allow-same-origin" src', 
            $content
        );
    }
    
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
 */
function shortcode_videos_transformer($atts, $link = null) {
    // Extract attributes
    $atts = shortcode_atts(array(
        'url' => $link,
        'width' => '735',
        'height' => '400'
    ), $atts);
    
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
        preg_match('/\/\/www.xvideos.com\/video([0-9]+)\//', $link, $idxvideos);
        $tubeserver = 'xvideos';
        $video = isset($idxvideos[1]) ? $idxvideos[1] : '';
    } elseif (stristr($link, 'pornhub.com')) {
        $idpornhub = substr($link, strpos($link, '?viewkey=') + 9);
        $tubeserver = 'pornhub';
        $video = $idpornhub;
    } elseif (stristr($link, 'redtube.com')) {
        $idredtube = substr($link, strpos($link, 'redtube.com/') + 12);
        $tubeserver = 'redtube';
        $video = $idredtube;
    } elseif (stristr($link, 'youporn.com') || stristr($link, 'youporngay.com')) {
        preg_match('/\/watch\/([0-9]+)\//', $link, $idyouporn);
        $tubeserver = 'youporn';
        $video = isset($idyouporn[1]) ? $idyouporn[1] : '';
    } elseif (endsWith($link, '.mp4') || endsWith($link, '.flv')) {
        if (get_option('kenplayer_jwplayer') == 'yes') {
            $urlPlayer = plugins_url("jwplayer/player-direct.php", __FILE__) . 
                "?tubeserver=" . urlencode(base64_encode($link));
        } else {
            $urlPlayer = plugins_url("player/player-direct.php", __FILE__) . 
                "?tubeserver=" . urlencode(base64_encode($link));
        }
    }
    
    // Generate player URL based on video service
    if (empty($urlPlayer)) {
        if (get_option('kenplayer_jwplayer') == 'yes') {
            if ($tubeserver != "" && $video != "") {
                $urlPlayer = plugins_url("jwplayer/player.php", __FILE__) . 
                    "?tubeserver=" . urlencode($tubeserver) . 
                    "&id=" . urlencode($video);
            }
            
            // Check for other supported services
            foreach ($datas as $data) {
                if (stristr($link, $data)) {
                    $urlPlayer = plugins_url("jwplayer/player-drive.php", __FILE__) . 
                        "?tubeserver=" . urlencode(base64_encode($link));
                    break;
                }
            }
        } else {
            if ($tubeserver != "" && $video != "") {
                $urlPlayer = plugins_url("player/player.php", __FILE__) . 
                    "?tubeserver=" . urlencode($tubeserver) . 
                    "&id=" . urlencode($video);
            }
            
            // Check for other supported services
            foreach ($datas as $data) {
                if (stristr($link, $data)) {
                    $urlPlayer = plugins_url("player/player-drive.php", __FILE__) . 
                        "?tubeserver=" . urlencode(base64_encode($link));
                    break;
                }
            }
        }
    }
    
    // Return iframe with player
    if (!empty($urlPlayer)) {
        return '<iframe src="' . esc_url($urlPlayer) . '" frameborder="0" allowfullscreen mozallowfullscreen webkitallowfullscreen msallowfullscreen width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"></iframe>';
    }
    
    return '<!-- No valid video URL provided -->';
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