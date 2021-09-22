<?php

namespace workhouse\helpers\Http\Controllers\Html;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use workhouse\cms\Models\Presenters\Media;

/**
 * Class Fields
 * @package workhouse\cms\Helpers
 */

class Fields {

	/**
	 * @var
	 */

	protected $field;

	/**
	 * @var null
	 */

	protected $label = null;

	/**
	 * @var bool
	 */

	protected $isRequired = false;

	/**
	 * @var bool
	 */

	protected $placeholder = true;

	/**
	 * @var null
	 */

	protected $name = null;

	/**
	 * @var null
	 */

	protected $orgName = null;

	/**
	 * @var array
	 */

	protected $extras = [];

	/**
	 * @var null
	 */

	protected $value;

	/**
	 * @var null
	 */

	protected $values = null;

	/**
	 * @var null
	 */

	protected $file = null;

	/**
	 * @var null
	 */

	protected $selected = [];

	/**
	 * @var null
	 */

	protected $valuePrepend = null;

	/**
	 * @var null
	 */

	protected $valueAppend = null;

	/**
	 * @var array
	 */

	protected $attributes = [
		'class' => []
	];

	/**
	 * @var bool
	 */

	protected $disabled = false;

	/**
	 * @var bool
	 */

	protected $readonly = false;

	/**
	 * @var bool
	 */

	protected $multiselect = false;

	/**
	 * @var bool
	 */

	protected $translatable = false;

	/**
	 * @var bool
	 */

	protected $plainText = false;

	/**
	 * @var bool
	 */

	protected $isBool = false;

	/**
	 * @var bool
	 */

	protected $trueFalseValue = false;

	/**
	 * @param $type
	 * @param $name
	 * @param null $label
	 *
	 * @return $this
	 */

	public function field($type, $name, $label = null, $extras = []){

		$this->field = $type;

		$this->name($name);

		if($label){

			$this->label($label);
		}

		$this->extras = $extras;

		return $this;
	}

	/**
	 * @return $this
	 */

	public function text($name, $label = null){

		return $this->field('text', $name, $label);
	}

	/**
	 * @param $name
	 * @param array $fields
	 *
	 * @return string
	 */

	public function repeater($name, $fields = []){

		$iteration = 0;

		//dd(\Form::getModel()->getProperty($name)->toArray());
		//$properties = model('Property')->where('name', )

		foreach(\Form::getModel()->getProperty($name) as $group){
			foreach($group->fields as $field){

				//dd($fields[$iteration]);

				/*$fieldGroups[] = array_map(function ($field) use ($name, $iteration) {

					$field = preg_replace_callback('/name="([^"]+)/', function ($matches) use ($name, &$iteration) {


						if(isset($matches[1])){

							return str_replace($matches[1], $name.'['.$iteration.']['.$matches[1].']', strtolower($matches[0]));
						}

					}, $field->toHtml());

					return \Html::tag('div', $field, ['class' => 'mt-repeater-input']);

				}, $properties);*/

				$iteration++;
			}
		}


		return $this->repeterTemplate($name, $fields);
	}

	/**
	 * @param $name
	 * @param $fields
	 *
	 * @return string
	 */

	private function repeterTemplate($name, $fields){

		return	'
			<div class="mt-repeater">
				<div data-repeater-list="'.$name.'">
					<div data-repeater-item="" class="mt-repeater-item" style="">'.implode('', $fields).'
						<div class="mt-repeater-input">
                			<a href="javascript:;" data-repeater-delete="" class="btn btn-danger mt-repeater-delete">
                    			<i class="fa fa-close"></i> Delete
                    		</a>
            			</div>
            		</div>
            	</div>
            	<a href="javascript:;" data-repeater-create="" class="btn btn-success mt-repeater-add">
                	<i class="fa fa-plus"></i> Add
                </a>
			</div>';
	}

	/**
	 * @return $this
	 */

	public function hidden($name, $label = null){

		return $this->field('hidden', $name, $label);
	}

	/**
	 * @return $this
	 */

	public function checkbox($name, $label = null, $text = null){

		$this->attributes['class'][] = 'custom-control-input';

		return $this->field('checkbox', $name, $label, ['text' => $text]);
	}

	/**
	 * @return $this
	 */

	public function iconPicker($name, $label = null, $type = 'brands'){

		$extras = ['type' => $type];

		$this->attributes['class'][] = 'icon-picker';

		return $this->field('iconPicker', $name, $label, $extras);
	}
	/**
	 * @return $this
	 */

	public function datePicker($name, $label = null){


		$this->attributes['class'][] = 'singleDatePicker';

		return $this->field('text', $name, $label);
	}

	/**
	 * @param $name
	 * @param null $label
	 *
	 * @return Fields
	 */

	public function editor($name, $label = null){

		return $this->textarea($name, $label, true);
	}

	/**
	 * @param bool $editor
	 *
	 * @return $this
	 */

	public function textarea($name, $label = null, $editor = false){

		if($editor){

			$this->attributes(['class' => ['wysiwyg-editor']]);
		}

		$this->attributes[ 'rows' ] = 3;

		return $this->field('textarea', $name, $label);
	}

	/**
	 * @param $name
	 * @param null $label
	 *
	 * @return Fields
	 */

	public function multiselect($name, $label = null){

		$name = Str::finish($name, '[]');

		return $this->select($name, $label, true);
	}

	/**
	 * @param $name
	 * @param null $label
	 * @param bool $multiselect
	 *
	 * @return $this
	 */

	public function select($name, $label = null, $multiselect = false){

		$this->placeholder = false;

		if($multiselect){

			$this->attributes(['multiple' => 'multiple']);
		}

		return $this->field('select', $name, $label);
	}

	/**
	 * @param $name
	 * @param bool $trueFalse
	 *
	 * @return $this
	 */

	public function bool($name, $label = null, $trueFalse = false){

		$this->trueFalseValue = $trueFalse;

		$this->isBool = true;

		$this->values = collect(['0' =>  $this->trueFalseValue ? 'False' : 'No',  '1' => $this->trueFalseValue ? 'True' : 'Yes']);

		return $this->select($name, $label);
	}

	/**
	 * @return $this
	 */

	public function asText(){

		$this->plainText = true;

		return $this;
	}

	/**
	 * @return $this
	 */

	public function password($name, $label = null){

		return $this->field('password', $name, $label);
	}

	/**
	 * @return $this
	 */

	public function email($name, $label = null){

		return $this->field('email', $name, $label);
	}

	/**
	 * @return $this
	 */

	public function number($name, $label = null){

		return $this->field('number', $name, $label);
	}

	/**
	 * @return $this
	 */

	public function phone($name, $label = null){

		return $this->field('tel', $name, $label);
	}

	/**
	 * @return $this
	 */

	public function file($name, $label = null){

		if(class_basename(\Form::getModel()) == 'Setting'){

			preg_match('#\[(.*?)\]#', $name, $match);

			$fileName = $match[1] ?? $name;

			$file = \Form::getModel()->value($fileName);

			if($file){

				$file = \Media::where('id', is_object($file) ? $file->getId() : $file)->first();

			} else {

				$file = null;
			}

		} else if(in_array($name, \Form::getModel()->getFillable())){


			$file = \Form::getModel()->getMedia($name);

		} else {

			$file = \Form::getModel()->getProperty($name);
		}

		$this->file  = $file;

		$this->value(optional($file)->getId());

		return $this->field('file', $name, $label);
	}

	/**
	 * @param bool $readonly
	 *
	 * @return $this
	 */

	public function readonly($readonly = true){

		$this->readonly = $readonly;

		return $this;
	}

	/**
	 * @param bool $disabled
	 *
	 * @return $this
	 */

	public function disabled($disabled = true){

		$this->disabled = $disabled;

		return $this;
	}

	/**
	 * @param array $selected
	 *
	 * @return $this
	 */

	public function selected($selected = []){

		$this->selected = $selected;

		return $this;
	}

	/**
	 * @param array $values
	 *
	 * @return $this
	 */

	public function values($values = []){

		$this->values = collect($values);

		return $this;
	}

	/**
	 * @param $value
	 * @param null $prepend
	 * @param null $append
	 *
	 * @return $this
	 */

	public function value($value, $prepend = null, $append = null){

		$this->value = $value;

		if($prepend){

			$this->valuePrepend = $prepend;
		}

		if($append){

			$this->valueAppend = $append;
		}

		return $this;
	}

	/**
	 * @param $label
	 *
	 * @return $this
	 */

	public function label($label){

		if($label == false){

			$this->label = $label;

		} else {

			if(request()->has('translations')){

				$label = Str::replaceFirst(']', '', Str::after($label, request()->get('translations').'['));
			}

			$this->label = ucwords(preg_replace('/[^A-Za-z0-9\-]/', ' ', $label));
		}

		return $this;
	}

	/**
	 * @param $label
	 *
	 * @return $this
	 */

	public function placeholder($label, $customPlaceholder = true){

		if($label === false){

			$this->placeholder = false;

		} else {

			if(request()->has('translations')){

				$label = Str::replaceFirst(']', '', Str::after($label, request()->get('translations').'['));
			}

			$this->placeholder = $label;

			if(!$customPlaceholder){

				$this->placeholder = Str::title(str_replace('_', ' ', $this->placeholder));
			}
		}

		return $this;
	}

	/**
	 * @param $name
	 *
	 * @return $this
	 */

	public function name($name){

		$this->orgName = $name;

		if(request()->has('translations')){

			$name = request()->get('translations').'[' . $this->orgName . ']';
		}

		$this->name = $name;

		if($name){

			$this->isRequired();
		}

		return $this;
	}

	/**
	 * @param $attributes
	 *
	 * @return $this
	 */

	public function attributes($attributes){

		$this->attributes = array_merge_recursive($this->attributes, $attributes);

		return $this;
	}

	/**
	 *
	 */

	public function isRequired(){

		if($model = \Form::getModel()){

			$model = get_class($model);

			if(Str::startsWith($model, 'App\Models')){

				$model = get_parent_class($model);
			}

			$model = Str::finish(Str::replaceFirst(Str::contains($model, '\Models\Presenters\\') ? 'Presenters' : 'Models', 'Requests', $model), 'Request');

			if(class_exists($model)){

				$model = new $model;

				$model = $model->getRules();

				if(isset($model[$this->name])){

					foreach($model[$this->name] as $rule){

						if(Str::contains($rule, 'required')){

							$this->isRequired = true;

							return;
						}
					}
				}
			}
		}
	}

	/**
	 * @param null $name
	 * @param array $attributes
	 *
	 * @return HtmlString
	 */

	public function add($name = null, $attributes = []){


		if(Str::startsWith(\Route::currentRouteName(), 'admin.')){

			$attributes['class'][] = 'form-control'; //TMP
		}

		\Fields::refresh();

		if($name){

			$this->name($name);
		}

		if(\Form::getModel()){

			if(\Form::getModel()->hasMultilanguage()){

				if(request()->has('translations')){

					if(!Str::startsWith($this->orgName, ['meta_']) && !in_array($this->orgName, optional(\Form::getModel())->getTranslatableFields() ?? []) ){

						return;
					}
				}
			}
		}

		//if(!$this->label && $this->placeholder === true){
		if( !$this->label && $this->placeholder !== false && empty ($this->placeholder ) ){

			$this->placeholder($this->name, false);
		}

		if(!$this->label){

			if($this->label !== false){

				$this->label($this->name);
			}
		}

		$errors = request()->session()->get('errors') ?: new ViewErrorBag();

		$hasError = $errors->has($this->name);

		if($hasError){

			$this->attributes(['class'  => ['is-invalid']]);
		}

		$this->attributes($attributes);

		if ( $this->disabled ) {

			$this->attributes['disabled'] = 'disabled';

		} elseif ( $this->readonly ) {

			$this->attributes['readonly'] = 'readonly';
		}

		if($this->placeholder){
			$this->attributes['placeholder'] = $this->placeholder;
		}

		$values = !is_array($this->value) ? [[$this->value, $this->valuePrepend, $this->valueAppend]] : $this->value;

		$content = $response = [];

		if($this->field !== 'hidden'){

			if($this->label !== false){

				array_push($content, \Form::label($this->name, $this->label, [
					'class' => [$this->isRequired ? 'required' : '']
				]));
			}
		}

		foreach($values as $value){

			if(!is_array($value)){

				$value = $values;
			}

			if($value[0]){

				$this->value($value[0] ?? '', $value[1] ?? null, $value[2] ?? null);
			}

			if($this->valuePrepend || $this->valueAppend){

				array_push($content, '<div class="input-group">');
			}

			if($this->valuePrepend){

				array_push($content, '<div class="input-group-prepend"><span class="input-group-text">'.$this->valuePrepend.'</span></div>');
			}

			if($this->disabled) {

				$this->value($this->value ?? optional(\Form::getModel())->{$this->name});
			}

			if($this->plainText){

				$_value = \Form::getValueAttribute($this->name) ?? '---';

				$field = $this->isBool ? ($this->trueFalseValue ? $_value : ($_value ? 'Yes' : 'No')  ) : $_value;

			} else {

				if(in_array($this->name, ['meta_title', 'meta_description'])){

					if(\Form::getModel()){

						$this->value = optional(\Form::getModel()->seo)->{$this->name};
					}

				} elseif(\Form::getModel() && !in_array($this->name, \Form::getModel()->getFillable()) && !in_array($this->name, ['link'])){

					if(method_exists(!\Form::getModel(), 'properties')&& !\Form::getModel()->properties->isEmpty()){

						$property = \Form::getModel()->properties->where('property', $this->name)->first();

						$this->value = optional($property)->value;
					}
				}

				switch ( $this->field ) {

					case 'select' :

						//unset($this->attributes['placeholder']);

						$field = \Form::{$this->field}( $this->name, $this->values, $this->selected, $this->attributes );

						break;

					case 'file' :

						if($this->file){

							$file = optional($this->file)->getFilePlaceholder();

						} else {

							$file = \Html::placeholder();
						}

						$field = $this->fileFieldTemplate($file);

						break;

					case 'password' :

						$field = \Form::{$this->field}( $this->name, $this->attributes );

						break;


					case 'iconPicker' :

						/*$field = '<div class="input-group mb-3">'.(\Form::text($this->name, $this->value, $this->attributes)).'<div class="input-group-append">
			                <a href="'.route('admin.general.modal.iconpicker').'" class="active-modal btn btn-outline-secondary" type="button">Pick an Icon</a>
			            </div></div>';*/

						$field = '<div id="target"></div>'.(\Form::hidden($this->name, $this->value));

						break;

					case 'checkbox':

						$checkbox = \Form::{$this->field}( $this->name, $this->value, $this->selected, $this->attributes );

						$label =  \Form::label($this->name, $this->extras['text'] ?? '', ['class' => 'custom-control-label']);

						$field = \Html::tag('div', $checkbox.$label, ['class' => 'custom-control custom-checkbox']);

						break;

					case 'repeater':

						$checkbox = \Form::{$this->field}( $this->name, $this->value, $this->selected, $this->attributes );

						$label =  \Form::label($this->name, $this->extras['text'] ?? '', ['class' => 'custom-control-label']);

						$field = \Html::tag('div', $checkbox.$label, ['class' => 'custom-control custom-checkbox']);

						break;

					default :

						$field = \Form::{$this->field}( $this->name, $this->value, $this->attributes );

						break;
				}
			}

			array_push($content, $field);

			if($this->valueAppend){

				array_push($content, '<div class="input-group-append"><span class="input-group-text">'.$this->valueAppend.'</span></div>');
			}

			if($this->valuePrepend || $this->valueAppend){

				array_push($content, '</div>');
			}

			if ($hasError){

				array_push($content, '<div class="invalid-feedback d-block"><strong>'.($errors->first($this->name)).'</strong></div>');
			}
		}

		$response = \Html::tag('div', $content);

		return $response;
	}

	/**
	 * @param $src
	 *
	 * @return string
	 */

	public function fileFieldTemplate($src){

		$id = Str::slug($this->name);

		$field =
			'<div class="card" style="width: 18rem;">

				<div class="card-body preview">

  					<img src="'.$src.'" class="card-img-top" id="preview-'.$id.'" alt="">

  				</div>

  				<hr />

  				<div class="card-body">

				    <a href="'.route('admin.media.show.modal', ['id' => $id]).'" data-id="" class="card-link active-modal">Change</a>

				    <a href="'.route('admin.media.upload.modal', ['id' => $id]).'" class="card-link active-modal">Upload</a>';

		if($this->file) {

			$field .= '<a href="#" class="card-link btn-delete-media" data-file="' . $id . '">Delete</a>';
		}

		$field .= \Form::hidden($this->name, is_object($this->value) ? $this->value->getId() : $this->value, ['id' => $id]).'</div>
			</div>';

		$field .=  '
					<script>
						 $(document).ready(function() {

							 $(document).on("click", ".btn-delete-media", function(e) {

								e.preventDefault();

								var file = $(this).attr("data-file");

								Swal.fire({
									title: "Are you sure?",
									type: "warning",
									showCancelButton: true,
									confirmButtonColor: "#d33",
									cancelButtonColor: "#3085d6",
									confirmButtonText: "Yes, delete it!"
						        }).then((willDelete) => {

									if (willDelete) {

										$("#"+file).val("");

										var imgId = $("#"+file).closest(".card").find(".preview").find("img").attr(\'id\');

										$("#"+file).closest(".card").find(".preview").html("<img src=\"'.(str_replace('"', '\'', $src)).'\" class=\"card-img-top\" id=\"+imgI+\" alt=\"\">");
									}
								});
							});
						});
					</script>';

		return $field ;
	}

	/**
	 * This function is used in media library page only
	 *
	 * @param $src
	 *
	 * @return string
	 */

	public function fileFieldTemplate2($src){

		$id = Str::slug($this->name);

		$field =
			'<div class="media-card">

				'.\Html::image($src->getFilePlaceholder(), null, ["id" => "preview-'.$id.'", 'class' => 'card-img-top rounded-0', 'popup' => true]).'

				<div class="header">

                   '. $src->getFilename() .'
                </div>

  				<div class="footer">
                	<div class="buttons-sm">
                    	<a href="'. route("admin.media.edit.modal", $src) .'" class="btn btn-warning btn-sm active-modal"><i class="fas fa-edit" aria-hidden="true"></i> Edit</a>

                        <a href="'. route("admin.media.delete", $src) .'" class="btn btn-danger btn-sm btn-alert" data-file="'. $src->getId() .'" data-method="DELETE" data-redirect="true"><i class="fas fa-trash" aria-hidden="true"></i> Delete</a>
                    </div>

                    <div class="detail">
                        <span class="badge">'. $src->getMime() .'</span>

                        <div class="icons">
                            <i class="fas fa-clock" data-toggle="tooltip" data-placement="top" title="'. $src->getDate("created_at") .'"></i>

                            <i class="fas fa-weight-hanging" data-toggle="tooltip" data-placement="top" title="'. $src->getSize() .'"></i>

                            <i class="fas fa-file-signature" data-toggle="tooltip" data-placement="top" title="'. ($src->getDimensions() ?? "---") .'"></i>

                            <i class="fas fa-images" data-toggle="tooltip" data-placement="top" title="'. (optional($src)->getAlt() ?? "---") .'"></i>
                        </div>
                    </div>
                </div>
            </div>';

		return $field ;
	}
}
