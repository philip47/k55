<?php
// Secure WordPress loading
$wp_load_path = '';
$current_dir = dirname(__FILE__);

// Look for wp-load.php in parent directories (max 5 levels up for security)
for ($i = 0; $i < 5; $i++) {
    $check_path = $current_dir . str_repeat('/..', $i) . '/wp-load.php';
    if (file_exists($check_path)) {
        $wp_load_path = $check_path;
        break;
    }
}

if (empty($wp_load_path)) {
    die('WordPress not found');
}

require_once($wp_load_path);

// Verify nonce for security
if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'kenplayer_video_' . (isset($_GET['id']) ? $_GET['id'] : ''))) {
    wp_die('Security check failed');
}

// Sanitize and validate input parameters
$tubeserver = isset($_GET['tubeserver']) ? sanitize_text_field($_GET['tubeserver']) : '';
$video = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';

// Enhanced validation
if (empty($tubeserver) || empty($video)) {
    wp_die('Invalid parameters');
}

// Validate tubeserver against allowed values
$allowed_servers = array('xvideos', 'youporn', 'pornhub', 'redtube', 'xhamster');
if (!in_array($tubeserver, $allowed_servers)) {
    wp_die('Invalid video source');
}

// Validate video ID format (allow dots for XVideos)
if (!preg_match('/^[A-Za-z0-9\-_\.]+$/', $video)) {
    wp_die('Invalid video ID');
}

// Check for cached results first
$cache_key = 'video_' . $tubeserver . '_' . $video;
$cached_results = get_transient($cache_key);

if ($cached_results !== false) {
    $resultados = $cached_results;
} else {
    function secure_http_request($url, $referer = '', $type = null) {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Check if URL is from allowed domains
        $allowed_domains = array('xvideos.com', 'pornhub.com', 'redtube.com', 'youporn.com', 'xhamster.com');
        $parsed_url = parse_url($url);
        $domain = isset($parsed_url['host']) ? preg_replace('/^www\./', '', $parsed_url['host']) : '';
        
        if (!in_array($domain, $allowed_domains)) {
            return false;
        }
        
        $user_agent = ($type === 'mobile') 
            ? 'Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36'
            : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
        
        // Use WordPress HTTP API for better security
        $args = array(
            'timeout' => 15,
            'user-agent' => $user_agent,
            'sslverify' => true,
            'headers' => array(
                'Referer' => $referer,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate'
            )
        );
        
        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        return wp_remote_retrieve_body($response);
    }

    function getstring($string, $start, $end) {
        $str = explode($start, $string);
        
        // Check if first explode was successful and contains elements
        if (isset($str[1])) {
            $str = explode($end, $str[1]);
            
            // Check if second explode was successful and contains elements
            if (isset($str[0])) {
                return $str[0];
            }
        }
        
        return ''; // Return empty string if process fails
    }

    function get_match_all($data, $start, $end) {
        $data = preg_replace('/\s+/',' ', $data);
        preg_match_all("#" . $start . "(.*?)" . $end . "#si", $data, $newPattern, PREG_SET_ORDER);    
        return $newPattern;
    }

    function obtenerVideo($tubeserver, $video){
      if($tubeserver == 'xvideos'){
        // Handle both old numeric format (12345) and new alphanumeric format (ohlvebk93b7)
        if (strpos($video, '.') === false && is_numeric($video)) {
            // Old format: numeric ID
            $url = "https://www.xvideos.com/video" . $video . "/xvideosx";
        } else {
            // New format: alphanumeric with or without dot
            $url = "https://www.xvideos.com/video." . $video . "/xvideosx";
        }
        $str = secure_http_request($url, "https://www.xvideos.com", 'mobile');
        
        if(!$str){return false;}
        $VideoUrlLow = getstring($str, "html5player.setVideoUrlLow('", "');");
        $VideoUrlHigh = getstring($str, "html5player.setVideoUrlHigh('", "');");
        $VideoUrlHD = getstring($str, "html5player.setVideoHLS('", "');");
        if($VideoUrlHigh != "") {
            $mp4 = $VideoUrlHigh;
        } else {
            $mp4 = $VideoUrlLow;
        }
        if($VideoUrlHD != "") {
            $mp4 = $VideoUrlHD;
        }
        $thumbnail = getstring($str, '<meta property="og:image" content="', '"');
      } elseif($tubeserver == 'youporn'){
        $url = 'https://www.youporn.com/watch/' . $video . '/';
        $str = secure_http_request($url, 'https://www.youporn.com', 'mobile');
        if(!$str){return false;}
        $mp4 = getstring($str, '<video id="player-html5" class=\'videoPlayer\' src="', '"');
        $thumbnail = getstring($str, 'poster="', '"');
      } elseif($tubeserver == 'redtube'){
        $url = 'https://www.redtube.com/' . $video . '/';
        $str = secure_http_request($url, 'https://www.redtube.com', 'mobile');
        if(!$str){return false;}
        preg_match('/"videoUrl":"(.*)"/', $str, $mp4);
        if (isset($mp4[1])) {
            $mp4 = str_replace("\\", "", $mp4[1]);
        } else {
            $mp4 = '';
        }
        $thumbnail = getstring($str, '<meta property="og:image" content="', '"');
      } elseif($tubeserver == 'pornhub'){
        $videos = array();
        $url = "https://www.pornhub.com/view_video.php?viewkey=" . $video;
        $source = secure_http_request($url, 'https://pornhub.com', 'mobile');
        if(!$source){return false;}
        $thumbnail = getstring($source, '"image_url":"', '"');
        $videos = get_match_all($source, '"videoUrl":"https:', '"}');
        if (isset($videos[0][1])) {
            $mp4 = 'https:' . $videos[0][1];
        } else {
            $mp4 = '';
        }
      }
      return array($mp4, $thumbnail);
    }

    $resultados = obtenerVideo($tubeserver, $video);
    
    // Cache results for 3 hours if successful
    if ($resultados) {
        set_transient($cache_key, $resultados, 3 * HOUR_IN_SECONDS);
    }
}

// Don't redeclare the endsWith function if it already exists
if (!function_exists('endsWith')) {
    function endsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }
}

if(!$resultados){?>
<meta name="robots" content="noindex">
<base target="_parent" />
<style>
body{
  margin: 0;
  padding: 0;
  font-family: arial;
  text-align: center;
}
.kt_imgrc{
    text-align: center;
}
.thumbs a {
    display: inline-block;
    font-size: 13px;
    margin: 0 0 10px 20px;
    vertical-align: middle;
    width: 30%;
}
a {
  color: #81afcd;
  text-decoration: none;
  target: parent;
}
.thumbs-aside a {
  margin: 0 0 10px 0;
}
.thumbs a img {
  width: 70%;
  height: auto;
}
.kt_imgrc:hover a .title {
  background: #006d04;
}
.title {
    width: 60%;
    min-height: 15px;
    text-overflow: ellipsis;
    transition: background 0.3s ease 0s, color 0.3s ease 0s;
    white-space: nowrap;
}
.title, .tools {
    width: 60%;
    background: #ebebeb none repeat scroll 0 0;
    color: #152530;
    display: block;
    overflow: hidden;
    margin: 0 auto;
    padding: 2px 2px 1px 4px;
}
</style>
<h3>Confira mais vídeos:</h3>
<section class="thumbs thumbs-aside">
<?php
// Keep original query_posts to avoid breaking existing functionality
query_posts('showposts=9&cat=5&orderby=date');
if (have_posts()) : ?>
            <?php $i=0; while (have_posts()) : the_post(); if($current_post_id==get_the_ID()) continue; $i++; ?>
 
<?php
$thumb = get_post_meta(get_the_ID(), 'thumb', true); 
if(!empty($thumb)) { 
    $thumb = $thumb;
} elseif (function_exists('kenplayer_get_thumbnail') && kenplayer_get_thumbnail() != "") {
    $thumb = kenplayer_get_thumbnail();
} else {
    $thumb = "http://i.imgur.com/fjJMVKZ.jpg";
}
?>
<a href="<?php the_permalink() ?>" class="kt_imgrc" title="<?php the_title_attribute(); ?>">
<span class="thumb-img">
    <img src="<?php echo $thumb; ?>" height="180" width="250" class="thumb"/> 
</span>
<span class="title"><?php echo get_the_title(); ?></span>
</a>

<?php endwhile; endif; wp_reset_query(); ?>
</section>
<?php    
} else {
    $mp4 = $resultados[0];
    $thumbnail = $resultados[1];
    $protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
    if($protocol == 'https'){
        $mp4 = str_replace("http://", "https://", $mp4);
        $thumbnail = str_replace("http://", "https://", $thumbnail);
    }
?>
<meta name="robots" content="noindex">
<link href="https://unpkg.com/video.js@7/dist/video-js.min.css" rel="stylesheet"/>
<script src="https://unpkg.com/video.js@7/dist/video.js"></script>
<script src="https://unpkg.com/videojs-contrib-hls@5.15.0/dist/videojs-contrib-hls.js"></script>
<!--plugins-->
<script data-cfasync="false" src='../video-js/plugins/videojs.logobrand.js'></script>
<link href="../video-js/plugins/videojs.logobrand.css" rel="stylesheet">
<!--/plugins-->

<!-- Fantasy-->
<link href="https://unpkg.com/@videojs/themes@1/dist/fantasy/index.css" rel="stylesheet">

<style>
body{
  margin: 0;
  padding: 0;
}
#parent{
  position: relative;
}
#child {
  width: 320px;
  height: 300px;
  text-align: center;
  overflow: auto;
  margin: auto;
  top: 0; left: 0; bottom: 0; right: 0;
  position: fixed;
  z-index: 999;
}
.texto{
  margin: 0;
  padding: 0;
  font-size: 14px;
  font-weight: bold;
  color: #333;
  background: #fff;
  height: 10px;
}
*{margin:0;padding:0;box-sizing:border-box}body,html{height:100%}html{font-size:60%}body{line-height:1.5;font-family:sans-serif;background:#000}.vjs-resolution-button .vjs-menu-icon:before{content:'\f110';font-family:VideoJS;font-weight:400;font-style:normal;font-size:1.5em;line-height:2em}#dvr-vid:before,.tt-sh nav:before{content:''}.vjs-resolution-button .vjs-resolution-button-label{font-size:1em;line-height:3em;position:absolute;top:0;left:0;width:100%;height:100%;text-align:center;box-sizing:inherit}.vjs-resolution-button .vjs-menu .vjs-menu-content{width:3em;left:50%;margin-left:0}.tt-rl,.video-js{width:100%;height:100%}.vjs-menu{margin-left:-28px}.vjs-resolution-button .vjs-menu li{text-transform:none;font-size:1em}.vjs-thumbnail-holder{position:absolute;left:-1000px}.vjs-thumbnail{position:absolute;left:0;bottom:1.3em;opacity:0;transition:opacity .2s ease;-webkit-transition:opacity .2s ease;-moz-transition:opacity .2s ease;-mz-transition:opacity .2s ease}.pst,.tt-cn{position:relative}.vjs-progress-control.fake-active .vjs-thumbnail,.vjs-progress-control:active .vjs-thumbnail,.vjs-progress-control:hover .vjs-thumbnail{opacity:1}.vjs-progress-control:active .vjs-thumbnail:active,.vjs-progress-control:hover .vjs-thumbnail:hover{opacity:0}.video-js .vjs-progress-control:hover .vjs-mouse-display,.video-js .vjs-progress-holder .vjs-play-progress .vjs-time-tooltip,.vjs-play-progress:after{display:none}.vjs-big-play-button{left:0!important;top:0!important;bottom:0!important;right:0!important;border-radius:5px!important;margin:auto!important}.tt-cn,.tt-rl{display:-webkit-box;display:-moz-box;display:-ms-flexbox;display:-webkit-flex;display:flex}.tt-cn{z-index:11;background-color:rgba(0,0,0,.85);height:100%}.tt-rl{-webkit-flex-wrap:wrap;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:center;-moz-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;-webkit-align-content:center;-ms-flex-line-pack:center;align-content:center;-webkit-box-align:center;-moz-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center;z-index:3}.tt-rl>*{-webkit-box-flex:0;-webkit-flex:0 auto;-ms-flex:0 auto;flex:0 auto}.pst{width:25%;padding:1px}#tt-nv,.pst a>span,.pst img{width:100%;height:100%;left:0;top:0}.pst>a{display:block;padding-top:75%}.pst a>span,.pst img{position:absolute}.pst img{z-index:1;padding:1px;background-color:#000}.pst a>span{z-index:2;background-color:rgba(0,0,0,.85);padding:.5rem;font-size:1.2rem;color:#fff;transition:all .2s;opacity:0;overflow:hidden;line-height:22px}.bt-rp,.bt-sh,.ttl{line-height:2rem;color:#fff}.pst a:hover>span{opacity:1}.pst a>span span{display:block;font-size:1rem;opacity:.5}.bt-rp,.bt-sh,.tt-sh{display:inline-block}.pst a>span strong{text-align:right;position:absolute;right:1rem;bottom:1rem;opacity:.3;font-size:.8rem}.bt-sh,.tt-sh{position:relative}.tt-bt,.ttl{-webkit-flex:0 0 100%;-ms-flex:0 0 100%;flex:0 0 100%;text-align:center;padding-top:1rem}.tt-bt{-webkit-align-self:flex-end;-ms-flex-item-align:end;align-self:flex-end}.bt-rp,.bt-sh{background-color:rgba(0,0,0,.5);border:0;padding:0 1rem;font-size:.8rem;border-radius:5px;text-transform:uppercase;font-weight:700;cursor:pointer}.bt-rp:hover,.bt-sh:hover{background-color:#000}.bt-sh{z-index:10}#tt-nv{position:fixed;z-index:9;-webkit-appearance:none;-moz-appearance:none;background-color:rgba(255,255,255,.6);display:none}#tt-nv:checked,#tt-nv:checked~nav{display:block}.tt-sh nav{position:absolute;z-index:10;bottom:2.5rem;width:160px;left:50%;margin-left:-80px;background-color:rgba(0,0,0,.7);font-size:0;padding:1rem 0;border-radius:5px;display:none}.tt-sh nav:before{left:0;right:0;bottom:-5px;margin:auto;width:0;position:absolute;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:5px solid rgba(0,0,0,.7)}.fc,.tw,.ws{background-image:url(assets/img/tics.png);width:40px;height:40px;border-radius:20px;display:inline-block;margin:0 3px;transition:all .2s;position:relative}#dvr-vid,.logo{position:absolute}.fc:hover,.tw:hover,.ws:hover{top:-2px;opacity:.8}.fc{background-color:#5e81d3}.tw{background-position:-40px 0;background-color:#42c0f3}.ws{background-position:-80px 0;background-color:#1ad722}.ttl{font-size:1.2rem;font-weight:400;padding-top:0;padding-bottom:1rem}@media (max-width:600px){.tt-cn{padding:0 20px}.pst{display:none}.pst:first-of-type,.pst:first-of-type+.pst,.pst:first-of-type+.pst+.pst,.pst:first-of-type+.pst+.pst+.pst{width:50%;display:block}.pst>a{padding-top:50%}}@media (min-width:700px){html{font-size:70%}}@media (min-width:1000px){html{font-size:100%}}.logo{left:15px;top:15px;z-index:10}.video-js .vjs-load-progress div,.video-js .vjs-slider{background-color:rgba(0,0,0,.3)!important}.kuzminplayer-play{display:none}#dvr-vid{width:320px;height:270px;padding:10px;background-color:#fff;border-radius:5px;left:0;top:0;right:0;bottom:0;margin:auto;z-index:9999;box-shadow:0 0 0 100vh rgba(0,0,0,.7)}#dvr-vid:before{position:fixed;z-index:1;left:0;top:0;width:100%;height:100%}#dvr-vid>div{position:relative;z-index:2}#dvr-vid>a{position:absolute;right:-10px;top:-10px;width:30px;height:30px;line-height:22px;text-align:center;border-radius:50%;font-size:15px;font-weight:700;color:#fff;border:3px solid #fff;background-color:#ef3e3e;text-decoration:none;box-shadow:0 0 20px rgba(0,0,0,.2);z-index:3}#dvr-vid>a:hover{top:-11px}
</style>

<div id="parent">
<?php 
$ads = get_option('kenplayer_ads'); 
if ($ads != '') { 
    $seconds = get_option('kenplayer_seconds'); 
    if (!$seconds) { 
        $seconds = 10; 
    }
?>
<div id="child">
  <a href='#' onclick='this.parentNode.parentNode.removeChild(this.parentNode)'><img src="fancy_close.png"/></a>
  <?php echo $ads; ?>
</div>
<?php } ?>

<video id="video" class="video-js vjs-theme-fantasy"
  controls preload="auto" width="100%" height="100%"
  poster="<?php echo $thumbnail; ?>"
  data-setup='{"example_option":true}'>

<?php
if (endsWith($mp4, '.flv')) {
?>
 <source src="<?php echo $mp4; ?>" type='video/x-flv' />
<?php
} elseif (strpos($mp4, '.m3u8')) {
?>
 <source src="<?php echo $mp4; ?>" type="application/x-mpegURL" />
<?php
} else {
?>
 <source src="<?php echo $mp4; ?>" type='video/mp4' />
<?php
}
?>
</video>

</div><!--/.parent-->

<script data-cfasync="false" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script>
// Initialize video.js player
var video = document.querySelector('video');
var player = videojs(video);
player.logobrand({
    image: "<?php echo get_option('kenplayer_logo'); ?>",
    destination: "<?php echo get_site_url(); ?>"
});

// Hide ad after specified time
setTimeout(function() {
    $('#child').fadeOut('fast');
}, <?php echo ($seconds * 1000); ?>); 
</script>
<?php
}
?>
<!--Kenplayer v2.1-->