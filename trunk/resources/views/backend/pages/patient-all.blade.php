@extends('backend.layout')
@section('page_title')
<h1>患者管理<span class="middle-sep mg-x-10"></span><a href="{{ url('/admin/patient/new') }}" class="btn btn-info">添加</a></h1>
@stop
@section('content')
<?php use App\Models\User;?>
<?php use App\Models\Patient;?>
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-filter col-md-12 row">
            <form action="" method="get" accept-charset="utf-8">
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" value="{{Input::get('keyword')}}" placeholder="患者姓名">
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
                    <th>用户名</th>
                    <th>姓名</th>
                    <th>性别</th>
                    <th>年龄</th>
                    <th>证件类型</th>
                    <th>证件内容</th>
                    <th>添加时间</th>
                    <th>状态</th>
                    <th class="do">操作</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($patientList))
                <tr>
                    <td colspan="8">无数据</td>
                </tr>
                @else
                @foreach($patientList as $patient)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{ $patient->id }}">
                    </td>
                    <td class="td-id">{{ $patient->id }}</td>
                    <td>@if(isset($patient->userInfo->name)) {{ $patient->userInfo->name }} @else —— @endif</td>
                    <td>{{ $patient->name }}</td>
                    <td>{{ User::$genderList[$patient->gender] }}</td>
                    <td>{{ $patient->age }}</td>
                    <td>@if($patient->cert_type == Patient::CERT_TYPE_ID_CARD) 身份证 @else @endif</td>
                    <td>{{ $patient->cert_id }}</td>
                    <td>{{ $patient->created_at->format('Y-m-d H:i') }}</td>
                    <td>@if($patient->status == Patient::STATUS_OK) 正常 @else 已删除 @endif</td>
                    <td class="do">
                        <a href="{{ url('admin/patient/edit') }}?patient_id={{ $patient->id }}"><span class="entypo-pencil"></span>编辑</a>
                        <a class="red" href="javascript:void(0);" id="common_delete_{{ $patient->id }}" model="patient"><span class="entypo-cancel-circled"></span>删除</a>
                    </td>
                </tr>
                @endforeach
                <tr>
                  <td colspan="9">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $patientList])</div>
                    </div>
                  </td>
                </tr>
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