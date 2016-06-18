<div id="compat-check" class="step-panel">
	<p>First we will check if your server meets the requirements for <strong>MTDb</strong>, if not the installer will list all the problems so you can fix them, you can check documentation on how to do that. Click <strong>Check Now</strong> to continue.</p>

	<div class="col-sm-offset-4 clearfix">
		<button class="btn btn-primary col-sm-2" data-bind="click: checkCompat, disable: working">Check Now</button>
		<button class="btn btn-success col-sm-2" data-bind="click: nextStep, enable: enableNext">Next</button>
	</div>

	<h4 class="hidden" id="problem">Seems like there are some problems with your server, to continue you'll have to fix them. Consult the table below for more details.</h4>

	<!-- ko if: compatResults -->
	<table class="table table-striped">
    	<thead>
        	<tr>
          		<th>#</th>
          		<th>Type</th>
          		<th>Name</th>
          		<th>Should be</th>
          		<th>Is</th>
        	</tr>
      	</thead>
     	<tbody>
     		<!-- ko foreach: compatResults().extensions -->
        	<tr>
          		<td data-bind="text: $index() + 1"></td>
          		<td>Extension</td>
          		<td data-bind="text: name"></td>
          		<td><span data-bind="text: expected ? 'Enabled' : 'Disabled', attr: { class: expected ? 'success' : 'success' }"></span></td>
          		<td><span data-bind="text: actual ? 'Enabled' : 'Disabled', attr: { class: actual ? 'success' : 'success' }"></span></td>
        	</tr>
        	<!-- /ko -->

        	<!-- ko foreach: compatResults().folders -->
        	<tr>
          		<td data-bind="text: $index() + 8"></td>
          		<td>Direcotry</td>
          		<td data-bind="text: path"></td>
          		<td><span class="success">Writable</span></td>
          		<td><span data-bind="text: writable ? 'Writable' : 'Not-Writable', attr: { class: writable ? 'success' : 'danger' }"></span></td>
          		
        	</tr>
        	 <!-- /ko -->
      	</tbody>
    </table>
    <!-- /ko -->
</div>  