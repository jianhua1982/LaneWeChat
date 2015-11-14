<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/14
 * Time: 18:13
 */

include 'lanewechat.php';

/**
 * 网页授权
 */

/**
 * Description: 通过code换取网页授权access_token
 * 首先请注意，这里通过code换取的网页授权access_token,与基础支持中的access_token不同。
 * 公众号可通过下述接口来获取网页授权access_token。
 * 如果网页授权的作用域为snsapi_base，则本步骤中获取到网页授权access_token的同时，也获取到了openid，snsapi_base式的网页授权流程即到此为止。
 * @param $code getCode()获取的code参数
 */

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    var $ret = \LaneWeChat\Core\WeChatOAuth::getAccessTokenAndOpenId($code);
    var_dump($ret);
}
else  {
    /**
     * Description: 获取CODE
     * @param $scope snsapi_base不弹出授权页面，只能获得OpenId;snsapi_userinfo弹出授权页面，可以获得所有信息
     * 将会跳转到redirect_uri/?code=CODE&state=STATE 通过GET方式获取code和state
     */
    //$redirect_uri = '获取CODE时，发送请求和参数给微信服务器，微信服务器会处理后将跳转到本参数指定的URL页面';
    $redirect_uri = '/gruntInfo.php';
    \LaneWeChat\Core\WeChatOAuth::getCode($redirect_uri, $state=1, $scope='snsapi_base');
}




