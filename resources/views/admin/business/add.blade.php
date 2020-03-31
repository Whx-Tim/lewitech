@extends('layouts.admin.app')

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <blockquote>
                <b>商家总数： {{ $count }}</b>
            </blockquote>
        </div>
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>校企名称</th>
                        <th>联系电话</th>
                        <th>联系人电话</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($businesses as $business)
                    <tr>
                        <td>{{ $business->id }}</td>
                        <td>{{ $business->name }}</td>
                        <td>{{ $business->phone }}</td>
                        <td>{{ $business->linkman }}</td>
                        <td>{{ $business->type }}</td>
                        <td>
                            <a href="javascript:;" class="btn btn-info"><i class="fa fa-eye"></i>&nbsp;查看</a>
                            <a href="javascript:;" class="btn btn-success"><i class="fa fa-edit"></i>&nbsp;审核/修改</a>
                            <a href="javascript:;" class="btn btn-warning"><i class="fa fa-trash-o"></i>&nbsp;删除</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
