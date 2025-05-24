<!DOCTYPE html>
<html>
<head>
    <title>KenPlayer Color Demo</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .color-scheme {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .color-preview {
            display: flex;
            gap: 15px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        .color-box {
            width: 100px;
            height: 60px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            text-align: center;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        h2 {
            color: #555;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .instructions {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <h1>🎨 KenPlayer Color Schemes Demo</h1>
        
        <div class="instructions">
            <strong>How to Use:</strong>
            <ol>
                <li>Choose a color scheme below</li>
                <li>Copy the CSS code</li>
                <li>Add it to your player files (see PLAYER-COLOR-CUSTOMIZATION.md)</li>
                <li>Test with debug-video.php</li>
            </ol>
        </div>

        <!-- Dark Theme -->
        <div class="color-scheme">
            <h2>🌙 Dark Theme (Professional)</h2>
            <div class="color-preview">
                <div class="color-box" style="background: #1a1a1a;">Control Bar<br>#1a1a1a</div>
                <div class="color-box" style="background: #ff6b6b;">Progress<br>#ff6b6b</div>
                <div class="color-box" style="background: #ffffff; color: #000;">Buttons<br>#ffffff</div>
                <div class="color-box" style="background: #333333;">Background<br>#333333</div>
            </div>
            <div class="code-block">
/* Dark Theme CSS */
.video-js .vjs-control-bar {
    background-color: #1a1a1a !important;
}
.video-js .vjs-progress-control .vjs-play-progress {
    background-color: #ff6b6b !important;
}
.video-js .vjs-button > .vjs-icon-placeholder:before {
    color: #ffffff !important;
}
.video-js .vjs-big-play-button {
    background-color: #1a1a1a !important;
    border-color: #ff6b6b !important;
}

/* JWPlayer Dark Theme */
.jw-skin-dark .jw-controlbar { background: #1a1a1a !important; }
.jw-skin-dark .jw-progress { background: #ff6b6b !important; }
.jw-skin-dark .jw-button-color { color: #ffffff !important; }
            </div>
        </div>

        <!-- Blue Theme -->
        <div class="color-scheme">
            <h2>💙 Blue Theme (Corporate)</h2>
            <div class="color-preview">
                <div class="color-box" style="background: #2c3e50;">Control Bar<br>#2c3e50</div>
                <div class="color-box" style="background: #3498db;">Progress<br>#3498db</div>
                <div class="color-box" style="background: #ecf0f1; color: #000;">Buttons<br>#ecf0f1</div>
                <div class="color-box" style="background: #34495e;">Background<br>#34495e</div>
            </div>
            <div class="code-block">
/* Blue Theme CSS */
.video-js .vjs-control-bar {
    background-color: #2c3e50 !important;
}
.video-js .vjs-progress-control .vjs-play-progress {
    background-color: #3498db !important;
}
.video-js .vjs-button > .vjs-icon-placeholder:before {
    color: #ecf0f1 !important;
}
.video-js .vjs-big-play-button {
    background-color: #2c3e50 !important;
    border-color: #3498db !important;
}

/* JWPlayer Blue Theme */
.jw-skin-blue .jw-controlbar { background: #2c3e50 !important; }
.jw-skin-blue .jw-progress { background: #3498db !important; }
.jw-skin-blue .jw-button-color { color: #ecf0f1 !important; }
            </div>
        </div>

        <!-- Red Theme -->
        <div class="color-scheme">
            <h2>❤️ Red Theme (Bold)</h2>
            <div class="color-preview">
                <div class="color-box" style="background: #2c1810;">Control Bar<br>#2c1810</div>
                <div class="color-box" style="background: #e74c3c;">Progress<br>#e74c3c</div>
                <div class="color-box" style="background: #ffffff; color: #000;">Buttons<br>#ffffff</div>
                <div class="color-box" style="background: #8b0000;">Background<br>#8b0000</div>
            </div>
            <div class="code-block">
/* Red Theme CSS */
.video-js .vjs-control-bar {
    background-color: #2c1810 !important;
}
.video-js .vjs-progress-control .vjs-play-progress {
    background-color: #e74c3c !important;
}
.video-js .vjs-button > .vjs-icon-placeholder:before {
    color: #ffffff !important;
}
.video-js .vjs-big-play-button {
    background-color: #2c1810 !important;
    border-color: #e74c3c !important;
}

/* JWPlayer Red Theme */
.jw-skin-red .jw-controlbar { background: #2c1810 !important; }
.jw-skin-red .jw-progress { background: #e74c3c !important; }
.jw-skin-red .jw-button-color { color: #ffffff !important; }
            </div>
        </div>

        <!-- Green Theme -->
        <div class="color-scheme">
            <h2>💚 Green Theme (Nature)</h2>
            <div class="color-preview">
                <div class="color-box" style="background: #1e3a1e;">Control Bar<br>#1e3a1e</div>
                <div class="color-box" style="background: #27ae60;">Progress<br>#27ae60</div>
                <div class="color-box" style="background: #ffffff; color: #000;">Buttons<br>#ffffff</div>
                <div class="color-box" style="background: #2d5a2d;">Background<br>#2d5a2d</div>
            </div>
            <div class="code-block">
/* Green Theme CSS */
.video-js .vjs-control-bar {
    background-color: #1e3a1e !important;
}
.video-js .vjs-progress-control .vjs-play-progress {
    background-color: #27ae60 !important;
}
.video-js .vjs-button > .vjs-icon-placeholder:before {
    color: #ffffff !important;
}
.video-js .vjs-big-play-button {
    background-color: #1e3a1e !important;
    border-color: #27ae60 !important;
}

/* JWPlayer Green Theme */
.jw-skin-green .jw-controlbar { background: #1e3a1e !important; }
.jw-skin-green .jw-progress { background: #27ae60 !important; }
.jw-skin-green .jw-button-color { color: #ffffff !important; }
            </div>
        </div>

        <!-- Purple Theme (Current) -->
        <div class="color-scheme">
            <h2>💜 Purple Theme (Current Fantasy)</h2>
            <div class="color-preview">
                <div class="color-box" style="background: #663399;">Control Bar<br>#663399</div>
                <div class="color-box" style="background: #ff69b4;">Progress<br>#ff69b4</div>
                <div class="color-box" style="background: #ffffff; color: #000;">Buttons<br>#ffffff</div>
                <div class="color-box" style="background: #4b0082;">Background<br>#4b0082</div>
            </div>
            <div class="code-block">
/* Purple Theme CSS (Current) */
.video-js .vjs-control-bar {
    background-color: #663399 !important;
}
.video-js .vjs-progress-control .vjs-play-progress {
    background-color: #ff69b4 !important;
}
.video-js .vjs-button > .vjs-icon-placeholder:before {
    color: #ffffff !important;
}
.video-js .vjs-big-play-button {
    background-color: #663399 !important;
    border-color: #ff69b4 !important;
}

/* JWPlayer Purple Theme */
.jw-skin-purple .jw-controlbar { background: #663399 !important; }
.jw-skin-purple .jw-progress { background: #ff69b4 !important; }
.jw-skin-purple .jw-button-color { color: #ffffff !important; }
            </div>
        </div>

        <!-- Orange Theme -->
        <div class="color-scheme">
            <h2>🧡 Orange Theme (Vibrant)</h2>
            <div class="color-preview">
                <div class="color-box" style="background: #3d2914;">Control Bar<br>#3d2914</div>
                <div class="color-box" style="background: #f39c12;">Progress<br>#f39c12</div>
                <div class="color-box" style="background: #ffffff; color: #000;">Buttons<br>#ffffff</div>
                <div class="color-box" style="background: #d68910;">Background<br>#d68910</div>
            </div>
            <div class="code-block">
/* Orange Theme CSS */
.video-js .vjs-control-bar {
    background-color: #3d2914 !important;
}
.video-js .vjs-progress-control .vjs-play-progress {
    background-color: #f39c12 !important;
}
.video-js .vjs-button > .vjs-icon-placeholder:before {
    color: #ffffff !important;
}
.video-js .vjs-big-play-button {
    background-color: #3d2914 !important;
    border-color: #f39c12 !important;
}

/* JWPlayer Orange Theme */
.jw-skin-orange .jw-controlbar { background: #3d2914 !important; }
.jw-skin-orange .jw-progress { background: #f39c12 !important; }
.jw-skin-orange .jw-button-color { color: #ffffff !important; }
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Important Notes:</strong>
            <ul>
                <li>Always backup your files before making changes</li>
                <li>Test colors on different devices and screen sizes</li>
                <li>Ensure good contrast for accessibility</li>
                <li>Clear browser cache after applying changes</li>
                <li>Use the debug-video.php file to test changes quickly</li>
            </ul>
        </div>

        <div class="instructions">
            <strong>📁 Files to Edit:</strong>
            <ul>
                <li><strong>VideoJS:</strong> /player/player.php (around line 290)</li>
                <li><strong>JWPlayer:</strong> /jwplayer/player.php (around line 450)</li>
                <li><strong>Admin Panel:</strong> /transform.php (see add-color-options-example.php)</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 40px 0; color: #666;">
            <p>🎨 For more customization options, see <strong>PLAYER-COLOR-CUSTOMIZATION.md</strong></p>
            <p>🔧 To add admin color options, see <strong>add-color-options-example.php</strong></p>
        </div>
    </div>
</body>
</html>