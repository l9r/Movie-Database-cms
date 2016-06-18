<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h3>Hi, {{{ $username }}}!</h3>
 
        <p>{{{ trans('users.pass reset email was you') }}}</p>

        <p>{{{ trans('users.pass reset email was not you') }}}</p>

        <a href="{{ URL::to("reset-password/{$code}") }}">{{ URL::to("reset-password/{$code}") }}</a>
    </body>
</html>
