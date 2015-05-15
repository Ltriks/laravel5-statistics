@extends('backend.layout')
@section('page_title')
<h1>编辑用户组<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/user-group/all') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<div class="row">
    <form action="{{url('/admin/user-group/update', ['id' => $group->id])}}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <div class="form-group">
            <label class="col-sm-2 control-label"><i class="required-star">*</i> 名称</label>
            <div class="col-sm-5">
                <input type="text" name="name" class="form-control" value="{{Input::old('name', $group->name)}}" placeholder="权限组名称">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><i class="required-star">*</i> 权限</label>
            <div class="col-sm-8">
                <div class="select-all-permission checkbox">
                <label><input type="checkbox" name="" value="{{Input::old('name')}}" class="select-all" data-target=".chkbox">全部</label>
                </div>
                @foreach(Config::get('permission') as $engName => $permission)
                <fieldset>
                    <legend>{{$permission['name']}}</legend>
                </fieldset>
                <div class="permissions">
                    @foreach($permission['action'] as $actionKey => $action)
                    <div class="checkbox permissions-inline">
                        <label><input type="checkbox" class="chkbox" name="permission[]" value="{{$engName}}.{{$actionKey}}" @if(in_array("$engName.$actionKey", Input::old('permission', $group->permission))) checked @endif>{{$action['label']}}</label>
                    </div>
                    @endforeach
                    <div class="desc gray">
                        {{!empty($permission['desc']) ? $permission['desc'] : ''}}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        
        <hr class="clearfix">
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success">提交</button>
                
            </div>
        </div>
    </form>
</div>
@stop

@section('page_css')
<link rel="stylesheet" href="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.css') }}">
@stop

@section('page_js')
<script src="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.min.js') }}" ></script>
<script src="{{ asset('/backend/js/holder.js') }}"></script>
@stop