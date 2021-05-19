<?php

//Common setting
require_once ('config.php');
require_once ('lib.php');

//Initialization
$funcId       = 'danh-muc';
$message      = '';
$messageClass = '';
$start        = 0;

session_start();

//Connect DB
$con = openDB();

//Get param
$param = getParam();

$htmlCategoryByUrl = '';
$htmlCategoryByUrl = getCategoryByUrl($con, $param, $funcId);

if (empty($param['url'])){
    closeDB();
    header('location: error404.php');
    exit();
} elseif ($param['url'] != $htmlCategoryByUrl['urlkey']){
    closeDB();
    header('location: error404.php');
    exit();
}

$totalRecord = totalRecord($con, $funcId, $param);

$currentPage = isset($param['page']) ? $param['page'] : 1;
$limit = 10;

$totalPage = ceil($totalRecord['total'] / $limit);

if ($currentPage > $totalPage){
    $currentPage = $totalPage;
} else if ($currentPage < 1){
    $currentPage = 1;
}


if ($currentPage > 0){
    $start = ($currentPage - 1) * $limit;
}
$curPrev = $currentPage - 1;
$curNext = $currentPage + 1;

$htmlNewsByCate = '';
$htmlNewsByCate = getNewsByCategory($con, $param, $funcId, $start, $limit);

$htmlPagination = '';
if ($currentPage > 1 && $totalPage > 1){
    $htmlPagination .= <<< EOF
        <a href="danh-muc.php?url={$htmlNewsByCate['urlkey']}&page={$curPrev}"><i class="fas fa-angle-left "></i></a>
EOF;
}

if (($currentPage - 3) > 1){
    $htmlPagination .= <<< EOF
        <a>...</a>
EOF;
}

for ($i = 1; $i <= $totalPage; $i++){
    if ($i == $currentPage){
        $css = 'background-color: #d61543; color: white;';
        $htmlPagination .=<<< EOF
            <a style="{$css}">{$i}</a>
EOF;
    } else{
        $htmlPagination .= <<< EOF
            <a href="danh-muc.php?url={$htmlNewsByCate['urlkey']}&page={$i}">{$i}</a>
EOF;
    }
}

if (($currentPage + 3) < $totalPage){
    $htmlPagination .= <<< EOF
        <a>...</a>
EOF;
}

if ($currentPage < $totalPage && $totalPage > 1){
    $htmlPagination .= <<< EOF
        <a href="danh-muc.php?url=tin-arsenal&page={$curNext}"><i class="fas fa-angle-right "></i></a>
EOF;
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
                            <li class="breadcrumb-item"><a href="http://{$_SERVER['SERVER_NAME']}">Trang chủ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{$htmlCategoryByUrl['category']}</li>
                        </ol>
                    </nav>
                    <div class="mt-5">
                        <div class="news-arsenal">
                            <h2 class="news-title-of-arsenal" style="margin-top: -26px;">
                                Chuyên mục: <a>{$htmlCategoryByUrl['category']}</a>
                            </h2>
                            <div class="content-news-of-arsenal">
                                {$htmlNewsByCate['html']}
                                <div class="pagination-news">
                                    {$htmlPagination}
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

function getCategoryByUrl($con, $param, $funcId){
    $category = [];
    $pgParam = [];
    $pgParam[] = $param['url'];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT category           ";
    $sql .= "     , urlkey             ";
    $sql .= "  FROM category           ";
    $sql .= " WHERE deldate IS NULL    ";
    $sql .= "   AND urlkey = $1        ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else{
        $recCnt = pg_num_rows($query);
    }

    if ($recCnt != 0){
        $category = pg_fetch_assoc($query);
    }
    return $category;
}

function totalRecord($con, $funcId, $param){
    $cnt = [];
    $pgParam = [];
    $pgParam[] = $param['url'];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT COUNT(news.id) AS TOTAL             ";
    $sql .= "  FROM news                                ";
    $sql .= " INNER JOIN category                       ";
    $sql .= "    ON news.category = category.id         ";
    $sql .= " WHERE news.DELDATE IS NULL                ";
    $sql .= "   AND category.deldate IS NULL            ";
    $sql .= "   AND urlkey = $1                         ";
    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else{
        $recCnt = pg_num_rows($query);
    }

    if ($recCnt != 0){
        $cnt = pg_fetch_assoc($query);
    }
    return $cnt;
}

function getNewsByCategory($con, $param, $funcId, $start, $limit){
    $pgParam = [];
    $pgParam[] = $param['url'];
    $pgParam[] = $start;
    $pgParam[] = $limit;
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
    $sql .= " INNER JOIN category                   ";
    $sql .= "    ON news.category = category.id     ";
    $sql .= " WHERE news.deldate IS NULL            ";
    $sql .= "   AND category.deldate IS NULL        ";
    $sql .= "   AND category.urlkey = $1            ";
    $sql .= "   ORDER BY news.createdate DESC       ";
    $sql .= " OFFSET $2                             ";
    $sql .= " LIMIT $3                              ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else{
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        while ($row = pg_fetch_assoc($query)){
            $thumbnail = checkImage($row['thumbnail']);
            $urlKey = $row['urlkey'];
            $titleEncoded = urlencode(str_replace(' ', '-', $row['title']));
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
                                <span>viết bởi <a style="color: #d61543;">{$row['createby']}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
EOF;
        }
    } else {
        $html .= <<< EOF
            <div class="container front-view-content mt-3 pb-5">
                Danh mục này hiện chưa có bài viết
            </div>
EOF;

    }
    return ['html' => $html, 'urlkey' => $urlKey];
}
?>