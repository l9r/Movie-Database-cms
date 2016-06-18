@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard" class="media-page">
@stop

@section('content')
	<section id="dash-container" class="with-filter-bar">

	@include('Dashboard.Partials.Sidebar')

	<section class="content">

        @include('Partials.MediaUploadPage')

	</section>
	</section>

@stop

@section('ads')
@stop

@section('scripts')

{{ HTML::script('assets/js/vendor/uploader.min.js') }}

<script>  
    app.viewModels.media.start();
	app.paginator.start(app.viewModels.media, $('#dash-container')[0], 24);
</script>

@stop

