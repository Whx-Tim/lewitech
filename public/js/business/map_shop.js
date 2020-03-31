/**
 * Created by www on 2016-10-25.
 */
var addShopApplyBtn = function(){

    /*------------添加自定义控件-----------*/
    // 定义一个商家申请控件类
    function ShopApplyBtnControl(){
        // 默认停靠位置和偏移量
        this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
        this.defaultOffset = new BMap.Size(9, 10);
    }

    // 通过JavaScript的prototype属性继承于BMap.Control
    ShopApplyBtnControl.prototype = new BMap.Control();

    // 自定义控件必须实现自己的initialize方法,并且将控件的DOM元素返回
    // 在本方法中创建个div元素作为控件的容器,并将其添加到地图容器中
    ShopApplyBtnControl.prototype.initialize = function(map){
        // 创建OM结构
        var div = document.createElement("div");
        var div1 = document.createElement("div");//btn
        var div2 = document.createElement("div");//close
        var that = this;

        div.appendChild(div1);
        div.appendChild(div2);
        // 设置样式
        div.style.backgroundImage = "url(/images/business/shop_apply_bar.png)";
        div.style.backgroundPosition = "center";
        div.style.backgroundRepeat = "no-repeat";
        div.style.backgroundSize = "100% 100%";
        div.style.height = "113px";
        div.style.cursor = "pointer";
        div.style.outline = "medium";
        div1.style.width = "57px";
        div1.style.height = "57px";
        div1.style.backgroundImage = "url(/images/business/shop_apply_btn.png)";
        div1.style.backgroundPosition = "0 0";
        div1.style.backgroundRepeat = "no-repeat" ;
        div1.style.backgroundSize = "57px 57px";
        div1.style.textAlign = "center";
        div2.style.width = "13px";
        div2.style.height = "13px";
        div2.style.backgroundImage = "url(/images/business/shop_close_btn.png)";
        div2.style.backgroundPosition = "0 0";
        div2.style.backgroundRepeat = "no-repeat" ;
        div2.style.backgroundSize = "100% 100%";
        div2.style.cursor = "pointer";
        div2.style.top = "16px";
        div2.style.right = "15px";
        div2.style.position = "absolute";
        div2.style.display = "block";

        // 控件居中
        this.setCenter = function( ){
            var ctlWidth = document.documentElement.clientWidth - 14;
            var ctlHeight = ctlWidth * 0.307;
            div.style.backgroundSize = ctlWidth+"px "+ctlHeight+"px";
            div.style.width = ctlWidth+"px";
            //div.style.height = ctlHeight+"px";
            div1.style.margin = "66px auto"
            //div1.style.marginRight = ctlWidth/30+"px";
            //div1.style.marginTop = ctlHeight/2-12.5+"px";
            var centerOffset = new BMap.Size(5,6);
            this.setOffset(centerOffset);
        };
        // 控件样式状态切换
        var topBar = document.getElementById("topBar");
        this.setBtnCtlState = function(state){
            if( state == "ready" ){
                map.addControl(markBtnCtrl);
                markBtnCtrl.setCenter();
                topBar.style.display = "none";
                //transform(topBar,{top:'-50px'},'linear',function(){});
            }
            else if( state == "marking" ){
                map.removeControl(markBtnCtrl);
                //transform(topBar,{top:'0'},'linear',function(){});
                topBar.style.display = "block";
            }
            else{
                console.log("the ApplyBtnCtl's state error!!");
            }
        };
        // 绑定事件
        div.onclick = function (e) {
            var geolocation = new BMap.Geolocation();
            geolocation.getCurrentPosition(function(r){
                if(this.getStatus() == BMAP_STATUS_SUCCESS){
                    map.panTo(r.point);
                }
                else {
                    alert('定位城市失败，请检查浏览器权限或是否开启GPS！');
                }
            },{enableHighAccuracy: true});


            that.setBtnCtlState("marking");

            if(headPointOnNow != null){
                headPointOnNow.setOFF();
            }
            headPointOnNow = null;

            var bottomInfWindow = document.getElementById("bottomInfWindow");
            bottomInfWindow.innerHTML = "";
            bottomInfWindow.style.bottom = "-138px";

            // 恢复底部控件
            // 移除底部控件
            map.removeControl(navigationControl);
            map.removeControl(scaleControl);
            map.removeControl(geolocationControl);
            // 设定底部控件位置偏移
            navigationControl =new BMap.NavigationControl();
            scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 5)});
            geolocationControl = new BMap.GeolocationControl();
            // 添加底部控件
            map.addControl(navigationControl);
            map.addControl(scaleControl);
            map.addControl(geolocationControl);

            centerPoint = map.getCenter();
            console.log("Map's center Position is ("+centerPoint.lng+","+centerPoint.lat+")");
            centerIcon.style.display = "block";
            centerIcon.style.left = document.body.clientWidth / 2 - 31 + "px";
            centerIcon.style.top = document.body.clientHeight / 2 - 66 + "px";
            console.log(document.body.clientWidth)
            console.log(document.body.clientHeight)
        };
        div1.onclick = function(e){
            var geolocation = new BMap.Geolocation();
            geolocation.getCurrentPosition(function(r){
                if(this.getStatus() == BMAP_STATUS_SUCCESS){
                    map.panTo(r.point);
                }
                else {
                    alert('定位城市失败，请检查浏览器权限或是否开启GPS！');
                }
            },{enableHighAccuracy: true});


            that.setBtnCtlState("marking");

            if(headPointOnNow != null){
                headPointOnNow.setOFF();
            }
            headPointOnNow = null;

            var bottomInfWindow = document.getElementById("bottomInfWindow");
            bottomInfWindow.innerHTML = "";
            bottomInfWindow.style.bottom = "-138px";

            // 恢复底部控件
            // 移除底部控件
            map.removeControl(navigationControl);
            map.removeControl(scaleControl);
            map.removeControl(geolocationControl);
            // 设定底部控件位置偏移
            navigationControl =new BMap.NavigationControl();
            scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 5)});
            geolocationControl = new BMap.GeolocationControl();
            // 添加底部控件
            map.addControl(navigationControl);
            map.addControl(scaleControl);
            map.addControl(geolocationControl);

            centerPoint = map.getCenter();
            console.log("Map's center Position is ("+centerPoint.lng+","+centerPoint.lat+")");
            centerIcon.style.display = "block";
            centerIcon.style.left = document.body.clientWidth / 2 - 31 + "px";
            centerIcon.style.top = document.body.clientHeight / 2 - 66 + "px";
            console.log(document.body.clientWidth)
            console.log(document.body.clientHeight)
        };
        div2.onclick = function(e){
            window.event? window.event.cancelBubble = true : e.stopPropagation();
            div.style.display = 'none';
        }
        document.getElementById("cancelMarkBtn").onclick = function(e){
            that.setBtnCtlState("ready");
            centerIcon.style.display = "none";
            centerPoint = map.getCenter();

        };
        document.getElementById("confirmMarkBtn").onclick = function(e) {
            centerIcon.style.display = "none";
            centerPoint = map.getCenter();
            console.log(centerPoint);
            window.location.href = "/wechat/business/add?lng="+centerPoint.lng+"&lat="+centerPoint.lat;
        };
        // 添加DOM元素到地图中
        map.getContainer().appendChild(div);
        // 将DOM元素返回
        return div;
    };
    ShopApplyBtnControl.prototype.setCenter = function () {
        that.setCenter();
    };
    // 创建控件
    markBtnCtrl = new ShopApplyBtnControl();
    // 添加到地图当中
    map.addControl(markBtnCtrl);
    markBtnCtrl.setCenter();
};


/*------------定义商家位置头像覆盖物-----------*/
function ShopPosOverlay(point,info){
    this._point = point;
    this._info = info;
    //console.log('ShopPosOverlay:'+info);
}
ShopPosOverlay.prototype = new BMap.Overlay();
ShopPosOverlay.prototype.initialize = function(_map){
    //console.log('initialize:'+this._info.business_poster);
    this._map = _map;
    var div   = this._div = document.createElement("div");
    var head = this._head = document.createElement("div");
    var that = this;
    var tempPoint = this._point;
    var imgUrl = this._info.poster.split(".");
    // imgUrl = "/n/" + imgUrl.join(".100.");//宽度为200的图像路径
    imgUrl = "http://wj.qn.h-hy.com/"+this._info.poster+"?imageMogr/auto-orient/thumbnail/200x";
    //console.log('img'+imgUrl);
    //定义气泡DOM
    div.style.position  = "absolute";
    div.style.zIndex    = BMap.Overlay.getZIndex(this._point.lat);
    div.style.display = "block";
    div.style.backgroundImage = "url(/images/business/mark_point.png)";
    div.style.backgroundRepeat = "no-repeat";
    div.style.backgroundPosition = "0 0";
    div.style.whiteSpace = "nowrap";
    div.style.MozUserSelect = "none";
    //定义头像DOM
    //根据类别显示气泡头像
    var type = that._info.type
    if (type == 0) {
        //餐厅
        head.style.background = "url(/images/business/bigct.png)";
    } else if (type == 1) {
        //typeInfo = '酒店类';
        head.style.background = "url(/images/business/bighotel.png)";
    } else if (type == 2) {
        //typeInfo = '生活出行类';
        head.style.background = "url(/images/business/bigcx.png)";
    } else if (type == 3) {
        //typeInfo = '运动健康类';
        head.style.background = "url(/images/business/bigsports.png)";
    } else {
        return false;
    }

    head.style.position = "absolute";
    head.style.borderRadius = "50%";
    //气泡的大小
    var zoom = map.getZoom();
    if ( zoom >= 14) {
        div.style.width = "72px";//93
        div.style.height = "79px";//116
        div.style.backgroundSize = "145px 79px";//100 247
        head.style.backgroundSize = "100% 100%";
        head.style.width = "51px";
        head.style.height = "50px";
        head.style.top = "9px";
        head.style.left = "11px";
    }else{
        div.style.width = "36px";//93
        div.style.height = "39px";//116
        div.style.backgroundSize = "72px 39px";//100 247
        head.style.backgroundSize = "26px 26px";
        head.style.width = "26px";
        head.style.height = "26px";
        head.style.top = "4px";
        head.style.left = "5px";
    }
    div.appendChild(head);

    var isMove = false;
    //div.addEventListener("click",overlayOnClick);
    div.addEventListener("touchend",function(){
        if (isMove == true) {
            isMove = false;//非点击，而是点住头像拖动
            return
        }else{
            overlayOnClick();
        }
    });
    div.addEventListener("touchmove",function(){
        isMove = true;
    })

    var _this=this;
    function overlayOnClick(e)//单击热点图层
    {
        touchMapTurnOffHead = false;
        //markButtonCtrl.setBtnCtlState("ready");
        centerIcon.style.display = "none";
        centerPoint = map.getCenter();

        if(headPointOnNow != null){
            headPointOnNow.setOFF();
        }
        that.setON();
        headPointOnNow = that;
        markBtnCtrl.setBtnCtlState("ready");

        //markButtonCtrl.setBtnCtlState();
        //map.removeOverlay(centerIcon2);
        //centerIcon.style.display = "none";

        //map.panTo(tempPoint);
        map.removeControl(navigationControl);   // 添加默认缩放平移控件
        map.removeControl(scaleControl);   // 添加比例尺控件控件
        map.removeControl(geolocationControl);  // 添加移动端定位控件


        navigationControl =new BMap.NavigationControl({offset: new BMap.Size(12, 170)});
        scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 170)});
        geolocationControl = new BMap.GeolocationControl({offset: new BMap.Size(12, 170)});

        map.addControl(navigationControl);   // 添加默认缩放平移控件
        map.addControl(scaleControl);   // 添加比例尺控件
        map.addControl(geolocationControl);  // 添加移动端定位控件


        var shopName      = that._info.name;//商家名
        var shopTelephone = that._info.phone;//商家电话
        var shopAddress   = that._info.address;//商家地址
        // var shopType      = that._info.business_type;//商家类别
        var shopType      = showTypeInfo(parseInt(that._info.type));
        var shopAvgSpend  = that._info.price?that._info.price:'';//商家人均消费
        console.log(shopAvgSpend);
        var imgSrc        = "http://wj.qn.h-hy.com/"+that._info.poster+"?imageMogr/auto-orient/thumbnail/200x" ;//that._info.creator.s_wechat_head_img;//商家照片
        var shopScore     = parseFloat(that._info.score);
        var content =
            '<div id="shopInf">' +
            '<div class="head-box"><img src="'+imgSrc+'" alt=""></div>' +
            '<div class="shop-name">'+shopName+'</div>' +
            '<div class="shop-address"><span></span><span>'+shopAddress   +'</span></div>' +
            '<div class="shop-phone"><span></span><a href="tel:'+shopTelephone.replace("-","")+'">'+shopTelephone +'</a></div>' +
            '<div id="shopAttrBtn"></div>' +
            '<div class="shop-attr">'+
            '<div class="shop-type" ><span>类别</span><span>'+(shopType?shopType:'无')       +'</span></div>' +
            '<div class="shop-score"><span>评分</span><div class="star-wrap">'+score2Img(shopScore)+'</div></div>' +
            '<div class="shop-Avg"  ><span>人均</span><span>'+(shopAvgSpend?'￥'+shopAvgSpend:'0')  +'</span></div>' +
            '</div>'+
            '</div>';
        var bottomInfWindow = document.getElementById("bottomInfWindow");
        bottomInfWindow.innerHTML = content;
        bottomInfWindow.style.bottom = "-138px";
        transform(bottomInfWindow,{bottom:'12px'},'linear',function(){
            map.panTo(tempPoint);
            //console.log('success   回调函数');
        });
        function score2Img(score) {
            var star = Math.round(score * 2);
            var starOne = parseInt(score);
            var starHalf = star?(parseInt(score)!==star/2):0;
            var starImg = "";
            for (var i=0;i<5;i=i+1){
                if(i<starOne) starImg = starImg + '<i class="star-one"></i>';
                else if( starHalf == true ) {
                    starImg = starImg + '<i class="star-half"></i>';
                    starHalf = false;
                }
                else starImg = starImg + '<i class="star-none"></i>';
            }
            return starImg;
        }
        document.getElementById("shopAttrBtn").addEventListener("click", function(e) {
            window.location.href="/wechat/business/detail/"+that._info.id;
        });
        document.getElementById("shopInf").addEventListener("click", function(e) {
            window.location.href="/wechat/business/detail/"+that._info.id;
        });//点击整个底部信息区域也会跳转
        setTimeout('touchMapTurnOffHead=true',200);
    }

    map.getPanes().markerPane.appendChild(div);

    return div;
};
ShopPosOverlay.prototype.setON = function () {   // 定义点亮的方法
    this._div.style.backgroundRepeat = "no-repeat";
    this._div.style.zIndex=9999;
    if(map.getZoom()>=14){
        this._div.style.backgroundPosition = "-72px 0px";
    }else{
        this._div.style.backgroundPosition = "-36px 0px";
    }
};
ShopPosOverlay.prototype.setOFF = function () {   // 定义灭掉的方法
    this._div.style.zIndex=-1;
    this._div.style.backgroundRepeat = "no-repeat";
    this._div.style.backgroundPosition = "-3px 100%";
    this._div.style.backgroundPosition = "0px 0px";
};
ShopPosOverlay.prototype.draw = function(){
    var map = this._map;
    var pixel = map.pointToOverlayPixel(this._point);//减去气泡的高度，底部尖对准位置
    if (map.getZoom()>=14) {
        this._div.style.left = pixel.x - 36 + "px";
        this._div.style.top  = pixel.y - 74 + "px";
    }else{
        this._div.style.left = pixel.x - 18 + "px";
        this._div.style.top  = pixel.y - 37 + "px";
    }
};

function showTypeInfo(type) {
    var typeInfo = '';
    if (type == 0) {
        typeInfo = '餐饮娱乐类';
        // $('.shop-address').children('span:first').css("background-image","url(/n/assets/baidu_map/baidu_map_api/icon_restaurant.png)")
    } else if (type == 1) {
        typeInfo = '酒店类';
        // $('.shop-address').children('span:first').css("background-image","url(/n/assets/baidu_map/baidu_map_api/icon_hotel.png)")
    } else if (type == 2) {
        typeInfo = '生活出行类';
        // $('.shop-address').children('span:first').css("background-image","url(/n/assets/baidu_map/baidu_map_api/icon_dailylife.png)")
    } else if (type == 3) {
        typeInfo = '运动健康类';
        // $('.shop-address').children('span:first').css("background-image","url(/n/assets/baidu_map/baidu_map_api/icon_fitness.png)")
    } else {
        return false;
    }

    return typeInfo;
}