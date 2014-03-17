<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Backup extends XGPCore
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
		$parse				= $this->_lang;

		// ON POST
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
		{
			// SAVE DATA
			if ( isset ( $_POST['save'] ) && $_POST['save'] )
			{
				Functions_Lib::update_config ( 'auto_backup' , ( ( isset ( $_POST['auto_backup'] ) && $_POST['auto_backup'] == 'on' ) ? 1 : 0 ) );
			}

			// BACKUP DATABASE RIGHT NOW
			if ( isset ( $_POST['backup'] ) && $_POST['backup'] )
			{
				$result	= parent::$db->backup_db();

				if ( $result != FALSE )
				{
					$parse['alert']	= Administration_Lib::save_message ( 'ok' , str_replace ( '%s' , round ( $result / 1024 , 2 ) , $this->_lang['bku_backup_done'] ) );
				}
			}
		}

		// PARSE DATA
		$auto_backup_status	= Functions_Lib::read_config ( 'auto_backup' );
		$parse['color']		= ( $auto_backup_status == 1 ) ? 'text-success' : 'text-error';
		$parse['checked']	= ( $auto_backup_status == 1 ) ? 'checked' : '';

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( "adm/backup_view" ) , $parse ) );
	}
}
/* end of backup.php */