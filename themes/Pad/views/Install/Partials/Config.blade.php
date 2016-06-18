{{ Form::open(array('class' => 'clearfix', 'data-bind' => 'submit: storeBasics')) }}
	<div class="col-sm-offset-3 col-sm-6 step-panel">
		<h2>Admin Account</h2>

		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="form-control">
		</div>

		<div class="form-group">
			<label for="email">Email</label>
			<input type="text" name="email" id="email" class="form-control">
		</div>

		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="form-control">
		</div>

		<div class="form-group">
			<label for="password_confirmation">Confirm Password</label>
			<input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
		</div>

		<button type="submit" class="btn btn-success col-sm-2 pull-right">Next</button>
	</div>

{{ Form::close() }}