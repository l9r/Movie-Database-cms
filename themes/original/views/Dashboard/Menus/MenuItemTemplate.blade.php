@if (isset($child) && $child)
	<li class="panel panel-default panel-small" style="margin-left: 20px">
@else
	<li class="panel panel-default panel-small">
@endif


	<div class="panel-heading">
		<h3 class="panel-title" data-bind="text: label().trunc(25)"></h3>

		<div class="panel-btns">
            <button title="" class="panel-btn" data-bind="click: $root.showPanelBody">
                <i class="fa fa-caret-down"></i>
            </button>

            <button class="panel-btn" data-bind="click: removeLink.bind($data, $parent)">
                <i class="fa fa-times"></i>
            </button>
        </div>
	</div>
	<div class="panel-body" style="display: none">
		<label for="label">{{ trans('dash.label') }}</label>
		<input name="label" data-bind="value: label, valueUpdate: 'keyup'" class="form-control form-thin" type="text">

		<label for="action">{{ trans('main.action') }}</label>
		<input name="action" data-bind="value: action" class="form-control form-thin" type="text">

		<label for="weight">{{ trans('dash.order') }}</label>
		<input name="weight" data-bind="value: weight, valueUpdate: 'keyup'" class="form-control form-thin" type="text">

		<div class="form-group">
			<label for="type">{{ trans('dash.type') }}</label>
			<select class="form-control" name="type" id="type" data-bind="value: type">
				<option value="link">Link</option>
				<option value="route">Route</option>
				<option value="page">Page</option>
			</select>
		</div>

		<div class="form-group">
			<label for="visibility">{{ trans('dash.visibility') }}</label>
			<select class="form-control" name="visibility" id="visibility" data-bind="value: visibility">
				<option value="everyone">{{ trans('dash.everyone') }}</option>
				<option value="admin">{{ trans('dash.admin') }}</option>
			</select>
		</div>

	</div>
</li>