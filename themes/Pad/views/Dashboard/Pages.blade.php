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
				<select name="visibility" class="form-control" data-bind="value: params.visibility">
					<option value="">{{ trans('dash.visibility').'...' }}</option>
					<option value="public">{{ trans('dash.public') }}</option>
					<option value="admin">{{ trans('dash.adminOnly') }}</option>
				</select>
			</section>
			<section class="col-sm-4">
				<i class="fa fa-search"></i>
				<input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
			</section>
			<section class="col-sm-1"></section>
			<a href="{{ route('pages.create') }}" class="col-sm-1 btn btn-primary"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</a>
		</div>	
		
		<section class="dash-padding">
			<table class="table table-striped table-centered table-responsive">
		    	<thead>
		        	<tr>
		        		<th>#</th>
		          		<th>{{ trans('dash.title') }}</th>
		          		<th>{{ trans('dash.slug') }}</th>
		          		<th>{{ trans('dash.body') }}</th>      		
		          		<th>{{ trans('dash.createdAt') }}</th>
		          		<th>{{ trans('dash.actions') }}</th>
		        	</tr>
		      	</thead>
		    	<tbody data-bind="foreach: sourceItems">
					<tr>
						<td class="col-sm-1" data-bind="text: $index() + 1"></td>
						<td class="col-sm-2"><a data-bind="attr: { href: url }, text: title"></a></td>
						<td class="col-sm-2" data-bind="text: slug"></td>
						<td class="col-sm-4" data-bind="html: body.replace(/(<([^>]+)>)/ig,'').trunc(250)"></td>
						<td class="col-sm-2" data-bind="text: created_at"></td>
						<td class="col-sm-1">
							<button class="btn btn-danger btn-sm" data-bind="click: $root.deleteItem"><i class="fa fa-trash-o"></i> </button>
							<a data-bind="attr: { href: vars.urls.baseUrl+'/pages/'+id+'/edit' }" class="btn btn-primary btn-sm" ><i class="fa fa-wrench"></i> </a>
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
		app.paginator.start(app.viewModels.pages, '#dash-container');
	</script>
@stop

@section('footer')
@stop