@extends('backend.layout')
@section('page_title')
<?php use App\Models\DiseaseSymptoms;?>
<?php use App\Models\Category;?>
<h1>病症管理<span class="middle-sep mg-x-10"></span><a href="{{ url('/admin/disease-symptoms/new') }}" class="btn btn-info">新建</a></h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-filter col-md-12 row">
            <form action="" method="get" accept-charset="utf-8">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="col-md-2">
                    <select class="selectboxit auto-submit" data-target=".filter-submit-button" name="category_id">
                        <option value='0' >--分类--</option>
                        <?php  $categoryList = App\Models\Category::getDiseaseSymptoms()?>
                        @foreach($categoryList as $k => $v)
                        <option value="{{ $v->id }}" @if(Request::Input('category_id') == $v->id) selected @endif>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" value="{{Input::get('keyword')}}" placeholder="名称">
                </div>
                <div class="col-md-1">
                    <input type="submit" class="submit-btn btn btn-blue filter-submit-button" value="搜索" />
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
                    <th>名称</th>
                    <th>分类</th>
                    <th>添加时间</th>
                    <th>状态</th>
                    <th class="do">操作</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($diseaseSymptomsList))
                <tr>
                    <td colspan="8">无数据</td>
                </tr>
                @else
                @foreach($diseaseSymptomsList as $diseaseSymptoms)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{ $diseaseSymptoms->id }}">
                    </td>
                    <td class="td-id">{{ $diseaseSymptoms->id }}</td>
                    <td>{{ $diseaseSymptoms->name }}</td>
                    <td>{{ Category::showTitle($diseaseSymptoms->category_id) }}</td>
                    <td>{{isset($diseaseSymptoms->created_at) ? $diseaseSymptoms->created_at->format('Y-m-d H:i') : '无'}}</td>
                    <td>@if($diseaseSymptoms->status == DiseaseSymptoms::STATUS_OK) 正常 @else 已删除 @endif</td>
                    <td class="do">
                        <a href="{{ url('admin/disease-symptoms/edit') }}?diseaseSymptomsId={{ $diseaseSymptoms->id }}"><span class="entypo-pencil"></span>编辑</a>
                        <a class="red" href="javascript:void(0);" id="common_delete_{{ $diseaseSymptoms->id }}" model="disease-symptoms"><span class="entypo-cancel-circled"></span>删除</a>
                    </td>
                </tr>
                @endforeach
                <tr><td colspan="9">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $diseaseSymptomsList])</div>
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