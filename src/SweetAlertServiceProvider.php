<?php

namespace workhouse\helpers;

use RealRashid\SweetAlert\ToSweetAlert;

/**
 * Class HtmlServiceProvider
 * @package workhouse\helpers
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