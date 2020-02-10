<?php
namespace DotzFramework\Utilities;

use Symfony\Component\HttpFoundation\Request;

class SymfonyRequest {
	
	/**
	 * Instance of self
	 */
	protected static $obj;

	/**
	 * Instance of Symfony HTTP Foundations request object
	 */
	public $object;

	
	/**
	 * Symfony HttpFoundation Request object:
	 *  - $request->getUri():
	 *    Get full URL as seen in address bar. 
	 *    The return value is normalized and 
	 *    reconstructed. Use with care.

	 *  - $request->getPathInfo():
	 *    Only the path from a URI. So: 
	 *    http://example.com/app.php/foo/bar?query=value
	 *    will return only '/foo/bar'.

	 *  - $request->getQueryString():
	 *    Returns the ?param=val part of the URI. 
	 *    Normalizes a query string.
	 *    It builds a normalized query string, 
	 *    where keys/value pairs are alphabetized,
	 *    have consistent escaping and unneeded 
	 *    delimiters are removed.

	 *  - $request->getRequestUri():
	 *    Returns the requested URI (path and query 
	 *    string). The raw URI (i.e. not URI 
	 *    decoded). Server address/domain not 
	 *    included.
	 * 
	 *  - getMethod() vs. getRealMethod():
	 *    The getMethod() is more accurate as it 
	 *    takes into account server 
	 *    header manipulation
	 *
	 *  - $request->getContent(): Finally, the raw 
	 *    data sent with the request body can be 
	 *    accessed using getContent(). 
	 *    
	 *    For instance, this may be useful to process 
	 *    a JSON string  sent to the application by a 
	 *    remote service using the HTTP POST method.
	 *  
	 */
	protected function __construct() {

		$this->object = 
			new Request( $_GET,
								$_POST,
								array(),
								$_COOKIE,
								$_FILES,
								$_SERVER );

	}

	/**
	 * Get singleton pattern instance of this Class
	 */
	public static function get(){
		if(self::$obj){
			return self::$obj;
		}else{
			return self::$obj = new SymfonyRequest();
		}
	}

}
