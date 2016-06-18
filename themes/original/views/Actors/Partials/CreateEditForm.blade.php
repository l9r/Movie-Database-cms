<div class="form-group">
  {{ Form::label('name', trans('main.name')) }}
  {{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
  {{ $errors->first('name', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('image', trans('main.image')) }}
  {{ Form::text('image', Input::old('image'), array('class' => 'form-control')) }}
  {{ $errors->first('image', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('bio', trans('main.bio')) }}
  {{ Form::textarea('bio', Input::old('bio'), array('class' => 'form-control', 'rows' => 5)) }}
  {{ $errors->first('bio', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('full_bio_link', trans('main.full bio link')) }}
  {{ Form::text('full_bio_link', Input::old('full_bio_link'), array('class' => 'form-control')) }}
  {{ $errors->first('full_bio_link', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('awards', trans('main.awards')) }}
  {{ Form::text('awards', Input::old('awards'), array('class' => 'form-control')) }}
  {{ $errors->first('awards', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('birth_date', trans('main.birth date')) }}
  {{ Form::text('birth_date', Input::old('birth_date'), array('class' => 'form-control')) }}
  {{ $errors->first('birth_date', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('birth_place', trans('main.birth place')) }}
  {{ Form::text('birth_place', Input::old('birth_place'), array('class' => 'form-control')) }}
  {{ $errors->first('birth_place', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('sex', trans('main.sex')) }}
  {{ Form::text('sex', Input::old('sex'), array('class' => 'form-control')) }}
  {{ $errors->first('sex', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('imdb_id', 'imdb id') }}
  {{ Form::text('imdb_id', Input::old('imdb_id'), array('class' => 'form-control')) }}
  {{ $errors->first('imdb_id', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
  {{ Form::label('tmdb_id', 'tmdb id') }}
  {{ Form::text('tmdb_id', Input::old('tmdb_id'), array('class' => 'form-control')) }}
  {{ $errors->first('tmdb_id', '<span class="help-block alert alert-danger">:message</span>') }}
</div>

<div class="form-group">
    {{ Form::label('allow_update', 'Auto update actors information?') }}
    {{ Form::select('allow_update', array('1' => trans('dash.yes'), '0' => trans('dash.no')), isset($actor['allow_update']) && $actor['allow_update'] == '0' ? '0' : '1' , array('class' => 'form-control')) }}
    <p class="help-block">Setting this to 'No' will prevent this actors information from being auto updated. Good if you have made any manual changes.</p>
</div>