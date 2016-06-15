<?php 
namespace router;

function load_mapper($routerDirectory){
	$mapper = $routerDirectory.'/json.txt';
	$fp = fopen($mapper, 'r');

	if(!$fp){ return false; }

	$content = fread($fp, filesize($mapper));

	fclose($fp);

	if($content){
		$configs = json_decode($content);

		if($configs){
			
			return (check_json_settings($configs)) ? $configs : false;
		}else{
			return false;
		}
	}else{
		return false;
	}
} 

/**
 * Checks to see if esssential settings are defined in json.txt
 * 
 * @param type object $configs
 * @return boolean
 */
function check_json_settings($configs){
    if(isset($configs->appURL)
            && isset($configs->controllersDirectory)
            && isset($configs->controllerBased)
            && isset($configs->ErrorPage)){
        return true;
    }
}

/**
 * This helper function will determine the URI elelments that relate to 
 * a path IN the APP. It will take out the uri elements that that are part
 * of the APP URL as defined in json.txt.
 * 
 * @param type string $app_url
 * @param type string $host
 * @param type string $uri
 */
function get_uri($app_url, $host, $full_uri){
    $host = trim($host, 'www.');
    
    if(strpos($app_url, $host) !== 0){
        return false;
    }
    
    //we want to exclude this from the final URI returned by this function...
    $app_uri = trim(substr($app_url, strlen($host)), '/');
    
    $uri = trim(substr(trim($full_uri, '/'), strlen($app_uri)), '/');
    
    $URI = explode('/', $uri);
    
    foreach ($URI as $k => $value) {
        preg_match('#([A-Za-z1-9_-])+#', $value, $matches);
        $URI[$k] = $matches[0];
    }
    return $URI;
}