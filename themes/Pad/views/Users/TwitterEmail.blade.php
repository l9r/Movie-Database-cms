@extends('Main.Boilerplate')

@section('htmltag')
  <html id="login-page">
@stop

@section('title')
  <title>twitter - {{ $options->getSiteName() }}</title>
@stop

@section('bodytag')
  <html class="nav-no-border">
@stop

@section('content')

  <div class="container">
    <div class="col-sm-2"></div>

    <div class="col-sm-8" id="login-box">

      @include('Partials.Response')

      @if (isset($failure))
        <div class="alert alert-danger alert-dismissable"> {{ $failure }}
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
      @endif

      {{ Form::open(array('url' => '/social/twitter/email')) }}

      <div class="form-group">
        <label class="mar-bot" for="email"><i class="fa fa-user"></i></span> {{ trans( 'main.twitter mail expl' ) }} </label>
        {{ Form::text('email', Input::old('email'), array('class' => 'form-control')) }}
        
        {{ $errors->first('email', "<span class='help-block alert alert-danger'>:message</span>") }}
      </div>
          
      <hr>
      <button type="submit" class="btn btn-warning pull-right">{{ trans('users.confirm') }}</button>

      {{ Form::close() }}

    </div>
    <div class="col-sm-2"></div>
    <div class="push"></div>
  </div>
@stop

@section('ads')
@stop