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
					<select class="form-control" data-bind="value: params.type">
						<option value="">{{ trans('main.type') }}...</option>
						<option value="movie">{{ trans('main.movie') }}</option>
						<option value="series">{{ trans('main.series') }}</option>
					</select>
				</section>
				{{--<section class="col-sm-2 filter-dropdown">
					<select class="form-control" data-bind="value: params.type">
						<option value="">{{ trans('main.type') }}...</option>
						<option value="movie">{{ trans('main.movie') }}</option>
						<option value="series">{{ trans('main.series') }}</option>
					</select>
				</section>--}}
				<section class="col-sm-4">
					<i class="fa fa-search"></i>
					<input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
				</section>
				@if(Helpers::hasAccess('titles.create') || Helpers::hasSuperAccess())
				<div class="col-sm-1"></div>
				<a href="{{ route('movies.create') }}" class="col-sm-1 btn btn-primary"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</a>
				</div>
				@endif

			<section class="dash-padding">

				@if (Helpers::isDemo())
					<div class="alert alert-danger alert-dismissable" style="margin-bottom: 45px">
						Note that normally dashboard itself and its button in menu would only be visible and accessible to Admin and not everyone.
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					</div>
				@endif

				<table class="table table-striped table-centered table-responsive">
			    	<thead>
			        	<tr>
			        		<th>{{ trans('main.poster') }}</th>
			        		<th>{{ trans('main.type') }}</th>
			          		<th>{{ trans('main.title') }}</th>
			          		<th>{{ trans('main.plot') }}</th>
			          		<th>{{ trans('main.release date') }}</th>
			          		<th>{{ trans('dash.createdAt') }}</th>
							@if(Helpers::hasAnyAccess(['titles.edit', 'titles.delete']) || Helpers::hasSuperAccess())
			          		<th>{{ trans('dash.actions') }}</th>
							@endif
			        	</tr>
			      	</thead>
			    	<tbody data-bind="foreach: sourceItems">
						<tr>
							<td class="col-sm-1"><img class="img-responsive col-sm-12" data-bind="attr: { src: poster, alt: title }"></td>
							<td class="col-sm-1" data-bind="text: type"></td>
							<td class="col-sm-2"><a data-bind="text: title, attr: { href: vars.urls.baseUrl+'/'+vars.trans[type]+'/'+id }"></a></td>
							<td class="col-sm-4" data-bind="text: plot ? plot.trunc(250) : null"></td>
							<td class="col-sm-1" data-bind="text: release_date"></td>
							<td class="col-sm-2" data-bind="text: created_at"></td>

							@if(Helpers::hasAnyAccess(['titles.edit', 'titles.delete']) || Helpers::hasSuperAccess())
							<td class="col-sm-1">
								@if(Helpers::hasAccess('titles.delete') || Helpers::hasSuperAccess())
								<button class="btn btn-danger btn-sm" data-bind="click: $root.deleteItem"><i class="fa fa-trash-o"></i> </button>
								@endif
								@if(Helpers::hasAccess('titles.edit') || Helpers::hasSuperAccess())
								<a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[type]+'/'+id+'/edit'}" class="btn btn-primary btn-sm" ><i class="fa fa-wrench"></i> </a>
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
		vars.trans.movie = '<?php echo strtolower(trans("main.movies")); ?>';
        vars.trans.series = '<?php echo strtolower(trans("main.series")); ?>';

		app.paginator.start(app.viewModels.titles, '.content', 15);
	</script>
@stop