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
<link rel="shortcut icon" href="favicon.ico"/>
<link rel="stylesheet" href="plugins/css/style.css">
<link rel="stylesheet" href="plugins/css/bootstrap.min.css">
<link rel="stylesheet" href="plugins/css/w3.css">
<link rel="stylesheet" href="plugins/fontawesome/css/all.css">
<link rel="stylesheet" href="plugins/css/style-page-news.css">
<meta name="theme-color" content="#D61543">
{$cssHTML}
EOF;
?>