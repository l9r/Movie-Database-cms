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
				<section class="col-sm-3 filter-dropdown">
					<select class="form-control" data-bind="value: params.order">
						<option value="">{{ trans('dash.orderBy') }}...</option>
						<option value="reportsDesc">{{ trans('stream::main.reportsDesc') }}</option>
						<option value="reportsAsc">{{ trans('stream::main.reportsAsc') }}</option>
						<option value="labelDesc">{{ trans('stream::main.labelDesc') }}</option>
						<option value="labelAsc">{{ trans('stream::main.labelAsc') }}</option>
						<option value="approvedAsc">{{ trans('stream::main.approvedAsc') }}</option>
					</select>
				</section>
				<section class="col-sm-3">
					<i class="fa fa-search"></i>
					<input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
				</section>
				<section class="col-sm-3 filter-dropdown">
					<select class="form-control" data-bind="value: numOfReports">
						<option value="">{{ trans('stream::main.moreRepsThen') }}</option>
						@foreach(range(0, 100, 10) as $num)
							<option value="{{ $num ? $num : 1 }}">{{ $num ? $num : 1 }}</option>
						@endforeach
					</select>
				</section>
				@if(Helpers::hasAnyAccess(['links.delete', 'superuser']))
				<button class="col-sm-1 btn btn-danger" data-bind="click: deleteLinks, enable: numOfReports">{{ trans('dash.delete') }}</a>
				@endif
			</div>


			<section class="dash-padding">
				<table class="table table-striped table-centered table-responsive table-bordered">
			    	<thead>
			        	<tr>
			        		<th>{{ trans('stream::main.label') }}</th>
			        		<th>{{ trans('main.title') }}</th>
			        		<th>{{ trans('main.type') }}</th>
			          		<th>{{ trans('stream::main.reports') }}</th>
							@if(Helpers::hasAnyAccess(['links.approve', 'superuser']))
			          		<th>{{ trans('stream::main.approved') }}</th>
							@endif
			          		<th>{{ trans('stream::main.seasonNum') }}</th>
			          		<th>{{ trans('stream::main.episodeNum') }}</th> 		
			          		<th>{{ trans('dash.url') }}</th>
							@if(Helpers::hasAnyAccess(['links.delete','titles.edit', 'superuser']))
			          		<th>{{ trans('dash.actions') }}</th>
							@endif
			        	</tr>
			      	</thead>
			    	<tbody data-bind="foreach: sourceItems">
			    		<!-- ko if: title -->
						<tr >
							<td class="col-sm-1" data-bind="text: label"></td>
							<td class="col-sm-2" ><a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[title.type]+'/'+title.id }, text: title.title"></a></td>
							<td class="col-sm-1" data-bind="text: type"></td>
							<td class="col-sm-1" data-bind="text: reports"></td>
							@if(Helpers::hasAnyAccess(['links.approve', 'superuser']))
							<td class="col-sm-1" data-bind="attr: { 'data-id': id }">	
								<!-- ko ifnot: parseInt(approved) -->

									<button data-bind="click: $root.approveLink" class="btn btn-success">{{ trans('stream::main.approve') }}</button>

								<!-- /ko -->

								<!-- ko if: parseInt(approved) -->
									{{ trans('dash.yes') }}
								<!-- /ko -->	
							</td>
							@endif
							<td class="col-sm-1" data-bind="text: season"></td>
							<td class="col-sm-1" data-bind="text: episode"></td>
							<td class="col-sm-3" data-bind="text: url"></td>

							@if(Helpers::hasAnyAccess(['links.delete','titles.edit', 'superuser']))
							<td class="col-sm-1">
								@if(Helpers::hasAnyAccess(['links.delete', 'superuser']))
								<button class="btn btn-danger btn-sm" data-bind="click: $root.deleteItem"><i class="fa fa-trash-o"></i> </button>
								@endif
								@if(Helpers::hasAnyAccess(['titles.edit', 'superuser']))
								<a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[title.type]+'/'+title.id+'/edit#links'}" class="btn btn-primary btn-sm" ><i class="fa fa-wrench"></i> </a>
								@endif
							</td>
							@endif
						</tr>
						<!-- /ko -->			       		
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

		app.paginator.start(app.viewModels.links, '.content', 15);
	</script>
@stop