@extends('backend.layout')
@section('page_title')
<?php use App\Models\Hospital ?>
<?php use App\Models\Department ?>
<?php $hospital_id = Input::get('hospital_id', 0) ?>
<h1>@if($hospital_id){{ Hospital::getName($hospital_id) }}-@endif科室管理<span class="middle-sep mg-x-10"></span><a href="{{ url('/admin/department/new') }}?hospital_id={{ $hospital_id }}" class="btn btn-info">新建</a></h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-filter col-md-12 row">
            <form action="" method="get" accept-charset="utf-8">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="type" value="{{ Input::get('type', 1) }}">
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
                    <th>科室名称</th>
                    <th>添加时间</th>
                    <th>状态</th>
                    <th class="do">操作</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($departmentList))
                <tr>
                    <td colspan="8">无数据</td>
                </tr>
                @else
                @foreach($departmentList as $department)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{ $department->id }}">
                    </td>
                    <td class="td-id">{{ $department->id }}</td>
                    <td>{{ $department->name }}</td>
                    <td>{{isset($department->created_at) ? $department->created_at->format('Y-m-d H:i') : '无'}}</td>
                    <td>@if($department->status == Department::STATUS_OK) 正常 @else 已删除 @endif</td>
                    <td class="do">
                        <a href="{{ url('admin/department/edit') }}?department_id={{ $department->id }}"><span class="entypo-pencil"></span>编辑</a>
                        <a class="red" href="javascript:void(0);" id="common_delete_{{ $department->id }}" model="department"><span class="entypo-cancel-circled"></span>删除</a>
                    </td>
                </tr>
                @endforeach
                <tr><td colspan="9">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $departmentList])</div>
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