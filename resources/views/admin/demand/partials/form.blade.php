<form>
    {!! csrf_field() !!}
    <div class="form-group"><label>标题</label><input type="text" class="form-control" name="title" value="{{ $demand->title or '' }}"></div>
    <div class="form-group"><label>联系电话</label><input type="text" class="form-control" name="title" value="{{ $demand->phone or '' }}"></div>
    <div class="form-group"><label>联系称呼</label><input type="text" class="form-control" name="title" value="{{ $demand->name or '' }}"></div>
    <div class="form-group">
        <label>状态</label>
        @if(!empty($demand->status))
            <select name="status" class="form-control">
                <option value="0" {{ $demand->status == 0 ? 'selected' : '' }}>未审核</option>
                <option value="1" {{ $demand->status == 1 ? 'selected' : '' }}>已审核</option>
                <option value="2" {{ $demand->status == 2 ? 'selected' : '' }}>已关闭</option>
            </select>
        @else
            <select name="status" class="form-control">
                <option value="0">未审核</option>
                <option value="1">已审核</option>
                <option value="2">已关闭</option>
            </select>
        @endif
    </div>
    <div class="form-group">
        <label>需求内容</label>
        <textarea name="content" rows="5" class="form-control">{{ $demand->content or '' }}</textarea>
    </div>

    @if(!empty($demand))
        <a href="{{ $demand->adminEditUrl() }}" class="btn btn-primary btn-block" id="update-btn">更新</a>
    @else
        <a href="{{ route('admin.demand.add') }}" class="btn btn-primary btn-block" id="add-btn">添加</a>
    @endif
</form>