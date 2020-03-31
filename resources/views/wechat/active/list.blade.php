@extends('layouts.wechat')

@section('title', '活动列表')

@section('css')
    <link rel="stylesheet" href="{{ url('css/active/list.css') }}">
    <link rel="stylesheet" href="{{ url('css/plugins/swiper/swiper.min.css') }}">
@endsection

@section('content')
    <section class="container">
        <div class="banner">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><a href="http://mp.weixin.qq.com/s/QU7xdkAiLGQDIzNPvlPQLQ"><img src="/images/active/banner1.png"></a></div>
                    <div class="swiper-slide"><a href="https://mp.weixin.qq.com/s/zTrlfDQ3TCValZeWBACbFQ"><img src="/images/active/banner2.png"></a></div>
                    <div class="swiper-slide"><img src="/images/active/banner3.png"></div>
                    <div class="swiper-slide"><img src="/images/active/banner4.png"></div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <div class="search">
            <input type="text" name="search" placeholder="请输入关键字" id="search">
            <span class="search-btn">
                <img src="/images/active/search.png" alt="">
                搜索
            </span>
        </div>
        <ul class="list">
            {{--<li>--}}
                {{--<div class="list-top">--}}
                    {{--<div class="active-poster"><img src="/images/no-avatar.png"></div>--}}
                    {{--<div class="active-info">--}}
                        {{--<h4 class="active-name">动物解剖研究活动</h4>--}}
                        {{--<p class="active-phone">13418866733</p>--}}
                        {{--<p class="active-address">环城路6号</p>--}}
                        {{--<p class="active-count"><span class="view-count">111</span><span class="apply-count">222</span></p>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="list-bottom">--}}
                    {{--<p>主办方<span class="active-sponsor">深圳大学</span></p>--}}
                {{--</div>--}}
            {{--</li>--}}
        </ul>
        <button type="button" class="publish-btn">发起<br>活动</button>
    </section>
@endsection

@section('javascript')
    <script src="{{ url('/js/plugins/swiper/swiper.min.js') }}"></script>
    <script>
        var mySwiper = new Swiper('.swiper-container',{
            autoplay: 3000,
            autoplayDisableOnInteraction: false,
            freeMode: false,
            pagination: ".swiper-pagination",
            loop: true,
            height: 300
//            width: window.innerWidth
        });

        var loading = true;
        var list_url = '{{ url('wechat/active/getList?page=1&per_page=1') }}';
        function getActive()
        {
            if (loading) {
                loading = false;
            } else {
                return ;
            }
            if (list_url == null) return;
            $.ajax({
                url: list_url,
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        var actives = data.data.actives.data;
                        list_url = data.data.actives.next_page_url;
                        for (var i in actives) {
                            var html = '<li onclick="detail('+ actives[i].id +')">\
                                        <div class="list-top">\
                                        <div class="active-poster"><img src="http://wj.qn.h-hy.com/'+ actives[i].poster +'"></div>\
                                            <div class="active-info">\
                                                <h4 class="active-name">'+ actives[i].name +'</h4>\
                                                <p class="active-phone">'+ actives[i].phone +'</p>\
                                                <p class="active-address">'+ actives[i].location +'</p>\
                                                <p class="active-count"><span class="view-count">'+ actives[i].view.count +'</span><span class="apply-count">'+ actives[i].enrolls_count +'</span></p>\
                                            </div>\
                                        </div>\
                                        <div class="list-bottom">\
                                            <p>主办方<span class="active-sponsor">'+ actives[i].sponsor +'</span></p>\
                                        </div>\
                                    </li>';
                            $('ul.list').append(html);
                        }
                        console.log(data);
                    }
                    loading = true;
                },
                error: function (error) {
                    console.log(error.responseText);
                    loading=true;
                }
            });
        }

        $('.search-btn').click(function () {
            loading = false;
            $.ajax({
                url: '{{ url('wechat/active/search') }}',
                type: 'get',
                data: {
                    'search': $('#search').val()
                },
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        var actives = data.data.actives;
                        $('ul.list').html('');
                        for (var i in actives) {
                            var html = '<li onclick="detail('+ actives[i].id +')">\
                                        <div class="list-top">\
                                        <div class="active-poster"><img src="http://wj.qn.h-hy.com/'+ actives[i].poster +'"></div>\
                                            <div class="active-info">\
                                                <h4 class="active-name">'+ actives[i].name +'</h4>\
                                                <p class="active-phone">'+ actives[i].phone +'</p>\
                                                <p class="active-address">'+ actives[i].location +'</p>\
                                                <p class="active-count"><span class="view-count">'+ actives[i].view.count +'</span><span class="apply-count">'+ actives[i].enrolls_count +'</span></p>\
                                            </div>\
                                        </div>\
                                        <div class="list-bottom">\
                                            <p>主办方<span class="active-sponsor">'+ actives[i].sponsor +'</span></p>\
                                        </div>\
                                    </li>';
                            $('ul.list').append(html);
                        }
                    }
                },
                error: function (err) {
                    toastr.error('搜索失败');
                    console.log(err.responseText);
                }
            });
        });


        function detail(id) {
            window.location.href = '/wechat/active/detail/'+id;
        }

        $('.publish-btn').click(function () {
            window.location.href = '{{ url('wechat/active/publish') }}';
        });

        $(window).scroll(function () {
            var scrollTop = $(this).scrollTop();
            var scrollHeight = $(document).height();
            var windowHeight = $(this).height();
            // console.log("scrollTop="+scrollTop);
            // console.log("scrollHeight="+scrollHeight);
            // console.log("windowHeight="+windowHeight);
            if (scrollTop + windowHeight >= scrollHeight ) {
                getActive();
            }
        });
        getActive();
    </script>
@endsection