@extends('Main.Boilerplate')

@section('title')

	<title>{{{ $user->username }}} - {{ trans('users.profile') }}</title>

@stop

@section('bodytag')
	<body class="padding nav user-profile">
@stop

@section('content')
	
	<div class="container push-footer-wrapper">
		
		@include('Users.Partials.Header')

		<div class="lists-wrapper reviews">
			
			@foreach ($reviews as $k => $r)

			<li>			
				<div class="row review-info">
                	<span class="review-score"><span>{{{ $r->score * 10 }}}</span></span>  <strong>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($r->created_at))->diffForHumans() }}</strong> - <strong>{{ Title::find($r->title_id)->title }}</strong>
	            </div>

	            <p class="review-body">{{{ $r->body }}}</p>

	            <hr> 
			</li>

		@endforeach

		{{ $reviews->links() }}
			
		</div>
	<div class="push"></div>
	</div>

@stop

@section('ads')
@stop
