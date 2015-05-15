@extends('backend.layout')
@section('page_title')
<h1>所有医院<span class="middle-sep mg-x-10"></span><a href="{{ url('/admin/hospital/new') }}" class="btn btn-info">新建</a></h1>
@stop
@section('content')
<?php use App\Models\Hospital;?>
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-filter col-md-12 row">
            <form action="" method="get" accept-charset="utf-8">
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" value="{{Input::get('keyword')}}" placeholder="医院名">
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
                    <th>名称</th>
                    <th>类型</th>
                    <th>等级</th>
                    <th>添加时间</th>
                    <th>状态</th>
                    <th>排序</th>
                    <th class="do">操作</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($hospitalList))
                <tr>
                    <td colspan="8">无数据</td>
                </tr>
                @else
                @foreach($hospitalList as $hospital)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{ $hospital->id }}">
                    </td>
                    <td class="td-id">{{ $hospital->id }}</td>
                    <td>{{ $hospital->name }}</td>
                    <td>@if($hospital->type) {{ Hospital::$typeList[$hospital->type] }} @else 无 @endif</td>
                    <td>@if($hospital->level) {{ Hospital::$levelList[$hospital->level] }} @else 无 @endif</td>
                    <td>{{isset($hospital->created_at) ? $hospital->created_at->format('Y-m-d H:i') : '无'}}</td>
                    <td>@if($hospital->status == Hospital::STATUS_OK) 正常 @else 已删除 @endif</td>
                    <td width="100"><input type="text" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-original-title="有效值：0 ~ 10000,越大越靠前" data-id="{{$hospital->id}}" data-old="{{$hospital->sort}}" value="{{$hospital->sort}}" class="unsigned-int-input sorter form-control" max-num="10000"></td>
                    <td class="do">
                        <a href="{{ url('admin/hospital-department/all') }}?hospital_id={{ $hospital->id }}" target="_blank"><span class="entypo-pencil"></span>所有科室</a>
                        <a href="{{ url('admin/doctor/all') }}?hospital_id={{ $hospital->id }}" target="_blank"><span class="entypo-pencil"></span>所有医生</a>
                        <a href="{{ url('admin/hospital/edit') }}?hospital_id={{ $hospital->id }}"><span class="entypo-pencil"></span>编辑</a>
                        <a class="red" href="javascript:void(0);" id="common_delete_{{ $hospital->id }}" model="hospital"><span class="entypo-cancel-circled"></span>删除</a>
                    </td>
                </tr>
                @endforeach
                <tr><td colspan="9">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $hospitalList])</div>
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