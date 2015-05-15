@extends('backend.layout')
@section('page_title')
<h1>编辑患者<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/patient/all') }}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<?php use App\Models\User; ?>
<?php use App\Models\Patient; ?>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-body">
                <form action="{{url('/admin/patient/update')}}" method="post" accept-charset="utf-8" class="form-horizontal">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id" value="{{ $patientInfo->id }}"/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"> 姓名</label>
                        <div class="col-sm-6">
                            <input type="text" name="name" class="form-control" placeholder="患者姓名" value="{{Input::old('name', $patientInfo->name)}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">性别</label>
                        <div class="col-sm-6">
                            @if(Input::old('gender', -1) == -1)
                            <label class="radio-inline">
                              <input type="radio" name="gender" @if(Input::old('gender', $patientInfo->gender) == 1) checked @endif value="1"> 男
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="gender" @if(Input::old('gender', $patientInfo->gender) == 0) checked @endif  value="0"> 女
                            </label>
                            @else
                            <label class="radio-inline">
                              <input type="radio" name="gender" @if(Input::old('gender') == 1) checked @endif value="1"> 男
                            </label>
                            <label class="radio-inline">
                              <input type="radio" name="gender" @if(Input::old('gender') == 0) checked @endif  value="0"> 女
                            </label>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"> 年龄</label>
                        <div class="col-sm-6">
                            <input type="text" name="age" class="form-control" placeholder="年龄" value="{{Input::old('age', $patientInfo->age)}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">证件类型</label>
                        <div class="col-sm-5">
                            <select name="cert_type" class="selectboxit">
                                <option value="0" @if($patientInfo->cert_type == Patient::CERT_TYPE_ID_CARD) selected @endif>身份证</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"> 证件内容</label>
                        <div class="col-sm-6">
                            <input type="text" name="cert_id" class="form-control" placeholder="证件内容" value="{{Input::old('cert_id', $patientInfo->cert_id)}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"> 简介</label>
                        <div class="col-sm-6">
                            <textarea name="brief" class="form-control" placeholder="" style="height: 200px;">{{ Input::old('brief', $patientInfo->brief) }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-success">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /col-md-8-->
</div>
@stop

@section('page_css')
<link rel="stylesheet" href="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.css') }}">
@stop

@section('page_js')
<script src="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.min.js') }}" ></script>
@stop