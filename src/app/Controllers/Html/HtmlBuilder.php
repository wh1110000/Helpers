<?php

namespace wh1110000\Helpers\Controllers\Html;

use function GuzzleHttp\Psr7\mimetype_from_filename;
use Illuminate\Support\Optional;
use Illuminate\Support\Str;

/**
 * Class HtmlBuilder
 * @package Workhouse\Cms\Helpers
 */

class HtmlBuilder extends TemplateBuilder {

	/**
	 * @param string $object
	 * @param string $alt
	 * @param array $attributes
	 * @param null $secure
	 *
	 * @return bool|\Illuminate\Support\HtmlString|string
	 */

	public function image($object, $alt = '', $attributes = [], $secure = null){

		$instance = new \Media();

		if(($object instanceof $instance || $object instanceof Optional) && method_exists($instance, 'getFile')){

			if($object->fileExists()){

				$url = $object->getFile();

				$attributes['alt'] = $alt ?: $object->getAlt();

			} else {

				return $this->toHtmlString('');
			}

		} else {

			$url = $object;
			
			$object = $instance->where('filename', Str::afterLast($url, '/'))->first();

			$attributes['alt'] = $alt;
		}
		///}

		if(!$url){

			if(isset($attributes['placeholder']) && $attributes['placeholder'] == true){

				$url = \Html::placeholder();
			} else {
				 return $this->toHtmlString('');
			}
		
		}

		$attributes['class'] = $attributes['class'] ?? [];

		$inline = true;

		if($attributes['class']) {

			$inline = false;
		}

		$attributes['class'] = array_merge(empty($attributes['reset']) ? ['img-fluid'] : [], is_array($attributes['class']) ? $attributes['class'] : explode(' ', $attributes['class']));

		if(isset($attributes['reset'])) {

			unset( $attributes['reset'] );
		}

		$isPopup = false;

		if(isset($attributes['popup'])) {

			$isPopup = $attributes['popup'] == true ?: false;

			unset( $attributes['popup'] );
		}

		$mime = mimetype_from_filename($url);

		if(\Illuminate\Support\Str::contains($mime, 'image/svg') && $inline){

			return file_get_contents($url);
		}

	    if((!isset($attributes['alt']) || is_null($attributes['alt'])) && ($object instanceof $instance)){

			$attributes['alt'] = $object->getAlt();
	    }
		    
		$img = $this->toHtmlString('<img src="' . $this->url->asset($url, $secure) . '"' . $this->attributes($attributes) . '>');

		if($isPopup) {

			return $this->link($url, $img, ['class' => 'image-popup'], null, false);
		}

		return $img;
	}

	//COMPONENETS

	/**
	 * @param $status
	 * @param null $title
	 * @param array $extras
	 * @param string $titleTag
	 *
	 * @return \Illuminate\Support\HtmlString
	 */

	public function block($status, $title = null, $extras = [], $titleTag = 'h5'){

		$html = '';

		$showBlock = $title || !empty($extras);

		switch ($status){

			case 'open':

				$rows = [];

				if($title){

					array_push($rows, $this->tag('div', $this->tag($titleTag, __($title))->toHtml(), ['class' => 'col']));
				}

				if(!empty($extras)){

					array_push($rows, $this->tag('div', $extras, ['class' => 'col text-right']));
				}

				if($showBlock) {

					array_push( $rows, $this->tag( 'div', '<hr class="my-2"/>', [ 'class' => 'col-12' ] ) );

					$html .= $this->tag('div', $rows, ['class' => 'row mb-4']);
				}

				break;

			case 'close':

				break;
		}

		return $this->toHtmlString($html);
	}

	/**
	 * @param $status
	 * @param null $title
	 * @param null $description
	 * @param string $titleTag
	 *
	 * @return \Illuminate\Support\HtmlString
	 */

	public function section($status, $title = null, $description = null, $titleTag = 'h5'){

		$html = '';

		switch ($status){

			case 'open':

				$rows = [];

				if($title){

					array_push($rows, $this->tag($titleTag, __($title)));
				}

				if($description){

					array_push($rows, $this->tag('small',  $description));
				}

				$html .= '<section class="mb-4">'.implode('',$rows).'<hr />';

				break;

			case 'close':

				$html .= '</section>';

				break;
		}

		return $this->toHtmlString($html);
	}

	/**
	 * @param $type
	 * @param $content
	 *
	 * @return string
	 */

	public function badge($type, $content){

		return $this->tag('span', $content, ['class' => 'badge badge-'.$type.' d-inline-block block mb-3']);
	}
}
