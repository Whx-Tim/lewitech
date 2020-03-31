@extends('layouts.web')

@section('title', '首页')

@push('css')
<style>
    .acting-box {
        display: inline-block;
        margin-right: 20px;
        position: relative;
    }
    .acting-box-footer {
        padding: 0 5px;
        position: absolute;
        bottom: 0;
        left: 0;
        color: #ffffff;
        width: 100%;
    }
    .acting-box-footer > h5 {
        font-weight: bolder;
    }

    .acting-title {
        position: absolute;
        top: -50px;
        left: 10px;
        padding: 5px;
        font-size: 20px;
    }

    .introduction {
        background-image: url('{{ asset('images/web/introduction_bg.png') }}');
        background-size: 100%;
        background-repeat: no-repeat;
        min-height: 550px !important;
    }

    .active-box {
        width: 600px;
        height: 300px;
        position: absolute;
        top: 20%;
        left: 50%;
        overflow: hidden;
        overflow-y: auto;
    }
    .active-box  li.hot:after {
        position: absolute;
        content: '';
        background-image: url('{{ asset('images/web/hot.png') }}');
        background-size: 100% 100%;
        background-repeat: no-repeat;
        width: 83px;
        height: 71px;
        top: 0;
        right: 0;
    }
    .active-box .image {
        display: inline-block;
    }
    .acting-box .info {
        display: inline-block;
    }
    .active-box-link {
        position: absolute;
        right: 0;
        top: 25%;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="carousel carousel-slider">
            <div class="carousel-fixed-item" style="bottom: 38%">
                <a href="javascript:;" class="carousel-prev left"><i class="material-icons large white-text">keyboard_arrow_left</i></a>
                <a href="javascript:;" class="carousel-next right"><i class="material-icons large white-text">keyboard_arrow_right</i></a>
            </div>
            <a href="{{ route('web.umbrella') }}" class="carousel-item"><img src="{{ asset('images/web/banner_1.jpeg') }}"></a>
            {{--<a href="{{ route('web.app') }}" class="carousel-item"><img src="{{ asset('images/web/banner_2.jpeg') }}"></a>--}}
            <a href="{{ route('web.umbrella') }}" class="carousel-item"><img src="{{ asset('images/web/banner_3.jpeg') }}"></a>
        </div>
    </div>
    <div class="row" style="margin-top: 60px; margin-bottom: 60px">
        <div class="col m1 offset-m4 l1 offset-l4 center-align">
            <a href="{{ route('web.introduction') }}" class="cyan-text">
                <img src="{{ asset('images/web/introduction_icon.png') }}" width="100" style="margin-bottom: 10px"><br>
                公司介绍
            </a>
        </div>
        <div class="col m1 offset-m2 l1 offset-l2 center-align">
            <a href="{{ route('web.app') }}" class="cyan-text">
                <img src="{{ asset('images/web/project_icon.png') }}" width="100" style="margin-bottom: 10px"><br>
                项目介绍
            </a>
        </div>
    </div>
    <div class="row" style="margin-bottom: 80px">
        <div class="col m12 l12 grey lighten-1" style="height: 2px"></div>
    </div>
    <div class="row">
        <div class="col m9 l9 grey lighten-3" style="padding: 20px">
            <div class="col m9 l9 offset-l4" style="position: relative">
                <span class="acting-title cyan white-text">乐微活动</span>
                <div class="acting-box">
                    <img src="{{ asset('images/web/active.png') }}" width="373">
                    <div class="acting-box-footer cyan">
                        <h6>第二期准备中!</h6>
                        <h5>乐微校园活动 <small class="right">2017/10/16</small></h5>
                    </div>
                </div>
                <div class="acting-box">
                    <img src="{{ asset('images/web/class.png') }}" width="373">
                    <div class="acting-box-footer cyan">
                        <h6>火热进行中...</h6>
                        <h5>乐微共享课堂 <small class="right">2017/10/16</small></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="min-height: 550px;margin-top: 100px;margin-bottom: 100px;position: relative">
        <div class="col m10 offset-m2 l10 offset-l2 introduction">
            <div class="card" style="margin-top: 30px;margin-left: 50px;width: 52%">
                <div class="card-panel">
                    <h4>公司简介</h4>
                    <div class="col m6 l6 grey"></div>
                    <div class="row">
                        <div class="col m8 l8">
                            <p style="text-indent: 2em; line-height: 35px">乐微科技（深圳）有限公司成立于2016年，是一家致力于通过互联网技术整合高校校友资源、建立全国各地高校校友资源共享互助平台的互联网企业。公司拥有强大的平台技术壁垒，通过区块链技术、大数据和AI人工智能技术的结合，真实而高效地建立起高校校友的信用体系。在强大的技术力量、丰富的高校校友资源、精英运营团队以及"用户即股东"共享模式的共同推进下，乐微科技（深圳）有限公司将竭诚打造一流的互联网高校校友产品</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="active-box grey lighten-3">
            <ul>
                <li class="hot" style="padding-top:15px;border-bottom: 1px solid rgb(150,150,150);position: relative">
                    <div class="row">
                        <div class="col l4">
                            <img src="{{ asset('images/web/active.png') }}" width="180px">
                        </div>
                        <div class="col l8">
                            <p style="margin: 0">第一次圆满成功，第二期即将启动...</p>
                            <h5 style="margin-top: 20px">乐微校园活动 <br><small>2017.10.16</small></h5>
                            <a href="http://mp.weixin.qq.com/s/uXYD99DmT1ZEJu9ZEh3Mdg" class="active-box-link"><i class="material-icons large cyan-text">keyboard_arrow_right</i></a>
                        </div>
                    </div>
                </li>
                <li style="padding-top:15px;border-bottom: 1px solid rgb(150,150,150);position: relative">
                    <div class="row">
                        <div class="col l4">
                            <img src="{{ asset('images/web/class.png') }}" width="180px">
                        </div>
                        <div class="col l8">
                            <p style="margin: 0">第一期：共享雨伞与乐微模式！</p>
                            <h5 style="margin-top: 20px">乐微共享课堂 <br><small>2017.10.16</small></h5>
                            <a href="https://mp.weixin.qq.com/s/HsdzV8Q16JYua2phB8BHkg" class="active-box-link"><i class="material-icons large cyan-text">keyboard_arrow_right</i></a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@endsection

@push('javascript')
<script>
$('.carousel.carousel-slider').carousel({
    fullWidth: true,
    duration: 200,
    indicators: true
});

setInterval(function () {
    $('.carousel').carousel('next');
}, 5000);

$('.carousel-next').on('click', function () {
    $('.carousel').carousel('next');
});
$('.carousel-prev').on('click', function () {
    $('.carousel').carousel('prev');
})

</script>
@endpush