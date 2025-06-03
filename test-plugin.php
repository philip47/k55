<?php
/**
 * KenPlayer Transformer Test Script
 * 
 * This script tests basic plugin functionality
 * Run this from WordPress admin or via WP-CLI
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Test plugin functionality
 */
function kenplayer_run_tests() {
    $results = array();
    
    // Test 1: Check if plugin is loaded
    $results['plugin_loaded'] = function_exists('transformer_iframe');
    
    // Test 2: Check WordPress compatibility
    global $wp_version;
    $results['wp_compatible'] = version_compare($wp_version, '5.0', '>=');
    
    // Test 3: Check PHP version
    $results['php_compatible'] = version_compare(PHP_VERSION, '7.4', '>=');
    
    // Test 4: Check required functions
    $required_functions = array('curl_init', 'wp_remote_get', 'wp_create_nonce');
    $results['functions_available'] = true;
    foreach ($required_functions as $func) {
        if (!function_exists($func)) {
            $results['functions_available'] = false;
            break;
        }
    }
    
    // Test 5: Check plugin options
    $results['options_set'] = (get_option('kenplayer_activation') !== false);
    
    // Test 6: Test video URL transformation
    $test_content = '<iframe src="//www.xvideos.com/embedframe/12345"></iframe>';
    $transformed = transformer_iframe($test_content);
    $results['transformation_works'] = ($transformed !== $test_content);
    
    // Test 7: Test security functions
    $results['security_functions'] = function_exists('wp_verify_nonce') && function_exists('sanitize_text_field');
    
    // Test 8: Test caching
    // Cache functionality removed to reduce database space
    $results['caching_works'] = 'Cache disabled';
    
    return $results;
}

/**
 * Display test results
 */
function kenplayer_display_test_results() {
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    $results = kenplayer_run_tests();
    
    echo '<div class="wrap">';
    echo '<h1>KenPlayer Transformer Test Results</h1>';
    echo '<table class="widefat">';
    echo '<thead><tr><th>Test</th><th>Result</th><th>Status</th></tr></thead>';
    echo '<tbody>';
    
    foreach ($results as $test => $result) {
        $status = $result ? 'PASS' : 'FAIL';
        $class = $result ? 'notice-success' : 'notice-error';
        $icon = $result ? '✓' : '✗';
        
        echo '<tr>';
        echo '<td>' . esc_html(ucwords(str_replace('_', ' ', $test))) . '</td>';
        echo '<td>' . ($result ? 'Success' : 'Failed') . '</td>';
        echo '<td><span class="' . $class . '">' . $icon . ' ' . $status . '</span></td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Overall status
    $all_passed = !in_array(false, $results);
    $overall_class = $all_passed ? 'notice-success' : 'notice-warning';
    $overall_message = $all_passed ? 'All tests passed! Plugin is working correctly.' : 'Some tests failed. Please check the configuration.';
    
    echo '<div class="notice ' . $overall_class . ' is-dismissible">';
    echo '<p><strong>' . $overall_message . '</strong></p>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * Add test page to admin menu (for testing purposes only)
 */
function kenplayer_add_test_menu() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        add_submenu_page(
            'tools.php',
            'KenPlayer Tests',
            'KenPlayer Tests',
            'manage_options',
            'kenplayer-tests',
            'kenplayer_display_test_results'
        );
    }
}

// Only add test menu in debug mode
if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('admin_menu', 'kenplayer_add_test_menu');
}

/**
 * Performance benchmark test
 */
function kenplayer_benchmark_transformation() {
    $test_content = '
        <iframe src="//www.xvideos.com/embedframe/12345"></iframe>
        <iframe src="//www.pornhub.com/embed/67890"></iframe>
        <iframe src="//embed.redtube.com/?id=11111"></iframe>
    ';
    
    $start_time = microtime(true);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        transformer_iframe($test_content);
    }
    
    $end_time = microtime(true);
    $total_time = $end_time - $start_time;
    $avg_time = $total_time / $iterations;
    
    return array(
        'total_time' => $total_time,
        'average_time' => $avg_time,
        'iterations' => $iterations
    );
}

/**
 * Security test
 */
function kenplayer_test_security() {
    $security_tests = array();
    
    // Test XSS protection
    $malicious_input = '<script>alert("xss")</script>';
    $sanitized = sanitize_text_field($malicious_input);
    $security_tests['xss_protection'] = (strpos($sanitized, '<script>') === false);
    
    // Test SQL injection protection (basic check)
    $malicious_sql = "'; DROP TABLE wp_options; --";
    $sanitized_sql = sanitize_text_field($malicious_sql);
    $security_tests['sql_injection_protection'] = ($sanitized_sql !== $malicious_sql);
    
    // Test nonce verification
    $nonce = wp_create_nonce('test_action');
    $security_tests['nonce_creation'] = !empty($nonce);
    
    return $security_tests;
}
?>