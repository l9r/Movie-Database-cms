<!DOCTYPE html>
<html>
	<head>	
       	<title>{{ $options->getSiteName() }}</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="shortcut icon" href="{{{ asset('assets/images/favicon.ico') }}}">
        <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Bitter:700' rel='stylesheet' type='text/css'>

        {{ HTML::style('themes/original/assets/css/styles.min.css?v1') }}
        {{ Hooks::renderCss() }}

	</head>

<body id="install">

	<div class="container cont-pad-bottom" id="content">
		
		<div class="row col-xs-offset-5"><img class="img-responsive" src="{{ asset('assets/images/logo.png') }}" alt="logo"></div>
		<hr>

		<section class="row">
			<ul class="wizard list-unstyled clearfix">
				<li class="active compat">
					<a href="#compat"> <span class="step">1</span> <span class="title">Compatability</span> </a>
				</li>
				<li class="db">
					<a href="#db"> <span class="step">2</span> <span class="title">Database</span> </a>
				</li>
				<li class="config">
					<a href="#config"> <span class="step">3</span> <span class="title">Configuration</span> </a>
				</li>
				<li class="finalize">
					<a href="#finalize"> <span class="step">4</span> <span class="title">Finalize</span> </a>
				</li>
			</ul>
			<div class="clearfix"></div>
		</section>

		<script type="text/html" id="compat">
			@include('Install.Partials.Compat')
		</script>

		<script type="text/html" id="config">
			@include('Install.Partials.Config')
		</script>

		<script type="text/html" id="db">
			@include('Install.Partials.Db')
		</script>

		<script type="text/html" id="finalize">
			@include('Install.Partials.Finalize')
		</script>

		<section class="row" data-bind="template: { name: currentStep }"></section>
	</div>

	 <script>
        var vars = {
            trans: {
                working: '<?php echo trans("dash.working"); ?>',
                error:   '<?php echo trans("dash.somethingWrong"); ?>',
                movie:'<?php echo strtolower(trans("main.movies")); ?>',
                series: '<?php echo strtolower(trans("main.series")); ?>',
                news: '<?php echo strtolower(trans("main.news")); ?>',
                prev: '<?php echo trans("dash.prev"); ?>',
                next: '<?php echo trans("dash.next"); ?>',
                search: '<?php echo trans("main.search"); ?>',
                more: '<?php echo trans("main.more"); ?>',
                less: '<?php echo trans("main.less"); ?>'
            },
            urls: {
                baseUrl: '<?php echo url(); ?>'
            },
            token: '<?php echo Session::get("_token"); ?>'
        };
    </script>

    {{ HTML::script('assets/js/scripts.min.js') }}
    {{ Hooks::renderScripts() }}
	
	<script>
	app.viewModels.install.start();
	</script>

	<div id="main-loading-outter">
		<div id="main-loading-container">
			<div class="loader" id="main-spinner">
				<div class="inner one"></div>
				<div class="inner two"></div>
				<div class="inner three"></div>
			</div>
		</div>
	</div>

</body>

</html>
