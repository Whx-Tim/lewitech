@extends('layouts.index_business')

@section('title', '编辑商家信息')

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
            <div class="item-tag" style="position: static;">上传商家图片</div>
            <div class="maskLayer">
                <div id="multiple_image" class="dropzone">
                    <div class="dz-space"></div>
                </div>
            </div>
            <img src="{{ asset('images/business/plus.png') }}" alt="上传图片" class="dz-first">
        </div>

        <form id="mainForm">
            {!! csrf_field() !!}
            <input type="hidden" name="business_lat" value="">
            <input type="hidden" name="business_lng" value="">
            <div class="business-input-item">
                <div class="item-tag">商家名称:</div>
                <div class="text-input">
                    <textarea name="business_name" rows="1" class="business-text" placeholder="商家名称"></textarea>
                </div>
            </div>
            <div class="business-input-item">
                <div class="item-tag">商家电话:</div>
                <div class="text-input">
                    <input type="number" name="business_phone" class="business-input" placeholder="商家电话" value="">
                </div>
            </div>
            <div class="business-input-item">
                <div class="item-tag">联系电话:</div>
                <div class="text-input">
                    <input type="number" name="business_linkman" class="business-input" placeholder="联系人电话" value="">
                </div>
            </div>
            <div class="business-input-item">
                <div class="item-tag">地址:</div>
                <div class="text-input">
                    <textarea name="business_address" rows="1" class="business-text" placeholder="商家地址"></textarea>
                </div>
            </div>
            <div class="business-input-item">
                <div class="item-tag">类别:</div>
                <div class="text-input">
                    <select name="business_type" class="business-input">
                        <option value="0" selected>餐饮娱乐类</option>
                        <option value="1">酒店类</option>
                        <option value="2">生活出行类</option>
                        <option value="3">运动健康类</option>
                    </select>
                </div>
            </div>
            <div class="business-input-item business-box-shadow">
                <div class="item-tag">商家简介:</div>
                <div class="text-input">
                    <textarea name="business_introduction" rows="4" class="business-text" placeholder="商家简介"></textarea>
                </div>
            </div>
            <input type="hidden" name="business_poster" value="">
            <input type="hidden" name="business_image" value="">
            <input type="hidden" name="user_id" value="">
        </form>

        <div class="business-submit-btn">
            <a href="javascript:submit_business();" class="btn-save">保存</a>
        </div>
    </div>

    <script src="/n/assets/admin/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="/n/assets/admin/plugins/dropzone/dropzone.min.js"></script>
    <script type="text/javascript">

        var route = location.href;
        var str = route.split("/");
        var business_id = str[str.length-1];

        $(function () {
            var business_poster = '';
            var business_image = '';

            $.ajax({
                url: '/n/api/v1/index/map/business/edit/'+business_id,
                type: 'get',
                dataType: 'json',
                async: false,
                success: function (data) {
                    if (data.errcode) {
                        alert(data.errmsg);
                    } else {
                        $('[name=business_lat]').val(data.data.business_lat);
                        $('[name=business_lng]').val(data.data.business_lng);
                        $('[name=business_name]').html(data.data.business_name);
                        $('[name=business_phone]').val(data.data.business_phone);
                        $('[name=business_linkman]').val(data.data.business_linkman);
                        $('[name=business_address]').html(data.data.business_address);
                        $('[name=business_introduction]').html(data.data.business_introduction);
                        $('[name=business_poster]').val(data.data.business_poster);
                        $('[name=business_image]').val(data.data.business_image);
                        $('[name=user_id]').val(data.data.user_id);
                        business_poster = data.data.business_poster;
                        business_image = data.data.business_image;
                        $("select").find('[value='+ data.data.business_type +']').attr('selected',true)
                    }
                }
            });


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
                url: '/n/map/business/upload',
                maxFiles: 1,
                maxFilesSize: 8,
                paramName: 'file',
                acceptedFiles: 'image/*',
                dictDefaultMessage: '',
                dictRemoveFile: '×',
                dictCancelUpload: '×',
                addRemoveLinks: true,
                thumbnailWidth: 75,
                thumbnailHeight: 75,
                init: function () {
                    var mockFile = {
                        name: business_poster,
                        size: 1,
                        accepted: true
                    };
                    var imageUrl = "/n/"+business_poster;
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, imageUrl);
                    this.emit("complete", mockFile);
                    this.on("success", function (file,data) {
                        $("[name=business_poster]").val(data.data.path);
                    });
                    this.on("removedfile", function (file) {
                        $("[name=business_poster]").val('');
                    });
                }
            });


            $("#multiple_image").dropzone({
                url: '/n/map/business/upload',
                maxFiles: 20,
                maxFileSize: 8,
                paramName: 'file',
                acceptedFiles: "image/*",
                dictDefaultMessage: "",
                dictRemoveFile: '×',
                dictCancelUpload: '×',
                addRemoveLinks: true,
                thumbnailWidth: 75,
                thumbnailHeight: 75,
                init: function () {
                    var image = business_image.split(',');
                    var x;
                    for (x in image) {
                        if (x > 0) {
                            var mockFile = {
                                name: image[x],
                                accepted: true
                            };
                            var imageUrl = "/n/"+image[x];
                            this.emit("addedfile", mockFile);
                            this.emit("thumbnail", mockFile, imageUrl);
                            this.emit("complete", mockFile);
                            this.files.push(mockFile);
                        }
                    }
                    $('.multiple-upload').height($("#multiple_image").height() + 40 + "px");
                    this.on("queuecomplete", function (file) {
                        var business_image = $("[name=business_image]");
                        var path = '';
                        for(var i in this.getAcceptedFiles() ){
                            if (typeof this.getAcceptedFiles()[i].xhr == "undefined") {
                                path += "," + this.getAcceptedFiles()[i].name;
                            } else {
                                path += ","+JSON.parse(this.getAcceptedFiles()[i].xhr.response).data.path;
                            }
                        }
                        business_image.val(path);
                    });
                    this.on("removedfile", function (file) {
                        var business_image = $("[name=business_image]");
                        var path = '';
                        for(var i in this.getAcceptedFiles() ){
                            if (typeof this.getAcceptedFiles()[i].xhr == "undefined") {
                                path += "," + this.getAcceptedFiles()[i].name;
                            } else {
                                path += ","+JSON.parse(this.getAcceptedFiles()[i].xhr.response).data.path;
                            }
                        }
                        business_image.val(path);
                        $('.multiple-upload').height($("#multiple_image").height() + 40 + "px");
                    });
                    this.on("complete", function (file) {
                        $('.multiple-upload').height($("#multiple_image").height() + 40 + "px");
                    });
                    this.on("addedfile", function (file) {
                        $('.multiple-upload').height($("#multiple_image").height() + 40 + "px");
                    });

                }

            });

        });

        function submit_business() {
            var data = $('#mainForm').serialize();
            $.ajax({
                url: '/n/map/business/edit/'+business_id,
                data: data,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status === 1) {
                        alert(data.message);
                        self.location = data.redirect;
                    } else {
                        alert(data.message);
                    }

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
        }
    </script>
@endsection