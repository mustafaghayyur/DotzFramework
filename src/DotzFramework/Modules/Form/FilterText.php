<?php
namespace DotzFramework\Modules\Form;

use DotzFramework\Core\Dotz;

/**
 * Useful filter for WYSIWYG editors.
 * This class could be vulnerable to attacks.
 * Use at your own descretion.
 *
 * Want to build on it?
 * Feel free to extend and overwrite the process method.
 * You can then pass your version of FilterText to 
 * $this->input->post() in the controller.
 */
class FilterText {

	/**
	 * Carries a list of tags not to be filtered out.
	 */
	protected $allowedTags;

	/**
	 * Carries a list of tags for which only the opening and
	 * closing tags are to be removed. The contents of these
	 * tags are to be kept.
	 */
	protected $stripOnlyTags;

	public function __construct($allowedTags = []){
		
		$this->allowedTags = [
			'div', 'p', 'a', 'span', 'table',
			'th', 'tr', 'td', 'br', 'h1', 'h2',
			'h3', 'h4', 'h5', 'h6', 'li', 'ul',
			'ol', 'img', 'section', 'nav', 'desc',
			'hr', 'footer', 'header', 'em', 'b', 
			'strong', 'aside', 'pre', 'style',
			'dl', 'dt', 'dd', 'blockquote', 
			'noscript', 'code', 'big', 'small'
		];

		$this->stripOnlyTags = [
			'html', 'body'
		];

		array_merge($this->allowedTags, $allowedTags);

	}

	public function getAllowedTags(){
		return $this->allowedTags;
	}

	/**
	 * Use this method to add to the ignore list...
	 */
	public function addAllowedTags(Array $array){
		$this->allowedTags = array_merge($this->allowedTags, $array);
		return $this->getAllowedTags();
	}

	public function getStripOnlyTags(){
		return $this->stripOnlyTags;
	}

	/**
	 * Any other tags you need the contents kept of?
	 * Pass them along to this method while applying the filter
	 * to your input->post() call.
	 */
	public function addToStripOnlyTagsList(Array $array){
		$this->stripOnlyTags = array_merge($this->stripOnlyTags, $array);
		return $this->getStripOnlyTags();
	}

	public function process($inputName, $text = ''){

		// Keeps a count of instances removed for each tag.
		// This count can be lower than $tagsArr, when preg_replace()
		// removes the entire opening & closing tag block together 
		$removed = [];
		
		// keeps a count of instances found for each tag
		$tagsArr = [];

		preg_match_all("|(<[^>]+>)|U",
		    $text, $tags, PREG_PATTERN_ORDER
		);

		// loop through each tag and pick out tag names to further nick-pick
		for ($s=0; $s < count(Dotz::grabKey($tags, 0)); $s++) { 

			preg_match('#</?([^> \n]+)#', 
				$tags[0][$s], $tagName
			);
			
			$tagName[1] = isset($tagName[1]) ? $tagName[1] : '';

			$tagsArr[$tagName[1]] = isset($tagsArr[$tagName[1]]) 
									? $tagsArr[$tagName[1]] 
									: 0;
			
			$tagsArr[$tagName[1]]++;
		}

		foreach ($tagsArr as $tag => $count) {

			$l1 = round($count / 2);
			$l2 = $count;
			$c = 0;
				
			if(!in_array(strtolower($tag), $this->allowedTags)){

				$t = preg_quote($tag, '#');

				if(in_array(strtolower($tag), $this->stripOnlyTags)){
					
					// remove only the opening and closing tags...
					$text = preg_replace("#<\/?".$t."\b.*>#Us",
						"", $text, $l2, $c 
					);

				}else{

					// need to remove the whole tag and its contents.
					// keep in mind $c in this case is only half of what it should be.
					$_text = preg_replace("#<".$t."[^>]*+>((?:(?!<\/".$t.").)*+)<\/".$t."[^>]*+>#Us",		 
						 "", $text, $l1, $c
					);


					if(preg_last_error() != PREG_NO_ERROR){
						throw new Exception('Cannot process input '.$inputName.'. Filter had the following error code: '. preg_last_error());
					}

					if($c === 0){

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

				$removed[strtolower($tag)] = isset($removed[strtolower($tag)]) 
												? $removed[strtolower($tag)] 
												: 0;

				$removed[strtolower($tag)] = $removed[strtolower($tag)] + $c;
			}
		}

		// incase a script tag slipped through...
		$text = preg_replace("#<script\b.*>(.*)<\/script\b.*>#Us",		 
			 "", $text, -1, $c
		);

		$text = preg_replace("#(<\/?script[^>]+>)#U",		 
			 "", $text, -1, $c
		);

		return trim($text);

	}

}