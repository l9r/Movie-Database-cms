<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">

		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex1-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
    	</button>

		<a class="navbar-brand" href="{{ route('home') }}">
			<img class="brand-logo" src="{{ $options->getLogo() }}">
		</a>	
    	
      	</div>

		<div class="collapse navbar-collapse" id="navbar-ex1-collapse">

			{{-- main navigation --}}
			<ul class="nav navbar-nav main-menu">
				{{ HTML::getMenu('header') }}
		    </ul>
		    {{-- /main navigation --}}

		    <ul class="nav navbar-nav navbar-right logged-in-box">
		    	{{-- search bar --}}
				<li class="hidden-xs">
					{{ Form::open(array('url' => Str::slug(trans('main.search')), 'method' => 'GET', 'class' => 'navbar-form', 'id' => 'searchbar')) }}
					    <div class="form-group">
					               
					            <div class="input-group" id="navbar-search">
					                <input class="form-control" placeholder="{{ trans('main.search') }}..." autocomplete="off" data-bind="value: query, valueUpdate: 'keyup', hideOnBlur" name="q" type="search">
					                <span class="input-group-btn">
					                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> </button>
					                </span>
					            </div>
					        
					        <div class="autocomplete-container">

					            <div class="arrow-up"></div>
					            <section class="auto-heading">{{ trans('main.resultsFor') }} <span data-bind="text: query"></span></section>

					            <section class="suggestions" data-bind="foreach: autocompleteResults">
					                <div class="media">
					                    <a class="pull-left col-sm-2" data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[type]+'/'+id+'-'+title.replace(/\s+/g, '-').toLowerCase() }">
					                        <img class="media-object img-responsive" data-bind="attr: { src: poster, alt: title }">
					                    </a>
					                    <div class="media-body">
					                        <a data-bind="attr: { href: vars.urls.baseUrl+'/'+vars.trans[type]+'/'+id+'-'+title.replace(/\s+/g, '-').toLowerCase() }"><h6 class="media-heading" data-bind="text: title"></h6></a>
					                    </div>
					                </div>
					            </section>
					            
					        </div>

					    </div>
					{{ Form::close() }}
				</li>
				{{-- /search bar --}}

		   	 	@if( ! Sentry::check())
					<li><a href="{{ url(Str::slug(trans('main.register'))) }}">{{ trans('main.register-menu') }}</a></li>
					<li><a href="{{ url(Str::slug(trans('main.login'))) }}">{{ trans('main.login-menu') }}</a></li>
		    	@else
					<li class="dropdown simple-dropdown hidden-xs" id="logged-in-box">
		                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		                   	<img class="small-avatar" src="{{ Helpers::smallAvatar() }}" class="img-responsive">
		                    <span>{{{ Helpers::loggedInUser()->first_name ? Helpers::loggedInUser()->first_name : Helpers::loggedInUser()->username }}}</span> <b class="caret"></b>
		                </a>
		                <ul class="dropdown-menu" role="menu">
		                	@if(Helpers::hasAccess('super'))
		                    	<li><a href="{{ url('dashboard') }}">{{ trans('dash.dashboard') }}</a></li>
		                    @endif
		                    <li><a href="{{ route('users.show', Helpers::loggedInUser()->id) }}">{{ trans('users.profile') }}</a></li>
		                    <li><a href="{{ route('users.edit', Helpers::loggedInUser()->id) }}">{{ trans('dash.settings') }}</a></li>
		                    <li><a href="{{ action('SessionController@logOut') }}"> {{ trans('main.logout') }}</a></li>
		                    
		                </ul>
		            </li>

		            <li class="visible-xs"><a href="{{ route('users.show', Helpers::loggedInUser()->id) }}">{{ trans('users.profile') }}</a></li>
		            <li class="visible-xs"><a href="{{ route('users.edit', Helpers::loggedInUser()->id) }}">{{ trans('dash.settings') }}</a></li>
		            <li class="visible-xs"><a href="{{ action('SessionController@logOut') }}"> {{ trans('main.logout') }}</a></li>

		        @endif
			</ul>
	    </div>
	</div>
</nav>