<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Fleet3 extends XGPCore
{
	const MODULE_ID = 8;

	private $_lang;
	private $_current_user;
	private $_current_planet;

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
		$this->_current_planet	= parent::$users->get_planet_data();

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
		if ( ! isset ( $_POST ) or empty ( $_POST ) )
		{
			Functions_Lib::redirect ( 'game.php?page=fleet1' );
		}

		$resource	=	parent::$objects->get_objects();
		$pricelist	=	parent::$objects->get_price();
		$reslist	=	parent::$objects->get_objects_list();
		$lang		= 	$this->_lang;

		#####################################################################################################
		// SOME DEFAULT VALUES
		#####################################################################################################
		// ARRAYS
		$exp_values				= array ( 1 , 2 , 3 , 4 , 5 );
		$hold_values			= array ( 0 , 1 , 2 , 4 , 8 , 16 , 32 );

		// LANG
		$this->_lang['js_path']	= XGP_ROOT . JS_PATH;
		$parse					= $this->_lang;

		// LOAD TEMPLATES REQUIRED
		$mission_row_template	= parent::$page->get_template ( 'fleet/fleet3_mission_row' );
		$input_template			= parent::$page->get_template ( 'fleet/fleet3_inputs' );
		$stay_template			= parent::$page->get_template ( 'fleet/fleet3_stay_row' );
		$options_template		= parent::$page->get_template ( 'fleet/fleet_options' );

		// OTHER VALUES
		$galaxy     			= (int)$_POST['galaxy'];
		$system     			= (int)$_POST['system'];
		$planet     			= (int)$_POST['planet'];
		$planettype 			= (int)$_POST['planettype'];
		$fleet_acs 				= (int)$_POST['fleet_group'];
		$YourPlanet 			= FALSE;
		$UsedPlanet 			= FALSE;
		$MissionSelector		= '';

		// QUERYS
		$select        			= parent::$db->query_fetch	( "SELECT `planet_user_id`
																FROM `" . PLANETS . "`
																WHERE `planet_galaxy` = '" . $galaxy . "'
																	AND `planet_system` = '" . $system . "'
																	AND `planet_planet` = '" . $planet . "'
																	AND `planet_type` = '" . $planettype . "';");

		if ( $select )
		{
			if ( $select['planet_user_id'] == $this->_current_user['user_id'] )
			{
				$YourPlanet = TRUE;
				$UsedPlanet = TRUE;
			}
			else
			{
				$UsedPlanet = TRUE;
			}
		}

		if ( $_POST['planettype'] == 2 )
		{
			if ($_POST['ship209'] >= 1)
			{
				$missiontype = array ( 8 => $this->_lang['type_mission'][8] );
			}
			else
			{
				$missiontype = array();
			}
		}
		elseif ($_POST['planettype'] == 1 or $_POST['planettype'] == 3)
		{
			if ($_POST['ship208'] >= 1 && !$UsedPlanet)
			{
				$missiontype = array ( 7 => $this->_lang['type_mission'][7] );
			}

			elseif ($_POST['ship210'] >= 1 && !$YourPlanet)
			{
				$missiontype = array ( 6 => $this->_lang['type_mission'][6] );
			}


			if ( $_POST['ship202'] >= 1 or
				 $_POST['ship203'] >= 1 or
				 $_POST['ship204'] >= 1 or
				 $_POST['ship205'] >= 1 or
				 $_POST['ship206'] >= 1 or
				 $_POST['ship207'] >= 1 or
				 $_POST['ship210'] >= 1 or
				 $_POST['ship211'] >= 1 or
				 $_POST['ship213'] >= 1 or
				 $_POST['ship214'] >= 1 or
				 $_POST['ship215'] >= 1 )
			{

				if ( !$YourPlanet )
				{
					$missiontype[1] = $this->_lang['type_mission'][1];
				}

				$missiontype[3] 	= $this->_lang['type_mission'][3];
				$missiontype[5] 	= $this->_lang['type_mission'][5];
			}
		}
		elseif ( $_POST['ship209'] >= 1 or $_POST['ship208'] )
		{
			$missiontype[3] 		= $this->_lang['type_mission'][3];
		}

		if ($YourPlanet)
		{
			$missiontype[4] 		= $this->_lang['type_mission'][4];
		}

		if ($_POST['planettype'] == 3 || $_POST['planettype'] == 1 && ($fleet_acs > 0) && $UsedPlanet)
		{
			$acs = parent::$db->query_fetch ( "SELECT `acs_fleet_galaxy`, `acs_fleet_planet``, `acs_fleet_system`, `acs_fleet_planet_type`
												FROM `" . ACS_FLEETS . "`
												WHERE `acs_fleet_id` = '" . $fleet_acs . "';");

			if ( 	$acs['acs_fleet_galaxy'] == $galaxy &&
					$acs['acs_fleet_planet'] == $planet &&
					$acs['acs_fleet_system'] == $system &&
					$acs['acs_fleet_planet_type'] == $planettype )
			{
				$missiontype[2] 	= $this->_lang['type_mission'][2];
			}
		}

		if($_POST['planettype'] == 3 && $_POST['ship214'] >= 1 && !$YourPlanet && $UsedPlanet)
		{
			$missiontype[9] = $this->_lang['type_mission'][9];
		}

		$fleetarray    		= unserialize(base64_decode(str_rot13($_POST['usedfleet'])));
		$mission       		= $_POST['target_mission'];
		$SpeedFactor   		= $_POST['speedfactor'];
		$AllFleetSpeed 		= Fleets_Lib::fleet_max_speed ($fleetarray, 0, $this->_current_user);
		$GenFleetSpeed 		= $_POST['speed'];
		$MaxFleetSpeed 		= min($AllFleetSpeed);
		$distance      		= Fleets_Lib::target_distance($_POST['thisgalaxy'], $_POST['galaxy'], $_POST['thissystem'], $_POST['system'], $_POST['thisplanet'], $_POST['planet']);
		$duration      		= Fleets_Lib::mission_duration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
		$consumption   		= Fleets_Lib::fleet_consumption($fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $this->_current_user);

		#####################################################################################################
		// INPUTS DATA
		#####################################################################################################
		$parse['metal'] 			= floor($this->_current_planet['planet_metal']);
		$parse['crystal'] 			= floor($this->_current_planet['planet_crystal']);
		$parse['deuterium'] 		= floor($this->_current_planet['planet_deuterium']);
		$parse['consumption'] 		= $consumption;
		$parse['distance']			= $distance;
		$parse['speedfactor'] 		= $_POST['speedfactor'];
		$parse['thisgalaxy'] 		= $_POST['thisgalaxy'];
		$parse['thissystem'] 		= $_POST['thissystem'];
		$parse['thisplanet'] 		= $_POST['thisplanet'];
		$parse['galaxy'] 			= $_POST['galaxy'];
		$parse['system'] 			= $_POST['system'];
		$parse['planet'] 			= $_POST['planet'];
		$parse['thisplanettype']	= $_POST['thisplanettype'];
		$parse['planettype'] 		= $_POST['planettype'];
		$parse['speedallsmin'] 		= $_POST['speedallsmin'];
		$parse['speed'] 			= $_POST['speed'];
		$parse['speedfactor'] 		= $_POST['speedfactor'];
		$parse['usedfleet'] 		= $_POST['usedfleet'];
		$parse['maxepedition'] 		= $_POST['maxepedition'];
		$parse['curepedition'] 		= $_POST['curepedition'];
		$parse['fleet_group'] 		= $_POST['fleet_group'];
		$parse['acs_target_mr'] 	= $_POST['acs_target_mr'];

		#####################################################################################################
		// EXTRA INPUTS
		#####################################################################################################
		$input_extra	= '';

		foreach ( $fleetarray as $Ship => $Count )
		{
			$input_parse['ship']		=	$Ship;
			$input_parse['amount']		=	$Count;
			$input_parse['capacity']	=	$pricelist[$Ship]['capacity'];
			$input_parse['consumption']	=	Fleets_Lib::ship_consumption ( $Ship , $this->_current_user );
			$input_parse['speed']		=	Fleets_Lib::fleet_max_speed ( "" , $Ship , $this->_current_user );

			$input_extra .= parent::$page->parse_template ( $input_template , $input_parse );
		}

		#####################################################################################################
		// TOP TABLE TITLE
		#####################################################################################################
		if ( $_POST['thisplanettype'] == 1 )
		{
			$parse['title'] = "". $_POST['thisgalaxy'] .":". $_POST['thissystem'] .":". $_POST['thisplanet'] ." - ".$this->_lang['fl_planet']."";

		}
		elseif ( $_POST['thisplanettype'] == 3 )
		{
			$parse['title'] = "". $_POST['thisgalaxy'] .":". $_POST['thissystem'] .":". $_POST['thisplanet'] ." - ".$this->_lang['fl_moon']."";
		}

		#####################################################################################################
		// MISSION TYPES
		#####################################################################################################
		if ( count ( $missiontype ) > 0 )
		{
			if ( $planet == 16 )
			{
				$parse_mission['value']					= 15;
				$parse_mission['mission']				= $this->_lang['type_mission'][15];
				$parse_mission['expedition_message']	= $this->_lang['fl_expedition_alert_message'];
				$parse_mission['id']					= ' ';
				$parse_mission['checked']				= ' checked="checked"';

				$MissionSelector	.=	parent::$page->parse_template ( $mission_row_template , $parse_mission );
			}
			else
			{
				$i = 0;

				foreach ( $missiontype as $a => $b )
				{
					$parse_mission['value']					= $a;
					$parse_mission['mission']				= $b;
					$parse_mission['expedition_message']	= '';
					$parse_mission['id']					= ' id="inpuT_' . $i . '" ';
					$parse_mission['checked']				= ( ( $mission == $a ) ? ' checked="checked"' : '' );

					$i++;

					$MissionSelector	.=	parent::$page->parse_template ( $mission_row_template , $parse_mission );
				}
			}
		}
		else
		{
			Functions_Lib::redirect ( 'game.php?page=fleet1' );
		}

		#####################################################################################################
		// STAY / EXPEDITION BLOCKS
		#####################################################################################################
		$stay_row['options']	= '';

		if ( $planet == 16 )
		{
			$stay_row['stay_type']			= 'expeditiontime';

			foreach ( $exp_values as $value )
			{
				$stay['value']			= $value;
				$stay['selected']		= '';
				$stay['title']			= $value;

				$stay_row['options']  .= parent::$page->parse_template ( $options_template , $stay );
			}

			$StayBlock = parent::$page->parse_template ( $stay_template , array_merge ( $stay_row , $this->_lang ) );
		}
		elseif ( $missiontype[5] != '' )
		{
			$stay_row['stay_type']			= 'holdingtime';

			foreach ( $hold_values as $value )
			{

				$stay['value']			= $value;
				$stay['selected']		= ( ( $value == 1 ) ? ' selected' : '' );
				$stay['title']			= $value;

				$stay_row['options']  .= parent::$page->parse_template ( $options_template , $stay );
			}

			$StayBlock = parent::$page->parse_template ( $stay_template , array_merge ( $stay_row , $this->_lang ) );
		}

		$parse['input_extra'] 			= $input_extra;
		$parse['missionselector'] 		= $MissionSelector;
		$parse['stayblock'] 			= $StayBlock;

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'fleet/fleet3_table' ) , $parse ) );
	}
}
/* end of fleet3.php */