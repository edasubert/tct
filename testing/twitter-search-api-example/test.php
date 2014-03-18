<?php
	header('Content-Type: text/html; charset=utf-8');
	
	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);

	require_once('config.php');

	require_once('api/TwitterAPIExchange.php');
	 
	/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
	$settings = array(
	    'oauth_access_token' => ACCESS_TOKEN,
	    'oauth_access_token_secret' => ACCESS_TOKEN_SECRET,
	    'consumer_key' => CONSUMER_KEY,
	    'consumer_secret' => CONSUMER_SECRET
	);
	
	$url = "https://api.twitter.com/1.1/search/tweets.json";
	 
	$requestMethod = "GET";
	
	$getfield = '?q=lang%3Auk';
	 
	$twitter = new TwitterAPIExchange($settings);
	
	$data = json_decode( $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(), $assoc = TRUE );
	
	$data = $data["statuses"];
	
	foreach( $data as $key => $value )
	{
		echo $value["text"]."<br>";
	}
?>
