<?php
$scriptHTML = !isset($scriptHTML) ? '' : $scriptHTML;
// Output HTML
print <<< EOF
<script src="plugins/js/jquery-3.4.1.slim.min.js"></script>
<script src="plugins/js/popper.min.js"></script>
<script src="plugins/js/bootstrap.min.js"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v10.0&appId=470633147353941&autoLogAppEvents=1" nonce="OuW9kimi"></script>
<script src="plugins/js/baseScript.js"></script>
{$scriptHTML}
EOF;
?>