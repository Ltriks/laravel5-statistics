@extends('backend.layout')
@section('page_title')
<h1>所有用户<span class="middle-sep mg-x-10"></span><a href="{{url('/admin/user/new')}}" class="btn btn-info">新建</a></h1>
@stop
@section('content')
<?php use App\Models\User; ?>
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-filter col-md-12 row">
            <form action="" method="get" accept-charset="utf-8">
                <div class="col-md-2">
                    <select class="selectboxit auto-submit" data-target=".submit-btn" name="role">
                        <option value="0" >--角色--</option>
                        <option value="{{ User::ROLE_USER }}" @if(Request::Input('role') == User::ROLE_USER) selected @endif >普通用户</option>
                        <option value="{{ User::ROLE_ADMIN }}" @if(Request::Input('role') == User::ROLE_ADMIN) selected @endif>管理员</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="time_range" class="form-control daterange" value="{{Input::get('time_range')}}" placeholder="注册时间"/>
                </div>
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" value="{{Input::get('keyword')}}" placeholder="用户名/昵称/邮箱">
                </div>
                <div class="col-md-1">
                    <input type="submit" class="submit-btn btn btn-blue" value="搜索" />
                </div>
            </form>
        </div>
        <hr class="clearfix">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="select-all" data-target=".chkbox">
                    </th>
                    <th>ID</th>
                    <th>头像</th>
                    <th>昵称</th>
                    <th>邮箱</th>
                    <th>状态</th>
                    <th>注册时间</th>
                    <th class="do">操作</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($userList))
                <tr>
                    <td colspan="8">无数据</td>
                </tr>
                @else
                @foreach($userList as $user)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{$user->id}}">
                    </td>
                    <td class="td-id">{{$user->id}}</td>
                    <td><img src="{{$user->avatar()}}" class="avatar" height="25"></td>
                    <td>{{$user->name}}</td>
                    <td>{{empty($user->email) ? '无' : $user->email}}</td>
                    <td><?php echo $user->statusToHtml(); ?></td>
                    <td>{{isset($user->created_at) ? $user->created_at->format('Y-m-d H:i') : '无'}}</td>
                    <td class="do">
                        <a href="{{ url('admin/user/edit') }}?user_id={{ $user->id }}"><span class="entypo-pencil"></span>编辑</a>
                        <a class="red" href="javascript:void(0);" id="common_delete_{{ $user->id }}" model="user"><span class="entypo-cancel-circled"></span>删除</a>
                    </td>
                </tr>
                @endforeach
                <tr><td colspan="9">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $userList])</div>
                    </div>
                </td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop fade in" style="display: none;"></div>
<div class="modal-dialog" style="display:none;z-index: 10000;position: fixed;top: 20%;left: 20%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="close-float">×</button>
            <h4 class="modal-title">更改权限组</h4>
        </div>
        <form name="update_group">
        <input type="hidden" name="update_user_id" value="" />
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">用户组</label>
                    <div class="col-sm-5">
                        <select name="group_id" class="selectboxit">
                            <option value="0">-无-</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal-footer">
            <button id="sub_update_group" class="btn btn-info">提交</button>
        </div>
    </div>
</div>

@stop

@section('page_css')
<link rel="stylesheet" href="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.css') }}">
<link rel="stylesheet" href="{{ asset('/backend/js/daterangepicker/daterangepicker-bs3.css') }}">
@stop

@section('page_js')
<script src="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.min.js') }}" ></script>
<script src="{{ asset('/backend/js/daterangepicker/moment.min.js') }}" ></script>
<script src="{{ asset('/backend/js/daterangepicker/daterangepicker.js') }}" ></script>
<script type="text/javascript">
$(function(){
  $("[id^='edit_group_']").on('click', function(e){
    e.preventDefault();
    var userId = $(this).attr('id').split('_')[2];
    $("[name='update_user_id']").val(userId);
    $(".modal-backdrop").show();
    $(".modal-dialog").show();
  })
  // 修改用户呢组
  $("#sub_update_group").on('click', function(e){
    e.preventDefault();
    var data = {};
    data.id = $("[name='update_user_id']").val();
    data.group_id = $("[name='group_id']").val();
    if (!data.group_id) {
      alert('请选择分组');
    }

    $.post("{{ url('admin/user/update-group') }}", data, function(json){
      if (json) {
        json.error_doe = parseInt(json.error_code);
        if (json.error_code == 0) {
          alert('操作成功');
          resetGroupForm();
        }
      }
    },'json')
  })
    // 关闭浮层
    $("#close-float").on('click', function(e){
      e.preventDefault();
      resetGroupForm();
    })

    function resetGroupForm()
    {
      $("[name='update_user_id']").val('');
      $(".modal-backdrop").hide();
      $(".modal-dialog").hide();
    }
})
</script>
@stop