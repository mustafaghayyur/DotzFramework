<?php
namespace DotzFramework\Utilities;

use DotzFramework\Core\Dotz;
use \Firebase\JWT\JWT;


class CSRF {

	public static function checkOrigin(){
		
		$headers = Dotz::get()->load('request')->headers;
		$origin = empty($headers->get('origin')) ? $headers->get('referer') : $headers->get('origin');
		
		preg_match('#(http(s)?://)?([\w_\-\.]+)#', $origin, $o); 
		preg_match('#(http(s)?://)?([\w_\-\.]+)#', $headers->get('host'), $h); 

		if($o[3] === $h[3]){
			return true;
		}

		return false;

	}

	public static function generateToken(){
		
		$c = Dotz::config('app');
		$key = $c->secretKey;

		$payload = array(
		    "iss" => $c->httpProtocol.'://'.$c->url,
		    "iat" => time(),
		    "exp" => time() + (60 * 10)
		);

		return JWT::encode($payload, $key, 'HS256');

	}

	public static function validateToken($token){
		
		$c = Dotz::config('app');
		$key = $c->secretKey;

		if(JWT::decode($token, $key, array('HS256'))){
			return true;
		}

		return false;

	}
}