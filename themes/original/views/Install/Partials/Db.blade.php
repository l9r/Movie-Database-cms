{{ Form::open(array('class' => 'step-panel clearfix', 'data-bind' => 'submit: prepareDb')) }}
	<div class="row">
		<h2>Database Configuration</h2>

		<div class="alert alert-danger fade in hidden" id="error">
	      	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	      	<span id="msg"></span>
	    </div>

		<div class="form-group">
			<label for="host">Host</label>
			<input type="text" name="host" id="host" class="form-control" value="localhost">
		</div>

		<div class="form-group">
			<label for="database">Database Name</label>
			<input type="text" name="database" id="database" class="form-control" value="movies">
		</div>

		<div class="form-group">
			<label for="username">Username</label>
			<input type="username" name="username" id="username" class="form-control" value="root">
		</div>

		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="form-control">
		</div>
	</div>

	<hr>

	<div class="row manual-fill">
		<div class="col-sm-6">
			<p>There might be some problems if <strong>app/config/database.php</strong> file is not writable. In that case you can open it up with a text editor, fill in the details manually and check the box below.</p>
			<div class="checkbox">
		      	<input type="checkbox" value="true" id="filledManually" name="filledManually"><label for="filledManually">I filled in my database details manually</label>	    	
		  	</div>
		</div>
		<div class="col-sm-2"></div>
		<button type="submit" class="btn btn-success col-sm-4">Next</button>
	</div>
{{ Form::close() }}