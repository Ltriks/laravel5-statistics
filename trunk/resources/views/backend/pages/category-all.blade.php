@extends('backend.layout')
@section('page_title')
<?php use App\Models\Category;?>
<h1>所有{{ Category::$typeTitle[Input::get('type', 1)] }}<span class="middle-sep mg-x-10"></span><a href="{{ url('/admin/category/new') }}?type={{ Input::get('type', 1) }}" class="btn btn-info">新建</a></h1>
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
                    <th>名称</th>
                    <th>icon</th>
                    <th>添加时间</th>
                    <th>状态</th>
                    <th>排序</th>
                    <th class="do">操作</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($categoryList))
                <tr>
                    <td colspan="8">无数据</td>
                </tr>
                @else
                @foreach($categoryList as $category)
                <tr>
                    <td>
                        <input type="checkbox" name="id" class="chkbox" value="{{ $category->id }}">
                    </td>
                    <td class="td-id">{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>@if($category->icon) <img src="{{ $category->icon }}" height="100" /> @endif</td>
                    <td>{{isset($category->created_at) ? $category->created_at->format('Y-m-d H:i') : '无'}}</td>
                    <td>@if($category->status == Category::STATUS_OK) 正常 @else 已删除 @endif</td>
                    <td width="100"><input type="text" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-original-title="有效值：0 ~ 10000,越大越靠前" data-id="{{$category->id}}" data-old="{{$category->sort}}" value="{{$category->sort}}" class="unsigned-int-input sorter form-control" max-num="10000"></td>
                    <td class="do">
                        <a href="{{ url('admin/category/edit') }}?category_id={{ $category->id }}"><span class="entypo-pencil"></span>编辑</a>
                        <a class="red" href="javascript:void(0);" id="common_delete_{{ $category->id }}" model="category"><span class="entypo-cancel-circled"></span>删除</a>
                    </td>
                </tr>
                @endforeach
                <tr><td colspan="9">
                    <div class="table-actions row">
                        <div class="pull-right">@include('backend.lib.pager', ['object' => $categoryList])</div>
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