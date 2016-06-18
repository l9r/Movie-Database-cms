@extends('Main.Boilerplate')

@section('title')
	<title>{{ $page->title . ' - ' . $options->getSiteName() }}</title>
@stop

@section('bodytag')
	<body id="pages-show">
@stop

@section('content')
	
	<div class="main-container container">
		
		<h1 class="page-title">{{ $page->title }}</h1>

		<p class="page-body">{{ $page->body }}</p>

	</div>

@stop