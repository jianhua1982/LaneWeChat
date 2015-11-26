<?php
/**
 * Created by PhpStorm.
 * User: cup
 * Date: 15/11/26
 * Time: AM10:07
 */

include '../lanewechat.php';

use LaneWeChat\Core\ApiSignature;


//初始化签名类
if (isset($_SERVER['HTTP_REFERER'])) {
    $url = $_SERVER['HTTP_REFERER'];
}
else {
    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ;
}

//$url = "$_SERVER[HTTP_REFERER]";

// mock
//$url = 'http://www.wygreen.cn/LaneWeChat/createSig.php';

$sig = new ApiSignature(WECHAT_APPID, WECHAT_APPSECRET, $url);

//include 'lanewechat.php';
//\LaneWeChat\Core\ApiSignature  $sig = new \LaneWeChat\Core\ApiSignature(WECHAT_APPID, WECHAT_APPSECRET, );

$ret = json_encode($sig->getSignPackage());
echo $ret;