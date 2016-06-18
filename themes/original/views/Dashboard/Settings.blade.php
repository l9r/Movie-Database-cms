@extends('Main.Boilerplate')

@section('bodytag')
	<body id="dashboard">
@stop

@section('content')

	<section id="dash-container">

		@include('Dashboard.Partials.Sidebar')

		<div class="content row col-sm-11 settings">

			<section class="row">
         		@include('Partials.Response')
        	</section>

        	{{ Form::open(array('url' => 'dashboard/options')) }}

	            <div class="panel">
	            	<div class="panel-heading">{{ trans('dash.meta') }}</div>
	            	<div class="panel-body">

	            		<div class="form-group">
			              {{ Form::label('logo', 'Logo URL') }}
			              {{ Form::text('logo', isset($options->options['logo']) ? $options->options['logo'] : null, array('class' => 'form-control')) }}
			              <span class="help-block">If you need to upload the logo you can do it in media manager.</span>
			              {{ $errors->first('logo', '<span class="help-block alert alert-danger">:message</span>') }}		           
			            </div> 

			            <div class="form-group">
			            	{{ Form::label('siteName', trans('dash.siteName')) }}
			                {{ Form::text('siteName', isset($options->options['siteName']) ? $options->options['siteName'] : null, array('class' => 'form-control')) }}   
			            </div>

			           <div class="form-group">
			            	{{ Form::label('metaTitle', trans('dash.metaTitle')) }}
			                {{ Form::text('metaTitle', isset($options->options['metaTitle']) ? $options->options['metaTitle'] : null, array('class' => 'form-control')) }}   
			            </div>

			            <div class="form-group">
			            	{{ Form::label('siteDescription', trans('dash.siteDescription')) }}
			                {{ Form::text('siteDescription', isset($options->options['siteDescription']) ? $options->options['siteDescription'] : null, array('class' => 'form-control')) }}   
			            </div>

			            <div class="form-group">
			            	{{ Form::label('mainSiteKeywords', trans('dash.mainSiteKeywords')) }}
			                {{ Form::text('mainSiteKeywords', isset($options->options['mainSiteKeywords']) ? $options->options['mainSiteKeywords'] : null, array('class' => 'form-control')) }}   
			            </div>

			            <div class="form-group">
			            	{{ Form::label('titlePageKeywords', trans('dash.titlePageKeywords')) }}
			                {{ Form::text('titlePageKeywords', isset($options->options['titlePageKeywords']) ? $options->options['titlePageKeywords'] : null, array('class' => 'form-control')) }}   
			            </div>

			            <div class="form-group">
			            	{{ Form::label('actorPageKeywords', trans('dash.actorPageKeywords')) }}
			                {{ Form::text('actorPageKeywords', isset($options->options['actorPageKeywords']) ? $options->options['actorPageKeywords'] : null, array('class' => 'form-control')) }}   
			            </div>
	            	</div>
	            </div>
				
				<div class="panel">
					<div class="panel-heading">{{ trans('dash.providers') }}</div>
					<div class="panel-body">
						<div class="form-group">
			            	{{ Form::label('data_provider', trans('dash.primary data provider')) }}
			                {{ Form::select('data_provider', array('tmdb' => 'themoviedb', 'imdb' => 'IMDb', 'db' => 'Local'), $options->options['data_provider'] == 'imdb' ? 'imdb' : ($options->options['data_provider'] === 'tmdb' ? 'tmdb' : 'db' ), array('class' => 'form-control')) }}
			                <span class="help-block">* {{ trans('dash.provider explanation') }}</span>
			                {{ $errors->first('data_provider', '<span class="help-block alert alert-danger">:message</span>') }}        
			            </div> 

			            <div class="form-group">
			            	{{ Form::label('search_provider', trans('dash.primary search provider')) }}
			                {{ Form::select('search_provider', array('tmdb' => 'themoviedb', 'imdb' => 'IMDb', 'db' => 'Local'), $options->options['search_provider'] == 'imdb' ? 'imdb' : ($options->options['search_provider'] === 'tmdb' ? 'tmdb' : 'db' ), array('class' => 'form-control')) }}
			                <span class="help-block">* {{ trans('dash.search provider expl') }} </span>
			                {{ $errors->first('search_provider', '<span class="help-block alert alert-danger">:message</span>') }}
			            </div>

			            <div class="form-group">
			            	{{ Form::label('news_provider', 'News Provider') }}
			               	{{ Form::select('news_provider', array('screenrant' => 'ScreenRant', 'firstshowing' => 'FirstShowing'), isset($options->options['news_provider']) ? $options->options['news_provider'] : 'firstshowing', array('class' => 'form-control')) }}
			                {{ $errors->first('news_provider', '<span class="help-block alert alert-danger">:message</span>') }}           
			            </div>        
					</div>
				</div>

				<div class="panel">
					<div class="panel-heading">{{ trans('dash.keysAndUrls') }}</div>
					<div class="panel-body">
						<div class="form-group">
			            	{{ Form::label('tmdb_api_key', trans('dash.tmdb api key')) }}
			                <input class="form-control" name="tmdb_api_key" type="password" value="{{ isset($options->options['tmdb_api_key']) ? $options->options['tmdb_api_key'] : '' }}" id="tmdb_api_key">
			                <span class="help-block">* {{ trans('dash.key explanation') }} <a href="https://www.themoviedb.org/account/signup"><strong>{{ trans('dash.here') }}</strong></a>.</span>
			                {{ $errors->first('tmdb_api_key', '<span class="help-block alert alert-danger">:message</span>') }}           
			            </div>

			            <div class="form-group">
			            	{{ Form::label('youtube_api_key', 'Youtube Api Key') }}
			                <input class="form-control" name="youtube_api_key" type="password" value="{{ isset($options->options['youtube_api_key']) ? $options->options['youtube_api_key'] : '' }}" id="youtube_api_key">
			                <span class="help-block">* Your youtube api key, required for trailers to work properly. You can register for it <a href="https://console.developers.google.com/"><strong>{{ trans('dash.here') }}</strong> </a>(Create new project -> Credentials -> Create new Key -> Browser Key).</span>
			                {{ $errors->first('youtube_api_key', '<span class="help-block alert alert-danger">:message</span>') }}           
			            </div>

			            <div class="form-group">
			              	{{ Form::label('disqus_short_name', trans('dash.short name')) }}
			                {{ Form::text('disqus_short_name', isset($options->options['disqus_short_name']) ? $options->options['disqus_short_name'] : '', array('class' => 'form-control')) }}
			                <span class="help-block">
			                  * {{ trans('dash.short name explanation') }} <a href="https://disqus.com/admin/signup/"><strong>{{ trans('dash.here') }}</strong></a>.
			                </span>
			                {{ $errors->first('disqus_short_name', '<span class="help-block alert alert-danger">:message</span>') }}                            
			            </div>

			             <div class="form-group">
			              	{{ Form::label('contact_us_email', trans('dash.contact us email')) }}	     
			                {{ Form::text('contact_us_email', isset($options->options['contact_us_email']) ? $options->options['contact_us_email'] : '', array('class' => 'form-control')) }}
			                <span class="help-block">
			                  * {{ trans('dash.contact email explanation') }}.
			                </span>   
			                {{ $errors->first('contact_us_email', '<span class="help-block alert alert-danger">:message</span>') }}	                    
			            </div>

			            <div class="form-group">
			              	{{ Form::label('fb_url', trans('dash.facebook url')) }}             
			                {{ Form::text('fb_url', isset($options->options['fb_url']) ? $options->options['fb_url'] : '', array('class' => 'form-control')) }}
			                {{ $errors->first('fb_url', '<span class="help-block alert alert-danger">:message</span>') }}	                        
			            </div>

			            <div class="form-group">
			              	{{ Form::label('google_url', 'Your google url') }}
			                {{ Form::text('google_url', isset($options->options['google_url']) ? $options->options['google_url'] : '', array('class' => 'form-control')) }}
			                {{ $errors->first('google_url', '<span class="help-block alert alert-danger">:message</span>') }}          
			            </div>

			            <div class="form-group">
			              	{{ Form::label('tw_url', 'Your twitter url') }}
			                {{ Form::text('tw_url', isset($options->options['tw_url']) ? $options->options['tw_url'] : '', array('class' => 'form-control')) }}
			                {{ $errors->first('tw_url', '<span class="help-block alert alert-danger">:message</span>') }}            
			            </div>

			             <div class="form-group">
			              	{{ Form::label('youtube_url', 'Your youtube url') }}
			                {{ Form::text('youtube_url', isset($options->options['youtube_url']) ? $options->options['youtube_url'] : '', array('class' => 'form-control')) }}
			                {{ $errors->first('youtube_url', '<span class="help-block alert alert-danger">:message</span>') }}            
			            </div>
			            
			            <div class="form-group">
			              	{{ Form::label('amazon_id', trans('dash.amazon aff id')) }}
			                {{ Form::text('amazon_id', isset($options->options['amazon_id']) ? $options->options['amazon_id'] : '', array('class' => 'form-control')) }}
			                {{ $errors->first('amazon_id', '<span class="help-block alert alert-danger">:message</span>') }}          
			            </div>
					</div>
				</div>
	           	
				<div class="panel">
					<div class="panel-heading">{{ trans('dash.options') }}</div>
					<div class="panel-body">
                        <div class="form-group">
                            {{ Form::label('theme', 'Theme') }}
                            {{ Form::select('theme', $dirs, isset($options->options['theme']) ? $options->options['theme'] : 'original', array('class' => 'form-control')) }}
                            {{ $errors->first('theme', '<span class="help-block alert alert-danger">:message</span>') }}
                        </div>

			            <div class="form-group">
		            		{{ Form::label('enable_buy_now', 'Enable buy now button?') }}
		                	{{ Form::select('enable_buy_now', array(0 => trans('dash.no'), 1 => trans('dash.yes')), isset($options->options['enable_buy_now']) ? $options->options['enable_buy_now'] : 0, array('class' => 'form-control')) }}
		                	{{ $errors->first('enable_buy_now', '<span class="help-block alert alert-danger">:message</span>') }}            
			            </div>

			            <div class="form-group">
			            	{{ Form::label('enable_news', 'Show news section on the homepage?') }}
		                	{{ Form::select('enable_news', array(0 => trans('dash.no'), 1 => trans('dash.yes')), isset($options->options['enable_news']) ? $options->options['enable_news'] : 1, array('class' => 'form-control')) }}
		                	{{ $errors->first('enable_buy_now', '<span class="help-block alert alert-danger">:message</span>') }}                
			            </div>

			            <div class="form-group">
			            	{{ Form::label('video_player', 'Which player to use for plying trailers?') }}
			                {{ Form::select('video_player', array('default' => 'Default (Youtube)', 'custom' => 'Custom (VideoJS)'), isset($options->options['video_player']) ? $options->options['video_player'] : 'default', array('class' => 'form-control')) }}
			                {{ $errors->first('video_player', '<span class="help-block alert alert-danger">:message</span>') }}
			            </div>

			            <div class="form-group">
			              	{{ Form::label('tmdb_language', trans('dash.tmdb language')) }}
			              	{{ Form::text('tmdb_language', isset($options->options['tmdb_language']) ? $options->options['tmdb_language'] : '', array('class' => 'form-control')) }}
			              	{{ $errors->first('tmdb_language', '<span class="help-block alert alert-danger">:message</span>') }}
			              	<span class="help-block"> * {{ trans('dash.tmdb lang expl') }}.</span>      
			            </div>

			            <div class="form-group">
			            	{{ Form::label('save_tmdb', trans('dash.save images locally')) }}
			                {{ Form::select('save_tmdb', array(0 => trans('dash.no'), 1 => trans('dash.yes')), isset($options->options['save_tmdb']) ? $options->options['save_tmdb'] : 0, array('class' => 'form-control')) }}
			                {{ $errors->first('save_tmdb', '<span class="help-block alert alert-danger">:message</span>') }}           
			            </div>

			            
			            <div class="form-group">
			             	{{ Form::label('uri_separator', trans('dash.uri separator')) }}
			              	{{ Form::text('uri_separator', isset($options->options['uri_separator']) ? $options->options['uri_separator'] : '', array('class' => 'form-control')) }}
			              	<span class="help-block">* {{ trans('dash.uri separator explanation') }}.</span>   
			              	{{ $errors->first('uri_separator', '<span class="help-block alert alert-danger">:message</span>') }}             
			            </div>

			          

			            <div class="form-group">
			              	{{ Form::label('uri_case', trans('dash.resource uri first letter')) }}
			                {{ Form::select('uri_case', array('uppercase' => trans('dash.uppercase'), 'lowercase' => trans('dash.lowercase')), isset($options->options['uri_case']) ? $options->options['uri_case'] : 'lowercase', array('class' => 'form-control')) }}
			                <span class="help-block">* <strong>254-Thor-The-Dark-World</strong> {{ trans('dash.or') }} <strong>254-thor-the-dark-world</strong></span>   
			                {{ $errors->first('uri_case', '<span class="help-block alert alert-danger">:message</span>') }}             
			            </div>

			           <div class="form-group">
			              	{{ Form::label('require_act', trans('dash.req user acti')) }}
			                {{ Form::select('require_act', array(1 => 'yes', 0 => 'no'), isset($options->options['require_act']) ? $options->options['require_act'] : 'no', array('class' => 'form-control')) }}
			                {{ $errors->first('require_act', '<span class="help-block alert alert-danger">:message</span>') }}          
			            </div>

			            <div class="form-group">
							<label for="genres" class="col-sm-2">Genres</label>
							{{ Form::textarea('genres', isset($options->options['genres']) ? $options->options['genres'] : '', array('class' => 'form-control', 'rows' => 2)) }}
							<i class="help-block">Enter the genres that users can filter titles on here, separate each new one with a pipe(|).</i>
						</div>
					</div>
				</div>
	          	        
	            <button type="submit" class="submit-btn btn btn-primary">{{ trans('dash.update') }}</button>

         	{{ Form::close() }}
        </div>
		</div>

			

	</section>

@stop

@section('ads')	
@stop