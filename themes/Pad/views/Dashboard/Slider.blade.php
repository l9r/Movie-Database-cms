@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard">
@stop

@section('content')
	<section id="dash-container" class="slider-page">

	@include('Dashboard.Partials.Sidebar')

	<section class="content">

		@if(Helpers::hasAccess('slides.create') || Helpers::hasSuperAccess())
		<section class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{{ trans('dash.newSlide') }}</h3>
				<div class="panel-btns" data-bind="click: collapse">
                    <button class="panel-btn">
                        <i class="fa fa-caret-down"></i>
                    </button>
                </div>
			</div>

			<div class="panel-body collapse" id="add-new">
				{{ Form::open(array('url' => 'slides/add', 'class' => 'col-sm-6', 'data-bind' => 'submit: saveSlide', 'id' => 'save-slide')) }}
					<div class="form-group">
						<label for="name">{{ trans('main.title') }}</label>
						<input type="text" class="form-control" name="title" id="name" data-bind="value: app.models.slide.title">
					</div>
					<div class="form-group">
						<label for="name">{{ trans('main.body') }}</label>
						<textarea rows="5" class="form-control" name="body" id="body" data-bind="value: app.models.slide.body"></textarea>
					</div>
					<div class="form-group">
						<label for="name">{{ trans('main.image') }}</label>
						<input type="text" class="form-control" name="image" id="image" data-bind="value: app.models.slide.image">
					</div>
					<div class="form-group">
						<label for="name">{{ trans('main.genre') }}</label>
						<input type="text" class="form-control" name="genre" id="genre" data-bind="value: app.models.slide.genre">
					</div>
					<div class="form-group">
						<label for="name">{{ trans('main.director') }}</label>
						<input type="text" class="form-control" name="director" id="director" data-bind="value: app.models.slide.director">
					</div>
					<div class="form-group">
						<label for="name">{{ trans('main.stars') }}</label>
						<input type="text" class="form-control" name="stars" id="stars" data-bind="value: app.models.slide.stars">
					</div>
					<div class="form-group">
						<label for="name">{{ trans('main.trailer') }}</label>
						<input type="text" class="form-control" name="trailer" id="trailer" data-bind="value: app.models.slide.trailer">
					</div>
					<div class="form-group">
						<label for="name">{{ trans('main.trailerImage') }}</label>
						<input type="text" class="form-control" name="trailer_image" id="trailer_image" data-bind="value: app.models.slide.trailer_image">
					</div>
					<div class="form-group">
						<label for="name">{{ trans('main.poster') }}</label>
						<input type="text" class="form-control" name="trailer_image" id="poster" data-bind="value: app.models.slide.poster">
					</div>
					<div class="form-group">
						<label for="name">{{ trans('dash.link') }}</label>
						<input type="text" class="form-control" name="link" id="link" data-bind="value: app.models.slide.link">
					</div>

					<button class="btn btn-primary" type="submit">{{ trans('dash.save') }}</button>
				{{ Form::close() }}

				<section class="col-sm-6">
					<div class="form-group">
						<label for="name">{{ trans('dash.populateSlide') }}</label>
						<input type="text" class="form-control" name="query" id="query" data-bind="value: populateQuery,valueUpdate: 'keyup'">
						<i class="help-block">{{ trans('dash.popSlideExpl') }}</i>

						<div class="from-group">
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload-media-modal">
								{{ trans('dash.uploadImage') }}
							</button>
						</div>

						<div class="autocomplete-container-modal" data-bind="visible: populateQuery">
                        	<section class="suggestions" data-bind="foreach: populateResults">
                                <div class="media" data-bind="click: $root.populateSlide">
                                    <div class="media-body">
                                        <h5 class="media-heading" data-bind="text: title"></h5>
                                        <p data-bind="text: plot"></p>
                                    </div>
                                </div>
                                <hr>
                            </section>
            			</div>
					</div>
				</section>
			</div>
		</section>
		@endif
		<table class="table table-striped slides-table table-centered">
	    	<thead>
	        	<tr>		
	          		<th>{{ trans('main.image') }}</th>
	          		<th>{{ trans('main.title') }}</th>        		
	          		<th>{{ trans('main.body') }}</th>
	          		<th>{{ trans('dash.link') }}</th>
					@if(Helpers::hasAnyAccess(['slides.edit', 'slides.delete']) || Helpers::hasSuperAccess())
	          		<th>{{ trans('dash.actions') }}</th>
	        		@endif
				</tr>
	      	</thead>
	    	<tbody data-bind="foreach: allSlides">
				<tr>
					<td class="col-sm-3"><img class="img-responsive col-sm-12" data-bind="attr: { src: image, alt: title }"></td>
					<td class="col-sm-2" data-bind="text: title"></td>						
					<td class="col-sm-3"><p data-bind="text: body"></p></td>
					<td class="col-sm-2"><a data-bind="attr: { href: link }, text: link"></a></td>
					@if(Helpers::hasAnyAccess(['slides.edit', 'slides.delete']) || Helpers::hasSuperAccess())
					<td class="col-sm-2">
						@if(Helpers::hasAccess('slides.delete') || Helpers::hasSuperAccess())
						<button class="btn btn-danger btn-sm" data-bind="click: $parent.removeSlide"><i class="fa fa-trash-o" ></i></button>
						@endif
						@if(Helpers::hasAccess('slides.edit') || Helpers::hasSuperAccess())
						<button class="btn btn-primary btn-sm" data-bind="click: $parent.edit"><i class="fa fa-wrench" ></i></button>
						@endif
					</td>
					@endif
				</tr>
	    	</tbody>
	    </table>

	</section>
	</section>

	@include('Partials.MediaUploadModal')

@stop

@section('ads')	
@stop

@section('scripts')

	{{ HTML::script('assets/js/vendor/uploader.min.js') }}

	<script>
		app.viewModels.media.editingSlider(true);
		app.viewModels.media.start();

		app.paginator.start(app.viewModels.media, $('#upload-media-modal')[0], 24);	

		app.viewModels.slider.allSlides(<?php echo $slides; ?>);
		ko.applyBindings(app.viewModels.slider, $('#dash-container')[0]);
			
	</script>

@stop

@section('footer')
@stop