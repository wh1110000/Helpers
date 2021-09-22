<?php

namespace workhouse\helpers\Http\Controllers\Html;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\HtmlString;

/**
 * Class Button
 * @package workhouse\cms\Helpers
 */

class Button extends HtmlBuilder {

	/**
	 * @var string
	 */

	protected $label;

	/**
	 * @var
	 */

	protected $route;

	/**
	 * @var
	 */

	protected $icon;

	/**
	 * @var
	 */

	protected $class = [];

	/**
	 * @var
	 */

	protected $visible = true;

	/**
	 * @var array
	 */

	protected $attributes = [
		'id' => '',
		'class' => ['btn', 'mb-2']
	];

	/**
	 * @param $id
	 *
	 * @return $this
	 */

	public function id($id){

		$this->attributes['id'] = $id;

		return $this;
	}

	/**
	 * @param $class
	 *
	 * @return $this
	 */

	public function class($class){

		$class = is_array($class) ? $class : (array) $class;

		$this->attributes['class'] = array_merge($this->attributes['class'], $class);

		return $this;
	}

	/**
	 * @param $label
	 *
	 * @return $this
	 */

	public function label($label){

		$this->label = $label;

		return $this;
	}

	/**
	 * @param $icon
	 *
	 * @return $this
	 */

	public function icon($icon){

		$this->icon = $icon;

		return $this;
	}

	/**
	 * @param $route
	 *
	 * @return $this
	 */

	public function route($route){

		if(is_array($route)){

			$route = route(array_shift($route), $route);

		} else if(!filter_var($route, FILTER_VALIDATE_URL)){

			$route = $route !== '#' ? (\Route::has($route) ? route($route) : $route) : $route;
		}

		$this->route = $route;

		return $this;
	}

	/**
	 * @param $visible
	 *
	 * @return $this
	 */

	public function visible($visible){

		$this->visible = $visible;

		return $this;
	}

	/**
	 * @return $this
	 */

	public function modal(){

		$this->attributes['class'][] = 'active-modal';

		return $this;
	}

	/**
	 * @return mixed
	 */

	public function isVisible(){

		return $this->visible;
	}

	/**
	 * @return HtmlString
	 */

	public function getLabel(){

		return new HtmlString(($this->icon ? \Html::tag('i', '', ['class' => $this->icon]) : '') . ' ' . $this->label);
	}

	/**
	 * @param $attributes
	 * @param bool $clearExisting
	 *
	 * @return $this
	 */

	public function setAttributes($attributes, $clearExisting = false){

		$attributes['class'][] = 'btn';

		if(!is_array($this->attributes)){

			$this->attributes = [];
		}

		$_attributes = is_array($attributes) ? $attributes : (array) $attributes;

		if($clearExisting){

			$this->attributes = $_attributes;

		} else {

			$this->attributes = array_merge_recursive($_attributes, $this->attributes);
		}

		$this->attributes = array_filter($this->attributes);

		return $this;
	}

	/**
	 * @param bool $toHtml
	 *
	 * @return HtmlString|string
	 */

	public function render($toHtml = false){

		$html = $this->isVisible() ? $this->link($this->route, $this->getLabel()->toHtml(), $this->attributes, $secure = null, $escape = false) : '';

		$this->refresh();

		return  $toHtml && ($html instanceof HtmlString) ? $html->toHtml() : $html;
	}

	/**
	 * ===============================================================================================
	 */

	/**
	 * @param $route
	 * @param bool $visible
	 * @param bool $render
	 *
	 * @return HtmlString|string|Button
	 */

	public function add($route, $visible = true, $render = true){

		return $this->preDefinedButton($route, __('cms::general.add'), 'fas fa-plus', ['class' => ['btn-success']], $visible, $render);
	}

	/**
	 * @param $route
	 * @param bool $visible
	 * @param bool $render
	 *
	 * @return HtmlString|string|Button
	 */

	public function edit($route, $visible = true, $render = true){

		return $this->preDefinedButton($route, __('cms::general.edit'), 'fas fa-edit', ['class' => ['btn-warning']], $visible, $render);
		//return $render ? $this->render($route) : $this;
	}

	/**
	 * @param $route
	 * @param bool $visible
	 * @param bool $render
	 *
	 * @return HtmlString|string|Button
	 */

	public function show($route, $visible = true, $render = true){

		return $this->preDefinedButton($route, __('cms::general.show'), 'fas fa-eye', ['class' => ['btn-info']], $visible, $render);
	}

	/**
	 * @param $route
	 * @param bool $visible
	 * @param bool $render
	 *
	 * @return HtmlString|string|Button
	 */

	public function delete($route, $visible = true, $render = true){

		//$this->attributes['data-method'] = 'DELETE';

		return $this->preDefinedButton($route, __('cms::general.delete'), 'fas fa-trash', ['data-method' => 'DELETE', 'class' => ['btn-danger','btn-alert']], $visible, $render);
	}

	/**
	 * @param $route
	 * @param bool $visible
	 * @param bool $render
	 *
	 * @return HtmlString|string|Button
	 */

	public function download($route, $visible = true, $render = true){

		return $this->preDefinedButton($route, __('cms::general.download'), 'fas fa-download', ['class' => ['btn-warning']], $visible, $render);
	}

	/**
	 * @param $route
	 * @param bool $visible
	 * @param bool $render
	 *
	 * @return HtmlString|string|Button
	 */

	public function back($route, $visible = true, $render = true){

		return $this->preDefinedButton($route, __('cms::general.back'), 'fas fa-undo-alt', ['class' => ['btn-default']], $visible, $render);
	}

	/**
	 * @param $route
	 * @param $label
	 * @param $icon
	 * @param $attributes
	 * @param $visible
	 * @param $render
	 *
	 * @return HtmlString|string|Button
	 */

	public function preDefinedButton($route, $label, $icon, $attributes, $visible, $render){


		$this->visible($visible);

		$this->route($route);

		$this->label($label);

		$this->icon($icon);

		$this->setAttributes($attributes);

		if($render){

			if($this->isVisible()) {

				return $this->render();

			} else {


				return $this->toHtmlString('');
			}
		}

		return $this;
	}

	 /* =============================================================================================== */

	/**
	 *
	 */

	public function refresh(){

		//$default = \workhouse\helpers\Facades\Button::init();

		$this->label = null;

		$this->route = null;

		$this->icon = null;

		$this->class = [];

		$this->visible = true;

		$this->attributes = [
			'id' => '',
			'class' => ['btn', 'mb-2']
		];
	}
}
