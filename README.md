# PHP Flickr API Wrapper

Easily make API calls to Flickr's REST services. Returns results as objects.

## Example Usage

	Flickr::$api_key      = 'my_flickr_api_key';
	Flickr::$cache_dir    = 'cache/flickr/';     // Defaults to 'cache'
	Flickr::$cache_expire = 600;                 // Defaults to 86400 seconds (1 day)
	
	$params = array
	(
		'user_id'  => 'my_flickr_user_id',
		'extras'   => 'url_sq,url_m',
		'per_page' => 10,
	);
	
	$result = Flickr::call('people.getPublicPhotos', $params);
	
	foreach ($result->photos->photo as $photo)
	{
		$photo_info = Flickr::call('photos.getInfo', array('photo_id' => $photo->id));
	}
	
	// Now I want to grab someone else's photos
	Flickr::$api_key = 'my_new_flickr_api_key';
	
	$params = array
	(
		'user_id' => 'my_new_flickr_user_id',
	);
	
	$result = Flickr::call('people.getPublicPhotos', $params);