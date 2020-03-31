@extends('layouts.admin.app')

@section('admin.title', '活动管理')

@section('breadcrumb')
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <blockquote>
                <b>活动总数：{{ $count }}</b>
                <a href="{{ url('admin/active/add') }}" class="pull-right btn btn-info"><i class="fa fa-plus"></i>添加活动</a>&nbsp;
                <a href="{{ route('admin.active.banner.index') }}" class="pull-right btn btn-info"><i class="fa fa-list"></i>&nbsp;banner</a>
            </blockquote>
        </div>
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>活动名称</th>
                        <th>主办方</th>
                        <th>地址</th>
                        <th>活动开始时间</th>
                        <th>活动报名截止时间</th>
                        <th>状态</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($actives as $active)
                    <tr>
                        <td>{{ $active->id }}</td>
                        <td><a href="{{ $active->adminDetailUrl() }}">{{ str_limit($active->name, 15) }}</a></td>
                        <td>{{ str_limit($active->sponsor, 10) }}</td>
                        <td>{{ $active->location }}</td>
                        <td>{{ $active->start_time }}</td>
                        <td>{{ $active->end_at }}</td>
                        <td status>{{ $active->status2String() }}</td>
                        <td>
                            <a href="{{ $active->adminEditUrl() }}" operation="edit"><i class="fa fa-pencil fa-2x"></i></a>
                            <a href="{{ $active->adminDeleteUrl() }}" operation="delete"><i class="fa fa-trash-o fa-2x"></i></a>
                            <a href="{{ $active->adminCheckUrl() }}" operation="check"><i class="fa fa-eye fa-2x"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination-home">
                {!! $actives->links() !!}
            </div>
        </div>
    </div>
@endsection