<!DOCTYPE html>

<html>

  <head>
      <title>Origins - Updater</title>

      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
      <link href='http://fonts.googleapis.com/css?family=Ceviche+One' rel='stylesheet' type='text/css'>

      {{ HTML::style('themes/original/assets/css/styles.min.css') }}
      
  </head>

  <body id="update">

  		<div class="container">
  			<div class="col-sm-2"></div>
			
			<div class="col-sm-8" id="update-box">
				
				<h2>Welcome to MTDb Updater.</h2>

        <span class="help-block">This might take several minutes, please don't close this browser tab while schema updating is not complete.</span>

				{{ Form::open(array('route' => 'update.schema')) }}

					<button type="submit" class="btn btn-primary btn-lg">Update Database Schema and Continue</button>

				{{ Form::close() }}

			</div>

  			<div class="col-sm-2"></div>
  		</div>
    
  </body>
</html>