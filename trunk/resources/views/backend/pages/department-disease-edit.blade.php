@extends('backend.layout')
@section('page_title')
<?php use App\Models\User; ?>
<?php use App\Models\DepartmentDisease; ?>
<?php use App\Models\HospitalDepartment; ?>
<?php $hospital_id = Request::Input('hospital_id') ?>
<h1>科室->病症管理<span class="middle-sep mg-x-10"></span><a href="{{ url('admin/hospital-department/all') }}?hospital_id={{ $hospital_id }}" class="btn btn-info">返回</a>
</h1>
@stop
@section('content')
<div class="row">
  <form action="{{ url('admin/department-disease/update') }}" method="post" accept-charset="utf-8" class="form-horizontal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="department_id" value="{{ $departmentInfo->id }}" />
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>科室名称</label>
      <div class="col-sm-6">
        <input type="text" name="name" class="form-control" placeholder="科室名称" value="{{ Input::old('name', $departmentInfo->name) }}" readonly="true">
      </div>
    </div>
    <?php use App\Models\Category; ?>
    <?php use App\Models\DoctorDisease; ?>
    <?php $departmentDiseaseList = DepartmentDisease::getDepartmentDiseaseList($departmentInfo->id);?>
    <?php $list = Category::getDiseaseSymptomsOption();?>
    @if(count($list))
    <div class="form-group">
      <label class="col-sm-2 control-label"><i class="required-star">*</i>选择病症</label>
      <div class="col-sm-6 panel panel-default" style="padding-top:15px;margin-left: 1em;margin-top: 1em;">
          @foreach($list as $k => $v)
          <div class="panel panel-default">
            <div class="col-sm-4">
              <label class="control-label">{{ $v['name'] }}</label>
            </div>
            <div class="col-sm-12" style="clear:both;">
              <?php $disease_symptoms = Input::old('disease_symptoms', $departmentDiseaseList) ?>
              @foreach($v['list'] as $k2 => $v2)
              <label style="display:inline-block"><span><input type="checkbox" style="margin-right:5px;" name="disease_symptoms[]" value="{{ $v2['id'] }}" @if(in_array($v2['id'], $departmentDiseaseList)) checked @endif />{{ $v2['name'] }}</span></label>
              @endforeach
            </div>
          </div>
          @endforeach
      </div>
    </div>
    @endif 
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