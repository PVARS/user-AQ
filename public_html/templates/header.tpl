<?php
require_once ('lib.php');
$funcId = 'header';

$con = openDB();

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$curPageURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$htmlCategory = '';
$htmlCategory = getAllCategory($con, $funcId);

//Output HTML
print <<<EOF
<!--Navbar-->
<header class="container-fuild" style="background-color: #d61543 !important; position: fixed; z-index: 999; width: 100%; box-shadow: 0px 2px 5px -2px rgba(0,0,0,0.75);">
    <nav class="navbar navbar-expand-lg navbar-light bg-light container" style="background-color: #d61543 !important;">
        <a class="navbar-brand" href="http://{$_SERVER['SERVER_NAME']}">
            <img src="plugins/images/logo.png" width="160">
        </a>
        <button class="navbar-toggler outline-none" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="fas fa-bars" style="color: white; font-size: 30px;"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="http://{$_SERVER['SERVER_NAME']}" class="nav-link"><i class="fas fa-home"></i>&nbspTrang Chủ</a>
                </li>
                {$htmlCategory}
            </ul>
        </div>
    </nav>
</header>
EOF;
?>