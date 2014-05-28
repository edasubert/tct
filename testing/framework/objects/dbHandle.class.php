<?php
	if ( ! defined( 'PCAFW' ) ) 
	{
		echo 'This file can only be called via the main index.php file, and not directly';
		exit();
	}

	require_once( "database.class.php" );
	
class dbHandle extends database
{
// SUPPORT =============================================================
	private $registry;
	
	public $lastId;
	
	private $sourceTable;
	private $tranTable;
	private $sourceStateTable;
	private $tranStateTable;
	private $scoreChangeTable;
	private $translatorsTable;
	private $translatorsLangTable;
	private $translatorsSetTable;
	
	public function set( $sourceTable, $tranTable, $sourceStateTable, $tranStateTable, $scoreChangeTable, $translatorsTable, $translatorsLangTable, $translatorsSetTable )
	{
		$this->sourceTable = $sourceTable;
		$this->tranTable = $tranTable;
		$this->sourceStateTable = $sourceStateTable;
		$this->tranStateTable = $tranStateTable;
		$this->scoreChangeTable = $scoreChangeTable;
		$this->translatorsTable = $translatorsTable;
		$this->translatorsLangTable = $translatorsLangTable;
		$this->translatorsSetTable = $translatorsSetTable;
		
		return TRUE;
	}
	
	public function __construct( &$instance )
	{
		$this->registry = $instance;
	}

	private function checkTran( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->tranTable." WHERE ID = '".$id."' AND flag = '0' AND deleted = '0'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return TRUE;
	}
	
	public function checkSource( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->sourceTable." WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return TRUE;
	}
	
	public function checkSourceIdOut( $idOut )
	{
		$id = $this->sanitizeData( $idOut );
		
		$query = "SELECT * FROM ".$this->sourceTable." WHERE IDOut = '".$id."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return TRUE;
	}
	
	public function checkSourceHash( $hash )
	{
		$hash = $this->sanitizeData( $hash );
		
		$query = "SELECT * FROM ".$this->sourceTable." WHERE hash = '".$hash."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return TRUE;
	}
	
	public function checkTranslator( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->translatorsTable." WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return TRUE;
	}
	
// ADDITIONS ===========================================================
	public function addSource( $idOut, $text, $langSource, $langTarget, $author, $timestamp )
	{
		$idOut = $this->sanitizeData( $idOut );
		$text = $this->sanitizeData( $text );
		$langSource = $this->sanitizeData( $langSource );
		$langTarget = $this->sanitizeData( $langTarget );
		$author = $this->sanitizeData( $author );
		$timestamp = $this->sanitizeData( $timestamp );
		
		$hash = md5( $text.$idOut );
		
		$query = "INSERT INTO ".$this->sourceTable." ( IDOut, text, langSource, langTarget, author, timestamp, hash ) VALUES ( '".$idOut."', '".$text."', '".$langSource."', '".$langTarget."', '".$author."', '".$timestamp."', '".$hash."' )";
		
		$this->executeQuery( $query );
		
		$this->lastId = $this->insertId();
		return TRUE;
	}
	
	public function addTran( $idSource, $text, $author, $timestamp )
	{
		$text = $this->sanitizeData( $text );
		$author = $this->sanitizeData( $author );
		$idSource = $this->sanitizeData( $idSource );
		$timestamp = $this->sanitizeData( $timestamp );
		
		if ( !$this->checkSource( $idSource ) )
			trigger_error( 'Source with id '.$idSource.' does not exist.', E_USER_ERROR );
		
		$query = "INSERT INTO ".$this->tranTable." ( IDSource, text, author, timestamp ) VALUES ( '".$idSource."', '".$text."', '".$author."', '".$timestamp."' )";
		$this->executeQuery( $query );
		
		$query = "UPDATE ".$this->sourceTable." SET numTran = numTran + 1 WHERE ID = '".$idSource."'";
		$this->executeQuery( $query );
		
		$this->lastId = $this->insertId();
		return TRUE;
	}
	
// GETTERS =============================================================

	public function getSource( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->sourceTable." WHERE ID = '".$id."' AND flag = '0' AND deleted = '0'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return $this->getRows();
	}
	
	public function getSourceHash( $hash )
	{
		$hash = $this->sanitizeData( $hash );
		
		$query = "SELECT * FROM ".$this->sourceTable." WHERE hash = '".$hash."' AND flag = '0' AND deleted = '0'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return $this->getRows();
	}
	
	public function getTranslation( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->tranTable." WHERE ID = '".$id."' AND flag = '0' AND deleted = '0'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() == 0 )
			return FALSE;
		
		return $this->getRows();
	}
	
	public function listSource( $start, $count )
	{
		$start = $this->sanitizeData( $start );
		$count = $this->sanitizeData( $count );
		
		$query = "SELECT * FROM ".$this->sourceTable." WHERE flag = '0' AND deleted = '0' ORDER BY timestamp DESC LIMIT ".$start.",".$count."";
		$this->executeQuery( $query );
		
		if ( $this->numRows() < 1 )
			return FALSE;
		
		$output = array();
		while ( $row = $this->getRows() )
		{
			array_push( $output, $row );
		}
		
		return $output;
	}
	
	public function listSourceAuthor( $author, $start, $count )
	{
		$start = $this->sanitizeData( $start );
		$count = $this->sanitizeData( $count );
		$author = $this->sanitizeData( $author );
		
		$query = "SELECT * FROM ".$this->sourceTable." WHERE author = '".$author."' AND flag = '0' AND deleted = '0' ORDER BY timestamp DESC LIMIT ".$start.",".$count."";
		$this->executeQuery( $query );
		
		if ( $this->numRows() < 1 )
			return FALSE;
		
		$output = array();
		while ( $row = $this->getRows() )
		{
			array_push( $output, $row );
		}
		
		return $output;
	}
	
	public function listSourceScore( )
	{
		$query = "SELECT * FROM ".$this->sourceTable." WHERE numTran > 1 AND flag = '0' AND deleted = '0' ORDER BY timestamp";
		$this->executeQuery( $query );
		
		if ( $this->numRows() < 1 )
			return FALSE;
		
		$output = array();
		while ( $row = $this->getRows() )
		{
			array_push( $output, $row );
		}
		
		return $output;
	}
	
	public function listTran( $id, $start, $count )
	{
		$id = $this->sanitizeData( $id );
		$start = $this->sanitizeData( $start );
		$count = $this->sanitizeData( $count );
		
		$query = "SELECT * FROM ".$this->tranTable." WHERE IDSource = '".$id."' AND flag = '0' AND deleted = '0' ORDER BY score DESC LIMIT ".$start.",".$count."";
		$this->executeQuery( $query );
		
		if ( $this->numRows() < 1 )
			return FALSE;
			
		$output = array();
		while ( $row = $this->getRows() )
		{
			array_push( $output, $row );
		}
		
		return $output;
	}

// CHANGE STATE ========================================================
	public function setDeletedSource( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->tranTable." SET deleted = '1' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function unsetDeletedSource( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->tranTable." SET deleted = '0' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function setFlagSource( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->tranTable." SET flag = '1' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function unsetFlagSource( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->tranTable." SET flag = '0' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	
	public function setDeletedTran( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->tranTable." SET deleted = '1' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function unsetDeletedTran( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->tranTable." SET deleted = '0' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function setFlagTran( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->tranTable." SET flag = '1' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	
	public function unsetFlagTran( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->tranTable." SET flag = '0' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	
	public function deleteTran( $idTran, $author )
	{
		$author = $this->sanitizeData( $author );
		$idTran = $this->sanitizeData( $idTran );
		
		if ( !$this->checkTran( $idTran ) )
			return FALSE;
		
		$query = "SELECT * FROM ".$this->tranStateTable." WHERE IDTran = '".$idTran."'  AND author = '".$author."' AND action = b'01'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 0 )
			return FALSE;
		
		$query = "INSERT INTO ".$this->tranStateTable." ( IDTran, author, action ) VALUES ( '".$idTran."', '".$author."', b'01' )";
		
		$this->executeQuery( $query );
		
		$this->lastId = $idTran;
		return TRUE;
	}
	
	public function flagTran( $idTran, $author )
	{
		$author = $this->sanitizeData( $author );
		$idTran = $this->sanitizeData( $idTran );
		
		if ( !$this->checkTran( $idTran ) )
			return FALSE;
		
		$query = "SELECT * FROM ".$this->tranStateTable." WHERE IDTran = '".$idTran."'  AND author = '".$author."' AND action = b'10'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 0 )
			return FALSE;
		
		$query = "INSERT INTO ".$this->tranStateTable." ( IDTran, author, action ) VALUES ( '".$idTran."', '".$author."', b'10' )";
		
		$this->executeQuery( $query );
		
		$this->lastId = $idTran;
		return TRUE;
	}
	
	
	public function deleteSource( $idSource, $author )
	{
		$author = $this->sanitizeData( $author );
		$idSource = $this->sanitizeData( $idSource );
		
		if ( !$this->checkSource( $idSource ) )
			return FALSE;
		
		$query = "SELECT * FROM ".$this->sourceStateTable." WHERE IDSource = '".$idSource."'  AND author = '".$author."' AND action = b'01'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 0 )
			return FALSE;
		
		$query = "INSERT INTO ".$this->sourceStateTable." ( IDSource, author, action ) VALUES ( '".$idSource."', '".$author."', b'01' )";
		
		$this->executeQuery( $query );
		
		$this->lastId = $idSource;
		return TRUE;
	}
	
	public function flagSource( $idSource, $author )
	{
		$author = $this->sanitizeData( $author );
		$idSource = $this->sanitizeData( $idSource );
		
		if ( !$this->checkSource( $idSource ) )
			return FALSE;
			
		$query = "SELECT * FROM ".$this->sourceStateTable." WHERE IDSource = '".$idSource."'  AND author = '".$author."' AND action = b'10'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 0 )
			return FALSE;
		
		$query = "INSERT INTO ".$this->sourceStateTable." ( IDSource, author, action ) VALUES ( '".$idSource."', '".$author."', b'10' )";
		
		$this->executeQuery( $query );
		
		$this->lastId = $idSource;
		return TRUE;
	}
	
	public function countDeletedTran( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->tranStateTable." WHERE IDTran = '".$id."' AND action = b'01'";
		$this->executeQuery( $query );
		
		return $this->numRows();
	}
	public function countFlagTran( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->tranStateTable." WHERE IDTran = '".$id."' AND action = b'10'";
		$this->executeQuery( $query );
		
		return $this->numRows();
	}
	public function countDeletedSource( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->sourceStateTable." WHERE IDSource = '".$id."' AND action = b'01'";
		$this->executeQuery( $query );
		
		return $this->numRows();
	}
	public function countFlagSource( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->sourceStateTable." WHERE IDSource = '".$id."' AND action = b'10'";
		$this->executeQuery( $query );
		
		return $this->numRows();
	}
	
// SCORE ===============================================================
	public function getScore( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT score FROM ".$this->tranTable." WHERE ID = '".$id."' AND flag = '0' AND deleted = '0'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return $this->getRows()["score"];
	}
	
	public function setScore( $id, $score )
	{
		$id = $this->sanitizeData( $id );
		$score = $this->sanitizeData( $score );
		
		$query = "UPDATE ".$this->tranTable." SET score = '".$score."' WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		$this->lastId = $id;
		return TRUE;
	}
	
	public function score( $idWin, $idLost, $author )
	{
		$idWin = $this->sanitizeData( $idWin );
		$idLost = $this->sanitizeData( $idLost );
		
		$scoreWin  = $this->getScore( $idWin );
		$scoreLost = $this->getScore( $idLost );
		
		if ( !$scoreWin || !$scoreLost )
			return FALSE;
			
		$query = "SELECT * FROM ".$this->scoreChangeTable." WHERE IDTranWin = '".$idWin."'  AND IDTranLost = '".$idLost."' AND author = '".$author."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 0 )
			return FALSE;
		
		//new scores - Elo rating system
		$scoreWin += 32*(1 - 1/(1 + pow( 10, ( $scoreLost -  $scoreWin )/400 ) ) );
		$scoreLost+= 32*(0 - 1/(1 + pow( 10, ( $scoreWin  - $scoreLost )/400 ) ) );
		
		$this->setScore( $idWin , $scoreWin );
		$this->setScore( $idLost, $scoreLost );
		
		$query = "INSERT INTO ".$this->scoreChangeTable." ( IDTranWin, IDTranLost, ScoreTranWin, ScoreTranLost, author ) VALUES ( '".$idWin."', '".$idLost."', '".$scoreWin."', '".$scoreLost."', '".$author."')";
		
		$this->executeQuery( $query );
		
		$this->lastId = [$idWin, $idLost];
		return TRUE;
	}
	
	
	public function bestTranslation( $idSource )
	{
		$id = $this->sanitizeData( $idSource );
		
		$query = "SELECT * FROM ".$this->tranTable." WHERE IDSource = '".$id."'  AND flag = '0' AND deleted = '0' ORDER BY score DESC;";
		$this->executeQuery( $query );
		
		if ( $this->numRows() <= 0 )
			return FALSE;
			
		return $this->getRows();
	}
	
// TRANSLATORS =========================================================
	public function addTranslator( $mail, $name, $interLang, $description )
	{
		$mail = $this->sanitizeData( $mail );
		$name = $this->sanitizeData( $name );
		$interLang = $this->sanitizeData( $interLang );
		$description = $this->sanitizeData( $description );
		
		$query = "INSERT INTO ".$this->translatorsTable." ( mail, name, interLang, description ) VALUES ( '".$mail."', '".$name."', '".$interLang."', '".$description."' )";
		
		$this->executeQuery( $query );
		
		$this->lastId = $this->insertId();
		
		return TRUE;
	}
	
	public function addTranslatorByMail( $mail )
	{
		$mail = $this->sanitizeData( $mail );
		
		$query = "INSERT INTO ".$this->translatorsTable." ( mail ) VALUES ( '".$mail."' )";
		
		$this->executeQuery( $query );
		
		$this->lastId = $this->insertId();
		
		return TRUE;
	}
	
	public function getTranslator( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT * FROM ".$this->translatorsTable." WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		$output = $this->getRows();
		
			$query = "SELECT * FROM ".$this->translatorsLangTable." WHERE IDTranslator = '".$output["ID"]."' ORDER BY lang";
			$this->executeQuery( $query );
			
			$output["from"] = array();
			$output["to"] = array();
			
			while ( $row = $this->getRows() )
			{
				if ( $row["fromLang"] )
				{
					array_push( $output["from"], $row["lang"] );
				}
				else
				{
					array_push( $output["to"], $row["lang"] );
				}
			}
		
		return $output;
	}
	
	public function getTranslatorByMail( $mail )
	{
		$mail = $this->sanitizeData( $mail );
		
		$query = "SELECT * FROM ".$this->translatorsTable." WHERE mail = '".$mail."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return $this->getRows();
	}
	
	public function getTranslatorIdByHash( $hash )
	{
		$hash = $this->sanitizeData( $hash );
		
		$query = "SELECT * FROM ".$this->translatorsSetTable." WHERE hash = '".$hash."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return $this->getRows()["IDTranslator"];
	}
	
	public function listTranslator( $start, $count )
	{
		$start = $this->sanitizeData( $start );
		$count = $this->sanitizeData( $count );
		
		$query = "SELECT * FROM ".$this->translatorsTable." WHERE activated = '". TRUE ."' AND disabled = '". FALSE ."' ORDER BY name LIMIT ".$start.",".$count."";
		$this->executeQuery( $query );
		
		if ( $this->numRows() < 1 )
			return FALSE;
		
		$output = array();
		while ( $row = $this->getRows() )
		{
			array_push( $output, $row );
		}
		
		foreach ( $output as $key => $translator )
		{
			$query = "SELECT * FROM ".$this->translatorsLangTable." WHERE IDTranslator = '".$translator["ID"]."' ORDER BY lang";
			$this->executeQuery( $query );
			
			$output[$key]["from"] = array();
			$output[$key]["to"] = array();
			
			while ( $row = $this->getRows() )
			{
				if ( $row["fromLang"] )
				{
					array_push( $output[$key]["from"], $row["lang"] );
				}
				else
				{
					array_push( $output[$key]["to"], $row["lang"] );
				}
			}
		}
		
		return $output;
	}
	
	public function setReqTranslator( $id, $hash )
	{
		$id = $this->sanitizeData( $id );
		$hash = $this->sanitizeData( $hash );
		
		$query = "REPLACE INTO ".$this->translatorsSetTable." ( IDTranslator, hash ) VALUES ( '".$id."', '".$hash."' )";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return $this->getRows();
	}
	
	public function setTranslator( $id, $description, $interLang )
	{
		$id = $this->sanitizeData( $id );
		$description = $this->sanitizeData( $description );
		$interLang = $this->sanitizeData( $interLang );
		
		$query = "UPDATE ".$this->translatorsTable." SET description = '".$description."', interLang = '".$interLang."' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function addTranslatorLanguage( $id, $lang, $fromLang )
	{
		$id = $this->sanitizeData( $id );
		$lang = $this->sanitizeData( $lang );
		$fromLang = $this->sanitizeData( $fromLang );
		
		if( !$this->checkTranslator( $id ) )
			return FALSE;
		
		$query = "INSERT INTO ".$this->translatorsLangTable." ( IDTranslator, fromLang, lang ) VALUES ( '".$id."', '".$fromLang."', '".$lang."' )";
		
		$this->executeQuery( $query );
		
		$this->lastId = $this->insertId();
		
		return TRUE;
	}
	
	public function checkTranslatorLanguage( $id, $lang, $fromLang )
	{
		$id = $this->sanitizeData( $id );
		$lang = $this->sanitizeData( $lang );
		$fromLang = $this->sanitizeData( $fromLang );
		
		if( !$this->checkTranslator( $id ) )
			return FALSE;
		
		$query = "SELECT * FROM ".$this->translatorsLangTable." WHERE lang = '".$lang."' AND fromLang = '". $fromLang ."'";
		
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return TRUE;
	}
	
	public function checkLangTo( $lang )
	{
		$lang = $this->sanitizeData( $lang );
		
		$query = "SELECT a.* FROM ".$this->translatorsLangTable." a, ".$this->translatorsTable." b WHERE a.lang = '".$lang."' AND a.fromLang = '". FALSE ."' AND a.IDTranslator = b.ID AND b.activated = '". TRUE ."' AND b.disabled = '". FALSE ."' AND b.vacation = '". FALSE ."'";
		
		$this->executeQuery( $query );
		
		if ( $this->numRows() == 0 )
			return FALSE;
		
		$ids = array();
		while( $row = $this->getRows() )
		{
			array_push( $ids, $row["IDTranslator"] );
		}
		return $ids;
	}
	
	public function checkLangFrom( $lang )
	{
		$lang = $this->sanitizeData( $lang );
		
		$query = "SELECT a.* FROM ".$this->translatorsLangTable." a, ".$this->translatorsTable." b WHERE a.lang = '".$lang."' AND a.fromLang = '". TRUE ."' AND a.IDTranslator = b.ID AND b.activated = '". TRUE ."' AND b.disabled = '". FALSE ."' AND b.vacation = '". FALSE ."'";
		
		$this->executeQuery( $query );
		
		if ( $this->numRows() == 0 )
			return FALSE;
		
		$ids = array();
		while( $row = $this->getRows() )
		{
			array_push( $ids, $row["IDTranslator"] );
		}
		return $ids;
	}
	
	public function listLangTo(  )
	{
		$query = "SELECT a.lang FROM ".$this->translatorsLangTable." a, ".$this->translatorsTable." b WHERE a.fromLang = '". FALSE ."' AND a.IDTranslator = b.ID AND b.activated = '". TRUE ."' AND b.disabled = '". FALSE ."' AND b.vacation = '". FALSE ."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() == 0 )
			return FALSE;
		
		$lang = array();
		while( $row = $this->getRows() )
		{
			array_push( $lang, $row["lang"] );
		}
		return $lang;
	}
	
	public function listLangFrom(  )
	{
		$query = "SELECT a.lang FROM ".$this->translatorsLangTable." a, ".$this->translatorsTable." b WHERE a.fromLang = '". TRUE ."' AND a.IDTranslator = b.ID AND b.activated = '". TRUE ."' AND b.disabled = '". FALSE ."' AND b.vacation = '". FALSE ."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() == 0 )
			return FALSE;
		
		$lang = array();
		while( $row = $this->getRows() )
		{
			array_push( $lang, $row["lang"] );
		}
		return $lang;
	}
	
	public function getRequest( $hash )
	{
		$hash = $this->sanitizeData( $hash );
		
		$query = "SELECT * FROM ".$this->translatorsSetTable." WHERE hash = '".$hash."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return $this->getRows();
	}
	
	public function activateTranslator( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->translatorsTable." SET activated = '".TRUE."' WHERE ID = '".$id."'";
		
		return $this->executeQuery( $query );
	}
	
	public function disableTranslator( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->translatorsTable." SET disabled = '".TRUE."' WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		return TRUE;
	}
	
	public function enableTranslator( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->translatorsTable." SET disabled = '".FALSE."' WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		return TRUE;
	}
	
	public function startVacationTranslator( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->translatorsTable." SET vacation = '".TRUE."' WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		return TRUE;
	}
	
	public function endVacationTranslator( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE ".$this->translatorsTable." SET vacation = '".FALSE."' WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		return TRUE;
	}
	
	
}
?>
