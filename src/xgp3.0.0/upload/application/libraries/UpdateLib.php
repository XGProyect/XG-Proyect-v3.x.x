<?php

/**
 * Update Libray.
 *
 * PHP Version 5.5+
 *
 * @category Library
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */

namespace application\libraries;

use application\core\XGPCore;

/**
 * UpdateLib Class.
 *
 * @category Classes
 *
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 *
 * @link     http://www.xgproyect.org
 *
 * @version  3.0.0
 */
class UpdateLib extends XGPCore
{
    /**
     * __construct.
     */
    public function __construct()
    {
        parent::__construct();

        // Other stuff
        $this->cleanUp();
        $this->createBackup();

        // Updates
        $this->updateFleets();
        $this->updateStatistics();
    }

    /**
     * cleanUp.
     */
    private function cleanUp()
    {
        $last_cleanup     = FunctionsLib::read_config('last_cleanup');
        $cleanup_interval = 6; // 6 HOURS

        if ((time() >= ($last_cleanup + (3600 * $cleanup_interval)))) {

            // TIMERS
            $del_before   = time() - (60 * 60 * 24 * 7); // 1 WEEK
            $del_inactive = time() - (60 * 60 * 24 * 30); // 1 MONTH
            $del_deleted  = time() - (60 * 60 * 24 * 7); // 1 WEEK

            // USERS TO DELETE
            $ChooseToDelete = parent::$db->query(
                'SELECT u.`user_id`
                FROM `' . USERS . '` AS u
                INNER JOIN `' . SETTINGS . "` AS s ON s.setting_user_id = u.user_id
                WHERE (s.`setting_delete_account` < '" . $del_deleted . "' AND s.`setting_delete_account` <> 0) OR
                (u.`user_onlinetime` < '" . $del_inactive . "' AND u.`user_onlinetime` <> 0 AND u.`user_authlevel` <> 3)"
            );

            if ($ChooseToDelete) {
                while ($delete = parent::$db->fetchArray($ChooseToDelete)) {
                    parent::$users->delete_user($delete['id']);
                }
            }

            parent::$db->query('DELETE FROM ' . MESSAGES . " WHERE `message_time` < '" . $del_before . "';");
            parent::$db->query('DELETE FROM ' . REPORTS . " WHERE `report_time` < '" . $del_before . "';");

            FunctionsLib::update_config('last_cleanup', time());
        }
    }

    /**
     * createBackup.
     */
    private function createBackup()
    {
        // LAST UPDATE AND UPDATE INTERVAL, EX: 15 MINUTES
        $auto_backup     = FunctionsLib::read_config('auto_backup');
        $last_backup     = FunctionsLib::read_config('last_backup');
        $update_interval = 6; // 6 HOURS

        // CHECK TIME
        if ((time() >= ($last_backup + (3600 * $update_interval))) &&  ($auto_backup == 1)) {
            parent::$db->backupDb(); // MAKE BACKUP

            FunctionsLib::update_config('last_backup', time());
        }
    }

    /**
     * updateBuildingsQueue.
     *
     * @param array $current_planet Current planet
     * @param array $current_user   Current user
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
                        DevelopmentsLib::set_first_element($current_planet, $current_user);
                    }
                } else {
                    break;
                }
            }
        }
    }

    /**
     * updateFleets.
     */
    private function updateFleets()
    {
        include_once XGP_ROOT . 'application/libraries/MissionControlLib.php';

        $_fleets = parent::$db->query(
            'SELECT 
            fleet_start_galaxy, 
            fleet_start_system, 
            fleet_start_planet, 
            fleet_start_type
            FROM ' . FLEETS . "
            WHERE `fleet_start_time` <= '" . time() . "' AND `fleet_mess` ='0'
            ORDER BY fleet_id ASC;"
        );

        while ($row = parent::$db->fetchArray($_fleets)) {
            $array                  = array();
            $array['planet_galaxy'] = $row['fleet_start_galaxy'];
            $array['planet_system'] = $row['fleet_start_system'];
            $array['planet_planet'] = $row['fleet_start_planet'];
            $array['planet_type']   = $row['fleet_start_type'];

            new MissionControlLib($array);
        }

        parent::$db->freeResult($_fleets);

        $_fleets = parent::$db->query(
            'SELECT 
            fleet_end_galaxy, 
            fleet_end_system, 
            fleet_end_planet, 
            fleet_end_type
            FROM ' . FLEETS . "
            WHERE `fleet_end_time` <= '" . time() . "
            ORDER BY fleet_id ASC';"
        );

        while ($row = parent::$db->fetchArray($_fleets)) {
            $array                  = array();
            $array['planet_galaxy'] = $row['fleet_end_galaxy'];
            $array['planet_system'] = $row['fleet_end_system'];
            $array['planet_planet'] = $row['fleet_end_planet'];
            $array['planet_type']   = $row['fleet_end_type'];

            new MissionControlLib($array);
        }

        parent::$db->freeResult($_fleets);

        unset($_fleets);
    }

    /**
     * updateStatistics.
     */
    private function updateStatistics()
    {
        // LAST UPDATE AND UPDATE INTERVAL, EX: 15 MINUTES
        $stat_last_update = FunctionsLib::read_config('stat_last_update');
        $update_interval  = FunctionsLib::read_config('stat_update_time');

        if ((time() >= ($stat_last_update + (60 * $update_interval)))) {
            $result = StatisticsLib::make_stats();

            FunctionsLib::update_config('stat_last_update', $result['stats_time']);
        }
    }

    /**
     * checkBuildingQueue.
     *
     * @param array $current_planet Current planet
     * @param array $current_user   Current user
     *
     * @return bool
     */
    private static function checkBuildingQueue(&$current_planet, &$current_user)
    {
        $resource  = parent::$objects->getObjects();
        $ret_value = false;

        if ($current_planet['planet_b_building_id'] != 0) {
            $current_queue = $current_planet['planet_b_building_id'];

            if ($current_queue != 0) {
                $queue_array = explode(';', $current_queue);
            }

            $build_array    = explode(',', $queue_array[0]);
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
                $needed = DevelopmentsLib::development_price(
                    $current_user,
                    $current_planet,
                    $element,
                    true,
                    $for_destroy
                );

                $units   = $needed['metal'] + $needed['crystal'] + $needed['deuterium'];
                $current = (int) $current_planet['planet_field_current'];
                $max     = (int) $current_planet['planet_field_max'];

                if ($current_planet['planet_type'] == 3) {
                    if ($element == 41) {
                        $current    += 1;
                        $max        += FIELDS_BY_MOONBASIS_LEVEL;
                        ++$current_planet[$resource[$element]];
                    } elseif ($element != 0) {
                        if ($for_destroy == false) {
                            $current += 1;
                            ++$current_planet[$resource[$element]];
                        } else {
                            $current -= 1;
                            --$current_planet[$resource[$element]];
                        }
                    }
                } elseif ($current_planet['planet_type'] == 1) {
                    if ($for_destroy == false) {
                        $current    += 1;
                        ++$current_planet[$resource[$element]];
                    } else {
                        $current    -= 1;
                        --$current_planet[$resource[$element]];
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
                $current_planet['building_points']      = StatisticsLib::calculate_points(
                    $element,
                    $current_planet[$resource[$element]]
                );

                parent::$db->query(
                    'UPDATE ' . PLANETS . ' AS p
                    INNER JOIN ' . USERS_STATISTICS . ' AS s ON s.user_statistic_user_id = p.planet_user_id
                    INNER JOIN ' . BUILDINGS . ' AS b ON b.building_planet_id = p.`planet_id` SET
                    `' . $resource[$element] . "` = '" . $current_planet[$resource[$element]] . "',
                    `user_statistic_buildings_points` = `user_statistic_buildings_points` + '" .
                        $current_planet['building_points'] . "',
                    `planet_b_building` = '" . $current_planet['planet_b_building'] . "',
                    `planet_b_building_id` = '" . $current_planet['planet_b_building_id'] . "',
                    `planet_field_current` = '" . $current_planet['planet_field_current'] . "',
                    `planet_field_max` = '" . $current_planet['planet_field_max'] . "'
                    WHERE `planet_id` = '" . $current_planet['planet_id'] . "';"
                );

                $ret_value = true;
            } else {
                $ret_value = false;
            }
        } else {
            $current_planet['planet_b_building']    = 0;
            $current_planet['planet_b_building_id'] = 0;

            parent::$db->query(
                'UPDATE ' . PLANETS . " SET
                `planet_b_building` = '" . $current_planet['planet_b_building'] . "',
                `planet_b_building_id` = '" . $current_planet['planet_b_building_id'] . "'
                WHERE `planet_id` = '" . $current_planet['planet_id'] . "';"
            );

            $ret_value = false;
        }

        return $ret_value;
    }
}

/* end of UpdateLib.php */
