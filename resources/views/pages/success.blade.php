@extends('layouts.app')

@section('title', '操作成功')

@section('content')
	<div class="panel panel-default">
		<div class="panel-heading">操作成功</div>
		<div class="panel-body text-center">
			<h3>{{ $msg }}</h1>
			<a href="{{ route('root') }}" class="btn btn-primary btn-sm">返回首页</a>
		</div>
	</div>
@stop