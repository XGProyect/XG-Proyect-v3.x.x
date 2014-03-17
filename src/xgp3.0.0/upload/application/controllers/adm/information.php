<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Information extends XGPCore
{
	private $_lang;
	private $_current_user;

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
		if ( Administration_Lib::have_access ( $this->_current_user['user_authlevel'] ) && Administration_Lib::authorization ( $this->_current_user['user_authlevel'] , 'observation' ) == 1 )
		{
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
		$parse					= $this->_lang;
		$update					= Functions_Lib::read_config ( 'stat_last_update' );
		$backup					= Functions_Lib::read_config ( 'last_backup' );
		$cleanup				= Functions_Lib::read_config ( 'last_cleanup' );
		$modules				= explode ( ';' , Functions_Lib::read_config ( 'modules' ) );
		$count_modules			= 0;

		// COUNT MODULES
		foreach ( $modules as $module )
		{
			if ( $module == 1 )
			{
				$count_modules++;
			}
		}

		// LOAD STATISTICS
		$inactive_time	= ( time() - 60 * 60 * 24 * 7 );
		$users_count	= parent::$db->query_fetch ( "SELECT (
																SELECT COUNT(user_id)
																	FROM " . USERS . "
															 ) AS users_count,

															 ( SELECT COUNT(user_id)
															 		FROM " . USERS . "
															 		WHERE user_onlinetime < {$inactive_time} AND user_onlinetime <> 0
															 ) AS inactive_count,

															 ( SELECT COUNT(setting_user_id)
															 		FROM " . SETTINGS . "
															 		WHERE setting_vacations_status <> 0
															 ) AS on_vacation,

															 ( SELECT COUNT(setting_user_id)
															 		FROM " . SETTINGS . "
															 		WHERE setting_delete_account <> 0
															 ) AS to_delete,

															 ( SELECT COUNT(user_id)
															 		FROM " . USERS . "
															 		WHERE user_banned <> 0
															 ) AS banned_users,

															 ( SELECT COUNT(fleet_id)
															 		FROM " . FLEETS . "
															 ) AS fleets_count" );

		// LOAD STATISTICS
		$db_tables	= parent::$db->query ( "SHOW TABLE STATUS" );
		$db_size	= 0;

		while ( $row = parent::$db->fetch_array ( $db_tables ) )
		{
			$db_size += $row['Data_length'] + $row['Index_length'];
		}

		// PARSE STATISTICS
		$parse['info_points']				= date ( Functions_Lib::read_config ( 'date_format_extended' ) , $update ) . ' | ' . Format_Lib::pretty_time ( ( time() - $update ) );
		$parse['info_backup']				= date ( Functions_Lib::read_config ( 'date_format_extended' ) , $backup ) . ' | ' . Format_Lib::pretty_time ( ( time() - $backup ) );
		$parse['info_cleanup']				= date ( Functions_Lib::read_config ( 'date_format_extended' ) , $cleanup ) . ' | ' . Format_Lib::pretty_time ( ( time() - $cleanup ) );
		$parse['info_modules']				= $count_modules . '/' . ( count ( $modules ) - 1 );
		$parse['info_total_users']			= $users_count['users_count'];
		$parse['info_inactive_users']		= $users_count['inactive_count'];
		$parse['info_vacation_users']		= $users_count['on_vacation'];
		$parse['info_delete_mode_users']	= $users_count['to_delete'];
		$parse['info_banned_users']			= $users_count['banned_users'];
		$parse['info_flying_fleets']		= $users_count['fleets_count'];
		$parse['info_database_size']		= round ( $db_size / 1024 , 1 ) . ' kb';
		$parse['info_database_server']		= 'MySQL ' . parent::$db->server_info();

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( "adm/information_view" ) , $parse ) );
	}
}
/* end of information.php */