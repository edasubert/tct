<?php
//http://cuni1-khresmoi.ms.mff.cuni.cz:8080/khresmoi?action=translate&sourceLang=en&targetLang=cs&text=%22test%20of%20translation%22

	if ( ! defined( 'PCAFW' ) ) 
	{
		echo 'This file can only be called via the main index.php file, and not directly';
		exit();
	}
	
class MTMHandle
{
// SUPPORT =============================================================
	private $registry;
		
	public function __construct( &$instance )
	{
		$this->registry = $instance;
	}
	
// TRANSLATE ===========================================================
	public function translate( $sourceLang, $targetLang, $text )
	{
		$input = array(
			"action" 		=> "translate",
			"sourceLang" 	=> urlencode( $sourceLang ),
			"targetLang" 	=> urlencode( $targetLang ),
			"text"			=> urlencode( $text ),
			"alignmentInfo"	=> "false"
			);
		$request = "http://cuni1-khresmoi.ms.mff.cuni.cz:8080/khresmoi";
		$sep = "?";
		
		foreach ( $input as $key => $value )
		{
			$request.= $sep.$key."=".$value;
			$sep = "&";
		}
		
		$data = file_get_contents( $request );
		$data = json_decode( $data, TRUE );
		
		if ( $data["errorMessage"] != "OK" )
			return FALSE;
		
		return $data["translation"][0]["translated"][0]["text"];
	}
	
}
?>
