@extends('layouts.admin.app')

@section('admin.title', '需求管理')

@section('breadcrumb')
    <li class="active">需求管理</li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <blockquote>
                <b>需求总数： {{ $count }}</b>
                <a href="{{ route('admin.demand.add') }}" class="btn btn-info pull-right"><i class="fa fa-plus"></i>添加需求</a>
            </blockquote>
        </div>
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>需求标题</th>
                    <th>需求发布用户</th>
                    <th>审核状态</th>
                    <th>创建时间</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($demands as $demand)
                    <tr>
                        <td>{{ $demand->id }}</td>
                        <td><a href="{{ $demand->adminDetailUrl() }}">{{ str_limit($demand->title, 15) }}</a></td>
                        <td>{{ $demand->user->detail->nickname or '' }}</td>
                        <td>{{ $demand->status2String() }}</td>
                        <td>{{ $demand->created_at->toDateString() }}</td>
                        <td>
                            <a href="{{ $demand->adminEditUrl() }}" operation="edit"><i class="fa fa-pencil fa-2x"></i></a>&nbsp;
                            <a href="{{ $demand->adminDeleteUrl() }}" operation="delete"><i class="fa fa-close fa-2x"></i></a>&nbsp;
                            <a href="{{ $demand->adminCheckUrl() }}" operation="check"><i class="fa fa-eye fa-2x"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination-home">
                {!! $demands->links() !!}
            </div>
        </div>
    </div>
@endsection