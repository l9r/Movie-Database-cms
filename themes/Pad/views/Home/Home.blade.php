@extends('Main.Boilerplate')

@section('assets')
  @parent
  {{ HTML::style('themes/original/assets/css/slider-single.css') }}
@stop

@section('bodytag')
	<body id="home" class="nav-trans animate-nav">
@stop

@section('nav')
	@include('Partials.Navbar')
@stop

@section('content')
 	{{ $content }}
@stop

@section('scripts')

	{{ HTML::script('assets/js/slick.min.js') }}

	<script>
		vars.trailersPlayer = '<?php echo $options->trailersPlayer(); ?>';
        ko.applyBindings(app.viewModels.home, $('.content')[0]);
    </script>

@stop