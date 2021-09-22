<?php

namespace workhouse\helpers\Http\Controllers\Html\Admin;

use Illuminate\Support\Collection;

/**
 * Class Tab
 * @package workhouse\cms\Helpers
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
