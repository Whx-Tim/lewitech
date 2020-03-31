<aside class="Sidebar">
    <ul class="sidebar-links">
        <li><a href="{{ url('admin') }}" class="{{ request()->is('admin') ? 'selected' : '' }}"><i class="fa fa-dashboard icon-btn"></i>&nbsp;控制台</a></li>
        <li><a href="{{ url('admin/user') }}" class="{{ request()->is('admin/user*') ? 'selected' : '' }}"><i class="fa fa-user icon-btn"></i>&nbsp;用户管理</a></li>
        <li><a href="{{ url('admin/active') }}" class="{{ request()->is('admin/active*') ? 'selected' : '' }}"><i class="fa fa-envira icon-btn"></i>&nbsp;活动管理</a></li>
        <li><a href="{{ url('admin/demand') }}" class="{{ request()->is('admin/demand*') ? 'selected' : '' }}"><i class="fa fa-book icon-btn"></i>&nbsp;需求管理</a></li>
        <li><a href="{{ url('admin/umbrella') }}" class="{{ request()->is('admin/umbrella*') ? 'selected' : '' }}"><i class="fa fa-umbrella icon-btn"></i>&nbsp;公益伞管理</a></li>
        <li><a href="{{ route('admin.business.index') }}" class="{{ request()->is('admin/business*') ? 'selected' : '' }}"><i class="fa fa-shopping-bag"></i>&nbsp;校企管理</a></li>
        <li><a href="{{ route('admin.logout') }}" class=""><i class="fa fa-sign-out icon-btn"></i>&nbsp;注销</a></li>
    </ul>
</aside>