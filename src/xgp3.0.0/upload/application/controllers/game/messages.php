<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Messages extends XGPCore
{
	const MODULE_ID = 18;

	private $_lang;
	private $_current_user;
	private $_have_premium;
	private $_message_type	= array (
										0 => array ( 'type_name' => 'espioopen' ),
										1 => array ( 'type_name' => 'combatopen' ),
										2 => array ( 'type_name' => 'expopen' ),
										3 => array ( 'type_name' => 'allyopen' ),
										4 => array ( 'type_name' => 'useropen' ),
										5 => array ( 'type_name' => 'generalopen' )
									);

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
		$this->_have_premium	= Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_commander'] );

		// build the page
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
		// some values by default
		$parse				= $this->_lang;
		$parse['js_path']	= XGP_ROOT . JS_PATH;

		// display an specific category of items
		if ( isset ( $_GET['dsp'] ) && $_GET['dsp'] == 1 && $this->_have_premium )
		{
			$mode			= '';
			$get_messages	= '';

			foreach ( $_GET as $field => $value )
			{
				if ( Functions_Lib::in_multiarray ( $field , $this->_message_type ) )
				{
					$type_id			= Functions_lib::recursive_array_search ( $field , $this->_message_type );
					$get_messages	   .= $type_id . ',';
					$active[$type_id]	= 1;
				}
			}

			// get list of messages
			$message_list	= parent::$db->query ( "SELECT *
														FROM `" . MESSAGES . "`
														WHERE `message_receiver` = " . $this->_current_user['user_id'] . "
															AND `message_type` IN (" . rtrim ( $get_messages , ',' ) . ");" );

			// set messages as read
			parent::$db->query ( "UPDATE `" . MESSAGES . "`
									SET `message_read` = '1'
									WHERE `message_receiver` = " . $this->_current_user['user_id'] . "
										AND `message_type` IN (" . rtrim ( $get_messages , ',' ) . ");" );
		}
		else
		{
			$mode			= isset ( $_GET['mode'] ) ? $_GET['mode'] : NULL;
		}

		// to delete something
		$to_delete    		= isset ( $_POST['deletemessages'] ) ? $_POST['deletemessages'] : NULL;

		if ( isset ( $to_delete ) )
		{
			$mode			= "delete";
		}

		$write_to			= isset ( $_GET['id'] ) ? (int)$_GET['id'] : NULL;

		switch ( $mode )
		{
			case 'write':

				$text		= '';
				$error_page	= '';

				if ( !is_numeric ( $write_to ) )
				{
					Functions_Lib::redirect ( 'game.php?page=messages' );
				}
				else
				{
					$OwnerHome		= 	parent::$db->query_fetch ( "SELECT u.`user_name`, p.`planet_galaxy`, p.`planet_system`, p.`planet_planet`
																		FROM " . PLANETS . " AS p
																		INNER JOIN " . USERS . " as u ON p.planet_user_id = u.user_id
																		WHERE p.`planet_user_id` = '" . (int)$write_to . "';" );

					if ( !$OwnerHome )
					{
						Functions_Lib::redirect ( 'game.php?page=messages' );
					}
				}

				if ( $_POST )
				{
					$error 	= 	0;

					if ( !$_POST['subject'] )
					{
						$error++;
						$parse['error_text']	=	$this->_lang['mg_no_subject'];
						$parse['error_color']	=	'#FF0000';
						$error_page				=	parent::$page->parse_template ( parent::$page->get_template ( 'messages/messages_error_table' ) , $parse );
					}

					if ( !$_POST['text'] )
					{
						$error++;
						$parse['error_text']	=	$this->_lang['mg_no_text'];
						$parse['error_color']	=	'#FF0000';
						$error_page				=	parent::$page->parse_template ( parent::$page->get_template ( 'messages/messages_error_table' ) , $parse );
					}

					if ( $error == 0 )
					{
						$parse['error_text']	= $this->_lang['mg_msg_sended'];
						$parse['error_color']	= '#00FF00';
						$error_page				= parent::$page->parse_template ( parent::$page->get_template ( 'messages/messages_error_table' ) , $parse );
						$Owner   				= $write_to;
						$Sender  				= $this->_current_user['user_id'];
						$From    				= $this->_current_user['user_name'] . ' [' .$this->_current_user['user_galaxy'] . ':' . $this->_current_user['user_system'] . ':' . $this->_current_user['user_planet'] . ']';
						$Subject 				= $_POST['subject'];
						$Message				= Functions_Lib::format_text ( $_POST['text'] );

						Functions_Lib::send_message ( $Owner , $Sender , '' , 4 , $From , $Subject , $Message );

						$subject 				= '';
						$text    				= '';
					}
				}

				$parse['id']           		= $write_to;
				$parse['to']           		= $OwnerHome['user_name'] . ' [' .$OwnerHome['planet_galaxy'] . ':' . $OwnerHome['planet_system'] . ':' . $OwnerHome['planet_planet'] . ']';
				$parse['subject']      		= ( !isset ( $subject ) ) ? $this->_lang['mg_no_subject'] : $subject;
				$parse['text']         		= $text;
				$parse['status_message']	= $error_page;

				parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'messages/messages_pm_form_view' ) , $parse ) );

			break;

			case 'delete':

				if($to_delete == 'deleteall')
				{
					parent::$db->query ( "DELETE FROM " . MESSAGES . "
										WHERE `message_receiver` = '" . $this->_current_user['user_id'] . "';" );
				}
				elseif ( $to_delete == 'deletemarked' )
				{
					foreach ( $_POST as $Message => $Answer )
					{
						if ( preg_match ( "/delmes/i" , $Message ) && $Answer == 'on' )
						{
							$MessId   = str_replace("delmes", "", $Message);
							$MessHere = parent::$db->query_fetch ( "SELECT *
																	FROM " . MESSAGES . "
																	WHERE `message_id` = '". (int)$MessId ."' AND
																			`message_receiver` = '" . $this->_current_user['user_id'] . "';" );

							if ( $MessHere )
							{
								parent::$db->query ( "DELETE FROM " . MESSAGES . "
														WHERE `message_id` = '" . (int)$MessId . "';" );
							}
						}
					}
				}
				elseif ( $to_delete == 'deleteunmarked' )
				{
					foreach ( $_POST as $Message => $Answer )
					{
						$CurMess    	= preg_match ( "/showmes/i" , $Message );
						$MessId     	= str_replace ( "showmes" , "" , $Message );
						$Selected   	= "delmes" . $MessId;
						$IsSelected		= $_POST[$Selected];

						if ( preg_match ( "/showmes/i" , $Message ) && !isset ( $IsSelected ) )
						{
							$MessHere = parent::$db->query_fetch ( "SELECT *
																	FROM " . MESSAGES . "
																	WHERE `message_id` = '" . (int)$MessId . "' AND
																			`message_receiver` = '" . $this->_current_user['user_id'] . "';" );

							if ( $MessHere )
							{
								parent::$db->query_fetch ( "DELETE FROM " . MESSAGES . "
															WHERE `message_id` = '" . (int)$MessId . "';" );
							}
						}
					}
				}

				Functions_Lib::redirect ( 'game.php?page=messages' );

			break;

			default:

				if ( $this->_have_premium )
				{
					$type_row_template				= parent::$page->get_template ( 'messages/messages_body_premium_row_view' );
					$rows							= '';
					$this->make_counts();

					while ( $messages_list = parent::$db->fetch_assoc ( $this->_messages_count ) )
					{
						$this->_message_type[$messages_list['message_type']]['count']	= $messages_list['message_type_count'];
						$this->_message_type[$messages_list['message_type']]['unread']	= $messages_list['unread_count'];
					}

					foreach ( $this->_message_type as $id => $data )
					{
						$parse['message_type']		= $data['type_name'];
						$parse['message_type_name']	= $this->_lang['mg_type'][$id];
						$parse['message_amount']	= isset ( $data['count'] ) ? $data['count'] : 0;
						$parse['message_unread']	= isset ( $data['unread'] ) ? $data['unread'] : 0;
						$parse['checked']			= ( isset ( $active[$id] ) ? 'checked' : '' );
						$parse['checked_status']	= ( isset ( $active[$id] ) ? 1 : 0 );

						$rows		   			   .= parent::$page->parse_template ( $type_row_template , $parse );
					}

					$parse['message_type_rows']		= $rows;
					$parse['buddys_count']			= $this->_extra_count['buddys_count'];
					$parse['alliance_count']		= $this->_extra_count['alliance_count'];
					$parse['operators_count']		= $this->_extra_count['operators_count'];
					$parse['notes_count']			= $this->_extra_count['notes_count'];
					$parse['message_list']			= isset ( $message_list ) ? $this->load_messages ( $message_list ) : '';
					$parse['delete_options']		= isset ( $_GET['dsp'] ) ? $this->load_delete_box() : '';
				}
				else
				{
					// get list of messages
					$message_list	= parent::$db->query ( "SELECT *
																FROM `" . MESSAGES . "`
																WHERE `message_receiver` = " . $this->_current_user['user_id'] . ";" );

					// set messages as read
					parent::$db->query ( "UPDATE `" . MESSAGES . "`
											SET `message_read` = '1'
											WHERE `message_receiver` = " . $this->_current_user['user_id'] . ";" );


					$single_message_template	= parent::$page->get_template ( 'messages/messages_list_row_view' );
					$list_of_messages			= '';

					while ( $message = parent::$db->fetch_array ( $message_list ) )
					{
						$message['message_text']	= nl2br ( $message['message_text'] );
						$list_of_messages		   .= parent::$page->parse_template ( $single_message_template , $message );
					}

					$parse['message_list']	= $list_of_messages;
				}

				parent::$page->display ( parent::$page->parse_template ( $this->set_default_template() , $parse ) );

			break;
		}
	}

	/**
	 * method load_messages
	 * param
	 * return the list of messages
	 */
	private function load_messages ( $messages )
	{
		$single_message_template	= parent::$page->get_template ( 'messages/messages_list_row_view' );
		$list_of_messages			= '';

		while ( $message = parent::$db->fetch_array ( $messages ) )
		{
			$message['message_text']	= nl2br ( $message['message_text'] );
			$list_of_messages		   .= parent::$page->parse_template ( $single_message_template , $message );
		}

		$parse					= $this->_lang;
		$parse['message_list']	= $list_of_messages;

		return	parent::$page->parse_template ( parent::$page->get_template ( 'messages/messages_list_container_view' ) , $parse );
	}

	/**
	 * method load_delete_box
	 * param
	 * return the list of delete actions
	 */
	private function load_delete_box ()
	{
		return parent::$page->parse_template ( parent::$page->get_template ( 'messages/messages_delete_options_view' ) , $this->_lang );
	}

	/**
	 * method make_counts
	 * param
	 * return some counts
	 */
	private function make_counts ()
	{
		$this->_messages_count	= parent::$db->query ( "SELECT `message_type`,
																COUNT(`message_type`) AS message_type_count,
																SUM(`message_read` = 0) AS unread_count
															FROM " . MESSAGES . "
															WHERE `message_receiver` = '" . $this->_current_user['user_id'] . "'
															GROUP BY `message_type`" );

		$this->_extra_count		= parent::$db->query_fetch ( "SELECT
																( SELECT COUNT(`user_id`)
																		FROM `" . USERS . "`
																		WHERE `user_ally_id` = '" . $this->_current_user['user_ally_id'] . "' AND `user_ally_id` <> 0
																 ) AS alliance_count,

																 ( SELECT COUNT(`buddy_id`)
																 		FROM `" . BUDDY . "`
																 		WHERE `buddy_sender` = '" . $this->_current_user['user_ally_id'] . "' OR `buddy_receiver` = '" . $this->_current_user['user_ally_id'] . "'
																 ) AS buddys_count,

																 ( SELECT COUNT(`note_id`)
																 		FROM `" . NOTES . "`
																 		WHERE `note_owner` = '" . $this->_current_user['user_ally_id'] . "'
																 ) AS notes_count,

																 ( SELECT COUNT(`user_id`)
																 		FROM " . USERS . "
																 		WHERE user_authlevel <> 0
																 ) AS operators_count"
															);
	}

	/**
	 * method set_default_template
	 * param
	 * return default template
	 */
	private function set_default_template ()
	{
		if ( $this->_have_premium )
		{
			return parent::$page->get_template ( 'messages/messages_body_premium_view' );
		}
		else
		{
			return parent::$page->get_template ( 'messages/messages_body_common_view' );
		}
	}
}
/* end of messages.php */