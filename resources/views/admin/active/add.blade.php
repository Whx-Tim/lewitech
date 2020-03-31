@extends('layouts.admin.app')

@section('admin.title', '添加活动')

@section('breadcrumb')
    <li><a href="{{ route('admin.active.index') }}">活动管理</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>活动封面图片</label>
                <img src="" alt="" style="width: 100%;">
                <div id="dropzone" class="dropzone" img-input="poster"></div>
            </div>
            @include('admin.active.partials.form')
        </div>
    </div>
@endsection

@section('admin.script')
    @include('admin.active.partials.script')
@endsection
