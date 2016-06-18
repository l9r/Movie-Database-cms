<section class="content" data-bind="playVideos">
	
	@if ( ! $slides->isEmpty())
		<div class="jumbotron">
			<div class="home-slider" data-bind="slider">
			  	@foreach($slides as $slide)
			  		<div class="slide" style="background: url('{{ $slide->image }}')">
			  			<div class="overlay">
			  				<section class="pull-left details-column">
		                        <h2><a href="{{ url($slide->link) }}">{{ $slide->title }}</a></h2>
		                        <p>{{ $slide->body }}</p>
		                        <ul class="slider-details list-unstyled">
		                            @if ($slide->director)
		                                <li><strong>{{ trans('main.directedBy') }}</strong>{{ $slide->director }}</li>
		                            @endif
		                            @if ($slide->stars)
		                                <li><strong>{{ trans('main.stars') }}</strong>{{ $slide->stars }}</li>
		                            @endif
		                            @if ($slide->genre)
		                                <li><strong>{{ trans('main.genre') }}</strong>{{ str_replace(' | ', ', ', $slide->genre) }}</li>
		                            @endif
		                            @if ($slide->release_date)
		                                <li><strong>{{ trans('main.release date') }}</strong>{{ $slide->release_date }}</li>
		                            @endif
		                        </ul>
		                    </section>

		                    @if ($slide->trailer)
	                            <div id="trailer-box" class="pull-right trailer-column play" data-source="{{ $slide->trailer }}" data-poster="{{ $slide->trailer_image }}">
	                                <img src="{{ $slide->trailer_image }}" class="img-responsive">
	                                <div class="overlay"></div>
	                                <div class="center"><img src="{{ asset('assets/images/play.png') }}"> </div>
	                            </div>
	                        @endif
			  			</div>
			  		</div>
			  	@endforeach
			</div>
		</div>
	@endif

    <div class="container {{ $slides->isEmpty() ? 'no-slider' : '' }}">

		@if ($ad = $options->getHomeJumboAd())
            <div id="ad">{{ $ad }}</div>
        @endif

    	@include('Partials.Response')

		<div class="{{ $options->enableNews() ? 'col-sm-9' : 'col-sm-12 no-news' }}">
			@if($categories->count())
				@foreach($categories as $category)
					@if ($category->active)
						<section class="row {{ $category->show_rating ? 'with-rating' : '' }} title-sizes">
							<h2 class="heading"><i class="{{ $category->icon }}"></i> {{ $category->name }}</h2>

							@if ($category->actor && ! $category->actor->isEmpty())
								@foreach($category->actor->slice(0, $category->limit) as $actor)
									<figure class="{{ $options->enableNews() ? 'col-md-3 col-sm-6' : 'col-lg-2 col-md-3 col-sm-4' }}  pretty-figure">
										<a href="{{ Helpers::url($actor->name, $actor->id, 'people') }}"><img src="{{ $actor->image }}" alt="{{ $actor->name }}" class="img-responsive"></a>
										<figcaption><a href="{{ Helpers::url($actor->name, $actor->id, 'people') }}">{{ $actor->name }}</a></figcaption>
									</figure>
								@endforeach
							@endif

							@if ($category->title && ! $category->title->isEmpty())
								@foreach($category->title->slice(0, $category->limit) as $title)
									<figure class="{{ $options->enableNews() ? 'col-md-3 col-sm-6' : 'col-lg-2 col-md-3 col-sm-4' }}  pretty-figure">
										<div class="{{ $category->show_trailer ? 'play' : '' }}" data-source="{{ $title->trailer }}" data-poster="{{ $title->background }}">

											@if ($category->show_trailer && $title->trailer)
												<img src="{{ $title->poster }}" alt="{{ $title->title }}" class="img-responsive">
												<div class="center"><img src="{{ asset('assets/images/play.png') }}"> </div>
											@else
												<a href="{{ Helpers::url($title->title, $title->id, $title->type) }}"><img src="{{ $title->poster }}" alt="{{ $title->title }}" class="img-responsive"></a>
											@endif

										</div>
                                        @if(Hooks::hasReplace('Home.ForEachMovie'))
                                            {{ Hooks::renderReplace('Home.ForEachMovie', $title) }}
                                        @endif
										<figcaption {{ ! $title->mc_user_score ? 'class="no-rating"' : ''}}>
											<a href="{{ Helpers::url($title->title, $title->id, $title->type) }}">{{ $title->title }}</a>

											@if ($category->show_rating && $title->mc_user_score)
												<div class="home-rating" data-bind="raty: <?php echo $title->mc_user_score; ?>, readOnly: true, stars: 10"></div>
											@endif
										</figcaption>
									</figure>
								@endforeach
							@endif
						</section>
					@endif		
				@endforeach
			@else
				<h4>Create categories you want to display from <strong>dashboard > categories</strong> page.</h4>
			@endif
		</div>
		
		<!-- latest news-->
		@if ($options->enableNews())
			<div class="col-sm-3" id="news-container">
				<h2 class="heading"><i class="fa fa-bullhorn"></i> {{ trans('main.latest news') }}</h2>

				<ul class="list-unstyled">
					@foreach($news as $k => $item)

						@if ($k == 4)
							@if ($ad = $options->getHomeNewsAd())
					            <div id="ad">{{ $ad }}</div>
					        @endif
						@endif

						<li>
							<figure class="pretty-figure">
								<a href="{{ Helpers::url($item->title, $item->id, 'news') }}"><img src="{{ $item->image }}" alt="{{ $item->title }}" class="img-responsive"></a>
								<figcaption><a href="{{ Helpers::url($item->title, $item->id, 'news') }}">{{ $item->title }}</a></figcaption>
							</figure>
						</li>
					@endforeach
				</ul>
			</div>
		@endif
		<!-- /latest news-->

	</div>
</section>

<!-- video modal -->
<div class="modal fade" id="vid-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        	<button type="button" class="modal-close" data-dismiss="modal" aria-hidden="true"> 
                <span class="fa-stack fa-lg">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-times fa-stack-1x fa-inverse"></i>
                </span>
            </button>
            <div class="modal-body"> </div>
        </div>
     </div>
</div>
<!-- /video modal -->