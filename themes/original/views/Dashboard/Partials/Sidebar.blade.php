<aside id="sidebar">	
	<ul class="sidebar-inner">

		<li class="sidebar-item {{ Request::is('*media*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/media') }}">
                <i class="sidebar-icon fa fa-camera-retro"></i>
                <span class="sidebar-text">{{ trans('dash.media') }}</span>
            </a>
		</li>

		<li class="sidebar-item {{ Request::is('*pages*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/pages') }}">
                <i class="sidebar-icon fa fa-edit"></i>
                <span class="sidebar-text">{{ trans('dash.pages') }}</span>
            </a>
		</li>

		@if(Helpers::hasAnyAccess(['titles.create', 'titles.edit', 'titles.delete', 'superuser']))
		<li class="sidebar-item {{ Request::is('*dashboard') ? 'active' : '' }}">
			<a href="{{ url('dashboard') }}">
                <i class="sidebar-icon fa fa-film"></i>
                <span class="sidebar-text">{{ trans('dash.titles') }}</span>
            </a>
		</li>
		@endif

		<li class="sidebar-item {{ Request::is('*menus*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/menus') }}">
                <i class="sidebar-icon fa fa-bars"></i>
                <span class="sidebar-text">{{ trans('dash.menus') }}</span>
            </a>
		</li>

		<li class="sidebar-item {{ Request::is('*categories*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/categories') }}">
                <i class="sidebar-icon fa fa-th"></i>
                <span class="sidebar-text">{{ trans('dash.categories') }}</span>
            </a>
		</li>

		@if(Helpers::hasAnyAccess(['people.create', 'people.edit', 'people.delete', 'superuser']))
		<li class="sidebar-item {{ Request::is('*actors*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/actors') }}">
                <i class="sidebar-icon fa fa-user"></i>
                <span class="sidebar-text">{{ trans('main.actors') }}</span>
            </a>
		</li>
		@endif

		@if(Helpers::hasAnyAccess(['slides.create', 'slides.edit', 'slides.delete', 'superuser']))
		<li class="sidebar-item {{ Request::is('*slider*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/slider') }}">
                <i class="sidebar-icon fa fa-photo"></i>
                <span class="sidebar-text">{{ trans('dash.slider') }}</span>
            </a>
		</li>
		@endif

		<li class="sidebar-item {{ Request::is('*groups*') ? 'active' : '' }}">
			<a href="{{ url('dashboard/groups') }}">
				<i class="sidebar-icon fa fa-users"></i>
				<span class="sidebar-text">{{ trans('main.groups') }}</span>
			</a>
		</li>

		@if(Helpers::hasAnyAccess(['users.create', 'users.edit', 'users.delete', 'superuser']))
		<li class="sidebar-item {{ Request::is('*users*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/users') }}">
                <i class="sidebar-icon fa fa-users"></i>
                <span class="sidebar-text">{{ trans('main.users') }}</span>
            </a>
		</li>
		@endif

        @if(Helpers::hasAnyAccess(['tv_networks.create', 'tv_networks.edit', 'tv_networks.delete', 'superuser']))
            <li class="sidebar-item {{ Request::is('*tvNetworks*') ? 'active' : '' }}">
                <a href="{{ url('dashboard/tvNetworks') }}">
                    <i class="sidebar-icon fa fa-users"></i>
                    <span class="sidebar-text">{{ trans('main.tvNetworks1') }}</span>
                </a>
            </li>
        @endif

        @if(Helpers::hasAnyAccess(['product_companies.create', 'product_companies.edit', 'product_companies.delete', 'superuser']))
            <li class="sidebar-item {{ Request::is('*productCompanies*') ? 'active' : '' }}">
                <a href="{{ url('dashboard/productionCompanies') }}">
                    <i class="sidebar-icon fa fa-users"></i>
                    <span class="sidebar-text">{{ trans('main.productionCompanies') }}</span>
                </a>
            </li>
        @endif

		@if(Helpers::hasAnyAccess(['settings.manage', 'superuser']))
		<li class="sidebar-item {{ Request::is('*settings*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/settings') }}">
                <i class="sidebar-icon fa fa-gears"></i>
                <span class="sidebar-text">{{ trans('dash.settings') }}</span>
            </a>
		</li>
		@endif

		@if(Helpers::hasAnyAccess(['actions.manage', 'superuser']))
		<li class="sidebar-item {{ Request::is('*actions*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/actions') }}">
                <i class="sidebar-icon fa fa-external-link"></i>
                <span class="sidebar-text">{{ trans('dash.actions') }}</span>
            </a>
		</li>
		@endif

		@if(Helpers::hasAnyAccess(['ads.manage', 'superuser']))
		<li class="sidebar-item {{ Request::is('*ads*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/ads') }}">
                <i class="sidebar-icon fa fa-dollar"></i>
                <span class="sidebar-text">{{ trans('dash.ads') }}</span>
            </a>
		</li>
		@endif

		<li class="sidebar-item {{ Request::is('*news*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/news') }}">
                <i class="sidebar-icon fa fa-bullhorn"></i>
                <span class="sidebar-text">{{ trans('main.news') }}</span>
            </a>
		</li>

		<li class="sidebar-item {{ Request::is('*reviews*') ? 'active' : '' }}">				
			<a href="{{ url('dashboard/reviews') }}">
                <i class="sidebar-icon fa fa-thumbs-up"></i>
                <span class="sidebar-text">{{ trans('main.reviews') }}</span>
            </a>
		</li>

		{{ Hooks::renderHtml('Dashboard.Sidebar') }}

	</ul>
</aside>