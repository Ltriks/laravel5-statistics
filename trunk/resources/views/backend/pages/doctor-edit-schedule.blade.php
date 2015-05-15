@extends('backend.layout')
@section('page_title')
<?php use App\Models\DoctorSchedule; ?>
<h1>医生->排班<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/doctor/all') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<div class="row">
  <form action="{{ url('admin/doctor/update-schedule') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="doctor_id" value="{{ $doctorInfo->id }}" />
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>姓名</label>
      <div class="col-sm-6">
        <input type="text" name="name" class="form-control" placeholder="姓名" value="{{ $doctorInfo->name }}" readonly="true">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>排班</label>
      <div style="margin-left: 15em;margin-top: 1em;" class="col-sm-12">
        <?php $DoctorScheduleList = DoctorSchedule::getList($doctorInfo->id);?>
        <?php $weekList = \Config::get('date.weekList'); ?>
        <?php for($i=1; $i<= 7; $i++) { ?>
        <div style="display:inline-block;">
          <div class="col-sm-3">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">{{ $weekList[$i] }}</span>
              <select name="week[{{ $i }}]" class="form-control selectboxit" aria-describedby="basic-addon1">
                @foreach(DoctorSchedule::$scheduleList as $k => $v)
                @if($DoctorScheduleList)
                <option value="{{ $k }}" @if($k == Input::old("week[{$i}]", $DoctorScheduleList[$i-1])) selected @endif>{{ $v }}</option>
                @else
                <option value="{{ $k }}" @if($k == Input::old("week[{$i}]", [])) selected @endif>{{ $v }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
    <hr>
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
<script src="{{ asset('/backend/js/yichao/doctor.js') }}" ></script>
@stop