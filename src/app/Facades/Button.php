<?php

namespace workhouse\helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Button
 * @package workhouse\cms\Facades
 */

class Button extends Facade {

	/**
	 * @return string
	 */

	protected static function getFacadeAccessor() {

		return 'Button';
	}

	/**
	 * @return mixed
	 */

	public static function init() {

		static::clearResolvedInstance(static::getFacadeAccessor());

		return static::getFacadeRoot();
	}
}