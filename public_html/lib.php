<?php
/**
 * Connect db
 * @return false|resource
 */
function openDB(){
    $dsn = array(
//        'host'     => '139.59.120.51',
        'host'     => 'localhost',
        'port'     => '5432',
        'user'     => 'postgres',
//        'dbname'   => 'arsenalquan',
        'dbname'   => 'arsequan',
        'password' => '123456'
    );

    $host = 'host = '.$dsn['host'].' port = '.$dsn['port'].' user = '.$dsn['user'].' dbname = '.$dsn['dbname'].' password = '.$dsn['password'];
    $con = pg_connect($host);

    if(!$con){
        systemError('systemError(lib) Database connection error'.$con);
    }
    return $con;
}

/**
 * Close db
 */
function closeDB(){
    pg_close();
}

/**
 * Error page
 */
function systemErrorPrint(){
    echo <<<EOF
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
    <title>System Error</title>
    </head>
    <body id="systemError">
    <section id="main">
        <article id="login_form" class="module width_half">
            <header><h3>The system is paused</h3></header>
            <div class="module_content">
                <p>We apologize for the inconvenience. <br /> Excuse me, but please wait a little longer.</p>
            </div>
        </article>
    </section>
    
    </body>
    </html>
EOF;
}

/**
 * Notification error
 */
function systemError(){
    closeDB();
    //Print error
    systemErrorPrint();
    exit();
}

/**
 * Eliminate full-width and half-width spaces
 * @param $str
 * @return string
 */
function trimBlank($str){
    $stringValue = $str;
    $stringValue=trim($stringValue);
    
    return $stringValue;
}

/**
 * Get param
 * @return array
 */
function getParam(){
    $param = array();
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $a = $_POST;
    }else{
        $a = $_GET;
    }
    foreach($a as $k => $v) {
        if (is_array($v)) {
            foreach($v as $k2 => $v2) {
                if(get_magic_quotes_gpc()) {
                    $v2 = stripslashes($v2);
                }
                $v2 = htmlspecialchars($v2,ENT_QUOTES);
                $v2 = trimBlank($v2);
                $param[$k][$k2] = $v2;
            }
        }else{
            if(get_magic_quotes_gpc()) {
                $v = stripslashes($v);
            }
            $v = htmlspecialchars($v,ENT_QUOTES);
            $v = trimBlank($v);
            $param[$k] = $v;
        }
    }
    return $param;
}

/**
 * Check image
 * @param $image
 * @return string
 */
function checkImage($image){
    if (empty($image)){
        $thumbnail = 'plugins/images/default-placeholder.png';
    } else {
        $thumbnail = $image;
    }
    return $thumbnail;
}

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
        systemError('systemError(' . $funcId . ') SQL Error???', $sql . print_r($pgParam, true));
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

function getAllCategoryFoot($con, $funcId){
    $pgParam = [];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT id                  ";
    $sql .= "     , urlkey              ";
    $sql .= "     , category            ";
    $sql .= "  FROM category            ";
    $sql .= " WHERE deldate IS NULL     ";
    $sql .= " ORDER BY id ASC           ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error???', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        while ($row = pg_fetch_assoc($query)){
            $html .= <<< EOF
                <li class="list-categories" style="border-bottom: 1px solid rgba(255, 255, 255, 0.460); font-family: 'SVN-AgencyFBbold'; font-size: 20px;"><a href="danh-muc.php?url={$row['urlkey']}">{$row['category']}</a></li>
EOF;
        }
    }
    return $html;
}

function getDisperserNews($con, $funcId){
    $newsArray = [];
    $pgParam = [];
    $recCnt = 0;
    $active = 'active';

    $sql = "";
    $sql .= "SELECT id                  ";
    $sql .= "     , title               ";
    $sql .= "     , thumbnail           ";
    $sql .= "  FROM news                ";
    $sql .= " WHERE deldate IS NULL     ";
    $sql .= " AND category = 4          ";
    $sql .= " ORDER BY createdate DESC  ";
    $sql .= " LIMIT 3                   ";

    $query = pg_query_params($con, $sql, $pgParam);
    if (!$query){
        systemError('systemError(' . $funcId . ') SQL Error???', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        $newsArray = pg_fetch_all($query);
    }

    foreach ($newsArray as $k => $v){
        $titleEncoded = urlencode(str_replace(' ', '-', $newsArray[$k]['title']));
        $urlRedirect = 'tin-tuc.php?key='.$newsArray[$k]['id'].'&'.$titleEncoded.'';
        if ($k !== 0){
            $active = '';
        }
        $html .= <<< EOF
            <div class="carousel-item {$active}">
                <a href="{$urlRedirect}">
                    <img class="d-block w-100 img-fuild" alt="" style="object-fit: cover;" height="400px" h src="{$newsArray[$k]['thumbnail']}" alt="{$newsArray[$k]['thumbnail']}" data-holder-rendered="true">
                </a>
                <div class="carousel-caption-outstanding">
                    <a href="{$urlRedirect}" class="title-content-h2">
                        <h2 class="card-title text-carousel limit-text-line">{$newsArray[$k]['title']}</h2>
                    </a>
                </div>
            </div>
EOF;
    }
    return $html;
}

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
        systemError('systemError(' . $funcId . ') SQL Error???', $sql . print_r($pgParam, true));
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
        systemError('systemError(' . $funcId . ') SQL Error???', $sql . print_r($pgParam, true));
    } else {
        $recCnt = pg_num_rows($query);
    }

    $html = '';
    if ($recCnt != 0){
        $dataCate = pg_fetch_assoc($query);
    }
    return $dataCate;
}

function convert_name($str) {
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'a', $str);
    $str = preg_replace("/(??|??|???|???|???|??|???|???|???|???|???)/", 'e', $str);
    $str = preg_replace("/(??|??|???|???|??)/", 'i', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'o', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???)/", 'u', $str);
    $str = preg_replace("/(???|??|???|???|???)/", 'y', $str);
    $str = preg_replace("/(??)/", 'd', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'A', $str);
    $str = preg_replace("/(??|??|???|???|???|??|???|???|???|???|???)/", 'E', $str);
    $str = preg_replace("/(??|??|???|???|??)/", 'I', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???)/", 'O', $str);
    $str = preg_replace("/(??|??|???|???|??|??|???|???|???|???|???)/", 'U', $str);
    $str = preg_replace("/(???|??|???|???|???)/", 'Y', $str);
    $str = preg_replace("/(??)/", 'D', $str);
    $str = preg_replace("/(\???|\???|\???|\???|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^)/", '-', $str);
    $str = preg_replace("/( )/", '-', $str);
    return $str;
}
?>
