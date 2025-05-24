# KenPlayer Transformer - Player Color Customization Guide

## 🎨 How to Change Player Colors

The KenPlayer Transformer plugin supports color customization for both VideoJS and JWPlayer. Here's how to customize the colors for each player type.

## 📋 Table of Contents
1. [VideoJS Color Customization](#videojs-color-customization)
2. [JWPlayer Color Customization](#jwplayer-color-customization)
3. [Adding Color Settings to Admin Panel](#adding-color-settings-to-admin-panel)
4. [Custom CSS Examples](#custom-css-examples)
5. [Available JWPlayer Skins](#available-jwplayer-skins)

---

## 🎬 VideoJS Color Customization

### Method 1: Edit VideoJS CSS File
Edit `/video-js/video-js.css` to change default colors:

```css
/* Main player text color */
.vjs-default-skin {
  color: #cccccc; /* Change to your preferred color */
}

/* Control bar background */
.vjs-default-skin .vjs-control-bar {
  background-color: rgba(7,20,30,0.7); /* Dark blue background */
}

/* Progress bar colors */
.vjs-default-skin .vjs-play-progress {
  background-color: #ff6b6b; /* Red progress bar */
}

.vjs-default-skin .vjs-load-progress {
  background-color: rgba(255,255,255,0.4); /* Light buffer bar */
}

/* Volume and seek bar background */
.vjs-default-skin .vjs-slider {
  background-color: rgba(51, 51, 51, 0.9); /* Dark background */
}

/* Button hover effects */
.vjs-default-skin .vjs-control:hover {
  color: #ff6b6b; /* Red hover color */
}
```

### Method 2: Create Custom CSS File
Create a new file `/video-js/custom-colors.css`:

```css
/* Custom VideoJS Theme - Dark Red */
.vjs-default-skin {
  color: #ffffff;
}

.vjs-default-skin .vjs-control-bar {
  background: linear-gradient(to bottom, rgba(20,20,20,0.8), rgba(10,10,10,0.9));
}

.vjs-default-skin .vjs-play-progress {
  background-color: #e74c3c; /* Red progress */
}

.vjs-default-skin .vjs-volume-level {
  background-color: #e74c3c; /* Red volume */
}

.vjs-default-skin .vjs-big-play-button {
  background-color: rgba(231,76,60,0.8);
  border-color: #e74c3c;
}

.vjs-default-skin .vjs-big-play-button:hover {
  background-color: rgba(231,76,60,1);
}
```

---

## 🎯 JWPlayer Color Customization

### Available Skins
The plugin includes several pre-built JWPlayer skins in `/jwplayer/skins/`:

1. **beelden.css** - Clean modern look
2. **bekle.css** - Minimalist design
3. **five.css** - Colorful theme
4. **glow.css** - Dark theme with glow effects
5. **roundster.css** - Rounded elements
6. **seven.css** - Red accent theme
7. **six.css** - Blue theme
8. **stormtrooper.css** - Black and white
9. **vapor.css** - Purple/pink theme

### Method 1: Use Existing Skins
To use a different skin, modify the JWPlayer setup in `/jwplayer/player.php`:

```javascript
var jw = jwplayer("jwplayer").setup({
    // ... other settings ...
    skin: {
        name: "seven" // Use the seven.css skin (red theme)
    }
});
```

### Method 2: Create Custom JWPlayer Skin
Create a new file `/jwplayer/skins/custom.css`:

```css
/* Custom JWPlayer Skin - Blue Theme */
.jw-skin-custom .jw-background-color {
    background: #2c3e50; /* Dark blue background */
}

.jw-skin-custom .jw-controlbar {
    background: rgba(52, 73, 94, 0.8); /* Blue control bar */
}

.jw-skin-custom .jw-text {
    color: #ecf0f1; /* Light text */
}

.jw-skin-custom .jw-button-color {
    color: #3498db; /* Blue buttons */
}

.jw-skin-custom .jw-button-color:hover {
    color: #5dade2; /* Light blue on hover */
}

.jw-skin-custom .jw-progress {
    background: #3498db; /* Blue progress bar */
}

.jw-skin-custom .jw-rail {
    background-color: #34495e; /* Dark rail */
}

.jw-skin-custom .jw-buffer {
    background-color: #7f8c8d; /* Gray buffer */
}
```

Then use it in the player:

```javascript
var jw = jwplayer("jwplayer").setup({
    // ... other settings ...
    skin: {
        name: "custom"
    }
});
```

---

## ⚙️ Adding Color Settings to Admin Panel

To add color customization to the WordPress admin panel, add this to `transform.php`:

### 1. Add Settings Fields

Add to the settings form in `kenplayer_config()` function:

```php
<tr>
    <th scope="row">Player Theme Color</th>
    <td>
        <select name="kenplayer_theme_color">
            <option value="default" <?php selected(get_option('kenplayer_theme_color'), 'default'); ?>>Default</option>
            <option value="red" <?php selected(get_option('kenplayer_theme_color'), 'red'); ?>>Red</option>
            <option value="blue" <?php selected(get_option('kenplayer_theme_color'), 'blue'); ?>>Blue</option>
            <option value="green" <?php selected(get_option('kenplayer_theme_color'), 'green'); ?>>Green</option>
            <option value="purple" <?php selected(get_option('kenplayer_theme_color'), 'purple'); ?>>Purple</option>
            <option value="orange" <?php selected(get_option('kenplayer_theme_color'), 'orange'); ?>>Orange</option>
        </select>
        <p class="description">Choose a color theme for the video player</p>
    </td>
</tr>

<tr>
    <th scope="row">JWPlayer Skin</th>
    <td>
        <select name="kenplayer_jwplayer_skin">
            <option value="default" <?php selected(get_option('kenplayer_jwplayer_skin'), 'default'); ?>>Default</option>
            <option value="glow" <?php selected(get_option('kenplayer_jwplayer_skin'), 'glow'); ?>>Glow (Dark)</option>
            <option value="seven" <?php selected(get_option('kenplayer_jwplayer_skin'), 'seven'); ?>>Seven (Red)</option>
            <option value="six" <?php selected(get_option('kenplayer_jwplayer_skin'), 'six'); ?>>Six (Blue)</option>
            <option value="vapor" <?php selected(get_option('kenplayer_jwplayer_skin'), 'vapor'); ?>>Vapor (Purple)</option>
            <option value="stormtrooper" <?php selected(get_option('kenplayer_jwplayer_skin'), 'stormtrooper'); ?>>Stormtrooper (B&W)</option>
        </select>
        <p class="description">Choose a skin for JWPlayer (only applies when JWPlayer is selected)</p>
    </td>
</tr>
```

### 2. Update Settings Validation

Add to `kenplayer_validate_settings()` function:

```php
if (isset($input['kenplayer_theme_color'])) {
    $validated['kenplayer_theme_color'] = sanitize_text_field($input['kenplayer_theme_color']);
}

if (isset($input['kenplayer_jwplayer_skin'])) {
    $validated['kenplayer_jwplayer_skin'] = sanitize_text_field($input['kenplayer_jwplayer_skin']);
}
```

### 3. Apply Colors in Player Files

Update the player files to use the selected colors:

**For VideoJS** (in `/player/player.php`):

```php
<?php
$theme_color = get_option('kenplayer_theme_color', 'default');
$color_schemes = array(
    'red' => array('primary' => '#e74c3c', 'secondary' => '#c0392b'),
    'blue' => array('primary' => '#3498db', 'secondary' => '#2980b9'),
    'green' => array('primary' => '#2ecc71', 'secondary' => '#27ae60'),
    'purple' => array('primary' => '#9b59b6', 'secondary' => '#8e44ad'),
    'orange' => array('primary' => '#f39c12', 'secondary' => '#e67e22'),
    'default' => array('primary' => '#ffffff', 'secondary' => '#cccccc')
);
$colors = $color_schemes[$theme_color];
?>

<style>
.vjs-default-skin .vjs-play-progress {
    background-color: <?php echo $colors['primary']; ?> !important;
}
.vjs-default-skin .vjs-volume-level {
    background-color: <?php echo $colors['primary']; ?> !important;
}
.vjs-default-skin .vjs-big-play-button {
    background-color: <?php echo $colors['primary']; ?> !important;
    border-color: <?php echo $colors['secondary']; ?> !important;
}
</style>
```

**For JWPlayer** (in `/jwplayer/player.php`):

```php
<?php
$jwplayer_skin = get_option('kenplayer_jwplayer_skin', 'default');
if ($jwplayer_skin !== 'default') {
    echo '<link rel="stylesheet" href="skins/' . esc_attr($jwplayer_skin) . '.css">';
}
?>

<script type="text/javascript">
var jw = jwplayer("jwplayer").setup({
    // ... other settings ...
    <?php if ($jwplayer_skin !== 'default'): ?>
    skin: {
        name: "<?php echo esc_js($jwplayer_skin); ?>"
    },
    <?php endif; ?>
    // ... rest of settings ...
});
</script>
```

---

## 🎨 Custom CSS Examples

### Dark Theme with Red Accents
```css
/* VideoJS Dark Red Theme */
.vjs-default-skin {
    color: #ffffff;
}
.vjs-default-skin .vjs-control-bar {
    background: rgba(20, 20, 20, 0.9);
}
.vjs-default-skin .vjs-play-progress {
    background: #ff4757;
}
.vjs-default-skin .vjs-big-play-button {
    background: rgba(255, 71, 87, 0.8);
}
```

### Light Theme with Blue Accents
```css
/* VideoJS Light Blue Theme */
.vjs-default-skin {
    color: #2c3e50;
}
.vjs-default-skin .vjs-control-bar {
    background: rgba(255, 255, 255, 0.9);
}
.vjs-default-skin .vjs-play-progress {
    background: #3498db;
}
.vjs-default-skin .vjs-big-play-button {
    background: rgba(52, 152, 219, 0.8);
}
```

### Gradient Theme
```css
/* VideoJS Gradient Theme */
.vjs-default-skin .vjs-control-bar {
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
}
.vjs-default-skin .vjs-play-progress {
    background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
}
```

---

## 🔧 Quick Implementation

### Option 1: Simple Color Change (Fastest)
1. Edit `/video-js/video-js.css`
2. Find `.vjs-default-skin .vjs-play-progress`
3. Change `background-color` to your preferred color

### Option 2: Use JWPlayer Skin (Easy)
1. Go to WordPress Admin → Settings → KenPlayer Config
2. Change "Custom player" to "JWPlayer"
3. Edit `/jwplayer/player.php`
4. Add skin parameter to jwplayer setup:
```javascript
skin: { name: "seven" } // for red theme
```

### Option 3: Full Customization (Advanced)
1. Create custom CSS file
2. Add color settings to admin panel
3. Modify player files to use dynamic colors

---

## 📝 Color Codes Reference

### Popular Color Schemes
- **Red**: `#e74c3c`, `#c0392b`
- **Blue**: `#3498db`, `#2980b9`
- **Green**: `#2ecc71`, `#27ae60`
- **Purple**: `#9b59b6`, `#8e44ad`
- **Orange**: `#f39c12`, `#e67e22`
- **Pink**: `#e91e63`, `#ad1457`
- **Teal**: `#1abc9c`, `#16a085`

### Adult/Porn Site Color Schemes
- **PornHub**: `#ff9000`, `#000000`
- **XVideos**: `#c41230`, `#8b0000`
- **RedTube**: `#ff0000`, `#cc0000`
- **YouPorn**: `#ff6600`, `#e55a00`

---

## 🚀 Testing Your Changes

1. **Clear Cache**: Clear any caching plugins
2. **Test Both Players**: Switch between VideoJS and JWPlayer
3. **Check Mobile**: Test on mobile devices
4. **Browser Testing**: Test in different browsers

---

## 💡 Tips

1. **Backup First**: Always backup files before editing
2. **Use CSS Variables**: For easier maintenance
3. **Test Contrast**: Ensure text is readable
4. **Mobile Friendly**: Test on small screens
5. **Performance**: Minimize CSS file sizes

---

## 🔗 Resources

- [VideoJS Theming Guide](https://docs.videojs.com/tutorial-skins.html)
- [JWPlayer Skin Reference](https://developer.jwplayer.com/jwplayer/docs/jw8-css-skin-reference)
- [CSS Color Picker](https://htmlcolorcodes.com/)
- [Gradient Generator](https://cssgradient.io/)

---

This guide provides multiple methods to customize player colors, from simple edits to advanced admin panel integration. Choose the method that best fits your technical skill level and requirements.