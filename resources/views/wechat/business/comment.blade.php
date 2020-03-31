<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>评论</title>
    <link href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: Microsoft Yahei, sans-serif;
        }

        body, html{
            width: 100%;
            height: 100%;
        }

        .business-poster{
            position: relative;
            width: 100%;
            height: 212pt;
        }

        .business-poster img {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .business-poster-facing {
            z-index: 99;
            width: calc(100% - 21pt);
            margin-left: 10.5pt;
            min-height: 109pt;
            margin-top: -54.5pt;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 8px rgb(200,200,200);
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .business-poster-facing .facing {
            color: #353535;
        }

        .facing-title {
            text-align: center;
            width: 100%;
            font-weight: bold;
            font-size: 18pt;
            padding-top: 6pt;
        }
        .cost-div{
            display: block;
            box-sizing: border-box;
            width: 100%;
            height: 41px;
            border-bottom: 1px solid rgb(220,220,220);
        }
        .cost-div span{
            display: inline-block;
            font-size: 14px;
            line-height: 13px;
            height: 14px;
            margin: 13px 7pt 13px 20pt;
            color: #909090;
        }
        .cost-div i {
            font-weight: bold;
            font-style: normal;
            color: #353535;
            font-size: 20px;
            position: absolute;
            margin-top: 6px;
        }
        .cost-div input#yourCost{
            border:none;
            outline: none;
            color: #343434;
            display: inline-block;
            font-size: 20px;
            font-weight: bold;
            line-height: 13px;
            min-width: 200px;
            width:62.5%;
            position: absolute;
            margin-top: 6px;
            margin-left: 20px;
        }
        .text-div{
            display: block;
            width: 100%;
            height: 40%;
            position: relative;
        }
        .text-div span {
            /*position: absolute;*/
            /*top: 13px;*/
            /*left: 20pt;*/
            padding: 13px 0;
            margin: 0 20pt;
            border-bottom: 1px solid rgb(235,235,235);
            display: block;
            color: #909090;
            font-size: 14px;
            line-height: 13px;
            height: 14px;
        }
        .text-div #yourDescription{
            display: block;
            box-sizing: border-box;
            border:none;
            outline: none;
            color: #353535;
            font-size: 14px;
            line-height: 16px;
            width: calc(100% - 40pt);
            padding-top: 14px;
            padding-left: 14px;
            margin: 0 20pt;
            word-break: break-all;
            height: 100%;
            resize: none;
        }
        .bottom-bar{
            display: block;
            width: 100%;
            height: 65px;
            /*border-top: 1px solid #f1f1f1;*/
            /*position: fixed;*/
            bottom: 0;
        }
        .bottom-bar #submitBtn{
            display: block;
            width: calc(100% - 70pt);
            height: 28px;
            font-size: 18px;
            line-height: 27px;
            text-align: center;
            color: #FFFFFF;
            padding: 13px 0;
            background-color: #e24f81;
            border-radius: 30px;
            letter-spacing: 3px;
            margin: 10px auto;
            margin-bottom: 20px;
        }
        .bottom-bar #submitBtn:active{
            margin-top: 12px;
            box-shadow: none;
        }

        .business-score {
            width: 100%;
            text-align: center;
            margin-top: 7pt;
            color: #909090;
        }

        .business-score h4 {
            font-weight: 500;
            font-size: 16px;
            letter-spacing: 1px;
            /*word-spacing: 8px;*/
        }

        #starTouch i {
            display: inline-block;
            font-size: 52px;
            color: #EDEDED;
            margin: 7px 4px;
            /*width: 36pt;*/
            /*height: 36pt;*/
            vertical-align: top;
            /*background-image: url("/n/assets/baidu_map/baidu_map_api/score_star_o.png");*/
            /*background-repeat: no-repeat;*/
            /*background-size: 100% 100%;*/
            /*margin: 8pt 4pt;*/
        }

        .star-one {
            background-image: url("/n/assets/baidu_map/baidu_map_api/score_star.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        #commentForm {
            border-top: 1px solid rgb(220,220,220);
            margin-top: 30pt;
        }

        .star-click {
            color: #e24f81;
        }

        #star {
            text-align: center;
            padding-top: 10px;
        }

        #star img{
            width: 36pt;
            height: 36pt;
        }

        .flex-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="business-poster">
    <img src="{{ $poster or asset('images/no-avatar.png') }}" alt="商家海报">
</div>
<div class="business-poster-facing">
    <div class="facing">
        <h4 class="facing-title">{{ $business->name }}</h4>
        <div class="business-score">
            <h4>请点击星星评分</h4>
            <div class="flex-container">
                <div id="star"></div>
            </div>
        </div>
    </div>
</div>
<form id="commentForm">
    <div class="cost-div">
        <span>您的花费</span><i>￥</i><input  id="yourCost" type="number" name="money" value="">
    </div>
    <div class="text-div">
        <span>您的评论</span><textarea id="yourDescription" rows="8" name="content"></textarea>
    </div>
    <input type="hidden" name="score" value="">
</form>
<div class="bottom-bar">
    <div id="submitBtn">提交评价</div>
</div>
</body>
<script src="//cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
<script src="{{ asset('js/plugins/jquery.raty/jquery.raty.js') }}"></script>
<script>
    $('#star').raty({
        numberMax: 5,
        half: false,
        size: 18,
        width: 300,
        starOn: '{{ asset('images/business/score_star.png') }}',
        starOff: '{{ asset('images/business/score_star_o.png') }}',
        click: function (score,evt) {
            $("input[name=score]").val(score);
        }
    });

    $('#submitBtn').click(function () {
        var FormData = $("#commentForm").serialize();

        $.ajax({
            url: '{{ route('wechat.business.comment', ['business' => $business->id]) }}',
            type: 'POST',
            dataType: 'json',
            data: FormData,
            success: function (data) {
                alert(data.message);
                self.location = data.data.redirect;
            },
            error: function (err) {
                var errData = JSON.parse(err.responseText);
                for(var i in errData){
                    var errinfo = errData[i][0]
                }
                alert(errinfo);
            }
        })
    });
</script>
</html>