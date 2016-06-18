@extends('Main.Boilerplate')

@section('title')
    <title>{{ trans('main.overview of') }} '{{{ $title->title }}}' {{ trans_choice('main.season', 1) }} {{{ $num }}} - {{ $options->getSiteName() }}</title>
@stop

@section('bodytag')
  <body id="title-page">
@stop

@section('content')

    <div class="container episode-list" id="content">

        @include('Titles.Partials.DetailsPanel')

        <div class="heading">{{ trans_choice('main.season', 1).' '.$num.' '.trans('main.episodeList') }}</div>

        <ul class="list-unstyled" id="episode-list" data-bind="moreLess, playVideos">
            @foreach($episodes as $episode)
                <li class="media">
                    @if ($episode->promo)
                        <a class="col-sm-3 play" href="{{ Helpers::episodeUrl($title->title, $title->id, $title->type, $num, $episode->episode_number) }}" data-source="{{ $episode->promo }}" data-poster="{{ str_replace('w300', 'original', Helpers::getEpisodeImage($title, $episode)) }}">
                            <img class="media-object img-responsive" src="{{ Helpers::getEpisodeImage($title, $episode) }}" alt="{{ $episode->title }}">
                            <div class="center"><img class="img-responsive" src="{{ asset('assets/images/play.png') }}"> </div>
                            @if (Hooks::hasReplace('Titles.Seasons.ForEachMovie'))
                                {{ Hooks::renderReplace('Titles.Seasons.ForEachMovie', $episode, 'episode') }}
                            @endif
                        </a>
                    @else
                        <a class="col-sm-3" href="{{ Helpers::episodeUrl($title->title, $title->id, $title->type, $num, $episode->episode_number) }}">
                            <img class="media-object img-responsive" src="{{ Helpers::getEpisodeImage($title, $episode) }}" alt="{{ $episode->title }}">
                            @if (Hooks::hasReplace('Titles.Seasons.ForEachMovie'))
                                {{ Hooks::renderReplace('Titles.Seasons.ForEachMovie', $episode, 'episode') }}
                            @endif
                        </a>
                    @endif
                    <div class="media-body">
                        <h4 class="media-heading"><a href="{{ Helpers::episodeUrl($title->title, $title->id, $title->type, $num, $episode->episode_number) }}">{{ trans('main.episode').' '.$episode->episode_number.' - '.$episode->title }}</a></h4>
                        <strong>{{ trans('main.release date').': '.$episode->release_date }}</strong>
                        <p>{{ $episode->plot }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

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

@stop

@section('scripts')
    <script>
        ko.applyBindings(app.viewModels.titles.show, $('#content')[0]);
    </script>
@stop