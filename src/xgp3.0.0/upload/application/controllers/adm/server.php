<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Server extends XGPCore
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

			Functions_Lib::update_config ( 'game_name' 				, $this->_game_config['game_name'] 				);
			Functions_Lib::update_config ( 'game_logo' 				, $this->_game_config['game_logo'] 				);
			Functions_Lib::update_config ( 'lang' 					, $this->_game_config['lang'] 					);
			Functions_Lib::update_config ( 'game_speed' 			, $this->_game_config['game_speed'] 			);
			Functions_Lib::update_config ( 'fleet_speed' 			, $this->_game_config['fleet_speed']            );
			Functions_Lib::update_config ( 'resource_multiplier' 	, $this->_game_config['resource_multiplier']    );
			Functions_Lib::update_config ( 'admin_email' 			, $this->_game_config['admin_email'] 			);
			Functions_Lib::update_config ( 'forum_url' 				, $this->_game_config['forum_url'] 				);
			Functions_Lib::update_config ( 'reg_enable'				, $this->_game_config['reg_enable'] 			);
			Functions_Lib::update_config ( 'game_enable'			, $this->_game_config['game_enable'] 			);
			Functions_Lib::update_config ( 'close_reason' 			, $this->_game_config['close_reason'] 			);
			Functions_Lib::update_config ( 'ssl_enabled' 			, $this->_game_config['ssl_enabled'] 			);
			Functions_Lib::update_config ( 'date_time_zone' 		, $this->_game_config['date_time_zone'] 		);
			Functions_Lib::update_config ( 'date_format' 			, $this->_game_config['date_format'] 			);
			Functions_Lib::update_config ( 'date_format_extended' 	, $this->_game_config['date_format_extended'] 	);
			Functions_Lib::update_config ( 'adm_attack' 			, $this->_game_config['adm_attack'] 			);
			Functions_Lib::update_config ( 'debug' 					, $this->_game_config['debug'] 					);
			Functions_Lib::update_config ( 'fleet_cdr' 				, $this->_game_config['fleet_cdr'] 				);
			Functions_Lib::update_config ( 'defs_cdr' 				, $this->_game_config['defs_cdr'] 				);
			Functions_Lib::update_config ( 'noobprotection' 		, $this->_game_config['noobprotection'] 		);
			Functions_Lib::update_config ( 'noobprotectiontime' 	, $this->_game_config['noobprotectiontime'] 	);
			Functions_Lib::update_config ( 'noobprotectionmulti' 	, $this->_game_config['noobprotectionmulti'] 	);

			$parse['alert']					= Administration_Lib::save_message ( 'ok' , $this->_lang['se_all_ok_message'] );
		}

		$parse['game_name']              	= $this->_game_config['game_name'];
		$parse['game_logo']              	= $this->_game_config['game_logo'];
		$parse['language_settings']			= Functions_Lib::get_languages ( $this->_game_config['lang'] );
		$parse['game_speed']             	= $this->_game_config['game_speed'] / 2500;
		$parse['fleet_speed']            	= $this->_game_config['fleet_speed'] / 2500;
		$parse['resource_multiplier']    	= $this->_game_config['resource_multiplier'];
		$parse['admin_email']              	= $this->_game_config['admin_email'];
		$parse['forum_url']              	= $this->_game_config['forum_url'];
		$parse['closed']                 	= $this->_game_config['game_enable'] == 1 ? " checked = 'checked' " : "";
		$parse['close_reason']           	= stripslashes ( $this->_game_config['close_reason'] );
		$parse['ssl_enabled']               = $this->_game_config['ssl_enabled'] == 1 ? " checked = 'checked' " : "";
		$parse['date_time_zone']			= $this->time_zone_picker();
		$parse['date_format']               = $this->_game_config['date_format'];
		$parse['date_format_extended']      = $this->_game_config['date_format_extended'];
		$parse['adm_attack']             	= $this->_game_config['adm_attack'] == 1 ? " checked = 'checked' " : "";
		$parse['debug']                  	= $this->_game_config['debug'] == 1 ? " checked = 'checked' " : "";
		$parse['shiips'] 					= $this->_game_config['fleet_cdr'];
		$parse['defenses'] 					= $this->_game_config['defs_cdr'];
		$parse['noobprot']            	 	= $this->_game_config['noobprotection'] == 1 ? " checked = 'checked' " : "";
		$parse['noobprot2'] 				= $this->_game_config['noobprotectiontime'];
		$parse['noobprot3'] 				= $this->_game_config['noobprotectionmulti'];


		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'adm/server_view' ) ,  $parse ) );
	}

	/**
	 * method run_validations
	 * param
	 * return Run validations before insert data into the configuration file, if some data is not correctly validated it's not inserted.
	 */
	private function run_validations()
	{
		/*
		 * SERVER SETTINGS
		 */

		// NAME
		if ( isset ( $_POST['game_logo'] ) && $_POST['game_logo'] != '' )
		{
			$this->_game_config['game_logo'] 	 = $_POST['game_logo'];
		}

		// LOGO
		if ( isset ( $_POST['game_name'] ) && $_POST['game_name'] != '' )
		{
			$this->_game_config['game_name'] 	 = $_POST['game_name'];
		}

		// LANGUAGE
		if ( isset ( $_POST['language'] ) )
		{
			$this->_game_config['lang']	= $_POST['language'];
		}
		else
		{
			$this->_game_config['lang'];
		}

		// GENERAL RATE
		if ( isset ( $_POST['game_speed'] ) && is_numeric ( $_POST['game_speed'] ) )
		{
			$this->_game_config['game_speed']	= ( 2500 * $_POST['game_speed'] );
		}

		// SPEED OF FLEET

		if ( isset ( $_POST['fleet_speed'] ) && is_numeric ( $_POST['fleet_speed'] ) )
		{
			$this->_game_config['fleet_speed']	= ( 2500 * $_POST['fleet_speed'] );
		}

		// SPEED OF PRODUCTION
		if ( isset ( $_POST['resource_multiplier'] ) && is_numeric ( $_POST['resource_multiplier'] ) )
		{
			$this->_game_config['resource_multiplier']	= $_POST['resource_multiplier'];
		}

		// ADMIN EMAIL CONTACT
		if ( isset ( $_POST['admin_email'] ) && $_POST['admin_email'] != '' && Functions_Lib::valid_email ( $_POST['admin_email'] ) )
		{
			$this->_game_config['admin_email']	= $_POST['admin_email'];
		}

		// FORUM LINK
		if ( isset ( $_POST['forum_url'] ) && $_POST['forum_url'] != '' )
		{
			$this->_game_config['forum_url']	= Functions_Lib::prep_url ( $_POST['forum_url'] );
		}

		// ACTIVATE SERVER
		if ( isset ( $_POST['closed'] ) && $_POST['closed'] == 'on' )
		{
			$this->_game_config['game_enable']	 = 1;
		}
		else
		{
			$this->_game_config['game_enable']	= 0;
		}

		// OFF-LINE MESSAGE
		if ( isset ( $_POST['close_reason'] ) && $_POST['close_reason'] != '' )
		{
			$this->_game_config['close_reason'] 	= addslashes ( $_POST['close_reason'] );
		}

		// SSL ENABLED
		if ( isset ( $_POST['ssl_enabled'] ) && $_POST['ssl_enabled'] == 'on' )
		{
			$this->_game_config['ssl_enabled']	= 1;
		}
		else
		{
			$this->_game_config['ssl_enabled']	= 0;
		}

		/*
		 * DATE AND TIME PARAMETERS
		 */
		// SHORT DATE
		if ( isset ( $_POST['date_time_zone'] ) && $_POST['date_time_zone'] != '' )
		{
			$this->_game_config['date_time_zone']		= $_POST['date_time_zone'];
		}

		if ( isset ( $_POST['date_format'] ) && $_POST['date_format'] != '' )
		{
			$this->_game_config['date_format']			= $_POST['date_format'];
		}

		// EXTENDED DATE
		if ( isset ( $_POST['date_format_extended'] ) && $_POST['date_format_extended'] != '' )
		{
			$this->_game_config['date_format_extended']	= $_POST['date_format_extended'];
		}

		/*
		 * SEVERAL PARAMETERS
		 */

		// PROTECTION
		if ( isset ( $_POST['adm_attack'] ) && $_POST['adm_attack'] == 'on' )
		{
			$this->_game_config['adm_attack']	= 1;
		}
		else
		{
			$this->_game_config['adm_attack']	= 0;
		}

		// DEBUG MODE
		if ( isset ( $_POST['debug'] ) && $_POST['debug'] == 'on' )
		{
			$this->_game_config['debug']	= 1;
		}
		else
		{
			$this->_game_config['debug']	= 0;
		}

		// SHIPS TO DEBRIS
		if ( isset ( $_POST['Fleet_Cdr'] ) && is_numeric ( $_POST['Fleet_Cdr'] ) )
		{
			if ( $_POST['Fleet_Cdr'] < 0 )
			{
				$this->_game_config['fleet_cdr']	= 0;
				$Number2							= 0;
			}
			else
			{
				$this->_game_config['fleet_cdr']	= $_POST['Fleet_Cdr'];
				$Number2							= $_POST['Fleet_Cdr'];
			}
		}

		// DEFENSES TO DEBRIS
		if ( isset ( $_POST['Defs_Cdr'] ) && is_numeric ( $_POST['Defs_Cdr'] ) )
		{
			if ( $_POST['Defs_Cdr'] < 0 )
			{
				$this->_game_config['defs_cdr']	= 0;
				$Number							= 0;
			}
			else
			{
				$this->_game_config['defs_cdr'] 	= $_POST['Defs_Cdr'];
				$Number								= $_POST['Defs_Cdr'];
			}
		}


		// PROTECTION FOR NOVICES
		if ( isset ( $_POST['noobprotection'] ) && $_POST['noobprotection'] == 'on')
		{
			$this->_game_config['noobprotection']	= 1;
		}
		else
		{
			$this->_game_config['noobprotection']	= 0;
		}

		// PROTECTION N. POINTS
		if ( isset ( $_POST['noobprotectiontime'] ) && is_numeric ( $_POST['noobprotectiontime'] ) )
		{
			$this->_game_config['noobprotectiontime']	= $_POST['noobprotectiontime'];
		}

		// PROTECCION N. LIMIT POINTS
		if ( isset ( $_POST['noobprotectionmulti'] ) && is_numeric ( $_POST['noobprotectionmulti'] ) )
		{
			$this->_game_config['noobprotectionmulti']	= $_POST['noobprotectionmulti'];
		}
	}

	/**
	 * method time_zone_picker
	 * param
	 * return return the select options
	 */
	private function time_zone_picker()
	{
		$utc				= new DateTimeZone ( 'UTC' );
		$dt 				= new DateTime ( 'now' , $utc );
		$time_zones			= '';
		$current_time_zone	= Functions_Lib::read_config ( 'date_time_zone' );

		// Get the data
		foreach ( DateTimeZone::listIdentifiers() as $tz )
		{
			$current_tz		= new DateTimeZone ( $tz );
			$offset 		= $current_tz->getOffset ( $dt );
			$transition		= $current_tz->getTransitions ( $dt->getTimestamp() , $dt->getTimestamp() );

			foreach ( $transition as $element => $data )
			{
				$time_zones_data[$data['offset']][]	= $tz;
			}
		}

		// Sort by key
		ksort ( $time_zones_data );

		// Build the combo
		foreach ( $time_zones_data as $offset => $tz )
		{
			$time_zones				.= '<optgroup label="GMT' . $this->format_offset ( $offset ) . '">';

			foreach ( $tz as $key => $zone )
			{
					$time_zones	   .= '<option value="' . $zone . '" ' . ( $current_time_zone == $zone ? ' selected' : '' ) . ' >' . $zone . '</option>';
			}

			$time_zones				.= '</optgroup>';
		}

		// Return data
		return $time_zones;
	}

	/**
	 * method format_offset
	 * param
	 * return return the format offset
	 */
	private function format_offset ( $offset )
	{
		$hours 		= $offset / 3600;
		$remainder 	= $offset % 3600;
		$sign 		= $hours > 0 ? '+' : '-';
		$hour 		= (int) abs($hours);
		$minutes 	= (int) abs($remainder / 60);

		if ( $hour == 0 && $minutes == 0 )
		{
			$sign	= ' ';
		}

		return $sign . str_pad ( $hour , 2 , '0' , STR_PAD_LEFT ) . ':' . str_pad ( $minutes , 2 , '0' );
	}
}
/* end of server.php */