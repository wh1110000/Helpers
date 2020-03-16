<?php

namespace Workhouse\Helpers\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Class Section
 * @package Workhouse\Cms\Helpers
 */

class Section {

	/**
	 * @var null
	 */

	public $name;

	/**
	 * @var Collection
	 */

	protected $fields;

	/**
	 * @var bool
	 */

	public $inputGroup = false;

	/**
	 * @var bool
	 */

	public $visible = false;

	/**
	 * Section constructor.
	 *
	 * @param null $name
	 */

	public function __construct($name = null) {

		$this->name = $name;

		$this->fields = new Collection();
	}

	/**
	 * @param $name
	 */

	public function setName($name){

		$name = is_array($name) ? Arr::first($name) : $name;

		$this->name = $name;
	}

	/**
	 * @param $condition
	 * @param $field
	 *
	 * @return $this|Section
	 */

	public function addFieldWhen($condition, $field){

		if($condition){

			return $this->addField($field);
		}

		return $this;
	}

	/**
	 * @param $field
	 * @param null $label
	 *
	 * @return $this
	 */

	public function addField($field, $label = null) {

		if($this->visible){

			if(is_array($field)){

				$this->inputGroup = true;

				$field = $this->groupFields($field, $label);
			}


			if($field instanceof HtmlString){

				$field = $field->toHtml();
			}

			$this->fields->push($field);
		}

		return $this;
	}

	/**
	 * @param $fields
	 * @param $label
	 *
	 * @return string|void
	 */

	public function groupFields($fields, $label){

		$col = '';

		foreach (array_filter($fields) as $field){

			$col .= preg_replace('/<div>/', '<div class="col">', $field->toHtml(), 1);
		}

		if($col){

			$this->inputGroup = true;

			$response = \Html::tag('div', $col, ['class'=>'form-row']);

			if($label){

				$response = Str::start($response, \Html::tag('label', $label));
			}

			return $response;
		}

		return;
	}

	/**
	 * @return Collection
	 */

	public function getFields(){

		return $this->fields;
	}

	/**
	 * @return null
	 */

	public function getName(){

		return $this->name;
	}
}