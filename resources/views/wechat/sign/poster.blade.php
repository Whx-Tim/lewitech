@extends('layouts.wechat')

@section('title', '海报')

@section('content')
    <section class="container">
        <img src="{{ $path }}" style="width: 100%">
    </section>
@endsection