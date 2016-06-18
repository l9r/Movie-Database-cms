<div id="filter-row" class="row">           
    <button class="col-sm-1 btn btn-primary" data-bind="click: app.paginator.previousPage, enable: app.paginator.hasPrevious">
        <fa class="fa fa-chevron-left"></fa> {{ trans('dash.previous') }}
    </button>
    <button class="col-sm-1 btn btn-primary" data-bind="click: app.paginator.nextPage, enable: app.paginator.hasNext">
        {{ trans('dash.next') }} <fa class="fa fa-chevron-right"></fa>
    </button>
    <section class="col-sm-4 filter-dropdown">
        <select name="type" class="form-control" data-bind="value: params.type">
            <option value="">{{ trans('main.type').'...' }}</option>
            <option value="external">{{ trans('dash.external') }}</option>
            <option value="upload">{{ trans('dash.upload') }}</option>
        </select>
    </section>
    <section class="col-sm-4">
        <i class="fa fa-search"></i>
        <input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
    </section>
    <div class="col-sm-2 filter-dropdown">
        <select name="order" class="form-control" data-bind="value: params.order">
            <option value="">{{ trans('dash.sortBy').'...' }}</option>
            <option value="created_atDesc">{{ trans('dash.uploadDesc') }}</option>
            <option value="created_atAsc">{{ trans('dash.uploadAsc') }}</option>
        </select>
    </div>
</div>

<section class="col-sm-12">
    <div id="upload">
	    <section id="dropzone" class="row">
	    	<div>
                <h3>{{ trans('dash.dropToUpl') }}</h3>
                <span class="btn btn-primary fileinput-button">
                    <i class="fa fa-plus"></i>
                    <span>{{ trans('dash.orSelectFiles') }}</span>
                    <input id="fileupload" type="file" name="files[]" multiple>
                </span>      
            </div>
            <div class="checkbox pull-right"><label><input type="checkbox" value="true" data-bind="checked: keepOriginalName"> {{ trans('dash.keepOriginal') }}</label></div>
            <!-- ko if: editingTitle -->
            <i class="help-block">{{ trans('dash.titleImgUplExpl') }}</i>
            <!-- /ko -->
	    </section>
    </div>

    <div id="progress" class="progress" style="visibility: hidden">
        <div class="progress-bar progress-bar-success"></div>
    </div>
    <div id="files" class="files"></div>
    <div id="errors"></div>

    <div id="full-path" class="row"></div>

    <section data-bind="foreach: sourceItems" id="browse" class="row">	
		<figure class="col-lg-2 col-md-4 col-xs-6">
			<img class="img-responsive img-thumbnail" data-bind="attr: { src: $root.makePath(path) }, click: $root.insertIntoCKE">
			<div class="link-icon" data-bind="click: $root.showPath"><i class="fa fa-external-link"></i></div>
			<div class="delete-icon" data-bind="click: $root.deleteItem"><i class="fa fa-times"></i></div>
		</figure>	
	</section>
	
</section>