@if ( ! $title->link->isEmpty())
    <div id="videos">
        <ul class="nav nav-tabs">
            @foreach ($title->link as $k => $video)             
                    @if((int)$video->approved)
                        <li {{ $k === 0 ? 'class="active"' : null }} id="{{$video->id}}">
                            <a href="#" data-bind="click: renderTab.bind($data, {{(int)$video->id}}, '{{$video->url}}', '{{$video->type}}', 500)">
                                {{ $video->label }}
                                <i class="fa fa-flag report" data-bind="click: report.bind($data, {{$video->id}})" title="{{ trans('stream.reportLink') }}"></i>
                            </a>
                        </li>
                    @endif             
            @endforeach
        </ul>

        <div class="tab-content"></div>
    </div>

    <video id="trailer" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="100%" height="500px"> </video>
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
@endif  