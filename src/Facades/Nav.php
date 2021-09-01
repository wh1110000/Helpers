<?php

namespace wh1110000\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Nav
 * @package Workhouse\Cms\Facades
 */

class Nav extends Facade {

	/**
	 * @return string
	 */

	protected static function getFacadeAccessor() {

		return 'Nav';
	}
}