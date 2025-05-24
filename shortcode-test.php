<?php
/**
 * KenPlayer Shortcode Test File
 * 
 * This file can be used to test the shortcode functionality
 * Place this file in your WordPress root directory and access it via browser
 * 
 * @package KenPlayer Transformer
 * @version 3.0.0
 */

// Load WordPress if not already loaded
if (!defined('ABSPATH')) {
    require_once('wp-load.php');
}

// Start output
?>
<!DOCTYPE html>
<html>
<head>
    <title>KenPlayer Shortcode Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { border: 1px solid #ddd; padding: 15px; margin: 15px 0; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        code { background: #f5f5f5; padding: 2px 5px; }
    </style>
</head>
<body>
    <h1>KenPlayer Shortcode Test</h1>
    
    <div class="test-section">
        <h2>Plugin Status</h2>
        <p><strong>Shortcode registered:</strong> 
            <span class="<?php echo shortcode_exists('kenplayer') ? 'success' : 'error'; ?>">
                <?php echo shortcode_exists('kenplayer') ? 'YES ✓' : 'NO ✗'; ?>
            </span>
        </p>
        <p><strong>Function exists:</strong> 
            <span class="<?php echo function_exists('shortcode_videos_transformer') ? 'success' : 'error'; ?>">
                <?php echo function_exists('shortcode_videos_transformer') ? 'YES ✓' : 'NO ✗'; ?>
            </span>
        </p>
        <p><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></p>
        <p><strong>Plugin Activation:</strong> <?php echo get_option('kenplayer_activation', 'Not set'); ?></p>
    </div>

    <div class="test-section">
        <h2>Shortcode Usage Instructions</h2>
        <p>To use the KenPlayer shortcode in your posts or pages, use this format:</p>
        <code>[kenplayer url="VIDEO_URL"]</code>
        
        <h3>Supported Video Sites:</h3>
        <ul>
            <li>XVideos (e.g., https://www.xvideos.com/video12345/title or https://www.xvideos.com/video.abc123/title)</li>
            <li>Pornhub (e.g., https://www.pornhub.com/view_video.php?viewkey=abc123)</li>
            <li>RedTube (e.g., https://www.redtube.com/12345)</li>
            <li>YouPorn (e.g., https://www.youporn.com/watch/12345/title)</li>
            <li>XHamster (e.g., https://www.xhamster.com/videos/title-12345)</li>
            <li>Direct MP4/FLV/WebM files (e.g., https://example.com/video.mp4)</li>
            <li>Google Drive videos</li>
            <li>YouTube videos</li>
        </ul>
        
        <h3>Optional Parameters:</h3>
        <ul>
            <li><code>width</code> - Player width in pixels (default: 735)</li>
            <li><code>height</code> - Player height in pixels (default: 400)</li>
        </ul>
        
        <h3>Example Usage:</h3>
        <code>[kenplayer url="https://www.xvideos.com/video12345/sample-video" width="800" height="450"]</code>
    </div>

    <div class="test-section">
        <h2>Test 1: Empty Shortcode (Should Show Usage Instructions)</h2>
        <p><strong>Shortcode:</strong> <code>[kenplayer]</code></p>
        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
            <?php echo do_shortcode('[kenplayer]'); ?>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 2: Sample XVideos URL (Numeric ID)</h2>
        <p><strong>Shortcode:</strong> <code>[kenplayer url="https://www.xvideos.com/video12345/sample-video"]</code></p>
        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
            <?php echo do_shortcode('[kenplayer url="https://www.xvideos.com/video12345/sample-video"]'); ?>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 2b: XVideos URL (Alphanumeric ID)</h2>
        <p><strong>Shortcode:</strong> <code>[kenplayer url="https://www.xvideos.com/video.ohlvebk93b7/sample-title"]</code></p>
        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
            <?php echo do_shortcode('[kenplayer url="https://www.xvideos.com/video.ohlvebk93b7/sample-title"]'); ?>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 3: Sample Pornhub URL</h2>
        <p><strong>Shortcode:</strong> <code>[kenplayer url="https://www.pornhub.com/view_video.php?viewkey=sample123"]</code></p>
        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
            <?php echo do_shortcode('[kenplayer url="https://www.pornhub.com/view_video.php?viewkey=sample123"]'); ?>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 4: Sample MP4 URL</h2>
        <p><strong>Shortcode:</strong> <code>[kenplayer url="https://example.com/sample-video.mp4"]</code></p>
        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
            <?php echo do_shortcode('[kenplayer url="https://example.com/sample-video.mp4"]'); ?>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 5: Unsupported URL (Should Show Error)</h2>
        <p><strong>Shortcode:</strong> <code>[kenplayer url="https://unsupported-site.com/video"]</code></p>
        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
            <?php echo do_shortcode('[kenplayer url="https://unsupported-site.com/video"]'); ?>
        </div>
    </div>

    <div class="test-section">
        <h2>Test 6: Custom Dimensions</h2>
        <p><strong>Shortcode:</strong> <code>[kenplayer url="https://www.xvideos.com/video12345/sample" width="600" height="350"]</code></p>
        <div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
            <?php echo do_shortcode('[kenplayer url="https://www.xvideos.com/video12345/sample" width="600" height="350"]'); ?>
        </div>
    </div>

    <div class="test-section">
        <h2>Troubleshooting</h2>
        
        <?php if (!shortcode_exists('kenplayer')): ?>
            <div style="background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px 0;">
                <strong>❌ Shortcode Not Registered</strong><br>
                The [kenplayer] shortcode is not registered. This could be because:
                <ul>
                    <li>The plugin is not activated</li>
                    <li>There's a PHP error preventing the plugin from loading</li>
                    <li>The plugin files are missing or corrupted</li>
                </ul>
                <strong>Solutions:</strong>
                <ul>
                    <li>Check if the plugin is activated in WordPress admin</li>
                    <li>Check for PHP errors in your error logs</li>
                    <li>Re-upload the plugin files</li>
                </ul>
            </div>
        <?php else: ?>
            <div style="background: #e8f5e8; border: 1px solid #4caf50; padding: 10px; margin: 10px 0;">
                <strong>✅ Shortcode Registered Successfully</strong><br>
                The [kenplayer] shortcode is properly registered and ready to use.
            </div>
        <?php endif; ?>

        <h3>Common Issues:</h3>
        <ul>
            <li><strong>Shortcode shows as text:</strong> Make sure you're using the correct syntax and the plugin is activated</li>
            <li><strong>Video doesn't load:</strong> Check if the video URL is from a supported site</li>
            <li><strong>Player shows error:</strong> The video might be private, deleted, or the URL format is incorrect</li>
            <li><strong>No output at all:</strong> Check for PHP errors or plugin conflicts</li>
        </ul>

        <h3>Debug Information:</h3>
        <ul>
            <li><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></li>
            <li><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></li>
            <li><strong>Plugin Directory:</strong> <?php echo plugin_dir_url(__FILE__); ?></li>
            <li><strong>Active Theme:</strong> <?php echo wp_get_theme()->get('Name'); ?></li>
        </ul>
    </div>

    <div class="test-section">
        <h2>How to Use in Posts/Pages</h2>
        <ol>
            <li>Go to your WordPress admin dashboard</li>
            <li>Create a new post or edit an existing one</li>
            <li>In the content editor, add the shortcode: <code>[kenplayer url="YOUR_VIDEO_URL"]</code></li>
            <li>Replace YOUR_VIDEO_URL with the actual video URL from a supported site</li>
            <li>Save/publish the post</li>
            <li>View the post on the frontend to see the embedded video player</li>
        </ol>
    </div>

</body>
</html>