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

$valueKeyWord = $param['tu-khoa'] ?? '';

$htmlNewsByKeyWord = '';
$htmlNewsByKeyWord = getNewsByKeyWord($con, $funcId, $valueKeyWord);

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
                            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
                        </ol>
                    </nav>
                    <div class="mt-5">
                        <div class="news-arsenal">
                            <h2 class="news-title-of-arsenal" style="margin-top: -26px;">
                                Kết quả tìm kiếm với: <a>"{$valueKeyWord}"</a>
                            </h2>
                            <div class="content-news-of-arsenal">
                                {$htmlNewsByKeyWord}
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

function getNewsByKeyWord($con, $funcId, $valueKeyWord){
    $pgParam = [];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT*FROM news                                  ";
    $sql .= " WHERE deldate IS NULL                            ";
    $sql .= "   AND content ILIKE '%".$valueKeyWord."%'        ";
    $sql .= " ORDER BY createdate DESC                         ";

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
                Không có dữ liệu tìm kiếm...
            </div>
EOF;

    }
    return $html;
}
?>