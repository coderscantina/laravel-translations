<?php namespace CodersCantina\Translations\Facades;

use Illuminate\Support\Facades\Facade;

class Translation extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'translator';
    }
}
