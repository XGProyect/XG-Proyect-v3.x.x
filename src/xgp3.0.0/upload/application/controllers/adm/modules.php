<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Modules extends XGPCore
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
		if ( Administration_Lib::have_access ( $this->_current_user['user_authlevel'] ) && Administration_Lib::authorization ( $this->_current_user['user_authlevel'] , 'edit_users' ) == 1 )
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
		$parse			= $this->_lang;
		$modules_array	= '';
		$modules_count	= count ( explode ( ';' , Functions_Lib::read_config ( 'modules' ) ) );
		$row_template	= parent::$page->get_template ( 'adm/modules_row_view' );
		$module_rows	= '';
		$parse['alert']	= '';

		// SAVE PAGE
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['save'] )
		{
			for ( $i = 0 ; $i <= $modules_count - 2 ; $i++ )
			{
				$modules_array	.= ( ( isset ( $_POST["status{$i}"] ) ) ? 1 : 0 ) . ';';
			}

			Functions_Lib::update_config ( 'modules' , $modules_array );

			$parse['alert']				= Administration_Lib::save_message ( 'ok' , $this->_lang['se_all_ok_message'] );
		}

		// SHOW PAGE
		$modules_array	= explode ( ';' , Functions_Lib::read_config ( 'modules' ) );

		foreach ( $modules_array as $module => $status )
		{
			if ( $status != NULL )
			{
				$parse['module']		= $module;
				$parse['module_name']	= $this->_lang['module'][$module];
				$parse['module_value']	= ( $status == 1 ) ? 'checked' : '';
				$parse['color']			= ( $status == 1 ) ? 'text-success' : 'text-error';

				$module_rows	.= parent::$page->parse_template ( $row_template , $parse );
			}
		}

		$parse['module_rows']	= $module_rows;

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( "adm/modules_view" ) , $parse ) );
	}
}
/* end of modules.php */