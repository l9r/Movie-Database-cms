 <div class="form-group">
  {{ Form::label('title', trans('main.title')) }}
  {{ Form::text('title', Input::old('title'), array('class' => 'form-control')) }}
</div>
  
<div class="form-group">
  {{ Form::label('type', trans('main.type')) }}
  {{ Form::select('type', array('movie' => 'movie', 'series' => 'series'), isset($title['type']) && $title['type'] == 'series' ? 'series' : 'movie' , array('class' => 'form-control', 'id' => 'type')) }}
</div>

<div class="form-group">
   {{ Form::label('related_to', trans('main.assign_to')) }}
   {{ Form::select('related_to_id', $relatedTo, isset($title) ? $title['related_to_id'] : $firstProdCompany, array('class' => 'form-control', 'id' => 'relatedTo')) }}
</div>

<div class="form-group">
  {{ Form::label('plot', trans('main.plot')) }}
  {{ Form::textarea('plot', Input::old('plot'), array('class' => 'form-control', 'rows' => 8)) }}
</div>

<div class="form-group">
  {{ Form::label('tagline', trans('main.tagline')) }}
  {{ Form::text('tagline', Input::old('tagline'), array('class' => 'form-control')) }}
</div>

 <div class="form-group">
  {{ Form::label('genre', trans('main.genre')) }}
  {{ Form::text('genre', Input::old('genre'), array('class' => 'form-control')) }}
  <span class="help-block">*{{ trans('main.genre expl') }}</span>
</div>

<div class="form-group">
  {{ Form::label('affiliate_link', trans('main.affiliate link')) }}
  {{ Form::text('affiliate_link', Input::old('affiliate_link'), array('class' => 'form-control')) }}
  <span class="help-block">*{{ trans('main.affil expl') }}</span>
</div>

<div class="form-group">
  {{ Form::label('poster', trans('main.poster')) }}
  {{ Form::text('poster', Input::old('poster'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('trailer', trans('main.trailer')) }}
  {{ Form::text('trailer', Input::old('trailer'), array('class' => 'form-control')) }}
  <span class="help-block">*{{ trans('main.trailer expl') }}</span>
</div>

<div class="form-group">
  {{ Form::label('imdb_id', 'imdb id') }}
  {{ Form::text('imdb_id', Input::old('imdb_id'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('tmdb_id', 'tmdb id') }}
  {{ Form::text('tmdb_id', Input::old('tmdb_id'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('imdb_rating', trans('main.imdb rating')) }}
  {{ Form::text('imdb_rating', Input::old('imdb_rating'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('mc_user_score', trans('main.metacritic rating')) }}
  {{ Form::text('mc_user_score', Input::old('mc_user_score'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('release_date', trans('main.release date')) }}
  {{ Form::text('release_date', Input::old('release_date'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('background', trans('main.background')) }}
  {{ Form::text('background', Input::old('background'), array('class' => 'form-control')) }}
  <span class="help-block">*{{ trans('main.background expl') }}</span>
</div>

<div class="form-group">
  {{ Form::label('awards', trans('main.awards')) }}
  {{ Form::text('awards', Input::old('awards'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('runtime', trans('main.runtime')) }}
  {{ Form::text('runtime', Input::old('runtime'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('budget', trans('main.budget')) }}
  {{ Form::text('budget', Input::old('budget'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('revenue', trans('main.revenue')) }}
  {{ Form::text('revenue', Input::old('revenue'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('language', trans('main.language')) }}
  {{ Form::text('language', Input::old('language'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('country', trans('main.country')) }}
  {{ Form::text('country', Input::old('country'), array('class' => 'form-control')) }}
</div>

<div class="form-group">
  {{ Form::label('allow_update', 'Auto update title if possible?') }}
  {{ Form::select('allow_update', array('1' => trans('dash.yes'), '0' => trans('dash.no')), isset($title['allow_update']) && $title['allow_update'] == '0' ? '0' : '1' , array('class' => 'form-control')) }}
  <p class="help-block">{{ trans('dash.stopAutoUpdateExpl') }}</p>
</div>


