<div class="jumbotron">
	<section style="background-image: url({{ $user->background ? asset($user->background) : asset('assets/images/ronin.jpg') }})" id="img-bg">
		<div id="img-contents" class="col-sm-offset-5">
			<img width="100px" height="100px" class="img-thumbnail" src="{{ $user->avatar ? asset($user->avatar) : asset('assets/images/no_user_icon_big.jpg')}}" alt="">
			<h1>{{ $user->first_name && $user->last_name ? $user->first_name . ' ' . $user->last_name : $user->username }}</h1>
		</div>
	</section>
	<div id="under-image-cont" class="clearfix">			
		<div id="under-image-wrapper">
			<div class="pull-left">
				<div class="stats">
					<span class="number">{{ $watCount }}</span> <br> <span class="text-muted">{{ trans('main.titles watchlisted') }}</span>
				</div>
				<div class="stats">
					<span class="number">{{ $favCount }}</span> <br> <span class="text-muted">{{ trans('main.titles favorited') }}</span>
				</div>
				<div class="stats">
					<span class="number">{{ $revCount }}</span> <br> <span class="text-muted">{{ trans('main.reviews written') }}</span>
				</div>
				<div class="stats">
					<span class="number">{{ $user->created_at->toFormattedDateString() }}</span> <br> <span class="text-muted">{{ trans('main.member since') }}</span>
				</div>
			</div>
			<div class="pull-right">
				<select name="list-name" data-bind="value: params.listName" class="form-control">
					<option value="watchlist">{{ trans('users.watchlist') }}</option>
					<option value="favorite">{{ trans('users.favorites') }}</option>}
				</select>
			</div>
		</div>
	</div>
</div>