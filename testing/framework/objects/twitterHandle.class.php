<?php
	if ( ! defined( 'PCAFW' ) ) 
	{
		echo 'This file can only be called via the main index.php file, and not directly';
		exit();
	}
	
	require_once('api/twitter/TwitterAPIExchange.php');
	
class twitterHandle
{
	private $registry;
	private $API;
	private $settings;
	
	public function __construct( &$instance )
	{
		$this->registry = $instance;
	}
	
	public function set( $consumer_key, $consumer_secret, $access_token, $access_token_secret )
	{
		$this->settings = array();
		
		$this->settings['oauth_access_token']			= $access_token;
		$this->settings['oauth_access_token_secret']	= $access_token_secret;
		$this->settings['consumer_key']				= $consumer_key;
		$this->settings['consumer_secret']			= $consumer_secret;
	}
	
// TWITTER PULL ========================================================

	public function pullTweet()
	{
		$this->API = new TwitterAPIExchange( $this->settings );
		
		$hashtag = $this->registry->getSetting( "hashtag" );
		
		require("lang/languageCodes.php");
		
		$url = "https://api.twitter.com/1.1/search/tweets.json";
		
		$requestMethod = "GET";
		
		$data = json_decode( $this->API->setGetfield( "?q=%23".$hashtag )->buildOauth($url, $requestMethod)->performRequest(), $assoc = TRUE );
		unset( $this->API );
		
		$data = $data["statuses"];
		
		$output = array();
		
		foreach( $data as $value )
		{
			
			$text = $value["text"];
			$text = str_replace( "#".$hashtag." ", "", $text );
			$text = $tweet = preg_replace('/#[\w-]{2} /i', '', $text);
			
			$langCode = strtolower( array_intersect( array_map( "strtoupper", hashtagPull($value) ), array_keys( $languageCodes ) )[1] );
			
			array_push( $output, array( "id" => $value["id"], "text" => $text, "from" => $value["lang"], "to" => $langCode, "author"=> $value["user"]["screen_name"], "author_id" => $value["user"]["id_str"] ) );
		}
		return $output;
	}
	
// TWITTER PUSH ========================================================

	public function pushTweet( $text )
	{
		$this->API = new TwitterAPIExchange( $this->settings );
		
		$url = "https://api.twitter.com/1.1/statuses/update.json";
		$requestMethod = "POST";
		$postfields = array('status' => $text ); 
		$this->API->buildOauth($url, $requestMethod);
		$this->API->setPostfields($postfields);
		$this->API->performRequest();
		
		unset( $this->API );
	}
	
// TWITTER PRIVATE MESSAGE =============================================

	public function privateMessage( $user_id, $text )
	{
		$this->API = new TwitterAPIExchange( $this->settings );
		
		$url = "https://api.twitter.com/1.1/direct_messages/new.json";
		$requestMethod = "POST";
		$postfields = array( 'user_id' => $user_id, 'text' => $text ); 
		$this->API->buildOauth($url, $requestMethod);
		$this->API->setPostfields($postfields);
		$this->API->performRequest();
		
		unset( $this->API );
	}
	
}

// SUPPORT FUNCTION ====================================================
function hashtagPull( $tweet )
{
	$result = array();
	foreach ( $tweet["entities"]["hashtags"] as $hashtag )
	{
		array_push( $result, $hashtag["text"] );
	}
	return $result;
}

?>
