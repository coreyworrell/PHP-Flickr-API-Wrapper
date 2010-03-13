<?php
/**
 * Flickr API wrapper
 *
 * Hits up flicker for requests, returns them as objects,
 * and caches them on file for faster performance
 *
 * @author   Corey Worrell
 * @url      http://coreyworrell.com
 * @version  1.0
 */
class Flickr {

	// Public API URI
	protected static $_rest_uri = 'http://api.flickr.com/services/rest/';
	
	// Parameters for method calls
	protected static $_params = array();
	
	// Flickr API key
	public static $api_key;
	
	// Directory to store cache files
	public static $cache_dir = 'cache';
	
	// Number of seconds to cache requests
	public static $cache_expire = 86400;
	
	/**
	 * Make an API request to Flickr
	 *
	 * @param   string   Method name (without the 'flickr.' prefix)
	 * @param   array    Associative array of parameters for request
	 * @return  stdClass JSON decoded result from Flickr
	 */
	public static function call($method, array $params = array())
	{
		if (self::$api_key === NULL AND ! isset($params['api_key']))
		{
			throw new Exception('API Key must be set before calling a method!');
		}
		
		$params['method'] = "flickr.$method";
		
		return self::_request($params);
	}
	
	/**
	 * Internal method
	 * Builds the URI and handles response and caching
	 *
	 * @param   array    Associative array of parameters for request
	 * @return  stdClass JSON decoded result from Flickr
	 */
	private static function _request(array $params)
	{
		self::_cache();
		
		$defaults = array
		(
			'api_key'        => self::$api_key,
			'format'         => 'json',
			'nojsoncallback' => 1,
		);
		
		$params = $defaults + $params;
		
		ksort($params);
		
		$uri = self::$_rest_uri.'?'.http_build_query($params, NULL, '&');
		
		$cache_file = rtrim(self::$cache_dir, '/').'/'.md5($uri).'_flickr.txt';
		
		if (file_exists($cache_file) AND time() < (filemtime($cache_file) + self::$cache_expire))
		{
			$response = file_get_contents($cache_file);
		}
		else
		{
			$response = file_get_contents($uri);
			
			file_put_contents($cache_file, $response);
		}
		
		return json_decode($response);
	}
	
	/**
	 * Internal method
	 * Handles creation of cache directory and deletes expired cache
	 *
	 * @return   void
	 */
	private static function _cache()
	{
		$dir = rtrim(self::$cache_dir, '/').'/';
		
		if ( ! is_dir($dir))
		{
			mkdir($dir, 0777);
		}
		
		// Garbage collection
		if (mt_rand(1, 100) === 1)
		{
			$iterator = dir($dir);
			
			while ($file = $iterator->read())
			{
				$file = $dir.$file;
				
				if (substr($file, -11) === '_flickr.txt' AND time() > (filemtime($file) + self::$cache_expire))
				{
					unlink($file);
				}
			}
		}
	}
	
	final private function __construct()
	{
		// This is a static class
	}

}