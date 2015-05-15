@extends('backend.layout')
@section('page_title')
<?php use App\Models\User; ?>
<?php use App\Models\Hospital; ?>
<?php use App\Models\HospitalDepartment; ?>
<?php $hospital_id = Request::Input('hospital_id') ?>
<h1>医生->标签管理<span class="middle-sep mg-x-10"></span><a href="{{ Cache::get('referer') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<div class="row">
  <form action="{{ url('admin/doctor/update-tag') }}?r={{ uniqid() }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="doctor_id" value="{{ $doctorInfo->id }}" />
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>姓名</label>
      <div class="col-sm-6">
        <input type="text" name="name" class="form-control" placeholder="姓名" value="{{ $doctorInfo->name }}" readonly="true">
      </div>
    </div>
    @include('backend.lib.doctor-tag')
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
@stop