<?php
/**
 * Created by PhpStorm.
 * User: cup
 * Date: 15/11/26
 * Time: PM7:10
 */


/**
 * 网页授权
 */

include 'lanewechat.php';

/**
 * Description: 通过code换取网页授权access_token
 * 首先请注意，这里通过code换取的网页授权access_token,与基础支持中的access_token不同。
 * 公众号可通过下述接口来获取网页授权access_token。
 * 如果网页授权的作用域为snsapi_base，则本步骤中获取到网页授权access_token的同时，也获取到了openid，snsapi_base式的网页授权流程即到此为止。
 * @param $code getCode()获取的code参数
 */

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $ret = \LaneWeChat\Core\WeChatOAuth::getAccessTokenAndOpenId($code);
    //$ret = json_encode($ret);
    var_dump($ret);
}
else  {
    /**
     * Description: 获取CODE
     * @param $scope snsapi_base不弹出授权页面，只能获得OpenId;snsapi_userinfo弹出授权页面，可以获得所有信息
     * 将会跳转到redirect_uri/?code=CODE&state=STATE 通过GET方式获取code和state
     */
    //$redirect_uri = '获取CODE时，发送请求和参数给微信服务器，微信服务器会处理后将跳转到本参数指定的URL页面';
    $redirect_uri = '/LaneWeChat//gruntInfo.php';
    \LaneWeChat\Core\WeChatOAuth::getCode($redirect_uri, $state='qianduoduo', $scope='snsapi_userinfo');
}


// 用户授权通过
//if (isset($_GET['code'])) {
//    $code = $_GET['code'];
//
//    //echo "Got code ".$code.'\br';
//
//    $result = CommonUtil::getUserOpenId($code);
//
//    //echo '$result = '.json_encode($result).'<br>';
//
//    if($result['access_token'] && $result['openid']) {
//        $ret = CommonUtil::getUserInfo($result['access_token'], $result['openid']);
//        echo json_encode($ret);
//    }
//    else {
//        echo 'access_token not found!!!';
//    }
//}
//
//else {
//    echo 'code not found!!!';
//}