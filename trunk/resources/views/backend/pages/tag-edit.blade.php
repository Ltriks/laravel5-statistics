@extends('backend.layout')
@section('page_title')
<?php use App\Models\Tag; ?>
<?php $type = Input::get('type', Tag::OBJECT_TYPE_DOCTOR); ?>
<h1>编辑{{ Tag::$nameList[$type] }}标签<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/tag/all') }}?type={{ $type }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<div class="row">
  <form action="{{ url('admin/tag/update') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="{{ $tagInfo->id }}" />
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>名称</label>
      <div class="col-sm-6">
        <input type="text" name="name" class="form-control" placeholder="名称必须唯一并且长度为4" value="{{ Input::old('name', $tagInfo->name) }}" maxlength="4"></div>
    </div>
    <!-- <div class="form-group">
      <label class="col-sm-2 control-label">排序</label>
      <div class="col-sm-2">
        <input type="text" name="sort" class="unsigned-int-input form-control" max='30' placeholder="0~10000" value="{{ Input::old('sort') }}" >
      </div>
      <div class="col-md-4" style=" padding-top: 5px; "></div>
    </div> -->
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