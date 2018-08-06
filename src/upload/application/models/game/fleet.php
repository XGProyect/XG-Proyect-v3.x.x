<?php
/**
 * Fleet Model
 *
 * PHP Version 5.5+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\game;

use application\core\entities\FleetEntity;
use application\core\enumerators\MissionsEnumerator as Missions;

/**
 * Fleet Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Fleet
{

    private $db = null;

    /**
     * __construct()
     */
    public function __construct($db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * __destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Get ships by planet id
     * 
     * @param int $planet_id Planet ID
     * 
     * @return array
     */
    public function getShipsByPlanetId($planet_id)
    {
        if ((int) $planet_id > 0) {

            return $this->db->queryFetch(
                    "SELECT 
                        s.`ship_small_cargo_ship`,
                        s.`ship_big_cargo_ship`,
                        s.`ship_light_fighter`,
                        s.`ship_heavy_fighter`,
                        s.`ship_cruiser`,
                        s.`ship_battleship`,
                        s.`ship_colony_ship`,
                        s.`ship_recycler`,
                        s.`ship_espionage_probe`,
                        s.`ship_bomber`,
                        s.`ship_solar_satellite`,
                        s.`ship_destroyer`,
                        s.`ship_deathstar`,
                        s.`ship_battlecruiser`
                    FROM `" . SHIPS . "` AS s 
                    WHERE s.`ship_planet_id` = '" . $planet_id . "';"
            );
        }

        return [];
    }

    /**
     * Get all fleets by user id or owner
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getAllFleetsByUserId($user_id)
    {
        if ((int) $user_id > 0) {

            return $this->db->queryFetchAll(
                    "SELECT 
                        f.*
                    FROM `" . FLEETS . "` f
                    WHERE f.`fleet_owner` = '" . $user_id . "';"
            );
        }

        return [];
    }
    
    /**
     * Get ACS Data by group ID
     * 
     * @param int $group_id
     * 
     * @return array
     */
    public function getAcsDataByGroupId(string $group_id)
    {        
        if (!empty($group_id)) {

            return $this->db->queryFetchAll(
                    "SELECT 
                        acs.*
                    FROM `" . ACS_FLEETS . "` acs
                    WHERE acs.`acs_fleet_id` = '" . $group_id . "';"
            );
        }

        return [];
    }

    /**
     * Get all user planets
     * 
     * @param int $user_id User ID
     * 
     * @return array
     */
    public function getAllPlanetsByUserId($user_id)
    {
        if ((int) $user_id > 0) {

            return $this->db->queryFetchAll(
                    "SELECT 
                    p.`planet_id`,
                    p.`planet_name`,
                    p.`planet_galaxy`,
                    p.`planet_system`,
                    p.`planet_planet`,
                    p.`planet_type`
                FROM `" . PLANETS . "` AS p
                WHERE p.`planet_user_id` = '" . $user_id . "';"
            );
        }

        return [];
    }

    /**
     * Get ongoing ACS attacks
     * 
     * @param
     * 
     * @return mixed
     */
    public function getOngoingAcs($user_id)
    {
        return $this->db->queryFetchAll(
                "SELECT 
                    acs.* 
                FROM `" . ACS_FLEETS . "` acs"
        );
    }

    /**
     * Get planet owner by coords
     * 
     * @param int $g    Galaxy
     * @param int $s    System
     * @param int $p    Planet
     * @param int $pt   Planet Type
     * 
     * @return bool
     */
    public function getPlanetOwnerByCoords(int $g, int $s, int $p, int $pt): array
    {
        return $this->db->queryFetch(
                "SELECT 
                `planet_user_id`
            FROM `" . PLANETS . "`
            WHERE `planet_galaxy` = '" . $g . "'
                AND `planet_system` = '" . $s . "'
                AND `planet_planet` = '" . $p . "'
                AND `planet_type` = '" . $pt . "';"
            ) ?? [];
    }

    /**
     * Get target data by coords
     * 
     * @param int $g    Galaxy
     * @param int $s    System
     * @param int $p    Planet
     * @param int $pt   Planet Type
     * 
     * @return type
     */
    public function getTargetDataByCoords(int $g, int $s, int $p, int $pt): array
    {
        return $this->db->queryFetch(
                "SELECT 
                p.`planet_user_id`,
                p.`planet_debris_metal`,
                p.`planet_debris_crystal`,
                p.`planet_invisible_start_time`,
                p.`planet_destroyed`,
                u.`user_id`,
                u.`user_authlevel`,
                u.`user_onlinetime`,
                u.`user_ally_id`,
                s.`setting_vacations_status`
            FROM `" . PLANETS . "` p
            INNER JOIN `" . USERS . "` u ON u.`user_id` = p.`planet_user_id`
            INNER JOIN `" . SETTINGS . "` s ON s.`setting_user_id` = u.`user_id`
            WHERE p.`planet_galaxy` = '" . $g . "'
                AND p.`planet_system` = '" . $s . "'
                AND p.`planet_planet` = '" . $p . "'
                AND p.`planet_type` = '" . $pt . "'"
            ) ?? [];
    }

    /**
     * Get ACS count
     * 
     * @param type $acs_fleet_id
     * 
     * @return int
     */
    public function getAcsCount($acs_fleet_id): int
    {
        return $this->db->queryFetch(
            "SELECT COUNT(`acs_fleet_id`) AS `acs_amount`
            FROM `" . ACS_FLEETS . "`
            WHERE `acs_fleet_id` = '" . $acs_fleet_id . "'"
        )['acs_amount'] ?? 0;
    }

    /**
     * Insert a new fleet
     * 
     * @param array $fleet_data
     * @param array $planet_data
     * 
     * @return boolean
     */
    public function insertNewFleet(array $fleet_data, array $planet_data, array $fleet_ships): bool
    {
        try {

            $this->db->beginTransaction();

            // prepare the query
            foreach ($fleet_data as $field => $value) {

                $sql[] = "`" . $field . "` = '" . $value . "'";
            }

            $this->db->query(
                "INSERT INTO `" . FLEETS . "` SET "
                . join(', ', $sql) .
                ", `fleet_creation` = '" . time() . "';"
            );

            // remove ships and resources
            $this->updatePlanet($planet_data, $fleet_data, $fleet_ships);

            $this->db->commitTransaction();

            return true;
        } catch (Exception $e) {

            $this->db->rollbackTransaction();

            return false;
        }
    }

    /**
     * Update planet based on the received values
     * 
     * @param array $planet_data Planet Data
     * @param array $fleet_data  Fleet Data
     * @param array $fleet_ships Fleet Ships
     * 
     * @return void
     */
    public function updatePlanet(array $planet_data, array $fleet_data, array $fleet_ships)
    {
        // prepare the query
        foreach ($fleet_ships as $field => $value) {

            $sql[] = "`" . $field . "` = `" . $field . "` - '" . $value . "'";
        }

        $this->db->query(
            "UPDATE `" . PLANETS . "` AS p
            INNER JOIN `" . SHIPS . "` AS s ON s.`ship_planet_id` = p.`planet_id` SET
            " . join(', ', $sql) . ", 
            `planet_metal` = `planet_metal` - " . $fleet_data['fleet_resource_metal'] . ",
            `planet_crystal` = `planet_crystal` - " . $fleet_data['fleet_resource_crystal'] . ",
            `planet_deuterium` = `planet_deuterium` - " . ($fleet_data['fleet_resource_deuterium'] + $fleet_data['fleet_fuel']) . "
            WHERE `planet_id` = " . $planet_data['planet_id'] . ";"
        );
    }

    /**
     * Get buddies
     * 
     * @param int $current_planet Current Planet ID
     * @param int $target_planet  Target Planet ID
     * 
     * @return array
     */
    public function getBuddies(int $current_planet, int $target_planet): string
    {
        return $this->db->queryFetch(
                "SELECT COUNT(*) AS buddies
            FROM  `" . BUDDY . "`
            WHERE (
                (
                    buddy_sender = '" . $current_planet . "'
                    AND buddy_receiver = '" . $target_planet . "'
                )
                OR (
                    buddy_sender = '" . $target_planet . "'
                    AND buddy_receiver = '" . $current_planet . "'
                )
            )
            AND buddy_status = 1"
            )['buddies'];
    }

    /**
     * Get ACS Max Time
     * 
     * @param int $group_id Group ID
     * 
     * @return string
     */
    public function getAcsMaxTime(int $group_id): string
    {
        return $this->db->queryFetch(
                "SELECT MAX(`fleet_start_time`) AS start_time
                FROM `" . FLEETS . "`
                WHERE `fleet_group` = '" . $group_id . "';"
            )['start_time'];
    }

    /**
     * Update ACS Fleets Times
     * 
     * @param int $group_id   Group ID
     * @param int $start_time Start Time
     * @param int $end_time   End Time
     * 
     * @return void
     */
    public function updateAcsTimes(int $group_id, int $start_time, int $end_time)
    {
        $this->db->query(
            "UPDATE `" . FLEETS . "` SET
            `fleet_start_time` = '" . $start_time . "',
            `fleet_end_time` = fleet_end_time + '" . $end_time . "'
            WHERE `fleet_group` = '" . $group_id . "';"
        );
    }

    /**
     * Get the ACS Owner
     * 
     * @param int $fleet_group
     * 
     * @return string
     */
    public function getAcsOwner(int $fleet_group): int
    {
        return $this->db->queryFetch(
            "SELECT af.`acs_fleet_owner`
                FROM `" . ACS_FLEETS . "` af
                WHERE af.`acs_fleet_id` = '" . $fleet_group . "';"
        )['acs_fleet_owner'] ?? 0;
    }
    
    /**
     * Remove an ACS fleet
     * 
     * @param int $fleet_group
     * 
     * @return void
     */
    public function removeAcs(int $fleet_group): void
    {
        $this->db->query(
            "DELETE FROM `" . ACS_FLEETS . "`
            WHERE `acs_fleet_id` = '" . $fleet_group . "';"
        );

        $this->db->query(
            "UPDATE `" . FLEETS . "` f SET
                f.`fleet_group` = '0'
            WHERE f.`fleet_group` = '" . $fleet_group . "';"
        );
    }
    
    /**
     * Return the fleet to its start planet
     * 
     * @param FleetEntity $fleet
     * @param int         $user_id Current user ID
     * 
     * @return bool
     */
    public function returnFleet(FleetEntity $fleet, int $user_id): bool
    {
        try {

            $this->db->beginTransaction();

            if ($fleet->getFleetGroup() > 0) {

                $acs = $this->getAcsOwner($fleet->getFleetGroup());

                if ($acs['acs_fleet_owner'] == $fleet->getFleetOwner() 
                    && $fleet->getFleetMission() == Missions::attack) {

                    $this->removeAcs($fleet->getFleetGroup());
                }

                if ($fleet->getFleetMission() == Missions::acs) {

                    $this->db->query(
                        "UPDATE `" . FLEETS . "` f SET
                            f.`fleet_group` = '0'
                        WHERE f.`fleet_id` = '" . $fleet->getFleetId() . "';"
                    );
                }
            }

            $base_time = time();
            $fleet_creation = $fleet->getFleetCreation();
            $current_time = $base_time - $fleet_creation;
            $flight_lenght = $fleet->getFleetStartTime() - $fleet_creation;
            $return_time = $base_time + $current_time;
            
            if ($fleet->getFleetEndStay() != 0 
                && $current_time > $flight_lenght) {
                
                $return_time = $base_time + $flight_lenght;
            }

            $this->db->query(
                "UPDATE `" . FLEETS . "` f SET
                    f.`fleet_start_time` = '" . $base_time . "',
                    f.`fleet_end_stay` = '0',
                    f.`fleet_end_time` = '" . $return_time . "',
                    f.`fleet_target_owner` = '" . $user_id . "',
                    f.`fleet_mess` = '1'
                WHERE f.`fleet_id` = '" . $fleet->getFleetId() . "';"
            );

            $this->db->commitTransaction();

            return true;
        } catch (Exception $e) {

            $this->db->rollbackTransaction();

            return false;
        }
    }
    
    /**
     * Create a new ACS Record
     * 
     * @param type $acs_code
     * @param FleetEntity $fleet
     * 
     * @return boolean
     */
    public function createNewAcs($acs_code, FleetEntity $fleet)
    {
        try {

            $this->db->beginTransaction();

            $this->db->query(
                "INSERT INTO `" . ACS_FLEETS . "` SET
                    `acs_fleet_name` = '" . $acs_code . "',
                    `acs_fleet_owner` = '" . $fleet->getFleetOwner() . "',
                    `acs_fleet_fleets` = '" . $fleet->getFleetId() . "',
                    `acs_fleet_galaxy` = '" . $fleet->getFleetEndGalaxy() . "',
                    `acs_fleet_system` = '" . $fleet->getFleetEndSystem() . "',
                    `acs_fleet_planet` = '" . $fleet->getFleetEndPlanet() . "',
                    `acs_fleet_planet_type` = '" . $fleet->getFleetEndType() . "',
                    `acs_fleet_invited` = '" . serialize([$fleet->getFleetOwner()]) . "'"
            );

            $group_id = $this->db->insertId();
            
            $this->db->query(
                "UPDATE `" . FLEETS . "` SET
                    `fleet_group` = '" . $group_id . "'
                WHERE `fleet_id` = '" . $fleet->getFleetId() . "'"
            );

            $this->db->commitTransaction();

            return $group_id;
        } catch (Exception $e) {

            $this->db->rollbackTransaction();

            return false;
        }
    }
    
    /**
     * Get all the ACS Members
     * 
     * @param string $acs_members
     * 
     * @return array
     */
    public function getListOfAcsMembers($acs_members)
    {
        return $this->db->queryFetchAll(
            "SELECT 
                u.`user_id`,
                u.`user_name`
            FROM `" . USERS . "` u
            WHERE u.`user_id` IN (" . $acs_members . ");"
        );
    }
}

/* end of fleet.php */
