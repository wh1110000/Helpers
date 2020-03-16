<?php

namespace Workhouse\Helpers;

use Illuminate\Support\ServiceProvider;
use Workhouse\Helpers\Controllers\Button;
use Workhouse\Helpers\Controllers\DataTable;
use Workhouse\Helpers\Controllers\Fields;
use Workhouse\Helpers\Controllers\Row;

/**
 * Class HelpersServiceProvider
 * @package Workhouse\Helpers
 */

class HelpersServiceProvider extends ServiceProvider {

	/**
	 *
	 */

	public function register() {

		$this->registerDataTable();

		$this->registerRow();

		$this->registerFields();

		$this->registerButton();
	}

	/**
	 *
	 */

	public function registerDataTable(){

		$this->app->singleton('DataTable', function () {

			return new DataTable;
		});
	}

	/**
	 *
	 */

	public function registerRow(){

		$this->app->bind('Row', function() {

			return new Row();
		});
	}

	/**
	 *
	 */

	public function registerFields(){

		$this->app->bind('Fields', function() {

			return new Fields();
		});
	}

	/**
	 *
	 */

	public function registerButton(){

		$this->app->singleton('Button', function($app) {

			return new Button($app['url'], $app['view']);
		});
	}

	/**
	 *
	 */

	public function boot() {

		$this->loadViews();
	}

	/**
	 *
	 */

	public function loadViews(){

		$this->loadViewsFrom(__DIR__.'/resources/views/datatable', 'datatable');
	}

	/**
	 *
	 */

	public function loadPublish(){

		$this->publishes([
			__DIR__ . '/resources/views' => resource_path('views/vendor/datatable')
		], 'datatable-view');
	}
}
