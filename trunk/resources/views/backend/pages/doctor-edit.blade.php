@extends('backend.layout')
@section('page_title')
<?php use App\Models\User; ?>
<?php use App\Models\Hospital; ?>
<?php use App\Models\HospitalDepartment; ?>
<?php $hospital_id = Request::Input('hospital_id') ?>
<?php $hospicalList = Hospital::getHospitalList(); ?>
<?php $hospitalDepartments = HospitalDepartment::getTopList(); ?>
<h1>编辑医生<span class="middle-sep mg-x-10"></span><a href="{{ Cache::get('referer') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<div class="row">
  <form action="{{ url('admin/doctor/update') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="{{ $doctorInfo->id }}" />
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>姓名</label>
      <div class="col-sm-6">
        <input type="text" name="name" class="form-control" placeholder="姓名" value="{{ Input::old('name', $doctorInfo->name) }}">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>性别</label>
      <div class="col-sm-3">
        <select class="selectboxit" name="gender">
          @foreach(User::$genderList as $k => $v)
          <option value="{{ $k }}" @if($k == Input::old('gender', $doctorInfo->gender)) selected @endif>{{ $v }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>级别</label>
      <div class="col-sm-3">
        <select class="selectboxit" name="level">
          @foreach(App\Models\Doctor::$levelList as $k => $v)
          <option value="{{ $k }}" @if($k == Input::old('level', $doctorInfo->level)) selected @endif>{{ $v }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <br>
    <div class="clearfix">
      <label class="col-sm-2 control-label"></label>
      <div class="col-sm-5">
        <div class="fileinput-new thumbnail" >
          <img id="photo-view-img" data-src="holder.js/50%x80/text:200k以内的图片" alt="..." src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMzYiIGhlaWdodD0iODAiPjxyZWN0IHdpZHRoPSIyMzYiIGhlaWdodD0iODAiIGZpbGw9IiNlZWUiLz48dGV4dCB0ZXh0LWFuY2hvcj0ibWlkZGxlIiB4PSIxMTgiIHk9IjQwIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjE1cHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+MjAwa+S7peWGheeahOWbvueJhzwvdGV4dD48L3N2Zz4=">
        </div>
      </div>
    </div>
    <div class="clearfix">
      <label class="col-sm-2 control-label">头像</label>
      <div class="col-sm-5">
        <div class="input-group">
          <input type="url" id="img-url" data-preview="#photo-view-img" placeholder="填写图片URL或者选择本地文件上传" name="avatar" value="{{ Input::old('avatar', $doctorInfo->avatar) }}" class="url-image form-control">
          <span class="input-group-btn">
          <button class="btn btn-white file-uploader" data-option="{urlContainer:'#img-url', accept:{extensions: 'jpeg,jpg,png,gif,bmp', mimeTypes:'image/*'},maxSize:200}" type="button"><i class="entypo-popup"></i> 本地文件</button>
          </span>
        </div><!-- /input-group -->
      </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"> <i class="required-star">*</i>选择医院</label>
        <div class="col-sm-3">
            <select name="hospital_id" class="selectboxit">
              @foreach($hospicalList as $k => $v)
              <option @if(Input::old('hospital_id', $doctorInfo->hospital_id) == $v->id) selected @endif value="{{ $v->id }}">{{ $v->name }}</option>
              @endforeach
            </select>
        </div>
    </div>
    @include('backend.lib.department-doctor-select')
    <div class="form-group">
        <label class="col-sm-2 control-label">简介</label>
        <div class="col-sm-6">
            <textarea name="brief" class="form-control" placeholder="" style="height: 200px;">{{ Input::old('brief', $doctorInfo->brief) }}</textarea>
        </div>
    </div>
    @include('backend.lib.doctor-tag')
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>职称</label>
      <div class="col-sm-6">
        <input type="text" name="title" class="form-control" placeholder="职称" value="{{ Input::old('title', $doctorInfo->title) }}">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>工作年限</label>
      <div class="col-sm-3">
        <input type="text" name="experience" class="form-control" placeholder="工作年限" value="{{ Input::old('experience', $doctorInfo->experience) }}">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>毕业学校</label>
      <div class="col-sm-3">
        <input type="text" name="school" class="form-control" placeholder="毕业学校" value="{{ Input::old('school', $doctorInfo->school) }}">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>是否认证</label>
      <div class="col-sm-3">
        <select class="selectboxit" name="is_verified">
          @foreach(User::$verifiedList as $k => $v)
          <option value="{{ $k }}" @if($k == Input::old('is_verified', $doctorInfo->is_verified)) selected @endif>{{ $v }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>挂号费</label>
      <div class="col-sm-3">
        <input type="text" name="price" class="form-control" placeholder="挂号费" value="{{ Input::old('price', $doctorInfo->price) }}">
      </div>
    </div>
    <hr />
    <?php use App\Models\DoctorSchedule; ?>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>排班</label>
      <div style="margin-left: 15em;margin-top: 1em;" class="col-sm-10">
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
@include('backend.lib.fileuploader')
@stop

@section('page_css')
<link rel="stylesheet" href="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.css') }}">
@stop

@section('page_js')
<script src="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.min.js') }}" ></script>
@stop