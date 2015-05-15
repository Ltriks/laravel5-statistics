@extends('backend.layout')
@section('page_title')
<h1>控制面板</h1>
@stop
@section('content')
<div class="row homepage">
    <div class="col-md-12"><h1>{{ getUserInfo('nickname') }} 你好，欢迎回来！</h1></div>
</div>
@stop