<?php

namespace wh1110000\helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Row
 * @package Workhouse\Cms\Facades
 */

class Row extends Facade {

	/**
	 * @return string
	 */

	protected static function getFacadeAccessor() {

		return 'Row';
	}

	/**
	 * @return mixed
	 */

	public static function init() {

		static::clearResolvedInstance(static::getFacadeAccessor());

		return static::getFacadeRoot();
	}
}