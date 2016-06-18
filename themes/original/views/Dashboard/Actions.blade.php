@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard" class="actions">
@stop

@section('content')

	<section id="dash-container" class="with-filter-bar">

		@include('Dashboard.Partials.Sidebar')

		<div class="content clearfix">

			<section class="row">
         		@include('Partials.Response')
        	</section>

			<section class="dash-padding">

				<div class="col-sm-12">
					@include('Dashboard.Partials.StatsBar')
				</div>

		 		<div class="scraper-container col-sm-4 {{ $options->getDataProvider() !== 'imdb' ? 'disabled' : '' }}">
			 		<div class="scraper-contents">
			 			<h4><i class="fa fa-wrench"></i> {{ trans('dash.scrape imdb') }}</h4>
				 		{{ Form::open(array('url' => 'dashboard/imdb-advanced')) }}

					    <div class="form-group">
					      {{ Form::label('from', trans('dash.from')) }}
					      {{ Form::text('from', 1980, array('class' => 'form-control')) }}
					      {{ $errors->first('from', '<span class="help-block alert alert-danger">:message</span>') }}          
					    </div>           

					    <div class="form-group">
					      {{ Form::label('to', trans('dash.to')) }}
					      {{ Form::text('to', 2015, array('class' => 'form-control')) }}
					      {{ $errors->first('to', '<span class="help-block alert alert-danger">:message</span>') }}
					    </div>

					    <div class="form-group">
					      {{ Form::label('minVotes', trans('dash.min votes')) }}
					      {{ Form::text('minVotes', 2000, array('class' => 'form-control')) }}
					      {{ $errors->first('minVotes', '<span class="help-block alert alert-danger">:message</span>') }}          
					    </div>

					    <div class="form-group">
					      {{ Form::label('minRating', trans('dash.min rating')) }}
					      {{ Form::text('minRating', 4, array('class' => 'form-control')) }}
					      {{ $errors->first('minRating', '<span class="help-block alert alert-danger">:message</span>') }}         
					    </div>

					    <div class="form-group">
					      {{ Form::label('howMuch', trans('dash.how much')) }}
					      {{ Form::text('howMuch', 1000, array('class' => 'form-control')) }}  
					      {{ $errors->first('howMuch', '<span class="help-block alert alert-danger">:message</span>') }}            
					    </div>

					    <div class="form-group">
					      {{ Form::label('offset', trans('dash.offset')) }}
					      {{ Form::text('offset', Input::old('offset'), array('class' => 'form-control', 'placeholder' => '1000...')) }}   
					      {{ $errors->first('offset', '<span class="help-block alert alert-danger">:message</span>') }}            
					    </div>

					    <button type="submit" class="btn btn-primary">{{ trans('dash.scrape') }}</button>

					    {{ Form::close() }}
			 		</div>
		 		</div>

			 	<div class="scraper-container col-sm-4 {{ $options->getDataProvider() !== 'tmdb' ? 'disabled' : '' }}">
			 		<div class="scraper-contents">
			 			<h4><i class="fa fa-wrench"></i> {{ trans('dash.scrape tmdb') }}</h4>
				 		{{ Form::open(array('url' => 'dashboard/tmdb-discover')) }}

						    <div class="form-group">
						      {{ Form::label('sort_by', trans('dash.sort by')) }}
						      {{ Form::select('sort_by', array('popularity.desc' => trans('dash.popularity'), 'vote_average.desc' => trans('dash.votes'), 'release_date.desc' => trans('dash.year')), 'popularity.desc', array('class' => 'form-control')) }}
						      {{ $errors->first('sort_by', '<span class="help-block alert alert-danger">:message</span>') }}          
						    </div>

						    <div class="form-group">
						      {{ Form::label('include_adult', trans('dash.include adult')) }}
						      {{ Form::select('include_adult', array('true' => trans('dash.yes'), 'false' => trans('dash.no')), 'false', array('class' => 'form-control')) }}
						      {{ $errors->first('include_adult', '<span class="help-block alert alert-danger">:message</span>') }}          
						    </div>

						    <div class="form-group">
						      {{ Form::label('type', trans('main.type')) }}
						      {{ Form::select('type', array('movie' => trans('main.movie'), 'tv' => trans('main.series')), 'movie', array('class' => 'form-control')) }}
						      {{ $errors->first('type', '<span class="help-block alert alert-danger">:message</span>') }}          
						    </div>

						    <div class="form-group">
						      {{ Form::label('language', trans('dash.language')) }}
						      {{ Form::text('language', '', array('class' => 'form-control')) }}
						      <span class="help-block"> * {{ trans('dash.tmdb lang expl') }}.</span>
						      {{ $errors->first('language', '<span class="help-block alert alert-danger">:message</span>') }}
						    </div>

						    <div class="form-group">
						      {{ Form::label('release_date*ite', trans('dash.from')) }}
						      {{ Form::text('release_date*ite', '1980-01-01', array('class' => 'form-control')) }}
						      {{ $errors->first('release_date*ite', '<span class="help-block alert alert-danger">:message</span>') }}
						    </div>
						     
						    <div class="form-group">
						      {{ Form::label('release_date*lte', trans('dash.to')) }}
						      {{ Form::text('release_date*lte', '2015-01-01', array('class' => 'form-control')) }}
						      {{ $errors->first('release_date*lte', '<span class="help-block alert alert-danger">:message</span>') }}
						    </div>

						    <div class="form-group">
						      {{ Form::label('howMuch', trans('dash.how much')) }}
						      {{ Form::text('howMuch', 100, array('class' => 'form-control')) }}
						      {{ $errors->first('howMuch', '<span class="help-block alert alert-danger">:message</span>') }}
						    </div>

						    <div class="form-group">
						      {{ Form::label('page', trans('dash.offset')) }}
						      {{ Form::text('page', 1, array('class' => 'form-control')) }}
						      {{ $errors->first('page', '<span class="help-block alert alert-danger">:message</span>') }}
						    </div>

						    <button type="submit" class="btn btn-primary">{{ trans('dash.scrape') }}</button>

						{{ Form::close() }}
			 		</div>
			 	</div>

			 	<div class="scraper-container col-sm-4 {{ $options->getDataProvider() === 'db' ? 'disabled' : '' }}">
					<div class="scraper-contents">
						<h4>{{ trans('dash.fully scrape') }}</h4>
						{{ Form::label('amount', trans('dash.how much')) }}
				        {{ Form::open(array('route' => 'titles.scrapeFully')) }}
				        {{ Form::text('amount', 100, array('class' => 'form-control')) }}<br>
				        <button type="submit" class="btn btn-primary">{{ trans('dash.scrape') }}</button>
				        {{ Form::close() }}
					</div>
			 	</div>

			 	<div class="scraper-container col-sm-4">
					<div class="scraper-contents delete-container">
						{{ Form::open(array('url' => 'dashboard/make-site-map')) }}
			         		<button type="submit" class="btn btn-primary"><i class="fa fa-sitemap"></i> Generate Sitemap</button>      
			        	{{ Form::close() }}
						<h4>{{ trans('dash.delete data') }}</h4>
						{{ Form::open(array('url' => 'dashboard/truncate-by-year')) }}
							{{ Form::label('from', trans('dash.from')) }}
				      		{{ Form::text('from', 1988, array('class' => 'form-control')) }}

				      		<div class="form-group">
				      			{{ Form::label('to', trans('dash.to')) }}
				      			{{ Form::text('to', 1998, array('class' => 'form-control')) }}
				      		</div>

			         		<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('dash.delete by year') }}</button>      
			        	{{ Form::close() }}

						{{ Form::open(array('url' => 'dashboard/truncate-no-posters')) }}
							{{ Form::hidden('table', 'titles') }}
			         		<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('dash.delete titles no posters') }}</button>      
			        	{{ Form::close() }}
			        	{{ Form::open(array('url' => 'dashboard/truncate-no-posters')) }}
							{{ Form::hidden('table', 'actors') }}
			         		<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('dash.delete actors no images') }}</button>      
			        	{{ Form::close() }}
			        	
				        {{ Form::open(array('url' => 'dashboard/truncate')) }}
			         		<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('dash.truncate all data') }}</button>      
			        	{{ Form::close() }}
					</div>
			 	</div>

			 	<div class="scraper-container col-sm-4">
					<div class="scraper-contents">
				        <p>This will clear all the application cache. You might need to do it if you add/remove some item from the site and the change doesn't reflect automatically.</p>

				        {{ Form::open(array('url' => 'dashboard/clear-cache')) }}
				        	<button type="submit" class="btn btn-primary">{{ trans('dash.clearCache') }}</button>
				        {{ Form::close() }}
					</div>
			 	</div>
			</section>

		</div>

	</section>

@stop

@section('ads')	
@stop

@section('scripts')
	<script>


	app.paginator.start(app.viewModels.titles, '.content', 15);

	</script>
@stop