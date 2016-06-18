<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h3>{{trans('main.hello') }} {{{ $username }}}!</h3>
 
        <p>{{{ trans('users.your new password is') }}} <strong>{{{ $password }}}</strong> {{{ trans('users.you can now login') }}}</p>

        <a style="margin-top:20px" href="{{ url('login') }}">{{ trans('main.brand') . ' ' . trans('main.login page') }}</a>
    </body>
</html>
