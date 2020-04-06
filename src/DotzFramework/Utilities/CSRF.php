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
		$life = (int)$c->tokenLife;

		$payload = array(
		    "iss" => $c->httpProtocol.'://'.$c->url,
		    "iat" => time(),
		    "exp" => time() + $life
		);

		return JWT::encode($payload, $c->secretKey, 'HS256');

	}

	public static function validateToken($token){
		
		$c = Dotz::config('app');

		if(JWT::decode($token, $c->secretKey, array('HS256'))){
			return true;
		}

		return false;

	}
}