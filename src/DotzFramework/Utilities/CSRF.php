<?php
namespace DotzFramework\Utilities;

use DotzFramework\Core\Dotz;
use DotzFramework\Core\ErrorHandler;
use \Firebase\JWT\JWT;


class CSRF {

	public static function checkOrigin(){
		
		$headers = Dotz::module('request')->headers;
		
		$origin = empty($headers->get('origin', null)) ? $headers->get('referer', null) : $headers->get('origin', null);

		if($origin === null){
			if(Dotz::config('app.csrf.nullOrigins') === 'allowed'){
				return true;
			}
		}
		
		preg_match('#(http(s)?://)?([\w_\-\.]+)#', $origin, $o); 
		preg_match('#(http(s)?://)?([\w_\-\.]+)#', $headers->get('host'), $h); 

		if($o[3] === $h[3]){
			return true;
		}

		return false;

	}

	public static function generateToken(){
		
		$c = Dotz::config('app');
		
		if(!isset($c->csrf->tokenLife)){
			throw new \Exception(ErrorHandler::ERROR1);
		}

		$life = (int)$c->csrf->tokenLife;

		$payload = array(
		    "iss" => $c->httpProtocol.'://'.$c->url,
		    "iat" => time()
		);

		if($life > 0) {
			$payload['exp'] = time() + $life;
		}

		return JWT::encode($payload, $c->csrf->secretKey, 'HS256');

	}

	/**
	 * Returns boolean true on success. False on failiure.
	 * 	- if $returnException = true; returns exeption message on failiure
	 */
	public static function validateToken($token, $returnException = false){
		
		$c = Dotz::config('app');

		if(!isset($c->csrf->secretKey)){
			throw new \Exception(ErrorHandler::ERROR1);
		}

		try{
			$payload = JWT::decode($token, $c->csrf->secretKey, array('HS256'));
		}catch(\Exception $e){
			// Csrf tokens are not understood by most
			// we hid the JWT token error to minimize possible confusion.
			// This function just returns a boolean value.
			$exception = $e->getMessage();
		}

		if(isset($payload) && is_object($payload)){
			return true;
		}else{
			
			if($returnException){
				return isset($exception) ? $exception : false;
			}

			return false;
		}

	}
}