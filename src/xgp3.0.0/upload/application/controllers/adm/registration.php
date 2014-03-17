<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Registration extends XGPCore
{
	private $_current_user;
	private $_game_config;
	private	$_lang;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		// check if session is active
		parent::$users->check_session();

		$this->_lang			= parent::$lang;
		$this->_current_user	= parent::$users->get_user_data();

		// Check if the user is allowed to access
		if ( Administration_Lib::have_access ( $this->_current_user['user_authlevel'] ) && Administration_Lib::authorization ( $this->_current_user['user_authlevel'] , 'config_game' ) == 1 )
		{
			$this->_game_config		= Functions_Lib::read_config ( '' , TRUE );

			$this->build_page();
		}
		else
		{
			die ( Functions_Lib::message ( $this->_lang['ge_no_permissions'] ) );
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
		$parse			= $this->_lang;
		$parse['alert']	= '';

		if ( isset ( $_POST['opt_save'] ) && $_POST['opt_save'] == '1' )
		{
			// CHECK BEFORE SAVE
			$this->run_validations();

			Functions_Lib::update_config ( 'reg_enable'				, $this->_game_config['reg_enable'] 			);
			Functions_Lib::update_config ( 'reg_welcome_message'	, $this->_game_config['reg_welcome_message'] 	);
			Functions_Lib::update_config ( 'reg_welcome_email'		, $this->_game_config['reg_welcome_email'] 		);

			$parse['alert']					= Administration_Lib::save_message ( 'ok' , $this->_lang['ur_all_ok_message'] );
		}

		$parse['reg_closed']				= $this->_game_config['reg_enable'] == 1 ? " checked = 'checked' " : "";
		$parse['reg_welcome_message']		= $this->_game_config['reg_welcome_message'] == 1 ? " checked = 'checked' " : "";
		$parse['reg_welcome_email']			= $this->_game_config['reg_welcome_email'] == 1 ? " checked = 'checked' " : "";

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'adm/registration_view' ) ,  $parse ) );
	}

	/**
	 * method run_validations
	 * param
	 * return Run validations before insert data into the configuration file, if some data is not correctly validated it's not inserted.
	 */
	private function run_validations()
	{
		// Activate registrations
		if ( isset ( $_POST['reg_closed'] ) && $_POST['reg_closed'] == 'on' )
		{
			$this->_game_config['reg_enable']	= 1;
		}
		else
		{
			$this->_game_config['reg_enable'] 	= 0;
		}

		// Enable welcome message
		if ( isset ( $_POST['reg_welcome_message'] ) && $_POST['reg_welcome_message'] == 'on' )
		{
			$this->_game_config['reg_welcome_message']	= 1;
		}
		else
		{
			$this->_game_config['reg_welcome_message'] 	= 0;
		}

		// Enable welcome email
		if ( isset ( $_POST['reg_welcome_email'] ) && $_POST['reg_welcome_email'] == 'on' )
		{
			$this->_game_config['reg_welcome_email']	= 1;
		}
		else
		{
			$this->_game_config['reg_welcome_email'] 	= 0;
		}
	}
}
/* end of registration.php */