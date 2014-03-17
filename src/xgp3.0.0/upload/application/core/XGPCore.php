<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

abstract class XGPCore
{
	protected static $db;
	protected static $lang;
	protected static $users;
	protected static $objects;
	protected static $page;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		$this->set_db_class(); // DATABASE
		$this->set_lang_class(); // LANGUAGE
		$this->set_users_class(); // USERS
		$this->set_objects_class(); // OBJECTS
		$this->set_template_class(); // TEMPLATE
	}

	/**
	 * method set_db_class
	 * param
	 * return database instance
	 */
	private function set_db_class ()
	{
		require_once ( XGP_ROOT. '/application/core/Database.php' );
		self::$db		= new Database();
	}

	/**
	 * method set_lang_class
	 * param
	 * return language instance
	 */
	private function set_lang_class ()
	{
		require_once ( XGP_ROOT. '/application/core/Language.php' );
		$languages		= new Language();
		self::$lang		= $languages->lang();
	}

	/**
	 * method set_users_class
	 * param
	 * return users instance
	 */
	private function set_users_class()
	{
		require_once ( XGP_ROOT . '/application/libraries/Users_Lib.php' );
		self::$users	= new Users_Lib();
	}

	/**
	 * method set_objects_class
	 * param
	 * return objects instance
	 */
	private function set_objects_class ()
	{
		require_once ( XGP_ROOT. '/application/core/Objects.php' );
		self::$objects	= new Objects();
	}

	/**
	 * method set_template_class
	 * param
	 * return template instance
	 */
	private function set_template_class ()
	{
		require_once ( XGP_ROOT. '/application/libraries/Template_Lib.php' );
		self::$page		= new Template_Lib ( self::$lang , self::$users );
	}
}
/* end of XGPCore.php */