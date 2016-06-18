@extends('Main.Boilerplate')

@section('bodytag')
	<body id="contact-page">
@stop

@section('content')

    <div class="container">

    	<div class="col-sm-3"></div>

		<div class="col-sm-6" id="contact-box">
			
			@include('Partials.Response')

			<h2>{{ trans('main.contact') }}</h2>

			<div class="help-block">To contact us use the form below.</div>

			{{ Form::open(array('route' => 'submit.contact', 'class' => 'form-horizontal')) }}

				{{ Form::label('name', trans('main.name')) }} <span class="req">*</span>
				{{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
				{{ $errors->first('name', '<span class="help-block alert alert-danger">:message</span>') }}

				{{ Form::label('email', trans('main.email')) }} <span class="req">*</span>
				{{ Form::text('email', Input::old('email'), array('class' => 'form-control')) }}
				{{ $errors->first('email', '<span class="help-block alert alert-danger">:message</span>') }}

				{{ Form::label('comment', trans('main.contact messages')) }} <span class="req">*</span>
				{{ Form::textarea('comment', Input::old('comment'), array('class' => 'form-control', 'rows' => 5)) }}
				{{ $errors->first('comment', '<span class="help-block alert alert-danger">:message</span>') }}

				{{ Form::label('human', trans('main.are you human')) }} <span class="req">*</span>

				<br>
				{{ HTML::image(Captcha::img(), trans('main.captchaImage'), array('class' => 'captcha-image')) }}
				{{ Form::text('captcha', null, array('class' => 'form-control')) }}
				{{ $errors->first('captcha', '<span class="help-block alert alert-danger">:message</span>') }}
				
				

				{{ Form::submit('Submit', array('class' => 'btn btn-success')) }}

    		{{ Form::close() }}

		</div>

    	<div class="col-sm-3"></div>
    </div>

@stop

@section('ads')
@stop


