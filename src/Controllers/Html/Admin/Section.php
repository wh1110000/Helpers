<?php

namespace wh1110000\Helpers\Controllers\Html\Admin;

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

				$_field = $field;
				$field = $field->toHtml();
			}
			
		//	if($field != '<div><label for="short_description" class="">Short Description</label><textarea class="form-control" rows="3" placeholder name="short_description" cols="50" id="short_description"></textarea></div>' && $field!='<div><label for="excerpt" class="">Excerpt</label><textarea class="form-control" rows="3" placeholder name="excerpt" cols="50" id="excerpt"></textarea></div>' && $field != '<div><label for="title" class="required">Title</label><input class="form-control" placeholder name="title" type="text" value="Terms and Conditions" id="title"></div>')
	//	  dd($_field);
			
			
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