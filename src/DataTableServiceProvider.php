<?php

namespace Workhouse\DataTable;

use Illuminate\Support\ServiceProvider;

/**
 * Class dataTableServiceProvider
 * @package Workhouse\DataTable
 */

class DataTableServiceProvider extends ServiceProvider {

	/**
	 *
	 */

	public function register() {

		$this->app->bind('DataTable', function () {

			return new DataTable();
		});

		//$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		//$loader->alias('DataTable', \Workhouse\Cms\Facades\DataTable::class);
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

		$this->loadViewsFrom(__DIR__.'/resources/views', 'datatable');
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
