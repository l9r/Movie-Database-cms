@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard">
@stop

@section('content')

	<section id="dash-container" class="categories-page">

		@include('Dashboard.Partials.Sidebar')

		<div class="content container-fluid">

			<section class="col-sm-8">
				<!-- ko ifnot: active() -->
					<h2>Select a category from the right <i class="fa fa-arrow-right"></i></h2>
				<!-- /ko -->

				<!-- ko if: active() -->
					<div id="category-panel">
						<h4 class="category-heading clearfix">
							<div class="pull-left">
								<i data-bind="attr: { class: active().icon }"></i>
								<span data-bind="text: active().name"></span>
							</div>
							<section id="category-search" class="pull-right">
				
								<div id="search-group" class="clearfix">
									<label class="hidden" data-bind="attr: { for: name }">{{ trans('dash.searchForATitle') }}</label>
						        	<select name="queryType" class="form-control pull-right" data-bind="value: queryType">
						        		<option value="title">Title</option>
						        		<option value="actor">Actor</option>
						        	</select>
							     	<input type="text" class="form-control pull-left" placeholder="{{ trans('dash.searchForATitle') }}" autocomplete="off" data-bind="hideShow, attr: { id: name }, value: $root.query, valueUpdate: 'keyup'">
							    </div>

						        <div class="autocomplete-container" data-bind="visible: $root.autocompleteResults().length">
						            <div class="arrow-up"></div>
						            <section class="auto-heading">{{ trans('dash.resultsFor') }} <span data-bind="text: $root.query"></span></section>

						            <section class="suggestions" data-bind="foreach: $root.autocompleteResults">
						                <div class="media" data-bind="click: $root.attach">
						                	<!-- ko if: $root.queryType() == 'actor'-->
						                    <a href="#" class="col-sm-2"><img class="media-object img-responsive" data-bind="attr: { src: image, alt: name }"></a>
						                    <div class="media-body">
						                        <h6 class="media-heading" data-bind="text: name"></h6>
						                        <p data-bind="text: bio ? bio.trunc(160) : null"></p>
						                    </div>
						                    <!-- /ko -->

						                    <!-- ko if: $root.queryType() == 'title'-->
						                    <a href="#" class="col-sm-2"><img class="media-object img-responsive" data-bind="attr: { src: poster, alt: title }"></a>
						                    <div class="media-body">
						                        <h6 class="media-heading" data-bind="text: title"></h6>
						                        <p data-bind="text: plot ? plot.trunc(160) : null"></p>
						                    </div>
						                    <!-- /ko -->
						                </div>
						            </section>
						        </div>
							</section>
						</h4>
						<div class="category-body">
							<!-- ko if: active().title -->
							<div data-bind="foreach: active().title">
								<figure class="col-md-4 col-lg-3 pretty-figure">
									<img data-bind="attr: { src: poster, alt: title }" class="img-responsive">
									<i class="fa fa-trash-o remove-title" data-bind="click: $root.detach"></i>
									<figcaption><a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[type]+'/'+id+'-'+title.replace(/\s+/g, '-').toLowerCase() }, text: title"></a></figcaption>
								</figure>
							</div>
							<!-- /ko -->

							<!-- ko if: active().actor -->
							<div data-bind="foreach: active().actor">
								<figure class="col-md-4 col-lg-3 pretty-figure">
									<img data-bind="attr: { src: image, alt: name }" class="img-responsive">
									<i class="fa fa-trash-o remove-title" data-bind="click: $root.detach"></i>
                                    <figcaption>
                                        <a data-bind="attr: { href: vars.urls.baseUrl+'/people/'+id+'-'+name.replace(/\s+/g, '-').toLowerCase() }, text: name"></a>
                                    </figcaption>
								</figure>
							</div>
							<!-- /ko -->
						</div>
						
					</div>
				<!-- /ko -->
			</section>

	    	
			<section id="categories-list" class="col-sm-4">
				<h4 class="clearfix">
					<div class="pull-left">Available Categories</div>
					<div class="pull-right"> <button data-bind="click: showCreateModal" class="btn btn-primary btn-sm"> <i class="fa fa-plus"></i></button></div>
				</h4>
				<ul class="list-unstyled" data-bind="foreach: sourceItems">
					<li data-bind="click: $root.setActiveCategory" class="clearfix">
						<div  class="pull-left">
							<i data-bind="attr: { class: icon }"></i>
							<span data-bind="text: name"></span>
						</div>
						<div class="action-btns pull-right">
							<button class="btn btn-primary btn-sm" data-bind="click: $root.showEditModal, clickBubble: false"><i class="fa fa-gears"></i> </button>
							<button class="btn btn-danger btn-sm" data-bind="click: $root.delete, clickBubble: false"><i class="fa fa-trash-o"></i> </button>
						</div>
					</li>
		    	</ul>
			</section>
			

	    	<div class="modal fade" id="edit-category-modal">
			    <div class="modal-dialog">
			        <div class="modal-content">
			            <div class="modal-header">
			                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</h4>
			            </div>
			            <div class="modal-body">
			            	{{ Form::open(array('route' => 'categories.store', 'data-bind' => 'submit: create')) }}
			            		<div class="form-group">
			            			<label for="name">{{ trans('main.name') }}</label>
			            			<input type="text" name="name" class="form-control" data-bind="value: app.models.category.name">
			            		</div>

			            		<div class="form-group">
			            			<label for="name">{{ trans('dash.icon') }}</label>
			            			<input type="text" name="name" class="form-control" data-bind="value: app.models.category.icon">
			            			<i class="help-block">{{ trans('dash.iconExpl') }}</i>
			            		</div>

			            		<div class="form-group">
			            			<label for="auto_update">{{ trans('dash.autoUpdate') }}</label>
			            			<select name="auto_update" class="form-control" data-bind="value: app.models.category.autoUpdate">
			            				<option value="1">{{ trans('dash.yes') }}</option>
			            				<option value="0">{{ trans('dash.no') }}</option>
			            			</select>
			            			<i class="help-block">{{ trans('dash.autoUpdateExpl') }}</i>
			            		</div>

			            		<div class="form-group">
			            			<label for="query">{{ trans('dash.whatToUpdateWith') }}</label>
			            			<select name="query" class="form-control" data-bind="value: app.models.category.query, enable: parseInt(app.models.category.autoUpdate())">
			            				<option value="popularActors">{{ trans('dash.popularActors') }}</option>
			            				<option value="popularTitles">{{ trans('dash.popularTitles') }}</option>
			            				<option value="latestTitles">{{ trans('dash.latestTitles') }}</option>
			            				<option value="topRatedTitles">{{ trans('dash.topRatedTitles') }}</option>
			            			</select>
			            		</div>

			            		<div class="form-group">
			            			<label for="show_trailer">{{ trans('dash.showTrailer') }}</label>
			            			<select name="show_trailer" class="form-control" data-bind="value: app.models.category.showTrailer, enable: app.models.category.query() != 'popularActors'">
			            				<option value="1">{{ trans('dash.yes') }}</option>
			            				<option value="0">{{ trans('dash.no') }}</option>
			            			</select>
			            		</div>

			            		<div class="form-group">
			            			<label for="show_rating">{{ trans('dash.showRating') }}</label>
			            			<select name="show_rating" class="form-control" data-bind="value: app.models.category.showRating, enable: app.models.category.query() != 'popularActors'">
			            				<option value="1">{{ trans('dash.yes') }}</option>
			            				<option value="0">{{ trans('dash.no') }}</option>
			            			</select>
			            		</div>

			            		<div class="form-group">
			            			<label for="active">{{ trans('dash.active') }}</label>
			            			<select name="active" class="form-control" data-bind="value: app.models.category.active">
			            				<option value="1">{{ trans('dash.yes') }}</option>
			            				<option value="0">{{ trans('dash.no') }}</option>
			            			</select>
			            		</div>

			            		<div class="form-group">
			            			<label for="weight">{{ trans('dash.weight') }}</label>
			            			<input type="text" name="weight" class="form-control" data-bind="value: app.models.category.weight">
			            			<i class="help-block">{{ trans('dash.weightExpl') }}</i>
			            		</div>

			            		<div class="form-group">
			            			<label for="limit">{{ trans('dash.limit') }}</label>
			            			<select name="limit" class="form-control" data-bind="value: app.models.category.limit">
			            				<option value="4">4</option>
			            				<option value="6">6</option>
			            				<option value="8">8</option>
			            				<option value="12">12</option>
			            				<option value="16">16</option>
			            				<option value="18">18</option>
			            				<option value="24">24</option>
			            				<option value="32">32</option>
			            			</select>
			            			<i class="help-block">{{ trans('dash.limitExpl') }}</i>
			            		</div>

			            		<button type="submit" class="btn btn-success">{{ trans('dash.submit') }}</button>
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
		app.viewModels.categories.start();
	</script>
@stop