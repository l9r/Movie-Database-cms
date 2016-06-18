<?php

use Illuminate\Support\Facades\Facade;

class Hooks extends Facade {

    protected static function getFacadeAccessor() { return 'Lib\Hooks\Hooks'; }

}