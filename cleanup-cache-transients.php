<?php
/**
 * Cache Transients Cleanup Script
 * 
 * This script removes all cache-related transients from the WordPress database
 * to free up space. Run this script once to clean up existing transients.
 * 
 * Usage: 
 * 1. Upload this file to your WordPress root directory
 * 2. Run it via browser: yoursite.com/cleanup-cache-transients.php
 * 3. Delete this file after running
 * 
 * OR add this code to your theme's functions.php and call kenplayer_cleanup_all_cache()
 */

// Prevent direct access if not in WordPress context
if (!defined('ABSPATH')) {
    // Load WordPress if running standalone
    require_once(dirname(__FILE__) . '/wp-config.php');
}

/**
 * Clean up all KenPlayer cache transients
 */
function kenplayer_cleanup_all_cache() {
    global $wpdb;
    
    echo "<h2>KenPlayer Cache Cleanup</h2>\n";
    
    // Get count before cleanup
    $before_count = $wpdb->get_var("
        SELECT COUNT(*) FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient_kenplayer_%' 
        OR option_name LIKE '_transient_timeout_kenplayer_%'
        OR option_name LIKE '_transient_video_%'
        OR option_name LIKE '_transient_timeout_video_%'
    ");
    
    echo "<p>Found {$before_count} cache transients to remove.</p>\n";
    
    // Remove all KenPlayer related transients
    $queries = array(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_kenplayer_%'",
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_kenplayer_%'",
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_video_%'",
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_video_%'"
    );
    
    $total_removed = 0;
    foreach ($queries as $query) {
        $removed = $wpdb->query($query);
        $total_removed += $removed;
        echo "<p>Executed: " . esc_html($query) . " - Removed: {$removed} entries</p>\n";
    }
    
    // Get count after cleanup
    $after_count = $wpdb->get_var("
        SELECT COUNT(*) FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient_kenplayer_%' 
        OR option_name LIKE '_transient_timeout_kenplayer_%'
        OR option_name LIKE '_transient_video_%'
        OR option_name LIKE '_transient_timeout_video_%'
    ");
    
    echo "<h3>Cleanup Complete!</h3>\n";
    echo "<p><strong>Total transients removed: {$total_removed}</strong></p>\n";
    echo "<p>Remaining cache transients: {$after_count}</p>\n";
    
    // Optimize the options table
    $wpdb->query("OPTIMIZE TABLE {$wpdb->options}");
    echo "<p>Options table optimized.</p>\n";
    
    return $total_removed;
}

// If running standalone (not in WordPress admin)
if (!is_admin() && !defined('DOING_AJAX')) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>KenPlayer Cache Cleanup</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .success { color: green; }
            .warning { color: orange; }
            .error { color: red; }
        </style>
    </head>
    <body>
        <?php
        if (isset($_GET['run']) && $_GET['run'] === 'cleanup') {
            kenplayer_cleanup_all_cache();
            echo '<p class="success">Cache cleanup completed! You can now delete this file.</p>';
        } else {
            ?>
            <h1>KenPlayer Cache Cleanup</h1>
            <p class="warning"><strong>Warning:</strong> This will remove all KenPlayer cache transients from your database.</p>
            <p>This action cannot be undone, but it's safe to run as it only removes cache data.</p>
            <p><a href="?run=cleanup" onclick="return confirm('Are you sure you want to clean up all cache transients?')">
                <strong>Click here to run cleanup</strong>
            </a></p>
            <p><em>After running the cleanup, please delete this file from your server for security.</em></p>
            <?php
        }
        ?>
    </body>
    </html>
    <?php
}

/**
 * Add admin menu item for cache cleanup (optional)
 * Uncomment this section if you want to add it to WordPress admin
 */
/*
add_action('admin_menu', 'kenplayer_add_cleanup_menu');

function kenplayer_add_cleanup_menu() {
    add_management_page(
        'KenPlayer Cache Cleanup',
        'Cache Cleanup',
        'manage_options',
        'kenplayer-cache-cleanup',
        'kenplayer_cleanup_admin_page'
    );
}

function kenplayer_cleanup_admin_page() {
    if (isset($_POST['cleanup_cache']) && wp_verify_nonce($_POST['_wpnonce'], 'kenplayer_cleanup')) {
        $removed = kenplayer_cleanup_all_cache();
        echo '<div class="notice notice-success"><p>Cache cleanup completed! Removed ' . $removed . ' transients.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>KenPlayer Cache Cleanup</h1>
        <form method="post">
            <?php wp_nonce_field('kenplayer_cleanup'); ?>
            <p>This will remove all KenPlayer cache transients from the database to free up space.</p>
            <p class="submit">
                <input type="submit" name="cleanup_cache" class="button-primary" 
                       value="Clean Up Cache Transients" 
                       onclick="return confirm('Are you sure you want to clean up all cache transients?')" />
            </p>
        </form>
    </div>
    <?php
}
*/
?>