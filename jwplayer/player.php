<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
$tubeserver = strip_tags($_GET['tubeserver']);
$video = strip_tags($_GET['id']);
if(!ctype_alnum($tubeserver)){
  echo 'Invalid info.';
  exit;
}

function curl($url, $referer, $type = null) {
    $agent = ($type != null && $type == 'movil') ? 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; GT-I9300 Build/IMM76D)' : 'Mozilla/5.0(Windows;U;WindowsNT5.0;en-US;rv:1.4)Gecko/20030624Netscape/7.1(ax)';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $page = curl_exec($ch);
    curl_close($ch);
    return $page;
}

function getstring($string, $start, $end) {
    $str = explode($start, $string);
    
    // Verifica se o primeiro explode foi bem-sucedido e contém elementos
    if (isset($str[1])) {
        $str = explode($end, $str[1]);
        
        // Verifica se o segundo explode foi bem-sucedido e contém elementos
        if (isset($str[0])) {
            return $str[0];
        }
    }
    
    return ''; // Retorna uma string vazia se o processo falhar
}

function get_match_all($data, $start, $end) {
	$data   = preg_replace('/\s+/',' ',$data);
	preg_match_all("#".$start."(.*?)".$end."#si", $data, $newPattern, PREG_SET_ORDER);	
	return $newPattern;
}
function obtenerVideo($tubeserver, $video){
  if($tubeserver == 'xvideos'){
    $userAgent  = array('http' => array('user_agent' => 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; GT-I9300 Build/IMM76D)'));
    //$str = @file_get_contents('https://www.xvideos.com/video'.$video.'/', false, stream_context_create($userAgent) );
	$str = curl("https://www.xvideos.com/video".$video."/videoxxx", "http://www.xvideos.com", 'movil');
	
    if(!$str){return false;}
    $VideoUrlLow=getstring($str,"html5player.setVideoUrlLow('","');");
    $VideoUrlHigh=getstring($str,"html5player.setVideoUrlHigh('","');");
    $VideoUrlHD=getstring($str,"html5player.setVideoHLS('","');");
    if($VideoUrlHigh!=""){$mp4=$VideoUrlHigh;} else {$mp4=$VideoUrlLow;}
	if($VideoUrlHD!=""){$mp4=$VideoUrlHD;}
	/* using toolshot 
	$data_get=fetchUrl("http://player1.toolshot.com/?url=http://www.xvideos.com/video".$video."/", "http://www.xvideos.com", 'movil');
	preg_match('#source[^>]+src="([^"]+)"#mis', $data_get, $match);
	$mp4=$match[1];
	/* end using toolshot */
    $thumbnail = getstring($str,'<meta property="og:image" content="','"');

	
  }elseif($tubeserver == 'foxtube'){
    $url = 'http://r.foxtube.com/'.$video.'/es/';
    $str = file_get_contents($url);
    preg_match("/var v_path = '(.*)';/", $str, $mp4);
    $mp4 = $mp4[1];
    $thumbnail = 'http://v.fxtimg.com/'.$video.'/preview.jpg';
  }elseif($tubeserver == 'pornmaki'){
    $url = 'http://pornmaki.com/embed/'.$video;
    $str = file_get_contents($url);
    preg_match("/label:\"800\", file:\"(.*)\"/", $str, $mp4);
    preg_match("/var image = \"(.*)\";/", $str, $thumbnail);
    $mp4 = $mp4[1];
    $thumbnail = $thumbnail[1];  
	}elseif($tubeserver == 'befuck'){
	$source = curl("http://www.befuck.com/player/embed.xml?video_id=".$video, 'http://www.befuck.com');
		preg_match('/video_url="(.*)" preview_url/', $source, $mp4);
		preg_match('/preview_url="(.*)" embed/', $source, $foto);
		$mp4 = $mp4[1];
		$thumbnail = $foto[1];
  }elseif($tubeserver == 'pornoid'){
	$source = curl("http://www.pornoid.com/player/embed.xml?video_id={$video}", 'http://www.pornoid.com');
		preg_match('/video_url="(.*)" preview_url/', $source, $mp4);
		preg_match('/preview_url="(.*)" embed/', $source, $foto);
		$mp4 = $mp4[1];
		$thumbnail = $foto[1];
  }elseif($tubeserver == 'youjizz'){
	$source = curl("http://www.youjizz.com/videos/a-".$video.".html", 'http://www.youjizz.com', 'movil');
		preg_match('/<a class="preview_thumb" href="(.*)">/', $source, $mp4);
		preg_match('/<img height="226" width="300" src="(.*)" alt/', $source, $foto);
		$mp4 = $mp4[1];
		$thumbnail = $foto[1];
	}elseif($tubeserver == 'youporn'){
	$url = 'http://www.youporn.com/watch/'.$video.'/';
	$userAgent  = array('http' => array('user_agent' => 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; GT-I9300 Build/IMM76D)'));
    $str = file_get_contents($url, false, stream_context_create($userAgent) );
	if(!$str){return false;}
$mp4=getstring($str,'<video id="player-html5" class=\'videoPlayer\' src="','"');
	$thumbnail=getstring($str,'poster="','"');
  }elseif($tubeserver == 'redtube'){
    $userAgent  = array('http' => array('user_agent' => 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; GT-I9300 Build/IMM76D)'));
    $str = file_get_contents('http://www.redtube.com/'. $video .'/', false, stream_context_create($userAgent) );
  if(!$str){return false;}
	preg_match('/"videoUrl":"(.*)"/', $str, $mp4);
	$mp4=getstring($mp4[1] ,'"videoUrl":"','"');
  //$mp4=getstring($str ,'"videoUrl":"','"');
  $thumbnail=getstring($str ,'<meta property="og:image" content="','"');
  $mp4=str_replace("\\", "", $mp4);

	}elseif($tubeserver == 'tube8'){
    $source = curl("http://www.tube8.com/a/a/".$video."/", 'http://www.tube8.com', 'movil');
		preg_match('/page_params.video_urls.sd = "(.*)";/', $source, $video);
		preg_match('/<img id="videoImage" src="(.*)" \/>/', $source, $foto);
		$mp4 = $video[1];
		$thumbnail = $foto[1];

	}elseif($tubeserver == 'pornhub'){
		$videos = array();
		$source = curl("http://www.pornhub.com/view_video.php?viewkey=$video", 'http://pornhub.com', 'movil');
		//preg_match('/<img  class="mainImage removeWhenPlaying"  src="(.*)" alt="">/', $source, $foto);
		//$thumbnail=$foto[1];
		//$source = curl("https://www.pornhub.com/embed/$video", 'http://pornhub.com', 'movil');
		$thumbnail=getstring($source,'"image_url":"','"');
		$videos=get_match_all($source, '"videoUrl":"https:', '"}');
		$mp4='https:'.$videos[0][1];

  }elseif($tubeserver == 'xhamster'){
    $url = 'http://es.xhamster.com/movies/'.$video.'/.html?prs=--';
    $source = curl("http://xhamster.com/xembed.php?video=$video", 'http://xhamster.com');
	preg_match('/<a target="_blank" class="noFlash" href="(.*)">/', $source, $link);
	$link = substr($link[1], 0, strpos($link[1], '?'));
	$source = curl($link, 'http://xhamster.com');
	preg_match('/<a href="(.*)" class="mp4Thumb" target="_blank">/', $source, $videolink);
	$thumb=getstring($source,'class="mp4Thumb" target="_blank"',"<div class='iconPlay'>");
	
    $mp4 = $videolink[1];
	$thumbnail=getstring($thumb,'"','"');
  }
  return array ($mp4, $thumbnail);
}
$resultados = obtenerVideo($tubeserver, $video);
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
query_posts('showposts=9&cat=5&orderby=date');
if (have_posts()) : ?>
					
            <?php $i=0; while (have_posts()) : the_post(); if($current_post_id==get_the_ID()) continue; $i++; ?>
 
<?php
$thumb = get_post_meta( get_the_ID(), 'thumb', true ); 
if(!empty($thumb)) { 
$thumb=$thumb;
} elseif (kenplayer_get_thumbnail()!="") {$thumb=kenplayer_get_thumbnail();} else {$thumb="http://i.imgur.com/fjJMVKZ.jpg";}
?>
<a href="<?php the_permalink() ?>" class="kt_imgrc" title="<?php the_title_attribute(); ?>">
<span class="thumb-img">
	<img src="<?php echo $thumb; ?>" height="180" width="250" class="thumb"/> 

</span>
<span class="title"><?php echo $post->post_title; ?></span>

</a>

<?php endwhile; endif; wp_reset_query(); ?>


	</section>
<?php	
} else {
$mp4 = $resultados[0];
$thumbnail = $resultados[1];
$protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
if($protocol=='https'){
	$mp4=str_replace("http://","https://",$mp4);
	$thumbnail=str_replace("http://","https://",$thumbnail);
}


?>



<!DOCTYPE HTML>
<html>
<head>
<meta name="robots" content="noindex">
<style type="text/css">
html, body {
height: 100%;
width: 100%;
padding: 0;
margin: 0;
}
#jwplayer {
width: 100% !important;
height: 100% !important;
padding: 0;
}
@media (max-width: 540px) {
   .hidePauseAdZone{
        display: none !important;
    }    
}
</style>
</head>
<body>
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
  width: 60%;
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

<!DOCTYPE HTML>
<html>
<head>
<meta name="robots" content="noindex,nofollow" />
 <link href="https://vjs.zencdn.net/8.0.4/video-js.css" rel="stylesheet" />
<script type="text/javascript" src="https://content.jwplatform.com/libraries/sF5h5K2X.js"></script>
<script type="text/javascript">jwplayer.key="rqQQ9nLfWs+4Fl37jqVWGp6N8e2Z0WldRIKhFg==";</script>
<style type="text/css">
html, body {
height: 100%;
width: 100%;
padding: 0;
margin: 0;
}
#jwplayer {
width: 100% !important;
height: 100% !important;
padding: 0;
}

</style>
</head>
<body>
<body>

<?php if (get_option('kenplayer_ads') <> '') { ?>
<div style="position: relative;	width: 100%; height: 100%;">
<div id="jwplayer"></div>
<div class="hidePauseAdZone" style="<?php echo $adstart; ?>position: absolute;top: 15%;left: 50%;margin-left: -150px;text-align: center; background: #ccc;border: 1px solid #000;">
<span style="padding: 5px;display: block;text-align: center; color: #000;background: #ccc;font-size: 12px">
ADVERTISEMENT
<a href="#close" style="color: #fff; float:right; text-decoration: none" onclick="jw.play();">
<i style="margin-left: .3em; font-size: 17px; font-weight: bold"onmouseover="this.style.color='#FA80A5'"onmouseout="this.style.color='#FFF'">&times;</i>
</a>
</span>
<div style="width: 300px; height: 250px; display: table-cell; vertical-align: middle;">
<?php echo stripslashes(get_option('kenplayer_ads')); ?>
<div id="playerPause"></div>
</div>
</div>
</div>
<div style="display: none" class="adZonesHolder" data-id="playerPause">
<noindex><div class="pr-widget" data-h="200" data-res="true" data-w="300" id="pr-cw60"></div></noindex>
</div>
<?php } else { ?>
<div id="jwplayer"></div>
<?php } ?>
<script type="text/javascript">
<?php if (get_option('kenplayer_ads') <> '') { ?>
var jw = null;

    function moveAdZonesData(){
        var adzones = document.getElementsByClassName("adZonesHolder");
        for (var i=0; i < adzones.length; i++) {
            var id = adzones[i].dataset.id;
            if (null != id) {
                document.getElementById(id).innerHTML = adzones[i].innerHTML;
                document.getElementById(id).style.display == '<?php echo $adspause; ?>';
            } else {
                return false;
            }
        }
        return false;
    };
<?php } ?>
var jw = jwplayer("jwplayer").setup({
<?php if (is_array($mp4)) { ?>
sources: [
<?php foreach ($mp4 as $video){ echo '{  file: "'.$video['link'].'", label:"'.$video['label'].'" },';} ?>
],
<?php } else { ?>
   file: '<?php echo $mp4; ?>',
<?php } ?>   

	image:"<?php echo $thumbnail; ?>",
	width: "100%",
	height: "100%",
	aspectratio: "16:9",
	startparam: "start",
	autostart: false,
	primary: 'html5',
	
	logo: {
        file: "",
        link: "",
        hide: "true",
        position: "top-left",
    },


   }); 
<?php if (get_option('kenplayer_ads') <> '') { ?>  
jwplayer().on('pause', function(e) {
return document.getElementsByClassName("hidePauseAdZone")[0].style.display = '<?php echo $adspause; ?>';
});
jwplayer().on('play', function(e) {
return document.getElementsByClassName("hidePauseAdZone")[0].style.display = 'none';
});
moveAdZonesData();     
<?php } ?>     
</script>
</div><!--/.parent-->

<script data-cfasync="false" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


<?php
}
?>
<!--<?php if (in_array('curl', get_loaded_extensions())) {
  echo "cURL installed. ";
} else {
  echo "cURL NOT installed. ";
}
echo ini_get('allow_url_fopen') ? "file_get_content Enabled" : "file_get_content Disabled";
?>-->
<!--Kenplayer v2.0-->