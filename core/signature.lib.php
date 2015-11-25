<?php
/**
 * Created by PhpStorm.
 * User: cup
 * Date: 15/11/25
 * Time: PM7:20
 */

require('HttpUtil.php');
require_once 'Simple-PHP-Cache/cache.class.php';

/**
 * 微信公众平台 JS_SDK 签名
 *
 * @author xienan
 */

class Signature
{
    /**
     * 生成微信签名需要参数
     */
    private $appId;
    private $appSecret;
    private $mem;
    private $url;
    private $cache;

    public function __construct($appId, $appSecret, $mem, $url) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->mem = $mem;
        $this->url = $url;

        $this->cache = new Cache();
    }


    /**
     * 生成签名
     */
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$this->url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $this->url,
            "signature" => $signature
        );
        return $signPackage;
    }

    /**
     * 获取api_ticket  jsapi_ticket 应该全局存储与更新，读写memcache
     */
    private function getJsApiTicket() {

        $ticketKey = 'wxwallet_jsapi_ticket';

        $data = $this->cache->retrieve($ticketKey);
        if (!$data) {
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode(HttpUtil::sendGetRequet($url));
            if($res){
                $ticket = $res->ticket;
                if($ticket){
                    //$this->mem->set($ticketKey, $ticket, 0, 7000);
                    $this->cache->store($ticketKey, $ticket, 7000);
                }
            }
        }else {
            $ticket = $data;
        }
        return $ticket;
    }

    /**
     * 获取access_token  access_token应该全局存储与更新，读写memcache
     */
    private function getAccessToken() {

        $tokenKey = 'wxwallet_access_token';

        //$data = $this->mem->get($tokenKey);
        $data = $this->cache->retrieve($tokenKey);

        //var_dump($data);

        if (!$data) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode(HttpUtil::sendGetRequet($url));
            if($res){
                $access_token = $res->access_token;
                if($access_token) {
                    //$this->mem->set($tokenKey, $access_token, 0, 7000);
                    $this->cache->store($tokenKey, $access_token, 7000 );
                }
            }
        }else{
            $access_token = $data;
        }
        return $access_token;
    }

    /**
     * 生成随机字符串
     */
    private function createNonceStr($length = 16) {
        $chars = "";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}