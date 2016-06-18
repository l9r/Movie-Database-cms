@extends('Main.Boilerplate')

@section('bodytag')
	<body id="search-page">
@stop

@section('nav')
  @include('Partials.Navbar')
@stop

@section('content')

<div class="container">

	<div class="row well">

		<div class="pull-left">
			<p><i class="fa fa-search"></i> {{ trans('main.top matches for') }} <strong>{{{ $term }}}</strong></p>
		</div>

    	<div class="pull-right hidden-xs">
			<ul class="btn-group list-unstyled list-inline">
				<li class="active"><a href="#all" class="btn btn-primary" data-toggle="tab"><i class="fa fa-video-camera visible-xs"></i><span class="hidden-xs">{{ trans('main.all') }}</span></a></li>
			    <li><a href="#movies" class="btn btn-primary" data-toggle="tab"><i class="fa visible-xs fa-users"></i><span class="hidden-xs">{{ trans('main.movies') }}</span></a></li>
			    <li><a href="#series" class="btn btn-primary" data-toggle="tab"><i class="fa visible-xs fa-thumbs-up"></i><span class="hidden-xs">{{ trans('main.series') }}</span></a></li>
			    @if ( isset($actors) && ! $actors->isEmpty() )		
			    	<li><a href="#people" class="btn btn-primary" data-toggle="tab"><i class="fa fa-video-camera visible-xs"></i><span class="hidden-xs">{{ trans('main.people') }}</span></a></li>
			    @endif    
			</ul>
		 </div>

	</div>

    <div class="row"> @include('Partials.Response') </div>

	<div class="tab-content clearfix">
		<div class="tab-pane fade in active" id="all">
	       	@if ( isset($data) && ! $data->isEmpty() )
				<div class="row">
					<h2>{{ trans('main.movies') }}</h2>

					@foreach(Helpers::withImages($data, 6, 'movie') as $k => $r)
						@if ($r->type == 'movie')
							<figure class="col-lg-2 col-md-3 col-sm-6 pretty-figure">
						    	<a href="{{Helpers::url($r['title'], $r['id'], $r['type'])}}"><img class ="img-responsive" src="{{str_replace('w185', 'w342', $r->poster) }}" alt="{{{ $r['title'] }}}"></a>

							  	<figcaption title="{{{ $r->title }}}" >
							  		<a href="{{ Helpers::url($r['title'], $r['id'], $r['type']) }}">{{  Helpers::shrtString($r['title']) }}</a>
							  	</figcaption>
						    </figure>
						@endif
					@endforeach
				</div>
			@endif

	       	@if ( isset($data) && ! $data->isEmpty() )
				<div class="row">
					<h2>{{ trans('main.series') }}</h2>

					@foreach(Helpers::withImages($data, 6, 'series') as $k => $r)
						@if ($r->type == 'series')
							<figure class="col-lg-2 col-md-3 col-sm-6 pretty-figure">
						    	<a href="{{Helpers::url($r['title'], $r['id'], $r['type'])}}"><img class ="img-responsive" src="{{str_replace('w185', 'w342', $r->poster) }}" alt="{{{ $r['title'] }}}"></a>

							  	<figcaption title="{{{ $r->title }}}" >
							  		<a href="{{ Helpers::url($r['title'], $r['id'], $r['type']) }}">{{  Helpers::shrtString($r['title']) }}</a>
							  	</figcaption>
						    </figure>
						@endif
					@endforeach
				</div>
			@endif

			@if ( isset($actors) && ! $actors->isEmpty() )
				<div class="row">
					<h2>{{ trans('main.people') }}</h2>

					@foreach(Helpers::withImages($actors, 6) as $k => $r)
						<figure class="col-lg-2 col-md-3 col-sm-6 pretty-figure">
					    	<a href="{{Helpers::url($r['name'], $r['id'], 'people')}}"><img class ="img-responsive" src="{{str_replace('w185', 'w342', $r->image) }}" alt="{{{ $r['name'] }}}"></a>

						  	<figcaption title="{{{ $r->name }}}" >
						  		<a href="{{Helpers::url($r['name'], $r['id'], 'people')}}">{{ $r['name'] }} </a>
						  	</figcaption>    
					    </figure>
					@endforeach
				</div>
			@endif
      	</div>

      	<div class="tab-pane fade in" id="movies">
	       	@if ( isset($data) && ! $data->isEmpty() )
				@foreach($data->slice(0,12) as $k => $r)
					@if ($r->type == 'movie')
						<figure class="col-lg-2 col-md-3 col-sm-6 pretty-figure">
					    	<a href="{{Helpers::url($r['title'], $r['id'], $r['type'])}}"><img class ="img-responsive" src="{{str_replace('w185', 'w342', $r->poster) }}" alt="{{{ $r['title'] }}}"></a>

						  	<figcaption title="{{{ $r->title }}}" >
						  		<a href="{{Helpers::url($r['title'], $r['id'], $r['type'])}}"> {{  Helpers::shrtString($r['title']) }} </a>
						  	</figcaption>
					    </figure>
					@endif
				@endforeach
			@else
				<div><h3 class="nothing-found">{{ trans('main.no movies found') }}</h3></div>
			@endif
      	</div>

    	<div class="tab-pane fade" id="series">
       		@if ( isset($data) && ! $data->isEmpty() )
				@foreach($data as $k => $r)
					@if ($r->type == 'series')
						<figure class="col-lg-2 col-md-3 col-sm-6 pretty-figure">
					    	<a href="{{Helpers::url($r['title'], $r['id'], $r['type'])}}"><img class ="img-responsive" src="{{str_replace('w185', 'w342', $r->poster) }}" alt="{{{ $r['title'] }}}"></a>

						  	<figcaption title="{{{ $r->title }}}" >
						  		<a href="{{Helpers::url($r['title'], $r['id'], $r['type'])}}"> {{  Helpers::shrtString($r['title']) }} </a>
						  	</figcaption> 
					    </figure>
					@endif
				@endforeach
			@else
				<div><h3 class="nothing-found">{{ trans('main.no series found') }}</h3></div>
			@endif
      	</div>

      	<div class="tab-pane fade" id="people">
        	@if ( isset($actors) && ! $actors->isEmpty() )
				@foreach($actors as $k => $r)
					<figure class="col-lg-2 col-md-3 col-sm-6 pretty-figure">
				    	<a href="{{Helpers::url($r['name'], $r['id'], 'people')}}"><img class ="img-responsive" src="{{str_replace('w185', 'w342', $r->image) }}" alt="{{{ $r['name'] }}}"></a>

					  	<figcaption title="{{{ $r->name }}}" >
					  		<a href="{{Helpers::url($r['name'], $r['id'], 'people')}}">{{ $r['name'] }} </a>
					  	</figcaption>    
				    </figure>
				@endforeach
			@else
				<div><h3 class="nothing-found">{{ trans('main.no actors found') }}</h3></div>
			@endif
      	</div>
    </div>
</div>

@stop
