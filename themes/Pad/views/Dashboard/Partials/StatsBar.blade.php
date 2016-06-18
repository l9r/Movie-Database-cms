<div class="row" id="stats-bar">
				
	<div class="col-sm-2 stats-box">
		<div class="col-sm-6 stats-icon">
			<i class="fa fa-film"></i>
		</div>
		<div class="col-sm-6 stats-text">
			<strong>{{ $count['titles'] }}</strong>
			<span>{{ trans('dash.titles') }}</span> <span class="hidden-md hidden-sm">{{ trans('dash.inDB') }}</span>
		</div>
	</div>

	<div class="col-sm-2 stats-box">
		<div class="col-sm-6 stats-icon">
			<i class="fa fa-bullhorn"></i>
		</div>
		<div class="col-sm-6 stats-text">
			<strong class="stats-num">{{ $count['news'] }}</strong>
			<span class="stats-text">{{ trans('main.news') }}</span> <span class="hidden-md hidden-sm">{{ trans('dash.inDB') }}</span>
		</div>
	</div>

	<div class="col-sm-2 stats-box">
		<div class="col-sm-6 stats-icon">
			<i class="fa fa-video-camera"></i>
		</div>
		<div class="col-sm-6 stats-text">
			<strong class="stats-num">{{ $count['actors'] }}</strong>
			<span class="stats-text">{{ trans('main.actors') }}</span> <span class="hidden-md hidden-sm">{{ trans('dash.inDB') }}</span>
		</div>
	</div>

	<div class="col-sm-2 stats-box">
		<div class="col-sm-6 stats-icon">
			<i class="fa fa-users"></i>
		</div>
		<div class="col-sm-6 stats-text">
			<strong class="stats-num">{{ $count['users'] }}</strong>
			<span class="stats-text">{{ trans('main.users') }}</span> <span class="hidden-md hidden-sm">{{ trans('dash.inDB') }}</span>
		</div>
	</div>

	<div class="col-sm-2 stats-box">
		<div class="col-sm-6 stats-icon">
			<i class="fa fa-thumbs-down"></i>
		</div>
		<div class="col-sm-6 stats-text">
			<strong class="stats-num">{{ $count['reviews'] }}</strong>
			<span class="stats-text">{{ trans('main.reviews') }}</span> <span class="hidden-md hidden-sm">{{ trans('dash.inDB') }}</span>
		</div>
	</div>

</div>