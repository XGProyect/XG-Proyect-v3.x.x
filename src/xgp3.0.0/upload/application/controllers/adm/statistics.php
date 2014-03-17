<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Statistics extends XGPCore
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
		if ( Administration_Lib::have_access ( $this->_current_user['user_authlevel'] ) && Administration_Lib::authorization ( $this->_current_user['user_authlevel'] , 'config_game' ) == 1 )
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
		$game_stat				= Functions_Lib::read_config ( 'stat' );
		$game_stat_level		= Functions_Lib::read_config ( 'stat_level' );
		$game_stat_settings		= Functions_Lib::read_config ( 'stat_settings' );
		$game_stat_update_time	= Functions_Lib::read_config ( 'stat_update_time' );
		$this->_lang['alert']	= '';

		if ( isset ( $_POST['save'] ) && ( $_POST['save'] == $this->_lang['cs_save_changes'] ) )
		{
			if ( isset ( $_POST['stat'] ) && $_POST['stat'] != $game_stat )
			{
				Functions_Lib::update_config ( 'stat' , $_POST['stat'] );

				$game_stat	= $_POST['stat'];
				$ASD3		= $_POST['stat'];
			}

			if ( isset ( $_POST['stat_level'] ) && is_numeric ( $_POST['stat_level'] ) && $_POST['stat_level'] != $game_stat_level )
			{
				Functions_Lib::update_config ( 'stat_level' ,  $_POST['stat_level'] );

				$game_stat_level 	= $_POST['stat_level'];
				$ASD1				= $_POST['stat_level'];
			}

			if ( isset ( $_POST['stat_settings'] ) &&  is_numeric ( $_POST['stat_settings'] ) && $_POST['stat_settings'] != $game_stat_settings )
			{
				Functions_Lib::update_config ( 'stat_settings' ,  $_POST['stat_settings'] );

				$game_stat_settings  = $_POST['stat_settings'];
			}

			if ( isset ( $_POST['stat_update_time'] ) && is_numeric ( $_POST['stat_update_time'] ) && $_POST['stat_update_time'] != $game_stat_update_time )
			{
				Functions_Lib::update_config ( 'stat_update_time' ,  $_POST['stat_update_time'] );

				$game_stat_update_time	= $_POST['stat_update_time'];
			}

			$this->_lang['alert']		= Administration_Lib::save_message ( 'ok' , $this->_lang['cs_all_ok_message'] );
		}

		$selected						=	"selected=\"selected\"";
		$stat							=	( ( $game_stat == 1 ) ? 'sel_sta1' : 'sel_sta0' );
		$this->_lang[$stat]				=	$selected;
		$this->_lang['stat_level']		=	$game_stat_level;
		$this->_lang['stat_settings']	=	$game_stat_settings;
		$this->_lang['stat_update_time']=	$game_stat_update_time;
		$this->_lang['yes']				=	$this->_lang['cs_yes'][1];
		$this->_lang['no']				=	$this->_lang['cs_no'][0];

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'adm/statistics_view' ) , $this->_lang ) );
	}
}
/* end of statistics.php */