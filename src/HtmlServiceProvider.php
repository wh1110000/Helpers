<?php

namespace workhouse\helpers;

use Illuminate\Support\ServiceProvider;
use workhouse\helpers\Controllers\FormBuilder;
use workhouse\helpers\Controllers\HtmlBuilder;

/**
 * Class HtmlServiceProvider
 * @package workhouse\helpers
 */

class HtmlServiceProvider extends ServiceProvider {

	public function register() {

		$this->registerHtmlBuilder();

		$this->registerFormBuilder();
	}

	/**
	 * Register the html builder instance.
	 */

	protected function registerHtmlBuilder(){

		$this->app->singleton('Html', function ($app) {

			return new HtmlBuilder($app['url'], $app['view']);
		});
	}

	/**
	 * Register the form builder instance.
	 */

	protected function registerFormBuilder(){

		$this->app->singleton('Form', function ($app) {

			$form = new FormBuilder($app['Html'], $app['url'], $app['view'], $app['session.store']->token(), $app['request']);

			return $form->setSessionStore($app['session.store']);
		});
	}
}