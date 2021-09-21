<?php

namespace workhouse\helpers\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use workhouse\cms\Services\Menu\Admin;
use workhouse\cms\Services\Menu\Website;
use workhouse\helpers\Controllers\Button;
use workhouse\helpers\Controllers\DataTable;
use workhouse\helpers\Controllers\Fields;
use workhouse\helpers\Controllers\Row;
use workhouse\helpers\View\Components\Cookie;
use workhouse\helpers\View\Components\Modal;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformations;
use Doctrine\Inflector\Rules\Word;



/**
 * Class HelpersServiceProvider
 * @package workhouse\helpers
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

		$this->registerNav();

		$this->registerInflector();


	}

	/**
	 *
	 */

	public function boot(){

		Blade::component('modal', Modal::class);
		Blade::component('cookie', Cookie::class);

		$this->loadViews();

		/*$inflector = InflectorFactory::create()
			->withSingularRules(
                 new Ruleset(
                     new Transformations(),
                     new Patterns(),
                     new Substitutions(new Su.bstitution(new Word('media'), new Word('media')))
                 )
			)
			->withPluralRules(
				new Ruleset(
                     new Transformations(),
                     new Patterns(),
                     new Substitutions(
                         new Substitution(new Word('media'), new Word('media'))
                     )
                 )
             )
             ->build();*/


		/*Inflector::rules('singular', [

			'irregular' => array(
				'media'      => 'media',
				'medium'      => 'medium'
			)
		] );

		Inflector::rules('plural', [

			'irregular' => array(
				'media'      => 'media',
				'medium'      => 'medium'
			)
		]);*/
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

	public function registerNav(){

		$this->app->singleton('Nav', function() {

			$route = \Request()->route();

			if(!is_null($route) && (\Str::startsWith($route->getName(), 'admin.'))){

				return new Admin();

			} else {

				return new Website();
			}

		});
	}

	public function registerInflector(){

		$this->app->bind('DoctrineInflector', function() {

			return InflectorFactory::create()
			                ->withSingularRules(
				                new Ruleset(
					                new Transformations(),
					                new Patterns(),
					                new Substitutions(
						                new Substitution( new Word( 'media' ), new Word( 'Media' ) )
					                )
				                )
			                )
			                ->withPluralRules(
				                new Ruleset(
					                new Transformations(),
					                new Patterns(),
					                new Substitutions(
						                new Substitution( new Word( 'media' ), new Word( 'media' ) )
					                )
				                )
			                )
			                ->build();
			});


	}

	/**
	 *
	 */

	public function loadViews(){

		$this->loadViewsFrom(__DIR__.'/resources/views/DataTable', 'datatable');
		$this->loadViewsFrom(__DIR__.'/resources/views/Modal', 'modal');
		$this->loadViewsFrom(__DIR__.'/resources/views/Cookie', 'cookie');
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
