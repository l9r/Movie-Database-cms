@extends('Main.Boilerplate')

@section('title')
	<title> {{  trans('main.editing') . ' - ' . $actor['name'] . ' - ' . trans('main.brand')}}</title>
@stop

@section('bodytag')
	<body id="edit">
@stop

@section('content')

<div class="container">

	<div class="col-sm-12">

    	<h3 class="heading">{{ trans('main.edit title heading', array('title' => $actor['name'])) }} <i class="fa fa-pencil"></i></h3 class="heading">

    	<p class="padding-top-bot"> {{ trans('main.edit cast expl') }} </p>

    	<div class="row"> @include('Partials.Response') </div>
 			
 			{{ Form::model($actor, array('route' => array(Str::slug(trans('main.people')) . '.update', $actor['id']), 'method' => 'PUT')) }}
			
				@include('Actor.Partials.CreateEditForm')


				{{ Form::hidden('id', $actor['id']) }}
				{{ Form::hidden('allow_update', 0) }}

				<a type="button" href="{{ url(Str::slug(trans('main.people')) .'/'. Request::Segment(2)) }}" class="btn btn-warning">
					<i class="fa fa-arrow-left"></i> {{ trans('main.back') }}
					</a>

				{{ Form::submit( trans('main.update'), array('class' => 'btn btn-success') ) }}

			{{ Form::close() }}
        
    </div>
</div>

@stop

@section('ads')
@stop