@extends('backend.layout')
@section('page_title')
<?php use App\Models\Hospital; ?>
<?php use App\Models\HospitalDepartment; ?>
<?php $hospital_id = Request::Input('hospital_id') ?>
<?php $hospicalList = Hospital::getHospitalList(); ?>
<?php $hospitalDepartments = HospitalDepartment::getTopList(); ?>
<h1>编辑科室<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/hospital-department/all') }}?hospital_id={{ $hospital_id }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<div class="row">
  <form action="{{ url('admin/hospital-department/update') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="{{ $departmentInfo->id }}">
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>所属医院</label>
      <div class="col-sm-6 panel panel-default" style="padding-top:15px;margin-left: 1em;margin-top: 1em;">
          <div class="panel panel-default">
            <div class="col-sm-4">
              <label class="control-label">医院名称</label>
            </div>
            <div class="col-sm-12" style="clear:both;">
              @foreach($hospicalList as $k => $v)
              <label style="display:inline-block"><span><input type="radio" style="margin-right:5px;" name="hospital_id" value="{{ $v->id }}" @if(Input::old('hospital_id', $departmentInfo->hospital_id) == $v->id) checked @endif />{{ $v->name }}</span></label>
              @endforeach
            </div>
          </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-2 control-label">所属科室</label>
      <div class="col-sm-3">
        <select class="selectboxit" name="parent_id">
          <option value="0">--无--</option>
          @foreach($hospitalDepartments as $k => $v)
          <option value="{{ $v->id }}" @if($k == Input::old('parent_id', $departmentInfo->parent_id)) selected @endif>{{ $v->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>名称</label>
      <div class="col-sm-6">
        <input type="text" name="name" class="form-control" placeholder="科室名称唯一" value="{{ Input::old('name', $departmentInfo->name) }}">
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
      <label class="col-sm-2 control-label">图片</label>
      <div class="col-sm-5">
        <div class="input-group">
          <input type="url" id="img-url" data-preview="#photo-view-img" placeholder="填写图片URL或者选择本地文件上传" name="photo" value="{{ Input::old('photo', $departmentInfo->photo) }}" class="url-image form-control">
          <span class="input-group-btn">
          <button class="btn btn-white file-uploader" data-option="{urlContainer:'#img-url', accept:{extensions: 'jpeg,jpg,png,gif,bmp', mimeTypes:'image/*'},maxSize:200}" type="button"><i class="entypo-popup"></i> 本地文件</button>
          </span>
        </div><!-- /input-group -->
      </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">简介</label>
        <div class="col-sm-6">
            <textarea name="brief" class="form-control" placeholder="" style="height: 200px;">{{ Input::old('brief', $departmentInfo->brief) }}</textarea>
        </div>
    </div>
    @include('backend.lib.disease-symptoms')
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
<script src="{{ asset('/backend/js/yichao/department-hospital.js') }}" ></script>
@stop