<?php
	header('Content-Type: text/html; charset=utf-8');
	
	define( "APP_PATH", dirname( __FILE__ ) ."/" );
	define( "SERVER", $_SERVER["SERVER_NAME"] );
	define( "PCAFW", TRUE );
	
	date_default_timezone_set('Europe/Prague');
	
	require_once("control.class.php");
	
	$control = new Control();
	
	$control->registry->storeSetting( "twittercrowdtranslation@gmail.com", "rftMail" );
	$control->registry->storeSetting( "Twitter Crowd Translation", "rftMailName" );
	
	$control->registry->storeSetting( "tctrq", "hashtag" );
	
	$control->registry->storeSetting( "default", "template" );
	
	$control->registry->storeSetting( 5, "numTran" );
	$control->registry->storeSetting( 10, "numTranslators" );
	
	$control->registry->getObject( "lang" )->load( "en" );
	$control->registry->getObject( "lang" )->load( "cs" );
	
	$control->pullTweet();
?>
