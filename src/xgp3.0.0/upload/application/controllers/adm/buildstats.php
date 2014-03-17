<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class ShowBuildStatsPage extends XGPCore
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
		if ( Administration_Lib::have_access ( $this->_current_user['user_authlevel'] ) && Administration_Lib::authorization ( $this->_current_user['user_authlevel'] , 'use_tools' ) == 1 )
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
		// RUN STATISTICS SCRIPT AND THE SET THE RESULT
		$result					= Statistics_Lib::make_stats();

		// PREPARE DATA TO PARSE
		$parse					= $this->_lang;
		$parse['memory_p']		= str_replace ( array ( "%p" , "%m" ) , $result['memory_peak'] , $this->_lang['sb_top_memory'] );
		$parse['memory_e']		= str_replace ( array ( "%e" , "%m" ) , $result['end_memory'] , $this->_lang['sb_final_memory'] );
		$parse['memory_i']		= str_replace ( array ( "%i" , "%m" ) , $result['initial_memory'] , $this->_lang['sb_start_memory'] );
		$parse['alert']			= Administration_Lib::save_message ( 'ok' , str_replace ( "%t" , $result['totaltime'] , $this->_lang['sb_stats_update'] ) );

		// UPDATE STATISTICS LAST UPDATE
		Functions_Lib::update_config( 'stat_last_update', $result['stats_time']);

		// SHOW TEMPLATE
		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'adm/buildstats_view' ) , $parse ) );
	}
}
/* end of buildstats.php */