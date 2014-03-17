<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if(!defined('INSIDE')){ die(header ( 'location:../../' ));}

class Debug_Lib extends XGPCore
{
	private $log;
	private $numqueries;
	private $_lang;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		$this->vars 		= $this->log = '';
		$this->numqueries 	= 0;
		$this->_lang		= parent::$lang;
	}

	/**
	 * method dump()
	 * param $var
	 * return dump variable content and location
	 */
	private function dump ( $var )
	{
	    $result	= var_export ( $var , true );
	    $loc 	= whereCalled();

	    return "\n<pre>Dump: $loc\n$result</pre>";
	}

	/**
	 * method where_called()
	 * param $level
	 * return line, object and file name where the query was executed
	 */
	private function where_called ( $level = 1 )
	{
	    $trace 	= debug_backtrace();
	    $file   = $trace[$level]['file'];
	    $line   = $trace[$level]['line'];
	    $object = $trace[$level]['object'];

	    if (is_object($object)) { $object = get_class($object); }

	    $break = Explode('/', $file);
	    $pfile = $break[count($break) - 1];

	    return "Where called: line $line of $object <br/>(in $pfile)";
	}

	/**
	 * method add()
	 * param $query
	 * return add a debug line to the debug table
	 */
	public function add ( $query )
	{
		$this->numqueries++;
		$this->log .= '<tr><th rowspan="2">Query ' . $this->numqueries . ':</th><th>' . $query . '</th></tr><tr><th>' . $this->where_called ( 3 ) . '</th></tr>';
	}

	/**
	 * method echo_log()
	 * param
	 * return print all the debug lines previously added
	 */
	public function echo_log()
	{
		return  '<br><table><tr><td class="k" colspan="2"><a href="' . XGP_ROOT . 'admin.php?page=settings">Debug Log</a>:</td></tr>' . $this->log . '</table>';
	}

	/**
	 * method error()
	 * param $message
	 * $title
	 * return handle errors messages
	 */
	public function error ( $message , $title )
	{
		if ( Functions_Lib::read_config ( 'debug' ) == 1 )
		{
			echo '<h2>'.$title.'</h2><br><font color="red">' . $message . '</font><br><hr>';
			echo $this->echo_log();
			echo $this->where_called ( 3 );
		}
		else
		{
			if ( isset ( parent::$users->get_user_data ) )
			{
				$user_id	= parent::$users->get_user_data();
				$user_id	= $user_id['user_id'];
			}
			else
			{
				$user_id	= 0;
			}

			// format log
			$log	= '|' . $user_id . '|'. $title .'|' . $message . '|' . $this->where_called ( 3 ) . '|';

			// log the error
			$this->write_errors ( $log , "ErrorLog" );

			// notify administrator
			mail ( Functions_Lib::read_config ( 'admin_email' ) , '[DEBUG][' . $title . ']' , $this->where_called ( 3 ) );

			// show page to the user
			echo '<!DOCTYPE html>
					<html lang=en>
					  <meta charset=utf-8>
					  <meta name=viewport content="initial-scale=1, minimum-scale=1, width=device-width">
					  <title>Error 500 (Internal Server Error)</title>
					  <style>
					    *{margin:0;padding:0}html,code{font:15px/22px arial,sans-serif}html{background:#fff;color:#222;padding:15px}body{margin:7% auto 0;max-width:390px;min-height:180px;padding:30px 0 15px}* > p{margin:11px 0 22px;overflow:hidden}ins{color:#777;text-decoration:none}a img{border:0}@media screen and (max-width:772px){body{background:none;margin-top:0;max-width:none;padding-right:0}}
					  </style>
					  <a href=//www.google.com/><img src="http://www.xgproyect.net/images/misc/xg-logo.png" alt="XG Proyect"></a>
					  <p><b>500.</b> <ins>That’s an error.</ins>
					  <p>The requested URL throw an error. Contact the game Administrator. <ins>That’s all we know.</ins>
					';
		}

		die();
	}

	/**
	 * method write_errors()
	 * param $Text
	 * $log_file
	 * return write the errors into the log file
	 */
	private function write_errors ( $text , $log_file )
	{
		$file		= XGP_ROOT . LOGS_PATH . $log_file . ".php";

		if ( !file_exists ( $file ) && is_writable ( $file ) )
		{
			@fopen ( $file , "w+" );
			@fclose ( fopen ( $file , "w+" ) );
		}

		$fp		 =	@fopen ( $file , "a" );
		$date	 =	$text;
		$date	.=	date ( Functions_Lib::read_config ( 'date_format_extended' ) , time() ) . "||\n";

		@fwrite ( $fp , $date );
		@fclose ( $fp );
	}
}
/* end of Debug_Lib.php */