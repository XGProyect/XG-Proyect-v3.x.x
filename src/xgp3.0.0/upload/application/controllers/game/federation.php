<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Federation extends XGPCore
{
	const MODULE_ID	= 8;

	private $_lang;
	private $_current_user;
	private $_fleet_id;

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
		#####################################################################################################
		// SOME DEFAULT VALUES
		#####################################################################################################
		// LOAD TEMPLATES REQUIRED
		$options_template	= parent::$page->get_template ( 'fleet/fleet_options' );

		// LANGUAGE
		$parse 				= $this->_lang;

		// OTHER VALUES
		$this->_fleet_id 	= isset ( $_GET['fleet'] ) ? (int)$_GET['fleet'] : NULL;
		$union				= isset ( $_GET['union'] ) ? (int)$_GET['union'] : NULL;
		$acs_user_message	= '';

		if ( !is_numeric ( $this->_fleet_id ) or empty ( $this->_fleet_id ) or !is_numeric ( $union ) or empty ( $union ) )
		{
			Functions_Lib::redirect ( 'game.php?page=fleet1' );
		}

		// QUERY
		$fleet 			= parent::$db->query_fetch ( "SELECT `fleet_id`,
																`fleet_start_time`,
																`fleet_end_time`,
																`fleet_mess`,
																`fleet_group`,
																`fleet_end_galaxy`,
																`fleet_end_system`,
																`fleet_end_planet`,
																`fleet_end_type`
														FROM " . FLEETS . "
														WHERE fleet_id = '" . intval ( $this->_fleet_id ) . "'" );

		$query_buddies	= parent::$db->query ( "SELECT `user_id`, `user_name`
													FROM " . BUDDY . " AS b
													LEFT JOIN " . USERS . " AS u ON ((u.user_id = b.buddy_sender) OR (u.user_id = b.buddy_receiver))
													WHERE (`buddy_sender` = '" . $this->_current_user['user_id'] . "' OR
															`buddy_receiver` = '" . $this->_current_user['user_id'] . "') AND
															`buddy_status` = '1';" );

		if ( $fleet['fleet_id'] == '' )
		{
			Functions_Lib::redirect ( 'game.php?page=fleet1' );
		}

		// ACTIONS
		if ( $_POST['save_acs'] )
		{
			$this->set_name ( $_POST['name_acs'] );
		}

		// REMOVE A MEMBER
		if ( $_POST['remove'] )
		{
			$this->remove_user ( $_POST['members_list'] );
		}

		// ADD A MEMBER
		if ( $_POST['search_user'] or $_POST['add'] )
		{
			$user_to_add	= $_POST['search_user'] ? '' : $_POST['add'] ? $_POST['friends_list'] : '';

			if ( $this->add_user ( $user_to_add ) )
			{
				$acs_user_message	= "<font color=\"lime\">" . $this->_lang['fl_player'] . " " . $_POST['addtogroup'] . " " .  $this->_lang['fl_add_to_attack'] . "</font>";
			}
			else
			{
				$acs_user_message	= "<font color=\"red\">" . $this->_lang['fl_player'] . " " . $_POST['addtogroup'] . " " . $this->_lang['fl_dont_exist'] . "</font>";
			}
		}

		if ( $fleet['fleet_start_time'] <= time() or $fleet['fleet_end_time'] < time() or $fleet['fleet_mess'] == 1 )
		{
			Functions_Lib::redirect ( 'game.php?page=fleet1' );
		}

		if ( empty ( $fleet['fleet_group'] ) )
		{
			$rand 				= mt_rand ( 100000 , 999999999 );
			$acs_code 			= "AG" . $rand;
			$federation_invited = intval ( $this->_current_user['user_id'] );

			parent::$db->query ( "INSERT INTO " . ACS_FLEETS . " SET
									`acs_fleet_name` = '" . $acs_code . "',
									`acs_fleet_members` = '" . $this->_current_user['user_id'] . "',
									`acs_fleet_fleets` = '" . $this->_fleet_id . "',
									`acs_fleet_galaxy` = '" . $fleet['fleet_end_galaxy'] . "',
									`acs_fleet_system` = '" . $fleet['fleet_end_system'] . "',
									`acs_fleet_planet` = '" . $fleet['fleet_end_planet'] . "',
									`acs_fleet_planet_type` = '" . $fleet['fleet_end_type'] . "',
									`acs_fleet_invited` = '" . $federation_invited . "'" );

			$acs_id			= parent::$db->insert_id();
			$acs_madnessred = parent::$db->query ( "SELECT `acs_fleet_invited`, `acs_fleet_name`
													FROM " . ACS_FLEETS . "
													WHERE `acs_fleet_name` = '" . $acs_code . "' AND
															`acs_fleet_members` = '" . $this->_current_user['user_id'] . "' AND
															`acs_fleet_fleets` = '" . $this->_fleet_id . "' AND
															`acs_fleet_galaxy` = '" . $fleet['fleet_end_galaxy'] . "' AND
															`acs_fleet_system` = '" . $fleet['fleet_end_system'] . "' AND
															`acs_fleet_planet` = '" . $fleet['fleet_end_planet'] . "' AND
															`acs_fleet_invited` = '" . $this->_current_user['user_id'] . "'" );

			parent::$db->query ( "UPDATE " . FLEETS . "
									SET fleet_group = '" . $acs_id . "'
									WHERE fleet_id = '" . intval ( $this->_fleet_id ) . "'" );
		}
		else
		{
			$acs_madnessred = parent::$db->query ( "SELECT `acs_fleet_invited`, `acs_fleet_name`
													FROM " . ACS_FLEETS . "
													WHERE acs_fleet_id = '" . intval ( $fleet['fleet_group'] ) . "'" );
		}


		$row 				= parent::$db->fetch_array ( $acs_madnessred );
		$federation_invited	= $row['acs_fleet_invited'];
		$parse['acs_code']	= $row['acs_fleet_name'];
		$members 			= explode ( "," , $federation_invited );
		$members_count		= 0;

		foreach ( $members as $a => $b )
		{
			if ( $b != '' )
			{
				$member_qry 	= parent::$db->query ( "SELECT `user_name`
														FROM " . USERS . "
														WHERE `user_id` ='".intval($b)."' ;" );

				while ( $row = parent::$db->fetch_array ( $member_qry ) )
				{
					$members_option['value']	= $row['user_name'];
					$members_option['selected']	= '';
					$members_option['title']	= $row['user_name'];
					$members_row    			.= parent::$page->parse_template ( $options_template , $members_option );
				}
			}
			$members_count++;
		}


		while ( $buddies = parent::$db->fetch_array ( $query_buddies ) )
		{
			if ( $buddies['user_id'] != $this->_current_user['user_id'] )
			{
				$members_option['value']	= $buddies['user_name'];
				$members_option['selected']	= '';
				$members_option['title']	= $buddies['user_name'];
				$friends_row    		   .= parent::$page->parse_template ( $options_template , $members_option );
			}
		}

		$parse['friends']				= $friends_row;
		$parse['invited_count']			= $members_count;
		$parse['invited_members']		= $members_row;
		$parse['fleetid']				= $_GET['fleetid'];
		$parse['federation_invited']	= $federation_invited;
		$parse['add_user_message']		= $acs_user_message;

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'fleet/fleet_federation' ) , $parse ) , FALSE , '' , FALSE );
	}

	/**
	 * method set_name
	 * param $acs_name
	 * return set acs name
	 */
	private function set_name ( $acs_name )
	{
		$name_len	= strlen ( $acs_name );

		if ( $name_len >= 3 && $name_len <= 20  )
		{
			parent::$db->query ( "UPDATE " . ACS_FLEETS . "
									SET `acs_fleet_name` = '" . parent::$db->escape_value ( $acs_name ) . "'
									WHERE acs_fleet_members = '" . intval ( $this->_current_user['user_id'] ) . "';" );
		}

		return TRUE;
	}

	/**
	 * method add_user
	 * param
	 * return search and add the user
	 */
	private function add_user ( $member_name = '' )
	{
		if ( $member_name == '' )
		{
			$member_name	= $_POST['addtogroup'];
		}

		$added_user_id		= 0;
		$member_qry			= parent::$db->query_fetch ( "SELECT `user_id`
															FROM " . USERS . "
															WHERE `user_name` ='" . parent::$db->escape_value ( $member_name ) . "';" );

		if ( ( $member_qry['user_id'] != NULL ) && ( $this->members_count ( $_POST['federation_invited'] ) < 5 ) && ( $member_qry['user_id'] != $this->_current_user['user_id'] ) )
		{
			$new_member_string	= parent::$db->escape_value ( $_POST['federation_invited'] ) . ',' . $member_qry['user_id'];

			parent::$db->query ( "UPDATE " . ACS_FLEETS . " SET
									`acs_fleet_invited` = '" . $new_member_string . "'
									WHERE `acs_fleet_fleets` = '" . $this->_fleet_id . "';" );

			$invite_message = $this->_lang['fl_player'] . $this->_current_user['user_name'] . $this->_lang['fl_acs_invitation_message'];
			Functions_Lib::send_message ( $member_qry['user_id'] , $this->_current_user['user_id'] , '' , 5 , $this->_current_user['user_name'] , $this->_lang['fl_acs_invitation_title'] , $invite_message );

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method add_user
	 * param
	 * return search and add the user
	 */
	private function remove_user ( $member_name = '' )
	{
		$remove_user_id		= 0;
		$member_qry			= parent::$db->query_fetch ( "SELECT `user_id`
															FROM " . USERS . "
															WHERE `user_name` ='" . parent::$db->escape_value ( $member_name ) . "';" );

		if ( ( $member_qry['user_id'] != NULL ) && ( $this->members_count ( $_POST['federation_invited'] ) >= 1 ) && ( $member_qry['user_id'] != $this->_current_user['user_id'] ) )
		{
			$members	= explode ( ',' , $_POST['federation_invited'] );

			foreach ( $members as $member_id )
			{
				if ( $member_qry['user_id'] != $member_id )
				{
					$new_member_string .= $member_id . ',';
				}
			}

			$new_member_string	= substr_replace ( $new_member_string , '' , -1 );

			parent::$db->query ( "UPDATE " . ACS_FLEETS . " SET
									`acs_fleet_invited` = '" . $new_member_string . "'
									WHERE `acs_fleet_fleets` = '" . $this->_fleet_id . "';" );

			$invite_message = $this->_lang['fl_player'] . $this->_current_user['user_name'] . $this->_lang['fl_acs_invitation_message'];
			Functions_Lib::send_message ( $member_qry['user_id'] , $this->_current_user['user_id'] , '' , 5 , $this->_current_user['user_name'] , $this->_lang['fl_acs_invitation_title'] , $invite_message );

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method can_add_members
	 * param $members_array
	 * return TRUE if can add members, FALSE if queue is full
	 */
	private function members_count ( $members_array )
	{
		$member_id		= explode ( ',' , $members_array );

		return count ( $member_id );
	}
}
/* end of federation.php */