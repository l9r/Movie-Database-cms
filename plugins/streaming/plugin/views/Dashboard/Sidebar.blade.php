
@if(Helpers::hasAnyAccess(['links.delete','links.approve','titles.edit', 'superuser']))
<li class="sidebar-item">
	<a href="{{ url('dashboard/links') }}">
        <i class="sidebar-icon fa fa-external-link"></i>
        <span class="sidebar-text">{{ trans('stream::main.links') }}</span>
    </a>
</li>
@endif