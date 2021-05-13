<?php

//Common setting
require_once ('config.php');

//Initialization
$funcId       = 'error404';

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
    <!--Content-->
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h3>Lỗi! Trang này không tồn tại</h3>
                <h1><span>4</span><span>0</span><span>4</span></h1>
            </div>
            <h2>Arsenal Quán xin lỗi! Trang bạn yêu cầu không tồn tại</h2>
            <a class="back-to-home" href="#">Trở lại trang chủ <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
    <!--Content-->
EOF;

//Footer
include($TEMP_APP_FOOTER_PATH);

//Meta JS
include ($TEMP_APP_METAJS_PATH);
echo <<<EOF
</body>
</html>
EOF;
?>