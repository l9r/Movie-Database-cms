@extends('Main.Boilerplate')

@section('title')
	<title>{{ trans('main.news archive') }} - {{ $options->getSiteName() }}</title>
@stop

@section('bodytag')
	<body id="news-index">
@stop

@section('content')
	
	<div class="container content">
		<div id="pagi-bar" class="row">
			<button data-bind="click: app.paginator.previousPage, enable: app.paginator.hasPrevious" class="previous col-xs-3 btn btn-primary">{{ trans('dash.prev') }}</button>

			<div class="col-xs-6 pagi-pages">
				<span data-bind="text: app.paginator.currentPage()"></span> {{ trans('main.outOf') }} 
				<span data-bind="text: app.paginator.totalPages()"></span> {{ trans('main.pages') }}
			</div>

			<button data-bind="click: app.paginator.nextPage, enable: app.paginator.hasNext" class="next col-xs-3 btn btn-primary">{{ trans('dash.next') }}</button>
		</div>

		<section data-bind="foreach: sourceItems">
			<div class="media">
			  	<a class="col-sm-4" data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans.news+'/'+id+'-'+title.replace(/\s+/g, '-').toLowerCase() }">
			    	<img class="media-object img-responsive" data-bind="attr: { src: image, alt: title }">
			  	</a>
			  	<div class="media-body">
			    	<h3 class="media-heading"><a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans.news+'/'+id+'-'+title.replace(/\s+/g, '-').toLowerCase() }, text: title"></a></h3>
			    	<p data-bind="html: body.replace(/(<([^>]+)>)/ig,'').trunc(400)"></p>
			    	<p class="pull-left text-muted">{{ trans('dash.published') }}: <span data-bind="text: created_at"></span></p>
			    	<!-- ko if: source -->
			    	<p class="pull-right text-muted">{{ trans('main.source') }}: <span data-bind="text: source"></span></p>
			    	<!-- /ko -->
			  	</div>
			</div>
		</section>
	</div>

@stop

@section('scripts')
	<script>
        vars.trans.news = '<?php echo strtolower(trans("main.news")); ?>';
		app.paginator.start(app.viewModels.news, '.content', 15);
	</script>
@stop