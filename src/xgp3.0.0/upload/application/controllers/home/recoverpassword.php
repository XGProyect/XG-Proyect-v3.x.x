<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Recoverpassword extends XGPCore
{
	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		$this->build_page();
	}

	/**
	 * method __destruct
	 * param
	 * return close db connection
	 */
	public function __destruct()
	{
		parent::$db->close_connection();
	}

	/**
	 * method build_page
	 * param
	 * return main method, loads everything
	 */
	private function build_page()
	{
		$lang	= parent::$lang;
		$parse 	= $lang;

		if ( $_POST )
		{
			$this->process_request ( $_POST['email'] );
			Functions_Lib::message ( $lang['mail_sended'] , "./" , 2 , FALSE , FALSE );
		}
		else
		{
			$parse['year']		   = date ( 'Y' );
			$parse['version']	   = VERSION;
			$parse['forum_url']    = Functions_Lib::read_config ( 'forum_url' );
			parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'home/lostpassword' ) , $parse ) , FALSE , '' , FALSE );
		}
	}

	/**
	 * generate_password()
	 * param
	 * return generates a password
	**/
	private function generate_password()
	{
		$characters	= "aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890";
		$count		= strlen ( $characters );
		$new_pass	= "";
		$lenght		= 6;
		srand ( ( double)microtime() * 1000000 );

		for ( $i = 0 ; $i < $lenght ; $i++ )
		{
			$character_boucle	= mt_rand ( 0 , $count - 1 );
			$new_pass			= $new_pass . substr ( $characters , $character_boucle , 1 );
		}

		return $new_pass;
	}

	/**
	 * process_request()
	 * param $mail
	 * return process the user request for a new password
	**/
	private function process_request ( $mail )
	{
		$lang		= parent::$lang;
		$ExistMail 	= parent::$db->query_fetch	( "SELECT `user_name`
										 			FROM " . USERS . "
										 			WHERE `user_email` = '" . parent::$db->escape_value ( $mail ) . "' LIMIT 1;" );

		if ( empty ( $ExistMail['user_name'] ) )
		{
			Functions_Lib::message ( $lang['mail_not_exist'] , "index.php?page=recoverpassword" , 2 , FALSE , FALSE );
		}
		else
		{
			$new_password	= $this->send_pass_email ( $mail , $ExistMail['user_name'] );

			parent::$db->query ( "UPDATE " . USERS . " SET
									`user_password` ='". sha1 ( $new_password ) ."'
									WHERE `user_email`='". parent::$db->escape_value ( $mail ) ."' LIMIT 1;" );
		}
	}

	/**
	 * send_pass_email()
	 * param1 $emailaddress
	 * param2 $UserName
	 * return prepare the email and return mail status, delivered or not
	**/
	private function send_pass_email ( $emailaddress , $UserName )
	{
		$lang							= parent::$lang;
		$game_name						= Functions_Lib::read_config ( 'game_name' );

		$parse							= $lang;
		$parse['user_name']				= $UserName;
		$parse['user_pass']				= $this->generate_password();
		$parse['game_url']				= GAMEURL;
		$parse['reg_mail_text_part1']	= str_replace ( '%s' , $game_name , $lang['reg_mail_text_part1'] );
		$parse['reg_mail_text_part7']	= str_replace ( '%s' , $game_name , $lang['reg_mail_text_part7'] );

		$email 							= parent::$page->parse_template (  parent::$page->get_template ( 'home/recover_password_email_template' ) , $parse );
		$status 						= $this->send_mail ( $emailaddress , $lang['mail_title'] , $email );

		return $parse['user_pass'];
	}

	/**
	 * send_mail()
	 * param1 $to
	 * param2 $title
	 * param3 $body
	 * param4 $from
	 * return send the email to destiny
	**/
	private function send_mail ( $to , $title , $body , $from = '' )
	{
		$from = trim ( $from );

		if ( !$from )
		{
			$from = Functions_Lib::read_config ( 'admin_email' );
		}

		$rp 	= Functions_Lib::read_config ( 'admin_email' );

		$head  	= '';
		$head  .= "Content-Type: text/html \r\n";
		$head  .= "charset: UTF-8 \r\n";
		$head  .= "Date: " . date('r') . " \r\n";
		$head  .= "Return-Path: $rp \r\n";
		$head  .= "From: $from \r\n";
		$head  .= "Sender: $from \r\n";
		$head  .= "Reply-To: $from \r\n";
		$head  .= "Organization: $org \r\n";
		$head  .= "X-Sender: $from \r\n";
		$head  .= "X-Priority: 3 \r\n";

		$body 	= str_replace ( "\r\n" , "\n" , $body );
		$body 	= str_replace ( "\n" , "\r\n" , $body );

		return @mail ( $to , $title , $body , $head );
	}
}
/* end of recoverpassword.php */