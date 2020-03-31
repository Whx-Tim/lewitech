@extends('layouts.wechat')

@section('title', '发布活动')

@section('css')
    <link rel="stylesheet" href="{{ url('css/active/publish.css') }}">
    <link rel="stylesheet" href="{{ url('plugins/weui/weui.css') }}">
@endsection

@section('content')
    <section class="container">
        <form id="fileForm">
            <div class="upload">
                <input type="file" id="file" name="file" class="file_upload" accept="image/*">
                <img src="/images/active/camera.png">
                <div class="upload-text">点击上传活动的封面照片</div>
                {{--<input type="hidden" name="type" value="">--}}
                <input type="hidden" name="token" value="{{ $token }}">
            </div>
        </form>
        <form id="mainForm">
            <div class="input-group"><label>活动标题</label><input type="text" name="name" placeholder="活动标题"></div>
            <div class="input-group"><label>开始时间</label><input type="datetime-local" name="start_time" placeholder="活动时间"  class="weui-input" readonly></div>
            <div class="input-group"><label>结束时间</label><input type="datetime-local" name="end_time" placeholder="活动时间" class="weui-input" readonly></div>
            <div class="input-group"><label>截止时间</label><input type="datetime-local" name="end_at" placeholder="活动时间" class="weui-input" readonly></div>
            <div class="input-group"><label>主办方</label><input type="text" name="sponsor" placeholder="主办方"></div>
            <div class="input-group"><label>联系电话</label><input type="number" name="phone" placeholder="联系电话"></div>
            <div class="input-group"><label>活动地点</label><input type="text" name="location" placeholder="活动地点"></div>
            {{--<input type="text" name="apply_money" placeholder="报名费用">--}}
            <div class="input-group"><label>限制人数</label><input type="number" name="persons" placeholder="限制人数"></div>
            <div class="input-group"><label>活动介绍</label><textarea name="description" rows="5" placeholder="活动介绍"></textarea></div>
            <input type="hidden" name="poster" value="">
            <input type="hidden" name="lng" value="">
            <input type="hidden" name="lat" value="">
        </form>
        <div class="footer-btn">
            <button type="button" class="publish-btn">发布</button>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="{{ url('plugins/weui/weui.js') }}"></script>
    <script>
        $('.publish-btn').click(function () {
            var formData = $('#mainForm').serialize();
            $.ajax({
                url: '{{ url('wechat/active/publish') }}',
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
                        window.location.href = '/wechat/active/list';
                    }
                },
                error: function (error) {
                    if (error.status == 422) {
                        var errData = JSON.parse(error.responseText);
                        var errInfo;
                        for (var i in errData) {
                            errInfo = errData[i][0];
                        }
                        toastr.error(errInfo);
                    }
                }
            })
        });

        String.prototype.replaceAll = function (s1, s2) {
            return this.replace(new RegExp(s1, 'gm'), s2);
        };

        function closeImg()
        {
            $('.facing-img').hide();
            $('.close-btn').hide();
            $('.facing-img').remove();
            $('.close-btn').remove();
            $('[name=poster]').val('');
        }

        document.getElementById('file').onchange = function () {
            doUpload()
        };
        function doUpload() {
            var formData = new FormData($( "#fileForm" )[0]);
            $('.upload-text').html("上传图片(提交中..)");
            $.ajax({
                url: 'http://up-z2.qiniu.com/' ,
                type: 'POST',
                data: formData,
                // async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('.upload-text').html("点击上传活动的封面照片");
                    if (data.errcode){
                        alert(data.errmsg);
                    }else{
                        $('[name=poster]').val(data.key);
                        $('.upload').append('<img src="http://wj.qn.h-hy.com/'+ data.key +'" class="facing-img"><div class="close-btn" onclick="closeImg()">×</div>');

                    }
                },
                error: function (returndata) {
                    $('#upload_title').html("上传图片");
                    alert('上传失败');
                    console.log(returndata);
                }
            });
        }
    </script>
@endsection