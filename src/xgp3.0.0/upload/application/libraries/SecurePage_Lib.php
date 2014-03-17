<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

 // This class is originally developed by "Bendikt Martin Myklebust" this is updated by "Rakesh Chandel".
 //To Secure Global varaible values consisting in array while posting from GET,Session, and POST.

/**
 * SecurePage by Jstar (xgproyect.net)
 * This class implement a security controll against SQL-injection and xss
 *
 * --Usage--
 * in the main page of your application:
 * require('SecurePage_Lib.php');
 * SecurePage::run();
 *
 * */

class SecurePage_Lib
{
   private static $instance = null;

	/**
	 * __construct()
	 */
	public function __construct()
	{
	   //apply controller to all
	   $_GET 		= array_map ( array ( $this , 'validate' ) , $_GET );
	   $_POST 		= array_map ( array ( $this , 'validate' ) , $_POST );
	   $_REQUEST 	= array_map ( array ( $this , 'validate' ) , $_REQUEST );
	   $_SERVER 	= array_map ( array ( $this , 'validate' ) , $_SERVER );
	   $_COOKIE 	= array_map ( array ( $this , 'validate' ) , $_COOKIE );
	}

	/**
	 * method validate
	 * param $value
	 * return
	 */
   private function validate ( $value )
   {
      if ( ! is_array ( $value ) )
      {
         $value = str_ireplace ( "script" , "blocked" , $value );
         $value = ( get_magic_quotes_gpc() ) ? htmlentities ( stripslashes ( $value ) , ENT_QUOTES , 'UTF-8' , FALSE ) : htmlentities ( $value , ENT_QUOTES , 'UTF-8' , FALSE );
         //$value = mysql_real_escape_string ( $value );
      }
      else
      {
         $c	= 0;

		 foreach ( $value as $val )
         {
            $value[$c]	= $this->validate ( $val );
            $c++;
         }
      }
      return $value;
   }

	/**
	 * method run
	 * param
	 * return
	 */
   public static function run()
   {
      if ( self::$instance == null )
      {
         $c	= __CLASS__;
         self::$instance = new $c();
      }

   }
}
/* end of SecurePage_Lib.php */