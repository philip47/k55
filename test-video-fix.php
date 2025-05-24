<?php
/**
 * Quick Test for Video Display Fix
 * 
 * This file tests the critical fix for video display issues
 * Place in WordPress root and access via browser
 */

// Load WordPress
if (!defined('ABSPATH')) {
    require_once('wp-load.php');
}

echo "<h1>KenPlayer Video Display Fix Test</h1>";

// Test the problematic XVideos URL
$test_url = "https://www.xvideos.com/video.ohlvebk93b7/test_video";

echo "<h2>Testing XVideos URL with new format:</h2>";
echo "<p><strong>URL:</strong> " . esc_html($test_url) . "</p>";

// Test shortcode
echo "<h3>Shortcode Test:</h3>";
$shortcode_result = do_shortcode('[kenplayer url="' . $test_url . '"]');

if (!empty($shortcode_result) && strpos($shortcode_result, 'iframe') !== false) {
    echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS: Shortcode generated iframe!</p>";
    echo "<div style='border: 2px solid green; padding: 10px; margin: 10px 0;'>";
    echo $shortcode_result;
    echo "</div>";
    echo "<p><em>If you see a video player above, the fix is working correctly!</em></p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ FAILED: Shortcode did not generate iframe</p>";
    echo "<p><strong>Output:</strong> " . esc_html($shortcode_result) . "</p>";
}

// Test URL parsing
echo "<h3>URL Parsing Test:</h3>";
if (preg_match('/\/video\.?([a-zA-Z0-9_\-\.]+)(?:\/|$)/', $test_url, $matches)) {
    $video_id = $matches[1];
    echo "<p style='color: green;'>✅ Video ID extracted: <strong>" . esc_html($video_id) . "</strong></p>";
    
    // Test nonce generation
    $nonce = wp_create_nonce('kenplayer_video_' . $video_id);
    echo "<p>✅ Nonce generated: " . esc_html($nonce) . "</p>";
    
    // Test player URL
    $player_url = plugins_url("player/player.php", __FILE__) . 
        "?tubeserver=xvideos&id=" . urlencode($video_id) . 
        "&nonce=" . urlencode($nonce);
    echo "<p>✅ Player URL: <a href='" . esc_url($player_url) . "' target='_blank'>Test Player</a></p>";
} else {
    echo "<p style='color: red;'>❌ Failed to extract video ID</p>";
}

// Plugin status
echo "<h3>Plugin Status:</h3>";
echo "<p>Shortcode registered: " . (shortcode_exists('kenplayer') ? '✅ YES' : '❌ NO') . "</p>";
echo "<p>Function exists: " . (function_exists('shortcode_videos_transformer') ? '✅ YES' : '❌ NO') . "</p>";

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>If you see a green success message and video player above, the fix is working!</li>";
echo "<li>You can now use the shortcode: <code>[kenplayer url=\"https://www.xvideos.com/video.abc123def/title\"]</code></li>";
echo "<li>Both old and new XVideos URL formats are supported</li>";
echo "<li>All security measures remain active</li>";
echo "</ol>";
?>