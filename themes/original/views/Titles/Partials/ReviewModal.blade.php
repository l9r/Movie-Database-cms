<div class="modal fade" id="review-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">{{ trans('main.writeReview') .' '. $title->title }}</h3>
            </div>

            <div class="modal-body">
              
                <ul id="errors"></ul>
                
                {{ Form::open(array('url' => Str::slug(trans('main.movies')).'/'.$title->id.'/reviews', 'data-bind' => 'submit: create')) }}
               		<div id="star-rating">
               			<label for="score">{{ trans('main.yourRating') }}</label>
               			<span id="score" name="score" data-bind="raty, clickCallback: function(score, evt) {app.models.userReview.score(score)}"></span>
               		</div>

               		<div class="form-group">
                    <label for="body" class="sr-only">{{ trans('main.review') }}</label>
                    <textarea autocomplete="off" class="form-control" data-bind="value: app.models.userReview.body, charsRemaining" id="body" maxlength="2000" name="body" cols="50" rows="10"></textarea>   
                  </div>

					       <button type="submit" class="btn btn-primary">{{ trans('main.publish') }}</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>