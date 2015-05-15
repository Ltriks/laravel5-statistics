<?php use App\Models\Message; ?>
@extends('backend.layout')
@section('page_title')
<h1>添加消息<span class="middle-sep mg-x-10"></span><a href="{{Cache::get('referer')}}" class="btn btn-info">返回列表</a>
</h1>
@stop
@section('content')
<div class="row">
    <form action="{{url('/admin/message/create')}}" method="post" accept-charset="utf-8" class="form-horizontal">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <div class="form-group">
            <label class="col-sm-2 control-label"><i class="required-star">*</i> 内容</label>
            <div class="col-sm-5">
                <textarea name="message" id="message_content" maxlength="40" class="autogrow form-control" placeholder="推送消息不宜过长，请尽量精简，最长40字符">{{Input::old('message')}}</textarea>
            </div>
            <div class="col-md-4 ugc-tips">
              <div class="alert alert-info">
                <h4>操作说明：</h4>
                <p>1.<strong>对象</strong> 一栏请填写 <strong>类型</strong> 中所选择的类型对应的值，比如当 <strong>类型</strong> 为 <strong>链接</strong> 时请在 <strong>对象</strong> 一栏填写URL</p>
                <p>2.填写其它数字ID时，请确认该ID存在</p>
                <p>3.<strong>消息类型为系统消息时，则推送全部用户！</strong></p>
              </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><i class="required-star">*</i> 内容类型</label>
            <div class="col-sm-3">
                <select name="action" id="action" class="form-control selectboxit placeholder-changer" data-placeholder-target="#object">
                    <option value="{{ Message::ACTION_UNKNOWN }}" @if(Input::old('action', '') !== '' && Input::old('action', '') == Message::ACTION_UNKNOWN) selected @endif data-placeholder="">请选择</option>
                    <option value="{{ Message::ACTION_TEXT }}" @if(Input::old('action', '') !== '' && Input::old('action', '') == Message::ACTION_TEXT) selected @endif data-placeholder="文本">文本</option>
                    <option value="{{ Message::ACTION_LINK }}" @if(Input::old('action', '') !== '' && Input::old('action', '') == Message::ACTION_LINK) selected @endif data-placeholder="请填写链接URL">链接</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"><i class="required-star">*</i> 对象</label>
            <div class="col-sm-5">
                <input type="text" id="object" placeholder="" id="object_id" name="object" value="{{Input::old('object')}}" class="url-image form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">消息类型</label>
            <div class="col-sm-2">
                <select name="type" class="selectboxit">
                    <option value="0">系统</option>
                    <option value="1">用户</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"> 指定用户</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <input type="text" name="target_user" value="{{Input::old('target_user')}}" class="form-control" placeholder="填写用户ID,多个值请使用半角逗号分隔">
                    <span class="input-group-btn">
                        <button class="btn btn-white typeahead-search-btn" data-target="#user-search-box" type="button"><i class="entypo-popup"></i> <span>搜索</span></button>
                    </span>
                </div>
                <!-- typeahead -->
                <input type="text" id="user-search-box" placeholder="ID、手机号等关键字皆可搜索, 输入关键字后等待结果显示" data-target="[name='target_user']" value="" class="serchbox form-control typeahead" data-remote="{{url('admin/user/all')}}?keyword=%QUERY" data-view-callback="userTpl" data-empty-string="未找到相关用户" displayKey="id">
            </div>
        </div>

        <hr class="clearfix">
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
                <button type="submit" id="submit-btn" class="btn btn-success">提交</button>
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
<script src="{{ asset('/backend/js/typeahead.min.js') }}"></script>
<script>
    $(function(){
        showUser();
        $("[name='type']").on('change', function(){
            showUser();
        })
        function showUser()
        {
            var type = $("[name='type']").val();
            if (type == 0) {
                $("[name='target_user']").closest(".form-group").hide();
            } else {
                $("[name='target_user']").closest(".form-group").show();
            }
        }
        $('#submit-btn').on('click', function() {
            if ($('#message_content').val().length && $('#action').val().length) {
                if(!confirm('消息创建之后不可再修改，确认创建？')) {
                    return false;
                }
            };
        });

    });
</script>
@stop