<?php
/**
 * Phalanx Controller
 *
 * PHP Version 7.1+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\game;

use application\core\Controller;
use application\core\Database;
use application\libraries\FleetsLib;
use application\libraries\FunctionsLib;

/**
 * Phalanx Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Phalanx extends Controller
{

    const MODULE_ID = 11;

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
        parent::$users->checkSession();

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_db = new Database();
        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();
        $this->_current_planet = parent::$users->getPlanetData();
        $this->_formula = FunctionsLib::loadLibrary('FormulaLib');

        $this->build_page();
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        $this->_db->closeConnection();
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
        $radar_limit_inf = $this->_current_planet['planet_system'] - $this->_formula->phalanxRange($this->_current_planet['building_phalanx']);
        $radar_limit_sup = $this->_current_planet['planet_system'] + $this->_formula->phalanxRange($this->_current_planet['building_phalanx']);
        $radar_limit_inf = max($radar_limit_inf, 1);
        $radar_limit_sup = min($radar_limit_sup, MAX_SYSTEM_IN_GALAXY);

        /* input validation */
        $Galaxy = (int) $_GET['galaxy'];
        $System = (int) $_GET['system'];
        $Planet = (int) $_GET['planet'];
        $PlType = (int) $_GET['planettype'];
        /* cheater detection */
        if ($System < $radar_limit_inf or $System > $radar_limit_sup or $Galaxy != $this->_current_planet['planet_galaxy'] or $PlType != 1 or $this->_current_planet['planet_type'] != 3) {
            FunctionsLib::redirect('game.php?page=galaxy');
        }

        $TargetName = '';

        /* main page */
        if ($this->_current_planet['planet_deuterium'] > 10000) {
            $this->_db->query("UPDATE " . PLANETS . " SET
            						`planet_deuterium` = `planet_deuterium` - '10000'
            						WHERE `planet_id` = '" . $this->_current_user['user_current_planet'] . "';");

            $TargetInfo = $this->_db->queryFetch("SELECT `planet_name`, `planet_user_id`
            												FROM " . PLANETS . "
            												WHERE `planet_galaxy` = '" . $Galaxy . "' AND
            														`planet_system` = '" . $System . "' AND
            														`planet_planet` = '" . $Planet . "' AND
            														`planet_type` = 1");

            $TargetID = $TargetInfo['planet_user_id'];
            $TargetName = $TargetInfo['planet_name'];
            $TargetInfo = $this->_db->queryFetch("SELECT `planet_destroyed`
            												FROM " . PLANETS . "
            												WHERE `planet_galaxy` = '" . $Galaxy . "' AND
            														`planet_system` = '" . $System . "' AND
            														`planet_planet` = '" . $Planet . "' AND
            														`planet_type` = 3 ");
            //if there isn't a moon,
            if ($TargetInfo === false) {
                $TargetMoonIsDestroyed = true;
            } else {
                $TargetMoonIsDestroyed = $TargetInfo['planet_destroyed'] !== 0;
            }

            $FleetToTarget = $this->_db->query("SELECT *
            										FROM " . FLEETS . "
            										WHERE ( ( `fleet_start_galaxy` = '" . $Galaxy . "' AND
            													`fleet_start_system` = '" . $System . "' AND
            													`fleet_start_planet` = '" . $Planet . "' ) OR
            												( `fleet_end_galaxy` = '" . $Galaxy . "' AND
            													`fleet_end_system` = '" . $System . "' AND
            													`fleet_end_planet` = '" . $Planet . "' )
            											   ) ;");

            $Record = 0;
            $fpage = array();
            while ($FleetRow = $this->_db->fetchArray($FleetToTarget)) {
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

                $fpage[$ArrivetoTargetTime] = '';
                $fpage[$EndStayTime] = '';
                $fpage[$ReturnTime] = '';

                /* 1)the arrive to target fleet table event
                 * you can see start-fleet event only if this is a planet(or destroyed moon)
                 * and if the fleet mission started from this planet is different from hold
                 * or if it's a enemy mission.
                 */
                if ($ArrivetoTargetTime > time()) {
                    //scannig of fleet started planet
                    if ($isStartedfromThis && ($FleetRow['fleet_start_type'] == 1 || ($FleetRow['fleet_start_type'] == 3 && $TargetMoonIsDestroyed))) {
                        if ($Mission != 4) {
                            $Label = "fs";
                            $fpage[$ArrivetoTargetTime] .= "\n" . FleetsLib::flyingFleetsTable($FleetRow, 0, $myFleet, $Label, $Record, $this->_current_user);
                        }
                    }
                    //scanning of destination fleet planet
                    elseif (!$isStartedfromThis && ($FleetRow['fleet_end_type'] == 1 || ($FleetRow['fleet_end_type'] == 3 && $TargetMoonIsDestroyed))) {
                        $Label = "fs";
                        $fpage[$ArrivetoTargetTime] .= "\n" . FleetsLib::flyingFleetsTable($FleetRow, 0, $myFleet, $Label, $Record, $this->_current_user);
                    }
                }
                /* 2)the stay fleet table event
                 * you can see stay-fleet event only if the target is a planet(or destroyed moon) and is the targetPlanet
                 */
                if ($EndStayTime > time() && $Mission == 5 && ($FleetRow['fleet_end_type'] == 1 || ($FleetRow['fleet_end_type'] == 3 && $TargetMoonIsDestroyed)) && $isTheTarget) {
                    $Label = "ft";
                    $fpage[$EndStayTime] .= "\n" . FleetsLib::flyingFleetsTable($FleetRow, 1, $myFleet, $Label, $Record, $this->_current_user);
                }
                /* 3)the return fleet table event
                 * you can see the return fleet if this is the started planet(or destroyed moon)
                 * but no if it is a hold mission or mip
                 */
                if ($ReturnTime > time() && $Mission != 4 && $Mission != 10 && $isStartedfromThis && ($FleetRow['fleet_start_type'] == 1 || ($FleetRow['fleet_start_type'] == 3 && $TargetMoonIsDestroyed))) {
                    $Label = "fe";
                    $fpage[$ReturnTime] .= "\n" . FleetsLib::flyingFleetsTable($FleetRow, 2, $myFleet, $Label, $Record, $this->_current_user);
                }
            }
            ksort($fpage);
            $Fleets = '';
            foreach ($fpage as $FleetTime => $FleetContent) {
                $Fleets .= $FleetContent . "\n";
            }

            $parse['phl_fleets_table'] = $Fleets;
            $parse['phl_er_deuter'] = "";
        } else {
            $parse['phl_er_deuter'] = $this->_lang['px_no_deuterium'];
        }

        $parse['phl_pl_galaxy'] = $Galaxy;
        $parse['phl_pl_system'] = $System;
        $parse['phl_pl_place'] = $Planet;
        $parse['phl_pl_name'] = $TargetName;

        parent::$page->display(
            $this->getTemplate()->set(
                'galaxy/phalanx_body',
                $parse
            ),
            false,
            '',
            false
        );
    }
}

/* end of phalanx.php */
