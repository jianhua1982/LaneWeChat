<?php
/**
 * This file for getting access token of wechat
 *
 * @file 			/lib/weixin/token/jsapi_ticket.php
 * @author			KavMors
 * @version			2015-2-4
 */

include_once (dirname(__FILE__).'/../../config.php');

class JsapiTicket {
	const RECORD_PATH = '/';
	private $file;
	private $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi';
	private $appid;

	/**
	 * set appid for initializing
	 */
	public function __construct($appid=false) {
		$this->appid = $appid===false? APPID: $appid;
	}

	/**
	 * get the jsapi_ticket or reload it
	 * @param boolean $reload - true to reload the jsapi_ticket
	 */
	public function get($reload=false) {
		$this->file = dirname(__FILE__).self::RECORD_PATH.$this->appid.'_jsapi_ticket.json';
		if ($reload) return $this->reloadTicket();
		if (file_exists($this->file)) {
			$record = json_decode(file_get_contents($this->file), true);
			if ($record['expires_time'] <= time()) {
				return $this->reloadTicket();
			} else return $record['jsapi_ticket'];
		} else {
			return $this->reloadTicket();
		}
			
	}

	private function reloadTicket() {
		include_once (dirname(__FILE__).'/access_token.php');
		include_once (dirname(__FILE__).'/../../util/http_client.php');
		$this->url .= "&access_token=".(new AccessToken())->get();
		$stream = json_decode((new HttpClient($this->url))->get(), true);
		$ticket = $stream['ticket'];
		if ($ticket) {
			$expires_in = $stream['expires_in'];
			$expires_time = intval(time())+intval($expires_in)-60;
			$file_stream = json_encode(array('expires_time'=>$expires_time, 'jsapi_ticket'=>$ticket));
			file_put_contents($this->file, $file_stream);
		}
		return $ticket;
	}
}
?>