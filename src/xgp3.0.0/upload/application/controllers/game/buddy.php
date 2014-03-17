<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Buddy extends XGPCore
{
	const MODULE_ID = 20;

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
		$mode					= isset ( $_GET['mode'] ) ? intval ( $_GET['mode'] ) : NULL;
		$bid					= isset ( $_GET['bid'] ) ? intval ( $_GET['bid'] ) : NULL;
		$sm						= isset ( $_GET['sm'] ) ? intval ( $_GET['sm'] ) : NULL;
		$user					= isset ( $_GET['u'] ) ? intval ( $_GET['u'] ) : NULL;
		$this->_lang['js_path']	= XGP_ROOT . JS_PATH;
		$parse					= $this->_lang;
		$requestsSended			= '';
		$requestsReceived		= '';
		$budys					= '';

		switch ( $mode )
		{
			case 1:

				switch ( $sm )
				{
					// REJECT / CANCEL
					case 1:

						$senderID = parent::$db->query_fetch ( "SELECT *
																	FROM " . BUDDY . "
																	WHERE `buddy_id`='" . intval ( $bid ) . "'" );

						if ( $senderID['buddy_status'] == 0 )
						{
							if ( $senderID['buddy_sender'] != $this->_current_user['user_id'] )
							{
								Functions_Lib::send_message ( $senderID['buddy_sender'] , $this->_current_user['user_id'] , '' , 5 , $this->_current_user['user_name'] , $this->_lang['bu_rejected_title'] , str_replace ( '%u' , $this->_current_user['user_name'] , $this->_lang['bu_rejected_text'] ) );
							}
							elseif ( $senderID['buddy_sender'] == $this->_current_user['user_id'] )
							{
								Functions_Lib::send_message ( $senderID['buddy_receiver'] , $this->_current_user['user_id'] , '' , 5 , $this->_current_user['user_name'] , $this->_lang['bu_rejected_title'] , str_replace ( '%u' , $this->_current_user['user_name'] , $this->_lang['bu_rejected_title'] ) );
							}
						}
						else
						{
							if ( $senderID['buddy_sender'] != $this->_current_user['user_id'] )
							{
								Functions_Lib::send_message ( $senderID['buddy_sender'] , $this->_current_user['user_id'] , '' , 5 , $this->_current_user['user_name'] , $this->_lang['bu_deleted_title'] , str_replace ( '%u' , $this->_current_user['user_name'] , $this->_lang['bu_deleted_text'] ) );
							}
							elseif ( $senderID['buddy_sender'] == $this->_current_user['user_id'] )
							{
								Functions_Lib::send_message ( $senderID['buddy_receiver'] , $this->_current_user['user_id'] , '' , 5 , $this->_current_user['user_name'] , $this->_lang['bu_deleted_title'] , str_replace ( '%u' , $this->_current_user['user_name'] , $this->_lang['bu_deleted_text'] ) );
							}
						}

						parent::$db->query ( "DELETE FROM " . BUDDY . "
												WHERE `buddy_id`='" . intval ( $bid ) . "' AND
														(`buddy_receiver`='" . $this->_current_user['user_id'] . "' OR `buddy_sender`='" . $this->_current_user['user_id'] . "') " );

						Functions_Lib::redirect ( 'game.php?page=buddy' );

					break;

						// ACCEPT
					case 2:

						$senderID = parent::$db->query_fetch ( "SELECT *
																FROM " . BUDDY . "
																WHERE `buddy_id`='" . intval ( $bid ) . "'" );

						Functions_Lib::send_message ( $senderID['buddy_sender'] , $this->_current_user['user_id'] , '' , 5 , $this->_current_user['user_name'] , $this->_lang['bu_accepted_title'] , str_replace ( '%u' , $this->_current_user['user_name'] , $this->_lang['bu_accepted_text'] ) );

						parent::$db->query ( "UPDATE " . BUDDY . "
												SET `buddy_status` = '1'
												WHERE `buddy_id` ='" . intval ( $bid ) . "' AND
														`buddy_receiver`='" . $this->_current_user['user_id'] . "'" );

						Functions_Lib::redirect ( 'game.php?page=buddy' );

					break;

						// SEND REQUEST
					case 3:

						$query = parent::$db->query_fetch ( "SELECT `buddy_id`
																FROM " . BUDDY . "
																WHERE (`buddy_receiver`='" . intval ( $this->_current_user['user_id'] ) . "' AND
																		`buddy_sender`='" . intval ( $_POST['user'] ) . "') OR
																		(`buddy_receiver`='" . intval ( $_POST['user'] ) . "' AND
																			`buddy_sender`='" . intval( $this->_current_user['user_id'] ) . "')" );

						if ( !$query )
						{

							$text = parent::$db->escape_value ( strip_tags ( $_POST['text'] ) );

							Functions_Lib::send_message ( intval ( $_POST['user'] ) , $this->_current_user['user_id'] , '' , 5 , $this->_current_user['user_name'] , $this->_lang['bu_to_accept_title'] , str_replace ( '%u' , $this->_current_user['user_name'] , $this->_lang['bu_to_accept_text'] ) );

							parent::$db->query ( "INSERT INTO " . BUDDY . " SET
													`buddy_sender`='" . intval ( $this->_current_user['user_id'] ) . "',
													`buddy_receiver`='" . intval ( $_POST['user'] ) . "',
													`buddy_status`='0',
													`buddy_request_text`='" . $text . "'" );

							Functions_Lib::redirect ( 'game.php?page=buddy' );
						}
						else
						{
							Functions_Lib::message ( $this->_lang['bu_request_exists'] , 'game.php?page=buddy' , 2 , FALSE , FALSE , FALSE );
						}

						break;
						// ANY OTHER OPTION EXIT
					default:

						Functions_Lib::redirect ( 'game.php?page=buddy' );

					break;
				}

				break;

				// FRIENDSHIP REQUEST
			case 2:

				// IF USER = REQUESTED USER, SHOW ERROR.
				if ( $user == $this->_current_user['user_id'] )
				{
					Functions_Lib::message ( $this->_lang['bu_cannot_request_yourself'] , 'game.php?page=buddy' , 2 , FALSE , FALSE , FALSE );
				}
				else
				{
					// SEARCH THE PLAYER
					$player				= parent::$db->query_fetch ( "SELECT `user_name`
																		FROM " . USERS . "
																		WHERE `user_id`='" . intval ( $user ) . "'" );

					// IF PLAYER EXISTS, PROCEED
					if ( $player )
					{
						$parse['user']		= $user;
						$parse['player']	= $player['user_name'];

						parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'buddy/buddy_request' ) , $parse )  , FALSE , '' , FALSE  );
					}
					else // EXIT
					{
						Functions_Lib::redirect ( 'game.php?page=buddy' );
					}
				}

				break;

				// NOTHING SELECTED
			default:

				$getBuddys 		= parent::$db->query ( "SELECT *
														FROM " . BUDDY . "
														WHERE `buddy_sender`='" . intval ( $this->_current_user['user_id'] ) . "' OR
																`buddy_receiver`='" . intval ( $this->_current_user['user_id'] ) . "'" );

				$subTemplate	= parent::$page->get_template ( 'buddy/buddy_row' );

				while ( $buddy = parent::$db->fetch_assoc ( $getBuddys ) )
				{
					if ( $buddy['buddy_status'] == 0 )
					{
						if ( $buddy['buddy_sender'] == $this->_current_user['user_id'] )
						{
							$buddy_receiver = parent::$db->query_fetch ( "SELECT u.`user_id`, u.`user_name`, u.`user_galaxy`, u.`user_system`, u.`user_planet`, u.`user_ally_id`, a.`alliance_name`
																			FROM " . USERS . " AS u
																			LEFT JOIN `" . ALLIANCE . "` AS a ON a.`alliance_id` = u.`user_ally_id`
																			WHERE u.`user_id`='" . intval ( $buddy['buddy_receiver'] ) . "'" );

							$parse['id']				= $buddy_receiver['user_id'];
							$parse['username']			= $buddy_receiver['user_name'];
							$parse['ally_id']			= $buddy_receiver['user_ally_id'];
							$parse['alliance_name']		= $buddy_receiver['alliance_name'];
							$parse['galaxy']			= $buddy_receiver['user_galaxy'];
							$parse['system']			= $buddy_receiver['user_system'];
							$parse['planet']			= $buddy_receiver['user_planet'];
							$parse['text']				= $buddy['buddy_request_text'];
							$parse['action']			= '<a href="game.php?page=buddy&mode=1&sm=1&bid=' . $buddy['buddy_id'] . '">' . $this->_lang['bu_cancel_request'] . '</a>';

							$requestsSended .= parent::$page->parse_template ( $subTemplate , $parse );
						}
						else
						{
							$buddy_sender	= parent::$db->query_fetch ( "SELECT `user_id`, `user_name`, `user_galaxy`, `user_system`, `user_planet`,`user_ally_id`, `alliance_name`
																			FROM " . USERS . "
																			WHERE `user_id`='" . intval ( $buddy['buddy_sender'] ) . "'" );

							$parse['id']				= $buddy_sender['user_id'];
							$parse['username']			= $buddy_sender['user_name'];
							$parse['ally_id']			= $buddy_sender['user_ally_id'];
							$parse['alliance_name']		= $buddy_sender['alliance_name'];
							$parse['galaxy']			= $buddy_sender['user_galaxy'];
							$parse['system']			= $buddy_sender['user_system'];
							$parse['planet']			= $buddy_sender['user_planet'];
							$parse['text']				= $buddy['buddy_request_text'];
							$parse['action']			= '<a href="game.php?page=buddy&mode=1&sm=2&bid=' . $buddy['buddy_id'] . '">' . $this->_lang['bu_accept'] . '</a><br /><a href="game.php?page=buddy&mode=1&sm=1&bid=' . $buddy['buddy_id'] . '">' . $this->_lang['bu_decline'] . '</a>';

							$requestsReceived .= parent::$page->parse_template ( $subTemplate , $parse );
						}
					}
					else
					{
						if ( $buddy['buddy_sender'] == $this->_current_user['user_id'] )
						{
							$buddy_receiver = parent::$db->query_fetch ( "SELECT `user_id`, `user_name`, `user_onlinetime`, `user_galaxy`, `user_system`, `user_planet`,`user_ally_id`, `alliance_name`
																			FROM " . USERS . "
																			WHERE `user_id`='" . intval ( $buddy['buddy_receiver'] ) . "'" );
						}
						else
						{
							$buddy_receiver = parent::$db->query_fetch ( "SELECT `user_id`, `user_name`, `user_onlinetime`, `user_galaxy`, `user_system`, `user_planet`,`user_ally_id`, `alliance_name`
																			FROM " . USERS . "
																			WHERE `user_id`='" . intval ( $buddy['buddy_sender'] ) . "'" );
						}

						$parse['id']				= $buddy_receiver['user_id'];
						$parse['username']			= $buddy_receiver['user_name'];
						$parse['ally_id']			= $buddy_receiver['user_ally_id'];
						$parse['alliance_name']		= $buddy_receiver['alliance_name'];
						$parse['galaxy']			= $buddy_receiver['user_galaxy'];
						$parse['system']			= $buddy_receiver['user_system'];
						$parse['planet']			= $buddy_receiver['user_planet'];
						$parse['text']				= '<font color="' . ( ( $buddy_receiver['user_onlinetime'] + 60 * 10 >= time() ) ? 'lime">' . $this->_lang['bu_connected'] . '' : ( ( $buddy_receiver['user_onlinetime'] + 60 * 15 >= time() )? 'yellow">' . $this->_lang['bu_fifteen_minutes'] : 'red">' . $this->_lang['bu_disconnected'] ) ) . '</font>';
						$parse['action']			= '<a href="game.php?page=buddy&mode=1&sm=1&bid=' . $buddy['buddy_id'] . '">' . $this->_lang['bu_delete'] . '</a>';

						$budys .= parent::$page->parse_template ( $subTemplate , $parse );
					}
				}

				$parse['request_received']	= $requestsSended;
				$parse['request_sended']	= $requestsReceived;
				$parse['buddys']			= $budys;

				parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'buddy/buddy_body' ) , $parse ) , FALSE , '' , FALSE );

				break;
		}
	}
}
/* end of buddy.php */