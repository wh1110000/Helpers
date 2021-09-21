<?php

namespace workhouse\helpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class DataTable
 * @package Workhouse\DataTable\Facades
 */

class DataTable extends Facade {

	/**
	 * @return string
	 */

	protected static function getFacadeAccessor() : string {

		return 'DataTable';
	}

	/**
	 * @return mixed
	 */

	public static function refresh() {

		static::clearResolvedInstance(static::getFacadeAccessor());

		return static::getFacadeRoot();
	}
}