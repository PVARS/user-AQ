<?php
require_once ('lib.php');
$funcId = 'footer';

$con = openDB();

$htmlCategory = getAllCategoryFoot($con, $funcId);

// Output HTML
print <<< EOF
<a class="top-link hide" href="" id="js-top">
<i class="fas fa-chevron-up"></i>
</a>

<footer class="footer-fuild container-fuild" style="background-color: #172030; position: relative; bottom: 0px; left: 0px; right: 0px;">
<div class="container" style="background-color: #172030;">
    <div class="row container-footer">
        <div class="categories col-md-4 col-sm-12 col-xs-12">
            <h3 style="color: white; font-family: 'SVN-AgencyFBbold'; text-transform: uppercase; border-bottom: 4px solid rgba(255, 255, 255, 0.460);padding-bottom: 18px; margin-bottom: 26px;">Chuyên mục</h3>
            <ul>
                {$htmlCategory}
            </ul>
        </div>
        <div class="contact-footer col-md-4 col-sm-12 col-xs-12">
            <h3 style="color: white; font-family: 'SVN-AgencyFBbold'; text-transform: uppercase; border-bottom: 4px solid rgba(255, 255, 255, 0.460);padding-bottom: 18px; margin-bottom: 26px;">Liên hệ</h3>
            <ul>
                <li class="list-contact" style="border-bottom: 1px solid rgba(255, 255, 255, 0.460);">
                    <font style="font-family: 'SVN-AgencyFBbold'; font-size: 20px;">Email</font><br><a href="mailto:Phanvuars@gmail.com" style="text-transform: none">Phanvuars@gmail.com</a></li>
                <li class="list-contact" style="border-bottom: 1px solid rgba(255, 255, 255, 0.460);">
                    <font style="font-family: 'SVN-AgencyFBbold'; font-size: 20px;">Điện thoại</font><br><a href="tel:0796554628">0796554628</a></li>
            </ul>
        </div>
        <div class="contact-footer col-md-4 col-sm-12 col-xs-12">
            <h3 style="color: white; font-family: 'SVN-AgencyFBbold'; text-transform: uppercase; border-bottom: 4px solid rgba(255, 255, 255, 0.460);padding-bottom: 18px; margin-bottom: 26px;">fanpage arsenal quán</h3>
            <div class="iframe-fanpage-aq">
                <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Farsevn%2F&tabs=timeline&width=380&height=300&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId
                                     " width="100%" height="300" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>
            </div>
        </div>
    </div>
</div>
<div class="copyright-aq">
    <div class="text-copyright container">
        Copyright&copy; Ghi rõ nguồn từ "<a href="https://www.facebook.com/groups/ArsenalQuan/" target="_blank">Arsenal Quán</a>" khi phát hành lại thông tin từ website này.
    </div>
</div>
</footer>
EOF;

function getAllCategoryFoot($con, $funcId){
    $pgParam = [];
    $recCnt = 0;

    $sql = "";
    $sql .= "SELECT id                  ";
    $sql .= "     , category            ";
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
            $html .= <<< EOF
                <li class="list-categories" style="border-bottom: 1px solid rgba(255, 255, 255, 0.460); font-family: 'SVN-AgencyFBbold'; font-size: 20px;"><a href="/transfer-page">{$row['category']}</a></li>
            EOF;
        }
    }
    return $html;
}
?>