@extends('Main.Boilerplate')

@section('bodytag')
	<body id="titles-index">
@stop

@section('assets')
	@parent

  <meta name="fragment" content="!">
  <meta name="title" content="{{ trans('main.meta title') }}">
  <meta name="description" content="{{ trans('main.meta description') }}">
  <meta name="keywords" content="{{ trans('main.meta keywords') }}">

	{{ HTML::style('themes/original/assets/css/pikaday.css') }}
@stop

@section('content')

  	<div class="container" id="content">

  		@include('Titles.Partials.FilterBar')

        {{ Hooks::renderHtml('Titles.Index.UnderFilters') }}

        <div class="clearfix">
            @if(Helpers::hasAccess('titles.create') || Helpers::hasSuperAccess())
                <a class="btn new-button btn-primary pull-left" href="{{ Request::url().'/create' }}">{{ trans('dash.createNew') }}</a>
            @endif
            <div class="index-pagination"></div>
        </div>

  		<section data-bind="foreach: {data: sourceItems, afterRender: lazyLoadImage}" class="row">

  			<figure class="col-sm-4 col-md-3 col-lg-2 pretty-figure" data-bind="attr: { data: $index }">
  				<a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[type]+'/'+id+'-'+title.replace(/\s+/g, '-').toLowerCase() }">
                    <img class="img-responsive" data-bind="attr: { 'data-original': poster, alt: title }">
                </a>
                {{ Hooks::renderHtml('Titles.Index.ForEachMovie') }}
                <figcaption>
                    <a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[type]+'/'+id+'-'+title.replace(/\s+/g, '-').toLowerCase() }, text: title"></a>
                </figcaption>
  		    </figure>
  			
  		</section>

        <div class="clearfix">
            <div class="index-pagination bottom-pagination"></div>
        </div>

	</div>

@stop

@section('scripts')
	<script>app.viewModels.titles.index.start('<?php echo $type; ?>');</script>
@stop
