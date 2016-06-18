@extends('Main.Boilerplate')

@section('bodytag')
	<body id="create-edit-page">
@stop

@section('content')

	{{ Form::open(array('route' => array('pages.store'), 'id' => 'form', 'class' => 'container cont-pad-bottom', 'data-bind' => 'submit: publish, pretifyControls')) }}

		<div class="col-sm-9">
			<div class="form-group">
				<label for="title" class="sr-only">{{ trans('dash.title') }}</label>
				<input class="form-control" placeholder="{{ trans('dash.title') }}..." name="title" type="text" id="title"
					   data-bind="value: app.models.page.title, charsRemaining" maxlength="60" autocomplete="off">
			</div>
			
            <div class="ck-container">
            	<textarea name="body" id="editor" data-bind="ckeditor" rows="30" cols="80">
            		{{ isset($page['body']) ? $page['body'] : trans('dash.pageContent') }}
            	</textarea>
            </div>
		</div>

		<div class="col-sm-3">
			@include('Partials.CreateEditPageSidebar')
		</div>

	{{ Form::close() }}

	@include('Partials.MediaUploadModal')

	

@stop

@section('scripts')

	{{ HTML::script('assets/js/vendor/ckeditor/ckeditor.js') }}
	{{ HTML::script('assets/js/vendor/uploader.min.js') }}

	@if (isset($page))
		
		<script>
			app.models.page.title('<?php echo $page["title"] ?>');
			app.models.page.slug('<?php echo $page["slug"] ?>');
			app.models.page.visibility('<?php echo $page["visibility"] ?>');
			app.viewModels.createEditPage.creating(false);
		</script>

	@endif

	<script>
		app.viewModels.createEditPage.start();
	</script>
	
@stop