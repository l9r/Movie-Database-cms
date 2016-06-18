@extends('Main.Boilerplate')

@section('bodytag')
  <body id="not-found">
@stop

@section('nav')
@stop

@section('content')

  <div class="clearfix">
    <div class="align">
      <h1>Oops!</h1>
      <h2>{{ trans('dash.404') }}</h2>
      <p>{{ trans('dash.404sub') }}</p>
      <a href="{{ route('home') }}" class="btn btn-primary btn-block"><i class="fa fa-chevron-left"></i> {{ trans('dash.takeMeHome') }}</a>
    </div>
  </div>

@stop

@section('ads')
@stop

@section('footer')
@stop

@section('scripts')
  <script>
    $.fn.center = function () {
       this.css("position","absolute");
       this.css("top", ( ($(window).height() - this.height() ) / 2) - 100 + "px");
       this.css("left", ( $(window).width() - this.width() ) / 2 + "px");
       return this;
    }

    $('.align').center();
  </script>
@stop

