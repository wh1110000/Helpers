<?php

namespace Collective\Html;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Collective\Html\FormBuilder
 */

class Form extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */

    protected static function getFacadeAccessor() {

        return 'Form';
    }
}
