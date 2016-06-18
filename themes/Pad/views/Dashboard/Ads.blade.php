@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard">
@stop

@section('content')

	<section id="dash-container">

		@include('Dashboard.Partials.Sidebar')

		<div class="content col-sm-11 ads">

			<section class="row">
         		@include('Partials.Response')
        	</section>

        	{{ Form::model($options->getAll(), array('url' => 'dashboard/options', 'class' => 'form-horizontal')) }}

	            <div class="form-group">
					<label for="analytics">Google Analytics</label>
					{{ Form::textarea('analytics', null, array('class' => 'form-control', 'rows' => 6)) }}
					<i class="help-block">{{ trans('dash.gooAnalytics') }}</i>
				</div>


				<div class="form-group">
					<label for="ad_footer_all">Ad slot #1</label>
					{{ Form::textarea('ad_footer_all', null, array('class' => 'form-control', 'rows' => 6)) }}
					<i class="help-block">{{ trans('dash.adFooterAll') }}</i>
				</div>

				<div class="form-group">
					<label for="ad_title_jumbo">Ad slot #2</label>
					{{ Form::textarea('ad_title_jumbo', null, array('class' => 'form-control', 'rows' => 6)) }}
					<i class="help-block">{{ trans('dash.adTitleJumbo') }}</i>
				</div>

				<div class="form-group">
					<label for="ad_home_jumbo">Ad slot #3</label>
					{{ Form::textarea('ad_home_jumbo', null, array('class' => 'form-control', 'rows' => 6)) }}
					<i class="help-block">{{ trans('dash.adHomeJumbo') }}</i>
				</div>

				<div class="form-group">
					<label for="ad_home_news">Ad slot #4</label>
					{{ Form::textarea('ad_home_news', null, array('class' => 'form-control', 'rows' => 6)) }}
					<i class="help-block">{{ trans('dash.adHomeNews') }}</i>
				</div>

				<button class="btn btn-success">{{ trans('main.submit') }}</button>

	            
         	{{ Form::close() }}
        </div>

	</section>

@stop

@section('ads')	
@stop