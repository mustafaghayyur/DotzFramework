<?php
namespace DotzFramework\Modules\Form;

use DotzFramework\Core\Dotz;

/**
 * Generate the perfect WYSIWYG editor.
 * One that truely shows the text as it would show on the website.
 */
class WYSIWYG {

	public $bodyClass;
	
	public $shellClass;
	
	public $contentClass;

	public function __construct(){
		
	}

	public function bodyClass($class = 'page'){
		$this->bodyClass = $class;
	}

	public function shellClass($class = 'shell'){
		$this->shellClass = $class;
	}

	public function contentClass($class = 'content'){
		$this->contentClass = $class;
	}

	public function styles(){
		
		$path = Dotz::get()->load('configs')->props->app->viewsDir;

		if(file_exists($path)){
			if(file_exists($path.'/assets/css')){
				$files = scandir($path.'/assets/css');

				array_shift($files);
				array_shift($files);

				return $files;
			}
		}

		return null;
	}

	public static function decode($json){
		$obj = json_decode($json);

		$text = '';

		foreach ($obj->ops as $i) {

			if(isset($i->attributes)){
				if(isset($i->attributes->bold)){
					$text .= '<strong>'.$i->attributes->insert.'</strong>';
				}

				if(isset($i->attributes->link)){
					$text .= '<a href="'.$i->attributes->link.'">'.$i->attributes->insert.'</a>';
				}

				if(isset($i->attributes->italic)){
					$text .= '<em>'.$i->attributes->insert.'</em>';
				}
			}else{
				$text .= $i->insert;
			}

		}
	}

	public function generate($attributes){

		$label = isset($attributes['label']) ? $attributes['label'] : '';
		$name = isset($attributes['name']) ? $attributes['name'] : '';
		$hidden = isset($attributes['additional']) ? $attributes['additional'] : '';

		echo '<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">';
		
		echo <<<EOD
			{$hidden}

			<label for="{$name}">{$label}</label>

			<div id="toolbar">
			  <button class="ql-bold">Bold</button>
			  <button class="ql-italic">Italic</button>
			</div>

			<div id="{$name}WYSIWYG">
			  <p>Hello World!</p>
			</div>
EOD;

		echo '<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>';

		$code = <<<EOD
			var editor = new Quill('#{$name}WYSIWYG', {
			    modules: { toolbar: '#toolbar' },
			    theme: 'snow'
			});

			var input = $('.{$name}InputField');

			$(editor).change(function(e) {
				console.log('sss');
			  input.value = JSON.stringify(editor.getContents());
			  
			  console.log("Input Val:", input, input.serializeArray());
			  
			});
EOD;

		$js = Dotz::get()->load('js');
		$js->add('wysiwyg', $code);
		echo $js->stringify();
	}

}