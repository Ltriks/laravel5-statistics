@extends('backend.layout')
@section('page_title')
<?php use App\Models\DiseaseSymptoms; ?>
<h1>新建病症<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/disease-symptoms/all') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<div class="row">
  <form action="{{ url('admin/disease-symptoms/create') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>选择分类</label>
      <div class="col-sm-3">
        <select name="category_id" class="selectboxit">
            <?php  $categoryList = App\Models\Category::getDiseaseSymptoms()?>
            @if($categoryList)
            @foreach($categoryList as $k => $v)
            <option value="{{ $v->id }}">{{ $v->name }}</option>
            @endforeach
            @else
            <option value="0">请选择</option>
            @endif
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>名称</label>
      <div class="col-sm-3">
        <input type="text" name="name" class="form-control" placeholder="名称必须唯一" value="{{ Input::old('name') }}"></div>
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