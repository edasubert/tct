<?php

class Registry {
	 
	/**
	 * Our array of objects
	 * @access private
	 */
	private static $objects = array();
	
	/**
	 * Our array of events
	 * @access private
	 */
	private static $events = array();
	
	/**
	 * Our array of settings
	 * @access private
	 */
	private static $settings = array();
	  
	/**
	 * The instance of the registry
	 * @access private
	 */
	private static $instance;
	 
	/**
	 * Private constructor to prevent it being created directly
	 * @access private
	 */
	private function __construct()
	{
	 
	}
		 
	/**
	 * singleton method used to access the object
	 * @access public
	 * @return 
	 */
	public static function singleton()
	{
		if( !isset( self::$instance ) )
		{
			$obj = __CLASS__;
			self::$instance = new $obj;
		}
		 
		return self::$instance;
	}
	 
	/**
	 * prevent cloning of the object: issues an E_USER_ERROR if this is attempted
	 */
	public function __clone()
	{
		trigger_error( 'Cloning the registry is not permitted', E_USER_ERROR );
	}
	 
	/**
	 * Stores an object in the registry
	 * @param String $object the name of the object
	 * @param String $key the key for the array
	 * @return void
	 */
	public function storeObject( $object, $key )
	{
		require_once('objects/' . $object . '.class.php');
		self::$objects[ $key ] = new $object( self::$instance );
	}
	 
	/**
	 * Gets an object from the registry
	 * @param String $key the array key
	 * @return object
	 */
	public function getObject( $key )
	{
		if( is_object ( self::$objects[ $key ] ) )
		{
			return self::$objects[ $key ];
		}
	}
	 
	/**
	 * Stores settings in the registry
	 * @param String $data
	 * @param String $key the key for the array
	 * @return void
	 */
	public function storeSetting( $data, $key )
	{
		self::$settings[ $key ] = $data;
	}
	
	/**
	 * Gets a setting from the registry
	 * @param String $key the key in the array
	 * @return void
	 */
	public function getSetting( $key )
	{
		return self::$settings[ $key ];
	}
	
	/**
	 * Stores an event in the registry
	 * @param Function $function variable function
	 * @param String $name the key for the array
	 * @return void
	 */
	public function storeEvent( $function, $name )
	{
		if ( !is_callable(  $function ) )
			trigger_error( 'Event '.$name.' function not callable', E_USER_ERROR );
			
			
		if ( !isset( self::$events[ $name ] ) || !is_array( self::$events[ $name ] ) )
		{
			self::$events[ $name ] = array();
		}
		
		array_push( self::$events[ $name ], $function );
	}
	
	/**
	 * Stores an array of events in the registry
	 * @param array
	 * @return void
	 */
	public function storeArrayEvent( $array )
	{
		if ( !is_callable(  $function ) )
			trigger_error( 'Event '.$name.' function not callable', E_USER_ERROR );
			
			
		if ( !isset( self::$events[ $name ] ) || !is_array( self::$events[ $name ] ) )
		{
			self::$events[ $name ] = array();
		}
		
		array_push( self::$events[ $name ], $function );
	}
	
	/**
	 * Triggers an event
	 * @access public
	 * @param String $name
	 * @return
	 */
	public static function triggerEvent( $name )
	{
		if( !isset( self::$instance ) )
			trigger_error( 'Event trigged before initialization of registry', E_USER_ERROR );
		
		if( isset( self::$events[ $name ] ) && is_array( self::$events[ $name ] ) )
		{
			foreach( self::$events[ $name ] as $event )
			{
				$event( self::$instance );
			}
		}
		
	}
}
 
?>
