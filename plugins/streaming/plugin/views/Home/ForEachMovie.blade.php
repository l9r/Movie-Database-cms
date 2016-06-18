@if ($title->link && ! $title->link->isEmpty())
    <div class="status">{{ trans('stream::main.availToStream') }}</div>
@endif