<?php
$cssHTML = !isset($cssHTML) ? '' : $cssHTML;

// Output HTML
print <<< EOF
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Arsenal Quán - Trang thông tin điện tử Arsenal</title>
<link rel="stylesheet" href="plugins/css/style.css">
<link rel="stylesheet" href="plugins/css/bootstrap.min.css">
<link rel="stylesheet" href="plugins/css/w3.css">
<link rel="stylesheet" href="plugins/fontawesome/css/all.css">
<link rel="stylesheet" href="plugins/css/style-page-news.css">
{$cssHTML}
EOF;
?>