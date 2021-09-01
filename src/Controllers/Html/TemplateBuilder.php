<?php

namespace wh1110000\Helpers\Controllers\Html;

use Collective\Html\HtmlBuilder;
use Favicon\FaviconHtmlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Class TemplateBuilder
 * @package Workhouse\Cms\Helpers
 */

class TemplateBuilder extends HtmlBuilder {

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */

	public function languageSelector(){

		$languages = \Country::where('active', 1)->get();

		return view('blocks::language-selector', compact('languages'));
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */

	public function share($post){

		return view('blocks::share', compact('post'));
	}

	/**
	 * @param int $view
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */

	public function socialMedia($view = 1){

		switch ($view){

			case 1:

				$template = 'LABEL';

				break;

			case 2:

				$template = 'ICON';

				break;

			default:

				$template = 'BOTH';
		}

		$socialMedia = \SocialMedia::whereNotNull('name')->orderBy('order')->get();

		return view('blocks::social-media', compact('socialMedia', 'template'));
	}

	/**
	 * @param null $prepend
	 * @param bool $append
	 * @param string $separator
	 *
	 * @return \Illuminate\Support\HtmlString
	 */

	public function metaTags($prepend = null, $append = false, $separator = ' - '){

		$page = request()->get('currentPost') ?: request()->get('currentPage');

		$title = optional($page)->getMeta('title') ?: null;

		$prepend = $prepend ?? '';

		if(!is_null($title)){

			$title = Str::contains($title, '::') ? Str::replaceFirst(Str::before($title, '::').'::', '', $title) : $title;

			$title = $append ? Str::finish($prepend, ($separator === false ? '' : $separator ).$title) : $title ;

		} else {

			$title = $prepend;
		}

		$description = optional($page)->getMeta('description') ?: null;;

		if(Str::contains($description, '::')){

			$description = Str::replaceFirst(Str::before($description, '::').'::', '', $description);
		}

		return $this->toHtmlString(implode(PHP_EOL, [
			$this->tag('title',  (string) $title),
			$this->meta('description',  (string) $description),
			$this->meta('viewport', 'width=device-width, initial-scale=1'),
			$this->meta('', 'IE=edge', ['http-equiv' => 'X-UA-Compatible']),
			$this->meta('', '', ['charset' => 'utf-8']),
			$this->meta('csrf-token', csrf_token())
		]));
	}

	/**
	 * @return \Illuminate\Support\HtmlString
	 */

	public function googleTagManager($position = 'body'){

		$googleTagManagerId = getSetting('GOOGLE_TAG_ID');

		if($googleTagManagerId){

			$html = '<!--Google Tag Manager '.($position == 'head' ? '' : ' (noscript) ').(App::environment(['production']) ? '-->' : '');

			switch ( $position ) {

				case 'head':

					$html .= "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
					new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
					j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
					'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
					})(window,document,'script','dataLayer','".$googleTagManagerId."');</script>
					";

					break;

				case 'body':

					$html .= '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$googleTagManagerId.'" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';

					break;
			}


			$html .=  '<!-- End Google Tag Manager '.($position == 'head' ? '' : ' (noscript) ').'-->';

		} elseif($googleTagManagerCode = getSetting('GOOGLE_CODE')){

			$html = '';

			if(App::environment(['production'])) {

				switch ( $position ) {

					case 'head':

						$html .= $googleTagManagerCode;

						break;

					case 'body':

						$html .= '';

						break;
				}

			} else {

				$html .=  '<!--'.(is_null($googleTagManagerCode) ? 'Google Tag Manager Code Not Provided' : ': '.$googleTagManagerCode).'-->';
			}

		} else {

			$html = '';
		}

		return $this->toHtmlString($html);
	}

	/**
	 * @return \Illuminate\Support\HtmlString
	 */

	public function bugherd($disable = false, $showOnProduction = false){

		if($disable == true || (App::environment(['production']) && $showOnProduction == false)){

			return '';
		}

		$html = '<!--Bugherd-->';

		$html .= "<script type='text/javascript'>
                      	(function (d, t) {
	                         var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
			                  bh.type = 'text/javascript';
			                  bh.src = 'https://www.bugherd.com/sidebarv2.js?apikey=".(App::environment(['production']) ? env('BUGHERD_PROD') : env('BUGHERD_DEV'))."';
			                  s.parentNode.insertBefore(bh, s);
			            })(document, 'script');
            		</script>";

		$html .=  '<!--END Bugherd-->';

		return $this->toHtmlString($html);
	}

	/**
	 * @return \Illuminate\Support\HtmlString
	 */

	public function favicons() {

		$basePath = '/images/favicons/';

		if($favicon = getSetting('FAVICON')){

			if(file_exists(storage_path('app/public/'.$basePath.$favicon))){

				$faviconHtmlGenerator = new FaviconHtmlGenerator( config( 'app.name' ), $this->url->asset('storage/'.$basePath).'/', '#FFF', '#FFF', '#FFF' );

				return $this->toHtmlString($faviconHtmlGenerator->generate());
			}
		}

		return $this->toHtmlString('
			<link rel="apple-touch-icon" sizes="57x57" href="'.$this->url->asset($basePath.'default/apple-icon-57x57.png').'">
		    <link rel="apple-touch-icon" sizes="60x60" href="'.$this->url->asset($basePath.'default/apple-icon-60x60.png').'">
		    <link rel="apple-touch-icon" sizes="72x72" href="'.$this->url->asset($basePath.'default/apple-icon-72x72.png').'">
		    <link rel="apple-touch-icon" sizes="76x76" href="'.$this->url->asset($basePath.'default/apple-icon-76x76.png').'">
		    <link rel="apple-touch-icon" sizes="114x114" href="'.$this->url->asset($basePath.'default/apple-icon-114x114.png').'">
		    <link rel="apple-touch-icon" sizes="120x120" href="'.$this->url->asset($basePath.'default/apple-icon-120x120.png').'">
		    <link rel="apple-touch-icon" sizes="144x144" href="'.$this->url->asset($basePath.'default/apple-icon-144x144.png').'">
		    <link rel="apple-touch-icon" sizes="152x152" href="'.$this->url->asset($basePath.'default/apple-icon-152x152.png').'">
		    <link rel="apple-touch-icon" sizes="180x180" href="'.$this->url->asset($basePath.'default/apple-icon-180x180.png').'">
		    <link rel="icon" type="image/png" sizes="16x16" href="'.$this->url->asset($basePath.'default/favicon-16x16.png').'">
		    <link rel="icon" type="image/png" sizes="32x32" href="'.$this->url->asset($basePath.'default/favicon-32x32.png').'">
		    <link rel="icon" type="image/png" sizes="96x96" href="'.$this->url->asset($basePath.'default/favicon-96x96.png').'">
		    <link rel="icon" type="image/png" sizes="192x192" href="'.$this->url->asset($basePath.'default/android-icon-192x192.png').'">');
	}

	/**
	 * @return \Illuminate\Support\HtmlString
	 */

	public function copyright() {

		$copyright = trans('cms::general.copyright');

		return $this->toHtmlString($copyright);
	}

	/**
	 * @return string
	 */

	public function placeholder(){

		return asset( 'images/noimage.png' );
	}

	/**
	 * @return \Illuminate\Support\HtmlString
	 */

	public function logoutForm(){

		$form = '';

		if(auth()->check()){

			$form =  $this->toHtmlString(\Form::open(['url' => route('logout') , 'method' => 'POST', 'id' => 'logout-form-user']).\Form::close(false));
		}

		return $this->toHtmlString($form);
	}
}