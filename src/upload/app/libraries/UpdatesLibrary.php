<?php
/**
 * Updates Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace App\libraries;

use App\core\enumerators\BuildingsEnumerator as Buildings;
use App\core\enumerators\PlanetTypesEnumerator;
use App\core\Language;
use App\core\XGPCore;
use App\helpers\UrlHelper;
use App\libraries\DevelopmentsLib as Developments;
use App\libraries\FormatLib as Format;
use App\libraries\Functions;
use App\libraries\MissionControlLibrary;
use App\libraries\OfficiersLib as Officiers;
use App\libraries\ProductionLib as Production;
use App\libraries\Statistics_library;

/**
 * UpdatesLibrary Class
 */
class UpdatesLibrary extends XGPCore
{
    /**
     * @var mixed
     */
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
        $this->Update_Model = Functions::modelLoader('libraries/UpdatesLibrary');

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
        $last_cleanup = Functions::readConfig('last_cleanup');
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
            $this->Update_Model->deleteExpiredAcs();

            Functions::updateConfig('last_cleanup', time());
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
        $auto_backup = Functions::readConfig('auto_backup');
        $last_backup = Functions::readConfig('last_backup');
        $update_interval = 6; // 6 HOURS

        // CHECK TIME
        if ((time() >= ($last_backup + (3600 * $update_interval))) && ($auto_backup == 1)) {
            $this->Update_Model->generateBackUp(); // MAKE BACKUP

            Functions::updateConfig('last_backup', time());
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
        while ($current_planet['planet_b_building_id'] != 0) {
            if ($current_planet['planet_b_building'] <= time()) {
                if (self::checkBuildingQueue($current_planet, $current_user)) {
                    self::setFirstElement($current_planet, $current_user);
                } else {
                    break;
                }
            } else {
                break;
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
        // let's start the missions control process
        $mission_control = new MissionControlLibrary();
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
        $stat_last_update = Functions::readConfig('stat_last_update');
        $update_interval = Functions::readConfig('stat_update_time');

        if ((time() >= ($stat_last_update + (60 * $update_interval)))) {
            $result = new Statistics_library();

            Functions::updateConfig('stat_last_update', $result->makeStats()['stats_time']);
        }
    }

    /**
     * Check the current queue, remove the first element and update the planet with what was just completed
     *
     * @param array $current_planet
     * @param array $current_user
     *
     * @return boolean
     */
    private static function checkBuildingQueue(&$current_planet, &$current_user): bool
    {
        $db = Functions::modelLoader('libraries/UpdatesLibrary');
        $resource = parent::$objects->getObjects();
        $ret_value = false;

        if ($current_planet['planet_b_building_id'] != 0) {
            $current_queue = $current_planet['planet_b_building_id'];

            if ($current_queue != 0) {
                $queue_array = explode(";", $current_queue);
            }

            $build_array = explode(",", $queue_array[0]);
            $element = $build_array[0];
            $build_end_time = floor($build_array[3]);
            $build_mode = $build_array[4];

            array_shift($queue_array);

            $for_destroy = ($build_mode == 'destroy') ? true : false;

            if ($build_end_time <= time()) {
                $current = (int) $current_planet['planet_field_current'];
                $max = (int) $current_planet['planet_field_max'];

                if ($element == Buildings::BUILDING_MONDBASIS) {
                    $current += 1;
                    $max += FIELDS_BY_MOONBASIS_LEVEL;
                    $current_planet[$resource[$element]]++;
                } else {
                    if ($for_destroy == false) {
                        $current += 1;
                        $current_planet[$resource[$element]]++;
                    } else {
                        $current -= 1;
                        $current_planet[$resource[$element]]--;
                    }
                }

                $new_queue = (count($queue_array) == 0) ? 0 : join(';', $queue_array);

                $current_planet['planet_b_building'] = 0;
                $current_planet['planet_b_building_id'] = $new_queue;
                $current_planet['planet_field_current'] = $current;
                $current_planet['planet_field_max'] = $max;
                $current_planet['building_points'] = Statistics_library::calculatePoints(
                    $element,
                    $current_planet[$resource[$element]]
                );

                $db->updatePlanet(
                    $resource[$element],
                    $current_planet[$resource[$element]],
                    $current_planet
                );

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
     * Set the next element in the queue to be the first
     *
     * @param array $current_planet
     * @param array $current_user
     *
     * @return void
     */
    public static function setFirstElement(&$current_planet, $current_user): void
    {
        $db = Functions::modelLoader('libraries/UpdatesLibrary');
        $lang = new Language;
        $lang = $lang->loadLang(['game/global', 'game/constructions', 'game/buildings'], true);
        $resource = parent::$objects->getObjects();

        if ($current_planet['planet_b_building'] == 0) {
            $current_queue = $current_planet['planet_b_building_id'];

            if ($current_queue != 0) {
                $queue_array = explode(";", $current_queue);
                $loop = true;

                while ($loop) {
                    $list_id_array = explode(",", $queue_array[0]);
                    $element = $list_id_array[0];
                    $level = $list_id_array[1];
                    $build_time = $list_id_array[2];
                    $build_end_time = $list_id_array[3];
                    $build_mode = $list_id_array[4];
                    $no_more_level = false;

                    $for_destroy = ($build_mode == 'destroy') ? true : false;

                    $is_payable = Developments::isDevelopmentPayable(
                        $current_user,
                        $current_planet,
                        $element,
                        true,
                        $for_destroy
                    );

                    if ($for_destroy) {
                        if ($current_planet[$resource[$element]] == 0) {
                            $is_payable = false;
                            $no_more_level = true;
                        }
                    }

                    if ($is_payable) {
                        $price = Developments::developmentPrice($current_user, $current_planet, $element, true, $for_destroy);

                        $current_planet['planet_metal'] -= $price['metal'];
                        $current_planet['planet_crystal'] -= $price['crystal'];
                        $current_planet['planet_deuterium'] -= $price['deuterium'];

                        $prevData = 0;

                        // if we upgrade robots or nanobots we must recalculate everything
                        foreach ($queue_array as $queue_item => $data) {
                            $element_data = explode(",", $data);
                            $previous_time = $element_data[2];
                            $element_data[2] = Developments::developmentTime($current_user, $current_planet, $element_data[0]);
                            if ($for_destroy) {
                                $element_data[2] = DevelopmentsLib::tearDownTime(
                                    $element_data[0],
                                    $current_planet[$resource[Buildings::BUILDING_ROBOT_FACTORY]],
                                    $current_planet[$resource[Buildings::BUILDING_NANO_FACTORY]],
                                    $current_planet[$resource[$element_data[0]]]
                                );
                            }

                            if ($prevData == 0) {
                                // remove the previous building time and add the new building time
                                $element_data[3] = $element_data[3] - $previous_time + $element_data[2];

                                // for planet_b_building, set the first queue element completion time
                                $build_end_time = $element_data[3];
                            } else {
                                $element_data[3] = $prevData + $element_data[2];
                            }

                            $prevData = $element_data[3];

                            $recalculated_queue[$queue_item] = join(",", $element_data);
                        }

                        $new_queue = join(";", $recalculated_queue);

                        if ($new_queue == '') {
                            $new_queue = '0';
                        }

                        $loop = false;
                    } else {
                        $element_name = $lang->language[$resource[$element]];

                        if ($no_more_level == true) {
                            $message = '';
                        } else {
                            $price = Developments::developmentPrice(
                                $current_user,
                                $current_planet,
                                $element,
                                true,
                                $for_destroy
                            );

                            $insufficient = [];

                            if ($price['metal'] > $current_planet['planet_metal']) {
                                $insufficient[] = $lang->line('metal');
                            }

                            if ($price['crystal'] > $current_planet['planet_crystal']) {
                                $insufficient[] = $lang->line('crystal');
                            }

                            if ($price['deuterium'] > $current_planet['planet_deuterium']) {
                                $insufficient[] = $lang->line('deuterium');
                            }

                            $message = sprintf(
                                $lang->line('bd_building_queue_not_enough_resources'),
                                $lang->line('bd_building_queue_' . $build_mode . '_order'),
                                $element_name,
                                $level,
                                UrlHelper::setUrl(
                                    'game.php?page=galaxy&mode=3&galaxy=' . $current_planet['planet_galaxy'] . '&system=' . $current_planet['planet_system'],
                                    $current_planet['planet_name'] . ' ' . Format::prettyCoords(
                                        $current_planet['planet_galaxy'],
                                        $current_planet['planet_system'],
                                        $current_planet['planet_planet']
                                    )
                                ),
                                join(', ', $insufficient)
                            );
                        }

                        if ($message != '') {
                            Functions::sendMessage(
                                $current_user['user_id'],
                                0,
                                '',
                                5,
                                $lang->line('bd_building_queue_not_enough_resources_from'),
                                $lang->line('bd_building_queue_not_enough_resources_subject'),
                                $message,
                                true
                            );
                        }

                        array_shift($queue_array);

                        foreach ($queue_array as $num => $info) {
                            $fix_ele = explode(",", $info);
                            $fix_ele[3] = $fix_ele[3] - $build_time; // build end time
                            $queue_array[$num] = join(",", $fix_ele);
                        }

                        $actual_count = count($queue_array);

                        if ($actual_count == 0) {
                            $build_end_time = '0';
                            $new_queue = '0';
                            $loop = false;
                        }
                    }
                }
            } else {
                $build_end_time = '0';
                $new_queue = '0';
            }

            $current_planet['planet_b_building'] = $build_end_time;
            $current_planet['planet_b_building_id'] = $new_queue;

            $db->updateQueueResources($current_planet);
        }
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

        $game_resource_multiplier = Functions::readConfig('resource_multiplier');
        $game_metal_basic_income = Functions::readConfig('metal_basic_income');
        $game_crystal_basic_income = Functions::readConfig('crystal_basic_income');
        $game_deuterium_basic_income = Functions::readConfig('deuterium_basic_income');

        if ($current_user['preference_vacation_mode'] > 0) {
            $game_metal_basic_income = 0;
            $game_crystal_basic_income = 0;
            $game_deuterium_basic_income = 0;
        }

        $current_planet['planet_metal_max'] = Production::maxStorable($current_planet[$resource[22]]);
        $current_planet['planet_crystal_max'] = Production::maxStorable($current_planet[$resource[23]]);
        $current_planet['planet_deuterium_max'] = Production::maxStorable($current_planet[$resource[24]]);

        $MaxMetalStorage = $current_planet['planet_metal_max'];
        $MaxCristalStorage = $current_planet['planet_crystal_max'];
        $MaxDeuteriumStorage = $current_planet['planet_deuterium_max'];

        $Caps = [];
        $BuildTemp = $current_planet['planet_temp_max'];
        $sub_query = '';
        $parse['production_level'] = 100;

        $post_percent = Production::maxProduction(
            $current_planet['planet_energy_max'],
            $current_planet['planet_energy_used']
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
            $geologe_boost = 1 + (1 * (Officiers::isOfficierActive(
                $current_user['premium_officier_geologist']
            ) ? GEOLOGUE : 0));
            $engineer_boost = 1 + (1 * (Officiers::isOfficierActive(
                $current_user['premium_officier_engineer']
            ) ? ENGINEER_ENERGY : 0));

            // PRODUCTION FORMULAS
            $metal_prod = eval($ProdGrid[$ProdID]['formule']['metal']);
            $crystal_prod = eval($ProdGrid[$ProdID]['formule']['crystal']);
            $deuterium_prod = eval($ProdGrid[$ProdID]['formule']['deuterium']);
            $energy_prod = eval($ProdGrid[$ProdID]['formule']['energy']);

            // PRODUCTION
            $Caps['planet_metal_perhour'] += Production::currentProduction(
                Production::productionAmount($metal_prod, $geologe_boost, $game_resource_multiplier),
                $post_percent
            );

            $Caps['planet_crystal_perhour'] += Production::currentProduction(
                Production::productionAmount($crystal_prod, $geologe_boost, $game_resource_multiplier),
                $post_percent
            );

            $Caps['planet_deuterium_perhour'] += Production::currentProduction(
                Production::productionAmount($deuterium_prod, $geologe_boost, $game_resource_multiplier),
                $post_percent
            );

            if ($ProdID >= 4) {
                if ($ProdID == 12 && $current_planet['planet_deuterium'] == 0) {
                    continue;
                }

                $Caps['planet_energy_max'] += Production::productionAmount(
                    $energy_prod,
                    $engineer_boost,
                    0,
                    true
                );
            } else {
                $Caps['planet_energy_used'] += Production::productionAmount(
                    $energy_prod,
                    1,
                    0,
                    true
                );
            }
        }

        if ($current_planet['planet_type'] == PlanetTypesEnumerator::MOON) {
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

            $MetalBaseProduc = (($ProductionTime * ($game_metal_basic_income / 3600)));
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

            $CristalBaseProduc = (($ProductionTime * ($game_crystal_basic_income / 3600)));
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

            $DeuteriumBaseProduc = (($ProductionTime * ($game_deuterium_basic_income / 3600)));
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
            $db = Functions::modelLoader('libraries/UpdatesLibrary');

            // SHIPS AND DEFENSES UPDATE
            $builded = self::updateHangarQueue($current_user, $current_planet, $ProductionTime);
            $ship_points = 0;
            $defense_points = 0;

            if ($builded != '') {
                foreach ($builded as $element => $count) {
                    if ($element != '') {
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

                        if ($resource[$element] != '') {
                            $sub_query .= "`" . $resource[$element] . "` = '" . $current_planet[$resource[$element]] . "', ";
                        }
                    }
                }
            }

            // RESEARCH UPDATE
            if ($current_planet['planet_b_tech'] <= time() && $current_planet['planet_b_tech_id'] != 0) {
                $current_user['research_points'] = Statistics_library::calculatePoints(
                    $current_planet['planet_b_tech_id'],
                    $current_user[$resource[$current_planet['planet_b_tech_id']]],
                    'tech'
                );

                $current_user[$resource[$current_planet['planet_b_tech_id']]]++;

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
                'tech_query' => $tech_query,
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
                        $AcumTime = Developments::developmentTime(
                            $current_user,
                            $current_planet,
                            $Item[0]
                        );
                        $BuildArray[$Node] = [$Item[0], $Item[1], $AcumTime];
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
