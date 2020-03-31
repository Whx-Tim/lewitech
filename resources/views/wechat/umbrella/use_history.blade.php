@extends('wechat.umbrella.common.layout')

@section('css')
    <style>
        ul {
            list-style: none;
            padding: 10px;
        }

        .list-item {
            display: flex;
            flex-direction: row;
            background-color: #fff;
            margin-top: 20px;
        }

        .list-item-left,
        .list-item-right {
            padding: 10px;
        }

        .list-item .list-item-left {
            flex: .3;
            position: relative;
            border-right: 1px solid #efefef;
        }
        .list-item .list-item-left img {
            width: 100%;
            margin-top: 10px;
        }

        .list-item .list-item-right {
            flex: .7;
            font-family: "Lantinghei SC", sans-serif;
        }

        .list-item .list-item-right h4 {
            color: #0cbeaf;
        }
        .list-item .list-item-right p {
            display: block;
            font-size: 12px;
            color: #636363;
        }
        .list-item .list-item-right p img {
            width: 15px;
            vertical-align: sub;
            margin-right: 10px;
        }
        .list-item .list-item-right p span {
            color: #1d1d1d;
            font-size: 14px;
            margin-right: 10px;
        }
    </style>
@endsection

@section('content')
    <section class="container">
        <div class="top">
            <img src="{{ asset('images/umbrella/icon-return.png') }}" class="top-icon">
            <h3>当前用户</h3>
        </div>
        <ul>
            <li class="list-item">
                <div class="list-item-left">
                    <img src="{{ asset('images/no-avatar.png') }}">
                </div>
                <div class="list-item-right">
                    <h4>昵称</h4>
                    <p><img src="{{ asset('images/umbrella/icon-borrow.png') }}"><span>借出时间</span>2017-07-21</p>
                    <p><img src="{{ asset('images/umbrella/icon-still.png') }}"><span>归还时间</span>2017-07-21</p>
                    <p><img src="{{ asset('images/umbrella/icon-station.png') }}"><span>所在站点</span>深大地铁站</p>
                </div>
            </li><li class="list-item">
                <div class="list-item-left">
                    <img src="{{ asset('images/no-avatar.png') }}">
                </div>
                <div class="list-item-right">
                    <h4>昵称</h4>
                    <p><img src="{{ asset('images/umbrella/icon-borrow.png') }}"><span>借出时间</span>2017-07-21</p>
                    <p><img src="{{ asset('images/umbrella/icon-still.png') }}"><span>归还时间</span>2017-07-21</p>
                    <p><img src="{{ asset('images/umbrella/icon-station.png') }}"><span>所在站点</span>深大地铁站</p>
                </div>
            </li><li class="list-item">
                <div class="list-item-left">
                    <img src="{{ asset('images/no-avatar.png') }}">
                </div>
                <div class="list-item-right">
                    <h4>昵称</h4>
                    <p><img src="{{ asset('images/umbrella/icon-borrow.png') }}"><span>借出时间</span>2017-07-21</p>
                    <p><img src="{{ asset('images/umbrella/icon-still.png') }}"><span>归还时间</span>2017-07-21</p>
                    <p><img src="{{ asset('images/umbrella/icon-station.png') }}"><span>所在站点</span>深大地铁站</p>
                </div>
            </li><li class="list-item">
                <div class="list-item-left">
                    <img src="{{ asset('images/no-avatar.png') }}">
                </div>
                <div class="list-item-right">
                    <h4>昵称</h4>
                    <p><img src="{{ asset('images/umbrella/icon-borrow.png') }}"><span>借出时间</span>2017-07-21</p>
                    <p><img src="{{ asset('images/umbrella/icon-still.png') }}"><span>归还时间</span>2017-07-21</p>
                    <p><img src="{{ asset('images/umbrella/icon-station.png') }}"><span>所在站点</span>深大地铁站</p>
                </div>
            </li>
        </ul>
    </section>
    @include('wechat.umbrella.common.footer')
@endsection

@section('javascript')

@endsection