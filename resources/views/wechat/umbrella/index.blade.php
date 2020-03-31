@extends('wechat.umbrella.common.layout')

@section('title', '共享雨伞')

@section('css')
    <style>

        section.container {
            padding: 10px;
        }
        .page-heading {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid rgb(200,200,200);
            background-image: url("{{ asset('images/umbrella/index-banner.png') }}");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            min-height: 200px;
            /*display: flex;*/
            /*flex-direction: column;*/
            /*justify-content: center;*/
            /*align-items: center;*/
            color: #fff;
            letter-spacing: .1em;
        }

        .page-heading h4 {
            padding-top: 40px;
            font-size: 17px;
            text-align: center;
            font-weight: 600;
        }
        .page-heading p {
            font-size: 15px;
            text-align: center;
        }

        .font-sc {
            font-family: "Lantinghei SC", sans-serif;
            background-color: #fff;
        }

        .user-avatar {
            position: relative;
            display: block;
            height: 60px;
        }
        .user-avatar img {
            position: absolute;
            top: -65px;
            left: 0;
            margin-left: calc(50% - 50px);
            width: 100px;
            height: 100px;
            border: none;
            border-radius: 50%;
        }

        .page-station {
            display: none;
            position: fixed;
            width: 95%;
            height: 95%;
            left: 2.5%;
            top: 2.5%;
            background-color: #efefef;
            padding: 20px 10px;
            box-sizing: border-box;
        }

        #station-form .form-group {
            position: relative;
            display: block;
            width: 100%;
            padding: 15px;
            box-sizing: border-box;
            /*height: 40px;*/
            /*line-height: 40px;*/
            margin-bottom: 20px;
            background-color: #ffffff;

        }

        #station-form .form-group.active {
            border: 1px solid #48acc8;
            color: #48acc8;
        }

        #station-form .form-group.have:after {
            position: absolute;
            content: '有伞';
            top: 15px;
            right: 60px;
            color: rgb(200,200,200);
            font-size: 15px;
        }
        #station-form .form-group.no-have:after {
            position: absolute;
            content: '没伞';
            top: 15px;
            right: 60px;
            color: rgb(200,200,200);
            font-size: 15px;
        }

        input[type=radio][name=station] {
            position: absolute;
            display: block;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 9;
            opacity: 0;
        }

        input[type=radio][name=station]:checked {
            position: relative;
            opacity: 1;
        }
        input[type=radio][name=station]:checked:after {
            position: absolute;
            content: '';
            background-image: url("{{ asset('images/umbrella/icon-select-active.png') }}");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            height: 25px;
            width: 25px;
            top: -25px;
            right: 10px;
        }

        .page-agreement {
            display: none;
            position: fixed;
            width: 95%;
            height: 90%;
            left: 2.5%;
            top: 2.5%;
            background-color: #efefef;
            padding: 20px 10px;
            box-sizing: border-box;
            overflow-y: auto;
            margin-bottom: 40px;
        }


        /*input[type=radio][name=station]:before {*/
            /*content: '';*/
            /*width: 100%;*/
            /*height: 100%;*/
            /*!*top: 0;*!*/
            /*!*left: 0;*!*/
            /*z-index: 10;*/
        /*}*/

        .btn-area {
            display: flex;
            position: fixed;
            left: 0;
            bottom: 45px;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-area-item {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
            background-color: #48acc8;
            color: #ffffff;
            font-size: 16px;
            letter-spacing: .5em;
        }
    </style>
@endsection

@section('content')
    <div class="page-heading">
        <h4>
            @if($user->umbrellaInfo->status == 1)
                请点击下方按钮借伞
            @elseif(($user->umbrellaInfo->status == 3))
                请让对方使用微信扫一扫<br>扫描雨伞上的二维码进行传递
            @else
                请在15天之内归还雨伞<br>已经借了:{{ $user->umbrellaInfo->standing() }}
                {{--@if($can_share)--}}
                    {{----}}
                {{--@else--}}
                    {{--请点击绑定雨伞<br>扫描雨伞的二维码,进行雨伞的绑定--}}
                {{--@endif--}}
            @endif

        </h4>
        <p>
            用户状态:{{ $user->umbrellaInfo->status2string() }}
        </p>
    </div>
    <div class="user-avatar">
        <img src="{{ $user->detail->head_img or asset('images/no-avatar.png') }}">
    </div>
    <section class="container">
        {{--<div class="weui-cell font-sc"><div class="weui-cell__bd"><p>可借雨伞数</p></div><div class="weui-cell__ft">{{ $have_amount }}/{{ $can_count }}</div></div>--}}
        {{--<a class="weui-cell weui-cell_access font-sc" href="{{ route('wechat.umbrella.donate') }}"><div class="weui-cell__bd"><p>捐赠的雨伞</p></div><div class="weui-cell__ft">0把</div></a>--}}
        <a class="weui-cell weui-cell_access font-sc" href="{{ route('wechat.umbrella.history') }}"><div class="weui-cell__bd"><p>借伞记录</p></div><div class="weui-cell__ft">{{ $history_count }}条</div></a>
        {{--<button type="button" class="general-btn" id="borrow-btn" {{ $user->umbrellaInfo->status == 1 ? '' : 'disabled' }}>我要借伞</button>--}}
        @if($user->umbrellaInfo->status == 1)
            <button type="button" class="general-btn" id="borrow-btn">我要借伞</button>
        @elseif($user->umbrellaInfo->status == 3)
            <button type="button" class="general-btn" id="cancel-share">取消传递</button>
        @else
            @if($can_share)
                <button type="button" class="general-btn" id="share-btn">我要传递</button>
                <button type="button" class="general-btn" id="still-btn">我要还伞</button>
            @else
                <button type="button" class="general-btn" id="still-btn">我要还伞</button>
                {{--<button type="button" class="general-btn" id="pass-btn">查看我的凭证</button>--}}
                {{--<button type="button" class="general-btn" id="bind-btn">绑定雨伞</button>--}}
            @endif
        @endif
        <button type="button" class="general-btn" id="fresh-btn">刷新当前状态</button>
        <button type="button" class="general-btn" id="gratuity-btn">打赏</button>
        <button type="button" class="general-btn" id="user-btn">使用说明</button>
        <h4 style="text-align: center;font-size: 12px;font-weight: 200">如有任何疑问,请加客服微信号：<br>weijuanpingtai</h4>
        {{--<a href="{{ route('wechat.umbrella.cancel') }}" style="display: block;text-align: center;">有异常</a>--}}
    </section>
    {{--@include('wechat.umbrella._user_agreement')--}}
    {{--<div class="page-station">--}}
        {{--<form id="station-form">--}}
            {{--<div class="form-group have">--}}
                {{--深大地铁站--}}
                {{--<input type="radio" name="station" value="1">--}}
            {{--</div>--}}
            {{--<div class="form-group have">--}}
                {{--桃园地铁站--}}
                {{--<input type="radio" name="station" value="2">--}}
            {{--</div>--}}
            {{--<div class="form-group no-have">--}}
                {{--高新园地铁站--}}
                {{--<input type="radio" name="station" value="3">--}}
            {{--</div>--}}
            {{--<div class="form-group no-have">--}}
                {{--车公庙地铁站--}}
                {{--<input type="radio" name="station" value="4">--}}
            {{--</div>--}}
            {{--<div class="btn-area">--}}
                {{--<button type="button" class="btn-area-item" id="cancel-btn">返回</button>--}}
                {{--<button type="button" class="btn-area-item" id="submit-btn">确认</button>--}}
            {{--</div>--}}

        {{--</form>--}}
    {{--</div>--}}
    @include('wechat.umbrella.common.footer')
@endsection

@section('javascript')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config(<?php echo $js->config(array('scanQRCode', 'checkJsApi'), false) ?>);

    </script>
    <script>
        $('input[name=station]').change(function () {
            $('.form-group').each(function () {
                $(this).removeClass('active');
            });
            $(this).parent().addClass('active');
            console.log($(this).val());
        });

        $('#gratuity-btn').on('click', function () {
            window.location.href = '{{ route('wechat.pay.response.umbrella.gratuity') }}';
        });

        $('#user-btn').click(function (event) {
            event.preventDefault();
            window.location.href = 'https://mp.weixin.qq.com/s/as9jg72grHcNUkdA0FTtEw';
//            $('.page-agreement').fadeIn(300);
        });

//        $('#cancel-btn').click(function () {
//            $('.page-agreement').fadeOut(300);
//        });
//        $('#submit-btn').click(function () {
//            $('.page-agreement').fadeOut(300);
//        });

        $('#pass-btn').click(function () {
            window.location.href = '{{ route('wechat.umbrella.pass') }}';
        });

        $('#fresh-btn').click(function () {
            window.location.reload(true);
        });

        @if($user->umbrellaInfo->status == 3)
        checkShare();
        function checkShare () {
            $.ajax({
                url: '{{ route('wechat.umbrella.check.share') }}',
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if(data.code) {
                        setTimeout(function () {
                            checkShare();
                        }, 5000);
                    } else {
                        window.location.href = '{{ route('wechat.umbrella.share.success') }}';
                    }
                },
            })
        }

        @endif

        $('#share-btn').click(function () {
            $.ajax({
                url: '{{ route('wechat.umbrella.share') }}',
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
                        window.location.reload();
                    }
                }
            })
        });

        $('#cancel-share').click(function () {
            $.ajax({
                url: '{{ route('wechat.umbrella.no-share') }}',
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
                        window.location.reload();
                    }
                }
            })
        });

        wx.ready(function () {
//            wx.checkJsApi({
//                jsApiList: ['scanQRCode'],
//                success: function (res) {
//                    alert(res);
//                }
//            });
            $('#still-btn').click(function () {
                var $confirm = confirm('请确认抵达共享雨伞还伞点，点击确认后扫码还伞。');
                if ($confirm) {
                    wx.scanQRCode();
                }
            });

//            $('#borrow-btn').click(function () {
//                wx.scanQRCode();
//            });
        });


        $('#borrow-btn').click(function () {
            var $confirm = confirm('请确认抵达共享雨伞借伞点，确认有伞后，点击屏幕确认按钮并扫描雨伞上的二维码。');
            if ($confirm) {
                wx.scanQRCode();
                {{--$.ajax({--}}
                    {{--url: '{{ route('wechat.umbrella.borrow') }}',--}}
                    {{--type: 'get',--}}
                    {{--dataType: 'json',--}}
                    {{--success: function (data) {--}}
                        {{--if (data.code) {--}}
                            {{--toastr.error(data.message);--}}
                        {{--} else {--}}
                            {{--toastr.success(data.message);--}}
                            {{--setTimeout(function () {--}}
                                {{--window.location.href = data.data.redirect;--}}
                            {{--}, 1000);--}}
                        {{--}--}}
                    {{--}--}}
                {{--})--}}
            }
        })
    </script>
@endsection