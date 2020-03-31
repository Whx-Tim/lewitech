@extends('layouts.business')

@section('title', '校企优惠详情')

@section('css')
    <style>
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

        .business-poster-facing {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 99;
            width: 100%;
            opacity: .85;
            color: #ffffff;
            background-color: #000000;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .facing-title {
            font-family: Microsoft YaHei, sans-serif;
            font-weight: bold;
            font-size: 18pt;
            padding-top: 6pt;
            padding-left: 19pt;
            padding-right: 19pt;
        }

        .facing-item {
            display: inline-block;
            margin-left: 19pt;
            padding-bottom: 3pt;
            font-size: 12pt;
        }

        #business_type,
        #business_price,
        #business_score,
        #business_comment_count{
            display: inline-block;
        }

        #business_type span,
        #business_price span{
            color: #b9b9b9;
        }

        .business-item {
            border-bottom: 4pt solid #f2f2f2;
        }

        .business-item .business-item-body {
            padding: 4pt 19pt;
        }

        .business-item h4 {
            display: block;
            padding: 4.5pt 19pt;
            color: #f4568b;
            border-bottom: 1px solid #dedede;
            font-size: 12pt;
        }

        .score-star {
            margin-top: 4pt;
            height: 14pt;
            width: 14pt;
            padding-right: 4pt;
            vertical-align: sub;
        }
    </style>
@endsection

@section('content')
    <div class="business-poster">
        <img src="{{ $poster or asset('images/no-avatar.png') }}" alt="商家海报">
    </div>

    <div class="business-poster-facing">
        <div class="facing">
            <h4 class="facing-title">{{ $business->name }}</h4>
            <div class="facing-item">
                <div id="business_score" score="{{ $business->score }}"></div>
                {{--<div id="business_comment_count"></div>--}}
                <div id="business_price">{{ $business->price }}</div>
                <div id="business_type">{{ $business->type2string() }}</div>
            </div>
        </div>
    </div>

    <div class="business-item">
        <h4>校企优惠折扣详情</h4>
        <div class="business-item-body">
            {!! $business->detail !!}
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/plugins/jquery/3.2.1/jquery.min.js') }}"></script>
    <script>

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
    </script>
@endsection