<?php

namespace LeeOvery\Flasher;

use Illuminate\Support\Facades\Facade;

class Flasher extends Facade {

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'flasher';
    }

} 