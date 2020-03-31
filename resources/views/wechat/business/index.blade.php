<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>校企地图</title>
    <link rel="stylesheet" href="{{ asset('css/business/schoolmate.map.css') }}">

    <style type="text/css">
        #centerIcon{
            width: 62px;
            height:91px;
            display: none;
            position :fixed;
            z-index: 9999;
            background-image: url("{{ asset('images/business/marking_shop.png') }}");
            background-position: 0 0;
            background-repeat: no-repeat;
            background-size:100% 100%;
        }
    </style>

</head>
<body>
<div id="l-map"></div>
<div id="topBar">
    <div id="confirmMarkBtn" class="btn">确定</div>
    <div id="cancelMarkBtn" class="btn">取消</div>
</div>
<div id="bottomInfWindow">

</div>
<div id="centerIcon"></div>
<script src="//cdn.bootcss.com/zepto/1.2.0/zepto.min.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/getscript?v=2.0&ak=GG3uqKUdcs1x7vlYVnbnGIBiuMMZBLIe&services=&t=20160928173929"></script>
<!-- 加载海量点列表 -->
<script type="text/javascript" src="{{ asset('js/business/points-sample-data.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/business/map_shop.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/business/map_star.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/business/map_ajax.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/business/transform.js') }}"></script>
<script type="text/javascript">
    // 百度地图API功能
    var map = new BMap.Map("l-map");          // 创建地图实例
    var point = new BMap.Point(114.02597366,22.54605355);  // 创建点坐标
    map.centerAndZoom(point, 13);                 // 初始化地图，设置中心点坐标和地图级别
    map.enableScrollWheelZoom();
    map.enableInertialDragging();
    // var self_user_id=getAuthUserId();
    var headPointOnNow;   // 记录点亮头像
    var markBtnCtrl;   // 全局标记控件
    var touchMapTurnOffHead = false;

    //初始定位
    var geolocation = new BMap.Geolocation();
    geolocation.getCurrentPosition(function(r){
        if(this.getStatus() == BMAP_STATUS_SUCCESS){
            map.panTo(r.point);
        }
        else {
            alert('定位城市失败，请检查浏览器权限或是否开启GPS！');
        }
    },{enableHighAccuracy: true});
    //关于状态码
    //BMAP_STATUS_SUCCESS   检索成功。对应数值“0”。
    //BMAP_STATUS_CITY_LIST 城市列表。对应数值“1”。
    //BMAP_STATUS_UNKNOWN_LOCATION  位置结果未知。对应数值“2”。
    //BMAP_STATUS_UNKNOWN_ROUTE 导航结果未知。对应数值“3”。
    //BMAP_STATUS_INVALID_KEY   非法密钥。对应数值“4”。
    //BMAP_STATUS_INVALID_REQUEST   非法请求。对应数值“5”。
    //BMAP_STATUS_PERMISSION_DENIED 没有权限。对应数值“6”。(自 1.1 新增)
    //BMAP_STATUS_SERVICE_UNAVAILABLE   服务不可用。对应数值“7”。(自 1.1 新增)
    //BMAP_STATUS_TIMEOUT   超时。对应数值“8”。(自 1.1 新增)

    //标注相关代码
    var centerPoint = map.getCenter();
    var centerIcon = document.getElementById("centerIcon");
    var cpIcon = new BMap.Icon("{{ asset('images/business/marking_shop.png') }}", new BMap.Size(30, 50), {
        anchor: new BMap.Size(15, 46), // 指定定位位置
        imageOffset: new BMap.Size(0, 0), // 设置图片偏移
        imageSize: new BMap.Size(30, 50)  // 设置图片大小
    });
    var centerIcon2 = new BMap.Marker(centerPoint,{icon:cpIcon});
    centerIcon2.setTop(true,27000000);

    //开启默认控件
    var navigationControl =new BMap.NavigationControl();
    var scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 5)});
    var geolocationControl = new BMap.GeolocationControl();
    map.addControl(navigationControl);   // 添加默认缩放平移控件
    map.addControl(scaleControl);   // 添加比例尺控件
    map.addControl(geolocationControl);  // 添加移动端定位控件
    mapType="Business";//定义一个全局变量，用于map_ajax.js区分活动页面与校企页面
    getUserInfo();
    // addMakeBtn();
    // addChatBtn();
    addShopApplyBtn();


    var overlays = new Array();
    function checkOverlayExist(info){
        for (var i in overlays) {
            if (info==overlays[i])
                return true;
        }
        return false;
    }
    addShopPosOverlays();
    function addShopPosOverlays(){
        console.log("addShopPosOverlays!!");
        var bs = map.getBounds();   //获取可视区域
        var bssw = bs.getSouthWest();   //可视区域左下角
        var bsne = bs.getNorthEast();   //可视区域右上角
        $.ajax({
            type: 'get',
            dataType:'json',
            cache:false,
            data:"sw_lng="+bssw.lng+"&sw_lat="+bssw.lat+"&ne_lng="+bsne.lng+"&ne_lat="+bsne.lat,
            url: "{{ route('wechat.business.get.index') }}",
            success: function(data){
                var zoom = map.getZoom();
                if (zoom<=12) {
                    console.log('777');
                    console.log(data.data);
                    for (var i in data.data) {
                        data.data[i].lng=data.data[i].lng;
                        data.data[i].lat=data.data[i].lat;
                    }
                    s = new Stars();
                    s.setRs(data.data);
                    return;
                }
                for (var i in data.data) {
                    if (!checkOverlayExist(data.data[i].id)){
                        //console.log("add");
                        //console.log(data.data[i]);
                        var shopOverlay = new ShopPosOverlay(new BMap.Point(data.data[i].lng,data.data[i].lat),data.data[i]);
                        map.addOverlay(shopOverlay);
                        shopOverlay.setOFF();
                    }
                }
            },
            error : function() {
            }
        });
    }
    var last_zoom=13;
    map.addEventListener("zoomend",function(e){
        var zoom=e.target.getZoom();
        e.target.clearOverlays();
        addShopPosOverlays();
        last_zoom=zoom;
        touchMapTurnOffHead = false;
    });
    map.addEventListener("touchend",turnOffHeadMapEvent);
    map.addEventListener("click",turnOffHeadMapEvent);

    window.onload = function(){
        //防止重复显示头像的bug
        var geo = document.getElementsByClassName('BMap_geolocationIcon')[1];
        geo.onclick = function(){
            console.log('geoClear');
            map.clearOverlays();
            overlays = new Array();
        }
    }

</script>
</body>
</html>