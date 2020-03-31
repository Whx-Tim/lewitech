@push('css')
<style>
    .sidebar {
        position: fixed;
        top: 15%;
        left: 5%;
        width: 150px;
        min-height: 200px;
        background-color: #58c4ce;
        color: #ffffff;
        padding: 30px 10px;
        border-radius: 25px;
    }

    .sidebar ul li a {
        width: 100%;
    }

    .sidebar:before,
    .sidebar:after {
        position: absolute;
        left: 48%;
        width: 10px;
        height: 10px;
        content: '';
        border-radius: 50%;
    }

    .sidebar:before {
        top: 15px;
        background-color: #ffffff;
        border: none;
    }

    .sidebar:after {
        bottom: 15px;
        border: 2px solid #ffffff;
    }

    .sidebar-btn {

    }
</style>
@endpush

<div class="sidebar">
    <ul>
        <li><a href="{{ route('web.introduction') }}" class="btn sidebar-btn" data-activates="company-sidebar">公司介绍</a></li>
        <li><a href="#" class="dropdown-button btn sidebar-btn" data-activates="project-sidebar">项目介绍</a></li>
    </ul>
</div>
<ul id="project-sidebar" class="dropdown-content">
    <li><a href="{{ route('web.app') }}">校友共享圈</a></li>
    <li><a href="{{ route('web.umbrella') }}">爱心公益伞</a></li>
    {{--<li><a href="{{ route('web.sign') }}">早起打卡</a></li>--}}
</ul>