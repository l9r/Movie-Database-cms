 <section id="actions-row" class="row">
    <div id="social" class="col-md-4 col-xs-12">
        {{ HTML::socialLink('facebook') }}
        {{ HTML::socialLink('twitter') }}
        {{ HTML::socialLink('google') }}
    </div>

    <div class="col-lg-4 col-md-3 hidden-sm hidden-xs col-xs-12" id="status">
        @if ( ! $links->isEmpty())
            <span class="text-success"><a href="">{{ trans('stream::main.availToStream') }}</a></span>
        @else
            <span class="text-danger">{{ trans('stream::main.notAvailToStream') }}</span>
        @endif
    </div>
    <div id="lists" class="col-lg-4 col-md-5 col-xs-12">
        <div class="btn btn-primary" data-toggle="modal" data-target="#add-link-modal"><i class="fa fa-plus"></i> {{ trans('stream::main.addLink') }}</div>
    </div>
</section>

@if ( ! $links->isEmpty())
    <div id="videos">
        <ul class="nav nav-tabs">
            @foreach ($links as $k => $video)             
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
@endif
