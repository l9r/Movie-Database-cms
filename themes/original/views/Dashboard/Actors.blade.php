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
						<option value="nameDesc">{{ trans('dash.nameDesc') }}</option>
						<option value="nameAsc">{{ trans('dash.nameAsc') }}</option>
					</select>
				</section>
				<section class="col-sm-4">
					<i class="fa fa-search"></i>
					<input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
				</section>
				@if(Helpers::hasAccess('people.create') || Helpers::hasSuperAccess())
				<div class="col-sm-1"></div>
				<a href="{{ url(Str::slug(trans('main.people')).'/create') }}" class="col-sm-1 btn btn-primary"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</a>
			</div>
				@endif


			<section class="dash-padding">
				<table class="table table-striped table-centered table-responsive">
			    	<thead>
			        	<tr>
			        		<th>{{ trans('main.image') }}</th>
			          		<th>{{ trans('main.name') }}</th>
			          		<th>{{ trans('main.bio') }}</th>
			          		<th>{{ trans('main.birth date') }}</th> 
			          		<th>{{ trans('main.birth place') }}</th>		
			          		<th>{{ trans('dash.createdAt') }}</th>

							@if(Helpers::hasAnyAccess(['people.edit', 'people.delete']) || Helpers::hasSuperAccess())
								<th>{{ trans('dash.actions') }}</th>
							@endif
			        	</tr>
			      	</thead>
			    	<tbody data-bind="foreach: sourceItems">
						<tr>
							<td class="col-sm-1"><img class="img-responsive col-sm-12" data-bind="attr: { src: image, alt: name }"></td>
							<td class="col-sm-2" data-bind="text: name"></td>
							<td class="col-sm-4" data-bind="text: bio ? bio.trunc(250) : null"></td>
							<td class="col-sm-1" data-bind="text: birth_date"></td>
							<td class="col-sm-1" data-bind="text: birth_place"></td>
							<td class="col-sm-2" data-bind="text: created_at"></td>

							@if(Helpers::hasAnyAccess(['people.edit', 'people.delete']) || Helpers::hasSuperAccess())
							<td class="col-sm-1">
								@if(Helpers::hasAccess('people.delete') || Helpers::hasSuperAccess())
								<button class="btn btn-danger btn-sm" data-bind="click: $root.deleteItem"><i class="fa fa-trash-o"></i> </button>
								@endif
								@if(Helpers::hasAccess('people.edit') || Helpers::hasSuperAccess())
								<a class="btn btn-primary btn-sm" data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans.people+'/'+id+'/edit' }"><i class="fa fa-wrench"></i> </a>
								@endif
							</td>
							@endif
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
		vars.trans.people = '<?php echo strtolower(trans("main.people")); ?>';
		app.paginator.start(app.viewModels.actors, '.content', 15);
	</script>
@stop