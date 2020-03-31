@extends('layouts.wechat')

@section('title', '校友圈用户活动报名')

@section('css')
    <style>
        section.container {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            background-image: url("/images/temp/background.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            color: #ffffff;
            padding-bottom: 100px;
        }

        section h2 {
            text-align: center;
        }

        section p {
            margin: 2px;
            padding: 5px;
        }

        .apply-btn {
            display: block;
            padding: 15px;
            font-size: 20px;
            background-color: #73c9fd;
            color: #ffffff;
            font-weight: bold;
            border: none;
            border-radius: 15px;
            box-sizing: border-box;
            box-shadow: 0 3px 2px 0 rgb(150,150,150);
            width: 100%;
            margin-top: 40px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <h2>乐微科技六月校友圈用户活动</h2><br>
        <p><b>主题:</b>乐微科技大学毕业典礼</p>
        <p><b>时间:</b>2017年6月25日14点-17点</p>
        <p><b>地点:</b>深圳大学科技楼3号报告厅</p>
        <p><b>流程:</b>
            一、乐微科技公司早起打卡功能介绍即本月早起之星颁奖典礼；<br>
            （1）14:00-14:10，主持人维护会场秩序，并组织开场；<br>
            （2）14:10-14:40，总经理林文丽做早起打卡功能介绍；<br>
            （3）14:40-15:00，总经理林文丽为本月早起打卡之星颁奖。<br>
            二、乐微校友圈用户毕业典礼；<br>
            （1）15:00-15:40，由主持人分别宣读毕业生名字，总经理林文丽颁发毕业证书，毕业生发表简单的感言；<br>
            （2）15:40-16:00，切蛋糕，庆祝毕业，并给6月生日的校友圈用户送上生日礼物。<br>
            三、户外拍摄环节。<br>
            （1）16:00-16:30，组织校友圈用户手持乐微宣传物料，进行单人毕业照拍摄，航拍团队跟拍花絮；<br>
            （2）16:35-16:50，校友圈用户自由合照，航拍团队跟拍花絮；<br>
            （3）16:50-17:00，组织拍摄大合照。<br>
        </p>
        <form>
            {!! csrf_field() !!}
            <div class="weui-cells weui-cells_form">
                <div class="weui-cell" style="color: black">
                    <div class="weui-cell__hd"><label class="weui-label">姓名</label></div>
                    <div class="weui-cell__bd">
                        <input type="text" class="weui-input" name="name" placeholder="请输入您的姓名">
                    </div>
                </div>
                <div class="weui-cell" style="color: black">
                    <div class="weui-cell__hd"><label class="weui-label">手机</label></div>
                    <div class="weui-cell__bd">
                        <input type="number" class="weui-input" name="phone" placeholder="请输入您的手机号码">
                    </div>
                </div>
                <div class="weui-cell weui-cell_switch">
                    <div class="weui-cell__bd" style="color: black">是否6月生日</div>
                    <div class="weui-cell__ft">
                        <input type="checkbox" class="weui-switch" onchange="birthday()">
                    </div>
                </div>
                <div class="weui-cell weui-cell_switch">
                    <div class="weui-cell__bd" style="color: black">是否有朋友一起到来</div>
                    <div class="weui-cell__ft">
                        <input type="checkbox" class="weui-switch" onchange="haveFriend()">
                    </div>
                </div>
                <div class="weui-cell" style="color: black; display: none" id="friend">
                    <div class="weui-cell__hd"><label class="weui-label">朋友人数</label></div>
                    <div class="weui-cell__bd">
                        <input type="number" class="weui-input" name="friend" placeholder="请输入与多少个朋友一起来">
                    </div>
                </div>
            </div>
            <input type="hidden" name="is_birthday" value="0">
            <input type="hidden" name="is_friend" value="0">
        </form>
        <button type="button" class="apply-btn">报名</button>
    </section>
@endsection

@section('javascript')
    <script>

        function birthday()
        {
            var $this = $('[name=is_birthday]');
            var val = $this.val();
            if (val == '1') {
                $this.val('0');
            } else {
                $this.val('1');
            }
            console.log($this.val());
        }

        function haveFriend()
        {
            var $this = $('[name=is_friend]');
            var val = $this.val();
            if (val == '1') {
                $this.val('0');
                $('#friend').hide(300);
            } else {
                $this.val('1');
                $('#friend').show(300);
            }
            console.log($this.val());
        }
        //        $('input[type=checkbox]').each(function () {
        //            $(this).change(function () {
        //
        //            })
        //        });

        $('.apply-btn').click(function () {
            if ($('[name=is_friend]').val() != '0') {
                if ($('[name=friend]').val() == '') {
                    toastr.error('请输入一起来的朋友数');
                    return ;
                }
            }
            var formData = $('form').serialize();
            $.ajax({
                url: '{{ route('wechat.temp.apply', ['type' => 'active804']) }}',
                data: formData,
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
                    }
                }
            })
        })
    </script>
@endsection