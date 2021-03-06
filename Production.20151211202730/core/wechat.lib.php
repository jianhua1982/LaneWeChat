<?php
namespace LaneWeChat\Core;
/**
 * 微信公众平台来来路认证，处理中心，消息分发
 * Created by Lane.
 * Author: lane
 * Date: 14-03-03
 * Time: 上午10:20
 * Mail: lixuan868686@163.com
 * Website: http://www.lanecn.com
 */
class Wechat{

    /**
     * 调试模式，将错误通过文本消息回复显示
     * @var boolean
     */
    private $debug;

    /**
     * 以数组的形式保存微信服务器每次发来的请求
     * @var array
     */
    private $request;

    /**
     * 初始化，判断此次请求是否为验证请求，并以数组形式保存
     * @param string $token 验证信息
     * @param boolean $debug 调试模式，默认为关闭
     */
    public function __construct($token, $debug = FALSE) {
        //未通过消息真假性验证
       if ($this->isValid() && $this->validateSignature($token)) {
            return $_GET['echostr'];
        }
        //是否打印错误报告
        $this->debug = $debug;

        //接受并解析微信中心POST发送XML数据
        if(isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $xml = (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);

            //将数组键名转换为小写
            $this->request = array_change_key_case($xml, CASE_LOWER);
        }
    }

    /**
     * 判断此次请求是否为验证请求
     * @return boolean
     */
    private function isValid() {
        return isset($_GET['echostr']);
    }

    /**
     * 判断验证请求的签名信息是否正确
     * @param  string $token 验证信息
     * @return boolean
     */
    private function validateSignature($token) {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $signatureArray = array($token, $timestamp, $nonce);
        sort($signatureArray, SORT_STRING);
        return sha1(implode($signatureArray)) == $signature;
    }

    /**
     * 获取本次请求中的参数，不区分大小
     * @param  string $param 参数名，默认为无参
     * @return mixed
     */
    protected function getRequest($param = FALSE) {
        if ($param === FALSE) {
            return $this->request;
        }
        $param = strtolower($param);
        if (isset($this->request[$param])) {
            return $this->request[$param];
        }
        return NULL;
    }

    /**
     * 分析消息类型，并分发给对应的函数
     * @return void
     */
    public function run() {

        $action = '';
        if (isset($_GET["requestAction"])) {
            $action = $_GET["requestAction"];
        }
        elseif (isset($_POST["requestAction"])) {
            $action = $_POST["requestAction"];
        }

        if(strlen($action)) {
            return $this->requestAction($action);
        }

        return WechatRequest::switchType($this->request);
    }

    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = WECHAT_TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            echo $_GET['echostr'];
            return true;
        }else{
            return false;
        }
    }

    // customize action.

    private function requestAction($action){
        switch($action) {
            case 'wxJsSignature': {
                // got wx js signature
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $url = $_SERVER['HTTP_REFERER'];
                }
                else {
                    $url = self::_phpFileFullUrl();
                }

                //var_dump($url);

                $sig = new ApiSignature(WECHAT_APPID, WECHAT_APPSECRET, $url);
                $ret = json_encode($sig->getSignPackage());
                echo $ret;
            }
                break;

            case 'fetchCode': {
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $redirectUrl = $_SERVER['HTTP_REFERER'];
                }
                else {
                    $redirectUrl = $_SERVER['REQUEST_URI'];
                }

                WeChatOAuth::getCode($redirectUrl, $state='qianduoduo', $scope='snsapi_base');
            }
                break;

            case 'fetchOpenid': {
                if (!isset($_GET['code'])) {
                    echo json_encode(['msg'=>'no code here !!!']);
                    return;
                }

                $code = $_GET['code'];
                $ret = WeChatOAuth::getAccessTokenAndOpenId($code);
                echo json_encode($ret);
            }
                break;

            case 'scanCodePay': {

                //$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_APPSECRET;

//                var params = {
//                    id: $('#channel').val() + '01',  // '01' means pay
//                                amount: $('#money').val(),
//                                openId: openid,
//                                authCode: result
//                            };

                //var_dump($_GET);

                $params = ['id'=>$_GET['id'], 'amount'=>$_GET['amount'], 'authCode'=>$_GET['authCode'], 'openId'=>$_GET['openId']];

                //$params = [];
                //parse_str()

                //var_dump($params);

                $ret = Curl::callWebServer(WECHAT_URL . 'zero/scanweb', json_encode($params), 'POST');

                //var_dump($ret);

                // check receipt openid here.


                /**
                 * Notify JS frontend.
                 */
                echo json_encode($ret);

                /**
                 * Notify the receipt.
                 */
                $retParams = $ret['params'];
                if (is_string($retParams)) {
                    $retParams = json_decode($retParams, true);
                }

                $debugMode = (isset($_GET['debugMode']) && $_GET['debugMode'] === '1');
                if($debugMode) {
                    // mock
                    $ret['code'] = '00';
                    $retParams['total_amount'] = '0.01';
                }

                if(($ret['code'] === '00') && $retParams && $retParams['total_amount'] && !is_null($retParams['total_amount'])) {
                    $content = '收到' . '付款金额' . $retParams['total_amount'];
                    ResponseInitiative::text($params['openId'], $content);
                }
            }
                break;

//            case 'notifyPayResult': {
//                //if(isset($_POST()))
//                if (!isset($_GET['code'])) {
//                    echo json_encode(['msg'=>'no code here !!!']);
//                    return;
//                }
//
//                $code = $_GET['code'];
//                $ret = WeChatOAuth::getAccessTokenAndOpenId($code);
//                echo json_encode($ret);
//            }
                break;
            default:{
                //return ResponsePassive::text($request['fromusername'], $request['tousername'], '收到未知的消息，我不知道怎么处理');
            }
        }
    }

    private static function _phpFileFullUrl() {
        return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}



