@extends('layouts.business')

@section('title','校企详情')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/plugins/swiper/swiper.min.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            font-family: Microsoft YaHei, sans-serif;
        }

        .business-poster{
            position: relative;
            width: 100%;
            height: 212pt;
            border-bottom: 4pt solid #f2f2f2;
        }

        .business-poster img {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .circle {
            width: 48pt;
            height: 48pt;
            float: left;
            border-radius: 50%;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
        }


        .facing-title {
            font-family: Microsoft YaHei, sans-serif;
            font-weight: bold;
            font-size: 18pt;
            padding: 6pt 19pt;
        }

        .facing-item {
            display: inline-block;
            margin-left: 19pt;
            padding-bottom: 3pt;
            font-size: 12pt;
        }

        #business_detail_img{
            display: inline-block;
        }

        #business_type span,
        #business_price span,
        .business_score span{
            color: #b9b9b9;
        }

        .business-info {
            display: inline-block;
            width: 250px;
        }

        @media screen and (max-width: 330px) {
            .business-info {
                width: 200px;
            }
        }

        .business-info h4 {
            padding: 0;
            height: 100%;
            color: #b9b9b9;
            text-align: center;
            margin-top: 10pt;
        }

        .business-introduction {
            position: relative;
            width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding-left: 20pt;
            padding-right: 20pt;
            border-bottom: 3pt solid rgb(235,235,235);
        }

        .business-introduction h4 {
            padding: 0;
            padding-top: 18pt;
            color: #919191;
            font-size: 12pt;
        }

        .business-introduction p {
            padding: 0;
            word-wrap: break-word;
            color: #343434;
            margin-bottom: 14pt;
            font-size: 11pt;
        }

        #business_introduction {
            padding: 0;
            word-wrap: break-word;
            color: #343434;
            margin-bottom: 14pt;
            font-size: 11pt;
            margin-left: 19pt;
            padding-top: 10pt;
            padding-right: 19pt;
        }

        .business-img {
            position: relative;
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: flex-start;
            padding-right: 20px;
            overflow-x: auto;
            border-bottom: 1px solid #dedede;
        }

        .business-img-title {
            display: block;
            padding: 4.5pt 18pt;
            font-size: 12pt;
            color: #919191;
            border-bottom: 1px solid rgb(235,235,235);
        }


        .business-img .business-img-item img {
            display: inline-block;
            width: 60pt;
            height: 60pt;
            margin-top: 9.5pt;
            margin-bottom: 9.5pt;
            margin-right: 4pt;
        }

        .business-img .business-img-item:first-child {
            margin-left: 18pt;
        }

        .business-img-space {
            width: 18pt;
            height: 60pt;
            background-color: #ffffff;
        }

        .business-comment-title {
            min-height: 27pt;
        }
        .business-comment-title h4 {
            position: relative;
            font-weight: 500;
            padding-top: 6pt;
            padding-bottom: 6pt;
            padding-left: 20pt;
            color: #919191;
            font-size: 12pt;
        }
        .business-comment-title span {
            font-size: 12pt;
            font-weight: normal;
        }
        .business-comment-title small {
            float: right;
            font-weight: normal;
            margin-right: 45pt;
            font-size: 12pt;
        }
        .business-comment-title small a {
            text-decoration: none;
            color: #919191;
        }

        .business-comment-title small a img {
            position: absolute;
            width: 40pt;
            height: 40pt;
            top: -14pt;
            vertical-align: bottom;
        }

        .business-comment-body {
            width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .comment-item {
            padding-bottom: 4pt;
            padding-left: 12pt;
            border-bottom: 1px solid rgb(206,206,206);
        }

        .comment-item:first-child {
            border-top: 1px solid rgb(206,206,206);
        }

        .comment-item:last-child {
            border-bottom: none;
        }

        .comment-user {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .comment-user img {
            margin-top: 6pt;
            vertical-align: bottom;
        }

        .comment-user-info {
            padding-top: 6pt;
            display: inline-block;
            width: calc(100% - 64pt);
        }

        .comment-user h5,.comment-user h6 {
            margin: 0;
            display: inline-block;
            padding-left: 7pt;
        }

        .comment-user h5 {
            font-size: 12pt;
            font-weight: bold;
            padding-top: 6pt;
        }

        .comment-user h6 {
            float: right;
            margin-top: 1.5pt;
        }

        .comment-user h6 img {
            padding: 0;
        }

        .comment-user-info h6 time {
            margin-left: 10pt;
        }

        .comment-user-content {
            padding-top: 6pt;
            padding-right: 12pt;
            padding-bottom: 8pt;
            margin-left: 55pt;
            color: #9e9e9e;
        }

        .business-loading-more {
            width: 100%;
            min-height: 27pt;
            text-align: center;
            line-height: 27pt;
            background-color: #f2f2f2;
            padding: 5pt 0;
            font-size: 12pt;
            color: #848484;
        }

        .business-loading-more a{
            text-decoration: none;
            border: none;
            color: #848484;
            font-size: 12pt;
        }

        .score-star {
            margin-top: 4pt;
            height: 12pt;
            width: 12pt;
            padding-right: 4pt;
            vertical-align: sub;
        }

        .small-star {
            height: 13pt;
            width: 13pt;
        }

        .image-layer {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000000;
            display: none;
            position: fixed;
            z-index: 999;
            opacity: 1;
        }

        .layer-control {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .layer-control-image {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .layer-control ul{
            color: #ffffff;
            list-style: none;
            font-size: 20pt;
        }

        .image-layer .layer-control-image img{
            /*display: block;*/
            width: 100%;
            height: auto;
        }

        .business-item {
            border-bottom: 4pt solid #f2f2f2;
        }

        .business-item .business-item-body {
            padding-top: 4pt;
            padding-bottom: 4pt;
        }

        .business-item h4 {
            display: block;
            padding: 4.5pt 19pt;
            color: #f4568b;
            border-bottom: 1px solid #dedede;
            font-size: 12pt;
        }

        #business_detail_img {
            margin-left: 19pt;
            width: 60pt;
            height: 60pt;
            vertical-align: top;
        }

        #business_discount {
            font-size: 12pt;
            color: #f4568b;
        }

        #business_discount_date {
            color: #aaaaaa;
        }

        .business-info-more {
            float: right;
            color: #e3e3e3;
            font-size: 8pt;
            font-weight: 500;
            line-height: 24px;
        }

        .business-info-more img {
            height: 15px;
            vertical-align: sub;
        }

        #business_address,
        #business_phone {
            padding: 6pt 19pt;
            color: #363636;
            font-size: 10pt;
        }
        #business_address {
            border-bottom: 1px solid #e3e3e3;
        }
        #business_phone {
            border-bottom: 1px solid #e3e3e3;
        }

        #business_address img,
        #business_phone img {
            height: 10pt;
            padding-right: 10pt;
        }

        #business_address p,
        #business_phone p {
            padding: 0;
            margin: 0;
            display: inline;
        }

        #business_address div span,
        #business_phone span {
            float: right;
            font-size: 8pt;
            color: #d8d8d8;
        }

        #business_address_layer,
        #business_phone_layer {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000000;
            display: none;
            position: fixed;
            z-index: 999;
            opacity: .85;
        }

        #business_branch_store {
            display: inline;
        }
        /*新需求*/
        .business-poster-facing {
            z-index: 99;
            width: 100%;
            color: #000000;
            background-color: #ffffff;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .business_score {
            border-bottom: 4pt solid #f2f2f2;
        }

        #business_price,
        #business_type,
        .business_score {
            padding: 6pt 19pt;
            border-bottom: 1px solid #f2f2f2;
        }

        #business_price p,
        #business_type p,
        .business_score #business_score{
            display: inline-block;
            padding-left: 10pt;
        }

        .map-btn {
            text-decoration: none;
            color: #ef3865;
            background-color: #ffffff;
            z-index: 9999;
            box-shadow: 0 0 8px 1px rgb(150,150,150);
            padding: 10pt 5pt;
            font-size: 13pt;
            position: fixed;
            top: 60pt;
            left: 0;
            width: 15pt;
            text-align: center;
            line-height: 1.2;
            border-radius: 0 5px 5px 0;
        }

        .map-btn img {
            width: 15pt;
            height: 15pt;
        }
    </style>
@endsection

@section('content')
    <div class="business-poster-facing">
        <div class="facing">
            <h4 class="facing-title">{{ $business->name or '数据正在加载' }}</h4>
        </div>
    </div>
    <div class="business-poster">
        <img src="{{ $posterWith600 or asset('images/no-avatar.png') }}" alt="商家海报">
    </div>
    <div class="business-item">
        <h4 style="font-weight: bold;">
            校友专属优惠
            <span class="business-info-more">
                更多详情
                <img src="{{ asset('images/business/icon_more.png') }}">
            </span>
        </h4>
        <div class="business-item-body">
            <img src="{{ $posterWith600 or asset('images/no-avatar.png') }}" id="business_detail_img">
            <div class="business-info">
                <div id="business_discount">{{ $business->discount }}</div>
                <div id="business_discount_date">{{ $business->discount_date }}</div>
                <div class="business-branch-address">
                    <span style="display: none">参与店铺:</span>
                    <p id="business_branch_store"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="business-img-title">商家相册</div>
    <div class="swiper-container1 swiper-container-horizontal" style="overflow-x: hidden;border-bottom: 1px solid #dedede;">
        <div class="swiper-wrapper" style="margin: 9.5pt 19pt;">
            @foreach($imageWith200 as $key => $image)
                <div class="swiper-slide"><img src="{{ $image }}" style="height: 80px;width: 80px;" image-id="{{ $key }}" showimage></div>
            @endforeach
        </div>
    </div>

    <div id="business_address">
        <img src="{{ asset('images/business/business_address.png') }}">
        <p>{{ $business->address }}</p>
        @if(!$business->branches->isEmpty())
            <div style="display: block; height: 10pt;">
                <span>点击查看更多门店地址</span>
            </div>
        @endif
    </div>
    <div id="business_phone">
        <img src="{{ asset('images/business/business_phone.png') }}">
        <p>{{ $business->phone }}</p>
        @if(!$business->branches->isEmpty())
            <span>点击查看更多门店电话</span>
        @endif
    </div>
    <div id="business_type">
        <span>类别:</span>
        <p>{{ $business->type2string() }}</p>
    </div>
    <div class="business_score">
        <span>评分:</span>
        <div id="business_score" score="{{ $business->score }}"></div>
    </div>

    <div class="business-item">
        <h4>商家简介</h4>
        <p id="business_introduction">
            {{ $business->introduction }}
        </p>
    </div>

    <div class="business-comment">
        <div class="business-comment-title">
            <h4>评价
                <small><a href="#" id="commentControl">我要评价&nbsp;<img src="{{ asset('images/business/comment.png') }}" alt=""></a></small>
            </h4>
        </div>
        @foreach($commentInfo as $comment)
            <div class="business-comment-body">
                <div class="comment-item">
                    <div class="comment-user">
                        <img src="{{ $comment['head_img'] }}" class="circle">
                        <div class="comment-user-info"><h5>{{ $comment['nickname'] }}</h5><h6 score="{{ $comment['score'] }}"></h6></div>
                        <div class="comment-user-content">{{ $comment['content'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="business-loading-more">
        <a href="javascript:void(0)">点击加载更多评价</a>
    </div>
    <div class="image-layer">
        <div class="layer-control-image">
            <div class="swiper-container swiper-container-horizontal" style="width: 100%">
                <div class="swiper-wrapper" style="align-items: center">
                    @foreach($imageWith600 as $key => $image)
                        <div class="swiper-slide"><img src="{{ $image }}" class="page" action-id="{{ $key }}"></div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
    <div id="business_address_layer">
        <div class="layer-control">
            <ul id="business_branch_address">
                @foreach($business->branches as $branch)
                    <li>{{ $branch->name . ':' . $branch->address }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div id="business_phone_layer">
        <div class="layer-control">
            <ul id="business_branch_phone">
                @foreach($business->branches as $branch)
                    <li>{{ $branch->name . ':' . $branch->phone }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <a href="{{ route('wechat.business.index') }}" class="map-btn"><img src="{{ asset('images/business/icon_map.png') }}">返回地图</a>
@endsection

@section('javascript')
    <script src="{{ asset('js/plugins/jquery/3.2.1/jquery.min.js') }}"></script>
    <script src="{{ asset('js/plugins/swiper/swiper.min.js') }}"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
        var route = location.href;
        var share_title = '';
        var str = route.split("/");
        var business_id = str[str.length-1];
        var comments;
        var comment_counter = 0;
        var comment_length;
        var swiper = new Swiper('.swiper-container', {
            freeMode: false,
            pagination: ".swiper-pagination",
            width: window.innerWidth
        });
        var swiper1 = new Swiper('.swiper-container1', {
            spaceBetween: 10,
            centeredSlides : true,
            width: 80
        });
        $('.business-info-more').click(function () {
            window.location.href = '{{ route('wechat.business.discount', ['business' => $business->id]) }}';
        });

        //前端数据初始化---------start
        $.ajax({
            url: '/n/api/v1/index/map/business/detail/'+business_id,
            type: 'get',
            dataType: 'json',
            async: false,
            success: function (data) {
                if (data.errcode) {
                    alert(data.errmsg);
                } else {
                    var business_branch = JSON.parse(data.data.business_branch_address);
                    var business_branch_store = '';
                    var image = data.imageWith200;
                    var image600 = data.imageWith600;
                    comments = data.comments;
                    comment_length = comments.length;
                    if (!isEmpty(business_branch)) {
                        for (var j in business_branch) {
                            $('#business_branch_address').append('<li>'+ business_branch[j].business_store +':'+ business_branch[j].business_address +'</li>')
                            $('#business_branch_phone').append('<li>'+ business_branch[j].business_store +':'+ business_branch[j].business_phone +'</li>')
                            business_branch_store += business_branch[j].business_store + '、';
                        }
                        business_branch_store = business_branch_store.substr(0,business_branch_store.length-1);
                        $('.business-branch-address').children('span').css("display",'inline');
                    } else {
                        $('#business_address').children('div').hide();
                        $('#business_phone').children('span').hide();
                    }

                    $('.business-poster').children(':first').attr("src", data.posterWith600);
                    $('#business_detail_img').attr("src", data.posterWith600);
                    $('.facing-title').html(data.data.business_name);
                    $('#business_phone').children('p').html(data.data.business_phone);
                    $('#business_address').children('p').html(data.data.business_address);
                    var typeStr = typeToString(data.data.business_type);
                    $('#business_type').children('p').html(typeStr);
//                    $('#business_price').children('p').append(data.data.business_price+'元/人');
                    $('#business_score').attr('score', data.data.business_score);
                    $('#business_comment_count').html(data.comment_count + '条');
                    $('#business_introduction').html(data.data.business_introduction);
                    $('#business_discount').html(data.data.business_discount);
                    $('#business_discount_date').html(data.data.business_discount_date);
                    $('#business_branch_store').html(business_branch_store);
                    share_title = data.data.share;

                    for (var j in image) {
//                        $('.business-img').append('<div class="business-img-item"><img src="' + image[j] + '" alt="商家相册" image-id="'+ j +'" showimage></div>');
                        swiper1.appendSlide('<div class="swiper-slide"><img style="height: 80px; width: 80px;" src="'+ image[j] +'" image-id="'+ j +'" showimage></div>')
                    }
//                    $('.business-img').append('<div class="business-img-item"><div class="business-img-space"></div></div>');
                    for (var x in image600) {
//                        $('.layer-control-image').append('<img class="page" src="'+ image600[x] +'" action-id="'+ x +'">');
                        swiper.appendSlide('<div class="swiper-slide"><img class="page" src="'+ image600[x] +'" action-id="'+ x +'"></div>')
                    }

                    for (var i=0;i<5;i++) {
                        if (comment_counter >= comment_length) {
                            $('.business-loading-more').html('暂无更多评价');
                            break;
                        }
                        $('.business-comment').append('<div class="business-comment-body">' +
                            '<div class="comment-item">' +
                            '<div class="comment-user">' +
                            '<img src="'+ comments[comment_counter].head_img +'" class="circle">' +
                            '<div class="comment-user-info"><h5>'+ comments[comment_counter].nickname +'</h5><h6 score="'+ comments[comment_counter].score +'"></h6></div>' +
                            '<div class="comment-user-content">'+ comments[comment_counter].content +'</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>');
                        comment_counter++;
                    }

                    refreshUserScore();
                }
            }
        });
        //前端数据初始化---------end

        $('.business-loading-more').click(function () {
            $('.business-loading-more').children('a').html('数据正在加载...');
            for (var i=0;i<5;i++) {
                if (comment_counter >= comment_length) {
                    $('.business-loading-more').html('暂无更多评价');
                    break;
                }
                $('.business-comment').append('<div class="business-comment-body">' +
                    '<div class="comment-item">' +
                    '<div class="comment-user">' +
                    '<img src="'+ comments[comment_counter].head_img +'" class="circle">' +
                    '<div class="comment-user-info"><h5>'+ comments[comment_counter].nickname +'</h5><h6 score="'+ comments[comment_counter+i].score +'"></h6></div>' +
                    '<div class="comment-user-content">'+ comments[comment_counter].content +'</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
                comment_counter++;
            }
            refreshUserScore();
            $('.business-loading-more').children('a').html('点击加载更多评价');
        });

        $('#business_address').children('div').click(function () {
            $('#business_address_layer').show();
        });
        $('#business_address_layer').click(function () {
            $(this).hide();
        });

        $('#business_phone').children('span').click(function () {
            $('#business_phone_layer').show();
        });
        $('#business_phone_layer').click(function () {
            $(this).hide();
        });

        //商家相册的图片缩放功能---------start
        $('img[showimage]').each(function () {
            $(this).on('click', function (e) {
                var src = $(this).attr('src');
                var id = $(this).attr('image-id');
                var status = $(this).attr('status');
                swiper.slideTo(id,300,false);
                $('.image-layer').show();
            }.bind(this))
        });
        $('img[action-id]').each(function () {
            $(this).on('click', function (e) {
                $('.image-layer').hide();
            }.bind(this))
        });
        //商家相册的图片缩放功能---------end

        function refreshUserScore() {
            $('h6[score]').each(function () {
                var score = $(this).attr('score');
                $(this).html(score2SmallImg(score));
            });
        }

        //商家大评分----------start
        $('#business_score').html(score2Img($('#business_score').attr('score')));
        function score2Img(score) {
            var star = Math.round(score * 2);
            var starOne = parseInt(score);
            var starHalf = star?(parseInt(score)!==star/2):0;
            var starImg = "";
            for (var i=0;i<5;i=i+1){
                if(i<starOne) starImg = starImg + '<img class="score-star" src="{{ asset('images/business/score_star.png') }}">';
                else if( starHalf == true ) {
                    starImg = starImg + '<img class="score-star" src="{{ asset('images/business/score_star_half_o.png') }}">';
                    starHalf = false;
                }
                else starImg = starImg + '<img class="score-star" src="{{ asset('images/business/score_star_o.png') }}">';
            }
            return starImg;
        }
        //商家大评分----------end

        //用户小评分----------start
        function score2SmallImg(score) {
            var star = Math.round(score * 2);
            var starOne = parseInt(score);
            var starHalf = star?(parseInt(score)!==star/2):0;
            var starImg = "";
            for (var i=0;i<5;i=i+1){
                if(i<starOne) starImg = starImg + '<img class="small-star" src="{{ asset('images/business/score_star.png') }}">';
                else if( starHalf == true ) {
                    starImg = starImg + '<img class="small-star" src="{{ asset('images/business/score_star_half_o.png') }}">';
                    starHalf = false;
                }
                else starImg = starImg + '<img class="small-star" src="{{ asset('images/business/score_star_o.png') }}">';
            }
            return starImg;
        }
        //用户小评分----------end

        //类别后台数字对应前端文字---------start
        function typeToString(type) {
            var typeStr = '';
            if (type == 0) {
                typeStr = '餐饮娱乐类';
                $('#business_address').children('img').attr('src','/n/assets/baidu_map/baidu_map_api/icon_restaurant.png')
            } else if (type == 1) {
                typeStr = '酒店类';
                $('#business_address').children('img').attr('src','/n/assets/baidu_map/baidu_map_api/icon_hotel.png')
            } else if (type == 2) {
                typeStr = '生活出行类';
                $('#business_address').children('img').attr('src','/n/assets/baidu_map/baidu_map_api/icon_dailylife.png')
            } else if (type == 3) {
                typeStr = '运动健康类';
                $('#business_address').children('img').attr('src','/n/assets/baidu_map/baidu_map_api/icon_fitness.png')
            }

            return typeStr;
        }
        //类别后台数字对应前端文字---------end


        //评论----------start
        $("#commentControl").on("click", function (e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route('wechat.business.comment', ['business' => $business->id]) }}',
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        alert(data.message);
                    } else {
                        self.location = data.data.redirect
                    }
                },
                error: function (err) {
                    alert(err);
                }
            })
        });
        //评论----------end

        function isEmpty(e)
        {
            if (typeof(e) == 'undefined' ) {
                return true;
            } else if (typeof(e) != "object" ) {
                return true;
            } else if (!e) {
                return true;
            }

            return false;
        }

        //        function onshare(action_type_prefix,shareType)
        //        {
        //        }

        function setShare(title)
        {
            var desc= $('#business_discount').html();
            var link= window.location.href;
            var type='link';
            var imgUrl= $('.business-poster').children('img').attr('src');
            var dataUrl='';
            wx.onMenuShareTimeline({
                title: title, // 分享标题
                link: link, // 分享链接
                imgUrl: imgUrl, // 分享图标
                success: function () {
//                    onshare("redHat","Timeline");
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
//                    onshare("redHat","AppMessage");
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
//                    onshare("redHat","ShareQQ");
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        }
        wx.config(<?php echo $js->config(array('onMenuShareQQ','onMenuShareAppMessage', 'onMenuShareTimeline'), false) ?>);
        wx.ready(function(){
            setShare(share_title);
        });
    </script>
@endsection