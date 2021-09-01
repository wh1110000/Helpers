<?php

namespace wh1110000\helpers;

use RealRashid\SweetAlert\ToSweetAlert;

/**
 * Class HtmlServiceProvider
 * @package Workhouse\Helpers
 */

class SweetAlertServiceProvider extends \RealRashid\SweetAlert\SweetAlertServiceProvider {

	/**
	 *
	 */

	public function boot() {

		parent::boot();

		$this->loadMiddlewares();
	}

	/**
	 *
	 */

	public function loadMiddlewares(){

		$this->app['router']->pushMiddlewareToGroup('web', ToSweetAlert::class);
	}

}