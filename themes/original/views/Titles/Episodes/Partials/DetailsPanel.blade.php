<section class="row" id="details-container">
    <div class="col-md-2 visible-lg">
        <img class="img-responsive" src="{{ $title->poster }}">
    </div>
    <div class="col-lg-10 clearfix" id="details">
        <div class="col-md-8">
            <h3>
                <p class="episode-byline">
                    <a href="{{ Helpers::url($title->title, $title->id, $title->type) }}">{{ $title->title }}</a>:
                    {{trans_choice('main.season', 1).' '.$season->number.', '.trans('main.episode').' '.$episode->episode_number }}
                </p>
                <a href="{{ Request::url() }}">
                    {{ $episode->title }}
                </a>
            </h3>

            <ul class="list-unstyled list-inline">
                @foreach(explode('|', $title->genre) as $genre)

                    <li><a href="{{ route(($title->type == 'series' ? $title->type : $title->type.'s').'.index').'?genre='.trim($genre) }}">{{ $genre }}</a></li>
                @endforeach
            </ul>

            <div data-bind="moreLess">
                <p>{{ $episode->plot }}</p>
            </div>

            <div id="people">
                @if ( ! $title->director->isEmpty())
                    <strong>{{ trans('main.directors') }}:</strong>
                    <ul class="list-unstyled list-inline">
                        @foreach($title->director as $director)
                            <li>{{ $director->name }}</li>
                        @endforeach
                    </ul><br>
                @endif

                @if ( ! $title->writer->isEmpty())
                    <strong>{{ trans('main.writing') }}:</strong>
                    <ul class="list-unstyled list-inline">
                        @foreach($title->writer->slice(0, 3) as $writer)
                            <li>{{ $writer->name }}</li>
                        @endforeach
                    </ul><br>
                @endif

                @if ( ! $title->actor->isEmpty())
                    <strong>{{ trans('main.stars') }}:</strong>
                    <ul class="list-unstyled list-inline">
                        @foreach($title->actor->slice(0, 3) as $actor)
                            <li><a href="{{ Helpers::url($actor->name, $actor->id, 'people') }}">{{ $actor->name }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-md-4" id="sub-details">

            @if ($episode->release_date)
                <strong>{{ trans('main.release date') .': ' }}</strong><span>{{ $title->release_date }}</span><br>
            @endif

            @if ( ! $title->season->isEmpty())
                <strong>{{ trans('main.seasons') }}: </strong>
                @foreach($title->season as $s)
                    <a href="{{ Helpers::season($title->title, $s) }}">{{ $s->number }}</a>
                @endforeach
                <br>
            @endif
            <div id="ratings">
                @if ($title->mc_user_score)
                    <strong class="raty">Metacritic: </strong>
                    <span class="hidden-md" data-bind="raty: {{ $title->mc_user_score }}, stars: 5, readOnly: true"></span>
                    <span class="raty-text">{{ $title->mc_user_score }}/10</span>
                    <br>
                @endif

                @if ($title->tmdb_rating)
                    <strong class="raty">TMDb: </strong>
                    <span class="hidden-md" style="padding-left: 32px" data-bind="raty: {{ $title->tmdb_rating }}, stars: 5, readOnly: true"></span>
                    <span class="raty-text">{{ $title->tmdb_rating }}/10</span>
                    <br>
                @endif

                @if ($title->imdb_rating)
                    <strong class="raty">IMDb: </strong>
                    <span class="hidden-md" style="padding-left: 32px" data-bind="raty: {{ $title->imdb_rating }}, stars: 5, readOnly: true"></span>
                    <span class="raty-text">{{ $title->imdb_rating }}/10</span>
                @endif
            </div>


            <ul class="list-unstyled">
                @if ($title->country)
                    <li><strong>{{ trans('main.country') .': ' }}</strong><span>{{ $title->country }}</span></li>
                @endif

                @if ($title->language)
                    <li><strong>{{ trans('dash.language') .': ' }}</strong><span>{{ $title->language }}</span></li>
                @endif

                @if ($title->runtime)
                    <li><strong>{{ trans('main.runtime') .': ' }}</strong><span>{{ $title->runtime }}</span></li>
                @endif

                @if ($title->budget)
                    <li><strong>{{ trans('main.budget') .': ' }}</strong><span>{{ $title->budget }}</span></li>
                @endif

                @if ($title->revenue)
                    <li><strong>{{ trans('main.revenue') .': ' }}</strong><span>{{ $title->revenue }}</span></li>
                @endif
            </ul>
        </div>

    </div>
</section>