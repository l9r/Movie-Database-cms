@extends('Main.Boilerplate')

@section('title')
	<title> {{  trans('main.create actor') . ' - ' . trans('main.brand') }}</title>
@stop

@section('bodytag')
	<body id="create-edit-page">
@stop

@section('content')

<div class="container" id="content">

	<div class="col-sm-9">

		 <ul class="nav nav-tabs nav-justified">
            <li class="active"><a href="#details" data-toggle="tab">{{ trans('main.details') }}</a></li>
            <li><a href="#filmo" data-toggle="tab">{{ trans('main.filmo') }}</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="details">
            	{{ Form::open(array('id' => 'main-form')) }}
					@include('Actors.Partials.CreateEditForm')
				{{ Form::close() }}
            </div>

            <div class="tab-pane" id="filmo">
            	<table class="table table-striped table-centered">
			    	<thead>
			        	<tr>
			          		<th>#</th>
			          		<th>{{ trans('main.title') }}</th>
			          		<th>{{ trans('main.known for') }}</th>
			          		<th>{{ trans('main.year') }}</th>
			          		<th>{{ trans('dash.actions') }}</th>
			        	</tr>
			      	</thead>
			      <tbody data-bind="foreach: vars.filmo">
			        <tr>
			        	<td class="col-sm-1" data-bind="text: $index()+1"></td>
			          	<td class="col-sm-4" data-bind="text: title"></td>
			          	<td class="col-sm-3">
			          	{{ Form::open() }}
				          	<select class="form-control" name="known_for" onchange="app.viewModels.actorsCreate.knownFor(this.parentNode)">
				          		<option value="0">{{ trans('dash.no') }}</option>
			          			<!-- ko if: pivot.known_for -->
			          			<option value="1" selected>{{ trans('dash.yes') }}</option>
			          			<!-- /ko -->
			          			<!-- ko ifnot: pivot.known_for -->
			          			<option value="1">{{ trans('dash.yes') }}</option>
			          			<!-- /ko -->
				          	</select>
				          	<input type="hidden" name="title_id" data-bind="attr: { value: id }">
				          	<input type="hidden" name="actor_id" data-bind="attr: { value: vars.actor.id }">
			          	{{ Form::close() }}
			          	</td>
			          	<td class="col-sm-2" data-bind="text: year"></td>
			          	<td class="col-sm-2">
			          		<button class="btn btn-danger" data-bind="click: $root.detachTitle">{{ trans('dash.detach') }}</button>
			          	</td>
			        </tr>
			      </tbody>
			    </table>
            </div>
        </div>
    </div>

    <section class="col-sm-3">
        <div class="panel panel-default" data-bind="preventSubmitOnEnter">
            <div class="panel-heading"><i class="fa fa-save"></i> {{ trans('dash.save') }}</div>
            <div class="panel-body">
                <button type="button" class="btn btn-primary" data-bind="click: save">
                    {{ trans('dash.save') }}
                </button>
            </div>  
        </div>

        <div class="panel panel-default" data-bind="preventSubmitOnEnter">
            <div class="panel-heading"><i class="fa fa-picture-o"></i> {{ trans('dash.uploadImage') }}</div>
            <div class="panel-body">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload-media-modal">
                    {{ trans('dash.uploadImage') }}
                </button>
            </div>  
        </div>
    </section>
</div>

@include('Partials.MediaUploadModal')

@stop

@section('ads')
@stop

@section('scripts')

    {{ HTML::script('assets/js/vendor/uploader.min.js') }}

    @if (isset($actor))
        <script>
            vars.actor = <?php echo $actor->toJson(); ?>;
            app.viewModels.actorsCreate.map();
        </script>
    @endif

    <script>
    	vars.people = '<?php echo Str::slug(trans("main.people")); ?>';
        app.viewModels.actorsCreate.start();
    </script>

@stop