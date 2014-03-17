<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Update extends XGPCore
{
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
		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'install/in_update' ) , $this->_lang ) );
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
}
/* end of update.php */