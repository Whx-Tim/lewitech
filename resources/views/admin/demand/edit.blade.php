@extends('layouts.admin.app')

@section('admin.title', '需求编辑')

@section('breadcrumb')
    <li><a href="{{ route('admin.demand.index') }}">需求管理</a></li>
@endsection

@section('admin.content')
    <div class="row">
        <div class="col-md-12">
            @include('admin.demand.partials.form')
        </div>
    </div>
@endsection