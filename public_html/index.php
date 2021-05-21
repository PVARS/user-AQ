<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$funcId  = 'homepage';

session_start();

//Connect DB
$con = openDB();

//Get param
$param = getParam();

$htmlOutstandingNews = '';
$htmlOutstandingNews = getOutstandingNews($con, $funcId);

$htmlOutstandingNewsNext = '';
$htmlOutstandingNewsNext = getOutstandingNewsNext($con, $funcId);

$htmlTransferNews = '';
$htmlTransferNews = getTransferNews($con, $funcId);

$htmlNewsArsenal = '';
$htmlNewsArsenal = getNewsArsenal($con, $funcId);

$htmlAnalysisArsenal = '';
$htmlAnalysisArsenal = getAnalysisArsenal($con, $funcId);

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
        <div id="carousel" style="padding-top: 70px;">
            <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleCaptions" data-slide-to="1" class=""></li>
                    <li data-target="#carouselExampleCaptions" data-slide-to="2" class=""></li>
                </ol>
                <div class="carousel-inner">
                    {$htmlOutstandingNews}
                </div>
                <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
    <main class="container" style="padding-top: 50px;">
        <div class="row">
            <article class="col-md-8 col-sm-12 col-xs-12">
                <!--Content-->
                <div class="title-arsenal">
                    <h2 class="news-title-arsenal">Tin Mới Nhất</h2>
                </div>
                <div class="card-news-content">
                    <div class="card-news">
                        {$htmlOutstandingNewsNext['htmlOne']}
                    </div>

                    <div class="content-news-down">
                        <div class="row">
                            {$htmlOutstandingNewsNext['htmlThree']}
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center block-mt">
                    <div class="pagination mt-5">
                        <a class="loadMore" href="/news-page">Xem thêm&nbsp;<i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="news-transfer-arsenal">
                        <h2 class="news-transfer-title-arsenal">
                            <a href="danh-muc.php?url=tin-chuyen-nhuong">Tin chuyển nhượng</a>
                        </h2>
                        <div class="content-news-transfer">
                            {$htmlTransferNews}
                            <div class="d-flex justify-content-center block-mt">
                                <div class="pagination">
                                    <a class="loadMore" href="danh-muc.php?url=tin-chuyen-nhuong">Xem thêm&nbsp;<i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="news-arsenal">
                        <h2 class="news-title-of-arsenal" style="margin-top: -26px;">
                            <a href="danh-muc.php?url=tin-arsenal">Tin Arsenal</a>
                        </h2>
                        <div class="content-news-of-arsenal">
                            {$htmlNewsArsenal}
                            <div class="d-flex justify-content-center block-mt">
                                <div class="pagination">
                                    <a class="loadMore" href="danh-muc.php?url=tin-arsenal">Xem thêm&nbsp;<i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-news-down" style="margin-bottom: 60px; margin-top: -40px;">
                        <h2 class="news-transfer-title-arsenal">
                            <a href="danh-muc.php?url=phan-tich">Góc Phân Tích</a>
                        </h2>
                        <div class="row">
                            {$htmlAnalysisArsenal}
                        </div>
                        <div class="d-flex justify-content-center block-mt">
                            <div class="pagination">
                                <a class="loadMore" href="danh-muc.php?url=phan-tich">Xem thêm&nbsp;<i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Content-->
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

function getOutstandingNews($con, $funcId){
    $newsArray = [];
    $pgParam = [];
    $recCnt = 0;
    $active = 'active';

    $sql = "";
    $sql .= "SELECT id                  ";
    $sql .= "     , title               ";
    $sql .= "     , shortdescription    ";
    $sql .= "     , thumbnail           ";
    $sql .= "  FROM news                ";
    $sql .= " WHERE deldate IS NULL     ";
    $sql .= " ORDER BY id DESC          ";
    $sql .= "     , createdate DESC     ";
    $sql .= " LIMIT 3                   ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        $newsArray = pg_fetch_all($query);
    }

    foreach ($newsArray as $k => $v){
        $thumbnail = checkImage($newsArray[$k]['thumbnail']);
        $titleEncoded = convert_name($newsArray[$k]['title']);
        $urlRedirect = 'tin-tuc.php?key='.$newsArray[$k]['id'].'&'.$titleEncoded.'';
        if ($k !== 0) $active = '';
        $html .= <<< EOF
            <div class="carousel-item {$active}">
                <a href="{$urlRedirect}">
                    <img class="d-block w-100" alt="{$thumbnail}" style="object-fit: cover;" height="700px" h src="{$thumbnail}" data-holder-rendered="true">
                </a>
                <div class="carousel-caption">
                    <a href="{$urlRedirect}" class="title-content-h2">
                        <h2 class="card-title text-carousel limit-text-line" style="font-size: 70px !important;">{$newsArray[$k]['title']}</h2>
                    </a>
                    <p class="card-text content-news mt-3 text-carousel limit-text-line">{$newsArray[$k]['shortdescription']}</p>
                </div>
            </div>
EOF;
    }
    return $html;
}

function getOutstandingNewsNext($con, $funcId){
    $pgParamOne = [];
    $recCntOne = 0;

    $sql = "";
    $sql .= "SELECT id                  ";
    $sql .= "     , title               ";
    $sql .= "     , shortdescription    ";
    $sql .= "     , thumbnail           ";
    $sql .= "     , createby            ";
    $sql .= "  FROM news                ";
    $sql .= " WHERE deldate IS NULL     ";
    $sql .= " ORDER by createdate DESC  ";
    $sql .= " OFFSET 3 ROWS             ";
    $sql .= " LIMIT 1                   ";

    $query = pg_query_params($con, $sql, $pgParamOne);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParamOne, true));
    } else {
        $recCntOne = pg_num_rows($query);
    }

    $htmlOne = '';
    if ($recCntOne != 0){
        while ($row = pg_fetch_assoc($query)){
            $thumbnail = checkImage($row['thumbnail']);
            $titleEncoded = convert_name($row['title']);
            $urlRedirect = 'tin-tuc.php?key='.$row['id'].'&'.$titleEncoded.'';
            $htmlOne .= <<< EOF
                <a href="{$urlRedirect}">
                    <img src="{$thumbnail}" class="card-img-top img-fuild" alt="{$thumbnail}">
                </a>
                <a href="{$urlRedirect}" class="title-content-h2">
                    <h2 class="card-title">{$row['title']}</h2>
                </a>
                <p class="card-text content-news mt-3 limit-text-line">{$row['shortdescription']}</p>
                <div class="post-info">
                    <span>viết bởi <a href="/" style="color: #d61543;">{$row['createby']}</a></span>
                </div>
EOF;

        }
    }

    $pgParamThree = [];
    $recCntThree = 0;

    $sql = "";
    $sql .= "SELECT id                  ";
    $sql .= "     , title               ";
    $sql .= "     , shortdescription    ";
    $sql .= "     , thumbnail           ";
    $sql .= "     , createby            ";
    $sql .= "  FROM news                ";
    $sql .= " WHERE deldate IS NULL     ";
    $sql .= " ORDER BY createdate DESC  ";
    $sql .= " OFFSET 4 ROWS             ";
    $sql .= " LIMIT 3                   ";

    $query = pg_query_params($con, $sql, $pgParamThree);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParamThree, true));
    } else {
        $recCntThree = pg_num_rows($query);
    }

    $htmlThree = '';
    if ($recCntThree != 0){
        while ($row = pg_fetch_assoc($query)){
            $thumbnail = checkImage($row['thumbnail']);
            $titleEncoded = convert_name($row['title']);
            $urlRedirect = 'tin-tuc.php?key='.$row['id'].'&'.$titleEncoded.'';
            $htmlThree .= <<< EOF
                <div class="col-lg-4 col-sm-12 mt-5">
                    <a href="{$urlRedirect}">
                        <img src="{$thumbnail}" class="card-img-top img-fuild object-fit-image" alt="{$thumbnail}">
                    </a>
                    <a href="{$urlRedirect}" class="title-content-h2">
                        <h3 class="card-title title-laste-news limit-text-line">{$row['title']}</h3>
                    </a>
                    <p class="card-text content-news mt-3 limit-text-line">{$row['shortdescription']}</p>
                    <div class="post-info">
                        <span>viết bởi <a href="/" style="color: #d61543;">{$row['createby']}</a></span>
                    </div>
                </div>
EOF;

        }
    }

    return ["htmlOne" => $htmlOne, "htmlThree" => $htmlThree];
}

function getTransferNews($con, $funcId){
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
    $sql .= " AND category = 3          ";
    $sql .= " ORDER BY createdate DESC  ";
    $sql .= " LIMIT 3                   ";

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
                <div class="container content-news-transfer">
                    <div class="row">
                        <a href="{$urlRedirect}" class="col-lg-5">
                            <img src="{$thumbnail}" alt="{$thumbnail}" class="card-img-top img-fuild object-fit-image">
                        </a>
                        <div class="col-lg-7 text-news-transfer mt-3">
                            <a class="header-news-transfer limit-text-line" href="{$urlRedirect}">{$row['title']}</a>
                            <div class="front-view-content mt-3">
                                <span class="limit-text-line">{$row['shortdescription']}</span>
                            </div>
                            <div class="post-info">
                                <span>viết bởi <a href="/" style="color: #d61543;">{$row['createby']}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
EOF;
        }
    }
    return $html;
}

function getNewsArsenal($con, $funcId){
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
    $sql .= " AND category = 2          ";
    $sql .= " ORDER BY createdate DESC  ";
    $sql .= " LIMIT 3                   ";

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
                <div class="container content-news-of-arsenal">
                    <div class="row">
                        <a href="{$urlRedirect}" class="col-lg-5">
                            <img src="{$thumbnail}" alt="{$thumbnail}" class="card-img-top img-fuild object-fit-image">
                        </a>
                        <div class="col-lg-7 text-news-transfer mt-3">
                            <a href="{$urlRedirect}" class="header-news-transfer limit-text-line">{$row['title']}</a>
                            <div class="front-view-content mt-3 limit-text-line">
                                {$row['shortdescription']}
                            </div>
                            <div class="post-info">
                                <span>viết bởi <a href="/" style="color: #d61543;">{$row['createby']}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
EOF;
        }
    }
    return $html;
}

function getAnalysisArsenal($con, $funcId){
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
    $sql .= " AND category = 4          ";
    $sql .= " ORDER BY createdate DESC  ";
    $sql .= " LIMIT 3                   ";

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
                <div class="col-lg-4 col-sm-12 mt-3 mb-5">
                    <a href="{$urlRedirect}">
                        <img src="{$thumbnail}" class="card-img-top img-fuild object-fit-image" alt="{$thumbnail}">
                    </a>
                    <a href="{$urlRedirect}" class="title-content-h2">
                        <h3 class="header-news-transfer card-title title-laste-news">{$row['title']}</h3>
                    </a>
                    <p class="card-text content-news mt-3 limit-text-line">{$row['shortdescription']}</p>
                    <div class="post-info">
                        <span>viết bởi <a href="/" style="color: #d61543;">{$row['createby']}</a></span>
                    </div>
                </div>
EOF;
        }
    }
    return $html;
}
?>