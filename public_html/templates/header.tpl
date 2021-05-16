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
        <a class="navbar-brand" href="{$curPageURL}">
            <img src="plugins/images/logo.png" width="160">
        </a>
        <button class="navbar-toggler outline-none" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="fas fa-bars" style="color: white; font-size: 30px;"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{$curPageURL}" class="nav-link"><i class="fas fa-home"></i>&nbspTrang Chủ</a>
                </li>
                {$htmlCategory}
            </ul>
        </div>

    </nav>
</header>
EOF;

function getAllCategory($con, $funcId){
    $pgParam = [];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT id                  ";
    $sql .= "     , icon                ";
    $sql .= "     , category            ";
    $sql .= "     , urlkey              ";
    $sql .= "  FROM category            ";
    $sql .= " WHERE deldate IS NULL     ";
    $sql .= " ORDER BY id ASC           ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error：', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        while ($row = pg_fetch_assoc($query)){
            $icon = html_entity_decode($row['icon']);

            $html .= <<< EOF
            <li class="nav-item">
                <a href="danh-muc.php?url={$row['urlkey']}" class="nav-link">{$icon}&nbsp{$row['category']}</a>
            </li>
            EOF;
        }
    }
    return $html;
}
?>