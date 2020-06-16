<?php

if (!function_exists('redirectBack')) {

	/**
	 * @param bool $success
	 * @param null $route
	 *
	 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
	 */

	function redirectBack($success = false, $route = null){

		$redirect = is_null($route)  ? redirect()->back() :  redirect($route);

		$redirect = $redirect->with('toast_'.($success ? 'success' : 'error'), $success ? 'Record has been saved' : 'Oops! Something went wrong...');

		//$redirect = $redirect->withInput();

		return $redirect;
	}
}

if (!function_exists('getSetting')) {

	/**
	 * @param $key
	 *
	 * @return mixed
	 */

	function getSetting($key) {

		return config('settings.'.$key);
	}
}

if (!function_exists('countDimensions')) {

	/**
	 * @param $array
	 *
	 * @return int
	 */

	function countDimensions($array) {

		if (is_array(reset($array))) {

			$return = countDimensions(reset($array)) + 1;

		} else {

			$return = 1;
		}

		return $return;
	}
}

if (! function_exists('formatSizeUnits')) {

	/**
	 * @param $bytes
	 *
	 * @return array
	 */

	function formatSizeUnits($bytes) {

		if ($bytes >= 1073741824) {

			$result = [
				'number' => number_format($bytes / 1073741824, 2),
				'unit' => 'GB'
			];

		} elseif ($bytes >= 1048576) {

			$result = [
				'number' => number_format($bytes / 1048576, 2),
				'unit' => 'MB'
			];

		} elseif ($bytes >= 1024) {
			$result = [
				'number' => number_format($bytes / 1024, 2),
				'unit' => 'KB'
			];

		} else {

			$result = [
				'number' => $bytes,
				'unit' => \Doctrine\Common\Inflector\Inflector::pluralize('byte', $bytes)
			];
		}

		return $result;
	}
}

if (! function_exists('minutes')) {

	/**
	 * @param $seconds
	 *
	 * @return string
	 */

	function minutes( $seconds ) {

		$value = $seconds;

		$dt = \Carbon\Carbon::now();

		$days = $dt->diffInDays( $dt->copy()->addSeconds( $value ) );

		$hours = $dt->diffInHours( $dt->copy()->addSeconds( $value )->subDays( $days ) );

		$minutes = $dt->diffInMinutes( $dt->copy()->addSeconds( $value )->subDays( $days )->subHours( $hours ) );

		$seconds = $dt->diffInSeconds( $dt->copy()->addSeconds( $value )->subDays( $days )->subHours( $hours )->subMinutes( $minutes ) );

		return \Carbon\CarbonInterval::days( $days )->hours( $hours )->minutes( $minutes )->seconds( $seconds )->forHumans();
	}
}

if (! function_exists('rgbToHex')) {

	/**
	 * @param $r
	 * @param $g
	 * @param $b
	 *
	 * @return string
	 */

	function rgbToHex( $r, $g, $b){

		return dechex($r) .dechex($g) .dechex($b);
	}
}

if(!function_exists('specialCharsToSpaces')){

	/**
	 * @param $string
	 *
	 * @return string
	 */

	function specialCharsToSpaces($string){

		$string = \Doctrine\Common\Inflector\Inflector::camelize($string);

		preg_match_all('/((?:^|[A-Z])[a-z]+)/', $string, $matches);

		return implode(' ', array_map('ucfirst', \Illuminate\Support\Arr::first($matches))) ;
	}
}

if(!function_exists('generateRoute')) {

	/**
	 * @return array
	 */

	function getRoutes(){

		$routeNames = [];

		foreach (\Route::getRoutes()->getRoutes() as $route) {

			$action = $route->getAction();

			if (array_key_exists('as', $action)) {

				if(!Str::startsWith($action['as'], 'admin.') && !in_array($action['as'], $routeNames)){

					$routeNames[] = $action['as'];
				}
			}
		}

		return $routeNames;
	}
}

if(!function_exists('generateRoute')){

	/**
	 * @param \Workhouse\Cms\Models\Page $page
	 *
	 * @return null|string
	 */

	function generateRoute(\Workhouse\Cms\Models\Page $page){

		$routes = getRoutes();

		$route = null;

		if($page){

			if(in_array($page->getLink(), $routes)){

				$route = route($page->getLink());

			} else if(in_array($page->getLink().'.index', $routes)){

				$route = route($page->getLink().'.index');

			} else if(in_array($page->getLink().'.show', $routes)){

				$route = route($page->getLink().'.show', $page);

			} elseif($page->getType() == 'internal') {

				$route = route('page.show', $page->getLink());

			} else {

				$route = route('page.show', $page->getLink());
			}
		}

		return $route;
	}
}

if(!function_exists('getActiveLanguages')){

	/**
	 * @param array $exclude
	 *
	 * @return mixed
	 */

	function getActiveLanguages($exclude = []){

		$style = getSetting('TRANSLATIONBY');

		if ($style == 'country'){

			$languages =  \Country::query();

		} else {

			$languages = \Language::groupBy('code');
		}

		if(!empty($exclude)){

			$languages = $languages->whereNotIn('code', $exclude);
		}

		return $languages->where('active', 1)->orderBy('priority')->orderBy('name')->get();
	}
}

if(!function_exists('getCurrentLanguage')){

	/**
	 * @return mixed
	 */

	function getCurrentLanguage(){

		return optional(model(getSetting('TRANSLATIONBY') == 'country' ? 'Country' : 'Language')->current()->first())->getCode();
	}
}

if(!function_exists('getLanguage')){

	/**
	 * @return mixed
	 */

	function getLanguage($type = 'default'){

		$model = \Language::query();

		if($type == 'current'){

			return $model->current();
		}

		return $model->getDefaultLanguage();
	}
}

if(!function_exists('isLanguage')){

	/**
	 * @param $languageCode
	 *
	 * @return bool
	 */

	function isLanguage($languageCode){

		return getLanguage('current')->getLanguageCode() == $languageCode;
	}
}

if(!function_exists('camelToSnake')) {

	/**
	 * @param $input
	 *
	 * @return string
	 */

	function camelToSnake( $input ) {

		if ( preg_match( '/[A-Z]/', $input ) === 0 ) {

			return $input;
		}

		$pattern = '/([a-z])([A-Z])/';

		$r = strtolower( preg_replace_callback($pattern, function ($a) {

			return $a[1] . "_" . strtolower( $a[2] );

		}, $input));

		return $r;
	}
}


