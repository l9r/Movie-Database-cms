@extends('Main.Boilerplate')

@section('bodytag')
	<body id="titles-index">
@stop


@section('content')

	<div class="container" id="content">

		<div id="pagi-bar" class="row">
			<button data-bind="click: app.paginator.previousPage, enable: app.paginator.hasPrevious" class="previous col-xs-3 btn btn-primary">{{ trans('dash.prev') }}</button>

			<div class="col-xs-6 pagi-pages">
				<span data-bind="text: app.paginator.currentPage()"></span> {{ trans('main.outOf') }} 
				<span data-bind="text: app.paginator.totalPages()"></span> {{ trans('main.pages') }}
			</div>

			<button data-bind="click: app.paginator.nextPage, enable: app.paginator.hasNext" class="next col-xs-3 btn btn-primary">{{ trans('dash.next') }}</button>
		</div>

		<section data-bind="foreach: sourceItems" class="row">

			<figure class="col-sm-4 col-md-3 col-lg-2 pretty-figure">
				<a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans.people+'/'+id+'-'+name.replace(/\s+/g, '-').toLowerCase() }"><img class="img-responsive" data-bind="attr: { src: image, alt: name }"></img></a>
				<figcaption><a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans.people+'/'+id+'-'+name.replace(/\s+/g, '-').toLowerCase() }, text: name.trunc(30)"></a></figure>
			</figure>
			
		</section>
  	
	</div>

@stop

@section('scripts')
	<script>
		vars.trans.people = '<?php echo strtolower(trans("main.people")); ?>';
		app.paginator.start(app.viewModels.actors, '#content', 18);
	</script>
@stop
