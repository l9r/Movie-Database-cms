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
					</select>
				</section>
				<section class="col-sm-4">
					<i class="fa fa-search"></i>
					<input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
				</section>
				<div class="col-sm-1"></div>
				<button class="col-sm-1 btn btn-primary" data-toggle="modal" data-target="#new-user-modal"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</button>
			</div>	


			<section class="dash-padding">
				<table class="table table-striped table-centered table-responsive">
			    	<thead>
			        	<tr>
			        		<th>{{ trans('main.id') }}</th>
			          		<th>{{ trans('groups.name') }}</th>
			          		<th>{{ trans('groups.permission') }}</th>
			          		<th>{{ trans('dash.actions') }}</th>
			        	</tr>
			      	</thead>
			    	<tbody data-bind="foreach: sourceItems">
						<tr>
							<td class="col-sm-1" data-bind="text: id"></td>
							<td class="col-sm-1" data-bind="text: name"></td>
							<td class="col-sm-1" data-bind="text: permissions"></td>

							<td class="col-sm-1">
								<button class="btn btn-danger btn-sm" data-bind="click: app.paginator.deleteItem"><i class="fa fa-trash-o"></i> </button>
								<a class="btn btn-primary btn-sm" data-bind="click: $root.populateModal.bind($data, id)"><i class="fa fa-wrench"></i> </a>
							</td>
						</tr>				       		
			    	</tbody>
			    </table>
			</section>

			<div class="modal fade" id="new-user-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			    <div class="modal-dialog">
			        <div class="modal-content">
			            <div class="modal-header">
			                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</h4>
			            </div>
			            <div class="modal-body">
			            	{{ Form::open(array('route' => 'groups.createNew', 'data-bind' => 'submit: create')) }}
			            		<div class="form-group">
			            			<label for="name">{{ trans('groups.name') }}</label>
			            			<input type="text" name="name" class="form-control">
			            		</div>


			            		<div class="form-group">
			            			<label for="permissions">{{ trans('groups.permissions') }}</label> <br>
			            			<input type="checkbox" name="superuser_checked" data-id="superuser">Superuser <br>
									<input type="hidden" id="hidden_superuser" name="superuser" value="0">
									<label for="permissions">{{ trans('groups.t_permission') }}</label> <br>
									<input type="checkbox"  name="titles_edit_checked" data-id="titles_edit" >Titles Edit
									<input type="hidden" id="hidden_titles_edit" name="titles_edit" value="0">
									<input type="checkbox" name="titles_create_checked" data-id="titles_create" >Titles Create
									<input type="hidden" name="titles_create" id="hidden_titles_create" value="0">
									<input type="checkbox" name="titles_delete_checked" data-id="titles_delete" >Titles Delete <br>
									<input type="hidden" name="titles_delete" id="hidden_titles_delete" value="0">
									<label for="permissions">{{ trans('groups.p_permission') }}</label> <br>
									<input type="checkbox" name="people_create_checked" data-id="people_create"  >People Create 
									<input type="hidden" name="people_create" id="hidden_people_create" value="0">
									<input type="checkbox" name="people_edit_checked" data-id="people_edit" >People Edit
									<input type="hidden" name="people_edit" id="hidden_people_edit" value="0">
									<input type="checkbox" name="people_delete_checked" data-id="people_delete" >People Delete <br>
									<input type="hidden" name="people_delete" id="hidden_people_delete" value="0">
									<label for="permissions">{{ trans('groups.u_permission') }}</label> <br>
									<input type="checkbox" name="users_create_checked" data-id="users_create" >Users Create
									<input type="hidden" name="users_create" value="0" id="hidden_users_create">
									<input type="checkbox" name="users_edit_checked" data-id="users_edit" >Users Edit
									<input type="hidden" name="users_edit" id="hidden_users_edit" value="0" >
									<input type="checkbox" name="users_delete_checked" data-id="users_delete" >Users Delete <br>
									<input type="hidden" name="users_delete" id="hidden_users_delete" value="0">
									<label for="permissions">{{ trans('groups.s_permission') }}</label> <br>
									<input type="checkbox" name="slides_create_checked" data-id="slides_create" >Slides Create
									<input type="hidden" name="slides_create" id="hidden_slides_create" value="0">
									<input type="checkbox" name="slides_edit_checked" data-id="slides_edit" >Slides Edit
									<input type="hidden" name="slides_edit" id="hidden_slides_edit" value="0">
									<input type="checkbox" name="slides_delete_checked" data-id="slides_delete" >Slides Delete <br>
									<input type="hidden" name="slides_delete" id="hidden_slides_delete" value="0">
									<label for="permissions">{{ trans('groups.ac_permission') }}</label> <br>
									<input type="checkbox" name="actions_manage_checked" data-id="actions_manage" >Actions Manage <br>
									<input type="hidden" name="actions_manage" id="hidden_actions_manage" value="0">
									<label for="permissions">{{ trans('groups.settings_permission') }}</label> <br>
									<input type="checkbox" name="settings_manage_checked" data-id="settings_manage"  >Settings Manage <br>
									<input type="hidden" name="settings_manage" id="hidden_settings_manage" value="0" >
									<label for="permissions">{{ trans('groups.ad_permission') }}</label> <br>
									<input type="checkbox" name="ads_manage_checked" data-id="ads_manage"  >Ads Manage <br>
									<input type="hidden" name="ads_manage" id="hidden_ads_manage" value="0">
									<label for="permissions">{{ trans('groups.review_permission') }}</label> <br>
									<input type="checkbox" name="reviews_delete_checked" data-id="reviews_delete" >Reviews Delete <br>
									<input type="hidden" name="reviews_delete" id="hidden_reviews_delete" value="0">
									<label for="permissions">{{ trans('groups.link_permission') }}</label> <br>
									<input type="checkbox" name="links_approve_checked" data-id="links_approve">Links Approve
									<input type="hidden" name="links_approve" id="hidden_links_approve" value="0">
									<input type="checkbox" name="links_delete_checked" data-id="links_delete">Links Delete
									<input type="hidden" name="links_delete" id="hidden_links_delete" value="0">
			            			
			            		</div>


			            		<button type="submit" class="btn btn-success">{{ trans('main.submit') }}</button>
			            	{{ Form::close() }}
			            </div>
			        </div>
			    </div>
			</div>

		</div>

	</section>

@stop

@section('ads')	
@stop

@section('scripts')
	<script>
		vars.trans.groups = '<?php echo strtolower(trans("main.groups")); ?>';
		app.paginator.start(app.viewModels.groups, '.content', 15);
		app.viewModels.groups.registerEvents();
		
		$('input[type="checkbox"]').on('change', function(e){
			var id = $(this).data('id');
			if($(this).prop('checked'))
			{
				$("#hidden_"+id).val(1);
			} else {
				$("#hidden_"+id).val(0);
			}
		});
		
	</script>
	
@stop