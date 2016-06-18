@extends('Main.Boilerplate')

@section('title')
  <title>{{{ $title->title }}} - {{ $options->getSiteName() }}</title>
@stop

@section('meta')
  <meta name="title" content="{{ $title->title . ' - ' . $options->getSiteName() }}">
  <meta name="description" content="{{ $title->plot }}">
  <meta name="keywords" content="{{ $options->getTitlePageKeywords() }}">
  <meta property="og:title" content="{{ $title->title . ' - ' . $options->getSiteName() }}"/>
  <meta property="og:url" content="{{ Request::url() }}"/>
  <meta property="og:site_name" content="{{ $options->getSiteName() }}"/>
  <meta property="og:image" content="{{str_replace('w342', 'original', asset($title->poster))}}"/>
  <meta name="twitter:card" content="summary">
  <meta name="twitter:site" content="@{{ $options->getSiteName() }}">
  <meta name="twitter:title" content="{{ $title->title }}">
  <meta name="twitter:description" content="{{ $title->plot }}">
  <meta name="twitter:image" content="{{ $title->poster }}">
  <link rel="canonical" href="{{ url(Str::slug(trans('main.'.$title->type === 'movie' ? 'movies' : 'series')).'/'.$title->id.'-'.Str::slug($title->title)) }}">
@stop

@section('assets')
  @parent
@stop

@section('bodytag')
  <body id="title-page" itemscope itemtype="http://schema.org/Movie">
@stop

@section('content')

    <section class="container" id="content">

        <div class="row responses"> @include('Partials.Response') </div>

        <div class="col-sm-9">
            <div id="ko-bind">
                <div class="row">

                    @if ($title->type === 'movie' && Hooks::hasReplace('Titles.Show.LinksPanel'))
                        <div data-bind="moreLess" class="row streaming-details">
                            @include('Titles.Partials.DetailsPanel')

                            <div id="social">
                                {{ HTML::socialLink('facebook') }}
                                {{ HTML::socialLink('twitter', $title->title) }}
                                {{ HTML::socialLink('google') }}
                            </div>
                            <div id="lists">
								@if ($title->link->isEmpty())
									<button class="btn btn-primary" data-toggle="modal" data-target="#add-link-modal"><i class="fa fa-plus"></i> {{ trans('stream::main.addLink') }}</button>
								@endif

                                @if ($options->enableBuyNow())
                                    @if ($title->affiliate_link)
                                        <a href="{{ $title->affiliate_link }}" class="btn btn-primary"><i class="fa fa-dollar"></i> {{ trans('main.buy now') }}</a>
                                    @else
                                        <a href="{{ HTML::referralLink($title->title) }}" class="btn btn-primary"><i class="fa fa-dollar"></i> {{ trans('main.buy now') }}</a>
                                    @endif
                                @endif

                                @if (Sentry::getUser())
                                    <button class="btn btn-primary" id="watchlist" data-bind="click: handleLists.bind($data, 'watchlist')">
                                        <!-- ko if: watchlist -->
                                        <i class="fa fa-check-square-o"></i>
                                        <!-- /ko -->

                                        <!-- ko ifnot: watchlist -->
                                        <i class="fa fa-square-o"></i>
                                        <!-- /ko -->

                                        {{ trans('users.watchlist') }}
                                    </button>
                                    <button class="btn btn-primary" id="favorite" data-bind="click: handleLists.bind($data, 'favorite')">
                                        <!-- ko if: favorite -->
                                        <i class="fa fa-check-square-o"></i>
                                        <!-- /ko -->

                                        <!-- ko ifnot: favorite -->
                                        <i class="fa fa-square-o"></i>
                                        <!-- /ko -->

                                        {{ trans('main.favorite') }}
                                    </button>
                                @else
                                    <a class="btn btn-primary" id="watchlist" href="{{ url('login') }}">{{ trans('users.watchlist') }}</a>
                                    <a class="btn btn-primary" id="favorite" href="{{ url('login') }}">{{ trans('main.favorite') }}</a>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            @if (Hooks::hasReplace('Titles.Show.LinksPanel'))
                                {{ Hooks::renderReplace('Titles.Show.LinksPanel', $title) }}
                            @endif
                        </div>
                    @else
                        @if ($title->trailer)
                            <div id="trailer-mask" data-bind="click: showTrailer" data-src="{{ $title->trailer }}">
                                <img class="img-responsive img-thumbnail" src="{{ $title->background }}">
                                <div class="center"><img class="img-responsive" src="{{ asset('assets/images/play.png') }}"> </div>
                            </div>
                            <video id="trailer" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="100%" height="500px"> </video>
                        @else
                             <div id="trailer-mask">
                                <img class="img-responsive img-thumbnail" src="{{ $title->background }}">
                            </div>
                        @endif
                        <div id="social">
                            {{ HTML::socialLink('facebook') }}
                            {{ HTML::socialLink('twitter', $title->title) }}
                            {{ HTML::socialLink('google') }}
                        </div>
                        <div id="lists">
                            @if ($options->enableBuyNow())
                                @if ($title->affiliate_link)
                                    <a href="{{ $title->affiliate_link }}" class="btn btn-primary"><i class="fa fa-dollar"></i> {{ trans('main.buy now') }}</a>
                                @else
                                    <a href="{{ HTML::referralLink($title->title) }}" class="btn btn-primary"><i class="fa fa-dollar"></i> {{ trans('main.buy now') }}</a>
                                @endif
                            @endif

                            @if (Sentry::getUser())
                                <button class="btn btn-primary" id="watchlist" data-bind="click: handleLists.bind($data, 'watchlist')">
                                    <!-- ko if: watchlist -->
                                    <i class="fa fa-check-square-o"></i>
                                    <!-- /ko -->

                                     <!-- ko ifnot: watchlist -->
                                    <i class="fa fa-square-o"></i>
                                    <!-- /ko -->

                                    {{ trans('users.watchlist') }}
                                </button>
                                <button class="btn btn-primary" id="favorite" data-bind="click: handleLists.bind($data, 'favorite')">
                                    <!-- ko if: favorite -->
                                    <i class="fa fa-check-square-o"></i>
                                    <!-- /ko -->

                                     <!-- ko ifnot: favorite -->
                                    <i class="fa fa-square-o"></i>
                                    <!-- /ko -->

                                    {{ trans('main.favorite') }}
                                </button>
                            @else
                                <a class="btn btn-primary" id="watchlist" href="{{ url('login') }}">{{ trans('users.watchlist') }}</a>
                                <a class="btn btn-primary" id="favorite" href="{{ url('login') }}">{{ trans('main.favorite') }}</a>
                            @endif
                        </div>
                     @endif
                </div>

                @if ($ad = $options->getTitleJumboAd())
                    <div id="ad">{{ $ad }}</div>
                @endif

                @if (isset($episodes->previous) && isset($episodes->next))
                <div id="episodes-guide" data-bind="moreLess: 83" class="row">
                    <div class="col-md-6 prev-ep">
                        <h3 class="prev-ep-heading"><span>({{ 'S0'.$episodes->previous->season_number.'E0'.$episodes->previous->episode_number }}) {{ trans('main.prevEp') }}</span></h3>
                        <div class="media">
                            <a class="pull-left" href="{{ Helpers::episodeUrl($title->title, $title->id, $title->type, $episodes->previous->season_number, $episodes->previous->episode_number) }}">
                                <img class="media-object img-thumbnail" src="{{ Helpers::getEpisodeImage($title, $episodes->previous) }}" alt="{{ $episodes->previous->title }}">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <a href="{{ Helpers::episodeUrl($title->title, $title->id, $title->type, $episodes->previous->season_number, $episodes->previous->episode_number) }}">{{ $episodes->previous->title }}</a>
                                </h4>
                                <h2 class="air-date">{{ trans('main.airedOn').' '.$episodes->previous->release_date }} </h2>
                                <p>{{ $episodes->previous->plot ? $episodes->previous->plot : trans('main.noSummary') }}</p>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 next-ep">
                        <h3><span>{{ trans('main.nextEp') }} ({{ 'S0'.$episodes->next->season_number.'E0'.$episodes->next->episode_number }})</span></h3>
                        <div class="media">
                             <a class="pull-right" href="{{ Helpers::episodeUrl($title->title, $title->id, $title->type, $episodes->next->season_number, $episodes->next->episode_number) }}">
                                <img class="media-object img-thumbnail" src="{{ Helpers::getEpisodeImage($title, $episodes->next) }}" alt="{{ $episodes->next->title }}">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <a href="{{ Helpers::episodeUrl($title->title, $title->id, $title->type, $episodes->next->season_number, $episodes->next->episode_number) }}">{{ $episodes->next->title }}</a>
                                </h4>
                                <h2 class="air-date airs-on">{{ trans('main.airsOn').' '.$episodes->next->release_date }} </h2>
                                <p>{{ $episodes->next->plot ? $episodes->next->plot : trans('main.noSummary') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($title->type === 'series' || ! Hooks::hasReplace('Titles.Show.LinksPanel'))
                    <div data-bind="moreLess" class="row">
                        @include('Titles.Partials.DetailsPanel')
                    </div>
                @endif
            </div>

            <section class="row">
                <ul class="nav nav-tabs row">
                    <li class="active"><a href="#cast" data-toggle="tab">{{ trans('main.cast') }}</a></li>
                    <li><a href="#reviews" data-toggle="tab">{{ trans('main.reviews') }}</a></li>
                    <li><a href="#comments" data-toggle="tab">{{ trans('main.comments') }}</a></li>
                    @if(Helpers::hasAccess('titles.edit'))
                        <li><a href="{{ Request::url().'/edit' }}">{{ trans('dash.edit') }}</a></li>
                    @endif
                    <li class="visible-xs"><a href="#images-col">{{ trans('dash.images') }}</a></li>
                </ul>

                <div class="tab-content row">
                    <div class="tab-pane active" id="cast">
                        @foreach($title->actor as $actor)
                            @if(!str_contains($actor->image, 'imdbnoimage'))
                                <figure class="pretty-figure col-md-2 col-sm-6">
                                    <a href="{{ Helpers::url($actor->name, $actor->id, 'people') }}">
                                        <img data-original="{{ $actor->image }}" alt="{{ $actor->name }}" class="img-responsive">
                                    </a>
                                    <figcaption class="{{ $actor->pivot->char_name === 'Unknown' ? 'no-char-name' : '' }}">
                                        <a href="{{ Helpers::url($actor->name, $actor->id, 'people') }}">{{ str_limit($actor->name, 13) }}</a>
                                        @if ($actor->pivot->char_name !== 'Unknown')
                                            <div class="char-name">{{ $actor->pivot->char_name }}</div>
                                        @endif
                                    </figcaption>
                                </figure>
                            @endif
                        @endforeach
                    </div>
                    <div class="tab-pane clearfix" id="comments">
                        <div id="disqus_thread"></div>
                    </div>
                    <div class="tab-pane" id="reviews">

                        <section id="filter-bar" class="clearfix">
                            <div class="form-inline pull-left">
                                {{ Form::select('sort',
                                    array('' => trans('dash.sortBy'), 'dateDesc' => 'Newest First', 'dateAsc' => 'Oldest First', 'scoreDesc' => trans('dash.highRateFirst'), 'scoreAsc' => trans('dash.lowRateFirst')), '',
                                    array('class' => 'form-control', 'data-bind' => 'value: currentSort')) }}

                                <select class="form-control" data-bind="value: currentType" name="type">
                                    <option value="all">{{ trans('main.type') }}...</option>
                                    <option value="user">{{ trans('main.user') }}</option>
                                    <option value="critic">{{ trans('main.critic') }}</option>
                                </select>
                            </div>

                            @if (Sentry::getUser())
                                <button type="button" data-toggle="modal" data-target="#review-modal" class="btn btn-primary pull-right">{{ trans('main.write one') }}</button>
                            @else
                                <a href="{{ url(Str::slug(trans('main.login'))) }}" class="btn btn-primary pull-right">{{ trans('main.write one') }}</a>
                            @endif
                        </section>

                        <!-- ko if: filteredReviews().length <= 0 -->
                        <h2 align="center">{{ trans('dash.noResults') }}</h2>
                        <!-- /ko -->

                        <ul class="boxed-items" data-bind="foreach: filteredReviews">
                            <!-- ko if: type === 'user' || type === 'critic' -->
                            <li class="clearfix">
                                <h3 data-bind="text: author"></h3> <span data-bind="text: source"></span>
                                <div class="rating" data-bind="raty: score, stars: 10, readOnly: true"></div>
                                <p data-bind="text: body.trunc(350)"></p>

                                <!-- ko if: $data.hasOwnProperty('created_at') -->
                                <span class="text-muted">{{ trans('main.published') }} <strong data-bind="text: created_at"></strong></span>
                                <!-- /ko -->

                                <!-- ko if: $data.hasOwnProperty('link') && link -->
                                <a target="_blank" class="pull-right" data-bind="attr: { href: link }">{{ trans('main.full review') }} <i class="fa fa-external-link"></i></a>
                                <!-- /ko -->

                                <!-- ko if: user_id && user_id == app.viewModels.titles.show.userId() -->
                                <button class="btn btn-sm btn-danger pull-right" data-bind="click: $root.delete"><i class="fa fa-trash-o"></i> </button>
                                <!-- /ko -->

                            </li>
                            <!-- /ko -->
                        </ul>

                        @include('Titles.Partials.ReviewModal')
                    </div>
                </div>
            </section>

        </div>

        <div class="col-sm-3" id="images-col">
            @foreach($title->image as $img)
                <img src="{{ $img->path }}" alt="{{ $img->title }}" class="img-responsive img-thumbnail">
            @endforeach
        </div>

    </section>

    <div class="modal" id="video-modal" data-src="{{$title->trailer}}">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="modal-close" data-dismiss="modal" aria-hidden="true">
                    <span class="fa-stack fa-lg">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-times fa-stack-1x fa-inverse"></i>
                    </span>
                </button>
                <div id="video-container"></div>
            </div>
        </div>
    </div>

    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
        <div class="slides"></div>
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
    </div>

    {{ Hooks::renderHtml('Titles.Show.BeforeScripts') }}

@stop

@section('scripts')

    {{ HTML::script('assets/js/vendor/gallery.min.js') }}

    <script>
        app.viewModels.reviews.sourceItems(<?php echo $title->review->toJson(); ?>);
        ko.applyBindings(app.viewModels.titles.show, $('#ko-bind')[0]);
        ko.applyBindings(app.viewModels.reviews, $('#reviews')[0]);

        vars.disqus = '<?php echo $options->getDisqusShortname(); ?>';
        vars.lists = <?php echo json_encode($lists); ?>;
        vars.titleId = '<?php echo $title->id; ?>';
        vars.trailersPlayer = '<?php echo $options->trailersPlayer(); ?>';
        vars.userId = '<?php echo Sentry::getUser() ? Sentry::getUser()->id : false ?>';
        vars.title = <?php echo $title->toJson(); ?>;

        app.viewModels.titles.show.start(<?php echo $title->link && ! $title->link->isEmpty() ? $title->link->first()->toJson() : null; ?>);

        $('[data-original]').lazyload();
    </script>

    {{ Hooks::renderHtml('Titles.Show.AfterScripts') }}

@stop
