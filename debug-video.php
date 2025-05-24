<?php
/**
 * Debug Video Display Issues - Enhanced Version
 */

// Load WordPress
if (!defined('ABSPATH')) {
    require_once('wp-load.php');
}

echo "<h2>KenPlayer Video Debug - Enhanced</h2>";

// Test URLs
$test_urls = [
    "https://www.xvideos.com/video.ohlvebk93b7/test_video",
    "https://www.xvideos.com/video12345/old_format",
    "https://www.pornhub.com/view_video.php?viewkey=ph123456789",
];

foreach ($test_urls as $test_url) {
    echo "<div style='border: 1px solid #ccc; margin: 20px 0; padding: 15px;'>";
    echo "<h3>Testing URL: " . esc_html($test_url) . "</h3>";
    
    // Test URL parsing
    $link = $test_url;
    $video_id = '';
    $tubeserver = '';
    
    if (stristr($link, 'xvideos.com')) {
        if (preg_match('/\/video\.?([a-zA-Z0-9_\-\.]+)(?:\/|$)/', $link, $idxvideos)) {
            $tubeserver = 'xvideos';
            $video_id = $idxvideos[1];
            echo "<p><strong>✓ XVideos ID extracted:</strong> " . esc_html($video_id) . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to extract XVideos ID</p>";
        }
    } elseif (stristr($link, 'pornhub.com')) {
        if (preg_match('/viewkey=([a-zA-Z0-9_\-]+)/', $link, $idpornhub)) {
            $tubeserver = 'pornhub';
            $video_id = $idpornhub[1];
            echo "<p><strong>✓ PornHub ID extracted:</strong> " . esc_html($video_id) . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to extract PornHub ID</p>";
        }
    }
    
    if (!empty($video_id) && !empty($tubeserver)) {
        // Test nonce generation
        $nonce = wp_create_nonce('kenplayer_video_' . $video_id);
        echo "<p><strong>Nonce generated:</strong> " . esc_html($nonce) . "</p>";
        
        // Test player URL generation
        $urlPlayer = plugins_url("player/player.php", __FILE__) . 
            "?tubeserver=" . urlencode($tubeserver) . 
            "&id=" . urlencode($video_id) . 
            "&nonce=" . urlencode($nonce);
        echo "<p><strong>Player URL:</strong> <a href='" . esc_url($urlPlayer) . "' target='_blank'>" . esc_html($urlPlayer) . "</a></p>";
        
        // Test shortcode
        echo "<h4>Shortcode Test:</h4>";
        $shortcode_result = do_shortcode('[kenplayer url="' . $test_url . '"]');
        echo "<p><strong>Shortcode output:</strong></p>";
        echo "<pre>" . esc_html($shortcode_result) . "</pre>";
        
        if (!empty($shortcode_result) && strpos($shortcode_result, 'iframe') !== false) {
            echo "<p style='color: green;'>✓ Shortcode generated iframe successfully</p>";
            echo "<div style='border: 1px solid #ddd; padding: 10px;'>";
            echo $shortcode_result;
            echo "</div>";
        } else {
            echo "<p style='color: red;'>✗ Shortcode failed to generate iframe</p>";
        }
    }
    
    echo "</div>";
}

// Plugin status
echo "<div style='border: 2px solid #333; margin: 20px 0; padding: 15px; background: #f9f9f9;'>";
echo "<h3>Plugin Status</h3>";
echo "<p><strong>Shortcode registered:</strong> " . (shortcode_exists('kenplayer') ? '<span style="color: green;">YES ✓</span>' : '<span style="color: red;">NO ✗</span>') . "</p>";
echo "<p><strong>Function exists:</strong> " . (function_exists('shortcode_videos_transformer') ? '<span style="color: green;">YES ✓</span>' : '<span style="color: red;">NO ✗</span>') . "</p>";
echo "<p><strong>WordPress Version:</strong> " . get_bloginfo('version') . "</p>";
echo "<p><strong>Plugin files exist:</strong></p>";
$files_to_check = [
    'transform.php',
    'player/player.php',
    'jwplayer/player.php',
    'create_tag_func.php'
];
foreach ($files_to_check as $file) {
    $file_path = dirname(__FILE__) . '/' . $file;
    echo "<p>&nbsp;&nbsp;- " . $file . ": " . (file_exists($file_path) ? '<span style="color: green;">EXISTS</span>' : '<span style="color: red;">MISSING</span>') . "</p>";
}
echo "</div>";

// Test direct player access
echo "<div style='border: 2px solid #333; margin: 20px 0; padding: 15px; background: #fff3cd;'>";
echo "<h3>Direct Player Test</h3>";
echo "<p>Test the player directly with a sample XVideos ID:</p>";
$test_id = "ohlvebk93b7";
$test_nonce = wp_create_nonce('kenplayer_video_' . $test_id);
$direct_player_url = plugins_url("player/player.php", __FILE__) . 
    "?tubeserver=xvideos&id=" . urlencode($test_id) . 
    "&nonce=" . urlencode($test_nonce);
echo "<p><a href='" . esc_url($direct_player_url) . "' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px;'>Test Player Directly</a></p>";
echo "<p><small>This will open the player in a new tab. If you see a video player, the fix is working!</small></p>";
echo "</div>";
?>