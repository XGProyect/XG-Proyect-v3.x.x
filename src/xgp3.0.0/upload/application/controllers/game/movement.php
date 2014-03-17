<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Movement extends XGPCore
{
	const MODULE_ID	= 9;

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

		$this->send_back_fleet();
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
		//	ELEMENTS
		$resource			= parent::$objects->get_objects();

		// QUERYS
		$count				= parent::$db->query_fetch ( "SELECT
															(SELECT COUNT(fleet_owner) AS `actcnt`
																FROM " . FLEETS . "
																WHERE `fleet_owner` = '" . $this->_current_user['user_id'] . "') AS max_fleet,
															(SELECT COUNT(fleet_owner) AS `expedi`
																FROM " . FLEETS . "
																WHERE `fleet_owner` = '" . $this->_current_user['user_id'] . "'
																	AND `fleet_mission` = '15') AS max_expeditions");

		// LANGUAGE
		$this->_lang['js_path']	= XGP_ROOT . JS_PATH;
		$parse 					=  $this->_lang;

		$MaxFlyingFleets    	= $count['max_fleet'];
		$MaxExpedition      	= $this->_current_user[$resource[124]];

		if ($MaxExpedition >= 1)
		{
			$ExpeditionEnCours  = $count['max_expeditions'];
			$EnvoiMaxExpedition = Fleets_Lib::get_max_expeditions ( $MaxExpedition );
		}
		else
		{
			$ExpeditionEnCours 	= 0;
			$EnvoiMaxExpedition = 0;
		}

		$MaxFlottes		= Fleets_Lib::get_max_fleets ( $this->_current_user[$resource[108]] , $this->_current_user['premium_officier_admiral'] );
		$missiontype	= Fleets_Lib::get_missions();
		$ShipData       = '';

		$parse['flyingfleets']			= $MaxFlyingFleets;
		$parse['maxfleets']				= $MaxFlottes;
		$parse['currentexpeditions']	= $ExpeditionEnCours;
		$parse['maxexpeditions']		= $EnvoiMaxExpedition;
		$i  							= 0;
		$flying_fleets					= '';

		if ( $count['max_fleet'] <> 0 or $MaxExpedition <> 0 )
		{

			$fq = parent::$db->query ( "SELECT *
										FROM " . FLEETS . "
										WHERE fleet_owner = '" . $this->_current_user['user_id'] . "'");

			while ( $f = parent::$db->fetch_array ( $fq ) )
			{
				$i++;

				$parse['num']				=	$i;
				$parse['fleet_mission']		=	$missiontype[$f['fleet_mission']];

				if ( Fleets_Lib::is_fleet_returning ( $f ) )
				{
					$parse['tooltip']		=	 $this->_lang['fl_returning'];
					$parse['title']			=	 $this->_lang['fl_r'];
				}
				else
				{
					$parse['tooltip']		=	 $this->_lang['fl_onway'];
					$parse['title']			=	 $this->_lang['fl_a'];
				}

				$fleet 						= 	explode ( ";" , $f['fleet_array'] );
				$e 							= 	0;
				$parse['fleet']				=	'';

				foreach ( $fleet as $a => $b )
				{
					if ( $b != '' )
					{
						$e++;
						$a 					= explode(",", $b);
						$parse['fleet']    .=  $this->_lang['tech'][$a[0]]. ":". $a[1] ."\n";

						if ($e > 1)
						{
							$parse['fleet'].= "\t";
						}
					}
				}

				$parse['fleet_amount']		=	Format_Lib::pretty_number ( $f['fleet_amount'] );
				$parse['fleet_start']		=	Format_Lib::pretty_coords ( $f['fleet_start_galaxy'] , $f['fleet_start_system'] , $f['fleet_start_planet'] );
				$parse['fleet_start_time']	=	date ( Functions_Lib::read_config ( 'date_format_extended' ) , $f['fleet_creation'] );
				$parse['fleet_end']			=	Format_Lib::pretty_coords ( $f['fleet_end_galaxy'] , $f['fleet_end_system'] , $f['fleet_end_planet'] );
				$parse['fleet_end_time']	=	date ( Functions_Lib::read_config ( 'date_format_extended' ) , $f['fleet_start_time'] );
				$parse['fleet_arrival']		=	date ( Functions_Lib::read_config ( 'date_format_extended' ) , $f['fleet_end_time'] );

				//now we can view the call back button for ships in maintaing position (2)
				if ($f['fleet_mess'] == 0 or $f['fleet_mess'] == 2)
				{
					$parse['inputs']  = '<form action="game.php?page=movement&action=return" method="post">';
					$parse['inputs'] .= '<input name="fleetid\" value="' . $f['fleet_id'] . '" type="hidden">';
					$parse['inputs'] .= '<input value="' . $this->_lang['fl_send_back'] . '" type="submit" name="send">';
					$parse['inputs'] .= '</form>';

					if ($f['fleet_mission'] == 1)
					{
						$parse['inputs']	.= '<a href="#" onClick="f(\'game.php?page=federationlayer&union=' . $f['fleet_group'] . '&fleet=' . $f['fleet_id'] . '\', \'\')">';
						$parse['inputs']	.= '<input value="' . $this->_lang['fl_acs'] . '" type="button">';
						$parse['inputs']	.= '</a>';
					}
				}
				else
				{
					$parse['inputs'] = '&nbsp;-&nbsp;';
				}

				$flying_fleets	.= parent::$page->parse_template ( parent::$page->get_template ( 'movement/fleet_row_fleets' ) , $parse );
			}
		}

		if ( $i == 0 )
		{
			$parse['num']				=	'-';
			$parse['fleet_mission']		=	'-';
			$parse['title']				=	'';
			$parse['fleet_amount']		=	'-';
			$parse['fleet_start']		=	'-';
			$parse['fleet_start_time']	=	'-';
			$parse['fleet_end']			=	'-';
			$parse['fleet_end_time']	=	'-';
			$parse['fleet_arrival']		=	'-';
			$parse['inputs']			=	'-';

			$flying_fleets	.= parent::$page->parse_template ( parent::$page->get_template ( 'movement/fleet_row_fleets' ) , $parse );
		}

		$parse['fleetpagerow'] 			= $flying_fleets;
		$parse['envoimaxexpedition']	= $EnvoiMaxExpedition;
		$parse['expeditionencours']		= $ExpeditionEnCours;

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'movement/fleet_table' ) , $parse ) );
	}

	/**
	 * method send_back_fleet
	 * param
	 * returns the fleet to the planet
	 */
	private function send_back_fleet()
	{
		if ( ( isset ( $_POST['fleetid'] ) ) && ( is_numeric ( $_POST['fleetid'] ) ) && ( isset ( $_GET['action'] ) ) && ( $_GET['action'] == 'return' ) )
		{
			$fleet_id  	= (int)$_POST['fleetid'];
			$i 			= 0;
			$fleet_row 	= parent::$db->query_fetch ( "SELECT *
														FROM " . FLEETS . "
														WHERE `fleet_id` = '". $fleet_id ."';" );

			if ( $fleet_row['fleet_owner'] == $this->_current_user['user_id'] )
			{
				if ( $fleet_row['fleet_mess'] == 0 or $fleet_row['fleet_mess'] == 2 )
				{
					if ( $fleet_row['fleet_group'] > 0 )
					{
						$acs	= parent::$db->query_fetch ( "SELECT `acs_fleet_members`
																FROM `" . ACS_FLEETS . "`
																WHERE `acs_fleet_id` = '". $fleet_row['fleet_group'] ."';" );

						if ( $acs['acs_fleet_members'] == $fleet_row['fleet_owner'] && $fleet_row['fleet_mission'] == 1 )
						{
							parent::$db->query ( "DELETE FROM `" . ACS_FLEETS . "`
													WHERE `acs_fleet_id` ='". $fleet_row['fleet_group'] ."';" );

							parent::$db->query ( "UPDATE " . FLEETS . " SET
													`fleet_group` = '0'
													WHERE `fleet_group` = '". $fleet_row['fleet_group'] ."';" );
						}

						if ( $fleet_row['fleet_mission'] == 2 )
						{
							parent::$db->query ("UPDATE " . FLEETS . " SET
												`fleet_group` = '0'
												WHERE `fleet_id` = '".  $fleet_id ."';" );
						}
					}

					$CurrentFlyingTime	= time() - $fleet_row['fleet_creation'];
					$fleetLeght			= $fleet_row['fleet_start_time'] - $fleet_row['fleet_creation'];
					$ReturnFlyingTime  	= ( $fleet_row['fleet_end_stay'] != 0 && $CurrentFlyingTime > $fleetLeght ) ? $fleetLeght + time() : $CurrentFlyingTime + time();


					parent::$db->query ( "UPDATE " . FLEETS . " SET
											`fleet_start_time` = '". (time() - 1) ."',
											`fleet_end_stay` = '0',
											`fleet_end_time` = '". ($ReturnFlyingTime + 1) ."',
											`fleet_target_owner` = '". $this->_current_user['user_id'] ."',
											`fleet_mess` = '1'
											WHERE `fleet_id` = '" . $fleet_id . "';" );
				}
			}
		}
	}
}
/* end of movement.php */