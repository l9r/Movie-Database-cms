@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard">
@stop

@section('content')
	<section id="dash-container">

	@include('Dashboard.Partials.Sidebar')

	<section class="content" id="dashboard-menus"> 		
		
		{{-- Create new menu --}}
		<div id="create-new-cont" class="collapse">
			<section class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title">{{ trans('dash.newMenu') }}</h3></div>
				<div class="panel-body">
					<label for="name">{{ trans('main.name') }}</label>
					<input data-bind="value: app.models.menu.name, valueUpdate: 'keyup'" class="form-control" name="name" type="text">
					
					<div class="form-group">
					  		<label for="position1">{{ trans('dash.position') }}</label>
					    	<select class="form-control" data-bind="value: app.models.menu.position" id="position1">
					    		<option value="header">{{ trans('dash.header') }}</option>
					    		<option value="footer">{{ trans('dash.footer') }}</option>
					    	</select>
					  	</div>

					<button data-bind="click: createNew, enable: app.models.menu.name" type="button" class="btn btn-primary">{{ trans('dash.create') }}</button>
				</div>
			</section>		
		</div>
		{{-- /Create new menu --}}

		<div class="row">

			{{-- Attach link --}}
			<section class="col-sm-4 panel-group" id="accordion">

				<!-- ko if: activeMenu -->
				<section class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">{{ trans('dash.parentItem') }}</h3>
					</div>
					<div id="attachTo">
						<div class="panel-body">
							<select class="form-control" data-bind="options: activeMenu().items(),
									optionsText: function(item) { return item.label },
									value: attachingTo,
									optionsCaption: '{{ trans('dash.none') }}...'"></select>
						</div>
					</div>
				</section>
				<!-- /ko -->

				<section class="panel panel-default">
					<div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#links">
						<h3 class="panel-title">{{ trans('dash.menuAddLink') }}</h3>

						<div class="panel-btns">
		                    <button class="panel-btn">
		                        <i class="fa fa-caret-down"></i>
		                    </button>
		                </div>
					</div>
					<div id="links" class="panel-collapse collapse in">
						<div class="panel-body">
							<input class="form-control" placeholder="{{ trans('dash.url') }}..." name="action" type="text">
							<input class="form-control" placeholder="{{ trans('dash.label') }}..." name="label" type="text">
							<input class="form-control" placeholder="{{ trans('dash.order') }}..." name="weight" type="text">
							<div class="form-group">
								<select class="form-control" name="visibility" id="visibility">
									<option value="" selected>{{ trans('dash.visibility') }}...</option>
									<option value="everyone">{{ trans('dash.everyone') }}</option>
									<option value="admin">{{ trans('dash.admin') }}</option>
								</select>
							</div>

							<button data-bind="click: attachLink" type="button" class="btn btn-primary">{{ trans('dash.attach') }}</button>
						</div>
					</div>
				</section>
				{{-- /Attach link --}}

				{{-- Attach page --}}
				<section class="panel panel-default">
					<div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#pages">
						<h3 class="panel-title">{{ trans('dash.menuAddPage') }}</h3>

						<div class="panel-btns">
		                    <button class="panel-btn">
		                        <i class="fa fa-caret-down"></i>
		                    </button>
		                </div>
					</div>
					<div id="pages" class="panel-collapse collapse">
						<div class="panel-body">
							<div data-bind="foreach: vars.pages, iCheck">
								<div class="radio">
									<label>
										<input type="radio" class="pages" name="page" data-bind="value: $data, checked: $root.selectedPage">
										<span data-bind="text: $data"></span>
									</label>
								</div>
							</div>
							
							<button data-bind="click: attachPage" type="button" class="btn btn-primary">{{ trans('dash.attach') }}</button>					
						</div>
					</div>
				</section>
				{{-- /Attach page --}}

				{{-- Attach route --}}
				<section class="panel panel-default">
					<div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#routes">
						<h3 class="panel-title">{{ trans('dash.menuAddRoute') }}</h3>
						
						<div class="panel-btns">
		                    <button class="panel-btn">
		                        <i class="fa fa-caret-down"></i>
		                    </button>
		                </div>
					</div>
					<div id="routes" class="panel-collapse collapse">
						<div class="panel-body">
							<div data-bind="foreach: vars.routes" class="row">					
								<div class="checkbox col-sm-6">
								  <label>
								    <input type="checkbox" data-bind="checkboxArray: { checked: $parent.selectedRoutes, label: $data }">
								    <span data-bind="text: $data"></span>
								  </label>
								</div>
							</div>	
							
							<button class="btn btn-primary" data-bind="click: attachRoutes">{{ trans('dash.attach') }}</button>
						</div>
					</div>
				</section>
				{{-- /Attach route --}}

			</section>

			<section class="col-sm-8">
				<div class="panel panel-default" id="menu-edit-cont">
					<div class="panel-heading">
						<h3 class="panel-title">
							<span data-bind="if: allMenus().length && activeMenu">{{ trans('dash.editing') }}: <i data-bind="text: activeMenu().name"></i></span>
							<span data-bind="if: !allMenus().length">{{ trans('dash.noMenus') }}</span>
						</h3></div>
					<div class="panel-body">
									
					  	<div class="form-group" data-bind="if: allMenus().length">
					    	<label for="active-name">{{ trans('dash.selectMenu') }}</label>
					    	<select id="active-name" class="form-control" data-bind="options: allMenus, optionsText: function(item) { return item.name }, value: activeMenu"></select>
					  	</div>
					  	
						<!-- ko if: activeMenu -->
						<div class="form-group">
					  		<label for="active">{{ trans('dash.active') }}</label>
					    	<select class="form-control" data-bind="value: activeMenu().active" id="active">
					    		<option value="0">{{ trans('dash.no') }}</option>
					    		<option value="1">{{ trans('dash.yes') }}</option>
					    	</select>
					  	</div>

					  	<div class="form-group">
					  		<label for="position2">{{ trans('dash.position') }}</label>
					    	<select class="form-control" data-bind="value: activeMenu().active" id="position2">
					    		<option value="header">{{ trans('dash.header') }}</option>
					    		<option value="footer">{{ trans('dash.footer') }}</option>
					    	</select>
					  	</div>
						<!-- /ko -->
					  
					  <!-- ko if: allMenus().length -->
					  <button data-bind="click: save" type="button" class="btn btn-success">{{ trans('dash.save') }}</button>
					  <button data-bind="click: deleteMenu" type="button" class="btn btn-danger">{{ trans('main.delete') }}</button>
					  <!-- /ko -->

					  <button class="btn btn-primary" data-toggle="collapse" data-target="#create-new-cont">{{ trans('dash.createNew') }}</button>
						  
					<!-- ko if: allMenus().length -->
						<hr>

						<h3>{{ trans('dash.modMenuItems') }}</h3>
	
						<p>{{ trans('dash.menuItemExpl', array('caret' => '<i class="caret"></i>', 'times' => '<i class="fa fa-times"></i>')) }}</p>

						<!-- ko if: activeMenu -->
						<ul class="list-unstyled menu-items-list" data-bind="foreach: activeMenu().items()">

							@include('Dashboard.Menus.MenuItemTemplate')

							<!-- ko if: children().length > 0-->
								<!-- ko foreach: children() -->
									@include('Dashboard.Menus.MenuItemTemplate', array('child' => true))
								<!-- /ko -->
							<!-- /ko -->
						</ul>
						<!-- /ko -->
					<!-- /ko -->

					</div>
				</div>

				@include('Dashboard.Menus.Templates.DemoMenu')
			</section>
		</div>

	</section>
	</section>

@stop

@section('scripts')
	<script>
	vars.routes = <?php echo json_encode($routes); ?>;
	vars.partials = <?php echo json_encode($partials); ?>;
	vars.pages = <?php echo json_encode($pages); ?>;
	        
	vars.menus = app.viewModels.menus.map(<?php echo $options->getMenus(); ?>);
	app.viewModels.menus.allMenus(vars.menus);
	ko.applyBindings(app.viewModels.menus, $('#dash-container')[0]);
	</script>
@stop

@section('footer')
@stop