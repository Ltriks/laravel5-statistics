@extends('backend.layout')
@section('page_title')
<h1>添加医院<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/hospital/all') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<?php use App\Models\Hospital; ?>
<div class="row">
  <form action="{{ url('admin/hospital/create') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>名称</label>
      <div class="col-sm-6">
        <input type="text" name="name" class="form-control" placeholder="名称必须唯一" value="{{ Input::old('name') }}"></div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">等级</label>
      <div class="col-sm-3">
        <select class="selectboxit" name="level">
          <option value="0">请选择</option>
          @foreach(Hospital::$levelList as $k => $v)
          <option value="{{ $k }}" @if($k == Input::old('level')) selected @endif>{{ $v }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">类型</label>
      <div class="col-sm-3">
        <select class="selectboxit" name="type">
          <option value="0">请选择</option>
          @foreach(Hospital::$typeList as $k => $v)
          <option value="{{ $k }}" @if($k == Input::old('type')) selected @endif>{{ $v }}</option>
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
      <label class="col-sm-2 control-label">图片</label>
      <div class="col-sm-5">
        <div class="input-group">
          <input type="url" id="img-url" data-preview="#photo-view-img" placeholder="填写图片URL或者选择本地文件上传" name="image" value="{{ Input::old('image') }}" class="url-image form-control">
          <span class="input-group-btn">
          <button class="btn btn-white file-uploader" data-option="{urlContainer:'#img-url', accept:{extensions: 'jpeg,jpg,png,gif,bmp', mimeTypes:'image/*'},maxSize:200}" type="button"><i class="entypo-popup"></i> 本地文件</button>
          </span>
        </div><!-- /input-group -->
      </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">简介</label>
        <div class="col-sm-6">
            <textarea name="intro" class="form-control" placeholder="" style="height: 200px;">{{ Input::old('intro') }}</textarea>
        </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">地址</label>
      <div class="col-sm-6">
        <input type="text" name="address" class="unsigned-int-input form-control" max='30' placeholder="医院地址" value="{{ Input::old('address') }}" >
      </div>
      <div class="col-md-4" style=" padding-top: 5px; "></div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">排序</label>
      <div class="col-sm-2">
        <input type="text" name="sort" class="unsigned-int-input form-control" max='30' placeholder="0~10000" value="{{ Input::old('sort') }}" >
      </div>
      <div class="col-md-4" style=" padding-top: 5px; "></div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">价格</label>
      <div class="col-sm-2">
        <input type="text" name="price" class="unsigned-int-input form-control" max='30' placeholder="价格" value="{{ Input::old('price') }}" >
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