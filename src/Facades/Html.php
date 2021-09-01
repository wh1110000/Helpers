<?php

namespace wh1110000\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Collective\Html\FormBuilder
 */

class Html extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */

    protected static function getFacadeAccessor() {

        return 'Html';
    }
}
