@extends('backend.layout')
@section('page_title')
<h1>编辑科室<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/department/all') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<?php use App\Models\Hospital; ?>
<?php use App\Models\Category; ?>
<?php use App\Models\Department; ?>
<?php $hospital_id = Request::Input('hospital_id') ?>
<div class="row">
  <form action="{{ url('admin/department/create') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="{{ $departmentInfo->id }}" />
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>科室名称</label>
      <div class="col-sm-6">
        <input type="text" name="name" class="form-control" placeholder="科室名称唯一" value="{{ $departmentInfo->name }}">
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
@stop