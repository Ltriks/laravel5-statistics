@extends('backend.layout')
@section('page_title')
<?php use App\Models\Doctor; ?>
<?php use App\Models\Hospital; ?>
<?php use App\Models\Department; ?>
<?php use App\Models\HospitalDepartment; ?>
<?php $hospital_id = Request::Input('hospital_id', 0); ?>
<?php $hospicalList = Hospital::getHospitalList(); ?>
<h1>@if($hospital_id){{ Hospital::getName($hospital_id) }}-@endif 医生管理<span class="middle-sep mg-x-10"></span><a href="{{ url('/admin/doctor/new') }}?hospital_id={{ $hospital_id }}" class="btn btn-info">添加</a></h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-filter col-md-12 row">
            <form action="" method="get" accept-charset="utf-8">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="col-md-2">
                  <select class="selectboxit auto-submit" data-target=".submit-btn" name="sort">
                    <option value='' >--排序--</option>
                    <option value='id-DESC' @if(Request::Input('sort') == 'id-DESC') selected @endif>创建时间 - ↓</option>
                    <option value='id-ASC' @if(Request::Input('sort') == 'id-ASC') selected @endif>创建时间 - ↑</option>
                  </select> 
                </div>
                <div class="col-md-2">
                  <select name="hospital_id" class="selectboxit auto-submit" data-target=".submit-btn" name="sort">
                    <option value='0' >--医院名称--</option>
                    @foreach($hospicalList as $k => $v)
                    <option @if(Request::Input('hospital_id', $hospital_id) == $v->id) selected @endif value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                  </select> 
                </div>
                <div class="col-md-2">
                  <select name="level" class="selectboxit auto-submit" data-target=".submit-btn" name="sort">
                    <option value='0' >--级别--</option>
                    @foreach(Doctor::$levelList as $k => $v)
                    <option @if(Request::Input('level', 0) == $k) selected @endif value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                  </select> 
                </div>
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" value="{{Input::get('keyword')}}" placeholder="名称">
                </div>
                <div class="col-md-1">
                    <input type="submit" class="submit-btn btn btn-blue" value="搜索" />
                </div>
            </form>
        </div>
        <hr class="clearfix">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="select-all" data-target=".chkbox">
                    </th>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>所在医院</th>
                    <th>级别</th>
                    <th>是否认证</th>
                    <th>添加时间</th>
                    <th>状态</th>
                    <th class="do">操作</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($doctorList))
                <tr>
                    <td colspan="8">无数据</td>
                </tr>
                @else
                @foreach($doctorList as $doctor)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{ $doctor->id }}">
                    </td>
                    <td class="td-id">{{ $doctor->id }}</td>
                    <td>{{ $doctor->name }}@if($doctor->is_verified) <span>&nbsp;&nbsp;<img width="18" src="{{ asset('backend/images/b4_certification@2x.png') }}" /></span>@endif</td>
                    <td>{{ $doctor->hospitalInfo->name }}</td>
                    <td>{{ isset(Doctor::$levelList[$doctor->level])?Doctor::$levelList[$doctor->level]:'无' }}</td>
                    <td>@if($doctor->is_verified) 已认证 @else 未认证 @endif</td>
                    <td>{{isset($doctor->created_at) ? $doctor->created_at->format('Y-m-d H:i') : '无'}}</td>
                    <td>@if($doctor->status == HospitalDepartment::STATUS_OK) 正常 @else 已删除 @endif</td>
                    <td class="do">
                        <a href="{{ url('admin/reg/all') }}?doctor_id={{ $doctor->id }}" target="_blank"><span class="entypo-newspaper"></span>查看挂号单</a>
                        <a href="{{ url('admin/rating/all') }}?doctor_id={{ $doctor->id }}" target="_blank"><span class="entypo-newspaper"></span>评价</a>
                        <!-- <a href="{{ url('admin/doctor-disease/edit') }}?doctor_id={{ $doctor->id }}&hospital_id={{ $doctor->hospital_id }}"><span class="entypo-pencil"></span>病症管理</a> -->
                        <a href="{{ url('admin/doctor/edit') }}?doctor_id={{ $doctor->id }}&hospital_id={{ $doctor->hospital_id }}"><span class="entypo-pencil"></span>编辑</a>
                        <a class="red" href="javascript:void(0);" id="common_delete_{{ $doctor->id }}" model="doctor"><span class="entypo-cancel-circled"></span>删除</a>
                    </td>
                </tr>
                @endforeach
                <tr><td colspan="9">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $doctorList])</div>
                    </div>
                </td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@stop

@section('page_css')
<link rel="stylesheet" href="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.css') }}">
<link rel="stylesheet" href="{{ asset('/backend/js/daterangepicker/daterangepicker-bs3.css') }}">
@stop

@section('page_js')
<script src="{{ asset('/backend/js/selectboxit/jquery.selectBoxIt.min.js') }}" ></script>
<script src="{{ asset('/backend/js/daterangepicker/moment.min.js') }}" ></script>
<script src="{{ asset('/backend/js/daterangepicker/daterangepicker.js') }}" ></script>
@stop