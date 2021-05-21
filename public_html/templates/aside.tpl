<?php
require_once ('lib.php');
$funcId = 'aside';

$con = openDB();

$param = getParam();

$valueKeyWord = $param['tu-khoa'] ?? '';

$htmlDisperserNews = '';
$htmlDisperserNews = getDisperserNews($con, $funcId);

//Output HTML
print <<< EOF
<aside class="col-md-4 col-sm-12 col-xs-12">
<div class="box-search">
    <h3 style="font-family: 'SVN-AgencyFBbold'; text-transform: uppercase; border-bottom: 4px solid rgba(0, 0, 0, 0.15);padding-bottom: 18px; margin-bottom: 26px; color: #454d59;">Tìm kiếm</h3>
    <form action="tim-kiem.php" method="GET" id="search-form">
        <div class="input-group mb-3">
            <input type="text" class="form-control search-input s" name="tu-khoa" placeholder="Từ khoá" value="{$valueKeyWord}">
            <div class="input-group-append">
                <button type="submit" class="input-group-text search-img"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
</div>
<div class="iframe-facebook">
    <h3 style="text-transform: uppercase; border-bottom: 4px solid rgba(0, 0, 0, 0.15);padding-bottom: 18px; margin-top: 30px; font-family: 'SVN-AgencyFBbold'; color: #454d59;">
        Nhóm Arsenal Quán</h3>
    <div class="table-iframe-facebook">
        <table border="0" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
            <tr>
                <td height="28" style="line-height:28px;">&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;background-color:#ffffff;
                                    border:1px solid #dddfe2;border-radius:3px;font-family:Helvetica, Arial, sans-serif;margin:0px auto;">
                        <tr style="padding-bottom: 8px;">
                            <td>
                                <img class="img" src="plugins/images/bianhomfb.jpg" width="100%" style="height: 4rem" alt="" />
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;font-weight:bold;padding:8px 8px 0px 8px;text-align:center;">
                                Arsenal Quán
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#90949c;font-size:12px;font-weight:normal;text-align:center;">
                                Nhóm Riêng tư · 22.274 thành viên
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:8px 12px 12px 12px;">
                                <table border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse;width:100%;">
                                    <tr>
                                        <td style="background-color:#d61543;border-radius:3px;text-align:center;">
                                            <a style="color:#3b5998;text-decoration:none;cursor:pointer;width:100%;" href="https://www.facebook.com/plugins/group/join/popup/?group_id=192954384415228&amp;source=email_campaign_plugin" target="_blank" rel="noopener">
                                                <table border="0" cellspacing="0" cellpadding="3" align="center" style="border-collapse:collapse;">
                                                    <tr>
                                                        <td style="border-bottom:3px solid #d61543;border-top:3px solid #d61543;color:#FFF;font-family:Helvetica, Arial, sans-serif;font-size:12px;font-weight:bold;">
                                                            Tham gia nhóm
                                                        </td>
                                                    </tr>
                                                </table>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top:1px solid #dddfe2;font-size:12px;padding:8px 12px;">TIÊU CHÍ - Arsenal Quán sẽ là nơi giành cho tất cả các Gooner/Goonerette giao lưu những thông tin về Arsenal nói chung và bóng đá nói riêng. - Nhóm k...
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td height="28" style="line-height:28px;">&nbsp;</td>
            </tr>
        </table>
    </div>
</div>
<div class="outstanding-news">
    <h3 style="text-transform: uppercase; border-bottom: 4px solid rgba(0, 0, 0, 0.15);padding-bottom: 18px; margin-bottom: 26px; font-family: 'SVN-AgencyFBbold'; color: #454d59;">
        tản mạn</h3>
    <div id="carouselOutstanding" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselOutstanding" data-slide-to="0" class="active"></li>
            <li data-target="#carouselOutstanding" data-slide-to="1" class=""></li>
            <li data-target="#carouselOutstanding" data-slide-to="2" class=""></li>
        </ol>
        <div class="carousel-inner">
            {$htmlDisperserNews}
        </div>
        <a class="carousel-control-prev" href="#carouselOutstanding" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselOutstanding" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>

<div class="next-match">
    <h3 style="text-transform: uppercase; border-bottom: 4px solid rgba(0, 0, 0, 0.15);padding-bottom: 18px; margin-bottom: 26px; font-family: 'SVN-AgencyFBbold'; color: #454d59;">
        Trận đấu tiếp theo</h3>
    <div id="fs-upcoming"></div>
    <script>
        (function(w, d, s, o, f, js, fjs) {
            w['fsUpcomingEmbed'] = o;
            w[o] = w[o] || function() {
                (w[o].q = w[o].q || []).push(arguments)
            };
            js = d.createElement(s), fjs = d.getElementsByTagName(s)[0];
            js.id = o;
            js.src = f;
            js.async = 1;
            fjs.parentNode.insertBefore(js, fjs);
        }(window, document, 'script', 'fsUpcoming', 'https://cdn.footystats.org/embeds/upcoming.js'));
        fsUpcoming('params', {
            teamID: 59
        });
    </script>
</div>

<div class="team-widget">
    <h3 style="text-transform: uppercase; border-bottom: 4px solid rgba(0, 0, 0, 0.15);padding-bottom: 18px; margin-bottom: 26px; font-family: 'SVN-AgencyFBbold'; color: #454d59;">
        Thống kê trong mùa</h3>
    <iframe src="https://footystats.org/api/club?id=59" height="100%" width="100%" style="height:420px; width:100%;" frameborder="0"></iframe>
</div>

<div class="league-table">
    <h3 style="text-transform: uppercase; border-bottom: 4px solid rgba(0, 0, 0, 0.15);padding-bottom: 18px; margin-bottom: 26px; font-family: 'SVN-AgencyFBbold'; color: #454d59;">
        Bảng xếp hạng</h3>
    <div id="fs-standings"></div>
    <script>
        (function(w, d, s, o, f, js, fjs) {
            w['fsStandingsEmbed'] = o;
            w[o] = w[o] || function() {
                (w[o].q = w[o].q || []).push(arguments)
            };
            js = d.createElement(s), fjs = d.getElementsByTagName(s)[0];
            js.id = o;
            js.src = f;
            js.async = 1;
            fjs.parentNode.insertBefore(js, fjs);
        }(window, document, 'script', 'mw', 'https://cdn.footystats.org/embeds/standings.js'));
        mw('params', {
            leagueID: 4759
        });
    </script>
</div>
</aside>
EOF;
?>