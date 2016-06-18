<div class="modal fade" id="edit-ep-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-pencil"></i> Add/Edit an Episode</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('id' => 'edit-ep-form', 'data-bind' => 'submit: addEpisode')) }}
                    <div class="form-group">
                      {{ Form::label('title', trans('main.title')) }}
                      {{ Form::text('title', Input::old('title'), array('class' => 'form-control', 'data-bind' => 'value: app.models.episodeForm.title')) }}
                    </div>

                    <div class="form-group">
                      {{ Form::label('poster', trans('main.poster path')) }}
                      {{ Form::text('poster', Input::old('poster'), array('class' => 'form-control', 'data-bind' => 'value: app.models.episodeForm.poster')) }}                 
                    </div>

                    <div class="form-group">
                      {{ Form::label('release_date', trans('main.release date')) }}
                      {{ Form::text('release_date', Input::old('release_date'), array('class' => 'form-control', 'data-bind' => 'value: app.models.episodeForm.release_date')) }}      
                    </div>

                    <div class="form-group">
                      {{ Form::label('plot', trans('main.plot')) }}
                      {{ Form::textarea('plot', Input::old('plot'), array('class' => 'form-control', 'rows' => '7', 'data-bind' => 'value: app.models.episodeForm.plot')) }}            
                    </div>

                    <div class="form-group">
                      {{ Form::label('promo', trans('main.promo')) }}
                      {{ Form::text('promo', Input::old('promo'), array('class' => 'form-control', 'data-bind' => 'value: app.models.episodeForm.promo')) }}              
                    </div>

                    <div class="form-group">
                      {{ Form::label('episode_number', trans('main.number')) }}
                      {{ Form::text('episode_number', Input::old('episode_number'), array('class' => 'form-control', 'data-bind' => 'value: app.models.episodeForm.episode_number')) }}             
                    </div>

                    <button type="submit" class="btn btn-primary">{{ trans('dash.save') }}</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>