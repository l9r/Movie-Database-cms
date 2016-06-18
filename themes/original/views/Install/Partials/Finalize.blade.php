{{ Form::open(array('url' => 'install/finalize', 'class' => 'clearfix', 'data-bind' => 'submit: finalize')) }}
	<div class="row">
		<div class="step-panel col-sm-8 col-sm-offset-2">
			<h2>Finalization</h2>

			<div class="alert alert-danger fade in hidden" id="error">
		      	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		      	<span id="msg"></span>
		    </div>

			<p style="margin-top: 25px;">Click <strong>Finish</strong> below to finish the installation. Here are some things you should do next:</p>

			<ul>
				<li>Fetch some news from dashboard > news page, by clicking <strong>update</strong> button at the top.</li>
				<li>Create categories you would like to show on homepage from <strong>dashboard > categories</strong> page.</li>
				<li>Fetch some titles from <strong>dashboard > actions</strong> page to fill up your database.</li>
				<li>Set your site meta data like <strong>title, description and keywords</strong> from <strong>dashboard > settings</strong> page.</li>
			</ul>
			<hr>

			<div class="row">
				<button type="submit" class="btn btn-success col-sm-2 col-sm-offset-5">Finish</button>
			</div>
		</div>
	</div>
{{ Form::close() }}