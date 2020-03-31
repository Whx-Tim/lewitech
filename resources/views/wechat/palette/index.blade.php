@extends('layouts.wechat')

@section('title', '中秋节')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/palette/main.css') }}">
    {{--<link rel="stylesheet" href="{{ asset('css/palette/page-file.css') }}">--}}
    {{--<link rel="stylesheet" href="{{ asset('css/palette/page-clip.css') }}">--}}
    {{--<link rel="stylesheet" href="{{ asset('css/palette/page-sky.css') }}">--}}
    {{--<link rel="stylesheet" href="{{ asset('css/palette/page-card.css') }}">--}}
    {{--<link rel="stylesheet" href="{{ asset('css/palette/page-palette.css') }}">--}}
    {{--<link rel="stylesheet" href="{{ asset('css/palette/page.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('plugins/swiper/swiper.min.css') }}">
    <link href="https://cdn.bootcss.com/animate.css/3.5.2/animate.css" rel="stylesheet">
@endsection

@section('content')
    <section class="container">
        @include('wechat.palette._file')
        @include('wechat.palette._sky')
        @include('wechat.palette._card')
        @include('wechat.palette._description')
        <div class="page page-result">
            <div class="user">
                <img class="avatar" src="{{ Auth::user()->detail->head_img or asset('images/no-avatar.png') }}">
                <div class="nickname">{{ Auth::user()->detail->nickname }}</div>
            </div>
            <div class="background-1">
                <img src="">
                <div class="canvas-image-area"></div>
                <div class="description-area">
                    <p></p>
                </div>
            </div>
            <div class="background-2">
                <img src="">
            </div>
            <div class="result-img-area"></div>
        </div>
        {{--<div class="page page-canvas active" id="result-container">--}}
            {{--<div class="user">--}}
                {{--<img class="avatar" src="{{ asset('images/no-avatar.png') }}">--}}
                {{--<div class="nickname">XIN</div>--}}
            {{--</div>--}}
            {{--<div class="background-1">--}}
                {{--<img src="{{ asset('images/palette/sky_4.png') }}">--}}
                {{--<div class="canvas-image-area"></div>--}}
                {{--<div class="description-area">--}}
                    {{--<p></p>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="background-2">--}}
                {{--<img src="{{ asset('images/palette/3.jpeg') }}">--}}
            {{--</div>--}}
        {{--</div>--}}
        @include('wechat.palette._palette')
        @include('wechat.palette._clip')
    </section>

@endsection

@section('javascript')
    <script src="{{ asset('js/plugins/photo_clip/hammer.js') }}"></script>
    <script src="{{ asset('js/plugins/photo_clip/iscroll-zoom.js') }}"></script>
    <script src="{{ asset('js/plugins/photo_clip/jquery.photoClip.js') }}"></script>
    <script src="{{ asset('plugins/swiper/swiper.min.js') }}"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var upload_file,
            clip_file;

        var sky_images = [
            @for($i=1; $i <= 4; $i++)
                '{{ asset('images/palette/sky_'. $i .'.png') }}',
            @endfor
        ];

        var sky_index = 0; //星空背景序号
        var source; //上传图片路径
        var description; //输入的文字信息
        var month; // 画出的月亮图片路径

        var page_file = $('.page-file');
        var page_clip = $('.page-clip');
        var page_card = $('.page-card');
        var page_palette = $('.page-palette');
        var page_description = $('.page-description');
        var page_result = $('.page-result');

        var mySwiper = new Swiper('.swiper-container',{
            freeMode: false,
            pagination: ".swiper-pagination",
            loop: true,
            width: window.innerWidth,
            prevButton: '.swiper-button-prev',
            nextButton: '.swiper-button-next'
        });

        $.fn.extend({
            animateCss: function (animationName, callback) {
                var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
                this.addClass('animated ' + animationName).one(animationEnd, function() {
                    $(this).removeClass('animated ' + animationName);
                    (callback && typeof(callback) === 'function') && callback();
                });


                return this;
            },
            pageShow: function (animationName) {
                var animation;
                var $this = this;
                if (isEmpty(animationName)) {
                    animation = 'fadeInUp';
                } else {
                    animation = animationName;
                }
                $this.show();
                $this.animateCss(animation);

            },
            pageHide: function (animationName) {
                var $this = this;
                var animation;
                if (isEmpty(animationName)) {
                    animation = 'fadeOutDown';
                } else {
                    animation = animationName;
                }
                this.animateCss(animation, function () {
                    $this.hide();
                })
            },
            pageChange: function (animationName) {
                var animation;
                var $this = this;
                var $next = $this.next();
                if (isEmpty(animationName)) {
                    animation = 'fadeOutDown';
                } else {
                    animation = animationName;
                }

                $this.animateCss(animation, function () {
                    $this.hide(0, function () {
                        $next.fadeIn(800);
                    });

                })
            }
        });

        function isEmpty(mixed_var)
        {
            if (mixed_var === "" || mixed_var === 0 || mixed_var === null || mixed_var === false || typeof(mixed_var) === 'undefined') {
                return true;
            }

            if (typeof(mixed_var) == 'object') {
                var key;
                for (key in mixed_var) {
                    return false;
                }

                return true;
            }

            return false;
        }


        $('#clip-area').photoClip({
            width: document.body.scrollWidth,
            height: 250,
            file: '#file',
            ok: '#clip-submit',
            outputType: 'png',
            strictSize: false,
            loadStart: function (file) {
                upload_file = file;
                $('.page-file').removeClass('active');
                page_clip.pageShow();
            },
            loadComplete: function (src) {
            },
            clipFinish: function (dataUrl) {
                clip_file = dataUrl;
                var img = '<img src="'+ dataUrl +'" class="result">';
                page_clip.pageHide();
                page_file.addClass('active');
                upload7niu(dataUrl, 'clip', function (key) {
                    console.log(key);
                    source = key;
                    $('.file-upload-box').html(img);
                    $('.upload-img-box').children('img').attr('src', dataUrl);
                });
                $('.background-2').each(function () {
                    $(this).children('img').attr('src', dataUrl);
                });
            }
        });

        $('#clip-cancel').on('click', function () {
            page_clip.pageHide();
            $('.page-file').addClass('active');
        });

        $('.next-btn').on('click', function () {
            var $this = $(this);
            var $page = $this.parents('.page');
            if ($page.hasClass('page-file')) {
                if (isEmpty(clip_file)) {
                    toastr.error('请选择一张图片');

                    return ;
                }
            }
            if ($page.hasClass('page-sky')) {
                $('.background-1').each(function () {
                    $(this).children('img').attr('src', sky_images[sky_index]);
                });
//                $('#canvas').css('background-image', 'url("'+ sky_images[sky_index] +'")');
            }
            if ($page.hasClass('page-card')) {
                var canvas_img = canvas.toDataURL();
                if (!isEmpty(canvas_img)) {
                    upload7niu(canvas_img, 'month', function (key) {
                        month = key;
                        console.log(key)
                    });
                }
                page_palette.pageHide();
                $('.canvas-image-area').html('<img src="'+ canvas_img +'">');
            }
            $page.pageChange();
        });

        $('.palette-click-area').on('click', function () {
            $(this).html('');
            page_palette.pageShow();
        });

        $('#refresh-btn').on('click', function () {
            window.location.reload();
        });

        $('.again-btn').on('click', function () {
            window.location.reload();
        });

        $('#generate').on('click', function () {
            var content = $('#text').val();
            content = content.replace(new RegExp("\n", 'gm'), '<br>');
            description = content;
            ajaxUpload(sky_index+1, source, content, month);
            $('.description-area').children('p').html(content);
            page_description.pageChange();
        });

        $('.img-box').children('img').each(function (index) {
            var $this = $(this);
            console.log(index);
            $this.on('click', function () {
                $('.img-box').children('img').removeClass('active');
                $this.addClass('active');
                sky_index = index;
            })
        });

        function upload7niu(dataUrl, type, callback)
        {
            $.ajax({
                url: '/wechat/palette/token/'+type,
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        console.log(data.message);

                        var xhr = new XMLHttpRequest();
                        var url = 'http://upload-z2.qiniu.com/putb64/-1';
                        var code = dataUrl;
                        code = String(code);
                        code = code.replace('data:image/png;base64,', '');

                        xhr.open('POST', url, true);
                        xhr.setRequestHeader("Content-Type", "application/octet-stream");
                        xhr.setRequestHeader("Authorization", "UpToken " + data.data.token);

                        xhr.onreadystatechange = function () {
                            if (xhr.readyState == 4) {
                                if (xhr.status == 200) {
                                    (callback && typeof(callback) === 'function') && callback(JSON.parse(xhr.responseText).key);
                                } else {
                                    toastr.error('图片上传失败，请稍后重试');
                                    console.log(xhr.responseText);
                                }
                            }
                        };
                        xhr.send(code);
                    }
                }
            })

        }

        function ajaxUpload(index, path, content, month)
        {
            var formData = {
                sky_index: index,
                source: path,
                description: content,
                month: month
            };

            $.ajax({
                url: '{{ route('wechat.palette.save') }}',
                data: formData,
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        console.log(data.message);
                        window.location.href = '{{ url('wechat/palette/result') }}/'+data.data.id;
                    }
                },
                error: function (error) {
                    if (error.status = 422) {
                        var errorData = JSON.parse(err.responseText);
                        var errorInfo;
                        for(var i in errorData) {
                            errorInfo = errorData[i][0];
                        }
                        toastr.error(errorInfo);
                    } else {
                        console.log(err.responseText);
                        toastr.error('系统错误，请联系相关管理员');
                    }
                }
            })
        }




        /**
         * 画板处理开始
         */
        var colors = [
            '#5d647f','#fd9827','#fffd38','#c6d9ef'
        ];
        var pencils = [
            1, 3, 5, 'eraser'
        ];

        var canvas = document.getElementById('canvas');
        canvas.addEventListener('mousemove', onMouseMove, false);
        canvas.addEventListener('mousedown', onMouseDown, false);
        canvas.addEventListener('mouseup', onMouseUp, false);

        canvas.addEventListener('touchstart',onMouseDown,false);
        canvas.addEventListener('touchmove',onMouseMove,false);
        canvas.addEventListener('touchend',onMouseUp,false);


        canvas.height = 250;
        canvas.width = getWidth() - 25;
        var ctx = canvas.getContext('2d');
        ctx.lineWidth = 1.0; // 设置线宽
        var lineWidth = ctx.lineWidth+1;
        ctx.strokeStyle = "#fffd38"; // 设置线的颜色
        ctx.shadowColor = "#fffd38";
        ctx.shadowBlur = 1;

        var flag = false;
        var clear = false;
        function onMouseMove(evt)
        {
            evt.preventDefault();
            var p = pos(evt);
            if (flag)
            {
                if (clear) {
                    ctx.clearRect(p.x-10, p.y-10, 20, 20);
                } else {
                    ctx.lineTo(p.x, p.y-50);
                    ctx.lineWidth = lineWidth; // 设置线宽
                    ctx.stroke();
                }
            }
        }

        function onMouseDown(evt)
        {
            evt.preventDefault();
            ctx.beginPath();
            var p = pos(evt);

            ctx.moveTo(p.x, p.y-50);
            flag = true;
        }


        function onMouseUp(evt)
        {
            evt.preventDefault();
            flag = false;
        }

        function pos(event)
        {
            var x,y;
            if(isTouch(event)){
                x = event.touches[0].pageX;
                y = event.touches[0].pageY;
            }else{
                x = event.layerX;
                y = event.layerY;
            }
            return {x:x,y:y};
        }

        function isTouch(event)
        {
            var type = event.type;
            if(type.indexOf('touch')>=0){
                return true;
            }else{
                return false;
            }
        }

        function getWidth()
        {
            var xWidth = null;

            if (window.innerWidth !== null) {
                xWidth = window.innerWidth;
            } else {
                xWidth = document.body.clientWidth;
            }

            return xWidth;
        }

        $('.tool-color').children('div').each(function (index) {
            var $this = $(this);
            $this.on('click', function () {
                $('.tool-color').children('div').removeClass('active');
                $this.addClass('active');
                ctx.strokeStyle = colors[index]; // 设置线的颜色
                ctx.shadowColor = colors[index];
            })
        });
        $('.tool-pencil').children('div').each(function (index) {
            var $this = $(this);
            $this.on('click', function () {
                $('.tool-pencil').children('div').removeClass('active');
                $this.addClass('active');
                if (index == 3) {
                    clear = true;
                } else {
                    clear = false;
                    ctx.lineWidth = pencils[index]; // 设置线宽
                    lineWidth = ctx.lineWidth+1;
                }
            })
        });

        $('#palette-clear').on('click', function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        $('#palette-submit').on('click', function () {
            var canvas_img = canvas.toDataURL();
            if (!isEmpty(canvas_img)) {
                upload7niu(canvas_img, 'month', function (key) {
                    month = key;
                    console.log(key)
                });
            }
            page_palette.pageHide();
            page_card.pageChange();
            $('.canvas-image-area').html('<img src="'+ canvas_img +'">');
        });
        /**
         * 画板处理结束
         */


        function setShare(title)
        {
            var desc='中秋快乐 :)';
            var link=window.location.href;
            var type='link';
            var imgUrl="{{ asset('images/palette/share.jpg') }}";
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
        wx.config(<?php echo $js->config(array('onMenuShareQQ','onMenuShareAppMessage', 'onMenuShareTimeline'), false) ?>);
        wx.ready(function(){
            setShare('我正在潜心创作一个“月亮”，你也来画一个吧~');
        });
    </script>
@endsection