<?php

namespace Workhouse\Helpers\Controllers;

use Illuminate\Support\Collection;

/**
 * Class Tab
 * @package Workhouse\Cms\Helpers
 */

class Tab {

	/**
	 * @var
	 */

	public $tab;

	/**
	 * Tab constructor.
	 */

	public function __construct() {

		$this->tab = new Collection();
	}

	/**
	 * @param $label
	 * @param $rows
	 */

	public function addTab($label, $rows){

		$tab['label'] = $label;

		$tab['rows'] = $rows;

		$this->tab->push($tab);
	}

}