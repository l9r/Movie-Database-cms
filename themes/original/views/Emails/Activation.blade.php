<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h3>{{ trans('main.activate email 1') . ', ' . $username }}!</h3>
 
        <p>
       	  To complete the registration please click the link below.
       	  <br>
       	  <a href="{{ URL::to("activate/{$id}/{$code}") }}">{{ URL::to("activate/{$id}/{$code}") }}</a>
        </p>
    </body>
</html>
