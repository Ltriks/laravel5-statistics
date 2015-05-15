@extends('backend.layout')
@section('page_title')
<h1>所有反馈<span class="middle-sep mg-x-10"></span><a href="{{ url('/admin/feedback/new') }}" class="btn btn-info">新建</a></h1>
@stop
@section('content')
<?php use App\Models\Feedback;?>
<div class="row">
    <div class="col-md-12 form-group">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="select-all" data-target=".chkbox">
                    </th>
                    <th>ID</th>
                    <th>昵称</th>
                    <th>反馈内容</th>
                    <th>反馈时间</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($feedbackList))
                <tr>
                    <td colspan="5">无数据</td>
                </tr>
                @else
                @foreach($feedbackList as $feedback)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{ $feedback->id }}">
                    </td>
                    <td class="td-id">{{ $feedback->id }}</td>
                    <td>{{ $feedback->userInfo->name }}</td>
                    <td>{{ $feedback->body }}</td>
                    <td>{{isset($feedback->created_at) ? $feedback->created_at->format('Y-m-d H:i') : '无'}}</td>
                </tr>
                @endforeach
                <tr><td colspan="9">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $feedbackList])</div>
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
                    <label class="col-sm-2 control-label">医院组</label>
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
  // 修改医院
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