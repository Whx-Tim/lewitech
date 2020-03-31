@extends('layouts.wechat')

@section('title', '乐微互助')

@section('css')
    <style>
        html, body {
            font-family: 'Microsoft YaHei', sans-serif;
            width: 100%;
        }

        section.container {
            width: 100%;
            padding-bottom: 10vh;
            background-color: #e3ebec;
        }

        .banner {
            position: relative;
            box-sizing: border-box;
            height: auto;
        }

        .banner > img {
            width: 100%;
            display: block;
        }
        .banner > img:after {
            clear: both;
        }

        .banner > .banner-mask {
            position: absolute;
            width: 100%;
            min-height: 10vh;
            bottom: 0;
            left: 0;
            background-color: rgba(0,0,0, .4);
            color: #ffffff;
            display: flex;
            flex-direction: row;
            padding-top: 5px;
        }

        .banner-mask > .mask-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .mask-item > .mask-item-item {
            flex: 1;
        }

        .mask-item-item > img {
            height: 20px;
        }
        .mask-item-item:nth-of-type(2) {
            color: #ffffff;
            font-weight: bolder;
            font-size: 16px;
        }
        .mask-item-item:nth-of-type(3) {
            color: #a3e8fe;
            font-size: 11px;
            font-weight: bolder;
        }

        .box {
            position: relative;
            width: 100vw;
        }
        .box > img {
            width: 100%;
            display: block;
        }

        .why-content {
            position: absolute;
            width: 86vw;
            left: 7vw;
            top: 10px;
            padding: 5px;
            background-color: #ffffff;
            box-sizing: border-box;
            box-shadow: 0 1px 8px 1px #a3e8fe;
            border-radius: 10px;
        }
        .why-content > h3 > img {
            width: 17px;
            vertical-align: text-top;
        }
        .why-content > h3 {
            font-size: 15px;
        }
        .why-content > h4 {
            font-size: 13px;
            color: #a3e8fe;
        }
        .why-content > p {
            text-indent: 2em;
            font-size: 11px;
        }

        .regulation-box,
        .question-box {
            background-color: #e3ebec;
            padding: 10px;
            box-sizing: border-box;
        }
        .regulation-content,
        .question-content {
            background-color: #ffffff;
            padding: 10px;
            padding-bottom: 20px;
            border-radius: 5px;
        }
        .regulation-content > h3,
        .question-content > h3{
            font-size: 17px;
            margin-bottom: 10px;
        }
        .regulation-content > h3 > img,
        .question-content > h3 > img{
            width: 17px;
            vertical-align: sub;
        }
        .regulation-content > p{
            line-height: 22px;
            font-size: 12px;
            counter-increment: regulation;
        }
        .regulation-content > p:before {
            content: counter(regulation)". ";
        }

        .progress-content {
            position: absolute;
            width: 86vw;
            top: 5px;
            left: 7vw;
            background-color: #ffffff;
            padding: 5px;
            border-radius: 5px;
        }
        .progress-content > h3 {
            font-size: 16px;
        }
        .progress-content > h3 > img {
            width: 18px;
            vertical-align: middle;
        }
        .progress-step {
            position: relative;
        }
        .progress-step:nth-of-type(1):before {
            height: calc(100% - 4px);
            top: 4px;
        }
        .progress-step:before {
            position: absolute;
            height: 100%;
            width: 2px;
            content: '';
            background-color: #b5b5b6;
            top: 0;
            left: 9px;
        }
        .progress-step > h4,
        .progress-step > p {
            padding-left: 25px;
            font-size: 13px;
        }
        .progress-step > h4 {
            position: relative;
        }
        .progress-step > h4:after {
            position: absolute;
            content: '';
            top: 6px;
            left: 6px;
            width: 8px;
            height: 8px;
            background-color: #7091de;
            border-radius: 50%;
        }
        .progress-step > h4:before {
            position: absolute;
            content: '';
            top: 4px;
            left: 4px;
            width: 12px;
            height: 12px;
            background-color: #c5e4f3;
            border-radius: 50%;
        }

        .question-item > p {
            background-color: #ececec;
            font-size: 13px;
            line-height: 20px;
            padding: 5px 0;
            display: none;
        }
        .question-item > p > span {
            display: block;
            line-height: 20px;
            counter-increment: question-span;
        }
        .question-item > p > span:before {
            content: counter(question-span)". ";
        }
        .question-item > h4 {
            font-size: 14px;
            color: #38bbce;
            font-weight: bolder;
        }
        .question-item {
            counter-increment: question;
            margin-bottom: 10px;
        }
        .question-title:before {
            content: "Q"counter(question)" : ";
        }

        .footer-btn-area {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100vw;
            min-height: 10vh;
            box-sizing: border-box;
            padding: 20px 40px;
            background-color: #e3ebec;
        }
        .btn-apply {
            width: 100%;
            background-color: #3dc9c0;
            color: #ffffff;
            font-weight: bolder;
            text-align: center;
            padding: 10px 0;
            border: none;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
        }

        .question-title {
            clear: both;
        }
        .question-title > small {
            float: right;
            transition: .3s;
        }
        .question-title > small > img {
            width: 20px;
        }
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(180deg);
            }
        }
        .question-title > small.active > img {
            transform: rotate(180deg);
            animation: .3s rotate linear;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="banner">
            <div class="banner-mask">
                <div class="mask-item">
                    <div class="mask-item-item"><img src="{{ asset('images/help/icon-money.png') }}"></div>
                    <div class="mask-item-item">总金额（元）</div>
                    <div class="mask-item-item">{{ $total_money }}元</div>
                </div>
                <div class="mask-item">
                    <div class="mask-item-item"><img src="{{ asset('images/help/icon-users.png') }}"></div>
                    <div class="mask-item-item">已加入人数</div>
                    <div class="mask-item-item">{{ $total_amount }}人</div>
                </div>
                <div class="mask-item">
                    <div class="mask-item-item"><img src="{{ asset('images/help/icon-status.png') }}"></div>
                    <div class="mask-item-item">我的状态</div>
                    <div class="mask-item-item">{{ $vip }}</div>
                </div>
            </div>
            <img src="{{ asset('images/help/banner.png') }}">
        </div>
        <div class="box why-box">
            <img src="{{ asset('images/help/bg-1.png') }}">
            <div class="why-content">
                <h3><img src="{{ asset('images/help/icon-question-rectangle.png') }}">&nbsp;什么是乐微重病互助</h3>
                <h4>一人患病，万人帮助！</h4>
                <p>每位用户通过捐赠10元的方式，获取互助资格，在将来有需要的时候，能申请使用重病互助金治疗。同时，乐微科技（深圳）有限公司大力支持本项目，公司每增加一名股东，就会捐赠1000元给重病互助基金</p>
            </div>
        </div>
        <div class="box regulation-box">
            <div class="regulation-content">
                <h3><img src="{{ asset('images/help/icon-regulation.png') }}">&nbsp;相关规则</h3>
                <p>成为乐微科技（深圳）有限公司股东后，在本页面捐赠10元，即可加入；</p>
                <p>发生互助行为时，所有用户的捐赠金额都将由平台统一调配</p>
                <p>加入本互助行动时，需无重大疾病史及相关症状就诊记录，无慢性病史；</p>
                <p>会员对其他患病会员的分摊是一种单向赠予行为，尽管存在会员公约等约束机制，但并不能预期获得确定的风险保障。</p>
            </div>
        </div>
        <div class="box progress-box">
            <img src="{{ asset('images/help/bg-2.png') }}">
            <div class="progress-content">
                <h3><img src="{{ asset('images/help/icon-progress.png') }}">&nbsp;互助申请流程</h3>
                <div class="progress-step">
                    <h4>准备材料</h4>
                    <p>申请材料具体详见《常见问题Q5》；</p>
                </div>
                <div class="progress-step">
                    <h4>申请互助</h4>
                    <p>拨打客服热线26533151，提交相关资料；</p>
                </div>
                <div class="progress-step">
                    <h4>事件调查</h4>
                    <p>平台初审并交付第三方权威调查机构调查；</p>
                </div>
                <div class="progress-step">
                    <h4>事件公示</h4>
                    <p>公示期内接受所有互助会员拨打客服电话对事件进行合理治理；</p>
                </div>
                <div class="progress-step">
                    <h4>资金划款</h4>
                    <p>公示无异议后，平台直接划款</p>
                </div>
            </div>
        </div>
        <div class="box question-box">
            <div class="question-content">
                <h3><img src="{{ asset('images/help/icon-question-circle.png') }}">&nbsp;常见问题</h3>
                <div class="question-item">
                    <h4 class="question-title">什么是重病互助行动？ <small><img src="{{ asset('images/help/icon-angle.png') }}"></small></h4>
                    <p>互助行动是一种全新的健康互助机制，用户只需支付10元即可加入，经过180天的观察期后即可获得相应的健康互助资格。无病时帮助他人，患病时人人助我。</p>
                </div>
                <div class="question-item">
                    <h4 class="question-title">什么是观察期？<small><img src="{{ asset('images/help/icon-angle.png') }}"></small></h4>
                    <p>又称等待期或免责期，是为了防止参与行动会员明知道或可能知道已确诊或即将确诊互助行动互助范围内的疾病，而马上加入以获得利益的行为</p>
                </div>
                <div class="question-item">
                    <h4 class="question-title">什么人可以加入互助行动？<small><img src="{{ asset('images/help/icon-angle.png') }}"></small></h4>
                    <p>身体健康的乐微科技（深圳）有限公司股东。</p>
                </div>
                <div class="question-item">
                    <h4 class="question-title">什么情况下可以获得救助？<small><img src="{{ asset('images/help/icon-angle.png') }}"></small></h4>
                    <p>
                        <span>罹患重大疾病；</span>
                        <span>罹患重病的为本行动的合格会员，且已度过观察期；</span>
                        <span>患病认定后（未身故情况下）30天内发起互助申请，其中患病认定指：经二级甲等及以上医院专科医生首次确诊罹患本行动规定的重大疾病；</span>
                        <span>互助申请人必须为互助会员本人。</span>
                    </p>
                </div>
                <div class="question-item">
                    <h4 class="question-title">申请互助需要准备什么材料？<small><img src="{{ asset('images/help/icon-angle.png') }}"></small></h4>
                    <p>
                        <span>互助会员生活照一张；</span>
                        <span>互助会员身份证正反面照片各一张；</span>
                        <span>如申请人不具有完全民事行为能力，需委托他人代为申请，需提交《授权委托书》原件、委托人和受托人的身份证明等相关证明文件；</span>
                        <span>二级甲等及以上医院专科医生出具的病历，病理检验报告，血液检查报告及其他科学诊断报告，附有病理学检查结果的临床诊断证明文件；</span>
                        <span>二级甲等及以上医院专科医生出具的门诊病历</span>
                    </p>
                </div>
                <div class="question-item">
                    <h4 class="question-title">乐微重病互助是保险吗？<small><img src="{{ asset('images/help/icon-angle.png') }}"></small></h4>
                    <p>乐微重病互助不是保险，而是会员之间相互帮助的公益行动，无病时帮助他人，患病时人人助我。乐微重病互助不承诺任何给付补偿责任。</p>
                </div>
            </div>
        </div>
        <div class="footer-btn-area">
            <button type="button" class="btn btn-apply">捐赠十元立即加入</button>
        </div>
    </section>
@endsection

@section('javascript')
    <script>
        $('.btn-apply').on('click', function () {
            window.location.href = '{{ route('wechat.help.apply') }}';
        });
        var first_click = true;
        $('.question-title').children('small').on('touchstart', function () {
            var $this = $(this);
            $this.addClass('active');
            $this.parent().next().fadeIn(300);
        });
        $('.question-title').delegate('small.active', 'touchstart', function () {
            var $this = $(this);
            $this.one('touchstart', function () {
                $this.removeClass('active');
                $this.parent().next().fadeOut(300);
            })
        });
        @if(session('error_message'))
            toastr.warning('{{ session('error_message') }}');
        @endif
    </script>
@endsection