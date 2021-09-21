<?php

namespace workhouse\helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Nav
 * @package workhouse\cms\Facades
 */

class Nav extends Facade {

	/**
	 * @return string
	 */

	protected static function getFacadeAccessor() {

		return 'Nav';
	}
}