var userInfo;


function setCookie(c_name,value,expiredays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+expiredays);
    document.cookie=c_name+ "=" +encodeURIComponent(value)+
        ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}
function getCookie(c_name)
{
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=");
        if (c_start!=-1)
        {
            c_start=c_start + c_name.length+1;
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return decodeURIComponent(document.cookie.substring(c_start,c_end))
        }
    }
    return ""
}

function checkLogined()
{
    if (!userInfo){
        window.location.href="http://weijuan.szu.edu.cn/n/user/loginBySession?refer=/n/map/";
        return false;
    }
    return true;
}

function checkUnRead()
{
    $.ajax({
        type: 'get',
        dataType:'json',
        cache:false,
        url: "/n/api/v1/index/map/message/isHaveUnread",
        data:"",
        success: function(data){
            if (data.errcode){
            }else{
                if (data.data){
                    chatBtnCtrl.setReadState( true );
                }else{

                }
            }
        },
        error : function() {
            // alert("消息发送失败");
        }
    });
}
function getUserInfo()
{
    $.ajax({
        type: 'get',
        dataType:'json',
        cache:false,
        url: "/n/api/v1/index/user/getInfo",
        success: function(data){
            console.log(data);
            if (!data.errcode){
                userInfo=data.data.user;
                if ( mapType == "Schoolmate") {
                    checkUnRead();
                }
                getMyself();

            }else{
                if (data.errcode==1){
                    // window.location.href="http://weijuan.szu.edu.cn/n/user/loginBySession?refer=/n/map/";
                }else{
                    alert(data.errmsg);
                }
            }
        },
        error : function() {
        }
    });
}
var self_comment="";
function getMyself()
{
    $.ajax({
        type: 'get',
        dataType:'json',
        cache:false,
        url: "/n/api/v1/index/map/myself",
        success: function(data){
            if (!data.errcode){
                self_comment=data.data.comment;
            }else{
            }
        },
        error : function() {
        }
    });
}

function turnOffHeadMapEvent(e) {
    if (headPointOnNow != null && touchMapTurnOffHead) {
        headPointOnNow.setOFF();
        headPointOnNow._div.style.zIndex=-1;
        headPointOnNow = null;
        var bottomInfWindow = document.getElementById("bottomInfWindow");
        bottomInfWindow.innerHTML = "";
        bottomInfWindow.style.bottom = "-138px";
        // 移除底部控件
        map.removeControl(navigationControl);
        map.removeControl(scaleControl);
        map.removeControl(geolocationControl);
        // 设定底部控件位置偏移
        navigationControl = new BMap.NavigationControl();
        scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 5)});
        geolocationControl = new BMap.GeolocationControl();
        // 添加底部控件
        map.addControl(navigationControl);
        map.addControl(scaleControl);
        map.addControl(geolocationControl);
    }
}

function Point() {}

Point.prototype.newPoint = function(lng,lat,comment,callback){
    if (!callback) callback=function(errcode,errmsg,data){};
    $.ajax({
        type: 'post',
        dataType:'json',
        cache:false,
        data:"lng="+lng+"&lat="+lat+"&comment="+comment,
        url: "/n/api/v1/index/mapPoint",
        success: function(data){
            callback(data);
        },
        error : function() {
            callback(-1,"请求失败",[]);
        }
    });
};

Point.prototype.showPoints = function(){
    var _this=this;
    $.ajax({
        type: 'get',
        dataType:'json',
        cache:false,
        data:"",
        url: "/n/api/v1/index/mapPoint",
        success: function(data){
            _this.AddManyPoint(data.data);
        },
        error : function() {
        }
    });
};
var newoverlays = new Array();
Point.prototype.checkOverlayExist = function(info){
    for (var i in newoverlays) {
        if (info==newoverlays[i].id)
            return true;
    }
    return false;
};
var isAdOverlaysvaild=true;
Point.prototype.addOverlays = function(){
    var _this=this;
    var bs = map.getBounds();   //获取可视区域
    var bssw = bs.getSouthWest();   //可视区域左下角
    var bsne = bs.getNorthEast();   //可视区域右上角
    //isAdOverlaysvaild=true;
    $.ajax({
        type: 'get',
        dataType:'json',
        cache:false,
        data:"sw_lng="+bssw.lng+"&sw_lat="+bssw.lat+"&ne_lng="+bsne.lng+"&ne_lat="+bsne.lat,
        url: "/n/api/v1/index/mapPoint",
        success: function(data){

            // if (!isAdOverlaysvaild)
            //     return;

            var start = new Date().getTime();//起始时间

            //计算当前视域内已有点数
            //bssw.lng左上x;bssw.lat左上y;bsne.lng右下x；bsne.lat右下y
            var existPoints=0;
            var allCounts = 30;//允许存在的点数
            // console.log(bssw.lng);
            // console.log(bsne.lng);
            // console.log(bssw.lat);
            // console.log(bsne.lat);

            for (var i in newoverlays){
                if (newoverlays[i].lat > bssw.lat && newoverlays[i].lat < bsne.lat && newoverlays[i].lng > bssw.lng && newoverlays[i].lng < bsne.lng) {
                    existPoints++;
                }
            }
            console.log('视域内原有'+existPoints+'个已存点');
            //计算结束
            var arr = data.data
            // if (isRam) {
            //     //用户选择换一批，随机打乱顺序重新加载
            //     arr.sort(function(){ return 0.5 - Math.random(); });
            //     isRam = false;
            // }

            for (var i in arr) {
                if (!_this.checkOverlayExist(arr[i].id)){
                    _this.addOverlay(arr[i]);
                    existPoints ++;
                }
                if (newoverlays.length > allCounts ) {
                    if (newoverlays.length - existPoints >0) {
                        //视域外有点则顺序删除已让出资源
                        map.removeOverlay(newoverlays[0].obj);
                        newoverlays.shift();
                    }else{
                        //视域外无点则不再渲染新点
                        console.log('已达最大数量'+allCounts);
                        break;
                    }

                }


            }
            var end = new Date().getTime();//接受时间
            console.log('新头像渲染消耗时间：'+(end - start)+"ms");
        },
        error : function() {
        }
    });
};

var addChatBtn = function(){
    // 定义一个对话控件类
    function ChatBtnControl(){
        // 默认停靠位置和偏移量
        this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
        this.defaultOffset = new BMap.Size(12, 64);
    }

    // 通过JavaScript的prototype属性继承于BMap.Control
    ChatBtnControl.prototype = new BMap.Control();

    // 自定义控件必须实现自己的initialize方法,并且将控件的DOM元素返回
    // 在本方法中创建个div元素作为控件的容器,并将其添加到地图容器中
    ChatBtnControl.prototype.initialize = function(map) {
        // 创建DOM结构
        var div = document.createElement("div");
        var redDot = document.createElement("div");
        div.appendChild(redDot);
        // 添加文字说明
        div.className = "toolbar toolbar-message";
        redDot.style.width = "9px";
        redDot.style.height = "9px";
        redDot.style.position = "absolute";
        redDot.style.background = "#e24e82";
        redDot.style.borderRadius = "50%";
        redDot.style.display = "none";
        redDot.style.top = "8px";
        redDot.style.left = "27px";
        redDot.style.border = "2px solid white";
        // 未读状态切换方法
        this.setReadState = function( state ){
            if(state == true) redDot.style.display = "block";
            else redDot.style.display = "none";
        };
        // 绑定事件
        div.onclick = function(e){
            window.location.href="/n/map/chat";
        };
        // 添加DOM元素到地图中
        map.getContainer().appendChild(div);
        // 将DOM元素返回
        return div;
    };
    // 未读状态切换方法 参数true为显示小红点，false为不显示
    ChatBtnControl.prototype.setReadState = function (state) {
        this.setReadState( state );
    };
    // 创建控件
    chatBtnCtrl = new ChatBtnControl();
    // 添加到地图当中
    map.addControl(chatBtnCtrl);
    /*end---------添加自定义控件-----------*/
};

var addMarkBtn = function(){

    /*------------添加自定义控件-----------*/
    // 定义一个标记工具控件类
    function MarkBtnControl(){
        // 默认停靠位置和偏移量
        this.defaultAnchor = 0;
        this.defaultOffset = new BMap.Size(0, 0);
    }

    // 通过JavaScript的prototype属性继承于BMap.Control
    MarkBtnControl.prototype = new BMap.Control();

    // 自定义控件必须实现自己的initialize方法,并且将控件的DOM元素返回
    // 在本方法中创建个div元素作为控件的容器,并将其添加到地图容器中
    MarkBtnControl.prototype.initialize = function(map){
        // 创建OM结构
        var div = document.createElement("div");
        var div1 = document.createElement("div");
        div1.className = "toolbar toolbar-market"
        //var div2 = document.createElement("div");
        var that = this;
        var oldPoint;

        div.appendChild(div1);

        // 控件样式状态切换
        this.setBtnCtlState = function(state){

            var topBar = document.getElementById('topBar')
            if( state == "ready" ){
                topBar.style.display = "none";
                console.log(1);
            }
            else if( state == "marking" ){
                topBar.style.display = "block";
                console.log(2);
            }
            else if( state == "submitting" ){
                //div.style.display = "none";
                //transform(topBar,{top:'-50px'},'linear',function(){});
                topBar.style.display = "block";
                console.log(3);
            }
            else{
                console.log("the MarkBtnCtl's state error!!");
            }
        };
        // 绑定事件
        div1.onclick = function(e){
            if (!checkLogined()){
                return;
            }
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
                headPointOnNow._div.style.zIndex=-1;
            }
            headPointOnNow = null;

            var bottomInfWindow = document.getElementById("bottomInfWindow");
            bottomInfWindow.innerHTML = "";
            bottomInfWindow.style.bottom = "-138px";

            // 恢复底部控件
            // 移除底部控件
            //map.removeControl(navigationControl);
            map.removeControl(scaleControl);
            //map.removeControl(geolocationControl);
            // 设定底部控件位置偏移
            //navigationControl =new BMap.NavigationControl();
            scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 5)});
            //geolocationControl = new BMap.GeolocationControl();
            // 添加底部控件
            //map.addControl(navigationControl);
            map.addControl(scaleControl);
            //map.addControl(geolocationControl);

            centerPoint = map.getCenter();
            console.log("Map's center Position is ("+centerPoint.lng+","+centerPoint.lat+")");
            centerIcon.style.display = "block";
            centerIcon.style.left = document.body.clientWidth / 2 - 28 + "px";
            centerIcon.style.top = document.body.clientHeight / 2 - 54 + "px";

        };
        document.getElementById("cancelMarkBtn").onclick = function(e){
            that.setBtnCtlState("ready");
            centerIcon.style.display = "none";
            centerPoint = map.getCenter();

        };
        document.getElementById("confirmMarkBtn").onclick = function(e){

            that.setBtnCtlState("submitting");

            centerIcon.style.display = "none";
            centerPoint = map.getCenter();
            centerIcon2 = new BMap.Marker(centerPoint,{icon:cpIcon});
            map.addOverlay(centerIcon2);
            centerIcon2.setTop(true,27000000);
            centerIcon2.setZIndex(45000000);

            // 上移底部控件
            // 移除底部控件
            //map.removeControl(navigationControl);
            map.removeControl(scaleControl);
            //map.removeControl(geolocationControl);
            // 设定底部控件位置偏移
            //navigationControl =new BMap.NavigationControl({offset: new BMap.Size(12, 170)});
            scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 170)});
            //geolocationControl = new BMap.GeolocationControl({offset: new BMap.Size(12, 170)});
            // 添加底部控件
            //map.addControl(navigationControl);
            map.addControl(scaleControl);
            //map.addControl(geolocationControl);
            var schoolmateLocationLng = centerPoint.lng; //校友位置经度
            var schoolmateLocationLat = centerPoint.lat; //校友位置纬度
            map.removeControl(scaleControl);
            scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 15)});
            map.addControl(scaleControl);
            that.setBtnCtlState("ready");
            map.removeOverlay(centerIcon2);
            centerPoint = map.getCenter();
            centerIcon.style.display = "none";
            // var bottomInfWindow = document.getElementById("bottomInfWindow");
            // bottomInfWindow.innerHTML = "";
            // bottomInfWindow.style.bottom = "-138px";
            var point = new Point();
            point.newPoint(schoolmateLocationLng,schoolmateLocationLat,'',function(data){
                if (data.errcode){
                    alert(data.errmsg);
                }else{
                    that.setBtnCtlState("ready");
                    centerIcon.style.display = "none";
                    if (last_zoom>12){
                        //头像
                        point.addOverlay(data.data);
                    }else{
                        //闪烁
                        point.AddOneManyPoint(data.data);
                    }
                }
            });
        };
        // 添加DOM元素到地图中
        map.getContainer().appendChild(div);
        // 将DOM元素返回
        return div;
    };
    MarkBtnControl.prototype.setMarkBtnCtl = function (state) {
        this.setBtnCtlState(state);
    };
    // 创建控件
    markBtnCtrl = new MarkBtnControl();
    // 添加到地图当中
    map.addControl(markBtnCtrl);
};

Point.prototype.addOverlay = function(info){

    /*------------定义校友位置头像覆盖物-----------*/
    function SchoolmatePosOverlay(point, headImg){
        this._point = point;
        this._headImg = headImg;
        this._info = info;
    }
    SchoolmatePosOverlay.prototype = new BMap.Overlay();
    SchoolmatePosOverlay.prototype.initialize = function(_map){

        this._map = _map;
        var that = this;
        var zoom = map.getZoom();
        if (zoom && zoom >= 14) {
            var div = this._div = document.getElementById('head-big').cloneNode(true);
        }else{
            var div = this._div = document.getElementById('head-small').cloneNode(true);
        }
        var head = this._head = div.getElementsByTagName('div')[0];
        div.style.display = "block";
        head.style.backgroundImage = "url("+this._headImg+")";

        var isMove = false;
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
            markBtnCtrl.setMarkBtnCtl("ready");
            centerIcon.style.display = "none";
            centerPoint = map.getCenter();

            if(headPointOnNow != null){

                headPointOnNow.setOFF();
                headPointOnNow._div.style.zIndex=-1;
            }
            that.setON();
            headPointOnNow = that;
            headPointOnNow._div.style.zIndex=9999;
            map.removeOverlay(centerIcon2);

            //map.panTo(tempPoint);
            //map.removeControl(navigationControl);   // 添加默认缩放平移控件
            map.removeControl(scaleControl);   // 添加比例尺控件控件
            //map.removeControl(geolocationControl);  // 添加移动端定位控件


            //navigationControl =new BMap.NavigationControl({offset: new BMap.Size(12, 170)});
            scaleControl = new BMap.ScaleControl({offset: new BMap.Size(120, 170)});
            //geolocationControl = new BMap.GeolocationControl({offset: new BMap.Size(12, 170)});

            //map.addControl(navigationControl);   // 添加默认缩放平移控件
            map.addControl(scaleControl);   // 添加比例尺控件
            //map.addControl(geolocationControl);  // 添加移动端定位控件


            var schoolmateName    = _this._info.creator.s_wechat_nick?_this._info.creator.s_wechat_nick:'';//校友微信昵称
            if (_this._info.nick)
                schoolmateMark    = _this._info.nick;//校友备注名
            else
                schoolmateMark    = '';
            var schoolmateCollege = _this._info.creator.s_college_name;//校友学院
            var schoolmateGrade   = _this._info.creator.s_grade;//校友年级
            var schoolmateComment = _this._info.comment;//校友介绍-上限40
            var imgSrc = _this._info.creator.s_wechat_head_img;//校友介绍-上限40
            var content =
                '<div id="schoolmateInf">' +
                '<div class="head-box"><img src="'+imgSrc+'" alt=""></div>' +
                '<span>'+schoolmateName   +'</span>' +
                '<span>'+(schoolmateGrade&& schoolmateGrade>0?schoolmateGrade:"未登记")    +'</span>' +
                '<span>'+(schoolmateCollege?schoolmateCollege:"未登记")+'</span>' +
                '<div class="schoolmate-mark"><span>备注</span>'+
                '<span id="schoolmateMark">'+(schoolmateMark?schoolmateMark:"点击右侧编辑按钮进行备注")+'</span>'+
                '<input id="schoolmateMarkInput"/>'+
                '<i id="schoolmateMarkEdit"></i></div>' +
                (_this._info.created_by==self_user_id?'':'<a id="messageButton" href="javascript:window.location.href=\'/n/map/chat/'+_this._info.created_by+'\';"></a>') +
                '</div>';
            console.log(_this._info);
            var bottomInfWindow = document.getElementById("bottomInfWindow");
            bottomInfWindow.innerHTML = content;
            bottomInfWindow.style.bottom = "-138px";
            transform(bottomInfWindow,{bottom:'12px'},'linear',function(){
                //     map.panTo(tempPoint);
                //console.log('success   回调函数');
            });
            var schoolmateMarkShow = document.getElementById("schoolmateMark");
            var schoolmateMarkInput = document.getElementById("schoolmateMarkInput");
            var schoolmateMarkEdit = document.getElementById("schoolmateMarkEdit");
            // schoolmateMarkShow.addEventListener("click",changeSchoolmateMark);
            // schoolmateMarkShow.addEventListener("touch",changeSchoolmateMark);
            schoolmateMarkEdit.addEventListener("click",changeSchoolmateMark);
            schoolmateMarkEdit.addEventListener("touch",changeSchoolmateMark);
            schoolmateMarkInput.addEventListener("blur",submitSchoolmateMark);
            schoolmateMarkInput.addEventListener("keydown",submitSchoolmateMark);
            function changeSchoolmateMark(e){
                schoolmateMarkShow.style.display = "none";
                schoolmateMarkInput.style.display = "inline-block";
                schoolmateMarkEdit.className='editIng';//将修改按钮变灰色
                schoolmateMarkInput.focus();
                schoolmateMarkInput.value = (schoolmateMarkShow.innerText=="点击右侧编辑按钮进行备注")?"":schoolmateMarkShow.innerText;
            }
            var submitTimeout;
            function submitSchoolmateMark(e){
                if( ((e.type == "keydown")&&(event.keyCode==13))
                    ||(e.type == "blur")){
                    if (submitTimeout > (new Date()).valueOf())
                        return;
                    submitTimeout=(new Date()).valueOf() +1000 * 0.3;
                    schoolmateMarkShow.style.display = "inline-block";
                    schoolmateMarkInput.style.display = "none";
                    schoolmateMarkEdit.className='';//将修改按钮变红色
                    schoolmateMarkShow.innerText  =  (schoolmateMarkInput.value=="")?"点击右侧编辑按钮进行备注":schoolmateMarkInput.value;
                    $.ajax({
                        type: 'post',
                        dataType:'json',
                        cache:false,
                        url: "/n/api/v1/index/map/message/setNick",
                        data:"friend_id="+_this._info.created_by+"&nick="+schoolmateMarkInput.value,
                        success: function(data){
                            if (data.errcode){
                                alert(data.errmsg);
                            }else{
                                console.log(data);
                            }
                        },
                        error : function() {
                            // alert("消息发送失败");
                        }
                    });
                }
            }
            setTimeout('touchMapTurnOffHead=true',200);
        }

        map.getPanes().markerPane.appendChild(div);

        return div;
    };
    SchoolmatePosOverlay.prototype.setON = function () {   // 定义点亮的方法
        this._div.style.backgroundRepeat = "no-repeat";
        this._div.style.zIndex=9999;

        if(map.getZoom()>=14){
            this._div.style.backgroundPosition = "-72px 0px";
        }else{
            this._div.style.backgroundPosition = "-36px 0px";
        }

    };
    SchoolmatePosOverlay.prototype.setOFF = function () {   // 定义灭掉的方法
        this._div.style.zIndex=-1;
        this._div.style.backgroundRepeat = "no-repeat";
        this._div.style.backgroundPosition = "0 0";
        // if(map.getZoom()>=14){
        //     this._head.style.backgroundSize = "50px 50px";
        //     this._head.style.width = "50px";
        //     this._head.style.height = "50px";
        //     this._head.style.top = "9px";
        //     this._head.style.left = "11px";
        // }else{
        //     this._head.style.backgroundSize = "26px 26px";
        //     this._head.style.width = "26px";
        //     this._head.style.height = "26px";
        //     this._head.style.top = "4px";
        //     this._head.style.left = "5px";
        // }

    };
    SchoolmatePosOverlay.prototype.draw = function(){
        var map = this._map;
        var pixel = map.pointToOverlayPixel(this._point);
        //减去气泡的高度，底部尖对准位置
        if (map.getZoom()>=14) {
            this._div.style.left = pixel.x - 36 + "px";
            this._div.style.top  = pixel.y - 74 + "px";
        }else{
            this._div.style.left = pixel.x - 18 + "px";
            this._div.style.top  = pixel.y - 37 + "px";
        }

    };
    //添加一个覆盖物，参数为 位置点 和 头像图象位置
    var headImg="/images/no-avatar.png";
    if (info.creator && info.creator.s_wechat_head_img)
        headImg=info.creator.s_wechat_head_img;
    var scoolmateHeadOverlay = new SchoolmatePosOverlay(new BMap.Point(info.lng,info.lat), headImg );
    //
    //overlays.push(info.id);
    var obj = new Object();
    obj.id = info.id;
    obj.lng = info.lng;
    obj.lat = info.lat;
    obj.obj = scoolmateHeadOverlay;
    newoverlays.push(obj);
    map.addOverlay(scoolmateHeadOverlay);

};


var pointCollection_Global;
var Overlays=new Array();

Point.prototype.clearOverlays = function ()
{
    map.clearOverlays();
    newoverlays = new Array();
    //overlays = new Array();
};
var points = [];  // 添加海量点数据
var s;
Point.prototype.AddManyPoint = function (point_data)
{
    s = new Stars();
    console.log("AddManyPoint");
    s.setRs(point_data);
};
Point.prototype.AddOneManyPoint = function (point_data)
{
    s.addOneRs(point_data);
};
