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
 * @version  3.0.0
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
 * @version  3.0.0
 */
class Update extends XGPCore
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // load Model
        parent::loadModel('libraries/update');
        
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
        $last_cleanup       = FunctionsLib::readConfig('last_cleanup');
        $cleanup_interval   = 6; // 6 HOURS

        if ((time() >= ($last_cleanup + (3600 * $cleanup_interval)))) {

            // TIMERS
            $del_planets    = time() - (60 * 60 * 24); // 1 DAY
            $del_before     = time() - (60 * 60 * 24 * 7); // 1 WEEK
            $del_inactive   = time() - (60 * 60 * 24 * 30); // 1 MONTH
            $del_deleted    = time() - (60 * 60 * 24 * 7); // 1 WEEK

            // USERS TO DELETE
            $ChooseToDelete = $this->Update_Model->deleteUsersByDeletedAndInactive($del_deleted, $del_inactive);

            if ($ChooseToDelete) {
                
                while ($delete = parent::$db->fetchArray($ChooseToDelete)) {
                    
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
        $auto_backup        = FunctionsLib::readConfig('auto_backup');
        $last_backup        = FunctionsLib::readConfig('last_backup');
        $update_interval    = 6; // 6 HOURS

        // CHECK TIME
        if ((time() >= ($last_backup + (3600 * $update_interval))) &&  ($auto_backup == 1)) {
            
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

                    UpdateResourcesLib::updateResources(
                        $current_user,
                        $current_planet,
                        $current_planet['planet_b_building'],
                        false
                    );

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
        
        include_once XGP_ROOT . LIB_PATH . 'MissionControlLib.php';

        $_fleets    = $this->Update_Model->getStartFleets();

        while ($row = parent::$db->fetchArray($_fleets)) {
            
            $array                  = array();
            $array['planet_galaxy'] = $row['fleet_start_galaxy'];
            $array['planet_system'] = $row['fleet_start_system'];
            $array['planet_planet'] = $row['fleet_start_planet'];
            $array['planet_type']   = $row['fleet_start_type'];

            new MissionControlLib($array);
        }

        $this->Update_Model->clearResults($_fleets);

        $_fleets    = $this->Update_Model->getEndFleets();

        while ($row = parent::$db->fetchArray($_fleets)) {
            $array                  = array();
            $array['planet_galaxy'] = $row['fleet_end_galaxy'];
            $array['planet_system'] = $row['fleet_end_system'];
            $array['planet_planet'] = $row['fleet_end_planet'];
            $array['planet_type']   = $row['fleet_end_type'];

            new MissionControlLib($array);
        }

        $this->Update_Model->clearResults($_fleets);
        unset($_fleets);
    }

    /**
     * updateStatistics
     *
     * @return void
     */
    private function updateStatistics()
    {
        // LAST UPDATE AND UPDATE INTERVAL, EX: 15 MINUTES
        $stat_last_update   = FunctionsLib::readConfig('stat_last_update');
        $update_interval    = FunctionsLib::readConfig('stat_update_time');

        if ((time() >= ($stat_last_update + (60 * $update_interval)))) {

            $result = StatisticsLib::makeStats();

            FunctionsLib::updateConfig('stat_last_update', $result['stats_time']);
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
        $resource   = parent::$objects->getObjects();
        $ret_value  = false;

        if ($current_planet['planet_b_building_id'] != 0) {

            $current_queue  = $current_planet['planet_b_building_id'];

            if ($current_queue != 0) {
                $queue_array    = explode(";", $current_queue);
            }

            $build_array    = explode(",", $queue_array[0]);
            $build_end_time = floor($build_array[3]);
            $build_mode     = $build_array[4];
            $element        = $build_array[0];

            array_shift($queue_array);

            if ($build_mode == 'destroy') {

                $for_destroy = true;
            } else {

                $for_destroy = false;
            }

            if ($build_end_time <= time()) {

                $needed     = DevelopmentsLib::developmentPrice(
                    $current_user,
                    $current_planet,
                    $element,
                    true,
                    $for_destroy
                );
                
                $units      = $needed['metal'] + $needed['crystal'] + $needed['deuterium'];
                $current    = (int)$current_planet['planet_field_current'];
                $max        = (int)$current_planet['planet_field_max'];

                if ($current_planet['planet_type'] == 3) {
                    if ($element == 41) {

                        $current    += 1;
                        $max        += FIELDS_BY_MOONBASIS_LEVEL;
                        $current_planet[$resource[$element]]++;
                    } elseif ($element != 0) {

                        if ($for_destroy == false) {

                            $current += 1;
                            $current_planet[$resource[$element]]++;
                        } else {

                            $current -= 1;
                            $current_planet[$resource[$element]]--;
                        }
                    }
                } elseif ($current_planet['planet_type'] == 1) {
                    
                    if ($for_destroy == false) {

                        $current    += 1;
                        $current_planet[$resource[$element]]++;
                    } else {

                        $current    -= 1;
                        $current_planet[$resource[$element]]--;
                    }
                }

                if (count($queue_array) == 0) {

                    $new_queue = 0;
                } else {

                    $new_queue = implode(';', $queue_array);
                }

                $current_planet['planet_b_building']    = 0;
                $current_planet['planet_b_building_id'] = $new_queue;
                $current_planet['planet_field_current'] = $current;
                $current_planet['planet_field_max']     = $max;
                $current_planet['building_points']      = StatisticsLib::calculatePoints(
                    $element,
                    $current_planet[$resource[$element]]
                );

                $this->Update_Model->updatePlanet(
                    $resource[$element],
                    $current_planet[$resource[$element]],
                    $current_planet
                );
                
                $ret_value = true;
            } else {

                $ret_value = false;
            }
        } else {
            $current_planet['planet_b_building']    = 0;
            $current_planet['planet_b_building_id'] = 0;

            $this->Update_Model->updateBuildingsQueue($current_planet);

            $ret_value = false;
        }

        return $ret_value;
    }
}

/* end of Update.php */
