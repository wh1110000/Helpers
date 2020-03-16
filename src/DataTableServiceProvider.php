<?php

namespace Workhouse\DataTable;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Workhouse\DataTable\Controllers\DataTable;
use Workhouse\DataTable\Facades\DataTable as DataTableFacade;

/**
 * Class dataTableServiceProvider
 * @package Workhouse\DataTable
 */

class DataTableServiceProvider extends ServiceProvider {

	/**
	 *
	 */

	public function register() {

		$this->app->alias('DataTable', DataTable::class);

		$this->app->singleton('DataTable', function () {

			return new DataTable;
		});

		//$loader = AliasLoader::getInstance();

		//$loader->alias('DataTable', DataTableFacade::class);
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
