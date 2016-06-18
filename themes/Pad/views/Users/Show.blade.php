@extends('Main.Boilerplate')

@section('title')
	<title>{{{ $user->username }}} - {{ trans('users.profile') }}</title>
@stop

@section('assets')
	@parent

	{{ HTML::style('themes/original/assets/css/pikaday.css') }}
@stop

@section('bodytag')
	<body id='users-show'>
@stop

@section('content')
	
	<div class="container push-footer-wrapper" id="content">
		
		@include('Users.Partials.Header')

		<div class="lists-wrapper">

			@include('Titles.Partials.FilterBar')

			<div class="clearfix">
	            <div class="index-pagination"></div>
	        </div>

	  		<section data-bind="foreach: sourceItems" class="row">

	  			<figure class="col-sm-4 col-md-3 col-lg-2 pretty-figure" data-bind="attr: { data: $index }">
	  				<a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[type]+'/'+id+'-'+title.replace(/\s+/g, '-').toLowerCase() }"><img class="img-responsive" data-bind="attr: { src: poster, alt: title }"></a>
	                {{ Hooks::renderHtml('Titles.Index.ForEachMovie') }}

	                <figcaption data-bind="text: title"></figcaption>
	  			</figure>
	  			
	  		</section>

	        <div class="clearfix">
	            <div class="index-pagination bottom-pagination"></div>
	        </div>		
		</div>
	</div>

@stop

@section('ads')
@stop

@section('scripts')
	<script>
		vars.trans.movie = '<?php echo strtolower(trans("main.movies")); ?>';
		vars.trans.series = '<?php echo strtolower(trans("main.series")); ?>';

		app.viewModels.profile.start('<?php echo $user->id; ?>');
	</script>
@stop
