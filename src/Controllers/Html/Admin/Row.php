<?php

namespace wh1110000\Helpers\Controllers\Html\Admin;

use Illuminate\Support\Collection;

/**
 * Class Row
 * @package Workhouse\Cms\Helpers
 */

class Row {

	/**
	 * @var Collection
	 */

	protected $cols;

	/**
	 * @var
	 */

	protected $col;

	/**
	 * @var bool
	 */

	public $colVisible = true;

	/**
	 * @var
	 */

	public $tabs;

	/**
	 * Row constructor.
	 */

	public function __construct() {

		$this->cols = new Collection();
	}

	/**
	 * @param $condition
	 * @param $field
	 *
	 * @return $this
	 */

	public function addFieldWhen($condition, $field) {

		$this->col->addFieldWhen($condition, $field);

		return $this;
	}

	/**
	 * @param $field
	 * @param string $label
	 *
	 * @return $this
	 */

	public function addField($field, $label = '') {

		$this->col->addField($field, $label);

		return $this;
	}

	/**
	 * @param null $width
	 *
	 * @return $this
	 */

	public function addCol($width = null) {

		$this->col = new Col();

		$this->col->setWidth($width);

		$this->cols->push($this->col);

		return $this;
	}

	/**
	 * @param $condition
	 * @param null $width
	 *
	 * @return $this
	 */

	public function addColWhen($condition, $width = null) {

		$this->colVisible = $condition;

		$this->col = new Col();

		$this->col->setWidth($width);

		if($this->colVisible){

			$this->cols->push($this->col);
		}

		return $this;
	}

	/**
	 * @param $condition
	 * @param $name
	 *
	 * @return $this|Col
	 */

	public function addSectionWhen($condition, $name) {

		$this->addSection($name, $this->colVisible ? $condition : false);

		return $this;
	}

	/**
	 * @param null $name
	 * @param bool $condition
	 *
	 * @return $this
	 */

	public function addSection($name = null, $condition = true) {

		$this->col->addSection($name, $condition);

		return $this;
	}

	/**
	 * @return Collection
	 */

	public function getCols(){

		return $this->cols;
	}
}