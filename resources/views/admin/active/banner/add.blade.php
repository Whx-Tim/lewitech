@extends('layouts.admin.app')

@section('admin.title', '添加活动banner')

@section('breadcrumb')
    <li><a href="{{ route('admin.active.index') }}">活动管理</a></li>
    <li><a href="{{ route('admin.active.banner.index') }}">banner</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>活动封面图片</label>
                <img src="" alt="" style="width: 100%;">
                <div id="dropzone" class="dropzone" img-input="path"></div>
            </div>
            <form>
                {!! csrf_field() !!}
                <div class="form-group"><label>网址</label><input type="text" class="form-control" name="address"></div>
                <div class="form-group"><label>顺序</label><input type="number" class="form-control" name="order" placeholder="顺序越低越前，最低为1"></div>
                <input type="hidden" name="path" value="">
                <a href="{{ route('admin.active.banner.add') }}" id="add-btn" class="btn btn-success btn-block">添加</a>
            </form>
        </div>
    </div>
@endsection