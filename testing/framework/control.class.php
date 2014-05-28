<?php

//	LINK
// Source (phrase + all translations)	?p=source&id=[id]
// Score	?p=score&id=[id]

define( "FLAG_LIMIT", 3 );
define( "DELETED_LIMIT", 3 );

define( "BEST_LIMIT", 1500 );

class Control {
	
	public $registry;
	public $languageCode;
	public $lang;
	public $message;
	
	public function __construct()
	{
		require_once("config/dbConfig.php");
		require_once("config/GMConfig.php");
		require_once("config/twitterConfig.php");
		require_once("registry.class.php");
		require_once("lang/languageCodes.php");
		
		$this->registry = Registry::singleton();
		
		// DATABASE
		$this->registry->storeObject( "dbHandle", "db" );
		
		$this->registry->getObject("db")->newConnection( HOST, USERNAME, PASSWORD, DBNAME );
		$this->registry->getObject("db")->set("Source", "Translation", "SourceState", "TranState", "ScoreChange", "Translators", "TranslatorsLang", "TranslatorsSet" );
		
		// TWITTER
		$this->registry->storeObject( "twitterHandle", "twitter" );
		$this->registry->getObject( "twitter" )->set( CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET );
		
		// MAIL
		$this->registry->storeObject( "GmailHandle", "mail" );
		$this->registry->getObject( "mail" )->setUser( GM_USERNAME, GM_PASSWORD );
		
		// LANGUAGE
		$this->registry->storeObject( "language", "lang" );
		
		// MACHINE TRANSLATION
		$this->registry->storeObject( "MTMHandle", "monkey" );
		
		// LANGUAGE CODE
		$this->languageCode = $languageCodes;
		unset( $languageCodes );
		
		$this->message = FALSE;
	}
	
	
	// periodical
	public function pullTweet()
	{
		$count = 0;
		// check database for translated languages
		$from = $this->registry->getObject( "db" )->listLangFrom();
		$to = $this->registry->getObject( "db" )->listLangTo();
		
		$tweets = $this->registry->getObject( "twitter" )->pullTweet();
		
		foreach( $tweets as $tweet )
		{
			$hash = md5( $tweet["text"].$tweet["id"] );
			
			if( !$this->registry->getObject("db")->checkSourceIdOut( $tweet["id"] ) && !$this->registry->getObject("db")->checkSourceHash( $hash ) )
			{
				if( in_array( $tweet["from"], $from ) && in_array( $tweet["to"], $to ) )
				{
					$this->registry->getObject("db")->addSource( $tweet["id"], $tweet["text"], $tweet["from"], $tweet["to"], $tweet["author"], date("Y-m-d H:i:s") );
					
					$this->notifyTranslator( $this->registry->getObject("db")->lastId );
					$this->notifyAuthor( $tweet["author_id"], $this->registry->getObject("db")->lastId );
					$this->machineTranslation( $this->registry->getObject("db")->lastId );
					
					++$count;
				}
			}
		}
		
		return $count;
		
	}
	
	// periodical
	public function pullMail()
	{
		$mail = $this->registry->getObject( "mail" )->get();
		
		$count = 0;
		
		if ( !is_array( $mail ) )
			return FALSE;
			
		foreach( $mail as $letter )
		{
			if( $this->registry->getObject( "db" )->checkSourceHash( $letter["hash"] ) )
			{
				$translator = $this->registry->getObject( "db" )->getTranslatorByMail( $letter["mail"] );
				
				if ( !($translator === FALSE ) )
				{
					$source = $this->registry->getObject( "db" )->getSourceHash( $letter["hash"] );
					
					$this->registry->getObject( "db" )->addTran( $source["ID"], $letter["text"], $translator["ID"], date("Y-m-d H:i:s") );
					++$count;
				}
			}
		}
		return $count;
	}
	
	public function notifyAuthor( $user_id, $id )
	{
		if( !$this->registry->getObject("db")->checkSource( $id ) )
			return FALSE;
		
		$source = $this->registry->getObject("db")->getSource( $id );
		
		$text = $this->registry->getObject( "lang" )->get( $source["langSource"], "We have listed your request for translation." ). "\n"
			. $this->registry->getObject( "lang" )->get( $source["langSource"], "More at:" ). "\n"
			. "http://".SERVER."/phrase/a/".$source["hash"] 
			;
		
		$this->registry->getObject( "twitter" )->privateMessage( $user_id, $text );
	}
	
	public function showSource( $hash, $tranCount )
	{
		$source = $this->registry->getObject("db")->getSourceHash( $hash );
		
		if ( $source === FALSE )
			return FALSE;
		
		require_once( "template/source.template.php" );
		
		source_template( $source, $this );
		$this->listTran( $source["ID"], 0, $tranCount );
		
	}
	
	public function listSource( $start, $count, $tranCount )
	{
		$list = $this->registry->getObject("db")->listSource( $start, $count );
		
		if ( !is_array( $list ) )
			return FALSE;
		
		require_once( "template/source.template.php" );
		
		foreach( $list as $item )
		{
			source_template( $item, $this );
			$this->listTran( $item["ID"], 0, $tranCount );
		}
	}
	
	public function listSourceAuthor( $author, $start )
	{
		$list = $this->registry->getObject("db")->listSourceAuthor( $author, $start, $this->registry->getSetting( "numTranslators" ) );
		
		if ( !is_array( $list ) )
			return FALSE;
		
		require_once( "template/source.template.php" );
		
		foreach( $list as $item )
		{
			$item["numTran"] = $this->registry->getObject("db")->getNumTran( $item["ID"] );
			
			source_template( $item, $this );
		}
	}
	
	public function listTran( $id, $start, $count )
	{
		$list = $this->registry->getObject("db")->listTran( $id, $start, $count );
		
		if ( !is_array( $list ) )
			return FALSE;
		
		require_once( "template/tran.template.php" );
		
		foreach( $list as $item )
		{
			$author = $this->registry->getObject("db")->getTranslator( $item["author"] );
			
			if ( $author === FALSE )
				continue;
				
			$item["IDAuthor"] = $item["author"];
			$item["author"] = $author["name"];
			
			tran_template( $item, $this );
		}
	}
// SCORE ===============================================================
	public function score( $idWin, $idLost )
	{
		$author = md5($_SERVER['HTTP_USER_AGENT'] .  $_SERVER['REMOTE_ADDR']);
		
		$output = $this->registry->getObject("db")->score( $idWin, $idLost, $author );
		
		$tran = $this->registry->getObject("db")->getTranslation( $idWin );
		
		if ( $tran["score"] >= BEST_LIMIT )
			$this->postBest( $tran["IDSource"] );
		return $output;
	}
	
	public function scoreTemplate()
	{
		$source = $this->registry->getObject("db")->listSourceScore();
		if ( $source === FALSE )
			return FALSE;
		
		$key = array_rand( $source );
		$source = $source[$key];
		
		$list = $this->registry->getObject("db")->listTran( $source["ID"], 0, 2 );
		
		if ( $list === FALSE || count( $list ) != 2 )
			return FALSE;
		
		$index = rand(0,1);
		
		$tran1 = $list[$index];
		$tran2 = $list[1-$index];
		
		require_once( "template/score.template.php" );
		score_template( $source, $tran1, $tran2, $this );
	}
	
	public function postBest( $IDSource )
	{
		$source = $this->registry->getObject("db")->getSource( $IDSource );
		$tran = $this->registry->getObject("db")->bestTranslation( $IDSource );
		
		if ( $tran === FALSE || $source === FALSE )
			return FALSE;
		
		$link = "http://".SERVER."/phrase/a/".$source["hash"];
		
		$text = "@".$source["author"]." "
			.$tran["text"]
			." ".$link;
		
		$this->registry->getObject( "twitter" )->pushTweet( $text );
	}
	
	
	// FLAG ============================================================
	public function flagTran( $id )
	{
		$author = md5($_SERVER['HTTP_USER_AGENT'] .  $_SERVER['REMOTE_ADDR']);
		
		$this->registry->getObject( "db" )->flagTran( $id, $author, date("Y-m-d H:i:s") );
		
		if ( $this->registry->getObject( "db" )->countFlagTran( $id ) >= FLAG_LIMIT )
		{
			$this->registry->getObject( "db" )->setFlagTran( $id );
		}
	}
	
	public function deleteTran( $id )
	{
		$this->registry->getObject( "db" )->deleteTran( $id, "[UNDEFINED]", date("Y-m-d H:i:s") );
		
		if ( $this->registry->getObject( "db" )->countDeletedTran( $id ) >= DELETED_LIMIT )
		{
			$this->registry->getObject( "db" )->setDeletedTran( $id );
		}
	}
	
	public function flagSource( $id )
	{
		$author = md5($_SERVER['HTTP_USER_AGENT'] .  $_SERVER['REMOTE_ADDR']);
		
		$this->registry->getObject( "db" )->flagSource( $id, $author, date("Y-m-d H:i:s") );
		
		if ( $this->registry->getObject( "db" )->countFlagSource( $id ) >= FLAG_LIMIT )
		{
			$this->registry->getObject( "db" )->setFlagSource( $id );
		}
	}
	
	public function deleteSource( $id )
	{
		$this->registry->getObject( "db" )->deleteSource( $id, "[UNDEFINED]", date("Y-m-d H:i:s") );
		
		if ( $this->registry->getObject( "db" )->countDeletedSource( $id ) >= DELETED_LIMIT )
		{
			$this->registry->getObject( "db" )->setDeletedSource( $id );
		}
	}
	
	public function machineTranslation( $id )
	{
		// MTMonkey
		if( !$this->registry->getObject("db")->checkSource( $id ) )
			return FALSE;
		
		$source = $this->registry->getObject("db")->getSource( $id );
		
		$translation = $this->registry->getObject( "monkey" )->translate( $source["langSource"], $source["langTarget"], $source["text"] );
		
		if ( !$translation )
			return FALSE;
		
		$this->registry->getObject( "db" )->addTran( $source["ID"], $translation, 3, date("Y-m-d H:i:s") );
		// HERE ==========================================================================================================================
		return TRUE;
	}
	
	
	
	// TRANSLATORS =====================================================
	public function notifyTranslator( $id )
	{
		if( !$this->registry->getObject("db")->checkSource( $id ) )
			return FALSE;
		
		$source = $this->registry->getObject("db")->getSource( $id );
		
		$idFrom = $this->registry->getObject("db")->checkLangFrom( $source["langSource"] );
		$idTo = $this->registry->getObject("db")->checkLangTo( $source["langTarget"] );
		
		if ( !is_array($idFrom) || !is_array($idTo ) )
			return FALSE;
		
		$translators = array_intersect( $idFrom, $idTo );
		
		foreach( $translators as $translator )
		{
			$translator = $this->registry->getObject("db")->getTranslator( $translator );
			
			if ( !filter_var( $translator["mail"], FILTER_VALIDATE_EMAIL ) )
				continue;
			
			$subj = $this->registry->getObject( "lang" )->get( $translator["interLang"], "Request for translation" );
			$from = $this->registry->getSetting( "rftMail" );
			$fromName = $this->registry->getSetting( "rftMailName" );
			
			$text = $this->registry->getObject( "lang" )->get( $translator["interLang"], "Please translate this sentence." )
				."<br>\n"
				.$this->registry->getObject( "lang" )->get( $translator["interLang"], "To language:" )
				.$this->languageCode[strtoupper( $source["langTarget"] )]
				."<br>\n<br>\n"
				.$source["text"]
				."<br>\n<br>\n"
				."ID:".$source["hash"];
			
			$this->registry->getObject( "mail" )->send( $translator["mail"], $subj, $text, $from, $fromName );
		}
		return TRUE;
	}
	
	public function registerTranslator( $mail )
	{
		if ( !( $this->registry->getObject( "db" )->getTranslatorByMail( $mail ) === FALSE ) )
			return FALSE;
		
		$this->registry->getObject( "db" )->addTranslatorByMail( $mail );
		
		$subj = "Translator registration.";
		$from = $this->registry->getSetting( "rftMail" );
		$fromName = $this->registry->getSetting( "rftMailName" );
		
		$translator = $this->registry->getObject( "db" )->lastId;
		
		$salt = uniqid(mt_rand(), true);
		$hash = md5( $salt.$translator );
		
		$this->registry->getObject( "db" )->setReqTranslator( $translator, $hash );
		
		$text = "Thank you for registration as translator on tct.\n<br/> Please confirm your registration by following the following link:\n<br/>\n<br/>"
			."http://".SERVER."/?settings=".$hash ;
		
		$this->registry->getObject( "mail" )->send( $mail, $subj, $text, $from, $fromName );
		return TRUE;
	}
	
	public function setReqTranslator( $mail )
	{
		$translator = $this->registry->getObject( "db" )->getTranslatorByMail( $mail );
		
		if ( ( $translator === FALSE ) )
			return FALSE;
			
		$salt = uniqid(mt_rand(), true);
		
		$hash = md5( $salt.$translator["ID"] );
		
		$this->registry->getObject( "db" )->setReqTranslator( $translator["ID"], $hash );
		
		$subj = $this->registry->getObject( "lang" )->get( $translator["interLang"], "Request for change of settings" );
		$from = $this->registry->getSetting( "rftMail" );
		$fromName = $this->registry->getSetting( "rftMailName" );
		
		$text = $this->registry->getObject( "lang" )->get( $translator["interLang"], "We have received a request for change of settings for this account. You can do so on following link. (Link will expire in 10 minutes.) If you did not request change of settings please ignore this message." )
			."<br>\n"
			."http://".SERVER."/?settings=".$hash 
			;
		
		$this->registry->getObject( "mail" )->send( $translator["mail"], $subj, $text, $from, $fromName );
		return TRUE;
	}
	
	public function setTranslator( $hash, $description, $interLang, $from, $to )
	{
		$id = $this->registry->getObject( "db" )->getTranslatorIdByHash( $hash );
		if ( $id === FALSE )
			return FALSE;
		
		$this->registry->getObject( "db" )->setTranslator( $id, $description, $interLang );
		
		foreach ( $from as $lang )
		{
			if ( $this->registry->getObject( "db" )->checkTranslatorLanguage( $id, $lang, "TRUE" ) )
			{
				$this->registry->getObject( "db" )->addTranslatorLanguage( $id, $lang, "TRUE" );
			}
		}
		
		foreach ( $to as $lang )
		{
			if ( $this->registry->getObject( "db" )->checkTranslatorLanguage( $id, $lang, "FALSE" ) )
			{
				$this->registry->getObject( "db" )->addTranslatorLanguage( $id, $lang, "FALSE" );
			}
		}
	}
	
	public function getTranslator( $numPage )
	{
		$translators = $this->registry->getObject( "db" )->listTranslator( $numPage*$this->registry->getSetting( "numTranslators" ), $this->registry->getSetting( "numTranslators" ) );
		
		if ( $translators === FALSE )
			return FALSE;
		
		require_once( "template/translator.template.php" );
		
		foreach ( $translators as $item )
		{
			translator_template( $item, $this );
		}
	}
	
	public function getTranslatorProfile( $id )
	{
		$translator = $this->registry->getObject( "db" )->getTranslator( $id );
		
		if ( $translator === FALSE )
			return FALSE;
		
		require_once( "template/translator.template.php" );
		
		translator_template( $translator, $this );
	}
	
	public function activateTranslator( $hash )
	{
		$request = $this->registry->getObject( "db" )->getRequest( $hash );
		
		if ( $request === FALSE )
			return FALSE;
			
		$this->registry->getObject( "db" )->activateTranslator( $request["IDTranslator"] );
		
		return TRUE;
	}
	
	public function settingsTemplate( $hash )
	{
		$request = $this->registry->getObject( "db" )->getRequest( $hash );
		
		if ( $request === FALSE )
			return FALSE;
			
		$translator = $this->registry->getObject( "db" )->getTranslator( $request["IDTranslator"] );
		
		require_once( "template/settings.template.php" );
		settings_template( $this, $hash );
	}
	
	// SUPPORT =========================================================
	public function urlClean( $str )
	{
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		
		return $clean;
	}
	
	public function setMessage( $title, $body )
	{
		$this->message = array( "title" => $title, "body" => $body );
	}
}

?>
