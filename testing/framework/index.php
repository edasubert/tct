<?php  

	session_start();
	
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	require_once("dbConfig.php");
	
	define( "APP_PATH", dirname( __FILE__ ) ."/" );
	define( "PCAFW", TRUE );
	
	require_once("registry.class.php");
	
	date_default_timezone_set('Europe/Prague');
	
	
	$registry = Registry::singleton();
	
	$registry->storeObject( "twitterDb", "db" );
	
	$registry->getObject("db")->newConnection( HOST, USERNAME, PASSWORD, DBNAME );
	
	//$registry->getObject("db")->addSource( "text", "cs", "ar", "eda", date("Y-m-d H:i:s") );
	//$registry->getObject("db")->addTranslation( 2, "texto", "eda", date("Y-m-d H:i:s") );
	
	//$registry->getObject("db")->setDeleted( 3 );
	//$registry->getObject("db")->unsetDeleted( 2 );
	//$registry->getObject("db")->setFlag( 1 );
	
	$registry->getObject("db")->score( 2, 1 );
	echo "database queries";
	
	exit();
 
?>
