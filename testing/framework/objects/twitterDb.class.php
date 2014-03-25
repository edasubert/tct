<?php
	require_once( "database.class.php" );
	
class twitterDb extends database
{
// ADDITIONS ===========================================================
	public function addSource( $text, $langSource, $langTarget, $author, $timestamp )
	{
		$text = $this->sanitizeData( $text );
		$langSource = $this->sanitizeData( $langSource );
		$langTarget = $this->sanitizeData( $langTarget );
		$author = $this->sanitizeData( $author );
		$timestamp = $this->sanitizeData( $timestamp );
		
		$query = "INSERT INTO Source ( text, langSource, langTarget, author, date ) VALUES ( '".$text."', '".$langSource."', '".$langTarget."', '".$author."', '".$timestamp."' )";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function addTranslation( $idSource, $text, $author, $timestamp )
	{
		$text = $this->sanitizeData( $text );
		$author = $this->sanitizeData( $author );
		$idSource = $this->sanitizeData( $idSource );
		$timestamp = $this->sanitizeData( $timestamp );
		
		//check for idSource
		
		$query = "INSERT INTO Translation ( IDSource, text, author, date ) VALUES ( '".$idSource."', '".$text."', '".$author."', '".$timestamp."' )";
		
		$this->executeQuery( $query );
		return TRUE;
	}

// CHANGE STATE ========================================================
	public function setDeleted( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE Translation SET deleted = '1' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function unsetDeleted( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE Translation SET deleted = '0' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function setFlag( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE Translation SET flag = '1' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
	public function unsetFlag( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "UPDATE Translation SET flag = '0' WHERE ID = '".$id."'";
		
		$this->executeQuery( $query );
		return TRUE;
	}
	
// SCORE ===============================================================
	public function getScore( $id )
	{
		$id = $this->sanitizeData( $id );
		
		$query = "SELECT score FROM Translation WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		if ( $this->numRows() != 1 )
			return FALSE;
		
		return $this->getRows()["score"];
	}
	
	public function setScore( $id, $score )
	{
		$id = $this->sanitizeData( $id );
		$score = $this->sanitizeData( $score );
		
		$query = "UPDATE Translation SET score = '".$score."' WHERE ID = '".$id."'";
		$this->executeQuery( $query );
		
		return TRUE;
	}
	
	public function score( $idWin, $idLost )
	{
		$idWin = $this->sanitizeData( $idWin );
		$idLost = $this->sanitizeData( $idLost );
		
		$scoreWin  = $this->getScore( $idWin );
		$scoreLost = $this->getScore( $idLost );
		
		if ( !$scoreWin || !$scoreLost )
			return FALSE;
		
		//new scores - Elo rating system
		$scoreWin += 32*(1 - 1/(1 + pow( 10, ( $scoreLost -  $scoreWin )/400 ) ) );
		$scoreLost+= 32*(0 - 1/(1 + pow( 10, ( $scoreWin  - $scoreLost )/400 ) ) );
		
		$this->setScore( $idWin , $scoreWin );
		$this->setScore( $idLost, $scoreLost );
		
		return TRUE;
	}
	
}
?>
