<?php

namespace Workhouse\DataTable\Controllers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Class DataTable
 * @package Workhouse\Cms\Helpers
 */

class DataTable {

	/**
	 * @var string
	 */

	protected $id = 'dtable';

	/**
	 * @var \Illuminate\Http\Request
	 */

	protected $request;

	/**
	 * @var bool
	 */

	protected $deferLoading = true;

	/**
	 * @var array
	 */

	protected $columns = [];

	/**
	 * @var float|int
	 */

	protected $offset;

	/**
	 * @var int|mixed
	 */

	protected $limit = 10;

	/**
	 * @var bool
	 */

	protected $indexColumn = true;

	/**
	 * @var
	 */

	protected $results;

	/**
	 * @var array
	 */

	protected $editColumns = [];

	/**
	 * @var
	 */

	protected $data;

	/**
	 * @var
	 */

	protected $route;

	/**
	 * @var
	 */

	protected $deleteRoute;

	/**
	 * @var
	 */

	protected $buttons;

	/**
	 * @var array
	 */

	protected $indexColumnData = [
		'title' => '#',
		'width' => 5,
		'class' => 'text-center',
		'orderable' => false
	];

	/**
	 * @var array
	 */

	protected $extnColumnData = [];

	/**
	 * @var bool
	 */

	protected $actionColumn = true;

	/**
	 * @var array
	 */

	protected $actionColumnData = [
		'title' => 'Action',
		'width' => 15,
		'class' => 'text-center',
		'orderable' => false
	];

	/**
	 * @var
	 */

	protected $query;

	/**
	 * @var
	 */

	protected $name;

	/**
	 * @var
	 */

	protected $orderBy;

	/**
	 * @var string
	 */

	protected $orderDir = 'desc';

	/**
	 * @var array
	 */

	protected $blackList = ['extn'];

	/**
	 * DataTable constructor.
	 */

	public function __construct() {

		$this->request = request();

		if($this->request->ajax()){

			$data['datatable_'.$this->request->header('referer')] = $this->request->all();

			session($data);

		} else {

			if(url()->current() != url()->previous()){

				session()->forget('datatable_'.url()->current());
			}
		}

		$this->request = $this->request->merge(session()->get('datatable_'.($this->request->ajax() ? $this->request->header('referer') : url()->current())) ?? []);

		$this->limit = $this->request->length > 0 ? $this->request->length : (config('constants.table_items_per_page') ?? 10);

		$this->offset = ( $this->request->start / $this->limit ) * $this->limit;
	}

	/**
	 *
	 */

	public function setIndexColumn(){

		$indexColumn['index'] = $this->indexColumnData;

		$indexColumn['extn'] = $this->extnColumnData;

		$this->columns = $indexColumn + $this->columns;
	}
	/**
	 *
	 */

	public function setId($id){

		$this->id = $id;

		return $this;
	}

	/**
	 *
	 */

	public function setDeferLoading($deferLoading){

		$this->deferLoading = $deferLoading;

		return $this;
	}

	/**
	 * @return $this
	 */

	public function disableIndexColumn(){

		$this->indexColumn = false;

		return $this;
	}

	/**
	 * @param $route
	 *
	 * @return $this
	 */

	public function setRoute($route){

		if(is_array($route)){

			$route = route(Arr::first($route), array_shift($route));

		} else if(!filter_var($route, FILTER_VALIDATE_URL)){

			$route = $route !== '#' ? route($route) : $route;
		}

		$this->route = $route;

		return $this;
	}

	/**
	 * @param $route
	 * @param array $params
	 *
	 * @return $this
	 */

	public function setDeleteRoute($route, $params = []){

		$this->deleteRoute = is_array($params) && !empty($params) ? route($route, $params) : route($route);

		return $this;
	}

	/**
	 * @return mixed
	 */

	public function getRoute(){

		return $this->route;
	}

	/**
	 * @return mixed
	 */

	public function getDeleteRoute(){

		return $this->deleteRoute;
	}

	/**
	 * @return $this
	 */

	public function disableActionColumn(){

		$this->actionColumn = false;

		return $this;
	}

	/**
	 *
	 */

	public function setActionColumn(){

		$actionColumn['action'] = $this->actionColumnData;

		$this->columns = $this->columns + $actionColumn;
	}

	/**
	 * @param $query
	 *
	 * @return $this
	 */

	public function of($query){

		$query->withCount(array_keys($query->getEagerLoads()));

		$this->query = $query;

		return $this;
	}

	/**
	 * @param array $columns
	 *
	 * @return $this
	 */

	public function setColumns($columns = []){

		$this->columns = $columns;

		return $this;
	}

	/**
	 * @param $filter
	 *
	 * @return bool
	 */

	private function checkIfRelationship($filter){

		return isset($filter['is_relationship']) && $filter['is_relationship'] == true ? true : false;
	}

	/**
	 * @param $indexValue
	 * @param $field
	 * @param array $filter
	 *
	 * @return mixed
	 */

	private function textFilter($indexValue, $field, $filter = []) {

		$value = $this->request->columns[$indexValue]['search']['value'] ?? '';

		if ( $value != '' ) {

			if(in_array($field, array_keys($this->query->getEagerLoads()))){

				if(isset($filter['searchBy']) && !empty($filter['searchBy'])){

					$this->query = $this->query->whereHas($field, function($query) use ($filter, $indexValue, $value){

						return $query->where(\DB::raw('CONCAT_WS(" ", '.implode(',', $filter['searchBy']).')'), "like", "%" . trim( $value ) . "%" );
					}) ;

				} else {

					$this->query = $this->query->has( $field, "like", "%" . trim( $value ) . "%" );
				}

			} else {

				if(isset($filter['searchBy']) && !empty($filter['searchBy'])){

					$this->query = $this->query->where( \DB::raw('CONCAT_WS(" ", '.implode(',', $filter['searchBy']).')'), "like", "%" . trim( $value ) . "%" );

				} else {

					$this->query = $this->query->where( $field, "like", "%" . trim( $value ) . "%" );
				}
			}
		}

		return $this->query;
	}

	/**
	 * @param $indexValue
	 * @param $field
	 * @param array $filter
	 *
	 * @return mixed
	 */

	private function numberFilter($indexValue, $field, $filter = []) {

		return $this->textFilter($indexValue, $field, $filter);
	}

	/**
	 * @param $indexValue
	 * @param $field
	 * @param array $filter
	 *
	 * @return mixed
	 */

	private function selectFilter($indexValue, $field, $filter = []) {

		$value = $this->request->columns[$indexValue]['search']['value'] ?? '';

		if ( $value != '' ) {

			$isRelationship = $this->checkIfRelationship($filter);

			if($isRelationship){

				if(isset($filter['searchBy']) && !empty($filter['searchBy'])){

					$this->query = $this->query->whereHas($field, function($query) use ($filter, $indexValue, $value){

						return $query->where(\DB::raw('CONCAT_WS(" ", '.implode(',', $filter['searchBy']).')'), "like", "%" . trim( $value ) . "%" );
					}) ;

				} else {

					$this->query = $this->query->has( $field, "like", $value );

				}

			} else {

				$this->query = $this->query->where( $field, "like",  $value  );
			}
		}

		return $this->query;
	}

	/**
	 * @param $indexValue
	 * @param $field
	 * @param array $filter
	 *
	 * @return mixed
	 */

	private function dateFilter($indexValue, $field, $filter = []) {

		$value = $this->request->columns[$indexValue]['search']['value'] ?? '';

		if ($value != '') {

			$isRelationship = $this->checkIfRelationship($filter);

			$dates = explode(' to ', $value);

			if($isRelationship){

				/*$this->query->whereHas($field, function($query) use ($field, $dates) {
					$query->whereBetween($field, [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
				});*/

			} else {

				$this->query->whereBetween($field, [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
			}
		}

		return $this->query;
	}

	/**
	 * @return mixed
	 */

	public function order(){

		$index = [];

		array_walk($this->columns, function(&$item, $key) use (&$index) {
			if(!isset($item['orderable']) || $item['orderable'] == true){

				$index[] = $key;

				return true;
			}
		});

		if(!$this->orderBy){

			$this->orderBy = Arr::first($index);

			if(isset($this->request->order[0]['column'])){

				$order = array_keys($this->columns)[$this->request->order[0]['column']];

				if(in_array($order, $index)){

					$this->orderBy = $order;
				}
			}
		}

		if(isset($this->columns[$this->orderBy]['column_name'])){

			if($this->columns[$this->orderBy]['column_name'] == 'count'){

				$this->orderBy = $this->orderBy.'_count';

			} else {

				$this->orderBy = $this->columns[$this->orderBy]['column_name'];
			}
		}

		if(isset($this->request->order[0]['dir']))
			$this->orderDir = $this->request->order[0]['dir'];

		if($this->orderBy){

			$this->query = $this->query->orderBy($this->orderBy, $this->orderDir);
		}

		return $this->query;
	}

	/**
	 * @param $order
	 *
	 * @return $this
	 */

	public function setOrderBy($order){

		$this->orderBy = $order;

		return $this;
	}

	/**
	 * @param $blackList
	 *
	 * @return $this
	 */

	public function blackList($blackList){

		$this->blackList = array_merge($this->blackList, (array) $blackList);

		return $this;
	}

	/**
	 * @return array
	 */

	private function generateTable(){

		$this->columns = array_diff_key($this->columns, array_flip($this->blackList));

		return $this->columns;
	}

	/**
	 * @param $name
	 * @param $content
	 * @param string $divider
	 *
	 * @return $this
	 */

	public function editColumn($name, $content, $divider = '<br />'){

		if(is_string($content)){

			switch($content){

				case 'popup';

					$content = (function($model) use ($name) {

						return \Html::image($model->getMedia($name),'', ['popup' => true])->toHtml();

					});

					break;
			}

		} elseif(is_array($content)){

			$content = (function($model) use ($name, $content, $divider) {

				$data = array_map(function($value) use ($model, $divider){

					return $model->{$value}();

				}, $content);

				$data = array_filter($data, function($row){

					return strlen(preg_replace('/\s+/', '', strip_tags($row))) > 0;

				});

				return implode($divider, $data);
			});
		}

		$this->editColumns[$name] = $content instanceof HtmlString ? $content->toHtml() : $content;;

		return $this;
	}

	/**
	 * @param $content
	 *
	 * @return $this
	 */

	public function setActionButtons($content){

		$this->editColumns['action'] = $content;

		return $this;
	}

	/**
	 * @param $key
	 * @param array $data
	 * @param $row
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed|null|string
	 */

	protected function editColumnContent($key, $data = [], $row){

		$data['key'] = $key;

		if(isset($this->editColumns[$key])){

			$value = $this->editColumns[$key];

			$value = $this->compileContent($value, $data, $row);

			$value = $value instanceof HtmlString ? $value->toHtml() : $value;

			return $value;
		}

		return null;
	}

	/**
	 * @return array
	 */

	public function generateData(){

		$data = [];

		$indexCount = $this->offset + 1;

		$relationships = array_keys($this->query->getEagerLoads());

		foreach ($this->results AS $value) {

			$columns = [];

			foreach($this->columns as $key=>$column){

				$methodName = $column['method'] ??'get'.ucfirst(Str::camel($key));

				if($key == 'index'){

					$columns[$key] = $indexCount++;

				} elseif(isset($column['isImage']) && $column['isImage'] === true){

					$columns[$key] = \Html::image($value->getMedia($key), '', ['popup' => true]);

				} elseif(array_key_exists($key, $this->editColumns)){

					$columns[$key] = $this->editColumnContent($key, [], $value);

				} elseif(in_array($key, $relationships)){

					if(isset($column['column_name'])){

						if($column['column_name'] == 'count'){

							$columns[$key] = $value->{$key.'_count'} ?? 0;
						}

					}

				}  elseif(method_exists($value, $methodName)){

					$columns[$key] = call_user_func([$value, $methodName]);

				}  elseif(isset($column['filter']['type']) && in_array($column['filter']['type'], ['date'] )){

					switch($column['filter']['type']){

						case 'date':

							$columns[$key] = $value->getDate($key);

							break;
					}

				} else {

				//	$columns[$key] = $value->{$key} ?: ($value->{$methodName} ?? (Str::contains($key, '_count') ? 0 : ''));
				}



				if(isset($columns[$key])){

				//	$columns[$key] = $columns[$key] instanceof HtmlString ? $columns[$key]->toHtml() : $columns[$key];
				}


			}


			array_push($data, $columns);
		}

		return $data;
	}

	/**
	 * @return $this
	 */

	public function make(){

		if($this->indexColumn){

			$this->setIndexColumn();
		}

		if($this->actionColumn){

			$this->setActionColumn();
		}

		$data['table'] = $this->generateTable();

		foreach((array) $this->columns as $field => $attributes){

			if(isset($attributes['filter'])){

				$indexValue = array_search($field, array_keys($this->columns));

				switch($attributes['filter']['type']){

					case 'text':

						$this->textFilter($indexValue, $field, $attributes['filter']);

						break;

					case 'select':

						$this->selectFilter($indexValue, $field, $attributes['filter']);

						break;

					case 'date':

						$this->dateFilter($indexValue, $field, $attributes['filter']);

						break;

					case 'number':

						$this->numberFilter($indexValue, $field, $attributes['filter']);

						break;

					default:

						$this->textFilter($indexValue, $field, $attributes['filter']);

						break;
				}
			}
		}

		$this->order();


		if($this->deferLoading){

			$count = $this->query->get()->count();

			$this->results = $this->query->limit($this->limit)->offset($this->offset)->get();

		} else {

			$count = 0;

			$this->results = [];
		}

		$data['data'] = $this->generateData();

		$this->data = [
			'recordsTotal' => $count,
			'recordsFiltered' => $count,
			'data' => $this->generateData(),
			'table' => $this->generateTable(),
		];

		return $this;
	}

	/**
	 * @return array
	 */

	public function getColumns(){

		return $this->columns;
	}

	/**
	 * @return mixed
	 */

	public function getData(){

		return $this->data;
	}

	/**
	 * @return mixed
	 */

	public function render(){

		return \View::make('components::datatable', [
			'data' => $this->getData(),
			'columns' => $this->getColumns(),
			'route' => $this->getRoute(),
			'deleteRoute' => $this->getDeleteRoute(),
	        'order' => [
				'column' => array_search($this->orderBy, array_keys($this->columns)),
				'direction' => strtolower($this->orderDir)
			],
			'page' => $this->request->start ?? 0,
			'pageLength' => $this->request->length ?? 10,
			'id' => $this->id,
			'deferLoading' => $this->deferLoading,
		]);
	}

	/*
	 *
	 */

	public function response(){

		return request()->ajax() ? response()->json($this->getData()) : $this;
	}

	/**
	 * @param $content
	 * @param array $data
	 * @param $param
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|null|string
	 */

	public function compileContent($content, array $data, $param) {

		if (is_string($content)) {

			return $this->compileBlade($content, $this->getMixedValue($data, $param));

		} elseif (is_callable($content)) {

			$value = $content($param);

			if(is_array($value)){

				$value = implode(' ', $value);

			}

			return $value ?? null;
		}

		return $content;
	}

	/**
	 * @param $str
	 * @param array $data
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
	 */

	public function compileBlade($str, $data = []) {

		if (view()->exists($str)) {

			return view($str, $data);
		}

		ob_start() && extract($data, EXTR_SKIP);
		eval('?>' . app('blade.compiler')->compileString($str));
		$str = ob_get_contents();
		ob_end_clean();

		return $str;
	}

	/**
	 * @param array $data
	 * @param $param
	 *
	 * @return array
	 */

	public function getMixedValue(array $data, $param) {

		$casted = $this->castToArray($param);

		$data['model'] = $param;

		foreach ($data as $key => $value) {

			if (isset($casted[$key])) {

				$data[$key] = $casted[$key];
			}
		}

		return $data;
	}

	/**
	 * @param $param
	 *
	 * @return array|\stdClass
	 */

	private function castToArray($param) {

		if ($param instanceof \stdClass) {

			$param = (array) $param;

			return $param;
		}

		if ($param instanceof Arrayable) {

			return $param->toArray();
		}

		return $param;
	}
}