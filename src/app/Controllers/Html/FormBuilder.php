<?php

namespace Workhouse\Helpers\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Workhouse\Cms\Models\Lang;

/**
 * Class FormBuilder
 * @package Workhouse\Cms\Helpers
 */

class FormBuilder extends \Collective\Html\FormBuilder {

	/**
	 * @var array
	 */

	public $tabs = [];

	/**
	 * @param null $key
	 *
	 * @return HtmlString
	 */

	public function fields($key = null){

		$rows = $this->tabsOrder($this->model->form($key));

		$html = collect();

		if($rows instanceof Tab){

			foreach ($rows->tab as $tab){

				if($tab['rows']){

					$html->put($tab['label'], $this->render($tab['rows']));
				}
			}

		} else {

			$html->push($this->render($rows));
		}

		if($this->model->hasSeo()){

			$html->put('Seo',$this->render($this->model->metaFieldsForm()));
		}

		if($html->count() > 1){

			$tab = 1;

			$content = new Collection();
			$tabIds = new Collection();

			foreach($html as $key => $render) {

				$tabId = 'tab_'.$key;

				$tabIds->put($key, $tabId);

				$content->push(\Html::tag('div', $render->implode(''), ['class' => 'tab-pane fade' . ($tab == 1 ? ' show active' : ''), 'id' => $tabId,  'role'=>"tabpanel", 'aria-labelledby'=>$tabId.'-tab'])->toHtml());

				$tab++;
			}

			$_tabs = new Collection();

			$tab = 1;

			foreach($tabIds as $label => $tabId){

				$label = preg_replace('/(?<!\ )[A-Z]/', ' $0', Str::replaceLast('Tab', '', ucfirst($label)));

				$a = \Html::tag('a', $label, ['class' => 'nav-link'. ($tab == 1 ? ' active': ''), 'id' => $tabId . '-tab', 'data-toggle' => 'tab', 'href' => '#'.$tabId, 'role' => 'tab', 'aria-controls' => 'language-settings', 'aria-selected' => "($tab == 1 ? ' true': 'false')"]);

				$_tabs->push(\Html::tag('li', $a->toHtml(), ['class' => 'nav-item'])->toHtml());

				$tab++;
			}

			$nav = \Html::tag('ul', $_tabs->implode(''), ['class' => 'nav nav-tabs', 'id' => 'myTab', 'role' => 'tablist']);

			$content = \Html::tag('div', $content->implode(''), ['class' => 'tab-content', 'id' => 'myTabContent']);

			$rendered = Str::finish($nav, $content);

		} else {

			$rendered = $html->first()->implode('');

		}

		return new HtmlString($rendered);
	}

	/**
	 * @param $rows
	 *
	 * @return mixed
	 */

	private function tabsOrder($rows){

		if($this->model->tabsOrder){

			$order  = array_map(function ($tab){

				return Str::finish($tab, 'Tab');

			}, $this->model->tabsOrder);

			$lastIndex = count($order);

			$rows->tab = $rows->tab->sortBy(function($model) use ($order, &$lastIndex){

				$search = array_search($model['label'], $order);

				$index = $search !== false ? $search : $lastIndex;

				$lastIndex++;

				return $index;
			});
		}

		return $rows;
	}

	/**
	 * @param $rows
	 *
	 * @return \Illuminate\Support\Collection
	 */

	private function render($rows){

		if(!is_array($rows)){

			$rows = [$rows];
		}

		$html = collect();

		foreach (array_filter($rows) as $row){

			$html->push('<div class="row">');

			foreach($row->getCols() as $col){

				$html->push('<div class="col-'.$col->getWidth().'">');

				if($col->hasSections()){

					foreach($col->getSections() as $section) {

						$html->push('<section>');

						if($section->getName()){

							$html->push('<h5>'.$section->getName().'</h5>');
						}

						$this->addFields($section, $html);

						$html->push('</section>');
					}

				} else {

					$this->addFields($col, $html);
				}

				$html->push('</div>');
			}

			$html->push('</div>');
		}

		return $html;
	}

	/**
	 * @param $data
	 * @param $html
	 */

	private function addFields($data, &$html){

		$fields = $data->getFields()->filter();

		if($fields->isNotEmpty()){

			$html->push($fields->implode(''));
		}
	}

	/**
	 * @param mixed $model
	 * @param array $options
	 *
	 * @return \Illuminate\Support\HtmlString
	 */

	public function model($model, array $options = []) {

		if(property_exists($model, 'uploadable') && !empty($model->uploadable)){

			$options['files'] = true;
		}

		if(request()->has('translations')){

			if($model->getNamespace()){

				$options['route'] = isset($options['route']) ? $options['route'] : [$model->getNamespace().'.save', $model];
			}

			$options['route'] = array_merge($options['route'], ['translations' => request()->get('translations')]);
		}

		if(!isset($options['method'])){

			$options['method'] = 'PUT';
		}

		return parent::model($model, $options);
	}

	/**
	 * @param bool $submitBtn
	 *
	 * @return HtmlString|string
	 */

	public function close($submitBtn = true){

		if(request()->ajax()){

			request()->session()->forget('errors');

		} elseif($submitBtn !== false){

			return $this->toHtmlString($this->submit(is_string($submitBtn) ? $submitBtn : null).'</form>');
		}

		return parent::close();
	}

	/**
	 * @param string $method
	 *
	 * @return string
	 */

	protected function getAppendage($method) {

		$appendage = parent::getAppendage($method);

		if(optional($this->model)->getPostType() == 'contact'){

			$appendage .= app('captcha')->render();
		}

		return $appendage;
	}

	/**
	 * @param null $value
	 * @param array $options
	 *
	 * @return HtmlString
	 */

	public function submit($value = null, $options = []){

		if(isset($options['class'])){

			$options['class'] = !is_array($options['class']) ? (array) $options['class'] : [];
		}

		$options['name'] = $options['name'] ?? Str::slug($value);

		$options['id'] = $this->getIdAttribute(null, $options);

		if(!$value && optional($this->model)->getPostType() == 'contact'){

			$value = __('contact::general.submit');
		}

		$options['type'] = 'submit';

		return parent::button($value ?: __('cms::general.save'), array_filter($options));
	}

	/**
	 * @param string $name
	 * @param null $value
	 *
	 * @return array|mixed|null|string
	 */

	public function getValueAttribute($name, $value = null) {

		if(is_null($value) && !Str::startsWith($name, 'settings')){

			if(!is_null($name) && $this->getModel()){

                $_name = str_replace( [ request()->get( 'translations' ) . '[', ']' ], '', $name );

                if(!Str::startsWith($_name, '_')){

                    if(Str::startsWith($_name, 'meta_')){

                        $value = optional(Lang::where('model_type', $this->getModel()->getMorphClass())->where('model_id', $this->getModel()->getId())->where('lang', request()->get( 'translations' ) )->where('field', $_name)->first())->text;

                    } else {

                        $value = $this->getModel()->{$_name} ?? '';
				    }
                }
			}
		}

		return parent::getValueAttribute($name, $value);
	}

	/**
	 * @param array|string $options
	 *
	 * @return string
	 */

	protected function getRouteAction($options) {

		if(is_array($options)){

			if(isset($options[1])){

				$object = &$options[1];

				if(is_object($object)){

					$object = $object->getLink(true);
				}
			}
		}

		return parent::getRouteAction($options);
	}
}
