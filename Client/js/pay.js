/**
 * Created by cup on 15/12/2.
 */


var PHP_ROOT = 'http://www.wygreen.cn/LaneWeChat/wechat.php';

/**
 * 向php后台发送请求
 * @param path
 * @param data
 * @param success
 * @param fail
 */
function ajaxPhp(appName, action, data, success, fail) {
    "use strict";
    $.ajax({
        type: "GET",
        url: PHP_ROOT + '?appName=' + appName + '&requestAction=' + action,
        dataType: "json",
        data: data,
        success: function (resp) {
            if (success) {
                if(typeof resp === 'string') {
                    resp = JSON.parse(resp);
                }
                success(resp);
            }
        },
        error: function (err) {
            if (fail) {
                fail(err);
            }
            else {
                // fail
                var msg = 'err = ' + JSON.stringify(err);
                console.log(msg);
            }
        }
    });
}

var BACKEND_ROOT = 'http://www.wygreen.cn/LaneWeChat/';
var appName = 'testaccount';
appName = 'tujiayanmei';

/**
 * 向php后台发送请求
 * @param path
 * @param data
 * @param success
 * @param fail
 */
function ajax(path, data, success, fail) {
    "use strict";
    $.ajax({
        type: "GET",
        url: BACKEND_ROOT + path,
        dataType: "json",
        data: data,
        success: function (resp) {
            if (success) {
                success(resp);
            }
        },
        error: function (err) {
            if (fail) {
                fail(err);
            }
            else {
                // fail
                var msg = 'err = ' + JSON.stringify(err);
                console.log(msg);
                alert(msg);
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function(){

    //alert('Got DOMContentLoaded');
    console.log('Got DOMContentLoaded');

    /**
     * bind tap event
     *
     */
    $("input[name='submit']").on('click', function(){
        // 提交 付款
        // wx is ready.
        wx.ready(function () {
            console.log('>> ready');
            //alert('>> ready');

            wx.scanQRCode({
                needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                    //ajax('payBill', );
                }
            });
        });
    });

    /**
     * got js-sdk signature
     *
     */  // Server/createSig.php
    ajaxPhp(appName, 'wxJsSignature', null, function(data){
        // success
        var msg = 'data = ' + JSON.stringify(data);
        console.log(msg);
        //alert(msg);
        //process(data);

        var configParams = data;
        $.extend(configParams, {
            debug: true,
            jsApiList: [
                //'onMenuShareTimeline',
                //'onMenuShareAppMessage',
                'scanQRCode'
            ]
        });

        wx.config(configParams);

        // wx is ready.
        wx.ready(function () {
            console.log('>> ready');
            //alert('>> ready');
        });

        wx.error(function (res) {
            alert(res && res.errMsg);
        });
    });


    return;

    /**
     *  check user login process
     *
     */
    var openIdKey = 'www.qianduoduo.com.openid';
    var openid = localStorage.getItem(openIdKey);
    if(openid && openid.length) {
        // found cached one.
        userLoginProcess(openid);
    }
    else {
        // do auth process
        ajaxPhp('Server/getOpenId.php', null, function(data){
            // success
            openid = data.openid;
            localStorage.setItem(openIdKey, openid);
            userLoginProcess(openid);
        });
    }

    function userLoginProcess(openid) {
        /**
         *  openid --> backend, backend then tell the client whether new user or not (need to register?).
         */

        ajax('user.login', {openid: openid}, function(data){
            // success
            if(data.resp === '00') {
                //
            }

            if(true) {
                // need login, using phone + sms code to verify.
            }
        });
    }

}, false);