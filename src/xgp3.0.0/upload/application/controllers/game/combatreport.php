<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class CombatReport extends XGPCore
{
	const MODULE_ID	= 23;

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

		// Check module access
		Functions_Lib::module_message ( Functions_Lib::is_module_accesible ( self::MODULE_ID ) );

		$this->_lang			= parent::$lang;
		$this->_current_user	= parent::$users->get_user_data();

		$this->build_page();
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
		$report		= isset ( $_GET['report'] ) ? $_GET['report'] : die();
		$raportrow	= parent::$db->query_fetch ( "SELECT *
													FROM " .  REPORTS . "
													WHERE `report_rid` = '" . (  parent::$db->escape_value ( $report ) ) . "';");


		$owners		= explode ( ',' , $raportrow['report_owners'] );

		if ( ( $owners[0] == $this->_current_user['user_id'] ) && ( $raportrow['report_destroyed'] == 1 ) )
		{
			$page	= parent::$page->parse_template ( parent::$page->get_template ( 'combatreport/combatreport_no_fleet_view' ) , $this->_lang );
		}
		else
		{
			$report = stripslashes ( $raportrow['report_content'] );

			foreach ( $this->_lang['tech_rc'] as $id => $s_name )
			{
				$search		= array ( '[ship['.$id.']]' );
				$replace	= array ( $s_name );
				$report		= str_replace ( $search , $replace , $report );
			}

			$no_fleet 		= parent::$page->parse_template ( parent::$page->get_template ( 'combatreport/combatreport_no_fleet_view' ) , $this->_lang );
			$destroyed 		= parent::$page->parse_template ( parent::$page->get_template ( 'combatreport/combatreport_destroyed_view' ) , $this->_lang );
			$search  		= array ( $no_fleet );
			$replace  		= array ( $destroyed );
			$report 		= str_replace ( $search , $replace , $report );
			$page 		   	= $report;
		}

		parent::$page->display ( $page , FALSE , '' , FALSE );
	}
}
/* end of combatreport.php */