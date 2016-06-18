@extends('Main.Boilerplate')

@section('title')
	<title> {{  trans('users.profile details') . ' - ' . $options->getSiteName() }}</title>
@stop

@section('bodytag')
	<body id="users-edit">
@stop

@section('content')
 
    <div class="container content">
        <div class="row"> @include('Partials.Response') </div>

    <section class="col-sm-12 users-upload-box">
      
      <img width="100px" height="100px" src="{{{ $user->avatar ? asset($user->avatar) : asset('assets/images/no_user_icon_big.jpg') }}}" alt="{{{ $user->username . trans('users.avatar') }}}" class="img-responsive thumb">

      {{ Form::open(array('route' => array('users.avatar', $user->id), 'files' => true)) }}

        <div class="form-group">
          {{ Form::file('avatar') }}
          {{ $errors->first('avatar', '<span class="help-block alert alert-danger">:message</span>') }}
          <span class="help-block">*{{ trans('users.avatar expl') }}</span>
        </div>

        <button type="submit" class="btn btn-success">{{ trans('users.upload') }}</button>

      {{ Form::close() }}

      <img width="50%" src="{{{ $user->background ? asset($user->background) : asset('assets/images/ronin.jpg') }}}" alt="{{{ $user->username . trans('users.avatar') }}}" class="img-responsive thumb">

       {{ Form::open(array('route' => array('users.bg', $user->id), 'files' => true)) }}

        <div class="form-group">
          {{ Form::file('bg') }}
          {{ $errors->first('bg', '<span class="help-block alert alert-danger">:message</span>') }}
          <span class="help-block">*{{ trans('main.user bg expl') }}</span>
        </div>

        <button type="submit" class="btn btn-success">{{ trans('users.upload') }}</button>

      {{ Form::close() }}

    </section>

    <div class="col-sm-12">
      {{ Form::model($user, array('route' => array(Str::slug(trans('main.users')).'.update', $user->username), 'method' => 'PUT')) }}
        
        <div class="form-group">
          {{ Form::label('first_name', trans('users.first name')) }}
          {{ Form::text('first_name', Input::old('first_name'), array('class' => 'form-control')) }}
          {{ $errors->first('first_name', '<span class="help-block alert alert-danger">:message</span>') }}
        </div>

        <div class="form-group">
          {{ Form::label('last_name', trans('users.last name')) }}
          {{ Form::text('last_name', Input::old('last_name'), array('class' => 'form-control')) }}
          {{ $errors->first('last_name', '<span class="help-block alert alert-danger">:message</span>') }}
        </div>   

        <div class="form-group">
          {{ Form::label('gender', trans('users.gender')) }}
          {{  Form::select('gender', array('male' => trans('main.male'), 'female' => trans('main.female')), $user->gender, array('class' => 'form-control')) }}
          {{ $errors->first('gender', '<span class="help-block alert alert-danger">:message</span>') }}
        </div>

       

        <button type="submit" class="btn btn-success">{{ trans('dash.update') }}</button>
        <a type="button" href="{{ Helpers::url($user->username, $user->id, 'users') }}" class="btn btn-primary">{{ trans('users.profile') }}</a>
        <a class="btn btn-primary" href="{{ Helpers::url($user->username, $user->id, 'users') . '/change-password' }}">{{ trans('main.changePassword') }}</a>

         
         
      {{ Form::close() }}
    </div>

  <div class="push"></div>
  </div>


  @stop

@section('ads')
@stop