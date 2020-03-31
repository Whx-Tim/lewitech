@extends('layouts.admin.app')

@section('admin.title', '活动banner管理')

@section('breadcrumb')
    <li><a href="{{ route('admin.active.index') }}">活动管理</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <blockquote>
            <b>banner数：{{ $count }}</b>
            <a href="{{ route('admin.active.banner.add') }}" class="btn btn-info pull-right"><i class="fa fa-plus"></i>&nbsp;添加</a>
        </blockquote>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>网址</th>
                        <th>图片</th>
                        <th>顺序</th>
                        <th>创建时间</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($banners as $banner)
                    <tr>
                        <td>{{ $banner->id }}</td>
                        <td>{{ $banner->content->address }}</td>
                        <td>{{ $banner->content->path }}</td>
                        <td>{{ $banner->content->order }}</td>
                        <td>{{ $banner->created_at }}</td>
                        <td>
                            <a href="{{ route('admin.active.banner.edit', ['id' => $banner->id]) }}" operation="edit"><i class="fa fa-pencil fa-2x"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection