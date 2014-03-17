<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

/**
 * @autor jstar,
 * @version v2
 * @copyright gnu v3
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Phalanx extends XGPCore
{
	const MODULE_ID	= 11;

	private $_lang;
	private $_formula;
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
		$this->_formula			= Functions_Lib::load_library ( 'Formula_Lib' );

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
        $parse = $this->_lang;
        /* range */
        $radar_limit_inf = $this->_current_planet['planet_system'] - $this->_formula->phalanx_range($this->_current_planet['building_phalanx']);
        $radar_limit_sup = $this->_current_planet['planet_system'] + $this->_formula->phalanx_range($this->_current_planet['building_phalanx']);
        $radar_limit_inf = max($radar_limit_inf, 1);
        $radar_limit_sup = min($radar_limit_sup, MAX_SYSTEM_IN_GALAXY);

        /* input validation */
        $Galaxy = (int)$_GET['galaxy'];
        $System = (int)$_GET['system'];
        $Planet = (int)$_GET['planet'];
        $PlType = (int)$_GET['planettype'];
        /* cheater detection */
        if ( $System < $radar_limit_inf or $System > $radar_limit_sup or $Galaxy != $this->_current_planet['planet_galaxy'] or $PlType != 1 or $this->_current_planet['planet_type'] != 3 )
        {
        	Functions_Lib::redirect ( 'game.php?page=galaxy' );
        }

		/* main page */
        if ($this->_current_planet['planet_deuterium'] > 10000)
        {
            parent::$db->query ( "UPDATE " . PLANETS . " SET
            						`planet_deuterium` = `planet_deuterium` - '10000'
            						WHERE `planet_id` = '" . $this->_current_user['user_current_planet'] . "';");

            $TargetInfo 	= parent::$db->query_fetch ( "SELECT `planet_name`, `planet_user_id`
            												FROM " . PLANETS . "
            												WHERE `planet_galaxy` = '" . $Galaxy . "' AND
            														`planet_system` = '" . $System . "' AND
            														`planet_planet` = '" . $Planet . "' AND
            														`planet_type` = 1" );

            $TargetID 		= $TargetInfo['planet_user_id'];
            $TargetName 	= $TargetInfo['planet_name'];
            $TargetInfo 	= parent::$db->query_fetch ( "SELECT `planet_destroyed`
            												FROM " . PLANETS . "
            												WHERE `planet_galaxy` = '" . $Galaxy . "' AND
            														`planet_system` = '" . $System . "' AND
            														`planet_planet` = '" . $Planet . "' AND
            														`planet_type` = 3 " );
            //if there isn't a moon,
            if ($TargetInfo === false)
            {
                $TargetMoonIsDestroyed = true;
            }
            else
            {
                $TargetMoonIsDestroyed = $TargetInfo['planet_destroyed'] !== 0;
            }


            $FleetToTarget = parent::$db->query ( "SELECT *
            										FROM " . FLEETS . "
            										WHERE ( ( `fleet_start_galaxy` = '" . $Galaxy . "' AND
            													`fleet_start_system` = '" . $System . "' AND
            													`fleet_start_planet` = '" . $Planet . "' ) OR
            												( `fleet_end_galaxy` = '" . $Galaxy . "' AND
            													`fleet_end_system` = '" . $System . "' AND
            													`fleet_end_planet` = '" . $Planet . "' )
            											   ) ;" );

            $Record = 0;
            $fpage = array();
            while ( $FleetRow = parent::$db->fetch_array ( $FleetToTarget ) )
            {
                $Record++;

                $ArrivetoTargetTime = $FleetRow['fleet_start_time'];
                $EndStayTime = $FleetRow['fleet_end_stay'];
                $ReturnTime = $FleetRow['fleet_end_time'];
                $Mission = $FleetRow['fleet_mission'];
                $myFleet = ($FleetRow['fleet_owner'] == $TargetID) ? true : false;
                $FleetRow['fleet_resource_metal'] = 0;
                $FleetRow['fleet_resource_crystal'] = 0;
                $FleetRow['fleet_resource_deuterium'] = 0;
                $isStartedfromThis = $FleetRow['fleet_start_galaxy'] == $Galaxy && $FleetRow['fleet_start_system'] == $System && $FleetRow['fleet_start_planet'] == $Planet;
                $isTheTarget = $FleetRow['fleet_end_galaxy'] == $Galaxy && $FleetRow['fleet_end_system'] == $System && $FleetRow['fleet_end_planet'] == $Planet;


                /* 1)the arrive to target fleet table event
                * you can see start-fleet event only if this is a planet(or destroyed moon)
                * and if the fleet mission started from this planet is different from hold
                * or if it's a enemy mission.
                */
                if ($ArrivetoTargetTime > time())
                {
                    //scannig of fleet started planet
                    if ($isStartedfromThis && ($FleetRow['fleet_start_type'] == 1 || ($FleetRow['fleet_start_type'] == 3 && $TargetMoonIsDestroyed)))
                    {
                        if ($Mission != 4)
                        {
                            $Label = "fs";
                            $fpage[$ArrivetoTargetTime] .= "\n". Fleets_Lib::flying_fleets_table($FleetRow, 0, $myFleet, $Label, $Record,$this->_current_user);
                        }
                    }
                    //scanning of destination fleet planet
                    elseif (!$isStartedfromThis && ($FleetRow['fleet_end_type'] == 1 || ($FleetRow['fleet_end_type'] == 3 && $TargetMoonIsDestroyed)))
                    {
                        $Label = "fs";
                        $fpage[$ArrivetoTargetTime] .= "\n". Fleets_Lib::flying_fleets_table($FleetRow, 0, $myFleet, $Label, $Record,$this->_current_user);
                    }
                }
                /* 2)the stay fleet table event
                * you can see stay-fleet event only if the target is a planet(or destroyed moon) and is the targetPlanet
                */
                if ($EndStayTime > time() && $Mission == 5 && ($FleetRow['fleet_end_type'] == 1 || ($FleetRow['fleet_end_type'] == 3 && $TargetMoonIsDestroyed)) && $isTheTarget)
                {
                    $Label = "ft";
                    $fpage[$EndStayTime] .= "\n". Fleets_Lib::flying_fleets_table($FleetRow, 1, $myFleet, $Label, $Record,$this->_current_user);
                }
                /* 3)the return fleet table event
                * you can see the return fleet if this is the started planet(or destroyed moon)
                * but no if it is a hold mission or mip
                */
                if ($ReturnTime > time() && $Mission != 4 && $Mission != 10 && $isStartedfromThis && ($FleetRow['fleet_start_type'] == 1 || ($FleetRow['fleet_start_type'] == 3 && $TargetMoonIsDestroyed)))
                {
                    $Label = "fe";
                    $fpage[$ReturnTime] .= "\n". Fleets_Lib::flying_fleets_table($FleetRow, 2, $myFleet, $Label, $Record,$this->_current_user);
                }
            }
            ksort($fpage);
            foreach ($fpage as $FleetTime => $FleetContent)
                $Fleets .= $FleetContent . "\n";

            $parse['phl_fleets_table'] = $Fleets;
            $parse['phl_er_deuter'] = "";
        }
        else
            $parse['phl_er_deuter'] = $this->_lang['px_no_deuterium'];

        $parse['phl_pl_galaxy'] = $Galaxy;
        $parse['phl_pl_system'] = $System;
        $parse['phl_pl_place'] = $Planet;
        $parse['phl_pl_name'] = $TargetName;

        parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'galaxy/phalanx_body' ) , $parse ) , FALSE , '' , FALSE );
    }
}
/* end of phalanx.php */