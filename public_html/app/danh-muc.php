<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$funcId       = 'danh-muc';
$message      = '';
$messageClass = '';

session_start();

//Connect DB
$con = openDB();

//Get param
$param = getParam();

$htmlNewsByCate = '';
$htmlNewsByCate = getNewsByCategory($con, $funcId, $param);

if (empty($param['url'])){
    closeDB();
    header('location: error404.php');
    exit();
} elseif ($param['url'] != $htmlNewsByCate['urlKey']){
    closeDB();
    header('location: error404.php');
    exit();
}

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
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="trang-chu.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tin Arsenal</li>
                        </ol>
                    </nav>
                    <div class="mt-5">
                        <div class="news-arsenal">
                            <h2 class="news-title-of-arsenal" style="margin-top: -26px;">
                                Chuyên mục: <a>Tin Arsenal</a>
                            </h2>
                            <div class="content-news-of-arsenal">
                                {$htmlNewsByCate['html']}
                                <div class="pagination-news ">
                                    <a href="# "><i class="fas fa-angle-left "></i></a>
                                    <a href="# " class="active ">1</a>
                                    <a href="# ">2</a>
                                    <a href="# ">3</a>
                                    <a href="# ">4</a>
                                    <a href="# ">5</a>
                                    <a href="# ">...&nbsp;</a>
                                    <a href="# "><i class="fas fa-angle-right "></i></a>
                                </div>
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

function getNewsByCategory($con, $funcId, $param){
    $pgParam = [];
    $pgParam[] = $param['url'];
    $recCnt = 0;
    $urlKey = '';

    $sql = "";
    $sql .= "SELECT news.id                         ";
    $sql .= "     , news.title                      ";
    $sql .= "     , news.shortdescription           ";
    $sql .= "     , news.thumbnail                  ";
    $sql .= "     , news.createby                   ";
    $sql .= "     , category.urlkey                 ";
    $sql .= "  FROM news                            ";
    $sql .= "  INNER JOIN category                  ";
    $sql .= "    ON news.category = category.id     ";
    $sql .= " WHERE news.deldate IS NULL            ";
    $sql .= "   AND category.deldate IS NULL        ";
    $sql .= "   AND category.urlkey = $1            ";
    $sql .= " ORDER BY news.createdate DESC         ";
    $sql .= " LIMIT 10                              ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        while ($row = pg_fetch_assoc($query)){
            $urlKey = $row['urlkey'];
            $titleEncoded = urlencode(str_replace(' ', '-', $row['title']));
            $urlRedirect = 'tin-tuc.php?key='.$row['id'].'&'.$titleEncoded.'';
            $html .= <<< EOF
                <div class="container content-news-of-arsenal">
                    <div class="row">
                        <a href="{$urlRedirect}" class="col-lg-5">
                            <img src="{$row['thumbnail']}" alt="{$row['thumbnail']}" class="card-img-top img-fuild">
                        </a>
                        <div class="col-lg-7 text-news-transfer mt-3">
                            <a href="{$urlRedirect}" class="header-news-transfer limit-text-line">{$row['title']}</a>
                            <div class="front-view-content mt-3 limit-text-line">
                                {$row['shortdescription']}
                            </div>
                            <div class="post-info">
                                <span>viết bởi <a style="color: #d61543;">{$row['createby']}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
EOF;
        }
    }
    return ['html' => $html, 'urlKey' => $urlKey];
}
?>