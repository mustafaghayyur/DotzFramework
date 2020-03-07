<?php
namespace DotzFramework\Utilities;

use DotzFramework\Core\Dotz;

class CSRF {

	public static function checkOrigin(){
		
		$headers = Dotz::get()->load('request')->headers;
		$origin = empty($headers->get('origin')) ? $headers->get('referer') : $headers->get('origin');
		
		preg_match('#(http(s)?://)([\w_\-\.]+)#', $origin, $o); 
		preg_match('#(http(s)?://)?([\w_\-\.]+)#', $headers->get('host'), $h); 

		if($o[3] === $h[3]){
			return true;
		}

		return false;

	}

}