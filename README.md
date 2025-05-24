# KenPlayer Transformer v3.0.0

A WordPress plugin that transforms embedded video players from adult video sites into custom video players with enhanced security and performance.

## Features

- **Secure Video Transformation**: Converts embedded videos from adult sites (XVideos, PornHub, RedTube, YouPorn) into custom players
- **Enhanced Security**: 
  - Input validation and sanitization
  - CSRF protection with nonces
  - Domain whitelisting for external requests
  - Secure HTTP requests using WordPress HTTP API
  - XSS protection
- **Performance Optimizations**:
  - Caching for video data and transformations
  - Optimized regex patterns
  - Reduced HTTP requests
- **WordPress Compatibility**: 
  - Compatible with WordPress 5.0+
  - Follows WordPress coding standards
  - Proper capability checks
  - Internationalization ready

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- cURL extension (recommended)

## Installation

1. Upload the plugin files to `/wp-content/plugins/kenplayer-transformer/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings in the admin panel

## Configuration

### Basic Settings

- **Activation**: Enable/disable video transformation
- **Player Type**: Choose between VideoJS or JWPlayer
- **Video Sources**: Select which adult video sites to transform
- **Responsive**: Enable responsive video players with FluidVids

### Advanced Settings

- **Logo**: Set custom logo and link
- **Advertising**: Add custom advertising HTML
- **Custom Fields**: Use custom fields for embed codes
- **Poster Images**: Set default poster images

## Security Features

### Input Validation
- All user inputs are sanitized and validated
- Video IDs are validated against allowed patterns
- URLs are validated and restricted to allowed domains

### CSRF Protection
- Nonce verification for all forms and AJAX requests
- Secure token generation for video players

### Domain Whitelisting
- External requests limited to approved domains
- HTTPS-only connections for license verification

### XSS Prevention
- All output is properly escaped
- HTML content is filtered through wp_kses

## Performance Optimizations

### Caching
- Video data cached for 3 hours
- Transformation results cached for 1 hour
- HTTP requests cached for 30 minutes

### Efficient Processing
- Early returns for disabled features
- Optimized regex patterns
- Reduced database queries

## Supported Video Sites

- **XVideos** - All URL formats including new alphanumeric IDs (video.abc123, video123456)
- **PornHub** - Both viewkey and embed URLs
- **RedTube** - Standard video URLs
- **YouPorn** - Watch URLs with video IDs
- **XHamster** - Videos and movies URLs
- **Direct Video Files** - MP4, FLV, WebM, M4V
- **Google Drive** - Shared video files
- **YouTube** - Standard video URLs

## Shortcode Usage

Use the `[kenplayer]` shortcode to embed videos in posts and pages:

```
[kenplayer url="VIDEO_URL" width="735" height="400"]
```

### Supported Parameters:
- `url` - The video URL (required)
- `width` - Player width in pixels (default: 735)
- `height` - Player height in pixels (default: 400)

### Example Usage:
```
[kenplayer url="https://www.xvideos.com/video12345/sample-video"]
[kenplayer url="https://www.pornhub.com/view_video.php?viewkey=abc123" width="800" height="450"]
[kenplayer url="https://example.com/video.mp4"]
```

### Troubleshooting Shortcode Issues:

If the shortcode is not working:

1. **Check if shortcode is registered**: Upload `shortcode-test.php` to your WordPress root directory and visit it in your browser
2. **Verify plugin activation**: Make sure the plugin is activated in WordPress admin
3. **Check for PHP errors**: Look in your error logs for any PHP errors
4. **Test with sample URLs**: Use the test file to verify functionality

### Testing the Shortcode:

1. Upload the included `shortcode-test.php` file to your WordPress root directory
2. Visit `yoursite.com/shortcode-test.php` in your browser
3. This will show you if the shortcode is working and provide debugging information

## Developer Notes

### Hooks and Filters

- `kenplayer_video_sources`: Filter available video sources
- `kenplayer_player_config`: Modify player configuration
- `kenplayer_cache_duration`: Adjust cache durations

### Constants

- `KENPLAYER_VERSION`: Plugin version
- `KENPLAYER_PLUGIN_URL`: Plugin URL
- `KENPLAYER_PLUGIN_PATH`: Plugin path

## Changelog

### Version 3.0.0
- Complete security overhaul
- WordPress 6.6 compatibility
- Performance optimizations
- Enhanced input validation
- CSRF protection implementation
- Caching improvements
- Code quality improvements

### Version 2.1
- Original release
- Basic video transformation
- Admin interface

## License

GPL v2 or later

## Support

For support and updates, visit: http://xwpthemes.com

## Security Considerations

This plugin handles adult content and should be used responsibly:

- Ensure your site complies with local laws and regulations
- Implement appropriate age verification if required
- Consider privacy implications of video transformations
- Regularly update the plugin for security patches

## Technical Details

### File Structure
```
kenplayer-transformer/
├── transform.php (Main plugin file)
├── create_tag_func.php (Core functionality)
├── connect.php (License activation)
├── player/ (VideoJS player files)
├── jwplayer/ (JWPlayer files)
├── js/ (JavaScript files)
├── css/ (Stylesheets)
└── README.md
```

### Database Options
- `kenplayer_activation`: Enable/disable transformation
- `kenplayer_jwplayer`: Player type selection
- `kenplayer_*`: Various configuration options

### Caching Keys
- `kenplayer_transform_*`: Transformation cache
- `kenplayer_curl_*`: HTTP request cache
- `video_*`: Video data cache