@extends('Main.Boilerplate')

@section('title')
    <title> {{ $title->title.' '.trans_choice('main.season', 1).' '.$season->number.', '.trans('main.episode').' '.$episode->episode_number. ' - ' .$options->getSiteName() }}</title>
@stop

@section('bodytag')
  <body id="title-page" class="episode-page original-theme">
@stop

@section('content')

    <div class="container" id="content">
        <div class="row">
            <div class="col-sm-9">
                <div class="clearfix">
                    @if ($hasReplace = Hooks::hasReplace('Titles.Show.LinksPanel'))
                        <div data-bind="moreLess" class="streaming-details">
                            @include('Titles.Episodes.Partials.DetailsPanel')
                        </div>

                        <div class="row">
                            {{ Hooks::renderReplace('Titles.Show.LinksPanel', $links, 'links') }}
                        </div>
                    @endif

                    @if( ! $hasReplace || $links->isEmpty())
                        <div class="row">
                            @if ($episode->promo)
                                <div id="trailer-mask" data-bind="click: showTrailer" data-src="{{ $episode->promo }}">
                                    <img class="img-responsive img-thumbnail" src="{{ Helpers::original(Helpers::getEpisodeImage($title, $episode)) }}">
                                    <div class="center"><img class="img-responsive" src="{{ asset('assets/images/play.png') }}"> </div>
                                </div>
                                <video id="trailer" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="100%" height="500px"> </video>
                            @else
                                <div id="trailer-mask">
                                    <img class="img-responsive img-thumbnail" src="{{ Helpers::original(Helpers::getEpisodeImage($title, $episode)) }}">
                                </div>
                            @endif
                            @if (isset($links) && $links->isEmpty())
                                <button class="btn btn-primary" style="position: absolute; top: 0" data-toggle="modal" data-target="#add-link-modal"><i class="fa fa-plus"></i> {{ trans('stream::main.addLink') }}</button>
                            @endif
                        </div>
                     @endif    
                </div>

                @if ($ad = $options->getTitleJumboAd())
                    <div id="ad">{{ $ad }}</div>
                @endif

                @if( ! $hasReplace)
                    @include('Titles.Episodes.Partials.DetailsPanel')
                @endif
            </div>
            <div class="col-sm-3" id="images-col">
                @if($title->image->count())
                    @foreach($title->image->slice(0, 4) as $img)
                        <img src="{{ $img->path }}" alt="{{ $img->title }}" class="img-responsive img-thumbnail">
                    @endforeach
                @endif
            </div>
        </div>
        <div class="row" id="episode-grid">
            <h2>{{ trans('main.otherEpsForSeason') }}</h2>
            @foreach($season->episode as $ep)
                @if ($ep->episode_number == $episode->episode_number)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <figure>
                            <img src="{{ Helpers::getEpisodeImage($title, $ep) }}" alt="{{ $ep->title }}" class="img-responsive">
                            <figcaption>
                                <span>{{ trans('main.episode').' '.$ep->episode_number.' - '. $ep->title }}</span>
                            </figcaption>
                        </figure>
                    </div>
                @else
                    <a href="{{ Helpers::episodeUrl($title->title, $title->id, $title->type, $season->number, $ep->episode_number) }}" class="col-sm-6 col-md-4 col-lg-3">
                        <figure>
                            <img src="{{ Helpers::getEpisodeImage($title, $ep) }}" alt="{{ $ep->title }}" class="img-responsive">
                            <figcaption>
                                <span>{{ trans('main.episode').' '.$ep->episode_number.' - '. str_limit($ep->title, 25) }}</span>
                            </figcaption>
                        </figure>
                    </a>
                @endif
            @endforeach
        </div>
        <div class="row">
            <div id="disqus_thread"></div>
        </div>
    </div>

    {{ Hooks::renderHtml('Titles.Show.BeforeScripts') }}

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

@stop

@section('scripts')

    <script>
        vars.title = <?php echo $title->toJson(); ?>;
        vars.disqus = '<?php echo $options->getDisqusShortname(); ?>';    
        vars.titleId = '<?php echo $title->id; ?>';
        vars.trailersPlayer = '<?php echo $options->trailersPlayer(); ?>';
        vars.userId = '<?php echo Sentry::getUser() ? Sentry::getUser()->id : false ?>';
        ko.applyBindings(app.viewModels.titles.show, $('#content')[0]);
        app.viewModels.titles.create.activeSeason('<?php echo $season->number ?>');
        app.viewModels.titles.create.activeEpisode('<?php echo $episode->episode_number ?>');
        app.viewModels.titles.show.start(<?php echo isset($links) && ! $links->isEmpty() ? $links->first()->toJson() : null; ?>);   
    </script>

    {{ Hooks::renderHtml('Titles.Show.AfterScripts') }}
@stop