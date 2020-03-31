@extends('layouts.business')

@section('title','校企进驻')

@section('css')
    <link href="{{ asset('css/business/dropzone.css') }}" rel="stylesheet">
    {{--<link href="//cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">--}}
    @include('wechat.business._add_edit_css')
@endsection

@section('content')
    <div class="business-list">
        <form id="fileForm">
            <div class="business-input-item upload-file business-box-shadow">
                <div class="item-tag" style="position: static">上传海报图片</div>
                {{--<div class="img-upload-btn">+</div>--}}
                {{--<input type="file" name="file" id="file" class="file_upload" accept="image/*">--}}
                {{--<input type="hidden" name="type" value="business">--}}
                <div class="maskLayer">
                    <div id="poster-upload" class="dropzone"></div>
                </div>
                <img src="{{ asset('images/business/plus.png') }}" alt="上传图片" class="dz-first">
            </div>
        </form>
        <div class="business-input-item upload-file m-md-top multiple-upload business-box-shadow">
            <div class="item-tag" style="position: static;">上传校企图片</div>
            <div class="maskLayer">
                <div id="multiple_image" class="dropzone">
                    <div class="dz-space"></div>
                </div>
            </div>
            <img src="{{ asset('images/business/plus.png') }}" alt="上传图片" class="dz-first">
        </div>

        <form id="mainForm">
            {!! csrf_field() !!}
            <input type="hidden" name="lat" value="{{ $lat }}">
            <input type="hidden" name="lng" value="{{ $lng }}">
            <div class="business-input-item">
                <div class="item-tag">校企名称</div>
                <div class="text-input">
                    <textarea name="name" rows="1" class="business-text" placeholder="校企名称"></textarea>
                </div>
            </div>
            <div class="business-input-item">
                <div class="item-tag">校企电话</div>
                <div class="text-input">
                    <input type="number" name="phone" class="business-input" placeholder="校企电话" value="">
                </div>
            </div>
            <div class="business-input-item">
                <div class="item-tag">联系电话</div>
                <div class="text-input">
                    <input type="number" name="linkman" class="business-input" placeholder="联系人电话" value="">
                </div>
            </div>
            <div class="business-input-item">
                <div class="item-tag">地址</div>
                <div class="text-input">
                    <textarea name="address" rows="1" class="business-text" placeholder="校企地址"></textarea>
                </div>
            </div>
            <div class="business-input-item">
                <div class="item-tag">类别</div>
                <div class="text-input">
                    <select name="type" class="business-input">
                        <option value="0" selected>餐饮娱乐类</option>
                        <option value="1">酒店类</option>
                        <option value="2">生活出行类</option>
                        <option value="3">运动健康类</option>
                    </select>
                </div>
            </div>
            <div class="business-input-item business-box-shadow">
                <div class="item-tag">校企简介</div>
                <div class="text-input">
                    <textarea name="introduction" rows="4" class="business-text" placeholder="校企简介"></textarea>
                </div>
            </div>
            <input type="hidden" name="poster" value="">
            <input type="hidden" name="image" value="">
        </form>

        <div class="business-submit-btn">
            <a href="javascript:;" class="btn-save">保存</a>
        </div>
    </div>
    <script src="{{ asset('js/plugins/jquery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('js/business/dropzone.min.js') }}"></script>
    <script type="text/javascript">
        var token='{{ $token }}';

        $(function () {
            $('.business-text').bind('keydown keyup', function () {
                $(this).autosize();
            });

            $.fn.autosize = function () {
                $(this).height('0px');
                var setHeight = $(this).get(0).scrollHeight-13;
                if ($(this).attr("_height") != setHeight) {
                    $(this).height(setHeight + "px").attr("_height",setHeight);
                } else {
                    $(this).height($(this).attr("_height") + "px");
                }
            };

            $("#poster-upload").dropzone({
                url: 'http://up-z2.qiniu.com/',
                maxFiles: 1,
                maxFilesSize: 15,
                paramName: 'file',
                acceptedFiles: 'image/*',
                dictDefaultMessage: '',
                dictRemoveFile: '×',
                dictCancelUpload: '×',
                addRemoveLinks: true,
                thumbnailWidth: 75,
                thumbnailHeight: 75,
                init: function () {
                    this.on("sending", function (file,xhr,formData) {
                        formData.append("token", token);
                    });
                    this.on("success", function (file,data) {
                        $("[name=poster]").val(data.key);
                        upload_result("uploadsuccess",data.key);
                    });
                    this.on("removedfile", function (file) {
                        $("[name=poster]").val('');
                    });
                    this.on("error", function (file,errorMessage) {
                        console.log(file);
                        console.log(errorMessage);
                        if (errorMessage && errorMessage.error)
                            upload_result("upload error",errorMessage.error);
                        else
                            upload_result("unknow","");
                    });
                }
            });

            $("#multiple_image").dropzone({
                url: 'http://up-z2.qiniu.com/',
                maxFiles: 20,
                maxFileSize: 15,
                paramName: 'file',
                acceptedFiles: "image/*",
                dictDefaultMessage: "",
                dictRemoveFile: '×',
                dictCancelUpload: '×',
                addRemoveLinks: true,
                thumbnailWidth: 75,
                thumbnailHeight: 75,
                init: function () {
                    this.on("sending", function (file,xhr,formData) {
                        formData.append("token", token);
                    });
                    this.on("queuecomplete", function (file) {
                        var image = $("[name=image]");
                        var path = '';
                        for( i in this.getAcceptedFiles() ){
                            path += ","+JSON.parse(this.getAcceptedFiles()[i].xhr.response).key;
                        }
                        image.val(path);
                    });
                    this.on("removedfile", function (file) {
                        var image = $("[name=image]");
                        var path = '';
                        for( i in this.getAcceptedFiles() ){
                            path += ","+JSON.parse(this.getAcceptedFiles()[i].xhr.response).key;
                        }
                        image.val(path);
                        $('.multiple-upload').height($("#multiple_image").height() + 40 + "px");
                    });
                    this.on("complete", function (file) {
                        $('.multiple-upload').height($("#multiple_image").height() + 40 + "px");
                    });
                    this.on("addedfile", function (file) {
                        $('.multiple-upload').height($("#multiple_image").height() + 40 + "px");
                    });
                    this.on("error", function (file,errorMessage) {
                        console.log(file);
                        console.log(errorMessage);
                        if (errorMessage && errorMessage.error)
                            upload_result("upload error",errorMessage.error);
                        else
                            upload_result("unknow","");
                    });
                }

            });
        });

        $('.btn-save').click(function () {
            var data = $('#mainForm').serialize();
            $.ajax({
                url: '{{ route('wechat.business.add') }}',
                data: data,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    alert(data.message);
                    self.location = data.data.redirect;
                },
                error: function (err) {
                    var errData = JSON.parse(err.responseText);
                    var errInfo = '';
                    for(var i in errData) {
                        console.log(errData[i][0]);
                        errInfo = errData[i][0];
                    }
                    alert(errInfo);
                }
            })
        });

        function upload_result(code,error)
        {
            $.ajax({
                type: 'post',
                dataType:'json',
                cache:false,
                url: "{{url('/act/exportLog')}}",
                data: "action_type_prefix="+code+"&shareType="+error,
                success: function(data){
                },
                error : function() {
                }
            });
        }

    </script>
@endsection