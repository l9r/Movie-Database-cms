<div id="filter-bar" class="clearfix">
	<section class="row">
		<div class="col-sm-3">
			<select name="genres" class="form-control" data-bind="value: genre">
				<option value="">{{ trans('dash.genres') }}</option>
					
					@foreach ($options->getGenres() as $genre)
						<option value="{{ strtolower($genre) }}">{{ $genre }}</option>
					@endforeach
			</select>

			<ul id="selected-genres" data-bind="foreach: params.genres" class="list-unstyled list-inline">
				<li data-bind="click: $root.removeGenre"><i class="fa fa-times"></i> <span data-bind="text: app.utils.ucFirst($data)"></span></li>
			</ul>
		</div>
		<div class="col-sm-3">
			<input type="text" name="search" class="form-control" placeholder="{{ trans('dash.searchByTitle') }}" data-bind="value: params.query, valueUpdate: 'keyup'">
		</div>
		<div class="col-sm-3">
			<select name="sort" class="form-control" data-bind="value: params.order">
				<option value="">{{ trans('dash.orderBy') }}</option>
				<option value="release_dateDesc">{{ trans('dash.relDateDesc') }}</option>
				<option value="release_dateAsc">{{ trans('dash.relDateAsc') }}</option>
				<option value="mc_user_scoreDesc">{{ trans('dash.rateDesc') }}</option>
				<option value="mc_user_scoreAsc">{{ trans('dash.rateAsc') }}</option>
				<option value="mc_num_of_votesDesc">{{ trans('dash.rateNumDesc') }}</option>
				<option value="mc_num_of_votesAsc">{{ trans('dash.rateNumAsc') }}</option>
				<option value="titleAsc">{{ trans('dash.titleAsc') }}</option>
				<option value="titleDesc">{{ trans('dash.titleDesc') }}</option>
			</select>
		</div>
		<div class="col-sm-3">
			<input type="text" name="cast" class="form-control" placeholder="{{ trans('dash.haveActor') }}" data-bind="value: params.cast, valueUpdate: 'keyup'">
		</div>
	</section>

	<section class="row">
		<div class="col-sm-3">
			<input class="form-control date-picker" placeholder="{{ trans('dash.relBefore') }}" data-bind="value: params.before, picker: 'before'">
		</div>
		<div class="col-sm-3">
			<input class="form-control date-picker" placeholder="{{ trans('dash.relAfter') }}"  data-bind="value: params.after, picker: 'after'">
		</div>

		<div class="col-sm-3">
			<select name="minRating" class="form-control" data-bind="value: params.minRating">
				<option value="">{{ trans('dash.minRating') }}</option>
				@foreach(range(1, 10) as $num)
					<option value="{{ $num }}">{{ $num }}</option>
				@endforeach
			</select>
		</div>
		<div class="col-sm-3">
			<select name="maxRating" class="form-control" data-bind="value: params.maxRating">
				<option value="">{{ trans('dash.maxRating') }}</option>
				@foreach(range(1, 10) as $num)
					<option value="{{ $num }}">{{ $num }}</option>
				@endforeach
			</select>
		</div>
	</section>
</div>