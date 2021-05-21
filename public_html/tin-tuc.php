<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$funcId       = 'tin-tuc';
$message      = '';
$messageClass = '';

session_start();

//Connect DB
$con = openDB();

//Get param
$param = getParam();

if (empty($param['key'])){
    closeDB();
    header('location: error404.php');
    exit();
}

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$curPageURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$htmlNews = '';
$htmlNews = getNewsById($con, $funcId, $param);

$htmlOutstandingNews = '';
$htmlOutstandingNews = getOutstandingNews($con, $funcId);

//Decode to HTML icon
$iconCate = html_entity_decode($htmlNews['icon']);
//Format date
$datePost = date('d/m/Y', strtotime($htmlNews['createdate']));
$timePost = date('H:i', strtotime($htmlNews['createdate']));

//-----------------------------------------------------------
// HTML
//-----------------------------------------------------------
$titleHTML = '';
$cssHTML = '';
$scriptHTML = '';

echo <<<EOF
<!DOCTYPE html>
<html>
<head>
EOF;

//Meta CSS
include ($TEMP_APP_META_PATH);

echo <<<EOF
</head>
<body style="background-color: #f1f2f3;" id="{$funcId}">
EOF;
//Header
include ($TEMP_APP_HEADER_PATH);
//Conntent
echo <<<EOF
<!--Navbar-->
    <div class="container-fuild">
    <main class="container" style="padding-top: 100px;">
        <div class="row">
            <article class="col-md-8 col-sm-12 col-xs-12">
                <div class="content-box-news">
                    <nav aria-label="breadcrumb">
                        <a href="http://{$_SERVER['SERVER_NAME']}"><i class="fas fa-home"></i> Trang chủ</a> / <a href="danh-muc.php?url={$htmlNews['urlkey']}">{$iconCate}&nbsp{$htmlNews['category']}</a> / <a>{$htmlNews['title']}</a>
                    </nav>
                   <header class="mt-5">
                        <a href="danh-muc.php?url={$htmlNews['urlkey']}" class="thecategory">{$htmlNews['category']}</a>
                        <p class="mt-4" style="font-family: 'Open Sans'">
                            Đăng bởi <font style="color: #d61543; ">{$htmlNews['createby']}</font> vào lúc {$timePost} - {$datePost}
                        </p>
                    </header>
                    {$htmlNews['html']}
                </div>
                <div class="share-on-social">
                    <div class="fb-share-button" data-href="{$curPageURL}" data-layout="button" data-size="large">
                        <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a>
                    </div>
                </div>
                <div class="news-proposal">
                    <h3 style="font-family: 'SVN-AgencyFBbold'; border-bottom: 4px solid rgba(0, 0, 0, 0.15); padding-bottom: 18px; color: #454d59; ">Tin đề xuất</h3>
                    <div class="content-news-down">
                        <div class="row">
                            {$htmlOutstandingNews}
                        </div>
                    </div>
                </div>
            </article>
EOF;

//Aside
include($TEMP_APP_ASIDE_PATH);

echo <<< EOF
        </div>
    </main>
EOF;

//Footer
include($TEMP_APP_FOOTER_PATH);

//Meta JS
include ($TEMP_APP_METAJS_PATH);
echo <<<EOF
</body>
</html>
EOF;

function getNewsById($con, $funcId, $param){
    $dataNews = [];
    $pgParam = [];
    $pgParam[] = $param['key'];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT news.id                         ";
    $sql .= "     , news.title                      ";
    $sql .= "     , news.shortdescription           ";
    $sql .= "     , news.thumbnail                  ";
    $sql .= "     , news.content                    ";
    $sql .= "     , news.createby                   ";
    $sql .= "     , news.createdate                 ";
    $sql .= "     , news.category                   ";
    $sql .= "     , category.category               ";
    $sql .= "     , category.icon                   ";
    $sql .= "     , category.urlkey                 ";
    $sql .= "  FROM news                            ";
    $sql .= " INNER JOIN category                   ";
    $sql .= "    ON news.category = category.id     ";
    $sql .= " WHERE news.deldate IS NULL            ";
    $sql .= "   AND category.deldate IS NULL        ";
    $sql .= "   AND news.id = $1                    ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        $dataNews = pg_fetch_assoc($query);
        $thumbnail = checkImage($dataNews['thumbnail']);
        $contentNews = html_entity_decode($dataNews['content']);
        $html .= <<< EOF
            <h1 class="title-h1-news">{$dataNews['title']}</h1>
            <div class="featured-thumbnail">
                <img src="{$thumbnail}" alt="{$thumbnail}" width="100%">
            </div>
            <div class="post-content-news">
                <h3>{$dataNews['shortdescription']}</h3>
                 {$contentNews}
                <p class="mt-3" style="text-align: center;">
                    <em>
                    <strong>Nếu thấy bài viết hay, hãy bấm vào quảng cáo để ủng hộ team Arsenal Quán nhé! Thay mặt BQT xin chân thành cám ơn. Love you 3000 <3
                    </strong>
                  </em>
                </p>
            </div>
EOF;
    }
    return ['html'   => $html,
        'createby'   => $dataNews['createby'],
        'category'   => $dataNews['category'],
        'icon'       => $dataNews['icon'],
        'createdate' => $dataNews['createdate'],
        'title'      => $dataNews['title'],
        'urlkey'     => $dataNews['urlkey'],
        'thumbnail'  => $dataNews['thumbnail'],
        'title'      => $dataNews['title'],
        'shortdescription' => $dataNews['shortdescription']
    ];
}

function getOutstandingNews($con, $funcId){
    $pgParam = [];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT id                  ";
    $sql .= "     , title               ";
    $sql .= "     , shortdescription    ";
    $sql .= "     , thumbnail           ";
    $sql .= "     , createby            ";
    $sql .= "  FROM news                ";
    $sql .= " WHERE deldate IS NULL     ";
    $sql .= " ORDER BY createdate DESC  ";
    $sql .= " LIMIT 4                   ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        while ($row = pg_fetch_assoc($query)){
            $thumbnail = checkImage($row['thumbnail']);
            $titleEncoded = convert_name($row['title']);
            $urlRedirect = 'tin-tuc.php?key='.$row['id'].'&'.$titleEncoded.'';
            $html .= <<< EOF
            <div class="container content-news-transfer mt-unset">
                <div class="row">
                    <a href="{$urlRedirect}" class="col-lg-5">
                        <img src="{$thumbnail}" alt="{$thumbnail}" class="card-img-top img-fuild object-fit-image">
                    </a>
                    <div class="col-lg-7 text-news-transfer mt-3">
                        <a class="header-news-transfer limit-text-line" href="{$urlRedirect}">{$row['title']}</a>
                        <div class="front-view-content mt-3 limit-text-line">
                            {$row['shortdescription']}
                        </div>
                        <div class="post-info">
                            <span>Đăng bởi <a href="/" style="color: #d61543;">{$row['createby']}</a></span>
                        </div>
                    </div>
                </div>
            </div>
EOF;
        }
    }
    return $html;
}
?>