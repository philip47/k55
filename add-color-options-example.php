<?php
/**
 * Example: How to Add Color Options to KenPlayer Admin Panel
 * 
 * This file shows how to modify transform.php to add color customization options
 * Copy the relevant parts to your transform.php file
 */

// 1. ADD TO SETTINGS REGISTRATION (around line 50 in transform.php)
function kenplayer_register_settings() {
    // Existing settings...
    register_setting('kenplayer_config', 'kenplayer_activation');
    register_setting('kenplayer_config', 'kenplayer_player');
    // ... other existing settings ...
    
    // ADD THESE NEW COLOR SETTINGS:
    register_setting('kenplayer_config', 'kenplayer_primary_color', 'kenplayer_validate_color');
    register_setting('kenplayer_config', 'kenplayer_progress_color', 'kenplayer_validate_color');
    register_setting('kenplayer_config', 'kenplayer_button_color', 'kenplayer_validate_color');
}

// 2. ADD COLOR VALIDATION FUNCTION (add this new function)
function kenplayer_validate_color($color) {
    // Validate hex color format
    if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
        return $color;
    }
    return '#663399'; // Default purple color
}

// 3. ADD TO ADMIN FORM (in kenplayer_config function, around line 350)
// Add this section to the settings table:
?>

<!-- ADD THIS TO THE SETTINGS TABLE IN transform.php -->
<tr>
    <th scope="row">Player Colors</th>
    <td>
        <fieldset>
            <legend class="screen-reader-text"><span>Player Colors</span></legend>
            
            <label for="kenplayer_primary_color">
                <strong>Primary Color (Control Bar):</strong><br>
                <input type="color" 
                       id="kenplayer_primary_color" 
                       name="kenplayer_primary_color" 
                       value="<?php echo esc_attr(get_option('kenplayer_primary_color', '#663399')); ?>" />
            </label>
            <br><br>
            
            <label for="kenplayer_progress_color">
                <strong>Progress Bar Color:</strong><br>
                <input type="color" 
                       id="kenplayer_progress_color" 
                       name="kenplayer_progress_color" 
                       value="<?php echo esc_attr(get_option('kenplayer_progress_color', '#ff69b4')); ?>" />
            </label>
            <br><br>
            
            <label for="kenplayer_button_color">
                <strong>Button Color:</strong><br>
                <input type="color" 
                       id="kenplayer_button_color" 
                       name="kenplayer_button_color" 
                       value="<?php echo esc_attr(get_option('kenplayer_button_color', '#ffffff')); ?>" />
            </label>
            
            <p class="description">
                Choose custom colors for your video player. Changes will apply to both VideoJS and JWPlayer.
            </p>
        </fieldset>
    </td>
</tr>

<?php
// 4. ADD TO PLAYER FILES - VideoJS (player/player.php)
// Add this CSS section after line 288 (after the theme link):
?>

<!-- ADD THIS CSS TO player/player.php AFTER THE THEME LINK -->
<style>
/* Custom KenPlayer Colors */
<?php 
$primary_color = get_option('kenplayer_primary_color', '#663399');
$progress_color = get_option('kenplayer_progress_color', '#ff69b4');
$button_color = get_option('kenplayer_button_color', '#ffffff');
?>

.video-js .vjs-control-bar {
    background-color: <?php echo esc_attr($primary_color); ?> !important;
}

.video-js .vjs-progress-control .vjs-play-progress {
    background-color: <?php echo esc_attr($progress_color); ?> !important;
}

.video-js .vjs-button > .vjs-icon-placeholder:before {
    color: <?php echo esc_attr($button_color); ?> !important;
}

.video-js .vjs-big-play-button {
    background-color: <?php echo esc_attr($primary_color); ?> !important;
    border-color: <?php echo esc_attr($primary_color); ?> !important;
}

.video-js .vjs-big-play-button .vjs-icon-placeholder:before {
    color: <?php echo esc_attr($button_color); ?> !important;
}

.video-js .vjs-volume-control .vjs-volume-bar .vjs-volume-level {
    background-color: <?php echo esc_attr($progress_color); ?> !important;
}
</style>

<?php
// 5. ADD TO JWPLAYER FILE (jwplayer/player.php)
// Add this CSS section before the JWPlayer setup (around line 450):
?>

<!-- ADD THIS CSS TO jwplayer/player.php BEFORE JWPLAYER SETUP -->
<style>
/* Custom KenPlayer Colors for JWPlayer */
<?php 
$primary_color = get_option('kenplayer_primary_color', '#663399');
$progress_color = get_option('kenplayer_progress_color', '#ff69b4');
$button_color = get_option('kenplayer_button_color', '#ffffff');
?>

.jw-skin-kenplayer .jw-controlbar {
    background: <?php echo esc_attr($primary_color); ?> !important;
}

.jw-skin-kenplayer .jw-progress {
    background: <?php echo esc_attr($progress_color); ?> !important;
}

.jw-skin-kenplayer .jw-button-color {
    color: <?php echo esc_attr($button_color); ?> !important;
}

.jw-skin-kenplayer .jw-display-icon-container {
    background: <?php echo esc_attr($primary_color); ?> !important;
}

.jw-skin-kenplayer .jw-icon-display {
    color: <?php echo esc_attr($button_color); ?> !important;
}

.jw-skin-kenplayer .jw-rail {
    background: rgba(255,255,255,0.3) !important;
}
</style>

<?php
// 6. MODIFY JWPLAYER SETUP (in jwplayer/player.php around line 459)
// Add the skin configuration:
?>

<script>
var jw = jwplayer("jwplayer").setup({
    // ... existing configuration ...
    
    // ADD THIS SKIN CONFIGURATION:
    skin: {
        name: "kenplayer"
    },
    
    // ... rest of existing configuration ...
});
</script>

<?php
/**
 * INSTALLATION INSTRUCTIONS:
 * 
 * 1. Backup your files first:
 *    - cp transform.php transform.php.backup
 *    - cp player/player.php player/player.php.backup  
 *    - cp jwplayer/player.php jwplayer/player.php.backup
 * 
 * 2. Add the settings registration code to transform.php
 * 
 * 3. Add the color validation function to transform.php
 * 
 * 4. Add the color picker form fields to the admin settings table
 * 
 * 5. Add the custom CSS to both player files
 * 
 * 6. Add the skin configuration to JWPlayer setup
 * 
 * 7. Test the changes:
 *    - Go to WordPress Admin → Settings → KenPlayer Config
 *    - You should see the new color options
 *    - Change colors and save
 *    - Test both VideoJS and JWPlayer
 * 
 * QUICK TEST:
 * - Use the debug-video.php file to test color changes
 * - Try different colors and see immediate results
 * 
 * TROUBLESHOOTING:
 * - Clear browser cache after changes
 * - Check browser console for CSS errors
 * - Ensure color values are valid hex codes
 * - Test on different devices and browsers
 */
?>