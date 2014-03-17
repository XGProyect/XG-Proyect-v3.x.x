<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Maker extends XGPCore
{
	private $_current_user;
	private $_creator;
	private $_alert;
	private $_lang;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		// check if session is active
		parent::$users->check_session();

		$this->_lang			= parent::$lang;
		$this->_creator			= Functions_Lib::load_library ( 'Creator_Lib' );
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
		$parse						= $this->_lang;

		switch ( ( isset ( $_GET['mode'] ) ? $_GET['mode'] : '' ) )
		{
			case 'alliance':

				$parse['content']	= $this->make_alliace();

			break;

			case 'moon':

				$parse['content']	= $this->make_moon();

			break;

			case 'planet':

				$parse['content']	= $this->make_planet();

			break;

			case 'user':

				$parse['content']	= $this->make_user();

			break;

			case '':
			default:

				$parse['content']	= '';

			break;
		}

		$parse['alert']				= $this->_alert;

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'adm/maker_main_view' ) , $parse ) );
	}

	/**
	 * method make_alliace
	 * param
	 * return a created alliance
	 */
	private function make_alliace()
	{
		$parse					= $this->_lang;
		$parse['founders_combo']= $this->build_alliance_users_combo();

		if ( isset ( $_POST['add_alliance'] ) && $_POST['add_alliance'] )
		{
			$alliance_name		= parent::$db->escape_value ( (string)$_POST['name'] );
			$alliance_tag		= parent::$db->escape_value ( (string)$_POST['tag'] );
			$alliance_founder	= (int)$_POST['founder'];

			$check_alliance		= parent::$db->query_fetch ( "SELECT `alliance_id`
																FROM `" . ALLIANCE . "`
																WHERE `alliance_name` = '" . $alliance_name . "'
																	OR `alliance_tag` = '" . $alliance_tag . "';" );

			if ( ! $check_alliance &&  ! empty ( $alliance_founder ) && $alliance_founder > 0 )
			{
				parent::$db->query ( "INSERT INTO `" . ALLIANCE . "` SET
										`alliance_name`='" . $alliance_name . "',
										`alliance_tag`='" . $alliance_tag . "' ,
										`alliance_owner`='" . $alliance_founder . "',
										`alliance_owner_range` = '" . $this->_lang['mk_alliance_founder_rank'] . "',
										`alliance_register_time`='" . time() . "'" );

				$new_alliance_id	= parent::$db->insert_id();

				parent::$db->query ( "INSERT INTO " . ALLIANCE_STATISTICS . " SET
										`alliance_statistic_alliance_id`='" . $new_alliance_id . "'" );

				parent::$db->query ( "UPDATE `" . USERS . "` SET
										`user_ally_id`='" . $new_alliance_id . "',
										`user_ally_register_time`='" . time() . "'
										WHERE `user_id`='" . $alliance_founder . "'" );

				$this->_alert	= Administration_Lib::save_message ( 'ok' , $this->_lang['mk_alliance_added'] );
			}
			else
			{
				$this->_alert	= Administration_Lib::save_message ( 'warning' , $this->_lang['mk_alliance_all_fields'] );
			}
		}

		return parent::$page->parse_template ( parent::$page->get_template ( 'adm/maker_alliance_view' ) , $parse );
	}

	/**
	 * method make_moon
	 * param
	 * return a created moon
	 */
	private function make_moon()
	{
		$parse					= $this->_lang;
		$parse['planets_combo']	= $this->build_planet_combo();

		if ( isset ( $_POST['add_moon'] ) && $_POST['add_moon'] )
		{
			$planet_id  	= (int)$_POST['planet'];
			$moon_name  	= (string)$_POST['name'];
			$diameter		= (int)$_POST['planet_diameter'];
			$temp_min		= (int)$_POST['planet_temp_min'];
			$temp_max		= (int)$_POST['planet_temp_max'];
			$max_fields		= (int)$_POST['planet_field_max'];

			$moon_planet	= 	parent::$db->query_fetch ( "SELECT p.*, (SELECT `planet_id`
																			FROM " . PLANETS . "
																			WHERE `planet_galaxy` = (SELECT `planet_galaxy`
																										FROM " . PLANETS . "
																										WHERE `planet_id` = '" . $planet_id . "'
																											AND `planet_type` = 1)
																					AND `planet_system` = (SELECT `planet_system`
																											FROM " . PLANETS . "
																											WHERE `planet_id` = '" . $planet_id . "'
																												AND `planet_type` = 1)
																					AND `planet_planet` = (SELECT `planet_planet`
																											FROM " . PLANETS . "
																											WHERE `planet_id` = '" . $planet_id . "'
																												AND `planet_type` = 1)
																					AND `planet_type` = 3) AS id_moon
																FROM " . PLANETS . " AS p
																WHERE p.`planet_id` = '" . $planet_id . "' AND
																		p.`planet_type` = '1'" );


			if ( $moon_planet && is_numeric ( $planet_id ) )
			{
				if ( $moon_planet['id_moon'] == '' && $moon_planet['planet_type'] == 1 && $moon_planet['planet_destroyed'] == 0 )
				{
					$galaxy    = $moon_planet['planet_galaxy'];
					$system    = $moon_planet['planet_system'];
					$planet    = $moon_planet['planet_planet'];
					$owner     = $moon_planet['planet_user_id'];

					if ( $_POST['diameter_check'] == 'on' )
					{
						$size       	= mt_rand ( 4500 , 9999 );
					}

					if ( $_POST['diameter_check'] != 'on' && is_numeric ( $diameter ) )
					{
						$size			= $diameter;
					}
					else
					{
						$this->_alert	= Administration_Lib::save_message ( 'warning' , $this->_lang['mk_moon_only_numbers'] );
					}


					if ( $_POST['temp_check'] == 'on' )
					{
						$maxtemp		= $moon_planet['planet_temp_max'] - mt_rand(10, 45);
						$mintemp		= $moon_planet['planet_temp_min'] - mt_rand(10, 45);
					}
					elseif ( $_POST['temp_check'] != 'on' && is_numeric ( $temp_max ) && is_numeric ( $temp_min ) )
					{
						$maxtemp		= $temp_max;
						$mintemp		= $temp_min;
					}
					else
					{
						$this->_alert	= Administration_Lib::save_message ( 'warning' , $this->_lang['mk_moon_only_numbers'] );
					}

					parent::$db->query ( "INSERT INTO " . PLANETS . " SET
											`planet_name` = '" . $moon_name . "',
											`planet_user_id` = '" . $owner . "',
											`planet_galaxy` = '" . $galaxy . "',
											`planet_system` = '" . $system . "',
											`planet_planet` = '" . $planet . "',
											`planet_last_update` = '" . time() . "',
											`planet_type` = '3',
											`planet_image` = 'mond',
											`planet_diameter` = '" . $size . "',
											`planet_field_max` = '" .$max_fields . "',
											`planet_temp_min` = '" . $mintemp . "',
											`planet_temp_max` = '" . $maxtemp . "';" );

					$last_id	= parent::$db->insert_id();

					parent::$db->query ( "INSERT INTO " . BUILDINGS . " SET
											`building_planet_id` = '" . $last_id . "';" );

					parent::$db->query ( "INSERT INTO " . DEFENSES . " SET
											`defense_planet_id` = '" . $last_id . "';" );

					parent::$db->query ( "INSERT INTO " . SHIPS . " SET
											`ship_planet_id` = '" . $last_id . "';" );

					$this->_alert		= Administration_Lib::save_message ( 'ok' , $this->_lang['mk_moon_added'] );

				}
				else
				{
					$this->_alert		= Administration_Lib::save_message ( 'warning' , $this->_lang['mk_moon_add_errors'] );
				}
			}
			else
			{
				$this->_alert			= Administration_Lib::save_message ( 'error' , $this->_lang['mk_moon_planet_doesnt_exist'] );
			}
		}

		return parent::$page->parse_template ( parent::$page->get_template ( 'adm/maker_moon_view' ) , $parse );
	}

	/**
	 * method make_planet
	 * param
	 * return a created planet
	 */
	private function make_planet()
	{
		$parse					= $this->_lang;
		$parse['users_combo']	= $this->build_users_combo();

		if ( isset ( $_POST['add_planet'] ) && $_POST['add_planet'] )
		{
			$user_id	= (int)$_POST['user'];
			$galaxy     = (int)$_POST['galaxy'];
			$system     = (int)$_POST['system'];
			$planet     = (int)$_POST['planet'];
			$name       = (string)$_POST['name'];
			$field_max	= (int)$_POST['planet_field_max'];
			$i			= 0;

			$planet_query	=	parent::$db->query_fetch ( "SELECT *
																FROM " . PLANETS . "
																WHERE `planet_galaxy` = '" . $galaxy . "' AND
																		`planet_system` = '" . $system . "' AND
																		`planet_planet` = '" . $planet . "'" );

			$user_query	=	parent::$db->query_fetch ( "SELECT *
															FROM " . USERS . "
															WHERE `user_id` = '" . $user_id . "'" );

			if ( is_numeric ( $user_id ) && isset ( $user_id ) && ! $planet_query && $user_query )
			{
				if ( $galaxy < 1 or $system < 1 or $planet < 1 or ! is_numeric ( $galaxy ) or ! is_numeric ( $system ) or ! is_numeric ( $planet ) )
				{
					$error	= $this->_lang['mk_planet_unavailable_coords'];
					$i++;
				}

				if ( $galaxy > MAX_GALAXY_IN_WORLD or $system > MAX_SYSTEM_IN_GALAXY or $planet > MAX_PLANET_IN_SYSTEM )
				{
					$error .= $this->_lang['mk_planet_wrong_coords'];
					$i++;
				}

				if ( $i == 0 )
				{
					if ( $field_max <= 0 && ! is_numeric ( $field_max ) )
					{
						$field_max	= '163';
					}

					if ( strlen ( $name ) <= 0 )
					{
						$name		= $this->_lang['mk_planet_default_name'];
					}

					$this->_creator->create_planet ( $galaxy , $system , $planet , $user_id , '' , '' , FALSE ) ;

					parent::$db->query ( "UPDATE " . PLANETS . " SET
											`planet_field_max` = '" . $field_max . "',
											`planet_name` = '" . $name . "'
											WHERE `planet_galaxy` = '". $galaxy ."'
												AND `planet_system` = '". $system ."'
												AND `planet_planet` = '". $planet ."'
												AND `planet_type` = '1'" );

					$this->_alert	= Administration_Lib::save_message ( 'ok' , $this->_lang['mk_planet_added'] );
				}
				else
				{
					$this->_alert	= Administration_Lib::save_message ( 'warning' , $error );
				}
			}
			else
			{
				$this->_alert		= Administration_Lib::save_message ( 'warning' , $this->_lang['mk_planet_unavailable_coords'] );
			}
		}

		return parent::$page->parse_template ( parent::$page->get_template ( 'adm/maker_planet_view' ) , $parse );
	}

	/**
	 * method make_user
	 * param
	 * return a created user
	 */
	private function make_user()
	{
		$parse					= $this->_lang;
		$parse['level_combo']	= $this->build_level_combo();

		if ( isset ( $_POST['add_user'] ) && $_POST['add_user'] )
		{
			$name		= (string)$_POST['name'];
			$pass 		= (string)$_POST['password'];
			$email 		= (string)$_POST['email'];
			$galaxy		= (int)$_POST['galaxy'];
			$system		= (int)$_POST['system'];
			$planet		= (int)$_POST['planet'];
			$auth		= (int)$_POST['authlevel'];
			$time		= time();
			$i			= 0;
			$error		= '';

			$check_user = parent::$db->query_fetch ( "SELECT `user_name`
														FROM " . USERS . "
														WHERE `user_name` = '" . parent::$db->escape_value ( $_POST['name'] ) . "'
														LIMIT 1" );

			$check_email = parent::$db->query_fetch ( "SELECT `user_email`
														FROM " . USERS . "
														WHERE `user_email` = '" . parent::$db->escape_value ( $_POST['email'] ) . "'
														LIMIT 1" );

			$check_planet = parent::$db->query_fetch ( "SELECT COUNT(id) AS count
														FROM " . PLANETS . "
														WHERE `planet_galaxy` = '".$galaxy."' AND
																`planet_system` = '".$system."' AND
																`planet_planet` = '".$planet."' LIMIT 1" );


			if ( ! is_numeric ( $galaxy ) && ! is_numeric ( $system ) && ! is_numeric ( $planet ) )
			{
				$error	 =	$this->_lang['mk_user_only_numbers'];
				$i++;
			}
			elseif ( $galaxy > MAX_GALAXY_IN_WORLD or $system > MAX_SYSTEM_IN_GALAXY || $planet > MAX_PLANET_IN_SYSTEM || $galaxy < 1 || $system < 1 || $planet < 1)
			{
				$error	 =	$this->_lang['mk_user_wrong_coords'];
				$i++;
			}

			if ( ! $name or ! $email or ! $galaxy or ! $system or ! $planet )
			{
				$error	.=	$this->_lang['mk_user_complete_all'];
				$i++;
			}

			if ( ! Functions_Lib::valid_email ( strip_tags ( $email ) ) )
			{
				$error	.=	$this->_lang['mk_user_invalid_email'];
				$i++;
			}

			if ( $check_user )
			{
				$error	.=	$this->_lang['mk_user_existing_name'];
				$i++;
			}

			if ( $check_email )
			{
				$error	.=	$this->_lang['mk_user_existing_email'];
				$i++;
			}

			if ( $check_planet['count'] != 0 )
			{
				$error	.= $this->_lang['mk_user_existing_planet'];
				$i++;
			}

			if ( isset ( $_POST['password_check'] ) && $_POST['password_check'] )
			{
				$pass	= $this->generate_password();
			}
			else
			{
				if ( strlen ( $pass ) < 4 )
				{
					$error	.=	$this->_lang['mk_user_invalid_password'];
					$i++;
				}
			}

			if ( $i == 0 )
			{

				parent::$db->query ( "INSERT INTO " . USERS . " SET
										`user_name` = '" . parent::$db->escape_value ( strip_tags ( $name ) ) . "',
										`user_email` = '" . parent::$db->escape_value ( $email ) . "',
										`user_email_permanent` = '" . parent::$db->escape_value ( $email ) . "',
										`user_ip_at_reg` = '" . $_SERVER['REMOTE_ADDR'] . "',
										`user_home_planet_id` = '0',
										`user_register_time` = '" .$time. "',
										`user_onlinetime` = '" .$time. "',
										`user_authlevel` = '" .$auth. "',
										`user_password`='" . sha1 ( $pass ) . "';" );

				$last_user_id	= parent::$db->insert_id();

				$this->_creator->create_planet ( $galaxy , $system , $planet , $last_user_id , '' , TRUE );

				$last_planet_id	= parent::$db->insert_id();

				parent::$db->query ( "UPDATE " . USERS . " SET
										`user_home_planet_id` = '" . $last_planet_id . "',
										`user_current_planet` = '" . $last_planet_id . "',
										`user_galaxy` = '" . $galaxy . "',
										`user_system` = '" . $system . "',
										`user_planet` = '" . $planet . "'
										WHERE `user_id` = '" . $last_user_id . "'
										LIMIT 1;" );

				$this->_alert	= Administration_Lib::save_message ( 'ok' , str_replace ( '%s' , $pass , $this->_lang['mk_user_added'] ) );
			}
			else
			{
				$this->_alert	= Administration_Lib::save_message ( 'warning' , '<br/>' . $error );
			}

		}

		return parent::$page->parse_template ( parent::$page->get_template ( 'adm/maker_user_view' ) , $parse );
	}

	/**
	 * method build_users_combo
	 * param
	 * return the list of users
	 */
	private function build_users_combo()
	{
		$combo_rows	= '';
		$users		= parent::$db->query ( "SELECT `user_id`, `user_name`
												FROM " . USERS . ";" );

		while ( $users_row = parent::$db->fetch_array ( $users ) )
		{
			if ( isset ( $_GET['user'] ) && $_GET['user'] > 0 )
			{
				$combo_rows	.= '<option value="' . $users_row['user_id'] . '" ' . ( $_GET['user'] == $users_row['user_id'] ? ' selected' : '' ) . '>' .  $users_row['user_name'] . '</option>';
			}
			else
			{
				$combo_rows	.= '<option value="' . $users_row['user_id'] . '">' .  $users_row['user_name'] . '</option>';
			}
		}

		return $combo_rows;
	}

	/**
	 * method build_planet_combo
	 * param
	 * return the list of the user planets
	 */
	private function build_planet_combo()
	{
		$combo_rows	= '';
		$planets	= parent::$db->query ( "SELECT `planet_id`, `planet_name`, `planet_galaxy`, `planet_system`, `planet_planet`
												FROM `" . PLANETS . "`
												WHERE `planet_destroyed` = '0'
													AND `planet_type` = '1';" );

		while ( $planets_row = parent::$db->fetch_array ( $planets ) )
		{
			if ( isset ( $_GET['planet'] ) && $_GET['planet'] > 0 )
			{
				$combo_rows	.= '<option value="' . $planets_row['id'] . '" ' . ( $_GET['planet'] == $planets_row['id'] ? 'selected' : '' ) . ' >' .  $planets_row['planet_name'] . ' [' . $planets_row['planet_galaxy'] . ':' . $planets_row['planet_system'] . ':' . $planets_row['planet_planet'] . ']' . '</option>';
			}
			else
			{
				$combo_rows	.= '<option value="' . $planets_row['id'] . '">' .  $planets_row['planet_name'] . ' [' . $planets_row['planet_galaxy'] . ':' . $planets_row['planet_system'] . ':' . $planets_row['planet_planet'] . ']' . '</option>';
			}
		}

		return $combo_rows;
	}

	/**
	 * method build_level_combo
	 * param
	 * return the list of the user levels
	 */
	private function build_level_combo()
	{
		$combo_rows	= '';

		foreach ( $this->_lang['user_level'] as $level_id => $level_text )
		{
			$combo_rows	.= '<option value="' . $level_id . '">' .  $level_text . '</option>';
		}

		return $combo_rows;
	}

	/**
	 * method build_alliance_users_combo
	 * param
	 * return the list of users without alliance
	 */
	private function build_alliance_users_combo()
	{
		$combo_rows	= '';
		$users		= parent::$db->query ( "SELECT `user_id`, `user_name`
												FROM `" . USERS . "`
												WHERE `user_ally_id` = '0'
													AND `user_ally_request` = '0';" );

		while ( $users_row = parent::$db->fetch_array ( $users ) )
		{
			$combo_rows	.= '<option value="' . $users_row['user_id'] . '">' .  $users_row['user_name'] . '</option>';
		}

		return $combo_rows;
	}

	/**
	 * generate_password()
	 * param
	 * return generates a password
	 **/
	private function generate_password()
	{
		$characters	= "aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890";
		$count		= strlen ( $characters );
		$new_pass	= "";
		$lenght		= 6;
		srand ( ( double)microtime() * 1000000 );

		for ( $i = 0 ; $i < $lenght ; $i++ )
		{
			$character_boucle	= mt_rand ( 0 , $count - 1 );
			$new_pass			= $new_pass . substr ( $characters , $character_boucle , 1 );
		}

		return $new_pass;
	}
}
/* end of maker.php */