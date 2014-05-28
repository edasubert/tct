<?php
	echo "Begin install<br>\n";
	
	define( "APP_PATH", dirname( __FILE__ ) ."/" );
	define( "PCAFW", TRUE );

	require_once("config/dbConfig.php");
	
	require_once('registry.class.php');
	
	$registry = Registry::singleton();
	
	$registry->storeObject( "database", "db" );
	$registry->getObject("db")->newConnection( HOST, USERNAME, PASSWORD, DBNAME );
	
	// SOURCE ==========================================================
	$query = "CREATE TABLE IF NOT EXISTS Source
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	IDOut BIGINT(20),
	text TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	langSource VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	langTarget VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	author VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	timestamp TIMESTAMP NOT NULL,
	deleted BOOLEAN NOT NULL DEFAULT 0,
	flag BOOLEAN NOT NULL DEFAULT 0,
	hash VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	// TRANSLATION =====================================================
	$query = "CREATE TABLE IF NOT EXISTS Translation
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	IDSource INT,
	text TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	author INT NOT NULL,
	timestamp TIMESTAMP NOT NULL,
	score INT NOT NULL DEFAULT 1400,
	deleted BOOLEAN NOT NULL DEFAULT 0,
	flag BOOLEAN NOT NULL DEFAULT 0
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	// TRAN_STATE ======================================================
	$query = "CREATE TABLE IF NOT EXISTS TranState
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	IDTran INT,
	author VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	timestamp TIMESTAMP NOT NULL,
	action BIT(2) NOT NULL DEFAULT b'00'
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	// SOURCE_STATE ====================================================
	$query = "CREATE TABLE IF NOT EXISTS SourceState
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	IDSource INT,
	author VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	timestamp TIMESTAMP NOT NULL,
	action BIT(2) NOT NULL DEFAULT b'00',
	numTran INT
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	// SCORE_CHANGE ====================================================
	$query = "CREATE TABLE IF NOT EXISTS ScoreChange
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	IDTranWin INT,
	IDTranLost INT,
	ScoreTranWin INT,
	ScoreTranLost INT,
	author VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	timestamp TIMESTAMP NOT NULL
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	// TRANSLATORS =====================================================
	$query = "CREATE TABLE IF NOT EXISTS Translators
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	mail VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	name VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	interLang VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	description VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	activated BOOLEAN NOT NULL DEFAULT 0,
	disabled BOOLEAN NOT NULL DEFAULT 0,
	vacation BOOLEAN NOT NULL DEFAULT 0
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	// TRANSLATORS - LANGUAGES =========================================
	$query = "CREATE TABLE IF NOT EXISTS TranslatorsLang
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	IDTranslator INT,
	fromLang BOOLEAN NOT NULL DEFAULT 0,
	lang VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	// TRANSLATORS - SETTINGS ==========================================
	$query = "CREATE TABLE IF NOT EXISTS TranslatorsSet
	(
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID),
	IDTranslator INT,
	UNIQUE (IDTranslator),
	timestamp TIMESTAMP NOT NULL,
	hash VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
	)";
	
	$registry->getObject("db")->executeQuery( $query );
	
	echo "tables created<br>\n";
	
?>
