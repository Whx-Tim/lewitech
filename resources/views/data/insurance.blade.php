@extends('layouts.data')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col m12">
                <ul class="collapsible popout" data-collapsible="accordion">
                    @foreach($temps as $temp)
                    <li>
                        <div class="collapsible-header">{{ $temp->name }}</div>
                        <div class="collapsible-body">
                            <div class="row">
                                <div class="col m8 offset-m2">
                                    <div class="card">
                                        <div class="card-panel">
                                            <ul class="collection">
                                                <li class="collection-item">姓名：{{ $temp->name or '无' }}</li>
                                                <li class="collection-item">性别：{{ $temp->sex or '无' }}</li>
                                                <li class="collection-item">出生年份：{{ $temp->birthday_year or '无' }}</li>
                                                <li class="collection-item">现工作单位及职务：{{ $temp->position or '无' }}</li>
                                                <li class="collection-item">社会组织担任职务：{{ $temp->social or '无' }}</li>
                                                <li class="collection-item">所在企业介绍：{{ $temp->undertaking_introduction or '无' }}</li>
                                                <li class="collection-item">所在企业内容：{{ $temp->undertaking_content or '无' }}</li>
                                                <li class="collection-item">个人履历：{{ $temp->experience or '无' }}</li>
                                                <li class="collection-item">爱好：{{ $temp->hobby or '无' }}</li>
                                                <li class="collection-item">自我评价：{{ $temp->self_comment or '无' }}</li>
                                                <li class="collection-item">我心中的乐微：{{ $temp->lewitech or '无' }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('.collapsible').collapsible();
        });
    </script>
@endsection