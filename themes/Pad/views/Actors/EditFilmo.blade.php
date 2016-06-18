@extends('Main.Boilerplate')

@section('title')
	<title> {{  trans('main.editing') . ' - ' . $actor['name'] . ' - ' . trans('main.brand')}}</title>
@stop

@section('bodytag')
	<body id="edit">
@stop

@section('content')

<div class="container push-footer-wrapper">

	<div class="col-sm-12">

    	<h3 class="heading">{{ trans('main.editing filmo', array('name' => $actor['name'])) }} <i class="fa fa-pencil"></i></h3 class="heading">

    	<a href="{{ Helpers::url($actor['name'], $actor['id'], 'people') }}" class="btn btn-success mar-bot"><i class="fa fa-arrow-left"></i> Back</a>

    	<div class="row"> @include('Partials.Response') </div>
 			
 			<table class="table table-condensed col-sm-12">
				<tbody>

				<thead>
					<tr>
						<th>{{ trans('main.type') }}</th>
						<th>{{ trans('main.title') }}</th>
						<th class="known-for-th">{{ trans('main.known for') }}</th>
						<th></th>
						<th>{{ trans('main.year') }}</th>
						<th class="action-th">{{ trans('main.action') }}</th>
					</tr>
				</thead>

					@foreach ($actor['title'] as $v)

			        	<tr>
			        		<td class="col-sm-1">
			        			{{{ $v['type'] }}}
			        		</td>
			        		<td class="col-sm-5">
			        			<a href="{{ Helpers::url($v['title'], $v['id'], $v['type']) }}">{{{ $v['title'] }}}</a>
			        		</td>
			        		<td class="col-sm-2 known-for-td">

			        			{{ Form::open(array('route' => 'people.knownFor')) }}

			        				{{ Form::select('known_for', array('1' => trans('dash.yes'), '0' => trans('dash.no')), ($v['pivot']['known_for'] ? 1 : 0), array('class' => 'form-control', 'onchange' => 'this.form.submit()')) }}
								
									{{ Form::hidden('title_id', $v['id']) }}
			        				{{ Form::hidden('actor_id', $actor['id']) }}

								{{ Form::close() }}
			        		</td>
			        		<td class="col-sm-1"></td>
			        		<td class="col-sm-2">{{{ $v['release_date'] }}}</td>
			        		<td class="col-sm-1 action-td">

			        			{{ Form::open(array('route' => 'people.unlink', 'class' => 'pull-left')) }}
						            {{ Form::hidden('title', $v['id']) }}
						            {{ Form::hidden('actor', $actor['id']) }}
						            <button type ="submit" title="{{ trans('dash.delete') }}" class="btn btn-danger delete-cast-btn"><i class="fa fa-trash-o"></i></button>
						        {{ Form::close() }}

						        <a href ="{{ route( ($v['type'] == 'movie' ?  'movies' : 'series'  . '.edit', $v['id']) }}" class="btn btn-warning delete-cast-btn pull-right" title="{{ trans('main.edit') }}"><i class="fa fa-pencil"></i> </a>
						           
			        		</td>
			        	</tr>
			      		 
					@endforeach

				</tbody>
			</table>
    </div>
<div class="push"></div>
</div>

@stop

@section('ads')
@stop