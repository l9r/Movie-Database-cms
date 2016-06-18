<!DOCTYPE html>

@section('htmltag')
    <html>
@show

    <head>
        @section('title')
            <title>{{ $options->getSiteName() }}</title>
        @show

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        @section('meta')
            <meta name="title" content="{{ $options->getMetaTitle() }}">
            <meta name="description" content="{{ $options->getSiteDescription() }}">
            <meta name="keywords" content="{{ $options->getMainSiteKeywords() }}">
            <meta property="og:url" content="{{ Request::url() }}"/>
        @show

        @section('assets')
            <link rel="shortcut icon" href="{{{ asset('assets/images/favicon.ico') }}}">
            <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
            <link href='http://fonts.googleapis.com/css?family=Bitter:700' rel='stylesheet' type='text/css'>

            {{ HTML::style('themes/original/assets/css/styles.min.css?v11') }}
            {{ Hooks::renderCss() }}
        @show
    </head>

    @section('bodytag')
        <body>
    @show

    @section('nav')
        @include('Partials.Navbar')
    @show

    @yield('content')

    @section('ads')
        @if ($ad = $options->getFooterAd())
            <div id="ad">{{ $ad }}</div>
        @endif
    @show

    @section('footer')
        <footer id="footer">
            <section id="top" class="clearfix">

                <div class="col-sm-11 col-md-8 col-sm-offset-1 col-md-offset-3 col-lg-offset-4">
                    <div class="footer-heading clearfix hidden-xs">
                            <a href="{{ route('home') }}">
                                <img src="{{ $options->getLogo() }}">
                            </a>    
                        <h2 class="col-md-9 col-sm-8"> - {{ trans('main.footerSlogan') }}</h2>
                    </div>
                    
                    <section id="index">
                        <ul class="list-inline list-unstyled">
                            <li><a href="{{ url('feed/'.Str::slug(trans('main.newAndUpcoming'))) }}">{{ trans('main.moviesFeed') }}</a></li>
                            <li><a href="{{ url('feed/'.Str::slug(trans('main.news'))) }}">{{ trans('main.newsFeed') }}</a></li>
                            <li><a href="{{ url(Str::slug(trans('main.people'))) }}">{{ trans('main.people-menu') }}</a></li>
                            <li><a href="{{ route('series.index') }}">{{ trans('main.series-menu') }}</a></li>
                            <li><a href="{{ route('movies.index') }}">{{ trans('main.movies-menu') }}</a></li>                       
                            <li><a href="{{ url(Str::slug(trans('main.news'))) }}">{{ trans('main.news-menu') }}</a></li>
                        </ul>
                    </section>
                    
                    <div class="home-social">
                        <ul class="list-unstyled list-inline social-icons">
                            @if ($yurl = $options->getYoutube())
                                <li><a href="{{ $yurl }}"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa fa-youtube fa-stack-1x fa-inverse"></i></span> </a></li>
                            @endif
                            @if ($furl = $options->getFb())
                                <li><a href="{{ $furl }}"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa fa-facebook fa-stack-1x fa-inverse"></i></span> </a></li>
                            @endif
                            @if ($turl = $options->getTw())
                                <li><a href="{{ $turl }}"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa fa-twitter fa-stack-1x fa-inverse"></i></span> </a></li>
                            @endif
                            @if ($gurl = $options->getGoogle())
                                <li><a href="{{ $gurl }}"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa fa-google-plus fa-stack-1x fa-inverse"></i></span> </a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </section>
            <section id="bottom" class="clearfix">
                <div class="col-sm-6" id="copyright">{{ trans('main.copyright') }} &#169; <span class="brand">{{ $options->getSiteName() }}</span> {{ Carbon\Carbon::now()->year }}</div>
                {{ HTML::getMenu('footer') }}
            </section>
        </footer>
    @show

    <div id="main-loading-outter">
        <div id="main-loading-container">
            <div class="loader" id="main-spinner">
                <div class="inner one"></div>
                <div class="inner two"></div>
                <div class="inner three"></div>
            </div>
        </div>
    </div>

    <script>
        var vars = {
            trans: {
                working: '<?php echo trans("dash.working"); ?>',
                error:   '<?php echo trans("dash.somethingWrong"); ?>',
                movie:   '<?php echo strtolower(trans("main.movies")); ?>',
                series: '<?php echo strtolower(trans("main.series")); ?>',
                news: '<?php echo strtolower(trans("main.news")); ?>',
                prev: '<?php echo trans("dash.prev"); ?>',
                next: '<?php echo trans("dash.next"); ?>',
                search: '<?php echo trans("main.search"); ?>',
                more: '<?php echo trans("main.more"); ?>',
                less: '<?php echo trans("main.less"); ?>',
                pages: '<?php echo strtolower(trans("dash.pages")); ?>',
                siteName: '<?php echo trans("main.brand"); ?>',
                importFail: '<?php echo trans("dash.dataImportFail"); ?>',
                importSuccess: '<?php echo trans("dash.dataImportSuccess"); ?>'
            },
            urls: {
                baseUrl: '<?php echo url(); ?>'
            },
            token: '<?php echo Session::get("_token"); ?>'
        };

        vars.urls.dashPages = vars.urls.baseUrl + '/dashboard/pages'
    </script>

    {{ HTML::script('assets/js/scripts.min.js?v11') }}
    {{ Hooks::renderScripts() }}

    <script>
        app.perm(<?php echo Helpers::hasSuperAccess() ?>);
        ko.applyBindings(app.viewModels.autocomplete, $('.navbar')[0]);
    </script>

    @yield('scripts')

    @if ($options->getAnalytics())
        {{ $options->getAnalytics() }}
    @endif

  </body>
</html>