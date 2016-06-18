@extends('Main.Boilerplate')

@section('bodytag')
	<body id="titles-index">
@stop

@section('content')

  	<div class="container" id="content">

        <div class="row">
            {{ $movies->appends(array('_escaped_fragment_' => 1))->links() }}
        </div>

  		<section class="row">

            @foreach($movies as $movie)
      			<figure class="col-sm-4 col-md-3 pretty-figure">
      				<a href="{{ url(Str::slug(trans("main.$type")).'/'.$movie->id.'-'.Str::slug($movie->title)) }}"><img class="img-responsive" src="{{ $movie->poster }}" alt="{{ $movie->title }}"></a>
      				<figcaption class="clearfix">
      					@if ($movie->mc_user_score)
          					<a class="pull-left" href="{{ url(Str::slug(trans("main.$type")).'/'.$movie->id.'-'.Str::slug($movie->title)) }}">{{ $movie->title }}</a>
          					<div class="pull-right">{{ $movie->mc_user_score }}/10</div>
      					@else
      					    <a href="{{ url(Str::slug(trans("main.$type")).'/'.$movie->id.'-'.Str::slug($movie->title)) }}">{{ $movie->title }}</a>
      					@endif
      				</figcaption>
      			</figure>
            @endforeach
  			
  		</section>


	</div>

@stop
