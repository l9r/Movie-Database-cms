@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard">
@stop

@section('content')
	<section id="dash-container" class="with-filter-bar">

	@include('Dashboard.Partials.Sidebar')

	<section class="content">

		<div id="filter-row" class="clearfix">			
			<button class="col-sm-1 btn btn-primary" data-bind="click: app.paginator.previousPage, enable: app.paginator.hasPrevious">
				<fa class="fa fa-chevron-left"></fa> {{ trans('dash.previous') }}
			</button>
			<button class="col-sm-1 btn btn-primary" data-bind="click: app.paginator.nextPage, enable: app.paginator.hasNext">
				{{ trans('dash.next') }} <fa class="fa fa-chevron-right"></fa>
			</button>
			<section class="col-sm-4 filter-dropdown">
				<select name="type" class="form-control" data-bind="value: params.type">
					<option value="critic">{{ trans('dash.critic') }}</option>
					<option value="user">{{ trans('dash.user') }}</option>
				</select>
			</section>
			<section class="col-sm-4">
				<i class="fa fa-search"></i>
				<input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
			</section>
			<section class="col-sm-2"></section>
		</div>	
		
		<section class="dash-padding">
			<table class="table table-striped table-centered table-responsive">
		    	<thead>
		        	<tr>
		        		<th>#</th>
		          		<th>{{ trans('dash.type') }}</th>
		          		<th>{{ trans('dash.author') }}</th>
		          		<th>{{ trans('dash.source') }}</th>
		          		<th>{{ trans('dash.body') }}</th>      		
		          		<th>{{ trans('dash.createdAt') }}</th>
		          		<th>{{ trans('dash.actions') }}</th>
		        	</tr>
		      	</thead>
		    	<tbody data-bind="foreach: sourceItems">
					<tr>
						<td class="col-sm-1" data-bind="text: $index() + 1"></td>
						<td class="col-sm-1" data-bind="text: type"></td>
						<td class="col-sm-1" data-bind="text: author"></td>
						<td class="col-sm-1" data-bind="text: source"></td>
						<td class="col-sm-4" data-bind="html: body.replace(/(<([^>]+)>)/ig,'').trunc(250)"></td>
						<td class="col-sm-2" data-bind="text: created_at"></td>
						<td class="col-sm-1">
							<button class="btn btn-danger btn-sm" data-bind="click: $root.deleteItem"><i class="fa fa-trash-o"></i> </button>
						</td>
					</tr>				       		
		    	</tbody>
		    </table>
		</section>

	</section>
	</section>

@stop

@section('scripts')
	<script>
		vars.movies = '<?php echo route("movies.show", 1) ?>'
		app.paginator.start(app.viewModels.dashReviews, '.content');
	</script>
@stop

@section('footer')
@stop