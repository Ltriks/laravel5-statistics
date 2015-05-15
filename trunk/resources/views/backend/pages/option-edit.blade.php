<?php use App\Models\Option; ?>
@extends('backend.layout')
@section('page_title')
<h1>系统配置</h1>
@endsection
@section('content')
  <div class="row">
  <form action="{{ url('/admin/option/save') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
      <label class="col-sm-2 control-label">Android版板更新</label>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">类型</label>
      <div class="col-sm-2">
        <select class="selectboxit" name="android_update_type">
          @foreach(Option::$updateTypeList as $k => $v)
          <option value="{{ $k }}" @if($k == Input::old('android_update_type', Option::get('android_update_type', 1))) selected @endif>{{ $v }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">提示标题</label>
      <div class="col-sm-6">
        <input type="text" name="android_alert_title" value="{{ Input::old('android_alert_title', Option::get('android_alert_title', '')) }}" placeholder="提示标题" class="form-control">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">提示内容</label>
      <div class="col-sm-6">
        <textarea name="android_alert_body" class="form-control" placeholder="提示内容" style="height: 200px;">{{ Input::old('android_alert_body', Option::get('android_alert_body', '')) }}</textarea>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">下载地址</label>
      <div class="col-sm-6">
        <input type="text" name="android_download" value="{{ Input::old('android_download', Option::get('android_download', '')) }}" placeholder="下载地址" class="form-control">
      </div>
    </div>
    <hr />
    <div class="form-group">
      <label class="col-sm-2 control-label">IOS版板更新</label>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">类型</label>
      <div class="col-sm-2">
        <select class="selectboxit" name="ios_update_type">
          @foreach(Option::$updateTypeList as $k => $v)
          <option value="{{ $k }}" @if($k == Input::old('ios_update_type', Option::get('ios_update_type', 0))) selected @endif>{{ $v }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">提示标题</label>
      <div class="col-sm-6">
        <input type="text" name="ios_alert_title" value="{{ Input::old('ios_alert_title', Option::get('ios_alert_title', '')) }}" placeholder="提示标题" class="form-control">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">提示内容</label>
      <div class="col-sm-6">
        <textarea name="ios_alert_body" class="form-control" placeholder="提示内容" style="height: 200px;">{{ Input::old('ios_alert_body', Option::get('ios_alert_body', '')) }}</textarea>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">下载地址</label>
      <div class="col-sm-6">
        <input type="text" name="ios_download" value="{{ Input::old('ios_download', Option::get('ios_download', '')) }}" placeholder="下载地址" class="form-control">
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
@endsection

@section('page_css')
<link rel="stylesheet" href="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.css') }}"  id="style-resource-3">
@endsection

@section('page_js')
<script src="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.min.js') }}" id="script-resource-11"></script>
@endsection