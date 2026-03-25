<?php
// Verify Webhook (chỉ dùng khi Meta yêu cầu xác thực lần đầu)
if (isset($_REQUEST['hub_mode']) && $_REQUEST['hub_mode'] == 'subscribe') {
    echo $_REQUEST['hub_challenge'];
    exit;
}

if (getenv('REQUEST_METHOD') == 'OPTIONS')
{
    exit;
}

if (!defined("ROOT_PATH"))
{
    define("ROOT_PATH", dirname(__FILE__) . '/');
}
require ROOT_PATH . 'app/config/options.inc.php';

$http_referer = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']: gethostbyaddr($_SERVER['REMOTE_ADDR']);


$input = json_decode(file_get_contents('php://input'), true);
if ($input) {
    $_REQUEST = array_merge($_REQUEST, $input);
}

$opts = array('http' => array(
    'method'  => 'POST',
    'header'  => 'Content-type: application/x-www-form-urlencoded',
    'follow_location' => 1,
    'content' => http_build_query($_REQUEST + array('pj_http_referer' => $http_referer))
));
$context = stream_context_create($opts);
$res = file_get_contents(PJ_INSTALL_URL."index.php?controller=pjFront&action=pjActionConfirm", false, $context);
exit;
?>