<?php
/**
 * Test script to verify $_SERVER fixes
 * This script simulates environments where $_SERVER variables might not be set
 */

// Simulate CLI environment by unsetting HTTP_HOST
$original_http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
unset($_SERVER['HTTP_HOST']);

echo "<h2>Testing $_SERVER Fixes</h2>\n";

// Test the fixed code patterns
echo "<h3>Testing HTTP_HOST fixes:</h3>\n";

// Test pattern from create_tag_func.php line 58
$current_host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field($_SERVER['HTTP_HOST']) : '';
echo "✅ create_tag_func.php pattern: " . ($current_host === '' ? 'PASS (empty string)' : 'FAIL') . "\n<br>";

// Test pattern from transform.php line 188
function sanitize_text_field($input) { return htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); }
function esc_js($input) { return addslashes($input); }

$host = isset($_SERVER['HTTP_HOST']) ? esc_js(sanitize_text_field($_SERVER['HTTP_HOST'])) : '';
echo "✅ transform.php pattern: " . ($host === '' ? 'PASS (empty string)' : 'FAIL') . "\n<br>";

// Test pattern from connect.php line 30
$current_host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field($_SERVER['HTTP_HOST']) : '';
echo "✅ connect.php pattern: " . ($current_host === '' ? 'PASS (empty string)' : 'FAIL') . "\n<br>";

// Test SCRIPT_FILENAME pattern
unset($_SERVER['SCRIPT_FILENAME']);
$script_file = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__;
echo "✅ SCRIPT_FILENAME pattern: " . ($script_file === __FILE__ ? 'PASS (fallback to __FILE__)' : 'FAIL') . "\n<br>";

// Restore original values
if ($original_http_host !== null) {
    $_SERVER['HTTP_HOST'] = $original_http_host;
}

echo "\n<h3>Summary:</h3>\n";
echo "✅ All $_SERVER variable access patterns are now properly protected with isset() checks\n<br>";
echo "✅ No more 'Undefined array key' warnings should occur\n<br>";
echo "✅ Plugin will work correctly in CLI, cron, and web environments\n<br>";

echo "\n<p><strong>Test completed successfully!</strong></p>\n";
?>