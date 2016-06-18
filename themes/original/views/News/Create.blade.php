@extends('Main.Boilerplate')

@section('title')
	<title>{{ trans('dash.create') }} - {{ $options->getSiteName() }}</title>
@stop

@section('bodytag')
	<body id="news-create">
@stop

@section('content')
	
	{{ Form::open(array('action' => 'NewsController@store', 'cols' => 22, 'class' => "container")) }}

		<div class="col-sm-8">
			@include('Partials.Response')
		
			<div class="row form-group">
    			{{ Form::text('title', null, array('class' => 'form-control', 'placeholder' => 'Title...')) }}
    			{{ $errors->first('title', '<span class="help-block alert alert-danger">:message</span>') }}
  			</div>

			<div class="row form-group">
				{{ Form::textarea('body', null, array('class' => 'ckeditor', 'rows' => 22, 'cols' => 10)) }}
				{{ $errors->first('body', '<span class="help-block alert alert-danger">:message</span>') }}
			</div>
		</div>

	    <div class="col-sm-4">
	    	<div class="panel panel-default">
				<div class="panel-heading"><i class="fa fa-save"></i> {{ trans('dash.save') }}</div>
			  	<div class="panel-body">
			    	<button type="submit" class="btn btn-primary">{{ trans('main.publish') }}</button>
			  	</div>
			</div>

			<div class="panel panel-default" data-bind="preventSubmitOnEnter">
				<div class="panel-heading">{{ trans('dash.uploadImage') }}</div>
			  	<div class="panel-body">
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload-media-modal">
					 {{ trans('dash.uploadImage') }}
					</button>
			  	</div> 	
			</div>

			<div class="panel panel-default" data-bind="preventSubmitOnEnter">
				<div class="panel-heading">{{ trans('dash.mainImage') }}</div>
			  	<div class="panel-body">
			  		<img class="img-responsive" id="img-preview" data-bind="attr: { src: app.models.newsItem.image }">
			  		{{ Form::text('image', null, array('class' => 'form-control', 'placeholder' => trans('dash.url'))) }}
			  		{{ $errors->first('image', '<span class="help-block alert alert-danger">:message</span>') }}
			  	</div> 	
			</div>
	    </div>
	
	{{ Form::close() }}

	@include('Partials.MediaUploadModal')

@section('scripts')

	{{ HTML::script('assets/js/vendor/ckeditor/ckeditor.js') }}
	{{ HTML::script('assets/js/vendor/uploader.min.js') }}

	<script>

		//resize ckeditor by specified rows
		(function($) {
			jQuery.fn.cke_resize = function() {
			   return this.each(function() {
			      var $this = $(this);
			      var rows = $this.attr('rows');
			      var height = rows * 20;
			      $this.next("div.cke").find(".cke_contents").css("height", height);
			   });
			};
		})(jQuery);

		CKEDITOR.on( 'instanceReady', function(){
			$("textarea.ckeditor").cke_resize();
		});

		app.viewModels.media.ckeExists(true);
		app.viewModels.media.start();
        app.paginator.start(app.viewModels.media, $('#upload-media-modal')[0], 24);
		

	</script>

@stop

@stop

@section('ads')
@stop