<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Register extends XGPCore
{
	private $_creator;
	private $_lang;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_lang = parent::$lang;

		if ( Functions_Lib::read_config ( 'reg_enable' ) == 1 )
		{
			$this->_creator	= Functions_Lib::load_library ( 'Creator_Lib' );

			$this->build_page();
		}
		else
		{
			die ( Functions_Lib::message ( $this->_lang['re_disabled'] , 'index.php' , '5' , FALSE , FALSE ) );
		}
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
		if ( $_POST )
		{
			if ( ! $this->run_validations() )
			{
				Functions_Lib::redirect ( 'index.php' );
			}
			else
			{
				$user_password		= $_POST['password'];
				$user_name 			= $_POST['character'];
				$user_email 		= $_POST['email'];
				$hashed_password 	= sha1 ( $user_password );

				parent::$db->query ( "INSERT INTO " . USERS . " SET
										`user_name` = '" . parent::$db->escape_value ( strip_tags ( $user_name ) ) . "',
										`user_email` = '" . parent::$db->escape_value ( $user_email ) . "',
										`user_email_permanent` = '" . parent::$db->escape_value ( $user_email ) . "',
										`user_ip_at_reg` = '" . $_SERVER['REMOTE_ADDR'] . "',
										`user_agent` = '" . $_SERVER['HTTP_USER_AGENT'] . "',
										`user_home_planet_id` = '0',
										`user_register_time` = '" . time() . "',
										`user_password`='" . $hashed_password . "';" );

				$user_id = parent::$db->insert_id();

				parent::$db->query ( "INSERT INTO " . RESEARCH . " SET
										`research_user_id` = '" . $user_id . "';" );

				parent::$db->query ( "INSERT INTO " . USERS_STATISTICS . " SET
										`user_statistic_user_id` = '" . $user_id . "';" );

				parent::$db->query ( "INSERT INTO " . PREMIUM . " SET
										`premium_user_id` = '" . $user_id . "';" );

				parent::$db->query ( "INSERT INTO " . SETTINGS . " SET
										`setting_user_id` = '" . $user_id . "';" );

				$last_galaxy	= Functions_Lib::read_config ( 'lastsettedgalaxypos' );
				$last_system 	= Functions_Lib::read_config ( 'lastsettedsystempos' );
				$last_planet 	= Functions_Lib::read_config ( 'lastsettedplanetpos' );

				while ( ! isset ( $newpos_checked ) )
				{
					for ( $galaxy = $last_galaxy ; $galaxy <= MAX_GALAXY_IN_WORLD ; $galaxy++ )
					{
						for ( $system = $last_system ; $system <= MAX_SYSTEM_IN_GALAXY ; $system++ )
						{
							for ( $pos = $last_planet ; $pos <= 4 ; $pos++ )
							{
								$planet = round ( mt_rand ( 4 , 12 ) );

								switch ( $last_planet )
								{
									case 1:

										$last_planet	+= 1;

									break;

									case 2:

										$last_planet	+= 1;

									break;

									case 3:

										if ( $last_system == MAX_SYSTEM_IN_GALAXY )
										{
											$last_galaxy	+= 1;
											$last_system 	 = 1;
											$last_planet 	 = 1;

											break;
										}
										else
										{
											$last_planet	= 1;
										}

										$last_system	+= 1;

									break;
								}
								break;
							}
							break;
						}
						break;
					}

					$planet_row = parent::$db->query_fetch ( "SELECT *
																FROM " . PLANETS . "
																WHERE `planet_galaxy` = '" . $galaxy . "' AND
																		`planet_system` = '" . $system . "' AND
																		`planet_planet` = '" . $planet . "' LIMIT 1;" );

					if ( $planet_row['id'] == '0' )
					{
						$newpos_checked	= TRUE;
					}


					if ( ! $planet_row )
					{
						$this->_creator->create_planet ( $galaxy , $system , $planet , $user_id , '' , TRUE );
						$newpos_checked	= TRUE;
					}

					if ( $newpos_checked )
					{
						Functions_Lib::update_config ( 'lastsettedgalaxypos' , $last_galaxy );
						Functions_Lib::update_config ( 'lastsettedsystempos' , $last_system );
						Functions_Lib::update_config ( 'lastsettedplanetpos' , $last_planet );
					}
				}

				parent::$db->query ( "UPDATE " . USERS . " SET
										`user_home_planet_id` = (SELECT `planet_id` FROM " . PLANETS . " WHERE `planet_user_id` = '" . $user_id . "' LIMIT 1),
										`user_current_planet` = (SELECT `planet_id` FROM " . PLANETS . " WHERE `planet_user_id` = '" . $user_id . "' LIMIT 1),
										`user_galaxy` = '" . $galaxy . "',
										`user_system` = '" . $system . "',
										`user_planet` = '" . $planet . "'
										 WHERE `user_id` = '" . $user_id . "' LIMIT 1;" );

				$from 		= $this->_lang['re_welcome_message_from'];
				$subject 	= $this->_lang['re_welcome_message_subject'];
				$message 	= str_replace ( '%s' , $user_name , $this->_lang['re_welcome_message_content'] );

				// Send Welcome Message to the user if the feature is enabled
				if ( Functions_Lib::read_config ( 'reg_welcome_message' ) )
				{
					Functions_Lib::send_message ( $user_id , 0 , '' , 5 , $from , $subject , $message );
				}

				// Send Welcome Email to the user if the feature is enabled
				if ( Functions_Lib::read_config ( 'reg_welcome_email' ) )
				{
					$this->send_pass_email ( $user_email , $user_name , $user_password );
				}

				// User login
				if ( parent::$users->user_login ( $user_id , $user_name , $hashed_password ) )
				{
					// Redirect to game
					Functions_Lib::redirect ( 'game.php?page=overview' );
				}
			}
		}

		// If login fails
		Functions_Lib::redirect ( 'index.php' );
	}

	/**
	 * send_pass_email()
	 * param1 $emailaddress
	 * param2 $password
	 * return prepare the email and return mail status, delivered or not
	**/
	private function send_pass_email ( $emailaddress , $user_name , $password )
	{
		$game_name						= Functions_Lib::read_config ( 'game_name' );

		$parse							= $this->_lang;
		$parse['user_name']				= $user_name;
		$parse['user_pass']				= $password;
		$parse['game_url']				= GAMEURL;
		$parse['reg_mail_text_part1']	= str_replace ( '%s' , $game_name , $this->_lang['re_mail_text_part1'] );
		$parse['reg_mail_text_part7']	= str_replace ( '%s' , $game_name , $this->_lang['re_mail_text_part7'] );

		$email 							= parent::$page->parse_template (  parent::$page->get_template ( 'home/email_template' ) , $parse );
		$status 						= $this->send_mail ( $emailaddress , $this->_lang['re_register_at'] . Functions_Lib::read_config ( 'game_name' ) , $email );

		return $status;
	}

	/**
	 * send_mail()
	 * param $to
	 * param $title
	 * param $body
	 * param $from
	 * return send the email to destiny
	**/
	private function send_mail ( $to , $title , $body , $from = '' )
	{
		$from = trim ( $from );

		if ( !$from )
		{
			$from = Functions_Lib::read_config ( 'admin_email' );
		}

		$head  	= '';
		$head  .= "Content-Type: text/html \r\n";
		$head  .= "charset: UTF-8 \r\n";
		$head  .= "Date: " . date('r') . " \r\n";
		$head  .= "Return-Path: " . Functions_Lib::read_config ( 'admin_email' ) . " \r\n";
		$head  .= "From: $from \r\n";
		$head  .= "Sender: $from \r\n";
		$head  .= "Reply-To: $from \r\n";
		$head  .= "Organization: " . Functions_Lib::read_config ( 'game_name' ) . " \r\n";
		$head  .= "X-Sender: $from \r\n";
		$head  .= "X-Priority: 3 \r\n";

		$body 	= str_replace ( "\r\n" , "\n" , $body );
		$body 	= str_replace ( "\n" , "\r\n" , $body );

		return @mail ( $to , $title , $body , $head );
	}

	/**
	 * run_validations()
	 * param
	 * return run validations and return bool result
	 **/
	private function run_validations ()
	{
		$errors	= 0;

		if ( ! Functions_Lib::valid_email ( $_POST['email'] ) )
		{
			$errors++;
		}

		if ( ! $_POST['character'] )
		{
			$errors++;
		}

		if ( strlen ( $_POST['password'] ) < 8 )
		{
			$errors++;
		}

		if ( preg_match ( "/[^A-z0-9_\-]/" , $_POST['character'] ) == 1 )
		{
			$errors++;
		}

		if ( $_POST['agb'] != 'on' )
		{
			$errors++;
		}

		if ( $this->check_user() )
		{
			$errors++;
		}

		if ( $this->check_email() )
		{
			$errors++;
		}

		if ( $errors > 0 )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * check_user()
	 * param
	 * return check if the user exists
	 **/
	private function check_user ()
	{
		return parent::$db->query_fetch ( "SELECT `user_name`
											FROM " . USERS . "
											WHERE `user_name` = '" . parent::$db->escape_value ( $_POST['character'] ) . "'
											LIMIT 1;" );
	}

	/**
	 * check_email()
	 * param
	 * return check if the email exists
	 **/
	private function check_email ()
	{
		return parent::$db->query_fetch ( "SELECT `user_email`
											FROM " . USERS . "
											WHERE `user_email` = '" . parent::$db->escape_value ( $_POST['email'] ) . "'
											LIMIT 1;" );
	}
}
/* end of register.php */