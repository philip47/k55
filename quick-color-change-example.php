<?php
/**
 * Quick Color Change Example for KenPlayer Transformer
 * 
 * This file demonstrates how to quickly change player colors
 * Copy the code snippets to your player files to customize colors
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>KenPlayer Color Customization Examples</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .example { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .code { background: #f8f8f8; padding: 15px; border-radius: 4px; font-family: monospace; overflow-x: auto; }
        .color-preview { width: 30px; height: 30px; display: inline-block; margin-right: 10px; border-radius: 4px; vertical-align: middle; }
        h1 { color: #333; text-align: center; }
        h2 { color: #666; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .step { background: #e8f4fd; padding: 10px; margin: 10px 0; border-left: 4px solid #2196F3; }
    </style>
</head>
<body>

<h1>🎨 KenPlayer Color Customization Examples</h1>

<div class="example">
    <h2>🚀 Quick Start - Change VideoJS Colors</h2>
    <div class="step">
        <strong>Step 1:</strong> Edit <code>/player/player.php</code><br>
        <strong>Step 2:</strong> Add this CSS after line 288 (after the theme link)
    </div>
    
    <div class="code">
&lt;style&gt;
/* Red Theme */
.video-js .vjs-control-bar {
    background: rgba(231, 76, 60, 0.8) !important;
}
.video-js .vjs-play-progress {
    background: #e74c3c !important;
}
.video-js .vjs-big-play-button {
    background: rgba(231, 76, 60, 0.8) !important;
    border-color: #c0392b !important;
}
&lt;/style&gt;
    </div>
</div>

<div class="example">
    <h2>🎯 Quick Start - Change JWPlayer Colors</h2>
    <div class="step">
        <strong>Step 1:</strong> Edit <code>/jwplayer/player.php</code><br>
        <strong>Step 2:</strong> Add this before the jwplayer setup (around line 459)
    </div>
    
    <div class="code">
&lt;link rel="stylesheet" href="skins/seven.css" type="text/css" /&gt;

&lt;script type="text/javascript"&gt;
var jw = jwplayer("jwplayer").setup({
    // ... existing settings ...
    skin: {
        name: "seven"  // Red theme
    },
    // ... rest of settings ...
});
&lt;/script&gt;
    </div>
</div>

<div class="example">
    <h2>🎨 Popular Color Schemes</h2>
    
    <h3><span class="color-preview" style="background: #e74c3c;"></span>Red Theme (Adult/Porn Sites)</h3>
    <div class="code">
/* VideoJS Red Theme */
.video-js .vjs-control-bar { background: rgba(231, 76, 60, 0.8) !important; }
.video-js .vjs-play-progress { background: #e74c3c !important; }
.video-js .vjs-big-play-button { background: rgba(231, 76, 60, 0.8) !important; }

/* JWPlayer: Use skin "seven" */
    </div>
    
    <h3><span class="color-preview" style="background: #3498db;"></span>Blue Theme (Professional)</h3>
    <div class="code">
/* VideoJS Blue Theme */
.video-js .vjs-control-bar { background: rgba(52, 152, 219, 0.8) !important; }
.video-js .vjs-play-progress { background: #3498db !important; }
.video-js .vjs-big-play-button { background: rgba(52, 152, 219, 0.8) !important; }

/* JWPlayer: Use skin "six" */
    </div>
    
    <h3><span class="color-preview" style="background: #2ecc71;"></span>Green Theme (Nature)</h3>
    <div class="code">
/* VideoJS Green Theme */
.video-js .vjs-control-bar { background: rgba(46, 204, 113, 0.8) !important; }
.video-js .vjs-play-progress { background: #2ecc71 !important; }
.video-js .vjs-big-play-button { background: rgba(46, 204, 113, 0.8) !important; }

/* JWPlayer: Create custom green skin */
    </div>
    
    <h3><span class="color-preview" style="background: #9b59b6;"></span>Purple Theme (Fantasy - Current)</h3>
    <div class="code">
/* VideoJS Purple Theme (Current) */
.video-js .vjs-control-bar { background: rgba(155, 89, 182, 0.8) !important; }
.video-js .vjs-play-progress { background: #9b59b6 !important; }
.video-js .vjs-big-play-button { background: rgba(155, 89, 182, 0.8) !important; }

/* JWPlayer: Use skin "vapor" */
    </div>
    
    <h3><span class="color-preview" style="background: #34495e;"></span>Dark Theme (Modern)</h3>
    <div class="code">
/* VideoJS Dark Theme */
.video-js .vjs-control-bar { background: rgba(52, 73, 94, 0.9) !important; }
.video-js .vjs-play-progress { background: #ffffff !important; }
.video-js .vjs-big-play-button { background: rgba(52, 73, 94, 0.8) !important; }

/* JWPlayer: Use skin "glow" */
    </div>
</div>

<div class="example">
    <h2>🔧 Available JWPlayer Skins</h2>
    <p>The plugin includes these pre-built JWPlayer skins in <code>/jwplayer/skins/</code>:</p>
    
    <ul>
        <li><strong>glow.css</strong> - Dark theme with white controls</li>
        <li><strong>seven.css</strong> - Red accent theme (recommended for adult sites)</li>
        <li><strong>six.css</strong> - Blue theme</li>
        <li><strong>vapor.css</strong> - Purple/pink theme</li>
        <li><strong>stormtrooper.css</strong> - Black and white theme</li>
        <li><strong>beelden.css</strong> - Clean modern look</li>
        <li><strong>bekle.css</strong> - Minimalist design</li>
        <li><strong>five.css</strong> - Colorful theme</li>
        <li><strong>roundster.css</strong> - Rounded elements</li>
    </ul>
</div>

<div class="example">
    <h2>⚡ Super Quick Method</h2>
    <div class="step">
        <strong>For JWPlayer (Easiest):</strong> Just change one line!
    </div>
    
    <p>In <code>/jwplayer/player.php</code>, find the jwplayer setup and add:</p>
    <div class="code">
var jw = jwplayer("jwplayer").setup({
    // ... existing settings ...
    skin: { name: "seven" },  // Add this line for red theme
    // ... rest of settings ...
});
    </div>
    
    <div class="step">
        <strong>For VideoJS:</strong> Add CSS after the theme link
    </div>
    
    <p>In <code>/player/player.php</code>, after line 288, add:</p>
    <div class="code">
&lt;style&gt;
.video-js .vjs-play-progress { background: #e74c3c !important; }
.video-js .vjs-control-bar { background: rgba(231, 76, 60, 0.8) !important; }
&lt;/style&gt;
    </div>
</div>

<div class="example">
    <h2>🎯 Adult Site Color Schemes</h2>
    
    <h3>PornHub Style</h3>
    <div class="code">
/* Orange/Black Theme */
.video-js .vjs-control-bar { background: rgba(255, 144, 0, 0.8) !important; }
.video-js .vjs-play-progress { background: #ff9000 !important; }
    </div>
    
    <h3>XVideos Style</h3>
    <div class="code">
/* Red/Dark Theme */
.video-js .vjs-control-bar { background: rgba(196, 18, 48, 0.8) !important; }
.video-js .vjs-play-progress { background: #c41230 !important; }
    </div>
    
    <h3>RedTube Style</h3>
    <div class="code">
/* Bright Red Theme */
.video-js .vjs-control-bar { background: rgba(255, 0, 0, 0.8) !important; }
.video-js .vjs-play-progress { background: #ff0000 !important; }
    </div>
</div>

<div class="example">
    <h2>📝 Testing Your Changes</h2>
    <ol>
        <li>Make your color changes</li>
        <li>Clear browser cache (Ctrl+F5)</li>
        <li>Test with: <code>/debug-video.php</code></li>
        <li>Test both VideoJS and JWPlayer</li>
        <li>Check on mobile devices</li>
    </ol>
</div>

<div class="example">
    <h2>🔄 Backup Reminder</h2>
    <p><strong>Always backup before making changes:</strong></p>
    <div class="code">
cp player/player.php player/player.php.backup
cp jwplayer/player.php jwplayer/player.php.backup
    </div>
</div>

<div class="example">
    <h2>🆘 Need Help?</h2>
    <p>If colors don't appear:</p>
    <ul>
        <li>Check browser console for errors</li>
        <li>Ensure CSS is added in the correct location</li>
        <li>Clear all caches (browser + WordPress)</li>
        <li>Test with different browsers</li>
        <li>Use <code>!important</code> in CSS rules</li>
    </ul>
</div>

</body>
</html>