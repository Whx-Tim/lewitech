<!DOCTYPE html>
<!-- saved from url=(0116)http://notice.woai662.net/gk/appLogic2.php?type=trigger&media_id=gh_c9c0ca5481f7&from=singlemessage&isappinstalled=0 -->
<html lang="zh-CN"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="renderer" content="webkit">
    <meta name="apple-mobile-web-app-title" content="封面新闻">
    <meta name="format-detection" content="telephone=no">
    <script src="/heightExam/files/hm.js"></script><script src="/heightExam/files/stats.js" name="MTAH5" sid="500351748"></script><script src="/heightExam/files/stats.js(1)" id="WXH5" sid="500351748"></script>
    <script src="/heightExam/files/jweixin-1.0.0.js"> </script>
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?facbaebed7110b9d9462576d344bd9de";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <title>穿越回去重新高考</title>
    <link rel="stylesheet" href="/heightExam/files/common.css">
    <link rel="stylesheet" href="/heightExam/files/index.css">
</head>
<body>
<div id="startload" style="height: 100%; width: 100%; background: rgb(254, 254, 254); position: fixed; z-index: 999; text-align: center; padding-top: 150px; display: none;">
    <img src="/heightExam/files/load.gif" width="150px">
</div>
<div class="itembox ani">
    <div class="firstPage item" style="height: 874px;">
        <div class="words">
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                那一年的我
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                会解三角函数
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                能画大气环流图
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                看得懂电路图
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                会背化学元素周期表
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                了解世界各国的历史
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                而如今的我
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                除了玩手机
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                好像什么都不会了
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                好想穿越回当年
            </div>
            <div class="txt" style="opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
                再做一回牛逼的自己！
            </div>
        </div>
        <img class="man" src="/heightExam/files/man.png" alt="" style="opacity: 1;"><img id="backToschool" src="/heightExam/files/back-btn.png" class="imgBtn back-btn" alt="" style="opacity: 0.682696;">
    </div>
    <div class="ready item" style="display: none; height: 874px;">
        <div class="ready-txt">
            <div class="txt">
                现在离考试开始还有半个小时
            </div>
            <div class="txt">
                请各位同学尽快进入教室准备考试
            </div>
        </div>
        <img src="/heightExam/files/gif1.gif" class="ling" alt=""><img src="/heightExam/files/ready-btn.png" class="imgBtn ready-btn" id="ready-btn" alt="">
        <audio src="media/ling.mp3" preload="preload" id="msgaudio"></audio>
    </div>
    <div class="startExam item" style="display: none; height: 874px;">
        <img src="/heightExam/files/time.png" class="time" alt="">
        <div class="startTxt">
            <img src="/heightExam/files/start-txt.png" alt="">
        </div>
        <img src="/heightExam/files/examMan.png" class="examMan" alt=""><img src="/heightExam/files/start-btn.png" class="imgBtn start-btn" id="start-btn" alt="">
    </div>
    <div class="answer item" style="display: none; height: 874px;">
        <div class="answerbox">
            <div class="question">
            </div>
            <div class="options">
                <ul>
                    <li class="a"><a href="javascript:;" val="A" class="select-icon btn-select"><span class="bg"></span></a><span class="selecttxt"></span></li>
                    <li class="b"><a href="javascript:;" val="B" class="select-icon btn-select"><span class="bg"></span></a><span class="selecttxt">饮鸩止渴</span></li>
                    <li class="c"><a href="javascript:;" val="C" class="select-icon btn-select"><span class="bg"></span></a><span class="selecttxt">娇柔造作</span></li>
                    <li class="d"><a href="javascript:;" val="D" class="select-icon btn-select"><span class="bg"></span></a><span class="selecttxt">娇柔造作</span></li>
                </ul>
            </div>
            <div class="hand" style="width: 286px; height: 171px;">
                <div class="realy_hand">
                </div>
            </div>
        </div>
    </div>
    <div class="result item" style="display: none; height: 874px;">
        <div class="resultTxt">
            你的网络高考的成绩是<br>
            <span class="score" id="score"></span>
        </div>
        <div class="desc">
            <img src="/heightExam/files/desc-bg.png" alt="">
            <div class="desc-txt">
                80-90分：那一年的我，看得懂文言文；会解三角函数；能画大气环流图；看得懂电路图；会背化学元素周期表⋯⋯如今再战，功力还是不减当年。
            </div>
        </div>
        <img src="/heightExam/files/again-btn.png" class="imgBtn again-btn" id="again-btn" alt="">

    </div>
</div>
<div class="sharearr">
    <img src="/heightExam/files/page5_text2.png">
</div>



<section class="wx-line-card-copy" onclick="javascript: document.getElementById(&#39;wx-line-card-qr&#39;).style.display=&#39;block&#39;;">
    <i class="wx-line-card-copy-right-icon"></i><span id="wx-line-card-open-name">校友共享圈</span><i class="wx-line-card-qr-icon"></i>
</section>
<section class="wx-line-card-qr-content" id="wx-line-card-qr" onclick="javascript: document.getElementById(&#39;wx-line-card-qr&#39;).style.display=&#39;none&#39;;">
    <div class="wx-line-card-qr-box" id="wx-line-card-qr-box">
        <img id="wx-line-open-qr" class="wx-line-open-qr" src="/heightExam/files/saved_resource">
        <p>长按二维码，关注公众号</p>
    </div>
</section>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script>
    window.onload=function(){document.getElementById("startload").style.display="none"}
    var url="http://wx.lewitech.cn/exam";
    var title="一起来穿越回去重新高考";
    var desc="";
    var headimgurl="http://wx.lewitech.cn/img/logo.png";

    wx.config(<?php echo $js->config(array('onMenuShareQQ','onMenuShareAppMessage', 'onMenuShareTimeline'), false) ?>);
    wx.ready(function () {
        setShare(title, desc, url, headimgurl);
    });

    function setShare(title,desc,link,imgUrl)
    {
        console.log(title);
        console.log(desc);
        console.log(link);
        console.log(imgUrl);
        var type='link';
        var dataUrl='';

        wx.onMenuShareTimeline({
            title: title, // 分享标题
            link: link, // 分享链接
            imgUrl: imgUrl, // 分享图标
            success: function () {
                // onshare("badge","Timeline");
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
                // onshare("badge","AppMessage");
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
                // onshare("badge","ShareQQ");
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    }

//    var appId='wx7254b129dd96523e';
//    var timestamp='1496818766';
//    var nonceStr='IsFFI3taxNne1u7P';
//    var signature='b04c11da8d5bb425b25755e8c399184873281dae';



</script>
<script src="/heightExam/files/data.js"></script>

<script src="/heightExam/files/TweenMax.min.js"></script>
<script src="/heightExam/files/common.js"></script>
<script src="/heightExam/files/index.min.js"></script>
<script>
    COVER.openapi.weixin.wx_share({
        title: title,
        link: url,
        imgUrl: headimgurl
//        desc: "我的网络高考成绩是"+document.getElementById('score').innerHTML+"你可以考多少分呢？"
    })
</script>


</body></html>