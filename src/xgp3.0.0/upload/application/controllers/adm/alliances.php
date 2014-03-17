<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Alliances extends XGPCore
{
	private $_lang;
	private $_edit;
	private $_planet;
	private $_moon;
	private $_id;
	private $_alert_info;
	private $_alert_type;
	private $_user_query;
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

	######################################
	#
	# main methods
	#
	######################################

	/**
	 * method build_page
	 * param
	 * return main method, loads everything
	 */
	private function build_page()
	{
		$parse				= $this->_lang;
		$parse['alert']		= '';
		$alliance			= isset ( $_GET['alliance'] ) ? trim ( $_GET['alliance'] ) : NULL;
		$type				= isset ( $_GET['type'] ) ? trim ( $_GET['type'] ) : NULL;
		$this->_edit		= isset ( $_GET['edit'] ) ? trim ( $_GET['edit'] ) : NULL;

		if ( $alliance != '' )
		{
			if ( !$this->check_alliance ( $alliance ) )
			{
				$parse['alert']	= Administration_Lib::save_message ( 'error' , $this->_lang['al_nothing_found'] );
				$alliance		= '';
			}
			else
			{
				if ( $_POST )
				{
					// save the data
					$this->save_data ( $type );
				}

				$this->_alliance_query	= parent::$db->query_fetch ( "SELECT a.*, als.*
					           											FROM " . ALLIANCE . " AS a
					           											INNER JOIN " . ALLIANCE_STATISTICS . " AS als ON als.alliance_statistic_alliance_id = a.alliance_id
					           											WHERE (a.`alliance_id` = '{$this->_id}') LIMIT 1;");
			}
		}

		$parse['type']		= ( $type != '' ) ? $type : 'info';
		$parse['alliance']	= ( $alliance != '' ) ? $alliance : '';
		$parse['status']	= ( $alliance != '' ) ? '' : ' disabled';
		$parse['status_box']= ( $alliance != '' ) ? '' : ' disabled';
		$parse['tag']		= ( $alliance != '' ) ? 'a' : 'button';
		$parse['content']	= ( $alliance != '' && $type != '' ) ? $this->get_data ( $type ) : '';

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( "adm/alliances_view" ) , $parse ) );
	}

	/**
	 * method get_data
	 * param $type
	 * return the page for the current type
	 */
	private function get_data ( $type )
	{
		switch ( $type )
		{
			case 'info':
			case '':
			default:

				return $this->get_data_info();

			break;

			case 'ranks':

				return $this->get_data_ranks();

			break;

			case 'members':

				return $this->get_data_members();

			break;
		}
	}

	/**
	 * method save_data
	 * param $type
	 * return save data for the current type
	 */
	private function save_data ( $type )
	{
		switch ( $type )
		{
			case 'info':
			case '':
			default:

				// save the data
				if ( isset ( $_POST['send_data'] ) && $_POST['send_data'] )
				{
					$this->save_info();
				}

			break;

			case 'ranks':

				$this->save_ranks();

			break;

			case 'members':

				$this->save_members();

			break;
		}
	}

	######################################
	#
	# get_data methods
	#
	######################################

	/**
	 * method get_data_info
	 * param
	 * return the information page for the current alliance
	 */
	private function get_data_info()
	{
		$parse											= $this->_lang;
		$parse	   							   		   += (array)$this->_alliance_query;
		$parse['al_alliance_information']				= str_replace ( '%s' , $this->_alliance_query['alliance_name'] , $this->_lang['al_alliance_information'] );
		$parse['alliance_register_time']				= ( $this->_alliance_query['alliance_register_time'] == 0 ) ? '-' : date ( Functions_Lib::read_config ( 'date_format_extended' ) , $this->_alliance_query['alliance_register_time'] );
		$parse['alliance_owner']						= $this->build_users_combo ( $this->_alliance_query['alliance_owner'] );
		$parse['sel1']									= $this->_alliance_query['alliance_request_notallow'] == 1 ? 'selected' : '';
		$parse['sel0']									= $this->_alliance_query['alliance_request_notallow'] == 0 ? 'selected' : '';
		$parse['alert_info']							= ( $this->_alert_type != '' ) ? Administration_Lib::save_message ( $this->_alert_type , $this->_alert_info ) : '';

		return parent::$page->parse_template ( parent::$page->get_template ( "adm/alliances_information_view" ) , $parse );
	}

	/**
	 * method get_data_ranks
	 * param
	 * return the ranks page for the current alliance
	 */
	private function get_data_ranks()
	{
		$parse								= $this->_lang;
		$parse['al_alliance_ranks']			= str_replace ( '%s' , $this->_alliance_query['alliance_name'] , $this->_lang['al_alliance_ranks'] );
		$parse['image_path']				= XGP_ROOT . DEFAULT_SKINPATH;
		$parse['ally_ranks_old']			= base64_encode ( $this->_alliance_query['alliance_ranks'] );
		$alliance_ranks						= unserialize ( $this->_alliance_query['alliance_ranks'] );
		$template							= parent::$page->get_template ( "adm/alliances_ranks_row_view" );
		$i									= 0;
		$ranks								= '';

		if ( ! empty ( $alliance_ranks ) )
		{
			foreach ( $alliance_ranks as $rank_id => $rank_data )
			{

				$rank_data['delete']					= $rank_data['delete'] ? 'checked' : '';
				$rank_data['kick']						= $rank_data['kick'] ? 'checked' : '';
				$rank_data['bewerbungen']				= $rank_data['bewerbungen'] ? 'checked' : '';
				$rank_data['memberlist']				= $rank_data['memberlist'] ? 'checked' : '';
				$rank_data['bewerbungenbearbeiten']		= $rank_data['bewerbungenbearbeiten'] ? 'checked' : '';
				$rank_data['administrieren']			= $rank_data['administrieren'] ? 'checked' : '';
				$rank_data['onlinestatus']				= $rank_data['onlinestatus'] ? 'checked' : '';
				$rank_data['mails']						= $rank_data['mails'] ? 'checked' : '';
				$rank_data['rechtehand']				= $rank_data['rechtehand'] ? 'checked' : '';
				$rank_data['i']							= $i++;

				$ranks	   .= parent::$page->parse_template ( $template , $rank_data );
			}
		}

		$parse['ranks_table']				= empty ( $ranks ) ? $this->_lang['al_no_ranks'] : $ranks;
		$parse['alert_info']				= ( $this->_alert_type != '' ) ? Administration_Lib::save_message ( $this->_alert_type , $this->_alert_info ) : '';

		return parent::$page->parse_template ( parent::$page->get_template ( "adm/alliances_ranks_view" ) , $parse );
	}

	/**
	 * method get_research_info
	 * param
	 * return the research page for the current user
	 */
	private function get_data_members()
	{
		$parse							= $this->_lang;
		$parse['al_alliance_members']	= str_replace ( '%s' , $this->_alliance_query['alliance_name'] , $this->_lang['al_alliance_members'] );
		$all_members					= $this->get_members();
		$alliance_ranks					= unserialize ( $this->_alliance_query['alliance_ranks'] );
		$template						= parent::$page->get_template ( 'adm/alliances_members_row_view' );
		$members						= '';

		if ( ! empty ( $all_members ) )
		{
			while ( $member = parent::$db->fetch_assoc ( $all_members ) )
			{
				$member['alliance_request']			= ( $member['alliance_request'] ) ? $this->_lang['al_request_yes'] : $this->_lang['al_request_no'];
				$member['ally_request_text']		= ( $member['ally_request_text'] ) ? $this->_lang['ally_request_text'] : '-';
				$member['alliance_register_time']	= date ( Functions_Lib::read_config ( 'date_format_extended' ) , $member['alliance_register_time'] );

				if ( $member['user_id'] == $member['alliance_owner'] )
				{
					$member['ally_rank']		= $member['alliance_owner_range'];
				}
				else
				{
					if ( isset ( $member['ally_rank'] ) )
					{
						$member['ally_rank']	= $alliance_ranks[$member['ally_rank']]['name'];
					}
					else
					{
						$member['ally_rank']	= $this->_lang['al_rank_not_defined'];
					}
				}

				$members			   .= parent::$page->parse_template ( $template , $member );
			}
		}

		$parse['members_table']			= empty ( $members ) ? '<tr><td colspan="6" class="align_center text-error">' . $this->_lang['al_no_ranks'] .'</td></tr>' : $members;
		$parse['alert_info']			= ( $this->_alert_type != '' ) ? Administration_Lib::save_message ( $this->_alert_type , $this->_alert_info ) : '';

		return parent::$page->parse_template ( parent::$page->get_template ( "adm/alliances_members_view" ) , $parse );
	}

	######################################
	#
	# save / update methods
	#
	######################################

	/**
	 * method save_info
	 * param
	 * return save information for the current user
	 */
	private function save_info()
	{
		$alliance_name				= isset ( $_POST['alliance_name'] ) ? $_POST['alliance_name'] : '';
		$alliance_name_orig			= isset ( $_POST['alliance_name_orig'] ) ? $_POST['alliance_name_orig'] : '';
		$alliance_tag				= isset ( $_POST['alliance_tag'] ) ? $_POST['alliance_tag'] : '';
		$alliance_tag_orig			= isset ( $_POST['ally_tag_orig'] ) ? $_POST['ally_tag_orig'] : '';
		$alliance_owner				= isset ( $_POST['alliance_owner'] ) ? $_POST['alliance_owner'] : '';
		$alliance_owner_orig		= isset ( $_POST['alliance_owner_orig'] ) ? $_POST['alliance_owner_orig'] : '';
		$alliance_owner_range		= isset ( $_POST['alliance_owner_range'] ) ? $_POST['alliance_owner_range'] : '';
		$alliance_web				= isset ( $_POST['alliance_web'] ) ? $_POST['alliance_web'] : '';
		$alliance_image				= isset ( $_POST['alliance_image'] ) ? $_POST['alliance_image'] : '';
		$alliance_description		= isset ( $_POST['alliance_description'] ) ? $_POST['alliance_description'] : '';
		$alliance_text				= isset ( $_POST['alliance_text'] ) ? $_POST['alliance_text'] : '';
		$alliance_request			= isset ( $_POST['alliance_request'] ) ? $_POST['alliance_request'] : '';
		$alliance_request_notallow	= isset ( $_POST['alliance_request_notallow'] ) ? $_POST['alliance_request_notallow'] : '';

		$alliance_owner				= (int)$alliance_owner;
		$alliance_request_notallow	= (int)$alliance_request_notallow;
		$errors						= '';

		if ( $alliance_name != $alliance_name_orig )
		{
			if ( $alliance_name == '' or ! $this->check_name ( $alliance_name ) )
			{
				$errors	.= $this->_lang['al_error_alliance_name'] . '<br />';
			}
		}

		if ( $alliance_tag != $alliance_tag_orig )
		{
			if ( $alliance_tag == '' or ! $this->check_tag ( $alliance_tag ) )
			{
				$errors	.= $this->_lang['al_error_alliance_tag'] . '<br />';
			}
		}

		if ( $alliance_owner != $alliance_owner_orig )
		{
			if ( $alliance_owner <= 0 or $this->check_founder ( $alliance_owner ) )
			{
				$errors	.= $this->_lang['al_error_founder'] . '<br />';
			}
		}

		if ( $errors != '' )
		{
			$this->_alert_info	= $errors;
			$this->_alert_type	= 'warning';
		}
		else
		{
			parent::$db->query ( "UPDATE " . ALLIANCE . " SET
									`alliance_name` = '" . parent::$db->escape_value ( $alliance_name ) . "',
									`alliance_tag` = '" . parent::$db->escape_value ( $alliance_tag ) . "',
									`alliance_owner` = '" . $alliance_owner . "',
									`alliance_owner_range` = '" . parent::$db->escape_value ( $alliance_owner_range ) . "',
									`alliance_web` = '" . parent::$db->escape_value ( $alliance_web ) . "',
									`alliance_image` = '" . parent::$db->escape_value ( $alliance_image ) . "',
									`alliance_description` = '" . parent::$db->escape_value ( $alliance_description ) . "',
									`alliance_text` = '" . parent::$db->escape_value ( $alliance_text ) . "',
									`alliance_request` = '" . parent::$db->escape_value ( $alliance_request ) . "',
									`alliance_request_notallow` = '" . parent::$db->escape_value ( $alliance_request_notallow ) . "'
									WHERE `alliance_id` = '" . $this->_id . "';" );

			$this->_alert_info	= $this->_lang['al_all_ok_message'];
			$this->_alert_type	= 'ok';
		}
	}

	/**
	 * method save_ranks
	 * param
	 * return save ranks for the current alliance
	 */
	private function save_ranks()
	{
		$alliance_ranks	= unserialize ( base64_decode ( $_POST['ally_ranks_old'] ) );

		if ( isset ( $_POST['create_rank'] ) )
		{
			if ( ! empty ( $_POST['rank_name'] ) )
			{
				$alliance_ranks[]	= array	(
												'name' => parent::$db->escape_value ( strip_tags ( $_POST['rank_name'] ) ),
												'mails' => 0,
												'delete' => 0,
												'kick' => 0,
												'bewerbungen' => 0,
												'administrieren' => 0,
												'bewerbungenbearbeiten' => 0,
												'memberlist' => 0,
												'onlinestatus' => 0,
												'rechtehand' => 0
											);

				$ranks 			= serialize ( $alliance_ranks );

				parent::$db->query ( "UPDATE `" . ALLIANCE . "` SET
										`alliance_ranks`= '" . $ranks . "'
											WHERE `alliance_id`= '" . $this->_id. "'" );

				$this->_alert_info	= $this->_lang['al_rank_added'];
				$this->_alert_type	= 'ok';
			}
			else
			{
				$this->_alert_info	= $this->_lang['al_required_name'];
				$this->_alert_type	= 'warning';
			}
		}

		if ( isset ( $_POST['save_ranks'] ) )
		{
			$ally_ranks_new	= array();

			foreach ( $_POST['id'] as $id )
			{
				$ally_ranks_new[$id]['name'] 					= $alliance_ranks[$id]['name'];
				$ally_ranks_new[$id]['delete']					= isset ( $_POST['u' . $id . 'r0'] ) ? 1 : 0;
				$ally_ranks_new[$id]['kick']					= isset ( $_POST['u' . $id . 'r1'] ) ? 1 : 0;
				$ally_ranks_new[$id]['bewerbungen']				= isset ( $_POST['u' . $id . 'r2'] ) ? 1 : 0;
				$ally_ranks_new[$id]['memberlist']				= isset ( $_POST['u' . $id . 'r3'] ) ? 1 : 0;
				$ally_ranks_new[$id]['bewerbungenbearbeiten']	= isset ( $_POST['u' . $id . 'r4'] ) ? 1 : 0;
				$ally_ranks_new[$id]['administrieren']			= isset ( $_POST['u' . $id . 'r5'] ) ? 1 : 0;
				$ally_ranks_new[$id]['onlinestatus']			= isset ( $_POST['u' . $id . 'r6'] ) ? 1 : 0;
				$ally_ranks_new[$id]['mails']					= isset ( $_POST['u' . $id . 'r7'] ) ? 1 : 0;
				$ally_ranks_new[$id]['rechtehand']				= isset ( $_POST['u' . $id . 'r8'] ) ? 1 : 0;
			}

			$ranks	=	serialize ( $ally_ranks_new );

			parent::$db->query ( "UPDATE `" . ALLIANCE . "` SET
									`alliance_ranks` = '" . $ranks . "'
									WHERE `alliance_id`= '" . $this->_id . "'" );

			$this->_alert_info	= $this->_lang['al_rank_saved'];
			$this->_alert_type	= 'ok';
		}

		if ( isset ( $_POST['delete_ranks'] ) )
		{
			foreach ( $_POST['delete_message'] as $id => $active )
			{
				if ( $active == 'on' )
				{
					unset ( $alliance_ranks[$id] );
				}
			}

			parent::$db->query ( "UPDATE `" . ALLIANCE . "` SET
									`alliance_ranks` = '" . serialize ( $alliance_ranks ) . "'
									WHERE `alliance_id`= '" . $this->_id . "'" );

			$this->_alert_info	= $this->_lang['al_rank_removed'];
			$this->_alert_type	= 'ok';
		}
	}

	/**
	 * method save_research
	 * param
	 * return save research for the current user
	 */
	private function save_members()
	{
		if ( isset ( $_POST['delete_ranks'] ) )
		{
			$ids_array	= '';

			foreach ( $_POST['delete_message'] as $user_id => $delete_status )
			{
				if ( $delete_status == 'on' && $user_id > 0 && is_numeric ( $user_id ) )
				{
					$ids_array	.= $user_id . ',';
				}
			}

			parent::$db->query ( "UPDATE " . USERS . " SET
									`user_ally_id` = 0,
									`user_ally_request` = 0,
									`user_ally_request_text` = '',
									`user_ally_rank_id` = 0
									WHERE `user_id` IN (" . rtrim ( $ids_array , ',') . ")" );

			// RETURN THE ALERT
			$this->_alert_info	= $this->_lang['us_all_ok_message'];
			$this->_alert_type	= 'ok';
		}
	}

	######################################
	#
	# build combo methods
	#
	######################################

	/**
	 * method build_users_combo
	 * param $user_id
	 * return the list of users
	 */
	private function build_users_combo ( $user_id )
	{
		$combo_rows	= '';
		$users		= parent::$db->query ( "SELECT `user_id`, `user_name`
												FROM " . USERS . ";" );

		while ( $users_row = parent::$db->fetch_array ( $users ) )
		{
			$combo_rows	.= '<option value="' . $users_row['user_id'] . '" ' . ( $users_row['user_id'] == $user_id ? ' selected' : '' ) . '>' .  $users_row['user_name'] . '</option>';
		}

		return $combo_rows;
	}

	######################################
	#
	# other required methods
	#
	######################################

	/**
	 * method check_alliance
	 * param $alliance
	 * return TRUE if alliance exists, FALSE if alliance doesn't exist
	 */
	private function check_alliance ( $alliance )
	{
		$alliance_query		= parent::$db->query_fetch ( "SELECT `alliance_id`
															FROM " . ALLIANCE . "
															WHERE `alliance_name` = '" . $alliance . "' OR
																	`alliance_tag` = '" . $alliance . "';" );

		$this->_id			= $alliance_query['alliance_id'];

		return ( $alliance_query['alliance_id'] != '' && $alliance_query != NULL );
	}

	/**
	 * method check_tag
	 * param $alliance_tag
	 * return the validated the ally tag
	 */
	private function check_tag ( $alliance_tag )
	{
		$alliance_tag		= trim ( $alliance_tag );
		$alliance_tag		= htmlspecialchars_decode ( $alliance_tag , ENT_QUOTES );

		if ( $alliance_tag == '' or is_null ( $alliance_tag ) or ( strlen ( $alliance_tag ) < 3 ) or ( strlen ( $alliance_tag ) > 8 ) )
		{
			return FALSE;
		}

		$alliance_tag		= parent::$db->escape_value ( $alliance_tag );

		$check_tag 			= parent::$db->query_fetch ( "SELECT `alliance_tag`
															FROM `" . ALLIANCE . "`
															WHERE `alliance_tag` = '" . $alliance_tag . "'" );
		if ( $check_tag )
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * method check_name
	 * param $alliance_name
	 * return the validated the ally name
	 */
	private function check_name ( $alliance_name )
	{
		$alliance_name	= trim ( $alliance_name );
		$alliance_name	= htmlspecialchars_decode ( $alliance_name , ENT_QUOTES );

		if ( $alliance_name == '' or is_null ( $alliance_name ) or ( strlen ( $alliance_name ) < 3 ) or ( strlen ( $alliance_name ) > 30 ) )
		{
			return FALSE;
		}

		$alliance_name	= parent::$db->escape_value ( $alliance_name );

		$check_name 	= parent::$db->query_fetch ( "SELECT `alliance_name`
														FROM `" . ALLIANCE . "`
														WHERE `alliance_name` = '" . $alliance_name . "'" );

		if ( $check_name )
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * method check_founder
	 * param $user_id
	 * return the ally ID to verify
	 */
	private function check_founder ( $user_id )
	{
		$ally_data	= parent::$db->query_fetch ( "SELECT `user_ally_id`, `user_ally_request`
													FROM `" . USERS . "`
													WHERE `user_id` = '" . $user_id . "';" );

		return ( $ally_data['user_ally_id'] > 0 && ! empty ( $ally_data['user_ally_id'] ) && $ally_data['user_ally_request'] > 0 && ! empty ( $ally_data['user_ally_request'] ) );
	}

	/**
	 * method get_members
	 * param
	 * return the list of users that belong to the alliance
	 */
	private function get_members ()
	{
		return parent::$db->query ( "SELECT u.`user_id`,
											u.`user_name`,
											u.`user_ally_request`,
											u.`user_ally_request_text`,
											u.`user_ally_register_time`,
											u.`user_ally_rank_id`,
											a.`alliance_owner`,
											a.`alliance_owner_range`,
											a.`alliance_ranks`
										FROM `" . USERS . "` AS u
										LEFT JOIN `" . ALLIANCE . "` AS a ON a.`alliance_id` = u.`user_ally_id`
										WHERE u.`user_ally_id` = '" . $this->_id . "';" );
	}
}
/* end of alliances.php */