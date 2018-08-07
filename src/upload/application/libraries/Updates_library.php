<?php
/**
 * Update Library
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\libraries;

use application\core\XGPCore;

/**
 * Update Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Updates_library extends XGPCore
{

    private $Update_Model;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // load Model
        $this->Update_Model = FunctionsLib::modelLoader('libraries/updates_library');

        // Other stuff
        $this->cleanUp();
        $this->createBackup();

        // Updates
        $this->updateFleets();
        $this->updateStatistics();
    }

    /**
     * cleanUp
     *
     * @return void
     */
    private function cleanUp()
    {
        $last_cleanup = FunctionsLib::readConfig('last_cleanup');
        $cleanup_interval = 6; // 6 HOURS

        if ((time() >= ($last_cleanup + (3600 * $cleanup_interval)))) {

            // TIMERS
            $del_planets = time() - ONE_DAY;
            $del_before = time() - ONE_WEEK;
            $del_inactive = time() - ONE_MONTH;
            $del_deleted = time() - ONE_WEEK;

            // USERS TO DELETE
            $ChooseToDelete = $this->Update_Model->deleteUsersByDeletedAndInactive($del_deleted, $del_inactive);

            if ($ChooseToDelete) {

                foreach ($ChooseToDelete as $delete) {

                    parent::$users->deleteUser($delete['user_id']);
                }
            }

            // Misc deletions
            $this->Update_Model->deleteMessages($del_before);
            $this->Update_Model->deleteReports($del_before);
            $this->Update_Model->deleteSessions(date('Y-m-d H:i:s', $del_planets));
            $this->Update_Model->deleteDestroyedPlanets($del_planets);

            FunctionsLib::updateConfig('last_cleanup', time());
        }
    }

    /**
     * createBackup
     *
     * @return void
     */
    private function createBackup()
    {
        // LAST UPDATE AND UPDATE INTERVAL, EX: 15 MINUTES
        $auto_backup = FunctionsLib::readConfig('auto_backup');
        $last_backup = FunctionsLib::readConfig('last_backup');
        $update_interval = 6; // 6 HOURS

        // CHECK TIME
        if ((time() >= ($last_backup + (3600 * $update_interval))) && ($auto_backup == 1)) {

            $this->Update_Model->generateBackUp(); // MAKE BACKUP

            FunctionsLib::updateConfig('last_backup', time());
        }
    }

    /**
     * updateBuildingsQueue
     *
     * @param array $current_planet Current planet
     * @param array $current_user   Current user
     *
     * @return void
     */
    public static function updateBuildingsQueue(&$current_planet, &$current_user)
    {
        if ($current_planet['planet_b_building_id'] != 0) {

            while ($current_planet['planet_b_building_id'] != 0) {

                if ($current_planet['planet_b_building'] <= time()) {

                    if (self::checkBuildingQueue($current_planet, $current_user)) {

                        DevelopmentsLib::setFirstElement($current_planet, $current_user);
                    }
                } else {
                    break;
                }
            }
        }
    }

    /**
     * updateFleets
     *
     * @return void
     */
    private function updateFleets()
    {
        // language issues if is not present
        if (!defined('IN_GAME')) {

            define('IN_GAME', true);
        }

        // let's start the missions control process
        $mission_control = new Mission_control_library();
        $mission_control->arrivingFleets();
        $mission_control->returningFleets();
    }

    /**
     * updateStatistics
     *
     * @return void
     */
    private function updateStatistics()
    {
        // LAST UPDATE AND UPDATE INTERVAL, EX: 15 MINUTES
        $stat_last_update = FunctionsLib::readConfig('stat_last_update');
        $update_interval = FunctionsLib::readConfig('stat_update_time');

        if ((time() >= ($stat_last_update + (60 * $update_interval)))) {

            $result = new Statistics_library();

            FunctionsLib::updateConfig('stat_last_update', $result->makeStats()['stats_time']);
        }
    }

    /**
     * checkBuildingQueue
     *
     * @param array $current_planet Current planet
     * @param array $current_user   Current user
     *
     * @return boolean
     */
    private static function checkBuildingQueue(&$current_planet, &$current_user)
    {
        $db = FunctionsLib::modelLoader('libraries/updates_library');
        $resource = parent::$objects->getObjects();
        $ret_value = false;

        if ($current_planet['planet_b_building_id'] != 0) {

            $current_queue = $current_planet['planet_b_building_id'];

            if ($current_queue != 0) {
                $queue_array = explode(";", $current_queue);
            }

            $build_array = explode(",", $queue_array[0]);
            $build_end_time = floor($build_array[3]);
            $build_mode = $build_array[4];
            $element = $build_array[0];

            array_shift($queue_array);

            if ($build_mode == 'destroy') {

                $for_destroy = true;
            } else {

                $for_destroy = false;
            }

            if ($build_end_time <= time()) {

                $needed = DevelopmentsLib::developmentPrice(
                    $current_user, $current_planet, $element, true, $for_destroy
                );

                $units = $needed['metal'] + $needed['crystal'] + $needed['deuterium'];
                $current = (int) $current_planet['planet_field_current'];
                $max = (int) $current_planet['planet_field_max'];
                $message = '';
                
                if ($current_planet['planet_type'] == 3) {
                    if ($element == 41) {

                        $current += 1;
                        $max += FIELDS_BY_MOONBASIS_LEVEL;
                        $current_planet[$resource[$element]] ++;
                    } elseif ($element != 0) {

                        if (DevelopmentsLib::isDevelopmentPayable($current_user, $current_planet, $element, true, $for_destroy)) {
                        
                            if ($for_destroy == false) {

                                $current += 1;
                                $current_planet[$resource[$element]] ++;
                            } else {

                                $current -= 1;
                                $current_planet[$resource[$element]] --;
                            }
                        } else {

                            $message    = sprintf(
                                parent::$lang['sys_notenough_money'],
                                parent::$lang['tech'][$element],
                                FormatLib::prettyNumber($current_planet['planet_metal']),
                                parent::$lang['Metal'],
                                FormatLib::prettyNumber($current_planet['planet_crystal']),
                                parent::$lang['Crystal'],
                                FormatLib::prettyNumber($current_planet['planet_deuterium']),
                                parent::$lang['Deuterium'],
                                FormatLib::prettyNumber($needed['metal']),
                                parent::$lang['Metal'],
                                FormatLib::prettyNumber($needed['crystal']),
                                parent::$lang['Crystal'],
                                FormatLib::prettyNumber($needed['deuterium']),
                                parent::$lang['Deuterium']
                            );
                        }
                    }
                } elseif ($current_planet['planet_type'] == 1) {

                    if (DevelopmentsLib::isDevelopmentPayable($current_user, $current_planet, $element, true, $for_destroy)) {
                     
                        if ($for_destroy == false) {

                            $current += 1;
                            $current_planet[$resource[$element]] ++;
                        } else {

                            $current -= 1;
                            $current_planet[$resource[$element]] --;
                        }
                    } else {
                        
                        $message    = sprintf(
                            parent::$lang['sys_notenough_money'],
                            parent::$lang['tech'][$element],
                            FormatLib::prettyNumber($current_planet['planet_metal']),
                            parent::$lang['Metal'],
                            FormatLib::prettyNumber($current_planet['planet_crystal']),
                            parent::$lang['Crystal'],
                            FormatLib::prettyNumber($current_planet['planet_deuterium']),
                            parent::$lang['Deuterium'],
                            FormatLib::prettyNumber($needed['metal']),
                            parent::$lang['Metal'],
                            FormatLib::prettyNumber($needed['crystal']),
                            parent::$lang['Crystal'],
                            FormatLib::prettyNumber($needed['deuterium']),
                            parent::$lang['Deuterium']
                        );
                    }
                }

                if (count($queue_array) == 0) {

                    $new_queue = 0;
                } else {

                    $new_queue = implode(';', $queue_array);
                }

                $current_planet['planet_b_building'] = 0;
                $current_planet['planet_b_building_id'] = $new_queue;
                $current_planet['planet_field_current'] = $current;
                $current_planet['planet_field_max'] = $max;
                $current_planet['building_points'] = Statistics_library::calculatePoints(
                        $element, $current_planet[$resource[$element]]
                );

                $db->updatePlanet(
                    $resource[$element], $current_planet[$resource[$element]], $current_planet
                );

                if ($message != '') {
                    
                    FunctionsLib::sendMessage($current_user['user_id'], 0, '', 5, parent::$lang['sys_buildlist'], parent::$lang['sys_buildlist_fail'], $message); 
                }
                
                $ret_value = true;
            } else {

                $ret_value = false;
            }
        } else {
            $current_planet['planet_b_building'] = 0;
            $current_planet['planet_b_building_id'] = 0;

            $db->updateBuildingsQueue($current_planet);

            $ret_value = false;
        }

        return $ret_value;
    }

    /**
     * Update the planet resources
     *
     * @param array   $current_user   Current user
     * @param array   $current_planet Current planet
     * @param int     $UpdateTime     Update time
     * @param boolean $Simul          Simulation
     *
     * @return void
     */
    public static function updatePlanetResources(&$current_user, &$current_planet, $UpdateTime, $Simul = false)
    {
        $resource = parent::$objects->getObjects();
        $ProdGrid = parent::$objects->getProduction();
        $reslist = parent::$objects->getObjectsList();

        $game_resource_multiplier = FunctionsLib::readConfig('resource_multiplier');
        $game_metal_basic_income = FunctionsLib::readConfig('metal_basic_income');
        $game_crystal_basic_income = FunctionsLib::readConfig('crystal_basic_income');
        $game_deuterium_basic_income = FunctionsLib::readConfig('deuterium_basic_income');

        $current_planet['planet_metal_max'] = ProductionLib::maxStorable($current_planet[$resource[22]]);
        $current_planet['planet_crystal_max'] = ProductionLib::maxStorable($current_planet[$resource[23]]);
        $current_planet['planet_deuterium_max'] = ProductionLib::maxStorable($current_planet[$resource[24]]);

        $MaxMetalStorage = $current_planet['planet_metal_max'];
        $MaxCristalStorage = $current_planet['planet_crystal_max'];
        $MaxDeuteriumStorage = $current_planet['planet_deuterium_max'];

        $Caps = array();
        $BuildTemp = $current_planet['planet_temp_max'];
        $sub_query = '';
        $parse['production_level'] = 100;

        $post_percent = ProductionLib::maxProduction(
                $current_planet['planet_energy_max'], $current_planet['planet_energy_used']
        );

        $Caps['planet_metal_perhour'] = 0;
        $Caps['planet_crystal_perhour'] = 0;
        $Caps['planet_deuterium_perhour'] = 0;
        $Caps['planet_energy_max'] = 0;
        $Caps['planet_energy_used'] = 0;

        foreach ($ProdGrid as $ProdID => $formula) {

            $BuildLevelFactor = $current_planet['planet_' . $resource[$ProdID] . '_percent'];
            $BuildLevel = $current_planet[$resource[$ProdID]];
            $BuildEnergy = $current_user['research_energy_technology'];

            // BOOST
            $geologe_boost = 1 + ( 1 * ( OfficiersLib::isOfficierActive(
                    $current_user['premium_officier_geologist']
                ) ? GEOLOGUE : 0));
            $engineer_boost = 1 + ( 1 * ( OfficiersLib::isOfficierActive(
                    $current_user['premium_officier_engineer']
                ) ? ENGINEER_ENERGY : 0));

            // PRODUCTION FORMULAS
            $metal_prod = eval($ProdGrid[$ProdID]['formule']['metal']);
            $crystal_prod = eval($ProdGrid[$ProdID]['formule']['crystal']);
            $deuterium_prod = eval($ProdGrid[$ProdID]['formule']['deuterium']);
            $energy_prod = eval($ProdGrid[$ProdID]['formule']['energy']);

            // PRODUCTION
            $Caps['planet_metal_perhour'] += ProductionLib::currentProduction(
                    ProductionLib::productionAmount($metal_prod, $geologe_boost, $game_resource_multiplier), $post_percent
            );

            $Caps['planet_crystal_perhour'] += ProductionLib::currentProduction(
                    ProductionLib::productionAmount($crystal_prod, $geologe_boost, $game_resource_multiplier), $post_percent
            );

            $Caps['planet_deuterium_perhour'] += ProductionLib::currentProduction(
                    ProductionLib::productionAmount($deuterium_prod, $geologe_boost, $game_resource_multiplier), $post_percent
            );

            if ($ProdID >= 4) {

                if ($ProdID == 12 && $current_planet['planet_deuterium'] == 0) {
                    continue;
                }

                $Caps['planet_energy_max'] += ProductionLib::productionAmount(
                        $energy_prod, $engineer_boost, 0, true
                );
            } else {

                $Caps['planet_energy_used'] += ProductionLib::productionAmount(
                        $energy_prod, 1, 0, true
                );
            }
        }

        if ($current_planet['planet_type'] == 3) {

            $game_metal_basic_income = 0;
            $game_crystal_basic_income = 0;
            $game_deuterium_basic_income = 0;
            $current_planet['planet_metal_perhour'] = 0;
            $current_planet['planet_crystal_perhour'] = 0;
            $current_planet['planet_deuterium_perhour'] = 0;
            $current_planet['planet_energy_used'] = 0;
            $current_planet['planet_energy_max'] = 0;
        } else {

            $current_planet['planet_metal_perhour'] = $Caps['planet_metal_perhour'];
            $current_planet['planet_crystal_perhour'] = $Caps['planet_crystal_perhour'];
            $current_planet['planet_deuterium_perhour'] = $Caps['planet_deuterium_perhour'];
            $current_planet['planet_energy_used'] = $Caps['planet_energy_used'];
            $current_planet['planet_energy_max'] = $Caps['planet_energy_max'];
        }

        $ProductionTime = ($UpdateTime - $current_planet['planet_last_update']);
        $current_planet['planet_last_update'] = $UpdateTime;

        if ($current_planet['planet_energy_max'] == 0) {

            $current_planet['planet_metal_perhour'] = $game_metal_basic_income;
            $current_planet['planet_crystal_perhour'] = $game_crystal_basic_income;
            $current_planet['planet_deuterium_perhour'] = $game_deuterium_basic_income;

            $production_level = 100;
        } elseif ($current_planet['planet_energy_max'] >= $current_planet['planet_energy_used']) {

            $production_level = 100;
        } else {

            $production_level = floor(
                ($current_planet['planet_energy_max'] / $current_planet['planet_energy_used']) * 100
            );
        }

        if ($production_level > 100) {

            $production_level = 100;
        } elseif ($production_level < 0) {

            $production_level = 0;
        }

        if ($current_planet['planet_metal'] <= $MaxMetalStorage) {

            $MetalProduction = (
                ($ProductionTime * ($current_planet['planet_metal_perhour'] / 3600))
                ) * (0.01 * $production_level);

            $MetalBaseProduc = (($ProductionTime * ($game_metal_basic_income / 3600 )));
            $MetalTheorical = $current_planet['planet_metal'] + $MetalProduction + $MetalBaseProduc;

            if ($MetalTheorical <= $MaxMetalStorage) {

                $current_planet['planet_metal'] = $MetalTheorical;
            } else {

                $current_planet['planet_metal'] = $MaxMetalStorage;
            }
        }

        if ($current_planet['planet_crystal'] <= $MaxCristalStorage) {

            $CristalProduction = (
                ($ProductionTime * ($current_planet['planet_crystal_perhour'] / 3600))
                ) * (0.01 * $production_level);

            $CristalBaseProduc = (($ProductionTime * ($game_crystal_basic_income / 3600 )));
            $CristalTheorical = $current_planet['planet_crystal'] + $CristalProduction + $CristalBaseProduc;

            if ($CristalTheorical <= $MaxCristalStorage) {

                $current_planet['planet_crystal'] = $CristalTheorical;
            } else {

                $current_planet['planet_crystal'] = $MaxCristalStorage;
            }
        }

        if ($current_planet['planet_deuterium'] <= $MaxDeuteriumStorage) {

            $DeuteriumProduction = (
                ($ProductionTime * ($current_planet['planet_deuterium_perhour'] / 3600))
                ) * (0.01 * $production_level);

            $DeuteriumBaseProduc = (($ProductionTime * ($game_deuterium_basic_income / 3600 )));
            $DeuteriumTheorical = $current_planet['planet_deuterium'] +
                $DeuteriumProduction + $DeuteriumBaseProduc;

            if ($DeuteriumTheorical <= $MaxDeuteriumStorage) {

                $current_planet['planet_deuterium'] = $DeuteriumTheorical;
            } else {

                $current_planet['planet_deuterium'] = $MaxDeuteriumStorage;
            }
        }

        if ($current_planet['planet_metal'] < 0) {

            $current_planet['planet_metal'] = 0;
        }

        if ($current_planet['planet_crystal'] < 0) {

            $current_planet['planet_crystal'] = 0;
        }

        if ($current_planet['planet_deuterium'] < 0) {

            $current_planet['planet_deuterium'] = 0;
        }

        if ($Simul == false) {

            // new DB Object
            $db = FunctionsLib::modelLoader('libraries/updates_library');

            // SHIPS AND DEFENSES UPDATE
            $builded = self::updateHangarQueue($current_user, $current_planet, $ProductionTime);
            $ship_points = 0;
            $defense_points = 0;

            if ($builded != '') {

                foreach ($builded as $element => $count) {

                    if ($element <> '') {

                        // POINTS
                        switch ($element) {

                            case (($element >= 202) && ($element <= 215)):
                                $ship_points += Statistics_library::calculatePoints($element, $count) * $count;
                                break;

                            case (($element >= 401) && ($element <= 503)):
                                $defense_points += Statistics_library::calculatePoints($element, $count) * $count;
                                break;

                            default:
                                break;
                        }

                        if($resource[$element] != ''){

                            $sub_query .= "`" . $resource[$element] . "` = '" . $current_planet[$resource[$element]] . "', ";
                        }
                    }
                }
            }

            // RESEARCH UPDATE
            if ($current_planet['planet_b_tech'] <= time() && $current_planet['planet_b_tech_id'] != 0) {

                $current_user['research_points'] = Statistics_library::calculatePoints(
                        $current_planet['planet_b_tech_id'], $current_user[$resource[$current_planet['planet_b_tech_id']]], 'tech'
                );

                $current_user[$resource[$current_planet['planet_b_tech_id']]] ++;

                $tech_query = "`planet_b_tech` = '0',";
                $tech_query .= "`planet_b_tech_id` = '0',";
                $tech_query .= "`" . $resource[$current_planet['planet_b_tech_id']] . "` = '" .
                    $current_user[$resource[$current_planet['planet_b_tech_id']]] . "',";
                $tech_query .= "`user_statistic_technology_points` = `user_statistic_technology_points` + '" .
                    $current_user['research_points'] . "',";
                $tech_query .= "`research_current_research` = '0',";
            } else {

                $tech_query = "";
            }

            $db->updateAllPlanetData([
                'planet' => $current_planet,
                'ship_points' => $ship_points,
                'defense_points' => $defense_points,
                'sub_query' => $sub_query,
                'tech_query' => $tech_query
            ]);
        }
    }

    /**
     * Update the hangar queue, ships and defenses that were on queue
     *
     * @param array $current_user   Current user
     * @param array $current_planet Current planet
     * @param int   $ProductionTime Production time
     *
     * @return int
     */
    private static function updateHangarQueue($current_user, &$current_planet, $ProductionTime)
    {
        $resource = parent::$objects->getObjects();

        if ($current_planet['planet_b_hangar_id'] != "") {

            $Builded = [];
            $BuildArray = [];
            $BuildQueue = explode(';', $current_planet['planet_b_hangar_id']);

            $current_planet['planet_b_hangar'] += $ProductionTime;

            foreach ($BuildQueue as $Node => $Array) {
                if ($Array != '') {
                    $Item = explode(',', $Array);

                    if (isset($Item[0]) && $Item[0] != 0) {

                        $AcumTime = DevelopmentsLib::developmentTime(
                            $current_user, $current_planet, $Item[0]
                        );
                        $BuildArray[$Node] = array($Item[0], $Item[1], $AcumTime);
                    }
                }
            }

            $current_planet['planet_b_hangar_id'] = '';
            $UnFinished = false;

            foreach ($BuildArray as $Node => $Item) {

                $Element = $Item[0];
                $Count = $Item[1];
                $BuildTime = $Item[2];
                $Builded[$Element] = 0;

                if (!$UnFinished and $BuildTime > 0) {

                    $AllTime = $BuildTime * $Count;

                    if ($current_planet['planet_b_hangar'] >= $BuildTime) {

                        $Done = min($Count, floor($current_planet['planet_b_hangar'] / $BuildTime));

                        if ($Count > $Done) {

                            $current_planet['planet_b_hangar'] -= $BuildTime * $Done;

                            $UnFinished = true;
                            $Count -= $Done;
                        } else {

                            $current_planet['planet_b_hangar'] -= $AllTime;
                            $Count = 0;
                        }

                        $Builded[$Element] += $Done;
                        $current_planet[$resource[$Element]] += $Done;
                    } else {

                        $UnFinished = true;
                    }
                } elseif (!$UnFinished) {

                    $Builded[$Element] += $Count;
                    $current_planet[$resource[$Element]] += $Count;
                    $Count = 0;
                }

                if ($Count != 0) {

                    $current_planet['planet_b_hangar_id'] .= $Element . "," . $Count . ";";
                }
            }
        } else {
            $Builded = '';
            $current_planet['planet_b_hangar'] = 0;
        }

        return $Builded;
    }
}

/* end of Update.php */
