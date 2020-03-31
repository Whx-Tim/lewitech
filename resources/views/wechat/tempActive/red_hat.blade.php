<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>圣诞大装扮</title>
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link href="{{ asset('plugins/red_hat/css/basic.css') }}" rel='stylesheet' tyle='text/css'>
    <link href="{{ asset('plugins/red_hat/css/frame.css') }}" rel='stylesheet' tyle='text/css'>
    <link href="{{ asset('plugins/red_hat/css/main.css') }}" rel='stylesheet' tyle='text/css'>
    <link href="{{ asset('plugins/red_hat/css/statistics.css') }}" rel='stylesheet' tyle='text/css'>
    <link rel="stylesheet" href="{{ asset('plugins/weui/weui.css') }}">
    <style>
        .header {
            height: 60px;
        }

        .header .logo {
            display: block;
            float: left;
            width: 100%;
            height: 60px;
            text-align: center;
        }

        .logo img {
            margin-left: auto;
            margin-right: auto;
            width: 100px;
        }
    </style>
</head>
<body>
<div class="header">
    <span class="logo">
        <img src="{{ asset('images/logo_green.png') }}">
    </span>
</div>
@if(!$subscribe)
    <div style="font-size:20px;text-align:center;line-height:30px;color:#343434; margin-bottom: 20px;margin-top: 50px">
        扫一扫<br>获取校友共享圈圣诞帽装扮系统入口
    </div>
    <img src="{{ asset('images/event_qrcode/red_hat.png') }}" style="width:70%;margin-left: 15%">
@else
    <div id="mask-blur">
        <input id="avatar_upload" type="file" name="avatar" value="" accept="image/*">
        <div class="display_result">
            <img id="avatar_orignal" width="250" height="250" src="{{ $head_img }}" alt="1.请上传头像~按钮在最底部">
            <canvas id="avatar_edit" width="600" height="600">你的手机浏览器不支持canvas~</canvas>
            <div class="ctrl_bar">
                <span onmousedown="avatar_editor.moveX(-5);">左移</span>
                <span onmousedown="avatar_editor.moveX(5);">右移</span>
                <span onmousedown="avatar_editor.moveY(-5);">上移</span>
                <span onmousedown="avatar_editor.moveY(5);">下移</span><br>
                <span onmousedown="avatar_editor.zoom(0.1);">放大</span>
                <span onmousedown="avatar_editor.zoom(-0.1);">缩小</span>
                <span onmousedown="avatar_editor.rotate(-1);">左旋</span>
                <span onmousedown="avatar_editor.rotate(1);">右旋</span>
            </div>
            <div style="color: red"><b>小提示:</b>1.双指拉伸滑动可以放大和旋转装饰物 <br>2.左右滑动物品栏可查看更多装饰物</div>
            <div class="changeImg">

            </div>
            <div class="weui-btn weui-btn_primary" onclick="show_new_avatar()">~生成头像~</div>
            <div class="weui-btn weui-btn_default" onclick="add_more()">~再添加一个~</div>
        </div>

        <div class="buttom_submit" style="display: none">上传头像</div>

    </div>
    <div id="mask"></div>
    <div id="output">
        <img src="" id="output_img" width="100%" alt="生成的头像"><br>
        <span>~请长按保存~</span>
    </div>
@endif


</body>
<script src="{{ asset('plugins/red_hat/js/hammer.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/red_hat/js/utility.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/red_hat/js/main.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/red_hat/js/report.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/red_hat/js/statistics.js') }}" type="text/javascript"></script>
<script>
    function convertImgToBase64(url, callback, outputFormat){
        var canvas  = document.createElement('CANVAS'),
            ctx = canvas.getContext('2d'),
            img = new Image;
        img.crossOrigin = 'Anonymous';
        img.onload = function(){
            canvas.height = img.height;
            canvas.width = img.width;
            ctx.drawImage(img,0,0);
            var dataURL = canvas.toDataURL(outputFormat);
            callback.call(this, dataURL);
            canvas = null;
        };
        img.src = url;
    }
    var image_url = document.getElementById('avatar_orignal').src;
    convertImgToBase64(image_url,function (base64Img) {
        document.getElementById('avatar_orignal').src = base64Img;
    });
</script>
</html>