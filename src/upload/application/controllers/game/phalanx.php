<?php
/**
 * Phalanx Controller
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
use application\core\enumerators\PlanetTypesEnumerator;
use application\libraries\FleetsLib;
use application\libraries\Formulas;
use application\libraries\FunctionsLib;

/**
 * Phalanx Class
 */
class Phalanx extends Controller
{
    const MODULE_ID = 11;

    /**
     * @var mixed
     */
    private $_current_user;
    /**
     * @var mixed
     */
    private $_current_planet;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/phalanx');

        // load Language
        parent::loadLang('game/phalanx');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_current_user = parent::$users->getUserData();
        $this->_current_planet = parent::$users->getPlanetData();

        $this->build_page();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->langs->language;
        /* range */
        $radar_limit_inf = $this->_current_planet['planet_system'] - Formulas::phalanxRange($this->_current_planet['building_phalanx']);
        $radar_limit_sup = $this->_current_planet['planet_system'] + Formulas::phalanxRange($this->_current_planet['building_phalanx']);
        $radar_limit_inf = max($radar_limit_inf, 1);
        $radar_limit_sup = min($radar_limit_sup, MAX_SYSTEM_IN_GALAXY);

        /* input validation */
        $Galaxy = (int) $_GET['galaxy'];
        $System = (int) $_GET['system'];
        $Planet = (int) $_GET['planet'];
        $PlType = (int) $_GET['planettype'];
        /* cheater detection */
        if ($System < $radar_limit_inf or $System > $radar_limit_sup or $Galaxy != $this->_current_planet['planet_galaxy'] or $PlType != PlanetTypesEnumerator::PLANET or $this->_current_planet['planet_type'] != PlanetTypesEnumerator::MOON) {
            FunctionsLib::redirect('game.php?page=galaxy');
        }

        $TargetName = '';

        /* main page */
        if ($this->_current_planet['planet_deuterium'] >= 10000) {
            $this->Phalanx_Model->reduceDeuterium($this->_current_user['user_current_planet']);

            $target_planet_info = $this->Phalanx_Model->getTargetPlanetIdAndName($Galaxy, $System, $Planet);

            $TargetID = $target_planet_info['planet_user_id'];
            $TargetName = $target_planet_info['planet_name'];

            $target_moon = $this->Phalanx_Model->getTargetMoonStatus($Galaxy, $System, $Planet);

            //if there isn't a moon,
            if ($target_moon === false) {
                $TargetMoonIsDestroyed = true;
            } else {
                $TargetMoonIsDestroyed = (isset($target_moon['planet_destroyed']) && $target_moon['planet_destroyed'] !== 0);
            }

            $FleetToTarget = $this->Phalanx_Model->getFleetsToTarget($Galaxy, $System, $Planet);

            $Record = 0;
            $fpage = [];
            foreach ($FleetToTarget as $FleetRow) {
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
            $parse['phl_er_deuter'] = '';
        } else {
            $parse['phl_fleets_table'] = '';
            $parse['phl_er_deuter'] = $this->langs->line('px_no_deuterium');
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
