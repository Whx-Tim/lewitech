@extends('layouts.data')

@section('title', '6月17号股东活动报名情况')

@section('css')

@endsection

@section('content')
    <section class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>姓名</th>
                        <th>电话</th>
                        <th>是否生日</th>
                        <th>是否带朋友</th>
                        <th>朋友数</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($applies as $apply)
                        <tr>
                            <td>{{ json_decode($apply->data)->name }}</td>
                            <td>{{ json_decode($apply->data)->phone }}</td>
                            <td>{{ json_decode($apply->data)->is_birthday == 0 ? '否' : '是' }}</td>
                            <td>{{ empty(json_decode($apply->data)->is_friend) ? '否' : json_decode($apply->data)->is_friend == 0 ? '否' : '是' }}</td>
                            <td>{{ empty(json_decode($apply->data)->friend) ? 0 : json_decode($apply->data)->friend }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('javascript')

@endsection