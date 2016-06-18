@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard">
@stop

@section('content')

	<section id="dash-container" class="with-filter-bar">

		@include('Dashboard.Partials.Sidebar')

		<div class="content">

			<div id="filter-row" class="row">			
				<button class="col-sm-1 btn btn-primary" data-bind="click: app.paginator.previousPage, enable: app.paginator.hasPrevious">
					<fa class="fa fa-chevron-left"></fa> {{ trans('dash.previous') }}
				</button>
				<button class="col-sm-1 btn btn-primary" data-bind="click: app.paginator.nextPage, enable: app.paginator.hasNext">
					{{ trans('dash.next') }} <fa class="fa fa-chevron-right"></fa>
				</button>
				<section class="col-sm-4 filter-dropdown">
					<select class="form-control" data-bind="value: params.order">
						<option value="">{{ trans('dash.sortBy') }}...</option>
						<option value="created_atDesc">{{ trans('dash.createdAsc') }}</option>
						<option value="created_atAsc">{{ trans('dash.createdDesc') }}</option>
						<option value="titleDesc">{{ trans('dash.titleDesc') }}</option>
						<option value="titleAsc">{{ trans('dash.titleAsc') }}</option>
					</select>
				</section>
				<section class="col-sm-4">
					<i class="fa fa-search"></i>
					<input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
				</section>
				{{ Form::open(array('route' => 'news.ext', 'class' => 'form-inline')) }}
					<button type="submit" class="btn btn-primary col-sm-1">{{ trans('dash.update') }}</button>
				{{ Form::close() }}
				<a href="{{ url(Str::slug(trans('main.news')).'/create') }}" class="col-sm-1 btn btn-primary"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</a>
			</div>	


			<section class="dash-padding">
				<table class="table table-striped table-centered table-responsive">
			    	<thead>
			        	<tr>
			        		<th>{{ trans('main.image') }}</th>
			        		<th>{{ trans('main.source') }}</th>
			          		<th>{{ trans('main.title') }}</th>
			          		<th>{{ trans('main.body') }}</th>		
			          		<th>{{ trans('dash.createdAt') }}</th>
			          		
			          		<th>{{ trans('dash.actions') }}</th>
			        	</tr>
			      	</thead>
			    	<tbody data-bind="foreach: sourceItems">
						<tr>
							<td class="col-sm-1"><img class="img-responsive col-sm-12" data-bind="attr: { src: image, alt: title }"></td>
							<td class="col-sm-1" data-bind="text: source"></td>
							<td class="col-sm-2" data-bind="text: title"></td>
							<td class="col-sm-4" data-bind="text: body ? body.trunc(250) : null"></td>
							<td class="col-sm-2" data-bind="text: created_at"></td>
							
							<td class="col-sm-1">
								<button class="btn btn-danger btn-sm" data-bind="click: $root.deleteItem"><i class="fa fa-trash-o"></i> </button>
								<a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans.news+'/'+id+'/edit' }" class="btn btn-primary btn-sm" ><i class="fa fa-wrench"></i> </a>
							</td>
						</tr>				       		
			    	</tbody>
			    </table>
			</section>

		</div>

	</section>

@stop

@section('ads')	
@stop

@section('scripts')
	<script>
		app.paginator.start(app.viewModels.news, '.content', 15);
	</script>
@stop