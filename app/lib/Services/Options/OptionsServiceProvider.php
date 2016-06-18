<?php namespace Lib\Services\Options;
 
use DB, Exception, App;
use Illuminate\Support\ServiceProvider;

 
class OptionsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //get options from db and bind them to singleton, just incase 
        //we dont have options table yet catch the error so we won't 
        //error out at boot time and can create it later
        try
        {
            $options = DB::table('options')->lists('value', 'name');
        }
        catch (Exception $e)
        {
            $options = false;
        }

        App::singleton('options', function() use($options)
        {            
            return new Options($options);
        });
      
    }
}