# KenPlayer Transformer - Player Color Customization Guide

## 🎨 How to Change Player Colors

There are several ways to customize the colors of both VideoJS and JWPlayer in the KenPlayer Transformer plugin.

## 📹 VideoJS Player Color Customization

### Method 1: Using VideoJS Themes (Easiest)

The VideoJS player currently uses the "Fantasy" theme. You can change this by editing `/player/player.php`:

**Current theme (line 288):**
```html
<link href="https://unpkg.com/@videojs/themes@1/dist/fantasy/index.css" rel="stylesheet">
```

**Available VideoJS themes:**
```html
<!-- City Theme (Blue/Gray) -->
<link href="https://unpkg.com/@videojs/themes@1/dist/city/index.css" rel="stylesheet">

<!-- Forest Theme (Green) -->
<link href="https://unpkg.com/@videojs/themes@1/dist/forest/index.css" rel="stylesheet">

<!-- Sea Theme (Blue) -->
<link href="https://unpkg.com/@videojs/themes@1/dist/sea/index.css" rel="stylesheet">

<!-- Fantasy Theme (Purple/Pink) - Current -->
<link href="https://unpkg.com/@videojs/themes@1/dist/fantasy/index.css" rel="stylesheet">
```

### Method 2: Custom CSS Override

Add custom CSS after the theme link in `/player/player.php`:

```html
<style>
/* Custom VideoJS Colors */
.video-js .vjs-control-bar {
    background-color: #your-color !important; /* Control bar background */
}

.video-js .vjs-progress-control .vjs-progress-holder {
    background-color: #your-color !important; /* Progress bar background */
}

.video-js .vjs-progress-control .vjs-play-progress {
    background-color: #your-color !important; /* Progress bar fill */
}

.video-js .vjs-volume-control .vjs-volume-bar {
    background-color: #your-color !important; /* Volume bar */
}

.video-js .vjs-button > .vjs-icon-placeholder:before {
    color: #your-color !important; /* Button icons */
}

.video-js .vjs-big-play-button {
    background-color: #your-color !important; /* Big play button */
    border-color: #your-color !important;
}
</style>
```

### Method 3: Create Custom VideoJS Skin

1. Create a new CSS file in `/video-js/` folder (e.g., `custom-skin.css`)
2. Replace the theme link with your custom CSS file
3. Use VideoJS skin generator: http://designer.videojs.com

## 🎬 JWPlayer Color Customization

### Method 1: Using Built-in Skins (Easiest)

The plugin includes several JWPlayer skins in `/jwplayer/skins/`:

- `glow.css` - Dark theme with white controls
- `beelden.css` - Custom theme
- `bekle.css` - Custom theme  
- `five.css` - Custom theme
- `roundster.css` - Rounded theme
- `seven.css` - Custom theme
- `six.css` - Custom theme
- `stormtrooper.css` - Dark theme
- `vapor.css` - Light theme

**To apply a skin, edit `/jwplayer/player.php` and add before the JWPlayer setup:**

```html
<link rel="stylesheet" href="skins/glow.css" type="text/css" />
```

**Then add the skin class to the JWPlayer setup:**

```javascript
var jw = jwplayer("jwplayer").setup({
    // ... existing config ...
    skin: {
        name: "glow"  // or any other skin name
    }
});
```

### Method 2: Custom JWPlayer Colors

Add custom CSS in `/jwplayer/player.php`:

```html
<style>
/* Custom JWPlayer Colors */
.jw-skin-custom .jw-controlbar {
    background: #your-color !important; /* Control bar */
}

.jw-skin-custom .jw-progress {
    background: #your-color !important; /* Progress bar */
}

.jw-skin-custom .jw-button-color {
    color: #your-color !important; /* Button colors */
}

.jw-skin-custom .jw-display-icon-container {
    background: #your-color !important; /* Play button background */
}

.jw-skin-custom .jw-rail {
    background: #your-color !important; /* Progress rail */
}
</style>
```

**Then apply the custom skin:**

```javascript
var jw = jwplayer("jwplayer").setup({
    // ... existing config ...
    skin: {
        name: "custom"
    }
});
```

## 🛠️ Implementation Steps

### For VideoJS Player:

1. **Edit file:** `/player/player.php`
2. **Find line 288:** `<link href="https://unpkg.com/@videojs/themes@1/dist/fantasy/index.css" rel="stylesheet">`
3. **Replace with desired theme or add custom CSS**

### For JWPlayer:

1. **Edit file:** `/jwplayer/player.php`
2. **Add skin CSS link** before the `<script>` section
3. **Add skin configuration** to the JWPlayer setup around line 459

## 🎨 Popular Color Schemes

### Dark Theme
```css
/* Control bar: #1a1a1a */
/* Progress: #ff6b6b */
/* Buttons: #ffffff */
/* Background: #000000 */
```

### Blue Theme  
```css
/* Control bar: #2c3e50 */
/* Progress: #3498db */
/* Buttons: #ecf0f1 */
/* Background: #34495e */
```

### Red Theme
```css
/* Control bar: #2c1810 */
/* Progress: #e74c3c */
/* Buttons: #ffffff */
/* Background: #8b0000 */
```

### Purple Theme (Current Fantasy)
```css
/* Control bar: #663399 */
/* Progress: #ff69b4 */
/* Buttons: #ffffff */
/* Background: #4b0082 */
```

## 🔧 Adding Color Options to Admin Panel

To add color customization to the WordPress admin panel, you can modify `/transform.php`:

1. **Add new settings fields** in the `kenplayer_config()` function
2. **Add color picker inputs** for different player elements
3. **Use the saved colors** in the player files

### Example Admin Color Picker:

```php
// Add to settings table in transform.php
<tr>
    <th scope="row">Player Primary Color</th>
    <td>
        <input type="color" name="kenplayer_primary_color" value="<?php echo get_option('kenplayer_primary_color', '#663399'); ?>" />
        <p class="description">Choose the primary color for the video player</p>
    </td>
</tr>
```

### Use in Player Files:

```php
// In player.php files
$primary_color = get_option('kenplayer_primary_color', '#663399');
?>
<style>
.video-js .vjs-control-bar {
    background-color: <?php echo esc_attr($primary_color); ?> !important;
}
</style>
```

## 📱 Responsive Considerations

When customizing colors, ensure they work well on:
- Desktop browsers
- Mobile devices  
- Different screen sizes
- Light and dark environments

## 🧪 Testing Your Changes

1. **Clear browser cache** after making changes
2. **Test on multiple devices** and browsers
3. **Check contrast ratios** for accessibility
4. **Verify colors work** with different video content

## 🔄 Backup Before Changes

Always backup your files before making modifications:
```bash
cp player/player.php player/player.php.backup
cp jwplayer/player.php jwplayer/player.php.backup
```

## 📞 Need Help?

If you need assistance with color customization:
1. Check the browser console for CSS errors
2. Use browser developer tools to test colors
3. Refer to VideoJS and JWPlayer documentation
4. Test changes with the debug files provided

---

**Note:** After making color changes, test both VideoJS and JWPlayer to ensure consistency across both player types.