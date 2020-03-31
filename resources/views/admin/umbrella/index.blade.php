@extends('layouts.admin.app')

@section('admin.title', '公益伞管理')

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            <blockquote>
                <b>激活的雨伞数量：{{ $count }}</b>
            </blockquote>
        </div>
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>持有用户</th>
                        <th>所在站点</th>
                        <th>扫码次数</th>
                        <th>被借次数</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($umbrellas as $umbrella)
                    <tr>
                        <td>{{ $umbrella->id }}</td>
                        <td>{{ $umbrella->user->id or '未借出' }}</td>
                        <td>{{ $umbrella->station2string() }}</td>
                        <td>{{ $umbrella->scan_count }}</td>
                        <td>{{ $umbrella->real_scan_count }}</td>
                        <td></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination-home">
                {!! $umbrellas->links() !!}
            </div>
        </div>
    </div>
@endsection