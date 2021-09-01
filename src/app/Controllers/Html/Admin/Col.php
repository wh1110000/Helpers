<?php

namespace wh1110000\helpers\Controllers\Html\Admin;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Class Col
 * @package Workhouse\Cms\Helpers
 */

class Col {

	/**
	 * @var Collection
	 */

	protected $fields;

	/**
	 * @var int
	 */

	protected $width;

	/**
	 * @var Collection
	 */

	protected $sections;

	/**
	 * @var
	 */

	protected $section;

	/**
	 * @var bool
	 */

	public $inputGroup = false;

	/**
	 * Col constructor.
	 *
	 * @param int $width
	 */

	public function __construct($width = 12) {

		$this->width = $width;

		$this->fields = new Collection();

		$this->sections = new Collection();
	}

	/**
	 * @param $condition
	 * @param $field
	 *
	 * @return $this|Col
	 */

	public function addFieldWhen($condition, $field){

		if($condition){

			return $this->addField($field);
		}

		return $this;
	}

	/**
	 * @param $field
	 * @param string $label
	 *
	 * @return $this
	 */

	public function addField($field, $label = '') {

		if($this->section){

			$this->section->addField($field, $label);

		} else {

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
	 * @param $condition
	 * @param $name
	 *
	 * @return $this|Col
	 */

	public function addSectionWhen($condition, $name) {

		$this->addSection($name, $condition);

		return $this;
	}

	/**
	 * @param null $name
	 * @param bool $condition
	 *
	 * @return $this
	 */

	public function addSection($name = null, $condition = true) {

		$this->section = new Section();

		$this->section->visible = $condition;

		$this->section->setName($name);

		if($this->section->visible){

			$this->sections->push($this->section);
		}

		return $this;
	}

	/**
	 * @param $width
	 */

	public function setWidth($width) {

		$this->width = $width;
	}

	/**
	 * @return int
	 */

	public function getWidth() {

		return $this->width;
	}

	/**
	 * @return Collection
	 */

	public function getSections(){

		return $this->sections;
	}

	/**
	 * @return bool
	 */

	public function hasSections(){

		return $this->sections->isNotEmpty();
	}

	/**
	 * @return Collection
	 */

	public function getFields(){

		return $this->fields;
	}
}