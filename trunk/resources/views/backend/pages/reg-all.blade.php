@extends('backend.layout')
@section('page_title')
<h1>挂号单管理</h1>
@stop
@section('content')
<?php use App\Models\Reg;?>
<?php use App\Models\User;?>
<?php use App\Models\Hospital;?>
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-filter col-md-12 row">
            <form action="" method="get" accept-charset="utf-8">
                <div class="col-md-2">
                    <select class="selectboxit auto-submit" data-target=".submit-btn" name="hospital_id">
                        <option value='0' >--医院名称--</option>
                        <?php $hospitals = Hospital::getHospitalList() ?>
                        @foreach($hospitals as $k => $v)
                        <option value="{{ $v->id }}" @if(Request::Input('hospital_id', -1) == $v->id) selected @endif>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="selectboxit auto-submit" data-target=".submit-btn" name="state">
                        <option value='-1' >--状态--</option>
                        @foreach(Reg::$stateList as $k => $v)
                        <option value="{{ $k }}" @if(Request::Input('state', -1) == $k) selected @endif>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="patient_name" class="form-control" value="{{Request::Input('patient_name')}}" placeholder="病人姓名">
                </div>
                <div class="col-md-2">
                    <input type="text" name="doctor_name" class="form-control" value="{{Request::Input('doctor_name')}}" placeholder="医生姓名">
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
                    <th>编号</th>
                    <th>用户名</th>
                    <th>病人名称</th>
                    <th>医生</th>
                    <th>医院</th>
                    <th>部门</th>
                    <th>日期</th>
                    <th>价格</th>
                    <th>添加时间</th>
                    <th>状态</th>
                    <th class="do">操作</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($regList))
                <tr>
                    <td colspan="12">无数据</td>
                </tr>
                @else
                @foreach($regList as $reg)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{ $reg->id }}">
                    </td>
                    <td class="td-id">{{ $reg->serial_no }}</td>
                    <td>@if($reg->userInfo) {{ $reg->userInfo->name }} @else —— @endif</td>
                    <td>@if($reg->patientInfo) {{ $reg->patientInfo->name }} @else —— @endif</td>
                    <td>@if($reg->doctorInfo) {{ $reg->doctorInfo->name }} @else —— @endif</td>
                    <td>@if($reg->hospitalInfo) {{ $reg->hospitalInfo->name }} @else —— @endif</td>
                    <td>@if($reg->departmentInfo) {{ $reg->departmentInfo->name }} @else —— @endif</td>
                    <td>{{ $reg->date }} @if($reg->time == Reg::REG_TIME_FOREMOON) 上午 @else 下午 @endif</td>
                    <td>{{ $reg->price }}</td>
                    <td>{{ $reg->created_at->format('Y-m-d H:i') }}</td>
                    <td>{!! $reg->stateToHtml() !!}</td>
                    <td class="do">
                        @if($reg->state == Reg::ORDER_STATE_PENDING)
                        <a href="javascript:void(0);" id="update-status-{{ Reg::ORDER_STATE_FAILED }}-{{ $reg->id }}"><span class="entypo-pencil"></span>预约失败</a>
                        <a href="javascript:void(0);" id="update-status-{{ Reg::ORDER_STATE_SUCCESS }}-{{ $reg->id }}"><span class="entypo-pencil"></span>预约成功</a>
                        @endif
                        @if(!in_array($reg->state, [Reg::ORDER_STATE_CANCELLED, Reg::ORDER_STATE_FINISHED]))
                        <a href="javascript:void(0);" id="update-status-{{ Reg::ORDER_STATE_CANCELLED }}-{{ $reg->id }}"><span class="entypo-pencil"></span>取消</a>
                        @endif
                        <a class="red" href="javascript:void(0);" id="common_delete_{{ $reg->id }}" model="reg"><span class="entypo-cancel-circled"></span>删除</a>
                    </td>
                </tr>
                @endforeach
                <tr><td colspan="12">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $regList])</div>
                    </div>
                </td></tr>
                @endif
            </tbody>
        </table>
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
    $("[id^='update-status-']").on('click', function(){
      var alertStr = '';
      var reg_id = parseInt($(this).attr('id').split('-')[3]); 
      var state = parseInt($(this).attr('id').split('-')[2]);
      var order_state_failed = parseInt("{{ Reg::ORDER_STATE_FAILED }}");
      var order_state_succ   = parseInt("{{ Reg::ORDER_STATE_SUCCESS }}");
      var order_state_cancel = parseInt("{{ Reg::ORDER_STATE_CANCELLED }}");
      if (state) {
        if (state == order_state_cancel) {
          alertStr = '确定要执行取消预约操作吗？';
        } else if (state == order_state_succ) {
          alertStr = '确定要执行预约成功吗？';
        } else if (state == order_state_failed) {
          alertStr = '确定要执行预约失败吗？';
        } else {
          alert('操作失败');
          return false;
        }
        if (!confirm(alertStr)) {
          return false;
        }
        $.post('{{ url("admin/reg/update-status") }}', {'state':state, 'reg_id':reg_id}, function(json){
          if (json.error_code == 0) {
            alert(json.msg);
            window.location.reload();
          } else {
            alert(json.error_desc);
            window.location.reload();
          }
        });
      }
    })
  })
</script>
@stop











