<?php
	require_once("dbConfig.php");
	
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	define( "APP_PATH", dirname( __FILE__ ) ."/" );
	
	require_once('registry.class.php');
	$registry = Registry::singleton();
	
	$registry->storeObject( "database", "db" );
	$registry->getObject("db")->newConnection( HOST, USERNAME, PASSWORD, DBNAME );
	
	
	$query = "CREATE TABLE IF NOT EXISTS Source
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	text TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	langSource VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	langTarget VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	author VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	date TIMESTAMP NOT NULL
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	$query = "CREATE TABLE IF NOT EXISTS Translation
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	IDSource INT,
	text TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	author VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	date TIMESTAMP NOT NULL,
	score INT NOT NULL DEFAULT 1400,
	deleted BOOLEAN NOT NULL DEFAULT 0,
	flag BOOLEAN NOT NULL DEFAULT 0
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	echo "tables created";
	
?>
