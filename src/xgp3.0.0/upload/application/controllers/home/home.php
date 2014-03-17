<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Home extends XGPCore
{
	private $_lang;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_lang 	= parent::$lang;

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
		$parse	= $this->_lang;

		if ( $_POST )
		{
			$login 	= parent::$db->query_fetch	( "SELECT `user_id`, `user_name`, `user_password`, `user_banned`
													FROM " . USERS . "
													WHERE `user_name` = '" . parent::$db->escape_value ( $_POST['login'] ) . "'
														AND `user_password` = '" . sha1 ( $_POST['pass'] ) . "'
													LIMIT 1" );

			if ( $login['user_banned'] <= time() )
			{
				$this->remove_ban ( $login['user_name'] );
			}

			if ( $login )
			{
				// User login
				if ( parent::$users->user_login ( $login['user_id'] , $login['user_name'] , $login['user_password'] ) )
				{
					// Update current planet
					parent::$db->query ( "UPDATE " . USERS . " SET
											`user_current_planet` = `user_home_planet_id`
											WHERE `user_id` ='" . $login['user_id'] . "'" );

					// Redirect to game
					Functions_Lib::redirect ( 'game.php?page=overview' );
				}
			}

			// If login fails
			Functions_Lib::redirect ( 'index.php' );
		}
		else
		{
			$parse['year']		   	= date ( 'Y' );
			$parse['version']	   	= VERSION;
			$parse['servername']   	= Functions_Lib::read_config ( 'game_name' );
			$parse['game_logo']		= Functions_Lib::read_config ( 'game_logo' );
			$parse['forum_url']    	= Functions_Lib::read_config ( 'forum_url' );
			$parse['js_path']		= JS_PATH . 'home/';
			$parse['css_path']		= CSS_PATH . 'home/';
			$parse['img_path']		= IMG_PATH . 'home/';
			$parse['base_path']		= BASE_PATH;

			parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'home/index_body' ) , $parse ) , FALSE , '' , FALSE );
		}
	}

	/**
	 * remove_ban()
	 * param $user_name
	 * return run queries to lift the user ban
	**/
	private function remove_ban ( $user_name )
	{
		parent::$db->query ( "UPDATE " . USERS . " SET
								`user_banned` = '0'
								WHERE `user_name` = '" . $user_name . "' LIMIT 1;" );


		parent::$db->query ( "DELETE FROM " . BANNED . "
								WHERE `banned_who` = '" . $user_name . "'" );
	}
}
/* end of home.php */