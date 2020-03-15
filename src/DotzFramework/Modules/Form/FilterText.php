<?php
namespace DotzFramework\Modules\Form;

use DotzFramework\Core\Dotz;

class FilterText {

	public $allowedTags;

	public $removeOnlyTag;

	public function __construct($allowedTags = []){
		
		$this->allowedTags = [
			'div', 'p', 'a', 'span', 'table',
			'th', 'tr', 'td', 'br', 'h1', 'h2',
			'h3', 'h4', 'h5', 'h6', 'li', 'ul',
			'ol', 'img', 'section', 'nav', 'desc',
			'hr', 'footer', 'header', 'small',
			'em', 'b', 'strong', 'aside', 'pre',
			'dl', 'dt', 'dd', 'blockquote'
		];

		$this->removeOnlyTag = [
			'html', 'body'
		];

		array_merge($this->allowedTags, $allowedTags);

	}

	public function process($inputName, $text = ''){

		$removed = [];
		$tagsArr = [];

		preg_match_all("|(<[^>]+>)|U",
		    $text, $tags, PREG_PATTERN_ORDER
		);

		// loop through each tag and pick out tag names to further nick-pick
		for ($s=0; $s < count($tags[0]); $s++) { 

			//preg_match('#<!?([a-zA-Z0-9]+)#', 
			preg_match('#</?([^> \n]+)#', 
				$tags[0][$s], $tagName
			);
			
			$tagName[1] = isset($tagName[1]) ? $tagName[1] : '';
			$tagsArr[$tagName[1]] = isset($tagsArr[$tagName[1]]) ? $tagsArr[$tagName[1]] : 0;
			$tagsArr[$tagName[1]]++;
		}

		foreach ($tagsArr as $tag => $count) {

			$l1 = round($count / 2);
			$l2 = $count;
			$c = 0;
				
			if(!in_array(strtolower($tag), $this->allowedTags)){

				$t = preg_quote($tag, '#');

				if(in_array(strtolower($tag), $this->removeOnlyTag)){
					
					// remove only the opening and closing tags...
					$text = preg_replace("#<\/?".$t."\b.*>#Us",
						"", $text, $l2, $c 
					);

				}else{

					// need to remove the whole tag and its contents.
					//$_text = preg_replace("#<".$t."\b.*>([\w\d\s\`\~\!\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\[\}\]\\\|\;\:\'\"\,\>\.\/\?]*)<\/".$t."\b.*>#Us",		 
					$_text = preg_replace("#<".$t."[^>]*+>((?:(?!<\/".$t.").)*+)<\/".$t."[^>]*+>#Us",		 
						 "", $text, $l1, $c
					);


					if(preg_last_error() != PREG_NO_ERROR){
						throw new Exception('Cannot process input '.$inputName.'. Filter had the following error code: '. preg_last_error());
					}

					if($c === 0){
						echo 'f<br>';
 						// if that fails, at least remove the opening and closing tags.
						$text = preg_replace("#<\/?".$t."[^>]*+>#Us",	 
							 "", $text, $l2, $c
						);

						if($c === 0){

	 						// Maybe it's some sort of a script tag?
							$text = preg_replace("#<".$t."[^>]*+>#U",	 
								 "", $text, $l2, $c
							);

						}

					}else{
						$text = $_text;
					}
				}

				$removed[strtolower($tag)] = isset($removed[strtolower($tag)]) ? $removed[strtolower($tag)] : 0;
				$removed[strtolower($tag)] = $removed[strtolower($tag)] + $c;
			}
		}

		//incase a script tag slipped through...
		$text = preg_replace("#<script\b.*>(.*)<\/script\b.*>#Us",		 
			 "", $text, -1, $c
		);

		$text = preg_replace("#(<script[^>]+>)#U",		 
			 "", $text, -1, $c
		);

		return trim($text);

	}

}