<?php
	if ( ! defined( 'PCAFW' ) ) 
	{
		echo 'This file can only be called via the main index.php file, and not directly';
		exit();
	}

class language
{
	private $registry;
	private $phrases;
	
	public function __construct( &$instance )
	{
		$this->registry = $instance;
		$this->phrases = array();
	}
	
	public function get( $lang, $phrase )
	{
		if ( isset( $this->phrases[$lang][$phrase] ) )
		{
			return $this->phrases[$lang][$phrase];
		}
		if ( isset( $this->phrases["en"][$phrase] ) )
		{
			return $this->phrases["en"][$phrase];
		}
		return FALSE;
	}
	
	public function add( $lang, $phrase, $translation )
	{
		$this->phrases[$lang][$phrase] = $translation;
		return TRUE;
	}
	
	public function addArray( $lang, $array )
	{
		
		if ( !isset( $this->phrases[$lang] ) || !is_array( $this->phrases[$lang] ) )
		{
			$this->phrases[$lang] = array();
		}
		
		if ( is_array( $array ) )
		{
			$this->phrases[$lang] = array_merge( $this->phrases[$lang], $array );
			return TRUE;
		}
		return FALSE;
	}
	
	public function load( $lang )
	{
		require_once( "lang/".$lang.".lang.php" );
		
		if ( !isset( $this->phrases[$lang] ) || !is_array( $this->phrases[$lang] ) )
		{
			$this->phrases[$lang] = array();
		}
		
		if ( is_array( $$lang ) )
		{
			$this->phrases[$lang] = array_merge( $this->phrases[$lang], $$lang );
			return TRUE;
		}
		return FALSE;
	}
}
?>
