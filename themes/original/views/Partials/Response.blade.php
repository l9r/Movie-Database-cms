@if(Session::has('success'))
	<div class="alert alert-success alert-dismissable">
		{{{ Session::get('success') }}}
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	</div>
@elseif (Session::has('failure'))
	<div class="alert alert-danger alert-dismissable">
		{{{ Session::get('failure') }}}
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	</div>
@elseif (Session::has('info'))
	<div class="alert alert-info alert-dismissable">
		{{{ Session::get('info') }}}
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	</div>
@endif