<div class="panel panel-default">
	<div class="panel-heading">{{ trans('dash.visibility') }}</div>
  	<div class="panel-body">
  		<div class="radio">
			<label>
	    		<input name="status" type="radio" value="public" id="radio-public">
	    		<span>{{ trans('dash.public') }}</span>
	    	</label>
		</div>
		<div class="radio">
			<label>
				<input name="status" type="radio" value="admin" id="radio-admin">
				<span>{{ trans('dash.adminOnly') }}</span>
			</label>
		</div>
    	<button class="btn btn-primary">{{ trans('main.publish') }}</button>
  	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">{{ trans('dash.slug') }}</div>
  	<div class="panel-body">
		<input type="text" name="slug" data-bind="value: app.models.page.slug, valueUpdate: 'keyup', charsRemaining" class="form-control" placeholder="Enter pages slug..." maxlength="50" autocomplete="off">
		<i class="text-muted">{{ trans('dash.slug') }}: <strong data-bind="text: app.models.page.slug"></strong></i>
  	</div> 	
</div>

<div class="panel panel-default">
	<div class="panel-heading">{{ trans('dash.uploadImage') }}</div>
  	<div class="panel-body">
  		<img src="" class="img-responsive" id="img-preview" style="display: none">
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload-media-modal">
		 {{ trans('dash.uploadImage') }}
		</button>
  	</div> 	
</div>