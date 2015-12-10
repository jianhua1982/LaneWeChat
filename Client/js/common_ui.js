var common_ui={
    moveTag:true,
    /****
     * 功能：loading view
     * 前提条件：html中引入jquery或zepto
     *无入参
     * ****/
    initLoading:function(){
        if(document.getElementById("loadingAll")==null){
            var divIn=' <div class="load-container loadWaiting">'+
                ' <div class="loader" style="margin-top: 70%;"></div>'+
                ' </div>'+
                ' <div id="loadingText" style="">加载中</div>';
			var div = document.createElement("div");
			div.setAttribute("id","loadingAll");
            div.innerHTML =divIn;	
            document.body.appendChild(div);
        }
        document.getElementById("loadingAll").style.display = 'none';
    },
    showLoading:function(){
	    document.getElementById("loadingAll").style.display="block";
    },
    hideLoading:function(){
        document.getElementById("loadingAll").style.display="none";
    },
    /****
     * 功能：waiting view
     * 前提条件：html中引入jquery或zepto
     *无入参
     * ****/
    initWaitingView:function(){
        if($("#loadingHalf").length==0){
            var div=' <div id="loadingHalf">'+
                ' <div class="load-container loadWaiting">'+
                ' <div class="loader"></div>'+
                ' </div>'+
                ' <div id="loadingText" style="">加载中</div>'+
                '  </div>';
            $("body").append(div);
        }
        document.getElementById("loadingHalf").style.display = 'none';
    },
    showWaiting: function () {
        $("#loadingHalf").show();
        // 蒙板，加载页面，不允许用户点击其他页面其他内容。

    },
    hideWaiting:function(){
        $("#loadingHalf").hide();
    },
    /****
     * 功能：弹框初始化
     * 前提条件：html中引入jquery或zepto
     *无入参
     * ****/
    initPopBox:function() {
        if($("#showLog").length==0){
            var div = '<div id="showLog" >' +
                '<div class="showContent">' +
                '<div class="showContTitl">' +
                '<div id="dialogCont"></div>' +
                '</div>' +
                '<div class="showContConf" >' +
                '<div class="confirmContent">确定</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            $("body").append(div);
        }
        document.getElementById("showLog").style.display = 'none';
    },
    /****
     * 功能：show弹框
     * 前提条件：html中引入jquery或zepto
     *入参：
     * @dialogCont：弹框内容
     * @confirmContent：确定部分的名称，例如“我知道了”
     * @callback：点击确定之后回调方法
     * ****/
    showPopBox:function(dialogCont,confirmContent,callback){
        $("#dialogCont").html(dialogCont);
        if(confirmContent==undefined){
            $(".confirmContent").html("确定");
        }else{
            $(".confirmContent").html(confirmContent);
        }
        $(".showContConf").unbind('click').bind('click',function(){
            $("#showLog").hide();
            if(callback){
                callback();
            }
        });
        $("#showLog").show();
    },
    /****
     * 功能：下部弹框
     * 前提条件：html中引入jquery或zepto
     *无入参
     * ****/
    initBottomDialog:function(){
        if($("#bottomDialog").length==0){
            var div='<div id="bottomDialog" >'+
                '<div id="items" >'+
                '</div>'+
                '<div id="cancelBtn" >取消</div>'+
                '</div>';
            $("body").append(div);
        }
        document.getElementById("bottomDialog").style.display = 'none';
        $("#cancelBtn").bind("click",function(){
            $("#bottomDialog").hide();
        })
    },
    /****
     * 功能：下部弹框赋值
     * 前提条件：html中引入jquery或zepto
     *入参：
     * @list：下部弹框可以选择方式列表，例如common_ui.customBottomDialog([{name:"百度0","fun":"alert(0)"},{name:"百度1","fun":"alert(1)"}])
     * ****/
    customBottomDialog:function(list){
        $("#bottomDialog").show();
        for( var i=0;i<list.length;i++){
            var item=list[i];
            var itemId="item"+i;
            var div ='<div class="item"'
                +'id='
                +itemId
				+' onclick='
				+item.fun
                +'>'
                +item.name
				
                +' </div>';
            $("#items").append(div);
        }

    },
    /****
     * 功能：toast初始化
     * 前提条件：html中引入jquery或zepto
     * ****/
    initToastUI:function(){
        if($("#toastShow").length==0){
            var div='<div id="toastShow" >'+
                '<div class="toastCon" >'+
                '<span id="toastText"></span>'+
                '</div>'+
                '</div>';
            $("body").append(div);
        }
        document.getElementById("toastShow").style.display = 'none';
    },
    /****
     * 功能：show toast
     * 前提条件：html中引入jquery或zepto
     * 入参：
     * @txt：toast显示内容
     * ****/
    showToastUI:function(txt){
        $("#toastText").text(txt);
        $("#toastShow").show();
        setTimeout(function(){
            $("#toastText").text("");
            $("#toastShow").hide();
        },2000);
    },
    /****
     * 移动位置
     * parameter:
     * id:移动页面id，必填
     * direc:移动方向left,right,up,down必填
     * dist:移动距离，默认距离为屏幕实际尺寸，可选
     * dura:经过时间，单位毫秒，默认300ms，可选
     * ****/
    transform:function(id,dire,dist,dura)
    {
        if(this.moveTag)
        {
            //当滑动未完成时将该变量设置为false
            this.moveTag = false;
            var horizon,vertical,distance,duration;
            //x,y轴偏移
            var translateX = 0, translateY =0;
            var transZRegex = /\.*translate\((.*)px\)/i;
            var tabletParent = document.getElementById(id);
            //获取x，y方向上的偏移量
            if(tabletParent.style.webkitTransform != "")
            {
                var tempArr = transZRegex.exec(tabletParent.style.webkitTransform)[1];
                tempArr = tempArr.split("px,");
                translateX = parseFloat(tempArr[0]);
                translateY = parseFloat(tempArr[1]);
            }
            //设定时间
            if(dura != "" && dura != undefined)
            {
                duration = dura;
            }
            else
            {
                duration = 500;
            }
            //为移动距离赋值
            if(dist != "" && dist != undefined)
            {
                horizon = dist;
                vertical = dist;
            }
            else
            {
                horizon=document.body.clientWidth;
                vertical=document.body.clientHeight;
            }
            switch(dire)
            {
                case "left":
                    horizon = -horizon;
                    vertical = 0;
                    break;
                case "right":
                    horizon = horizon;
                    vertical = 0;
                    break;
                case "up":
                    horizon = 0;
                    vertical  = -vertical;
                    break;
                case "down":
                    horizon = 0;
                    vertical  = vertical;
                    break;
            }
            //添加偏移量
            horizon = translateX + horizon;
            vertical = translateY + vertical;
            document.getElementById(id).style.webkitTransform = "translate("+horizon+"px,"+vertical+"px)";
            document.getElementById(id).style.webkitTransitionDuration = duration + "ms";

            document.getElementById(id).addEventListener('webkitTransitionEnd', function () {
                this.moveTag = true;
            }.bind(this));
           /* document.getElementById(id).addEventListener("transitionend", function () {
                this.moveTag = true;
            }.bind(this));*/
        }
    },
    /****
     *初始化元素位置
     */
    initDomPosition:function(domList,dire)
    {
       var horizon = document.body.clientWidth;
        for(var x in domList){
            var node=document.getElementById(domList[x]);
            if(dire == "left")
            {
                node.style.left = -horizon + "px";
            }
            else
            {
                node.style.left = horizon + "px";
                console.info(node.style.left);
            }
            node.style.display = "block";
        }
    },
    /****
     * 前提条件：html中引入jquery和date.js文件，并且html中有input标签‘<input id="cardTimer"/>’
     *入参：
     * @inputId：input标签的id
    * ****/
    datePop:function(inputId){
        if($("#datePlugin").length==0){
            var div='<div id="datePlugin"></div>';
            $("body").append(div);
        }
        $("#"+inputId).date();
    },
    initCommonUI:function(){
        this.initLoading();
        this.initPopBox();
        this.initBottomDialog();
        this.initToastUI();
        this.initWaitingView();
    },

    dismiss: function() {
        // dismiss all popups, loadings, waitingView and so forth.
        this.hideLoading();
        this.hideWaiting();
        // hide popup.
    }
};






