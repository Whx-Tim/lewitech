@extends('layouts.admin.app')

@section('admin.title', '历史记录')

@section('breadcrumb')
    <li><a href="{{ route('admin.umbrella.index') }}">公益伞管理</a></li>
@endsection

@section('admin.content')
    <blockquote>
        <b>历史记录：{{ $count }}</b>
    </blockquote>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover Table">
                <thead>
                    <tr>

                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection