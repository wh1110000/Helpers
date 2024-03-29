<?php

namespace workhouse\helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Fields
 * @package workhouse\cms\Facades
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