@extends('layouts.web')

@section('title', '项目介绍')

@push('css')
<style>
    .image-box img {
        float: left;
        width: 100%;
    }
</style>
@endpush

@section('content')
    @include('web._sidebar')
    <div class="row" style="margin-top: 50px;">
        <div class="container light-blue lighten-5">
            <div class="row">
                <div class="col l10 offset-l1">
                    <h2 class="cyan-text" style="font-weight: bolder;margin-bottom: 0">项目介绍</h2>
                    <h3 class="grey-text" style="margin: 0;margin-bottom: 30px">PROJECT INTRODUCTION</h3>
                    <div class="col l12 grey" style="margin-bottom: 30px"></div>
                    <div class="row">
                        <h4 class="center-align">校友共享圈APP</h4>
                        <p class="right-align">2017-10-16</p>
                        <div class="row">
                            <div class="col l12 center-align">
                                <div class="image-box">
                                    <img src="{{ asset('images/web/app/01.png') }}">
                                    <img src="{{ asset('images/web/app/02.png') }}">
                                    <img src="{{ asset('images/web/app/03.png') }}">
                                    <img src="{{ asset('images/web/app/04.png') }}">
                                    <img src="{{ asset('images/web/app/05.png') }}">
                                    <img src="{{ asset('images/web/app/06.png') }}">
                                    <img src="{{ asset('images/web/app/07.png') }}">
                                    <img src="{{ asset('images/web/app/08.png') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

