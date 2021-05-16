<?php
$cssHTML = !isset($cssHTML) ? '' : $cssHTML;

require_once ('lib.php');
$funcId = 'header';

$con = openDB();

$param = getParam();

if(!empty($param['key'])){
    $newsMeta = getNewsMeta($con, $funcId, $param);
}

if(!empty($param['url'])){
    $cateMeta = getCategoryMeta($con, $funcId, $param);
}

$valueTitle = $newsMeta['title'] ?? $cateMeta['category'] ?? 'Arsenal Quán - Trang thông tin điện tử Arsenal';
$valueThumbnail = $newsMeta['thumbnail'] ?? 'https://i.imgur.com/AfbGYci.jpg';
$valueDescription = $newsMeta['shortdescription'] ?? 'Cập nhật tin tức nhanh chóng, tin cậy và kết nối cộng đồng Gooner';

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$curPageURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Output HTML
print <<< EOF
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="{$curPageURL}"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="{$valueTitle}" />
<meta property="og:description" content="{$valueDescription}" />
<meta property="og:image" content="{$valueThumbnail}" />
<title>Arsenal Quán - Trang thông tin điện tử Arsenal</title>
<link rel="stylesheet" href="plugins/css/style.css">
<link rel="stylesheet" href="plugins/css/bootstrap.min.css">
<link rel="stylesheet" href="plugins/css/w3.css">
<link rel="stylesheet" href="plugins/fontawesome/css/all.css">
<link rel="stylesheet" href="plugins/css/style-page-news.css">
{$cssHTML}
EOF;

function getNewsMeta($con, $funcId, $param){
    $dataNews = [];
    $pgParam = [];
    $pgParam[] = $param['key'];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT news.id                         ";
    $sql .= "     , news.title                      ";
    $sql .= "     , news.shortdescription           ";
    $sql .= "     , news.thumbnail                  ";
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
    }
    return ['thumbnail'  => $dataNews['thumbnail'],
            'title'      => $dataNews['title'],
            'shortdescription' => $dataNews['shortdescription']
    ];
}

function getCategoryMeta($con, $funcId, $param){
    $dataCate = [];
    $pgParam = [];
    $pgParam[] = $param['url'];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT id                              ";
    $sql .= "     , category                        ";
    $sql .= "  FROM category                        ";
    $sql .= " WHERE deldate IS NULL                 ";
    $sql .= "   AND urlkey = $1                     ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        $dataCate = pg_fetch_assoc($query);
    }
    return $dataCate;
}
?>