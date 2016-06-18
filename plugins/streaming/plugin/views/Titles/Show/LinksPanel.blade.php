<?php

$links = isset($title) ? $title->link : $links;

?>

@if ( ! isset($title) || $title->type == 'movie')
    @if (isset($links) && ! $links->isEmpty())
        <table class="table table-striped links-table">
            <thead>
                <tr>
                    <th class="name">{{ trans('stream::main.name') }}</th>
                    <th>{{ trans('stream::main.quality') }}</th>
                    <th>{{ trans('stream::main.rating') }}</th>
                    <th>{{ trans('stream::main.report') }}</th>
                    <th>{{ trans('stream::main.added') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($links as $k => $video)
                    @if((int)$video->approved)
                        <tr data-id="{{$video->id}}">
                            <td class="name hover" data-bind="click: playVideo.bind($data, '{{$video->url}}', '{{$video->type}}')"><img data-bind="attr: {src: app.utils.getFavicon('{{$video->url}}')}">{{ $video->label }}</td>
                            <td>HD</td>
                            <td>
                                <div class="vote-positive" data-bind="click: rateLink.bind($data, '{{$video->id}}', 'positive')"><i class="fa fa-thumbs-up"></i> (<span class="votes">{{$video->positive_votes}}</span>)</div>
                                <div class="vote-negative" data-bind="click: rateLink.bind($data, '{{$video->id}}', 'negative')">(<span class="votes">{{$video->negative_votes}}</span>) <i class="fa fa-thumbs-down"></i></div></td>
                            <th><i class="fa fa-warning text-primary"></i> <a href="#" data-bind="click: report.bind($data, '{{$video->id}}')">{{ trans('stream::main.report') }}</a></th>
                            <th>{{$video->created_at->diffForHumans()}}</th>
                        </tr>
                    @endif
                @endforeach
            <tr><td><div class="name"><button class="btn btn-primary" data-toggle="modal" data-target="#add-link-modal"><i class="fa fa-plus"></i> {{ trans('stream::main.addLink') }}</button></div></td><td></td><td></td><td></td><td></td></tr>
            </tbody>
        </table>
    @endif
@endif
