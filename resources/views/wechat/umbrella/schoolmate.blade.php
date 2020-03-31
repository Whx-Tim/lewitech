@extends('layouts.wechat')

@section('title', '深大校友认证')

@section('css')
    <style>
        
    </style>
@endsection

@section('content')
    <section class="container">
        <form>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">真实姓名</label></div>
                <div class="weui-cell__bd">
                    <input type="text" class="weui-input" name="name" placeholder="请输入真实姓名">
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">学号</label></div>
                <div class="weui-cell__bd">
                    <input type="text" class="weui-input" name="student_number" placeholder="入学学号">
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">入学年份</label></div>
                <div class="weui-cell__bd">
                    <input type="text" class="weui-input" name="grade" placeholder="请输入入学年份">
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">学院</label></div>
                <div class="weui-cell__bd">
                    <input type="text" class="weui-input" name="college" placeholder="请输入学院全称">
                </div>
            </div>
            <div class="weui-btn-area">
                <button type="button" class="weui-btn weui-btn_primary" id="pass-btn">认证</button>
            </div>
        </form>
    </section>
@endsection

@section('javascript')
    <script>
        $('#pass-btn').click(function () {
            var formData = $('form').serialize();
            $.ajax({
                url: '{{ route('wechat.umbrella.schoolmate') }}',
                type: 'post',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.code) {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
                        window.location.href = data.data.redirect;
                    }
                }
            })
        })
    </script>
@endsection