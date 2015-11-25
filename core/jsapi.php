<?php
/**
 * This file is to config js_api for wechat
 *
 * @file 			/lib/webpage/jsapi.php
 * @author			KavMors(kavmors@163.com)
 * @version 		2015-3-5
 */

include (dirname(__FILE__).'/../weixin/token/jsapi_ticket.php');

class Jsapi {
	private $apiList;
	private $debug;

	public function __construct($debug=false) {
		$this->debug = $debug? 'true': 'false';
	}

	public function config($apiList) {
		if (is_string($apiList)) $apiList = array($apiList);
		$apiList = json_encode($apiList);
		$signPackage = $this->getSignPackage();
		$rt = "wx.config({
				debug:$this->debug,
				appId:'$signPackage->appId',
				timestamp:$signPackage->timestamp,
				nonceStr:'$signPackage->nonceStr',
				signature:'$signPackage->signature',
				jsApiList:$apiList
			});\n";
		return $rt;
	}

	public function getSignPackage() {
		$jsapi_ticket = new JsapiTicket();
		$jsapiTicket = $jsapi_ticket->get();
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		//rank by ascii
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		$signPackage = array(
			"appId" => APPID,
			"nonceStr" => $nonceStr,
			"timestamp" => $timestamp,
			"url" => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return (Object)$signPackage; 
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
}
?>