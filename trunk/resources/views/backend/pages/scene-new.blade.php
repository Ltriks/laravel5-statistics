@extends('backend.layout')
@section('page_title')
<h1>新建场景<a href="{{ url('admin/scene/all') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<?php use App\Models\Scene; ?>
<div class="row">
  <form action="{{ url('admin/scene/create') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>名称</label>
      <div class="col-sm-6">
        <input type="text" name="title" class="form-control" placeholder="名称必须唯一" value=""></div>
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
      <label class="col-sm-2 control-label"><i class="required-star">*</i>场景Logo</label>
      <div class="col-sm-5">
        <div class="input-group">
          <input type="url" id="img-url" data-preview="#photo-view-img" placeholder="填写图片URL或者选择本地文件上传" name="logo" value="" class="url-image form-control">
          <span class="input-group-btn">
          <button class="btn btn-white file-uploader" data-option="{urlContainer:'#img-url', accept:{extensions: 'jpeg,jpg,png,gif,bmp', mimeTypes:'image/*'},maxSize:200}" type="button"><i class="entypo-popup"></i> 本地文件</button>
          </span>
        </div><!-- /input-group -->
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>排序</label>
      <div class="col-sm-2">
        <input type="text" name="sort" class="unsigned-int-input form-control" max='30' placeholder="0~10000" value="" >
      </div>
      <div class="col-md-4" style=" padding-top: 5px; "></div>
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