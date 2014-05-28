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
	
	$control->lang = "en";
	
	
	// OUTPUT ==========================================================
	$templatePath = "/template/".$control->registry->getSetting( "template" );
	include( "template/".$control->registry->getSetting( "template" )."/head.template.php" );
	head_template( $control, "titulek", $templatePath, "en" );
	
	if ( isset( $_GET["settings"] ) )
	{
		$control->activateTranslator( $_GET["settings"] );
		$control->settingsTemplate( $_GET["settings"] );
	}
	elseif( !isset($_GET["p"]) || $_GET["p"] == "phrases" )
	{
		include( "template/search.php" );
		$control->listSource(0,4,3);
	}
	else if( $_GET["p"] == "about" )
	{
		include( "template/about.php" );
	}
	else if( $_GET["p"] == "phrase" )
	{
		if ( isset( $_GET["hash"] ) )
		{
			$control->showSource($_GET["hash"], $control->registry->getSetting( "numTran" ));
		}
	}
	else if( $_GET["p"] == "translator" )
	{
		if ( isset( $_GET["numPage"] ) )
		{
			include( "template/register.template.php" );
			register_template( $control );
			
			$control->getTranslator( $_GET["numPage"] );
		}
		if ( isset( $_GET["profile"] ) )
		{
			$control->getTranslatorProfile( $_GET["profile"] );
		}
		
		//registration
		if ( isset( $_POST["email"] ) )
		{
			if ( $control->registerTranslator( $_POST["email"] ) )
			{
				$control->setMessage("Registration successful", "Please check your e-mail for further instructions." );
			}
		}
	}
	else if( $_GET["p"] == "search" )
	{
		if ( isset( $_GET["term"] ) )
		{
			include( "template/search.php" );
			$control->listSourceAuthor( $_GET["term"], 0 );
		}
	}
	else if( $_GET["p"] == "score" )
	{
		$control->scoreTemplate();
		
		if ( isset( $_GET["win"] ) && isset( $_GET["lost"] ) )
		{
			if ( !$control->score( $_GET["win"], $_GET["lost"] ) )
			{
				$control->setMessage( $control->registry->getObject( "lang" )->get( $control->lang , "ERROR" ), $control->registry->getObject( "lang" )->get( $control->lang , "Scoring was unsuccessful. Try another." ) );
			}
		}
	}
	
	
	if ( isset( $_GET["flagT"] ) )
	{
		if ( !$control->flagTran( $_GET["flagT"] ) )
		{
			$control->setMessage( $control->registry->getObject( "lang" )->get( $control->lang , "ERROR" ), $control->registry->getObject( "lang" )->get( $control->lang , "Flaging was unsuccessful." ) );
		}
	}
	
	if ( isset( $_GET["flagS"] ) )
	{
		if ( !$control->flagSource( $_GET["flagS"] ) )
		{
			$control->setMessage( $control->registry->getObject( "lang" )->get( $control->lang , "ERROR" ), $control->registry->getObject( "lang" )->get( $control->lang , "Flaging was unsuccessful." ) );
		}
	}
	
	if ( isset( $_GET["set"] ) )
	{
		//c5281db0c6c1a5e0336d1c3345196b39
		$control->setTranslator( "c5281db0c6c1a5e0336d1c3345196b39", "popisek jednoho překladatele", "en", array("fr"), array("en","cs") );
	}
	
	include( "template/".$control->registry->getSetting( "template" )."/foot.template.php" );
	foot_template( $control, $control->message, $templatePath );
	
	exit();
 
?>
