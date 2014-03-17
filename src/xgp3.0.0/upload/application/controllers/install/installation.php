<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Installation extends XGPCore
{
	private $_host;
	private $_db;
	private $_user;
	private $_password;
	private $_prefix;
	private $_lang;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_lang	= parent::$lang;


		if ( $this->server_requirementes() )
		{
			$this->build_page();
		}
		else
		{
			die ( Functions_Lib::message ( $this->_lang['ins_no_server_requirements'] ) );
		}
	}

	/**
	 * method __destruct
	 * param
	 * return close db connection
	 */
	public function __destruct ()
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
		$parse		= $this->_lang;
		$continue	= TRUE;

		// VERIFICATION - WE DON'T WANT ANOTHER INSTALLATION
		if ( $this->is_installed() )
		{
			die ( Functions_Lib::message ( $this->_lang['ins_already_installed'] , '' , '' , FALSE , FALSE ) );
		}

		if ( ! $this->check_xml_file() )
		{
			die ( Functions_Lib::message ( $this->_lang['ins_missing_xml_file'] , '' , '' , FALSE , FALSE ) );
		}

		// ACTION FOR THE CURRENT PAGE
		switch ( ( isset ( $_POST['page'] ) ? $_POST['page'] : '' ) )
		{
			case 'step1':

				$this->_host		= $_POST['host'];
				$this->_db 			= $_POST['db'];
				$this->_user 		= $_POST['user'];
				$this->_password	= $_POST['password'];
				$this->_prefix 		= $_POST['prefix'];

				if ( ! $this->validate_db_data() )
				{
					$alerts			= $this->_lang['ins_empty_fields_error'];
					$continue		= FALSE;
				}

				if ( ! $this->write_config_file() )
				{
					$alerts			= $this->_lang['ins_write_config_error'];
					$continue		= FALSE;
				}

				if ( $continue )
				{
					Functions_Lib::redirect ( '?page=install&mode=step2' );
				}

				$parse['alert']						= $this->save_message ( 'warning' , $alerts );
				$current_page						= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_database_view' ) , $parse );

			break;

			case 'step2':

				if ( ! $this->try_connection() )
				{
					$alerts		= $this->_lang['ins_not_connected_error'];
					$continue	= FALSE;
				}

				if ( $continue )
				{
					Functions_Lib::redirect ( '?page=install&mode=step3' );
				}

				$parse['alert']						= $this->save_message ( 'warning' , $alerts );
				$current_page						= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_database_view' ) , $parse );

			break;

			case 'step3':

				if ( !$this->insert_db_data() )
				{
					$alerts		= $this->_lang['ins_insert_tables_error'];
					$continue	= FALSE;
				}

				if ( $continue )
				{
					Functions_Lib::redirect ( '?page=install&mode=step4' );
				}

				$parse['alert']						= $this->save_message ( 'warning' , $alerts );
				$current_page						= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_database_view' ) , $parse );

			break;

			case 'step4':

				Functions_Lib::redirect ( '?page=install&mode=step5' );

			break;

			case 'step5':

				if ( !$this->create_account() )
				{
					$parse['alert']					= $this->save_message ( 'warning' , $this->_lang['ins_adm_empty_fields_eror'] );
					$current_page					= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_create_admin_view' ) , $parse );
					$continue						= FALSE;
				}

				if ( $continue )
				{
					Functions_Lib::update_config ( 'stat_last_update' , time() );
					Functions_Lib::update_config ( 'game_installed' , '1' );

					$current_page					= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_create_admin_done_view' ) , $this->_lang );
					$continue						= FALSE; // THIS CONTINUE ON FALSE MEANS "THIS IS THE END OF THE INSTALLATION, NO WHERE ELSE TO GO"
				}

			break;

			case '':
			default:
			break;
		}

		if ( $continue )
		{
			switch ( ( isset ( $_GET['mode'] ) ? $_GET['mode'] : '' ) )
			{
				case 'step1':

					$current_page				= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_database_view' ) , $this->_lang );

				break;

				case 'step2':

					$parse['step']				= 'step2';
					$parse['done_config']		= $this->_lang['ins_done_config'];
					$parse['done_connected']	= '';
					$parse['done_insert']		= '';
					$current_page				= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_done_actions_view' ) , $parse );

				break;

				case 'step3':

					$parse['step']				= 'step3';
					$parse['done_config']		= '';
					$parse['done_connected']	= $this->_lang['ins_done_connected'];
					$parse['done_insert']		= '';
					$current_page				= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_done_actions_view' ) , $parse );

				break;

				case 'step4':

					$parse['step']				= 'step4';
					$parse['done_config']		= '';
					$parse['done_connected']	= '';
					$parse['done_insert']		= $this->_lang['ins_done_insert'];
					$current_page				= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_done_actions_view' ) , $parse );

				break;

				case 'step5':
					$parse['step']				= 'step5';
					$current_page				= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_create_admin_view' ) , $parse );

				break;

				case 'license':

					$current_page				= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_license_view' ) , $this->_lang );

				break;

				case '':
				case 'overview':
				default:

					$current_page				= parent::$page->parse_template ( parent::$page->get_template ( 'install/in_welcome_view' ) , $this->_lang );

				break;
			}
		}

		parent::$page->display ( $current_page );
	}

	/**
	 * method server_requirementes
	 * param
	 * return true if the required server requirements are met
	 */
	private function server_requirementes()
	{
		if ( version_compare ( PHP_VERSION , '5.3.0' , '<' ) )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * method is_installed
	 * param
	 * return true if the game is already installed, false if not
	 */
	private function is_installed ()
	{
		return ( Functions_Lib::read_config ( 'game_installed' ) == 1 );
	}

	/**
	 * method server_requirementes
	 * param
	 * return true if the required server requirements are met
	 */
	private function try_connection()
	{
		// TRY
		if ( parent::$db->try_connection() && parent::$db->try_database() )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method write_config_file
	 * param
	 * return write configuration file
	 */
	private function write_config_file ()
	{
		$config_file	= @fopen ( XGP_ROOT . 'application/config/config.php' , "w" );

		if ( ! $config_file )
		{
			return FALSE;
		}

		$data 	 = "<?php\n";
		$data 	.= "if(!defined(\"INSIDE\")){ header(\"location:".XGP_ROOT."\"); }\n";
		$data 	.= "defined('DB_HOST') ? NULL : define('DB_HOST', '".$this->_host."');\n";
		$data 	.= "defined('DB_USER') ? NULL : define('DB_USER', '".$this->_user."');\n";
		$data 	.= "defined('DB_PASS') ? NULL : define('DB_PASS', '".$this->_password."');\n";
		$data 	.= "defined('DB_NAME') ? NULL : define('DB_NAME', '".$this->_db."');\n";
		$data 	.= "defined('DB_PREFIX') ? NULL : define('DB_PREFIX', '".$this->_prefix."');\n";
		$data 	.= "defined('SECRETWORD') ? NULL : define('SECRETWORD', 'xgp-".$this->generate_token()."');\n";
		$data	.= "?>";

		fwrite ( $config_file , $data );
		fclose ( $config_file );

		return TRUE;
	}

	/**
	 * method insert_db_data
	 * param
	 * return TRUE successfully inserted data | FALSE an error ocurred
	 */
	private function insert_db_data()
	{
		require_once ( XGP_ROOT . 'install/databaseinfos.php' );

		$status['acs_fleets']			= parent::$db->query ( $table_acs_fleets ); // TABLE ACS_FLEETS
		$status['alliance']				= parent::$db->query ( $table_alliance ); // TABLE ALLIANCES
		$status['alliance_statistics']	= parent::$db->query ( $table_alliance_statistics ); // TABLE ALLIANCE_STATISTICS
		$status['banned']				= parent::$db->query ( $table_banned ); // TABLE BANNED
		$status['buddys']				= parent::$db->query ( $table_buddys ); // TABLE BUDDYS
		$status['buildings']			= parent::$db->query ( $table_buildings ); // TABLE BUILDINGS
		$status['defenses']				= parent::$db->query ( $table_defenses ); // TABLE DEFENSES
		$status['fleets']				= parent::$db->query ( $table_fleets ); // TABLE FLEETS
		$status['messages']				= parent::$db->query ( $table_messages ); // TABLE MESSAGES
		$status['notes']				= parent::$db->query ( $table_notes ); // TABLE NOTES
		$status['planets']				= parent::$db->query ( $table_planets ); // TABLE PLANETS
		$status['premium']				= parent::$db->query ( $table_premium ); // TABLE PREMIUM
		$status['research']				= parent::$db->query ( $table_research ); // TABLE RESEARCH
		$status['reports']				= parent::$db->query ( $table_reports ); // TABLE REPORTS
		$status['settings']				= parent::$db->query ( $table_settings ); // TABLE SETTINGS
		$status['sessions']				= parent::$db->query ( $table_sessions ); // TABLE SESSIONS
		$status['ships']				= parent::$db->query ( $table_ships ); // TABLE SHIPS
		$status['users']				= parent::$db->query ( $table_users ); // TABLE USERS
		$status['users_statistics']		= parent::$db->query ( $table_user_statistics ); // TABLE USERS_STATISTICS


		foreach ( $status as $table => $state )
		{
			if ( $state != 1 )
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * method create_account
	 * param
	 * return TRUE successfully created admin | FALSE an error ocurred
	 */
	private function create_account()
	{
		// validations
		if ( empty ( $_POST['adm_user'] ) or empty ( $_POST['adm_pass'] ) or empty ( $_POST['adm_email'] ) or !Functions_Lib::valid_email ( $_POST['adm_email'] ) )
		{
			return FALSE;
		}

		// some default values
		$adm_name	= parent::$db->escape_value ( $_POST['adm_user'] );
		$adm_email	= parent::$db->escape_value ( $_POST['adm_email'] );
		$adm_pass	= sha1 ( $_POST['adm_pass'] );

		// a bunch of of queries :/
		parent::$db->query ( "INSERT INTO " . USERS . " SET
								`user_id` = '1',
								`user_name` = '". $adm_name ."',
								`user_email` = '". $adm_email ."',
								`user_email_permanent` = '". $adm_email ."',
								`user_ip_at_reg` = '". $_SERVER['REMOTE_ADDR'] . "',
								`user_agent` = '',
								`user_authlevel` = '3',
								`user_home_planet_id` = '1',
								`user_galaxy` = '1',
								`user_system` = '1',
								`user_planet` = '1',
								`user_current_planet` = '1',
								`user_register_time` = '". time() ."',
								`user_password` = '". $adm_pass ."';" );

		parent::$db->query ( "INSERT INTO " . PLANETS . " SET
								`planet_user_id` = '1',
								`planet_galaxy` = '1',
								`planet_system` = '1',
								`planet_planet` = '1',
								`planet_last_update` = '". time() ."',
								`planet_metal` = '500',
								`planet_crystal` = '500',
								`planet_deuterium` = '0';" );

		parent::$db->query ( "INSERT INTO " . RESEARCH . " SET
								`research_user_id` = '1';" );

		parent::$db->query ( "INSERT INTO " . USERS_STATISTICS . " SET
								`user_statistic_user_id` = '1';" );

		parent::$db->query ( "INSERT INTO " . PREMIUM . " SET
								`premium_user_id` = '1';" );

		parent::$db->query ( "INSERT INTO " . SETTINGS . " SET
										`setting_user_id` = '1';" );

		parent::$db->query ( "INSERT INTO " . BUILDINGS . " SET
								`building_planet_id` = '1';" );

		parent::$db->query ( "INSERT INTO " . DEFENSES . " SET
								`defense_planet_id` = '1';" );

		parent::$db->query ( "INSERT INTO " . SHIPS . " SET
								`ship_planet_id` = '1';" );


		// write the new admin email for support and debugging
		Functions_Lib::update_config ( 'admin_email' , $adm_email );

		return TRUE;
	}

	/**
	 * method validate_db_data
	 * param
	 * return check inserted data, try connection and return the result
	 */
	private function validate_db_data()
	{
		if ( !empty ( $this->_host ) && !empty ( $this->_db ) && !empty ( $this->_user ) && !empty ( $this->_password ) && !empty ( $this->_prefix ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method check_xml_file
	 * param
	 * return true if file was found, else if not
	 */
	private function check_xml_file()
	{
		$needed_config_file		= @fopen ( XGP_ROOT . 'application/config/config.xml' , "r" );
		$default_config_file	= @fopen ( XGP_ROOT . 'application/config/config.xml.cfg' , "r" );

		if ( ! $needed_config_file )
		{
			if ( ! $default_config_file )
			{
				return FALSE;
			}
			else
			{
				return $this->create_xml (); // WILL RETURN TRUE IF THE FILE WAS SUCCESSFULLY CREATED
			}
		}

		return TRUE;
	}

	/**
	 * method create_xml
	 * param
	 * return true if file was succesfully created
	 */
	private function create_xml()
	{
		$location					= XGP_ROOT . 'application/config/';
		$default_config_file 		= $location . 'config.xml.cfg';
		$needed_config_file 		= $location . 'config.xml';

		return ( copy ( $default_config_file , $needed_config_file ) );
	}

	/**
	 * method generate_token
	 * param
	 * return the security token generated
	 */
	private function generate_token()
	{
		$characters	= 'aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890';
		$count		= strlen ( $characters );
		$new_token	= '';
		$lenght		= 16;
		srand ( ( double)microtime() * 1000000 );

		for ( $i = 0 ; $i < $lenght ; $i++ )
		{
			$character_boucle	= mt_rand ( 0 , $count - 1 );
			$new_token			= $new_token . substr ( $characters , $character_boucle , 1 );
		}

		return $new_token;
	}

	/**
	 * method save_message
	 * param $result
	 * return show the save message
	 */
	private function save_message ( $result = 'ok' , $message )
	{
		switch ( $result )
		{
			case 'ok':
				$parse['color']		= 'alert-success';
				$parse['status']	= $this->_lang['ins_ok_title'];
			break;

			case 'error':
				$parse['color']		= 'alert-error';
				$parse['status']	= $this->_lang['ins_error_title'];
			break;

			case 'warning':
				$parse['color']		= 'alert-block';
				$parse['status']	= $this->_lang['ins_warning_title'];
			break;
		}

		$parse['message']			= $message;

		return parent::$page->parse_template ( parent::$page->get_template ( "adm/save_message_view" ) , $parse );
	}
}
/* end of installation.php */