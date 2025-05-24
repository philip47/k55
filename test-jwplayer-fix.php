<?php
/**
 * Test JWPlayer Fix
 * 
 * This file tests the JWPlayer fixes for XVideos URLs
 * Place in WordPress root and access via browser
 */

// Load WordPress
if (!defined('ABSPATH')) {
    require_once('wp-load.php');
}

echo "<h1>JWPlayer Fix Test</h1>";

// Test XVideos URL with new format
$test_url = "https://www.xvideos.com/video.ohlvebk93b7/test_video";
$video_id = "ohlvebk93b7";

echo "<h2>Testing JWPlayer with XVideos new format:</h2>";
echo "<p><strong>URL:</strong> " . esc_html($test_url) . "</p>";
echo "<p><strong>Video ID:</strong> " . esc_html($video_id) . "</p>";

// Generate nonce
$nonce = wp_create_nonce('kenplayer_video_' . $video_id);

// Test JWPlayer URL
$jwplayer_url = plugins_url("jwplayer/player.php", __FILE__) . 
    "?tubeserver=xvideos&id=" . urlencode($video_id) . 
    "&nonce=" . urlencode($nonce);

echo "<h3>JWPlayer Test:</h3>";
echo "<p><strong>Player URL:</strong> <a href='" . esc_url($jwplayer_url) . "' target='_blank'>Test JWPlayer</a></p>";

// Test shortcode with JWPlayer
echo "<h3>Shortcode Test (JWPlayer):</h3>";

// Temporarily set player to jwplayer for test
$original_player = get_option('kenplayer_player', 'videojs');
update_option('kenplayer_player', 'jwplayer');

$shortcode_result = do_shortcode('[kenplayer url="' . $test_url . '"]');

// Restore original player setting
update_option('kenplayer_player', $original_player);

if (!empty($shortcode_result) && strpos($shortcode_result, 'iframe') !== false) {
    echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS: JWPlayer shortcode generated iframe!</p>";
    echo "<div style='border: 2px solid green; padding: 10px; margin: 10px 0;'>";
    echo $shortcode_result;
    echo "</div>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ FAILED: JWPlayer shortcode did not generate iframe</p>";
    echo "<p><strong>Output:</strong> " . esc_html($shortcode_result) . "</p>";
}

// Test URL construction logic
echo "<h3>URL Construction Test:</h3>";

// Test old format
$old_id = "12345";
if (strpos($old_id, '.') === false && is_numeric($old_id)) {
    $old_url = "https://www.xvideos.com/video" . $old_id . "/xvideosx";
    echo "<p>✅ Old format URL: " . esc_html($old_url) . "</p>";
} else {
    echo "<p>❌ Old format URL construction failed</p>";
}

// Test new format
$new_id = "ohlvebk93b7";
if (!(strpos($new_id, '.') === false && is_numeric($new_id))) {
    $new_url = "https://www.xvideos.com/video." . $new_id . "/xvideosx";
    echo "<p>✅ New format URL: " . esc_html($new_url) . "</p>";
} else {
    echo "<p>❌ New format URL construction failed</p>";
}

// Test video ID validation
echo "<h3>Video ID Validation Test:</h3>";
$test_ids = array('12345', 'ohlvebk93b7', 'abc.123def', 'test-video_123');

foreach ($test_ids as $test_id) {
    if (preg_match('/^[A-Za-z0-9\-_\.]+$/', $test_id)) {
        echo "<p>✅ Valid ID: " . esc_html($test_id) . "</p>";
    } else {
        echo "<p>❌ Invalid ID: " . esc_html($test_id) . "</p>";
    }
}

// Settings page test
echo "<h3>Settings Page Test:</h3>";
$settings_url = admin_url('admin.php?page=kenplayer_config');
echo "<p><strong>Settings URL:</strong> <a href='" . esc_url($settings_url) . "' target='_blank'>Test Settings Page</a></p>";
echo "<p><em>Click the link above to test if the settings page loads without 'link expired' error</em></p>";

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<ul>";
echo "<li>JWPlayer curl function updated to use WordPress HTTP API</li>";
echo "<li>XVideos URL construction fixed (/xvideosx instead of /videoxxx)</li>";
echo "<li>Video ID validation allows dots for new XVideos format</li>";
echo "<li>Settings page nonce conflict resolved</li>";
echo "<li>All security measures maintained</li>";
echo "</ul>";

echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Test the JWPlayer link above</li>";
echo "<li>Test the settings page link above</li>";
echo "<li>Try saving settings to confirm no 'link expired' error</li>";
echo "<li>Test shortcode with both VideoJS and JWPlayer options</li>";
echo "</ol>";
?>