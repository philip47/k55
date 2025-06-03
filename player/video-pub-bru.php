<?php
$parse_uri = explode('wp-content', isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__);
require_once($parse_uri[0] . 'wp-load.php');
$tubeserver = strip_tags($_GET['tubeserver']);
$video = strip_tags($_GET['id']);
if (!ctype_alnum($tubeserver)) {
    echo 'Erro.';
    exit;
}

function getstring($string, $start, $end) {
    $str = explode($start, $string);
    if (isset($str[1])) {
        $str = explode($end, $str[1]);
        return $str[0];
    }
    return ''; // Adiciona retorno padrão se $start não for encontrado
}

function obtenerVideo($tubeserver, $video) {
    $mp4 = ''; // Inicializa as variáveis
    $thumbnail = ''; // Inicializa as variáveis
    
    if ($tubeserver == 'xvideos') {
        // Implementar lógica para xvideos
        $str = ''; // Define $str aqui se necessário
        if (empty($str)) {
            return false;
        }
        // A lógica de atribuição de $mp4 e $thumbnail deve ser aqui
    } elseif ($tubeserver == 'xhamster') {
        // Implementar lógica para xhamster
    }

    return array($mp4, $thumbnail);
}

$resultados = obtenerVideo($tubeserver, $video);
if (!$resultados) { ?>
    <meta name="robots" content="noindex">
    <base target="_parent" />
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: arial;
        text-align: center;
    }
    .sg_img {
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

    <!-- floater -->
    <style type="text/css">*{margin:0;padding:0}#picasa{position:absolute;width:100%!important;height:100%!important}.jw-button-color:hover,.jw-toggle,.jw-toggle:hover,.jw-open,.jw-progress{color:#ff00de!important;}.jw-active-option{background-color:#ff00de!important;}.jw-progress{background:#ff00de!important;}.jw-skin-seven .jw-toggle.jw-off{color:#fff!important}</style>
    <style type="text/css">#ads-foda-fx{position:fixed;width:300px;height:250px;top:10%;z-index:999999999999999;left:0;right:0;margin:0 auto;border:0 solid #ddd;-webkit-box-shadow:0 0 0 0 #B0B0B0;box-shadow:0 0 0 0 #000;-webkit-border-radius:4px 4px 4px 4px;border-radius:4px}#ads-foda-fx #fecharads2-fx{position:absolute;left:99%;top:6%;width:24px;height:24px;background:url(https://i.imgur.com/gc5Xawt.png) no-repeat;cursor:pointer}#ads-foda-fx #fecharads1-fx{opacity:.7;position:absolute;right:93%;bottom:92%;width:10px;height:10px;background:url() no-repeat;cursor:pointer;display:block}#ads-foda-fx #fecharads1-fx:hover{opacity:1}</style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">$a=jQuery.noConflict(),$a(document).ready(function(a){$a("#fecharads1-fx").hover(function(){$a("#fecharads1-fx").hide(),$a("#fecharads2-fx").css("display","block")}),$a("#fecharads2-fx").click(function(){$a("#ads-foda-fx").css("display","none")})});</script>
    <script>$(document).ready(function(){$(document).bind("contextmenu",function(n){return!1})});</script>
    <script>function appeardiv(){document.getElementById("mostrar").style.display="block"}window.onload=function(){setTimeout(appeardiv,5000)};</script>
    <center>
    <!-- fim do floater -->

    <h3>Confira mais vídeos:</h3>
    <a href="https://bit.ly/bruninhanet" target="_blank" rel="nofollow noopener noreferrer"><img class="aligncenter perfmatters-lazy entered pmloaded" src="https://www.sogatinhas.net/img/banners/packs.jpg" alt="" width="591" height="350" data-src="https://www.sogatinhas.net/img/banners/packs.jpg" data-ll-status="loaded"><noscript><img class="aligncenter" src="https://www.sogatinhas.net/img/banners/packs.jpg" alt="" width="591" height="350" /></noscript></a>					
</center>
<?php
}
?>