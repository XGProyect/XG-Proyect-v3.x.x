<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Ban extends XGPCore
{
	private $_lang;
	private $_current_user;
	private $_users_count	= 0;
	private $_banned_count	= 0;

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
		switch ( ( isset ( $_GET['mode'] ) ? $_GET['mode'] : '' ) )
		{
			case 'ban':

				$view	= $this->show_ban();

			break;

			case '':
			default:

				$view	= $this->show_default();

			break;
		}

		parent::$page->display ( $view );
	}

	/**
	 * method show_default
	 * param
	 * return build the default page
	 */
	private function show_default()
	{
		$parse 					= $this->_lang;
		$parse['js_path']		= XGP_ROOT . JS_PATH;

		if ( isset ( $_POST['unban_name'] ) && $_POST['unban_name'] )
		{
			$username	= parent::$db->escape_value ( $_POST['unban_name'] );

			parent::$db->query ( "DELETE FROM `" . BANNED . "`
									WHERE `banned_who` = '" . $username . "'");

			parent::$db->query ( "UPDATE `" . USERS . "` SET
									`user_banned` = '0'
									WHERE `user_name` = '" . $username . "'
									LIMIT 1" );

			$parse['alert']		= Administration_Lib::save_message ( 'ok' , ( str_replace ( '%s' , $username , $this->_lang['bn_lift_ban_success'] ) ) );
		}

		$parse['users_list']	= $this->get_users_list();
		$parse['banned_list']	= $this->get_banned_list();
		$parse['users_amount']	= $this->_users_count;
		$parse['banned_amount']	= $this->_banned_count;

		return parent::$page->parse_template ( parent::$page->get_template ( 'adm/ban_view' ) , $parse );
	}

	/**
	 * method show_ban
	 * param
	 * return build the ban page
	 */
	private function show_ban()
	{
		$parse 						= $this->_lang;
		$parse['js_path']			= XGP_ROOT . JS_PATH;
		$ban_name					= isset ( $_GET['ban_name'] ) ? parent::$db->escape_value ( $_GET['ban_name'] ) : NULL;

		if ( isset ( $_GET['banuser'] ) && isset ( $_GET['ban_name'] ) )
		{
			$parse['name']			= $ban_name;
			$parse['banned_until']	= '';
			$parse['changedate']	= $this->_lang['bn_auto_lift_ban_message'];
			$parse['vacation']		= '';

			$banned_user			= parent::$db->query_fetch ( "SELECT b.*, s.`setting_user_id`, s.`setting_vacations_status`
																	FROM `" . BANNED . "` AS b
																	INNER JOIN `" . SETTINGS . "` AS s
																		ON s.`setting_user_id` = (SELECT `user_id`
																									FROM `" . USERS . "`
																										WHERE `user_name` = '" . $ban_name . "'
																										LIMIT 1)
																	WHERE `banned_who` = '" . $ban_name . "'" );
			if ( $banned_user )
			{
				$parse['banned_until']			= $this->_lang['bn_banned_until'] . ' (' . date ( Functions_Lib::read_config ( 'date_format_extended' ) , $banned_user['banned_longer'] ) . ')';
				$parse['reason']				= $banned_user['banned_theme'];
				$parse['changedate']			= '<div style="float:left">' . $this->_lang['bn_change_date'] . '</div><div style="float:right">' . Administration_Lib::show_pop_up ( $this->_lang['bn_edit_ban_help'] ) . '</div>';
			}

			$parse['vacation']					= $banned_user['setting_vacations_status'] ? 'checked="checked"' : '';

			if ( isset ( $_POST['bannow'] ) && $_POST['bannow'] )
			{
				if ( ! is_numeric ( $_POST['days'] ) or ! is_numeric ( $_POST['hour'] ) )
				{
					$parse['alert']	= Administration_Lib::save_message ( 'warning' ,  $this->_lang['bn_all_fields_required'] );
				}
				else
				{
					$reas          		= (string)$_POST['why'];
					$days          		= (int)$_POST['days'];
					$hour          		= (int)$_POST['hour'];
					$admin_name			= $this->_current_user['user_name'];
					$admin_mail    		= $this->_current_user['user_email'];
					$current_time		= time();
					$ban_time			= $days * 86400;
					$ban_time  	   	   += $hour * 3600;

					if ( $banned_user['banned_longer'] > time() )
					{
						$ban_time  	   += ( $banned_user['banned_longer'] - time() );
					}

					if ( ( $ban_time + $current_time ) < time() )
					{
						$banned_until   = $current_time;
					}
					else
					{
						$banned_until   = $current_time + $ban_time;
					}

					if ( $banned_user )
					{
						parent::$db->query ( "UPDATE " . BANNED . "  SET
											`banned_who` = '" . $ban_name . "',
											`banned_theme` = '" . $reas . "',
											`banned_who2` = '" . $ban_name . "',
											`banned_time` = '" . $current_time . "',
											`banned_longer` = '" . $banned_until . "',
											`banned_author` = '" . $admin_name . "',
											`banned_email` = '" . $admin_mail . "'
											WHERE `banned_who2` = '".$ban_name."';" );
					}
					else
					{
						parent::$db->query ( "INSERT INTO " . BANNED . " SET
											`banned_who` = '" . $ban_name . "',
											`banned_theme` = '" . $reas . "',
											`banned_who2` = '" . $ban_name . "',
											`banned_time` = '" . $current_time . "',
											`banned_longer` = '" . $banned_until . "',
											`banned_author` = '" . $admin_name . "',
											`banned_email` = '" . $admin_mail . "';" );
					}

					$user_id	= parent::$db->query_fetch ( "SELECT `user_id`
																FROM " . USERS . "
																WHERE `user_name` = '" . $ban_name . "' LIMIT 1" );

					parent::$db->query ( "UPDATE " . USERS . " AS u, " . SETTINGS . " AS s, " . PLANETS . " AS p SET
											u.`user_banned` = '" . $banned_until . "',
											s.`setting_vacations_status` = '" . ( isset ( $_POST['vacat'] ) ? 1 : 0 ) . "',
											p.`planet_building_metal_mine_porcent` = '0',
											p.`planet_building_crystal_mine_porcent` = '0',
											p.`planet_building_deuterium_sintetizer_porcent` = '0'
											WHERE u.`user_id` = " . $user_id['user_id'] . "
													AND s.`setting_user_id` = " . $user_id['user_id'] . "
													AND p.`planet_user_id` = " . $user_id['user_id'] . ";" );

					$parse['alert']	= Administration_Lib::save_message ( 'ok' , ( str_replace ( '%s' , $ban_name , $this->_lang['bn_ban_success'] ) ) );
				}
			}
		}
		else
		{
			Functions_Lib::redirect ( 'admin.php?page=ban' );
		}

		return parent::$page->parse_template ( parent::$page->get_template ( "adm/ban_result_view" ) , $parse );
	}

	/**
	 * method get_users_list
	 * param
	 * return the users list (left select)
	 */
	private function get_users_list()
	{
		$query_order			= ( isset ( $_GET['order'] )  && $_GET['order'] == 'id' ) ? 'user_id' : 'user_name';
		$where_authlevel		= '';
		$where_banned			= '';
		$users_list				= '';

		if ( $this->_current_user['user_authlevel'] != 3 )
		{
			$where_authlevel	= "WHERE `user_authlevel` < '" . ( $this->_current_user['user_authlevel'] ) . "'";
		}

		if ( isset ( $_GET['view'] ) && ( $_GET['view'] == 'user_banned' ) )
		{
			if ( $this->_current_user['user_authlevel'] == 3 )
			{
				$where_banned	=	"WHERE `user_banned` <> '0'";
			}
			else
			{
				$where_banned	=	"AND `user_banned` <> '1'";
			}
		}

		// get the users according to the filters
		$users_query				=	parent::$db->query ( "SELECT `user_id`, `user_name`, `user_banned`
																FROM `" . USERS . "`
																" . $where_authlevel . " " . $where_banned . "
																ORDER BY " . $query_order . " ASC" );

		while ( $user = parent::$db->fetch_array ( $users_query ) )
		{
			$status	= '';

			if ( $user['user_banned'] == 1 )
			{
				$status	= $this->_lang['bn_status'];
			}

			$users_list	.=	'<option value="' . $user['user_name'] . '">' . $user['user_name'] . '&nbsp;&nbsp;(ID:&nbsp;' . $user['user_id'] . ')' . $status . '</option>';

			$this->_users_count++;
		}

		parent::$db->free_result ( $users_query ); // free resources

		return $users_list; // return builded list
	}

	/**
	 * method get_banned_list
	 * param
	 * return the banned users list (right select)
	 */
	private function get_banned_list()
	{
		$order			= ( isset ( $_GET['order2'] ) && $_GET['order2'] == 'id' ) ? 'user_id' : 'user_name';
		$banned_list	= '';

		// get the banned users
		$banned_query	= parent::$db->query ( "SELECT `user_id`, `user_name`
													FROM `" . USERS . "`
													WHERE `user_banned` <> '0'
													ORDER BY " . $order . " ASC");

		while ( $user = parent::$db->fetch_array ( $banned_query ) )
		{
			$banned_list	.=	'<option value="' . $user['user_name'] . '">' . $user['user_name'] . '&nbsp;&nbsp;(ID:&nbsp;' . $user['user_id'] . ')</option>';

			$this->_banned_count++;
		}

		parent::$db->free_result ( $banned_query ); // free resources

		return $banned_list; // return builded list
	}
}
/* end of ban.php */