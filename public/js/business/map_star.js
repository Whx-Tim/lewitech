

var BW            = 0,    //canvas width
    BH            = 0,    //canvas height
    ctx           = null,
    stars         = [],   //存储所有星星对象的数组
    rs            = [],   //最新的结果
    py            = null, //偏移
    canvas        = null; //偏移

function Stars(){
}

Stars.prototype.addOneRs = function(data) {
    rs.push(data);
    showStars(rs);
}
Stars.prototype.setRs = function(data) {
    rs=data;
    // 复杂的自定义覆盖物
    function ComplexCustomOverlay(point){
        this._point = point;
    }
    ComplexCustomOverlay.prototype = new BMap.Overlay();
    ComplexCustomOverlay.prototype.initialize = function(map){
        this._map = map;
        canvas = this.canvas = document.createElement("canvas");
        canvas.style.cssText = "position:absolute;left:0;top:0;";
        ctx = canvas.getContext("2d");
        var size = map.getSize();
        canvas.width = BW = size.width;
        canvas.height = BH = size.height;
        map.getPanes().labelPane.appendChild(canvas);
        //map.getContainer().appendChild(canvas);
        return this.canvas;
    }
    ComplexCustomOverlay.prototype.draw = function(){
        var map = this._map;
        var bounds = map.getBounds();
        var sw = bounds.getSouthWest();
        var ne = bounds.getNorthEast();
        var pixel = map.pointToOverlayPixel(new BMap.Point(sw.lng, ne.lat));
        py = pixel;
        if (rs.length > 0) {
            showStars(rs);
        }
    }
    var myCompOverlay = new ComplexCustomOverlay(new BMap.Point(116.407845,39.914101));
    map.addOverlay(myCompOverlay);

    showStars(rs);
    render();
}

function Star(options){
    this.init(options);
}

Star.prototype.init = function(options) {
    this.x   = ~~(options.x);
    this.y   = ~~(options.y);
    this.initSize(options.size);
    if (~~(0.5 + Math.random() * 7) == 1) {
        this.size = 0;
    } else {
        this.size = this.maxSize;
    }
}

Star.prototype.initSize = function(size) {
    var size = ~~(size);
    this.maxSize = size > 6 ? 6 : size;
}

Star.prototype.render = function(i) {
    var p = this;

    if(p.x < 0 || p.y <0 || p.x > BW || p.y > BH) {
        return;
    }

    ctx.beginPath();
    // var img=document.getElementById("tulip");
    // ctx.drawImage(img,p.x,p.y);
    var gradient = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.size);
    gradient.addColorStop(0, "rgba( 226,78,130,1)");
    gradient.addColorStop(1, "rgba( 226,78,130,1)");
    ctx.fillStyle = gradient;
    ctx.arc(p.x, p.y, p.size, Math.PI*2, false);
    ctx.fill();
    if (~~(0.5 + Math.random() * 7) == 1) {
        p.size = 0;
    } else {
        p.size = p.maxSize;
    }
}

function render(){
    //闪烁定时器
    renderAction();
    setTimeout(render, 300);
}

function renderAction() {
    ctx.clearRect(0, 0, BW, BH);
    ctx.globalCompositeOperation = "source-over";
    for(var i = 0, len = stars.length; i < len; ++i){
        if (stars[i]) {
            stars[i].render(i);
        }
    }
}



//显示星星
function showStars(rs) {
    console.log("showStars");
    stars.length = 0;
    var temp = {};
    for (var i = 0, len = rs.length; i < len; i++) {
        var item = rs[i];
        var px = map.pointToOverlayPixel(item);
        //create all stars
        var s = new Star({
            x: px.x - py.x,
            y: px.y - py.y,
            size: 4
        });
        stars.push(s);
        //}
    }
    canvas.style.left = py.x + "px";
    canvas.style.top = py.y + "px";
    renderAction();
}

