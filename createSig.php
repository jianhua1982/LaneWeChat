<?php
/**
 * Created by PhpStorm.
 * User: cup
 * Date: 15/11/26
 * Time: AM10:07
 */

namespace LaneWeChat;

use LaneWeChat\Core\ApiSignature;
use LaneWeChat\Core\Wechat;

/**
 * 微信插件唯一入口文件.
 * @Created by Lane.
 * @Author: lane
 * @Mail lixuan868686@163.com
 * @Date: 14-1-10
 * @Time: 下午4:00
 * @Blog: Http://www.lanecn.com
 */
//引入配置文件
include_once __DIR__.'/config.php';
//引入自动载入函数
include_once __DIR__.'/autoloader.php';
//调用自动载入函数
AutoLoader::register();

//初始化签名类

$url = "$_SERVER[HTTP_REFERER]";

// mock
//$url = 'http://www.wygreen.cn/LaneWeChat/createSig.php';


$sig = new ApiSignature(WECHAT_APPID, WECHAT_APPSECRET, $url);

//include 'lanewechat.php';
//\LaneWeChat\Core\ApiSignature  $sig = new \LaneWeChat\Core\ApiSignature(WECHAT_APPID, WECHAT_APPSECRET, );

$ret = json_encode($sig->getSignPackage());
echo $ret;