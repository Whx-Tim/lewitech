@extends('layouts.wechat')

@section('title', '开始你的表演')

@section('css')
    <style>
        body {
            background-color: #ecebeb;
        }
        section.container {
            position: relative;
            width: 100vw;
            height: 100vh;
            overflow-y: auto;
            background-image: url("http://wj.qn.h-hy.com/images/lewitech/badge/world/avatar_index.png");
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }

        ._preview-container {
            display: block;
            width: 100vw;
            padding-top: 100px;
            position: relative;
            text-align: center;
        }
        ._preview-container > ._bg-img {
            width: 100%;
            display: block;
        }
        ._preview-container > ._user-avatar {
            min-height: 20vh;
            width: 100vw;
            display: block;
            text-align: center;
        }
        ._footer-btn-area {
            position: fixed;
            width: 100vw;
            height: 10vh;
            bottom: 0;
            left: 0;
            display: block;
            text-align: center;
        }
        .generate-btn {
            width: 80%;
            background-color: #ff9629;
            font-size: 16px;
            border: none;
            border-radius: 20px;
            color: white;
            padding: 10px 0;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .btn {
            width: 60%;
            background-color: #ff9629;
            font-size: 13px;
            border: none;
            border-radius: 20px;
            color: white;
            outline: none;
            padding: 10px 0;
        }
        .btn > span {
            font-size: 11px;
        }
        ._user-avatar > img {
            width: 50vw;
        }
        ._user-avatar > p {
            margin: 0;
            padding: 0;
            font-size: 22px;
            font-weight: bolder;
            text-align: center;
            color: white;
        }
        ._user-avatar > p > span {
            background-color: black;
            color: white;
        }
        ._rank-container > h4 > img {
            width: 40px;
            vertical-align: middle;
        }
        ._counter > p:nth-of-type(1) {
            display: inline-block;
            background-color: #e13124;
            color: white;
            text-align: center;
            padding: 3px 5px;
            position: relative;
            border-radius: 5px;
        }
        ._counter > p:nth-of-type(1) > img {
            position: absolute;
            top: -5px;
            right: -3px;
            width: 5vw;
        }
        ._counter > p:nth-of-type(2) {
            display: block;
            color: white;
            text-align: right;
        }

        ._input-container {
            position: fixed;
            top: 0;
            left: 0;
            opacity: 0;
            pointer-events: none;
            width: 100vw;
            height: 100vh;
            overflow-y: auto;
            background-image: url("http://wj.qn.h-hy.com/images/lewitech/badge/world/avatar_index.png");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            transform: translateY(100vh);
            transition: .5s all ease-in-out;
        }
        ._input-container.active {
            opacity: 1;
            transition: .5s all ease-in-out;
            pointer-events: auto;
            transform: translateY(0);
        }

        .input-group {
            padding: 5px;
            box-sizing: border-box;
            border: 1px solid rgb(180, 180, 180);
            margin-bottom: 10px;
        }
        .input-control {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
            border: none;
            outline: none;
            font-size: 15px;
        }
        .text-limit {
            text-align: right;
            color: rgb(150, 150, 150);
        }

        .content-btn {
            margin-bottom: 10px;
        }


        #fileForm {
            width: 100%;
            height: 230px;
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            background-color: #f0f0f0;
            box-sizing: border-box;
        }

        #fileForm .upload {
            text-align: center;
            overflow-y: auto;
        }

        #fileForm .upload .file_upload {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 230px;
            z-index: 10;
        }

        #fileForm .upload img {
            width: 100px;
            height: 100px;
        }

        #fileForm .upload .upload-text {
            font-size: 15px;
            color: #3dd3c3;
        }

        #fileForm .upload .facing-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 230px;
            z-index: 11;
        }

        #fileForm .upload .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.6);
            height: 20px;
            width: 20px;
            border-radius: 20px;
            font-size: 18px;
            padding: 5px;
            z-index: 12;
            line-height: 20px;
        }
        #fileForm > .face-img {
            opacity: 0;
            pointer-events: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 230px;
            text-align: center;
            z-index: 13;
        }
        #fileForm > .face-img.active {
            opacity: 1;
            pointer-events: auto;
        }
        .face-img > img {
            width: 200px;
            height: 200px;
            margin-top: 15px;
            border-radius: 50%;
            border: none;
        }
        #upload-area {
            opacity: 0;
            pointer-events: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 99;
            background-color: rgb(100,100,100);
            transition: .5s all ease-in-out;
        }
        #upload-area.active {
            opacity: 1;
            pointer-events: auto;
            transition: .5s all ease-in-out;
        }
        #clip-area {
            height: 90vh;
        }

        .button-group {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 10vh;
            width: 100%;
            display: flex;
            flex-direction: row;
        }
        .button-group button {
            width: 45%;
            font-size: 15px;
            border: none;
            padding: 10px;
            font-weight: bold;
            color: #ffffff;
            flex: 1;
        }
        #upload-area .button-group button {
            pointer-events: none;
        }
        #upload-area.active .button-group button {
            pointer-events: auto;
        }
        #cancel-btn {
            background-color: rgb(200,200,200);
        }
        #clip-btn {
            background-color: #86d850;
        }

        ._input-box {
            width: 90%;
            margin-left: 5%;
            background-color: white;
            text-align: center;
            padding: 10px;
            box-sizing: border-box;
        }
        .next-btn {
            margin-top: 40px;
        }

    </style>
@endsection

@section('content')
    <section class="container">
        <div class="_preview-container">
            <div class="_user-avatar">
                <img src="{{ asset($user_avatar) }}" alt="">
                <p><span>请长按上图保存您的专属头像~</span></p>
            </div>
            {{--<button type="button" class="btn download-btn">点击保存头像</button>--}}
            <button type="button" class="btn next-btn">点击生成趣味分享图</button>
        </div>
        <div class="_input-container">
            <div class="_input-box">
                @if($type != 'team')
                    <button type="button" class="btn content-btn">请开始你的表演<br><span>点我预选文字喔~</span></button>
                @endif
                <form id="mainForm">
                    @if($type == 'team')
                        <div class="input-group">
                            <input type="text" class="input-control" name="content_1" value="我为" readonly>
                        </div>
                        <div class="input-group">
                            <input type="text" class="input-control" name="content_2" value="{{ $badge->badge->name }}" readonly>
                        </div>
                        <div class="input-group">
                            <input type="text" class="input-control" name="content_3" value="打CALL！" readonly>
                        </div>
                    @else
                        <div class="input-group">
                            <input type="text" class="input-control" name="content_1" maxlength="5">
                            <div class="text-limit"></div>
                        </div>
                        <div class="input-group">
                            <input type="text" class="input-control" name="content_2" maxlength="8">
                            <div class="text-limit"></div>
                        </div>
                        <div class="input-group">
                            <input type="text" class="input-control" name="content_3" maxlength="10">
                            <div class="text-limit"></div>
                        </div>
                    @endif
                    <input type="hidden" name="type" value="{{ $type }}">
                </form>
                @if($type == 'diy')
                    <form id="fileForm">
                        <div class="upload">
                            <input type="file" name="file" class="file_upload" id="file" accept="image/*">
                            <img src="{{ asset('images/camera.png') }}">
                            <div class="upload-text">点击上传趣味头像</div>
                        </div>
                        <div class="face-img">
                            <img src="" alt="">
                        </div>
                    </form>
                @endif
                <button type="button" class="generate-btn">点击生成趣味图片</button>
            </div>
        </div>
        <div id="upload-area">
            <div id="clip-area"></div>
            <div class="button-group">
                <button type="button" id="cancel-btn">取消</button>
                <button type="button" id="clip-btn">确认</button>
            </div>
        </div>
        {{--<div class="_footer-btn-area">--}}

        {{--</div>--}}
    </section>
@endsection

@section('javascript')
    <script src="{{ asset('plugins/photo_clip/hammer.js') }}"></script>
    <script src="{{ asset('plugins/photo_clip/iscroll-zoom.js') }}"></script>
    <script src="{{ asset('plugins/photo_clip/jquery.photoClip.js') }}"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function () {
            var team = '{{ $badge->badge->name }}';
            var content = [
                {
                    title: '慌得一批',
                    content_1: '我是',
                    content_2: team + '球迷',
                    content_3: '现在慌得一批'
                },
                {
                    title: '注定要凉',
                    content_1: '我不是',
                    content_2: '天生要强',
                    content_3: '我只是注定要凉'
                },
                {
                    title: '一点不慌',
                    content_1: '我是伪球迷',
                    content_2: '我现在一点不慌',
                    content_3: '因为我已经凉了'
                },
                {
                    title: '买了彩票',
                    content_1: '我买了',
                    content_2: team + '队',
                    content_3: '现在慌得一批'
                },
                {
                    title: '快乐足球',
                    content_1: '我是',
                    content_2: team + '球迷',
                    content_3: '我选择快乐足球'
                },
                {
                    title: '已经凉了',
                    content_1: '我是',
                    content_2: team + '球迷',
                    content_3: '我现在已经凉了'
                },
                {
                    title: '没机会凉',
                    content_1: '我是',
                    content_2: team + '球迷',
                    content_3: '想凉也没机会凉'
                }
            ];
            $('.next-btn').on('click', function () {
                $('._input-container').addClass('active');
            });
            $('.content-btn').on('click', function () {
                var content_picker = [];
                for(var i in content) {
                    content_picker.push({
                        value: i,
                        label: content[i].title
                    })
                }
                weui.picker(content_picker, {
                    onConfirm: function (result) {
                          var key = result[0].value;
                          var content_item = content[key];
                          $('.input-group:nth-of-type(1)').children('.input-control').val(content_item.content_1);
                          $('.input-group:nth-of-type(2)').children('.input-control').val(content_item.content_2);
                          $('.input-group:nth-of-type(3)').children('.input-control').val(content_item.content_3);
                          updateTextLimit($('.input-group:nth-of-type(1)').children('.input-control'));
                          updateTextLimit($('.input-group:nth-of-type(2)').children('.input-control'));
                          updateTextLimit($('.input-group:nth-of-type(3)').children('.input-control'));
                    }
                })
            });

            function updateTextLimit($input)
            {
                var $max = $input.attr('maxlength');
                var $length = $input.val().length;
                $input.next().html($length + '/' + $max);
            }

            $('.generate-btn').on('touchstart', function () {
                var formData = $('#mainForm').serialize();
                $.ajax({
                    url: '{{ route('wechat.badge.world.check.share') }}',
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.code) {
                            toastr.error(data.message);
                        } else {
                            window.location.href = data.data.redirect;
                        }
                    },
                    error: function () {
                        toastr.error('当前生成人数过多，请稍后重试');
                    }
                })
            });

            $('.text-limit').each(function () {
                var $max = $(this).prev().attr('maxlength');
                var $this = $(this);
                $(this).html('0/' + $max);
                $(this).prev().on('input', function () {
                    var $length = $(this).val().length;
                    $this.html($length + '/' + $max);
                });
            });

            $('#clip-area').photoClip({
                width: 200,
                height: 200,
                file: '#file',
                ok: '#clip-btn',
                outputType: 'jpg',
                strictSize: false,
                loadStart: function (file) {
                    $('#upload-area').addClass('active');
                },
                loadComplete: function (src) {
                },
                clipFinish: function (dataUrl) {
                    var input = '<input type="hidden" name="clip_image" value="'+ encodeURIComponent(dataUrl) +'">';
                    $('#mainForm').append(input);
                    $('.face-img > img').attr('src', dataUrl);
                    $('.face-img').addClass('active');
                    $('#upload-area').removeClass('active');
                    console.log(dataUrl);
                }
            });
            $('#cancel-btn').on('click', function () {
                console.log('aaaa');
                $('#upload-area').removeClass('active');
            });

            function setShare(title)
            {
                var desc=title;
                var link='{{ $link }}';
                var type='link';
                var imgUrl='http://wj.qn.h-hy.com/images/lewitech/badge/world/world_banner.jpeg';
                var dataUrl='';
                wx.onMenuShareTimeline({
                    title: title, // 分享标题
                    link: link, // 分享链接
                    imgUrl: imgUrl, // 分享图标
                    success: function () {
                        onshare("redHat","Timeline");
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareAppMessage({
                    title: title, // 分享标题
                    desc: desc, // 分享描述
                    link: link, // 分享链接
                    imgUrl: imgUrl, // 分享图标
                    type: type, // 分享类型,music、video或link，不填默认为link
                    dataUrl: dataUrl, // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        onshare("redHat","AppMessage");
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareQQ({
                    title: title, // 分享标题
                    desc: desc, // 分享描述
                    link: link, // 分享链接
                    imgUrl: imgUrl, // 分享图标
                    success: function () {
                        onshare("redHat","ShareQQ");
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            }
            wx.config(<?php echo $js->config(array('onMenuShareQQ','onMenuShareAppMessage', 'onMenuShareTimeline', 'downloadImage'), false) ?>);
            wx.ready(function(){
                setShare('好玩！自定义生成世界杯头像和趣图，你也来试试！');
                $('.download-btn').on('click', function () {
                    wx.downloadImage({
                        serverId: '{{ $media }}',
                        isShowProgressTips: 1,
                        success: function (res) {
                            alert('保存成功，请打开本地相册查看');
                        }
                    })
                })
            });
        });
    </script>
@endsection