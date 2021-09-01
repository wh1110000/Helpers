<?php

namespace wh1110000\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Fields
 * @package Workhouse\Cms\Facades
 */

class Fields extends Facade {

	/**
	 * @return string
	 */

	protected static function getFacadeAccessor() {

		return 'Fields';
	}

	/**
	 * @return mixed
	 */

	public static function refresh() {

		static::clearResolvedInstance(static::getFacadeAccessor());

		return static::getFacadeRoot();
	}
}