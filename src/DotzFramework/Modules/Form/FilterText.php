<?php
namespace DotzFramework\Modules\Form;

use DotzFramework\Core\Dotz;

class FilterText {

	public $allowedTags;

	public $removeOnlyTag;

	public function __construct($allowedTags = []){
		
		$this->allowedTags = [
			'div',
			'p',
			'a',
			'span',
			'table',
			'th',
			'tr',
			'td',
			'br',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
		];

		$this->removeOnlyTag = [
			'html',
			'body'
		];

		array_merge($this->allowedTags, $allowedTags);

	}

	public function process($text = null){

		$arr = explode("\n", $text);
		$removed = [];

		// loop through each line.
		foreach ($arr as $i => $line) {
			
			$arr[$i] = trim($line);

			preg_match_all("|(<[^>]+>)|U",
			    $line, $tags, PREG_PATTERN_ORDER
			);

			// loop through each tag and pick out opening tags only
			// to further nick-pick
			for ($s=0; $s < count($tags[0]); $s++) { 
				
				preg_match('#<!?([a-zA-Z0-1]+)#', 
					$tags[0][$s], $m
				);
				
				if(count($m) > 1){
					
					if(!in_array(strtolower($m[1]), $this->allowedTags)){

						if(in_array(strtolower($m[1]), $this->removeOnlyTag)){
							
							// remove only the opening and closing tags...
							$text = preg_replace("#</?".$m[1].".*/?>#Us",
								'', $text, -1, $c 
							);

						}else{

							// need to remove the whole tag and its contents.
							$text = preg_replace("#<".$m[1].".*>(.*)</".$m[1].".*>#Us",
								'', $text, -1, $c 
							);

							if($c == 0){

								// if that fails, at least remove the opening and closing tags.
								$text = preg_replace("#</?!?".$m[1].".*/?>#Us",
									'', $text, -1, $c 
								);
							}

						}
		
						$removed[strtolower($m[1])] = $c;
					}
				}

			}
		}

		$text = trim($text);

		var_dump($arr, $removed, $text);

	}

}