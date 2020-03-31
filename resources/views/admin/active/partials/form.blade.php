<form>
    {!! csrf_field() !!}
    <div class="form-group"><label>活动名称</label><input type="text" class="form-control" name="name" value="{{ $active->name or '' }}"></div>
    <div class="form-group"><label>主办方</label><input type="text" class="form-control" name="sponsor" value="{{ $active->sponsor or '' }}"></div>
    <div class="form-group"><label>联系电话</label><input type="text" class="form-control" name="phone" value="{{ $active->phone or '' }}"></div>
    <div class="form-group"><label>地址</label><input type="text" class="form-control" name="location" value="{{ $active->location or '' }}"></div>
    <div class="form-group"><label>限制人数</label><input type="text" class="form-control" name="persons" value="{{ $active->persons or '' }}"></div>
    <div class="form-group"><label>活动开始时间</label><input type="text" class="form-control" name="start_time" id="start_time" value="{{ $active->start_time or '' }}"></div>
    <div class="form-group"><label>活动结束时间</label><input type="text" class="form-control" name="end_time" id="end_time" value="{{ $active->end_time or '' }}"></div>
    <div class="form-group"><label>活动报名截止时间</label><input type="text" class="form-control" name="end_at" id="end_at" value="{{ $active->end_at or '' }}"></div>
    <div class="form-group">
        <label>活动状态</label>
        @if(!empty($active->status))
            <select name="status" class="form-control">
                <option value="0" {{ $active->status == 0 ? 'selected' : '' }}>未审核</option>
                <option value="1" {{ $active->status == 1 ? 'selected' : '' }}>已审核</option>
                <option value="2" {{ $active->status == 2 ? 'selected' : '' }}>已关闭</option>
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
        <label>活动介绍</label>
        <textarea name="description" rows="10" class="form-control">{{ $active->description or '' }}</textarea>
    </div>
    @if(!empty($active))
        <a href="{{ $active->adminEditUrl() }}" class="btn btn-primary btn-block" id="update-btn">更新</a>
    @else
        <a href="{{ route('admin.active.add') }}" class="btn btn-primary btn-block" id="add-btn">添加</a>
    @endif
    <input type="hidden" name="poster" value="{{ $active->poster or '' }}">
</form>

