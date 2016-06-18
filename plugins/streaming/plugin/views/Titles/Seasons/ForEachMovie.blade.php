@if (isset($episode) && $episode->links && count($episode->links))
    <div class="status">{{ trans('stream::main.availToStream') }}</div>
@endif