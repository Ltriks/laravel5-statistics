@extends('backend.layout')
@section('page_title')
<h1>新建用户<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/user/all') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<?php use App\Models\User; ?>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-body">
                <form action="{{url('/admin/user/create')}}" method="post" accept-charset="utf-8" class="form-horizontal">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><i class="required-star">*</i>手机号</label>
                        <div class="col-sm-6">
                            <input type="text" name="mobile" class="form-control" placeholder="ex:18600758952， 必须唯一" value="{{Request::old('mobile')}}"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"> 用户名</label>
                        <div class="col-sm-6">
                            <input type="text" name="username" class="form-control" placeholder="用户名必须唯一" value="{{Request::old('username')}}"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"> 昵称</label>
                        <div class="col-sm-6">
                            <input type="text" name="name" class="form-control" placeholder="昵称长度为10个字符" value="{{Request::old('name')}}" maxlength="16"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">性别</label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                              <input type="radio" name="gender" @if(Request::old('gender') == 1) checked @endif value="1"> 男
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="gender" @if(Request::old('gender') == 0) checked @endif  value="0"> 女
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><i class="required-star">*</i>密码</label>
                        <div class="col-sm-6">
                            <input type="password" name="password" class="form-control" placeholder="" value="" maxlength="16" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><i class="required-star">*</i>确认密码</label>
                        <div class="col-sm-6">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="" value="" maxlength="16"/>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态</label>
                        <div class="col-sm-5">
                            <select name="enabled" class="selectboxit">
                                <option value='{{User::ENABLED_TRUE}}' @if(Request::old('enabled') == User::ENABLED_TRUE) selected @endif>正常</option>
                                <option value='{{User::ENABLED_FALSE}}' @if(Request::old('enabled') == User::ENABLED_FALSE) selected @endif>已禁用</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">角色</label>
                        <div class="col-sm-5">
                            <select name="role" class="selectboxit">
                                <option value='{{User::ROLE_USER}}' @if(Request::old('role') == User::ROLE_USER) selected @endif>普通用户</option>
                                <option value='{{User::ROLE_ADMIN}}' @if(Request::old('role') == User::ROLE_ADMIN) selected @endif>管理员</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">用户组</label>
                        <div class="col-sm-5">
                            <select name="group" class="selectboxit">
                                <option value="0">-无-</option>
                                <?php $groupList = App\Models\UserGroup::getGroupList(); ?>
                                @foreach($groupList as $k => $v)
                                <option value="{{ $v->id }} @if(Input::old('group') == $v->id) selected @endif">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-success">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /col-md-8-->
    <blockquote class="col-md-4">
        <h3>操作说明：</h3>
        <p>* 用户名不可修改</p>
        <p>* 密码留空则不修改</p>
    </blockquote>
</div>
@stop

@section('page_css')
<link rel="stylesheet" href="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.css') }}">
@stop

@section('page_js')
<script src="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.min.js') }}" ></script>
@stop