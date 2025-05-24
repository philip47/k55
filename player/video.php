<meta name="robots" content="noindex">

<?php
// Set security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://ajax.googleapis.com; img-src 'self' https://i.imgur.com data:;");

// Validate and sanitize input parameters
$tubeserver = isset($_GET['tubeserver']) ? htmlspecialchars($_GET['tubeserver']) : '';
$video = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';

// More strict validation
if(!ctype_alnum($tubeserver) || empty($tubeserver)) {
  header("HTTP/1.1 403 Forbidden");
  echo 'Invalid request parameters.';
  exit;
}

// Load WordPress if the path exists
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
if (isset($parse_uri[0]) && file_exists($parse_uri[0] . 'wp-load.php')) {
  require_once($parse_uri[0] . 'wp-load.php');
} else {
  header("HTTP/1.1 500 Internal Server Error");
  exit;
}

function getstring($string, $start, $end) {
  if (empty($string) || empty($start) || empty($end)) {
    return '';
  }
  $str = explode($start, $string);
  if (count($str) < 2) {
    return '';
  }
  $str = explode($end, $str[1]);
  return $str[0];
}

function obtenerVideo($tubeserver, $video) {
  $mp4 = '';
  $thumbnail = '';
  
  // Implement proper validation and fetching logic
  if($tubeserver == 'xvideos') {
    // Secure implementation here
  } elseif($tubeserver == 'xhamster') {
    // Secure implementation here
  }
  
  return array($mp4, $thumbnail);
}

$resultados = obtenerVideo($tubeserver, $video);
if(!$resultados) {
?>
<meta name="robots" content="noindex">
<base target="_parent" />
<style>
body{
  margin: 0;
  padding: 0;
  font-family: arial;
  text-align: center;
}
.sg_img{
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
}
.thumbs-aside a {
  margin: 0 0 20px 0;
}
.thumbs a img {
  width: 90%;
  height: 110px;
}
.sg_img:hover a .title {
  background: #006d04;
}
.title {
  width: 70%;
  min-height: 15px;
  text-overflow: ellipsis;
  transition: background 0.3s ease 0s, color 0.3s ease 0s;
  white-space: nowrap;
}
.title, .tools {
  width: 87%;
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
    // Optimize WordPress query with proper parameters and security
    $recent_posts = wp_get_recent_posts(array(
        'cat'         => '5',
        'tag'         => 'brasileiras',
        'numberposts' => 6,
        'post_status' => 'publish',
        'orderby'     => 'rand',
        'order'       => 'DESC'
    ));
    
    foreach($recent_posts as $post) : 
        // Escape output to prevent XSS
        $permalink = esc_url(get_permalink($post['ID']));
        $title = esc_attr($post['post_title']);
    ?>
        <a href="<?php echo $permalink; ?>" class="sg_img" title="<?php echo $title; ?>">
            <span class="thumb-img">
                <?php echo get_the_post_thumbnail($post['ID'], array(220, 162)); ?>
            </span>
            <span class="title"><p class="slider-caption-class"><?php echo esc_html($post['post_title']); ?></p></span>
        </a>
    <?php endforeach; wp_reset_query(); ?>
</section>
<?php
}
?>