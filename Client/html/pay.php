<?php
/**
 * Created by PhpStorm.
 * User: cup
 * Date: 15/12/2
 * Time: PM7:00
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

    <title>Pay Demo</title>
    <style>

    section{
        margin: 10px;
            padding: 10px;
            -webkit-box-sizing:border-box;
    }

    select{
        margin: 30px;
        padding: 30px;
        -webkit-box-sizing:border-box;
    }

    </style>
</head>

<body>

<section>

    <p> 请选择付款类型 </p>

    <select>
        <option value="01">支付宝</option>
        <option value="02">微信支付</option>
        <option value="03">银联钱包</option>
        <option value="04">百度钱包</option>
    </select>

    <br>

    <label>
        <input type="text" name="money" value="188.88" placeholder="付款金额">
        <!--<input type="submit" name="submit">-->
        <button id="submit"> 提交 </button>
    </label>

    <!--<lable>   </lable>-->


</section>


<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="../js/zepto.min.js"></script>
<script src="../js/pay.js"></script>

<script>


</script>

</body>
</html>