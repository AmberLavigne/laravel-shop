@extends('layouts.app')
@section('title', '错误')
@section('content')
	<div class="panel panel-default">
		<div class="panel-heading">错误</div>
		<div class="panel-body">
			<h4>{{ $msg }}</h4>
			<a href="{{ route('root') }}" class="btn btn-primary btn-sm">返回首页</a>
		</div>
	</div>
@stop