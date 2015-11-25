<?php
namespace LaneWeChat\Core;
/**
 * 微信Access_Token的获取与过期检查
 * Created by jianhua.
 * Mail: jianhua_iphone@126.com
 * Website: http://www.cnblogs.com/best-html5-js/
 */

class ApiTicket{

    /**
     * 获取微信Access_Token
     */
    public static function getApiTicket(){
        //检测本地是否已经拥有api_ticket，并且检测api_ticket是否过期
        $apiTicket = self::_checkApiTicket();
        if($apiTicket === false){
            $apiTicket = self::_getApiTicket();
        }
        return $apiTicket['api_ticket'];
    }

    /**
     * @descrpition 从微信服务器获取微信ACCESS_TOKEN
     * @return Ambigous|bool
     */
    private static function _getApiTicket(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_APPSECRET;

//        $accessToken = $this->getAccessToken();
//        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
//        $res = json_decode(HttpUtil::sendGetRequet($url));
//        if($res){
//            $ticket = $res->ticket;
//            if($ticket){
//                //$this->mem->set($ticketKey, $ticket, 0, 7000);
//                $this->cache->store($ticketKey, $ticket, 7000);
//            }
//        }



        $apiTicket = Curl::callWebServer($url, '', 'GET');
        if(!isset($apiTicket['api_ticket'])){
            return Msg::returnErrMsg(MsgConstant::ERROR_GET_ACCESS_TOKEN, '获取ACCESS_TOKEN失败');
        }
        $apiTicket['time'] = time();
        $apiTicketJson = json_encode($apiTicket);
        //存入数据库
        /**
         * 这里通常我会把api_ticket存起来，然后用的时候读取，判断是否过期，如果过期就重新调用此方法获取，存取操作请自行完成
         *
         * 请将变量$apiTicketJson给存起来，这个变量是一个字符串
         */
        $f = fopen('api_ticket', 'w+');
        fwrite($f, $apiTicketJson);
        fclose($f);
        return $apiTicket;
    }

    /**
     * @descrpition 检测微信ACCESS_TOKEN是否过期
     *              -10是预留的网络延迟时间
     * @return bool
     */
    private static function _checkApiTicket(){
        //获取api_ticket。是上面的获取方法获取到后存起来的。
//        $apiTicket = YourDatabase::get('api_ticket');
        $data = file_get_contents('api_ticket');
        $apiTicket['value'] = $data;
        if(!empty($apiTicket['value'])){
            $apiTicket = json_decode($apiTicket['value'], true);
            if(time() - $apiTicket['time'] < $apiTicket['expires_in']-10){
                return $apiTicket;
            }
        }
        return false;
    }
}
?>